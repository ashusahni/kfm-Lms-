<?php
    $userLanguages = !empty($generalSettings['site_language']) ? [$generalSettings['site_language'] => getLanguages($generalSettings['site_language'])] : [];

    if (!empty($generalSettings['user_languages']) and is_array($generalSettings['user_languages'])) {
        $userLanguages = getLanguages($generalSettings['user_languages']);
    }

    $localLanguage = [];

    foreach($userLanguages as $key => $userLanguage) {
        $localLanguage[localeToCountryCode($key)] = $userLanguage;
    }

?>

<div class="top-navbar top-navbar-modern d-flex border-bottom">
    <div class="container d-flex justify-content-between align-items-center flex-column flex-lg-row py-0">
        <div class="top-nav-left d-flex flex-column flex-md-row align-items-center justify-content-center flex-grow-1">

            <?php if(getOthersPersonalizationSettings('platform_phone_and_email_position') == 'header'): ?>
                <div class="d-flex align-items-center flex-wrap gap-3">
                    <?php if(!empty($generalSettings['site_phone'])): ?>
                        <a href="tel:<?php echo e(preg_replace('/\s+/', '', $generalSettings['site_phone'])); ?>" class="top-nav-link text-decoration-none">
                            <i data-feather="phone" width="16" height="16"></i>
                            <?php echo e($generalSettings['site_phone']); ?>

                        </a>
                    <?php endif; ?>

                    <?php if(!empty($generalSettings['site_email'])): ?>
                        <a href="mailto:<?php echo e($generalSettings['site_email']); ?>" class="top-nav-link text-decoration-none">
                            <i data-feather="mail" width="16" height="16"></i>
                            <?php echo e($generalSettings['site_email']); ?>

                        </a>
                    <?php endif; ?>
                </div>
                <?php if(!empty($generalSettings['site_phone']) || !empty($generalSettings['site_email'])): ?>
                    <span class="border-sep d-none d-md-block"></span>
                <?php endif; ?>
            <?php endif; ?>

            <div class="d-flex align-items-center flex-wrap">
                <?php echo $__env->make('web.default.includes.top_nav.currency', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                <?php if(!empty($localLanguage) and count($localLanguage) > 1): ?>
                    <span class="border-sep"></span>
                    <form action="/locale" method="post" class="d-inline">
                        <?php echo e(csrf_field()); ?>

                        <input type="hidden" name="locale">
                        <?php if(!empty($previousUrl)): ?>
                            <input type="hidden" name="previous_url" value="<?php echo e($previousUrl); ?>">
                        <?php endif; ?>
                        <div class="language-select">
                            <div id="localItems"
                                 data-selected-country="<?php echo e(localeToCountryCode(mb_strtoupper(app()->getLocale()))); ?>"
                                 data-countries='<?php echo e(json_encode($localLanguage)); ?>'
                            ></div>
                        </div>
                    </form>
                <?php endif; ?>

                <span class="border-sep"></span>
                <form action="/search" method="get" class="navbar-search-modern form-inline my-0">
                    <button type="submit" class="search-icon-btn" aria-label="Search">
                        <i data-feather="search" width="18" height="18"></i>
                    </button>
                    <input class="form-control border-0" type="text" name="search" placeholder="<?php echo e(trans('navbar.search_anything')); ?>" aria-label="Search">
                </form>
            </div>
        </div>

        <div class="top-nav-right xs-w-100 d-flex align-items-center justify-content-between justify-content-lg-end">
            <?php echo $__env->make(getTemplate().'.includes.shopping-cart-dropdwon', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <span class="border-sep"></span>
            <?php echo $__env->make(getTemplate().'.includes.notification-dropdown', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php echo $__env->make('web.default.includes.top_nav.user_menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts_bottom'); ?>
    <link href="/assets/default/vendors/flagstrap/css/flags.css" rel="stylesheet">
    <script src="/assets/default/vendors/flagstrap/js/jquery.flagstrap.min.js"></script>
    <script src="/assets/default/js/parts/top_nav_flags.min.js"></script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\Users\ashut\Downloads\Telegram Desktop\rocket-lms_v1.8\Source\Source\resources\views/web/default/includes/top_nav.blade.php ENDPATH**/ ?>