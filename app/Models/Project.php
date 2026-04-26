<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'language',
        'code'
    ];

    /**
     * Auto cast attributes
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    

    /**
     * Project belongs to a User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
