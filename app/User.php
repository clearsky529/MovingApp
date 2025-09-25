<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\API\v1\OauthAccessToken;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Aws\S3\S3Client;

class User extends Authenticatable
{
    use Notifiable, HasRoles, HasApiTokens, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password', 'role_id', 'status', 'created_by','is_admin'
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function companyUser()
    {
        return $this->hasOne('App\CompanyUser','user_id');
    }

    public function company()
    {
        return $this->hasOne('App\Companies','tbl_users_id');
    }

    public function getActiveSubscription()
    {
        return $this->hasOne('App\UserSubscription','user_id')->latest();
    }
    
    public function AauthAcessToken()
    {
      return $this->hasMany('\App\OauthAccessToken');
    }

    public function getProfilepic()
    {
        
        $img = $this->profile_pic;
        $s3_base_url = config('filesystems.disks.s3.url');
        $s3_image_path = $s3_base_url.'clientsignature/';
        if(\Storage::has("/userprofile/".$img) && $img != '' ){
            return  asset($s3_image_path.$this->profile_pic);
        }else{
            return asset($s3_image_path.'avatar-placeholder.png');
            // return asset('public/user_image/avatar-placeholder.png');
        }
    }

    public function getRefferdCompany()
    {
        return $this->hasMany('App\Companies','referred_by','id');
    }
}
