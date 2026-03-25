<?php $__env->startSection('content'); ?>
    <section class="section">
        <div class="section-header">
            <h1><?php echo e($pageTitle); ?></h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="<?php echo e(getAdminPanelUrl()); ?>"><?php echo e(trans('admin/main.dashboard')); ?></a></div>
                <div class="breadcrumb-item"><?php echo e(trans('admin/main.course_batches') ?? 'Course batches'); ?></div>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4><?php echo e(trans('admin/main.course_batches') ?? 'Course batches'); ?></h4>
                    <div class="card-header-action">
                        <form method="get" action="<?php echo e(getAdminPanelUrl()); ?>/batches" class="form-inline d-inline">
                            <input type="text" name="q" class="form-control form-control-sm mr-2" value="<?php echo e(request('q')); ?>" placeholder="<?php echo e(trans('admin/main.search_courses') ?? 'Search courses'); ?>">
                            <select name="type" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                                <option value=""><?php echo e(trans('admin/main.all_types') ?? 'All types'); ?></option>
                                <option value="course" <?php echo e(request('type') == 'course' ? 'selected' : ''); ?>><?php echo e(trans('admin/main.courses')); ?></option>
                                <option value="webinar" <?php echo e(request('type') == 'webinar' ? 'selected' : ''); ?>><?php echo e(trans('admin/main.live_classes')); ?></option>
                                <option value="text_lesson" <?php echo e(request('type') == 'text_lesson' ? 'selected' : ''); ?>><?php echo e(trans('admin/main.text_courses')); ?></option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary"><?php echo e(trans('admin/main.search') ?? 'Search'); ?></button>
                        </form>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover font-14">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th><?php echo e(trans('admin/main.title') ?? 'Course'); ?></th>
                                    <th><?php echo e(trans('admin/main.type') ?? 'Type'); ?></th>
                                    <th class="text-center"><?php echo e(trans('admin/main.batches_count') ?? 'Batches'); ?></th>
                                    <th width="180"><?php echo e(trans('public.controls')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $webinars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $webinar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($webinar->id); ?></td>
                                        <td><?php echo e($webinar->title); ?></td>
                                        <td><?php echo e(trans('webinars.' . $webinar->type) ?? $webinar->type); ?></td>
                                        <td class="text-center"><?php echo e($webinar->batches_count ?? 0); ?></td>
                                        <td>
                                            <a href="<?php echo e(getAdminPanelUrl()); ?>/webinars/<?php echo e($webinar->id); ?>/batches" class="btn btn-sm btn-primary">
                                                <i class="fas fa-list"></i> <?php echo e(trans('admin/main.manage_batches') ?? 'Manage batches'); ?>

                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted"><?php echo e(trans('admin/main.no_courses_found') ?? 'No courses found.'); ?></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php if($webinars->hasPages()): ?>
                <div class="card-footer text-center">
                    <?php echo e($webinars->appends(request()->input())->links()); ?>

                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\ashut\Downloads\Telegram Desktop\rocket-lms_v1.8\backend\resources\views/admin/course_batches/batches_index.blade.php ENDPATH**/ ?>