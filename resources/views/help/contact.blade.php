@extends("layouts.master")

@section('content-title')
    HELP
@endsection
@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Help / Contact Us</a></li>
@endsection


@section("content")
    <div class="col-sm-12">
        <div class="card-block">
            <div class="col-sm-12">
                <div class="tab-content" id="myTabContent">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="infoModalLabel">APOTEk SYSTEMS LIMITED</h5>
                            </div>
                            <div class="modal-body">
                                <p><strong>Website:</strong> <a href="https://www.apoteksystems.com" target="_blank">www.apoteksystems.com</a></p>
                                <p><strong>Email:</strong> <a href="mailto:support@apoteksystems.com">info@apoteksystems.com</a></p>
                                <p><strong>Phone:</strong></p>
                                <ul>
                                    <li>+255 763 888 886</li>
                                    <li>+255 715 777 555</li>
                                    <li>+255 788 686 666</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection



