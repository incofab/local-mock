<?php

namespace App\Console\Commands;

use App\Enums\ExamStatus;
use App\Enums\HostPlatform;
use App\Support\LocalApiFixture;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class WritePracticeApiFixtureCommand extends Command
{
    protected $signature = 'app:write-practice-api-fixture
    {--code=PRACTICE-INSTITUTION : Institution code to initialize locally}
    {--platform=examscholars-mock : Host platform value}
    {--event-code=MIXED-PRACTICE : Event code for creating exams}
    {--path= : JSON file path to write}';

    protected $description =
        'Write local API fixture data for manually testing an exam with OBJ and theory questions';

    public function handle(): int
    {
        $path = $this->option('path') ?: LocalApiFixture::defaultPath();
        $payload = $this->payload(
            $this->option('code'),
            $this->option('platform'),
            $this->option('event-code')
        );

        File::ensureDirectoryExists(dirname($path));
        file_put_contents(
            $path,
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        $this->info("Practice API fixture written to {$path}");
        $this->line('Use these values when initializing the application:');
        $this->line("Institution code: {$payload['institution']['code']}");
        $this->line("Platform: {$payload['institution']['platform']}");
        $this->line("Event code: {$this->option('event-code')}");
        $this->line('Sample exam no after sync/download: MIXED-PRACTICE-001');

        return self::SUCCESS;
    }

    private function payload(
        string $institutionCode,
        string $platform,
        string $eventCode
    ): array {
        $eventId = 9001;
        $courseSessionId = 9101;
        $now = now()->toJSON();
        $event = [
            'id' => $eventId,
            'title' => 'Mixed OBJ and Theory Practice',
            'description' => 'Local fixture event for manual exam practice testing.',
            'duration' => 45,
            'status' => 'active',
            'code' => $eventCode,
            'external_content_id' => null,
            'external_event_courses' => [],
            'created_at' => $now,
            'updated_at' => $now,
        ];
        $deepEvent = [
            ...$event,
            'event_courses' => [
                [
                    'id' => 9201,
                    'event_id' => $eventId,
                    'course_session_id' => $courseSessionId,
                    'status' => 'active',
                    'num_of_questions' => 3,
                    'created_at' => $now,
                    'updated_at' => $now,
                    'course_session' => $this->courseSession($courseSessionId, $eventId),
                ],
            ],
        ];

        return [
            'institution' => [
                'id' => 9901,
                'name' => 'Practice Fixture Institution',
                'address' => 'Local Test Address',
                'phone' => '08000000000',
                'email' => 'fixture@example.test',
                'code' => $institutionCode,
                'platform' => $platform ?: HostPlatform::ExamscholarsMock->value,
            ],
            'events' => [
                [
                    ...$event,
                    'event_courses' => [],
                ],
            ],
            'deep_events' => [
                $eventId => $deepEvent,
            ],
            'deep_events_by_code' => [
                $eventCode => $deepEvent,
            ],
            'event_exams' => [
                $eventId => [
                    [
                        'id' => 9301,
                        'event_id' => $eventId,
                        'student_id' => 101,
                        'exam_no' => "{$eventCode}-001",
                        'time_remaining' => 0,
                        'start_time' => null,
                        'pause_time' => null,
                        'end_time' => null,
                        'score' => 0,
                        'num_of_questions' => 3,
                        'theory_score' => 0,
                        'theory_max_score' => 15,
                        'theory_evaluated' => false,
                        'status' => ExamStatus::Pending->value,
                        'attempts' => [],
                        'student' => [
                            'firstname' => 'Practice',
                            'lastname' => 'Student',
                            'code' => '001',
                        ],
                        'exam_courses' => [
                            [
                                'course_session_id' => $courseSessionId,
                                'score' => 0,
                                'num_of_questions' => 3,
                                'status' => ExamStatus::Pending->value,
                                'course_code' => 'ENG101',
                                'session' => '2026',
                                'theory_score' => 0,
                                'theory_max_score' => 15,
                                'theory_num_of_questions' => 2,
                                'theory_question_scores' => null,
                                'theory_evaluated' => false,
                            ],
                        ],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ],
                ],
            ],
        ];
    }

    private function courseSession(int $courseSessionId, int $eventId): array
    {
        return [
            'id' => $courseSessionId,
            'course_id' => 9401,
            'session' => '2026',
            'category' => 'practice',
            'general_instructions' => 'Answer all OBJ and theory questions.',
            'course' => [
                'id' => 9401,
                'course_code' => 'ENG101',
                'course_title' => 'English Practice',
                'description' => 'Local mixed question practice course.',
                'category' => 'practice',
                'order' => 1,
            ],
            'passages' => [],
            'instructions' => [],
            'questions' => [
                [
                    'id' => 9501,
                    'course_session_id' => $courseSessionId,
                    'topic_id' => null,
                    'question_no' => 1,
                    'question' => 'Choose the word that best completes the sentence: She _____ to school every weekday.',
                    'option_a' => 'go',
                    'option_b' => 'goes',
                    'option_c' => 'going',
                    'option_d' => 'gone',
                    'option_e' => null,
                    'answer' => 'B',
                    'answer_meta' => 'The singular subject "She" takes "goes".',
                ],
                [
                    'id' => 9502,
                    'course_session_id' => $courseSessionId,
                    'topic_id' => null,
                    'question_no' => 2,
                    'question' => 'Which option is closest in meaning to "brief"?',
                    'option_a' => 'Short',
                    'option_b' => 'Late',
                    'option_c' => 'Loud',
                    'option_d' => 'Heavy',
                    'option_e' => null,
                    'answer' => 'A',
                    'answer_meta' => 'Brief means short in duration or length.',
                ],
                [
                    'id' => 9503,
                    'course_session_id' => $courseSessionId,
                    'topic_id' => null,
                    'question_no' => 3,
                    'question' => 'Identify the noun in this sentence: The teacher smiled.',
                    'option_a' => 'the',
                    'option_b' => 'teacher',
                    'option_c' => 'smiled',
                    'option_d' => 'sentence',
                    'option_e' => null,
                    'answer' => 'B',
                    'answer_meta' => 'Teacher names a person, so it is the noun.',
                ],
            ],
            'theory_questions' => [
                [
                    'id' => 9601,
                    'event_id' => $eventId,
                    'course_session_id' => $courseSessionId,
                    'question_no' => 1,
                    'question_sub_number' => null,
                    'question' => 'Write a short paragraph explaining why punctuality is important for students.',
                    'marks' => 10,
                    'answer' => 'A strong answer should explain time management, preparedness, and respect for others.',
                    'marking_scheme' => 'Award marks for clear topic sentence, two supporting reasons, coherence, and grammar.',
                ],
                [
                    'id' => 9602,
                    'event_id' => $eventId,
                    'course_session_id' => $courseSessionId,
                    'question_no' => 2,
                    'question_sub_number' => 'a',
                    'question' => 'Mention two features of a formal letter.',
                    'marks' => 5,
                    'answer' => 'Sender address, date, receiver address, salutation, subject, complimentary close, signature.',
                    'marking_scheme' => 'Award 2.5 marks for each correct feature.',
                ],
            ],
        ];
    }
}
