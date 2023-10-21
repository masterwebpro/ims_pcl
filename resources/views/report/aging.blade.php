@extends('layouts.master')
@section('title')
    Reports
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
            Aging
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="tasksList">
                <div class="card-header border-0">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">Aging</h5>
                        <div class="flex-shrink-0">
                            <a href="#" class="submit-outbound-monitoring-xls btn btn-secondary btn-label rounded-pill end-0"><i class="ri-file-excel-line label-icon align-middle rounded-pill fs-16 me-2"></i>Export to Excel</a>
                        </div>
                    </div>
                </div>
                <div class="card-body border border-dashed border-end-0 border-start-0">
                    <form action="{{ route('reports.outbound-monitoring') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-xxl-4 col-sm-12">
                                <div class="search-box">
                                    <input type="text" name="q" class="form-control search"
                                        placeholder="Search for tasks or something...">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>
                            <!--end col-->

                            <div class="col-xxl-2 col-sm-4">
                                <div class="input-light">
                                    <select class="form-control" name="filter_date" id="filter_date">
                                        <option value="dispatch_date">Dispatch Date</option>
                                        <option value="created_at">Created Date</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-xxl-2 col-sm-4">
                                <input type="text" class="form-control" name="date" id="date_picker"
                                    data-provider="flatpickr" data-date-format="Y-d-m" data-range-date="true"
                                    placeholder="Select date range">
                            </div>
                            <!--end col-->
                            <div class="col-xxl-2 col-sm-4">
                                <button type="submit" class="btn btn-primary w-100"> <i
                                        class="ri-equalizer-fill me-1 align-bottom"></i>
                                    Filters
                                </button>
                            </div>
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
                                    <th class="sort" data-sort="dispatch_date">PRODUCT CODE</th>
                                    <th class="sort" data-sort="dispatch_date">PRODUCT NAME</th>
                                    <th class="sort" data-sort="dispatch_no">OPENING INVENTORY</th>
                                    <th class="sort" data-sort="dispatch_no">RECEIVING</th>
                                    <th class="sort" data-sort="dispatch_no">WITHDRAWAL</th>
                                    <th class="sort" data-sort="dispatch_no">CLOSING INVENTORY</th>
                                    <th class="sort" data-sort="dispatch_no">30 DAYS</th>
                                    <th class="sort" data-sort="dispatch_no">60 DAYS</th>
                                    <th class="sort" data-sort="dispatch_no">90 DAYS</th>
                                    <th class="sort" data-sort="dispatch_no">120 DAYS</th>
                                    <th class="sort" data-sort="dispatch_no">150 DAYS</th>
                                    <th class="sort" data-sort="dispatch_no">OVER 150 DAYS</th>
                                </tr>
                            </thead>

                            <tbody class="list form-check-all">
                                {{--  <? if($data_list->total() > 0 ) : ?>
                                <? foreach($data_list as $rd) :?>
                                <tr>
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
                                <? endif; ?>  --}}
                            </tbody>
                        </table>
                        <!--end table-->
                    </div>
                    <!-- Pagination -->
                    {{--  {!! $data_list->withQueryString()->links('pagination::bootstrap-5') !!}  --}}
                </div>
                <!--end card-body-->
            </div>
            <!--end card-->
        </div>
        <!--end col-->
    </div>
    <!--end row-->

    <div class="modal fade" id="show-po" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" id="exampleModalLabel">Search PO Number</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12 form-group">
                            <input type="text" class="form-control" dir="ltr" spellcheck=false
                                autocomplete="off" autocapitalize="off" name="po_num_holder" id="po_num_holder"
                                value="" placeholder="Enter PO Number">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-success" id="do-po-btn">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/select2/select2.min.js') }}"></script>

    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
    <!-- autocomplete js -->
    <script src="{{ URL::asset('/assets/libs/@tarekraafat/@tarekraafat.min.js') }}"></script>

    <script src="{{ URL::asset('/assets/js/report/report.js') }}"></script>
@endsection
