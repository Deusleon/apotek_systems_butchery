<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class LoginController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
    * Handle a login request to the application.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
    *
    * @throws \Illuminate\Validation\ValidationException
    */

    public function login( Request $request ) {

        session()->put( 'db_connection', 'demo' );

        $this->validateLogin( $request );

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }


    protected function authenticated(Request $request, $user)
    {
        // Example you already set db_connection earlier in login() — keep or move here if you prefer
        // session()->put('db_connection', 'demo');

        // Ensure user has a store relationship
        $store = null;
        if ($user->relationLoaded('store')) {
            $store = $user->store;
        } else {
            // attempt to load it safely
            $store = $user->store()->first();
        }

        // If no store assigned, you may decide on default behaviour
        if (!$store) {
            // Option: set to null or a fallback id
            session([
                'current_store_id' => null,
                'store' => null,
            ]);
            return null;
        }

        // If store name is 'ALL' (DB shows id = 1 for ALL), set special value.
        // Be consistent: we'll use id = 1 to represent ALL ( same as your DB )
        if ( strtoupper( $store->name ) === 'ALL' || $store->id == 1 ) {
            session( [
                'current_store_id' => 1,
                'store' => 'ALL',
            ] );
        } else {
            session( [
                'current_store_id' => $store->id,
                'store' => $store->name,
            ] );
        }

        return null;
    }
    /**
    * Override logout to clear session completely.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

    public function logout( Request $request ) {
        $this->guard()->logout();

        // Clear the session completely ( invalidate + regenerate token )
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect( '/login' );
    }

    /**
    * Get the needed authorization credentials from the request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return array
    */
    protected function credentials( Request $request ) {
        return [ 'email' => $request-> {
            $this->username()}
            , 'password' => $request->password, 'status' => 1 ];
        }

        /**
        * Where to redirect users after login.
        *
        * @var string
        */
        protected $redirectTo = '/home';

        /**
        * Create a new controller instance.
        *
        * @return void
        */

        public function __construct() {
            $this->middleware( 'guest' )->except( 'logout' );
        }
    }
