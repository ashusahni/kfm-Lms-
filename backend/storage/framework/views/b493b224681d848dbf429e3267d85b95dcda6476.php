<?php $__env->startPush('styles_top'); ?>
    <link rel="stylesheet" href="/assets/default/vendors/select2/select2.min.css">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <section class="section">
        <div class="section-header">
            <h1><?php echo e($pageTitle); ?></h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="<?php echo e(getAdminPanelUrl()); ?>"><?php echo e(trans('admin/main.dashboard')); ?></a></div>
                <div class="breadcrumb-item"><a href="<?php echo e(getAdminPanelUrl()); ?>/dieticians"><?php echo e(trans('admin/main.dietitians')); ?></a></div>
                <div class="breadcrumb-item"><?php echo e(trans('admin/main.assign_courses') ?? 'Assign courses'); ?></div>
            </div>
        </div>

        <?php if(!empty(session('toast'))): ?>
            <?php $toast = session('toast'); ?>
            <div class="alert alert-<?php echo e($toast['status'] ?? 'success'); ?> my-25">
                <strong><?php echo e($toast['title'] ?? ''); ?></strong> <?php echo e($toast['msg'] ?? ''); ?>

            </div>
        <?php endif; ?>

        <div class="section-body">
            <div class="row">
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label class="input-label"><?php echo e(trans('admin/main.dietitian') ?? 'Dietitian'); ?></label>
                                <div class="d-flex align-items-center">
                                    <figure class="avatar mr-2">
                                        <img src="<?php echo e($user->getAvatar()); ?>" alt="<?php echo e($user->full_name); ?>">
                                    </figure>
                                    <div>
                                        <div class="font-weight-bold"><?php echo e($user->full_name); ?></div>
                                        <?php if($user->email): ?>
                                            <div class="text-muted small"><?php echo e($user->email); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <form action="<?php echo e(getAdminPanelUrl()); ?>/dieticians/<?php echo e($user->id); ?>/assign-courses" method="post">
                                <?php echo csrf_field(); ?>
                                <div class="form-group">
                                    <label class="input-label"><?php echo e(trans('admin/main.courses') ?? 'Courses (programs)'); ?> <span class="text-muted">(<?php echo e(trans('admin/main.select_multiple') ?? 'Select one or more'); ?>)</span></label>
                                    <select name="webinar_ids[]" id="webinar_ids" class="form-control select2-multiple" multiple data-placeholder="<?php echo e(trans('admin/main.select_courses') ?? 'Select courses to assign'); ?>">
                                        <?php $__currentLoopData = $webinars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $webinar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($webinar->id); ?>" <?php if(in_array($webinar->id, $assignedIds)): ?> selected <?php endif; ?>>
                                                <?php echo e($webinar->title); ?> (ID: <?php echo e($webinar->id); ?>)
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <small class="form-text text-muted"><?php echo e(trans('admin/main.assign_courses_hint') ?? 'Assigned courses will appear in the dietician panel. The dietician can manage batches and view students for these courses only.'); ?></small>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary"><?php echo e(trans('admin/main.save') ?? 'Save'); ?></button>
                                    <a href="<?php echo e(getAdminPanelUrl()); ?>/dieticians" class="btn btn-secondary"><?php echo e(trans('admin/main.cancel') ?? 'Cancel'); ?></a>
                                    <a href="<?php echo e(getAdminPanelUrl()); ?>/users/<?php echo e($user->id); ?>/edit" class="btn btn-outline-secondary"><?php echo e(trans('admin/main.edit')); ?> <?php echo e(trans('admin/main.user')); ?></a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4><?php echo e(trans('admin/main.assigned_courses') ?? 'Currently assigned'); ?> (<?php echo e(count($assignedIds)); ?>)</h4>
                        </div>
                        <div class="card-body">
                            <?php if(count($assignedIds) > 0): ?>
                                <ul class="list-unstyled mb-0">
                                    <?php $__currentLoopData = $webinars->whereIn('id', $assignedIds); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="mb-2"><?php echo e($w->title); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-muted mb-0"><?php echo e(trans('admin/main.no_courses_assigned') ?? 'No courses assigned yet. Use the form to assign courses.'); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts_bottom'); ?>
<script src="/assets/default/vendors/select2/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#webinar_ids').select2({
            placeholder: $('#webinar_ids').data('placeholder'),
            allowClear: true,
            width: '100%'
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\ashut\Downloads\Telegram Desktop\rocket-lms_v1.8 (2)\backend\resources\views/admin/users/dietician_assign_courses.blade.php ENDPATH**/ ?>