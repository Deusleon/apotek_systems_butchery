<li class="nav-item"><a href="{{route('home')}}" class="nav-link"><span class="pcoded-micon">
            <i class="fas fa-tachometer-alt"></i></span><span class="pcoded-mtext">Dashboard</span></a>
</li>

<li class="nav-item pcoded-hasmenu">
    @if(auth()->user()->checkPermission('View Sales'))
        <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="fas fa-money-check-alt"></i></span>
            <span class="pcoded-mtext">Sales</span>
        </a>
        <ul class="pcoded-submenu">
            @if(auth()->user()->checkPermission('View Cash Sales'))
                <li class=""><a href="{{route('cash-sales.cashSale')}}" class="">Cash Sales</a></li>
            @endif
            @if(auth()->user()->checkPermission('View Credit Sales') || auth()->user()->checkPermission('View Credit Tracking') || auth()->user()->checkPermission('View Credit Payments'))
                <li class=""><a href="{{route('credit-sales.creditSale')}}" class="">Credit Sales</a></li>
            @endif
            @if(auth()->user()->checkPermission('View Sales Order') || auth()->user()->checkPermission('View Order List'))
                <li class=""><a href="{{route('sale-quotes.index')}}" class="">Sales Order</a></li>
            @endif
            @if(auth()->user()->checkPermission('View Sales History'))
                <li class=""><a href="{{route('sale-histories.SalesHistory')}}" class="">Sales History</a></li>
            @endif
            @if(auth()->user()->checkPermission('View Customers'))
                <li class=""><a href="{{route('customers.index')}}" class="">Customers</a></li>
            @endif
        </ul>
    @endif
</li>

<li class="nav-item pcoded-hasmenu">
    @if(auth()->user()->checkPermission('View Purchasing'))
        <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="fas fa-shopping-cart"></i></span>
            <span class="pcoded-mtext">Purchasing</span>
        </a>
        <ul class="pcoded-submenu">
            @if(auth()->user()->checkPermission('View Goods Receiving'))
                <li class=""><a href="{{route('goods-receiving.index')}}" class="">Goods Receiving</a></li>
            @endif
            @if(auth()->user()->checkPermission('View Purchase Returns'))
            <li class=""><a href="{{route('purchase-return.returns')}}" class="">Purchase Returns</a></li>
            @endif
            @if(auth()->user()->checkPermission('View Purchase Order'))
                <li class=""><a href="{{route('purchase-order.index')}}" class="">Purchase Order</a></li>
            @endif
            @if(auth()->user()->checkPermission('View Suppliers'))
                <li class=""><a href="{{route('suppliers.index')}}" class="">Suppliers</a></li>
            @endif
        </ul>
    @endif
</li>

<li class="nav-item pcoded-hasmenu">
    @if(auth()->user()->checkPermission('View Inventory'))
        <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="fas fa-dolly"></i></span>
            <span class="pcoded-mtext">Inventory</span>
        </a>
        <ul class="pcoded-submenu">
             @if(auth()->user()->checkPermission('View Product List'))
                <li class=""><a href="{{route('products.index')}}" class="">Product List</a></li>
            @endif
            @if(auth()->user()->checkPermission('Products Import'))
                <li class="nav-item"><a href="{{route('import-products')}}" class="nav-link"><span class="pcoded-mtext">Products
                            Import</span></a>
                </li>
            @endif
            @if(auth()->user()->checkPermission('View Current Stock'))
                <li class=""><a href="{{ route('current-stocks') }}" class="">Current Stock</a></li>
            @endif
            @if(auth()->user()->checkPermission('View Stock Details'))
                <li class=""><a href="{{ route('stock-details') }}" class="">Stock Details</a></li>
            @endif
            @if(auth()->user()->checkPermission('View Price List'))
                <li class=""><a href="{{ route('price-list.index') }}" class="">Price List</a></li>
            @endif
             @if(auth()->user()->checkPermission('View Stock Adjustment'))
                <li class=""><a href="{{ route('new-stock-adjustment') }}" class="">Stock Adjustment</a></li>
            @endif
            @if(auth()->user()->checkPermission('View Stock Requisition'))
                <li class=""><a href="{{ route('requisitions.create')}}" class="">Stock Requisition</a></li>
            @endif
            @if(auth()->user()->checkPermission('View Stock Issue'))
                <li class=""><a href="{{ route('issue.index') }}" class="">Stock Issue</a></li>
            @endif
            @if(auth()->user()->checkPermission('View Stock Transfer'))
                <li class=""><a href="{{ route('stock-transfer.index') }}" class="">Stock Transfer</a></li>
            @endif
            @if(auth()->user()->checkPermission('View Stock Count'))
                <li class=""><a href="{{ route('daily-stock-count.index') }}" class="">Stock Count</a></li>
            @endif
            @if(auth()->user()->checkPermission('View Stock Discrepancy Report'))
                <li class=""><a href="{{ route('stock-discrepancy-report') }}" class="">Stock Discrepancy Report</a></li>
            @endif
            @if(auth()->user()->checkPermission('View Batch Stock Count'))
                <li class=""><a href="{{ route('batch-stock-count.index') }}" class="">Batch Stock Count</a></li>
            @endif
            @if(auth()->user()->checkPermission('View Stock Count Schedules'))
                <li class=""><a href="{{ route('stock-count-schedules.index') }}" class="">Stock Count Schedules</a></li>
            @endif
        </ul>
    @endif
</li>


<li class="nav-item pcoded-hasmenu">
    @if(auth()->user()->checkPermission('View Transport'))
        <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="fas fa-truck"></i></span>
            <span class="pcoded-mtext">Transport</span>
        </a>
        <ul class="pcoded-submenu">
            <li class=""><a href="{{ route('transport-logistics.transporters.index') }}" class="">Transporters</a></li>
            <li class=""><a href="{{ route('vehicles.index') }}" class="">Vehicles</a></li>
            <li class=""><a href="{{ route('transport-orders.index') }}" class="">Orders</a></li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('payments.index') }}">
                    <i class="feather icon-dollar-sign"></i>
                    <span>Payments</span>
                </a>
            </li>
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
            @if(auth()->user()->checkPermission('View Invoices'))
                <li class=""><a href="{{route('invoice-management.index')}}" class="">Invoices</a></li>
            @endif
            {{-- @if(auth()->user()->checkPermission('View Assets'))
            <li class=""><a href="#" class="">Assets</a></li>
            @endif
            @if(auth()->user()->checkPermission('View Cash Flow'))
            <li class=""><a href="#" class="">Cash Flow</a></li>
            @endif
            --}}
        </ul>


    @endif
</li>


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
            @if(auth()->user()->checkPermission('View Stock Count Analytics'))
                <li class=""><a href="{{ route('stock-count-analytics') }}" class="">Stock Count Analytics</a></li>
            @endif
            @if(auth()->user()->checkPermission('View Transport Reports'))
                <li class=""><a href="{{route('transport-report-index')}}" class="">Transport Reports</a></li>
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
            @if(auth()->user()->checkPermission('View General'))
                <li>
                    <a href="#">General</a>
                    <ul class="pcoded-submenu">
                        @if(auth()->user()->checkPermission('View Configurations'))
                            <li class="nav-item"><a href="{{route('configurations.index')}}" class="nav-link">
                                    <span class="pcoded-mtext">Configurations</span></a>
                            </li>
                        @endif
                        @if(auth()->user()->checkPermission('View Product Categories'))
                            <li class=""><a href="{{route('product-categories.index')}}" class="">Product Categories</a></li>
                        @endif
                        {{-- @if('View Product Subcategories')
                        <li class=""><a href="{{route('sub-categories.index')}}" class="">Product Subcategories</a></li>
                        @endif --}}
                        @if(auth()->user()->checkPermission('View Price Categories'))
                            <li class=""><a href="{{route('price-categories.index')}}" class="">Price Categories</a></li>
                        @endif
                        @if(auth()->user()->checkPermission('View Expense Categories'))
                            <li class=""><a href="{{route('expense-categories.index')}}" class="">Expense Categories</a></li>
                        @endif
                        @if(auth()->user()->checkPermission('View Expense Sub Categories'))
                            <li class=""><a href="{{route('expense-subcategories.index')}}" class="">Expense Subcategories</a></li>
                        @endif
                        @if(auth()->user()->checkPermission('View Adjustment Reasons'))
                            <li class=""><a href="{{route('adjustment-reasons.index')}}" class="">Adjustment Reasons</a></li>
                        @endif
                        @if(auth()->user()->checkPermission('View Branches'))
                            <li class=""><a href="{{route('stores.index')}}" class="">Branches</a></li>
                        @endif
                        @if(auth()->user()->checkPermission('View Terms and Conditions'))
                            <li class="">
                                <a href="{{route('general-settings.index')}}" class="">Terms and Conditions</a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif
            @if(auth()->user()->checkPermission('View Security'))
                <li>
                    <a href="#">Security</a>
                    <ul class="pcoded-submenu">
                        @if(auth()->user()->checkPermission('View Roles'))
                            <li class=""><a href="{{route('roles.index')}}" class="">Roles</a></li>
                        @endif
                        @if(auth()->user()->checkPermission('View Users'))
                            <li class=""><a href="{{route('users.index')}}" class="">Users</a></li>
                        @endif
                        @if(auth()->user()->checkPermission('View Activities'))
                            <li class=""><a href="{{route('user-activities')}}" class="">Activities</a></li>
                        @endif
                    </ul>
                </li>
            @endif
            {{-- @if(auth()->user()->checkPermission('View Alerts'))
                <li>
                    <a href="#">Alerts</a>
                    <ul class="pcoded-submenu">
                        <li class=""><a href="#" class="">Alerts</a></li>
                        <li class=""><a href="#" class="">Alerts Details</a></li>
                        <li class=""><a href="#" class="">Min/Max Levels</a></li>
                    </ul>
                </li>
            @endif --}}
            @if(auth()->user()->checkPermission('View Tools'))
                <li>
                    <a href="#">Tools</a>
                    <ul class="pcoded-submenu">
                        <li class=""><a href="{{ route('database-backup.index') }}" class="">Database Backup</a></li>
                        <li class=""><a href="{{ route('tools.export-products') }}" class="">Export Stock to Excel</a></li>
                        <li class=""><a href="{{ route('tools.upload-price-form') }}" class="">Upload Price</a></li>
                        <li class=""><a href="#" class="">Clear Database</a></li>
                        <li class=""><a href="#" class="">Upload Stock</a></li>
                        <li class=""><a href="#" class="">Reset Stock</a></li>
                    </ul>
                </li>
            @endif
        </ul>

    @endif
</li>

<li class="nav-item pcoded-menu-caption">
    <label>HELP</label>
</li>

<li class="nav-item"><a href="{{ url('help/contact-us') }}" class="nav-link"><span class="pcoded-micon"><i
                class="fas fa-tablet"></i></span><span class="pcoded-mtext">Contact Us</span></a></li>
<li class="nav-item"><a href="{{ url('help/support') }}" class="nav-link"><span class="pcoded-micon"><i
                class="fas fa-question"></i></span><span class="pcoded-mtext">Support</span></a>
</li>