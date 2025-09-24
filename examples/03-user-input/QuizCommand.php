<?php

use WebFiori\Cli\Command;
use WebFiori\Cli\InputValidator;
use WebFiori\Cli\ArgumentOption;

/**
 * Interactive quiz command demonstrating input validation and scoring.
 * 
 * This command shows:
 * - Interactive quiz mechanics
 * - Different question types
 * - Input validation and scoring
 * - Progress tracking
 * - Results analysis and feedback
 */
class QuizCommand extends Command {
    private array $answers = [];
    private string $difficulty = 'medium';

    private array $questions = [];
    private int $score = 0;

    public function __construct() {
        parent::__construct('quiz', [
            '--difficulty' => [
                ArgumentOption::DESCRIPTION => 'Quiz difficulty level',
                ArgumentOption::OPTIONAL => true,
                ArgumentOption::DEFAULT => 'medium',
                ArgumentOption::VALUES => ['easy', 'medium', 'hard']
            ],
            '--questions' => [
                ArgumentOption::DESCRIPTION => 'Number of questions (5-20)',
                ArgumentOption::OPTIONAL => true,
                ArgumentOption::DEFAULT => '10'
            ]
        ], 'Interactive knowledge quiz with scoring and feedback');
    }

    public function exec(): int {
        $this->difficulty = $this->getArgValue('--difficulty') ?? 'medium';
        $questionCount = (int)($this->getArgValue('--questions') ?? 10);

        // Validate question count
        if ($questionCount < 5 || $questionCount > 20) {
            $this->error('Number of questions must be between 5 and 20');

            return 1;
        }

        $this->println("🧠 Welcome to the Knowledge Quiz!");
        $this->println("=================================");
        $this->println();

        $this->info("📊 Quiz Settings:");
        $this->println("   • Difficulty: ".ucfirst($this->difficulty));
        $this->println("   • Questions: $questionCount");
        $this->println();

        if (!$this->confirm('Ready to start?', true)) {
            $this->info('Maybe next time! 👋');

            return 0;
        }

        // Initialize questions
        $this->initializeQuestions();

        // Select random questions based on difficulty
        $selectedQuestions = $this->selectQuestions($questionCount);

        // Run the quiz
        $this->runQuiz($selectedQuestions);

        // Show results
        $this->showResults($questionCount);

        return 0;
    }

    /**
     * Ask a question and get user input.
     */
    private function askQuestion(array $question): string {
        if ($question['type'] === 'multiple') {
            $choice = $this->select('Your answer:', $question['options']);

            return (string)$choice;
        } else {
            return $this->getInput(
                'Your answer:',
                null,
                new InputValidator(function ($input) {
                    return !empty(trim($input));
                }, 'Please provide an answer')
            );
        }
    }

    /**
     * Check if the answer is correct.
     */
    private function checkAnswer(array $question, string $userAnswer): bool {
        if ($question['type'] === 'multiple') {
            return (int)$userAnswer === $question['correct'];
        } else {
            $correctAnswer = strtolower(trim($question['correct']));
            $userAnswerNormalized = strtolower(trim($userAnswer));

            return $correctAnswer === $userAnswerNormalized;
        }
    }

    /**
     * Initialize the question bank.
     */
    private function initializeQuestions(): void {
        $this->questions = [
            'easy' => [
                [
                    'type' => 'multiple',
                    'question' => 'What does PHP stand for?',
                    'options' => ['Personal Home Page', 'PHP: Hypertext Preprocessor', 'Private Home Page', 'Public Hypertext Processor'],
                    'correct' => 1
                ],
                [
                    'type' => 'input',
                    'question' => 'What is 5 + 7?',
                    'correct' => '12'
                ],
                [
                    'type' => 'multiple',
                    'question' => 'Which of these is a programming language?',
                    'options' => ['HTML', 'CSS', 'JavaScript', 'XML'],
                    'correct' => 2
                ],
                [
                    'type' => 'input',
                    'question' => 'What is the capital of France?',
                    'correct' => 'Paris'
                ],
                [
                    'type' => 'multiple',
                    'question' => 'What does CLI stand for?',
                    'options' => ['Command Line Interface', 'Computer Language Interface', 'Code Line Interface', 'Common Language Interface'],
                    'correct' => 0
                ]
            ],
            'medium' => [
                [
                    'type' => 'multiple',
                    'question' => 'Which HTTP status code indicates "Not Found"?',
                    'options' => ['200', '404', '500', '301'],
                    'correct' => 1
                ],
                [
                    'type' => 'input',
                    'question' => 'What is 15 × 8?',
                    'correct' => '120'
                ],
                [
                    'type' => 'multiple',
                    'question' => 'Which design pattern ensures a class has only one instance?',
                    'options' => ['Factory', 'Observer', 'Singleton', 'Strategy'],
                    'correct' => 2
                ],
                [
                    'type' => 'input',
                    'question' => 'In which year was PHP first released? (4 digits)',
                    'correct' => '1995'
                ],
                [
                    'type' => 'multiple',
                    'question' => 'What does REST stand for in web APIs?',
                    'options' => ['Representational State Transfer', 'Remote State Transfer', 'Relational State Transfer', 'Responsive State Transfer'],
                    'correct' => 0
                ]
            ],
            'hard' => [
                [
                    'type' => 'multiple',
                    'question' => 'What is the time complexity of quicksort in the average case?',
                    'options' => ['O(n)', 'O(n log n)', 'O(n²)', 'O(log n)'],
                    'correct' => 1
                ],
                [
                    'type' => 'input',
                    'question' => 'What is the result of 2^10? (numbers only)',
                    'correct' => '1024'
                ],
                [
                    'type' => 'multiple',
                    'question' => 'Which algorithm is used for finding the shortest path in a weighted graph?',
                    'options' => ['BFS', 'DFS', 'Dijkstra', 'Kruskal'],
                    'correct' => 2
                ],
                [
                    'type' => 'input',
                    'question' => 'What does SOLID stand for in programming principles? (first letter of each principle)',
                    'correct' => 'SOLID'
                ],
                [
                    'type' => 'multiple',
                    'question' => 'In database normalization, what does 3NF stand for?',
                    'options' => ['Third Normal Form', 'Triple Normal Form', 'Tertiary Normal Form', 'Three-way Normal Form'],
                    'correct' => 0
                ]
            ]
        ];
    }

    /**
     * Run the quiz with selected questions.
     */
    private function runQuiz(array $questions): void {
        $this->println();
        $this->success("🎯 Starting Quiz!");
        $this->println();

        foreach ($questions as $index => $question) {
            $questionNumber = $index + 1;
            $totalQuestions = count($questions);

            $this->info("Question $questionNumber/$totalQuestions:");
            $this->println($question['question']);
            $this->println();

            $userAnswer = $this->askQuestion($question);
            $isCorrect = $this->checkAnswer($question, $userAnswer);

            if ($isCorrect) {
                $this->success("✅ Correct!");
                $this->score++;
            } else {
                $this->error("❌ Incorrect!");
                $this->showCorrectAnswer($question);
            }

            $this->answers[] = [
                'question' => $question['question'],
                'user_answer' => $userAnswer,
                'correct' => $isCorrect
            ];

            $this->println();

            // Show progress
            if ($questionNumber < $totalQuestions) {
                $this->info("Score so far: $this->score/$questionNumber");
                $this->println();
            }
        }
    }

    /**
     * Select questions based on difficulty and count.
     */
    private function selectQuestions(int $count): array {
        $availableQuestions = $this->questions[$this->difficulty];

        // Add some questions from easier levels if needed
        if (count($availableQuestions) < $count) {
            if ($this->difficulty === 'hard') {
                $availableQuestions = array_merge($availableQuestions, $this->questions['medium']);
            }

            if ($this->difficulty !== 'easy') {
                $availableQuestions = array_merge($availableQuestions, $this->questions['easy']);
            }
        }

        // Shuffle and select
        shuffle($availableQuestions);

        return array_slice($availableQuestions, 0, $count);
    }

    /**
     * Show the correct answer.
     */
    private function showCorrectAnswer(array $question): void {
        if ($question['type'] === 'multiple') {
            $correctOption = $question['options'][$question['correct']];
            $this->info("Correct answer: $correctOption");
        } else {
            $this->info("Correct answer: ".$question['correct']);
        }
    }

    /**
     * Show detailed question-by-question results.
     */
    private function showDetailedResults(): void {
        $this->println();
        $this->info("📋 Detailed Results:");
        $this->println(str_repeat('-', 40));

        foreach ($this->answers as $index => $answer) {
            $questionNumber = $index + 1;
            $status = $answer['correct'] ? '✅' : '❌';

            $this->println("$questionNumber. $status ".substr($answer['question'], 0, 50). 
                          (strlen($answer['question']) > 50 ? '...' : ''));
        }

        $this->println();
    }

    /**
     * Show quiz results and analysis.
     */
    private function showResults(int $totalQuestions): void {
        $this->println();
        $this->success("🎉 Quiz Completed!");
        $this->println("==================");

        $percentage = round(($this->score / $totalQuestions) * 100, 1);

        $this->println("📊 Final Score: $this->score/$totalQuestions ($percentage%)");

        // Performance feedback
        $this->println();
        $this->info("📈 Performance Analysis:");

        if ($percentage >= 90) {
            $this->success("🏆 Excellent! You're a quiz master!");
            $grade = 'A+';
        } elseif ($percentage >= 80) {
            $this->success("🎯 Great job! Very impressive!");
            $grade = 'A';
        } elseif ($percentage >= 70) {
            $this->info("👍 Good work! Keep it up!");
            $grade = 'B';
        } elseif ($percentage >= 60) {
            $this->warning("📚 Not bad, but there's room for improvement!");
            $grade = 'C';
        } else {
            $this->warning("📖 Keep studying and try again!");
            $grade = 'D';
        }

        $this->println("🎓 Grade: $grade");

        // Show difficulty-specific feedback
        $this->println();
        $this->info("💡 Difficulty: ".ucfirst($this->difficulty));

        switch ($this->difficulty) {
            case 'easy':
                if ($percentage >= 80) {
                    $this->info("Ready to try medium difficulty!");
                }
                break;
            case 'medium':
                if ($percentage >= 85) {
                    $this->info("You might enjoy the hard difficulty!");
                } elseif ($percentage < 60) {
                    $this->info("Consider trying easy difficulty first.");
                }
                break;
            case 'hard':
                if ($percentage >= 70) {
                    $this->success("Impressive performance on hard questions!");
                } else {
                    $this->info("Hard questions are challenging - keep learning!");
                }
                break;
        }

        // Offer to show detailed results
        if ($this->confirm('Show detailed results?', false)) {
            $this->showDetailedResults();
        }

        // Ask about retaking
        if ($this->confirm('Take the quiz again?', false)) {
            $this->info('Run the command again to start a new quiz!');
        }
    }
}
