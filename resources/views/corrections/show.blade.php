@extends('corrections.layouts.app')

@section('title', 'Exam Corrections')

@section('content')
  <section class="result-card">
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
      <div>
        <span class="eyebrow">Released result</span>
        <h2 class="h3 mb-1">{{$exam->event->title}}</h2>
        <div class="muted">{{$exam->student->name}}</div>
      </div>
      <div class="status-pill status-correct">
        Score: {{$exam->score}} / {{$exam->num_of_questions}}
      </div>
    </div>

    <div class="student-strip">
      <div class="stat">
        <span>Exam No</span>
        <strong>{{$exam->exam_no}}</strong>
      </div>
      <div class="stat">
        <span>Student Code</span>
        <strong>{{$exam->student->code ?: 'Not provided'}}</strong>
      </div>
      <div class="stat">
        <span>Status</span>
        <strong>{{ucfirst($exam->status->value)}}</strong>
      </div>
      <div class="stat">
        <span>Courses</span>
        <strong>{{$courses->count()}}</strong>
      </div>
    </div>

    <ul class="nav nav-pills" id="courseTabs" role="tablist">
      @foreach($courses as $courseIndex => $course)
        <li class="nav-item" role="presentation">
          <button
            class="nav-link {{$courseIndex === 0 ? 'active' : ''}}"
            id="course-{{$courseIndex}}-tab"
            data-bs-toggle="tab"
            data-bs-target="#course-{{$courseIndex}}"
            type="button"
            role="tab"
          >
            {{$course['title']}}
          </button>
        </li>
      @endforeach
    </ul>

    <div class="tab-content">
      @foreach($courses as $courseIndex => $course)
        <div
          class="tab-pane fade {{$courseIndex === 0 ? 'show active' : ''}} subject-pane"
          id="course-{{$courseIndex}}"
          role="tabpanel"
        >
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <h3 class="h5 mb-0">{{$course['title']}}</h3>
            <span class="muted">{{$course['summary']}}</span>
          </div>

          <ul class="nav nav-tabs section-tabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button
                class="nav-link active"
                data-bs-toggle="tab"
                data-bs-target="#course-{{$courseIndex}}-obj"
                type="button"
                role="tab"
              >
                OBJ ({{$course['obj_questions']->count()}})
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button
                class="nav-link"
                data-bs-toggle="tab"
                data-bs-target="#course-{{$courseIndex}}-theory"
                type="button"
                role="tab"
              >
                Theory ({{$course['theory_questions']->count()}})
              </button>
            </li>
          </ul>

          <div class="tab-content">
            <div
              class="tab-pane fade show active"
              id="course-{{$courseIndex}}-obj"
              role="tabpanel"
            >
              <div class="question-stack">
                @forelse($course['obj_questions'] as $question)
                  <article class="question-card">
                    <div class="question-head">
                      <div class="d-flex align-items-center gap-2">
                        <span class="question-number">Q{{$question['number']}}</span>
                        @if($question['student_answer'] === null)
                          <span class="status-pill status-unanswered">Unanswered</span>
                        @elseif($question['is_correct'])
                          <span class="status-pill status-correct">Correct</span>
                        @else
                          <span class="status-pill status-wrong">Wrong</span>
                        @endif
                      </div>
                      <div class="muted">
                        Correct answer: <strong>{{$question['answer']}}</strong>
                        <span class="mx-1">|</span>
                        Your answer: <strong>{{$question['student_answer'] ?? 'None'}}</strong>
                      </div>
                    </div>

                    <div class="question-text html-content">{!! $question['question'] !!}</div>

                    <div class="options">
                      @foreach($question['options'] as $option)
                        @php
                          $isCorrect = $option['letter'] === $question['answer'];
                          $isStudent = $option['letter'] === $question['student_answer'];
                        @endphp

                        <div class="option {{$isCorrect ? 'correct' : ''}} {{$isStudent ? 'student' : ''}}">
                          <span class="option-letter">{{$option['letter']}}</span>
                          <div class="html-content">{!! $option['value'] !!}</div>
                          <div class="tag-row">
                            @if($isCorrect)
                              <span class="tag tag-correct">Correct option</span>
                            @endif
                            @if($isStudent)
                              <span class="tag tag-student">Your choice</span>
                            @endif
                          </div>
                        </div>
                      @endforeach
                    </div>

                    @if($question['answer_meta'])
                      <div class="answer-panel">
                        <h4>Explanation</h4>
                        <div class="html-content">{!! $question['answer_meta'] !!}</div>
                      </div>
                    @endif
                  </article>
                @empty
                  <div class="notice notice-warning">No OBJ questions were found for this course.</div>
                @endforelse
              </div>
            </div>

            <div
              class="tab-pane fade"
              id="course-{{$courseIndex}}-theory"
              role="tabpanel"
            >
              <div class="question-stack">
                @forelse($course['theory_questions'] as $question)
                  <article class="question-card">
                    <div class="question-head">
                      <div class="d-flex align-items-center gap-2">
                        <span class="question-number">Q{{$question['number']}}</span>
                        <span class="status-pill status-correct">
                          {{$question['score'] === null ? 'To Be Evaluated' : 'Score: '.$question['score'].'/'.$question['marks']}}
                        </span>
                      </div>
                      <div class="muted">Marks: <strong>{{$question['marks']}}</strong></div>
                    </div>

                    <div class="question-text html-content">{!! $question['question'] !!}</div>

                    <div class="answer-panel">
                      <h4>Your answer</h4>
                      <div class="html-content">
                        {!! $question['student_answer'] ?: '<span class="muted">No answer recorded.</span>' !!}
                      </div>
                    </div>
                    {{--                     
                    <div class="answer-panel">
                      <h4>Expected answer</h4>
                      <div class="html-content">{!! $question['answer'] !!}</div>
                    </div>

                    @if($question['marking_scheme'])
                      <div class="answer-panel">
                        <h4>Marking guide</h4>
                        <div class="html-content">{!! $question['marking_scheme'] !!}</div>
                      </div>
                    @endif --}}
                  </article>
                @empty
                  <div class="notice notice-warning">No theory questions were found for this course.</div>
                @endforelse
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </section>
@endsection
