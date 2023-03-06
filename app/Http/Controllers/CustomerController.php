<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

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
            'customer-id' => 'required|max:10',
            'name' => 'required|max:40',
            'nickname' => 'max:20',
            'tel' => 'max:40',
            'identity-number' => 'max:20',
            'birth-date' => 'nullable|date',
            'address' => 'max:40',
            'email' => 'nullable|max:30|email',
            'payment-number' => 'max:25',
        ]);

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
            'customer-id' => 'required|max:10',
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
        $columns = array('name','customer_id', 'name', 'nickname', 'address', 'birth_date', 'phone', 'identity_number', 'payment_number', 'email');

        $dataWithoutLimit = Customer::orderBy($columns[$request['order'][0]['column']], $request['order'][0]['dir'])
            ->where('customer_id', 'ILIKE', '%'.$request['columns'][1]['search']['value'].'%')
            ->where('name', 'ILIKE', '%'.$request['columns'][2]['search']['value'].'%')
            ->where('nickname', 'ILIKE', '%'.$request['columns'][3]['search']['value'].'%')
            ->where('address', 'ILIKE', '%'.$request['columns'][4]['search']['value'].'%')
            ->where('birth_date', 'ILIKE', '%'.$request['columns'][5]['search']['value'].'%')
            ->orWhereNull('birth_date')
            ->where('phone', 'ILIKE', '%'.$request['columns'][6]['search']['value'].'%')
            ->where('identity_number', 'ILIKE', '%'.$request['columns'][7]['search']['value'].'%')
            ->where('payment_number', 'ILIKE', '%'.$request['columns'][8]['search']['value'].'%')
            ->where('email', 'ILIKE', '%'.$request['columns'][9]['search']['value'].'%')
            ->get();

        $recordsTotal = count($dataWithoutLimit);

        $data = Customer::orderBy($columns[$request['order'][0]['column']], $request['order'][0]['dir'])
                ->where('customer_id', 'ILIKE', '%'.$request['columns'][1]['search']['value'].'%')
                ->where('name', 'ILIKE', '%'.$request['columns'][2]['search']['value'].'%')
                ->where('nickname', 'ILIKE', '%'.$request['columns'][3]['search']['value'].'%')
                ->where('address', 'ILIKE', '%'.$request['columns'][4]['search']['value'].'%')
                ->orWhereNull('birth_date')
                ->where('birth_date', 'ILIKE', '%'.$request['columns'][5]['search']['value'].'%')
                ->where('phone', 'ILIKE', '%'.$request['columns'][6]['search']['value'].'%')
                ->where('identity_number', 'ILIKE', '%'.$request['columns'][7]['search']['value'].'%')
                ->where('payment_number', 'ILIKE', '%'.$request['columns'][8]['search']['value'].'%')
                ->where('email', 'ILIKE', '%'.$request['columns'][9]['search']['value'].'%')
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
}
