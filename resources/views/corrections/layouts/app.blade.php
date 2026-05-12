<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Exam Corrections')</title>
  <link rel="stylesheet" href="{{asset('lib/fontawesome-6.7/css/all.min.css')}}">
  <link rel="stylesheet" href="{{asset('lib/bootstrap5.3/bootstrap.min.css')}}">
  <style>
    :root {
      --ink: #172033;
      --muted: #64748b;
      --line: #d8e0ea;
      --panel: #ffffff;
      --wash: #f4f7fb;
      --accent: #2563eb;
      --correct: #15803d;
      --correct-bg: #e9f8ef;
      --wrong: #b42318;
      --wrong-bg: #fff0ed;
      --student: #8a5a00;
      --student-bg: #fff7d6;
    }

    body {
      background: var(--wash);
      color: var(--ink);
      font-family: Arial, Helvetica, sans-serif;
    }

    .page-shell {
      max-width: 1120px;
      margin: 0 auto;
      padding: 32px 16px 56px;
    }

    .topbar {
      align-items: center;
      display: flex;
      justify-content: space-between;
      gap: 16px;
      margin-bottom: 22px;
    }

    .brand-mark {
      align-items: center;
      background: #0f172a;
      border-radius: 8px;
      color: #fff;
      display: inline-flex;
      height: 42px;
      justify-content: center;
      width: 42px;
    }

    .hero,
    .result-card,
    .question-card {
      background: var(--panel);
      border: 1px solid var(--line);
      border-radius: 8px;
      box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08);
    }

    .hero {
      padding: 28px;
    }

    .eyebrow {
      color: var(--accent);
      font-size: 0.78rem;
      font-weight: 700;
      letter-spacing: 0;
      text-transform: uppercase;
    }

    .hero h1 {
      font-size: clamp(1.8rem, 4vw, 3rem);
      font-weight: 800;
      line-height: 1.05;
      margin: 8px 0 10px;
    }

    .hero p,
    .muted {
      color: var(--muted);
    }

    .search-form {
      display: grid;
      gap: 12px;
      grid-template-columns: minmax(0, 1fr) auto;
      margin-top: 22px;
    }

    .search-form .form-control {
      border: 1px solid var(--line);
      border-radius: 8px;
      min-height: 52px;
    }

    .btn-primary {
      align-items: center;
      border-radius: 8px;
      display: inline-flex;
      gap: 8px;
      min-height: 52px;
      padding: 0 20px;
    }

    .notice {
      border-radius: 8px;
      margin-top: 18px;
      padding: 14px 16px;
    }

    .notice-warning {
      background: #fff7ed;
      border: 1px solid #fed7aa;
      color: #9a3412;
    }

    .result-card {
      padding: 22px;
    }

    .student-strip {
      display: grid;
      gap: 12px;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      margin-top: 18px;
    }

    .stat {
      background: #f8fafc;
      border: 1px solid var(--line);
      border-radius: 8px;
      padding: 14px;
    }

    .stat span {
      color: var(--muted);
      display: block;
      font-size: 0.78rem;
      margin-bottom: 4px;
    }

    .stat strong {
      overflow-wrap: anywhere;
    }

    .nav-pills {
      gap: 8px;
      margin-top: 22px;
    }

    .nav-pills .nav-link {
      border: 1px solid var(--line);
      border-radius: 8px;
      color: var(--ink);
      font-weight: 700;
    }

    .nav-pills .nav-link.active {
      background: #0f172a;
      border-color: #0f172a;
    }

    .subject-pane {
      padding-top: 22px;
    }

    .section-tabs .nav-link {
      border-radius: 8px 8px 0 0;
      color: var(--ink);
      font-weight: 700;
    }

    .question-stack {
      display: grid;
      gap: 14px;
      margin-top: 18px;
    }

    .question-card {
      box-shadow: none;
      padding: 18px;
    }

    .question-head {
      align-items: center;
      display: flex;
      gap: 12px;
      justify-content: space-between;
      margin-bottom: 12px;
    }

    .question-number {
      align-items: center;
      background: #e0edff;
      border-radius: 8px;
      color: #1d4ed8;
      display: inline-flex;
      font-weight: 800;
      min-height: 34px;
      min-width: 42px;
      justify-content: center;
      padding: 0 10px;
    }

    .status-pill {
      border-radius: 999px;
      font-size: 0.78rem;
      font-weight: 800;
      padding: 6px 10px;
      white-space: nowrap;
    }

    .status-correct {
      background: var(--correct-bg);
      color: var(--correct);
    }

    .status-wrong {
      background: var(--wrong-bg);
      color: var(--wrong);
    }

    .status-unanswered {
      background: #eef2f7;
      color: #475569;
    }

    .question-text,
    .html-content {
      line-height: 1.65;
      overflow-wrap: anywhere;
    }

    .options {
      display: grid;
      gap: 10px;
      margin-top: 14px;
    }

    .option {
      align-items: flex-start;
      border: 1px solid var(--line);
      border-radius: 8px;
      display: grid;
      gap: 10px;
      grid-template-columns: auto minmax(0, 1fr) auto;
      padding: 12px;
    }

    .option.correct {
      background: var(--correct-bg);
      border-color: #86efac;
    }

    .option.student {
      background: var(--student-bg);
      border-color: #fde68a;
    }

    .option.correct.student {
      background: #e6f8f0;
      border-color: #4ade80;
    }

    .option-letter {
      align-items: center;
      background: #e2e8f0;
      border-radius: 8px;
      display: inline-flex;
      font-weight: 800;
      height: 28px;
      justify-content: center;
      width: 28px;
    }

    .option.correct .option-letter {
      background: var(--correct);
      color: #fff;
    }

    .option.student:not(.correct) .option-letter {
      background: var(--student);
      color: #fff;
    }

    .tag-row {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      justify-content: flex-end;
    }

    .tag {
      border-radius: 999px;
      font-size: 0.72rem;
      font-weight: 800;
      padding: 5px 8px;
    }

    .tag-correct {
      background: var(--correct);
      color: #fff;
    }

    .tag-student {
      background: var(--student);
      color: #fff;
    }

    .answer-panel {
      background: #f8fafc;
      border: 1px solid var(--line);
      border-radius: 8px;
      margin-top: 14px;
      padding: 14px;
    }

    .answer-panel h4 {
      font-size: 0.84rem;
      font-weight: 800;
      margin-bottom: 8px;
    }

    @media (max-width: 760px) {
      .topbar,
      .question-head {
        align-items: flex-start;
        flex-direction: column;
      }

      .search-form,
      .student-strip {
        grid-template-columns: 1fr;
      }

      .btn-primary {
        justify-content: center;
        width: 100%;
      }

      .option {
        grid-template-columns: auto minmax(0, 1fr);
      }

      .tag-row {
        grid-column: 1 / -1;
        justify-content: flex-start;
      }
    }
  </style>
</head>
<body>
  <main class="page-shell">
    <div class="topbar">
      <div class="d-flex align-items-center gap-2">
        <span class="brand-mark"><i class="fa fa-check-double"></i></span>
        <strong>Exam Corrections</strong>
      </div>
      <a href="{{route('corrections.index')}}" class="btn btn-outline-secondary btn-sm">
        <i class="fa fa-rotate-left"></i> New search
      </a>
    </div>

    @yield('content')
  </main>

  <script src="{{asset('lib/bootstrap5.3/bootstrap.bundle.min.js')}}"></script>
</body>
</html>
