<?php

namespace App\Http\Controllers;

use App\Location;
use Exception;
use Illuminate\Http\Request;

class LocationController extends Controller
{

    public function index()
    {
        $locations = Location::orderBy('id', 'ASC')->get();
        $count = $locations->count();

        return view('masters.locations.index', compact("locations", 'count'));
    }

    public function store(Request $request)
    {
        try {
            $location = new Location;
            $location->name = $request->name;
            $location->save();
            session()->flash("alert-success", "Location Added successfully!");
            return back();
        } catch (Exception $e) {
            session()->flash("alert-danger", "Location Name Exists!");
            return back();
        }
    }

    public function destroy(Request $request)
    {
        try {
            Location::destroy($request->id);
            session()->flash("alert-danger", "Location Deleted successfully!");
            return back();
        } catch (Exception $exception) {
            session()->flash("alert-danger", "Location in use!");
            return back();
        }

    }

    public function update(Request $request, $id)
    {
        $location = Location::find($request->id);
        $location->name = $request->name;
        try {
            $location->save();
            session()->flash("alert-success", "Location Updated successfully!");
            return back();
        } catch (Exception $exception) {
            session()->flash("alert-danger", "Location Name Exists!");
            return back();
        }
    }

}
