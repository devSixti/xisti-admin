<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushEventTemplate extends Model
{
    protected $table = 'push_event_templates';

    protected $fillable = [
        'event_key',
        'label',
        'audience',
        'category',
        'app_notification_type',
        'title_code',
        'message_code',
        'title_es',
        'message_es',
        'title_en',
        'message_en',
        'sound_profile',
        'placeholders',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
