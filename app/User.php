<?php

namespace App;

use Illuminate\Notifications\Notifiable;
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
    
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }
    
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    public function follow($userId)
    {
        // 既にフォローしているかの確認
        $exist = $this->is_following($userId);
        // 自分自身ではないかの確認
        $its_me = $this->id == $userId;
    
        if ($exist || $its_me) {
            // 既にフォローしていれば何もしない
            return false;
        } else {
            // 未フォローであればフォローする
            $this->followings()->attach($userId);
            return true;
        }
    }
    
    public function unfollow($userId)
    {
        // 既にフォローしているかの確認
        $exist = $this->is_following($userId);
        // 自分自身ではないかの確認
        $its_me = $this->id == $userId;
    
        if ($exist && !$its_me) {
            // 既にフォローしていればフォローを外す
            $this->followings()->detach($userId);
            return true;
        } else {
            // 未フォローであれば何もしない
            return false;
        }
    }
    
    public function is_following($userId) {
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    public function feed_microposts(){
        $fllow_user_ids = $this->followings()->pluck('users.id')->toArray();
        $fllow_user_ids[] = $this->id;
        return Micropost::whereIn('user_id', $fllow_user_ids);
    }
    
    public function favoritePosts()
    {
        return $this->belongsToMany(Micropost::class, 'user_favorite', 'user_id', 'favorite_id')->withTimestamps();
    }
    
    public function favorite($micropostId){
        // 既にお気に入りかどうか
        $exist = $this->is_favorite($micropostId);

        if ($exist) {
            // 既にお気に入り登録していれば何もしない
            return false;
        } else {
            // 未登録であればお気に入り
            $this->favoritePosts()->attach($micropostId);
            return true;
        }
    }
    
    public function unfavorite($micropostId){
        // 既にお気に入りかどうか
        $exist = $this->is_favorite($micropostId);

        if ($exist) {
            // 既にお気に入り登録していればお気に入りから外す
            $this->favoritePosts()->detach($micropostId);
            return true;
        } else {
            // 未登録であれば何もしない
            return false;
        }
    }
    
    public function is_favorite($micropostId) {
        return $this->favoritePosts()->where('favorite_id', $micropostId)->exists();
    }
    
}
