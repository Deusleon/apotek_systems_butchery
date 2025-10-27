<?php

namespace App\Http\Controllers;

use App\Setting;
use App\User;
use App\Store;
use Spatie\Permission\Models\Role;
use Dompdf\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Traits\HasRoles;


class UserController extends Controller
{
    use HasRoles;


    public function index()
    {
        $users = User::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();
        $stores = Store::orderBy('name')->get();

        return view('users.index', compact('users', 'roles', 'stores'));
    }

    public function addPermission(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:50',
            'category' => 'required',
        ]);


        try{

            DB::table('permissions')
                ->insert([
                    'name'=>$request->name,
                    'guard_name'=>'web',
                    'created_at'=>now(),
                    'updated_at'=>now(),
                    'category'=>$request->category
                ]);

            Session::flash('success', 'Permission added successfully!');

            return redirect()->back();

        }catch (Exception $e)
        {
            Log::error('message',['ErrorAddingPermission'=>$e]);

            Session::flash('error', 'Oops something went wrong!');

            return redirect()->back();
        }catch (ValidationException $e) {

            Session::flash('danger', $e->errors()[0]);

            return redirect()->back();
        }
    }

    public function userActivities()
    {
        $activities = DB::table('activity_log')
            ->join('users','users.id','=','activity_log.causer_id')
            ->select('users.name','activity_log.subject_type as log_type','activity_log.created_at as log_date',
                'activity_log.description as data')
            ->get();


        return view('users.activities', compact("activities"));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:50',
                'position' => 'nullable|string|max:50',
                'role' => 'required|exists:roles,id',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
                'store_id' => 'required|exists:inv_stores,id',
            ]);

            $existingUser = User::where('name', $request->name)->count();
            if ($existingUser > 0) {
                session()->flash("alert-danger", "User with the same name already exists!");
                return back()->withInput();
            }

            $user = new User;
            $user->name = $request->name;
            $user->position = $request->position;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->status = '1';
            $user->password = Hash::make($request->password);
            $user->store_id = $request->store_id;
            $user->save();

            $role = Role::find($request->role);
            if ($role) {
                $user->syncRoles($role->name);
            }

            Log::info('User created successfully', ['user_id' => $user->id, 'email' => $user->email, 'created_by' => Auth::id()]);
            session()->flash("alert-success", "User created successfully!");
            return back();

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage() . ' at line ' . $e->getLine() . ' in ' . $e->getFile());
            session()->flash("alert-danger", "Failed to create user. Please try again.");
            return back();
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'name1' => 'required|string|max:100',
            'role1' => 'required',
            'position1' => 'nullable|string|max:50',
            'store_id' => 'required',
        ]);

        // $existingUser = User::where('name', $request->name1)->count();
        $existingUser = User::where('name', $request->name1)
            ->where('id', '!=', $request->UserID)
            ->count();
        if ($existingUser > 0) {
            session()->flash("alert-danger", "User with the same name already exists!");
            return back()->withInput();
        }

        $user = User::findOrFail($request->UserID);
        $user->name = $request->name1;
        $user->position = $request->position1;
        $user->mobile = $request->mobile1;
        $user->store_id = $request->store_id;
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
        if ($request->status == 0 || $request->status == -1) {
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

    public function passwordReset($email)
    {
        $token = csrf_token();
        return view('auth.passwords.admin_reset', compact('token', 'email'));
    }

    public function passwordResetUpdate(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        session()->flash("alert-success", "User password reset successfully!");
        return redirect()->route('users.index');
    }


    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Artisan::call('optimize:clear');    //Clears the application cache
//        Artisan::call('cache:clear');    //Clears the application cache
//        Artisan::call('config:clear');   //Clears the configuration cache
//        Artisan::call('route:clear');    //Clears the route cache
//        Artisan::call('view:clear');     //Clears the view cache

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }


    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return \Illuminate\Support\Facades\Auth::guard();
    }

}
