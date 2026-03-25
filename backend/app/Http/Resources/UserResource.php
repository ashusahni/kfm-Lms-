<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */

    public function toArray($request)
    {
        $email = $this->email;

        // Hide student email from dieticians/teachers & organizations to prevent direct contact
        $authUser = auth()->user();
        if (!empty($authUser) && method_exists($authUser, 'isTeacher') && method_exists($authUser, 'isOrganization')) {
            if (($authUser->isTeacher() || $authUser->isOrganization()) && $this->role_name === \App\Models\Role::$user) {
                $email = null;
            }
        }

        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'email' => $email,
            'rate' => $this->rates(),
            'headline' => $this->headline,
            'public_message' => (bool)$this->public_message,
            'offline' => (bool)$this->offline,
            'offline_message' => $this->offline_message,
            'verified' => (bool)$this->verified,
            'followers_count' => $this->followers()->count(),
            'following_count' => $this->following()->count(),
            'badges' => $this->badges,
            'auth_user_is_follower' => $this->authUserIsFollower,
            'about' => $this->about,


            'course_progress_count' => $this->course_progress,
            'passed_quizzes_count' => $this->passed_quizzes,
            'unsent_assignments_count' => $this->unsent_assignments,
            'pending_assignments_count' => $this->pending_assignments,


        ];
    }
}
