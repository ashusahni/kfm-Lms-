<?php

/**
 * Fit Karnataka Mission – Feature toggles and home section defaults.
 * When enabled, the LMS runs as health + diet + live coaching (not generic marketplace).
 */

return [

    'enabled' => env('FIT_KARNATAKA_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Feature toggles (disable for Fit Karnataka launch)
    |--------------------------------------------------------------------------
    */
    'disable' => [
        'forum' => true,
        'store_products' => true,
        'reward_points' => true,
        'gift' => true,
        'affiliate_referral' => true,
        'instructor_finder' => true,
        'organizations' => true,
        'subscription_plans' => false, // set true if not using subscriptions at launch
        'ad_banners' => true,
        'bundles' => false, // set true to hide bundles at launch
        'instructor_blog' => true,
        'become_instructor_public' => false, // true = hide from home if instructors are curated only
    ],

    /*
    |--------------------------------------------------------------------------
    | Home sections: only these are shown when Fit Karnataka is enabled.
    | Order and visibility are still controlled by admin home_sections table;
    | this list is the allow-list (sections not in this list are hidden on home).
    |--------------------------------------------------------------------------
    */
    'home_sections_allow' => [
        'latest_classes',      // shown as "Latest Programs"
        'featured_classes',   // shown as "Featured Programs"
        'instructors',
        'testimonials',
        'upcoming_courses',
        'video_or_image_section',
        'blog',
        'free_classes',       // optional
        'discount_classes',   // optional
    ],

    /*
    |--------------------------------------------------------------------------
    | Home sections to hide (even if enabled in DB)
    |--------------------------------------------------------------------------
    */
    'home_sections_hide' => [
        'latest_bundles',
        'best_rates',
        'best_sellers',
        'trend_categories',
        'store_products',
        'subscribes',
        'find_instructors',
        'reward_program',
        'become_instructor',
        'forum_section',
        'organizations',
        'full_advertising_banner',
        'half_advertising_banner',
    ],

    /*
    |--------------------------------------------------------------------------
    | Terminology overrides (keys used in lang files)
    |--------------------------------------------------------------------------
    */
    'terminology' => [
        'courses' => 'Programs',
        'course' => 'Program',
        'classes' => 'Programs',
        'class' => 'Program',
        'assignments' => 'Daily Challenges',
        'assignment' => 'Daily Challenge',
        'reviews' => 'Testimonials',
        'review' => 'Testimonial',
    ],

];
