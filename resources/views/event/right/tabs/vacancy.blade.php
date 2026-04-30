@if($event->vacancy)
    <div class="vacancy-header">
        <span class="job-title">{{ $event->vacancy->job_title }}</span>
    </div>

    <div class="vacancy-grid">
        <div class="vacancy-item">
            <span class="label">Компания</span>
            <div>
                <span class="value company">{{ $event->vacancy->company }}</span>
                <span class="company-industry">{{ $event->vacancy->industry }}</span>
            </div>
        </div>
        <div class="vacancy-item">
            <span class="label">Зарплата</span>
            <span class="value salary">{{ $event->vacancy->salary }}</span>
        </div>
        <div class="vacancy-item">
            <span class="label">Формат работы</span>
            <span class="value">{{ $event->vacancy->format_work }}</span>
        </div>
        <div class="vacancy-item">
            <span class="label">Город</span>
            <span class="value">{{ $event->vacancy->city}}</span>
        </div>
    </div>

    @if($event->vacancy->benefits !== 'Не указано')
        <div class="benefits-block">
            <span class="label">Плюшки</span>
            <div class="value benefits-list">
                @foreach(explode(',', $event->vacancy->benefits) as $benefit)
                    @if(trim($benefit))
                        <span class="benefit-badge">{{ trim($benefit) }}</span>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <div class="skills-block">
        <span class="label">Технологии и навыки</span>
        <div class="skills-list">
            @foreach(explode(',', $event->vacancy->skills) as $skill)
                @if(trim($skill))
                    <span class="skill-badge">{{ trim($skill) }}</span>
                @endif
            @endforeach
        </div>
    </div>

    <div class="vacancy-footer">
        <div class="link-vacancy-outline">
            <a href="{{ $event->vacancy->url }}" target="_blank" rel="noopener noreferrer">Открыть вакансию</a>
        </div>
    </div>

    <div class="top-questions">
        <h3>📋 Топ вопросов для подготовки</h3>
        <ul>
            @foreach($event->vacancy->top_questions as $question)
                <li>{{ $question }}</li>
            @endforeach
        </ul>
    </div>
@else
    <p>Данные о вакансии загружаются. Пожалуйста, обновите страницу позже.</p>
@endif
