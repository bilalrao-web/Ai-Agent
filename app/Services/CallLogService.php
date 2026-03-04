<?php

namespace App\Services;

use App\Models\CallLog;
use App\Models\ConversationMessage;

class CallLogService
{
    public function createLog(?int $customerId, string $simulatedQuery, ?string $callSid = null): CallLog
    {
        return CallLog::create([
            'customer_id' => $customerId,
            'simulated_query' => $simulatedQuery,
            'call_sid' => $callSid,
            'duration' => 0,
            'status' => 'completed',
        ]);
    }

    public function addMessage(int $callLogId, string $role, string $content): ConversationMessage
    {
        return ConversationMessage::create([
            'call_log_id' => $callLogId,
            'role' => $role,
            'content' => $content,
        ]);
    }
}
