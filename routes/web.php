<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'HomeController@login')->name('login');


Route::get('/changePassword', 'HomeController@showChangePasswordForm')->name('changePasswordForm');
Route::post('/changePassword', 'HomeController@changePassword')->name('changePassword');


Auth::routes(['register' => false]);


Route::middleware(["auth"])->group(function () {

    Route::get('/home', 'HomeController@index')->name('home');


    //Products routes
    Route::resource('masters/products', 'ProductController')->only([
        'index', 'store', 'update', 'destroy'
    ]);

    Route::post('masters/products/store',
        'ProductController@storeProduct')->name('store-products');

    Route::post('masters/products/all',
        'ProductController@allProducts')->name('all-products');

    Route::get('masters/products/product-category-filter',
        'ProductController@productCategoryFilter')->name('product-category-filter');

    Route::get('masters/products/status-filter',
        'ProductController@statusFilter')->name('status-filter');

    Route::get('masters/products/status-activate',
        'ProductController@statusActivate')->name('status-activate');


    //Categories routes
    Route::resource('masters/product-categories', 'CategoryController')->only([
        'index', 'store', 'update', 'destroy'
    ]);

    //Sub Categories Routes
    Route::resource('masters/sub-categories', 'SubCategoryController')->only([
        'index', 'store', 'update', 'destroy'
    ]);

    //Supplier routes
    Route::resource('masters/suppliers', 'SupplierController')->only([
        'index', 'store', 'destroy', 'update'
    ]);

    //Customer routes
    Route::resource('sales/customers', 'CustomerController')->only([
        'index', 'store', 'destroy', 'update'
    ]);


    //Location routes
    Route::resource('masters/locations', 'LocationController')->only([
        'index', 'store', 'destroy', 'update'
    ]);

    //Adjustment reason routes
    Route::resource('masters/adjustment-reasons', 'AdjustmentReasonController')->only([
        'index', 'store', 'update', 'destroy'
    ]);
    //Price Categories routes
    Route::resource('masters/price-categories', 'PriceCategoryController')->only([
        'index', 'store', 'update', 'destroy'
    ]);

    //Expense Categories routes
    Route::resource('masters/expense-categories', 'ExpenseCategoryController')->only([
        'index', 'store', 'update', 'destroy'
    ]);
    //Expense Subcategories routes
    Route::resource('masters/expense-subcategories', 'ExpenseSubcategoryController')->only([
        'index', 'store', 'update', 'destroy'
    ]);

    //Store routes
    Route::resource('masters/stores', 'StoreController')->only([
        'index', 'store', 'update', 'destroy'
    ]);

    //Purchase Order routes
    Route::resource('purchases/purchase-order', 'OrderController')->only([
        'index', 'store', 'update', 'destroy'
    ]);
    Route::get('purchases/purchase-order/select/filter-product', 'OrderController@filterSupplierProduct')->name('filter-product');
    Route::get('purchases/purchase-order/select/filter-product-input', 'OrderController@filterSupplierProductInput')->name('filter-product-input');


    //Purchase OrderList routes
    Route::get('purchases/order-history', 'PurchaseOrderListController@index')->name('order-history.index');
    Route::post('purchases/cancel-order', 'PurchaseOrderListController@destroy')->name('cancel-order.destroy');
    Route::get('purchases/order-date', 'PurchaseOrderListController@getOrderHistory')->name('getOrderHistory');
    Route::post('purchases/print-order', 'PurchaseOrderListController@printOrder')->name('printOrder');
    Route::get('ipurchases/print-order/pdfgen/{order_no}', 'PurchaseOrderListController@reprintPurchaseOrder')->name('purchase-order-pdf-gen');

    //Invoice management routes
    Route::resource('purchases/invoice-management', 'InvoiceController')->only([
        'index', 'store', 'update'
    ]);
    Route::get('purchases/invoice-received', 'InvoiceController@getInvoice')->name('getInvoice');
    Route::get('purchases/invoice-received/filter-by-due-date', 'InvoiceController@getInvoiceByDueDate')->name('get-invoice-by-due-date');

    //Material received routes
    Route::get('purchases/material-received', 'MaterialReceivedController@index')->name('material-received.index');
    Route::get('purchases/materials', 'MaterialReceivedController@getMaterialsReceived')->name('getMaterialsReceived');
    Route::post('purchases/material/edit', 'MaterialReceivedController@update')->name('material.edit');
    Route::post('purchases/material/delete', 'MaterialReceivedController@destroy')->name('material.delete');

    //GoodsReceiving Routes
    Route::get('purchases/goods-receiving', 'GoodsReceivingController@index')->name('goods-receiving.index');
//    Route::post('purchases/goods-receiving.order-receive', 'GoodsReceivingController@orderReceive')->name('goods-receiving.orderReceive');
    Route::get('purchases/loading-item-price', 'GoodsReceivingController@getItemPrice')->name('receiving-price-category');
    Route::get('purchases/loading-product-price', 'GoodsReceivingController@getItemPrice2')->name('product-price-category');
    Route::get('purchases/goods-receiving.item-receive', 'GoodsReceivingController@itemReceive')->name('goods-receiving.itemReceive');
    Route::get('purchases/supplier/select/filter-invoice', 'GoodsReceivingController@filterInvoice')->name('filter-invoice');
    Route::get('purchases/goods-receiving.order-receive', 'GoodsReceivingController@orderReceive')->name('goods-receiving.orderReceive');

    //Configurations Routes
    Route::get('/setting', 'ConfigurationsController@index')->name('configurations.index');
    Route::post('/setting', 'ConfigurationsController@store')->name('configurations.store');
    Route::post('/update-setting', 'ConfigurationsController@update')->name('configurations.update');

    //General settingroutes
    Route::get('masters/general-settings', 'GeneralSettingController@index')->name('general-settings.index');
    Route::put('masters/update-general-informations', 'GeneralSettingController@updateInfo')->name('general-settings.updateInfo');
    Route::put('masters/update-general-settings', 'GeneralSettingController@updateSetting')->name('general-settings.updateSetting');
    Route::put('masters/update-general-recepts', 'GeneralSettingController@updateReceipt')->name('general-settings.updateReceipt');

    //Cash Sales routes
    Route::get('sales/cash-sales', 'SaleController@cashSale')->name('cash-sales.cashSale');
    Route::get('sales/credit-sales', 'SaleController@creditSale')->name('credit-sales.creditSale');
    Route::get('sales/credit-customer-payments', 'SaleController@getCreditSale')->name('getCreditSale');
    Route::post('sales/cash-sales', 'SaleController@storeCashSale')->name('cash-sales.storeCashSale');
    Route::get('sales/payments', 'SaleController@getPaymentsHistory')->name('payments.getPaymentsHistory');
    Route::post('sales/credit-sales', 'SaleController@storeCreditSale')->name('credit-sales.storeCreditSale');
    Route::get('sales/credits-tracking', 'SaleController@creditsTracking')->name('credits-tracking.creditsTracking');
    Route::get('sales/credit-payments', 'SaleController@getCreditsCustomers')->name('credit-payments.getCreditsCustomers');
    Route::post('sales/credit-payments', 'SaleController@CreditSalePayment')->name('credit-payments.creditSalePayment');
    Route::get('sales/sale-histories', 'SaleController@SalesHistory')->name('sale-histories.SalesHistory');
    Route::post('sales/sale-date', 'SaleController@getSalesHistory')->name('getSalesHistory');
    Route::post('sales/select-products', 'SaleController@selectProducts')->name('selectProducts');
    Route::get('sales/cash-sale/receipt/{page}', 'SaleController@getCashReceipt')->name('getCashReceipt');
    Route::get('sales/credit-sale/receipt', 'SaleController@getCreditReceipt')->name('getCreditReceipt');
    Route::get('sales/sale/filter-by-word', 'SaleController@filterProductByWord')->name('filter-product-by-word');
    Route::post('sales/history/sale-reprint', 'SaleController@receiptReprint')->name('sale-reprint-receipt');
    Route::get('sales/credit-tracking/payment-history/filter', 'SaleController@paymentHistoryFilter')->name('payment-history-filter');


    //Sales Quotes routes
    Route::get('sales/sales-quotes', 'SaleQuoteController@index')->name('sale-quotes.index');
    Route::post('sales/sales-quotes/store', 'SaleQuoteController@store')->name('sale-quotes.store');
    Route::get('sales/sales-quotes/receipt', 'SaleQuoteController@getQuoteReceipt')->name('getQuoteReceipt');
    Route::post('sales/sales-quotes/save', 'SaleQuoteController@storeQuote')->name('storeQuote');
    Route::get('sales/sales-quotes/receipt-reprint/{quote_id}', 'SaleQuoteController@receiptReprint')->name('receiptReprint');

    //Sales Returns routes
    Route::get('sales/sales-returns', 'SaleReturnController@index')->name('sale-returns.index');
    Route::get('/sales-returns', 'SaleReturnController@getSales')->name('getSales');
    Route::get('/returns', 'SaleReturnController@getRetunedProducts')->name('getRetunedProducts');
    Route::post('sales/sales-returns', 'SaleReturnController@store')->name('sale-returns.store');
    Route::get('sales/returns-approval', 'SaleReturnController@getSalesReturn')->name('sale-returns-approval.getSalesReturn');


    //No longer used
    // Route::post('sales/returns-approval', 'SaleReturnController@approve')->name('sale-returns-approval.approve');
    // Route::post('sales/returns-rejection', 'SaleReturnController@reject')->name('sale-returns-rejection.reject');


    /*Current Stock routes*/
    Route::resource('inventory-management/current-stock', 'CurrentStockController')->only([
        'index', 'update'
    ]);
    Route::post('inventory-management/current-stock/in-stock',
        'CurrentStockController@allInStock')->name('all-in-stock');

    /*stock adjustment routes*/
    Route::resource('inventory-management/stock-adjustment', 'StockAdjustmentController')->only([
        'index', 'store', 'update', 'destroy'
    ]);

    Route::post('inventory-management/stock-adjustment/all',
        'StockAdjustmentController@allAdjustments')->name('all-adjustments');


    /*price list route*/
    Route::resource('inventory-management/price-list', 'PriceListController')->only([
        'index', 'store', 'update', 'destroy'
    ]);

    Route::post('inventory-management/price-list/all',
        'PriceListController@allPriceList')->name('all-price-list');

    /*Stock transfer routes*/
    Route::resource('inventory-management/stock-transfer', 'StockTransferController')->only([
        'index', 'store', 'update', 'destroy'
    ]);

    Route::get('inventory-management/stock-transfer-history', 'StockTransferController@stockTransferHistory')->name('stock-transfer-history');

    Route::get('inventory-management/stock-transfer-filter-by-store', 'StockTransferController@filterByStore')->name('filter-by-store');

    Route::get('inventory-management/stock-transfer-filter-by-word', 'StockTransferController@filterByWord')->name('filter-by-word');

    /*Stock transfer acknowledge routes*/
    Route::resource('inventory-management/stock-transfer-acknowledge', 'StockTransferAcknowledgeController')->only([
        'index', 'store', 'update', 'destroy'
    ]);

    /*Re print transfer routes*/
    Route::resource('inventory-management/stock-transfer-reprint', 'RePrintTransferController')->only(['index']);

    /*Re print stock issue*/
    Route::resource('inventory-management/stock-issue-reprint', 'RePrintIssueController')->only(['index']);

    /*Stock issue routes*/
    Route::resource('inventory-management/stock-issue', 'StockIssueController')->only([
        'index', 'store', 'update', 'destroy'
    ]);

    Route::get('inventory-management/stock-issue-history', 'IssueReturnController@issueHistory')
        ->name('stock-issue-history');

    /*Stock issue return routes*/
    Route::resource('inventory-management/stock-issue-return', 'IssueReturnController')->only([
        'index', 'store'
    ]);

    /*product ledger routes*/
    Route::resource('inventory-management/product-ledger', 'ProductLedgerController')->only([
        'index'
    ]);

    /*daily stock count routes*/
    Route::resource('inventory-management/daily-stock-count', 'DailyStockCountController')->only([
        'index'
    ]);

    /*outgoingstock routes*/
    Route::resource('inventory-management/out-going-stock', 'OutGoingStockController')->only([
        'index'
    ]);

    /*expense routes*/
    Route::resource('expense-management/expense', 'ExpenseController')->only([
        'index', 'store', 'update', 'destroy'
    ]);

    /*inventory report routes*/
    Route::get('inventory-management/inventory-report', 'InventoryReportController@index')->name('inventory-report-index');

    /*accounting report routes*/
    Route::get('accounting-management/accounting-report', 'AccountingReportController@index')->name('accounting-report-index');
    Route::get('accounting-management/accounting-report/report-filter', 'AccountingReportController@reportOption')->name('accounting-report-filter');


    /*sale report routes*/
    Route::get('sale-management/sale-report', 'SaleReportController@index')->name('sale-report-index');

    /*purchase report routes*/
    Route::get('purchase-management/purchase-report', 'PurchaseReportController@index')->name('purchase-report-index');

    Route::get('purchase-management/purchase-report/report-filter', 'PurchaseReportController@reportOption')->name('purchase-report-filter');


    /*filters route with ajax*/
    Route::get('inventory-management/myitems', 'CurrentStockController@filter')->name('myitems');

    Route::get('price/price-history', 'PriceListController@priceHistory')->name('price-history');

    Route::get('current-stock/stock-detail', 'CurrentStockController@currentStockDetail')->name('current-stock-detail');

    Route::get('current-stock/stock-price-category', 'PriceListController@priceCategory')->name('sale-price-category');

    Route::get('inventory-management/stock-transfer-save', 'StockTransferController@store')->name('stock-transfer-save');

    Route::get('inventory-management/stock-transfer-filter', 'StockTransferAcknowledgeController@transferFilter')->name('stock-transfer-filter');

    Route::get('inventory-management/stock-transfer-filter-detail', 'StockTransferAcknowledgeController@transferFilterDetailComplete')->name('stock-transfer-filter-detail');

    Route::get('inventory-management/stock-transfer-complete', 'StockTransferAcknowledgeController@stockTransferComplete')->name('stock-transfer-complete');

    Route::get('inventory-management/stock-transfer-filter-by-date', 'StockTransferController@filterTransferByDate')->name('stock-transfer-filter-date');

    Route::get('inventory-management/stock-transfer-show', 'StockTransferAcknowledgeController@stockTransferShow')->name('stock-transfer-show');

    Route::get('inventory-management/stock-issue-show', 'StockIssueController@stockIssueShow')->name('stock-issue-show');

    Route::get('inventory-management/stock-issue-show-reprint', 'StockIssueController@stockIssueShowReprint')->name('stock-issue-show-reprint');

    Route::get('inventory-management/stock-issue-filter', 'StockIssueController@stockIssueFilter')->name('stock-issue-filter');

    Route::get('inventory-management/product-ledger-filter', 'ProductLedgerController@showProductLedger')->name('product-ledger-show');

    Route::get('inventory-management/out-going-stock-filter', 'OutGoingStockController@showOutStock')->name('outgoing-stock-show');

    Route::get('inventory-management/daily-stock-count-filter', 'DailyStockCountController@showDailyStockFilter')->name('daily-stock-count-filter');

    Route::get('expense-management/expense-date-filter', 'ExpenseController@filterExpenseDate')->name('expense-date-filter');

    Route::get('inventory-report/inventory-report-filter', 'InventoryReportController@reportOption')->name('inventory-report-filter');

    Route::get('sale-report/sale-report-filter', 'SaleReportController@reportOption')->name('sale-report-filter');


    /*Pdf generator routes*/
    Route::get('inventory-management/stock-transfer/pdfgen/{transfer_no}', 'StockTransferController@generateStockTransferPDF')->name('stock-transfer-pdf-gen');

    Route::post('inventory-management/stock-transfer/pdfregen', 'StockTransferController@regenerateStockTransferPDF')->name('stock-transfer-pdf-regen');

    Route::get('inventory-management/stock-issue/pdfgen/{issue_no}', 'StockIssueController@generateStockIssuePDF')->name('stock-issue-pdf-gen');

    Route::post('inventory-management/stock-issue/pdfregen', 'StockIssueController@regenerateStockIssuePDF')->name('stock-issue-pdf-regen');

    Route::post('inventory-management/daily-stock-count/pdfgen', 'DailyStockCountController@generateDailyStockCountPDF')->name('daily-stock-count-pdf-gen');

    Route::get('inventory-management/inventory-count-sheet/pdfgen', 'InventoryCountSheetController@generateInventoryCountSheetPDF')->name('inventory-count-sheet-pdf-gen');

    //user roles
    Route::get('user-roles', 'RoleController@index')->name('roles.index');
    Route::get('user-roles/create', 'RoleController@create')->name('roles.create');
    Route::post('user-roles', 'RoleController@store')->name('roles.store');
    Route::get('user-roles/{id}/edit', 'RoleController@edit')->name('roles.edit');
    Route::post('user-roles/update', 'RoleController@update')->name('roles.update');
    Route::delete('user-roles/delete', 'RoleController@destroy')->name("roles.destroy");

    //users routes
    Route::get('users', 'UserController@index')->name('users.index');
    Route::post('users/register', 'UserController@store')->name("users.register");
    Route::post('users/update', 'UserController@update')->name("users.update");
    Route::put('users/delete', 'UserController@delete')->name("users.delete");
    Route::post('users/de-actiavate', 'UserController@deActivate')->name("users.deactivate");
    Route::post('change-password', 'UserController@changePassword')->name('change-password');
    Route::get('user-profile', 'UserController@profile')->name('user-profile');
    Route::post('user-profile/update', 'UserController@updateProfile')->name("update-profile");
    Route::get('users/search', 'UserController@search')->name("users.search");
    Route::post('users/user-role-id', 'UserController@getRoleID')->name('getRoleID');


    /*file import route*/
    Route::resource('file/import', 'ImportDataController')->only([
        'index'
    ]);
    Route::post('file/import/record-import', 'ImportDataController@recordImport')->name('record-import');
    Route::get('file/import/record-import', 'ImportDataController@getImportTemplate')->name('import-template');

});




