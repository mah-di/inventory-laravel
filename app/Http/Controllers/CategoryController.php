<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function all(Request $request)
    {
        try {
            $userId = $request->header('id');

            $categories = Category::where('user_id', '=', $userId)->get();

            return response()->json([
                'status' => 'success',
                'data' => $categories
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

            $category = Category::where([
                    'id' => $id,
                    'user_id' => $userId,
                ])->first();

            return response()->json([
                'status' => 'success',
                'data' => $category
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
                'name' => ['required', 'string', 'min:2']
            ]);

            $result = Category::create([
                ...$validData,
                'user_id' => $request->header('id')
            ]);

            if ($result === null) throw new Exception("Uxpected error occured, couldn't create category.");

            return response()->json([
                'status' => 'success',
                'message' => 'Category created successfully.',
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
                'name' => ['required', 'string', 'min:2']
            ]);

            Category::where([
                    'id' => $request->input('id'),
                    'user_id' => $request->header('id')
                ])->update($validData);

            return response()->json([
                'status' => 'success',
                'message' => 'Category updated successfully.',
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
            Category::where([
                    'id' => $request->input('id'),
                    'user_id' => $request->header('id')
                ])->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Category deleted.',
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
