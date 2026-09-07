document.addEventListener('DOMContentLoaded', () => {
    const wall = document.getElementById('notes-wall');

    if (!wall) {
        return;
    }

    const token = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const emptyState = document.getElementById('notes-empty');
    const addForm = document.getElementById('add-note-form');
    const noteInput = document.getElementById('note-input');
    const addError = document.getElementById('add-form-error');
    const addSwatches = document.querySelector('.note-add-swatches');
    const modal = document.getElementById('note-modal');
    const modalInput = document.getElementById('note-modal-input');
    const modalError = document.getElementById('modal-error');
    const modalSwatches = document.querySelector('.note-modal-swatches');
    const modalSave = document.getElementById('note-modal-save');
    const modalCancel = document.getElementById('note-modal-cancel');
    const modalClose = document.getElementById('note-modal-close');

    const colorBg = {
        yellow: 'bg-yellow',
        sky: 'bg-sky',
        coral: 'bg-coral',
    };

    const rotations = ['-rotate-2', 'rotate-1', 'rotate-2', '-rotate-1'];

    let addColor = 'yellow';
    let modalColor = 'yellow';
    let editingId = null;
    let errorTimer = null;

    async function api(url, options = {}) {
        const response = await fetch(url, {
            method: options.method ?? 'GET',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                ...(options.headers ?? {}),
            },
            body: options.body === undefined ? undefined : JSON.stringify(options.body),
        });

        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            const message = typeof data.message === 'string' && data.message
                ? data.message
                : 'Terjadi kesalahan. Coba lagi.';
            throw new Error(message);
        }

        return data;
    }

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;');
    }

    function setActiveSwatch(group, color) {
        for (const swatch of group.querySelectorAll('.note-swatch')) {
            const active = swatch.dataset.color === color;
            const check = swatch.querySelector('.material-symbols-outlined');

            swatch.classList.toggle('ring-4', active);
            swatch.classList.toggle('ring-sky-dark', active);
            swatch.classList.toggle('scale-105', active);
            swatch.setAttribute('aria-pressed', String(active));
            check.classList.toggle('opacity-0', !active);
            check.classList.toggle('opacity-100', active);
        }
    }

    function showError(message, target) {
        if (!target) {
            return;
        }

        target.textContent = message;
        target.classList.remove('hidden');

        window.clearTimeout(errorTimer);
        errorTimer = window.setTimeout(() => {
            target.classList.add('hidden');
        }, 4000);
    }

    function syncEmptyState() {
        const hasNotes = wall.querySelectorAll('.note-card').length > 0;
        emptyState.classList.toggle('flex', !hasNotes);
        emptyState.classList.toggle('hidden', hasNotes);
    }

    function noteCardHtml(note) {
        const rotation = rotations[Math.floor(Math.random() * rotations.length)];

        return `
            <article data-id="${note.id}" data-color="${note.color}"
                     class="note-card relative mb-5 inline-block w-full break-inside-avoid rounded-[18px] border-[3px] border-ink p-4 pt-5 ${colorBg[note.color]} text-ink-const shadow-[5px_5px_0_0_var(--ds-shadow)] ${rotation} transition-transform duration-200 hover:rotate-0">
                <span class="pointer-events-none absolute -top-2.5 left-1/2 h-4 w-4 -translate-x-1/2 rounded-full border-2 border-white bg-ink"></span>
                <p class="js-note-body whitespace-pre-wrap break-words font-body text-[15px] font-bold leading-6">${escapeHtml(note.body)}</p>
                <div class="mt-3 flex items-center justify-end gap-1.5">
                    <button type="button" class="js-note-edit flex h-7 w-7 items-center justify-center rounded-lg border-2 border-ink/40 bg-white/25 transition-all hover:bg-white/50" title="Ubah catatan">
                        <span class="material-symbols-outlined text-[15px]">edit</span>
                    </button>
                    <button type="button" class="js-note-delete flex h-7 w-7 items-center justify-center rounded-lg border-2 border-ink/40 bg-white/25 transition-all hover:bg-white/50" title="Hapus catatan">
                        <span class="material-symbols-outlined text-[15px]">delete</span>
                    </button>
                </div>
            </article>
        `;
    }

    function openModal(card) {
        editingId = card.dataset.id;
        modalColor = card.dataset.color;
        modalInput.value = card.querySelector('.js-note-body').textContent;
        modalError.classList.add('hidden');
        setActiveSwatch(modalSwatches, modalColor);
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modalInput.focus();
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        editingId = null;
    }

    for (const swatch of addSwatches.querySelectorAll('.note-swatch')) {
        swatch.addEventListener('click', () => {
            addColor = swatch.dataset.color;
            setActiveSwatch(addSwatches, addColor);
        });
    }

    for (const swatch of modalSwatches.querySelectorAll('.note-swatch')) {
        swatch.addEventListener('click', () => {
            modalColor = swatch.dataset.color;
            setActiveSwatch(modalSwatches, modalColor);
        });
    }

    addForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        const body = noteInput.value.trim();
        const submitBtn = addForm.querySelector('button[type="submit"]');

        if (!body) {
            showError('Isi catatan tidak boleh kosong.', addError);
            return;
        }

        submitBtn.disabled = true;

        try {
            const data = await api('/notes', { method: 'POST', body: { body, color: addColor } });
            noteInput.value = '';
            wall.insertAdjacentHTML('afterbegin', noteCardHtml(data.note));
            addError.classList.add('hidden');
            syncEmptyState();
            noteInput.focus();
        } catch (error) {
            showError(error.message, addError);
        } finally {
            submitBtn.disabled = false;
        }
    });

    wall.addEventListener('click', async (event) => {
        const card = event.target.closest('.note-card');

        if (!card) {
            return;
        }

        if (event.target.closest('.js-note-edit')) {
            openModal(card);
            return;
        }

        if (event.target.closest('.js-note-delete')) {
            const id = card.dataset.id;

            if (!window.confirm('Hapus catatan ini?')) {
                return;
            }

            try {
                await api(`/notes/${id}`, { method: 'DELETE' });
                card.remove();
                syncEmptyState();
            } catch (error) {
                showError(error.message, addError);
            }
        }
    });

    modalSave.addEventListener('click', async () => {
        const body = modalInput.value.trim();

        if (!body) {
            showError('Isi catatan tidak boleh kosong.', modalError);
            return;
        }

        modalSave.disabled = true;

        try {
            const data = await api(`/notes/${editingId}`, { method: 'PATCH', body: { body, color: modalColor } });
            const card = wall.querySelector(`.note-card[data-id="${editingId}"]`);

            card.querySelector('.js-note-body').textContent = data.note.body;

            for (const colorClass of Object.values(colorBg)) {
                card.classList.remove(colorClass);
            }

            card.classList.add(colorBg[data.note.color]);
            card.dataset.color = data.note.color;
            modalError.classList.add('hidden');
            closeModal();
        } catch (error) {
            showError(error.message, modalError);
        } finally {
            modalSave.disabled = false;
        }
    });

    modalCancel.addEventListener('click', closeModal);
    modalClose.addEventListener('click', closeModal);
    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });

    setActiveSwatch(addSwatches, addColor);
});
