<?php $__env->startSection('title'); ?> Receive Order <?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<?php $__env->startComponent('components.breadcrumb'); ?>
<?php $__env->slot('li_1'); ?> Inbound <?php $__env->endSlot(); ?>
<?php $__env->slot('title'); ?> Receive Order > <?php echo e($rcv->rcv_no); ?> <?php $__env->endSlot(); ?>
<?php echo $__env->renderComponent(); ?>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card" id="tasksList">
            <div class="card-header border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Receiving Number : <?php echo e($rcv->rcv_no); ?></h5>
                    <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">
                        <? if( $rcv->status != 'posted') : ?>
                        <a href="<?php echo e(URL::to('receive')); ?>/<?=_encode($rcv->id)?>/edit" class="btn btn-success btn-label rounded-pill"><i class="ri-edit-line label-icon align-middle rounded-pill fs-16 me-2"></i> Edit</a>
                        <? endif;?>
                        <a href="<?php echo e(URL::to('receive')); ?>" class="btn btn-primary btn-label rounded-pill"><i class="ri-arrow-go-back-line label-icon align-middle rounded-pill fs-16 me-2"></i> Back</a>
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
                                <p class="fw-medium mb-2" id="billing-name"><?php echo e($rcv->client->client_name); ?></p>
                                <p class="text-muted mb-1" id="billing-address-line-1"><?php echo e($rcv->client->address_1); ?> </p>
                                <p class="text-muted mb-1"><?php echo e($rcv->client->address_2); ?> </p>
                                <p class="text-muted mb-0"><?php echo e($rcv->client->city); ?>, <?php echo e($rcv->client->province); ?>  </p>
                            </div>
                            <!--end col-->
                            <div class="col-4">
                                <h6 class="text-muted text-uppercase fw-semibold mb-3">Warehouse / Store Address</h6>
                                <p class="fw-medium mb-2" id="shipping-name"><?php echo e($rcv->store->store_name); ?></p>
                                <p class="text-muted mb-1" id="shipping-address-line-1"><?php echo e($rcv->store->address_1); ?></p>
                                <p class="text-muted mb-1"><?php echo e($rcv->store->address_2); ?></p>
                                <p class="text-muted mb-0"><?php echo e($rcv->store->city); ?>, <?php echo e($rcv->store->province); ?> </p>
                            </div>

                            <div class="col-4 ">
                                <h6 class="text-muted text-uppercase fw-semibold">Supplier Name</h6>
                                <p class="text-muted mb-1" id="address-details"><?php echo e($rcv->supplier->supplier_name); ?></p>
                                <p class="text-muted mb-0" id="zip-code"><?php echo e($rcv->supplier->supplier_address); ?></p>
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
                                    <input type="text" class="form-control disabled" value="<?php echo e($rcv->po_num); ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label for="colFormLabel" class="col-lg-4  col-form-label">Sales Invoice</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control disabled" value="<?php echo e($rcv->sales_invoice); ?>" readonly>
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
                                    <input type="text" class="form-control disabled" value="<?php echo e($rcv->user_create->name); ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label for="colFormLabel" class="col-lg-4  col-form-label">Date Received</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control disabled" value="<?php echo e($rcv->date_received); ?>" readonly>
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
                                    <input type="text" class="form-control disabled" value="<?php echo e($rcv->inspect_by); ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label for="colFormLabel" class="col-lg-4  col-form-label">Inspect Date</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control disabled" value="<?php echo e($rcv->inspect_date); ?>" readonly>
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
                                    <input type="text" class="form-control disabled" value="<?php echo e($rcv->date_arrived); ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label for="colFormLabel" class="col-lg-4  col-form-label">Time Arrived</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control disabled" value="<?php echo e($rcv->date_arrived); ?>" readonly>
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
                                    <input type="text" class="form-control disabled" value="<?php echo e($rcv->date_departed); ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label for="colFormLabel" class="col-lg-4  col-form-label">Time Departed</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control disabled" value="<?php echo e($rcv->date_departed); ?>" readonly>
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
                                    <input type="text" class="form-control disabled" value="<?php echo e($rcv->plate_no); ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label for="colFormLabel" class="col-lg-4  col-form-label">Truck Type</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control disabled" value="<?php echo e($rcv->truck->vehicle_code); ?>" readonly>
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
                                    <input type="text" class="form-control disabled"  value="<?php echo e($rcv->warehouse->warehouse_name); ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label for="colFormLabel" class="col-lg-4  col-form-label">Remarks</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control disabled" value="<?php echo e($rcv->remarks); ?>" readonly>
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
                                            <td><?php echo e($item->product->product_code); ?></td>
                                            <td><?php echo e($item->product->product_sku); ?></td>
                                            <td class="text-start">
                                                <span class="fw-medium"><?php echo e($item->product->product_name); ?></span>
                                                <p class="text-muted mb-0"></p>
                                            </td>
                                            <td><?php echo e($item->uom->code); ?></td>
                                            <td><?php echo e($item->requested_qty); ?></td>
                                            <td><?php echo e($item->actual_qty); ?></td>
                                            <td class="text-end"><?php echo e(number_format($item->amount_per_unit,2)); ?></td>
                                            <td class="text-end"><?php echo e(number_format($item->actual_net_price,2)); ?></td>
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
                                            <th class="text-end"><?php echo e(number_format(($total_amount - $total_discount) ,2)); ?></th>
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

	<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>

<script src="<?php echo e(URL::asset('assets/js/jquery-3.6.0.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('/assets/js/app.min.js')); ?>"></script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/cris/Sites/PCL/ims/resources/views/receive/view.blade.php ENDPATH**/ ?>