<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function category()
    {
        return view('categories/category');
    }

    public function categoryAddView()
    {
        return view('categories/category_add');
    }

    public function categoryAdd(Request $request)
    {
        $validator = $request->validate([
            'name' => 'required|max:30'
        ]);

        $category = new Category();
        $category->name = $validator['name'];

        if ($category->save()) {
            $request->session()->flash('status', 'success');
            $request->session()->flash('message', 'Berhasil menambahkant data');
        }else {
            $request->session()->flash('status', 'danger');
            $request->session()->flash('message', 'Gagal menambahkan data');
        }
        return redirect('category');
    }

    public function categoryEditView($id)
    {
        $category = Category::find($id);
        $data['category'] = $category;
        return view('categories/category_edit', $data);
    }

    public function categoryEdit(Request $request)
    {
        $validator = $request->validate([
            'name' => 'required|max:30'
        ]);

        $category = Category::find($request['id']);
        $category->name = $validator['name'];

        if ($category->save()) {
            $request->session()->flash('status', 'success');
            $request->session()->flash('message', 'Berhasil mengedit data');
        }else {
            $request->session()->flash('status', 'danger');
            $request->session()->flash('message', 'Gagal mengedit data');
        }
        return redirect('category');
    }

    public function categoryDataGET(Request $request)
    {
        $columns = array('name', 'name');

        $dataWithoutLimit = Category::orderBy($columns[$request['order'][0]['column']], $request['order'][0]['dir'])
            ->where('name', 'ILIKE', '%'.$request['columns'][1]['search']['value'].'%')
            ->get();

        $recordsTotal = count($dataWithoutLimit);

        $data = Category::orderBy($columns[$request['order'][0]['column']], $request['order'][0]['dir'])
            ->where('name', 'ILIKE', '%'.$request['columns'][1]['search']['value'].'%')
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

    public function categoryDelete($id)
    {
        $category = Category::find($id);
        $categoryName = $category->name;
        try {
            $category->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil menghapus category '.$categoryName
            ]);
        }catch (QueryException $error) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Gagal menghapus category '.  $categoryName  .' dikarenakan category sedang digunakan'
            ]);
        }
    }
}
