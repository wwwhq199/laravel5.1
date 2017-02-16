<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model /*implements AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract*/
{
    //use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    //监听事件
    public static function boot() {
        parent::boot();
        //模型成功创建之前触发
        static::creating(function ($user) {
             $user->activation_token = str_random(30);
            //dump($user);
        });

        //模型成功创建之后触发
        //static::created(function () {});
    }
    public function gravatar($size = '100')
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }

    public function setPasswordAttribute($password) {
        $this->attributes['password'] = bcrypt($password);
    }

    function inform($inform) {
        var_dump($inform);
    }

    //用户和发送的微博的关联

    function statuses() {
        return $this->hasMany(Status::class);
    }

    public function feed()
    {
        $user_ids = Auth::user()->followings->pluck('id')->toArray();
        array_push($user_ids, Auth::user()->id);
        return Status::whereIn('user_id', $user_ids)
            ->with('user')
            ->orderBy('created_at', 'desc');
    }
    // 粉丝
    function followers() {
        return $this->belongsToMany(User::class,'followers','user_id','follower_id');
    }

    // 关注的人
    public function followings() {
       return $this->belongsToMany(User::class,'followers','follower_id','user_id');
    }

    //关注操作
    function follow($user_ids) {
        if(!is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }

        $this->followings()->sync($user_ids,false);
    }

    //取消关注
    function unFollow($user_ids) {
        if(!is_array($user_ids)) {
            $user_ids = compact('user_id');
        }

        $this->followings()->detach($user_ids);
    }
    //检测当前用户是否关注了某一个用户

    public function isFollowing($use_id) {
        return $this->followings->contains($use_id);
    }
}
