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
        Analysis
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="tasksList">
                <div class="card-header border-0">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">Analysis</h5>
                        <div class="flex-shrink-0">
                            <a href="#" class="submit-analysis-xls btn btn-secondary btn-label rounded-pill end-0"><i class="ri-file-excel-line label-icon align-middle rounded-pill fs-16 me-2"></i>Export to Excel</a>
                        </div>
                    </div>
                </div>
                <div class="card-body border border-dashed border-end-0 border-start-0">
                    <form action="{{ route('reports.analysis') }}" method="GET">
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
                                    <input type="number" class="form-control" name="year" value="<?=date('Y')?>" placeholder="Select Year">
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
                                    <th rowspan="2" class="align-middle text-center">COUNT of QUANTITY (in PCS) <br/> MATERIAL NO</th>
                                    <th colspan="<?=count($workWeeks) ?>" class="text-center">WEEK NO</th>
                                    <th rowspan="2" class="align-middle text-center">GRAND TOTAL</th>
                                </tr>
                                <tr>
                                    <? foreach ($workWeeks as $wk) : ?>
                                        <th class="text-center">WK {{$wk}}</th>
                                    <? endforeach; ?>
                                </tr>
                            </thead>
                            <tbody class="list">
                                <? foreach ($data_list as $product_code =>  $week) : ?>
                                    <tr>
                                        <td>{{$product_code}}</td>
                                        <?
                                        $total = 0;
                                        foreach ($week as $wk) : ?>
                                            <td class="text-end">{{number_format($wk,2,'.',',')}}</td>
                                            <? $total += $wk; ?>
                                        <? endforeach; ?>
                                        <td class="text-end">{{ number_format($total,2,'.',',')}}</td>
                                    </tr>
                                <? endforeach; ?>
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
@endsection
