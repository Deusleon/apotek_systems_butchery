@extends("layouts.master")

@section('page_css')
    <style>


    </style>
@endsection

@section('content-title')
    Issue Return
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Inventory / Issue Return </a></li>
@endsection


@section("content")
    <style>

        .datepicker > .datepicker-days {
            display: block;
        }

        ol.linenums {
            margin: 0 0 0 -8px;
        }

        .ms-container {
            background: transparent url('../assets/plugins/multi-select/img/switch.png') no-repeat 50% 50%;
            width: 100%;
        }

        .ms-selectable, .ms-selection {
            background: #fff;
            color: #555555;
            float: left;
            width: 45%;
        }

        #loading {
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            position: fixed;
            display: none;
            opacity: 0.7;
            background-color: #fff;
            z-index: 99;
            text-align: center;
        }

        #loading-image {
            position: absolute;
            top: 50%;
            left: 50%;
            z-index: 100;
        }

    </style>
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <div class="tab-pane fade show active" id="new_sale" role="tabpanel" aria-labelledby="new_sale-tab">
                    <form id="issue_re_print" action="{{ route('stock-issue-pdf-regen') }}" method="post"
                          enctype="multipart/form-data">
                        @csrf()
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" hidden>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="code">Issued To</label>
                                    <select id="from_id" name="from_id" class="js-example-basic-single form-control"
                                            onchange="issueLocation()">
                                        <option selected="true" value="0" disabled="disabled">Select location...
                                        </option>
                                        @foreach($locations as $location)
                                            <option value="{{$location->id}}">{{$location->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="issued_date">Issue Date</label>
                                    <input type="text" name="issued_date" class="form-control"
                                           id="d_auto_7">
                                </div>
                            </div>
                        </div>

                        <!-- ajax loading gif -->
                        <div id="loading">
                            <image id="loading-image" src="{{asset('assets/images/spinner.gif')}}"></image>
                        </div>

                        <div hidden>
                            <input type="text" name="issue_no" id="issue_no">
                        </div>

                        <div id="tbodyRePrint" style="display: block;" class="table-responsive">
                            <table id="fixed-header-re-print" class="display table nowrap table-striped table-hover"
                                   style="width:100%">

                                <thead>
                                <tr>
                                    <th>Issue #</th>
                                    <th>Issued To</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($all_issues as $all_issue)
                                    <tr>
                                        <td>{{$all_issue->issue_no}}</td>
                                        <td>{{$all_issue->issueLocation['name']}}</td>
                                        <td>
                                            <button type="button" onclick="showIssue(this.value)"
                                                    value="{{$all_issue->issue_no}}"
                                                    class='btn btn-sm btn-rounded btn-success'>Open
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>

                            </table>

                        </div>


                        <!-- filter issue reprint results -->
                        <div id="tbodyRePrintFilter" style="display: none;" class="table-responsive">
                            <table id="fixed-header-re-print-filter"
                                   class="display table nowrap table-striped table-hover"
                                   style="width:100%">

                                <thead>
                                <tr>
                                    <th>Issue #</th>
                                    <th>Issued To</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>

                            </table>

                        </div>

                        <!-- open issue return press -->
                        <div id="tbody1" style="display: none;" class="table-responsive">
                            <table id="fixed-header2" class="display table nowrap table-striped table-hover"
                                   style="width:100%">
                                <thead>
                                <tr>
                                    <th>Issue #</th>
                                    <th>Product Name</th>
                                    <th>Issued To</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>

                            </table>

                            <div class="btn-group" style="float: right;margin-right: 8.3%">
                                <a href="{{route('stock-issue-return.index')}}">
                                    <button type="button" class="btn btn-sm btn-danger btn-rounded" id="cancel">
                                        Close
                                    </button>
                                </a>
                            </div>

                            <div hidden>
                                <input type="text" name="issue_nos" id="issue_nos">
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>


        @include('stock_management.issue_return.return')

        @endsection


        @push("page_scripts")

            @include('partials.notification')
            <script src="{{asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")}}"></script>
            <script src="{{asset("assets/js/pages/ac-datepicker.js")}}"></script>


            <script type="text/javascript">
                var config = {
                    routes: {
                        stockIssueShow: '{{route('stock-issue-show')}}',
                        stockIssueFilter: '{{route('stock-issue-filter')}}'

                    }
                };

            </script>

            <script src="{{asset("assets/apotek/js/issue-return.js")}}"></script>



    @endpush
