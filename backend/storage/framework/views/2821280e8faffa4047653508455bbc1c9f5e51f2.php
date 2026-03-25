<?php
$spaPath = public_path('spa/index.html');
?>
<?php if(config('frontend.serve_react', false) && file_exists($spaPath)): ?>
<?php echo file_get_contents($spaPath); ?>

<?php else: ?>
<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Rocket LMS</title>
</head>
<body>
    <div id="root"></div>
    <script type="module" src="<?php echo e(asset('spa/assets/main.js')); ?>"></script>
</body>
</html>
<?php endif; ?>
<?php /**PATH C:\Users\ashut\Downloads\Telegram Desktop\rocket-lms_v1.8\backend\resources\views/spa.blade.php ENDPATH**/ ?>