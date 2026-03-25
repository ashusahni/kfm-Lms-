<?php $__env->startSection('content'); ?>
    <section class="section">
        <div class="section-header">
            <h1><?php echo e($pageTitle); ?></h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="<?php echo e(getAdminPanelUrl()); ?>"><?php echo e(trans('admin/main.dashboard')); ?></a></div>
                <div class="breadcrumb-item"><a href="<?php echo e(getAdminPanelUrl()); ?>/system-health"><?php echo e(trans('admin/main.system_health') ?? 'System health'); ?></a></div>
                <div class="breadcrumb-item">#<?php echo e($healthLog->id); ?></div>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4><?php echo e(trans('admin/main.health_log_detail') ?? 'Log detail'); ?> #<?php echo e($healthLog->id); ?></h4>
                </div>
                <div class="card-body">
                    <p><strong><?php echo e(trans('admin/main.check_name') ?? 'Check'); ?>:</strong> <?php echo e($healthLog->check_name); ?></p>
                    <p><strong><?php echo e(trans('admin/main.status') ?? 'Status'); ?>:</strong>
                        <?php if($healthLog->status === 'ok'): ?>
                            <span class="badge badge-success">OK</span>
                        <?php elseif($healthLog->status === 'warning'): ?>
                            <span class="badge badge-warning">Warning</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Failed</span>
                        <?php endif; ?>
                    </p>
                    <p><strong><?php echo e(trans('site.message')); ?>:</strong><br><?php echo e($healthLog->message ?? '—'); ?></p>
                    <?php if(!empty($healthLog->meta)): ?>
                        <p><strong>Meta:</strong></p>
                        <pre class="bg-light p-3 rounded font-12"><?php echo e(json_encode($healthLog->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
                    <?php endif; ?>
                    <p class="text-muted font-14 mb-0"><strong><?php echo e(trans('admin/main.created_at') ?? 'Created'); ?>:</strong> <?php echo e($healthLog->created_at ? dateTimeFormat($healthLog->created_at, 'j M Y H:i:s') : '—'); ?></p>
                </div>
                <div class="card-footer">
                    <a href="<?php echo e(getAdminPanelUrl()); ?>/system-health" class="btn btn-secondary"><?php echo e(trans('public.back') ?? 'Back'); ?></a>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\ashut\Downloads\Telegram Desktop\rocket-lms_v1.8\backend\resources\views/admin/system_health/show.blade.php ENDPATH**/ ?>