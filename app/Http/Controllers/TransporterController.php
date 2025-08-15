<?php

namespace App\Http\Controllers;

use App\Transporter;
use App\TransportOrder;
use Illuminate\Http\Request;

class TransporterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transporters = Transporter::all();
        return view('transporters.index', compact('transporters'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('transporters.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'registration_number' => 'required|string|max:255|unique:transporters,registration_number',
            'email' => 'required|email|unique:transporters,email',
            'phone' => 'required|string|max:20',
        ]);

        Transporter::create($request->all());

        session()->flash('alert-success', 'Transporter added successfully!');

        return redirect()->route('transport-logistics.transporters.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transporter  $transporter
     * @return \Illuminate\Http\Response
     */
    public function show(Transporter $transporter)
    {
        return view('transporters.show', compact('transporter'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Transporter  $transporter
     * @return \Illuminate\Http\Response
     */
    public function edit(Transporter $transporter)
    {
        return view('transporters.edit', compact('transporter'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transporter  $transporter
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transporter $transporter)
{
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'contact_person' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'email' => 'nullable|email|max:255',
        'transport_type' => 'required|in:road,rail,air,sea',
        'number_of_vehicles' => 'required|integer|min:1',
        'status' => 'required|in:active,inactive',
        'notes' => 'nullable|string',
    ]);

    $transporter->update($validatedData);

    return redirect()->route('transport-logistics.transporters.index')
        ->with('success', 'Transporter updated successfully!');
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transporter  $transporter
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transporter $transporter)
    {
        $transporter->delete();
        return redirect()->route('transporters.index');
    }

    /**
     * Display the specified transport order.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showOrder($id)
    {
        $order = TransportOrder::findOrFail($id);
        return response()->json($order);
    }

    /**
     * Show the form for editing the specified transport order.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editOrder($id)
    {
        $order = TransportOrder::findOrFail($id);
        return response()->json($order);
    }
}