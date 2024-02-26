<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('ownModule', Appointment::class);
        $selectedServiceId = $request->input('service'); // Fetch the 'service' parameter

        if ($selectedServiceId) {
            // If 'selectedService' is present, find the service by ID
            $service = Service::findOrFail($selectedServiceId);
        } else {
            $service = Service::first();
        }

        if ($service) {
            $this->authorize('create', [Appointment::class, $service]);

            $now = Carbon::now('GMT+3');

            // Ongoing appointment
            $ongoingAppointment = $service->appointments()
                ->select('start_time', 'end_time', 'name', 'email', 'phone')
                ->where('status', 'booked') // Add condition for status
                ->where('start_time', '<=', $now)
                ->where('end_time', '>', $now)
                ->first();

            // Previous appointment
            $previousAppointment = $service->appointments()
                ->select('start_time', 'end_time', 'name', 'email', 'phone')
                ->whereDate('start_time', '=', $now->toDateString()) // Limit records to today
                ->where('end_time', '<', $now)
                ->latest('end_time')
                ->first();

            // Next appointment
            $nextAppointment = $service->appointments()
                ->select('start_time', 'end_time', 'name', 'email', 'phone')
                ->where('status', 'booked') // Add condition for status
                ->whereDate('start_time', '=', $now->toDateString()) // Limit records to today
                ->where('start_time', '>', $now)
                ->orderBy('start_time', 'asc')
                ->first();

            if ($nextAppointment) {
                $futureAppointments = $service->appointments()
                    ->select('start_time', 'end_time', 'name', 'email', 'phone')
                    ->where('start_time', '>', $nextAppointment->start_time)
                    ->get();
            } else {
                // If there is no next appointment, this would mean all future appointments after current time
                $futureAppointments = $service->appointments()
                    ->select('start_time', 'end_time', 'name', 'email', 'phone')
                    ->where('start_time', '>', $now)
                    ->get();
            }
        }

        return Inertia::render('Appointment/Basic/Index', [
            'services' => Service::all(),
            'service' => $service ?? null,
            'appointments' => $futureAppointments ?? null,
            'ongoing' => $ongoingAppointment ?? null,
            'previous' => $previousAppointment ?? null,
            'next' => $nextAppointment ?? null
        ]);
    }

    public function create(Request $request)
    {
        $this->authorize('ownModule', Appointment::class);

        $selectedServiceId = $request->input('selectedService');
        $day = $request->input('day'); // Fetch the 'day' parameter

        if ($selectedServiceId) {
            // If 'selectedService' is present, find the service by ID
            $service = Service::findOrFail($selectedServiceId);
        } else {
            // If 'selectedService' is not present, get the first service
            $service = Service::first();
        }

        if ($service) {
            $this->authorize('create', [Appointment::class, $service]);

            // Check if 'day' is provided, else use today's date
            $date = $day ? Carbon::createFromFormat('Y-m-d', $day) : Carbon::now();

            // Fetch only start_time and end_time for appointments of the given date
            $appointments = $service->appointments()
                ->select('start_time', 'end_time', 'name', 'email', 'phone')
                ->where('status', 'booked') // Add condition for status
                ->whereDate('start_time', '=', $date->toDateString())
                ->orderBy('start_time', 'asc')
                ->get();
        }


        return Inertia::render('Appointment/Basic/Create', [
            'services' => Service::all(),
            'service' => $service ?? null,
            'appointments' => $appointments ?? null
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'service_id' => 'required|exists:services,id',
            'start_time' => 'required|date_format:H:i:s',
            'end_time' => 'required|date_format:H:i:s|after:start_time',
            'notes' => 'nullable|string|max:255',
            'price' => 'nullable|numeric',
            'day' => 'required|date', // Ensure this is a date.
        ]);
        $service = Service::findOrFail($validatedData['service_id']);
        $this->authorize('create', [Appointment::class, $service]);

        do {
            $slug = Str::random(16); // Or any length you prefer
        } while (Appointment::where('slug', $slug)->exists());

        // Extract the date part from the day
        $date = Carbon::parse($validatedData['day'])->toDateString(); // This will give you "2024-02-15"

        // Combine the date with start_time and end_time
        $startTimestamp = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $validatedData['start_time']);
        $endTimestamp = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $validatedData['end_time']);

        // Check for exact overlapping appointments, excluding edges
        $overlappingAppointments = Appointment::where(function ($query) use ($startTimestamp, $endTimestamp) {
            // Appointments that start or end within the new appointment's duration
            $query->where(function ($q) use ($startTimestamp, $endTimestamp) {
                $q->where('start_time', '>', $startTimestamp)
                    ->where('start_time', '<', $endTimestamp);
            })->orWhere(function ($q) use ($startTimestamp, $endTimestamp) {
                $q->where('end_time', '>', $startTimestamp)
                    ->where('end_time', '<', $endTimestamp);
            });
        })
            ->where('service_id', $validatedData['service_id']) // Add condition for service_id
            ->where('status', 'booked') // Add condition for status
            ->exists();

        $exclusiveStartOrEndAppointments = Appointment::where(function ($query) use ($startTimestamp, $endTimestamp) {
            $query->where('end_time', '=', $endTimestamp)
                ->orWhere('start_time', '=', $startTimestamp);
        })
            ->where('service_id', $validatedData['service_id']) // Add condition for service_id
            ->where('status', 'booked') // Add condition for status
            ->exists();



        if ($overlappingAppointments || $exclusiveStartOrEndAppointments) {
            return redirect()->back()->with('error', 'The selected time slot is not available.');
        }

        Appointment::Create([
            'service_id' => $validatedData['service_id'],
            'user_id' => auth()->id(),
            'slug' => $slug,
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'start_time' => $startTimestamp,
            'end_time' => $endTimestamp,
            'notes' => $validatedData['notes'],
            'price' => $validatedData['price'],
            'status' => 'booked', // Assuming new appointments default to 'booked'
        ]);
        return redirect()->back()->with('success', 'Appointment has been created');
    }
}
