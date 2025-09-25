<li class="nav-item"><a href="{{route('home')}}" class="nav-link"><span class="pcoded-micon">
    <i class="fas fa-tachometer-alt"></i></span><span class="pcoded-mtext">Dashboard</span></a>
</li>

<li class="nav-item pcoded-hasmenu">
    @if(auth()->user()->checkPermission('View Sales Management'))
        <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="fas fa-money-check-alt"></i></span>
            <span class="pcoded-mtext">Sales</span>
        </a>
        <ul class="pcoded-submenu">
            @if(auth()->user()->checkPermission('Cash Sales'))
                <li class=""><a href="{{route('cash-sales.cashSale')}}" class="">Cash Sale</a></li>
            @endif
            @if(auth()->user()->checkPermission('Credit Sales'))
                <li class=""><a href="{{route('credit-sales.creditSale')}}" class="">Credit Sales</a></li>
            @endif
            @if(auth()->user()->checkPermission('Sales Quotes'))
                <li class=""><a href="{{route('sale-quotes.index')}}" class="">Sales Order</a></li>
            @endif
            @if(auth()->user()->checkPermission('View Sales History'))
                <li class=""><a href="{{route('sale-histories.SalesHistory')}}" class="">Sales History</a></li>
            @endif
{{--            @if(auth()->user()->checkPermission('Sales Return'))--}}
{{--                <li class=""><a href="{{route('sale-returns.index')}}" class="">Sales Return</a></li>--}}
{{--            @endif--}}
{{--            @if(auth()->user()->checkPermission('Sales Return Approval'))--}}
{{--                <li class=""><a href="{{route('sale-returns-approval.getSalesReturn')}}" class="">Returns Approval</a>--}}
{{--                </li>--}}
{{--            @endif--}}
{{--            @if(auth()->user()->checkPermission('View Credit Tracking'))--}}
{{--                <li class=""><a href="{{route('credits-tracking.creditsTracking')}}" class="">Credit Tracking</a></li>--}}
{{--            @endif--}}
{{--            @if(auth()->user()->checkPermission('View Payment History'))--}}
{{--                <li class=""><a href="{{route('payments.getPaymentsHistory')}}" class="">Payment History</a></li>--}}
{{--            @endif--}}
            @if(auth()->user()->checkPermission('View Customers'))
                <li class=""><a href="{{route('customers.index')}}" class="">Customers</a></li>
            @endif
        </ul>
    @endif
</li>


<li class="nav-item pcoded-hasmenu">
    @if(auth()->user()->checkPermission('View Purchase Management'))
        <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="fas fa-shopping-cart"></i></span>
            <span class="pcoded-mtext">Purchasing</span>
        </a>
        <ul class="pcoded-submenu">
            @if(auth()->user()->checkPermission('Goods Receiving'))
                @if(auth()->user()->store_id == '1')
                <li class=""><a href="{{route('goods-receiving.index')}}" class="">Goods Receiving</a></li>
                @endif
            @endif
            @if(auth()->user()->checkPermission('Purchase Order'))
                <li class=""><a href="{{route('purchase-order.index')}}" class="">Purchase Order</a></li>
            @endif
{{--            @if(auth()->user()->checkPermission('View Purchase Order History'))--}}
{{--                <li class=""><a href="{{route('order-history.index')}}" class="">Purchase Order History</a></li>--}}
{{--            @endif--}}

            @if(auth()->user()->checkPermission('View Suppliers'))
                <li class=""><a href="{{ url('requisitions.index')}}" class="">Requisition</a></li>
            @endif
            @if(auth()->user()->checkPermission('View Stock Issue'))
                <li class=""><a href="{{ route('issue.index') }}" class="">Issue</a></li>
            @endif

            @if(auth()->user()->checkPermission('View Suppliers'))
                <li class=""><a href="{{route('suppliers.index')}}" class="">Suppliers</a></li>
            @endif
{{--            @if(auth()->user()->checkPermission('View Goods Received'))--}}
{{--                <li class=""><a href="{{route('material-received.index')}}" class="">Material Received</a></li>--}}
{{--            @endif--}}
{{--            @if(auth()->user()->checkPermission('Invoice Management'))--}}
{{--                <li class=""><a href="{{route('invoice-management.index')}}" class="">Invoice Management</a></li>--}}
{{--            @endif--}}
        </ul>
    @endif
</li>

<li class="nav-item pcoded-hasmenu">
    @if(auth()->user()->checkPermission('View Inventory Management'))
        <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="fas fa-dolly"></i></span>
            <span class="pcoded-mtext">Inventory</span>
        </a>
        <ul class="pcoded-submenu">
            @if(auth()->user()->checkPermission('View Current Stock'))
                <li class=""><a href="{{ route('current-stocks') }}" class="">Current Stock</a></li>
            @endif
            @if(auth()->user()->checkPermission('View Products'))
                <li class=""><a href="{{route('products.index')}}" class="">Product List</a></li>
            @endif
            @if(auth()->user()->checkPermission('View Price List'))
                <li class=""><a href="{{ route('price-list.index') }}" class="">Price List</a></li>
            @endif
            @if(auth()->user()->checkPermission('View Stock Transfer'))
                <li class=""><a href="{{ route('stock-transfer-history') }}" class="">Stock Transfer</a></li>
            @endif
            {{--            @if(auth()->user()->checkPermission('View Stock Transfer History'))--}}
            {{--                <li class=""><a href="{{ route('stock-transfer-reprint.index') }}" class="">Transfer History</a></li>--}}
            {{--            @endif--}}
            @if(auth()->user()->checkPermission('View Stock Adjustment'))
                <li class=""><a href="{{ route('stock-adjustment') }}" class="">Stock Adjustment</a></li>
            @endif
            {{--            @if(auth()->user()->checkPermission('View Outgoing Stock'))--}}
            {{--                <li class=""><a href="{{ route('out-going-stock.index') }}" class="">Outgoing Stock</a></li>--}}
            {{--            @endif--}}
            {{--            @if(auth()->user()->checkPermission('View Product Ledger'))--}}
            {{--                <li class=""><a href="{{ route('product-ledger.index') }}" class="">Product Ledger</a></li>--}}
            {{--            @endif--}}
            @if(auth()->user()->checkPermission('View Daily Stock Count'))
                <li class=""><a href="{{ route('daily-stock-count.index') }}" class="">Stock Count</a></li>
            @endif
            {{--            @if(auth()->user()->checkPermission('View Inventory Count Sheet'))--}}
            {{--                <li class=""><a href="{{ route('inventory-count-sheet-pdf-gen') }}" target="_blank" class="">Inventory--}}
            {{--                        Count--}}
            {{--                        Sheet</a></li>--}}
            {{--            @endif--}}

{{--            @if(auth()->user()->checkPermission('View Stock Issue'))--}}
{{--                <li class=""><a href="{{ route('stock-issue-history') }}" class="">Stock Issue</a></li>--}}
{{--            @endif--}}
{{--            @if(auth()->user()->checkPermission('Issue Re-Print'))--}}
{{--                <li class=""><a href="{{ route('stock-issue-reprint.index') }}" class="">Issue History</a></li>--}}
{{--            @endif--}}
        </ul>
    @endif
</li>

<li class="nav-item pcoded-hasmenu">
    @if(auth()->user()->checkPermission('View Accounting'))
        <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="fas fa-stream"></i></span>
            <span class="pcoded-mtext">Accounting</span>
        </a>
        <ul class="pcoded-submenu">
                    @if(auth()->user()->checkPermission('View Expenses'))
                        <li class="nav-item"><a href="{{route('expense.index')}}" class="nav-link">
                                <span class="pcoded-mtext">Expenses</span></a>
                        </li>
                    @endif
                    @if(auth()->user()->checkPermission('Invoice Management'))
                        <li class=""><a href="{{route('invoice-management.index')}}" class="">Invoice Management</a></li>
                    @endif
                    @if('View Product Subcategories')
                        <li class=""><a href="{{route('sub-categories.index')}}" class="">Assets Management</a></li>
                    @endif
                    @if(auth()->user()->checkPermission('View Price Categories'))
                        <li class=""><a href="{{route('price-categories.index')}}" class="">Balance Sheets</a></li>
                    @endif
                    @if(auth()->user()->checkPermission('View Expense Categories'))
                        <li class=""><a href="{{route('expense-categories.index')}}" class="">Bank Reconciliation</a></li>
                    @endif
                    <li class=""><a href="{{route('expense-subcategories.index')}}" class="">Budget Management</a></li>
                    @if(auth()->user()->checkPermission('View Adjustment Reasons'))
                        <li class=""><a href="{{route('adjustment-reasons.index')}}" class="">Cash Flow Management</a></li>
                    @endif
                    @if(auth()->user()->checkPermission('View Stores'))
                        <li class=""><a href="{{route('stores.index')}}" class="">Chart of Accounts</a></li>
                    @endif
                    @if(auth()->user()->checkPermission('View Locations'))
                        <li class=""><a href="{{route('locations.index')}}" class="">Financial Statements</a></li>
                    @endif
        </ul>


    @endif
</li>

{{--<li class="nav-item pcoded-hasmenu">--}}
{{--    @if(auth()->user()->checkPermission('View Expense Management'))--}}
{{--        <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="fas fa-dollar-sign"></i></span>--}}
{{--            <span class="pcoded-mtext">Expenses</span>--}}
{{--        </a>--}}
{{--        @if(auth()->user()->checkPermission('View Expenses'))--}}
{{--            <ul class="pcoded-submenu">--}}
{{--                <li class=""><a href="{{route('expense.index')}}" class="">Expenses</a></li>--}}
{{--            </ul>--}}
{{--        @endif--}}
{{--    @endif--}}
{{--</li>--}}

<li data-username="Vertical Horizontal Box Layout RTL fixed static collapse menu color icon dark"
    class="nav-item pcoded-hasmenu">
    @if(auth()->user()->checkPermission('View Reports'))
        <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="fas fa-file-pdf"></i></span><span
                class="pcoded-mtext">Reports</span></a>
        <ul class="pcoded-submenu">
            @if(auth()->user()->checkPermission('View Sales Reports'))
                <li class=""><a href="{{route('sale-report-index')}}" class="">Sales Reports</a></li>
            @endif
            @if(auth()->user()->checkPermission('View Inventory Reports'))
                <li class=""><a href="{{route('inventory-report-index')}}" class="">Inventory Reports</a></li>
            @endif
            @if(auth()->user()->checkPermission('View Purchase Reports'))
                <li class=""><a href="{{route('purchase-report-index')}}" class="">Purchase Reports</a>
                </li>
            @endif
            @if(auth()->user()->checkPermission('View Accounting Reports'))
                <li class=""><a href="{{route('accounting-report-index')}}" class="">Accounting Reports</a>
                </li>
            @endif
        </ul>
    @endif
</li>


<li class="nav-item pcoded-hasmenu">
    @if(auth()->user()->checkPermission('View Settings'))
        <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="fas fa-stream"></i></span>
            <span class="pcoded-mtext">Settings</span>
        </a>
        <ul class="pcoded-submenu">
            <li>
                <a href="#">General</a>
                <ul class="pcoded-submenu">
            @if(auth()->user()->checkPermission('View Settings'))
            <li class="nav-item"><a href="{{route('configurations.index')}}" class="nav-link">
                    <span class="pcoded-mtext">Configurations</span></a>
            </li>
            @endif
            @if(auth()->user()->checkPermission('View Products Categories'))
                <li class=""><a href="{{route('product-categories.index')}}" class="">Product Categories</a></li>
            @endif
            @if('View Product Subcategories')
                <li class=""><a href="{{route('sub-categories.index')}}" class="">Product Subcategories</a></li>
            @endif
            @if(auth()->user()->checkPermission('View Price Categories'))
                <li class=""><a href="{{route('price-categories.index')}}" class="">Price Categories</a></li>
            @endif
            @if(auth()->user()->checkPermission('View Expense Categories'))
                <li class=""><a href="{{route('expense-categories.index')}}" class="">Expense Categories</a></li>
            @endif
            <li class=""><a href="{{route('expense-subcategories.index')}}" class="">Expense Subcategories</a></li>
            @if(auth()->user()->checkPermission('View Adjustment Reasons'))
                <li class=""><a href="{{route('adjustment-reasons.index')}}" class="">Adjustment Reasons</a></li>
            @endif
            @if(auth()->user()->checkPermission('View Stores'))
                <li class=""><a href="{{route('stores.index')}}" class="">Branches</a></li>
            @endif
            @if(auth()->user()->checkPermission('View Locations'))
                <li class=""><a href="{{route('locations.index')}}" class="">Locations</a></li>
            @endif
            @if(auth()->user()->checkPermission('View Data Import'))
                <li class="nav-item"><a href="{{route('import.index')}}" class="nav-link"><span
                            class="pcoded-mtext">Import</span></a>
                </li>
            @endif
             <li class=""><a href="{{route('general-settings.index')}}" class="">Terms and Conditions</a></li>
        </ul>
            </li>
            <li>
                <a href="#">Security</a>
                <ul class="pcoded-submenu">
                    @if(auth()->user()->checkPermission('View Roles'))
                        <li class=""><a href="{{route('roles.index')}}" class="">Roles</a></li>
                    @endif
                    @if(auth()->user()->checkPermission('View Users'))
                        <li class=""><a href="{{route('users.index')}}" class="">Users</a></li>
                    @endif
                </ul>
            </li>
            <li>
                <a href="#">Alerts</a>
                <ul class="pcoded-submenu">
                        <li class=""><a href="#" class="">Alerts</a></li>
                        <li class=""><a href="#" class="">Alerts Details</a></li>
                        <li class=""><a href="#" class="">Min/Max Levels</a></li>
                        <li class=""><a href="#" class="">Database Backup</a></li>
                        <li class=""><a href="#" class="">Export to Excel</a></li>
                </ul>
            </li>

        </ul>


    @endif
</li>

<li class="nav-item pcoded-menu-caption">
    <label>HELP</label>
</li>
<li class="nav-item"><a href="" class="nav-link" target="_blank"><span class="pcoded-micon"><i
                class="fas fa-tablet"></i></span><span class="pcoded-mtext">Contact Us</span></a></li>
<li class="nav-item"><a href="" class="nav-link" target="_blank"><span class="pcoded-micon"><i
                class="fas fa-question"></i></span><span class="pcoded-mtext">Support</span></a>
</li>


