@extends('layouts.master')
@section('title')
    Supplier
@endsection
@section('css')
    <!--datatable css-->
    <link href="{{ URL::asset('assets/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <!--datatable responsive css-->
    <link href="{{ URL::asset('assets/css/responsive.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Maintenance
        @endslot
        @slot('title')
            Supplier
        @endslot
    @endcomponent


    <div class="alert alert-danger d-none" id="error-handling" role="alert">
        <ul class="errors">
        </ul>
    </div>

    <div class="row justify-content-center">
        <div class="col-xxl-10">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Supplier View</h4>
                    <div class="flex-shrink-0">
                        <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">
                            <a href="{{ URL::to('maintenance/supplier') }}/<?=_encode($supplier->id)?>/edit" class="btn btn-success btn-label rounded-pill"><i class="ri-edit-line label-icon align-middle rounded-pill fs-16 me-2"></i> Edit</a>
                            <a href="{{ URL::to('maintenance/supplier') }}"
                                class="btn btn-primary btn-label rounded-pill"><i
                                    class="ri-arrow-go-back-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                                Back</a>
                        </div>
                    </div>
                </div><!-- end card header -->
                <form method="POST" name="form-supplier" action="javascript:void(0);" id="form-supplier"
                    class="row g-3 needs-validation" novalidate>
                    @csrf
                    <div class="card-body">
                        <div class="form-container">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row mb-4">
                                        <div class="col-md-4 form-group">
                                            <label for="supplier_name" class="form-label">Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" required="required" name="supplier_name" disabled
                                                id="supplier_name" value="{{ $supplier->supplier_name }}" placeholder="Enter Name">
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label for="supplier_code" class="form-label">Code <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" required="required" name="supplier_code" disabled
                                                id="supplier_code" value="{{ $supplier->supplier_code }}" placeholder="Enter Code">
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label for="contact_no" class="form-label">Contact <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="contact_no" required="required"
                                                name="contact_no" class="form-control" value="{{ $supplier->contact_no }}" disabled
                                                placeholder="Enter Contact">
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-md-8 form-group">
                                            <div>
                                                <label for="address" class="form-label">Address <span
                                                    class="text-danger">*</span></label></label>
                                                <textarea class="form-control" id="address" rows="3" disabled placeholder="Enter address...">{{ $supplier->supplier_address }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div> <!-- end col -->
    </div> <!-- end col -->
@endsection
@section('script')
    <script src="{{ URL::asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/cleave.js/cleave.js.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/masks/jquery.mask.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/select2/select2.min.js') }}"></script>

    <script src="{{ URL::asset('assets/js/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/datatables/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/datatables/dataTables.responsive.min.js') }}"></script>

    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/maintenance/supplier.js') }}"></script>
@endsection
