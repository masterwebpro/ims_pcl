@extends('layouts.master')
@section('title') Purchase Order @endsection
@section('css')

@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Inbound @endslot
@slot('title') Purchase Order @endslot
@endcomponent

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card" id="tasksList">
            <div class="card-header border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">PO Number : {{ $po->po_num}}</h5>
                    <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">
                        <? if( $po->status != 'posted') : ?>
                            <a href="{{ URL::to('po') }}/<?=_encode($po->id)?>/edit" class="btn btn-success btn-label rounded-pill"><i class="ri-edit-line label-icon align-middle rounded-pill fs-16 me-2"></i> Edit</a>
                        <? else : ?>
                            <a href="javascript:window.print()" class="btn btn-warning btn-label d-print-none rounded-pill"><i class="ri-printer-line label-icon align-middle rounded-pill fs-16 me-2"></i> Print</a>
                        <? endif;?>
                        <a href="{{ URL::to('po') }}" class="btn btn-primary btn-label rounded-pill d-print-none"><i class="ri-arrow-go-back-line label-icon align-middle rounded-pill fs-16 me-2"></i> Back</a>
                    </div>
                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>
<!--end row-->


<div class="row justify-content-center">
    <div class="col-xxl-9">
        <div class="card" id="demo">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-header border-bottom-dashed p-4">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <div class="mt-sm-0 mt-0">
                                    <h6 class="text-muted text-uppercase fw-semibold">Supplier Name</h6>
                                    <p class="text-muted mb-1" id="address-details">{{ $po->supplier->supplier_name}}</p>
                                    <p class="text-muted mb-0" id="zip-code">{{ $po->supplier->supplier_address }}</p>
                                </div>
                             </div>
                        </div>
                    </div>
                    <!--end card-header-->
                </div>
                <!--end col-->
                <div class="col-lg-12">
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-lg-3 col-6">
                                <p class="text-muted mb-2 text-uppercase fw-semibold">PO Number</p>
                                <h5 class="fs-14 mb-0"><span id="po-num">{{ $po->po_num }}</span></h5>
                            </div>
                            <!--end col-->
                            <div class="col-lg-3 col-6">
                                <p class="text-muted mb-2 text-uppercase fw-semibold">PO Date</p>
                                <h5 class="fs-14 mb-0"><span id="po-date">{{ $po->po_date }}</span></h5>
                            </div>
                            <!--end col-->
                            <div class="col-lg-3 col-6">
                                <p class="text-muted mb-2 text-uppercase fw-semibold">PO Status</p>
                                <span class="badge fs-13 text-uppercase {{ $po->status }}" id="po-status">{{ $po->status }}</span>
                            </div>
                            <!--end col-->
                            <div class="col-lg-3 col-6">
                                <p class="text-muted mb-2 text-uppercase fw-semibold">Total Amount</p>
                                <h5 class="fs-14 mb-0"><span id="total-amount">{{ number_format($po->total_net, 2)}}</span></h5>
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </div>
                    <!--end card-body-->
                </div>
                <!--end col-->
                <div class="col-lg-12">
                    <div class="card-body p-4 border-top border-top-dashed">
                        <div class="row g-3">
                            <div class="col-6">
                                <h6 class="text-muted text-uppercase fw-semibold mb-3">Client Name</h6>
                                <p class="fw-medium mb-2" id="billing-name">{{ $po->client->client_name}}</p>
                                <p class="text-muted mb-1" id="billing-address-line-1">{{ $po->client->address_1}} </p>
                                <p class="text-muted mb-1">{{ $po->client->address_2 }} </p>
                                <p class="text-muted mb-0">{{ $po->client->city }}, {{ $po->client->province}}  </p>
                            </div>
                            <!--end col-->
                            <div class="col-6">
                                <h6 class="text-muted text-uppercase fw-semibold mb-3">Site Address</h6>
                                <p class="fw-medium mb-2" id="shipping-name">{{ $po->store->store_name}}</p>
                                <p class="text-muted mb-1" id="shipping-address-line-1">{{ $po->store->address_1}}</p>
                                <p class="text-muted mb-1">{{ $po->store->address_2}}</p>
                                <p class="text-muted mb-0">{{ $po->store->city }}, {{ $po->store->province}} </p>
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </div>
                    <!--end card-body-->
                </div>

                <!--end col-->
                <div class="col-lg-12">
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-borderless text-center table-nowrap align-middle mb-0">
                                <thead>
                                    <tr class="table-active">
                                        <th scope="col" style="width: 50px;">#</th>
                                        <th scope="col">Product Code</th>
                                        <th scope="col" class="text-start">Particulars</th>
                                        <th scope="col">UOM</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col" class="text-end">Unit Price</th>
                                        <th scope="col">Discount</th>
                                        <th scope="col" class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="products-list">
                                    <? 
                                    $x=1;
                                    $total_amount = 0;
                                    $total_discount = 0;
                                    foreach($po->items as $item) : 

                                        $total_amount += $item->total_amount;
                                        $total_discount += $item->discount;
                                    ?>
                                    <tr>
                                        <th scope="row"><?=$x++;?></th>
                                        <th scope="row">{{ $item->product->product_code}}</th>
                                        <td class="text-start">
                                            <span class="fw-medium">{{ $item->product->product_name}}</span>
                                            <p class="text-muted mb-0"></p>
                                        </td>
                                        <td>{{ $item->uom->uom_desc}}</td>
                                        <td>{{ $item->requested_qty}}</td>
                                        <td class="text-end">{{ number_format($item->unit_amount,2)}}</td>
                                        <td>{{ number_format($item->discount,2)}}</td>
                                        <td class="text-end">{{ $item->currency}} {{ number_format($item->total_amount,2)}}</td>
                                    </tr>
                                    <? endforeach;?>
                                </tbody>
                            </table>
                            <!--end table-->
                        </div>
                        <div class="border-top border-top-dashed mt-2">
                            <table class="table table-borderless table-nowrap align-middle mb-0 ms-auto" style="width:250px">
                                <tbody>
                                    <tr>
                                        <td>Sub Total</td>
                                        <td class="text-end">{{ number_format($total_amount,2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Discount</td>
                                        <td class="text-end">- {{ number_format($total_discount,2) }}</td>
                                    </tr>
                                    <tr class="border-top border-top-dashed fs-15">
                                        <th scope="row">Total Amount</th>
                                        <th class="text-end">{{ number_format(($total_amount - $total_discount) ,2) }}</th>
                                    </tr>
                                </tbody>
                            </table>
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

	@endsection
@section('script')

<script src="{{ URL::asset('assets/js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>

@endsection