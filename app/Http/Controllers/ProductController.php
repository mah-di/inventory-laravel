<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\File as FileRule;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{

    public function all(Request $request)
    {
        try {
            $userId = $request->header('id');

            $products = Product::where('user_id', '=', $userId)->get();

            return response()->json([
                'status' => 'success',
                'data' => $products
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

            $product = Product::where([
                    'id' => $id,
                    'user_id' => $userId,
                ])->first();

            return response()->json([
                'status' => 'success',
                'data' => $product
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
                'category_id' => ['required', 'exists:categories,id'],
                'name' => ['required', 'string', 'min:3'],
                'price' => ['required', 'numeric', 'gt:0', 'lt:1000000'],
                'stock' => ['required', 'numeric', 'gte:0'],
                'img' => [
                    'nullable',
                    FileRule::image()
                        ->min(32)
                        ->max(1024)
                ],
            ]);

            unset($validData['img']);
            $userID = $request->header('id');

            if ($request->hasFile('img')) {
                $img = $request->file('img');
                $fileName = $userID . time() . '.' . $img->getClientOriginalExtension();
                $validData['img_url'] = "img/products/{$fileName}";
                $img->move(public_path("img/products"), $fileName);
            }

            $result = Product::create([
                ...$validData,
                'user_id' => $userID,
            ]);

            if ($result === null) throw new Exception("Uxpected error occured, couldn't create product.");

            return response()->json([
                'status' => 'success',
                'message' => 'Product created successfully.',
                'data' => $result,
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
            $userID = $request->header('id');

            $validData = $request->validate([
                'category_id' => ['required', 'exists:categories,id'],
                'name' => ['required', 'string', 'min:3'],
                'price' => ['required', 'numeric', 'gt:0', 'lt:1000000'],
                'stock' => ['required', 'numeric', 'gte:0'],
                'img' => [
                    'nullable',
                    FileRule::image()
                        ->min(32)
                        ->max(1024)
                ],
                'img_url' => ['required'],
            ]);

            unset($validData['img']);

            if ($request->hasFile('img')) {
                if ($validData['img_url'] !== env("PRODUCT_IMG_URL"))
                    File::delete($validData['img_url']);

                $img = $request->file('img');
                $fileName = $userID . time() . '.' . $img->getClientOriginalExtension();
                $validData['img_url'] = "img/products/{$fileName}";
                $img->move(public_path("img/products"), $fileName);
            }

            Product::where([
                    'id' => $id,
                    'user_id' => $userID
                ])->update($validData);

            return response()->json([
                'status' => 'success',
                'message' => 'Product updated successfully.',
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
            Product::where([
                    'id' => $request->input('id'),
                    'user_id' => $request->header('id')
                ])->delete();

            if ($request->input('img_url') !== env("PRODUCT_IMG_URL"))
                File::delete($request->input('img_url'));

            return response()->json([
                'status' => 'success',
                'message' => 'Product deleted.',
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
