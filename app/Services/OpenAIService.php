<?php

namespace App\Services;

use Exception;
use OpenAI;

class OpenAIService
{
    public function generateHtml(string $prompt): string
    {
        try {
            $client = OpenAI::client(config('services.openai.secret'));

            $response = $client->chat()->create([
                'model' => config('services.openai.model'),
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant that converts text into a single HTML block.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            return $response->choices[0]->message->content ?? '';
        } catch (Exception $e) {
            // In a real application, you would log the error here.
            return '';
        }
    }
}
