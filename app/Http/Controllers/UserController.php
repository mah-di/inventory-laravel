<?php

namespace App\Http\Controllers;

use App\Helpers\JWTHelper;
use App\Mail\OTPMail;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{
    public function register(Request $request)
    {
        try
        {
            $validData = $request->validate([
                'firstName' => ['required', 'string', 'max:50'],
                'lastName' => ['required', 'string', 'max:50'],
                'email' => ['required', 'email', 'max:50', 'unique:'.User::class],
                'contact' => ['nullable', 'numeric', 'unique:'.User::class],
                'password' => ['required', 'string', 'min:8'],
            ]);

            $otp = rand(1000, 9999);

            $user = User::create([
                'firstName' => $validData['firstName'],
                'lastName' => $validData['lastName'],
                'email' => $validData['email'],
                'contact' => $validData['contact'],
                'password' => $validData['password'],
                'otp' => $otp,
            ]);

            Mail::to($user->email)->send(new OTPMail($otp));

            $token = JWTHelper::generateToken($user->email, $user->id);

            return response()->json([
                'status' => 'success',
                'message' => 'Registration Successful. 4 Digit OTP Code Has Been Sent To Your Email.',
                'token' => $token
            ], 200)
            ->cookie('token', $token, 60*24*180);

        } catch (Exception $exception)
        {

            return response()->json([
                'status' => 'fail',
                'message' => $exception->getMessage()
            ], 200);

        }
    }

    public function verifyEmail(Request $request)
    {
        try {
            $request->validate([
                'otp' => ['required', 'min:4', 'max:4']
            ]);

            $id = $request->header('id');
            $email = $request->header('email');
            $verifiedAt = now();

            $result = User::where([
                    'id' => $id,
                    'email' => $email,
                    'otp' => $request->input('otp'),
                ])
                ->update([
                    'otp' => 0,
                    'verified_at' => $verifiedAt,
                ]);

            if (!$result) throw new Exception("Invalid OTP Code");

            $token = JWTHelper::generateToken($email, $id, $verifiedAt);

            return response()->json([
                    'status' => 'success',
                    'message' => 'Email Verified.',
                    'token' => $token
                ], 200)
                ->cookie('token', $token, 60*24*180);

        } catch (Exception $exception) {

            return response()->json([
                'status' => 'fail',
                'message' => $exception->getMessage()
            ], 200);

        }
    }

    public function resendEmailVerificationOTP(Request $request)
    {
        try {
            $email = $request->header('email');
            $otp = rand(1000, 9999);

            User::where('email', '=', $email)
                ->update([
                    'otp' => $otp
                ]);

            Mail::to($email)->send(new OTPMail($otp));

            return response()->json([
                'status' => 'success',
                'message' => '4 Digit Code Has Been Sent To Your Email.',
            ], 200);

        } catch (Exception $exception) {

            return response()->json([
                'status' => 'fail',
                'message' => $exception->getMessage()
            ], 200);

        }

    }

    public function login(Request $request)
    {
        try {
            $validData = $request->validate([
                'email' => ['required', 'string', 'email'],
                'password' => ['required', 'string', 'min:8'],
            ]);

            $user = User::where('email', $validData['email'])->first();

            if (!$user or !Hash::check($validData['password'], $user->password))
            {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Credentials do not match.'
                ], 200);
            }

            $token = JWTHelper::generateToken($user->email, $user->id, $user->verified_at);

            return response()->json([
                'status' => 'success',
                'message' => 'Login Successful.',
                'token' => $token
            ], 200)
            ->cookie("token", $token, 60*24*180);

        } catch (Exception $exception) {

            return response()->json([
                'status' => 'fail',
                'message' => $exception->getMessage()
            ], 200);

        }
    }

    public function sendOTP(Request $request)
    {
        try {
            $request->validate([
                'email' => ['required', 'string', 'email', 'exists:users,email']
            ]);

            $email = $request->input('email');

            $otp = rand(1000, 9999);

            User::where('email', $email)
                ->update([
                    'otp' => $otp
                ]);

            Mail::to($email)->send(new OTPMail($otp));

            return response()->json([
                'status' => 'success',
                'message' => '4 digit OTP code has been sent to your email.'
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'status' => 'fail',
                'message' => $exception->getMessage()
            ], 200);
        }
    }

    public function verifyOTP(Request $request)
    {
        try {
            $request->validate([
                'otp' => ['required', 'min:4', 'max:4']
            ]);

            $user = User::where([
                    'email' => $request->input('email'),
                    'otp' => $request->input('otp'),
                ])
                ->first();

            if(!$user) throw new Exception("Invalid OTP Code.");

            $user->otp = 0;
            $user->verified_at = $user->verified_at ?? now();
            $user->save();

            $token = JWTHelper::generateToken($user->email, $user->id, $user->verified_at, 'password.reset.token');

            return response()->json([
                    'status' => 'success',
                    'message' => 'OTP verification successful.',
                    'token' => $token
                ])
                ->cookie('token', $token, 10);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'fail',
                'message' => $exception->getMessage()
            ]);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'password' => ['required', 'string', 'min:8']
            ]);

            $user = User::find($request->header('id'));

            $user->password = $request->input('password');

            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'New password saved.'
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'status' => 'fail',
                'message' => $exception->getMessage()
            ]);
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'password' => ['required', 'string', 'min:8'],
                'new_password' => ['required', 'string', 'min:8'],
            ]);

            $user = User::find($request->header('id'));

            if (!Hash::check($request->input('password'), $user->password)) throw new Exception("Wrong password");

            User::find($user->id)->update([
                'password' => $request->input('new_password')
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Password changed successfully.'
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'status' => 'fail',
                'message' => $exception->getMessage()
            ]);
        }
    }

    public function update(Request $request)
    {
        try {
            $id = $request->header('id');

            $validData = $request->validate([
                'firstName' => ['required', 'string', 'max:50'],
                'lastName' => ['required', 'string', 'max:50'],
                'contact' => ['nullable', 'numeric', 'unique:users,contact,'.$id],
            ]);

            User::find($id)
            ->update($validData);

            return response()->json([
                'status' => 'success',
                'message' => 'Profile information updated.'
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'status' => 'fail',
                'message' => $exception->getMessage()
            ]);
        }
    }

    public function getUser(Request $request)
    {
        try {
            $user = User::find($request->header('id'));

            return response()->json([
                'status' => 'success',
                'user' => $user
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'status' => 'fail',
                'message' => $exception->getMessage()
            ]);
        }
    }

    public function getSummary(Request $request)
    {
        try {
            $userID = $request->header('id');

            $data = [];

            $data['categoryCount'] = Category::where('user_id', $userID)->count();
            $data['productCount'] = Product::where('user_id', $userID)->count();
            $data['customerCount'] = Customer::where('user_id', $userID)->count();
            $data['invoiceCount'] = Invoice::where('user_id', $userID)->count();
            $data['totalSale'] = Invoice::where('user_id', $userID)->sum('payable');
            $data['vatCollected'] = Invoice::where('user_id', $userID)->sum('vat');
            $data['totalRevenue'] = $data['totalSale'] - $data['vatCollected'];

            return response()->json([
                'status' => 'success',
                'data' => $data,
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'status' => 'success',
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public function logout(Request $request)
    {
        return Redirect::route('login.view')->cookie('token', '', -1);
    }

}
