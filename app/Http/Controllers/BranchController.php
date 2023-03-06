<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function branch() {
        return view('branches/branch');
    }

    public function branchAddView() {
        return view('branches/branch_add');
    }

    public function branchAdd(Request $request)
    {
        $validator = $request->validate([
            'name' => 'required|max:20',
            'branch' => 'required|max:20',
            'address' => 'required|max:40',
            'tel' => 'required|max:40',
            'npwp' => 'max:20',
            'company' => 'required|max:40'
        ]);

        $branch = new Branch();
        $branch->name = $validator['name'];
        $branch->branch = $validator['branch'];
        $branch->address = $validator['address'];
        $branch->telephone = $validator['tel'];
        $branch->npwp = $validator['npwp'] ?? '';
        $branch->company = $validator['company'];

        if ($branch->save()) {
            $request->session()->flash('status', 'success');
            $request->session()->flash('message', 'Berhasil menambahkan data');
        }else {
            $request->session()->flash('status', 'danger');
            $request->session()->flash('message', 'Gagal menambahkan data');
        }
        return redirect('branch');
    }

    public function branchEditView($id)
    {
        $branch = Branch::find($id);
        $data['branch'] = $branch;
        return view('branches/branch_edit', $data);
    }

    public function branchEdit(Request $request)
    {
        $validator = $request->validate([
            'name' => 'required|max:20',
            'branch' => 'required|max:20',
            'address' => 'required|max:40',
            'tel' => 'required|max:40',
            'npwp' => 'max:20',
            'company' => 'required|max:40'
        ]);

        $branch = Branch::find($request['id']);
        $branch->name = $validator['name'];
        $branch->branch = $validator['branch'];
        $branch->address = $validator['address'];
        $branch->telephone = $validator['tel'];
        $branch->npwp = $validator['npwp'] ?? '';
        $branch->company = $validator['company'];

        if ($branch->save()) {
            $request->session()->flash('status', 'success');
            $request->session()->flash('message', 'Berhasil mengedit data');
        }else {
            $request->session()->flash('status', 'danger');
            $request->session()->flash('message', 'Gagal mengedit data');
        }
        return redirect('branch');

    }

    public function branchDataGet(Request $request) {
        $columns = array('name', 'name', 'branch', 'address', 'telephone', 'npwp', 'company');

        $dataWithoutLimit = Branch::orderBy($columns[$request['order'][0]['column']], $request['order'][0]['dir'])
            ->where('name', 'ILIKE',  '%'.$request['columns'][1]['search']['value'].'%')
            ->where('branch', 'ILIKE',  '%'.$request['columns'][2]['search']['value'].'%')
            ->where('address', 'ILIKE',  '%'.$request['columns'][3]['search']['value'].'%')
            ->where('telephone', 'ILIKE',  '%'.$request['columns'][4]['search']['value'].'%')
            ->where('npwp', 'ILIKE',  '%'.$request['columns'][5]['search']['value'].'%')
            ->where('company', 'ILIKE',  '%'.$request['columns'][6]['search']['value'].'%')
            ->get();
        $recordsTotal = count($dataWithoutLimit);

        $branchData = Branch::orderBy($columns[$request['order'][0]['column']], $request['order'][0]['dir'])
            ->where('name', 'ILIKE',  '%'.$request['columns'][1]['search']['value'].'%')
            ->where('branch', 'ILIKE',  '%'.$request['columns'][2]['search']['value'].'%')
            ->where('address', 'ILIKE',  '%'.$request['columns'][3]['search']['value'].'%')
            ->where('telephone', 'ILIKE',  '%'.$request['columns'][4]['search']['value'].'%')
            ->where('npwp', 'ILIKE',  '%'.$request['columns'][5]['search']['value'].'%')
            ->where('company', 'ILIKE',  '%'.$request['columns'][5]['search']['value'].'%')
            ->offset($request['start'])
            ->limit($request['length'])
            ->get();

        return response()->json([
            'draw' => intval($request['draw']),
            'recordsTotal' => intval($recordsTotal),
            'recordsFiltered' => intval($recordsTotal),
            "data" => $branchData
        ]);
    }

    public function branchDelete($id)
    {
        $branch = Branch::find($id);
        $branchName = $branch->name;
        try {
            $branch->delete();
            return response()->json([
               'status' => 'success',
               'message' => 'Berhasil menghapus cabang '.$branchName
            ]);
        } catch (QueryException $error) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Gagal menghapus cabang '.  $branchName  .' dikarenakan cabang masih memiliki data'
            ]);
        }
    }
}
