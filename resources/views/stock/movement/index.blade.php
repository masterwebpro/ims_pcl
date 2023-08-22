@extends('layouts.master')
@section('title') Stock Movement @endsection
@section('css')

<link rel="stylesheet" href="{{ URL::asset('/assets/libs/@tarekraafat/@tarekraafat.min.css') }} ">

@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Stock @endslot
@slot('title') Stock Movement  @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card" id="tasksList">
            <div class="card-header border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">&nbsp;</h5>
                    <div class="flex-shrink-0">
                        <button data-status="open" class="create-movement btn btn-success btn-label rounded-pill"><i class="bx bx-server label-icon align-middle rounded-pill fs-16 me-2"></i> Create New</button>
                    </div>
                </div>
            </div>
            <!--end card-body-->
        </div>
        <!--end card-->

        <div class="card">
            <div class="card-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-border-top nav-border-top-primary mb-3" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" data-bs-toggle="tab" href="#nav-border-top-home" role="tab" aria-selected="false" tabindex="-1">
                            Active
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" href="#nav-border-top-profile" role="tab" aria-selected="false" tabindex="-1">
                            Posted
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" href="#nav-border-top-messages" role="tab" aria-selected="false" tabindex="-1">
                            All
                        </a>
                    </li>
                </ul>
                <div class="tab-content text-muted">
                    <div class="tab-pane active" id="nav-border-top-home" role="tabpanel">
                        <div class="d-flex">
                            <div class="flex-grow-1 ms-2">
                                <div class="table-responsive table-card mb-4">
                                    <table class="table align-middle table-nowrap mb-0" id="tasksTable">
                                        <thead class="table-light text-muted">
                                            <tr>
                                                <th class="sort" data-sort="id">Reference #</th>
                                                <th class="sort" data-sort="client_name">Client Name</th>
                                                <th class="sort" data-sort="site">Site</th>
                                                <th class="sort" data-sort="warehouse">Warehouse</th>
                                                <th class="sort" data-sort="status">Status</th>
                                                <th class="sort" data-sort="created_by">Created By</th>
                                                <th class="sort" data-sort="action">Action</th>
                                            </tr>
                                        </thead>
                    
                                        <tbody class="list form-check-all">
                                            <? if($active_list->total() > 0 ) : ?>
                                                <? foreach($active_list as $active) :?>
                                                    <tr>
                                                        <td class="rcv_no">{{ $active->ref_no}}</td>
                                                        <td class="po_num">{{ $active->client->client_name}}</td>
                                                        <td class="sales_invoice">{{ $active->store->store_name}}</td>
                                                        <td>{{ $active->warehouse->warehouse_name}}</td>
                                                        
                                                        <td class="status"><span class="badge {{ $active->status }} text-uppercase fs-11">{{ $active->status }}</span></td>
                                                        <td>{{ $active->user_create->name}}</td>
                                                        <td class="action">
                                                            <div class="hstack gap-3 fs-12">
                                                                <a href="{{ URL::to('stock/movement') }}/<?=_encode($active->id)?>" data-id="{{$active->id}}" class="link-info text-info d-inline-block"><i class="ri-eye-fill align-bottom me-1"></i> View</a>
                                                                <? if($active->status != 'posted') : ?>
                                                                    <a href="{{ URL::to('stock/movement') }}/<?=_encode($active->id);?>/edit" data-id="{{$active->id}} " class="link-info edit-po"><i class="ri-pencil-fill align-bottom me-1"></i> Edit </a> </div>
                                                                <? endif; ?>
                                                        </td>
                                                    </tr>
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
                                <!-- Pagination -->
                                {!! $active_list->withQueryString()->links('pagination::bootstrap-5') !!}
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="nav-border-top-profile" role="tabpanel">
                        <div class="d-flex">
                            <div class="flex-grow-1 ms-2">
                                <div class="table-responsive table-card mb-4">
                                    <table class="table align-middle table-nowrap mb-0" id="tasksTable">
                                        <thead class="table-light text-muted">
                                            <tr>
                                                <th class="sort" data-sort="id">Reference #</th>
                                                <th class="sort" data-sort="client_name">Client Name</th>
                                                <th class="sort" data-sort="site">Site</th>
                                                <th class="sort" data-sort="warehouse">Warehouse</th>
                                                <th class="sort" data-sort="status">Status</th>
                                                <th class="sort" data-sort="created_by">Created By</th>
                                                <th class="sort" data-sort="action">Action</th>
                                            </tr>
                                        </thead>
                    
                                        <tbody class="list form-check-all">
                                            <? if($posted_list->total() > 0 ) : ?>
                                                <? foreach($posted_list as $active) :?>
                                                    <tr>
                                                        <td class="rcv_no">{{ $active->ref_no}}</td>
                                                        <td class="po_num">{{ $active->client->client_name}}</td>
                                                        <td class="sales_invoice">{{ $active->store->store_name}}</td>
                                                        <td>{{ $active->warehouse->warehouse_name}}</td>
                                                        
                                                        <td class="status"><span class="badge {{ $active->status }} text-uppercase fs-11">{{ $active->status }}</span></td>
                                                        <td>{{ $active->user_create->name}}</td>
                                                        <td class="action">
                                                            <div class="hstack gap-3 fs-12">
                                                                <a href="{{ URL::to('stock/movement') }}/<?=_encode($active->id)?>" data-id="{{$active->id}}" class="link-info text-info d-inline-block"><i class="ri-eye-fill align-bottom me-1"></i> View</a>
                                                                <? if($active->status != 'posted') : ?>
                                                                    <a href="{{ URL::to('stock/movement') }}/<?=_encode($active->id);?>/edit" data-id="{{$active->id}} " class="link-info edit-po"><i class="ri-pencil-fill align-bottom me-1"></i> Edit </a> </div>
                                                                <? endif; ?>
                                                        </td>
                                                    </tr>
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
                                <!-- Pagination -->
                                {!! $posted_list->withQueryString()->links('pagination::bootstrap-5') !!}
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="nav-border-top-messages" role="tabpanel">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="ri-checkbox-circle-line text-success"></i>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                Etsy mixtape wayfarers, ethical wes anderson tofu before they sold out mcsweeney's organic lomo retro fanny pack lo-fi farm-to-table readymade. Messenger bag gentrify pitchfork tattooed craft beer, iphone skateboard locavore carles etsy salvia banksy hoodie helvetica. DIY synth PBR banksy irony.
                                <div class="mt-2">
                                    <a href="javascript:void(0);" class="btn btn-link">Read More <i class="ri-arrow-right-line ms-1 align-middle"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- end card-body -->
        </div>
    </div>
    <!--end col-->
</div>
<!--end row-->



<div class="modal fade" id="show-form" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header bg-light p-3">
                <h5 class="modal-title" id="exampleModalLabel">Select Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <div class="modal-body">
            <form class="row g-3 needs-validation" novalidate>
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <label for="position" class="form-label">Client <span class="text-danger">*</span> </label>
                            <select class="form-select select2" required="required" id="client" name="client">
                                <option value="">Select Client</option>                                                            
                                <? foreach($client_list as $client) : ?>
                                    <option value="<?=$client->id?>" ><?=$client->client_name?></option>
                                <? endforeach;?>
                            </select>
                            <span class="text-danger error-msg client_error"></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12  mb-3">
                            <label for="customer_id" class="form-label">Site Name <span class="text-danger">*</span> </label>
                            <select class="form-select select2" required="required" id="store" name="store">
                                <option value="">Select Store/Warehouse</option>                                                            
                            </select>
                            <span class="text-danger error-msg store_error"></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <label for="customer_id" class="form-label">Warehouse Name <span class="text-danger">*</span> </label>
                            <select class="form-select select2" required="required" id="warehouse" name="warehouse">
                                <option value="">Select warehouse</option>                                                            
                            </select>
                            <span class="text-danger error-msg warehouse_error"></span>
                        </div>
                    </div>
                </div>
            </form>
            </div>
            <div class="modal-footer">
                <div class="hstack gap-2 justify-content-end">
                    <button data-status="open" id="movement-next-btn" class="btn btn-info btn-label rounded-pill"><i class="bx bx-skip-next-circle label-icon align-middle rounded-pill fs-16 me-2"></i> Next</button>
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
 
<script src="{{ URL::asset('/assets/js/stock/movement.js') }}"></script>



@endsection