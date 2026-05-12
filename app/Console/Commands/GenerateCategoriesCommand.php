<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Category;

class GenerateCategoriesCommand extends Command
{
    protected $signature = 'app:generate-categories {--count=15 : Number of categories to generate}';
    protected $description = 'Generate programming categories using the Gemini API';

    public function handle()
    {
        $count = (int) $this->option('count');
        $apiKey = env('GEMINI_API_KEY');
        
        if (empty($apiKey)) {
            $this->error('GEMINI_API_KEY is not set in the .env file.');
            return;
        }
        
        $this->info("Generating {$count} programming categories using Gemini API...");

        $prompt = "Generate a list of {$count} distinct programming categories or topics (like PHP, Laravel, Python, JavaScript, React, MySQL, Docker, etc.).
        Return ONLY a valid JSON array of strings representing the category names. Do not include markdown formatting or anything else.
        Example: [\"PHP\", \"Laravel\", \"Python\"]";

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'response_mime_type' => 'application/json',
            ]
        ]);

        if ($response->failed()) {
            $this->error('Failed to connect to Gemini API. HTTP Status: ' . $response->status());
            return;
        }

        $data = $response->json();
        
        if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            $this->error('Unexpected API response format.');
            return;
        }

        $jsonText = $data['candidates'][0]['content']['parts'][0]['text'];
        $categories = json_decode($jsonText, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($categories)) {
            $this->error('Failed to parse JSON from Gemini API: ' . json_last_error_msg());
            $this->line($jsonText);
            return;
        }

        $this->info("Successfully generated " . count($categories) . " categories. Saving to database...");

        foreach ($categories as $catName) {
            Category::firstOrCreate(['name' => $catName]);
        }

        $this->info('Done! Categories have been populated.');
    }
}
