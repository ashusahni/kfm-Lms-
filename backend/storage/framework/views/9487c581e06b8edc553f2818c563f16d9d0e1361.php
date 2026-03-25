<?php $__env->startSection('content'); ?>
    <section class="section">
        <div class="section-header">
            <h1><?php echo e($pageTitle); ?></h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="<?php echo e(getAdminPanelUrl()); ?>"><?php echo e(trans('admin/main.dashboard')); ?></a></div>
                <div class="breadcrumb-item"><?php echo e($pageTitle); ?></div>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4><?php echo e(trans('admin/main.course_health_log_settings') ?? 'Course health log settings'); ?></h4>
                    <div class="card-header-action">
                        <form method="get" action="<?php echo e(getAdminPanelUrl()); ?>/course-health-log-settings" class="form-inline">
                            <input type="text" name="q" class="form-control form-control-sm mr-2" placeholder="<?php echo e(trans('admin/main.search_course') ?? 'Search course...'); ?>" value="<?php echo e(request('q')); ?>">
                            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
                        </form>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover font-14">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th><?php echo e(trans('admin/main.title') ?? 'Course'); ?></th>
                                    <th class="text-center"><?php echo e(trans('admin/main.health_log_enabled') ?? 'Health log'); ?></th>
                                    <th class="text-center" width="120"><?php echo e(trans('public.controls')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $webinars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($w->id); ?></td>
                                        <td><?php echo e($w->title); ?></td>
                                        <td class="text-center">
                                            <?php if(in_array($w->id, $settingIds ?? [])): ?>
                                                <span class="badge badge-success"><i class="fas fa-check mr-1"></i> <?php echo e(trans('admin/main.configured') ?? 'Configured'); ?></span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?php echo e(getAdminPanelUrl()); ?>/course-health-log-settings/<?php echo e($w->id); ?>/edit" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-cog"></i> <?php echo e(trans('admin/main.settings') ?? 'Settings'); ?>

                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted"><?php echo e(trans('admin/main.no_courses') ?? 'No courses found.'); ?></td>
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

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\ashut\Downloads\Telegram Desktop\rocket-lms_v1.8\backend\resources\views/admin/course_health_log_settings/index.blade.php ENDPATH**/ ?>