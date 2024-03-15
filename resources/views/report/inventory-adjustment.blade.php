@extends('layouts.master')
@section('title') Inventory @endsection
@section('css')

<link rel="stylesheet" href="{{ URL::asset('/assets/libs/@tarekraafat/@tarekraafat.min.css') }} ">

@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Inventory @endslot
@slot('title') Reserve @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card" id="tasksList">
            <div class="card-header border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Inventory Reserve Monitoring</h5>
                    <div class="flex-shrink-0">
                        <a href="#" class="submit-reserve-xls btn btn-secondary btn-label rounded-pill end-0"><i class="ri-file-excel-line label-icon align-middle rounded-pill fs-16 me-2"></i>Export to Excel</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive table-card mb-4">
                    <table class="table align-middle table-nowrap mb-0" id="tasksTable">
                        <thead class="table-light text-muted">
                            <tr>
                                <th class="fw-medium">MasterID</th>
                                <th class="fw-medium">Product ID</th>
                                <th class="fw-medium">Product Code</th>
                                <th class="fw-medium">Withdraw QTY</th>
                                <th class="fw-medium">Dispatch QTY</th>
                                <th class="fw-medium">Must be Reserve QTY</th>
                                <th class="fw-medium">Inventory QTY</th>
                                <th class="fw-medium">Reserve QTY</th>
                            </tr>
                        </thead>

                        <tbody class="list form-check-all">
                            <? if(count($data) > 0 ) : ?>
                                <? foreach($data as $res) :?>
                                    <tr class="accordion-toggle">
                                        <td class="align-middle">{{ $res['id'] }}</td>
                                        <td class="align-middle">{{ $res['product_id'] }}</td>
                                        <td class="align-middle">{{ $res['product_code'] }}</td>
                                        <td class="align-middle text-end">{{number_format($res['wd_qty'],2) }}</td>
                                        <td class="align-middle text-end">{{number_format($res['dispatch_qty'],2) }}</td>
                                        <td class="align-middle text-end">{{ number_format(($res['wd_qty'] - $res['dispatch_qty']) , 2) }}</td>
                                        <td class="align-middle text-end">{{ number_format($res['inv_qty'],2) }}</td>
                                        <td class="align-middle text-end">{{ $res['reserve_qty'] }}</td>
                                    </tr>
                                    <? foreach($res['details'] as $dtl) :?>
                                    <tr class="accordion-content bg-info" style="display: none;">
                                        <td colspan="2">{{ $dtl['wd_no'] }}</td>
                                        <td colspan="2">{{ $dtl['wd_qty'] }}</td>
                                        <td colspan="2">{{ $dtl['dispatch_no'] }}</td>
                                        <td colspan="2">{{ $dtl['dispatch_qty'] }}</td>
                                    </tr>
                                    <? endforeach; ?>
                                <? endforeach; ?>
                            <? else :?>
                                <div class="noresult" style="display: none">
                                    <div class="text-center">
                                        <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                            colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                        <h5 class="mt-2">Sorry! No Result Found</h5>
                                        <p class="text-muted mb-0">We've searched more than 200k+ tasks We did not find any tasks
                                            for you search.</p>
                                    </div>
                                </div>
                            <? endif; ?>
                        </tbody>
                    </table>
                    <!--end table-->
                </div>
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

<script src="{{ URL::asset('/assets/js/report/inventory.js') }}"></script>



@endsection
