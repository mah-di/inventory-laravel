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
            $ownerID = $request->header('ownerID');

            $categories = Category::where('user_id', '=', $ownerID)->get();

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
            $ownerID = $request->header('ownerID');

            $category = Category::where([
                    'id' => $id,
                    'user_id' => $ownerID,
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
                'user_id' => $request->header('ownerID')
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
                    'user_id' => $request->header('ownerID')
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
                    'user_id' => $request->header('ownerID')
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
