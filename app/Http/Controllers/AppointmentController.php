<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Service;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AppointmentController extends Controller
{
    private string $timeZone = 'GMT+3';

    /**
     * @throws AuthorizationException + via getServiceFromRequest function
     * you need to have access to appointment module + via middleware
     * you need to have access to current service + via Service Policy
     */
    public function index(Request $request): \Inertia\Response
    {
        $activeServices = Service::where('status', 'active')->get();

        if ($activeServices->isEmpty()) {
            return Inertia::render('Appointment/Basic/Index', [
                'services' => collect(),
                'service' => null,
                'appointments' => null,
            ]);
        }

        $service = $this->getServiceFromRequest($request);
        $now = Carbon::now($this->timeZone);

        // Refactor appointments retrieval to reuse service object
        // Note: Consider consolidating or optimizing this part based on specific use case
        // Assuming $now is already defined
        $appointmentsQuery = $service->appointments()
            ->select('start_time', 'end_time', 'name', 'email', 'phone', 'status', 'notes', 'id', 'price');

        // Ongoing appointment
        $ongoingAppointment = (clone $appointmentsQuery)
            ->where('status', 'booked')
            ->where('start_time', '<=', $now)
            ->where('end_time', '>', $now)
            ->first();
        if ($ongoingAppointment) {
            $ongoingAppointment->type = 'ongoing';
        }

        // Previous appointment
        $previousAppointment = (clone $appointmentsQuery)
            ->whereDate('start_time', '=', $now->toDateString())
            ->whereIn('status', ['completed', 'missed', 'cancelled']) // Include both completed and missed statuses
            ->where('start_time', '<', $now)
            ->latest('end_time')
            ->get()
            ->each(function ($appointment) {
                $appointment->type = 'previous';
            });

        // Next appointment
        $nextAppointment = (clone $appointmentsQuery)
            ->where('status', 'booked')
            ->whereDate('start_time', '=', $now->toDateString())
            ->where('start_time', '>', $now)
            ->orderBy('start_time', 'asc')
            ->first();
        if ($nextAppointment) {
            $nextAppointment->type = 'next';
        }

        // Future appointments
        $futureAppointments = (clone $appointmentsQuery)
            ->where('status', 'booked')
            ->where('start_time', '>', $nextAppointment ? $nextAppointment->start_time : $now)
            ->orderBy('start_time', 'asc')
            ->get()
            ->each(function ($appointment) {
                $appointment->type = 'future';
            });


        // Combine all appointments into a single collection, filtering out null values for ongoing, previous, and next
        $allAppointments = collect([$ongoingAppointment, $nextAppointment])
            ->filter() // Filter out null values
            ->merge($futureAppointments)
            ->merge($previousAppointment); // Merge with future appointments

        // Sort all appointments by start time and reassign
        $allAppointments = $allAppointments->sortBy('start_time')->values();

        return Inertia::render('Appointment/Basic/Index', [
            'services' => $activeServices, // Assuming $activeServices is defined
            'service' => $service,
            'appointments' => $allAppointments,
        ]);
    }


    /**
     * @throws AuthorizationException + via getServiceFromRequest function
     * * you need to have access to appointment module + via middleware
     * * you need to have access to current service + via Service Policy
     */
    public function create(Request $request): \Inertia\Response
    {
        // Check if there are any services available at the very beginning
        if (Service::where('status', 'active')->exists()) {
            $service = $this->getServiceFromRequest($request);
            $day = $request->input('day'); // Fetch the 'day' parameter

            // Check if 'day' is provided, else use today's date with specified timezone
            $date = $day ? Carbon::createFromFormat('Y-m-d', $day) : Carbon::now($this->timeZone);

            // Fetch appointments only if a service is selected
            $appointments = $this->getAppointments($service, $date);
        } else {
            // No services are available, set $service and $appointments to null
            $service = null;
            $appointments = collect(); // Use an empty collection for $appointments to keep return type consistent
        }

        return Inertia::render('Appointment/Basic/Create', [
            'services' => Service::where('status', 'active')->get(),
            'service' => $service,
            'appointments' => $appointments
        ]);
    }

    /**
     * @throws AuthorizationException + via Service Policy
     * * you need to have access to appointment module + via middleware
     * * you need to have access to current service + via Service Policy
     * * you need to have access to current appointment + via Appointment Policy
     */
    public function edit(Appointment $appointment, Request $request): \Inertia\Response
    {
        $this->authorize('view', $appointment);
        $selectedServiceId = $request->input('service');
        $day = $request->input('day'); // Fetch the 'day' parameter

        if ($selectedServiceId) {

            $service = Service::where('status', 'active')
                ->where('id', $selectedServiceId)
                ->firstOrFail();
        } else {
            // Assuming $appointment is defined and has a relation named 'service'
            $service = $appointment->service;

            // Optionally, you can still check if the service is active, if necessary
            if ($service->status !== 'active') {
                abort(404, 'Active service not found.');
            }
        }

        $this->authorize('view', $service);


        // Simplify date handling using ternary operator
        $date = $day ? Carbon::createFromFormat('Y-m-d', $day) : Carbon::parse($appointment->start_time);

        // Fetch appointments excluding the current one and for the given date
        $appointments = $this->getAppointments($service, $date, $appointment->id);

        return Inertia::render('Appointment/Basic/Edit', [
            'services' => Service::where('status', 'active')->get(),
            'service' => $service,
            'appointments' => $appointments,
            'appointment' => $appointment
        ]);
    }

    /**
     * @throws AuthorizationException|ValidationException
     * * you need to have access to appointment module + via middleware
     * * you need to have access to requested service + via validateAppointmentData
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $this->validateAppointmentData($request);

        if ($this->checkOverlappingAppointments($validatedData)) {
            return redirect()->back()->with('error', 'The selected time slot is not available.');
        }

        // Extract start and end timestamps from the validated data
        [$startTimestamp, $endTimestamp] = $this->createTimestamps($validatedData);

        // Generate a unique slug for the appointment
        do {
            $slug = Str::random(8);
        } while (Appointment::where('slug', $slug)->exists());

        // Create the appointment
        Appointment::create([
            'service_id' => $validatedData['service_id'],
            'user_id' => auth()->id(), // Assuming you're tracking which user created the appointment
            'slug' => $slug,
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'start_time' => $startTimestamp,
            'end_time' => $endTimestamp,
            'notes' => $validatedData['notes'],
            'price' => $validatedData['price'],
            'status' => $validatedData['status'], // Assuming 'booked' status is set by default in the form
        ]);

        return redirect()->back()->with('success', 'Appointment has been created successfully.');
    }

    /**
     * @throws AuthorizationException|ValidationException
     * * you need to have access to appointment module + via middleware
     * * you need to have access to requested service + via validateAppointmentData
     * * you need to have access to current appointment + via Appointment Policy
     */
    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorize('update', $appointment);
        $validatedData = $this->validateAppointmentData($request, false);

        if ($this->checkOverlappingAppointments($validatedData, $appointment->id)) {
            return redirect()->back()->with('error', 'The selected time slot is not available.');
        }

        // Extract start and end timestamps from the validated data
        [$startTimestamp, $endTimestamp] = $this->createTimestamps($validatedData);

        // Update the appointment
        $appointment->update([
            'service_id' => $validatedData['service_id'],
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'start_time' => $startTimestamp,
            'end_time' => $endTimestamp,
            'notes' => $validatedData['notes'],
            'price' => $validatedData['price'],
            'status' => $validatedData['status'],
        ]);

        return redirect()->route('appointments.edit', ['appointment' => $appointment->id])->with('success', 'Appointment has been updated successfully.');
    }

    /**
     * @throws AuthorizationException
     * * you need to have access to appointment module + via middleware
     * * you need to have access to requested service + via validateAppointmentData
     * * you need to have access to current appointment + via Appointment Policy
     */
    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorize('update', $appointment);
        $request->merge([
            'day' => $appointment->start_time,
            'start_time' => Carbon::parse($appointment->start_time)->format('H:i:s'),
            'end_time' => Carbon::parse($appointment->end_time)->format('H:i:s')
        ]);

        $validatedData = $request->validate([
            'status' => 'required|string|in:booked,completed,cancelled,missed',
            'day' => 'required|date',
            'start_time' => 'required|date_format:H:i:s',
            'end_time' => 'required|date_format:H:i:s|after:start_time',
        ]);

        if ($validatedData['status'] === 'booked') {
            $validator = $this->checkStatusValid($validatedData);
            if ($validator->messages()->has('end_time')) {
                return redirect()->back()->with('error', 'This appointment can not be set as booked because of the time stamps.');
            }
        }
        // Update the appointment
        $appointment->update([
            'status' => $validatedData['status'],
        ]);
        return redirect()->back()->with('success', 'Appointment has been updated successfully.');
    }

    /**
     * Validate appointment request data.
     * @throws AuthorizationException
     * @throws ValidationException
     */
    private function validateAppointmentData($request, $isNew = true)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'service_id' => 'required|exists:services,id',
            'start_time' => 'required|date_format:H:i:s',
            'end_time' => 'required|date_format:H:i:s|after:start_time',
            'notes' => 'nullable|string|max:255',
            'price' => 'nullable|numeric',
            'day' => 'required|date',
            'status' => 'required|string|in:booked,completed,cancelled,missed'
        ];

        if ($isNew) {
            $request->merge(['status' => 'booked']);
        }
        // Perform validation
        $validatedData = $request->validate($rules);

        if ($validatedData['status'] === 'booked') {
            $validator = $this->checkStatusValid($validatedData);
            if ($validator->messages()->has('end_time')) {
                throw new ValidationException($validator);
            }
        }

        // Retrieve the service using the validated service_id
        $service = Service::where('id', $validatedData['service_id'])
            ->where('status', 'active')
            ->firstOrFail();

        // Authorization check
        $this->authorize('view', $service);

        // Return the validated data for further processing
        return $validatedData;
    }

    private function checkStatusValid($validatedData): \Illuminate\Validation\Validator
    {
        [$startTimestamp, $endTimestamp] = $this->createTimestamps($validatedData);

        // Initialize Validator for custom error messages
        $validator = Validator::make([], []); // Empty data and rules for manual error addition

        // Check if either the start or end time is not in the future
        if (!$startTimestamp->isFuture($this->timeZone)) {
            $validator->errors()->add('start_time', 'The start time must be in the future.');
        }
        if (!$endTimestamp->isFuture($this->timeZone)) {
            $validator->errors()->add('end_time', 'The end time must be in the future.');
        }

        return $validator;
    }

    /**
     * Check for overlapping appointments.
     */
    private function checkOverlappingAppointments($validatedData, $appointmentId = null)
    {
        [$startTimestamp, $endTimestamp] = $this->createTimestamps($validatedData);

        $query = Appointment::where(function ($query) use ($startTimestamp, $endTimestamp) {
            $query->where(function ($q) use ($startTimestamp, $endTimestamp) {
                $q->where('start_time', '<', $endTimestamp)
                    ->where('end_time', '>', $startTimestamp);
            });
        })
            ->where('service_id', $validatedData['service_id'])
            ->where('status', 'booked');

        if ($appointmentId) {
            $query->where('id', '!=', $appointmentId);
        }

        return $query->exists();
    }

    /**
     * Create start and end timestamps from request data.
     */
    private function createTimestamps($validatedData): array
    {
        $date = Carbon::parse($validatedData['day'], $this->timeZone)->toDateString();
        $startTimestamp = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $validatedData['start_time'], $this->timeZone);
        $endTimestamp = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $validatedData['end_time'], $this->timeZone);

        return [$startTimestamp, $endTimestamp];
    }

    /**
     * @throws AuthorizationException
     */
    private function getServiceFromRequest(Request $request)
    {
        $selectedServiceId = $request->input('service');
        $service = Service::where('status', 'active')
            ->when($selectedServiceId, function ($query, $selectedServiceId) {
                return $query->where('id', $selectedServiceId);
            }, function ($query) {
                return $query->orderBy('id')->first();
            })
            ->firstOrFail();
        $this->authorize('view', $service);
        return $service;
    }
    private function getAppointments($service, $date, $excludeId = null) {
        $query = $service->appointments()
            ->select('start_time', 'end_time', 'name', 'email', 'phone')
            ->where('status', 'booked')
            ->whereDate('start_time', '=', $date->toDateString());

        // Conditionally add the where clause if an ID is to be excluded
        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->orderBy('start_time', 'asc')->get();
    }

}
