<?php

namespace App\Http\Controllers;

use App\Jobs\BranchExportJob;
use App\Jobs\CustomerExportJob;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Bus\Batch;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CustomerController extends Controller
{
    public function customer()
    {
        return view('customers/customer');
    }

    public function customerAddView()
    {
        return view('customers/customer_add');
    }

    public function customerAdd(Request $request)
    {
        $validator = $request->validate([
            'customer-id' => 'required|max:25',
            'name' => 'required|max:40',
            'nickname' => 'max:20',
            'tel' => 'max:40',
            'identity-number' => 'max:20',
            'birth-date' => 'nullable|date',
            'address' => 'max:40',
            'email' => 'nullable|max:30|email',
            'payment-number' => 'max:25',
        ]);

        // validasi apakah customer telah ada berdasrakan id customer
        $customerExist = Customer::where("customer_id", 'ILIKE', '%'.$validator['customer-id'].'%')->count();
        if ($customerExist > 0 ) {
            $request->session()->flash('status', 'danger');
            $request->session()->flash('message', 'Gagal Customer ID sudah terdapat di dalam database');
            return redirect('customer/customer-add');
        }

        $customer = new Customer();
        $customer->customer_id = $validator['customer-id'];
        $customer->name = $validator['name'];
        $customer->nickname = $validator['nickname'] ?? '';
        $customer->identity_number = $validator['identity-number'] ?? '';
        $customer->phone = $validator['tel'] ?? '';
        $customer->birth_date = $validator['birth-date'];
        $customer->address = $validator['address'] ?? '';
        $customer->email = $validator['email'] ?? '';
        $customer->payment_number = $validator['payment-number'] ?? '';

        if ($customer->save()) {
            $request->session()->flash('status', 'success');
            $request->session()->flash('message', 'Berhasil menambahkan data');
        }else {
            $request->session()->flash('status', 'danger');
            $request->session()->flash('message', 'Gagal menambahkan data');
        }
        return redirect('customer');
    }

    public function customerEditView($id)
    {
        $customer = Customer::find($id);
        $data['customer']  = $customer;
        return view('customers/customer_edit', $data);
    }

    public function customerEdit(Request $request)
    {
        $validator = $request->validate([
            'customer-id' => 'required|max:25',
            'name' => 'required|max:40',
            'nickname' => 'max:20',
            'tel' => 'max:40',
            'identity-number' => 'max:20',
            'birth-date' => 'nullable|date',
            'address' => 'max:40',
            'email' => 'nullable|max:30|email',
            'payment-number' => 'max:25',
        ]);

        $customer = Customer::find($request['id']);
        $customer->customer_id = $validator['customer-id'];
        $customer->name = $validator['name'];
        $customer->nickname = $validator['nickname'] ?? '';
        $customer->identity_number = $validator['identity-number'] ?? '';
        $customer->phone = $validator['tel'] ?? '';
        $customer->birth_date = $validator['birth-date'];
        $customer->address = $validator['address'] ?? '';
        $customer->email = $validator['email'] ?? '';
        $customer->payment_number = $validator['payment-number'] ?? '';

        if ($customer->save()) {
            $request->session()->flash('status', 'success');
            $request->session()->flash('message', 'Berhasil mengedit data');
        }else {
            $request->session()->flash('status', 'danger');
            $request->session()->flash('message', 'Gagal mengedit data');
        }
        return redirect('customer');
    }

    public function customerDataGET(Request $request)
    {
        $customer = Customer::query();

        if ($request['columns'][1]['search']['value'] != '') {
                $customer->where('customer_id', 'ILIKE', '%'.$request['columns'][1]['search']['value'].'%');
        }

        if ($request['columns'][2]['search']['value'] != '') {
            $customer->where('name', 'ILIKE', '%'.$request['columns'][2]['search']['value'].'%');
        }

        if ($request['columns'][3]['search']['value'] != '') {
            $customer->where('nickname', 'ILIKE', '%'.$request['columns'][3]['search']['value'].'%');
        }

        if ($request['columns'][4]['search']['value'] != '') {
            $customer->where('address', 'ILIKE', '%'.$request['columns'][4]['search']['value'].'%');
        }

        if ($request['columns'][5]['search']['value'] != '') {
            $customer->where('birth_date', 'ILIKE', '%'.$request['columns'][5]['search']['value'].'%');
        }

        if ($request['columns'][6]['search']['value'] != '') {
            $customer->where('phone', 'ILIKE', '%'.$request['columns'][6]['search']['value'].'%');
        }

        if ($request['columns'][7]['search']['value'] != '') {
            $customer->where('identity_number', 'ILIKE', '%'.$request['columns'][7]['search']['value'].'%');
        }

        if ($request['columns'][8]['search']['value'] != '') {
            $customer->where('payment_number', 'ILIKE', '%'.$request['columns'][8]['search']['value'].'%');
        }

        if ($request['columns'][9]['search']['value'] != '') {
            $customer->where('email', 'ILIKE', '%'.$request['columns'][9]['search']['value'].'%');
        }

        $recordsTotal = count($customer->get());

        $filteredCustomer = $customer->offset($request['start'])
            ->limit($request['length'])
            ->get();

        return response()->json([
            'draw' => intval($request['draw']),
            'recordsTotal' => intval($recordsTotal),
            'recordsFiltered' => intval($recordsTotal),
            'data' => $filteredCustomer
        ]);
    }

    public function customerDelete($id)
    {
        $customer = Customer::find($id);
        $customerName = $customer->name;
        try {
            $customer->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil menghapus customer '.$customerName
            ]);
        }catch (QueryException $error) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Gagal menghapus customer '.  $customerName  .' dikarenakan customer masih memiliki data'
            ]);
        }
    }

    public function customerExportExcel(Request $request)
    {
        try {
            $name = Carbon::now()->timestamp;
            $batch = Bus::batch([
                new CustomerExportJob($request->all(), $name)
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
            'exportURL' => \url('customer/customer-export/download/'.$name)
        ]);
    }

    public function exportDownload($name)
    {
        return Storage::download('public/customer_'.$name.'.xlsx');
    }
}
