<?php $__env->startSection('title'); ?> Unit of Measure <?php $__env->stopSection(); ?>
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
<?php $__env->slot('title'); ?> Unit of Measure <?php $__env->endSlot(); ?>
<?php echo $__env->renderComponent(); ?>


<div class="alert alert-danger d-none" id="error-handling" role="alert">
    <ul class="errors">
    </ul>
</div>

<div class="row justify-content-center">
    <div class="col-xxl-10">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">UOM View</h4>
                <div class="flex-shrink-0">
                    <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">
                        <a href="<?php echo e(URL::to('maintenance/unit')); ?>/<?=_encode($uom->uom_id)?>/edit" class="btn btn-success btn-label rounded-pill"><i class="ri-edit-line label-icon align-middle rounded-pill fs-16 me-2"></i> Edit</a>
                        <a  href="<?php echo e(URL::to('maintenance/unit')); ?>" class="btn btn-primary btn-label rounded-pill"><i class="ri-arrow-go-back-line label-icon align-middle rounded-pill fs-16 me-2"></i> Back</a>
                    </div>
                </div>
            </div><!-- end card header -->
            <form  method="POST" name="form-uom" action="javascript:void(0);" id="form-uom" class="row g-3 needs-validation" novalidate>
            <?php echo csrf_field(); ?>
            <div class="card-body">
                <div class="form-container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row mb-4">
                                <div class="col-md-4 form-group">
                                    <label for="uom_id" class="form-label">Code <span class="text-danger">*</span></label>
                                    <input type="hidden" class="form-control" name="uom_id" id="uom_id" value="<?php echo e($uom->uom_id); ?>">
                                    <input type="text" class="form-control" required="required" disabled name="code" id="code" value="<?php echo e($uom->code); ?>" placeholder="Enter Code">
                                    <div class="invalid-feedback error-msg po_num_error">code is Required</div>
                                </div>

                                <div class="col-md-4 form-group">
                                    <label for="uom_desc" class="form-label">Description <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" required="required" name="uom_desc" disabled id="uom_desc" value="<?php echo e($uom->uom_desc); ?>" placeholder="Enter Description">
                                    <div class="invalid-feedback error-msg po_num_error">Description is Required</div>
                                </div>

                                <div class="col-md-4 form-group">
                                    <label for="inputState" class="form-label">Conversion <span class="text-danger">*</span></label>
                                    <input type="number" id="convertion_pc" required="required"  name="convertion_pc" disabled class="form-control" value="<?php echo e($uom->convertion_pc); ?>" placeholder="Enter Conversion">
                                    <div class="invalid-feedback text-danger error-msg po_date_error">Conversion is Required</div>
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
<script src="<?php echo e(URL::asset('/assets/js/maintenance/unit.js')); ?>"></script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\MAMP\htdocs\ims\resources\views/maintenance/unit/view.blade.php ENDPATH**/ ?>