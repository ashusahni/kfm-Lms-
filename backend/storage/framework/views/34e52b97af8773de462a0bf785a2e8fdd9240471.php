

<?php $__env->startPush('libraries_top'); ?>
    <link rel="stylesheet" href="/assets/default/vendors/chartjs/chart.min.css">
<?php $__env->stopPush(); ?>

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
            
            <div class="row mb-4">
                <div class="col-12">
                    <div class="btn-group">
                        <a href="<?php echo e(getAdminPanelUrl()); ?>/course-health-log-settings" class="btn btn-outline-primary">
                            <i class="fas fa-book-medical"></i> <?php echo e(trans('admin/main.course_health_log_settings') ?? 'Course health log settings'); ?>

                        </a>
                        <a href="<?php echo e(getAdminPanelUrl()); ?>/system-health" class="btn btn-outline-secondary">
                            <i class="fas fa-server"></i> <?php echo e(trans('admin/main.system_health') ?? 'System health'); ?>

                        </a>
                    </div>
                </div>
            </div>

            
            <div class="row">
                <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary"><i class="fas fa-clipboard-list"></i></div>
                        <div class="card-wrap">
                            <div class="card-header"><h4><?php echo e(trans('admin/main.total_logs') ?? 'Total logs'); ?></h4></div>
                            <div class="card-body"><?php echo e($stats['total'] ?? 0); ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-info"><i class="fas fa-users"></i></div>
                        <div class="card-wrap">
                            <div class="card-header"><h4><?php echo e(trans('admin/main.unique_students') ?? 'Unique students'); ?></h4></div>
                            <div class="card-body"><?php echo e($stats['unique_users'] ?? 0); ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success"><i class="fas fa-book"></i></div>
                        <div class="card-wrap">
                            <div class="card-header"><h4><?php echo e(trans('admin/main.logs_with_course') ?? 'With course'); ?></h4></div>
                            <div class="card-body"><?php echo e($stats['with_course'] ?? 0); ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning"><i class="fas fa-percentage"></i></div>
                        <div class="card-wrap">
                            <div class="card-header"><h4><?php echo e(trans('admin/main.avg_adherence') ?? 'Avg adherence %'); ?></h4></div>
                            <div class="card-body"><?php echo e($stats['avg_adherence'] ?? '—'); ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary"><i class="fas fa-tint"></i></div>
                        <div class="card-wrap">
                            <div class="card-header"><h4><?php echo e(trans('admin/main.avg_water') ?? 'Avg water (ml)'); ?></h4></div>
                            <div class="card-body"><?php echo e($stats['avg_water'] ?? '—'); ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-info"><i class="fas fa-fire"></i></div>
                        <div class="card-wrap">
                            <div class="card-header"><h4><?php echo e(trans('admin/main.avg_calories') ?? 'Avg calories'); ?></h4></div>
                            <div class="card-body"><?php echo e($stats['avg_calories'] ?? '—'); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            
            <?php if(!empty($chartData['labels']) && (array_sum($chartData['count'] ?? []) > 0 || array_filter($chartData['adherence'] ?? []))): ?>
            <div class="row">
                <div class="col-lg-6 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4><?php echo e(trans('admin/main.logs_per_day') ?? 'Logs per day'); ?></h4>
                        </div>
                        <div class="card-body">
                            <canvas id="healthLogCountChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4><?php echo e(trans('admin/main.adherence_trend') ?? 'Adherence trend (%)'); ?></h4>
                        </div>
                        <div class="card-body">
                            <canvas id="healthLogAdherenceChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            
            <div class="card">
                <div class="card-header flex-wrap">
                    <h4><?php echo e(trans('admin/main.student_health_logs') ?? 'Student health logs'); ?></h4>
                    <div class="card-header-action ml-auto">
                        <a href="<?php echo e(getAdminPanelUrl()); ?>/health-log/export/csv?<?php echo e(http_build_query(request()->query())); ?>" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-file-csv"></i> <?php echo e(trans('admin/main.export_csv') ?? 'Export CSV'); ?>

                        </a>
                        <a href="<?php echo e(getAdminPanelUrl()); ?>/health-log/export/json?<?php echo e(http_build_query(request()->query())); ?>" class="btn btn-outline-secondary btn-sm ml-1">
                            <i class="fas fa-file-code"></i> <?php echo e(trans('admin/main.export_json') ?? 'Export JSON'); ?>

                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="get" action="<?php echo e(getAdminPanelUrl()); ?>/health-log" class="form-inline flex-wrap mb-4">
                        <label class="mr-2 mb-2 mb-md-0"><?php echo e(trans('admin/main.course') ?? 'Course'); ?>:</label>
                        <select name="webinar_id" class="form-control form-control-sm mr-2 mb-2">
                            <option value=""><?php echo e(trans('admin/main.all_courses') ?? 'All courses'); ?></option>
                            <?php $__currentLoopData = $coursesWithLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($c->id); ?>" <?php echo e(request('webinar_id') == $c->id ? 'selected' : ''); ?>><?php echo e(\Str::limit($c->title, 40)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <label class="mr-2 mb-2 mb-md-0"><?php echo e(trans('admin/main.user_id') ?? 'User ID'); ?>:</label>
                        <input type="number" name="user_id" class="form-control form-control-sm mr-2 mb-2" placeholder="ID" value="<?php echo e(request('user_id')); ?>" style="width:90px">
                        <label class="mr-2 mb-2 mb-md-0"><?php echo e(trans('admin/main.from_date') ?? 'From'); ?>:</label>
                        <input type="date" name="from_date" class="form-control form-control-sm mr-2 mb-2" value="<?php echo e(request('from_date')); ?>">
                        <label class="mr-2 mb-2 mb-md-0"><?php echo e(trans('admin/main.to_date') ?? 'To'); ?>:</label>
                        <input type="date" name="to_date" class="form-control form-control-sm mr-2 mb-2" value="<?php echo e(request('to_date')); ?>">
                        <button type="submit" class="btn btn-sm btn-primary mb-2"><i class="fas fa-filter"></i> <?php echo e(trans('admin/main.filter')); ?></button>
                        <?php if(request()->hasAny(['webinar_id','user_id','from_date','to_date'])): ?>
                            <a href="<?php echo e(getAdminPanelUrl()); ?>/health-log" class="btn btn-sm btn-outline-secondary ml-2 mb-2"><?php echo e(trans('public.clear') ?? 'Clear'); ?></a>
                        <?php endif; ?>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover font-14">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th><?php echo e(trans('admin/main.student') ?? 'Student'); ?></th>
                                    <th><?php echo e(trans('admin/main.course') ?? 'Course'); ?></th>
                                    <th><?php echo e(trans('admin/main.log_date') ?? 'Date'); ?></th>
                                    <th class="text-center"><?php echo e(trans('admin/main.water') ?? 'Water'); ?></th>
                                    <th class="text-center"><?php echo e(trans('admin/main.calories') ?? 'Cal'); ?></th>
                                    <th class="text-center"><?php echo e(trans('admin/main.activity') ?? 'Activity'); ?></th>
                                    <th class="text-center"><?php echo e(trans('admin/main.adherence') ?? 'Adherence'); ?></th>
                                    <th class="text-center" width="80"><?php echo e(trans('public.controls')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($log->id); ?></td>
                                        <td><?php echo e(optional($log->user)->full_name ?? $log->user_id); ?> <small class="text-muted">#<?php echo e($log->user_id); ?></small></td>
                                        <td><?php echo e(optional($log->webinar)->title ?? '—'); ?></td>
                                        <td><?php echo e($log->log_date ? (\Carbon\Carbon::parse($log->log_date)->format('Y-m-d')) : '—'); ?></td>
                                        <td class="text-center"><?php echo e($log->water_ml ?? '—'); ?></td>
                                        <td class="text-center"><?php echo e($log->calories ?? '—'); ?></td>
                                        <td class="text-center"><?php echo e($log->activity_minutes ?? '—'); ?> min</td>
                                        <td class="text-center"><?php echo e($log->adherence_score !== null ? $log->adherence_score . '%' : '—'); ?></td>
                                        <td class="text-center">
                                            <a href="<?php echo e(getAdminPanelUrl()); ?>/health-log/<?php echo e($log->id); ?>" class="btn btn-sm btn-outline-primary" title="<?php echo e(trans('admin/main.show')); ?>"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-5 text-muted">
                                            <i class="fas fa-clipboard-list fa-2x mb-2 opacity-50"></i>
                                            <p class="mb-0"><?php echo e(trans('admin/main.no_student_health_logs') ?? 'No student health logs yet.'); ?></p>
                                            <p class="small mt-1"><?php echo e(trans('admin/main.students_log_in_panel') ?? 'Students log daily entries in their panel.'); ?></p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php if($logs->hasPages()): ?>
                <div class="card-footer text-center">
                    <?php echo e($logs->appends(request()->input())->links()); ?>

                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts_bottom'); ?>
    <script src="/assets/default/vendors/chartjs/chart.min.js"></script>
    <script>
        (function() {
            <?php if(!empty($chartData['labels'])): ?>
            var labels = <?php echo json_encode($chartData['labels'] ?? [], 15, 512) ?>;
            var countData = <?php echo json_encode($chartData['count'] ?? [], 15, 512) ?>;
            var adherenceData = <?php echo json_encode($chartData['adherence'] ?? [], 15, 512) ?>;

            var countCtx = document.getElementById('healthLogCountChart');
            if (countCtx && countData.length) {
                new Chart(countCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: '<?php echo e(trans("admin/main.logs") ?? "Logs"); ?>',
                            data: countData,
                            backgroundColor: 'rgba(40, 167, 69, 0.6)',
                            borderColor: '#28a745',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        scales: {
                            yAxes: [{ ticks: { beginAtZero: true, stepSize: 1 } }]
                        },
                        legend: { display: false }
                    }
                });
            }
            var adherenceCtx = document.getElementById('healthLogAdherenceChart');
            if (adherenceCtx && adherenceData.length) {
                new Chart(adherenceCtx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: '<?php echo e(trans("admin/main.avg_adherence") ?? "Avg adherence %"); ?>',
                            data: adherenceData,
                            borderColor: '#007bff',
                            backgroundColor: 'rgba(0, 123, 255, 0.1)',
                            fill: true,
                            tension: 0.3,
                            spanGaps: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        scales: {
                            yAxes: [{ ticks: { beginAtZero: true, max: 100 } }]
                        },
                        legend: { display: false }
                    }
                });
            }
            <?php endif; ?>
        })();
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\ashut\Downloads\Telegram Desktop\rocket-lms_v1.8\backend\resources\views/admin/health_log/index.blade.php ENDPATH**/ ?>