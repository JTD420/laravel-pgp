<?php

namespace App\Http\Controllers\PGP;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PGP\PGPcontroller;
use App\Models\PGP\Key;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;

class AuthenticationController extends Controller
{
    public function create()
    {
        return view('PGP::auth.signup');
    }

    public function store(Request $request, Validator $validator)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:15|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $prefix = config('PGP.prefix');

        if ($validator->fails()) {
            return redirect("$prefix.signup")
                ->withErrors($validator)
                ->withInput();
        }

        $user = new User;
        $user->name = $request->input('name');
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->save();

        // Will use to generate Pub/Priv Keypair later
        $name = $request->input('name');
        $email = $request->input('email');
        $passphrase = $request->input('password');

        // Log the user in
        Auth::login($user);
        $controller = new PGPcontroller();
        $keypair = $controller->generate_keypair($name, $email, $passphrase);
        $public_key = $keypair['public_key'];
        $private_key = $keypair['private_key'];
        $key = new Key;
        $key->public_key = $public_key;
        $key->private_key = $private_key;
        // Set the user_id field to the id of the newly created user
        $key->user_id = $user->id;
        // Save the key to the database
        $key->save();

        return redirect()->route("$prefix.conversations.index");
    }

    public function login()
    {
        return view('PGP::auth.login');
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function findUsername()
    {
        $login = request()->input('login');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        request()->merge([$fieldType => $login]);

        return $fieldType;
    }

    public function authenticate(Request $request, Validator $validator)
    {
        $validator = $validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        $prefix = config('PGP.prefix');

        // Check if the form data is valid
        if ($validator->fails()) {
            // If form data is invalid, redirect the user back with the errors.
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Get the login field type - email or username
        $fieldType = $this->findUsername();

        // Attempt to log the user in
        if (Auth::attempt([
            $fieldType => $request->login,
            'password' => $request->password,
        ])) {
            // If the login is successful, redirect the user to the appropriate dashboard
            $user = Auth::user();

            return redirect()->route("$prefix.conversations.index");
        } else {
            // If the login is unsuccessful, redirect the user back with an error message
            return redirect()->back()->withErrors([
                'message' => 'Login unsuccessful. Please try again.',
            ])->withInput();
        }
    }

    public function logout()
    {
        auth()->logout();
        $prefix = config('PGP.prefix');

        return redirect()->route("$prefix.login");
    }
}
