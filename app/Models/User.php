<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        "created_at",
        "updated_at",
        "verify_token",
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function AllUserPost()
    {
        return $this->hasMany(Post::class,"user_id",'uid');
        //return $this->hasMany(Post::class,"F.K in posts",'P.K in users');
    }

    public function AllUserPostComments()
    {
        return $this->hasManyThrough(Comment::class,Post::class,'user_id','post_id','uid','pid');
        //return $this->hasManyThrough(Comment::class,Post::class,"F.K in post","F.k in comments","P.K in users",'P.k in posts');
    }


}
