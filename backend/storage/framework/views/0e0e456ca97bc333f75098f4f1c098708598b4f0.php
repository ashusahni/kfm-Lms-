<?php $__env->startSection('content'); ?>
    <section class="section">
        <div class="section-header">
            <h1><?php echo e($pageTitle); ?></h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="<?php echo e(getAdminPanelUrl()); ?>"><?php echo e(trans('admin/main.dashboard')); ?></a></div>
                <div class="breadcrumb-item"><a href="<?php echo e(getAdminPanelUrl()); ?>/webinars"><?php echo e(trans('admin/main.webinars') ?? 'Courses'); ?></a></div>
                <div class="breadcrumb-item"><a href="<?php echo e(getAdminPanelUrl()); ?>/webinars/<?php echo e($webinar->id); ?>/batches"><?php echo e($webinar->title); ?> — <?php echo e(trans('admin/main.course_batches') ?? 'Batches'); ?></a></div>
                <div class="breadcrumb-item"><?php echo e(trans('admin/main.create_batch') ?? 'Create batch'); ?></div>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4><?php echo e($webinar->title); ?> — <?php echo e(trans('admin/main.create_batch') ?? 'Create batch'); ?></h4>
                </div>
                <form method="post" action="<?php echo e(getAdminPanelUrl()); ?>/webinars/<?php echo e($webinar->id); ?>/batches">
                    <?php echo csrf_field(); ?>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo e(trans('admin/main.name') ?? 'Name'); ?> <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="<?php echo e(old('name')); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo e(trans('admin/main.code') ?? 'Code'); ?></label>
                                    <input type="text" name="code" class="form-control" value="<?php echo e(old('code')); ?>" placeholder="e.g. JAN2025">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo e(trans('admin/main.start_date') ?? 'Start date'); ?></label>
                                    <input type="number" name="start_date" class="form-control" value="<?php echo e(old('start_date')); ?>" placeholder="Unix timestamp (optional)">
                                    <small class="text-muted"><?php echo e(trans('admin/main.unix_timestamp_optional') ?? 'Leave empty for no start limit'); ?></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo e(trans('admin/main.end_date') ?? 'End date'); ?></label>
                                    <input type="number" name="end_date" class="form-control" value="<?php echo e(old('end_date')); ?>" placeholder="Unix timestamp (optional)">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo e(trans('admin/main.capacity') ?? 'Capacity'); ?></label>
                                    <input type="number" name="capacity" class="form-control" value="<?php echo e(old('capacity')); ?>" min="0" placeholder="<?php echo e(trans('admin/main.unlimited_if_empty') ?? 'Unlimited if empty'); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo e(trans('admin/main.status') ?? 'Status'); ?></label>
                                    <select name="status" class="form-control">
                                        <?php $__currentLoopData = \App\Models\CourseBatch::$statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($s); ?>" <?php echo e(old('status', 'draft') == $s ? 'selected' : ''); ?>><?php echo e(ucfirst($s)); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label><?php echo e(trans('admin/main.sort_order') ?? 'Sort order'); ?></label>
                            <input type="number" name="sort_order" class="form-control" value="<?php echo e(old('sort_order', 0)); ?>" min="0">
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"><?php echo e(trans('public.save') ?? 'Save'); ?></button>
                        <a href="<?php echo e(getAdminPanelUrl()); ?>/webinars/<?php echo e($webinar->id); ?>/batches" class="btn btn-secondary"><?php echo e(trans('public.cancel') ?? 'Cancel'); ?></a>
                    </div>
                </form>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\ashut\Downloads\Telegram Desktop\rocket-lms_v1.8\backend\resources\views/admin/course_batches/create.blade.php ENDPATH**/ ?>