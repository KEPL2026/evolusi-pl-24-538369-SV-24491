@extends('layouts.app')

@section('content')
    @php
        $dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $dateLabel = $dayNames[(int) now()->dayOfWeek].', '.now()->day.' '.$monthNames[(int) now()->month - 1].' '.now()->year;
        $noteColorDot = [
            'yellow' => 'bg-yellow',
            'sky' => 'bg-sky',
            'coral' => 'bg-coral',
        ];
    @endphp

    <div class="mx-auto w-full max-w-[1120px] px-4 pb-10 pt-8 sm:px-6 sm:pt-10">

        <section class="sh-3 relative overflow-hidden rounded-[28px] border-[3px] border-ink bg-paper-raised p-6 sm:p-10">
            <div class="pointer-events-none absolute -right-10 -top-10 h-40 w-40 rounded-full bg-coral/20 blur-2xl"></div>
            <div class="pointer-events-none absolute -bottom-12 -left-10 h-44 w-44 rounded-full bg-sky/20 blur-2xl"></div>

            <div class="relative z-10">
                <span class="mb-4 inline-flex items-center gap-1.5 rounded-full border-[3px] border-ink bg-paper px-3 py-1 font-display text-[12px] font-semibold text-ink">
                    <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                    {{ $dateLabel }}
                </span>

                <h1 class="max-w-[22ch] font-display text-[32px] font-semibold leading-[1.1] tracking-[0.2px] text-ink sm:text-[44px] lg:text-[50px]">
                    Fokus, tandai kebiasaan, dan tetap sinkron tiap hari.
                </h1>
                <p class="mt-3 max-w-[60ch] text-[17px] leading-7 text-ink-soft">
                    Pantau progresmu hari ini dan lanjutkan ritme dari dashboard Daily Sync.
                </p>
            </div>
        </section>

        <section class="mt-10">
            <div class="mb-6 flex flex-wrap items-center gap-3">
                <span class="sh-1 flex h-9 w-9 -rotate-3 items-center justify-center rounded-[10px] border-[3px] border-ink bg-coral text-white">
                    <span class="material-symbols-outlined text-[18px]">insights</span>
                </span>
                <div class="flex-1">
                    <h2 class="font-display text-2xl font-semibold text-ink">Ringkasan Hari Ini</h2>
                    <p class="text-sm text-ink-soft">Capaianmu hari ini di semua modul.</p>
                </div>
                @if ($streak > 0)
                    <span class="inline-flex items-center gap-1.5 rounded-full border-[3px] border-ink bg-yellow px-3 py-1 font-display text-[13px] font-semibold text-ink-const shadow-[3px_3px_0_0_var(--ds-shadow)]">
                        <span class="material-symbols-outlined ms-fill text-[16px]">local_fire_department</span>
                        {{ $streak }} hari beruntun
                    </span>
                @endif
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <a href="{{ route('habits.index') }}"
                   class="group flex flex-col rounded-[24px] border-[3px] border-ink bg-paper-raised p-5 shadow-[5px_5px_0_0_var(--ds-shadow)] transition-transform hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl border-[3px] border-ink bg-green text-white shadow-[3px_3px_0_0_var(--ds-shadow)]">
                            <span class="material-symbols-outlined text-[22px]">checklist</span>
                        </span>
                        <span class="material-symbols-outlined text-ink-soft transition-transform group-hover:translate-x-1">arrow_forward</span>
                    </div>
                    <h3 class="mt-4 font-display text-[15px] font-semibold text-ink-soft">Kebiasaan Hari Ini</h3>
                    <p class="mt-1 font-display text-[28px] font-semibold leading-none text-ink">
                        {{ $progress['completed'] }} dari {{ $progress['total'] }}
                    </p>
                    <p class="mt-1 font-display text-[13px] font-semibold text-ink-soft">{{ $progress['percent'] }}% tercapai</p>
                    <div class="mt-4 h-[18px] w-full rounded-full border-[3px] border-ink bg-paper p-0.5">
                        <div class="striped-fill h-full rounded-full transition-all duration-300" style="width: {{ $progress['percent'] }}%;"></div>
                    </div>
                </a>

                <a href="{{ route('pomodoro.index') }}"
                   class="group flex flex-col rounded-[24px] border-[3px] border-ink bg-paper-raised p-5 shadow-[5px_5px_0_0_var(--ds-shadow)] transition-transform hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl border-[3px] border-ink bg-berry text-white shadow-[3px_3px_0_0_var(--ds-shadow)]">
                            <span class="material-symbols-outlined text-[22px]">timer</span>
                        </span>
                        <span class="material-symbols-outlined text-ink-soft transition-transform group-hover:translate-x-1">arrow_forward</span>
                    </div>
                    <h3 class="mt-4 font-display text-[15px] font-semibold text-ink-soft">Sesi Fokus</h3>
                    <p class="mt-1 font-display text-[28px] font-semibold leading-none text-ink">{{ $sessionMinutes }} menit</p>
                    <p class="mt-1 font-display text-[13px] font-semibold text-ink-soft">{{ $sessionCount }} sesi fokus hari ini</p>
                </a>

                <a href="{{ route('notes.index') }}"
                   class="group flex flex-col rounded-[24px] border-[3px] border-ink bg-paper-raised p-5 shadow-[5px_5px_0_0_var(--ds-shadow)] transition-transform hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl border-[3px] border-ink bg-yellow text-ink-const shadow-[3px_3px_0_0_var(--ds-shadow)]">
                            <span class="material-symbols-outlined text-[22px]">note_alt</span>
                        </span>
                        <span class="material-symbols-outlined text-ink-soft transition-transform group-hover:translate-x-1">arrow_forward</span>
                    </div>
                    <h3 class="mt-4 font-display text-[15px] font-semibold text-ink-soft">Catatan Tempel</h3>
                    <p class="mt-1 font-display text-[28px] font-semibold leading-none text-ink">{{ $noteTotal }} catatan</p>
                    <p class="mt-1 font-display text-[13px] font-semibold text-ink-soft">{{ $notesToday }} dibuat hari ini</p>
                </a>
            </div>
        </section>

        <section class="mt-10 grid gap-6 lg:grid-cols-2">
            <div class="sh-2 rounded-[24px] border-[3px] border-ink bg-paper-raised p-5 sm:p-6">
                <div class="flex items-center gap-2.5 border-b-[3px] border-dashed border-ink/20 pb-4">
                    <span class="sh-1 flex h-8 w-8 rotate-2 items-center justify-center rounded-lg border-[3px] border-ink bg-sky text-white">
                        <span class="material-symbols-outlined text-[18px]">checklist</span>
                    </span>
                    <h2 class="font-display text-lg font-semibold text-ink">Belum Dicap Hari Ini</h2>
                </div>

                @if ($unfinishedHabits->isNotEmpty())
                    <ul class="mt-4 space-y-2">
                        @foreach ($unfinishedHabits as $habit)
                            <li class="flex items-center justify-between gap-3 rounded-xl border-2 border-ink bg-paper px-3 py-2">
                                <span class="flex min-w-0 items-center gap-3">
                                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-[7px] border-2 border-ink bg-paper-raised"></span>
                                    <span class="truncate font-body text-[14px] font-bold text-ink">{{ $habit->name }}</span>
                                </span>
                                <a href="{{ route('habits.index') }}" class="shrink-0 font-display text-[12px] font-semibold text-sky-dark hover:underline">Cap</a>
                            </li>
                        @endforeach
                    </ul>
                    @if ($habits->count() > $unfinishedHabits->count())
                        <a href="{{ route('habits.index') }}" class="mt-3 inline-block font-display text-[12px] font-semibold text-ink-soft hover:underline">
                            Lihat semua kebiasaan
                        </a>
                    @endif
                @else
                    <div class="mt-4 flex items-center gap-3 rounded-xl border-2 border-ink bg-green/10 px-4 py-3">
                        <span class="material-symbols-outlined ms-fill text-green-dark text-[20px]">task_alt</span>
                        <p class="font-body text-[14px] font-bold text-ink">
                            {{ $habits->isEmpty() ? 'Belum ada kebiasaan yang dibuat.' : 'Semua kebiasaan sudah dicap hari ini.' }}
                        </p>
                    </div>
                @endif
            </div>

            <div class="sh-2 rounded-[24px] border-[3px] border-ink bg-paper-raised p-5 sm:p-6">
                <div class="flex items-center gap-2.5 border-b-[3px] border-dashed border-ink/20 pb-4">
                    <span class="sh-1 flex h-8 w-8 -rotate-2 items-center justify-center rounded-lg border-[3px] border-ink bg-yellow text-ink-const">
                        <span class="material-symbols-outlined text-[18px]">note_alt</span>
                    </span>
                    <h2 class="font-display text-lg font-semibold text-ink">Catatan Terbaru</h2>
                </div>

                @if ($latestNotes->isNotEmpty())
                    <ul class="mt-4 space-y-2">
                        @foreach ($latestNotes as $note)
                            <li class="flex items-center justify-between gap-3 rounded-xl border-2 border-ink bg-paper px-3 py-2">
                                <span class="flex min-w-0 items-center gap-3">
                                    <span @class(['h-4 w-4 shrink-0 rounded-full border-2 border-ink', $noteColorDot[$note->color]])></span>
                                    <span class="truncate font-body text-[14px] font-bold text-ink">{{ $note->body }}</span>
                                </span>
                                <a href="{{ route('notes.index') }}" class="shrink-0 font-display text-[12px] font-semibold text-sky-dark hover:underline">Buka</a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="mt-4 flex items-center gap-3 rounded-xl border-2 border-ink bg-paper px-4 py-3">
                        <span class="material-symbols-outlined text-[20px] text-ink-soft">sticky_note_2</span>
                        <p class="font-body text-[14px] font-bold text-ink">Belum ada catatan. Tempel catatan pertamamu di halaman Notes.</p>
                    </div>
                @endif
            </div>
        </section>

        <section class="mt-10">
            <div class="mb-6 flex items-center gap-3">
                <span class="sh-1 flex h-9 w-9 -rotate-3 items-center justify-center rounded-[10px] border-[3px] border-ink bg-coral text-white">
                    <span class="material-symbols-outlined text-[18px]">apps</span>
                </span>
                <div>
                    <h2 class="font-display text-2xl font-semibold text-ink">Modul Daily Sync</h2>
                    <p class="text-sm text-ink-soft">Semua modul sudah aktif, pilih untuk mulai bekerja.</p>
                </div>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ([
                    [
                        'icon' => 'timer',
                        'tile' => 'bg-berry text-white',
                        'title' => 'Pomodoro Timer',
                        'desc' => 'Sesi fokus 25 menit, istirahat pendek, dan notifikasi audio saat waktu habis.',
                        'tilt' => '-rotate-1',
                        'route' => 'pomodoro.index',
                    ],
                    [
                        'icon' => 'checklist',
                        'tile' => 'bg-green text-white',
                        'title' => 'Habit Checklist',
                        'desc' => 'Cap kebiasaan harian satu per satu dan pantau persentase pencapaianmu.',
                        'tilt' => 'rotate-1',
                        'route' => 'habits.index',
                    ],
                    [
                        'icon' => 'note_alt',
                        'tile' => 'bg-yellow text-ink-const',
                        'title' => 'Daily Quick Notes',
                        'desc' => 'Tempel ide dan pengingat harian di papan catatan berwarna.',
                        'tilt' => '-rotate-1',
                        'route' => 'notes.index',
                    ],
                ] as $feature)
                    @php
                        $cardClass = 'sh-2 flex flex-col rounded-[24px] border-[3px] border-ink bg-paper-raised p-6 transition-transform hover:-translate-y-1 '.$feature['tilt'];
                    @endphp
                    <a href="{{ route($feature['route']) }}" class="{{ $cardClass }}">
                        <span @class(['mb-4 flex h-11 w-11 items-center justify-center rounded-xl border-[3px] border-ink shadow-[3px_3px_0_0_var(--ds-shadow)]', $feature['tile']])>
                            <span class="material-symbols-outlined text-[22px]">{{ $feature['icon'] }}</span>
                        </span>
                        <h3 class="font-display text-xl font-semibold text-ink">{{ $feature['title'] }}</h3>
                        <p class="mt-2 text-[15px] leading-6 text-ink-soft">{{ $feature['desc'] }}</p>
                    </a>
                @endforeach
            </div>
        </section>
    </div>
@endsection
