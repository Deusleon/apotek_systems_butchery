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
        $transporters = Transporter::all();
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

        session()->flash("alert-success", "Transporter added successfully!");
        return back();
    }

    public function show(Vehicle $vehicle)
    {
        return view('vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle)
    {
        $transporters = Transporter::all();
        return view('vehicles.edit', compact('vehicle', 'transporters'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'plate_number' => 'required|unique:vehicles,plate_number,'.$vehicle->id,
            'transporter_id' => 'required|exists:transporters,id',
            'vehicle_type' => 'required',
            'capacity' => 'required|numeric',
        ]);

        $vehicle->update($request->all());
        
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

        session()->flash("alert-success", "Vehicle updated successfully!");
        return redirect()->route('vehicles.index');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        session()->flash("alert-success", "Vehicle deleted successfully!");
        return redirect()->route('vehicles.index');
    }
}