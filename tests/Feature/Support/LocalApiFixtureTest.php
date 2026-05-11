<?php

use App\Actions\EventExamsHandler;
use App\Actions\InstitutionHandler;
use App\Actions\SyncEvents;
use App\Enums\HostPlatform;
use App\Models\Event;
use App\Models\Exam;
use App\Support\LocalApiFixture;

use function Pest\Laravel\artisan;
use function Pest\Laravel\postJson;

afterEach(function () {
    if (is_file(LocalApiFixture::defaultPath())) {
        unlink(LocalApiFixture::defaultPath());
    }

    InstitutionHandler::getInstance()->deleteFile();
});

it(
    'writes and uses local API fixture data for mixed OBJ and theory exams',
    function () {
        artisan('app:write-practice-api-fixture')->assertExitCode(0);

        $res = InstitutionHandler::getInstance()->processInstitutionCode(
            'PRACTICE-INSTITUTION',
            HostPlatform::ExamscholarsMock->value
        );

        expect($res->isSuccessful())->toBeTrue();

        SyncEvents::make()->all();
        $event = Event::query()->where('code', 'MIXED-PRACTICE')->first();

        expect($event)->not->toBeNull();

        $download = (new EventExamsHandler($event))->downloadEventContent();

        expect($download->isSuccessful())
            ->toBeTrue()
            ->and(Exam::query()->where('exam_no', 'MIXED-PRACTICE-001')->exists())
            ->toBeTrue();

        postJson(route('api.start-exam'), [
            'exam_no' => 'MIXED-PRACTICE-001',
        ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath(
                'data.exam.exam_courses.0.course_session.questions.0.answer',
                null
            )
            ->assertJsonPath(
                'data.exam.exam_courses.0.course_session.theory_questions.0.answer',
                null
            )
            ->assertJsonCount(
                2,
                'data.exam.exam_courses.0.course_session.theory_questions'
            );
    }
);
