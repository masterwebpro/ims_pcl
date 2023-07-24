<?php $__env->startSection('title'); ?> <?php echo app('translator')->get('translation.dashboards'); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
<link href="<?php echo e(URL::asset('assets/libs/jsvectormap/jsvectormap.min.css')); ?>" rel="stylesheet" type="text/css" />
<link href="<?php echo e(URL::asset('assets/libs/swiper/swiper.min.css')); ?>" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<?php $__env->startComponent('components.breadcrumb'); ?>
<?php $__env->slot('li_1'); ?> Dashboards <?php $__env->endSlot(); ?>
<?php $__env->slot('title'); ?> Dashboard <?php $__env->endSlot(); ?>
<?php echo $__env->renderComponent(); ?>

	<?php if($errors->any()): ?>
		<div class="alert alert-danger">
			<?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<?php echo e($error); ?> <br>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		</div>
	<?php endif; ?>

	<?php echo e(Form::model($supplier, array('route' => array('suppliers.update', $supplier->id), 'method' => 'PUT'))); ?>


		<div class="mb-3">
			<?php echo e(Form::label('supplier_name', 'Supplier_name', ['class'=>'form-label'])); ?>

			<?php echo e(Form::text('supplier_name', null, array('class' => 'form-control'))); ?>

		</div>
		<div class="mb-3">
			<?php echo e(Form::label('supplier_code', 'Supplier_code', ['class'=>'form-label'])); ?>

			<?php echo e(Form::text('supplier_code', null, array('class' => 'form-control'))); ?>

		</div>
		<div class="mb-3">
			<?php echo e(Form::label('contact_no', 'Contact_no', ['class'=>'form-label'])); ?>

			<?php echo e(Form::text('contact_no', null, array('class' => 'form-control'))); ?>

		</div>
		<div class="mb-3">
			<?php echo e(Form::label('supplier_address', 'Supplier_address', ['class'=>'form-label'])); ?>

			<?php echo e(Form::textarea('supplier_address', null, array('class' => 'form-control'))); ?>

		</div>

		<?php echo e(Form::submit('Edit', array('class' => 'btn btn-primary'))); ?>


	<?php echo e(Form::close()); ?>


	<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
<!-- apexcharts -->
<script src="<?php echo e(URL::asset('/assets/libs/apexcharts/apexcharts.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('/assets/libs/jsvectormap/jsvectormap.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('assets/libs/swiper/swiper.min.js')); ?>"></script>
<!-- dashboard init -->
<script src="<?php echo e(URL::asset('/assets/js/pages/dashboard-ecommerce.init.js')); ?>"></script>
<script src="<?php echo e(URL::asset('/assets/js/app.min.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/cris/Sites/ANC/ims/resources/views/suppliers/edit.blade.php ENDPATH**/ ?>