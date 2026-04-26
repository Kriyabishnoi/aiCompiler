<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'file_name',
        'code',
        'language'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function executions()
    {
        return $this->hasMany(Execution::class, 'project_file_id');
    }
}
