<?php

use App\Enums\ExamStatus;
use App\Models\Event;
use App\Models\Exam;
use Illuminate\Support\Facades\File;

use function Pest\Laravel\get;

beforeEach(function () {
    $this->contentFolders = [];
});

afterEach(function () {
    foreach ($this->contentFolders as $folder) {
        if (is_dir($folder)) {
            File::deleteDirectory($folder);
        }
    }
});

it('shows the correction entry page', function () {
    get(route('corrections.index'))
        ->assertOk()
        ->assertViewIs('corrections.form')
        ->assertSee('Enter your exam number');
});

it('does not show corrections before release or evaluation', function () {
    $event = Event::factory()->create(['show_corrections' => false]);
    $exam = Exam::factory()
        ->ended()
        ->create([
            'event_id' => $event->id,
            'exam_no' => 'LOCKED-001',
            'exam_courses' => [],
        ]);

    get(route('corrections.index', ['exam_no' => $exam->exam_no]))
        ->assertOk()
        ->assertViewIs('corrections.form')
        ->assertSee('Corrections are not available for this exam yet.')
        ->assertDontSee('Released result');

    $event->fill(['show_corrections' => true])->save();
    $exam->fill(['status' => ExamStatus::Active])->save();

    get(route('corrections.index', ['exam_no' => $exam->exam_no]))
        ->assertOk()
        ->assertViewIs('corrections.form')
        ->assertSee('This exam result has not been fully evaluated yet.')
        ->assertDontSee('Released result');
});

it('shows released corrections for an evaluated exam', function () {
    $event = Event::factory()->create([
        'title' => 'Released Mock',
        'show_corrections' => true,
    ]);
    $courseSessionId = 4101;

    writeCorrectionCourseContent($event->id, $courseSessionId);
    $this->contentFolders[] = public_path("content/event_{$event->id}");

    $exam = Exam::factory()
        ->ended()
        ->create([
            'event_id' => $event->id,
            'exam_no' => 'RELEASED-001',
            'student' => [
                'firstname' => 'Ada',
                'lastname' => 'Student',
                'code' => '001',
            ],
            'score' => 1,
            'num_of_questions' => 3,
            'attempts' => [
                '5101' => 'B',
                '5102' => 'A',
                'theory-6101' => 'Punctuality helps students prepare early.',
            ],
            'exam_courses' => [
                [
                    'course_session_id' => $courseSessionId,
                    'score' => 1,
                    'num_of_questions' => 2,
                    'status' => ExamStatus::Ended->value,
                    'course_code' => 'ENG101',
                    'session' => '2026',
                    'theory_score' => 7,
                    'theory_max_score' => 10,
                    'theory_num_of_questions' => 1,
                    'theory_question_scores' => ['6101' => 7],
                    'theory_evaluated' => true,
                ],
            ],
        ]);

    get(route('corrections.index', ['exam_no' => $exam->exam_no]))
        ->assertOk()
        ->assertViewIs('corrections.show')
        ->assertSee('Released result')
        ->assertSee('Released Mock')
        ->assertSee('Ada Student')
        ->assertSee('ENG101 - English Practice')
        ->assertSee('Correct option')
        ->assertSee('Your choice')
        ->assertSee('Correct')
        ->assertSee('Wrong')
        ->assertSee('Expected answer')
        ->assertSee('Marking guide')
        ->assertSee('Score: 7/10');
});

it('blocks corrections until theory questions are evaluated', function () {
    $event = Event::factory()->create(['show_corrections' => true]);
    $courseSessionId = 4201;

    writeCorrectionCourseContent($event->id, $courseSessionId);
    $this->contentFolders[] = public_path("content/event_{$event->id}");

    $exam = Exam::factory()
        ->ended()
        ->create([
            'event_id' => $event->id,
            'exam_no' => 'THEORY-PENDING-001',
            'exam_courses' => [
                [
                    'course_session_id' => $courseSessionId,
                    'score' => 2,
                    'num_of_questions' => 2,
                    'theory_score' => 0,
                    'theory_max_score' => 10,
                    'theory_num_of_questions' => 1,
                    'theory_question_scores' => null,
                    'theory_evaluated' => false,
                ],
            ],
        ]);

    get(route('corrections.index', ['exam_no' => $exam->exam_no]))
        ->assertOk()
        ->assertViewIs('corrections.form')
        ->assertSee('This exam result has not been fully evaluated yet.')
        ->assertDontSee('Released result');
});

function writeCorrectionCourseContent(int $eventId, int $courseSessionId): void
{
    $folder = public_path("content/event_{$eventId}");
    File::ensureDirectoryExists($folder);
    File::ensureDirectoryExists("{$folder}/images");
    File::ensureDirectoryExists("{$folder}/exams");

    file_put_contents(
        "{$folder}/course_session_{$courseSessionId}.json",
        json_encode([
            'id' => $courseSessionId,
            'course_id' => 7201,
            'session' => '2026',
            'course' => [
                'id' => 7201,
                'course_code' => 'ENG101',
                'course_title' => 'English Practice',
            ],
            'questions' => [
                [
                    'id' => 5101,
                    'question_no' => 1,
                    'question' => 'Choose the correct verb.',
                    'option_a' => 'go',
                    'option_b' => 'goes',
                    'option_c' => 'going',
                    'option_d' => 'gone',
                    'option_e' => null,
                    'answer' => 'B',
                    'answer_meta' => 'She takes goes.',
                ],
                [
                    'id' => 5102,
                    'question_no' => 2,
                    'question' => 'Closest meaning to brief.',
                    'option_a' => 'Late',
                    'option_b' => 'Short',
                    'option_c' => 'Heavy',
                    'option_d' => 'Loud',
                    'option_e' => null,
                    'answer' => 'B',
                    'answer_meta' => 'Brief means short.',
                ],
            ],
            'theory_questions' => [
                [
                    'id' => 6101,
                    'question_no' => 1,
                    'question_sub_number' => null,
                    'question' => 'Explain punctuality.',
                    'marks' => 10,
                    'answer' => 'A strong answer mentions preparedness and respect.',
                    'marking_scheme' => 'Award marks for clear reasons.',
                ],
            ],
            'passages' => [],
            'instructions' => [],
        ])
    );
}
