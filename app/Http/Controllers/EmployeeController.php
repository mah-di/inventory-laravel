<?php

namespace App\Http\Controllers;

use App\Mail\OTPMail;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\InputBag;

class EmployeeController extends Controller
{

    public function index(Request $request)
    {
        try {
            $employees = User::where('owner_id', $request->header('id'))
                ->with(['roles'])
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $employees
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'fail',
                'message' => $exception->getMessage()
            ]);
        }
    }

    public function find(Request $request, string $id)
    {
        try {
            $employee = User::where([
                    'id' => $id,
                    'owner_id' => $request->header('id')
                ])
                ->with(['roles'])
                ->first();

            if (!$employee) throw new Exception("No Matching Employee Found.");

            return response()->json([
                'status' => 'success',
                'data' => $employee
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'fail',
                'message' => $exception->getMessage()
            ]);
        }
    }

    public function getRole(Request $request)
    {
        $validData = $request->validate([
            'role_id' => ['required', 'exists:roles,id']
        ]);

        $role = Role::where([
                'id' => $validData['role_id'],
                'user_id' => $request->header('ownerID')
            ])
            ->first();

        return $role;
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

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
                'owner_id' => $request->header('ownerID'),
            ]);

            foreach ($request->input('roles') as $role_id) {
                $request->setJson(new InputBag([
                    'role_id' => $role_id
                ]));

                $role = $this->getRole($request);

                $user->roles()->attach($role);
            }

            DB::commit();

            Mail::to($user->email)->send(new OTPMail($otp));

            return response()->json([
                'status' => 'success',
                'message' => 'Employee Registration Successful. Email Sent With 4 Digit OTP Code.'
            ], 200);

        } catch (Exception $exception) {
            return response()->json([
                'status' => 'fail',
                'message' => $exception->getMessage()
            ]);
        }
    }

    public function delete(Request $request)
    {
        try {
            User::where([
                    'id' => $request->input('id'),
                    'owner_id' => $request->header('id'),
                ])
                ->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Employee Deleted.'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'fail',
                'message' => $exception->getMessage()
            ]);
        }
    }

}
