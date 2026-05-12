<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Category;
use App\Models\Question;
use App\Models\Option;

class GenerateQuestionsCommand extends Command
{
    protected $signature = 'app:generate-questions 
                            {--batch-size=1 : Number of questions to generate per API call} 
                            {--target=100 : Total questions to generate per category}
                            {--delay=15 : Seconds to wait between API calls}';

    protected $description = 'Generate programming questions for each existing category in batches using the Gemini API';

    public function handle()
    {
        $batchSize = (int) $this->option('batch-size');
        $targetPerCategory = (int) $this->option('target');
        $delay = (int) $this->option('delay');
        $apiKey = env('GEMINI_API_KEY');

        if (empty($apiKey)) {
            $this->error('GEMINI_API_KEY is not set in the .env file.');
            return;
        }

        $categories = Category::all();

        if ($categories->isEmpty()) {
            $this->error('No categories found. Please run app:generate-categories first.');
            return;
        }

        $this->info("Found {$categories->count()} categories. Generating {$targetPerCategory} questions per category in batches of {$batchSize}...");

        foreach ($categories as $category) {
            $this->info("========================================");
            $this->info("Processing Category: {$category->name}");
            $this->info("========================================");

            // Check how many questions this category already has and skip if it reached target.
            $currentCount = $category->questions()->count();
            
            if ($currentCount >= $targetPerCategory) {
                $this->info("Category '{$category->name}' already reached the target of {$targetPerCategory} questions (Current: {$currentCount}). Skipping...");
                continue;
            } 
            
            while ($currentCount < $targetPerCategory) {
                // Adjust batch size if we are close to the target
                $questionsToGenerate = min($batchSize, $targetPerCategory - $currentCount);
                
                $this->info("Requesting {$questionsToGenerate} questions for {$category->name} (Current: {$currentCount}/{$targetPerCategory})...");

                $prompt = "Generate {$questionsToGenerate} programming questions focused SPECIFICALLY on the topic of: '{$category->name}'.
                Return the result as a valid JSON array of objects. Do not include markdown code blocks, just raw JSON.
                Each object should have the following exact schema:
                [
                    {
                        \"question_text\": \"The question text\",
                        \"code_snippet\": \"Optional code snippet related to the question, or null if none\",
                        \"answer_explanation\": \"Explanation of the correct answer\",
                        \"more_info_link\": \"Optional link to official documentation or resources (MUST BE EXACTLY ONE SINGLE URL, max 255 chars), or null\",
                        \"type\": \"Must be exactly 'unique-answer' or 'multi-answer'\",
                        \"options\": [
                            {
                                \"text\": \"Option text\",
                                \"correct\": boolean
                            }
                        ]
                    }
                ]
                Rules:
                1. For 'unique-answer', exactly one option must be true.
                2. For 'multi-answer', one or more options must be true.
                3. Make sure the options array has between 2 and 5 items.";

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
                    $statusCode = $response->status();
                    $this->error("Failed to connect to Gemini API for category {$category->name}. HTTP Status: {$statusCode}");
                    $this->error($response->body());
                    
                    // If the API is overloaded (503) or rate limited (429), take a longer pause
                    if ($statusCode === 503 || $statusCode === 429) {
                        $this->warn("API is overloaded or rate limited. Pausing for 60 seconds before retrying...");
                        sleep(60);
                    } else {
                        sleep($delay);
                    }
                    continue;
                }

                $data = $response->json();
                
                if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $this->error('Unexpected API response format. Skipping batch...');
                    sleep($delay);
                    continue;
                }

                $jsonText = $data['candidates'][0]['content']['parts'][0]['text'];
                $questions = json_decode($jsonText, true);

                if (json_last_error() !== JSON_ERROR_NONE || !is_array($questions)) {
                    $this->error('Failed to parse JSON from Gemini API: ' . json_last_error_msg());
                    $this->line($jsonText);
                    sleep($delay);
                    continue;
                }

                foreach ($questions as $qData) {
                    $moreInfoLink = $qData['more_info_link'] ?? null;
                    if (is_string($moreInfoLink)) {
                        // Sometimes the AI returns multiple URLs separated by semicolon, comma or space. Take the first one.
                        $moreInfoLink = preg_split('/[;,\s]+/', trim($moreInfoLink))[0] ?? null;
                        if ($moreInfoLink) {
                            $moreInfoLink = substr($moreInfoLink, 0, 255);
                        }
                    }

                    // Create the question
                    $question = Question::create([
                        'question_text' => $qData['question_text'] ?? 'Untitled Question',
                        'code_snippet' => $qData['code_snippet'] ?? null,
                        'answer_explanation' => $qData['answer_explanation'] ?? null,
                        'more_info_link' => $moreInfoLink,
                        'type' => $qData['type'] ?? 'unique-answer',
                    ]);

                    // Sync attaches the category to the pivot table (category_question)
                    $question->categories()->sync([$category->id]);

                    // Handle Options
                    if (isset($qData['options']) && is_array($qData['options'])) {
                        foreach ($qData['options'] as $optData) {
                            Option::create([
                                'question_id' => $question->id,
                                'text' => $optData['text'] ?? '',
                                'correct' => (bool)($optData['correct'] ?? false),
                            ]);
                        }
                    }
                    $currentCount++;
                }

                $this->info("Successfully added batch. Total for {$category->name} is now {$currentCount}/{$targetPerCategory}.");
                
                // Add a delay to avoid hitting rate limits on the API
                sleep($delay);
            }
            $this->info("Finished generating questions for category: {$category->name}");
        }

        $this->info('Done! All categories have been processed.');
    }
}
