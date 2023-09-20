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
    <!-- Filepond css -->
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/dropzone/dropzone.min.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/filepond/filepond.min.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css') }}">
    <link href="{{ URL::asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />

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
                            <button data-status="open" class="submit-pod btn btn-success btn-label rounded-pill"><i
                                class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                            Save</button>
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
                                <div class="col-lg-3 col-md-3">
                                    <label for="colFormLabel" class="form-label">Dispatch No</label>
                                    <input type="text" class="form-control" id="dispatch_by" name="dispatch_by" disabled
                                        value="<?=$pod->dispatch_no?>" placeholder="Dispatch No">
                                </div>
                                <div class="col-lg-3 col-md-3">
                                    <label for="colFormLabel" class="form-label">Dispatcher Name</label>
                                    <input type="text" class="form-control" id="dispatch_by" name="dispatch_by" disabled
                                        value="<?=$pod->dispatch_by?>" placeholder="Dispatch By">
                                    <span class="text-danger error-msg dispatch_by_error"></span>
                                </div>
                                <div class="col-lg-3 col-md-3 form-group">
                                        <label for="colFormLabel" class="form-label">Dispatch Date</label>
                                        <input type="date" class="form-control" id="dispatch_date" disabled
                                            name="dispatch_date" placeholder="Dispactch Date" value="{{ date('Y-m-d',strtotime($pod->dispatch_date)) }}" >
                                        <span class="text-danger error-msg dispatch_date_error"></span>
                                        <input type="hidden" name="batch_no"  value="{{$pod->batch_no}}" />
                                        <input type="hidden" name="id" id="id" value="{{ _encode($pod->id) }}" />
                                </div>
                                <div class="col-lg-3 col-md-3 form-group">
                                    <label for="colFormLabel" class="form-label">Driver</label>
                                            <input type="text" class="form-control" id="driver" name="driver" disabled
                                            value="{{ $pod->driver }}" placeholder="Driver Name">
                                </div>
                            </div>
                            <div class="row ms-3 mt-3 mx-3">
                                <div class="col-lg-3 col-md-3 form-group">
                                    <label for="colFormLabel" class="form-label">Plate No</label>
                                            <input type="text" class="form-control" id="trucker_name" name="plate_no" disabled
                                            value="{{ $pod->plate_no }}" placeholder="Trucker Name">
                                </div>
                                <div class="col-lg-3 col-md-3 form-group">
                                    <label for="colFormLabel" class="form-label">Trucker Name</label>
                                    <input type="text" class="form-control" id="trucker_name" name="trucker_name" disabled
                                        value="{{ $pod->trucker_name }}" placeholder="Trucker Name">
                                </div>
                                <div class="col-lg-3 col-md-3 form-group">
                                    <label for="colFormLabel" class="form-label">Truck Type</label>
                                            <input type="text" class="form-control" id="truck_type" name="truck_type" disabled
                                            value="{{ $pod->truck_type }}" placeholder="Truck Type">
                                </div>
                                <div class="col-lg-3 col-md-3 form-group">
                                    <label for="colFormLabel" class="form-label">Seal No.</label>
                                    <input type="text" class="form-control" id="seal_no" name="seal_no" disabled
                                        value="{{ $pod->seal_no }}" placeholder="Seal No.">
                                </div>
                            </div>
                            <div class="row ms-3 mt-3 mx-3">
                                <div class="col-lg-3 col-md-3 form-group">
                                    <label for="colFormLabel" class="form-label">Received Datetime<span
                                            class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="date" class="form-control" id="receive_date"
                                            name="receive_date" placeholder="Start Date" value="{{ date('Y-m-d',strtotime($pod->receive_date)) }}">
                                            <input type="time" class="form-control" id="receive_time"
                                            name="receive_time" placeholder="Dispatch Date" value="{{ date('H:i:s',strtotime($pod->receive_date)) }}">
                                        </div>
                                        <span class="text-danger error-msg receive_time_error"></span>
                                </div>
                                <div class="col-lg-3 col-md-3 form-group">
                                    <label for="colFormLabel" class="form-label">Received By</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="receive_by" name="receive_by"
                                            value="<?=$pod->receive_by?>" placeholder="Received By">
                                    </div>
                                    <span class="text-danger error-msg receive_by_error"></span>
                                </div>
                                <div class="col-lg-3 col-md-3 form-group">
                                    <div class="row">
                                        <label for="colFormLabel" class="form-label">Arrived Datetime<span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                                <input type="date" class="form-control" id="arrived_date"
                                                name="arrived_date" placeholder="Start Date" value="{{ date('Y-m-d',strtotime($pod->arrived_date)) }}">
                                                <input type="time" class="form-control" id="arrived_time"
                                                name="arrived_time" placeholder="Arrived Date" value="{{ date('H:i:s',strtotime($pod->arrived_date)) }}">
                                        </div>
                                        <span class="text-danger error-msg arrived_time_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3">
                                    <div class="row">
                                        <label for="colFormLabel" class="form-label">Departed Date<span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="date" class="form-control" id="depart_date"
                                                name="depart_date" placeholder="Depart Date" value="{{ date('Y-m-d',strtotime($pod->depart_date)) }}" >
                                            <input type="time" class="form-control" id="depart_time"
                                                name="depart_time" placeholder="Depart Date" value="{{ date('H:i:s',strtotime($pod->depart_date)) }}">
                                        </div>
                                        <span class="text-danger error-msg depart_time_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row ms-3 mt-3 mx-3">
                                <div class="col-lg-3 col-md-3 form-group">
                                    <div class="row">
                                        <label for="colFormLabel" class="form-label">Remarks</label>
                                        <div class="input-group">
                                            <textarea name="remarks" id="remarks" class="form-control" >{{ $pod->remarks }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3 form-group">
                                    <div class="row">
                                        <label for="colFormLabel" class="form-label">Status<span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <select class="form-select" name="status">
                                                <? foreach ($status as $stat):?>
                                                    <option value="<?=$stat?>" <?=($stat == $pod->status) ? 'selected' : ''?>>{{ $stat }}</option>
                                                <? endforeach;?>
                                            </select>
                                        </div>
                                        <span class="text-danger error-msg depart_date_error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 form-group">
                                    <div class="row">
                                        <label for="colFormLabel" class="form-label">Attachment</label>
                                        <div class="input-group">
                                            <button type="button" class="btn btn-info add-attachment">Attach File</button>
                                        </div>
                                    </div>
                                </div> <!-- end col -->

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
                                                    <th scope="col" class="text-center">Dispatch Quantity</th>
                                                    <th scope="col">Received Quantity</th>
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
                                                        </td>
                                                        <td class="ps-1 text-center w-25">
                                                            <input type="hidden" name="wddtl_id[]" readonly id="wddtl_id_{{$item->id}}" value="{{$item->id}}" />
                                                            <input type="text" class="form-control numeric" name="received_qty[]" value="{{ number_format($item->actual_rcv_qty,2) }}">
                                                            <span class="text-danger error-msg received_qty{{ $x - 2 }}_error"></span>
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
        <!-- show add serial Modal -->
    <div class="modal" id="show-attachment" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-light p-3">
                <h5 class="modal-title" id="exampleModalLabel">Attachment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    id="close-modal"></button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-body">
                        <div class="dropzone">
                            <div class="fallback">
                                <input name="file" type="file" multiple="multiple">
                            </div>
                            <div class="dz-message needsclick">
                                <div class="mb-1">
                                    <i class="display-4 text-muted ri-upload-cloud-2-fill"></i>
                                </div>
                                <h5>Drop files here or click to upload.</h5>
                            </div>
                        </div>

                        <ul class="list-unstyled mb-0" id="dropzone-preview">
                            <li class="mt-2" id="dropzone-preview-list">
                                <div class="border rounded">
                                    <div class="d-flex p-2">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar-sm bg-light rounded">
                                                <img data-dz-thumbnail class="img-fluid rounded d-block" src="assets/images/new-document.png" alt="Dropzone-Image" />
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="pt-1">
                                                <h5 class="fs-14 mb-1" data-dz-name>&nbsp;</h5>
                                                <p class="fs-13 text-muted mb-0" data-dz-size></p>
                                                {{-- <strong class="error text-danger" data-dz-errormessage></strong> --}}
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 ms-3">
                                            <button data-dz-remove class="btn btn-sm btn-danger">Delete</button>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <!-- end dropzon-preview -->
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close" >Cancel</button>
                        <button type="button" class="btn btn-success">Save</button>
                    </div>
                    <!-- end card body -->
                </div>
                <!-- end card -->
            </div>
        </div>
    </div>
</div>
    </form>
@endsection
@section('script')
    <!-- JAVASCRIPT -->
    <script src="{{ URL::asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/select2/select2.min.js') }}"></script>

    <script src="{{ URL::asset('assets/js/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/datatables/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/datatables/dataTables.responsive.min.js') }}"></script>

    <script src="{{  URL::asset('assets/libs/dropzone/dropzone.min.js') }}"></script>

    <!-- filepond js -->
    <script src="{{  URL::asset('assets/libs/filepond/filepond.min.js') }}"></script>
    <script src="{{  URL::asset('assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js') }}"></script>
    <script src="{{  URL::asset('assets/libs/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js') }}"></script>
    <script src="{{  URL::asset('assets/libs/filepond-plugin-image-exif-orientation/filepond-plugin-image-exif-orientation.min.js') }}"></script>
    <script src="{{  URL::asset('assets/libs/filepond-plugin-file-encode/filepond-plugin-file-encode.min.js') }}"></script>

    <script src="{{ URL::asset('assets/js/pages/form-file-upload.init.js') }}"></script>

    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/pod/pod.js') }}"></script>
@endsection
