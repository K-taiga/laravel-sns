<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Article extends Model
{
    protected $fillable = [
        'title',
        'body',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo('App\User');
    }

    public function likes(): BelongsToMany
    {
        // 関連するモデル名,中間テーブル名の順
        // 第二引数を省略した場合、２つのモデル名のスネークケースになる(ex.article_user)
        return $this->belongsToMany('App\User', 'likes')->withTimestamps();
    }

    public function isLikedBy(?User $user): bool
    {
        return $user
        // likesを呼びその先のUserモデルが返ってくる
        // そこに渡ってきたUserの$user->idがあるかチェックしboolで返す
         ?(bool)$this->likes->where('id', $user->id)->count()
         :false;
    }
}
