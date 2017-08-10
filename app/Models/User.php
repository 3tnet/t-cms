<?php

namespace App\Models;

use App\Models\Traits\Listable;
use Config;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Ty666\PictureManager\Traits\Picture;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends BaseModel implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;
    use Notifiable, Picture, Listable;
    use Authorizable {
        Authorizable::can as may;
        EntrustUserTrait::can insteadof Authorizable;
    }
    use EntrustUserTrait {
        restore as private restoreEntrust;
    }

    protected static $allowSortFields = ['id', 'user_name', 'nick_name', 'created_at', 'is_locked'];
    protected static $allowSearchFields = ['id', 'user_name', 'nick_name', 'email'];

    protected $casts = [
        'is_locked' => 'boolean'
    ];
    //use SoftDeletes { restore as private restoreSoftDelete; }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_name', 'nick_name', 'email', 'password', 'avatar'
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
     * For EntrustUserTrait and SoftDeletes conflict
     */
    public function restore()
    {
        $this->restoreEntrust();
        //$this->restoreSoftDelete();
    }

    public function getAvatarUrlsAttribute()
    {
        return $this->getPicure($this->avatar, ['is', 'xs', 'sm', 'md'], asset('images/default_avatar.jpg'));
    }

    public function getAvatar($style, $defaultAvatar = '')
    {
        return $this->getPicure($this->avatar, $style, $defaultAvatar);
    }



    /**
     * 角色的多对多关联
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Config::get('entrust.role'), Config::get('entrust.role_user_table'), Config::get('entrust.user_foreign_key'), Config::get('entrust.role_foreign_key'))
            ->ordered()->recent();
    }
}
