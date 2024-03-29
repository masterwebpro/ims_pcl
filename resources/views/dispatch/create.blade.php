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
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <div class="row ms-3 mt-3 mx-3">
                                <div class="col-lg-3 col-md-3 form-group">
                                    <label for="colFormLabel" class="form-label">Plate No <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select select2" required="required" id="plate_no" name="plate_no">
                                        <option value="">Select Plate No</option>
                                        <? foreach($plate_no_list as $plate) : ?>
                                            <option value="<?=$plate->plate_no?>">{{ $plate->plate_no }}</option>
                                        <? endforeach;?>
                                    </select>
                                    <span class="text-danger error-msg plate_no_error"></span>
                                </div>
                                <div class="col-lg-3 col-md-3 form-group">
                                    <label for="colFormLabel" class="form-label">Trucker Name<span
                                        class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="trucker_name" name="trucker_name" readonly
                                        value="" placeholder="Trucker Name">
                                </div>
                                <div class="col-lg-3 col-md-3 form-group">
                                    <label for="colFormLabel" class="form-label">Truck Type <span
                                            class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="truck_type" name="truck_type"
                                            value="" placeholder="Truck Type" readonly>
                                </div>
                                <div class="col-lg-3 col-md-3 form-group">
                                    <label for="colFormLabel" class="form-label">Seal No.<span
                                        class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="seal_no" name="seal_no"
                                        value="" placeholder="Seal No.">
                                    <span class="text-danger error-msg seal_no_error"></span>
                                </div>
                            </div>
                            <div class="row ms-3 mt-3 mx-3">
                                <div class="col-lg-3 col-md-3 form-group">
                                    <label for="colFormLabel" class="form-label">Dispatcher Name<span
                                        class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="dispatch_by" name="dispatch_by"
                                        value="" placeholder="Dispatcher Name">
                                    <span class="text-danger error-msg dispatch_by_error"></span>
                                </div>
                                <div class="col-lg-3 col-md-3 form-group">
                                    <label for="colFormLabel" class="form-label">Driver<span
                                        class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="driver" name="driver"
                                        value="" placeholder="Driver Name">
                                    <span class="text-danger error-msg driver_error"></span>
                                </div>
                                <div class="col-lg-3 col-md-3 form-group">
                                    <label for="colFormLabel" class="form-label">Helper</label>
                                        <input type="text" class="form-control" id="helper" name="helper"
                                        value="" placeholder="Helper Name">
                                    <span class="text-danger error-msg helper_error"></span>
                                </div>
                                <div class="col-lg-3 col-md-3 form-group">
                                    <label for="colFormLabel" class="form-label">Contact Number</label>
                                        <input type="text" class="form-control" id="contact_no" name="contact_no"
                                        value="" placeholder="Contact">
                                    <span class="text-danger error-msg contact_no_error"></span>
                                </div>
                            </div>
                            <div class="row ms-3 mt-3 mx-3">
                                <div class="col-lg-3 col-md-3 form-group">
                                    <label for="colFormLabel" class="form-label">Dispatch Date <span
                                            class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="dispatch_date"
                                            name="dispatch_date" placeholder="Dispatch Date" value="<?=date('Y-m-d');?>">
                                        <span class="text-danger error-msg dispatch_date_error"></span>
                                </div>
                                <div class="col-lg-3 col-md-3">
                                        <label for="colFormLabel" class="form-label">Start Picking<span
                                                class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="date" class="form-control" id="start_pick_date"
                                                    name="start_pick_date" placeholder="Start Date" value="<?=date('Y-m-d');?>">
                                                <input type="time" class="form-control" id="start_pick_time"
                                                    name="start_pick_time" placeholder="Picking Date" value="{{ date('H:i') }}">
                                            </div>
                                            <span class="text-danger error-msg start_pick_time_error"></span>
                                </div>
                                <div class="col-lg-3 col-md-3 form-group">
                                    <label for="colFormLabel" class="form-label">Finish Picking<span
                                            class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="date" class="form-control" id="finish_pick_date"
                                                name="finish_pick_date" placeholder="Finish Date" value="<?=date('Y-m-d');?>">
                                            <input type="time" class="form-control" id="finish_pick_time"
                                                name="finish_pick_time" placeholder="Finish Time" value="<?=date('H:i')?>">
                                        </div>
                                        <span class="text-danger error-msg finish_pick_time_error"></span>
                                </div>
                                <div class="col-lg-3 col-md-3 form-group">
                                    <label for="colFormLabel" class="form-label">Actual Truck Arrival<span
                                            class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="date" class="form-control" id="arrival_date"
                                                name="arrival_date" placeholder="Arrival Date" value="<?=date('Y-m-d');?>">
                                            <input type="time" class="form-control" id="arrival_time"
                                                name="arrival_time" placeholder="Arrival Time" value="<?=date('H:i');?>">
                                        </div>
                                        <span class="text-danger error-msg arrival_time_error"></span>
                                </div>
                            </div>
                            <div class="row ms-3 mt-3 mx-3">
                                <div class="col-lg-3 col-md-3 form-group">
                                        <label for="colFormLabel" class="form-label">Prepared By</label>
                                            <input type="text" class="form-control" id="created_by" name="created_by" readonly
                                                value="<?=$created_by?>" placeholder="Created By">
                                            <span class="text-danger error-msg created_by_error"></span>
                                </div>
                                <div class="col-lg-3 col-md-3">
                                    <label for="colFormLabel" class="form-label">Start Loading<span
                                            class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="date" class="form-control" id="start_date"
                                                name="start_date" placeholder="Start Date" value="<?=date('Y-m-d');?>">
                                            <input type="time" class="form-control" id="start_time"
                                                name="start_time" placeholder="Dispatch Date" value="{{ date('H:i') }}">
                                        </div>
                                        <span class="text-danger error-msg start_time_error"></span>
                                </div>
                                <div class="col-lg-3 col-md-3 form-group">
                                    <label for="colFormLabel" class="form-label">Finish Loading<span
                                            class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="date" class="form-control" id="finish_date"
                                                name="finish_date" placeholder="Finish Date" value="<?=date('Y-m-d');?>">
                                            <input type="time" class="form-control" id="finish_time"
                                                name="finish_time" placeholder="Finish Time" value="<?=date('H:i')?>">
                                        </div>
                                        <span class="text-danger error-msg finish_time_error"></span>
                                </div>
                                <div class="col-lg-3 col-md-3 form-group">
                                    <label for="colFormLabel" class="form-label">Depart Date/Time<span
                                            class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="date" class="form-control" id="date_departed"
                                                name="date_departed" placeholder="Departed Date" value="<?=date('Y-m-d');?>">
                                            <input type="time" class="form-control" id="time_departed"
                                                name="time_departed" placeholder="Departed Time" value="<?=date('H:i');?>">
                                        </div>
                                        <span class="text-danger error-msg time_departed_error"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-xxl-11">
                <div class="card" id="demo">
                    <div class="row ">
                        <div class=" col-lg-12">
                            <div class="card-header card-title mb-0 flex-grow-1">
                                <div class="d-flex align-items-center">
                                    <h5 class="card-title mb-0 flex-grow-1">Withdrawal List</h5>
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
                                                {{-- <th scope="col">Client</th> --}}
                                                {{--  <th scope="col">Deliver To</th>  --}}
                                                <th scope="col">Product Name</th>
                                                <th scope="col">Withdraw Quantity</th>
                                                <th scope="col">Dispatch Quantity</th>
                                                <th scope="col">Order No.</th>
                                                <th scope="col">Order Date</th>
                                                <th scope="col">DR Number</th>
                                                {{--  <th scope="col">PO Number</th>  --}}
                                                {{--  <th scope="col">Sales Invoice</th>  --}}
                                                <th scope="col" class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="newlink">
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="4" class="text-end">Total</td>
                                                <td class="text-start" id="total">0.00</td>
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
                        <div class="col-4 hstack gap-2 justify-content-end">
                            <button type="button" class="btn btn-warning" id="select-all"><i
                                class="ri-check-double-fill label-icon align-middle rounded-pill fs-16 me-2"></i> Select All</button>
                            <button type="button" class="btn btn-success" id="add-withdrawal"><i
                                    class="ri-add-line label-icon align-middle rounded-pill fs-16 me-2"></i> Add Selected</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle" width="100%" style="font-size: 12px;" id="show-withdrawal-list">
                            <thead class="table-light">
                                <tr>
                                    <th>&nbsp;</th>
                                    <th scope="col">WD #</th>
                                    <th scope="col">Customer</th>
                                    <th scope="col">Deliver To</th>
                                    <th scope="col">SAP Code</th>
                                    <th scope="col">Product Name</th>
                                    <th scope="col">Withdraw Quantity</th>
                                    <th scope="col">Unit</th>
                                    <th scope="col">Order No.</th>
                                    <th scope="col">Order Date</th>
                                    <th scope="col">DR Number</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
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
