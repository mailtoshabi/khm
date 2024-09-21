<?php

namespace App\Http\Controllers\CustomerAuth;

use  App\Models\Website\Customer;
use  App\Models\Website\CustomerDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
//Class needed for login and Logout logic
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Utilities\Utility;
use Laravel\Socialite\Facades\Socialite;
use \Cart;

class LoginController extends Controller
{

    //Where to redirect seller after login.
    protected $redirectTo = '/';

    use AuthenticatesUsers;
    //TODO : issue with logout - if a customer session expired due to idle or click logout after expiration.
    protected function credentials(Request $request)
    {
        /*$field = filter_var($request->get($this->username()), FILTER_VALIDATE_EMAIL)
            ? $this->username()
            : 'phone';*/
        $field = $this->username();

        return [
            $field => $request->get($this->username()),
            'password' => $request->password, 'status' => 1, 'is_active' => 1
        ];
    }

    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user())
            ?: response()->json();
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $errors = [$this->username() => trans('auth.customer_failed')];

        // Load user from database
        $user = Customer::where($this->username(), $request->{$this->username()})->first();

        // Check if user was successfully loaded, that the password matches
        // and active is not 1. If so, override the default error message.
        if ($user && Hash::check($request->password, $user->password) && $user->is_active != 1) {
            $errors = [$this->username() => trans('auth.suspended')];
        }

        if ($user && Hash::check($request->password, $user->password) && $user->status == 0) {
            $errors = [$this->username() => trans('auth.notactivated')];
        }

        if ($request->expectsJson()) {
            return response()->json($errors, 422);
        }

        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors($errors);
    }

    public function username()
    {
        return 'phone';
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect($this->redirectTo);
    }

    protected function guard()
    {
        return Auth::guard('customer');
    }

    //Shows customer login form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        if($request->has('password') && !empty($request->password)) {
            $this->validateLogin($request);
        }
        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if($request->has('set_password') && !empty($request->set_password)) {
            return $this->create($request->all());
        }else {
            if ($this->attemptLogin($request)) {
                return $this->sendLoginResponse($request);
            }
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    protected function check_customer(Request $request)
    {
        $customer = Customer::where('phone', $request->phone)->first();
        if ($customer) {
            return response()->json(['is_exist'=>1]);
        }else {
            $customer = new Customer;

            $customer->fill([
                /*'email' => $data['email'],*/
                'phone' => $request->phone,
                'password' => bcrypt(Utility::TEMP_PWD)
            ]);
            $customer->save();

            $customer_detail = new CustomerDetail;
            $customer_detail->fill([
                'customer_id' => $customer->id,
                'address' => [],
                'profile_pic' => '',
            ]);
            $customer_detail->save();
            return response()->json(['is_exist'=>0]);
        }
    }

        protected function create(array $data)
    {
        $phone = Session::get('kerala_h_m_cust_phone');
        $otp = Session::get('kerala_h_m_o_t_p');
        $validator = $this->validator($data);

        if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
        }else {
            if (($phone == $data['phone'])) {
                if(($otp == $data['nw_otp'])) {


                    $customer = Customer::where('phone', $phone)->first();
                    if ($customer) {
                        $customer->fill(['password' => bcrypt($data['set_password']), 'status'=>1, 'is_active'=>1])->save();
                        //TODO : code for autheticate user

                        //Authenticates customer
                        $this->guard()->login($customer);
                        //Redirects customer
//                return redirect($this->redirectPath);
                        return response()->json();

                        /*return $customer;*/
                    } else {
                        $validator->getMessageBag()->add("phone", "Customer doesn't exists");
                        return response()->json($validator->errors(), 422);
                    }
                }
                else {
                    $validator->getMessageBag()->add("nw_otp", "Invalid OTP");
                    return response()->json($validator->errors(), 422);
                }



            } else {
                // if phone and otp doesn't match.
                $validator->getMessageBag()->add("phone", "Something went wrong");
                return response()->json($validator->errors(), 422);
            }
        }

    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'phone' => 'required|max:255',
            'nw_otp' => 'required|min:6|max:6',
            'set_password' => 'required|min:6'
        ]);

    }

    public function redirectToGmail()
    {
        return Socialite::driver('google')->redirect();
    }
    public function handleGmailCallback()
    {
        // $user = Socialite::driver('google')->user();
        $customer = Socialite::driver('google')->user();

        $authCustomer = $this->findOrCreateUser($customer, 'google');
        // Auth::login($authUser, true);
        $this->guard()->login($authCustomer);

        if(\Cart::isEmpty()) {
            return redirect($this->redirectTo);
        }else {
            return redirect()->route('product.cart');
        }
        // return response()->json($customer);
    }

    public function findOrCreateUser($googleUser, $provider)
    {
        $authUser = Customer::where('email', $googleUser->email)->first();

        if ($authUser) {
            return $authUser;
        }

        $customer = new Customer;

        $customer->fill([
            'email' => $googleUser->email,
            'provider' => $provider,
            'provider_id' => $googleUser->id,
            'status' => 1,
            'is_active' => 1,
            'is_access' => 1,
            // 'phone' => $data['phone'],
            // 'password' => bcrypt($data['password'])
        ]);
        $customer->save();

        $customer_detail = new CustomerDetail;
        $customer_detail->fill([
            'customer_id' => $customer->id,
            'name' => $googleUser->name,
            'address' => [],
            'profile_pic' => $googleUser->avatar,
        ]);
        $customer_detail->save();

        return $customer;
    }


}
