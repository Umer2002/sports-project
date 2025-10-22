<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BugReport extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'category',
        'severity',
        'description',
        'steps',
        'environment',
        'include_logs',
        'contact',
        'share_diagnostics',
        'attachment_path',
    ];
}
