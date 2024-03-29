<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

use Auth;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable implements JWTSubject
{
    use Traits\ActiveUserHelper;
    use Traits\LastActivedAtHelper;
    
    use HasRoles;
    
    use Notifiable{
        notify as protected laravelNotify;
    }
    // 其实 Notifiable 这个 trait 自带 notify 方法发送消息通知，但是我们这里需要改写它，但同时又需要使用自带的这个 notify 方法，
    // 为了避免歧义，我们引用时用 `notify as protected laravelNotify` => 相当于把 Notifiable 这个 trait 中的 notify 方法改名为 laravelNotify 

    

    public function notify($instance)
    {
        //如果要通知的人是当前用户，就不必通知了！
        if ($this->id == Auth::id()) //这里的$this 是 $topic->user，Auth::id() 其实就是$reply->user->id
        {
            return;  
        }
        $this->increment('notification_count');
        $this->laravelNotify($instance);
    }

    public function markAsRead(){
        $this->notification_count = 0;
        $this->save();
        $this->unreadNotifications->markAsRead();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','introduction','avatar','phone',
        'weixin_openid', 'weixin_unionid','registration_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function topics()
    {
    	return $this->hasMany(Topic::class);
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function isAuthorOf($model)
    {
    	return $this->id == $model->user_id;
    }

    public function setPasswordAttribute($value)
    {
        //如果值的长度等于60，即认为已经做过加密
        if (strlen($value) !=60) {
            $value = bcrypt($value);
        }
        
        $this->attributes['password'] = $value;
    }

    public function setAvatarAttribute($path)
    {
        //如果不是http开头 那就是从后台管理上传的 需要补全URL
        if (! starts_With($path,'http')) {
            $path = config('app.url')."/uploads/images/avatars/$path";
        }
        $this->attributes['avatar'] = $path;
        
    }

     // Rest omitted for brevity

     public function getJWTIdentifier()
     {
         return $this->getKey();
     }
 
     public function getJWTCustomClaims()
     {
         return [];
     }

}
