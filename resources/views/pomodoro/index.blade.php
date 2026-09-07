@extends('layouts.app')

@section('content')
    <div class="mx-auto w-full max-w-[1120px] px-4 pb-10 pt-8 sm:px-6 sm:pt-10">

        <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="sh-1 flex h-11 w-11 -rotate-2 items-center justify-center rounded-xl border-[3px] border-ink bg-berry text-white">
                    <span class="material-symbols-outlined text-[22px]">timer</span>
                </span>
                <div>
                    <h1 class="font-display text-2xl font-semibold text-ink sm:text-3xl">Pomodoro Timer</h1>
                    <p class="text-sm text-ink-soft">Kerja fokus 25 menit, lalu beri jeda, dan ulangi.</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-1.5 rounded-full border-[3px] border-ink bg-berry px-3 py-1 font-display text-[13px] font-semibold text-white">
                    <span class="material-symbols-outlined ms-fill text-[16px]">check_circle</span>
                    <span id="session-count">{{ $sessionCount }}</span>
                    <span>sesi fokus</span>
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-full border-[3px] border-ink bg-paper px-3 py-1 font-display text-[13px] font-semibold text-ink">
                    <span class="material-symbols-outlined text-[16px]">schedule</span>
                    <span id="session-minutes">{{ $totalMinutes }}</span>
                    <span>menit fokus hari ini</span>
                </span>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-12">

            <section class="sh-3 flex flex-col rounded-[28px] border-[3px] border-ink bg-berry p-6 text-center sm:p-10 lg:col-span-7">
                <div class="mx-auto flex flex-wrap items-center justify-center gap-2">
                    @foreach ([
                        ['mode' => 'focus', 'label' => 'Sesi Fokus', 'id' => 'focus-min', 'value' => 25],
                        ['mode' => 'short_break', 'label' => 'Istirahat Pendek', 'id' => 'short-min', 'value' => 5],
                        ['mode' => 'long_break', 'label' => 'Istirahat Panjang', 'id' => 'long-min', 'value' => 15],
                    ] as $mode)
                        <button type="button" data-mode="{{ $mode['mode'] }}"
                                class="mode-btn rounded-full border-2 border-white/80 px-4 py-1.5 font-display text-[13px] font-semibold text-white transition-colors"
                                aria-pressed="false">
                            <span class="hidden sm:inline">{{ $mode['label'] }}</span>
                            <span class="sm:hidden">{{ Str::before($mode['label'], ' ') }}</span>
                            <span id="{{ $mode['id'] }}" class="font-bold">{{ $mode['value'] }}</span>m
                        </button>
                    @endforeach
                </div>

                <div class="flex w-full flex-1 flex-col items-center justify-center">

                <span id="timer-status"
                      class="mx-auto mt-6 inline-flex items-center gap-1.5 rounded-full border-[3px] border-ink bg-paper-raised px-5 py-1.5 font-display text-[13px] font-semibold text-ink">
                    <span class="material-symbols-outlined text-[16px]">timer</span>
                    <span id="status-label">Sesi Fokus</span>
                </span>

                <p id="timer-display"
                   class="mt-4 select-none font-display text-[84px] font-semibold leading-none tracking-tight text-white [text-shadow:4px_4px_0_#0f1b34] sm:text-[112px]">
                    25:00
                </p>
                <p id="round-info" class="mt-2 font-display text-[14px] font-semibold text-white/85">Ronde 1 dari 4</p>

                <div class="mx-auto mt-8 flex flex-wrap items-center justify-center gap-3">
                    <button type="button" id="toggle-timer"
                            class="btn-stamp min-w-[150px] bg-coral px-8 py-3 font-display font-semibold text-ink-const dark:text-white">
                        <span id="toggle-icon" class="material-symbols-outlined ms-fill text-[22px]">play_arrow</span>
                        <span id="toggle-label">Mulai</span>
                    </button>
                    <button type="button" id="reset-timer"
                            class="btn-stamp bg-paper-raised px-6 py-3 font-display font-semibold text-ink">
                        <span class="material-symbols-outlined text-[20px]">refresh</span>
                        Reset
                    </button>
                </div>

                <p id="timer-hint" class="mt-6 text-[13px] font-semibold text-white/75">
                    Notifikasi audio berbunyi saat timer selesai. Status timer tersimpan otomatis.
                </p>
                </div>
            </section>

            <aside class="space-y-6 lg:col-span-5">
                <section class="sh-2 rounded-[24px] border-[3px] border-ink bg-paper-raised p-5 sm:p-6">
                    <div class="flex items-center gap-2.5 border-b-[3px] border-dashed border-ink/20 pb-4">
                        <span class="sh-1 flex h-8 w-8 rotate-2 items-center justify-center rounded-lg border-[3px] border-ink bg-sky text-white">
                            <span class="material-symbols-outlined text-[18px]">tune</span>
                        </span>
                        <h2 class="font-display text-lg font-semibold text-ink">Atur Durasi Kustom</h2>
                    </div>

                    <div class="mt-4 space-y-3">
                        @foreach ([
                            ['key' => 'focus', 'label' => 'Sesi fokus', 'id' => 'focus-input', 'value' => 25],
                            ['key' => 'short', 'label' => 'Istirahat pendek', 'id' => 'short-input', 'value' => 5],
                            ['key' => 'long', 'label' => 'Istirahat panjang', 'id' => 'long-input', 'value' => 15],
                        ] as $field)
                            <div>
                                <label for="{{ $field['id'] }}" class="mb-1 block font-display text-[13px] font-semibold text-ink">
                                    {{ $field['label'] }} (menit)
                                </label>
                                <input id="{{ $field['id'] }}" type="number" min="1" max="180" value="{{ $field['value'] }}"
                                       class="w-full rounded-[10px] border-[3px] border-ink bg-paper px-3 py-2 font-display text-[15px] font-semibold text-ink shadow-[3px_3px_0_0_var(--ds-shadow)] focus:outline-none">
                            </div>
                        @endforeach
                        <p class="text-[13px] leading-5 text-ink-soft">
                            Siklus mengikuti metode Pomodoro: setelah 4 sesi fokus, istirahat panjang diberikan sebelum siklus diulang.
                        </p>
                    </div>
                </section>

                <section class="sh-2 rounded-[24px] border-[3px] border-ink bg-paper-raised p-5 sm:p-6">
                    <div class="flex items-center gap-2.5 border-b-[3px] border-dashed border-ink/20 pb-4">
                        <span class="sh-1 flex h-8 w-8 -rotate-2 items-center justify-center rounded-lg border-[3px] border-ink bg-green text-white">
                            <span class="material-symbols-outlined text-[18px]">history</span>
                        </span>
                        <h2 class="font-display text-lg font-semibold text-ink">Riwayat Sesi Hari Ini</h2>
                    </div>

                    <ul id="session-list" class="mt-4 space-y-2">
                        @foreach ($sessions as $session)
                            <li class="flex items-center justify-between gap-3 rounded-xl border-2 border-ink bg-paper px-3 py-2">
                                <span class="flex items-center gap-2 font-body text-[14px] font-bold text-ink">
                                    <span class="material-symbols-outlined ms-fill text-green-dark text-[16px]">check_circle</span>
                                    Sesi fokus {{ $session->duration_minutes }} menit
                                </span>
                                <span class="font-display text-[12px] font-semibold text-ink-soft">{{ $session->completed_at->format('H:i') }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <div id="session-empty" class="{{ $sessions->isEmpty() ? 'flex' : 'hidden' }} flex-col items-center gap-2 py-8 text-center">
                        <span class="material-symbols-outlined text-[32px] text-ink-soft">history</span>
                        <p class="text-sm text-ink-soft">Belum ada sesi fokus yang selesai hari ini.</p>
                    </div>
                </section>
            </aside>
        </div>
    </div>
@endsection
