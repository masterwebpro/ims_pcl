@extends('layouts.master')
@section('title')
    Expense
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
        Expense
        @endslot
    @endcomponent

    <div class="row justify-content-center">
        <div class="col-xxl-11">
            <div class="card" id="tasksList">
                <div class="card-header border-0">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1"><?=$expense->expense_no?></h5>
                        <div class="col-md-2 text-center">
                            <span class="badge  fs-16 <?=$expense->status?> text-uppercase"><?=$expense->status?></span>
                        </div>
                        <div class="col-md-6 text-end">
                            <button data-status="open" class="submit-open btn btn-success btn-label rounded-pill"><i
                                class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                            Save</button>
                            <button data-status="posted" class="submit-posted  btn btn-info btn-label rounded-pill"><i
                                class="ri-lock-line label-icon align-middle rounded-pill fs-16 me-2"></i> Post</button>
                                <a href="{{ URL::to('expense') }}" class="btn btn-primary btn-label rounded-pill"><i
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
    <form name="submit-expense" id="submit-expense">
        <div class="row justify-content-center">
            <div class="col-xxl-11">
                <div class="card" id="demo">
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <div class="row ms-3 mt-3 mx-3">
                                <div class="col-lg-3 col-md-3 form-group">
                                    <input type="hidden" name="expense_no" value="{{ $expense->expense_no }}">
                                    <label for="colFormLabel" class="form-label">Plate No <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select select2" required="required" id="plate_no" name="plate_no">
                                        <option value="">Select Plate No</option>
                                        <? foreach($plate_no_list as $plate) : ?>
                                            <option value="<?=$plate->plate_no?>" <?=($plate->plate_no == $expense->plate_no) ? 'selected' : ''?>>{{ $plate->plate_no }}</option>
                                        <? endforeach;?>
                                    </select>
                                    <span class="text-danger error-msg plate_no_error"></span>
                                </div>
                                <div class="col-lg-3 col-md-3 form-group">
                                    <label for="colFormLabel" class="form-label">Trucker Name</label>
                                    <input type="text" class="form-control" id="trucker_name" name="trucker_name" readonly
                                        value="" placeholder="Trucker Name">
                                </div>
                                <div class="col-lg-3 col-md-3 form-group">
                                    <label for="colFormLabel" class="form-label">Truck Type</label>
                                            <input type="text" class="form-control" id="truck_type" name="truck_type"
                                            value="" placeholder="Truck Type" readonly>
                                </div>
                                <div class="col-lg-3 col-md-3 form-group">
                                    <label for="colFormLabel" class="form-label">Expense Date <span
                                            class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="expense_date"
                                            name="expense_date" placeholder="expense Date" value="<?=date('Y-m-d',strtotime($expense->expense_date));?>">
                                        <span class="text-danger error-msg expense_date_error"></span>
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
                            <div class="card-body p-4">
                                <ul class="nav nav-tabs nav-justified nav-border-top nav-border-top-primary mb-3" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#home1" role="tab">
                                            DISPATCH LIST
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#profile1" role="tab">
                                            CHARGES LIST
                                        </a>
                                    </li>
                                </ul>

                                <!-- Tab panes -->
                                <div class="tab-content text-muted">
                                    <div class="tab-pane active" id="home1" role="tabpanel">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="col-md-12 mb-2">
                                                    <div class="input-group">
                                                        <span class="input-group-text" id="inputGroup-sizing-sm"><i
                                                                class="ri-barcode-line label-icon align-middle rounded-pill fs-16 me-2"></i>Scan
                                                            Code</span>
                                                        <input type="text" class="form-control"
                                                            aria-label="Recipient's username with two button addons">
                                                        <button class="btn btn-warning" id="find-dispatch" type="button"><i
                                                                class="ri-book-read-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                                                            Find Dispatch</button>
                                                    </div>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-striped" id="dispatch-list">
                                                        <thead>
                                                            <tr class="table-active">
                                                                <th scope="col">Dispatch No</th>
                                                                <th scope="col">Dispatch Date</th>
                                                                <th scope="col" class="text-center">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="dispatch">
                                                            <?
                                                            $rowCount = count($expense->items);
                                                            $x=1;
                                                            $total = 0;
                                                             ?>
                                                            @if(isset($expense->items))
                                                                @foreach($expense->items as $item)
                                                                <tr id="dispacth_{{$item->id}}">
                                                                    <td class="text-start fs-14">
                                                                        {{$item->dispatch_no}}

                                                                        <input type="hidden" name="dispatch_no[]" value="{{ $item->dispatch_no }}" />
                                                                    </td>
                                                                    <td class="text-start fs-14">
                                                                        {{ date('M d, Y', strtotime($item->dispatch_date)) }}
                                                                        <input type="hidden" name="dispatch_date[]" value="{{ $item->dispatch_date }}" />
                                                                    </td>
                                                                    <td>
                                                                        <div class="text-center text-align-justify">
                                                                            <button type="button" class="btn btn-danger mx-2 waves-effect waves-light remove-dispatch" data-id="{{ $x }}"><i class="ri-delete-bin-5-fill"></i> Remove</button>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                                @else
                                                                <tr class="">
                                                                    <td colspan="9" class="text-danger text-center">No Record Found!</td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="profile1" role="tabpanel">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="table-responsive">
                                                    <table class="table table-striped" id="particular-list">
                                                        <thead>
                                                            <tr class="table-active">
                                                                <th scope="col">Particulars</th>
                                                                <th scope="col">Amount</th>
                                                                <th scope="col" class="text-center">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="particular">
                                                            <?
                                                            $rowCount = count($expense->charges);
                                                            $x=1;
                                                            $total = 0;
                                                             ?>
                                                            @if(isset($expense->charges))
                                                                @foreach($expense->charges as $charge)
                                                                <tr id="product_{{$charge->id}}">
                                                                    <td>
                                                                        <input type="text" class="form-control" id="particular" value="{{ $charge->particular }}"
                                                                            name="particular[]" placeholder="Enter particular">
                                                                        <span class="text-danger error-msg particular{{ $x++ }}_error"></span>
                                                                    </td>
                                                                    <td class="text-start fs-14">
                                                                        <input type="text" class="form-control numeric" id="amount" value="{{ $charge->amount }}"
                                                                        name="amount[]" placeholder="Enter amount">
                                                                    <span class="text-danger error-msg amount{{ $x++ }}_error"></span>
                                                                    </td>
                                                                    <td>
                                                                        <div class="text-center">
                                                                            <button type="button" class="remove-row btn btn-danger remove-row mx-2 waves-effect waves-light">
                                                                                <i class="ri-delete-bin-5-fill"></i> Remove
                                                                            </button>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                                @else
                                                                <tr class="">
                                                                    <td colspan="9" class="text-danger text-center">No Record Found!</td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                        <tfoot>
                                                            <td colspan="3">
                                                                <button type="button" id="add-row" class="btn btn-info btn-label rounded-pill"><i
                                                                class="ri-add-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                                                                Add</button>
                                                                <button type="button" id="select-particular" class="btn btn-warning btn-label rounded-pill"><i
                                                                    class="ri-search-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                                                                    Select</button>
                                                            </td>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
        <div class="modal" id="show-dispatch" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" id="exampleModalLabel">Dispatch List</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
                </div>

                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table align-middle" width="100%" style="font-size: 12px;" id="show-dispatch-list">
                            <thead class="table-light">
                                <tr>
                                    <th>&nbsp;</th>
                                    <th scope="col">Dispatch No</th>
                                    <th scope="col">Dispatch Date</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-success" id="add-dispatch"><i
                                class="ri-add-line label-icon align-middle rounded-pill fs-16 me-2"></i> Add</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="show-particular" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" id="exampleModalLabel">Particular List</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
                </div>

                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table align-middle" width="100%" style="font-size: 12px;" id="show-particular-list">
                            <thead class="table-light">
                                <tr>
                                    <th>&nbsp;</th>
                                    <th scope="col">Code</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-success" id="add-selected-particular"><i
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
    <script src="{{ URL::asset('/assets/js/expense/expense.js') }}"></script>
@endsection
