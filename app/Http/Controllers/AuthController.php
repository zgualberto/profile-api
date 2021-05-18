<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Services\ConfirmCodeService;
use App\Services\UserService;
use Mail;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\ImageManagerStatic as Image;

class AuthController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->confirmCodeService = new ConfirmCodeService();
        $this->userService = new UserService();
    }

    /**
     * Invite user.
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function invite(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users'
        ]);

        $this->userService->create([
            'email' => $request->input('email'),
            'user_role' => ($request->input('user_role') ? $request->input('user_role') : 'user')
        ]);

        Mail::send([], [], function (Message $message) use ($request) {
            $message->to($request->input('email'))
                ->subject('Welcome to this portal!')
                ->from('no-reply@sample.com')
                ->setBody("Click on this link to continue registration \n <a href='" . env('APP_REGISTER_FORM_LINK', '[no_link]') . "?email=" . urlencode($request->input('email')) . "' target='_blank'>Link</a>", 'text/html');
        });

        return response()->json([
            'message' => 'Invitation sent to ' . $request->input('email')
        ], 200);
    }

    /**
     * Login user.
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $request->validate([
            'password' => 'required',
            'user_name' => 'required'
        ]);
        
        $user = $this->userService->find([
            'user_name' => $request->input('user_name')
        ]);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => "Cannot find that user"
            ], 401);
        }

        if (!$user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => "You haven't verified your email"
            ], 401);
        }

        if (!Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect password'
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $token
        ], 200);
    }

    /**
     * Continue registration of user.
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'user_name' => 'required',
            'password' => 'required'
        ]);

        $user = $this->userService->find([
            'email' => $request->input('email')
        ]);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => "Cannot find that email",
                'inputs' => $request->all()
            ], 400);
        }

        if ($user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => "User has already been registered",
                'inputs' => $request->all()
            ], 400);
        }

        $this->userService->update([
            'id' => $user->id
        ], [
            'user_name' => $request->input('user_name'),
            'password' => Hash::make($request->input('password'))
        ]);

        $code = mt_rand(100000, 999999);
        $this->confirmCodeService->create([
            'email' => $request->input('email'),
            'confirm_code' => $code
        ]);

        Mail::send([], [], function (Message $message) use ($request, $code) {
            $message->to($request->input('email'))
                ->subject('Please confirm your email')
                ->from('no-reply@sample.com')
                ->setBody("Confirmation code: " . $code, 'text/html');
        });

        return response()->json([
            'message' => 'Please verify email. Confirmation code sent to ' . $request->input('email')
        ], 200);
    }

    /**
     * Confirm email.
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'confirm_code' => 'required|integer',
        ]);

        $confirm = $this->confirmCodeService->find([
            'email' => $request->input('email'),
            'confirm_code' => $request->input('confirm_code'),
            'active' => true
        ]);

        if (!$confirm) {
            return response()->json([
                "success" => false,
                "message" => 'Invalid inputs',
                "inputs" => $request->all()
            ], 400);
        }

        $this->userService->update([
            'email' => $request->input('email')
        ], [
            'email_verified_at' => Carbon::now()
        ]);

        $this->confirmCodeService->update([
            'email' => $request->input('email')
        ], [
            'active' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Email successfully verified'
        ], 200);
    }

    /**
     * Confirm email.
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        if (!$request->input('name') && !$request->input('avatar')) {
            return response()->json([
                'success' => true,
                'message' => "Nothing to update",
                'inputs' => $request->all()
            ], 200);
        }

        if ($request->input('avatar')) {
            $img = Image::make(imagecreatefrompng($request->input('avatar')));
            $img->resize(256, 256);
            $img->save(public_path() . '/avatar/' . auth()->user()->email . '.png');

            $this->userService->update([
                'id' => auth()->user()->id
            ], [
                'avatar' => request()->getSchemeAndHttpHost() . '/avatar/' . auth()->user()->email . '.png'
            ]);
        }

        if ($request->input('name')) {
            $this->userService->update([
                'id' => auth()->user()->id
            ], [
                'name' => $request->input('name') 
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "User's profile was successfully updated",
            'user' => 
            'inputs' => $request->all()
        ], 200);
    }
}
