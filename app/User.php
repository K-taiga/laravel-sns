<?php

namespace App;

use App\Mail\BareMail;
use App\Notifications\PasswordResetNotification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordResetNotification($token,new BareMail()));
    }

    public function followers(): BelongsToMany
    {
        // リレーション元のusersテーブルはfollowee_idと紐づく
        // リレーション先のusersテーブルはfollower_idと紐づく
        return $this->belongsToMany('App\User','follows','followee_id','follower_id')->withTimestamps();
    }

    public function followings(): BelongsToMany
    {
        // リレーション元のusersテーブルはfollower_idと紐づく
        // リレーション先のusersテーブルはfollowee_idと紐づく
        return $this->belongsToManY('App\User','follows','follower_id','followee_id')->withTimestamps();
    }

    public function isFollowedBy(?User $user): bool
    {
        // followsのリレーション先(folloer_id)にuserのidがあるか
        return $user ? (bool)$this->followers->where('id', $user->id)->count() : false;
    }
}
