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
                    <h4 class="card-title mb-0 flex-grow-1">Supplier Creation</h4>
                    <div class="flex-shrink-0">
                        <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">
                            <button type="button" data-status="open"
                                class="btn btn-success btn-label rounded-pill submit-supplier"><i
                                    class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                                Save</button>
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
                                            <input type="hidden" class="form-control" name="id" id="id" value="">
                                            <input type="text" class="form-control" required="required" name="supplier_name"
                                                id="supplier_name" value="" placeholder="Enter Name">
                                            <div class="invalid-feedback error-msg">Name is Required
                                            </div>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label for="supplier_code" class="form-label">Code <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" required="required" name="supplier_code"
                                                id="supplier_code" value="" placeholder="Enter Code">
                                            <div class="invalid-feedback error-msg">Code is Required
                                            </div>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label for="contact_no" class="form-label">Contact <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="contact_no" required="required"
                                                name="contact_no" class="form-control" value=""
                                                placeholder="Enter Contact">
                                            <div class="invalid-feedback text-danger error-msg">Contact is
                                                Required</div>
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-md-8 form-group">
                                            <div>
                                                <label for="address" class="form-label">Address <span
                                                    class="text-danger">*</span></label></label>
                                                <textarea class="form-control" id="address" rows="3" placeholder="Enter address..."></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label for="category_id" class="form-label">Client</label>
                                            <select class="form-control category_id" id="client_id" name="client_id" data-choices
                                                data-choices-removeItem multiple>
                                                <option value="">Select Client</option>
                                                <? foreach ($client_list as $client) :?>
                                                <option value="<?=$client->id?>">{{ $client->client_name }}
                                                </option>
                                                <? endforeach;?>
                                            </select>
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
