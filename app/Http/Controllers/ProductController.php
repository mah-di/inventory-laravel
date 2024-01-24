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
            $ownerID = $request->header('ownerID');

            $products = Product::where('user_id', '=', $ownerID)->get();

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
            $ownerID = $request->header('ownerID');

            $product = Product::where([
                    'id' => $id,
                    'user_id' => $ownerID,
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
            $ownerID = $request->header('ownerID');

            if ($request->hasFile('img')) {
                $img = $request->file('img');
                $fileName = $ownerID . time() . '.' . $img->getClientOriginalExtension();
                $validData['img_url'] = "img/products/{$fileName}";
                $img->move(public_path("img/products"), $fileName);
            }

            $result = Product::create([
                ...$validData,
                'user_id' => $ownerID,
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
            $ownerID = $request->header('ownerID');

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
                $fileName = $ownerID . time() . '.' . $img->getClientOriginalExtension();
                $validData['img_url'] = "img/products/{$fileName}";
                $img->move(public_path("img/products"), $fileName);
            }

            Product::where([
                    'id' => $id,
                    'user_id' => $ownerID
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
                    'user_id' => $request->header('ownerID')
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
