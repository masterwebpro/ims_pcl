<?php $__env->startSection('title'); ?> Purchase Order <?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<?php $__env->startComponent('components.breadcrumb'); ?>
<?php $__env->slot('li_1'); ?> Dashboards <?php $__env->endSlot(); ?>
<?php $__env->slot('title'); ?> Dashboard <?php $__env->endSlot(); ?>
<?php echo $__env->renderComponent(); ?>

<div class="row">
    <div class="col-lg-12">
        <div class="card" id="tasksList">
            <div class="card-header border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">All PO List</h5>
                    <div class="flex-shrink-0">
                        <a href="<?php echo e(URL::to('po/create')); ?>"class="btn btn-danger add-po" ><i class="ri-add-line align-bottom me-1"></i> Create PO</a>
                    </div>
                </div>
            </div>
            <div class="card-body border border-dashed border-end-0 border-start-0">
                <form action="<?php echo e(route('po.index')); ?>" method="GET">
                    <div class="row g-3">
                        <div class="col-xxl-4 col-sm-12">
                            <div class="search-box">
                                <input type="text" name="q" class="form-control search"
                                    placeholder="Search for tasks or something...">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>
                        <!--end col-->

                        <div class="col-xxl-2 col-sm-4">
                            <div class="input-light">
                                <select class="form-control" name="filter_date" id="filter_date">
                                    <option value="po_date">PO Date</option>
                                    <option value="created_at">Created Date</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-xxl-2 col-sm-4">
                            <input type="text" class="form-control" name="date" id="date_picker"
                                data-provider="flatpickr" data-date-format="Y-d-m" data-range-date="true"
                                placeholder="Select date range">
                        </div>
                        <!--end col-->

                        <div class="col-xxl-3 col-sm-4">
                            <div class="input-light">
                                <select class="form-control" data-choices data-choices-search-false
                                    name="status" id="status">
                                    <option value="">Status</option>
                                    <option value="all" selected>All</option>
                                    <option value="open">Open</option>
                                    <option value="posted">Posted</option>
                                </select>
                            </div>
                        </div>
                        <!--end col-->
                        <div class="col-xxl-1 col-sm-4">
                            <button type="submit" class="btn btn-primary w-100"> <i
                                    class="ri-equalizer-fill me-1 align-bottom"></i>
                                Filters
                            </button>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                </form>
            </div>
            <!--end card-body-->
            <div class="card-body">
                <div class="table-responsive table-card mb-4">
                    <table class="table align-middle table-nowrap mb-0" id="tasksTable">
                        <thead class="table-light text-muted">
                            <tr>
                                <th class="sort" data-sort="id">PO Number</th>
                                <th class="sort" data-sort="po_date">PO Date</th>
                                <th class="sort" data-sort="supplier_name">Supplier Name</th>
                                <th class="sort" data-sort="client_name">Client Name</th>
                                <th class="sort" data-sort="store">Store/Warehouse</th>
                                <th class="sort" data-sort="net_total">Net Total</th>
                                <th class="sort" data-sort="status">Status</th>
                                <th class="sort" data-sort="action">Action</th>
                            </tr>
                        </thead>
       
                        <tbody class="list form-check-all">
                            <? if($po_list->total() > 0 ) : ?>
                                <? foreach($po_list as $po) :?>
                                    <tr>
                                        <td class="id"><?php echo e($po->po_num); ?></td>
                                        <td class="project_name"><?php echo e($po->po_date); ?></td>
                                        <td><?php echo e($po->supplier_name); ?></td>
                                        <td class="client_name"><?php echo e($po->client_name); ?></td>
                                        <td class="store"><?php echo e($po->store_name); ?></td>
                                        <td class="net_total"><?php echo e(number_format($po->total_net,2)); ?></td>
                                        <td class="status"><span class="badge <?php echo e($po->status); ?> text-uppercase fs-11"><?php echo e($po->status); ?></span></td>
                                        <td class="action">
                                            <div class="hstack gap-3 fs-12">
                                                <a href="<?php echo e(URL::to('po')); ?>/<?=_encode($po->id)?>" data-id="<?php echo e($po->id); ?>" class="link-info text-info d-inline-block"><i class="ri-eye-fill align-bottom me-1"></i> View</a>
                                                <? if($po->status != 'posted') : ?>
                                                    <a href="<?php echo e(URL::to('po')); ?>/<?=_encode($po->id);?>/edit" data-id="<?php echo e($po->id); ?> " class="link-info edit-po"><i class="ri-pencil-fill align-bottom me-1"></i> Edit </a> </div>
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
                <?php echo $po_list->withQueryString()->links('pagination::bootstrap-5'); ?>

            </div>
            <!--end card-body-->
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>
<!--end row-->

	<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>

<script src="<?php echo e(URL::asset('assets/js/jquery-3.6.0.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('/assets/js/app.min.js')); ?>"></script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/cris/Sites/PCL/ims/resources/views/po/index.blade.php ENDPATH**/ ?>