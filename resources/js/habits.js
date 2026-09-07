document.addEventListener('DOMContentLoaded', () => {
    const list = document.getElementById('habits-list');

    if (!list) {
        return;
    }

    const token = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const progressFill = document.getElementById('progress-fill');
    const progressText = document.getElementById('progress-text');
    const habitCount = document.getElementById('habit-count');
    const emptyState = document.getElementById('habits-empty');
    const streakChip = document.getElementById('streak-chip');
    const streakText = document.getElementById('streak-text');
    const streakBig = document.getElementById('streak-big');
    const pageError = document.getElementById('page-error');
    const addForm = document.getElementById('add-habit-form');
    const addName = document.getElementById('habit-name');
    const addError = document.getElementById('add-form-error');

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

    function setDone(row, done) {
        const stamp = row.querySelector('.js-toggle');
        const label = row.querySelector('.js-label');

        row.dataset.done = done ? 'true' : 'false';

        stamp.classList.toggle('bg-green', done);
        stamp.classList.toggle('text-white', done);
        stamp.classList.toggle('bg-paper-raised', !done);
        stamp.classList.toggle('text-transparent', !done);
        stamp.setAttribute('aria-pressed', String(done));
        stamp.title = done ? 'Batalkan cap' : 'Tandai selesai';

        label.classList.toggle('line-through', done);
        label.classList.toggle('text-ink-soft', done);
        label.classList.toggle('text-ink', !done);
    }

    function updateProgress(progress) {
        const completed = progress.completed ?? 0;
        const total = progress.total ?? 0;
        const percent = progress.percent ?? 0;

        progressFill.style.width = `${percent}%`;
        progressText.textContent = `${completed} / ${total} selesai • ${percent}%`;
        habitCount.textContent = `${total} item`;

        if (total === 0) {
            emptyState.classList.remove('hidden');
            emptyState.classList.add('flex');
        } else {
            emptyState.classList.add('hidden');
            emptyState.classList.remove('flex');
        }
    }

    function updateStreak(streak) {
        const value = Number(streak) || 0;

        streakChip.classList.toggle('flex', value > 0);
        streakChip.classList.toggle('hidden', value === 0);
        streakText.textContent = `${value} hari beruntun`;
        streakBig.textContent = String(value);
    }

    function setHabitStreak(row, streak) {
        const chip = row.querySelector('.js-streak');
        const value = Number(streak) || 0;

        chip.classList.toggle('flex', value > 0);
        chip.classList.toggle('hidden', value === 0);
        chip.querySelector('.js-streak-num').textContent = String(value);
    }

    function showError(message, target = pageError) {
        if (!target) {
            return;
        }

        target.textContent = message;
        target.classList.remove('hidden');

        window.clearTimeout(target._timer);
        target._timer = window.setTimeout(() => {
            target.classList.add('hidden');
        }, 4000);
    }

    function habitRowHtml(habit) {
        return `
            <li class="habit-row flex flex-wrap items-center gap-3 rounded-xl border-[3px] border-ink bg-paper p-3 transition-colors hover:bg-paper-raised sm:flex-nowrap"
                data-id="${habit.id}" data-done="false">
                <button type="button"
                        class="js-toggle flex h-8 w-8 shrink-0 items-center justify-center rounded-[10px] border-[3px] border-ink bg-paper-raised text-transparent transition-all duration-150 active:scale-75 active:-rotate-6"
                        aria-pressed="false" title="Tandai selesai">
                    <span class="material-symbols-outlined ms-fill text-[18px]">check</span>
                </button>
                <span class="js-label min-w-0 flex-1 truncate font-body text-[16px] font-bold text-ink">${escapeHtml(habit.name)}</span>
                <div class="js-edit hidden min-w-0 flex-1 items-center gap-2">
                    <input type="text" maxlength="120"
                           class="js-rename-input w-full rounded-[10px] border-[3px] border-ink bg-paper-raised px-3 py-1.5 font-body text-[15px] font-bold text-ink focus:outline-none"
                           value="${escapeHtml(habit.name)}">
                    <button type="button" class="js-rename-save btn-stamp shrink-0 bg-green px-3 py-1.5 text-white" title="Simpan nama">
                        <span class="material-symbols-outlined text-[16px]">check</span>
                    </button>
                    <button type="button" class="js-rename-cancel btn-stamp shrink-0 bg-paper px-3 py-1.5 text-ink" title="Batal">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                    </button>
                </div>
                <span class="js-streak hidden items-center gap-1 rounded-full border-2 border-ink bg-yellow px-2 py-0.5 font-display text-[12px] font-semibold text-ink-const">
                    <span class="material-symbols-outlined ms-fill text-[13px]">local_fire_department</span>
                    <span class="js-streak-num">0</span>
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
        `;
    }

    function enterEditMode(row) {
        const label = row.querySelector('.js-label');
        const editBox = row.querySelector('.js-edit');
        const input = row.querySelector('.js-rename-input');

        label.classList.add('hidden');
        editBox.classList.remove('hidden');
        editBox.classList.add('flex');
        input.focus();
        input.select();
    }

    function exitEditMode(row, savedName = null) {
        const label = row.querySelector('.js-label');
        const editBox = row.querySelector('.js-edit');

        if (savedName !== null) {
            label.textContent = savedName;
        }

        label.classList.remove('hidden');
        editBox.classList.add('hidden');
        editBox.classList.remove('flex');
    }

    list.addEventListener('click', async (event) => {
        const row = event.target.closest('.habit-row');

        if (!row) {
            return;
        }

        const toggleBtn = event.target.closest('.js-toggle');
        const editBtn = event.target.closest('.js-edit-btn');
        const deleteBtn = event.target.closest('.js-delete-btn');
        const saveBtn = event.target.closest('.js-rename-save');
        const cancelBtn = event.target.closest('.js-rename-cancel');

        if (toggleBtn) {
            const habitId = row.dataset.id;
            const wasDone = row.dataset.done === 'true';

            toggleBtn.disabled = true;
            setDone(row, !wasDone);

            try {
                const data = await api(`/habits/${habitId}/toggle`, { method: 'POST' });
                setDone(row, data.completed);
                updateProgress(data.progress);
                updateStreak(data.streak);
                setHabitStreak(row, data.habit_streak);
            } catch (error) {
                setDone(row, wasDone);
                showError(error.message);
            } finally {
                toggleBtn.disabled = false;
            }

            return;
        }

        if (editBtn) {
            enterEditMode(row);
            return;
        }

        if (cancelBtn) {
            exitEditMode(row);
            return;
        }

        if (saveBtn) {
            const input = row.querySelector('.js-rename-input');
            const name = input.value.trim();
            const habitId = row.dataset.id;

            if (!name) {
                showError('Nama kebiasaan tidak boleh kosong.');
                return;
            }

            saveBtn.disabled = true;

            try {
                const data = await api(`/habits/${habitId}`, { method: 'PATCH', body: { name } });
                exitEditMode(row, data.habit.name);
            } catch (error) {
                showError(error.message);
            } finally {
                saveBtn.disabled = false;
            }

            return;
        }

        if (deleteBtn) {
            const label = row.querySelector('.js-label');
            const habitId = row.dataset.id;

            if (!window.confirm(`Hapus kebiasaan "${label.textContent}"?`)) {
                return;
            }

            deleteBtn.disabled = true;

            try {
                const data = await api(`/habits/${habitId}`, { method: 'DELETE' });
                row.remove();
                updateProgress(data.progress);
                updateStreak(data.streak);
            } catch (error) {
                showError(error.message);
                deleteBtn.disabled = false;
            }

            return;
        }
    });

    addForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        const name = addName.value.trim();
        const submitBtn = addForm.querySelector('button[type="submit"]');

        if (!name) {
            showError('Nama kebiasaan tidak boleh kosong.', addError);
            return;
        }

        submitBtn.disabled = true;

        try {
            const data = await api('/habits', { method: 'POST', body: { name } });
            addName.value = '';
            addError.classList.add('hidden');
            list.insertAdjacentHTML('afterbegin', habitRowHtml(data.habit));
            updateProgress(data.progress);
            addName.focus();
        } catch (error) {
            showError(error.message, addError);
        } finally {
            submitBtn.disabled = false;
        }
    });
});
