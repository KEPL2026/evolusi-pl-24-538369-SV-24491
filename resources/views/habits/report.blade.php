@extends('layouts.app')

@section('content')
    <div class="report-print mx-auto w-full max-w-[1120px] px-4 pb-10 pt-8 sm:px-6 sm:pt-10">

        <div class="print-hidden mb-5 flex flex-wrap items-center gap-2">
            <a href="{{ route('habits.index') }}"
               class="btn-stamp bg-paper-raised px-4 py-1.5 font-display font-semibold text-ink">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Kebiasaan
            </a>
            <a href="{{ route('habits.export', ['month' => $previousMonth]) }}"
               class="btn-stamp bg-paper-raised px-4 py-1.5 text-ink"
               aria-label="Bulan sebelumnya">
                <span class="material-symbols-outlined text-[18px]">chevron_left</span>
            </a>
            <a href="{{ route('habits.export', ['month' => $nextMonth]) }}"
               @class(['btn-stamp px-4 py-1.5', $nextDisabled ? 'bg-paper text-ink-soft opacity-60 pointer-events-none' : 'bg-paper-raised text-ink'])
               aria-label="Bulan berikutnya" @if ($nextDisabled) aria-disabled="true" @endif>
                <span class="material-symbols-outlined text-[18px]">chevron_right</span>
            </a>
            <button type="button" onclick="window.print()"
                    class="btn-stamp ml-auto bg-coral px-5 py-1.5 font-display font-semibold text-ink-const dark:text-white">
                <span class="material-symbols-outlined ms-fill text-[18px]">picture_as_pdf</span>
                Unduh PDF
            </button>
        </div>

        <section class="report-card sh-3 rounded-[24px] border-[3px] border-ink bg-paper-raised p-5 sm:p-8">
            <div class="flex flex-wrap items-start justify-between gap-4 border-b-[3px] border-dashed border-ink/25 pb-5">
                <div class="flex items-center gap-3">
                    <span class="sh-1 flex h-11 w-11 -rotate-2 items-center justify-center rounded-xl border-[3px] border-ink bg-yellow text-ink-const">
                        <span class="material-symbols-outlined text-[22px]">history_edu</span>
                    </span>
                    <div>
                        <h1 class="font-display text-2xl font-semibold text-ink sm:text-3xl">Riwayat Habit</h1>
                        <p class="font-display text-[15px] font-semibold text-ink-soft" id="report-period">{{ $monthLabel }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-sm font-semibold">
                    <span class="rounded-full border-2 border-ink bg-paper px-3 py-1 font-display text-[12px] text-ink">{{ $rows->count() }} habit</span>
                    <span class="rounded-full border-2 border-ink bg-paper px-3 py-1 font-display text-[12px] text-ink">{{ $totalCompletions }} cap</span>
                    <span class="rounded-full border-2 border-ink bg-paper px-3 py-1 font-display text-[12px] text-ink">{{ $productiveDays }} hari produktif</span>
                </div>
            </div>

            @if ($rows->isEmpty())
                <div class="flex flex-col items-center gap-3 py-14 text-center">
                    <span class="sh-1 flex h-14 w-14 rotate-3 items-center justify-center rounded-2xl border-[3px] border-ink bg-yellow text-ink-const">
                        <span class="material-symbols-outlined text-[26px]">calendar_month</span>
                    </span>
                    <p class="font-display text-[15px] font-semibold text-ink">Belum ada kebiasaan yang dicap bulan ini.</p>
                    <p class="max-w-[44ch] text-sm text-ink-soft">Cap kebiasaanmu setiap hari dari halaman Habit Checklist, lalu kembali ke laporan ini untuk melihat riwayatnya.</p>
                </div>
            @else
                <div class="mt-5 flex items-center gap-2 text-sm text-ink-soft">
                    <span>Cap kebiasaan harian ditandai kotak terisi</span>
                    <span class="ml-1 inline-flex items-center gap-1">
                        <span class="report-cell is-done inline-flex h-4 w-4 items-center justify-center rounded-[4px] border-2 border-ink bg-green align-middle text-white">
                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <span class="font-display text-[12px] font-semibold">selesai</span>
                    </span>
                </div>

                <div class="report-table mt-4 overflow-x-auto pb-2">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="px-2 py-1.5 text-left font-display text-[13px] font-semibold text-ink">Kebiasaan</th>
                                <th class="px-2 py-1.5 text-center font-display text-[13px] font-semibold text-ink" title="Total cap bulan ini">Total</th>
                                @for ($day = 1; $day <= $daysInMonth; $day++)
                                    <th class="w-6 px-0 py-1.5 text-center font-display text-[11px] font-semibold {{ $day < 10 ? 'text-ink' : 'text-ink-soft' }}">{{ $day }}</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $row)
                                <tr>
                                    <td class="border-t-2 border-dashed border-ink/15 px-2 py-1.5 font-body text-[14px] font-bold text-ink">{{ $row['name'] }}</td>
                                    <td class="border-t-2 border-dashed border-ink/15 px-2 py-1.5 text-center font-display text-[13px] font-semibold text-ink">{{ $row['total'] }}</td>
                                    @for ($day = 1; $day <= $daysInMonth; $day++)
                                        <td class="w-6 border-t-2 border-dashed border-ink/15 px-0 py-1.5 text-center">
                                            @if (isset($row['days'][$day]))
                                                <span class="report-cell is-done inline-flex h-[18px] w-[18px] items-center justify-center rounded-[5px] border-2 border-ink bg-green align-middle text-white">
                                                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"/></svg>
                                                </span>
                                            @else
                                                <span class="report-cell inline-flex h-[18px] w-[18px] items-center justify-center rounded-[5px] border border-ink/30 align-middle"></span>
                                            @endif
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
@endsection
