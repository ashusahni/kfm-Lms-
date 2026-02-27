<?php $__env->startPush('libraries_top'); ?>
    <link rel="stylesheet" href="/assets/default/vendors/chartjs/chart.min.css">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <section class="section">
        <div class="section-header">
            <h1><?php echo e($pageTitle); ?></h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="<?php echo e(getAdminPanelUrl()); ?>"><?php echo e(trans('admin/main.dashboard')); ?></a></div>
                <div class="breadcrumb-item"><a href="<?php echo e(getAdminPanelUrl()); ?>/health-log"><?php echo e(trans('admin/main.health_log') ?? 'Health Log'); ?></a></div>
                <div class="breadcrumb-item"><?php echo e($pageTitle); ?></div>
            </div>
        </div>

        <div class="section-body">
            <div class="row mb-3">
                <div class="col-12">
                    <a href="<?php echo e(getAdminPanelUrl()); ?>/health-log" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> <?php echo e(trans('admin/main.back_to_health_log') ?? 'Back to Health Log'); ?></a>
                </div>
            </div>

            
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary"><i class="fas fa-clipboard-list"></i></div>
                        <div class="card-wrap">
                            <div class="card-header"><h4><?php echo e(trans('admin/main.health_log_total') ?? 'Total checks'); ?></h4></div>
                            <div class="card-body"><?php echo e($stats['total'] ?? 0); ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success"><i class="fas fa-check-circle"></i></div>
                        <div class="card-wrap">
                            <div class="card-header"><h4><?php echo e(trans('admin/main.health_status_ok') ?? 'OK'); ?></h4></div>
                            <div class="card-body"><?php echo e($stats['ok'] ?? 0); ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></div>
                        <div class="card-wrap">
                            <div class="card-header"><h4><?php echo e(trans('admin/main.health_status_warning') ?? 'Warning'); ?></h4></div>
                            <div class="card-body"><?php echo e($stats['warning'] ?? 0); ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-danger"><i class="fas fa-times-circle"></i></div>
                        <div class="card-wrap">
                            <div class="card-header"><h4><?php echo e(trans('admin/main.health_status_failed') ?? 'Failed'); ?></h4></div>
                            <div class="card-body"><?php echo e($stats['failed'] ?? 0); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header flex-wrap">
                            <h4 class="mb-0"><?php echo e(trans('admin/main.health_log_actions') ?? 'Actions & APIs'); ?></h4>
                            <div class="card-header-action ml-auto">
                                <form method="post" action="<?php echo e(getAdminPanelUrl()); ?>/system-health/run-check" class="d-inline" id="health-log-run-form">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-primary btn-lg btn-icon icon-left" id="btn-run-check">
                                        <i class="fas fa-play"></i> <?php echo e(trans('admin/main.run_health_check') ?? 'Run health check'); ?>

                                    </button>
                                </form>
                                <div class="btn-group ml-2">
                                    <a href="<?php echo e(getAdminPanelUrl()); ?>/system-health/export/csv?<?php echo e(http_build_query(request()->query())); ?>" class="btn btn-outline-secondary">CSV</a>
                                    <a href="<?php echo e(getAdminPanelUrl()); ?>/system-health/export/json?<?php echo e(http_build_query(request()->query())); ?>" class="btn btn-outline-secondary">JSON</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <?php if(!empty($stats['latest_at'])): ?>
                                <p class="text-muted mb-0 font-14">
                                    <?php echo e(trans('admin/main.last_check_at') ?? 'Last check'); ?>: <?php echo e(dateTimeFormat($stats['latest_at'], 'j M Y H:i')); ?>

                                    &nbsp;|&nbsp; <?php echo e(trans('admin/main.checks_last_24h') ?? 'Checks in last 24h'); ?>: <?php echo e($stats['last_24h'] ?? 0); ?>

                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if(!empty($chartData) && (($chartData['donut']['ok'] ?? 0) + ($chartData['donut']['warning'] ?? 0) + ($chartData['donut']['failed'] ?? 0)) > 0): ?>
            <div class="row">
                <div class="col-lg-6 col-md-12">
                    <div class="card">
                        <div class="card-header"><h4><?php echo e(trans('admin/main.health_status_distribution') ?? 'Status distribution'); ?></h4></div>
                        <div class="card-body"><canvas id="healthLogDonutChart" height="200"></canvas></div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12">
                    <div class="card">
                        <div class="card-header"><h4><?php echo e(trans('admin/main.health_trend_14_days') ?? 'Last 14 days'); ?></h4></div>
                        <div class="card-body"><canvas id="healthLogTrendChart" height="200"></canvas></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header"><h4><?php echo e(trans('admin/main.health_log_list') ?? 'Health log entries'); ?></h4></div>
                <div class="card-body">
                    <form method="get" action="<?php echo e(getAdminPanelUrl()); ?>/system-health" class="form-inline flex-wrap mb-4">
                        <input type="date" name="date_from" class="form-control form-control-sm mr-2 mb-2" value="<?php echo e(request('date_from')); ?>">
                        <input type="date" name="date_to" class="form-control form-control-sm mr-2 mb-2" value="<?php echo e(request('date_to')); ?>">
                        <select name="status" class="form-control form-control-sm mr-2 mb-2">
                            <option value=""><?php echo e(trans('admin/main.all_statuses') ?? 'All'); ?></option>
                            <option value="ok" <?php echo e(request('status') == 'ok' ? 'selected' : ''); ?>>OK</option>
                            <option value="warning" <?php echo e(request('status') == 'warning' ? 'selected' : ''); ?>>Warning</option>
                            <option value="failed" <?php echo e(request('status') == 'failed' ? 'selected' : ''); ?>>Failed</option>
                        </select>
                        <input type="text" name="check_name" class="form-control form-control-sm mr-2 mb-2" placeholder="<?php echo e(trans('admin/main.check_name') ?? 'Check name'); ?>" value="<?php echo e(request('check_name')); ?>">
                        <button type="submit" class="btn btn-sm btn-primary mb-2"><i class="fas fa-filter"></i> <?php echo e(trans('admin/main.filter')); ?></button>
                        <?php if(request()->hasAny(['date_from','date_to','status','check_name'])): ?>
                            <a href="<?php echo e(getAdminPanelUrl()); ?>/system-health" class="btn btn-sm btn-outline-secondary ml-2 mb-2"><?php echo e(trans('public.clear') ?? 'Clear'); ?></a>
                        <?php endif; ?>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover font-14">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th><?php echo e(trans('admin/main.check_name') ?? 'Check'); ?></th>
                                    <th class="text-center"><?php echo e(trans('admin/main.status')); ?></th>
                                    <th><?php echo e(trans('site.message')); ?></th>
                                    <th class="text-center"><?php echo e(trans('admin/main.created_at')); ?></th>
                                    <th class="text-center" width="80"><?php echo e(trans('public.controls')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $healthLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($log->id); ?></td>
                                        <td><?php echo e($log->check_name); ?></td>
                                        <td class="text-center">
                                            <?php if($log->status === 'ok'): ?>
                                                <span class="badge badge-success">OK</span>
                                            <?php elseif($log->status === 'warning'): ?>
                                                <span class="badge badge-warning">Warning</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Failed</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e(Str::limit($log->message, 60)); ?></td>
                                        <td class="text-center"><?php echo e($log->created_at ? dateTimeFormat($log->created_at, 'j M Y H:i') : '—'); ?></td>
                                        <td class="text-center">
                                            <a href="<?php echo e(getAdminPanelUrl()); ?>/system-health/<?php echo e($log->id); ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <?php echo e(trans('admin/main.health_log_no_entries') ?? 'No entries yet.'); ?>

                                            <form method="post" action="<?php echo e(getAdminPanelUrl()); ?>/system-health/run-check" class="d-inline mt-2"><?php echo csrf_field(); ?>
                                                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-play mr-1"></i> <?php echo e(trans('admin/main.run_health_check') ?? 'Run check'); ?></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php if($healthLogs->hasPages()): ?>
                <div class="card-footer text-center"><?php echo e($healthLogs->appends(request()->input())->links()); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts_bottom'); ?>
    <script src="/assets/default/vendors/chartjs/chart.min.js"></script>
    <script>
        (function() {
            var runForm = document.getElementById('health-log-run-form');
            if (runForm) {
                runForm.addEventListener('submit', function() {
                    var btn = document.getElementById('btn-run-check');
                    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Running...'; }
                });
            }
            <?php if(!empty($chartData) && (($chartData['donut']['ok'] ?? 0) + ($chartData['donut']['warning'] ?? 0) + ($chartData['donut']['failed'] ?? 0)) > 0): ?>
            var donutCtx = document.getElementById('healthLogDonutChart');
            if (donutCtx) {
                new Chart(donutCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['OK', 'Warning', 'Failed'],
                        datasets: [{
                            data: [<?php echo e($chartData['donut']['ok'] ?? 0); ?>, <?php echo e($chartData['donut']['warning'] ?? 0); ?>, <?php echo e($chartData['donut']['failed'] ?? 0); ?>],
                            backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                            borderWidth: 0
                        }]
                    },
                    options: { responsive: true, legend: { position: 'bottom' } }
                });
            }
            var trendCtx = document.getElementById('healthLogTrendChart');
            if (trendCtx) {
                new Chart(trendCtx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: <?php echo json_encode($chartData['labels'] ?? [], 15, 512) ?>,
                        datasets: [
                            { label: 'OK', data: <?php echo json_encode($chartData['ok'] ?? [], 15, 512) ?>, borderColor: '#28a745', backgroundColor: 'rgba(40,167,69,0.1)', fill: true, tension: 0.3 },
                            { label: 'Warning', data: <?php echo json_encode($chartData['warning'] ?? [], 15, 512) ?>, borderColor: '#ffc107', backgroundColor: 'rgba(255,193,7,0.1)', fill: true, tension: 0.3 },
                            { label: 'Failed', data: <?php echo json_encode($chartData['failed'] ?? [], 15, 512) ?>, borderColor: '#dc3545', backgroundColor: 'rgba(220,53,69,0.1)', fill: true, tension: 0.3 }
                        ]
                    },
                    options: { responsive: true, scales: { yAxes: [{ ticks: { beginAtZero: true } }] }, legend: { position: 'bottom' } }
                });
            }
            <?php endif; ?>
        })();
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\ashut\Downloads\Telegram Desktop\rocket-lms_v1.8\backend\resources\views/admin/system_health/index.blade.php ENDPATH**/ ?>