<?php

namespace App\Http\Controllers;

use App\Asset;
use App\AssetCategory;
use App\AssetLocation;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::with(['category', 'location', 'user'])->get();
        return view('assets.index', compact('assets'));
    }

    public function categories()
    {
        $categories = AssetCategory::all();

        return view('assets.categories',compact('categories'));
    }

    public function locations()
    {
        $locations = AssetLocation::all();

        return view('assets.locations',compact('locations'));
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        AssetCategory::create($validated);

        return redirect()->route('assets.categories')->with('success', 'Asset Category created successfully!');
    }

    public function updateCategory(Request $request,$id)
    {
        $update = DB::table('tbl_assets_categories')
            ->where('id',$id)
            ->update([
                'name'=>$request->name,
                'updated_at'=>now()
            ]);

        return redirect()->route('assets.categories')->with('success', 'Asset Category updated successfully!');
    }

    public function storeLocation(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address'=>'required'
        ]);

        AssetLocation::create($validated);

        return redirect()->route('assets.location')->with('success', 'Location created successfully!');
    }

    public function updateLocation(Request $request,$id)
    {
        $update = DB::table('tbl_assets_locations')
            ->where('id',$id)
            ->update([
                'name'=>$request->name,
                'address'=>$request->address,
                'updated_at'=>now()
            ]);

        return redirect()->route('assets.categories')->with('success', 'Location updated successfully!');
    }

    public function create()
    {
        $categories = AssetCategory::all();
        $locations = AssetLocation::all();
        $users = User::all();
        return view('assets.create', compact('categories', 'locations', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'assigned_user_id' => 'nullable|exists:users,id',
            'value' => 'nullable|numeric',
            'purchase_date' => 'nullable|date',
            'status' => 'required|string',
        ]);

        Asset::create($validated);
        return redirect()->route('assets.index')->with('success', 'Asset created successfully!');
    }

    public function edit(Asset $asset)
    {
        $categories = AssetCategory::all();
        $locations = AssetLocation::all();
        $users = User::all();
        return view('assets.edit', compact(['asset', 'categories', 'locations', 'users']));
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'assigned_user_id' => 'nullable|exists:users,id',
            'value' => 'nullable|numeric',
            'purchase_date' => 'nullable|date',
            'status' => 'required|string',
        ]);

        $asset->update($validated);
        return redirect()->route('assets.index')->with('success', 'Asset updated successfully!');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();
        return redirect()->route('assets.index')->with('success', 'Asset deleted successfully!');
    }
}
