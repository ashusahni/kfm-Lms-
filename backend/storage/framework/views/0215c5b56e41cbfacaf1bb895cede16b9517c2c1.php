<?php
    if (empty($authUser) and auth()->check()) {
        $authUser = auth()->user();
    }

    $navBtnUrl = null;
    $navBtnText = null;

    $navbarButton = getNavbarButton(!empty($authUser) ? $authUser->role_id : null, empty($authUser));
    if (!empty($navbarButton)) {
        $navBtnUrl = $navbarButton->url;
        $navBtnText = $navbarButton->title;
    }
?>

<div id="navbarVacuum"></div>
<nav id="navbar" class="navbar navbar-modern navbar-expand-lg">
    <div class="<?php echo e((!empty($isPanel) and $isPanel) ? 'container-fluid' : 'container'); ?>">
        <div class="navbar-modern-inner">

            <a class="navbar-brand-modern navbar-order <?php echo e((empty($navBtnUrl) and empty($navBtnText)) ? 'ml-auto' : ''); ?>" href="/">
                <?php if(!empty($generalSettings['logo'])): ?>
                    <img src="<?php echo e($generalSettings['logo']); ?>" class="img-cover" alt="site logo">
                <?php else: ?>
                    <span class="font-weight-600 text-dark"><?php echo e(!empty($generalSettings['site_name']) ? $generalSettings['site_name'] : 'Logo'); ?></span>
                <?php endif; ?>
            </a>

            <button class="navbar-toggler-modern navbar-order d-lg-none" type="button" id="navbarToggle" aria-label="Toggle menu">
                <i data-feather="menu" width="22" height="22"></i>
            </button>

            <div class="nav-main navbar-toggle-content d-lg-flex" id="navbarContent">
                <div class="navbar-toggle-header text-right d-lg-none">
                    <button type="button" class="btn btn-transparent p-0" id="navbarClose" aria-label="Close">
                        <i data-feather="x" width="28" height="28"></i>
                    </button>
                </div>

                <ul class="navbar-nav mr-auto d-flex align-items-center list-unstyled mb-0 pl-0">
                    <?php if(!empty($categories) and count($categories)): ?>
                        <li class="mr-lg-25">
                            <div class="menu-category">
                                <ul class="list-unstyled mb-0">
                                    <li class="cursor-pointer user-select-none d-flex xs-categories-toggle nav-link-modern">
                                        <i data-feather="grid" width="18" height="18"></i>
                                        <?php echo e(trans('categories.categories')); ?>


                                        <ul class="cat-dropdown-menu">
                                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li>
                                                    <a href="<?php echo e($category->getUrl()); ?>" class="<?php echo e((!empty($category->subCategories) and count($category->subCategories)) ? 'js-has-subcategory' : ''); ?>">
                                                        <div class="d-flex align-items-center">
                                                            <img src="<?php echo e($category->icon); ?>" class="cat-dropdown-menu-icon mr-10" alt="<?php echo e($category->title); ?> icon">
                                                            <?php echo e($category->title); ?>

                                                        </div>

                                                        <?php if(!empty($category->subCategories) and count($category->subCategories)): ?>
                                                            <i data-feather="chevron-right" width="16" height="16" class="d-none d-lg-inline-block ml-10"></i>
                                                            <i data-feather="chevron-down" width="16" height="16" class="d-inline-block d-lg-none"></i>
                                                        <?php endif; ?>
                                                    </a>

                                                    <?php if(!empty($category->subCategories) and count($category->subCategories)): ?>
                                                        <ul class="sub-menu" data-simplebar <?php if((!empty($isRtl) and $isRtl)): ?> data-simplebar-direction="rtl" <?php endif; ?>>
                                                            <?php $__currentLoopData = $category->subCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subCategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <li>
                                                                    <a href="<?php echo e($subCategory->getUrl()); ?>">
                                                                        <?php if(!empty($subCategory->icon)): ?>
                                                                            <img src="<?php echo e($subCategory->icon); ?>" class="cat-dropdown-menu-icon mr-10" alt="<?php echo e($subCategory->title); ?> icon">
                                                                        <?php endif; ?>

                                                                        <?php echo e($subCategory->title); ?>

                                                                    </a>
                                                                </li>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </ul>
                                                    <?php endif; ?>
                                                </li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    <?php endif; ?>

                    <?php if(!empty($navbarPages) and count($navbarPages)): ?>
                        <?php $__currentLoopData = $navbarPages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $navbarPage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="nav-item">
                                <a class="nav-link-modern" href="<?php echo e($navbarPage['link']); ?>"><?php echo e($navbarPage['title']); ?></a>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="nav-actions navbar-order d-flex align-items-center">
                <?php if(!empty($navBtnUrl)): ?>
                    <a href="<?php echo e($navBtnUrl); ?>" class="btn-nav-cta d-none d-lg-inline-flex">
                        <?php echo e($navBtnText); ?>

                    </a>
                    <a href="<?php echo e($navBtnUrl); ?>" class="nav-link-modern d-flex d-lg-none">
                        <?php echo e($navBtnText); ?>

                    </a>
                <?php endif; ?>

                <?php if(!empty($isPanel) && !empty($authUser) && $authUser->checkAccessToAIContentFeature()): ?>
                    <div class="js-show-ai-content-drawer show-ai-content-drawer-btn d-flex align-items-center mr-3">
                        <div class="d-flex align-items-center justify-content-center size-32 rounded-circle bg-white border">
                            <img src="/assets/default/img/ai/ai-chip.svg" alt="ai" width="16" height="16">
                        </div>
                        <span class="ml-2 font-weight-500 text-secondary font-14 d-none d-lg-block"><?php echo e(trans('update.ai_content')); ?></span>
                    </div>
                <?php endif; ?>

                <div class="d-none nav-notify-cart-dropdown top-navbar">
                    <?php echo $__env->make('web.default.includes.shopping-cart-dropdwon', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <span class="border-sep mx-2"></span>
                    <?php echo $__env->make('web.default.includes.notification-dropdown', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </div>
</nav>

<?php $__env->startPush('scripts_bottom'); ?>
    <script src="/assets/default/js/parts/navbar.min.js"></script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\Users\ashut\Downloads\Telegram Desktop\rocket-lms_v1.8\Source\Source\resources\views/web/default/includes/navbar.blade.php ENDPATH**/ ?>