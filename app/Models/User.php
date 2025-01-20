<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Namu\WireChat\Traits\Chatable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{

    use Notifiable;
    use HasRoles;
    use Chatable;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'language',
        'city',
        'state',
        'age',
        'phone',
        'bio',
        'role',
        'status',
        'otp',
        'role',
        'lat',
        'lng',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
            'password' => 'hashed',
        ];
    }

    // Implement JWTSubject methods
    public function getJWTIdentifier()
    {
        return $this->getKey(); // Typically the user ID
    }

    public function getJWTCustomClaims()
    {
        return []; // Add any custom claims here
    }

    /**
     * Get The Users Images
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany(UserImages::class);
    }


    // Custom logic for allowing chat creation
    public function canCreateChats(): bool
    {
        return $this->hasVerifiedEmail();
    }

     /**
    * Returns the URL for the user's cover image (avatar).
    * Adjust the 'avatar_url' field to your database setup.
    */
    public function getCoverUrlAttribute(): ?string
    {
      return $this->avatar ?? null;
    }

    /**
    * Returns the display name for the user.
    * Modify this to use your preferred name field.
    */
    public function getDisplayNameAttribute(): ?string
    {
      return ucfirst($this->username) ?? 'Anonymous';
    }

    /**
    * Search for users when creating a new chat or adding members to a group.
    * Customize the search logic to limit results, such as restricting to friends or eligible users only.
    */
    public function searchChatables(string $query)
    {
     $searchableFields = ['username', 'email'];
     return User::where(function ($queryBuilder) use ($searchableFields, $query) {
        foreach ($searchableFields as $field) {
                $queryBuilder->orWhere($field, 'LIKE', '%'.$query.'%');
        }
      })
        ->limit(20)
        ->get();
    }

    public function videos()
    {
        return $this->hasMany(VideoUpload::class);
    }

    public function image()
    {
        return $this->hasMany(UserImages::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    //backend
    public function reportuser()
    {
        return $this->hasMany(ReportUser::class, 'reported_user_id');
    }

    //block users
    public function blockuser()
    {
        return $this->hasMany(BlockUser::class, 'blocked_user_id');
    }
//social Media
    public function socialmedia()
    {
        return $this->hasMany(Socialmedia::class);
    }



}
