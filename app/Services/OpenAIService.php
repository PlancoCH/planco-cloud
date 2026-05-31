<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('OPENAI_API_KEY', '');
        $this->baseUrl = env('OPENAI_BASE_URL', 'https://api.openai.com/v1');
    }

    /**
     * Main request function to generate content from OpenAI.
     *
     * @param array $systemMessages Array of system messages (e.g. ['You are a helpful assistant', ...])
     * @param array $userMessages   Array of user messages (e.g. ['What is the weather like?', ...])
     * @param string $model         Model to use, defaults to 'gpt-4o' or 'gpt-3.5-turbo'
     * @return string|null          The generated content string from the response
     */
    public function request(array $systemMessages, array $userMessages, string $model = 'deepseek-v4-flash'): ?string
    {
        $messages = $this->buildMessagesArray($systemMessages, $userMessages);

        $payload = [
            'model' => $model,
            'messages' => $messages,
        ];

        return $this->sendRequest('/chat/completions', $payload);
    }

    /**
     * Helper to format both system and user statements into OpenAI's standardized array.
     */
    private function buildMessagesArray(array $systemMessages, array $userMessages): array
    {
        $formatted = [];

        foreach ($systemMessages as $msg) {
            $formatted[] = [
                'role' => 'system',
                'content' => is_array($msg) ? json_encode($msg) : $msg,
            ];
        }

        foreach ($userMessages as $msg) {
            $formatted[] = [
                'role' => 'user',
                'content' => is_array($msg) ? json_encode($msg) : $msg,
            ];
        }

        return $formatted;
    }

    /**
     * Helper to make the actual HTTP call to OpenAI and extract the content.
     */
    private function sendRequest(string $endpoint, array $payload): ?string
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(60)
                ->post($this->baseUrl . $endpoint, $payload);

            if ($response->successful()) {
                return $response->json('choices.0.message.content');
            }

            Log::error('OpenAI API Request Failed', [
                'status' => $response->status(),
                'response' => $response->json(),
                'payload' => $payload,
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('OpenAI API Exception', [
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
