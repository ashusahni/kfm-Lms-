<?php

namespace App\Providers;

use App\Models\Api\CourseForumAnswer;
use App\Models\Webinar;
use App\Models\CourseForum;
use App\Models\Section;
use App\Policies\CourseForumAnswerPolicy;
use App\Policies\CourseForumPolicy;
use App\Policies\WebinarPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        CourseForum::class => CourseForumPolicy::class,
        CourseForumAnswer::class => CourseForumAnswerPolicy::class ,
        Webinar::class => WebinarPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {

        $this->registerPolicies();

        // Load section-based gates only when the sections table exists and is reachable (avoids 500 on fresh deploy or missing migrations)
        try {
            if (!Schema::hasTable('sections')) {
                return;
            }
        } catch (\Throwable $e) {
            return; // DB not ready or connection failed; skip gates
        }

        try {
            $minutes = 60 * 60; // 1 hour
            $sections = Cache::remember('sections', $minutes, function () {
                return Section::all();
            });

            foreach ($sections as $section) {
                Gate::define($section->name, function ($user) use ($section) {
                    return $user->hasPermission($section->name);
                });
            }
        } catch (\Throwable $e) {
            // Sections table empty, DB error, or cache issue; continue without section gates
        }


        //
    }
}
