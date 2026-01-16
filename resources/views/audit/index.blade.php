@extends('layouts.master')
@section('title')
    Audit Trail
@endsection
@section('css')
    <link rel="stylesheet" href="{{ URL::asset('/assets/libs/@tarekraafat/@tarekraafat.min.css') }} ">
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Reports
        @endslot
        @slot('title')
            Audit Trail
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="tasksList">
                <div class="card-header border-0">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">Audit Trail Reports</h5>
                        <div class="flex-shrink-0">
                            <a href="#" class="submit-audit-logs-xls btn btn-secondary btn-label rounded-pill end-0"><i class="ri-file-excel-line label-icon align-middle rounded-pill fs-16 me-2"></i>Export to Excel</a>
                        </div>
                    </div>
                </div>
                <div class="card-body border border-dashed border-end-0 border-start-0">
                    <form action="{{ route('reports.audit-logs') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-lg-3 col-sm-12">
                                <div class="search-box">
                                    <input type="text" name="q" class="form-control search"
                                        placeholder="Search for tasks or something...">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <select class="form-select select2" id="customer" name="user_id">
                                    <option value="">Select User</option>
                                    <? foreach($client_list as $customer) : ?>
                                        <option value="<?=$customer->id?>" <?=($request->user_id == $customer->id) ? 'selected': ''?> ><?=$customer->name?></option>
                                    <? endforeach;?>
                                </select>
                            </div>
                            <div class="col-lg-3 col-sm-3">
                                <div class="input-light">
                                    <select class="form-control" data-choices data-choices-search-false name="status"
                                        id="status">
                                        <option value="">Status</option>
                                        <option <?= $request->status == 'all' ? 'selected' : '' ?> value="all" selected>
                                            All Status</option>
                                        <option <?= $request->status == 'open' ? 'selected' : '' ?> value="open">Open
                                        </option>
                                        <option <?= $request->status == 'closed' ? 'selected' : '' ?> value="closed">Closed
                                        </option>
                                        <option <?= $request->status == 'posted' ? 'selected' : '' ?> value="posted">Posted
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <button type="submit" class="btn btn-primary w-100"> <i
                                        class="ri-equalizer-fill me-1 align-bottom"></i>
                                    Filters
                                </button>
                            </div>
                            <!--end col-->
                            <div class="col-lg-3 col-sm-3">
                                <div class="input-light">
                                    <select class="form-control" data-choices data-choices-search-false name="type"
                                        id="type">
                                        <option value="">Transaction Type</option>
                                        <option <?= $request->type == 'WD' ? 'selected' : '' ?> value="WD">Withdrawal
                                        </option>
                                        <option <?=$request->type == 'RCV' ? 'selected' : '' ?> value="RCV">Receiving
                                        </option>
                                        <option <?= $request->type == 'PO' ? 'selected' : '' ?> value="PO">Purchase Order
                                        </option>
                                        <option <?= $request->type == 'SM' ? 'selected' : '' ?> value="SM">Stock Movement
                                        </option>
                                        <option <?= $request->type == 'ST' ? 'selected' : '' ?> value="ST">Stock Transfer
                                        </option>
                                        <option <?= $request->type == 'DP' ? 'selected' : '' ?> value="DP">Dispatch
                                        </option>
                                        <option <?= $request->type == 'DO' ? 'selected' : '' ?> value="DO">Delivery Order
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-4">
                                <input type="text" class="form-control" name="date" id="date_picker"
                                    data-provider="flatpickr" data-date-format="Y-m-d" value="{{isset($request->date) ? $request->date : ''}}" data-range-date="true"
                                    placeholder="Select date range">
                            </div>
                            <!--end col-->

                            <!--end col-->
                        </div>
                        <!--end row-->
                    </form>
                </div>
                <!--end card-body-->
                <div class="card-body">
                    <div class="table-responsive table-card mb-4">
                        <table class="table align-middle table-nowrap mb-0" id="tasksTable">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th class="sort" data-sort="dispatch_date">Transaction No</th>
                                    <th class="sort" data-sort="dispatch_no">Transaction Type</th>
                                    <th class="sort" data-sort="dispatch_no">Status</th>
                                    <th class="sort" data-sort="dispatch_no">Created By</th>
                                    <th class="sort" data-sort="dispatch_no">Created At</th>
                                    <th class="sort" data-sort="dispatch_no">Updated At</th>
                                </tr>
                            </thead>

                            <tbody class="list form-check-all">
                                <? if(count($data_list) > 0 ) : ?>
                                <? foreach($data_list as $rd) :?>
                                <tr>
                                    <td>
                                        {{ $rd->control_no }}
                                    </td>
                                    <td>
                                        @switch($rd->type)
                                        @case('WD')
                                            Withdrawal
                                            @break
                                        @case('RCV')
                                            Receiving
                                            @break
                                        @case('masterfile')
                                            Receiving
                                            @break
                                        @case('PO')
                                            Purchase Order
                                            @break
                                        @case('SM')
                                            Stock Movement
                                            @break
                                        @case('ST')
                                            Stock Transfer
                                            @break
                                        @case('DP')
                                            Dispatch
                                            @break
                                        @case('DO')
                                            Delivery Order
                                            @break
                                        @case('D')
                                            Delivery Order
                                            @break
                                        @default
                                            {{ $rd->type }}
                                    @endswitch
                                    </td>
                                    <td>
                                        {{ $rd->status }}
                                    </td>
                                    <td>
                                        {{ $rd->created_by }}
                                    </td>
                                    <td>
                                        {{ date('Y/m/d H:i:s',strtotime($rd->created_at)) }}
                                    </td>
                                    <td>
                                        {{ date('Y/m/d H:i:s',strtotime($rd->updated_at)) }}
                                    </td>
                                </tr>
                                <? endforeach; ?>
                                <? else :?>
                                <div class="noresult" style="display: none">
                                    <div class="text-center">
                                        <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                            colors="primary:#121331,secondary:#08a88a"
                                            style="width:75px;height:75px"></lord-icon>
                                        <h5 class="mt-2">Sorry! No Result Found</h5>
                                        <p class="text-muted mb-0">We've searched more than 200k+ tasks We did not find any
                                            tasks
                                            for you search.</p>
                                    </div>
                                </div>
                                <? endif; ?>
                            </tbody>
                        </table>
                        <!--end table-->
                    </div>
                    <!-- Pagination -->
                    {{-- {!! $data_list->withQueryString()->links('pagination::bootstrap-5') !!} --}}
                </div>
                <!--end card-body-->
            </div>
            <!--end card-->
        </div>
        <!--end col-->
    </div>
    <!--end row-->
@endsection
@section('script')
    <script src="{{ URL::asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/select2/select2.min.js') }}"></script>

    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
    <!-- autocomplete js -->
    <script src="{{ URL::asset('/assets/libs/@tarekraafat/@tarekraafat.min.js') }}"></script>

    <script src="{{ URL::asset('/assets/js/report/report.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/report/tableToExcel.js') }}"></script>

@endsection
