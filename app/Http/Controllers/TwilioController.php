<?php

namespace App\Http\Controllers;

use App\Models\CallLog;
use App\Models\ConversationMessage;
use App\Models\Customer;
use App\Services\CallLogService;
use App\Services\GeminiService;
use App\Services\TwilioService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TwilioController extends Controller
{
    public function __construct(
        protected TwilioService $twilioService,
        protected CallLogService $callLogService,
        protected GeminiService $geminiService
    ) {}

    public function handleInbound(Request $request): Response
    {
        $callerNumber = $request->input('From');
        $callSid = $request->input('CallSid');

        $customer = Customer::where('phone', $callerNumber)->first();
        $customerId = $customer?->id;

        $simulatedQuery = "Inbound call from {$callerNumber}";
        $callLog = $this->callLogService->createLog($customerId, $simulatedQuery, $callSid);
        if ($callSid) {
            $callLog->update(['status' => 'in_progress']);
        }

        session(['call_log_id' => $callLog->id, 'customer_id' => $customerId]);

        $actionUrl = route('twilio.process-speech', [
            'call_log_id' => $callLog->id,
            'customer_id' => $customerId ?? 0,
        ]);

        $twiml = $this->twilioService->buildGatherResponse(
            'Welcome! How can I help you today? Please speak your query.',
            $actionUrl
        );

        return response($twiml, 200, ['Content-Type' => 'text/xml']);
    }

    public function processSpeech(Request $request): Response
    {
        $userSpeech = $request->input('SpeechResult', '');
        $callLogId = (int) $request->input('call_log_id');
        $customerIdInput = (int) $request->input('customer_id');
        $customerId = $customerIdInput > 0 ? $customerIdInput : null;

        if ($callLogId < 1) {
            $twiml = $this->twilioService->buildVoiceResponse('Sorry, we could not identify your call. Goodbye.');
            return response($twiml, 200, ['Content-Type' => 'text/xml']);
        }

        $this->callLogService->addMessage($callLogId, 'user', $userSpeech);

        $context = ['customer_name' => null];
        if ($customerId) {
            $customer = Customer::with([
                'orders' => fn ($q) => $q->latest()->limit(3),
                'tickets' => fn ($q) => $q->latest()->limit(3),
            ])->find($customerId);
            if ($customer) {
                $context['customer_name'] = $customer->name;
                $context['orders'] = $customer->orders->map(fn ($o) => [
                    'id' => $o->id,
                    'order_number' => $o->order_number,
                    'status' => $o->status,
                    'delivery_date' => $o->delivery_date?->toDateString(),
                ])->toArray();
                $context['tickets'] = $customer->tickets->map(fn ($t) => [
                    'id' => $t->id,
                    'issue_type' => $t->issue_type,
                    'status' => $t->status,
                ])->toArray();
            }
        }
        if (empty($context['orders'])) {
            $context['orders'] = [];
        }
        if (empty($context['tickets'])) {
            $context['tickets'] = [];
        }

        $history = ConversationMessage::where('call_log_id', $callLogId)
            ->orderBy('created_at')
            ->get()
            ->map(fn ($m) => ['role' => $m->role, 'content' => $m->content])
            ->toArray();

        $aiResponse = $this->geminiService->generateResponse($userSpeech, $context, $history);

        $this->callLogService->addMessage($callLogId, 'assistant', $aiResponse);

        $actionUrl = route('twilio.process-speech', [
            'call_log_id' => $callLogId,
            'customer_id' => $customerId ?? 0,
        ]);

        $twiml = $this->twilioService->buildContinueResponse($aiResponse, $actionUrl);

        return response($twiml, 200, ['Content-Type' => 'text/xml']);
    }

    public function handleStatusCallback(Request $request): Response
    {
        $callSid = $request->input('CallSid');
        $callDuration = (int) $request->input('CallDuration', 0);

        CallLog::where('call_sid', $callSid)->update([
            'duration' => $callDuration,
            'status' => 'completed',
        ]);

        return response('OK', 200);
    }
}
