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
use App\Http\Controllers\UserController;
use App\Http\Controllers\InstallerController;
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

// install procedure
Route::controller(InstallerController::class)->name('install.')->group(function () {

    // check env file has copied or no
    Route::get('/install/step-one', 'stepOne')->name('step.one');
    Route::post('/install/step-one/process', 'stepOneProcess')->name('step.one.process');

    // check database connection
    Route::get('/install/step-two', 'stepTwo')->name('step.two');
    Route::post('/install/step-two/process', 'stepTwoProcess')->name('step.two.process');

    // create a new database
    Route::get('/install/step-three', 'stepThree')->name('step.three');
    Route::post('/install/step-three/process', 'stepThreeProcess')->name('step.three.process');

});

Route::middleware(['expired.check', 'auth.check'])->group(function () {

    Route::get('/', function () {
        return view('index');
    });

    /* Branch */
    Route::controller(BranchController::class)->group(function () {
        Route::get('/branch', 'branch');
        Route::post('/branch/data-get', 'branchDataGET');

        Route::get('/branch/branch-add', 'branchAddView');
        Route::post('/branch/branch-add/add', 'branchAdd');

        Route::get('/branch/branch-edit/{id}', 'branchEditView')->middleware('not.admin');
        Route::post('/branch/branch-edit/edit', 'branchEdit')->middleware('not.admin');

        Route::get('/branch/branch-delete/{id}', 'branchDelete')->middleware('not.admin');

        /* Export Branch */
        Route::post('/branch/branch-export/excel', 'branchExportExcel');

        /* Check  if job finished by timestamp */
        Route::get('/branch/branch-export/check/{id}/{name}', 'exportCheckStatus');
        Route::get('/branch/branch-export/download/{name}', 'exportDownload');
    });

    /* customers */
    Route::controller(CustomerController::class)->group(function () {
        Route::get('/customer', 'customer');
        Route::post('/customer/data-get', 'customerDataGET');

        Route::get('/customer/customer-add', 'customerAddView');
        Route::post('/customer/customer-add/add', 'customerAdd');

        Route::get('/customer/customer-edit/{id}', 'customerEditView')->middleware('not.admin');
        Route::post('/customer/customer-edit/edit', 'customerEdit')->middleware('not.admin');

        Route::get('/customer/customer-delete/{id}', 'customerDelete')->middleware('not.admin');

        /* Export Branch */
        Route::post('/customer/customer-export/excel', 'customerExportExcel');

        /* Check  if job finished by timestamp */
        Route::get('/customer/customer-export/check/{id}/{name}', 'exportCheckStatus');
        Route::get('/customer/customer-export/download/{name}', 'exportDownload');
    });

    /* Category */
    Route::controller(CategoryController::class)->group(function () {
        Route::get('/category', 'category');
        Route::post('/category/data-get', 'categoryDataGET');

        Route::get('/category/category-add', 'categoryAddView');
        Route::post('/category/category-add/add', 'categoryAdd');

        Route::get('/category/category-edit/{id}', 'categoryEditView')->middleware('not.admin');
        Route::post('/category/category-edit/edit', 'categoryEdit')->middleware('not.admin');

        /* Delete Category route */
        Route::get('/category/category-delete/{id}', 'categoryDelete')->middleware('not.admin');

        /* Export Branch */
        Route::post('/category/category-export/excel', 'categoryExportExcel');

        /* Check  if job finished by timestamp */
        Route::get('/category/category-export/check/{id}/{name}', 'exportCheckStatus');
        Route::get('/category/category-export/download/{name}', 'exportDownload');
    });

    /* Product */
    Route::controller(ProductController::class)->group(function() {
        Route::get('/product', 'product');
        Route::post('/product/data-get', 'productDateGET');

        Route::get('/product/product-add', 'productAddView');
        Route::post('/product/product-add/add', 'productAdd');

        Route::get('/product/product-edit/{id}', 'productEditView')->middleware('not.admin');
        Route::post('/product/product-edit/edit', 'productEdit')->middleware('not.admin');

        /* Delete Product route */
        Route::get('/product/product-delete/{id}', 'productDelete')->middleware('not.admin');

        /* Export Branch */
        Route::post('/product/product-export/excel', 'productExportExcel');

        /* Check  if job finished by timestamp */
        Route::get('/product/product-export/check/{id}/{name}', 'exportCheckStatus');
        Route::get('/product/product-export/download/{name}', 'exportDownload');

    });


    /* Advance Receives */
    Route::controller(AdvanceReceiveController::class)->group(function () {
        Route::get('/advance-receive', 'advanceReceive');
        Route::post('/advance-receive/data-get', 'advanceReceiveDataGET');

        Route::get('/advance-receive/advance-receive-add', 'advanceReceiveAddView');
        Route::post('/advance-receive/advance-receive-add/add', 'advanceReceiveAdd');

        Route::get('/advance-receive/advance-receive-edit/{id}', 'advanceReceiveEditView')->middleware('not.admin');
        Route::post('/advance-receive/advance-receive-edit/edit', 'advanceReceiveEdit')->middleware('not.admin');

        /* search user id */
        Route::get('/customer-get/{id}', 'getCustomerByID');

        /* search category by product id */
        Route::get('/category-get/{id}', 'getCategoryByID');

        /* Delete Advance Receive */
        Route::get('/advance-receive/advance-receive-delete/{id}', 'advanceReceiveDelete')->middleware('not.admin');

        /* Export advance receive */
        Route::post('/advance-receive/advance-receive-export/excel', 'advanceReceiveExportExcel');

        /* Check  if job finished by timestamp */
        Route::get('/advance-receive/advance-receive-export/check/{id}/{name}', 'exportCheckStatus');
        Route::get('/advance-receive/advance-receive-export/download/{name}', 'exportDownload');
    });

    /* Consumption */
    Route::controller(ConsumptionController::class)->group(function () {
        Route::get('/consumption', 'consumption');
        /* will replace to post if ok*/
        Route::post('/consumption/data-get', 'consumptionDataGET');

        Route::get('/consumption/consumption-add', 'consumptionAddView');
        Route::post('/consumption/consumption-get-available', 'consumptionGetAvailableData');
        Route::post('/consumption/consumption-add/add', 'consumptionAdd');

        Route::get('/consumption/consumption-edit/{id}', 'consumptionEditView')->middleware('not.admin');
        Route::post('/consumption/consumption-edit/edit', 'consumptionEdit')->middleware('not.admin');
        Route::post('/consumption/consumption-delete', 'consumptionDelete')->middleware('not.admin');
        Route::post('/consumption/consumption-get-selected-customer', 'consumptionGetSelectedCostumer');

        /* Export Consumption */
        Route::post('/consumption/consumption-export/excel', 'consumptionExportExcel');

        /* Check if job finished */
        Route::get('/consumption/consumption-export/check/{id}/{name}', 'exportCheckStatus');
        Route::get('/consumption/consumption-export/download/{name}', 'exportDownload');

    });

    /* Expired */
    Route::controller(ExpiredController::class)->group(function () {
        Route::get('/expired', 'expired');
        Route::get('/expired/data-get', 'expiredDataGET');
        Route::get('/expired/data-get-available', 'expiredDataGetAvailable');

        Route::get('/expired/add-expired', 'expiredAddView');
        Route::get('/expired/add-expired/{id}', 'expiredAdd');

        /* Export Expired */
        Route::post('/expired/expired-export/excel', 'expiredExportExcel');

        /* Check if job finished */
        Route::get('/expired/expired-export/check/{id}/{name}', 'exportCheckStatus');
        Route::get('/expired/expired-export/download/{name}', 'exportDownload');
    });

    /* Refund */
    Route::controller(RefundController::class)->group(function () {
        Route::get('/refund', 'refund');
        Route::get('/refund/data-get', 'refundDataGET');
        Route::get('/refund/data-get-available', 'refundDataGetAvailable');
        Route::get('/refund/get-branch-list', 'branchList');

        Route::get('/refund/add-refund', 'addRefundView');
        Route::post('/refund/add-refund/add', 'addRefund');

        /* Export Refund */
        Route::post('/refund/refund-export/excel', 'refundExportExcel');

        /* Check if job finished */
        Route::get('/refund/refund-export/check/{id}/{name}', 'exportCheckStatus');
        Route::get('/refund/refund-export/download/{name}', 'exportDownload');
    });

    /* Outstanding */
    Route::controller(OutstandingController::class)->group(function() {
        Route::get('/outstanding', 'outstanding');
        Route::get('/outstanding/data-get', 'outstandingDataGET');

        /* Export Outstanding */
        Route::post('/outstanding/outstanding-export/excel', 'outstandingExportExcel');

        /* Check if job finished */
        Route::get('/outstanding/outstanding-export/check/{id}/{name}', 'exportCheckStatus');
        Route::get('/outstanding/outstanding-export/download/{name}', 'exportDownload');

    });

    /* Settting */
    Route::controller(SettingController::class)->group(function () {
        Route::get('/setting', 'setting')->middleware('not.admin');
        Route::post('/setting/import-excel/process', 'importExcelProcess')->middleware('not.admin');

        Route::post('/setting/truncate/process', 'truncateProcess')->middleware('not.admin');
    });

    /* User */
    Route::controller(UserController::class)->group(function () {
        Route::get('/user', 'userView')->middleware('not.admin');
        Route::get('/user/user-add', 'userAddView')->middleware('not.admin');
        Route::get('/user/user-edit/{id}', 'userEditView')->middleware('not.admin');
        Route::get('/user/user-delete/{id}', 'userDelete')->middleware('not.admin');
        Route::get('logout', 'logout');

        Route::post('user/user-add/add', 'userAdd')->middleware('not.admin');
        Route::post('user/user-edit/edit', 'userEdit')->middleware('not.admin');

    });

});

// login
Route::get('/login', [UserController::class, 'loginView']);
Route::post('/login/process', [UserController::class, 'login']);

