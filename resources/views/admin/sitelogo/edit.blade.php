@extends('admin_layouts.app')
@section('styles')
    <style>
        .transparent-btn {
            background: none;
            border: none;
            padding: 0;
            outline: none;
            cursor: pointer;
            box-shadow: none;
            appearance: none;
            /* For some browsers */
        }


        .custom-form-group {
            margin-bottom: 20px;
        }

        .custom-form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        .custom-form-group input,
        .custom-form-group select {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #e1e1e1;
            border-radius: 5px;
            font-size: 16px;
            color: #333;
        }

        .custom-form-group input:focus,
        .custom-form-group select:focus {
            border-color: #d33a9e;
            box-shadow: 0 0 5px rgba(211, 58, 158, 0.5);
        }

        .submit-btn {
            background-color: #d33a9e;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
        }

        .submit-btn:hover {
            background-color: #b8328b;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/material-icons@1.13.12/iconfont/material-icons.min.css">
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="container mb-3">
                <a class="btn btn-icon btn-2 btn-primary float-end me-5" href="{{ route('admin.banners.index') }}">
                    <span class="btn-inner--icon mt-1"><i class="material-icons">arrow_back</i>Back</span>
                </a>
            </div>
            <div class="container my-auto mt-5">
                <div class="row">
                    <div class="col-lg-10 col-md-2 col-12 mx-auto">
                        <div class="card z-index-0 fadeIn3 fadeInBottom">
                            <div class="card-body">
                                <form role="form" class="text-start" action="{{ route('admin.sitelogo.update', $sitelogo->id) }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="custom-form-group">
                                        <label for="title">SiteLogo Image</label>
                                        <input type="file" class="form-control" id="" name="image">
                                        <img  src="{{asset('assets/img/sitelogo/'. $sitelogo->image)}}" width="150px" class="img-thumbnail" alt="">
                                    </div>
                                    <div class="custom-form-group">
                                        <button class="btn btn-primary" type="submit">Edit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>

    <script src="{{ asset('admin_app/assets/js/plugins/choices.min.js') }}"></script>
    <script src="{{ asset('admin_app/assets/js/plugins/quill.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

@endsection
