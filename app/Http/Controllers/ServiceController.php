<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    protected int $maxServicesAllowed = 10;

    public function index()
    {
        return Inertia::render('Appointment/Service/Index', [
            'services' => Service::all()
        ]);
    }


    public function show($slug): \Inertia\Response
    {
        $service = Service::where('slug', $slug)->firstOrFail();

        return Inertia::render('Appointment/Service/Show', [
            'service' => $service
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function edit(Service $service)
    {
        $this->authorize('view', $service);

        return Inertia::render('Appointment/Service/Edit', [
            'service' => $service
        ]);
    }

    public function create()
    {
        return Inertia::render('Appointment/Service/Create');
    }

    /**
     * Handle an incoming service store request.
     *
     */
    public function store(Request $request): RedirectResponse
    {
        $request->merge(['status' => 'active']);

        if (auth()->user()->tenant->services()->count() >= $this->maxServicesAllowed) {
            // Redirect back with an error message if the maximum limit is reached
            return redirect()->back()->with('error', 'You can not create more than 10 services.');
        }

        $validatedData = $request->validate($this->validationRules());
        do {
            $slug = Str::random(8); // Or any length you prefer
        } while (Service::where('slug', $slug)->exists());

        Service::Create([
            'slug' => $slug,
            'name' => $validatedData['name'],
            'bio' => $validatedData['bio'] ?? null,
            'email' => $validatedData['email'] ?? null,
            'phone' => $validatedData['phone'] ?? null,
            'external' => $validatedData['external'] ?? null,
            'opening_time' => $validatedData['opening_time'],
            'closing_time' => $validatedData['closing_time'],
            'status' => $validatedData['status'],
            'price' => $validatedData['price'] ?? null
        ]);


        return redirect()->back()->with('success', 'Service has been created');
    }

    /**
     * Handle an incoming service update request.
     *
     * @throws AuthorizationException
     */
    public function update(Request $request, Service  $service): RedirectResponse
    {
        // Authorize the update action
        $this->authorize('update', $service);

        // Validation
        $validatedData = $request->validate($this->validationRules());

        // Updating the service
        $service->update([
            'name' => $validatedData['name'],
            'bio' => $validatedData['bio'] ?? null,
            'email' => $validatedData['email'] ?? null,
            'phone' => $validatedData['phone'] ?? null,
            'external' => $validatedData['external'] ?? null,
            'opening_time' => $validatedData['opening_time'],
            'closing_time' => $validatedData['closing_time'],
            'status' => $validatedData['status'],
            'price' => $validatedData['price'] ?? null
        ]);

        return redirect()->back()->with('success', 'Service has been updated');
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(Service $service): RedirectResponse
    {
        // Ensure the user belongs to the same tenant as the current user and has the
        // privilege to make this operation based on this specific user's role
        $this->authorize('delete', $service);
        $service->delete();
        return redirect()->route("appointments.services.index")->with('success', 'Service deleted successfully!');
    }

    // Private method for validation rules
    private function validationRules(): array
    {
        return [
            'name' => 'required|string|max:64',
            'bio' => 'nullable|string|max:300',
            'email' => 'nullable|email',
            'phone' => 'nullable|regex:/^\d{10,15}$/',
            'external' => ['nullable', 'regex:/^https:\/\/pikarennfc\.com\/nfc\/[\w\-]+$/'],
            'opening_time' => 'required|date_format:H:i:s',
            'closing_time' => 'required|date_format:H:i:s|after_or_equal:opening_time',
            'status' => 'required|string|in:active,inactive', // Adjust as necessary
            'price' => 'nullable|numeric|between:0,999999.99',
        ];
    }
}
