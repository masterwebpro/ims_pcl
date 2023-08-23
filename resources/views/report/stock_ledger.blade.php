@extends('layouts.master')
@section('title') Report @endsection
@section('css')

<!--datatable css-->
<link href="{{ URL::asset('assets/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
<!--datatable responsive css-->
<link href="{{ URL::asset('assets/css/responsive.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />


@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Report @endslot
@slot('title') Stock Leger @endslot
@endcomponent

<div class="row justify-content-center">
    <div class="col-xxl-11">
        <div class="card" id="tasksList">
            <div class="card-header border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1"></h5>
                    <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">
                        <button class="submit-xls btn btn-secondary btn-label rounded-pill"><i class="ri-file-excel-line label-icon align-middle rounded-pill fs-16 me-2"></i> Export to Excel</button>
                        <button class="submit-print btn btn-primary btn-label rounded-pill"><i class="ri-printer-line label-icon align-middle rounded-pill fs-16 me-2"></i> Print</button>
                    </div>
                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>
<!--end row-->
<form name="submit-receive" id="submit-ledger">
    <div class="row justify-content-center">
        <div class="col-xxl-11">
            <div class="card">
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <div class="card-body p-4 ">
                            <div class="row g-3">
                                <div class="col-lg-2 col-md-4">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Client Name <span class="text-danger">*</span></h6>
                                    <p class=" mb-2" id="billing-name">
                                        <select class="form-select select2" required="required" id="client" name="client">
                                            <option value="">Select Client</option>                                                            
                                            <? foreach($client_list as $client) : ?>
                                                <option value="<?=$client->id?>" ><?=$client->client_name?></option>
                                            <? endforeach;?>
                                        </select>
                                        <span class="text-danger error-msg client_error"></span>
                                    </p>
                                </div>
                                <!--end col-->
                                <div class="col-lg-2 col-md-4">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Site Address <span class="text-danger">*</span></h6>
                                    <p class=" mb-2" id="shipping-name">
                                        <select class="form-select select2" required="required" id="store" name="store">
                                            <option value="">Select Store/Warehouse</option>                                                            
                                        </select>
                                        <span class="text-danger error-msg store_error"></span>
                                    </p>
                                </div>
                                <!--end col-->
                                <div class="col-lg-2 col-md-4">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Warehouse <span class="text-danger">*</span></h6>
                                    <p class="mb-2" id="warehouse1">
                                        <select class="form-select select2" required="required" id="warehouse" name="warehouse">
                                            <option value="">Select warehouse</option>                                                            
                                        </select>
                                        <span class="text-danger error-msg warehouse_error"></span>
                                    </p>
                                </div>
                                 <!--end col-->

                                 <div class="col-lg-2 col-md-4">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Item Type <span class="text-danger">*</span></h6>
                                    <p class=" mb-2" id="item_type">
                                        <select class="form-select select2" required="required" id="item_type" name="item_type">
                                            <option value="good">Good</option>   
                                            <option value="damage">Damage</option>   
                                            <option value="repair">Repair</option>                                                            
                                        </select>
                                        <span class="text-danger error-msg item_type_error"></span>
                                    </p>
                                </div>
                                 <!--end col-->
                                 <div class="col-lg-2 col-md-4">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Item Code <span class="text-danger">*</span></h6>
                                    <p class="fw-medium mb-2" id="item_code">
                                        <input type="text" class="form-control" placeholder="Item Code" name="item_code" id="item_code">
                                        <span class="text-danger error-msg item_code_error"></span>
                                     </p>
                                </div>
                                 <!--end col-->

                                <div class="col-lg-2 col-md-4">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">&nbsp;</h6>
                                    <button class="submit-search btn btn-warning btn-label rounded-pill"><i class="ri-search-line label-icon align-middle rounded-pill fs-16 me-2"></i> Search</button>
                                </div>
                                 <!--end col-->
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

    <div class="row justify-content-center">
        <div class="col-xxl-11">
            <div class="card" id="demo">
                <div class="card-body p-4 ">
                    <div class="row ">
                        <table id="item_list" style="font-size: 11px;"  width="100%"  class="table table-striped table-bordered table-hover align-middle">
                            <thead  class="table-light">
                                <tr>
                                    <th class="fw-medium text-center" width="150px;">Date</th>
                                    <th class="fw-medium text-center">Transaction</th>
                                    <th class="fw-medium text-center">Reference No</th>
                                    <th class="fw-medium text-center">Type</th>
                                
                                    <th class="fw-medium text-center" width="110px;">Deliveries QTY</th>
                                    <th class="fw-medium text-center" width="110px;">Transfer QTY</th>
                                    <th class="fw-medium text-center" width="110px;">Withdraw QTY</th>
                                    <th class="fw-medium text-center" width="100px;">Balance</th>
                                </tr>
                            </thead>
                                <tr>
                                    <td class="fw-medium " colspan="7" width="150px;">Beggining Balance (Date)</td>
                                    <td class="fw-medium text-center" width="100px;">0</td>
                                </tr>
                            <tbody>
                                <tr>
                                    <td class="" width="150px;">2023-01-01</td>
                                    <td class="">R-20230102-001</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">Good</td>
                                    <td class="text-center" >10</td>
                                    <td class="text-center" >-</td>
                                    <td class="text-center" >-</td>
                                    <td class="text-center" >10</td>
                                </tr>

                                <tr>
                                    <td class="" width="150px;">2023-01-01</td>
                                    <td class="">WD-20230102-001</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">Good</td>
                                    <td class="text-center" >-</td>
                                    <td class="text-center" >-</td>
                                    <td class="text-center" >5</td>
                                    <td class="text-center" >5</td>
                                </tr>

                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="fw-medium text-right" colspan="7" width="150px;">Remaining Inventory as of Today</td>
                                    <td class="fw-medium text-center" width="100px;">5</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <!--end row-->
            </div>
            <!--end card-->
        </div>
        <!--end col-->
    </div>
</form>

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
<script src="{{ URL::asset('/assets/js/report/stock-ledger.js') }}"></script>

@endsection