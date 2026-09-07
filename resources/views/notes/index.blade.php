@extends('layouts.app')

@section('content')
    @php
        $colorClasses = [
            'yellow' => 'bg-yellow text-ink-const',
            'sky' => 'bg-sky text-ink-const',
            'coral' => 'bg-coral text-ink-const',
        ];
        $rotations = ['-rotate-2', 'rotate-1', 'rotate-2', '-rotate-1'];
    @endphp

    <div class="mx-auto w-full max-w-[1120px] px-4 pb-10 pt-8 sm:px-6 sm:pt-10">

        <div class="mb-6 flex items-center gap-3">
            <span class="sh-1 flex h-11 w-11 -rotate-2 items-center justify-center rounded-xl border-[3px] border-ink bg-yellow text-ink-const">
                <span class="material-symbols-outlined text-[22px]">note_alt</span>
            </span>
            <div>
                <h1 class="font-display text-2xl font-semibold text-ink sm:text-3xl">Daily Quick Notes</h1>
                <p class="text-sm text-ink-soft">Tempel ide dan pengingat harianmu di papan catatan.</p>
            </div>
        </div>

        <section class="sh-2 rounded-[24px] border-[3px] border-ink bg-paper-raised p-5 sm:p-6">
            <form id="add-note-form" class="flex flex-col gap-4 sm:flex-row sm:items-end sm:gap-5">
                <div class="flex-1">
                    <label for="note-input" class="mb-1.5 block font-display text-[13px] font-semibold text-ink">
                        Catatan baru
                    </label>
                    <input id="note-input" type="text" maxlength="1000" autocomplete="off"
                           placeholder="cth. Beli bahan untuk eksperimen besok"
                           class="w-full rounded-[10px] border-[3px] border-ink bg-paper px-3 py-2 font-body text-[15px] font-bold text-ink shadow-[3px_3px_0_0_var(--ds-shadow)] placeholder:font-normal placeholder:text-ink-soft focus:outline-none">
                </div>
                <div>
                    <span class="mb-1.5 block font-display text-[13px] font-semibold text-ink">Warna</span>
                    <div class="note-add-swatches flex items-center gap-2">
                        @foreach (['yellow', 'sky', 'coral'] as $color)
                            <button type="button" data-color="{{ $color }}" title="Warna {{ $color }}"
                                    class="note-swatch flex h-8 w-8 items-center justify-center rounded-[10px] border-[3px] border-ink bg-{{ $color }} transition-transform active:scale-90"
                                    aria-pressed="false">
                                <span class="material-symbols-outlined text-[14px] opacity-0">check</span>
                            </button>
                        @endforeach
                    </div>
                </div>
                <button type="submit" class="btn-stamp self-start bg-coral px-5 py-2 font-display text-[15px] font-semibold text-ink-const dark:text-white sm:self-end">
                    Tempel
                </button>
            </form>
            <p id="add-form-error" class="mt-3 hidden font-body text-sm font-bold text-coral-dark"></p>
        </section>

        <section class="mt-8">
            <div id="notes-wall" class="columns-1 gap-5 sm:columns-2 xl:columns-3">
                @foreach ($notes as $note)
                    <article data-id="{{ $note->id }}" data-color="{{ $note->color }}"
                             @class([
                                 'note-card relative mb-5 inline-block w-full break-inside-avoid rounded-[18px] border-[3px] border-ink p-4 pt-5 shadow-[5px_5px_0_0_var(--ds-shadow)] transition-transform duration-200 hover:rotate-0',
                                 $colorClasses[$note->color],
                                 $rotations[$loop->index % count($rotations)],
                             ])>
                        <span class="pointer-events-none absolute -top-2.5 left-1/2 h-4 w-4 -translate-x-1/2 rounded-full border-2 border-white bg-ink"></span>
                        <p class="js-note-body whitespace-pre-wrap break-words font-body text-[15px] font-bold leading-6">{{ $note->body }}</p>
                        <div class="mt-3 flex items-center justify-end gap-1.5">
                            <button type="button" class="js-note-edit flex h-7 w-7 items-center justify-center rounded-lg border-2 border-ink/40 bg-white/25 transition-all hover:bg-white/50" title="Ubah catatan">
                                <span class="material-symbols-outlined text-[15px]">edit</span>
                            </button>
                            <button type="button" class="js-note-delete flex h-7 w-7 items-center justify-center rounded-lg border-2 border-ink/40 bg-white/25 transition-all hover:bg-white/50" title="Hapus catatan">
                                <span class="material-symbols-outlined text-[15px]">delete</span>
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>

            <div id="notes-empty" class="{{ $notes->isEmpty() ? 'flex' : 'hidden' }} flex-col items-center gap-3 py-14 text-center">
                <span class="sh-1 flex h-14 w-14 rotate-3 items-center justify-center rounded-2xl border-[3px] border-ink bg-yellow text-ink-const">
                    <span class="material-symbols-outlined text-[26px]">sticky_note_2</span>
                </span>
                <p class="font-display text-[15px] font-semibold text-ink">Papan catatan masih kosong.</p>
                <p class="max-w-[40ch] text-sm text-ink-soft">Tulis sesuatu di atas, pilih warna, lalu tekan Tempel.</p>
            </div>
        </section>
    </div>

    <div id="note-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-ink/50 p-4">
        <div class="sh-3 w-full max-w-md rounded-[24px] border-[3px] border-ink bg-paper-raised p-5 sm:p-6">
            <div class="flex items-center justify-between gap-3 border-b-[3px] border-dashed border-ink/20 pb-4">
                <h2 class="font-display text-lg font-semibold text-ink">Ubah Catatan</h2>
                <button type="button" id="note-modal-close" class="flex h-8 w-8 items-center justify-center rounded-lg border-[3px] border-ink bg-paper text-ink" title="Tutup">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                </button>
            </div>

            <div class="mt-4">
                <label for="note-modal-input" class="mb-1 block font-display text-[13px] font-semibold text-ink">Isi catatan</label>
                <textarea id="note-modal-input" rows="3" maxlength="1000"
                          class="w-full rounded-[10px] border-[3px] border-ink bg-paper px-3 py-2 font-body text-[15px] font-bold text-ink shadow-[3px_3px_0_0_var(--ds-shadow)] focus:outline-none"></textarea>
            </div>

            <div class="mt-4">
                <span class="mb-1 block font-display text-[13px] font-semibold text-ink">Warna</span>
                <div class="note-modal-swatches flex items-center gap-2">
                    @foreach (['yellow', 'sky', 'coral'] as $color)
                        <button type="button" data-color="{{ $color }}" title="Warna {{ $color }}"
                                class="note-swatch flex h-10 w-10 items-center justify-center rounded-[10px] border-[3px] border-ink bg-{{ $color }} transition-transform active:scale-90"
                                aria-pressed="false">
                            <span class="material-symbols-outlined text-[18px] opacity-0">check</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="mt-5 flex items-center gap-2">
                <button type="button" id="note-modal-save" class="btn-stamp flex-1 bg-coral py-2.5 font-display font-semibold text-ink-const dark:text-white">
                    <span class="material-symbols-outlined text-[18px]">check</span>
                    Simpan
                </button>
                <button type="button" id="note-modal-cancel" class="btn-stamp bg-paper px-5 py-2.5 font-display font-semibold text-ink">
                    Batal
                </button>
            </div>
            <p id="modal-error" class="mt-3 hidden font-body text-sm font-bold text-coral-dark"></p>
        </div>
    </div>
@endsection
