<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CallLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'call_sid',
        'transcript',
        'simulated_query',
        'duration',
        'escalated',
        'recording_url',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'escalated' => 'boolean',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function conversationMessages(): HasMany
    {
        return $this->hasMany(ConversationMessage::class);
    }
}
