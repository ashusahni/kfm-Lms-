<?php

namespace App\Models;

use App\Mixins\Certificate\MakeCertificate;
use App\User;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Jorenvh\Share\ShareFacade;
use Spatie\CalendarLinks\Link;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Webinar extends Model implements TranslatableContract
{
    use Translatable;
    use Sluggable;

    protected $table = 'webinars';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    static $active = 'active';
    static $pending = 'pending';
    static $isDraft = 'is_draft';
    static $inactive = 'inactive';

    static $webinar = 'webinar';
    static $course = 'course';
    static $textLesson = 'text_lesson';

    static $statuses = [
        'active', 'pending', 'is_draft', 'inactive'
    ];

    static $videoDemoSource = ['upload', 'youtube', 'vimeo', 'external_link'];

    /** Challenge course: batch-based with fixed duration (e.g. starts 1 Mar, ends 7 or 30 days later). */
    const CHALLENGE_7_DAYS = '7_days';
    const CHALLENGE_30_DAYS = '30_days';
    const CHALLENGE_TYPES = ['7_days', '30_days'];

    /** Program domain for health/fitness LMS: used for course structure (intake, health logs) and filtering. */
    const PROGRAM_DOMAIN_GENERAL = 'general';
    const PROGRAM_DOMAIN_HEALTH = 'health';
    const PROGRAM_DOMAIN_FITNESS = 'fitness';
    const PROGRAM_DOMAIN_NUTRITION = 'nutrition';
    const PROGRAM_DOMAIN_WELLNESS = 'wellness';
    const PROGRAM_DOMAINS = ['general', 'health', 'fitness', 'nutrition', 'wellness'];

    public $translatedAttributes = ['title', 'description', 'seo_description'];

    public function getTitleAttribute()
    {
        return getTranslateAttributeValue($this, 'title');
    }

    public function getDescriptionAttribute()
    {
        return getTranslateAttributeValue($this, 'description');
    }

    public function getSeoDescriptionAttribute()
    {
        return getTranslateAttributeValue($this, 'seo_description');
    }

    public function getPriceAttribute()
    {
        $result = $this->attributes['price'] ?? null;

        $user = auth()->user();

        if (!empty($this->attributes['organization_price']) and !empty($user) and $this->creator->isOrganization() and $user->organ_id == $this->creator_id) {
            $result = $this->attributes['organization_price'];
        }

        return $result;
    }

    public function creator()
    {
        return $this->belongsTo('App\User', 'creator_id', 'id');
    }

    public function teacher()
    {
        return $this->belongsTo('App\User', 'teacher_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id', 'id');
    }

    public function filterOptions()
    {
        return $this->hasMany('App\Models\WebinarFilterOption', 'webinar_id', 'id');
    }

    public function tickets()
    {
        return $this->hasMany('App\Models\Ticket', 'webinar_id', 'id');
    }


    public function chapters()
    {
        return $this->hasMany('App\Models\WebinarChapter', 'webinar_id', 'id');
    }

    public function sessions()
    {
        return $this->hasMany('App\Models\Session', 'webinar_id', 'id');
    }

    public function files()
    {
        return $this->hasMany('App\Models\File', 'webinar_id', 'id');
    }

    public function assignments()
    {
        return $this->hasMany('App\Models\WebinarAssignment', 'webinar_id', 'id');
    }

    public function textLessons()
    {
        return $this->hasMany('App\Models\TextLesson', 'webinar_id', 'id');
    }

    public function faqs()
    {
        return $this->hasMany('App\Models\Faq', 'webinar_id', 'id');
    }

    public function webinarExtraDescription()
    {
        return $this->hasMany('App\Models\WebinarExtraDescription', 'webinar_id', 'id');
    }

    public function prerequisites()
    {
        return $this->hasMany('App\Models\Prerequisite', 'webinar_id', 'id');
    }

    public function quizzes()
    {
        return $this->hasMany('App\Models\Quiz', 'webinar_id', 'id');
    }

    public function webinarPartnerTeacher()
    {
        return $this->hasMany('App\Models\WebinarPartnerTeacher', 'webinar_id', 'id');
    }

    /**
     * Dietitians assigned to this course by admin (many-to-many). They can manage batches and view client logs.
     */
    public function assignedDieticians()
    {
        return $this->belongsToMany('App\User', 'webinar_dietician', 'webinar_id', 'user_id');
    }

    public function tags()
    {
        return $this->hasMany('App\Models\Tag', 'webinar_id', 'id');
    }

    public function purchases()
    {
        return $this->hasMany('App\Models\Purchase', 'webinar_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\Comment', 'webinar_id', 'id');
    }

    public function reviews()
    {
        return $this->hasMany('App\Models\WebinarReview', 'webinar_id', 'id');
    }

    public function sales()
    {
        return $this->hasMany('App\Models\Sale', 'webinar_id', 'id')
            ->whereNull('refund_at')
            ->where('type', 'webinar');
    }

    public function batches()
    {
        return $this->hasMany('App\Models\CourseBatch', 'webinar_id', 'id')->orderBy('sort_order')->orderBy('start_date');
    }

    /**
     * Duration in days for challenge courses (7 or 30). Null if not a challenge course.
     */
    public function getChallengeDurationDays(): ?int
    {
        if ($this->challenge_type === self::CHALLENGE_7_DAYS) {
            return 7;
        }
        if ($this->challenge_type === self::CHALLENGE_30_DAYS) {
            return 30;
        }
        return null;
    }

    public function isChallengeCourse(): bool
    {
        return in_array($this->challenge_type ?? '', self::CHALLENGE_TYPES, true);
    }

    public function scopeHasActiveBatches($query)
    {
        return $query->whereHas('batches', function ($q) {
            $q->where('status', \App\Models\CourseBatch::STATUS_OPEN);
        });
    }

    public function feature()
    {
        return $this->hasOne('App\Models\FeatureWebinar', 'webinar_id', 'id');
    }

    public function noticeboards()
    {
        return $this->hasMany('App\Models\CourseNoticeboard', 'webinar_id', 'id');
    }

    public function forums()
    {
        return $this->hasMany('App\Models\CourseForum', 'webinar_id', 'id');
    }

    public function getRate()
    {
        $rate = 0;

        if (!empty($this->avg_rates)) {
            $rate = $this->avg_rates;
        } else {
            $reviews = $this->reviews()
                ->where('status', 'active')
                ->get();

            if (!empty($reviews) and $reviews->count() > 0) {
                $rate = number_format($reviews->avg('rates'), 2);
            }
        }


        if ($rate > 5) {
            $rate = 5;
        }

        return $rate > 0 ? number_format($rate, 2) : 0;
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public static function makeSlug($title)
    {
        return SlugService::createSlug(self::class, 'slug', $title);
    }

    public function bestTicket($with_percent = false)
    {
        $ticketPercent = 0;
        $bestTicket = $this->price;

        $activeSpecialOffer = $this->activeSpecialOffer();

        if ($activeSpecialOffer) {
            $bestTicket = $this->price - ($this->price * $activeSpecialOffer->percent / 100);
            $ticketPercent = $activeSpecialOffer->percent;
        } else {
            foreach ($this->tickets as $ticket) {

                if ($ticket->isValid()) {
                    $discount = $this->price - ($this->price * $ticket->discount / 100);

                    if ($bestTicket > $discount) {
                        $bestTicket = $discount;
                        $ticketPercent = $ticket->discount;
                    }
                }
            }
        }

        if ($with_percent) {
            return [
                'bestTicket' => $bestTicket,
                'percent' => $ticketPercent
            ];
        }

        return $bestTicket;
    }

    public function getDiscount($ticket = null, $user = null)
    {
        $activeSpecialOffer = $this->activeSpecialOffer();

        $discountOut = $activeSpecialOffer ? $this->price * $activeSpecialOffer->percent / 100 : 0;

        if (!empty($user) and !empty($user->getUserGroup()) and isset($user->getUserGroup()->discount) and $user->getUserGroup()->discount > 0) {
            $discountOut += $this->price * $user->getUserGroup()->discount / 100;
        }

        if (!empty($ticket) and $ticket->isValid()) {
            $discountOut += $this->price * $ticket->discount / 100;
        }

        return $discountOut;
    }

    public function getDiscountPercent()
    {
        $percent = 0;

        $activeSpecialOffer = $this->activeSpecialOffer();

        if (!empty($activeSpecialOffer)) {
            $percent += $activeSpecialOffer->percent;
        }

        $tickets = Ticket::where('webinar_id', $this->id)->get();

        foreach ($tickets as $ticket) {
            if (!empty($ticket) and $ticket->isValid()) {
                $percent += $ticket->discount;
            }
        }

        return $percent;
    }

    public function getWebinarCapacity()
    {
        $salesCount = !empty($this->sales_count) ? $this->sales_count : $this->sales()->count();

        $capacity = $this->capacity - $salesCount;

        return $capacity > 0 ? $capacity : 0;
    }

    public function getExpiredAccessDays($purchaseDate, $giftId = null)
    {
        if (!empty($giftId)) {
            $gift = Gift::query()->where('id', $giftId)
                ->where('status', 'active')
                ->first();

            if (!empty($gift) and !empty($gift->date)) {
                $purchaseDate = $gift->date;
            }
        }

        return strtotime("+{$this->access_days} days", $purchaseDate);
    }

    public function checkHasExpiredAccessDays($purchaseDate, $giftId = null)
    {
        // true => has access
        // false => not access (expired)

        if (!empty($giftId)) {
            $gift = Gift::query()->where('id', $giftId)
                ->where('status', 'active')
                ->first();

            if (!empty($gift) and !empty($gift->date)) {
                $purchaseDate = $gift->date;
            }
        }

        $time = time();

        return strtotime("+{$this->access_days} days", $purchaseDate) > $time;
    }

    public function getSaleItem($user = null)
    {
        if (empty($user)) {
            $user = auth()->user();
        }

        if (!empty($user)) {
            return Sale::query()->where('buyer_id', $user->id)
                ->where('webinar_id', $this->id)
                ->where('type', 'webinar')
                ->whereNull('refund_at')
                ->where('access_to_purchased_item', true)
                ->orderBy('created_at', 'desc')
                ->first();
        }

        return null;
    }

    /**
     * Whether the user can access video/content for this course.
     * Course content does not open immediately. Unlock rule:
     * - Student must have submitted the course (intake) form first.
     * - Content unlock delay = 48 hours after course form submission.
     * Purpose: dietician reviews case and prepares diet plan.
     * Access is granted when: intake submitted AND (48h passed since submission OR dietician set initial_conversation_at).
     */
    public function canUserAccessCourseContent($user = null): bool
    {
        if (empty($user) && auth()->check()) {
            $user = auth()->user();
        }
        if (empty($user)) {
            return false;
        }
        $sale = $this->getSaleItem($user);
        if (empty($sale)) {
            return false;
        }
        $intake = \App\Models\CourseIntake::where('user_id', $user->id)
            ->where('webinar_id', $this->id)
            ->first();
        if (empty($intake)) {
            return false;
        }
        $formSubmittedAt = (int) (is_object($intake->updated_at) ? $intake->updated_at->timestamp : strtotime($intake->updated_at));
        $initialConversationAt = $sale->initial_conversation_at ?? null;
        $fortyEightHoursSeconds = 48 * 3600;
        $unlockAfter = $formSubmittedAt + $fortyEightHoursSeconds;
        if ($initialConversationAt !== null && (int) $initialConversationAt > 0) {
            return true;
        }
        return time() >= $unlockAfter;
    }

    /**
     * Get the timestamp after which content unlocks (48h after form submission), or null if no intake yet.
     */
    public function getContentUnlockAfterTimestamp($user = null): ?int
    {
        if (empty($user) && auth()->check()) {
            $user = auth()->user();
        }
        if (empty($user)) {
            return null;
        }
        $intake = \App\Models\CourseIntake::where('user_id', $user->id)
            ->where('webinar_id', $this->id)
            ->first();
        if (empty($intake)) {
            return null;
        }
        $formSubmittedAt = (int) (is_object($intake->updated_at) ? $intake->updated_at->timestamp : strtotime($intake->updated_at));
        return $formSubmittedAt + (48 * 3600);
    }

    /**
     * When sequential_module_unlock is enabled: returns the number of consecutive chapters
     * (from the first) that the user has fully completed. 0 = only first chapter, 1 = first two, etc.
     * "Completed" = all file/session/text_lesson items in that chapter have a course_learning row for the user.
     */
    public function getUnlockedChapterIndexForUser($user = null): int
    {
        if (empty($user) && auth()->check()) {
            $user = auth()->user();
        }
        if (empty($user)) {
            return 0;
        }
        $chapters = $this->chapters()
            ->where('status', \App\Models\WebinarChapter::$chapterActive)
            ->orderBy('order')
            ->orderBy('id')
            ->get();
        if ($chapters->isEmpty()) {
            return 0;
        }
        $unlockedIndex = 0;
        foreach ($chapters as $index => $chapter) {
            $fileIds = $chapter->files()->where('status', 'active')->pluck('id')->toArray();
            $sessionIds = $chapter->sessions()->where('status', 'active')->pluck('id')->toArray();
            $textLessonIds = $chapter->textLessons()->where('status', 'active')->pluck('id')->toArray();
            if (empty($fileIds) && empty($sessionIds) && empty($textLessonIds)) {
                $unlockedIndex = $index + 1;
                continue;
            }
            $completedCount = \App\Models\CourseLearning::where('user_id', $user->id)
                ->where(function ($q) use ($fileIds, $sessionIds, $textLessonIds) {
                    if (!empty($fileIds)) {
                        $q->orWhereIn('file_id', $fileIds);
                    }
                    if (!empty($sessionIds)) {
                        $q->orWhereIn('session_id', $sessionIds);
                    }
                    if (!empty($textLessonIds)) {
                        $q->orWhereIn('text_lesson_id', $textLessonIds);
                    }
                })
                ->count();
            $requiredCount = count($fileIds) + count($sessionIds) + count($textLessonIds);
            if ($completedCount < $requiredCount) {
                break;
            }
            $unlockedIndex = $index + 1;
        }
        return $unlockedIndex;
    }

    /**
     * Returns the chapter order index (0-based) for use in sequential unlock. Chapters ordered by order asc, id asc.
     */
    public function getChapterOrderIndex($chapterId): ?int
    {
        $chapters = $this->chapters()
            ->where('status', \App\Models\WebinarChapter::$chapterActive)
            ->orderBy('order')
            ->orderBy('id')
            ->pluck('id')
            ->toArray();
        $pos = array_search((int) $chapterId, $chapters, true);
        return $pos !== false ? $pos : null;
    }

    public function checkUserHasBought($user = null, $checkExpired = true, $test = false): bool
    {
        $hasBought = false;

        if (empty($user) and auth()->check()) {
            $user = auth()->user();
        }

        if (!empty($user)) {
            $sale = $this->getSaleItem($user);

            if (!empty($sale)) {
                $hasBought = true;

                if ($sale->payment_method == Sale::$subscribe) {
                    $subscribe = $sale->getUsedSubscribe($sale->buyer_id, $sale->webinar_id);

                    if (!empty($subscribe)) {
                        $subscribeSaleCreatedAt = null;

                        if (!empty($subscribe->installment_order_id)) {
                            $installmentOrder = InstallmentOrder::query()->where('user_id', $user->id)
                                ->where('id', $subscribe->installment_order_id)
                                ->where('status', 'open')
                                ->whereNull('refund_at')
                                ->first();

                            if (!empty($installmentOrder)) {
                                $subscribeSaleCreatedAt = $installmentOrder->created_at;

                                if ($installmentOrder->checkOrderHasOverdue()) {
                                    $overdueIntervalDays = getInstallmentsSettings('overdue_interval_days');

                                    if (empty($overdueIntervalDays) or $installmentOrder->overdueDaysPast() > $overdueIntervalDays) {
                                        $hasBought = false;
                                    }
                                }
                            }
                        } else {
                            $subscribeSale = Sale::where('buyer_id', $user->id)
                                ->where('type', Sale::$subscribe)
                                ->where('subscribe_id', $subscribe->id)
                                ->whereNull('refund_at')
                                ->latest('created_at')
                                ->first();

                            if (!empty($subscribeSale)) {
                                $subscribeSaleCreatedAt = $subscribeSale->created_at;
                            }
                        }

                        if (!empty($subscribeSaleCreatedAt)) {
                            $usedDays = (int)diffTimestampDay(time(), $subscribeSaleCreatedAt);

                            if ($usedDays > $subscribe->days) {
                                $hasBought = false;
                            }
                        }
                    } else {
                        $hasBought = false;
                    }
                }

                if ($hasBought and !empty($this->access_days) and $checkExpired) {
                    $hasBought = $this->checkHasExpiredAccessDays($sale->created_at, $sale->gift_id);
                }
            }

            if (!$hasBought) {
                $hasBought = ($this->creator_id == $user->id or $this->teacher_id == $user->id);

                if (!$hasBought) {
                    $partnerTeachers = !empty($this->webinarPartnerTeacher) ? $this->webinarPartnerTeacher->pluck('teacher_id')->toArray() : [];

                    $hasBought = in_array($user->id, $partnerTeachers);
                }
            }

            if (!$hasBought) {
                $hasBought = $user->isAdmin();
            }

            if (!$hasBought) {
                $bundleWebinar = BundleWebinar::where('webinar_id', $this->id)
                    ->with([
                        'bundle'
                    ])->get();

                if ($bundleWebinar->isNotEmpty()) {
                    foreach ($bundleWebinar as $item) {
                        if (!empty($item->bundle) and $item->bundle->checkUserHasBought($user)) {
                            $hasBought = true;
                        }
                    }
                }
            }

            /* Check Installment */
            if (!$hasBought) {
                $installmentOrder = $this->getInstallmentOrder();

                if (!empty($installmentOrder)) {
                    $hasBought = true;

                    if ($installmentOrder->checkOrderHasOverdue()) {
                        $overdueIntervalDays = getInstallmentsSettings('overdue_interval_days');

                        if (empty($overdueIntervalDays) or $installmentOrder->overdueDaysPast() > $overdueIntervalDays) {
                            $hasBought = false;
                        }
                    }
                }
            }

            /* Check Gift */
            if (!$hasBought) {
                $gift = Gift::query()->where('email', $user->email)
                    ->where('status', 'active')
                    ->where('webinar_id', $this->id)
                    ->where(function ($query) {
                        $query->whereNull('date');
                        $query->orWhere('date', '<', time());
                    })
                    ->whereHas('sale')
                    ->first();

                if (!empty($gift)) {
                    $hasBought = true;
                }
            }
        }

        return $hasBought;
    }

    public function getInstallmentOrder()
    {
        $user = auth()->user();

        if (!empty($user)) {
            return InstallmentOrder::query()->where('user_id', $user->id)
                ->where('webinar_id', $this->id)
                ->where('status', 'open')
                ->whereNull('refund_at')
                ->first();
        }

        return null;
    }

    public function getFilesLearningProgressStat($userId = null)
    {
        $passed = 0;

        if (empty($userId)) {
            $userId = auth()->id();
        }

        $files = $this->files()
            ->where('status', 'active')
            ->get();

        foreach ($files as $file) {
            $status = CourseLearning::where('user_id', $userId)
                ->where('file_id', $file->id)
                ->first();

            if (!empty($status)) {
                $passed += 1;
            }
        }

        return [
            'passed' => $passed,
            'count' => count($files)
        ];
    }

    public function getSessionsLearningProgressStat($userId = null)
    {
        $passed = 0;

        if (empty($userId)) {
            $userId = auth()->id();
        }

        $sessions = $this->sessions()
            ->where('status', 'active')
            ->get();

        foreach ($sessions as $session) {
            $status = CourseLearning::where('user_id', $userId)
                ->where('session_id', $session->id)
                ->first();

            if (!empty($status)) {
                $passed += 1;
            }
        }

        return [
            'passed' => $passed,
            'count' => count($sessions)
        ];
    }

    public function getTextLessonsLearningProgressStat($userId = null)
    {
        $passed = 0;

        if (empty($userId)) {
            $userId = auth()->id();
        }

        $textLessons = $this->textLessons()
            ->where('status', 'active')
            ->get();

        foreach ($textLessons as $textLesson) {
            $status = CourseLearning::where('user_id', $userId)
                ->where('text_lesson_id', $textLesson->id)
                ->first();

            if (!empty($status)) {
                $passed += 1;
            }
        }

        return [
            'passed' => $passed,
            'count' => count($textLessons)
        ];
    }

    public function getAssignmentsLearningProgressStat($userId = null)
    {
        $passed = 0;

        if (empty($userId)) {
            $userId = auth()->id();
        }

        $assignments = $this->assignments()
            ->where('status', 'active')
            ->get();

        foreach ($assignments as $assignment) {
            $assignmentHistory = WebinarAssignmentHistory::where('assignment_id', $assignment->id)
                ->where('student_id', $userId)
                ->where('status', WebinarAssignmentHistory::$passed)
                ->first();

            if (!empty($assignmentHistory)) {
                $passed += 1;
            }
        }

        return [
            'passed' => $passed,
            'count' => count($assignments)
        ];
    }

    public function getQuizzesLearningProgressStat($userId = null)
    {
        $passed = 0;

        if (empty($userId)) {
            $userId = auth()->id();
        }

        $quizzes = $this->quizzes()
            ->where('status', 'active')
            ->get();

        foreach ($quizzes as $quiz) {
            $quizHistory = QuizzesResult::where('quiz_id', $quiz->id)
                ->where('user_id', $userId)
                ->where('status', QuizzesResult::$passed)
                ->first();

            if (!empty($quizHistory)) {
                $passed += 1;
            }
        }

        return [
            'passed' => $passed,
            'count' => count($quizzes)
        ];
    }

    public function getProgress($isLearningPage = false)
    {
        $progress = 0;

        if (
            auth()->check() and
            $this->checkUserHasBought() and
            (
                !$this->isWebinar() or
                ($this->isWebinar() and $this->isProgressing()) or
                $isLearningPage
            )
        ) {
            $user_id = auth()->id();

            $filesStat = $this->getFilesLearningProgressStat($user_id);
            $sessionsStat = $this->getSessionsLearningProgressStat($user_id);
            $textLessonsStat = $this->getTextLessonsLearningProgressStat($user_id);
            $assignmentsStat = $this->getAssignmentsLearningProgressStat($user_id);
            $quizzesStat = $this->getQuizzesLearningProgressStat($user_id);

            $passed = $filesStat['passed'] + $sessionsStat['passed'] + $textLessonsStat['passed'] + $assignmentsStat['passed'] + $quizzesStat['passed'];
            $count = $filesStat['count'] + $sessionsStat['count'] + $textLessonsStat['count'] + $assignmentsStat['count'] + $quizzesStat['count'];

            if ($passed > 0 and $count > 0) {
                $progress = ($passed * 100) / $count;

                $this->handleLearningProgress100Reward($progress, $user_id, $this->id);
            }
        } else if (!is_null($this->capacity)) {
            $salesCount = !empty($this->sales_count) ? $this->sales_count : $this->sales()->count();

            if ($salesCount > 0) {
                $progress = ($salesCount * 100) / $this->capacity;
            }
        }

        return round($progress, 2);
    }

    public function checkShowProgress($isLearningPage = false)
    {
        $show = false;

        if (
            auth()->check() and
            $this->checkUserHasBought() and
            (
                !$this->isWebinar() or
                ($this->isWebinar() and $this->isProgressing()) or
                $isLearningPage
            )
        ) {
            $show = true;
        } else if (!is_null($this->capacity)) {
            $show = true;
        }

        return $show;
    }

    public function handleLearningProgress100Reward($progress, $userId, $itemId)
    {
        if ($progress >= 100) {
            $rewardScore = RewardAccounting::calculateScore(Reward::LEARNING_PROGRESS_100);
            RewardAccounting::makeRewardAccounting($userId, $rewardScore, Reward::LEARNING_PROGRESS_100, $itemId, true);
        }
    }

    public function getImageCover()
    {
        return $this->image_cover;
    }

    public function getImage()
    {
        return $this->thumbnail;
    }

    public function getUrl()
    {
        return url('/course/' . $this->slug);
    }

    public function getLearningPageUrl()
    {
        return url('/course/learning/' . $this->slug);
    }

    public function getNoticeboardsPageUrl()
    {
        return $this->getLearningPageUrl() . '/noticeboards';
    }

    public function getForumPageUrl()
    {
        return $this->getLearningPageUrl() . '/forum';
    }

    public function isCourse()
    {
        return ($this->type == 'course');
    }

    public function isTextCourse()
    {
        return ($this->type == 'text_lesson');
    }

    public function isWebinar()
    {
        return ($this->type == 'webinar');
    }

    public function canAccess($user = null)
    {
        $result = false;

        if (!$user) {
            $user = auth()->user();
        }

        if (!empty($user)) {
            if ($this->creator_id == $user->id or $this->teacher_id == $user->id) {
                $result = true;
            }

            // Allow Access To Partner Teachers
            if (!$result and $this->isPartnerTeacher($user->id)) {
                $result = true;
            }
        }

        return $result;
    }

    public function canSale()
    {
        $result = true;

        if (!is_null($this->capacity)) {
            $salesCount = !empty($this->sales_count) ? $this->sales_count : $this->sales()->count();

            $result = $salesCount < $this->capacity;
        }

        if ($result and $this->type == 'webinar') {
            $result = ($this->start_date > time());
        }

        return $result;
    }

    public function canJoinToWaitlist()
    {
        $hasBought = $this->checkUserHasBought();

        return ($this->enable_waitlist and !$hasBought and !$this->canSale());
    }

    public function cantSaleStatus($hasBought)
    {
        $status = '';

        if ($hasBought) {
            $status = 'js-course-has-bought-status';
        } else {

            if (!is_null($this->capacity)) {
                $salesCount = !empty($this->sales_count) ? $this->sales_count : $this->sales()->count();

                if ($salesCount >= $this->capacity) {
                    $status = 'js-course-not-capacity-status';
                }
            } elseif ($this->type == 'webinar' and $this->start_date <= time()) {
                $status = 'js-course-has-started-status';
            }
        }

        return $status;
    }

    public function addToCalendarLink()
    {

        $date = \DateTime::createFromFormat('j M Y H:i', dateTimeFormat($this->start_date, 'j M Y H:i', false));

        $link = Link::create($this->title, $date, $date); //->description('Cookies & cocktails!')

        return $link->google();
    }

    public function activeSpecialOffer()
    {
        $activeSpecialOffer = SpecialOffer::where('webinar_id', $this->id)
            ->where('status', SpecialOffer::$active)
            ->where('from_date', '<', time())
            ->where('to_date', '>', time())
            ->first();

        return $activeSpecialOffer ?? false;
    }

    public function nextSession()
    {
        $sessions = $this->sessions()
            ->orderBy('date', 'asc')
            ->get();
        $time = time();

        foreach ($sessions as $session) {
            if ($session->date > $time) {
                return $session;
            }
        }

        return null;
    }

    public function lastSession()
    {
        $session = $this->sessions()
            ->orderBy('date', 'desc')
            ->first();

        return $session;
    }

    public function isProgressing()
    {
        $lastSession = $this->lastSession();
        //$nextSession = $this->nextSession();
        $isProgressing = false;

        if (!empty($lastSession)) {
            $agoraHistory = null;
            // Safety check: only query agora_history if table exists
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('agora_history')) {
                    $agoraHistory = AgoraHistory::where('session_id', $lastSession->id)
                        ->first();
                }
            } catch (\Exception $e) {
                // Table doesn't exist or other error, continue without agora_history
                $agoraHistory = null;
            }

            if (
                ($lastSession->session_api != "agora" and $lastSession->date > time()) or
                ($lastSession->session_api == "agora" and ($lastSession->date > time() and (empty($agoraHistory) or empty($agoraHistory->end_at))))
            ) {
                $isProgressing = true;
            }
        }

        if ($this->start_date > time()) {
            $isProgressing = true;
        }

        return $isProgressing;
    }

    public function getShareLink($social)
    {
        $link = ShareFacade::page($this->getUrl(), $this->title)
            ->facebook()
            ->twitter()
            ->whatsapp()
            ->telegram()
            ->getRawLinks();

        return !empty($link[$social]) ? $link[$social] : '';
    }

    public function isDownloadable()
    {
        $downloadable = $this->downloadable;

        if ($this->files->count() > 0) {
            $downloadableFiles = $this->files->where('downloadable', true)->count();

            if ($downloadableFiles > 0) {
                $downloadable = true;
            }
        }

        return $downloadable;
    }

    public function isOwner($userId = null)
    {
        if (empty($userId)) {
            $userId = auth()->id();
        }

        return (($this->creator_id == $userId) or ($this->teacher_id == $userId));
    }

    public function isPartnerTeacher($userId = null)
    {
        if (empty($userId)) {
            $userId = auth()->id();
        }

        $partnerTeachers = !empty($this->webinarPartnerTeacher) ? $this->webinarPartnerTeacher->pluck('teacher_id')->toArray() : [];

        return in_array($userId, $partnerTeachers);
    }

    public function getPrice()
    {
        $price = $this->price;

        $specialOffer = $this->activeSpecialOffer();
        if (!empty($specialOffer)) {
            $price = $price - ($price * $specialOffer->percent / 100);
        }

        return $price;
    }

    public function getStudentsIds()
    {
        $studentsIds = Sale::query()->where('webinar_id', $this->id)
            ->whereNull('refund_at')
            ->whereHas('buyer')
            ->pluck('buyer_id')
            ->toArray();

        // get users by installments
        $installmentOrders = InstallmentOrder::query()
            ->where('webinar_id', $this->id)
            ->where('status', 'open')
            ->whereNull('refund_at')
            ->get();

        foreach ($installmentOrders as $installmentOrder) {
            if (!empty($installmentOrder)) {
                $hasBought = true;

                if ($installmentOrder->checkOrderHasOverdue()) {
                    $overdueIntervalDays = getInstallmentsSettings('overdue_interval_days');

                    if (empty($overdueIntervalDays) or $installmentOrder->overdueDaysPast() > $overdueIntervalDays) {
                        $hasBought = false;
                    }
                }

                if ($hasBought) {
                    $studentsIds[] = $installmentOrder->user_id;
                }
            }
        }

        // get users by gifts
        $gifts = Gift::query()
            ->where('status', 'active')
            ->where('webinar_id', $this->id)
            ->where(function ($query) {
                $query->whereNull('date');
                $query->orWhere('date', '<', time());
            })
            ->whereHas('sale')
            ->get();

        foreach ($gifts as $gift) {
            $user = User::query()->select('id', 'email')->where('email', $gift->email)->first();

            if (!empty($user)) {
                $studentsIds[] = $user->id;
            }
        }

        // get users by bundle
        $bundleWebinar = BundleWebinar::where('webinar_id', $this->id)
            ->with([
                'bundle'
            ])->get();

        if ($bundleWebinar->isNotEmpty()) {
            foreach ($bundleWebinar as $item) {
                if (!empty($item->bundle)) {
                    $bundleStudents = $item->bundle->getStudentsIds();

                    $studentsIds = array_merge($studentsIds, $bundleStudents);
                }
            }
        }

        return array_unique($studentsIds);
    }

    public function sendNotificationToAllStudentsForNewQuizPublished($quiz)
    {
        $studentsIds = $this->getStudentsIds();

        $notifyOptions = [
            '[q.title]' => $quiz->title,
            '[c.title]' => $this->title
        ];

        if (count($studentsIds)) {
            foreach ($studentsIds as $studentId) {
                sendNotification("new_quiz", $notifyOptions, $studentId);
            }
        }

        $gifts = Gift::query()
            ->where('status', 'active')
            ->where('webinar_id', $this->id)
            ->where(function ($query) {
                $query->whereNull('date');
                $query->orWhere('date', '<', time());
            })
            ->whereHas('sale')
            ->get();

        foreach ($gifts as $gift) {
            $user = User::query()->select('id', 'email')->where('email', $gift->email)->first();

            if (empty($user)) {
                sendNotificationToEmail("new_quiz", $notifyOptions, $gift->email);
            }
        }
    }

    public function makeCourseCertificateForUser($user)
    {
        if (!empty($user) and $this->certificate and $this->getProgress(true) >= 100) {
            $check = Certificate::where('type', 'course')
                ->where('student_id', $user->id)
                ->where('webinar_id', $this->id)
                ->first();

            if (empty($check)) {
                $makeCertificate = new MakeCertificate();
                $userCertificate = $makeCertificate->saveCourseCertificate($user, $this);

                $certificateReward = RewardAccounting::calculateScore(Reward::CERTIFICATE);
                RewardAccounting::makeRewardAccounting($userCertificate->student_id, $certificateReward, Reward::CERTIFICATE, $userCertificate->id, true);
            }
        }
    }

}
