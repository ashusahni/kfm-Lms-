

<?php $__env->startSection('content'); ?>
    <section class="section">
        <div class="section-header">
            <h1><?php echo e($pageTitle); ?></h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="<?php echo e(getAdminPanelUrl()); ?>"><?php echo e(trans('admin/main.dashboard')); ?></a></div>
                <div class="breadcrumb-item"><a href="<?php echo e(getAdminPanelUrl()); ?>/health-log"><?php echo e(trans('admin/main.health_log') ?? 'Health Log'); ?></a></div>
                <div class="breadcrumb-item">#<?php echo e($log->id); ?></div>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4><?php echo e(trans('admin/main.log') ?? 'Log'); ?> #<?php echo e($log->id); ?></h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong><?php echo e(trans('admin/main.student') ?? 'Student'); ?>:</strong> <?php echo e(optional($log->user)->full_name ?? '—'); ?> (ID: <?php echo e($log->user_id); ?>)</p>
                            <p><strong><?php echo e(trans('admin/main.email') ?? 'Email'); ?>:</strong> <?php echo e(optional($log->user)->email ?? '—'); ?></p>
                            <p><strong><?php echo e(trans('admin/main.course') ?? 'Course'); ?>:</strong> <?php echo e(optional($log->webinar)->title ?? '—'); ?> <?php if($log->webinar_id): ?>(ID: <?php echo e($log->webinar_id); ?>)<?php endif; ?></p>
                            <p><strong><?php echo e(trans('admin/main.log_date') ?? 'Log date'); ?>:</strong> <?php echo e($log->log_date ? (\Carbon\Carbon::parse($log->log_date)->format('Y-m-d')) : '—'); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong><?php echo e(trans('admin/main.water') ?? 'Water'); ?> (ml):</strong> <?php echo e($log->water_ml ?? '—'); ?></p>
                            <p><strong><?php echo e(trans('admin/main.calories') ?? 'Calories'); ?>:</strong> <?php echo e($log->calories ?? '—'); ?></p>
                            <p><strong><?php echo e(trans('admin/main.protein') ?? 'Protein'); ?> / <?php echo e(trans('admin/main.carbs') ?? 'Carbs'); ?> / <?php echo e(trans('admin/main.fat') ?? 'Fat'); ?> (g):</strong> <?php echo e($log->protein ?? '—'); ?> / <?php echo e($log->carbs ?? '—'); ?> / <?php echo e($log->fat ?? '—'); ?></p>
                            <p><strong><?php echo e(trans('admin/main.activity') ?? 'Activity'); ?> (min):</strong> <?php echo e($log->activity_minutes ?? '—'); ?></p>
                            <p><strong><?php echo e(trans('admin/main.adherence') ?? 'Adherence'); ?> %:</strong> <?php echo e($log->adherence_score !== null ? $log->adherence_score . '%' : '—'); ?></p>
                        </div>
                    </div>
                    <?php if($log->activity_notes): ?>
                        <p><strong><?php echo e(trans('admin/main.activity_notes') ?? 'Activity notes'); ?>:</strong><br><?php echo e($log->activity_notes); ?></p>
                    <?php endif; ?>
                    <?php if($log->medicines): ?>
                        <p><strong><?php echo e(trans('admin/main.medicines_supplements') ?? 'Medicines / supplements'); ?>:</strong><br><?php echo e($log->medicines); ?></p>
                    <?php endif; ?>
                    <?php if(!empty($log->meals) && is_array($log->meals)): ?>
                        <p><strong><?php echo e(trans('admin/main.meals') ?? 'Meals'); ?>:</strong></p>
                        <pre class="bg-light p-3 rounded font-12"><?php echo e(json_encode($log->meals, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
                    <?php endif; ?>
                    <?php if(!empty($log->custom_data) && is_array($log->custom_data)): ?>
                        <p><strong><?php echo e(trans('admin/main.custom_data') ?? 'Custom data'); ?>:</strong></p>
                        <pre class="bg-light p-3 rounded font-12"><?php echo e(json_encode($log->custom_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <a href="<?php echo e(getAdminPanelUrl()); ?>/health-log?<?php echo e(http_build_query(request()->query())); ?>" class="btn btn-secondary"><?php echo e(trans('public.back') ?? 'Back'); ?></a>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\ashut\Downloads\Telegram Desktop\rocket-lms_v1.8\backend\resources\views/admin/health_log/show.blade.php ENDPATH**/ ?>