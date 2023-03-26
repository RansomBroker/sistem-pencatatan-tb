<?php

namespace App\Http\Controllers;

use App\Imports\AdvanceReceiveImport;
use App\Models\AdvanceReceive;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Consumption;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SettingController extends Controller
{
    public function setting()
    {
        return view('setting/setting');
    }

    public function importExcelProcess(Request $request)
    {
        $this->validate($request, [
            'excel-file' => 'required|mimes:xls,xlsx'
        ]);


        $collection = Excel::toCollection(new AdvanceReceiveImport, $request->file('excel-file'));

        $column = $collection[0][0];
        $advanceReceiveData = $collection[0]->splice(1);

        $i = 0;
        $totalData = count($advanceReceiveData);
        foreach ($advanceReceiveData as  $data) {
            $importBuyBranch = $data[0];
            $importCustomerID = $data[3];
            $importCustomerName = $data[4];
            $importBuyDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data[1])->format("Y-m-d");
            $importExpiredDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data[2])->format("Y-m-d");
            $importBuyPrice = $data[6];
            $importNetSale = $data[7];
            $importTax = $data[8];
            $importQty = $data[10];
            $importUnitPrice = $data[11];
            $importProduct = $data[12];
            $importCategory = $data[14];
            $importMemo = $data['13'] ?? "";
            $importNotes = $data['15'] ?? "";
            $importType = $data[5];
            $importPayment = $data[9];

            // Check apakah ada data jika tidak maka insert
            $branch = $this->branchCreateOrExist($importBuyBranch);
            $customer = $this->customerCreateOrExist($importCustomerID, $importCustomerName);
            $category = $this->categoryCreateOrExist($importCategory);
            $product = $this->productCreateOrExist($category->id, $importProduct);

            // buat advance receive dan consumption
            $advanceReceive = $this->advanceReceiveCreate($branch->id, $customer->id, $product->id);

            // Buat consumption Hitung Consumption
            $usedCount = 0;
            $status = null;
            $refundBranchID = null;
            foreach ($data = $data->splice(18, 24) as $key => $consumption) {
                if (($key % 2 == 1)) {
                    if (($consumption != null) && ($consumption != "EXPIRED") && ($consumption != "REFUND") && ($data[$key -1 ] != "REFUND") && ($data[$key -1 ] != "EXPIRED") ) {
                        $usedCount++;
                        $consumptionDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data[$key -1 ])->format("Y-m-d");
                        $consumptionBranch = $this->branchCreateOrExist($data[$key]);

                        // tambahkan consumption
                        $this->consumptionCreate($advanceReceive->id, $consumptionBranch->id, $consumptionDate, $usedCount);
                    } else {
                        $status = $data[$key-1];
                        if ($data[$key -1 ] == "REFUND") {
                            $refundBranchID = $this->branchCreateOrExist($data[$key]);
                        }
                        break;
                    }
                }
            }

            // jika qty == consumption
            if (($usedCount == $importQty) && ($status == null) && ($status != "REFUND") && ($status != "EXPIRED")) {
                $status = "OUT";
            }

            if ($status == null) {
                $status = "AVAILABLE";
            }

            $qtyTotal = $usedCount;
            $idrTotal = $usedCount * $importUnitPrice;

            $qtyExpired = 0;
            $idrExpired = 0;

            $qtyRefund = 0;
            $idrRefund = 0;

            if ($status == "EXPIRED") {
                $qtyExpired = $importQty - $qtyTotal;
                $idrExpired = $qtyExpired * $importUnitPrice;
            }

            if ($status == "REFUND") {
                $qtyRefund = $importQty - $qtyTotal;
                $idrRefund = $qtyRefund * $importUnitPrice;
            }

            $qtySumAll = $qtyTotal + $qtyExpired + $qtyRefund;
            $idrSumAll = $idrTotal + $idrExpired + $idrRefund;

            $qtyRemains = $importQty - $qtySumAll;
            $idrRemains = $importNetSale - $idrSumAll;

            if (($qtyRemains == 0) && $status == "EXPIRED") {
                $idrSumAll += $idrRemains;
                $idrExpired += $idrRemains;
                // paling akhir
                $idrRemains -= $idrRemains;
            }

            if (($qtyRemains == 0) && $status == "EXPIRED") {
                $idrSumAll += $idrRemains;
                $idrRefund += $idrRemains;
                // paling akhir
                $idrRemains -= $idrRemains;
            }

            if (($qtyRemains == 0) && $status == null) {
                $idrSumAll += $idrRemains;
                $idrTotal += $idrRemains;
                $idrRemains -= $idrRemains;
            }

            // update advance receive
            $updateAdvanceReceive = AdvanceReceive::find($advanceReceive->id);
            $updateAdvanceReceive->buy_date = $importBuyDate;
            $updateAdvanceReceive->expired_date = $importExpiredDate;
            $updateAdvanceReceive->type = $importType;
            $updateAdvanceReceive->buy_price = $importBuyPrice;
            $updateAdvanceReceive->net_sale = $importNetSale;
            $updateAdvanceReceive->tax = $importTax;
            $updateAdvanceReceive->unit_price = $importUnitPrice;
            $updateAdvanceReceive->payment = $importPayment;
            $updateAdvanceReceive->qty = $importQty;
            $updateAdvanceReceive->status = $status;
            $updateAdvanceReceive->notes = $importNotes;
            $updateAdvanceReceive->memo = $importMemo;

            // update perhitungan
            $updateAdvanceReceive->qty_total = $qtyTotal;
            $updateAdvanceReceive->idr_total = $idrTotal;
            $updateAdvanceReceive->qty_expired = $qtyExpired;
            $updateAdvanceReceive->idr_expired = $idrExpired;
            $updateAdvanceReceive->qty_refund = $qtyRefund;
            $updateAdvanceReceive->idr_refund = $idrRefund;
            $updateAdvanceReceive->qty_sum_all = $qtySumAll;
            $updateAdvanceReceive->idr_sum_all = $idrSumAll;
            $updateAdvanceReceive->qty_remains = $qtyRemains;
            $updateAdvanceReceive->idr_remains = $idrRemains;

            if ($status == "REFUND") {
                $updateAdvanceReceive->refund_branch_id = $refundBranchID->id;
            }

            $updateAdvanceReceive->save();
            $i++;
        }

        $request->session()->flash('status', 'success');
        $request->session()->flash('message', 'Berhasil import data Total Data yang diimport '. $i . ' '. 'dari '.$totalData);
        return redirect('setting');
    }

    private function branchCreateOrExist($name)
    {
        $branch = Branch::where("name", 'ILIKE', '%'.$name.'%')->first();

        if (is_null($branch)) {
            $branch = new Branch();
            $branch->name = $name;
            $branch->save();
            return $branch;
        }

        return $branch;
    }

    private function customerCreateOrExist($id, $name)
    {
        $customer = Customer::where("customer_id", 'ILIKE', '%'.$id.'%')->first();

        if (is_null($customer)) {
            $customer = new Customer();
            $customer->customer_id = $id;
            $customer->name = $name;
            $customer->save();
            return $customer;
        }

        return $customer;
    }

    private function categoryCreateOrExist($name)
    {
        $category = Category::where('name', 'ILIKE', '%'.$name.'%')->first();

        if (is_null($category)) {
            $category = new Category();
            $category->name = $name;
            $category->save();
            return  $category;
        }

        return $category;
    }

    private function productCreateOrExist($categoryId, $name)
    {
        $product = Product::where('name', 'ILIKE', '%'.$name.'%')
            ->where('category_id', $categoryId)
            ->first();

        if (is_null($product)) {
            $product = new Product();
            $product->category_id = $categoryId;
            $product->name = $name;
            $product->save();
            return $product;
        }

        return $product;
    }

    private function advanceReceiveCreate($branchID, $customerID, $productID)
    {
        $advanceReceive = new AdvanceReceive();
        $advanceReceive->branch_id = $branchID;
        $advanceReceive->customer_id = $customerID;
        $advanceReceive->product_id = $productID;
        $advanceReceive->save();

        $consumption =  new Consumption();
        $consumption->advance_receive_id = $advanceReceive->id;
        $consumption->save();

        $updateAdvanceReceive = AdvanceReceive::find($advanceReceive->id);
        $updateAdvanceReceive->consumption_id = $consumption->id;
        $updateAdvanceReceive->save();

        return $advanceReceive;
    }

    private function consumptionCreate($advanceReceiveID ,$branchID, $consumptionDate, $usedCount)
    {
        $consumption = Consumption::where('advance_receive_id', $advanceReceiveID)->first();

        $consumptionAdd = new Consumption();
        $consumptionAdd->parent_id = $consumption->id;
        $consumptionAdd->branch_id = $branchID;
        $consumptionAdd->consumption_date = $consumptionDate;
        $consumptionAdd->used_count = $usedCount;
        $consumptionAdd->save();

        return $consumptionAdd;
    }

}
