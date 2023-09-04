@extends('layouts.master')
@section('title')
    Dispatch
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
            Outbound
        @endslot
        @slot('title')
        Dispatch
        @endslot
    @endcomponent

    <div class="row justify-content-center">
        <div class="col-xxl-11">
            <div class="card" id="tasksList">
                <div class="card-header border-0">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">Create Dispatch</h5>
                        <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">
                            <button data-status="open" class="submit-open btn btn-success btn-label rounded-pill"><i
                                    class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                                Save</button>
                            <button data-status="posted" class="submit-posted  btn btn-info btn-label rounded-pill d-none"><i
                                    class="ri-lock-line label-icon align-middle rounded-pill fs-16 me-2"></i> Post</button>
                            <a href="{{ URL::to('dispatch') }}" class="btn btn-primary btn-label rounded-pill"><i
                                    class="ri-arrow-go-back-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                                Back</a>
                        </div>
                    </div>
                </div>
            </div>
            <!--end card-->
        </div>
        <!--end col-->
    </div>
    <!--end row-->
    <form name="submit-dispatch" id="submit-dispatch">
        <div class="row justify-content-center">
            <div class="col-xxl-11">
                <div class="card" id="demo">
                    <div class="row ">
                        <div class=" col-lg-12">
                            <div class="card-header card-title mb-0 flex-grow-1">
                                <div class="d-flex align-items-center">
                                    <h5 class="card-title mb-0 flex-grow-1">Withdrawal</h5>
                                    <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">
                                        <button type="button" id="find-withdrawal" class="btn btn-warning btn-label rounded-pill"><i
                                                class="ri-book-read-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                                            Find Withdrawal</button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <div class="table-responsive">
                                    <table class="table table-nowrap" id="withdrawal-list">
                                        <thead>
                                            <tr class="table-active">
                                                <th scope="col" style="width: 10px;">#</th>
                                                <th scope="col">WD #</th>
                                                <th scope="col">Client</th>
                                                <th scope="col">Deliver To</th>
                                                <th scope="col">No. of Package</th>
                                                <th scope="col">Order No.</th>
                                                <th scope="col">Order Date</th>
                                                <th scope="col">DR Number</th>
                                                <th scope="col">PO Number</th>
                                                <th scope="col">Sales Invoice</th>
                                                <th scope="col" class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="newlink">
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="4" class="text-end">Total</td>
                                                <td class="text-center" id="total">0.00</td>
                                                <td colspan="6"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <!--end table-->
                                </div>
                            </div>
                            <!--end card-body-->
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                </div>
                <!--end card-->
            </div>
            <!--end col-->
            <div class="col-xxl-11">
                <div class="card" id="demo">
                    <div class="row ">
                        <div class=" col-lg-12">
                            <div class="card-header card-title mb-0 flex-grow-1">
                                <div class="d-flex align-items-center">
                                    <h5 class="card-title mb-0 flex-grow-1">Vehicle</h5>
                                    <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">
                                        <button type="button" id="add-row" class="btn btn-info btn-label rounded-pill"><i
                                                class="ri-add-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                                            Add Truck</button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <div class="table-responsive">
                                    <table class="table table-nowrap" id="truck-list">
                                        <thead>
                                            <tr class="table-active">
                                                <th scope="col">Truck Type</th>
                                                <th scope="col">No. of Package</th>
                                                <th scope="col">Plate No.</th>
                                                <th scope="col">Driver</th>
                                                <th scope="col">Contact</th>
                                                <th scope="col" class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="newlink">
                                            <tr>
                                                <td>
                                                    <select class="form-select select2 truck_type" required="required" id="truck_type" name="truck_type[]">
                                                        <option value="">Select Truck Type</option>
                                                        <? foreach($truck_type_list as $truck) : ?>
                                                            <option value="<?=$truck->vehicle_code?>" ><?="(".$truck->vehicle_code.") ".$truck->vehicle_desc?></option>
                                                        <? endforeach;?>
                                                    </select>
                                                    <span class="text-danger error-msg truck_type0_error"></span>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control numeric" id="no_of_package"
                                                        name="no_of_package[]" placeholder="Enter Quantity">
                                                    <span class="text-danger error-msg no_of_package0_error"></span>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" id="plate_no"
                                                        name="plate_no[]" placeholder="Enter Plate No.">
                                                    <span class="text-danger error-msg plate_no0_error"></span>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" id="driver"
                                                        name="driver[]" placeholder="Enter Driver">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" id="contact"
                                                        name="contact[]" placeholder="Enter Contact">
                                                </td>
                                                <td>
                                                    <div class="text-center">
                                                        <button type="button" class="remove-row btn btn-icon btn-danger remove-truck mx-2 waves-effect waves-light">
                                                            <i class="ri-delete-bin-5-fill"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <!--end table-->
                                </div>
                            </div>
                            <!--end card-body-->
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                </div>
                <!--end card-->
            </div>
        </div>
    </form>


    <!-- show charges Modal -->
    <div class="modal" id="show-withdrawal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" id="exampleModalLabel">Withdrawal List</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <h6 class="text-muted text-uppercase fw-semibold">Keyword</h6>
                            <p class="fw-medium" id="billing-name">
                                <input type="text" class="form-control" id="keyword" name="keyword" value="" placeholder="Wd No,Order No, PO, Sales Invoice, DR No">
                            </p>
                        </div>
                        <div class="col-2">
                            <h6 class="text-muted text-uppercase fw-semibold">&nbsp;</h6>
                            <p class="fw-medium" id="billing-name">
                                <button data-status="open" id="search-withdrawal" class="btn btn-warning btn-label rounded-pill"><i class="ri-search-line label-icon align-middle rounded-pill fs-16 me-2"></i> Search </button>
                            </p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle" width="100%" style="font-size: 12px;" id="show-withdrawal-list">
                            <thead class="table-light">
                                <tr>
                                    <th>&nbsp;</th>
                                    <th scope="col">WD #</th>
                                    <th scope="col">Client</th>
                                    <th scope="col">Deliver To</th>
                                    <th scope="col">No. of Package</th>
                                    <th scope="col">Order No.</th>
                                    <th scope="col">Order Date</th>
                                    <th scope="col">DR Number</th>
                                    <th scope="col">PO Number</th>
                                    <th scope="col">Sales Invoice</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-success" id="add-withdrawal"><i
                                class="ri-add-line label-icon align-middle rounded-pill fs-16 me-2"></i> Add</button>
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
    <script src="{{ URL::asset('/assets/js/dispatch/dispatch.js') }}"></script>
@endsection
