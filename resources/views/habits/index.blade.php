@extends('layouts.app')

@section('content')
    <div class="mx-auto w-full max-w-[1120px] px-4 pb-10 pt-8 sm:px-6 sm:pt-10">

        <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="sh-1 flex h-11 w-11 -rotate-2 items-center justify-center rounded-xl border-[3px] border-ink bg-green text-white">
                    <span class="material-symbols-outlined text-[22px]">checklist</span>
                </span>
                <div>
                    <h1 class="font-display text-2xl font-semibold text-ink sm:text-3xl">Habit Checklist</h1>
                    <p class="text-sm text-ink-soft">Cap kebiasaan harianmu dan jaga ritme setiap hari.</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <span id="streak-chip"
                      @class([
                          'items-center gap-1.5 rounded-full border-[3px] border-ink bg-yellow px-3 py-1 font-display text-[13px] font-semibold text-ink-const shadow-[3px_3px_0_0_var(--ds-shadow)]',
                          $streak > 0 ? 'flex' : 'hidden',
                      ])>
                    <span class="material-symbols-outlined ms-fill text-[16px]">local_fire_department</span>
                    <span id="streak-text">{{ $streak }} hari beruntun</span>
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-full border-[3px] border-ink bg-paper px-3 py-1 font-display text-[13px] font-semibold text-ink">
                    <span class="material-symbols-outlined text-[16px]">today</span>
                    Hari Ini
                </span>
            </div>
        </div>

        <section class="sh-2 rounded-[24px] border-[3px] border-ink bg-paper-raised p-5 sm:p-6">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <span class="font-display text-[15px] font-semibold text-ink">Progres hari ini</span>
                <span id="progress-text" class="font-display text-sm font-semibold text-ink">
                    {{ $progress['completed'] }} / {{ $progress['total'] }} selesai • {{ $progress['percent'] }}%
                </span>
            </div>
            <div class="mt-3 h-[22px] w-full rounded-full border-[3px] border-ink bg-paper p-0.5 shadow-[3px_3px_0_0_var(--ds-shadow)]">
                <div id="progress-fill" class="striped-fill h-full rounded-full transition-all duration-300"
                     style="width: {{ $progress['percent'] }}%;"></div>
            </div>
        </section>

        <div class="mt-6 grid gap-6 lg:grid-cols-12">
            <section class="sh-2 rounded-[24px] border-[3px] border-ink bg-paper-raised p-5 sm:p-6 lg:col-span-7">
                <div class="flex items-center justify-between gap-2">
                    <h2 class="font-display text-xl font-semibold text-ink">Daftar Kebiasaan</h2>
                    <span id="habit-count" class="rounded-full border-2 border-ink bg-paper px-2.5 py-0.5 font-display text-[12px] font-semibold text-ink-soft">
                        {{ $habits->count() }} item
                    </span>
                </div>

                <ul id="habits-list" class="mt-4 space-y-3">
                    @foreach ($habits as $habit)
                        @php
                            $done = $habit->completions->isNotEmpty();
                            $habitStreak = $streaks[$habit->id] ?? 0;
                        @endphp
                        <li class="habit-row flex flex-wrap items-center gap-3 rounded-xl border-[3px] border-ink bg-paper p-3 transition-colors hover:bg-paper-raised sm:flex-nowrap"
                            data-id="{{ $habit->id }}" data-done="{{ $done ? 'true' : 'false' }}">
                            <button type="button"
                                    class="js-toggle flex h-8 w-8 shrink-0 items-center justify-center rounded-[10px] border-[3px] border-ink transition-all duration-150 active:scale-75 active:-rotate-6 {{ $done ? 'bg-green text-white' : 'bg-paper-raised text-transparent' }}"
                                    aria-pressed="{{ $done ? 'true' : 'false' }}" title="{{ $done ? 'Batalkan cap' : 'Tandai selesai' }}">
                                <span class="material-symbols-outlined ms-fill text-[18px]">check</span>
                            </button>

                            <span class="js-label min-w-0 flex-1 truncate font-body text-[16px] font-bold {{ $done ? 'text-ink-soft line-through' : 'text-ink' }}">
                                {{ $habit->name }}
                            </span>

                            <div class="js-edit hidden min-w-0 flex-1 items-center gap-2">
                                <input type="text" maxlength="120"
                                       class="js-rename-input w-full rounded-[10px] border-[3px] border-ink bg-paper-raised px-3 py-1.5 font-body text-[15px] font-bold text-ink focus:outline-none"
                                       value="{{ $habit->name }}">
                                <button type="button" class="js-rename-save btn-stamp shrink-0 bg-green px-3 py-1.5 text-white" title="Simpan nama">
                                    <span class="material-symbols-outlined text-[16px]">check</span>
                                </button>
                                <button type="button" class="js-rename-cancel btn-stamp shrink-0 bg-paper px-3 py-1.5 text-ink" title="Batal">
                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                </button>
                            </div>

                            <span class="js-streak {{ $habitStreak > 0 ? 'flex' : 'hidden' }} items-center gap-1 rounded-full border-2 border-ink bg-yellow px-2 py-0.5 font-display text-[12px] font-semibold text-ink-const">
                                <span class="material-symbols-outlined ms-fill text-[13px]">local_fire_department</span>
                                <span class="js-streak-num">{{ $habitStreak }}</span>
                            </span>

                            <div class="ml-auto flex items-center gap-1.5">
                                <button type="button" class="js-edit-btn flex h-8 w-8 items-center justify-center rounded-lg border-[3px] border-ink bg-paper-raised text-sky-dark transition-all hover:bg-paper active:translate-y-0.5" title="Ubah nama">
                                    <span class="material-symbols-outlined text-[16px]">edit</span>
                                </button>
                                <button type="button" class="js-delete-btn flex h-8 w-8 items-center justify-center rounded-lg border-[3px] border-ink bg-paper-raised text-coral-dark transition-all hover:bg-paper active:translate-y-0.5" title="Hapus kebiasaan">
                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                </button>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <div id="habits-empty" class="{{ $habits->isEmpty() ? 'flex' : 'hidden' }} flex-col items-center gap-3 py-10 text-center">
                    <span class="sh-1 flex h-14 w-14 rotate-3 items-center justify-center rounded-2xl border-[3px] border-ink bg-yellow text-ink-const">
                        <span class="material-symbols-outlined text-[26px]">sticky_note_2</span>
                    </span>
                    <p class="max-w-[30ch] font-display text-[15px] font-semibold text-ink">Belum ada kebiasaan.</p>
                    <p class="max-w-[40ch] text-sm text-ink-soft">Tambahkan lewat form di samping, lalu cap setiap kali kamu menyelesaikannya.</p>
                </div>
            </section>

            <aside class="space-y-6 lg:col-span-5">
                <section class="sh-2 rounded-[24px] border-[3px] border-ink bg-paper-raised p-5 sm:p-6">
                    <div class="flex items-center gap-2.5 border-b-[3px] border-dashed border-ink/20 pb-4">
                        <span class="sh-1 flex h-8 w-8 rotate-2 items-center justify-center rounded-lg border-[3px] border-ink bg-sky text-white">
                            <span class="material-symbols-outlined text-[18px]">add</span>
                        </span>
                        <h2 class="font-display text-lg font-semibold text-ink">Tambah Kebiasaan</h2>
                    </div>

                    <form id="add-habit-form" class="mt-4 space-y-3">
                        <label for="habit-name" class="block font-display text-[13px] font-semibold text-ink">
                            Nama kebiasaan baru
                        </label>
                        <input id="habit-name" name="name" type="text" maxlength="120" autocomplete="off"
                               placeholder="cth. Minum 8 gelas air"
                               class="w-full rounded-[10px] border-[3px] border-ink bg-paper px-3 py-2.5 font-body text-[15px] font-bold text-ink shadow-[3px_3px_0_0_var(--ds-shadow)] placeholder:font-normal placeholder:text-ink-soft focus:outline-none">
                        <p id="add-form-error" class="hidden font-body text-sm font-bold text-coral-dark"></p>
                        <button type="submit" class="btn-stamp w-full bg-coral py-2.5 font-display text-[16px] font-semibold text-ink-const dark:text-white">
                            Tambah Kebiasaan
                        </button>
                    </form>
                </section>

                <section class="sh-2 rotate-1 rounded-[24px] border-[3px] border-ink bg-yellow p-5 sm:p-6">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-9 w-9 -rotate-3 items-center justify-center rounded-lg border-[3px] border-ink bg-paper-raised text-coral-dark">
                            <span class="material-symbols-outlined ms-fill text-[18px]">local_fire_department</span>
                        </span>
                        <div>
                            <h2 class="font-display text-[15px] font-semibold text-ink-const">Hari Beruntun</h2>
                            <p class="font-display text-[13px] font-medium text-ink-const">Streak kebiasaanmu saat ini</p>
                        </div>
                    </div>
                    <p class="mt-4 font-display text-[42px] font-semibold leading-none text-ink-const">
                        <span id="streak-big">{{ $streak }}</span>
                        <span class="text-xl font-semibold">hari</span>
                    </p>
                    <p class="mt-2 text-sm font-semibold text-ink-const/80">
                        Selesaikan minimal satu kebiasaan setiap hari untuk menjaga streak tetap menyala.
                    </p>
                </section>
            </aside>
        </div>

        <p id="page-error" class="mt-4 hidden font-body text-sm font-bold text-coral-dark"></p>
    </div>
@endsection
