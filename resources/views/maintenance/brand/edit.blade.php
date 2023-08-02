@extends('layouts.master')
@section('title') Brand @endsection
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
@slot('title') Brand @endslot
@endcomponent


<div class="alert alert-danger d-none" id="error-handling" role="alert">
    <ul class="errors">
    </ul>
</div>

<div class="row justify-content-center">
    <div class="col-xxl-10">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Brand Edit</h4>
                <div class="flex-shrink-0">
                    <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">
                        <button type="button" data-status="open" class="btn btn-success btn-label rounded-pill submit-brand"><i class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2"></i> Save</button>
                        <a  href="{{ URL::to('maintenance/brand') }}" class="btn btn-primary btn-label rounded-pill"><i class="ri-arrow-go-back-line label-icon align-middle rounded-pill fs-16 me-2"></i> Back</a>
                    </div>
                </div>
            </div><!-- end card header -->
            <form  method="POST" name="form-brand" action="javascript:void(0);" id="form-brand" class="row g-3 needs-validation" novalidate>
            @csrf
            <div class="card-body">
                <div class="form-container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row mb-4">
                                <div class="col-md-4 form-group">
                                    <label for="brand_id" class="form-label">Brand Name <span class="text-danger">*</span></label>
                                    <input type="hidden" class="form-control" name="brand_id" id="brand_id" value="{{ $brand->brand_id }}">
                                    <input type="text" class="form-control" required="required" name="brand_name" id="brand_name" value="{{ $brand->brand_name }}" placeholder="Enter Brand Name">
                                    <div class="invalid-feedback error-msg po_num_error">code is Required</div>
                                </div>

                                <div class="col-md-4 form-group">
                                    <label for="sort_by" class="form-label">Sort By <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" required="required" name="sort_by" id="sort_by" value="{{ $brand->sort_by }}" placeholder="Enter Sort by">
                                    <div class="invalid-feedback error-msg po_num_error">Sortby is Required</div>
                                </div>
                                <div class="col-md-4 form-group">
                                    <div class="form-check form-switch form-switch-custom form-switch-primary my-4 mx-5">
                                        <label class="form-check-label" for="is_enabled">Enable</label>
                                        <input class="form-check-input" type="checkbox" role="switch" id="is_enabled" {{ (($brand->is_enabled == 1) ? "checked" : "") }}>
                                    </div>
                                </div>

                            </div>
                            <div class="row mb-4">
                                <div class="col-md-12 form-group">
                                        <label for="category_id" class="form-label">Category</label>
                                        <select class="form-control category_id" id="category_id" data-choices
                                            data-choices-removeItem multiple>
                                            <option value="">Select Category</option>
                                            <? foreach ($category_list as $key => $cat) :?>
                                            <option value="{{ $cat->category_id }}" <?=(in_array($cat->category_id, $category)) ? 'selected' : ''?>>{{ $cat->category_name }} </option>
                                            <? endforeach;?>
                                        </select>
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
<script src="{{ URL::asset('/assets/js/maintenance/brand.js') }}"></script>

@endsection
