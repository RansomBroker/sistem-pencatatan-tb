<?php

use App\Http\Controllers\AdvanceReceiveController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ConsumptionController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ExpiredController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RefundController;
use App\Http\Controllers\OutstandingController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['expired.check'])->group(function () {

    Route::get('/', function () {
        return view('index');
    });

    /* Branch */
    Route::controller(BranchController::class)->group(function () {
        Route::get('/branch', 'branch');
        Route::post('/branch/data-get', 'branchDataGET');

        Route::get('/branch/branch-add', 'branchAddView');
        Route::post('/branch/branch-add/add', 'branchAdd');

        Route::get('/branch/branch-edit/{id}', 'branchEditView');
        Route::post('/branch/branch-edit/edit', 'branchEdit');

        Route::get('/branch/branch-delete/{id}', 'branchDelete');
    });

    /* customers */
    Route::controller(CustomerController::class)->group(function () {
        Route::get('/customer', 'customer');
        Route::post('/customer/data-get', 'customerDataGET');

        Route::get('/customer/customer-add', 'customerAddView');
        Route::post('/customer/customer-add/add', 'customerAdd');

        Route::get('/customer/customer-edit/{id}', 'customerEditView');
        Route::post('/customer/customer-edit/edit', 'customerEdit');

        Route::get('/customer/customer-delete/{id}', 'customerDelete');
    });

    /* Category */
    Route::controller(CategoryController::class)->group(function () {
        Route::get('/category', 'category');
        Route::post('/category/data-get', 'categoryDataGET');

        Route::get('/category/category-add', 'categoryAddView');
        Route::post('/category/category-add/add', 'categoryAdd');

        Route::get('/category/category-edit/{id}', 'categoryEditView');
        Route::post('/category/category-edit/edit', 'categoryEdit');

        /* Delete Category route */
        Route::get('/category/category-delete/{id}', 'categoryDelete');
    });

    /* Product */
    Route::controller(ProductController::class)->group(function() {
        Route::get('/product', 'product');
        Route::post('/product/data-get', 'productDateGET');

        Route::get('/product/product-add', 'productAddView');
        Route::post('/product/product-add/add', 'productAdd');

        Route::get('/product/product-edit/{id}', 'productEditView');
        Route::post('/product/product-edit/edit', 'productEdit');

        /* Delete Product route */
        Route::get('/product/product-delete/{id}', 'productDelete');

    });


    /* Advance Receives */
    Route::controller(AdvanceReceiveController::class)->group(function () {
        Route::get('/advance-receive', 'advanceReceive');
        Route::post('/advance-receive/data-get', 'advanceReceiveDataGET');

        Route::get('/advance-receive/advance-receive-add', 'advanceReceiveAddView');
        Route::post('/advance-receive/advance-receive-add/add', 'advanceReceiveAdd');

        Route::get('/advance-receive/advance-receive-edit/{id}', 'advanceReceiveEditView');
        Route::post('/advance-receive/advance-receive-edit/edit', 'advanceReceiveEdit');

        /* create column */
        Route::get('/advance-receive/get-column', 'getColumn');

        /* search user id */
        Route::get('/customer-get/{id}', 'getCustomerByID');

        /* search category by product id */
        Route::get('/category-get/{id}', 'getCategoryByID');

        /* Delete Advance Receive */
        Route::get('/advance-receive/advance-receive-delete/{id}', 'advanceReceiveDelete');

    });

    /* Consumption */
    Route::controller(ConsumptionController::class)->group(function () {
        Route::get('/consumption', 'consumption');
        /* will replace to post if ok*/
        Route::get('/consumption/data-get', 'consumptionDataGET');

        Route::get('/consumption/consumption-add', 'consumptionAddView');
        Route::get('/consumption/consumption-get-available', 'consumptionGetAvailableData');
        Route::post('/consumption/consumption-add/add', 'consumptionAdd');

        Route::get('/consumption/consumption-edit/{id}', 'consumptionEditView');
        Route::post('/consumption/consumption-edit/edit', 'consumptionEdit');
        Route::post('/consumption/consumption-delete', 'consumptionDelete');
        Route::post('/consumption/consumption-get-selected-customer', 'consumptionGetSelectedCostumer');

        /* create column */
        Route::get('/consumption/get-column', 'getColumn');
    });

    /* Expired */
    Route::controller(ExpiredController::class)->group(function () {
        Route::get('/expired', 'expired');
        Route::get('/expired/data-get', 'expiredDataGET');
        Route::get('/expired/data-get-available', 'expiredDataGetAvailable');

        Route::get('/expired/add-expired', 'expiredAddView');
        Route::get('/expired/add-expired/{id}', 'expiredAdd');
    });

    /* Refund */
    Route::controller(RefundController::class)->group(function () {
        Route::get('/refund', 'refund');
        Route::get('/refund/data-get', 'refundDataGET');
        Route::get('/refund/data-get-available', 'refundDataGetAvailable');
        Route::get('/refund/get-branch-list', 'branchList');

        Route::get('/refund/add-refund', 'addRefundView');
        Route::post('/refund/add-refund/add', 'addRefund');
    });

    /* Outstanding */
    Route::controller(OutstandingController::class)->group(function() {
        Route::get('/outstanding', 'outstanding');
        Route::get('/outstanding/data-get', 'outstandingDataGET');
    });

    /* Settting */
    Route::controller(SettingController::class)->group(function () {
        Route::get('/setting', 'setting');
        Route::post('/setting/import-excel/process', 'importExcelProcess');
    });

});
