@section('page_css')
    <style>

    </style>
@endsection

@section("content")

    <div class="col-xl-12">
        <div class="card-columns">
            <div class="card" id="zoom">
                <img style="margin-left: auto; margin-right: auto; display: block; width: 35%;padding: 10px;"
                     src="{{asset('img/coupon.png')}}" alt="Card image cap">
                <div class="card-body">
                    <h5 class="card-title">Sales</h5>
                    <p class="card-text">Sales Module enables you to manage cash & credit sales, quotes, payments.</p>
                </div>
            </div>
            <div class="card" id="zoom">
                <img style="margin-left: auto; margin-right: auto; display: block; width: 35%;padding: 10px;"
                     src="{{asset('img/inventory.png')}}" alt="Card image cap">
                <div class="card-body">
                    <h5 class="card-title">Inventory</h5>
                    <p class="card-text">Inventory Module handles stock quantities, prices, stock adjustment, stock
                        transfer.</p>
                </div>
            </div>
            <div class="card" id="zoom">
                <img style="margin-left: auto; margin-right: auto; display: block; width: 35%;padding: 10px;"
                     src="{{asset('img/cart.png')}}" alt="Card image cap">
                <div class="card-body">
                    <h5 class="card-title">Purchases</h5>
                    <p class="card-text">Purchases Module manages purchases orders, goods receiving, supplier
                        invoices.</p>
                </div>
            </div>
            <div class="card" id="zoom">
                <img style="margin-left: auto; margin-right: auto; display: block; width: 35%;padding: 10px;"
                     src="{{asset('img/expenses.png')}}" alt="Card image cap">
                <div class="card-body">
                    <h5 class="card-title">Expenses</h5>
                    <p class="card-text">Expenses Module track and report all expenses associated with the business.</p>
                </div>
            </div>
            <div class="card" id="zoom">
                <img style="margin-left: auto; margin-right: auto; display: block; width: 35%;padding: 10px;"
                     src="{{asset('img/report.png')}}" alt="Card image cap">
                <div class="card-body">
                    <h5 class="card-title">Reports</h5>
                    <p class="card-text">Reports Module generates various kinds of reports for data visualization &
                        analytics.</p>
                </div>
            </div>
            <div class="card" id="zoom">
                <img style="margin-left: auto; margin-right: auto; display: block; width: 35%;padding: 10px;"
                     src="{{asset('img/list.png')}}" alt="Card image cap">
                <div class="card-body">
                    <h5 class="card-title">Masters</h5>
                    <p class="card-text">Masters Module manages products, categories, suppliers, stores, locations.</p>
                </div>
            </div>
        </div>
    </div>
@endsection

