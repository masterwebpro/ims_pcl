@extends('layouts.master')
@section('title') Receive Order @endsection
@section('css')

@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Inbound @endslot
@slot('title') Receive Order > {{ $rcv->rcv_no }} @endslot
@endcomponent

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card" id="tasksList">
            <div class="card-header border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Receiving Number : {{ $rcv->rcv_no}}</h5>
                    <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">
                        <? if( $rcv->status != 'posted') : ?>
                        <a href="{{ URL::to('receive') }}/<?=_encode($rcv->id)?>/edit" class="btn btn-success btn-label rounded-pill"><i class="ri-edit-line label-icon align-middle rounded-pill fs-16 me-2"></i> Edit</a>
                        <? endif;?>
                        <a href="{{ URL::to('receive') }}" class="btn btn-primary btn-label rounded-pill"><i class="ri-arrow-go-back-line label-icon align-middle rounded-pill fs-16 me-2"></i> Back</a>
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
            <div class="row ">
                <div class="col-lg-12">
                    <div class="card-body p-4 ">
                        <div class="row g-3">
                            <div class="col-4">
                                <h6 class="text-muted text-uppercase fw-semibold mb-3">Client Name</h6>
                                <p class="fw-medium mb-2" id="billing-name">{{ $rcv->client->client_name}}</p>
                                <p class="text-muted mb-1" id="billing-address-line-1">{{ $rcv->client->address_1}} </p>
                                <p class="text-muted mb-1">{{ $rcv->client->address_2 }} </p>
                                <p class="text-muted mb-0">{{ $rcv->client->city }}, {{ $rcv->client->province}}  </p>
                            </div>
                            <!--end col-->
                            <div class="col-4">
                                <h6 class="text-muted text-uppercase fw-semibold mb-3">Warehouse / Store Address</h6>
                                <p class="fw-medium mb-2" id="shipping-name">{{ $rcv->store->store_name}}</p>
                                <p class="text-muted mb-1" id="shipping-address-line-1">{{ $rcv->store->address_1}}</p>
                                <p class="text-muted mb-1">{{ $rcv->store->address_2}}</p>
                                <p class="text-muted mb-0">{{ $rcv->store->city }}, {{ $rcv->store->province}} </p>
                            </div>

                            <div class="col-4 ">
                                <h6 class="text-muted text-uppercase fw-semibold">Supplier Name</h6>
                                <p class="text-muted mb-1" id="address-details">{{ $rcv->supplier->supplier_name}}</p>
                                <p class="text-muted mb-0" id="zip-code">{{ $rcv->supplier->supplier_address }}</p>
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </div>
                    <!--end card-body-->
                </div>
                <!--end col-->
                <div class="col-lg-12 ">
                    <div class=" border border-bottom-dashed"></div>
                </div>

                <div class="col-lg-12">
                    <div class="row ms-3 mt-3 mx-3">
                        <div class="col-lg-6">
                            <div class="row">
                                <label for="colFormLabel" class="col-lg-4 col-form-label">PO Number</label> 
                                <div class="col-lg-8">
                                    <input type="text" class="form-control disabled" value="{{ $rcv->po_num }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label for="colFormLabel" class="col-lg-4  col-form-label">Sales Invoice</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control disabled" value="{{ $rcv->sales_invoice }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="row ms-3 mt-3 mx-3">
                        <div class="col-lg-6">
                            <div class="row">
                                <label for="colFormLabel" class="col-lg-4 col-form-label">Received By</label> 
                                <div class="col-lg-8">
                                    <input type="text" class="form-control disabled" value="{{ $rcv->user_create->name }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label for="colFormLabel" class="col-lg-4  col-form-label">Date Received</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control disabled" value="{{ $rcv->date_received }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12 mt-3">
                    <div class="row ms-3 mx-3">
                        <div class="col-lg-6">
                            <div class="row">
                                <label for="colFormLabel" class="col-lg-4 col-form-label">Inspect By</label> 
                                <div class="col-lg-8">
                                    <input type="text" class="form-control disabled" value="{{ $rcv->inspect_by }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label for="colFormLabel" class="col-lg-4  col-form-label">Inspect Date</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control disabled" value="{{ $rcv->inspect_date }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12 mt-3">
                    <div class="row ms-3 mx-3">
                        <div class="col-lg-6">
                            <div class="row">
                                <label for="colFormLabel" class="col-lg-4 col-form-label">Date Arrived</label> 
                                <div class="col-lg-8">
                                    <input type="text" class="form-control disabled" value="{{ $rcv->date_arrived }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label for="colFormLabel" class="col-lg-4  col-form-label">Time Arrived</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control disabled" value="{{ $rcv->date_arrived }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12 mt-3">
                    <div class="row ms-3 mx-3">
                        <div class="col-lg-6">
                            <div class="row">
                                <label for="colFormLabel" class="col-lg-4 col-form-label">Date Departed</label> 
                                <div class="col-lg-8">
                                    <input type="text" class="form-control disabled" value="{{ $rcv->date_departed }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label for="colFormLabel" class="col-lg-4  col-form-label">Time Departed</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control disabled" value="{{ $rcv->date_departed }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12 mt-3">
                    <div class="row ms-3 mx-3">
                        <div class="col-lg-6">
                            <div class="row">
                                <label for="colFormLabel" class="col-lg-4 col-form-label">Plate Number</label> 
                                <div class="col-lg-8">
                                    <input type="text" class="form-control disabled" value="{{ $rcv->plate_no }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label for="colFormLabel" class="col-lg-4  col-form-label">Truck Type</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control disabled" value="{{ $rcv->truck->vehicle_code }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12 mt-3">
                    <div class="row ms-3 mx-3">
                        <div class="col-lg-6">
                            <div class="row">
                                <label for="colFormLabel" class="col-lg-4 col-form-label">Warehouse</label> 
                                <div class="col-lg-8">
                                    <input type="text" class="form-control disabled"  value="{{ $rcv->warehouse->warehouse_name }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label for="colFormLabel" class="col-lg-4  col-form-label">Remarks</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control disabled" value="{{ $rcv->remarks }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--end col-->
                <div class=" col-lg-12 mt-5">
                    <div class="">
                        <div class="card-header card-title mb-0 flex-grow-1">
                            <div class="d-flex align-items-center">
                                <h5>Receiving Details</h5>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-borderless text-center table-nowrap align-middle mb-0">
                                    <thead>
                                        <tr class="table-active">
                                            <th scope="col" style="width: 50px;">#</th>
                                            <th scope="col">Code</th>
                                            <th scope="col">SKU</th>
                                            <th scope="col" class="text-start">Description</th>
                                            <th scope="col">UOM</th>
                                            <th scope="col">Rqstd Qty</th>
                                            <th scope="col">Actual Qty</th>
                                            <th scope="col" class="text-end">Unit Price</th>
                                            <th scope="col" class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody id="products-list">
                                    <? 
                                        $x=1;
                                        $total_amount = 0;
                                        $total_discount = 0;
                                        foreach($rcv->items as $item) : 

                                            $total_amount += $item->actual_net_price;
                                            $total_discount += $item->discount;
                                        ?>
                                        <tr>
                                            <th scope="row"><?=$x++;?></th>
                                            <td>{{ $item->product->product_code}}</td>
                                            <td>{{ $item->product->product_sku}}</td>
                                            <td class="text-start">
                                                <span class="fw-medium">{{ $item->product->product_name}}</span>
                                                <p class="text-muted mb-0"></p>
                                            </td>
                                            <td>{{ $item->uom->code}}</td>
                                            <td>{{ $item->requested_qty}}</td>
                                            <td>{{ $item->actual_qty}}</td>
                                            <td class="text-end">{{ number_format($item->amount_per_unit,2)}}</td>
                                            <td class="text-end">{{ number_format($item->actual_net_price,2)}}</td>
                                        </tr>
                                        <? endforeach;?>
                                    </tbody>
                                </table>
                                <!--end table-->
                            </div>
                            <div class="border-top border-top-dashed mt-2">
                                <table class="table table-borderless table-nowrap align-middle mb-0 ms-auto" style="width:250px">
                                    <tbody>
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