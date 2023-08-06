@extends('layouts.master')
@section('title')
    Product
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
            Product
        @endslot
    @endcomponent


    <div class="alert alert-danger d-none" id="error-handling" role="alert">
        <ul class="errors">
        </ul>
    </div>

    <div class="row justify-content-center">
        <div class="col-xxl-12">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-2">
                        <div class="nav nav-pills flex-column nav-pills-tab custom-verti-nav-pills text-center"
                            role="tablist" aria-orientation="vertical">
                            <a class="nav-link active show" id="custom-v-pills-home-tab" data-bs-toggle="pill"
                                href="#custom-v-pills-home" role="tab" aria-controls="custom-v-pills-home"
                                aria-selected="true">
                                <i class="ri-information-line d-block fs-20 mb-1"></i> General
                            </a>
                            <a class="nav-link" id="custom-v-pills-profile-tab" data-bs-toggle="pill"
                                href="#custom-v-pills-profile" role="tab" aria-controls="custom-v-pills-profile"
                                aria-selected="false">
                                <i class="ri-stackshare-line d-block fs-20 mb-1"></i> Attributes
                            </a>
                            <a class="nav-link" id="custom-v-pills-messages-tab" data-bs-toggle="pill"
                                href="#custom-v-pills-messages" role="tab" aria-controls="custom-v-pills-messages"
                                aria-selected="false">
                                <i class="ri-price-tag-3-line d-block fs-20 mb-1"></i> Pricing
                            </a>
                            <a class="nav-link" id="custom-v-pills-messages-tab" data-bs-toggle="pill"
                                href="#custom-v-pills-messages" role="tab" aria-controls="custom-v-pills-messages"
                                aria-selected="false">
                                <i class="ri-stack-line d-block fs-20 mb-1"></i> Unit
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-10">
                        <div class="tab-content text-muted mt-3 mt-lg-0">
                            <div class="tab-pane fade active show" id="custom-v-pills-home" role="tabpanel"
                                aria-labelledby="custom-v-pills-home-tab">
                                <form method="POST" name="form-product" action="javascript:void(0);"
                                    id="form-product"class="row g-3 needs-validation" novalidate>
                                    @csrf
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>General Information</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="row mb-3">
                                                        <div class="col-lg-3">
                                                            <label for="supplier_id" class="form-label">Supplier
                                                                Name <span class="text-danger">*</span></label>
                                                        </div>
                                                        <div class="col-lg-9">
                                                            <select class="form-control" data-choices name="supplier_id"
                                                                id="supplier_id">
                                                                <option value="">Select Supplier Name
                                                                </option>
                                                                <? foreach($supplier_list as $supplier) : ?>
                                                                <option value="<?= $supplier->id ?>">
                                                                    <?= $supplier->supplier_name ?></option>
                                                                <? endforeach;?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="form-check form-switch form-switch-primary form-switch-lg"
                                                        dir="ltr">
                                                        <input type="checkbox" class="form-check-input" id="is_serialize"
                                                            name="is_serialize">
                                                        <label for="is_serialize">Serialize</label>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="form-check form-switch form-switch-success form-switch-lg"
                                                        dir="ltr">
                                                        <input type="checkbox" class="form-check-input" name="is_enabled"
                                                            id="is_enabled" checked>
                                                        <label for="is_enabled">Enable</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="row mb-3">
                                                        <div class="col-lg-3">
                                                            <label for="product_code" class="form-label">Product
                                                                Code
                                                                <span class="text-danger">*</span></label>
                                                        </div>
                                                        <div class="col-lg-9">
                                                            <input type="text" class="form-control" id="product_code" name="product_code"
                                                                placeholder="Enter Product Code">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="row mb-3">
                                                        <div class="col-lg-3">
                                                            <label for="product_upc" class="form-label">Product
                                                                UPC</label>
                                                        </div>
                                                        <div class="col-lg-9">
                                                            <input type="url" class="form-control" id="product_upc" name="product_upc"
                                                                placeholder="Enter Product UPC">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="row mb-3">
                                                        <div class="col-lg-3">
                                                            <label for="product_name" class="form-label">Product
                                                                Name
                                                                <span class="text-danger">*</span></label>
                                                        </div>
                                                        <div class="col-lg-9">
                                                            <input type="url" class="form-control" id="product_name" name="product_name"
                                                                placeholder="Enter Product Name">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-6">
                                                    <div class="row mb-3">
                                                        <div class="col-lg-3">
                                                            <label for="product_sku" class="form-label">Product
                                                                SKU</label>
                                                        </div>
                                                        <div class="col-lg-9">
                                                            <input type="url" class="form-control" id="product_sku" name="product_sku"
                                                                placeholder="Enter Product SKU">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="row mb-3">
                                                        <div class="col-lg-3">
                                                            <label for="category_id" class="form-label">Category
                                                                <span class="text-danger">*</span></label>
                                                            </label>
                                                        </div>
                                                        <div class="col-lg-9">
                                                            <select class="form-control select2" name="category_id"
                                                                id="category_id">
                                                                <option value="">Select Category</option>
                                                                <? foreach($category as $index => $cat) : ?>
                                                                <option value="<?= $cat->category_id ?>">
                                                                    {{ $cat->category_name }}</option>
                                                                <? endforeach;?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="row mb-3">
                                                        <div class="col-lg-3">
                                                            <label for="brand_id" class="form-label">Brand
                                                                <span class="text-danger">*</span></label>
                                                        </div>
                                                        <div class="col-lg-9">
                                                            <select class="form-select select2" required="required"
                                                                id="brand" name="category_brand_id">
                                                                <option value="">Select Brand</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <div class="flex-shrink-0">
                                                <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0 float-end">
                                                    <a href="{{ URL::to('maintenance/product') }}"
                                                        class="btn btn-danger btn-label rounded-pill"><i
                                                            class="ri-close-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                                                        Cancel</a>
                                                    <button type="button" data-status="open"
                                                        class="btn btn-success btn-label rounded-pill submit-product"><i
                                                            class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                                                        Save</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!--end tab-pane-->
                        <div class="tab-pane fade" id="custom-v-pills-profile" role="tabpanel"
                            aria-labelledby="custom-v-pills-profile-tab">
                        </div>
                        <!--end tab-pane-->
                        <div class="tab-pane fade" id="custom-v-pills-messages" role="tabpanel"
                            aria-labelledby="custom-v-pills-messages-tab">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    <script src="{{ URL::asset('/assets/js/maintenance/product.js') }}"></script>
@endsection
