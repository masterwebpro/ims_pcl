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
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Create Product</h4>
                    <div class="flex-shrink-0">
                        <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">
                            <button type="button" class="btn btn-success btn-label rounded-pill submit-product"><i class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2"></i> Save</button>
                            <a href="{{ URL::to('maintenance/product') }}"
                                class="btn btn-primary btn-label rounded-pill"><i
                                    class="ri-arrow-go-back-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                                Back</a>
                        </div>
                    </div>
                </div><!-- end card header -->
                <div class="card-body">
                    <form name="submit-product" id="submit-product">
                            <div class="row">
                                <div class="col-6">
                                    <div class="row mb-3">
                                        <div class="col-lg-3">
                                            <label for="supplier_id" class="form-label">Supplier
                                                Name <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-lg-9">
                                            <select class="form-control select2" name="supplier_id"
                                                id="supplier_id">
                                                <option value="">Select Supplier Name
                                                </option>
                                                <? foreach($supplier_list as $supplier) : ?>
                                                <option value="<?= $supplier->id ?>">
                                                    <?= $supplier->supplier_name ?></option>
                                                <? endforeach;?>
                                            </select>
                                            <span class="text-danger error-msg supplier_id_error"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-2">
                                    <div class="form-check form-switch form-switch-primary form-switch-lg"
                                        dir="ltr">
                                        <input type="checkbox" class="form-check-input" id="is_serialize"
                                            name="is_serialize">
                                        <label for="is_serialize">Serialize</label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-check form-switch form-switch-success form-switch-lg"
                                        dir="ltr">
                                        <input type="checkbox" class="form-check-input" name="is_enabled"
                                            id="is_enabled" checked>
                                        <label for="is_enabled">Enable</label>
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
                                            <input type="url" class="form-control" id="product_name" required
                                                name="product_name" placeholder="Enter Product Name">
                                            <span class="text-danger error-msg product_name_error"></span>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-6">
                                    <div class="row mb-3">
                                        <div class="col-lg-3">
                                            <label for="customer_id" class="form-label">Customer
                                                Name <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-lg-9">
                                            <select class="form-control select2" name="customer_id"
                                                id="customer_id">
                                                <option value="">Select Customer Name
                                                </option>
                                                <? foreach($client_list as $client) : ?>
                                                <option value="<?= $client->id ?>">
                                                    <?= $client->client_name ?></option>
                                                <? endforeach;?>
                                            </select>
                                            <span class="text-danger error-msg customer_id_error"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="row mb-3">
                                        <div class="col-lg-3">
                                            <label for="sap_code" class="form-label">SAP Code</label>
                                        </div>
                                        <div class="col-lg-9">
                                            <input type="text" class="form-control" id="sap_code" name="sap_code" placeholder="Enter Product SAP Code">
                                            <span class="text-danger error-msg sap_code_error"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="row mb-3">
                                        <div class="col-lg-3">
                                            <label for="product_code" class="form-label">Product Code</label>
                                        </div>
                                        <div class="col-lg-9">
                                            <input type="text" class="form-control" id="product_code"
                                                name="product_code" placeholder="Enter Product Code" value="" >
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
                                            <input type="url" class="form-control" id="product_upc"
                                                name="product_upc" placeholder="Enter Product UPC">
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
                                            <input type="hidden" name="category" id="category">
                                            <select class="form-control select2" name="category_id"
                                                id="category_id">
                                                <option value="">Select Category</option>
                                                <? foreach($category as $index => $cat) : ?>
                                                <option value="<?= $cat->category_id ?>" data-category="{{ $cat->category_name }}">
                                                    {{ $cat->category_name }}</option>
                                                <? endforeach;?>
                                            </select>
                                            <span class="text-danger error-msg category_id_error"></span>
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
                                            <input type="text" class="form-control" id="product_sku"
                                                name="product_sku" placeholder="Enter Product SKU">
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
                                            <span class="text-danger error-msg category_brand_id_error"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="row mb-3">
                                        <div class="col-lg-3">
                                            <label for="uom_id" class="form-label">Product UOM <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-lg-9">
                                            <select class="form-control" name="uom_id[]" data-choices
                                                data-choices-removeItem multiple id="uom_id">
                                                <option value="">Select Unit of Measure </option>
                                                <? foreach($uom as $un) : ?>
                                                <option value="<?= $un->uom_id ?>"><?= $un->code ?> -
                                                    <?= $un->uom_desc ?></option>
                                                <? endforeach;?>
                                            </select>
                                            <span class="text-danger error-msg uom_id_error"></span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                    </form>
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
    <script src="{{ URL::asset('assets/js/pages/form-wizard.init.js') }}"></script>

    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/maintenance/product.js') }}"></script>
    <script src="{{ URL::asset('assets/js/plugins.min.js') }}"></script>
@endsection
