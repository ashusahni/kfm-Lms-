<?php
    $socials = getSocials();
    if (!empty($socials) and count($socials)) {
        $socials = collect($socials)->sortBy('order')->toArray();
    }

    $footerColumns = getFooterColumns();
    $isHomePage = request()->is('/');
?>

<footer class="footer footer-modern footer-modern position-relative user-select-none <?php echo e($isHomePage ? 'fk-footer' : 'bg-secondary'); ?>" <?php if($isHomePage): ?> style="background-color: #1e2d2a !important; color: rgba(255,255,255,0.9);" <?php endif; ?>>
    
    <div class="footer-newsletter">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12 col-md-5 mb-3 mb-md-0">
                    <strong class="footer-title d-block"><?php echo e(trans('footer.join_us_today')); ?></strong>
                    <span class="footer-about-desc d-block"><?php echo e(trans('footer.subscribe_content')); ?></span>
                </div>
                <div class="col-12 col-md-7">
                    <form action="/newsletters" method="post" class="form-inline-modern">
                        <?php echo e(csrf_field()); ?>

                        <input type="text" name="newsletter_email" class="form-control <?php $__errorArgs = ['newsletter_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="<?php echo e(trans('footer.enter_email_here')); ?>" aria-label="<?php echo e(trans('footer.enter_email_here')); ?>"/>
                        <?php $__errorArgs = ['newsletter_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback d-block w-100"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <button type="submit" class="btn-subscribe"><?php echo e(trans('footer.join')); ?></button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    
    <div class="footer-section">
        <div class="container">
            <div class="row">
                <div class="col-12 col-sm-6 col-lg-3 footer-col">
                    <?php if(!empty($footerColumns['first_column'])): ?>
                        <?php if(!empty($footerColumns['first_column']['title'])): ?>
                            <h3 class="footer-title"><?php echo e($footerColumns['first_column']['title']); ?></h3>
                        <?php endif; ?>
                        <?php if(!empty($footerColumns['first_column']['value'])): ?>
                            <div class="footer-about-desc footer-links">
                                <?php echo $footerColumns['first_column']['value']; ?>

                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if(!empty($socials) and count($socials)): ?>
                        <nav class="footer-social" aria-label="<?php echo e(trans('footer.social_links') ?? 'Social links'); ?>">
                            <?php $__currentLoopData = $socials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $social): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e($social['link']); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo e($social['title'] ?? ''); ?>">
                                    <img src="<?php echo e($social['image']); ?>" alt="<?php echo e($social['title'] ?? ''); ?>" width="20" height="20">
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </nav>
                    <?php endif; ?>
                </div>

                <div class="col-12 col-sm-6 col-lg-3 footer-col">
                    <?php if(!empty($footerColumns['second_column'])): ?>
                        <?php if(!empty($footerColumns['second_column']['title'])): ?>
                            <h3 class="footer-title"><?php echo e($footerColumns['second_column']['title']); ?></h3>
                        <?php endif; ?>
                        <?php if(!empty($footerColumns['second_column']['value'])): ?>
                            <div class="footer-links">
                                <?php echo $footerColumns['second_column']['value']; ?>

                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="col-12 col-sm-6 col-lg-3 footer-col">
                    <?php if(!empty($footerColumns['third_column'])): ?>
                        <?php if(!empty($footerColumns['third_column']['title'])): ?>
                            <h3 class="footer-title"><?php echo e($footerColumns['third_column']['title']); ?></h3>
                        <?php endif; ?>
                        <?php if(!empty($footerColumns['third_column']['value'])): ?>
                            <div class="footer-links">
                                <?php echo $footerColumns['third_column']['value']; ?>

                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="col-12 col-sm-6 col-lg-3 footer-col">
                    <?php if(!empty($footerColumns['forth_column'])): ?>
                        <?php if(!empty($footerColumns['forth_column']['title'])): ?>
                            <h3 class="footer-title"><?php echo e($footerColumns['forth_column']['title']); ?></h3>
                        <?php endif; ?>
                        <?php if(!empty($footerColumns['forth_column']['value'])): ?>
                            <div class="footer-links">
                                <?php echo $footerColumns['forth_column']['value']; ?>

                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if(getOthersPersonalizationSettings('platform_phone_and_email_position') == 'footer'): ?>
                        <div class="footer-links">
                            <?php if(!empty($generalSettings['site_phone'])): ?>
                                <div class="footer-contact-item">
                                    <i data-feather="phone" width="18" height="18" aria-hidden="true"></i>
                                    <a href="tel:<?php echo e(preg_replace('/\s+/', '', $generalSettings['site_phone'])); ?>"><?php echo e($generalSettings['site_phone']); ?></a>
                                </div>
                            <?php endif; ?>
                            <?php if(!empty($generalSettings['site_email'])): ?>
                                <div class="footer-contact-item">
                                    <i data-feather="mail" width="18" height="18" aria-hidden="true"></i>
                                    <a href="mailto:<?php echo e($generalSettings['site_email']); ?>"><?php echo e($generalSettings['site_email']); ?></a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <hr class="footer-divider my-0">
    <div class="footer-bottom">
        <div class="container">
            <div class="row align-items-center w-100">
                <div class="col-12 col-md-6 footer-bottom-copyright">
                    &copy; <?php echo e(date('Y')); ?> <?php echo e(trans('update.platform_copyright_hint')); ?>

                </div>
                <div class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end mt-2 mt-md-0">
                    <nav class="footer-bottom-links" aria-label="Legal">
                        <a href="/privacy"><?php echo e(trans('public.privacy_policy') ?? 'Privacy Policy'); ?></a>
                        <a href="/terms"><?php echo e(trans('auth.terms_and_rules') ?? 'Terms'); ?></a>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</footer>
<?php /**PATH C:\Users\ashut\Downloads\Telegram Desktop\rocket-lms_v1.8\Source\Source\resources\views/web/default/includes/footer.blade.php ENDPATH**/ ?>