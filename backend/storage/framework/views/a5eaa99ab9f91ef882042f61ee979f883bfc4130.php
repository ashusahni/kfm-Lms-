

<?php $__env->startSection('content'); ?>
    <section class="section">
        <div class="section-header">
            <h1><?php echo e($pageTitle); ?> #<?php echo e($healthLog->id); ?></h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="<?php echo e(getAdminPanelUrl()); ?>"><?php echo e(trans('admin/main.dashboard')); ?></a></div>
                <div class="breadcrumb-item"><a href="<?php echo e(getAdminPanelUrl()); ?>/health-log"><?php echo e(trans('admin/main.health_log') ?? 'Health Log'); ?></a></div>
                <div class="breadcrumb-item">#<?php echo e($healthLog->id); ?></div>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered font-14">
                        <tr>
                            <th width="160">ID</th>
                            <td><?php echo e($healthLog->id); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo e(trans('admin/main.check_name') ?? 'Check name'); ?></th>
                            <td><?php echo e($healthLog->check_name); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo e(trans('admin/main.status')); ?></th>
                            <td>
                                <?php if($healthLog->status === 'ok'): ?>
                                    <span class="badge badge-success">OK</span>
                                <?php elseif($healthLog->status === 'warning'): ?>
                                    <span class="badge badge-warning">Warning</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Failed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo e(trans('site.message')); ?></th>
                            <td><?php echo e($healthLog->message ?? '—'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo e(trans('admin/main.created_at')); ?></th>
                            <td><?php echo e($healthLog->created_at ? dateTimeFormat($healthLog->created_at, 'j M Y H:i') : '—'); ?></td>
                        </tr>
                        <?php if(!empty($healthLog->meta)): ?>
                            <tr>
                                <th>Meta (JSON)</th>
                                <td><pre class="mb-0 p-3 bg-light rounded font-12"><?php echo e(json_encode($healthLog->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); ?></pre></td>
                            </tr>
                        <?php endif; ?>
                    </table>
                    <div class="mt-3">
                        <a href="<?php echo e(getAdminPanelUrl()); ?>/health-log" class="btn btn-secondary"><?php echo e(trans('public.back')); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\ashut\Downloads\Telegram Desktop\rocket-lms_v1.8\backend\resources\views/admin/health_log/show.blade.php ENDPATH**/ ?>