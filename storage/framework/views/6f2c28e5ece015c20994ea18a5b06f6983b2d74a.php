<?php $__env->startSection('title'); ?> Warehouse <?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
<!--datatable css-->
<link href="<?php echo e(URL::asset('assets/css/dataTables.bootstrap5.min.css')); ?>" rel="stylesheet" type="text/css" />
<!--datatable responsive css-->
<link href="<?php echo e(URL::asset('assets/css/responsive.bootstrap.min.css')); ?>" rel="stylesheet" type="text/css" />
<link href="<?php echo e(URL::asset('assets/css/buttons.dataTables.min.css')); ?>" rel="stylesheet" type="text/css" />

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<?php $__env->startComponent('components.breadcrumb'); ?>
<?php $__env->slot('li_1'); ?> Maintenance <?php $__env->endSlot(); ?>
<?php $__env->slot('title'); ?> Warehouse <?php $__env->endSlot(); ?>
<?php echo $__env->renderComponent(); ?>


<div class="alert alert-danger d-none" id="error-handling" role="alert">
    <ul class="errors">
    </ul>
</div>

<div class="row justify-content-center">
    <div class="col-xxl-10">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Warehouse View</h4>
                <div class="flex-shrink-0">
                    <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">
                        <a href="<?php echo e(URL::to('maintenance/warehouse')); ?>/<?=_encode($warehouse->id)?>/edit" class="btn btn-success btn-label rounded-pill"><i class="ri-edit-line label-icon align-middle rounded-pill fs-16 me-2"></i> Edit</a>
                        <a  href="<?php echo e(URL::to('maintenance/warehouse')); ?>" class="btn btn-primary btn-label rounded-pill"><i class="ri-arrow-go-back-line label-icon align-middle rounded-pill fs-16 me-2"></i> Back</a>
                    </div>
                </div>
            </div><!-- end card header -->
            <form  method="POST" name="form-warehouse" action="javascript:void(0);" id="form-warehouse" class="row g-3 needs-validation" novalidate>
            <?php echo csrf_field(); ?>
            <div class="card-body">
                <div class="form-container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row mb-4">
                                <div class="col-md-4 offset-md-8 form-group">
                                    <div class="form-check form-switch form-switch-custom form-switch-primary float-end">
                                        <label class="form-check-label" for="is_enabled">Enable</label>
                                        <input class="form-check-input" type="checkbox" role="switch" id="is_enabled" name="is_enabled" disabled <?php echo e((($warehouse->is_enabled == 1) ? "checked" : "")); ?>>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-4 form-group">
                                    <label for="warehouse_id" class="form-label">Warehouse Name <span class="text-danger">*</span></label>
                                    <input type="hidden" class="form-control" name="id" id="id" value="">
                                    <input type="text" class="form-control" required="required" name="warehouse_name" id="warehouse_name" disabled value="<?php echo e($warehouse->warehouse_name); ?>" placeholder="Enter Warehouse Name">
                                    <div class="invalid-feedback error-msg po_num_error">Warehouse is Required</div>
                                </div>

                                <div class="col-md-4 form-group">
                                    <label for="client_id" class="form-label">Client Name</label>
                                    <select class="form-select select2" required="required" id="client_id" name="client_id" disabled>
                                        <option value="">Select Client</option>
                                        <? foreach($client_list as $client) : ?>
                                        <option value="<?=$client->id?>" <?=($client->id == $warehouse->client_id) ? 'selected' : ''?> ><?=$client->client_name?></option>
                                        <? endforeach;?>
                                    </select>
                                    <div class="invalid-feedback text-danger error-msg client_error">Client Name is Required</div>
                                </div>

                                <div class="col-md-4 form-group">
                                    <label for="store_id" class="form-label">Store Name</label>
                                    <select class="form-select select2" required="required" id="store_id" name="store_id" disabled>
                                        <option value="">Select Store</option>
                                        <? foreach($store_list as $store) : ?>
                                            <option value="<?=$store->id?>" <?=($store->id == $warehouse->store_id ) ? 'selected' : ''?> ><?=$store->store_name?></option>
                                        <? endforeach;?>
                                    </select>
                                    <div class="invalid-feedback text-danger error-msg client_error">Store Name is Required</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            </form>
        </div>
    </div> <!-- end col -->
</div> <!-- end col -->

	<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>

<script src="<?php echo e(URL::asset('assets/js/jquery-3.6.0.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('assets/libs/cleave.js/cleave.js.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('assets/libs/masks/jquery.mask.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('assets/libs/select2/select2.min.js')); ?>"></script>

<script src="<?php echo e(URL::asset('assets/js/datatables/jquery.dataTables.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('assets/js/datatables/dataTables.bootstrap5.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('assets/js/datatables/dataTables.responsive.min.js')); ?>"></script>

<script src="<?php echo e(URL::asset('/assets/js/app.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('/assets/js/maintenance/warehouse.js')); ?>"></script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\MAMP\htdocs\ims\resources\views/maintenance/warehouse/view.blade.php ENDPATH**/ ?>