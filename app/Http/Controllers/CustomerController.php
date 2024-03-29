<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class CustomerController extends Controller
{

    public function all(Request $request)
    {
        try {
            $ownerID = $request->header('ownerID');

            $customers = Customer::where('user_id', '=', $ownerID)->get();

            return response()->json([
                'status' => 'success',
                'data' => $customers
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
            $ownerID = $request->header('ownerID');

            $customer = Customer::where([
                    'id' => $id,
                    'user_id' => $ownerID,
                ])->first();

            return response()->json([
                'status' => 'success',
                'data' => $customer
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
                'name' => ['required', 'string', 'min:3'],
                'email' => ['nullable', 'email', 'unique:customers'],
                'contact' => ['required', 'digits_between:11,15', 'unique:customers'],
            ]);

            $result = Customer::create([
                ...$validData,
                'user_id' => $request->header('ownerID')
            ]);

            if ($result === null) throw new Exception("Uxpected error occured, couldn't create customer.");

            return response()->json([
                'status' => 'success',
                'message' => 'Customer created successfully.',
                'data' => $result
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
            $id = $request->input('id');

            $validData = $request->validate([
                'name' => ['required', 'string', 'min:3'],
                'email' => ['nullable', 'email', 'unique:customers,email,'.$id],
                'contact' => ['required', 'digits_between:11,15', 'unique:customers,contact,'.$id],
            ]);

            Customer::where([
                    'id' => $id,
                    'user_id' => $request->header('ownerID')
                ])->update($validData);

            return response()->json([
                'status' => 'success',
                'message' => 'Customer updated successfully.',
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
            Customer::where([
                    'id' => $request->input('id'),
                    'user_id' => $request->header('ownerID')
                ])->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Customer deleted.',
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

}
