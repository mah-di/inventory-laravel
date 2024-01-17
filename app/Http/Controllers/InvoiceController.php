<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceProduct;
use App\Models\Product;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\InputBag;

class InvoiceController extends Controller
{

    public function all(Request $request)
    {
        try {
            $userId = $request->header('id');

            $invoices = Invoice::where('user_id', '=', $userId)->with('customer')->get();

            return response()->json([
                'status' => 'success',
                'data' => $invoices
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

            $invoice = Invoice::where([
                    'id' => $id,
                    'user_id' => $userId,
                ])
                ->with([
                    'customer',
                    'invoiceProducts' => fn ($q) => $q->with(['product'])
                ])
                ->first();

            return response()->json([
                'status' => 'success',
                'data' => $invoice
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

    public function storeInvoiceProduct(Request $request)
    {
        $validData = $request->validate([
            'invoice_id' => ['required'],
            'product_id' => ['required', 'exists:products,id'],
            'qty' => ['required', 'numeric', 'gt:0', 'lte:' . Product::select(['stock'])->find($request->input('product_id'))->stock],
            'sale_price' => ['required', 'numeric', 'gte:0'],
        ]);

        InvoiceProduct::create([
            'user_id' => $request->header('id'),
            ...$validData,
        ]);

        Product::where('id', $request->input('product_id'))
            ->update([
                'stock' => DB::raw("stock - {$request->input('qty')}")
            ]);

    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validData = $request->validate([
                'customer_id' => ['required', 'exists:customers,id'],
                'total' => ['required', 'numeric', 'gte:0'],
                'discount' => ['required', 'numeric', 'gte:0'],
                'vat' => ['required', 'numeric', 'gte:0'],
                'payable' => ['required', 'numeric', 'gte:0'],
            ]);

            $invoice = Invoice::create([
                'user_id' => $request->header('id'),
                ...$validData,
            ]);

            if ($invoice === null) throw new Exception("Uxpected error occured, couldn't create invoice.");

            foreach($request->input('products') as $product) {
                $request->setJson(new InputBag([
                    'invoice_id' => $invoice->id,
                    'product_id' => $product['product_id'],
                    'qty' => $product['qty'],
                    'sale_price' => $product['sale_price']
                ]));

                $this->storeInvoiceProduct($request);
            }
            DB::commit();

            $invoice = Invoice::where('id', $invoice->id)
                ->with([
                    'customer',
                    'invoiceProducts' => fn ($q) => $q->with(['product'])
                ])
                ->first();

            return response()->json([
                'status' => 'success',
                'message' => 'Sale Has Been Recorded.',
                'data' => $invoice,
            ]);

        } catch (QueryException $exception) {
            DB::rollBack();

            return response()->json([
                'status' => 'fail',
                'message' => null
            ]);
        } catch (Exception $exception) {
            DB::rollBack();

            return response()->json([
                'status' => 'fail',
                'message' => $exception->getMessage()
            ]);
        }
    }

    public function deleteInvoiceProduct(InvoiceProduct $invoiceProduct)
    {
        $product = $invoiceProduct->product;

        $product->stock += $invoiceProduct->qty;

        $product->save();

        $invoiceProduct->delete();
    }

    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();

            $invoice = Invoice::where([
                    'id' => $request->input('id'),
                    'user_id' => $request->header('id')
                ])
                ->with([
                    'invoiceProducts' => fn ($q) => $q->with('product')
                    ])
                ->first();

            foreach ($invoice->invoiceProducts as $invoiceProduct)
                $this->deleteInvoiceProduct($invoiceProduct);

            $invoice->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Invoice deleted.',
            ]);

        } catch (QueryException $exception) {
            DB::rollBack();

            return response()->json([
                'status' => 'fail',
                'message' => null
            ]);
        } catch (Exception $exception) {
            DB::rollBack();

            return response()->json([
                'status' => 'fail',
                'message' => $exception->getMessage()
            ]);
        }
    }

}
