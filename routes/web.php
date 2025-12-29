<?php
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PriceListController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\TransporterController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Store;
use Maatwebsite\Excel\Row;

Route::post('/login', 'Auth\LoginController@login')->middleware('web');


// Route::get('/', 'HomeController@login')->name('login');
Route::get('/', function () {
    return redirect('/home');
});


Route::get('/changePassword', 'HomeController@showChangePasswordForm')->name('changePasswordForm');
Route::post('/changePassword', 'HomeController@changePassword')->name('changePassword');

Route::get('/profile', 'ProfileController@index')->name('showProfile');
Route::post('/updateProfileImage', 'ProfileController@updateProfileImage')->name('updateProfileImage');

Route::get('hardCodePwd','HomeController@hardCodePwd');



Auth::routes(['register' => false]);

// Change store routes


Route::middleware(["auth","main_branch"])->group(function () {

    // Change store routes
    Route::post('/set-current-store', function (Request $request) {
        $user = Auth::user();

        if ($user->store->name !== 'ALL') {
            return response()->json(['error' => 'Not allowed'], 403);
        }

        $storeId = $request->input('store_id');

        // Ensure store exists
        if (!Store::find($storeId)) {
            return response()->json(['error' => 'Invalid store'], 422);
        }

        session(['current_store_id' => $storeId]);

        return response()->json([
            'success' => true,
            'current_store_id' => $storeId
        ]);
    });

    Route::post('/change-store', [HomeController::class, 'changeStore'])->name('change_store');

    Route::get('/home', 'HomeController@index')->name('home');

    Route::get('/tasks', 'HomeController@taskSchedule')->name('task');
    Route::get('/tasks/read', 'HomeController@markAsRead')->name('task-read');

    Route::post('/home/stock-summary', 'HomeController@stockSummary')->name('stock-summary');


    //Products routes
    Route::resource('inventory/products', 'ProductController')->only([
        'store', 'update', 'destroy'
    ]);

    Route::get('inventory/product-list','ProductController@index')->name('products.index');
    Route::get('products/export', 'ProductController@export')->name('products.export');
    Route::get('tools/upload-price', 'ProductController@uploadPriceForm')->name('tools.upload-price-form');
    Route::post('tools/upload-price', 'ProductController@uploadPrice')->name('tools.upload-price');
    Route::get('tools/download-price-template', 'ProductController@downloadPriceTemplate')->name('tools.download-price-template');
    Route::get('tools/upload-stock', 'ProductController@uploadStockForm')->name('tools.upload-stock-form');
    Route::post('tools/upload-stock', 'ProductController@uploadStock')->name('tools.upload-stock');
    Route::get('tools/download-stock-template', 'ProductController@downloadStockTemplate')->name('tools.download-stock-template');
    Route::get('tools/reset-stock', 'ProductController@resetStockForm')->name('tools.reset-stock-form');
    Route::post('tools/reset-stock', 'ProductController@resetStock')->name('tools.reset-stock');
    Route::get('tools/export-stock', 'ProductController@exportForm')->name('tools.export-stock');

    // Product Import Routes
    Route::prefix('import')->group(function () {
        Route::get('/stocks', 'ImportDataController@importData')->name('import-data');
        Route::get('/products', 'ImportDataController@index' )->name('import-products');
        Route::get('/download-template', 'ImportDataController@downloadStockTemplate')->name('download-template');
        Route::get('/download-products-template', 'ImportDataController@downloadTemplate' )->name('download-products-template');
        // Route::get('/preview', 'ImportDataController@showPreview')->name('show-preview');
        Route::post('/previewStock', 'ImportDataController@previewStockImport')
            ->middleware(['web', 'log.upload'])
            ->name('preview-import');
        Route::post('/previewProducts', 'ImportDataController@previewImport')
            ->middleware(['web', 'log.upload'])
            ->name('preview-products-import');
        Route::post('/record', 'ImportDataController@recordStockImport')->name('record-import');
        Route::post('/record-products', 'ImportDataController@recordImport')->name('record-products-import');
    });

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

    Route::get('settings/general/product-categories','CategoryController@index')->name('product-categories.index');

    //Sub Categories Routes
    Route::resource('masters/sub-categories', 'SubCategoryController')->only([
        'index', 'store', 'update', 'destroy'
    ]);

    Route::get('settings/general/product-subcategories','SubCategoryController@index')->name('sub-categories.index');

    //Supplier routes
    Route::resource('purchasing/suppliers', 'SupplierController')->only([
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

    Route::get('settings/general/locations','LocationController@index')->name('locations.index');

    //Adjustment reason routes
    Route::resource('masters/adjustment-reasons', 'AdjustmentReasonController')->only([
        'index', 'store', 'update', 'destroy'
    ]);
    Route::get('settings/general/adjustment-reasons','AdjustmentReasonController@index')->name('adjustment-reasons.index');

    //Price Categories routes
    Route::resource('masters/price-categories', 'PriceCategoryController')->only([
        'index', 'store', 'update', 'destroy'
    ]);

    Route::get('settings/general/price-categories','PriceCategoryController@index')->name('price-categories.index');

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

    Route::get('settings/general/branches','StoreController@index')->name('stores.index');

    // Price List routes
    Route::get('inventory/price-list', [PriceListController::class, 'index'])->name('price-list.index');
    Route::post('inventory/price-list', [PriceListController::class, 'store'])->name('price-list.store');
    Route::put('inventory/price-list/{price_list}', [PriceListController::class, 'update'])->name('price-list.update');
    Route::delete('inventory/price-list/{price_list}', [PriceListController::class, 'destroy'])->name('price-list.destroy');
    Route::get('inventory/price-list/all', [PriceListController::class, 'allPriceList'])->name('all-price-list');

    //purchase return routes
        Route::get('purchasing/purchase-returns', 'PurchaseReturnController@index')
        ->name('purchase-return.returns');
        Route::get('purchasing/purchase_returns/approvals', 'PurchaseReturnController@approvals')->name('purchase-returns.approvals');
        Route::post('purchase-returns', 'PurchaseReturnController@store')->name('purchase-returns.store');
        Route::put('purchase-returns/{id}', 'PurchaseReturnController@update')->name('purchase-returns.update');
        Route::match(['get', 'post'], 'get-purchase-returns', 'PurchaseReturnController@getPurchaseReturns')->name('getPurchaseReturns');
    //Purchase Order routes
    Route::resource('purchasing/purchase-order', 'OrderController')->only([
        'index', 'store', 'update', 'destroy'
    ]);
    Route::get('purchasing/purchase-order/select/filter-product', 'OrderController@filterSupplierProduct')->name('filter-product');
    Route::get('purchasing/purchase-order/select/filter-product-input', 'OrderController@filterSupplierProductInput')->name('filter-product-input');

    //new route for the order list page
    Route::get('purchasing/purchase-order-list', 'OrderController@orderList')->name('purchases.purchase-order.list');

    //Purchase OrderList routes
    Route::get('purchasing/order-history', 'PurchaseOrderListController@index')->name('order-history.index');
    Route::post('purchasing/cancel-order', 'PurchaseOrderListController@destroy')->name('cancel-order.destroy');
    Route::get('purchasing/order-date', 'PurchaseOrderListController@getOrderHistory')->name('getOrderHistory');
    Route::get('purchasing/print-order/{id}/Purchase Order', 'PurchaseOrderListController@printOrder')->name('printOrder');
    Route::get('ipurchasing/print-order/pdfgen/{order_no}', 'PurchaseOrderListController@reprintPurchaseOrder')->name('purchase-order-pdf-gen');

    //Invoice management routes
    Route::resource('purchasing/invoice-management', 'InvoiceController')->only([
        'index', 'store', 'update'
    ]);

    Route::get('accounting/invoices','InvoiceController@index')->name('invoice-management.index');
    Route::get('accounting/invoices/payments','InvoiceController@payments')->name('invoice-management.payments');

    // Invoice Payment Routes
    Route::post('accounting/invoices/payments', 'InvoiceController@storePayment')->name('invoice-payments.store');
    Route::get('accounting/invoices/payments-history', 'InvoiceController@PaymentHistory')->name('invoice-payments-history');
    Route::get('accounting/invoices/payments/history', 'InvoiceController@getPaymentHistory')->name('invoice-payments.history');
    Route::get('accounting/invoices/get-supplier-invoices', 'InvoiceController@getSupplierInvoices')->name('get-supplier-invoices');

    Route::get('purchasing/invoice-received', 'InvoiceController@getInvoice')->name('getInvoice');
    Route::get('purchasing/invoice-received/filter-by-due-date', 'InvoiceController@getInvoiceByDueDate')->name('get-invoice-by-due-date');

    //Material received routes
    Route::get('purchasing/material-received', 'MaterialReceivedController@index')->name('material-received.index');
    Route::post('purchasing/materials', 'MaterialReceivedController@getMaterialsReceived')->name('getMaterialsReceived');
    Route::post('purchasing/material/edit', 'MaterialReceivedController@update')->name('material.edit');
    Route::post('purchasing/material/delete', 'MaterialReceivedController@destroy')->name('material.delete');

    //GoodsReceiving Routes
    Route::get('purchasing/goods-receiving', 'GoodsReceivingController@index')->name('goods-receiving.index');
    Route::get('purchasing/orders-receiving', 'GoodsReceivingController@orderReceiving')->name('orders-receiving.index');
    Route::post('purchasing/orders/{id}/approve', 'OrderController@approve')->name('orders.approve');
    Route::post('purchasing/goods-receiving/order-receive', 'GoodsReceivingController@orderReceive')->name('goods-receiving.orderReceive');
    Route::get('purchasing/loading-item-price', 'GoodsReceivingController@getItemPrice')->name('receiving-price-category');
    Route::get('purchasing/loading-product-price', 'GoodsReceivingController@getItemPrice2')->name('product-price-category');
    Route::get('purchasing/goods-receiving.item-receive', 'GoodsReceivingController@itemReceive')->name('goods-receiving.itemReceive');
    Route::get('purchasing/supplier/select/filter-invoice', 'GoodsReceivingController@filterInvoice')->name('filter-invoice');

    Route::get('purchasing/supplier/select/filter-price', 'GoodsReceivingController@filterPrice')->name('filter-price');
    Route::post('purchasing/purchase-order/list', 'GoodsReceivingController@purchaseOrderList')->name('purchase-order-list');
    Route::get('pharmacy/purchasing/loading-invoice-item-price', 'GoodsReceivingController@getInvoiceItemPrice')->name('receiving-item-prices');//receiving-item-prices
    Route::post('pharmacy/purchasing/goods-receiving.invoice-item-receive', 'GoodsReceivingController@invoiceitemReceive')->name('goods-receiving.invoiceitemReceive');

    //Configurations Routes
    Route::get('/settings/general/configurations', 'ConfigurationsController@index')->name('configurations.index');
    Route::post('/setting', 'ConfigurationsController@store')->name('configurations.store');
    Route::post('/update-setting', 'ConfigurationsController@update')->name('configurations.update');

    //General settingroutes
    Route::get('masters/general-settings', 'GeneralSettingController@index')->name('general-settings.index');
    Route::put('masters/update-general-informations', 'GeneralSettingController@updateInfo')->name('general-settings.updateInfo');
    Route::put('masters/update-general-settings', 'GeneralSettingController@updateSetting')->name('general-settings.updateSetting');
    Route::put('masters/update-general-recepts', 'GeneralSettingController@updateReceipt')->name('general-settings.updateReceipt');

    //Cash Sales routes
    Route::get('sales/cash-sales', 'SaleController@cashSale')->name('cash-sales.cashSale');

    // AI Assistant Routes
    Route::get('ai-assistant', 'ApiAssistantController@index')->name('ai-assistant.index');
    Route::get('sales/credit-sales', 'SaleController@creditSale')->name('credit-sales.creditSale');
    Route::get('sales/credit-customer-payments', 'SaleController@getCreditSale')->name('getCreditSale');
    Route::post('sales/cash-sales', 'SaleController@storeCashSale')->name('cash-sales.storeCashSale');
    Route::get('sales/payments', 'SaleController@getPaymentsHistory')->name('payments.getPaymentsHistory');
    Route::post('sales/credit-sales', 'SaleController@storeCreditSale')->name('credit-sales.storeCreditSale');
    Route::get('sales/credits-tracking', 'SaleController@creditsTracking')->name('credits-tracking.creditsTracking');
    Route::get('sales/credit-payments', 'SaleController@getCreditsCustomers')->name('credit-payments.getCreditsCustomers');
    Route::post('sales/credit-payments', 'SaleController@CreditSalePayment')->name('credit-payments.creditSalePayment');
    Route::get('sales/sales-history', 'SaleController@SalesHistory')->name('sale-histories.SalesHistory');
    Route::post('sales/sale-date', 'SaleController@getSalesHistory')->name('getSalesHistory');
    Route::post('sales/sales-data', 'SaleController@getSalesHistoryData')->name('getSalesHistoryData');
    Route::post('sales/select-products', 'SaleController@selectProducts')->name('selectProducts');
    Route::get('sales/cash-sale/receipt/{page}', 'SaleController@getCashReceipt')->name('getCashReceipt');
    Route::get('sales/credit-sale/receipt', 'SaleController@getCreditReceipt')->name('getCreditReceipt');
    Route::get('sales/sale/filter-by-word', 'SaleController@filterProductByWord')->name('filter-product-by-word');
    Route::post('sales/history/sale-reprint', 'SaleController@receiptReprint')->name('sale-reprint-receipt');
    Route::get('sales/history/sale-reprint/{receipt}', 'SaleController@receiptReprint')->name('sale-reprint-receipt-get');
    Route::get('sales/credit-tracking/payment-history/filter', 'SaleController@paymentHistoryFilter')->name('payment-history-filter');


    //Sales Order routes
    Route::get('sales/sales-order', 'SaleQuoteController@index')->name('sale-quotes.index');
    Route::get('sales/sales-order-list', 'SaleQuoteController@orderList')->name('sale-quotes.order_list');
    Route::post('sales/sales-orders', 'SaleQuoteController@store')->name('sale-quotes.store');
    Route::get('sales/sales-orders/get-quotes', 'SaleQuoteController@getQuotes')->name('sale-quotes.get-quotes');
    Route::get('sales/sales-orders/receipt', 'SaleQuoteController@getQuoteReceipt')->name('getQuoteReceipt');
    Route::post('sales/sales-orders/save', 'SaleQuoteController@storeQuote')->name('storeQuote');
    // Route::post('sales/sales-orders/update', 'SaleQuoteController@updateQuote')->name('updateQuote');
    Route::post('sales/sales-order/change-price', 'SaleQuoteController@changePriceCatg')->name('change-price-category');
    Route::post('sales/sales-order/change-customer', 'SaleQuoteController@changeCustomer')->name('change-quote-customer');
    Route::post('sales/sales-order/add-item','SaleQuoteController@addQuoteItem')->name('add-qoute-item');
    Route::post('sales/sales-orders/update-item', 'SaleQuoteController@updateQuoteItem')->name('update-qoute-item');
    Route::post('sales/sales-order/delete-item','SaleQuoteController@deleteQuoteItem')->name('delete-qoute-item');
    Route::post('sales/sales-orders/save-editings', 'SaleQuoteController@saveFinalQuote')->name('save-final-qoute');
    Route::get('sales/sales-orders/receipt-reprint/{quote_id}', 'SaleQuoteController@receiptReprint')->name('receiptReprint');
    Route::get('sales/sales-orders/edit-sale/{quote_id}', 'SaleQuoteController@update')->name('updateSale');
    Route::post('sales/sales-orders/convert-to-sales', 'SaleQuoteController@convertToSales')->name('convert-to-sales');
    Route::get('sales/sales-orders/generate-tax-invoice/{id}', 'SaleQuoteController@generateTaxInvoice')->name('generate-tax-invoice');
    Route::get('sales/sales-orders/generate-delivery-note/{id}', 'SaleQuoteController@generateDeliveryNote')->name('generate-delivery-note');

    //Sales Returns routes
    Route::get('sales/sales-returns', 'SaleReturnController@index')->name('sale-returns.index');
    Route::post('/sales-returns', 'SaleReturnController@getSales')->name('getSales');
    Route::get('/returns', 'SaleReturnController@getRetunedProducts')->name('getRetunedProducts');
    Route::post('sales/sales-returns', 'SaleReturnController@store')->name('sale-returns.store');
    Route::get('sales/returns-approval', 'SaleReturnController@getSalesReturn')->name('sale-returns-approval.getSalesReturn');


    //No longer used
    // Route::post('sales/returns-approval', 'SaleReturnController@approve')->name('sale-returns-approval.approve');
    // Route::post('sales/returns-rejection', 'SaleReturnController@reject')->name('sale-returns-rejection.reject');


    /*Current Stock routes*/
    Route::get('inventory/current-stocks/edit/{productId}', 'CurrentStockController@edit')->name('current-stock.edit');
    Route::post('inventory/current-stock/update', 'CurrentStockController@update')->name('current-stock.update');



    /*New Routes for Current Stock*/
    Route::get('inventory/current-stocks','CurrentStockController@currentStock')->name('current-stocks');
    Route::post('inventory/api/current-stocks','CurrentStockController@currentStockApi')->name('current-stocks-filter');
    Route::get('inventory/old-stocks','CurrentStockController@getOldStockValue')->name('old-stocks');
    Route::get('inventory/current-stock-value','CurrentStockController@getStockValue')->name('all-stocks');
    Route::post('inventory/current-stock-value','CurrentStockController@getStockValue')->name('current_stock_value');
    Route::post('inventory/filtered_values','CurrentStockController@filterStockValue')->name('stock-value-filter');

    Route::post('inventory/current-stock/in-stock',
        'CurrentStockController@allInStock')->name('all-in-stock');

    /*stock adjustment routes*/
    Route::group(['middleware' => ['permission:View Stock Adjustment|Stock Adjustment']], function () {
        Route::get('inventory/stock-adjustment', [StockAdjustmentController::class, 'index'])->name('stock-adjustments-history');
        Route::get('inventory/stock-adjustment/new_adjustment', [StockAdjustmentController::class, 'newAdjustment'])->name('new-stock-adjustment');
        Route::get('inventory/stock-adjustment/create', [StockAdjustmentController::class, 'create'])->name('stock-adjustments.create');
        Route::post('inventory/stock-adjustment', [StockAdjustmentController::class, 'store'])->name('stock-adjustments.store');
        Route::get('inventory/stock-adjustment/{adjustment}', [StockAdjustmentController::class, 'show'])->name('stock-adjustments.show');
        Route::post('inventory/stock-adjustment/all', [StockAdjustmentController::class, 'allAdjustments'])->name('all-adjustments');
    });

    /*price list route*/
    Route::resource('inventory/price-list', 'PriceListController')->only([
        'index', 'store', 'update', 'destroy'
    ]);

    Route::get('inventory/fetch-price-list', [PriceListController::class, 'fetchPriceList'])->name('fetch-price-list');

    Route::get('inventory/price-list/history',[PriceListController::class])->name('price-list-history');


    Route::post('price_list',[PriceListController::class,'filteredPriceList'])->name('price-list');

    Route::post('inventory/price-list/all',
        'PriceListController@allPriceList')->name('all-price-list');

    /*Stock transfer routes*/
    Route::resource('inventory/stock-transfer', 'StockTransferController')->only([
        'index', 'store', 'update', 'destroy', 'show', 'edit'
    ]);

    Route::post('inventory/stock-transfer_1', 'StockTransferController@storeTransfer')->name('stock_transfer.store');
    
    Route::post('inventory/stock-transfer-approve', 'StockTransferController@approveTransfer')->name('approve-transfer');

    Route::post('inventory/stock-transfer-reject', 'StockTransferController@rejectTransfer')->name('reject-transfer');

    Route::get('inventory/stock-transfer_', 'StockTransferController@stockTransferHistory')->name('stock-transfer-history');

    Route::get('inventory/stock-transfer-filter-by-store', 'StockTransferController@filterByStore')->name('filter-by-store');

    Route::get('inventory/stock-transfer-filter-by-word', 'StockTransferController@filterByWord')->name('filter-by-word');

    /*Stock transfer acknowledge routes*/
    Route::resource('inventory/stock-transfer-acknowledge', 'StockTransferAcknowledgeController')->only([
        'store', 'update', 'destroy'
    ]);

    Route::post('inventory/stock-transfer-acknowledge', 'StockTransferAcknowledgeController@acknowledgeTransfer')->name('acknowledge-transfer');
    Route::get('inventory/stock-transfer-acknowledge/{transfer_no}','StockTransferAcknowledgeController@index')->name('stock-transfer-acknowledge.index');
    Route::get('inventory/stock-transfer-acknowledge/{transfer_no}/acknowledge','StockTransferAcknowledgeController@fetchTransferToAcknowledge')->name('stock-transfer-to-acknowledge');

    /*Re print transfer routes*/
    Route::resource('inventory/stock-transfer-reprint', 'RePrintTransferController')->only(['index']);

    /*Re print stock issue*/
    Route::resource('inventory/stock-issue-reprint', 'RePrintIssueController')->only(['index']);

    /*Stock issue routes*/
    Route::resource('inventory/stock-issue', 'StockIssueController')->only([
        'index', 'store', 'update', 'destroy'
    ]);

    Route::get('inventory/stock-issue-history', 'IssueReturnController@issueHistory')
        ->name('stock-issue-history');

    /*Stock issue return routes*/
    Route::resource('inventory/stock-issue-return', 'IssueReturnController')->only([
        'index', 'store'
    ]);

    /*product ledger routes*/
    Route::resource('inventory/product-ledger', 'ProductLedgerController')->only([
        'index'
    ]);

    /*daily stock count routes*/
    Route::resource('inventory/daily-stock-count', 'DailyStockCountController')->only([
        'index'
    ]);

    /*outgoingstock routes*/
    Route::resource('inventory/out-going-stock', 'OutGoingStockController')->only([
        'index'
    ]);

    /*expense routes*/
    Route::resource('expenses/expense', 'ExpenseController')->only([
        'index', 'store', 'update', 'destroy'
    ]);

    Route::get('accounting/expenses','ExpenseController@index')->name('expense.index');

    /*inventory report routes*/
    Route::get('reports/inventory-report', 'InventoryReportController@index')->name('inventory-report-index');

    /*accounting report routes*/
    Route::get('reports/accounting-report', 'AccountingReportController@index')->name('accounting-report-index');
    Route::get('accounting-management/accounting-report/report-filter', 'AccountingReportController@reportOption')->name('accounting-report-filter');


    /*sale report routes*/
    Route::get('reports/sale-report', 'SaleReportController@index')->name('sale-report-index');

    /*purchase report routes*/
    Route::get('reports/purchase-report', 'PurchaseReportController@index')->name('purchase-report-index');

    Route::get('purchase-management/purchase-report/report-filter', 'PurchaseReportController@reportOption')->name('purchase-report-filter');
    
    Route::get('reports/transport-report', 'TransportReportController@index')->name('transport-report-index');
    Route::get('reports/transport-report/report-filter', 'TransportReportController@reportOption')->name('transport-report-filter');    

    /*filters route with ajax*/
    Route::get('inventory/myitems', 'CurrentStockController@filter')->name('myitems');

    Route::get('price/price-history', 'PriceListController@priceHistory')->name('price-history');

    Route::get('current-stock/stock-detail', 'CurrentStockController@currentStockDetail')->name('current-stock-detail');

    Route::get('current-stock/stock-detail-pricing', 'CurrentStockController@currentStockPricing')->name('current-pricing');

    Route::get('current-stock/stock-price-category', 'PriceListController@priceCategory')->name('sale-price-category');

    Route::get('inventory/stock-details', 'StockDetailsController@stockDetails')->name('stock-details');

    Route::post('inventory/stock-transfer-save', 'StockTransferController@store')->name('stock-transfer-save');

    Route::get('inventory/stock-transfer-filter', 'StockTransferAcknowledgeController@transferFilter')->name('stock-transfer-filter');
    Route::get('inventory/filter-transfered-stock', 'StockTransferAcknowledgeController@filterTransfer')->name('filter-transfered-stock');

    Route::get('inventory/stock-transfer-filter-detail', 'StockTransferAcknowledgeController@transferFilterDetailComplete')->name('stock-transfer-filter-detail');

    Route::get('inventory/stock-transfer-complete', 'StockTransferAcknowledgeController@stockTransferComplete')->name('stock-transfer-complete');

    Route::get('inventory/stock-transfer-filter-by-date', 'StockTransferController@filterTransferByDate')->name('stock-transfer-filter-date');

    Route::get('inventory/stock-transfer-show', 'StockTransferAcknowledgeController@stockTransferShow')->name('stock-transfer-show');

    Route::post('inventory/stock-transfer/update-details/{id}', 'StockTransferController@update')->name('stock-transfer.update_details');

    Route::get('inventory/stock-issue-show', 'StockIssueController@stockIssueShow')->name('stock-issue-show');

    Route::get('inventory/stock-issue-show-reprint', 'StockIssueController@stockIssueShowReprint')->name('stock-issue-show-reprint');

    Route::get('inventory/stock-issue-filter', 'StockIssueController@stockIssueFilter')->name('stock-issue-filter');

    Route::get('inventory/product-ledger-filter', 'ProductLedgerController@showProductLedger')->name('product-ledger-show');

    Route::get('inventory/out-going-stock-filter', 'OutGoingStockController@showOutStock')->name('outgoing-stock-show');

    Route::get('inventory/daily-stock-count-fetch', 'DailyStockCountController@fetchSalesWithStock')->name('daily-stock-count-fetch');
    
    Route::get('inventory/stock-taking', 'DailyStockCountController@stockTaking')->name('stock-taking');
    Route::post('inventory/stock-taking/process', 'DailyStockCountController@processStockTaking')->name('stock-taking.process');
    Route::post('inventory/stock-taking/snapshot', 'DailyStockCountController@snapshotStockValue')->name('stock-taking.snapshot');

    Route::get('expenses/expense-date-filter', 'ExpenseController@filterExpenseDate')->name('expense-date-filter');

    Route::get('inventory-report/inventory-report-filter', 'InventoryReportController@reportOption')->name('inventory-report-filter');

    Route::get('sale-report/sale-report-filter', 'SaleReportController@reportOption')->name('sale-report-filter');

    Route::get('inventory/stock-count-analytics', 'InventoryReportController@stockCountAnalytics')->name('stock-count-analytics');
    

    /*Pdf generator routes*/
    Route::get('inventory/stock-transfer/pdfgen/{transfer_no}', 'StockTransferController@generateStockTransferPDF')->name('stock-transfer-pdf-gen');

    Route::post('inventor/stock-transfer/pdfregen', 'StockTransferController@regenerateStockTransferPDF')->name('stock-transfer-pdf-regen');

    Route::get('inventory/stock-issue/pdfgen/{issue_no}', 'StockIssueController@generateStockIssuePDF')->name('stock-issue-pdf-gen');

    Route::post('inventory/stock-issue/pdfregen', 'StockIssueController@regenerateStockIssuePDF')->name('stock-issue-pdf-regen');

    Route::post('inventory/daily-stock-count/pdfgen', 'DailyStockCountController@generateDailyStockCountPDF')->name('daily-stock-count-pdf-gen');

    Route::get('inventory/daily-stock-count/export', 'DailyStockCountController@exportDailyStockCount')->name('daily-stock-count.export');

    Route::post('inventory/daily-stock-count/process-adjustment', 'DailyStockCountController@processStockCountAdjustment')->name('daily-stock-count.process-adjustment');

    Route::get('inventory/stock-discrepancy-report', 'InventoryReportController@stockDiscrepancyReport')->name('stock-discrepancy-report');

    Route::get('inventory/inventory-count-sheet/Inventory Count Sheet', 'InventoryCountSheetController@generateInventoryCountSheetPDF')->name('inventory-count-sheet-pdf-gen');

    // Batch Stock Count Routes
    Route::get('inventory/batch-stock-count', 'BatchStockCountController@index')->name('batch-stock-count.index');
    Route::post('inventory/batch-stock-count/preview', 'BatchStockCountController@preview')->name('batch-stock-count.preview');
    Route::post('inventory/batch-stock-count/process', 'BatchStockCountController@process')->name('batch-stock-count.process');

    //user roles
    Route::get('settings/security/roles', 'RoleController@index')->name('roles.index');
    Route::get('user-roles/create', 'RoleController@create')->name('roles.create');
    Route::post('user-roles', 'RoleController@store')->name('roles.store');
    Route::get('user-roles/{id}/edit', 'RoleController@edit')->name('roles.edit');
    Route::post('user-roles/update', 'RoleController@update')->name('roles.update');
    Route::delete('user-roles/delete', 'RoleController@destroy')->name("roles.destroy");

    //users routes
    Route::get('settings/security/users', 'UserController@index')->name('users.index');
    Route::post('users/register', 'UserController@store')->name("users.register");
    Route::post('users/update', 'UserController@update')->name("users.update");
    Route::put('users/delete', 'UserController@delete')->name("users.delete");
    Route::post('users/de-actiavate', 'UserController@deActivate')->name("users.deactivate");
    Route::post('change-password', 'UserController@changePassword')->name('change-password');
    Route::get('user-profile', 'UserController@profile')->name('user-profile');
    Route::post('user-profile/update', 'UserController@updateProfile')->name("update-profile");
    Route::get('users/search', 'UserController@search')->name("users.search");
    Route::post('users/user-role-id', 'UserController@getRoleID')->name('getRoleID');
    Route::get('users/password-reset/{email}', 'UserController@passwordReset')->name("password.reset.admin");
    Route::post('users/password-reset/update', 'UserController@passwordResetUpdate')->name("password.admin.update");


    /*file import route*/
    // Route::resource('/masters/import', 'ImportDataController')->only([...]);
    // Route::post('/masters/import/record-import', 'ImportDataController@recordImport')->name('masters.record-import');
    // Route::get('/masters/import/record-import', 'ImportDataController@downloadTemplate')->name('import-template');

    /* Budget */
    Route::get('/budget/budgets',[BudgetController::class,'index'])->name('budget.index');
    Route::post('/budget/budgets-store',[BudgetController::class,'index'])->name('budget.store');
    Route::post('/budget/budgets-update',[BudgetController::class,'index'])->name('budget.update');
    Route::post('/budget/budgets-approve',[BudgetController::class,'index'])->name('budget.approve');

    /* Requisition */
    Route::get('Inventory/StockRequisitions/Requisition-list', 'RequisitionController@index')->name('requisitions.index');
    Route::get('requisitions/issue-history/print/{id}', 'RequisitionController@printIssueHistory')->name('requisitions.issue-history.print');
    Route::get('Inventory/StockRequisitions/new', 'RequisitionController@create')->name('requisitions.create');
    Route::get('search_items', 'RequisitionController@search_items')->name('search_items');
    Route::get('requisitions/get-products-by-store/{store_id}', 'RequisitionController@getProductsByStore')->name('requisitions.get-products-by-store');
    Route::post('requisitions-store', 'RequisitionController@store')->name('requisitions.store');
    Route::post('requisitions-update', 'RequisitionController@update')->name('requisitions.update');
    Route::get('/requisitions-list', ['uses' => 'RequisitionController@getRequisitions', 'as' => 'requisitions-list']);
    Route::get('/requisitions-view/{id}', 'RequisitionController@show')->name('requisitions.view');
    Route::get('/print-requisitions', 'RequisitionController@printReq')->name('print-requisitions');
    Route::delete('/requisitions-delete', 'RequisitionController@destroy')->name("requisitions.delete");
    Route::post('/requisitions-data', 'RequisitionController@showRequisition')->name("requisitions.data");

    Route::get('Inventory/Stock-Issue/Issue List', 'RequisitionController@issueReq')->name('issue.index');
    Route::get('Inventory/Stock-Issue/Issue-history', 'RequisitionController@issueHistory')->name('requisitions-issue-history');
    Route::get('/requisitions-issue-history-list', ['uses' => 'RequisitionController@getRequisitionsHistory', 'as' => 'requisitions-issue-history-list']);
    Route::get('/requisitions-issue-list', ['uses' => 'RequisitionController@getRequisitionsIssue', 'as' => 'requisitions-issue-list']);
    Route::get('/requisition-issue/{id}', 'RequisitionController@issue')->name('requisitions.issue');
    Route::post('requisitions-issuing', 'RequisitionController@issuing')->name('requisitions.issuing');

    /* Assets */


    /* Change Store */
    // Route::post('home/change_store',[HomeController::class,'changeStore'])->name('change_store');

    /* Acknowledge all */
    Route::post('acknowledge-all',[StockTransferController::class,'acknowledgeAll'])->name('acknowledge-all');

    /* Add Permission */
    Route::post('add-permission',[UserController::class,'addPermission'])->name('add.permission');

    /* Activities */
    Route::get('user-activities',[UserController::class,'userActivities'])->name('user-activities');

    /* Sales Details */
    Route::post('sale_detail',[SaleController::class,'salesDetailsData'])->name('sale_detail');


    /* TOOLS */
    Route::get('settings/tools/database-backup', 'DatabaseBackupController@index')->name('database-backup.index');
    Route::post('settings/tools/database-backup', 'DatabaseBackupController@create')->name('database-backup.create');
    Route::get('settings/tools/database-backup/{filename}/download', 'DatabaseBackupController@download')->name('database-backup.download');
    Route::delete('settings/tools/database-backup/{filename}', 'DatabaseBackupController@delete')->name('database-backup.delete');

    Route::get('settings/tools/database-clear', 'DatabaseBackupController@clearIndex')->name('database-clear.index');
    Route::post('settings/tools/database-clear', 'DatabaseBackupController@clearDatabase')->name('database-clear.clear');

    /* HELP */

    Route::view('/help/support', 'help.support');
    Route::view('/help/contact-us', 'help.contact');

    /* SUPPORT */
    Route::get('/support', 'SupportController@index')->name('support.index');
    Route::get('/support/download-manual', 'SupportController@downloadManual')->name('support.download-manual');

    // Price Management Routes
    Route::get('inventory/current-stock/{id}/pricing', 'CurrentStockController@showPricing')
        ->name('current-stock.pricing')
        ->middleware('permission:manage price categories|override product prices|view price history');
    
    Route::post('inventory/current-stock/update-price', 'CurrentStockController@updatePrice')
        ->name('update-price')
        ->middleware('permission:manage price categories|override product prices');

});


Route::prefix('transport-logistics')->name('transport-logistics.')->group(function () {
    // Index route - shows all transporters
    Route::get('/transporters', [TransporterController::class, 'index'])
        ->name('transporters.index');  // This matches what your controller expects
    
    // Store route - creates new transporter
    Route::post('/transporters', [TransporterController::class, 'store'])
        ->name('transporters.store');
    
    // Edit route - shows edit form
    Route::get('/transporters/{transporter}/edit', [TransporterController::class, 'edit'])
        ->name('transporters.edit');
    
    // Update route - processes the edit form
    Route::put('/transporters/{transporter}', [TransporterController::class, 'update'])
        ->name('transporters.update');
    
    // Delete route - removes a transporter
    Route::delete('/transporters/{transporter}', [TransporterController::class, 'destroy'])
        ->name('transporters.destroy');
});

Route::resource('vehicles', 'VehicleController');
Route::resource('transport-orders', 'TransportOrderController');
Route::get('suppliers', [SupplierController::class, 'index']);
Route::get('stores', [StoreController::class, 'index']);




Route::get('logout', [UserController::class, 'logout'])->name('logout');

Route::get('/products/export', [ProductController::class, 'export'])->name('products.export');

Route::get('/test-log', function() {
    Log::emergency('Test emergency log');
    Log::alert('Test alert log');
    Log::critical('Test critical log');
    Log::error('Test error log');
    Log::warning('Test warning log');
    Log::notice('Test notice log');
    Log::info('Test info log');
    Log::debug('Test debug log');
    return 'Logs written - check storage/logs/laravel.log';
});

// Stock Count Schedules routes
    Route::resource('stock-count-schedules', 'StockCountScheduleController');
    Route::post('stock-count-schedules/{stock_count_schedule}/approve', 'StockCountScheduleController@approve')->name('stock-count-schedules.approve');

   // Transport Order nested payment routes
Route::prefix('transport-orders/{transportOrder}')->group(function() {
    Route::resource('payments', 'PaymentController')->names('transport-orders.payments')->only([
        'index', 'create', 'store', 'show', 'edit', 'update', 'destroy'
    ]);
});

// Standalone payment routes
Route::prefix('payments')->group(function() {
    Route::get('/', 'PaymentController@allPayments')->name('payments.index');
    Route::get('lookup', 'PaymentController@lookup')->name('payments.lookup');
    Route::post('create', 'PaymentController@createWithOrder')->name('payments.createWithOrder');
    
    // Standard resource routes for standalone payments
    Route::resource('/', 'PaymentController')->names([
        'show' => 'payments.show',
        'edit' => 'payments.edit',
        'update' => 'payments.update',
        'destroy' => 'payments.destroy'
    ])->parameters(['' => 'payment'])->only([
        'show', 'edit', 'update', 'destroy'
    ]);

    Route::get('transport-orders/{transportOrder}/payments/create', [PaymentController::class, 'create'])->name('payments.create');
});

Route::get('/payments/lookup-summary', [PaymentController::class, 'lookupSummary'])->name('payments.lookup-summary');
Route::get('/payments/create-form', [PaymentController::class, 'createForm'])->name('payments.create-form');
// Standalone payments
Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');

// Transport-order specific payments
Route::post('/transport-orders/{transportOrder}/payments', [PaymentController::class, 'store'])
    ->name('transport-orders.payments.store');

// Lookup route
Route::post('/payments/lookup', [PaymentController::class, 'lookup'])->name('payments.lookup');
Route::delete('/documents/{document}', 'DocumentController@destroy')->name('documents.destroy');
// Transport Reports Routes
Route::get('/transport-reports', 'TransportReportController@index')->name('transport-reports.index');
Route::post('/transport-reports/generate', 'TransportReportController@generateReport')->name('transport-reports.generate');

// Production Routes
Route::get('production', 'ProductionController@index')->name('production.index');
Route::post('production/store', 'ProductionController@store')->name('production.store');
Route::get('production/data', 'ProductionController@data')->name('production.data');
Route::delete('production/{id}', 'ProductionController@destroy')->name('production.destroy');

// Production Report Routes
Route::get('production-reports', 'ProductionReportController@index')->name('production-reports.index');
Route::get('production-reports/filter', 'ProductionReportController@filter')->name('production-report-filter');