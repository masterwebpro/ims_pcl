@extends('layouts.master')
@section('title')
    Client
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
            Client
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
                    <h4 class="card-title mb-0 flex-grow-1">Client Creation</h4>
                    <div class="flex-shrink-0">
                        <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">
                            <button type="button" data-status="open"
                                class="btn btn-success btn-label rounded-pill submit-client"><i
                                    class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                                Save</button>
                            <a href="{{ URL::to('maintenance/client') }}" class="btn btn-primary btn-label rounded-pill"><i
                                    class="ri-arrow-go-back-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                                Back</a>
                        </div>
                    </div>
                </div><!-- end card header -->
                <form method="POST" name="form-client" action="javascript:void(0);" id="form-client"
                    class="row g-3 needs-validation" novalidate>
                    @csrf
                    <div class="card-body">
                        <div class="form-container">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row mb-4">
                                        <div class="col-md-4 form-group">
                                            <label for="client_name" class="form-label">Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" required="required"
                                                name="client_name" id="client_name" value="" placeholder="Enter Name">
                                            <span class="text-danger error-msg client_name_error"></span>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label for="client_code" class="form-label">Code <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" required="required"
                                                name="client_code" id="client_code" value="" placeholder="Enter Code">
                                            <span class="text-danger error-msg client_code_error"></span>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="tin" class="form-label">Tin</label>
                                            <input type="text" class="form-control"
                                                name="tin" id="tin" value="" placeholder="Enter Tin">
                                        </div>
                                        <div class="col-md-2 mt-4">
                                            <div class="form-check form-switch form-switch-custom form-switch-primary">
                                                <label class="form-check-label" for="is_enabled">Enable</label>
                                                <input class="form-check-input" type="checkbox" role="switch" id="is_enabled" checked>
                                            </div>
                                            <div class="form-check form-switch form-switch-custom form-switch-primary">
                                                <label class="form-check-label" for="is_vatable">Vatable</label>
                                                <input class="form-check-input" type="checkbox" role="switch" id="is_vatable" checked>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-md-3 form-group">
                                            <label for="client_type" class="form-label">Type <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select select2" required="required" id="client_type"
                                                name="client_type">
                                                <option value="">Select Type</option>
                                                <? foreach($client_type as $k => $type) : ?>
                                                <option value="<?= $k ?>"><?= $type ?></option>
                                                <? endforeach;?>
                                            </select>
                                            <span class="text-danger error-msg client_type_error"></span>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label for="contact_person" class="form-label">Contact Person</label>
                                            <input type="text" class="form-control"
                                                name="contact_person" id="contact_person" value=""
                                                placeholder="Enter Contact Person">
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label for="email_address" class="form-label">Email Address</label>
                                            <input type="text" class="form-control"
                                                name="email_address" id="email_address" value=""
                                                placeholder="Enter Email Address">
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label for="phone_no" class="form-label">Phone</label>
                                            <input type="text" class="form-control" id="phone_no" name="phone_no" placeholder="(xxx)xxx-xxxx">
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-md-6 form-group">
                                                <label for="address_1" class="form-label">Address 1<span
                                                        class="text-danger">*</span></label></label>
                                                <input type="text" class="form-control" id="address_1" name="address_1" placeholder="Enter address...">
                                                <span class="text-danger error-msg address_1_error"></span>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label for="city" class="form-label">City<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" required="required"
                                                name="city" id="city" value=""
                                                placeholder="Enter City">
                                            <span class="text-danger error-msg city_error"></span>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label for="province" class="form-label">Province<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" required="required"
                                                name="province" id="province" value=""
                                                placeholder="Enter Province">
                                            <span class="text-danger error-msg province_error"></span>
                                        </div>

                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-md-6 form-group">
                                            <div>
                                                <label for="address_2" class="form-label">Address 2</label>
                                                <input type="text" class="form-control" id="address_2" name="address_2" placeholder="Enter address...">
                                            </div>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label for="country" class="form-label">Country<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" required="required"
                                                name="country" id="country" value=""
                                                placeholder="Enter Country">
                                            <span class="text-danger error-msg country_error"></span>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label for="zipcode" class="form-label">Zip Code.<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" required="required"
                                                name="zipcode" id="zipcode" value=""
                                                placeholder="Enter Zip Code">
                                            <span class="text-danger error-msg zipcode_error"></span>
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
    <script src="{{ URL::asset('/assets/js/maintenance/client.js') }}"></script>
@endsection
