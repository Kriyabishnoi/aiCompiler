<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Execution extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_file_id',
        'output',
        'status',
        'execution_time',
        'executed_at',
        'language'
    ];

    // Execution belongs to a ProjectFile
    public function projectFile()
    {
        return $this->belongsTo(ProjectFile::class, 'project_file_id');
    }
}
