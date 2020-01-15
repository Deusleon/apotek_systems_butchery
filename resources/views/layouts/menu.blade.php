<li class="nav-item"><a href="{{route('home')}}" class="nav-link"><span class="pcoded-micon">
    <i class="fas fa-tachometer-alt"></i></span><span class="pcoded-mtext">Dashboard</span></a>
</li>
@can('View Sales Management')
    <li class="nav-item pcoded-hasmenu">
        <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="fas fa-money-check-alt"></i></span>
            <span class="pcoded-mtext">Sales</span>
        </a>
        <ul class="pcoded-submenu">
            @can('Cash Sales')
                <li class=""><a href="{{route('cash-sales.cashSale')}}" class="">Point of Sale</a></li>
            @endcan
            @can('Credit Sales')
                <li class=""><a href="{{route('credit-sales.creditSale')}}" class="">Credit Sales</a></li>
            @endcan
            @can('View Sales History')
                <li class=""><a href="{{route('sale-histories.SalesHistory')}}" class="">Sales History</a></li>
            @endcan
            @can('Sales Quotes')
                <li class=""><a href="{{route('sale-quotes.index')}}" class="">Sales Quotes</a></li>
            @endcan
            @can('Sales Return')
                <li class=""><a href="{{route('sale-returns.index')}}" class="">Sales Return</a></li>
            @endcan
            @can('Sales Return Approval')
                <li class=""><a href="{{route('sale-returns-approval.getSalesReturn')}}" class="">Returns Approval</a>
                </li>
            @endcan
        <!--  This is no longer used as Credit Tracking is also used for Payment -->
        <!--  <li class=""><a href="{{route('credit-payments.getCreditsCustomers')}}" class="">Credits Payment</a></li> -->
            @can('View Credit Tracking')
                <li class=""><a href="{{route('credits-tracking.creditsTracking')}}" class="">Credit Tracking</a></li>
            @endcan
            @can('View Sales History')
                <li class=""><a href="{{route('payments.getPaymentsHistory')}}" class="">Payment History</a></li>
            @endcan
            @can('View Customers')
                <li class=""><a href="{{route('customers.index')}}" class="">Customers</a></li>
            @endcan
        </ul>
    </li>
@endcan


<li class="nav-item pcoded-hasmenu">
    @can('View Inventory Management')
        <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="fas fa-dolly"></i></span>
            <span class="pcoded-mtext">Inventory</span>
        </a>
        <ul class="pcoded-submenu">
            @can('View Current Stock')
                <li class=""><a href="{{ route('current-stock.index') }}" class="">Current Stock</a></li>
            @endcan
            @can('View Price List')
                <li class=""><a href="{{ route('price-list.index') }}" class="">Price List</a></li>
            @endcan
            @can('View Stock Adjustment')
                <li class=""><a href="{{ route('stock-adjustment.index') }}" class="">Adjustment History</a></li>
            @endcan
            @can('View Outgoing Stock')
                <li class=""><a href="{{ route('out-going-stock.index') }}" class="">Outgoing Stock</a></li>
            @endcan
            @can('View Product Ledger')
                <li class=""><a href="{{ route('product-ledger.index') }}" class="">Product Ledger</a></li>
            @endcan
            @can('View Daily Stock Count')
                <li class=""><a href="{{ route('daily-stock-count.index') }}" class="">Daily Stock Count</a></li>
            @endcan
            @can('View Inventory Count Sheet')
                <li class=""><a href="{{ route('inventory-count-sheet-pdf-gen') }}" target="_blank" class="">Inventory
                        Count
                        Sheet</a></li>
            @endcan
            @can('View Stock Transfer')
                <li class=""><a href="{{ route('stock-transfer-history') }}" class="">Stock Transfer</a></li>
            @endcan
            @can('View Stock Transfer History')
                <li class=""><a href="{{ route('stock-transfer-reprint.index') }}" class="">Transfer History</a></li>
            @endcan
            @can('View Stock Issue')
                <li class=""><a href="{{ route('stock-issue-history') }}" class="">Stock Issue</a></li>
            @endcan
            @can('Issue Re-Print')
                <li class=""><a href="{{ route('stock-issue-reprint.index') }}" class="">Issue History</a></li>
            @endcan
        </ul>
    @endcan
</li>

<li class="nav-item pcoded-hasmenu">
    @can('View Purchase Management')
        <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="fas fa-shopping-cart"></i></span>
            <span class="pcoded-mtext">Purchases</span>
        </a>
        <ul class="pcoded-submenu">
            @can('Purchase Order')
                <li class=""><a href="{{route('purchase-order.index')}}" class="">Purchase Order</a></li>
            @endcan
            @can('View Purchase Order History')
                <li class=""><a href="{{route('order-history.index')}}" class="">Purchase Order History</a></li>
            @endcan
            @can('Goods Receiving')
                <li class=""><a href="{{route('goods-receiving.index')}}" class="">Goods Receiving</a></li>
            @endcan
            @can('View Goods Received')
                <li class=""><a href="{{route('material-received.index')}}" class="">Material Received</a></li>
            @endcan
            @can('Invoice Management')
                <li class=""><a href="{{route('invoice-management.index')}}" class="">Invoice Management</a></li>
            @endcan
        </ul>
    @endcan
</li>

<li class="nav-item pcoded-hasmenu">
    @can('View Expense Management')
        <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="fas fa-dollar-sign"></i></span>
            <span class="pcoded-mtext">Expenses</span>
        </a>
        @can('View Expenses')
            <ul class="pcoded-submenu">
                <li class=""><a href="{{route('expense.index')}}" class="">Expenses</a></li>
            </ul>
        @endcan
    @endcan
</li>

<li data-username="Vertical Horizontal Box Layout RTL fixed static collapse menu color icon dark"
    class="nav-item pcoded-hasmenu">
    @can('View Reports')
        <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="fas fa-file-pdf"></i></span><span
                class="pcoded-mtext">Reports</span></a>
        <ul class="pcoded-submenu">
            @can('View Sales Reports')
                <li class=""><a href="{{route('sale-report-index')}}" class="">Sales Reports</a></li>
            @endcan
            @can('View Inventory Reports')
                <li class=""><a href="{{route('inventory-report-index')}}" class="">Inventory Reports</a></li>
            @endcan
            @can('View Purchase Reports')
                <li class=""><a href="{{route('purchase-report-index')}}" class="">Purchase Reports</a>
                </li>
            @endcan
            @can('View Accounting Reports')
                <li class=""><a href="{{route('accounting-report-index')}}" class="">Accounting Reports</a>
                </li>
            @endcan
        </ul>
    @endcan
</li>


<li class="nav-item pcoded-hasmenu">
    @can('View User Management')
        <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="fas fa-users"></i></span>
            <span class="pcoded-mtext">Users</span>
        </a>
        <ul class="pcoded-submenu">
            @can('View Roles')
                <li class=""><a href="{{route('roles.index')}}" class="">Roles</a></li>
            @endcan
            @can('View Users')
                <li class=""><a href="{{route('users.index')}}" class="">Users</a></li>
            @endcan
        </ul>
    @endcan
</li>

<li class="nav-item pcoded-hasmenu">
    @can('View Masters')
        <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="fas fa-stream"></i></span>
            <span class="pcoded-mtext">Masters</span>
        </a>
        <ul class="pcoded-submenu">
            @can('View Products')
                <li class=""><a href="{{route('products.index')}}" class="">Products</a></li>
            @endcan
            @can('View Products Categories')
                <li class=""><a href="{{route('categories.index')}}" class="">Product Categories</a></li>
            @endcan
            {{--            @can('View Product Subcategories')--}}
            {{--                <li class=""><a href="{{route('sub-categories.index')}}" class="">Product Subcategories</a></li>--}}
            {{--            @endcan--}}
            @can('View Price Categories')
                <li class=""><a href="{{route('price-categories.index')}}" class="">Price Categories</a></li>
            @endcan
            @can('View Expense Categories')
                <li class=""><a href="{{route('expense-categories.index')}}" class="">Expense Categories</a></li>
            @endcan
            {{--        <li class=""><a href="{{route('expense-subcategories.index')}}" class="">Expense Subcategories</a></li>--}}
            @can('View Adjustment Reasons')
                <li class=""><a href="{{route('adjustment-reasons.index')}}" class="">Adjustment Reasons</a></li>
            @endcan
            @can('View Suppliers')
                <li class=""><a href="{{route('suppliers.index')}}" class="">Suppliers</a></li>
            @endcan
            @can('View Stores')
                <li class=""><a href="{{route('stores.index')}}" class="">Stores</a></li>
            @endcan
            @can('View Locations')
                <li class=""><a href="{{route('locations.index')}}" class="">Locations</a></li>
            @endcan
            {{--        <!--   <li class=""><a href="{{route('general-settings.index')}}" class="">General Settings</a></li> -->--}}
        </ul>
    @endcan
</li>
@can('View Data Import')
    <li class="nav-item"><a href="{{route('import.index')}}" class="nav-link"><span class="pcoded-micon"><i
                    class="fas fa-file-excel"></i></span><span class="pcoded-mtext">Import</span></a>
    </li>
@endcan

@can('View Settings')
    <li class="nav-item"><a href="{{route('configurations.index')}}" class="nav-link"><span class="pcoded-micon"><i
                    class="feather icon-settings"></i></span><span class="pcoded-mtext">Settings</span></a>
    </li>
@endcan

<li class="nav-item pcoded-menu-caption">
    <label>Support</label>
</li>
<li class="nav-item"><a href="" class="nav-link" target="_blank"><span class="pcoded-micon"><i
                class="fas fa-tablet"></i></span><span class="pcoded-mtext">User Guide</span></a></li>
<li class="nav-item"><a href="" class="nav-link" target="_blank"><span class="pcoded-micon"><i
                class="fas fa-question"></i></span><span class="pcoded-mtext">Help Desk</span></a>
</li>


