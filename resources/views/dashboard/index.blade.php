@extends('layouts.app')

@section('content')
    <div class="mx-auto w-full max-w-[1120px] px-4 pb-8 pt-8 sm:px-6 sm:pt-12">

        <section class="sh-3 relative overflow-hidden rounded-[28px] border-[3px] border-ink bg-paper-raised p-6 sm:p-10">
            <div class="pointer-events-none absolute -right-10 -top-10 h-40 w-40 rounded-full bg-coral/20 blur-2xl"></div>
            <div class="pointer-events-none absolute -bottom-12 -left-10 h-44 w-44 rounded-full bg-sky/20 blur-2xl"></div>

            <div class="relative z-10">
                <h1 class="max-w-[20ch] font-display text-[34px] font-semibold leading-[1.1] tracking-[0.2px] text-ink sm:text-[46px] lg:text-[54px]">
                    Fokus, tandai kebiasaan, dan tetap sinkron tiap hari.
                </h1>
                <p class="mt-4 max-w-[62ch] text-[17px] leading-7 text-ink-soft sm:text-[19px] sm:leading-8">
                    Daily Sync menggabungkan pomodoro timer, habit checklist, dan catatan cepat dalam satu
                    dashboard bergaya neo-brutalism yang terasa seperti membuka buku catatan baru.
                </p>
            </div>
        </section>

        <section class="mt-10">
            <div class="mb-6 flex items-center gap-3">
                <span class="sh-1 flex h-9 w-9 -rotate-3 items-center justify-center rounded-[10px] border-[3px] border-ink bg-coral text-white">
                    <span class="material-symbols-outlined text-[18px]">apps</span>
                </span>
                <div>
                    <h2 class="font-display text-2xl font-semibold text-ink">Modul Daily Sync</h2>
                    <p class="text-sm text-ink-soft">Fitur dikembangkan bertahap, kartu aktif otomatis saat halamannya rilis.</p>
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
                    ],
                    [
                        'icon' => 'checklist',
                        'tile' => 'bg-green text-ink-const',
                        'title' => 'Habit Checklist',
                        'desc' => 'Cap kebiasaan harian satu per satu dan pantau persentase pencapaianmu.',
                        'tilt' => 'rotate-1',
                    ],
                    [
                        'icon' => 'note_alt',
                        'tile' => 'bg-yellow text-ink-const',
                        'title' => 'Daily Quick Notes',
                        'desc' => 'Catatan tempel ber-pin untuk to-do harian yang tersimpan di database.',
                        'tilt' => '-rotate-1',
                    ],
                ] as $feature)
                    <article @class(['sh-2 rounded-[24px] border-[3px] border-ink bg-paper-raised p-6 transition-transform hover:-translate-y-1', $feature['tilt']])>
                        <span @class(['mb-4 flex h-11 w-11 items-center justify-center rounded-xl border-[3px] border-ink shadow-[3px_3px_0_0_var(--ds-shadow)]', $feature['tile']])>
                            <span class="material-symbols-outlined text-[22px]">{{ $feature['icon'] }}</span>
                        </span>
                        <h3 class="font-display text-xl font-semibold text-ink">{{ $feature['title'] }}</h3>
                        <p class="mt-2 text-[15px] leading-6 text-ink-soft">{{ $feature['desc'] }}</p>
                        <span class="mt-5 inline-flex items-center gap-1.5 rounded-full border-2 border-ink bg-paper px-3 py-1 font-display text-[12px] font-semibold text-ink-soft">
                            <span class="material-symbols-outlined text-[14px]">construction</span>
                            Segera hadir
                        </span>
                    </article>
                @endforeach
            </div>
        </section>
    </div>
@endsection
