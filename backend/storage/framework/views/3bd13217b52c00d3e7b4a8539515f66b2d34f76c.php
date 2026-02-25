<?php $__env->startPush('styles_top'); ?>
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <link rel="stylesheet" href="<?php echo e(asset('assets/default/vendors/swiper/swiper-bundle.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/default/vendors/owl-carousel2/owl.carousel.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/default/css/fit-karnataka-home.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<?php
    $defaultHeroBg = 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=1920&q=80';
    $defaultHeroImage = 'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=600&q=80';
?>

<div class="fit-karnataka-home" id="fit-karnataka-home">

    
    <?php if(!empty($heroSectionData)): ?>
        <?php if(!empty($heroSectionData['has_lottie']) and $heroSectionData['has_lottie'] == "1"): ?>
            <?php $__env->startPush('scripts_bottom'); ?>
                <script src="<?php echo e(asset('assets/default/vendors/lottie/lottie-player.js')); ?>"></script>
            <?php $__env->stopPush(); ?>
        <?php endif; ?>
        <section class="fk-hero" <?php if(empty($heroSectionData['is_video_background'])): ?> style="background-image: url('<?php echo e($heroSectionData['hero_background'] ?? $defaultHeroBg); ?>')" <?php endif; ?> data-aos="fade">
            <?php if($heroSection == "1" && !empty($heroSectionData['is_video_background'])): ?>
                <video playsinline autoplay muted loop id="homeHeroVideoBackground" class="img-cover" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;z-index:0;">
                    <source src="<?php echo e($heroSectionData['hero_background']); ?>" type="video/mp4">
                </video>
            <?php endif; ?>
            <div class="fk-hero-overlay"></div>
            <div class="container user-select-none">
                <div class="row align-items-center">
                    <div class="col-12 col-lg-7">
                        <h1 class="mb-3"><?php echo e($heroSectionData['title']); ?></h1>
                        <p class="fk-hero-lead"><?php echo nl2br($heroSectionData['description']); ?></p>
                        <div class="fk-hero-ctas">
                            <a href="/register" class="fk-btn-primary">Join the Mission</a>
                            <a href="/classes" class="fk-btn-secondary">Explore Programs</a>
                        </div>
                        <form action="/search" method="get" class="d-inline-flex mt-4 w-100" style="max-width: 400px;">
                            <div class="form-group d-flex align-items-center m-0 p-2 bg-white rounded shadow-sm w-100" style="border-radius: 14px;">
                                <input type="text" name="search" class="form-control border-0" placeholder="<?php echo e(trans('home.slider_search_placeholder')); ?>"/>
                                <button type="submit" class="btn btn-primary rounded ml-2" style="border-radius: 10px;"><?php echo e(trans('home.find')); ?></button>
                            </div>
                        </form>
                    </div>
                    <div class="col-12 col-lg-5 fk-hero-image-wrap">
                        <?php if(!empty($heroSectionData['has_lottie']) and $heroSectionData['has_lottie'] == "1"): ?>
                            <lottie-player src="<?php echo e($heroSectionData['hero_vector']); ?>" background="transparent" speed="1" class="w-100" style="max-height: 380px;" loop autoplay></lottie-player>
                        <?php else: ?>
                            <img src="<?php echo e($heroSectionData['hero_vector'] ?? $defaultHeroImage); ?>" alt="<?php echo e($heroSectionData['title']); ?>" class="img-cover rounded shadow-lg">
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    <?php else: ?>
        
        <section class="fk-hero" style="background-image: url('<?php echo e($defaultHeroBg); ?>')" data-aos="fade">
            <div class="fk-hero-overlay"></div>
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-12 col-lg-8">
                        <h1>Move More. Live Better. Fit Karnataka.</h1>
                        <p class="fk-hero-lead">A Government of Karnataka initiative to promote fitness, sports, and wellness across the state. Join thousands of citizens building a healthier, more active Karnataka.</p>
                        <div class="fk-hero-ctas">
                            <a href="/register" class="fk-btn-primary">Join the Mission</a>
                            <a href="/classes" class="fk-btn-secondary">Explore Programs</a>
                        </div>
                        <form action="/search" method="get" class="d-inline-flex mt-4 w-100" style="max-width: 400px;">
                            <div class="form-group d-flex align-items-center m-0 p-2 bg-white rounded shadow-sm w-100" style="border-radius: 14px;">
                                <input type="text" name="search" class="form-control border-0" placeholder="Search fitness programs..."/>
                                <button type="submit" class="btn btn-primary rounded ml-2" style="border-radius: 10px;">Search</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-12 col-lg-4 fk-hero-image-wrap">
                        <img src="https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=500&q=80" alt="Fit Karnataka Yoga" class="img-cover rounded shadow-lg">
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    
    <section class="fk-about py-5" data-aos="fade-up">
        <div class="container">
            <div class="row align-items-start">
                <div class="col-12 col-lg-5 mb-4 mb-lg-0">
                    <span class="badge badge-danger mb-3 px-3 py-2 rounded-pill">About the Mission</span>
                    <h2 class="fk-section-head mb-3">Our Mission & Vision</h2>
                    <p class="fk-section-hint">Fit Karnataka Mission is a Government of Karnataka initiative to promote fitness, sports, and wellness across all 31 districts. We aim to build a healthier, more active community through inclusive programs, trained instructors, and partnerships with local bodies.</p>
                    <p class="fk-section-hint mt-3">From community yoga and running clubs to youth sports leagues and wellness workshops—every citizen can find a way to move, connect, and thrive.</p>
                </div>
                <div class="col-12 col-lg-7">
                    <div class="row fk-about-cards">
                        <div class="col-12 col-md-4 mb-4 mb-md-0">
                            <div class="card h-100 overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1461897104016-0b3b00cc81ee?w=400&q=80" alt="Community Fitness" class="card-img-top img-cover" style="height: 140px; object-fit: cover;">
                                <div class="card-body">
                                    <div class="fk-card-icon mt-0"><i data-feather="users" width="24" height="24"></i></div>
                                    <h5 class="card-title">Community Fitness</h5>
                                    <p class="card-text">Group activities, running clubs, and local fitness initiatives for all ages across Karnataka.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-4 mb-md-0">
                            <div class="card h-100 overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=400&q=80" alt="Youth Sports" class="card-img-top img-cover" style="height: 140px; object-fit: cover;">
                                <div class="card-body">
                                    <div class="fk-card-icon mt-0"><i data-feather="award" width="24" height="24"></i></div>
                                    <h5 class="card-title">Youth Sports</h5>
                                    <p class="card-text">Training camps and events to nurture young athletes and promote sports in schools.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="card h-100 overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=400&q=80" alt="Wellness & Yoga" class="card-img-top img-cover" style="height: 140px; object-fit: cover;">
                                <div class="card-body">
                                    <div class="fk-card-icon mt-0"><i data-feather="heart" width="24" height="24"></i></div>
                                    <h5 class="card-title">Wellness & Yoga</h5>
                                    <p class="card-text">Yoga, meditation, and wellness programs for mental and physical health in every district.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    
    <?php $statisticsSettings = getStatisticsSettings(); ?>
    <?php if(!empty($statisticsSettings['enable_statistics'])): ?>
        <?php if(!empty($statisticsSettings['display_default_statistics']) and !empty($homeDefaultStatistics)): ?>
            <section class="fk-stats" data-aos="fade-up">
                <div class="container">
                    <div class="row text-center">
                        <div class="col-6 col-lg-3 mb-4 mb-lg-0">
                            <div class="fk-stat-item">
                                <div class="fk-stat-number" data-count="<?php echo e($homeDefaultStatistics['skillfulTeachersCount']); ?>">0</div>
                                <div class="fk-stat-label">Active Instructors</div>
                                <div class="fk-stat-desc">Trained fitness & wellness experts</div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3 mb-4 mb-lg-0">
                            <div class="fk-stat-item">
                                <div class="fk-stat-number" data-count="<?php echo e($homeDefaultStatistics['studentsCount']); ?>">0</div>
                                <div class="fk-stat-label">Participants</div>
                                <div class="fk-stat-desc">Citizens moving with the mission</div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3 mb-4 mb-lg-0">
                            <div class="fk-stat-item">
                                <div class="fk-stat-number" data-count="<?php echo e($homeDefaultStatistics['liveClassCount']); ?>">0</div>
                                <div class="fk-stat-label">Live Sessions</div>
                                <div class="fk-stat-desc">Classes & workshops conducted</div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="fk-stat-item">
                                <div class="fk-stat-number" data-count="<?php echo e($homeDefaultStatistics['offlineCourseCount']); ?>">0</div>
                                <div class="fk-stat-label">Programs</div>
                                <div class="fk-stat-desc">Courses across Karnataka</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php elseif(!empty($homeCustomStatistics)): ?>
            <section class="fk-stats" data-aos="fade-up">
                <div class="container">
                    <div class="row">
                        <?php $__currentLoopData = $homeCustomStatistics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $homeCustomStatistic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-6 col-lg-3">
                                <div class="fk-stat-item">
                                    <div class="fk-stat-number" data-count="<?php echo e($homeCustomStatistic->count); ?>">0</div>
                                    <div class="fk-stat-label"><?php echo e($homeCustomStatistic->title); ?></div>
                                    <div class="fk-stat-desc"><?php echo e($homeCustomStatistic->description); ?></div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    <?php endif; ?>
    <?php if(empty($statisticsSettings['enable_statistics']) || (empty($homeDefaultStatistics) && empty($homeCustomStatistics))): ?>
        <div class="py-5"></div>
    <?php endif; ?>

    <?php $__currentLoopData = $homeSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $homeSection): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

        
        <?php if($homeSection->name == \App\Models\HomeSection::$featured_classes and !empty($featureWebinars) and !$featureWebinars->isEmpty()): ?>
            <section class="fk-programs home-sections container py-5" data-aos="fade-up">
                <span class="badge badge-danger mb-2 px-3 py-2 rounded-pill">Programs</span>
                <h2 class="fk-section-title">Featured Fit Karnataka Programs</h2>
                <p class="section-hint mb-4">Join fitness, sports, and wellness programs designed for citizens across Karnataka. From yoga to youth sports—find your fit.</p>
                <div class="row">
                    <?php $__currentLoopData = $featureWebinars->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-12 col-md-6 col-lg-4 mb-4">
                            <div class="fk-program-card card h-100">
                                <div class="fk-program-thumb">
                                    <a href="<?php echo e($feature->webinar->getUrl()); ?>">
                                        <img src="<?php echo e($feature->webinar->getImage()); ?>" class="img-cover" alt="<?php echo e($feature->webinar->title); ?>">
                                    </a>
                                </div>
                                <div class="card-body">
                                    <a href="<?php echo e($feature->webinar->getUrl()); ?>">
                                        <h5 class="card-title"><?php echo e($feature->webinar->title); ?></h5>
                                    </a>
                                    <p class="fk-program-desc"><?php echo e($feature->description); ?></p>
                                    <a href="<?php echo e($feature->webinar->getUrl()); ?>" class="fk-btn-learn">Learn More</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div class="text-center mt-4">
                    <a href="/classes" class="btn btn-primary rounded px-4 py-2" style="border-radius: 14px;">View All Programs</a>
                </div>
            </section>
        <?php endif; ?>

        <?php if($homeSection->name == \App\Models\HomeSection::$latest_bundles and !empty($latestBundles) and !$latestBundles->isEmpty()): ?>
            <section class="home-sections home-sections-swiper container" data-aos="fade-up">
                <div class="d-flex justify-content-between flex-wrap align-items-center">
                    <div>
                        <span class="badge badge-danger mb-2 px-3 py-2 rounded-pill">Programs</span>
                        <h2 class="section-title">Program Bundles</h2>
                        <p class="section-hint">Curated bundles for fitness, yoga, and wellness—save when you join multiple programs.</p>
                    </div>
                    <a href="/classes?type[]=bundle" class="btn btn-primary rounded px-4 py-2 mt-2 mt-md-0" style="border-radius: 14px;"><?php echo e(trans('home.view_all')); ?></a>
                </div>

                <div class="mt-10 position-relative">
                    <div class="swiper-container latest-bundle-swiper px-12">
                        <div class="swiper-wrapper py-20">
                            <?php $__currentLoopData = $latestBundles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $latestBundle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="swiper-slide">
                                    <?php echo $__env->make('web.default.includes.webinar.grid-card',['webinar' => $latestBundle], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        </div>
                    </div>

                    <div class="d-flex justify-content-center">
                        <div class="swiper-pagination bundle-webinars-swiper-pagination"></div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        
        <?php if($homeSection->name == \App\Models\HomeSection::$upcoming_courses and !empty($upcomingCourses) and !$upcomingCourses->isEmpty()): ?>
            <section class="fk-events home-sections container py-5" data-aos="fade-up">
                <span class="badge badge-danger mb-2 px-3 py-2 rounded-pill">Events</span>
                <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                    <div>
                        <h2 class="fk-section-title">Upcoming Events & Challenges</h2>
                        <p class="section-hint">Join runs, yoga camps, and fitness challenges happening across Karnataka. Register and be part of the movement.</p>
                    </div>
                    <a href="/upcoming_courses?sort=newest" class="btn btn-primary rounded px-4 py-2 mt-2 mt-md-0" style="border-radius: 14px;">View All Events</a>
                </div>
                <div class="position-relative">
                    <div class="swiper-container upcoming-courses-swiper px-12">
                        <div class="swiper-wrapper py-4">
                            <?php $__currentLoopData = $upcomingCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $upcomingCourse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="swiper-slide">
                                    <div class="fk-event-card card h-100">
                                        <div class="card-body">
                                            <span class="fk-event-date-badge"><?php echo e(dateTimeFormat($upcomingCourse->published_date ?? $upcomingCourse->created_at, 'j M Y')); ?></span>
                                            <a href="<?php echo e($upcomingCourse->getUrl()); ?>">
                                                <h5 class="card-title"><?php echo e(clean($upcomingCourse->title,'title')); ?></h5>
                                            </a>
                                            <p class="card-text"><?php echo e(\Str::limit($upcomingCourse->description ?? '', 90)); ?></p>
                                            <a href="<?php echo e($upcomingCourse->getUrl()); ?>" class="fk-btn-register">Register</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        <div class="swiper-pagination upcoming-courses-swiper-pagination"></div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if($homeSection->name == \App\Models\HomeSection::$latest_classes and !empty($latestWebinars) and !$latestWebinars->isEmpty()): ?>
            <section class="home-sections home-sections-swiper container" data-aos="fade-up">
                <div class="d-flex justify-content-between flex-wrap align-items-center">
                    <div>
                        <span class="badge badge-danger mb-2 px-3 py-2 rounded-pill">Programs</span>
                        <h2 class="section-title">Latest Programs</h2>
                        <p class="section-hint">New fitness, sports, and wellness programs added for the Fit Karnataka community.</p>
                    </div>
                    <a href="/classes?sort=newest" class="btn btn-primary rounded px-4 py-2 mt-2 mt-md-0" style="border-radius: 14px;"><?php echo e(trans('home.view_all')); ?></a>
                </div>

                <div class="mt-10 position-relative">
                    <div class="swiper-container latest-webinars-swiper px-12">
                        <div class="swiper-wrapper py-20">
                            <?php $__currentLoopData = $latestWebinars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $latestWebinar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="swiper-slide">
                                    <?php echo $__env->make('web.default.includes.webinar.grid-card',['webinar' => $latestWebinar], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        </div>
                    </div>

                    <div class="d-flex justify-content-center">
                        <div class="swiper-pagination latest-webinars-swiper-pagination"></div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if($homeSection->name == \App\Models\HomeSection::$best_rates and !empty($bestRateWebinars) and !$bestRateWebinars->isEmpty()): ?>
            <section class="home-sections home-sections-swiper container">
                <div class="d-flex justify-content-between">
                    <div>
                        <h2 class="section-title"><?php echo e(trans('home.best_rates')); ?></h2>
                        <p class="section-hint"><?php echo e(trans('home.best_rates_hint')); ?></p>
                    </div>

                    <a href="/classes?sort=best_rates" class="btn btn-border-white"><?php echo e(trans('home.view_all')); ?></a>
                </div>

                <div class="mt-10 position-relative">
                    <div class="swiper-container best-rates-webinars-swiper px-12">
                        <div class="swiper-wrapper py-20">
                            <?php $__currentLoopData = $bestRateWebinars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bestRateWebinar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="swiper-slide">
                                    <?php echo $__env->make('web.default.includes.webinar.grid-card',['webinar' => $bestRateWebinar], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center">
                        <div class="swiper-pagination best-rates-webinars-swiper-pagination"></div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if($homeSection->name == \App\Models\HomeSection::$trend_categories and !empty($trendCategories) and !$trendCategories->isEmpty()): ?>
            <section class="home-sections home-sections-swiper container" data-aos="fade-up">
                <span class="badge badge-danger mb-2 px-3 py-2 rounded-pill">Explore</span>
                <h2 class="section-title">Popular Categories</h2>
                <p class="section-hint">Fitness, yoga, sports, and wellness—find programs by category across Karnataka.</p>


                <div class="swiper-container trend-categories-swiper px-12 mt-40">
                    <div class="swiper-wrapper py-20">
                        <?php $__currentLoopData = $trendCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trend): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="swiper-slide">
                                <a href="<?php echo e($trend->category->getUrl()); ?>">
                                    <div class="trending-card d-flex flex-column align-items-center w-100">
                                        <div class="trending-image d-flex align-items-center justify-content-center w-100" style="background-color: <?php echo e($trend->color); ?>">
                                            <div class="icon mb-3">
                                                <img src="<?php echo e($trend->getIcon()); ?>" width="10" class="img-cover" alt="<?php echo e($trend->category->title); ?>">
                                            </div>
                                        </div>

                                        <div class="item-count px-10 px-lg-20 py-5 py-lg-10"><?php echo e($trend->category->webinars_count); ?> <?php echo e(trans('product.course')); ?></div>

                                        <h3><?php echo e($trend->category->title); ?></h3>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <div class="d-flex justify-content-center">
                    <div class="swiper-pagination trend-categories-swiper-pagination"></div>
                </div>
            </section>
        <?php endif; ?>

        <?php if($homeSection->name == \App\Models\HomeSection::$best_sellers and !empty($bestSaleWebinars) and !$bestSaleWebinars->isEmpty()): ?>
            <section class="home-sections container">
                <div class="d-flex justify-content-between">
                    <div>
                        <h2 class="section-title"><?php echo e(trans('home.best_sellers')); ?></h2>
                        <p class="section-hint"><?php echo e(trans('home.best_sellers_hint')); ?></p>
                    </div>

                    <a href="/classes?sort=bestsellers" class="btn btn-border-white"><?php echo e(trans('home.view_all')); ?></a>
                </div>

                <div class="mt-10 position-relative">
                    <div class="swiper-container best-sales-webinars-swiper px-12">
                        <div class="swiper-wrapper py-20">
                            <?php $__currentLoopData = $bestSaleWebinars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bestSaleWebinar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="swiper-slide">
                                    <?php echo $__env->make('web.default.includes.webinar.grid-card',['webinar' => $bestSaleWebinar], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center">
                        <div class="swiper-pagination best-sales-webinars-swiper-pagination"></div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if($homeSection->name == \App\Models\HomeSection::$discount_classes and !empty($hasDiscountWebinars) and !$hasDiscountWebinars->isEmpty()): ?>
            <section class="home-sections container">
                <div class="d-flex justify-content-between">
                    <div>
                        <h2 class="section-title"><?php echo e(trans('home.discount_classes')); ?></h2>
                        <p class="section-hint"><?php echo e(trans('home.discount_classes_hint')); ?></p>
                    </div>

                    <a href="/classes?discount=on" class="btn btn-border-white"><?php echo e(trans('home.view_all')); ?></a>
                </div>

                <div class="mt-10 position-relative">
                    <div class="swiper-container has-discount-webinars-swiper px-12">
                        <div class="swiper-wrapper py-20">
                            <?php $__currentLoopData = $hasDiscountWebinars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hasDiscountWebinar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="swiper-slide">
                                    <?php echo $__env->make('web.default.includes.webinar.grid-card',['webinar' => $hasDiscountWebinar], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center">
                        <div class="swiper-pagination has-discount-webinars-swiper-pagination"></div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if($homeSection->name == \App\Models\HomeSection::$free_classes and !empty($freeWebinars) and !$freeWebinars->isEmpty()): ?>
            <section class="home-sections home-sections-swiper container">
                <div class="d-flex justify-content-between">
                    <div>
                        <h2 class="section-title"><?php echo e(trans('home.free_classes')); ?></h2>
                        <p class="section-hint"><?php echo e(trans('home.free_classes_hint')); ?></p>
                    </div>

                    <a href="/classes?free=on" class="btn btn-border-white"><?php echo e(trans('home.view_all')); ?></a>
                </div>

                <div class="mt-10 position-relative">
                    <div class="swiper-container free-webinars-swiper px-12">
                        <div class="swiper-wrapper py-20">

                            <?php $__currentLoopData = $freeWebinars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $freeWebinar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="swiper-slide">
                                    <?php echo $__env->make('web.default.includes.webinar.grid-card',['webinar' => $freeWebinar], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        </div>
                    </div>

                    <div class="d-flex justify-content-center">
                        <div class="swiper-pagination free-webinars-swiper-pagination"></div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        
        <?php if($homeSection->name == \App\Models\HomeSection::$testimonials and !empty($testimonials) and !$testimonials->isEmpty()): ?>
            <section class="fk-testimonials home-sections container py-5" data-aos="fade-up">
                <span class="badge badge-danger mb-2 px-3 py-2 rounded-pill">Community</span>
                <div class="text-center mb-4">
                    <h2 class="fk-section-title">Stories from the Mission</h2>
                    <p class="section-hint">Hear from participants, instructors, and partners who are part of Fit Karnataka.</p>
                </div>
                <div class="position-relative">
                    <div class="swiper-container testimonials-swiper px-12">
                        <div class="swiper-wrapper">
                            <?php $__currentLoopData = $testimonials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $testimonial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="swiper-slide">
                                    <div class="fk-testimonial-card">
                                        <img src="<?php echo e($testimonial->user_avatar); ?>" alt="<?php echo e($testimonial->user_name); ?>" class="fk-testimonial-avatar img-cover">
                                        <h5 class="fk-testimonial-name"><?php echo e($testimonial->user_name); ?></h5>
                                        <span class="fk-testimonial-role d-block"><?php echo e($testimonial->user_bio); ?></span>
                                        <?php echo $__env->make('web.default.includes.webinar.rate',['rate' => $testimonial->rate, 'dontShowRate' => true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                        <p class="fk-testimonial-text mt-3"><?php echo nl2br($testimonial->comment); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        <div class="swiper-pagination testimonials-swiper-pagination"></div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if($homeSection->name == \App\Models\HomeSection::$subscribes and !empty($subscribes) and !$subscribes->isEmpty()): ?>
            <div class="home-sections position-relative subscribes-container pe-none user-select-none">
                <div id="parallax4" class="ltr d-none d-md-block">
                    <div data-depth="0.2" class="gradient-box left-gradient-box"></div>
                </div>

                <section class="container home-sections home-sections-swiper">
                    <div class="text-center">
                        <h2 class="section-title"><?php echo e(trans('home.subscribe_now')); ?></h2>
                        <p class="section-hint"><?php echo e(trans('home.subscribe_now_hint')); ?></p>
                    </div>

                    <div class="position-relative mt-30">
                        <div class="swiper-container subscribes-swiper px-12">
                            <div class="swiper-wrapper py-20">

                                <?php $__currentLoopData = $subscribes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subscribe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $subscribeSpecialOffer = $subscribe->activeSpecialOffer();
                                    ?>

                                    <div class="swiper-slide">
                                        <div class="subscribe-plan position-relative bg-white d-flex flex-column align-items-center rounded-sm shadow pt-50 pb-20 px-20">
                                            <?php if($subscribe->is_popular): ?>
                                                <span class="badge badge-primary badge-popular px-15 py-5"><?php echo e(trans('panel.popular')); ?></span>
                                            <?php elseif(!empty($subscribeSpecialOffer)): ?>
                                                <span class="badge badge-danger badge-popular px-15 py-5"><?php echo e(trans('update.percent_off', ['percent' => $subscribeSpecialOffer->percent])); ?></span>
                                            <?php endif; ?>

                                            <div class="plan-icon">
                                                <img src="<?php echo e($subscribe->icon); ?>" class="img-cover" alt="">
                                            </div>

                                            <h3 class="mt-20 font-30 text-secondary"><?php echo e($subscribe->title); ?></h3>
                                            <p class="font-weight-500 text-gray mt-10"><?php echo e($subscribe->description); ?></p>

                                            <div class="d-flex align-items-start mt-30">
                                                <?php if(!empty($subscribe->price) and $subscribe->price > 0): ?>
                                                    <?php if(!empty($subscribeSpecialOffer)): ?>
                                                        <div class="d-flex align-items-end line-height-1">
                                                            <span class="font-36 text-primary"><?php echo e(handlePrice($subscribe->getPrice(), true, true, false, null, true)); ?></span>
                                                            <span class="font-14 text-gray ml-5 text-decoration-line-through"><?php echo e(handlePrice($subscribe->price, true, true, false, null, true)); ?></span>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="font-36 text-primary line-height-1"><?php echo e(handlePrice($subscribe->price, true, true, false, null, true)); ?></span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="font-36 text-primary line-height-1"><?php echo e(trans('public.free')); ?></span>
                                                <?php endif; ?>
                                            </div>

                                            <ul class="mt-20 plan-feature">
                                                <li class="mt-10"><?php echo e($subscribe->days); ?> <?php echo e(trans('financial.days_of_subscription')); ?></li>
                                                <li class="mt-10">
                                                    <?php if($subscribe->infinite_use): ?>
                                                        <?php echo e(trans('update.unlimited')); ?>

                                                    <?php else: ?>
                                                        <?php echo e($subscribe->usable_count); ?>

                                                    <?php endif; ?>
                                                    <span class="ml-5"><?php echo e(trans('update.subscribes')); ?></span>
                                                </li>
                                            </ul>

                                            <?php if(auth()->check()): ?>
                                                <form action="/panel/financial/pay-subscribes" method="post" class="w-100">
                                                    <?php echo e(csrf_field()); ?>

                                                    <input name="amount" value="<?php echo e($subscribe->price); ?>" type="hidden">
                                                    <input name="id" value="<?php echo e($subscribe->id); ?>" type="hidden">

                                                    <div class="d-flex align-items-center mt-50 w-100">
                                                        <button type="submit" class="btn btn-primary <?php echo e(!empty($subscribe->has_installment) ? '' : 'btn-block'); ?>"><?php echo e(trans('update.purchase')); ?></button>

                                                        <?php if(!empty($subscribe->has_installment)): ?>
                                                            <a href="/panel/financial/subscribes/<?php echo e($subscribe->id); ?>/installments" class="btn btn-outline-primary flex-grow-1 ml-10"><?php echo e(trans('update.installments')); ?></a>
                                                        <?php endif; ?>
                                                    </div>
                                                </form>
                                            <?php else: ?>
                                                <a href="/login" class="btn btn-primary btn-block mt-50"><?php echo e(trans('update.purchase')); ?></a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>

                        </div>
                        <div class="d-flex justify-content-center">
                            <div class="swiper-pagination subscribes-swiper-pagination"></div>
                        </div>

                    </div>
                </section>

                <div id="parallax5" class="ltr d-none d-md-block">
                    <div data-depth="0.4" class="gradient-box right-gradient-box"></div>
                </div>

                <div id="parallax6" class="ltr d-none d-md-block">
                    <div data-depth="0.6" class="gradient-box bottom-gradient-box"></div>
                </div>
            </div>
        <?php endif; ?>

        <?php if($homeSection->name == \App\Models\HomeSection::$find_instructors and !empty($findInstructorSection)): ?>
            <section class="home-sections home-sections-swiper container find-instructor-section position-relative">
                <div class="row align-items-center">
                    <div class="col-12 col-lg-6">
                        <div class="">
                            <h2 class="font-36 font-weight-bold text-dark"><?php echo e($findInstructorSection['title'] ?? ''); ?></h2>
                            <p class="font-16 font-weight-normal text-gray mt-10"><?php echo e($findInstructorSection['description'] ?? ''); ?></p>

                            <div class="mt-35 d-flex align-items-center">
                                <?php if(!empty($findInstructorSection['button1']) and !empty($findInstructorSection['button1']['title']) and !empty($findInstructorSection['button1']['link'])): ?>
                                    <a href="<?php echo e($findInstructorSection['button1']['link']); ?>" class="btn btn-primary mr-15"><?php echo e($findInstructorSection['button1']['title']); ?></a>
                                <?php endif; ?>

                                <?php if(!empty($findInstructorSection['button2']) and !empty($findInstructorSection['button2']['title']) and !empty($findInstructorSection['button2']['link'])): ?>
                                    <a href="<?php echo e($findInstructorSection['button2']['link']); ?>" class="btn btn-outline-primary"><?php echo e($findInstructorSection['button2']['title']); ?></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6 mt-20 mt-lg-0">
                        <div class="position-relative ">
                            <img src="<?php echo e($findInstructorSection['image']); ?>" class="find-instructor-section-hero" alt="<?php echo e($findInstructorSection['title']); ?>">
                            <img src="/assets/default/img/home/circle-4.png" class="find-instructor-section-circle" alt="circle">
                            <img src="/assets/default/img/home/dot.png" class="find-instructor-section-dots" alt="dots">

                            <div class="example-instructor-card bg-white rounded-sm shadow-lg  p-5 p-md-15 d-flex align-items-center">
                                <div class="example-instructor-card-avatar">
                                    <img src="/assets/default/img/home/toutor_finder.svg" class="img-cover rounded-circle" alt="user name">
                                </div>

                                <div class="flex-grow-1 ml-15">
                                    <span class="font-14 font-weight-bold text-secondary d-block"><?php echo e(trans('update.looking_for_an_instructor')); ?></span>
                                    <span class="text-gray font-12 font-weight-500"><?php echo e(trans('update.find_the_best_instructor_now')); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if($homeSection->name == \App\Models\HomeSection::$reward_program and !empty($rewardProgramSection)): ?>
            <section class="home-sections home-sections-swiper container reward-program-section position-relative">
                <div class="row align-items-center">
                    <div class="col-12 col-lg-6">
                        <div class="position-relative reward-program-section-hero-card">
                            <img src="<?php echo e($rewardProgramSection['image']); ?>" class="reward-program-section-hero" alt="<?php echo e($rewardProgramSection['title']); ?>">

                            <div class="example-reward-card bg-white rounded-sm shadow-lg p-5 p-md-15 d-flex align-items-center">
                                <div class="example-reward-card-medal">
                                    <img src="/assets/default/img/rewards/medal.png" class="img-cover rounded-circle" alt="medal">
                                </div>

                                <div class="flex-grow-1 ml-15">
                                    <span class="font-14 font-weight-bold text-secondary d-block"><?php echo e(trans('update.you_got_50_points')); ?></span>
                                    <span class="text-gray font-12 font-weight-500"><?php echo e(trans('update.for_completing_the_course')); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6 mt-20 mt-lg-0">
                        <div class="">
                            <h2 class="font-36 font-weight-bold text-dark"><?php echo e($rewardProgramSection['title'] ?? ''); ?></h2>
                            <p class="font-16 font-weight-normal text-gray mt-10"><?php echo e($rewardProgramSection['description'] ?? ''); ?></p>

                            <div class="mt-35 d-flex align-items-center">
                                <?php if(!empty($rewardProgramSection['button1']) and !empty($rewardProgramSection['button1']['title']) and !empty($rewardProgramSection['button1']['link'])): ?>
                                    <a href="<?php echo e($rewardProgramSection['button1']['link']); ?>" class="btn btn-primary mr-15"><?php echo e($rewardProgramSection['button1']['title']); ?></a>
                                <?php endif; ?>

                                <?php if(!empty($rewardProgramSection['button2']) and !empty($rewardProgramSection['button2']['title']) and !empty($rewardProgramSection['button2']['link'])): ?>
                                    <a href="<?php echo e($rewardProgramSection['button2']['link']); ?>" class="btn btn-outline-primary"><?php echo e($rewardProgramSection['button2']['title']); ?></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if($homeSection->name == \App\Models\HomeSection::$become_instructor and !empty($becomeInstructorSection)): ?>
            <section class="home-sections home-sections-swiper container find-instructor-section position-relative">
                <div class="row align-items-center">
                    <div class="col-12 col-lg-6">
                        <div class="">
                            <h2 class="font-36 font-weight-bold text-dark"><?php echo e($becomeInstructorSection['title'] ?? ''); ?></h2>
                            <p class="font-16 font-weight-normal text-gray mt-10"><?php echo e($becomeInstructorSection['description'] ?? ''); ?></p>

                            <div class="mt-35 d-flex align-items-center">
                                <?php if(!empty($becomeInstructorSection['button1']) and !empty($becomeInstructorSection['button1']['title']) and !empty($becomeInstructorSection['button1']['link'])): ?>
                                    <a href="<?php echo e(empty($authUser) ? '/login' : (($authUser->isUser()) ? $becomeInstructorSection['button1']['link'] : '/panel/financial/registration-packages')); ?>" class="btn btn-primary mr-15"><?php echo e($becomeInstructorSection['button1']['title']); ?></a>
                                <?php endif; ?>

                                <?php if(!empty($becomeInstructorSection['button2']) and !empty($becomeInstructorSection['button2']['title']) and !empty($becomeInstructorSection['button2']['link'])): ?>
                                    <a href="<?php echo e(empty($authUser) ? '/login' : (($authUser->isUser()) ? $becomeInstructorSection['button2']['link'] : '/panel/financial/registration-packages')); ?>" class="btn btn-outline-primary"><?php echo e($becomeInstructorSection['button2']['title']); ?></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6 mt-20 mt-lg-0">
                        <div class="position-relative ">
                            <img src="<?php echo e($becomeInstructorSection['image']); ?>" class="find-instructor-section-hero" alt="<?php echo e($becomeInstructorSection['title']); ?>">
                            <img src="/assets/default/img/home/circle-4.png" class="find-instructor-section-circle" alt="circle">
                            <img src="/assets/default/img/home/dot.png" class="find-instructor-section-dots" alt="dots">

                            <div class="example-instructor-card bg-white rounded-sm shadow-lg border p-5 p-md-15 d-flex align-items-center">
                                <div class="example-instructor-card-avatar">
                                    <img src="/assets/default/img/home/become_instructor.svg" class="img-cover rounded-circle" alt="user name">
                                </div>

                                <div class="flex-grow-1 ml-15">
                                    <span class="font-14 font-weight-bold text-secondary d-block"><?php echo e(trans('update.become_an_instructor')); ?></span>
                                    <span class="text-gray font-12 font-weight-500"><?php echo e(trans('update.become_instructor_tagline')); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if($homeSection->name == \App\Models\HomeSection::$video_or_image_section and !empty($boxVideoOrImage)): ?>
            <section class="home-sections home-sections-swiper position-relative">
                <div class="home-video-mask"></div>
                <div class="container home-video-container d-flex flex-column align-items-center justify-content-center position-relative" style="background-image: url('<?php echo e($boxVideoOrImage['background'] ?? ''); ?>')">
                    <a href="<?php echo e($boxVideoOrImage['link'] ?? ''); ?>" class="home-video-play-button d-flex align-items-center justify-content-center position-relative">
                        <i data-feather="play" width="36" height="36" class=""></i>
                    </a>

                    <div class="mt-50 pt-10 text-center">
                        <h2 class="home-video-title"><?php echo e($boxVideoOrImage['title'] ?? ''); ?></h2>
                        <p class="home-video-hint mt-10"><?php echo e($boxVideoOrImage['description'] ?? ''); ?></p>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if($homeSection->name == \App\Models\HomeSection::$instructors and !empty($instructors) and !$instructors->isEmpty()): ?>
            <section class="home-sections container" data-aos="fade-up">
                <div class="d-flex justify-content-between flex-wrap align-items-center">
                    <div>
                        <span class="badge badge-danger mb-2 px-3 py-2 rounded-pill">People</span>
                        <h2 class="section-title">Fit Karnataka Instructors</h2>
                        <p class="section-hint">Meet certified fitness and wellness instructors guiding the mission across the state.</p>
                    </div>
                    <a href="/instructors" class="btn btn-primary rounded px-4 py-2 mt-2 mt-md-0" style="border-radius: 14px;"><?php echo e(trans('home.all_instructors')); ?></a>
                </div>

                <div class="position-relative mt-20 ltr">
                    <div class="owl-carousel customers-testimonials instructors-swiper-container">

                        <?php $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="item">
                                <div class="shadow-effect">
                                    <div class="instructors-card d-flex flex-column align-items-center justify-content-center">
                                        <div class="instructors-card-avatar">
                                            <img src="<?php echo e($instructor->getAvatar(108)); ?>" alt="<?php echo e($instructor->full_name); ?>" class="rounded-circle img-cover">
                                        </div>
                                        <div class="instructors-card-info mt-10 text-center">
                                            <a href="<?php echo e($instructor->getProfileUrl()); ?>" target="_blank">
                                                <h3 class="font-16 font-weight-bold text-dark-blue"><?php echo e($instructor->full_name); ?></h3>
                                            </a>

                                            <p class="font-14 text-gray mt-5"><?php echo e($instructor->bio); ?></p>
                                            <div class="stars-card d-flex align-items-center justify-content-center mt-10">
                                                <?php
                                                    $i = 5;
                                                ?>
                                                <?php while(--$i >= 5 - $instructor->rates()): ?>
                                                    <i data-feather="star" width="20" height="20" class="active"></i>
                                                <?php endwhile; ?>
                                                <?php while($i-- >= 0): ?>
                                                    <i data-feather="star" width="20" height="20" class=""></i>
                                                <?php endwhile; ?>
                                            </div>

                                            <?php if(!empty($instructor->hasMeeting())): ?>
                                                <a href="<?php echo e($instructor->getProfileUrl()); ?>?tab=appointments" class="btn btn-primary btn-sm rounded-pill mt-15"><?php echo e(trans('home.reserve_a_live_class')); ?></a>
                                            <?php else: ?>
                                                <a href="<?php echo e($instructor->getProfileUrl()); ?>" class="btn btn-primary btn-sm rounded-pill mt-15"><?php echo e(trans('public.profile')); ?></a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if($homeSection->name == \App\Models\HomeSection::$organizations and !empty($organizations) and !$organizations->isEmpty()): ?>
            <section class="home-sections home-sections-swiper container">
                <div class="d-flex justify-content-between">
                    <div>
                        <h2 class="section-title"><?php echo e(trans('home.organizations')); ?></h2>
                        <p class="section-hint"><?php echo e(trans('home.organizations_hint')); ?></p>
                    </div>

                    <a href="/organizations" class="btn btn-border-white"><?php echo e(trans('home.all_organizations')); ?></a>
                </div>

                <div class="position-relative mt-20">
                    <div class="swiper-container organization-swiper-container px-12">
                        <div class="swiper-wrapper py-20">

                            <?php $__currentLoopData = $organizations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $organization): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="swiper-slide">
                                    <div class="home-organizations-card d-flex flex-column align-items-center justify-content-center">
                                        <div class="home-organizations-avatar">
                                            <img src="<?php echo e($organization->getAvatar(120)); ?>" class="img-cover rounded-circle" alt="<?php echo e($organization->full_name); ?>">
                                        </div>
                                        <a href="<?php echo e($organization->getProfileUrl()); ?>" class="mt-25 d-flex flex-column align-items-center justify-content-center">
                                            <h3 class="home-organizations-title"><?php echo e($organization->full_name); ?></h3>
                                            <p class="home-organizations-desc mt-10"><?php echo e($organization->bio); ?></p>
                                            <span class="home-organizations-badge badge mt-15"><?php echo e($organization->webinars_count); ?> <?php echo e(trans('panel.classes')); ?></span>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center">
                        <div class="swiper-pagination organization-swiper-pagination"></div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if($homeSection->name == \App\Models\HomeSection::$blog and !empty($blog) and !$blog->isEmpty()): ?>
            <section class="home-sections container">
                <div class="d-flex justify-content-between">
                    <div>
                        <h2 class="section-title"><?php echo e(trans('home.blog')); ?></h2>
                        <p class="section-hint"><?php echo e(trans('home.blog_hint')); ?></p>
                    </div>

                    <a href="/blog" class="btn btn-border-white"><?php echo e(trans('home.all_blog')); ?></a>
                </div>

                <div class="row mt-35">

                    <?php $__currentLoopData = $blog; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-12 col-md-4 col-lg-4 mt-20 mt-lg-0">
                            <?php echo $__env->make('web.default.blog.grid-list',['post' =>$post], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                </div>
            </section>
        <?php endif; ?>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    
    <section class="fk-cta" data-aos="fade-up">
        <div class="container">
            <h2>Be Part of a Healthier Karnataka</h2>
            <p class="mb-4 mx-auto" style="max-width: 560px; opacity: 0.95;">Register today and get access to fitness programs, events, and a community that moves together.</p>
            <a href="/register" class="fk-cta-btn">Join the Mission</a>
        </div>
    </section>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts_bottom'); ?>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="<?php echo e(asset('assets/default/vendors/swiper/swiper-bundle.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/default/vendors/owl-carousel2/owl.carousel.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/default/vendors/parallax/parallax.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/default/js/parts/home.min.js')); ?>"></script>
    <script>
    (function($) {
        AOS.init({ duration: 600, once: true, offset: 80 });
        function animateValue(el, start, end, duration) {
            var startTimestamp = null;
            function step(timestamp) {
                if (!startTimestamp) startTimestamp = timestamp;
                var progress = Math.min((timestamp - startTimestamp) / duration, 1);
                el.textContent = Math.floor(progress * (end - start) + start);
                if (progress < 1) window.requestAnimationFrame(step);
            }
            window.requestAnimationFrame(step);
        }
        function initFkStatsCounter() {
            $('.fk-stat-number').each(function() {
                var $el = $(this);
                if ($el.data('counted')) return;
                var count = parseInt($el.data('count'), 10);
                if (isNaN(count)) return;
                var top = $el.offset().top;
                var winTop = $(window).scrollTop();
                var winH = $(window).height();
                if (winTop + winH > top - 100) {
                    $el.data('counted', true);
                    animateValue(this, 0, count, 1500);
                }
            });
        }
        $(document).ready(function() {
            initFkStatsCounter();
            $(window).on('scroll', function() { initFkStatsCounter(); });
        });
    })(jQuery);
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make(getTemplate().'.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\ashut\Downloads\Telegram Desktop\rocket-lms_v1.8\Source\Source\resources\views/web/default/pages/home.blade.php ENDPATH**/ ?>