<?php $__env->startSection('title'); ?>
    Client
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
            Client
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
                    <h4 class="card-title mb-0 flex-grow-1">Client Creation</h4>
                    <div class="flex-shrink-0">
                        <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">
                            <button type="button" data-status="open"
                                class="btn btn-success btn-label rounded-pill submit-client"><i
                                    class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                                Save</button>
                            <a href="<?php echo e(URL::to('maintenance/client')); ?>" class="btn btn-primary btn-label rounded-pill"><i
                                    class="ri-arrow-go-back-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                                Back</a>
                        </div>
                    </div>
                </div><!-- end card header -->
                <form method="POST" name="form-client" action="javascript:void(0);" id="form-client"
                    class="row g-3 needs-validation" novalidate>
                    <?php echo csrf_field(); ?>
                    <div class="card-body">
                        <div class="form-container">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row mb-4">
                                        <div class="col-md-4 form-group">
                                            <label for="client_name" class="form-label">Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" required="required"
                                                name="client_name" id="client_name" value="" placeholder="Enter Name">
                                            <div class="invalid-feedback error-msg">Name is Required
                                            </div>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label for="client_code" class="form-label">Code <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" required="required"
                                                name="client_code" id="client_code" value="" placeholder="Enter Code">
                                            <div class="invalid-feedback error-msg">Code is Required
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="tin" class="form-label">Tin <span
                                                class="text-danger">*</span></label>
                                                <input type="text" class="form-control" required="required"
                                                    name="tin" id="tin" value="" placeholder="Enter Tin">
                                                <div class="invalid-feedback error-msg">Tin is Required
                                            </div>
                                        </div>
                                        <div class="col-md-2 mt-4">
                                            <div class="form-check form-switch form-switch-custom form-switch-primary">
                                                <label class="form-check-label" for="is_enabled">Enable</label>
                                                <input class="form-check-input" type="checkbox" role="switch" id="is_enabled" checked>
                                            </div>
                                            <div class="form-check form-switch form-switch-custom form-switch-primary">
                                                <label class="form-check-label" for="is_vatable">Vatable</label>
                                                <input class="form-check-input" type="checkbox" role="switch" id="is_vatable" checked>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-md-3 form-group">
                                            <label for="client_type" class="form-label">Type <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select select2" required="required" id="client_type"
                                                name="client_type">
                                                <option value="">Select Type</option>
                                                <? foreach($client_type as $k => $type) : ?>
                                                <option value="<?= $k ?>"><?= $type ?></option>
                                                <? endforeach;?>
                                            </select>
                                            <div class="invalid-feedback text-danger error-msg">Client Type is
                                                Required</div>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label for="contact_person" class="form-label">Contact Person <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" required="required"
                                                name="contact_person" id="contact_person" value=""
                                                placeholder="Enter Contact Person">
                                            <div class="invalid-feedback error-msg">Contact Person is Required
                                            </div>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label for="email_address" class="form-label">Email Address <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" required="required"
                                                name="email_address" id="email_address" value=""
                                                placeholder="Enter Email Address">
                                            <div class="invalid-feedback error-msg">Email Address is Required
                                            </div>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label for="phone_no" class="form-label">Phone</label>
                                            <input type="text" class="form-control" id="phone_no" name="phone_no" placeholder="(xxx)xxx-xxxx">
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-md-6 form-group">
                                            <div>
                                                <label for="address_1" class="form-label">Address 1<span
                                                        class="text-danger">*</span></label></label>
                                                <input type="text" class="form-control" id="address_1" placeholder="Enter address...">
                                            </div>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label for="city" class="form-label">City<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" required="required"
                                                name="city" id="city" value=""
                                                placeholder="Enter City">
                                            <div class="invalid-feedback error-msg">City is Required
                                            </div>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label for="province" class="form-label">Province<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" required="required"
                                                name="province" id="province" value=""
                                                placeholder="Enter Province">
                                            <div class="invalid-feedback error-msg">Province is Required
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-md-6 form-group">
                                            <div>
                                                <label for="address_2" class="form-label">Address 2</label>
                                                <input type="text" class="form-control" id="address_2" placeholder="Enter address...">
                                            </div>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label for="country" class="form-label">Country<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" required="required"
                                                name="country" id="country" value=""
                                                placeholder="Enter Country">
                                            <div class="invalid-feedback error-msg">Country is Required
                                            </div>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label for="zipcode" class="form-label">Zip Code.<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" required="required"
                                                name="zipcode" id="zipcode" value=""
                                                placeholder="Enter Zip Code">
                                            <div class="invalid-feedback error-msg">City is Required
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

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\MAMP\htdocs\ims\resources\views/maintenance/client/create.blade.php ENDPATH**/ ?>