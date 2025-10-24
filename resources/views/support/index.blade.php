@extends("layouts.master")

@section('page_css')
    <style>
        .support-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 15px;
        }

        .support-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .contact-item {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            color: white;
        }

        .download-btn {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .download-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.4);
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #667eea;
        }

        .contact-icon {
            font-size: 2rem;
            margin-right: 15px;
        }
    </style>
@endsection

@section('content-title')
    Support
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Support </a></li>
@endsection

@section("content")
    <div class="col-sm-12">
        <ul class="nav nav-pills mb-3" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active text-uppercase" id="invoice-received" href="#" role="tab"
                    aria-controls="quotes_list" aria-selected="false">Support</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-uppercase" id="order-received" href="{{ route('support.download-manual') }}"
                    role="tab" aria-controls="new_quotes" aria-selected="true"> Download User Manual
                </a>
            </li>
        </ul>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label for="phone">Email : support@apoteksystems.com </label><br>
                        <label for="phone" class="d-flex">Phone :
                            <ul>
                                <li>+255 768 332 031</li>
                                <li>+255 735 332 031</li>
                            </ul>
                        </label>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push("page_scripts")
@endpush