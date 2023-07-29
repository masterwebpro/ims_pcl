@extends('layouts.master')
@section('title') Unit of Measure @endsection
@section('css')
<!--datatable css-->
<link href="{{ URL::asset('assets/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
<!--datatable responsive css-->
<link href="{{ URL::asset('assets/css/responsive.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />

@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Maintenance @endslot
@slot('title') Unit of Measure @endslot
@endcomponent


<div class="alert alert-danger d-none" id="error-handling" role="alert">
    <ul class="errors">
    </ul>
</div>

<div class="row justify-content-center">
    <div class="col-xxl-10">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">UOM Edit</h4>
                <div class="flex-shrink-0">
                    <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">
                        <button type="button" data-status="open" class="btn btn-success btn-label rounded-pill submit-uom"><i class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2"></i> Save</button>
                        <a  href="{{ URL::to('maintenance/unit') }}" class="btn btn-primary btn-label rounded-pill"><i class="ri-arrow-go-back-line label-icon align-middle rounded-pill fs-16 me-2"></i> Back</a>
                    </div>
                </div>
            </div><!-- end card header -->
            <form  method="POST" name="form-uom" action="javascript:void(0);" id="form-uom" class="row g-3 needs-validation" novalidate>
            @csrf
            <div class="card-body">
                <div class="form-container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row mb-4">
                                <div class="col-md-4 form-group">
                                    <label for="uom_id" class="form-label">Code <span class="text-danger">*</span></label>
                                    <input type="hidden" class="form-control" name="uom_id" id="uom_id" value="{{  $uom->uom_id }}">
                                    <input type="text" class="form-control" required="required" name="code" id="code" value="{{ $uom->code }}" placeholder="Enter Code">
                                    <div class="invalid-feedback error-msg po_num_error">code is Required</div>
                                </div>

                                <div class="col-md-4 form-group">
                                    <label for="uom_desc" class="form-label">Description <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" required="required" name="uom_desc" id="uom_desc" value="{{ $uom->uom_desc }}" placeholder="Enter Description">
                                    <div class="invalid-feedback error-msg po_num_error">Description is Required</div>
                                </div>

                                <div class="col-md-4 form-group">
                                    <label for="inputState" class="form-label">Conversion <span class="text-danger">*</span></label>
                                    <input type="number" id="convertion_pc" required="required"  name="convertion_pc" class="form-control" value="{{ $uom->convertion_pc }}" placeholder="Enter Conversion">
                                    <div class="invalid-feedback text-danger error-msg po_date_error">Conversion is Required</div>
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
<script src="{{ URL::asset('/assets/js/maintenance/unit.js') }}"></script>

@endsection
