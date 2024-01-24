<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Exception;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{

    public function store(Request $request)
    {
        try {
            foreach ($request->input('roleIds') as $roleID) {
                $role = Role::where([
                    'id' => $roleID,
                    'user_id' => $request->header('id'),
                ])->first();

                if (!$role->users()->find($request->input('employee_id')))
                    $role->users()->attach([$request->input('employee_id')]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Employee role assigned.'
            ]);
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
            foreach ($request->input('roleIds') as $roleID) {
                $role = Role::where([
                    'id' => $roleID,
                    'user_id' => $request->header('id'),
                ])->first();

                $role->users()->detach([$request->input('employee_id')]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Employee role removed.'
            ]);
        } catch (Exception $exception) {

            return response()->json([
                'status' => 'fail',
                'message' => $exception->getMessage()
            ]);
        }
    }

}
