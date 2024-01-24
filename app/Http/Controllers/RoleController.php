<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class RoleController extends Controller
{

    public function all(Request $request)
    {
        try {
            $userId = $request->header('id');

            $roles = Role::where('user_id', '=', $userId)
                ->with(['users'])
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $roles
            ]);

        } catch (QueryException $exception) {
            return response()->json([
                'status' => 'fail',
                'message' => null
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
            $userId = $request->header('id');

            $role = Role::where([
                    'id' => $id,
                    'user_id' => $userId,
                ])
                ->with(['users'])
                ->first();

            if (!$role) throw new Exception("404 Not Found.");

            return response()->json([
                'status' => 'success',
                'data' => $role
            ]);

        } catch (QueryException $exception) {
            return response()->json([
                'status' => 'fail',
                'message' => null
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'fail',
                'message' => $exception->getMessage()
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            $validData = $request->validate([
                'name' => ['required', 'string', 'min:2'],
                'slug' => ['required', 'alpha', 'min:2', 'not_in:owner,manager,editor,cashier']
            ]);

            $result = Role::create([
                ...$validData,
                'user_id' => $request->header('id')
            ]);

            if ($result === null) throw new Exception("Uxpected error occured, couldn't create role.");

            return response()->json([
                'status' => 'success',
                'message' => 'Role created successfully.',
            ]);

        } catch (QueryException $exception) {
            return response()->json([
                'status' => 'fail',
                'message' => null
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
            $validData = $request->validate([
                'name' => ['required', 'string', 'min:2'],
                'slug' => ['required', 'alpha', 'min:2'],
            ]);

            $role = Role::where([
                    'id' => $request->input('id'),
                    'user_id' => $request->header('id')
                ])->first();

            if ($role->slug === 'owner' and $validData['slug'] !== 'owner') throw new Exception("Can't update slug for role \"owner\".");

            if ($role->slug !== 'owner' and $validData['slug'] === 'owner') throw new Exception("Can't update, \"owner\" is a reserved slug.");

            if ($role->slug === 'manager' and $validData['slug'] !== 'manager') throw new Exception("Can't update slug for role \"manager\".");

            if ($role->slug !== 'manager' and $validData['slug'] === 'manager') throw new Exception("Can't update, \"manager\" is a reserved slug.");

            if ($role->slug === 'editor' and $validData['slug'] !== 'editor') throw new Exception("Can't update slug for role \"editor\".");

            if ($role->slug !== 'editor' and $validData['slug'] === 'editor') throw new Exception("Can't update, \"editor\" is a reserved slug.");

            if ($role->slug === 'cashier' and $validData['slug'] !== 'cashier') throw new Exception("Can't update slug for role \"cashier\".");

            if ($role->slug !== 'cashier' and $validData['slug'] === 'cashier') throw new Exception("Can't update, \"cashier\" is a reserved slug.");

            $role->update($validData);

            return response()->json([
                'status' => 'success',
                'message' => 'Role updated successfully.',
            ]);

        } catch (QueryException $exception) {
            return response()->json([
                'status' => 'fail',
                'message' => null
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
            $role = Role::where([
                    'id' => $request->input('id'),
                    'user_id' => $request->header('id')
                ])->first();

            if ($role->slug === 'owner') throw new Exception("Can't delete role \"owner\".");

            $role->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Role deleted.',
            ]);

        } catch (QueryException $exception) {
            return response()->json([
                'status' => 'fail',
                'message' => null
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'fail',
                'message' => $exception->getMessage()
            ]);
        }
    }

    public function getRoles(Request $request)
    {
        $roles = json_decode($request->header('roles'));

        return response()->json($roles);
    }

    public function getAssignableRoles(Request $request)
    {
        try {
            $roles = Role::where('user_id', $request->header('id'))
                ->whereNotIn('id', $request->input('roleIds'))
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $roles
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'status' => 'fail',
                'message' => $exception->getMessage()
            ]);
        }
    }

}

