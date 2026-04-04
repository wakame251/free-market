<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'users_name',
        'avatar_path',
        'post_code',
        'address',
        'building',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // プロフィール画像URL
    // Seeder画像: images/sample/xxx.png → asset(...)
    // アップロード画像: avatars/xxx.png → asset('storage/' . ...)
    public function getAvatarUrlAttribute(): ?string
    {
        if (empty($this->avatar_path)) {
            return null;
        }

        if (str_starts_with($this->avatar_path, 'images/')) {
            return asset($this->avatar_path);
        }

        return asset('storage/' . $this->avatar_path);
    }
}