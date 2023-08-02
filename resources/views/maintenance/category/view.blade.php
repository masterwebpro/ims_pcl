@extends('layouts.master')
@section('title') Category @endsection
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
@slot('title') Category @endslot
@endcomponent


<div class="alert alert-danger d-none" id="error-handling" role="alert">
    <ul class="errors">
    </ul>
</div>

<div class="row justify-content-center">
    <div class="col-xxl-10">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Category View</h4>
                <div class="flex-shrink-0">
                    <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">
                        <a href="{{ URL::to('maintenance/category') }}/<?=_encode($category->category_id)?>/edit" class="btn btn-success btn-label rounded-pill"><i class="ri-edit-line label-icon align-middle rounded-pill fs-16 me-2"></i> Edit</a>
                        <a  href="{{ URL::to('maintenance/category') }}" class="btn btn-primary btn-label rounded-pill"><i class="ri-arrow-go-back-line label-icon align-middle rounded-pill fs-16 me-2"></i> Back</a>
                    </div>
                </div>
            </div><!-- end card header -->
            <form  method="POST" name="form-category" action="javascript:void(0);" id="form-category" class="row g-3 needs-validation" novalidate>
            @csrf
            <div class="card-body">
                <div class="form-container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row mb-4">
                                <div class="col-md-4 form-group">
                                    <label for="category_name" class="form-label">Category Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" required="required" disabled
                                        name="category_name" id="category_name" value="{{ $category->category_name }}"
                                        placeholder="Enter Category Name">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label for="parent_id" class="form-label">Parent</label>
                                    <select class="form-select" id="parent_id" name="parent_id" disabled>
                                        <? foreach($category_list as $cat) : ?>
                                        <option value="<?=$cat->category_id?>" <?=($cat->category_id == $category->parent_id) ? 'selected' : ''?> ><?=($category->parent_id == 0) ? 'Root' : $cat->category_name?></option>
                                        <? endforeach;?>
                                    </select>
                                </div>
                                <div class="col-md-2 form-group">
                                    <label for="sort_by" class="form-label">Sort By <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" required="required" disabled
                                        name="sort_by" id="sort_by" value="{{ $cat->sort_by }}"
                                        placeholder="Enter Sort by">
                                </div>
                                <div class="col-md-2 form-group">
                                    <label for="is_enabled" class="form-label">Is Enabled</label>
                                    <select class="form-select" id="is_enabled" name="is_enabled" disabled>
                                        <option>{{ $category->is_enabled == 1 ? 'Yes' : 'No' }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="input-group">
                                    <span class="input-group-text">Sub Category Name</span>
                                    <input type="text" class="form-control" id="sub_category" aria-label="Sub Category Name" disabled
                                        placeholder="Enter Sub Category">
                                    <button type="button" data-status="open" disabled
                                        class="btn btn-success btn-label add-sub-category" id="add"><i
                                            class="ri-add-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                                        Add Sub Category</button>
                                </div>
                            </div>
                            <?php if(count($sub_category) > 0):?>
                            <div class="row mb-4">
                                <div class="col-md-12">
                                <label for="">Sub Category</label>
                                <ul class="list-group">
                                    <? foreach($sub_category as $sub):?>
                                        <li class="list-group-item"><i class="ri-menu-3-line align-middle me-2"></i> {{ $sub->category_name }}</li>
                                    <?endforeach;?>
                                </ul>
                                </div>
                            </div>
                            <? endif; ?>
                            <div class="row mb-2">
                                <div class="col-md-12 form-group">
                                    <label for="brand_id" class="form-label">Brand</label>
                                    <select class="form-control brand_id" id="brand_id" data-choices disabled
                                        data-choices-removeItem multiple>
                                        <option value="">Select Brand</option>
                                        <? foreach ($category_brand as $key => $brand) :?>
                                        <option value="{{ $brand->brand_id }}" selected>{{ $brand->brand_name }} </option>
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
<script src="{{ URL::asset('/assets/js/maintenance/category.js') }}"></script>

@endsection
