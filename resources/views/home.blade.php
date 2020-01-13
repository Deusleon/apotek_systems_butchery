@extends("layouts.master")

@section('page_css')
    <style>
        #daily_sales {
            width: 100%;
            height: 500px;
        }

        #monthly_sales {
            width: 100%;
            height: 400px;
        }

        #sales_by_category {
            width: 100%;
            height: 400px;
        }

    </style>
@endsection

@section('content-title')
    Dashboard

@endsection

@section('content-sub-title')


@endsection



@section("content")

    <div class="col-sm-12">

        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="pill" href="#pills-home" role="tab" aria-selected="true">Sales
                    Summary</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="pill" href="#pills-stock" role="tab" aria-selected="false">Stock
                    Summary</a>
            </li>

        </ul>

        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                {{-- row 1 start --}}
                <div class="row">
                    <!-- [ Today sales section ] start -->
                    <div class="col-md-6 col-xl-4">
                        <div class="card">
                            <div class="card-block">
                                <h6 class="mb-4">Average Daily Sales</h6>
                                <div class="row d-flex align-items-center">
                                    <div class="col-9">
                                        <h3 class="f-w-300 d-flex align-items-center m-b-0">
                                            Tshs {{ number_format($avgDailySales, 2) }}</h3>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- [ Today sales section ] end -->

                    <!-- [ This week sales section ] start -->
                    <div class="col-md-6 col-xl-4">
                        <div class="card">
                            <div class="card-block">
                                <h6 class="mb-4">Today Sales</h6>
                                <div class="row d-flex align-items-center">
                                    <div class="col-9">
                                        <h3 class="f-w-300 d-flex align-items-center  m-b-0">
                                            @if ($todaySales > $avgDailySales)
                                                <i class="feather icon-arrow-up text-c-green f-30 m-r-10"></i>
                                            @else
                                                <i class="feather icon-arrow-down text-c-red f-30 m-r-10"></i>
                                            @endif

                                            Tshs {{ number_format($todaySales, 2) }}

                                        </h3>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- [ This week  sales section ] end -->

                    <!-- [ This month sales section ] start -->
                    <div class="col-md-6 col-xl-4">
                        <div class="card">
                            <div class="card-block">
                                <h6 class="mb-4">Average Monthly Sales</h6>
                                <div class="row d-flex align-items-center">
                                    <div class="col-9">
                                        <h3 class="f-w-300 d-flex align-items-center  m-b-0">
                                            Tshs {{ number_format($avgDailySales * 30,2) }}</h3>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- [ this month  sales section ] end -->

                </div>
                {{-- row 1 end --}}


                {{-- row 2 start --}}
                <div class="row">
                    <div class="col-md-6 col-xl-6">
                        <div id='monthly_sales'></div>
                    </div>
                    <div class="col-md-6 col-xl-6">
                        <div id='sales_by_category'></div>
                    </div>
                </div>

                {{-- row 3 start --}}
                <div class="row">
                    <div id='daily_sales'></div>
                </div>

            </div>
            {{-- /Tab 1 --}}


            {{-- Tab 2 --}}
            <div class="tab-pane fade" id="pills-stock" role="tabpanel" aria-labelledby="pills-stock-tab">
                {{-- row starts --}}
                <div class="row">

                    <div class="col-xl-4 col-md-6">
                        <div class="card">
                            <div class="card-block">
                                <div class="row align-items-center justify-content-center">
                                    <div class="col">
                                        <h3 class="text-c-red">{{$outOfStock}}</h3>
                                        <h5>Out of Stock</h5>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6">
                        <div class="card">
                            <div class="card-block">
                                <div class="row align-items-center justify-content-center">
                                    <div class="col">
                                        <h3 class="text-c-green">{{$outOfStock}}</h3>
                                        <h5>Below Minimum Level</h5>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-12">
                        <div class="card">
                            <div class="card-block">
                                <div class="row align-items-center justify-content-center">
                                    <div class="col">
                                        <h3 class="text-c-red">{{$expired}}</h3>
                                        <h5>Expired</h5>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- row ends --}}

                <div class="row">
                    <!-- [ Out of stock start -->
                    <div class="col-xl-6 col-md-6">
                        <div class="card Recent-Users">
                            <div class="card-header">
                                <h5>Out of Stock Items</h5>

                            </div>
                            <div class="card-block px-0 py-3">
                                <div class="table-responsive">
                                    <table id="stock_items" class="display table nowrap table-striped table-hover"
                                           style="width:100%">
                                        <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Item</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        @foreach($outOfStockList as $stock)
                                            <tr>
                                                <td>{{$stock->product_id}}</td>
                                                <td>{{$stock->product->name}}</td>

                                            </tr>
                                        @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- [ out of stock ] end -->
                    <!-- [ frequent items start -->
                    <div class="col-xl-6 col-md-6">
                        <div class="card Recent-Users">
                            <div class="card-header">
                                <h5> Fast Moving Items</h5>

                            </div>
                            <div class="card-block px-0 py-3">
                                <div class="table-responsive">
                                    <table id="fast_moving" class="display table nowrap table-striped table-hover"
                                           style="width:100%">
                                        <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Item</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        @foreach($outOfStockList as $stock)
                                            <tr>
                                                <td>{{$stock->product_id}}</td>
                                                <td>{{$stock->product->name}}</td>

                                            </tr>
                                        @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- [ frequent items ] end -->
                </div>
            </div>
            {{-- /Tab 2 --}}

        </div>
    </div>










@endsection



@push("page_scripts")

    <script src="{{asset("assets/plugins/amcharts4/core.js")}}"></script>
    <script src="{{asset("assets/plugins/amcharts4/charts.js")}}"></script>
    <script src="{{asset("assets/plugins/amcharts4/themes/animated.js")}}"></script>



    <script>

        $('#stock_items').DataTable({
            searching: true,
            bPaginate: true,
        });

        $('#fast_moving').DataTable({
            searching: true,
            bPaginate: true,
        });


        // Themes begin
        am4core.useTheme(am4themes_animated);
        // Themes end

        // Create chart instance
        var chart = am4core.create("daily_sales", am4charts.XYChart);

        // Add data
        chart.data = @json($totalDailySales);

        // Set input format for the dates
        chart.dateFormatter.inputDateFormat = "dd-MM-yyyy";

        // Create axes
        var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

        //title
        var title = chart.titles.create();
        title.text = "Total Daily Sales";
        title.fontSize = 16;
        title.marginBottom = 15;

        // Create series
        var series = chart.series.push(new am4charts.LineSeries());
        series.dataFields.valueY = "value";
        series.dataFields.dateX = "date";
        series.tooltipText = "{value}";
        series.strokeWidth = 2;
        series.minBulletDistance = 5;
        series.fillOpacity = 0.3;


        // Drop-shaped tooltips
        series.tooltip.background.cornerRadius = 20;
        series.tooltip.background.strokeOpacity = 0;
        series.tooltip.pointerOrientation = "vertical";
        series.tooltip.label.minWidth = 40;
        series.tooltip.label.minHeight = 40;
        series.tooltip.label.textAlign = "middle";
        series.tooltip.label.textValign = "middle";

        // Make bullets grow on hover
        var bullet = series.bullets.push(new am4charts.CircleBullet());
        bullet.circle.strokeWidth = 2;
        bullet.circle.radius = 4;
        bullet.circle.fill = am4core.color("#fff");

        var bullethover = bullet.states.create("hover");
        bullethover.properties.scale = 1.3;

        // Make a panning cursor
        chart.cursor = new am4charts.XYCursor();
        chart.cursor.behavior = "panXY";
        chart.cursor.xAxis = dateAxis;
        chart.cursor.snapToSeries = series;

        // Create vertical scrollbar and place it before the value axis
        chart.scrollbarY = new am4core.Scrollbar();
        chart.scrollbarY.parent = chart.leftAxesContainer;
        chart.scrollbarY.toBack();

        // Create a horizontal scrollbar with previe and place it underneath the date axis
        chart.scrollbarX = new am4charts.XYChartScrollbar();
        chart.scrollbarX.series.push(series);
        chart.scrollbarX.parent = chart.bottomAxesContainer;

        chart.events.on("ready", function () {
            dateAxis.zoom({start: 0.79, end: 1});
        });

    </script>

    <!-- Total MOnthly Sales chart -->
    <script>
        am4core.ready(function () {

            // Themes begin
            am4core.useTheme(am4themes_animated);
            // Themes end

            // Create chart instance
            var chart = am4core.create("monthly_sales", am4charts.XYChart);
            chart.scrollbarX = new am4core.Scrollbar();

            // Add data
            chart.data = @json($totalMonthlySales);


            //title
            var title = chart.titles.create();
            title.text = "Total Sales By Month";
            title.fontSize = 16;
            title.marginBottom = 15;
            // Create axes
            var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
            categoryAxis.dataFields.category = "month";
            categoryAxis.renderer.grid.template.location = 0;
            categoryAxis.renderer.minGridDistance = 30;
            categoryAxis.renderer.labels.template.horizontalCenter = "right";
            categoryAxis.renderer.labels.template.verticalCenter = "middle";
            categoryAxis.renderer.labels.template.rotation = 270;
            categoryAxis.tooltip.disabled = true;
            categoryAxis.renderer.minHeight = 110;

            var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
            valueAxis.renderer.minWidth = 50;

            // Create series
            var series = chart.series.push(new am4charts.ColumnSeries());
            series.sequencedInterpolation = true;
            series.dataFields.valueY = "amount";
            series.dataFields.categoryX = "month";
            series.tooltipText = "[{categoryX}: bold]{valueY}[/]";
            series.columns.template.strokeWidth = 0;

            series.tooltip.pointerOrientation = "vertical";

            series.columns.template.column.cornerRadiusTopLeft = 10;
            series.columns.template.column.cornerRadiusTopRight = 10;
            series.columns.template.column.fillOpacity = 0.8;

            // on hover, make corner radiuses bigger
            var hoverState = series.columns.template.column.states.create("hover");
            hoverState.properties.cornerRadiusTopLeft = 0;
            hoverState.properties.cornerRadiusTopRight = 0;
            hoverState.properties.fillOpacity = 1;

            series.columns.template.adapter.add("fill", function (fill, target) {
                return chart.colors.getIndex(target.dataItem.index);
            });

            // Cursor
            chart.cursor = new am4charts.XYCursor();

        }); // end am4core.ready()
    </script>

    <!-- Sales by Category -->
    <script>
        am4core.ready(function () {

            // Themes begin
            am4core.useTheme(am4themes_animated);
            // Themes end

            // Create chart instance
            var chart = am4core.create("sales_by_category", am4charts.PieChart);

            // Add and configure Series
            var pieSeries = chart.series.push(new am4charts.PieSeries());
            pieSeries.dataFields.value = "amount";
            pieSeries.dataFields.category = "category";

            // Let's cut a hole in our Pie chart the size of 30% the radius
            chart.innerRadius = am4core.percent(30);

            //title
            var title = chart.titles.create();
            title.text = "Total Sales By Product Category";
            title.fontSize = 16;
            title.marginBottom = 15;

            // Put a thick white border around each Slice
            pieSeries.slices.template.stroke = am4core.color("#fff");
            pieSeries.slices.template.strokeWidth = 2;
            pieSeries.slices.template.strokeOpacity = 1;
            pieSeries.slices.template
                // change the cursor on hover to make it apparent the object can be interacted with
                .cursorOverStyle = [
                {
                    "property": "cursor",
                    "value": "pointer"
                }
            ];

            pieSeries.alignLabels = false;
            pieSeries.labels.template.bent = true;
            pieSeries.labels.template.radius = 3;
            pieSeries.labels.template.padding(0, 0, 0, 0);

            pieSeries.ticks.template.disabled = true;

            // Create a base filter effect (as if it's not there) for the hover to return to
            var shadow = pieSeries.slices.template.filters.push(new am4core.DropShadowFilter);
            shadow.opacity = 0;

            // Create hover state
            var hoverState = pieSeries.slices.template.states.getKey("hover"); // normally we have to create the hover state, in this case it already exists

            // Slightly shift the shadow and make it more prominent on hover
            var hoverShadow = hoverState.filters.push(new am4core.DropShadowFilter);
            hoverShadow.opacity = 0.7;
            hoverShadow.blur = 5;


            chart.data = @json($salesByCategory);

        }); // end am4core.ready()
    </script>

@endpush
