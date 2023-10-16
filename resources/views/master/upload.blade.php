@extends('layouts.master')
@section('title') Receive Order @endsection
@section('css')

@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Upload @endslot
@slot('title') Beginning Inventory @endslot
@endcomponent
<form id="uploadForm" enctype="multipart/form-data">
@csrf
    <div class="row justify-content-center">
        
        <div class="col-xxl-11">
            <div class="card">
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <div class="card-body p-4 ">
                            <div class="row g-3">
                                <div class="row">
                                    <h3>Upload Beginning Inventory</h3>
                                    <div class="col-12 form-group mt-3 mb-3">
                                        <input type="file" class="form-control" name="excel_file" accept=".xlsx, .xls">
                                        <span class="text-danger error-msg excel_file_error"></span>
                                    </div>
                                    <div class="col-12 form-group d-grid gap-2">
                                        <button type="button" class="btn btn-primary" id="uploadBegInv"><i class="ri-upload-line align-bottom me-1"></i> Upload</button>
                                    </div>
                                </div>
                            </div>
                            <!--end row-->
                        </div>
                        <!--end card-body-->
                    </div>

                </div>
            </div>
            <!--end card-->
        </div>
        <!--end col-->
    </div>
    <!--end row-->
</form>
	@endsection
@section('script')

<script src="{{ URL::asset('assets/js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/cleave.js/cleave.js.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/masks/jquery.mask.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/select2/select2.min.js') }}"></script>

<script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
<script src="{{ URL::asset('/assets/js/master/upload.js') }}"></script>

@endsection