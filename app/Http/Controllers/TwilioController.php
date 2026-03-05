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
        $callerNumber = $request->input('From', 'unknown');
        $callSid = $request->input('CallSid', 'test-' . time());

        $customer = Customer::where('phone', $callerNumber)->first();

        $callLog = $this->callLogService->createLog(
            customerId: $customer?->id,
            simulatedQuery: 'Inbound call from ' . $callerNumber,
            callSid: $callSid
        );

        session(['call_log_id' => $callLog->id]);
        session(['customer_id' => $customer?->id]);

        $twiml = $this->twilioService->buildGatherResponse(
            'Welcome! How can I help you today? Please speak your query.',
            route('twilio.process-speech')
        );

        return response($twiml, 200)->header('Content-Type', 'text/xml');
    }

    public function processSpeech(Request $request): Response
    {
        $userSpeech = $request->input('SpeechResult', 'I need help');
        $callLogId = session('call_log_id');
        $customerId = session('customer_id');

        // If session not found, use latest call log (for testing purposes only)
        if (! $callLogId) {
            $latestLog = \App\Models\CallLog::latest()->first();
            $callLogId = $latestLog?->id;
            $customerId = $latestLog?->customer_id;
        }

        if (! $callLogId) {
            $twiml = $this->twilioService->buildVoiceResponse('Sorry, we could not identify your call. Goodbye.');
            return response($twiml, 200)->header('Content-Type', 'text/xml');
        }

        $this->callLogService->addMessage($callLogId, 'user', $userSpeech);

        $customer = $customerId ? Customer::find($customerId) : null;
        $context = [
            'customer_name' => $customer?->name ?? 'Guest',
            'orders' => $customer?->orders()->latest()->take(3)->get(['id', 'order_number', 'status', 'delivery_date'])->toArray() ?? [],
            'tickets' => $customer?->tickets()->latest()->take(3)->get(['id', 'issue_type', 'status'])->toArray() ?? [],
        ];

        $history = ConversationMessage::where('call_log_id', $callLogId)
            ->orderBy('created_at')
            ->get()
            ->map(fn ($m) => ['role' => $m->role, 'content' => $m->content])
            ->toArray();

        $aiResponse = $this->geminiService->generateResponse($userSpeech, $context, $history);

        $this->callLogService->addMessage($callLogId, 'assistant', $aiResponse);

        $twiml = $this->twilioService->buildContinueResponse(
            $aiResponse,
            route('twilio.process-speech')
        );

        return response($twiml, 200)->header('Content-Type', 'text/xml');
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
