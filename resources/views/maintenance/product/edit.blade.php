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
                    <h4 class="card-title mb-0 flex-grow-1">Product Edit</h4>
                    <div class="flex-shrink-0">
                        <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">
                            <a href="{{ URL::to('maintenance/product') }}"
                                class="btn btn-primary btn-label rounded-pill"><i
                                    class="ri-arrow-go-back-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                                Back</a>
                        </div>
                    </div>
                </div><!-- end card header -->
                <div class="card-body">
                    <form method="POST" name="form-supplier" action="javascript:void(0);" id="form-product" class="form-steps" autocomplete="off">
                        <div class="step-arrow-nav mb-4">
                            <ul class="nav nav-pills custom-nav nav-justified" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="steparrow-gen-info-tab" data-bs-toggle="pill"
                                        data-bs-target="#steparrow-gen-info" type="button" role="tab"
                                        aria-controls="steparrow-gen-info" aria-selected="true">General</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="steparrow-attributes-info-tab" data-bs-toggle="pill"
                                        data-bs-target="#steparrow-attributes-info" type="button" role="tab"
                                        aria-controls="steparrow-attributes-info" aria-selected="false">Attributes</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="steparrow-pricing-info-tab" data-bs-toggle="pill"
                                        data-bs-target="#steparrow-pricing-info" type="button" role="tab"
                                        aria-controls="steparrow-pricing-info" aria-selected="false">Pricing</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="steparrow-units-info-tab" data-bs-toggle="pill"
                                        data-bs-target="#steparrow-units-info" type="button" role="tab"
                                        aria-controls="steparrow-units-info" aria-selected="false">Unit of Measure</button>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content">
                            <div class="tab-pane show active" id="steparrow-gen-info" role="tabpanel"
                                aria-labelledby="steparrow-gen-info-tab">
                                <div>
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
                                                            <option value="<?=$supplier->id?>" <?=($supplier->id == $product->supplier_id) ? 'selected' : ''?> ><?=$supplier->supplier_name?></option>
                                                        <? endforeach;?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-switch form-switch-primary form-switch-lg"
                                                dir="ltr">
                                                <input type="checkbox" class="form-check-input" id="is_serialize"  <?=($product->is_serialize == 1) ? 'checked' : '' ?>
                                                    name="is_serialize">
                                                <label for="is_serialize">Serialize</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-switch form-switch-success form-switch-lg"
                                                dir="ltr">
                                                <input type="checkbox" class="form-check-input" name="is_enabled"
                                                    id="is_enabled" <?=($product->is_enabled == 1) ? 'checked' : '' ?>>
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
                                                    <input type="hidden" name="product_id" id="product_id" value="{{ $product->product_id }}">

                                                    <input type="text" class="form-control" id="product_code"
                                                        name="product_code" placeholder="Enter Product Code" value="{{ $product->product_code }}" >
                                                    <div class="invalid-feedback">Please enter product code</div>
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
                                                    value="{{ $product->product_upc }}"
                                                        name="product_upc" placeholder="Enter Product UPC">
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
                                                    <input type="url" class="form-control" id="product_name"
                                                    value="{{ $product->product_name }}"
                                                        name="product_name" placeholder="Enter Product Name">
                                                    <div class="invalid-feedback">Please enter product name</div>
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
                                                    <input type="url" class="form-control" id="product_sku"
                                                    value="{{ $product->product_sku }}"
                                                        name="product_sku" placeholder="Enter Product SKU">
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
                                                        <option value="<?= $cat->category_id ?>" <?=($cat->category_id == $prod_category->category_id) ? 'selected' : ''?>>
                                                            {{ $cat->category_name }}</option>
                                                        <? endforeach;?>
                                                    </select>
                                                    <div class="invalid-feedback">Please Select Category</div>
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
                                                    <select class="form-select select2" required="required"  data-brand="{{ $prod_category->category_brand_id }}"
                                                        id="brand" name="category_brand_id">
                                                        <option value="">Select Brand</option>
                                                        <? foreach($brand as $index => $br) : ?>
                                                        <option value="<?= $br->brand_id ?>" <?=($br->brand_id == $prod_category->brand_id) ? 'selected' : ''?>>
                                                            {{ $br->brand_name }}</option>
                                                        <? endforeach;?>
                                                    </select>
                                                    <div class="invalid-feedback">Please Select Brand</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start gap-3 mt-4">
                                    <button type="button" class="btn btn-info btn-label right ms-auto nexttab nexttab" id="steparrow-attributes"
                                        data-nexttab="steparrow-attributes-info-tab"><i
                                            class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Next</button>
                                </div>
                            </div>
                            <!-- end tab pane -->

                            <div class="tab-pane fade" id="steparrow-attributes-info" role="tabpanel"
                                aria-labelledby="steparrow-attributes-info-tab">
                                <div>
                                    <label for="">List of Attributes</label>
                                    <div class="row justify-content-center" id="attributes">
                                        <h5 class="text-danger">No available attributes</h5>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start gap-3 mt-4">
                                    <button type="button" class="btn btn-danger btn-label previestab"
                                        data-previous="steparrow-gen-info-tab"><i
                                            class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i> Back to
                                        General</button>
                                    <button type="button" class="btn btn-info btn-label right ms-auto nexttab nexttab" id="steparrow-pricing"
                                        data-nexttab="steparrow-pricing-info-tab"><i
                                            class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Next</button>
                                </div>
                            </div>
                            <!-- end tab pane -->

                            <div class="tab-pane fade" id="steparrow-pricing-info" role="tabpanel"
                                aria-labelledby="steparrow-pricing-info-tab">
                                <div>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="row mb-3">
                                                <div class="col-lg-3">
                                                    <label for="product_code" class="form-label">Product
                                                        MSRP
                                                        <span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-lg-9">
                                                    <input type="hidden" name="product_price_id" id="product_price_id" value="{{ $price->product_price_id }}">
                                                    <input type="text" class="form-control numeric" id="msrp"  value="{{ $price->msrp }}"
                                                        name="msrp" placeholder="Enter Product MSRP">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="row mb-3">
                                                <div class="col-lg-3">
                                                    <label for="product_upc" class="form-label">Supplier
                                                        Price <span class="text-danger">*</span></label></label>
                                                </div>
                                                <div class="col-lg-9">
                                                    <input type="url" class="form-control numeric" id="supplier_price"  value="{{ $price->supplier_price }}"
                                                        name="supplier_price" placeholder="Enter Supplier Price">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="row mb-3">
                                                <div class="col-lg-3">
                                                    <label for="product_name" class="form-label">Product
                                                        SRP
                                                        <span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-lg-9">
                                                    <input type="url" class="form-control numeric" id="product_srp"  value="{{ $price->srp }}"
                                                        name="product_srp" placeholder="Enter Product SRP">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="row mb-3">
                                                <div class="col-lg-3">
                                                    <label for="product_name" class="form-label">Special
                                                        Price
                                                        <span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-lg-9">
                                                    <input type="url" class="form-control numeric" id="special_price"  value="{{ $price->special_price }}"
                                                        name="special_price" placeholder="Enter Special Price">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start gap-3 mt-4">
                                    <button type="button" class="btn btn-danger btn-label previestab"
                                        data-previous="steparrow-attributes-info-tab"><i
                                            class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i> Back to
                                        Attributes</button>
                                    <button type="button" class="btn btn-info btn-label right ms-auto nexttab nexttab" id="steparrow-units"
                                        data-nexttab="steparrow-units-info-tab"><i
                                            class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Next</button>
                                </div>
                            </div>
                            <!-- end tab pane -->
                            <div class="tab-pane fade" id="steparrow-units-info" role="tabpanel"
                                aria-labelledby="steparrow-units-info-tab">
                                <div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="row mb-3">
                                                <div class="form-group">
                                                    <label for="uom_id" class="form-label">Product UOM <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-control" name="uom_id" data-choices
                                                        data-choices-removeItem multiple id="uom_id">
                                                        <option value="">Select Unit of Measure </option>
                                                        <? foreach($uom as $un) : ?>
                                                        <option value="<?= $un->uom_id ?>" <?=(in_array($un->uom_id,$prod_uom)) ? 'selected' : ''?> ><?= $un->code ?> -
                                                            <?= $un->uom_desc ?></option>
                                                        <? endforeach;?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start gap-3 mt-4">
                                    <button type="button" class="btn btn-danger btn-label previestab"
                                        data-previous="steparrow-pricing-info-tab"><i
                                            class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i> Back to
                                        Pricing</button>
                                        <button type="button"
                                        class="btn btn-success btn-label right ms-auto rounded-pill submit-product"><i
                                            class="ri-check-double-line label-icon align-middle rounded-pill fs-16 ms-2"></i>Save</button>
                                </div>
                            </div>
                        </div>
                        <!-- end tab content -->
                    </form>
                </div>
                <!-- end card body -->
            </div>
        <!-- end card -->
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
