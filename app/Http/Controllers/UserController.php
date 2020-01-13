<?php

namespace App\Http\Controllers;

use App\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;


class UserController extends Controller
{
    use HasRoles;

    public function index()
    {
        $users = User::orderBy('name')->get();

        return view('users.index', compact("users"));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'position' => 'nullable|string|max:50',
            'role' => 'required',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);


        $user = new User;
        $user->name = $request->name;
        $user->position = $request->position;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->status = '-1';
        $user->password = Hash::make($request->password);
        $user->save();

        $user->syncRoles($request->role);

        session()->flash("alert-success", "User created successfully!");
        return back();
    }

    public function update(Request $request)
    {
        $request->validate([
            'name1' => 'required|string|max:100',
            'role1' => 'required',
            'position1' => 'nullable|string|max:50',
        ]);


        $user = User::findOrFail($request->UserID);
        $user->name = $request->name1;
        $user->position = $request->position1;
        $user->mobile = $request->mobile1;
        $user->save();

        $user->syncRoles($request->role1);

        session()->flash("alert-success", "User updated successfully!");
        return back();
    }


    public function deActivate(Request $request)
    {

        if ($request->status == 1) {
            $user = new User;
            $user = User::findOrFail($request->userid);
            $user->status = 0;
            $user->save();

            session()->flash("alert-success", "User de-activated successfully!");
            return redirect()->back();
        }
        if ($request->status == 0) {
            $user = new User;
            $user = User::findOrFail($request->userid);
            $user->status = 1;
            $user->save();

            session()->flash("alert-success", "User activated successfully!");
            return redirect()->back();
        }

    }

    public function getRoleID(Request $request)
    {
        $data = DB::table('roles')
            ->select('id')
            ->where('name', $request->role)
            ->get();

        return $data[0]->id;
    }


}
