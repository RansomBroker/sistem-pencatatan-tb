<?php

namespace App\Http\Controllers;

use App\Jobs\BranchExportJob;
use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

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
        $branch = Branch::query();

        if ($request['columns'][1]['search']['value'] != '') {
            $branch->where('name', 'ILIKE',  '%'.$request['columns'][1]['search']['value'].'%');
        }

        if ($request['columns'][2]['search']['value'] != '') {
            $branch->where('branch', 'ILIKE',  '%'.$request['columns'][2]['search']['value'].'%');
        }

        if ($request['columns'][3]['search']['value'] != '') {
            $branch->where('address', 'ILIKE',  '%'.$request['columns'][3]['search']['value'].'%');
        }

        if ($request['columns'][4]['search']['value'] != '') {
            $branch->where('telephone', 'ILIKE',  '%'.$request['columns'][4]['search']['value'].'%');
        }

        if ($request['columns'][5]['search']['value'] != '') {
            $branch->where('npwp', 'ILIKE',  '%'.$request['columns'][5]['search']['value'].'%');
        }

        if ($request['columns'][6]['search']['value'] != '') {
            $branch->where('company', 'ILIKE',  '%'.$request['columns'][6]['search']['value'].'%');
        }

        $recordsTotal = count($branch->get());
        $branchFiltered = $branch->offset($request['start'])
            ->limit($request['length'])
            ->get();

        return response()->json([
            'draw' => intval($request['draw']),
            'recordsTotal' => intval($recordsTotal),
            'recordsFiltered' => intval($recordsTotal),
            'data' => $branchFiltered
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

    public function branchExportExcel(Request $request)
    {
        try {
            $name = Carbon::now()->timestamp;
            $batch = Bus::batch([
                new BranchExportJob($request->all(), $name)
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
            'exportURL' => \url('branch/branch-export/download/'.$name)
        ]);
    }

    public function exportDownload($name)
    {
        return Storage::download('public/branch_'.$name.'.xlsx');
    }
}
