@section('page_css')
    <style>

    </style>
@endsection

@section("content")

    <div class="col-xl-12">
        <div class="card-columns">
            <div class="card" id="zoom">
                <img
                    style="margin-left: auto; margin-right: auto; margin-top: 10%; display: block; width: 35%;padding: 10px;"
                    src="{{asset('img/2.svg')}}" alt="Card image cap">
                <div class="card-body" style="margin-top: -5%">
                    <h5 class="card-title">Sales</h5>
                    <p class="card-text">Manages all sales transactions and customer records.</p>
                </div>
            </div>
            <div class="card" id="zoom">
                <img
                    style="margin-left: auto; margin-right: auto; margin-top: 10%; display: block; width: 35%;padding: 10px;"
                    src="{{asset('img/accounting.png')}}" alt="Card image cap">
                <div class="card-body" style="margin-top: -5%">
                    <h5 class="card-title">Accounting</h5>
                    <p class="card-text">Manage all financial records, including tracking expenses and Invoices.</p>
                </div>
            </div>
            <div class="card" id="zoom">
                <img
                    style="margin-left: auto; margin-right: auto; margin-top: 10%; display: block; width: 35%;padding: 10px;"
                    src="{{asset('img/inventory.png')}}" alt="Card image cap">
                <div class="card-body" style="margin-top: -10%">
                    <h5 class="card-title">Inventory</h5>
                    <p class="card-text">Controls all stock operations and product management.</p>
                </div>
            </div>
            <div class="card" id="zoom">
                <img
                    style="margin-left: auto; margin-right: auto; height: 120px; margin-top: 10%; display: block; width: 30%;padding: 10px;"
                    src="{{asset('img/5.svg')}}" alt="Card image cap">
                <div class="card-body" style="margin-top: -10%">
                    <h5 class="card-title">Reports</h5>
                    <p class="card-text">View and analyze reports for sales, inventory, purchases and accounting.</p>
                </div>
            </div>
            <div class="card" id="zoom">
                <img
                    style="margin-left: auto; margin-right: auto; height: 100px; margin-top: 10%; display: block; width: 30%;padding: 10px;"
                    src="{{asset('img/1.svg')}}" alt="Card image cap">
                <div class="card-body" style="margin-top: -10%">
                    <h5 class="card-title">Purchasing</h5>
                    <p class="card-text">Handles all purchase transactions and supplier management.</p>
                </div>
            </div>
            <div class="card" id="zoom">
                <img
                    style="margin-left: auto; margin-right: auto; margin-top: 10%; display: block; width: 35%;padding: 10px;"
                    src="{{asset('img/4.svg')}}" alt="Card image cap">
                <div class="card-body" style="margin-top: -7%">
                    <h5 class="card-title">Settings</h5>
                    <p class="card-text">Configure general preferences and manage security options.</p>
                </div>
            </div>
        </div>
    </div>
@endsection

