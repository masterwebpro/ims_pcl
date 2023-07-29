<?php $__env->startSection('title'); ?>
    Store
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <!--datatable css-->
    <link href="<?php echo e(URL::asset('assets/css/dataTables.bootstrap5.min.css')); ?>" rel="stylesheet" type="text/css" />
    <!--datatable responsive css-->
    <link href="<?php echo e(URL::asset('assets/css/responsive.bootstrap.min.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(URL::asset('assets/css/buttons.dataTables.min.css')); ?>" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Maintenance
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
        Store
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>


    <div class="alert alert-danger d-none" id="error-handling" role="alert">
        <ul class="errors">
        </ul>
    </div>

    <div class="row justify-content-center">
        <div class="col-xxl-10">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Store View</h4>
                    <div class="flex-shrink-0">
                        <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">
                            <a href="<?php echo e(URL::to('maintenance/store')); ?>/<?=_encode($store->id)?>/edit" class="btn btn-success btn-label rounded-pill"><i class="ri-edit-line label-icon align-middle rounded-pill fs-16 me-2"></i> Edit</a>
                            <a href="<?php echo e(URL::to('maintenance/store')); ?>" class="btn btn-primary btn-label rounded-pill"><i
                                    class="ri-arrow-go-back-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                                Back</a>
                        </div>
                    </div>
                </div><!-- end card header -->
                <form method="POST" name="form-store" action="javascript:void(0);" id="form-store"
                    class="row g-3 needs-validation" novalidate>
                    <?php echo csrf_field(); ?>
                    <div class="card-body">
                        <div class="form-container">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row mb-4">
                                        <div class="col-md-4 form-group">
                                            <label for="store_name" class="form-label">Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="hidden" class="form-control" name="id" id="id" value="<?php echo e($store->id); ?>">
                                            <input type="text" class="form-control" required="required"
                                                name="store_name" id="store_name" value="<?php echo e($store->store_name); ?>" placeholder="Enter Name" disabled>
                                            <div class="invalid-feedback error-msg">Name is Required
                                            </div>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label for="store_code" class="form-label">Code <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" required="required"
                                                name="store_code" id="store_code" value="<?php echo e($store->store_code); ?>" placeholder="Enter Code" disabled>
                                            <div class="invalid-feedback error-msg">Code is Required
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="tin" class="form-label">Tin <span
                                                class="text-danger">*</span></label>
                                                <input type="text" class="form-control" required="required"
                                                    name="tin" id="tin" value="<?php echo e($store->tin); ?>" placeholder="Enter Tin" disabled>
                                                <div class="invalid-feedback error-msg">Tin is Required
                                            </div>
                                        </div>
                                        <div class="col-md-2 mt-4">
                                            <div class="form-check form-switch form-switch-custom form-switch-primary">
                                                <label class="form-check-label" for="is_enabled">Enable</label>
                                                <input class="form-check-input" type="checkbox" role="switch" id="is_enabled" <?php echo e($store->is_enabled == 1 ? "checked" : ""); ?> disabled>
                                            </div>
                                            <div class="form-check form-switch form-switch-custom form-switch-primary">
                                                <label class="form-check-label" for="is_vatable">Vatable</label>
                                                <input class="form-check-input" type="checkbox" role="switch" id="is_vatable" <?php echo e($store->is_vatable == 1 ? "checked" : ""); ?> disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-md-3 form-group">
                                            <label for="client_type" class="form-label">Client Name <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select select2" required="required" id="client_type" disabled
                                                name="client_type">
                                                <option value="">Select Type</option>
                                                <? foreach($client as $cli) : ?>
                                                    <option value="<?=$cli->id?>" <?=($cli->id == $store->client_id) ? 'selected' : ''?> ><?=$cli->client_name?></option>
                                                <? endforeach;?>
                                            </select>
                                            <div class="invalid-feedback text-danger error-msg">Client Type is
                                                Required</div>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label for="contact_person" class="form-label">Contact Person <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" required="required" disabled
                                                name="contact_person" id="contact_person" value="<?php echo e($store->contact_person); ?>"
                                                placeholder="Enter Contact Person">
                                            <div class="invalid-feedback error-msg">Contact Person is Required
                                            </div>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label for="email_address" class="form-label">Email Address <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" required="required" disabled
                                                name="email_address" id="email_address" value="<?php echo e($store->email_address); ?>"
                                                placeholder="Enter Email Address">
                                            <div class="invalid-feedback error-msg">Email Address is Required
                                            </div>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label for="phone_no" class="form-label">Phone <span
                                                class="text-danger">*</span></label></label>
                                            <input type="text" class="form-control" id="phone_no" name="phone_no" placeholder="(xxx)xxx-xxxx" value="<?php echo e($store->phone_no); ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-md-6 form-group">
                                            <div>
                                                <label for="address_1" class="form-label">Address 1<span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="address_1" name="address_1" value="<?php echo e($store->address_1); ?>" placeholder="Enter address..." disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label for="city" class="form-label">City<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" required="required" disabled
                                                name="city" id="city" value="<?php echo e($store->city); ?>"
                                                placeholder="Enter City">
                                            <div class="invalid-feedback error-msg">City is Required
                                            </div>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label for="province" class="form-label">Province<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" required="required" disabled
                                                name="province" id="province" value="<?php echo e($store->province); ?>"
                                                placeholder="Enter Province">
                                            <div class="invalid-feedback error-msg">Province is Required
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-md-6 form-group">
                                            <div>
                                                <label for="address_2" class="form-label">Address 2</label>
                                                <input type="text" class="form-control" id="address_2" name="address_2" value="<?php echo e($store->address_2); ?>" placeholder="Enter address..." disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label for="country" class="form-label">Country<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" required="required" disabled
                                                name="country" id="country" value="<?php echo e($store->country); ?>"
                                                placeholder="Enter Country">
                                            <div class="invalid-feedback error-msg">Country is Required
                                            </div>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label for="zipcode" class="form-label">Zip Code<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" required="required" disabled
                                                name="zipcode" id="zipcode" value="<?php echo e($store->zipcode); ?>"
                                                placeholder="Enter Zip Code">
                                            <div class="invalid-feedback error-msg">Zip Code is Required
                                            </div>
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
    <script src="<?php echo e(URL::asset('/assets/js/maintenance/client.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\MAMP\htdocs\ims\resources\views/maintenance/store/view.blade.php ENDPATH**/ ?>