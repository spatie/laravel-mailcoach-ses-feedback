<?php

namespace Spatie\MailcoachSesFeedback\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SesProcessedMessage extends Model
{
    public $table = 'mailcoach_ses_processed_messages';

    protected $guarded = [];

    protected $casts = [

    ];

}
