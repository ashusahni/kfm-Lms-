<?php $__env->startSection('content'); ?>
    <section class="section">
        <div class="section-header">
            <h1><?php echo e($pageTitle); ?></h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="<?php echo e(getAdminPanelUrl()); ?>"><?php echo e(trans('admin/main.dashboard')); ?></a></div>
                <div class="breadcrumb-item"><a href="<?php echo e(getAdminPanelUrl()); ?>/webinars"><?php echo e(trans('admin/main.webinars') ?? 'Courses'); ?></a></div>
                <div class="breadcrumb-item"><?php echo e($webinar->title); ?></div>
                <div class="breadcrumb-item"><?php echo e(trans('admin/main.course_batches') ?? 'Batches'); ?></div>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4><?php echo e($webinar->title); ?> (ID: <?php echo e($webinar->id); ?>) — <?php echo e(trans('admin/main.course_batches') ?? 'Batches'); ?></h4>
                    <div class="card-header-action">
                        <a href="<?php echo e(getAdminPanelUrl()); ?>/webinars/<?php echo e($webinar->id); ?>/batches/create" class="btn btn-primary">
                            <i class="fas fa-plus"></i> <?php echo e(trans('admin/main.create_batch') ?? 'Create batch'); ?>

                        </a>
                        <form method="get" action="<?php echo e(getAdminPanelUrl()); ?>/webinars/<?php echo e($webinar->id); ?>/batches" class="form-inline d-inline ml-2">
                            <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                                <option value=""><?php echo e(trans('admin/main.all_statuses') ?? 'All statuses'); ?></option>
                                <?php $__currentLoopData = \App\Models\CourseBatch::$statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($s); ?>" <?php echo e(request('status') == $s ? 'selected' : ''); ?>><?php echo e(ucfirst($s)); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </form>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover font-14">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th><?php echo e(trans('admin/main.name') ?? 'Name'); ?></th>
                                    <th><?php echo e(trans('admin/main.code') ?? 'Code'); ?></th>
                                    <th><?php echo e(trans('admin/main.start_date') ?? 'Start'); ?></th>
                                    <th><?php echo e(trans('admin/main.end_date') ?? 'End'); ?></th>
                                    <th class="text-center"><?php echo e(trans('admin/main.capacity') ?? 'Capacity'); ?></th>
                                    <th class="text-center"><?php echo e(trans('admin/main.enrolled') ?? 'Enrolled'); ?></th>
                                    <th><?php echo e(trans('admin/main.status') ?? 'Status'); ?></th>
                                    <th width="180"><?php echo e(trans('public.controls')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $batches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($batch->id); ?></td>
                                        <td><?php echo e($batch->name); ?></td>
                                        <td><?php echo e($batch->code ?? '—'); ?></td>
                                        <td><?php echo e($batch->start_date ? dateTimeFormat($batch->start_date, 'j M Y') : '—'); ?></td>
                                        <td><?php echo e($batch->end_date ? dateTimeFormat($batch->end_date, 'j M Y') : '—'); ?></td>
                                        <td class="text-center"><?php echo e($batch->capacity ?? '∞'); ?></td>
                                        <td class="text-center"><?php echo e($batch->enrolled_count); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo e($batch->status === 'open' ? 'success' : ($batch->status === 'draft' ? 'secondary' : 'warning')); ?>"><?php echo e(ucfirst($batch->status)); ?></span>
                                        </td>
                                        <td>
                                            <a href="<?php echo e(getAdminPanelUrl()); ?>/webinars/<?php echo e($webinar->id); ?>/batches/<?php echo e($batch->id); ?>/edit" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                            <?php if($batch->sales()->count() == 0): ?>
                                                <a href="<?php echo e(getAdminPanelUrl()); ?>/webinars/<?php echo e($webinar->id); ?>/batches/<?php echo e($batch->id); ?>/delete" class="btn btn-sm btn-outline-danger" onclick="return confirm('<?php echo e(trans('admin/main.confirm_delete_batch') ?? 'Delete this batch?'); ?>');"><i class="fas fa-trash"></i></a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-5 text-muted"><?php echo e(trans('admin/main.no_batches') ?? 'No batches yet. Create one to offer this course in batches.'); ?></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php if($batches->hasPages()): ?>
                <div class="card-footer text-center">
                    <?php echo e($batches->appends(request()->input())->links()); ?>

                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\ashut\Downloads\Telegram Desktop\rocket-lms_v1.8\backend\resources\views/admin/course_batches/index.blade.php ENDPATH**/ ?>