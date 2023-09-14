@extends('layouts.master')
@section('title')
    POD
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
        POD
        @endslot
    @endcomponent

    <div class="row justify-content-center">
        <div class="col-xxl-11">
            <div class="card" id="tasksList">
                <div class="card-header border-0">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1"><?=$pod->batch_no?></h5>
                        <div class="col-md-2 text-center">
                            <span class="badge  fs-16 bg-warning text-uppercase"><?=$pod->status?></span>
                        </div>
                        <div class="col-md-6 text-end">
                            @if ($pod->status != 'Delivered')
                            <a href="{{ URL::to('pod/'._encode($pod->id).'/edit') }}" class="btn btn-success btn-label rounded-pill"><i
                                        class="ri-pencil-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                                    Edit</a>
                            @endif
                            <a href="{{ URL::to('pod') }}" class="btn btn-primary btn-label rounded-pill"><i
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
    <form name="submit-pod" id="submit-pod">
        <div class="row justify-content-center">
            <div class="col-xxl-11">
                <div class="card" id="demo">
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <div class="row ms-3 mt-3 mx-3">
                                <div class="col-lg-6 col-md-6">
                                    <div class="row">
                                        <label for="colFormLabel" class="col-lg-4  col-form-label">Dispatch Date <span
                                                class="text-danger">*</span></label>

                                        <div class="col-lg-8">
                                            <input type="date" class="form-control" id="dispatch_date"
                                                name="dispatch_date" placeholder="Dispactch Date" value="{{ $pod->dispatch_date }}" disabled>
                                            <span class="text-danger error-msg dispatch_date_error"></span>
                                            <input type="hidden" name="dispatch_no"  value="{{$pod->batch_no}}" />
                                            <input type="hidden" name="dispatch_id" id="dispatch_id" value="{{ _encode($pod->id) }}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="row">
                                        <label for="colFormLabel" class="col-lg-4 col-form-label">Dispatch By<span
                                            class="text-danger">*</span></label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" id="dispatch_by" name="dispatch_by" disabled
                                                value="<?=$pod->dispatch_by?>" placeholder="Dispatch By">
                                            <span class="text-danger error-msg dispatch_by_error"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row ms-3 mt-3 mx-3">
                                <div class="col-lg-6 col-md-6">
                                    <div class="row">
                                        <label for="colFormLabel" class="col-lg-4  col-form-label">Received Date<span
                                                class="text-danger">*</span></label>

                                        <div class="col-lg-8">
                                            <div class="input-group">
                                                <input type="date" class="form-control" id="receive_date"
                                                    name="receive_date" placeholder="Receive Date" value="{{ date('Y-m-d',strtotime($pod->start_datetime)) }}" disabled>
                                                <span class="text-danger error-msg receive_date_error"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="row">
                                        <label for="colFormLabel" class="col-lg-4 col-form-label">Received By</label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" id="receive_by" name="receive_by" disabled
                                                value="<?=$pod->receive_by?>" placeholder="Received By">
                                            <span class="text-danger error-msg receive_by_error"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row ms-3 mt-3 mx-3">
                                <div class="col-lg-6 col-md-6">
                                    <div class="row">
                                        <label for="colFormLabel" class="col-lg-4  col-form-label">Arrived Date<span
                                                class="text-danger">*</span></label>

                                        <div class="col-lg-8">
                                            <div class="input-group">
                                                <input type="date" class="form-control" id="arrived_date"
                                                    name="arrived_date" placeholder="Finish Date" value="{{ date('Y-m-d',strtotime($pod->arrived_date)) }}" disabled>
                                                <span class="text-danger error-msg arrived_date_error"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="row">
                                        <label for="colFormLabel" class="col-lg-4  col-form-label">Departed Date<span
                                                class="text-danger">*</span></label>

                                        <div class="col-lg-8">
                                            <div class="input-group">
                                                <input type="date" class="form-control" id="depart_date"
                                                    name="depart_date" placeholder="Depart Date" value="{{ date('Y-m-d',strtotime($pod->depart_date)) }}" disabled>
                                                <span class="text-danger error-msg depart_date_error"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row ms-3 mt-3 mx-3">
                                <div class="col-lg-6 col-md-6">
                                    <div class="row">
                                        <label for="colFormLabel" class="col-lg-4  col-form-label">Remarks</label>
                                        <div class="col-lg-8">
                                            <textarea name="remarks" id="remarks" class="form-control" disabled>{{ $pod->remarks }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class=" col-lg-12 mt-5">
                            <div class="">
                                <div class="card-header card-title mb-0 flex-grow-1">
                                    <div class="d-flex align-items-center">
                                        <h5>Withdrawal Details</h5>
                                    </div>
                                </div>
                                <div class="card-body p-4">
                                    <div class="table-responsive">
                                        <table class="table table-nowrap" id="product-list">
                                            <thead>
                                                <tr class="table-active">
                                                    <th scope="col" style="width: 10px;">#</th>
                                                    <th scope="col">Product</th>
                                                    <th scope="col">Requested Quantity</th>
                                                    <th scope="col">Unit</th>
                                                </tr>
                                            </thead>
                                            <tbody id="newlink">
                                                <?
                                                $rowCount = count($pod->items);
                                                $x=1;
                                                 ?>
                                                @if(isset($pod->items))
                                                    @foreach($pod->items as $item)
                                                    <tr id="product_{{$item->product_id}}">
                                                        <td class="text-start">
                                                            <input type="hidden" name="product_id[]" readonly id="product_id_{{$item->product_id}}" value="{{$item->product_id}}" />
                                                        {{$x++}} </td>
                                                        <td class="text-start fs-14">
                                                            {{$item->product->product_name}}<br/><small>{{$item->product->product_code}}</small>
                                                        </td>
                                                        <td class="ps-1 text-center">
                                                            {{ number_format($item->inv_qty,2) }}
                                                            <input type="hidden"  class="form-control inv_qty numeric" name="inv_qty[]" data-qty="{{ $item->master->inv_qty }}" data-id="{{$x}}" id="inv_qty_{{$x}}" value="{{$item->inv_qty}}" placeholder="Inv Qty" />
                                                        </td>
                                                        <td class=" ps-1">
                                                            {{ $item->master->uom->code }}
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                @else
                                                <tr class="">
                                                    <td colspan="8" class="text-danger text-center">No Record Found!</td>
                                                </tr>
                                                @endif

                                            </tbody>
                                        </table>

                                        <!--end table-->
                                    </div>
                                </div>
                                <!--end card-body-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
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
    <script src="{{ URL::asset('/assets/js/pod/pod.js') }}"></script>
@endsection
