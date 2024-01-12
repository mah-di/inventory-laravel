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
            $userId = $request->header('id');

            $customers = Customer::where('user_id', '=', $userId)->get();

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
            $userId = $request->header('id');

            $customer = Customer::where([
                    'id' => $id,
                    'user_id' => $userId,
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
                'contact' => ['required', 'numeric', 'gt:1000000000', 'lt:10000000000000', 'unique:customers'],
            ]);

            $result = Customer::create([
                ...$validData,
                'user_id' => $request->header('id')
            ]);

            if ($result === null) throw new Exception("Uxpected error occured, couldn't create customer.");

            return response()->json([
                'status' => 'success',
                'message' => 'Customer created successfully.',
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
                'contact' => ['required', 'numeric', 'gt:1000000000', 'lt:10000000000000', 'unique:customers,contact,'.$id],
            ]);

            Customer::where([
                    'id' => $id,
                    'user_id' => $request->header('id')
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
                    'user_id' => $request->header('id')
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
