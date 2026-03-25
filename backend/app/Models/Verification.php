<?php

namespace App\Models;

use App\Notifications\SendVerificationEmailCode;
use App\Notifications\SendVerificationSMSCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Verification extends Model
{
    use Notifiable;

    protected $table = 'verifications';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    const EXPIRE_TIME = 3600; // second => 1 hour

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function sendEmailCode()
    {
        if (config('app.verification_method') === 'magic_link') {
            $this->sendMagicLink();
            return;
        }
        $this->notify(new SendVerificationEmailCode($this));
    }

    public function sendMagicLink()
    {
        $this->notify(new \App\Notifications\SendVerificationMagicLink($this));
    }

    public function sendSMSCode()
    {
        $this->notify(new SendVerificationSMSCode($this));
    }
}
