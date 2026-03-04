<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
    }

    /**
     * Returns TwiML XML with <Say> only (voice: Polly.Joanna).
     */
    public function buildVoiceResponse(string $message): string
    {
        $message = htmlspecialchars($message, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<Response><Say voice=\"Polly.Joanna\">{$message}</Say></Response>";
    }

    /**
     * Returns TwiML with <Gather input="speech" timeout=5 speechTimeout="auto">, <Say> prompt inside, then fallback Say and Hangup.
     */
    public function buildGatherResponse(string $promptMessage, string $actionUrl): string
    {
        $promptMessage = htmlspecialchars($promptMessage, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $actionUrl = htmlspecialchars($actionUrl, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<Response>"
            . "<Gather input=\"speech\" timeout=\"5\" speechTimeout=\"auto\" action=\"{$actionUrl}\">"
            . "<Say voice=\"Polly.Joanna\">{$promptMessage}</Say>"
            . "</Gather>"
            . "<Say voice=\"Polly.Joanna\">We did not receive input. Goodbye.</Say>"
            . "<Hangup/>"
            . "</Response>";
    }

    /**
     * Says the AI message, then Gathers "Kya aur kuch chahiye?", then Say "Thank you. Goodbye." and Hangup.
     */
    public function buildContinueResponse(string $message, string $actionUrl): string
    {
        $message = htmlspecialchars($message, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $actionUrl = htmlspecialchars($actionUrl, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $followUp = htmlspecialchars('Kya aur kuch chahiye?', ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $goodbye = htmlspecialchars('Thank you. Goodbye.', ENT_XML1 | ENT_QUOTES, 'UTF-8');
        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<Response>"
            . "<Say voice=\"Polly.Joanna\">{$message}</Say>"
            . "<Gather input=\"speech\" timeout=\"5\" speechTimeout=\"auto\" action=\"{$actionUrl}\">"
            . "<Say voice=\"Polly.Joanna\">{$followUp}</Say>"
            . "</Gather>"
            . "<Say voice=\"Polly.Joanna\">{$goodbye}</Say>"
            . "<Hangup/>"
            . "</Response>";
    }
}
