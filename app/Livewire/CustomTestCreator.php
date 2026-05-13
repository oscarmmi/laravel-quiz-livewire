<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\Attributes\On;

class CustomTestCreator extends Component
{
    /**
     * Type of source to pull questions from ('categories' or 'quizzes').
     */
    public string $sourceType = 'categories';

    /**
     * Array of selected quiz IDs (when source is quizzes).
     */
    public array $selectedQuizzes = [];

    /**
     * Array of selected category IDs for question distribution.
     *
     * @var array<int, int>
     */
    public array $selectedCategories = [];

    /**
     * The exact number of questions the final test must contain.
     */
    public int $totalQuestions = 10;

    /**
     * Flag to track if form has been submitted.
     */
    public bool $isSubmitted = false;

    /**
     * Flag to track if modal should close after submission.
     */
    public bool $closeModalOnSuccess = true;

    /**
     * The total questions actually assigned after distribution.
     */
    public int $actualTotalQuestions = 0;

    /**
     * Collection of categories available for selection.
     *
     * @var Collection<int, Category>
     */
    public Collection $categories;

    /**
     * Collection of quizzes available for selection.
     *
     * @var Collection<int, Quiz>
     */
    public Collection $quizzes;

    /**
     * Distribution summary for display after submission.
     *
     * @var array<int, array{category: string, requested: int, actual: int}>
     */
    public array $distributionSummary = [];

    /**
     * Validation rules for the component's data.
     *
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'sourceType' => ['required', 'in:categories,quizzes'],
            'selectedCategories' => ['required_if:sourceType,categories', 'array'],
            'selectedCategories.*' => ['integer', 'exists:categories,id'],
            'selectedQuizzes' => ['required_if:sourceType,quizzes', 'array'],
            'selectedQuizzes.*' => ['integer', 'exists:quizzes,id'],
            'totalQuestions' => ['required', 'integer', 'min:1', 'max:1000'],
        ];
    }

    /**
     * Mount the component and load initial data.
     */
    public function mount(): void
    {
        $this->categories = Category::query()
            ->has('questions')
            ->withCount('questions')
            ->orderBy('name')
            ->get();

        $this->quizzes = Quiz::query()
            ->where('published', true)
            ->notByUser()
            ->orderBy('title')
            ->get();
    }

    /**
     * Render the component's view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.custom-test-creator');
    }

    /**
     * Handle form submission and distribute questions across selected categories.
     *
     * The distribution algorithm follows these steps:
     * 1. Calculate base quota: floor(totalQuestions / count(categories))
     * 2. Calculate remainder: totalQuestions % count(categories)
     * 3. Distribute +1 to first 'remainder' number of categories
     * 4. Handle "shortfall" edge case - if a category doesn't have enough
     *    questions, redistribute the missing amount to other categories
     *
     * @return void
     */
    public function submit(): void
    {
        $this->validate();

        $selectedSources = $this->sourceType === 'categories' ? $this->selectedCategories : $this->selectedQuizzes;
        $sourceCount = count($selectedSources);

        if ($sourceCount === 0) {
            $field = $this->sourceType === 'categories' ? 'selectedCategories' : 'selectedQuizzes';
            $this->addError($field, __('Please select at least one :type.', [
                'type' => $this->sourceType === 'categories' ? 'category' : 'quiz'
            ]));
            return;
        }

        $baseQuota = (int) floor($this->totalQuestions / $sourceCount);
        $remainder = $this->totalQuestions % $sourceCount;

        $quotas = [];
        for ($i = 0; $i < $sourceCount; $i++) {
            $quotas[$selectedSources[$i]] = $baseQuota + ($i < $remainder ? 1 : 0);
        }

        $adjustedQuotas = $this->handleShortfall($quotas);

        // Auto-generate the destination quiz
        $targetQuiz = Quiz::create([
            'title' => 'Custom Practice Test - ' . now()->format('M j, Y g:i A'),
            'slug' => \Illuminate\Support\Str::slug('custom-test-' . auth()->id() . '-' . now()->timestamp),
            'description' => 'A custom practice test generated automatically.',
            'published' => true,
            'public' => false,
            'user_id' => auth()->id(),
        ]);

        $this->distributeQuestions($adjustedQuotas, $targetQuiz);

        $this->isSubmitted = true;
    }

    /**
     * Handle the shortfall edge case by redistributing unavailable questions.
     *
     * When a category doesn't have enough questions to meet its quota,
     * we take all available questions and redistribute the "missing" amount
     * evenly among remaining categories that have surplus.
     *
     * @param array<int, int> $quotas The initially calculated quotas per category
     * @return array<int, int> The adjusted quotas after handling shortfall
     */
    protected function handleShortfall(array $quotas): array
    {
        $adjustedQuotas = $quotas;
        $missingAmount = 0;

        /**
         * First pass: Check each category for available questions
         * If a category has fewer questions than its quota, use all available
         * and calculate how many we're "short"
         */
        foreach ($quotas as $sourceId => $quota) {
            $query = Question::query();
            
            if ($this->sourceType === 'categories') {
                $query->whereHas('categories', function ($q) use ($sourceId) {
                    $q->where('categories.id', $sourceId);
                });
            } else {
                $query->whereHas('quizzes', function ($q) use ($sourceId) {
                    $q->where('quizzes.id', $sourceId);
                });
            }
            
            $availableCount = $query->count();

            if ($availableCount < $quota) {
                /**
                 * Category has fewer questions than requested quota
                 * - Use all available: $availableCount
                 * - Calculate shortfall: $quota - $availableCount
                 */
                $adjustedQuotas[$sourceId] = $availableCount;
                $missingAmount += ($quota - $availableCount);
            }
        }

        /**
         * If there's no missing amount, return adjusted quotas as-is
         */
        if ($missingAmount === 0) {
            return $adjustedQuotas;
        }

        /**
         * Redistribute the missing amount among categories that have surplus
         *
         * A category has surplus if: adjusted quota < original quota
         * (meaning it had enough questions to fulfill its full quota)
         */
        $surplusCategories = array_keys(
            array_filter(
                $quotas,
                fn($quota, $sourceId) => $adjustedQuotas[$sourceId] === $quota,
                ARRAY_FILTER_USE_BOTH
            )
        );

        $surplusCount = count($surplusCategories);

        /**
         * If no categories have surplus, we can't fully satisfy the request
         * Return what we have (this is an edge case where total available
         * questions across all categories is less than requested)
         */
        if ($surplusCount === 0) {
            return $adjustedQuotas;
        }

        /**
         * Distribute missing amount evenly among surplus categories
         * Use floor for base distribution, then distribute remainder
         */
        $extraPerCategory = (int) floor($missingAmount / $surplusCount);
        $extraRemainder = $missingAmount % $surplusCount;

        for ($i = 0; $i < $surplusCount; $i++) {
            $sourceId = $surplusCategories[$i];
            $extra = $extraPerCategory + ($i < $extraRemainder ? 1 : 0);
            $adjustedQuotas[$sourceId] += $extra;
        }

        return $adjustedQuotas;
    }

    /**
     * Fetch random questions from each category and attach to the quiz.
     *
     * @param array<int, int> $quotas The final quotas per category after adjustments
     * @return void
     */
    protected function distributeQuestions(array $quotas, Quiz $targetQuiz): void
    {
        $allQuestionIds = [];
        $this->distributionSummary = [];
        $this->actualTotalQuestions = 0;

        foreach ($quotas as $sourceId => $quota) {
            if ($quota <= 0) {
                continue;
            }

            $query = Question::query();
            
            if ($this->sourceType === 'categories') {
                $query->whereHas('categories', function ($q) use ($sourceId) {
                    $q->where('categories.id', $sourceId);
                });
                $sourceName = Category::find($sourceId)?->name ?? "Category #{$sourceId}";
            } else {
                $query->whereHas('quizzes', function ($q) use ($sourceId) {
                    $q->where('quizzes.id', $sourceId);
                });
                $sourceName = Quiz::find($sourceId)?->title ?? "Quiz #{$sourceId}";
            }

            $questionIds = $query->inRandomOrder()->limit($quota)->pluck('id')->toArray();
            $actualCount = count($questionIds);

            $this->distributionSummary[] = [
                'category' => $sourceName,
                'requested' => $quota,
                'actual' => $actualCount,
            ];

            $allQuestionIds = array_merge($allQuestionIds, $questionIds);
            $this->actualTotalQuestions += $actualCount;
        }

        /**
         * Sync the collected questions to the quiz
         * This replaces any existing questions with the new selection
         */
        $targetQuiz->questions()->sync($allQuestionIds);

        $this->dispatch('close');
    }

    /**
     * Reset the form to its initial state.
     *
     * @return void
     */
    public function resetForm(): void
    {
        $this->selectedCategories = [];
        $this->selectedQuizzes = [];
        $this->sourceType = 'categories';
        $this->totalQuestions = 10;
        $this->isSubmitted = false;
        $this->distributionSummary = [];
        $this->actualTotalQuestions = 0;
    }
}