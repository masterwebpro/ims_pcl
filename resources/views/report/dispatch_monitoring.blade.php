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
        Dispatch
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="tasksList">
                <div class="card-header border-0">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">Dispatch Monitoring</h5>
                        <div class="flex-shrink-0">
                            <a href="#" class="submit-dispatch-monitoring-xls btn btn-secondary btn-label rounded-pill end-0"><i class="ri-file-excel-line label-icon align-middle rounded-pill fs-16 me-2"></i>Export to Excel</a>
                        </div>
                    </div>
                </div>
                <div class="card-body border border-dashed border-end-0 border-start-0">
                    <form action="{{ route('reports.dispatch.monitoring') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-lg-3 col-sm-12">
                                <div class="search-box">
                                    <input type="text" name="q" class="form-control search"
                                        placeholder="Search for tasks or something...">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <select class="form-select select2" id="customer" name="customer">
                                    <option value="">Select Customer</option>
                                    <? foreach($client_list as $customer) : ?>
                                        <? if($customer->client_type == 'C') : ?>
                                            <option value="<?=$customer->id?>" <?=($request->customer == $customer->id) ? 'selected': ''?> ><?=$customer->client_name?></option>
                                        <? endif;?>
                                    <? endforeach;?>
                                </select>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <select class="form-select select2" id="company" name="company">
                                    <option value="">Select Company</option>
                                    <? foreach($client_list as $company) : ?>
                                        <? if($company->client_type == 'O') : ?>
                                            <option value="<?=$company->id?>"  <?=($request->company == $company->id) ? 'selected': ''?> ><?=$company->client_name?></option>
                                        <? endif;?>
                                    <? endforeach;?>
                                </select>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <button type="submit" class="btn btn-primary w-100"> <i
                                        class="ri-equalizer-fill me-1 align-bottom"></i>
                                    Filters
                                </button>
                            </div>
                            <!--end col-->
                            <div class="col-lg-3 col-sm-4">
                                <div class="input-group">
                                    <span class="input-group-text">Select Year</span>
                                    <input type="number" class="form-control" id="year" name="year" value="<?=($request->year) ? $request->year : date('Y')?>" placeholder="Select Year">
                                </div>
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
                        <table class="table align-middle table-bordered table-nowrap mb-0" id="tasksTable">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th rowspan="3" class="align-middle text-left">CATEGORY</th>
                                    <th rowspan="3" class="align-middle text-center">COUNT of QUANTITY (in PCS) <br/> MATERIAL NO</th>
                                    <th rowspan="3" class="align-middle text-center">DESCRIPTION</th>
                                    <th colspan="<?=count($workWeeks) ?>" class="text-center">WEEK NO</th>
                                    <th rowspan="3" class="align-middle text-center">GRAND TOTAL</th>
                                </tr>
                                <tr>
                                    <? foreach ($monthofWeek as $month => $week) : ?>
                                        <th class="text-center" colspan="{{ count($week) }}">{{ $month }}</th>
                                    <? endforeach; ?>
                                </tr>
                                <tr>
                                    <? foreach ($workWeeks as $wk) : ?>
                                        <th class="text-center">WK {{$wk}}</th>
                                    <? endforeach; ?>
                                </tr>
                            </thead>
                            <tbody class="list">
                                @if (count($data_list) > 0)
                                    <? foreach ($data_list as $product_code =>  $week) : ?>
                                        <tr>
                                            <td>{{ $week['category_name'] }}</td>
                                            <td>{{$product_code}}</td>
                                            <td class="text-wrap">{{ $week['product_name']}}</td>
                                            <?
                                            $total = 0;
                                            foreach ($week['week'] as $wk) : ?>
                                                <td class="text-end">{{number_format($wk,2,'.',',')}}</td>
                                                <? $total += $wk; ?>
                                            <? endforeach; ?>
                                            <td class="text-end">{{ number_format($total,2,'.',',')}}</td>
                                        </tr>
                                    <? endforeach; ?>
                                @else
                                    <tr>
                                        <td colspan="55" class="text-danger text-center">No Record Found!</td>
                                    </tr>
                                @endif

                            </tbody>
                        </table>
                        <!--end table-->
                    </div>
                    <!-- Pagination -->
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
