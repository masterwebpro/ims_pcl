<?php $__env->startSection('title'); ?> <?php echo app('translator')->get('translation.dashboards'); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
<!--datatable css-->
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<!--datatable responsive css-->
<link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<?php $__env->startComponent('components.breadcrumb'); ?>
<?php $__env->slot('li_1'); ?> Dashboards <?php $__env->endSlot(); ?>
<?php $__env->slot('title'); ?> Dashboard <?php $__env->endSlot(); ?>
<?php echo $__env->renderComponent(); ?>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h5 class="card-title mb-0 flex-grow-1">Ajax Datatables</h5>
				<div class="flex-shrink-0">
					<a href="<?php echo e(route('suppliers.create')); ?>" class="btn btn-info">Create</a>
				</div>
				
            </div>
            <div class="card-body">
				<table class="table table-bordered" id="supplier-table">
					<thead>
						<tr>
							<th>id</th>
							<th>supplier_name</th>
							<th>supplier_code</th>
							<th>contact_no</th>
							<th>supplier_address</th>
						</tr>
					</thead>
					<tbody>
						
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>


	<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>

<script src="<?php echo e(URL::asset('assets/js/jquery-3.6.0.min.js')); ?>"></script>

<script src="<?php echo e(URL::asset('assets/js/dataTables/jquery.dataTables.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('assets/js/dataTables/dataTables.bootstrap5.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('assets/js/dataTables/dataTables.responsive.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('assets/js/dataTables/dataTables.buttons.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('assets/js/dataTables/buttons.print.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('assets/js/dataTables/buttons.html5.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('assets/js/pdfmake/vfs_fonts.js')); ?>"></script>
<script src="<?php echo e(URL::asset('assets/js/pdfmake/pdfmake.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('assets/js/jszip/jszip.min.js')); ?>"></script>

<!-- <script src="<?php echo e(URL::asset('assets/js/suppliers/suppliers.js')); ?>"></script> -->

<script type="text/javascript">

   $(document).ready(function(){

      // Initialize
      $('#supplier-table').DataTable({
          processing: true,
          serverSide: true,
          ajax: "<?php echo e(route('getdata')); ?>",
          columns: [
			{ data: 'id' },
			{ data: 'supplier_name' },
              { data: 'supplier_code' },
              { data: 'supplier_address' },
              { data: 'contact_no' },
          ]
      });
   });

   </script>

<script src="<?php echo e(URL::asset('/assets/js/app.min.js')); ?>"></script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/cris/Sites/ANC/ims/resources/views/suppliers/index.blade.php ENDPATH**/ ?>