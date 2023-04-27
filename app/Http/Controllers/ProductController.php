<?php

namespace App\Http\Controllers;

use App\Jobs\CategoryExportJob;
use App\Jobs\ProductExportJob;
use App\Models\Category;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function product()
    {
        return view('products/product');
    }

    public function productDateGET(Request $request)
    {
        $columns = array('product_id', 'product_id', 'category_id', 'name');
        $dataWithoutLimit = Product::orderBy($columns[$request['order'][0]['column']], $request['order'][0]['dir'])
            ->with('categories')
            ->whereRelation('categories', 'name', 'ILIKE', '%'.$request['columns'][2]['search']['value'].'%')
            ->where('name', 'ILIKE', '%'.$request['columns'][3]['search']['value'].'%')
            ->where('product_id', 'ILIKE', '%'.$request['columns'][1]['search']['value'].'%')
            ->get();

        $recordsTotal = count($dataWithoutLimit);

        $data = Product::orderBy($columns[$request['order'][0]['column']], $request['order'][0]['dir'])
            ->with('categories')
            ->whereRelation('categories', 'name', 'ILIKE', '%'.$request['columns'][2]['search']['value'].'%')
            ->where('name', 'ILIKE', '%'.$request['columns'][3]['search']['value'].'%')
            ->where('product_id', 'ILIKE', '%'.$request['columns'][1]['search']['value'].'%')
            ->offset($request['start'])
            ->limit($request['length'])
            ->get();

        return response()->json([
            'draw' => intval($request['draw']),
            'recordsTotal' => intval($recordsTotal),
            'recordsFiltered' => intval($recordsTotal),
            "data" => $data
        ]);
    }

    public function productAddView()
    {
        $categories = Category::all();
        $data['categories'] = $categories;
        return view('products/product_add', $data);
    }

    public function productAdd(Request $request)
    {
        $validator = $request->validate([
            'product-id' => 'required|max:20',
            'category' => 'required',
            'name' => 'required|max:20',
        ]);

        $product = new Product();
        $product->category_id = $validator['category'];
        $product->product_id = $validator['product-id'];
        $product->name = $validator['name'];

        if ($product->save()) {
            $request->session()->flash('status', 'success');
            $request->session()->flash('message', 'Berhasil menambahkan  data');
        }else {
            $request->session()->flash('status', 'danger');
            $request->session()->flash('message', 'Gagal menambahkan data');
        }
        return redirect('product');
    }

    public function productEditView($id)
    {
        $product = Product::find($id);
        $categories = Category::all();
        $data['product'] = $product;
        $data['categories'] = $categories;
        return view('products/product_edit', $data);
    }

    public function productEdit(Request $request)
    {
        $validator = $request->validate([
            'product-id' => 'required|max:20',
            'category' => 'required',
            'name' => 'required|max:20',
        ]);

        $product = Product::find($request['id']);
        $product->category_id = $validator['category'];
        $product->product_id = $validator['product-id'];
        $product->name = $validator['name'];

        if ($product->save()) {
            $request->session()->flash('status', 'success');
            $request->session()->flash('message', 'Berhasil Mengedit  data');
        }else {
            $request->session()->flash('status', 'danger');
            $request->session()->flash('message', 'Gagal Mengedit data');
        }
        return redirect('product');
    }

    public function productDelete($id)
    {
        $product = Product::find($id);
        $productName = $product->name;

        try {
            $product->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil menghapus product '.$productName
            ]);
        }catch (QueryException $error) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Gagal menghapus product '.  $productName  .' dikarenakan product sedang digunakan'
            ]);
        }


    }

    public function productExportExcel(Request $request)
    {
        try {
            $name = Carbon::now()->timestamp;
            $batch = Bus::batch([
                new ProductExportJob($request->all(), $name)
            ])->dispatch();

            // flush all failed job if exist
            Artisan::call("queue:flush");
            Artisan::call("queue:work --stop-when-empty ");

            return response()->json([
                'status' => 'success',
                'name' => $name,
                'batchID' => $batch->id
            ]);

        }catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'name' => $name,
                'batchID' => ''
            ]);
        }
    }

    public function exportCheckStatus($id, $name)
    {
        $exportBatchStatusCanceled = Bus::findBatch($id)->canceled();
        $exportBatchStatusFinished = Bus::findBatch($id)->finished();

        if($exportBatchStatusFinished == 1 && $exportBatchStatusCanceled ==  1) {
            return response()->json([
                'status' => 'failed',
                'exportStatus' => $exportBatchStatusFinished,
                'exportURL' => null
            ]);
        }

        return response()->json([
            'status' => 'success',
            'exportStatus' => $exportBatchStatusFinished,
            'exportURL' => \url('product/product-export/download/'.$name)
        ]);
    }

    public function exportDownload($name)
    {
        return Storage::download('public/product_'.$name.'.xlsx');
    }


}
