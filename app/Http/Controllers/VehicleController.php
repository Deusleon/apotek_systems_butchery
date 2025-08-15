<?php

namespace App\Http\Controllers;

use App\Vehicle;
use App\Transporter;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with('transporter')->get();
        $transporters = Transporter::all(); 
        return view('vehicles.index', compact('vehicles','transporters'));
    }

    public function create()
    {
        // This will order by name after trimming whitespace, case-insensitive
        $transporters = Transporter::orderByRaw('LOWER(TRIM(name)) ASC')->get();
        
        return view('vehicles.create', compact('transporters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'plate_number' => 'required|unique:vehicles',
            'transporter_id' => 'required|exists:transporters,id',
            'vehicle_type' => 'required',
            'capacity' => 'required|numeric',
        ]);

        $vehicle = Vehicle::create($request->all());
        
        // Handle document uploads if any
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('vehicle_documents');
                $vehicle->documents()->create([
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'type' => $file->getClientMimeType()
                ]);
            }
        }

        session()->flash("alert-success", "Vehicle added successfully!");
        return redirect()->route('vehicles.index');

    }

    public function show(Vehicle $vehicle)
    {
        return view('vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle)
    {
        $transporters = Transporter::orderByRaw('LOWER(TRIM(name)) ASC')->get();
        return view('vehicles.edit', compact('vehicle', 'transporters'));
    }


    public function update(Request $request, Vehicle $vehicle)
{
    $request->validate([
        'plate_number' => 'required|unique:vehicles,plate_number,'.$vehicle->id,
        'transporter_id' => 'required|exists:transporters,id',
        'vehicle_type' => 'required',
        'capacity' => 'required|numeric',
        'make' => 'sometimes|required',
        'model' => 'sometimes|required',
        'year' => 'sometimes|required|integer',
        'color' => 'sometimes|required',
        'status' => 'sometimes|required',
        'chassis_number' => 'sometimes|nullable',
        'engine_number' => 'sometimes|nullable',
        'fitness_expiry' => 'sometimes|nullable|date',
        'insurance_expiry' => 'sometimes|nullable|date',
        'permit_expiry' => 'sometimes|nullable|date',
        'notes' => 'sometimes|nullable',
    ]);

    $vehicle->update($request->except('documents'));
    
    // Handle document uploads
    if ($request->hasFile('documents')) {
        foreach ($request->file('documents') as $file) {
            $path = $file->store('vehicle_documents', 'public');
            $vehicle->documents()->create([
                'name' => $file->getClientOriginalName(),
                'path' => str_replace('public/', '', $path),
                'type' => $file->getClientMimeType()
            ]);
        }
    }

    return redirect()->route('vehicles.index')
        ->with('alert-success', 'Vehicle updated successfully!');
}


    // public function update(Request $request, Vehicle $vehicle)
    // {
    //     $request->validate([
    //         'plate_number' => 'required|unique:vehicles,plate_number,'.$vehicle->id,
    //         'transporter_id' => 'required|exists:transporters,id',
    //         'vehicle_type' => 'required',
    //         'capacity' => 'required|numeric',
    //     ]);

    //     $vehicle->update($request->all());
        
    //     // Handle document uploads if any
    //     if ($request->hasFile('documents')) {
    //         foreach ($request->file('documents') as $file) {
    //             $path = $file->store('vehicle_documents');
    //             $vehicle->documents()->create([
    //                 'name' => $file->getClientOriginalName(),
    //                 'path' => $path,
    //                 'type' => $file->getClientMimeType()
    //             ]);
    //         }
    //     }

    //     session()->flash("alert-success", "Vehicle updated successfully!");
    //     // In your controller
    //     return redirect()->route('vehicles.index')
    //     ->with('alert-success', 'Vehicle added successfully!');
    //         }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        session()->flash("alert-success", "Vehicle deleted successfully!");
        return redirect()->route('vehicles.index');
    }
}