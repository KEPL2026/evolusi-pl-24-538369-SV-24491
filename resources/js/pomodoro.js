document.addEventListener('DOMContentLoaded', () => {
    const display = document.getElementById('timer-display');

    if (!display) {
        return;
    }

    const SETTINGS_KEY = 'daily-sync.pomodoro.settings';
    const STATE_KEY = 'daily-sync.pomodoro.state';

    const token = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const statusLabel = document.getElementById('status-label');
    const roundInfo = document.getElementById('round-info');
    const toggleBtn = document.getElementById('toggle-timer');
    const toggleIcon = document.getElementById('toggle-icon');
    const toggleLabel = document.getElementById('toggle-label');
    const resetBtn = document.getElementById('reset-timer');
    const timerHint = document.getElementById('timer-hint');
    const sessionList = document.getElementById('session-list');
    const sessionEmpty = document.getElementById('session-empty');
    const sessionCount = document.getElementById('session-count');
    const sessionMinutes = document.getElementById('session-minutes');
    const modeBtns = [...document.querySelectorAll('.mode-btn')];
    const inputs = {
        focus: document.getElementById('focus-input'),
        short: document.getElementById('short-input'),
        long: document.getElementById('long-input'),
    };

    const MODE_LABELS = {
        focus: 'Sesi Fokus',
        short_break: 'Istirahat Pendek',
        long_break: 'Istirahat Panjang',
    };

    const SETTING_FOR_MODE = {
        focus: 'focus',
        short_break: 'short',
        long_break: 'long',
    };

    const defaultSettings = { focus: 25, short: 5, long: 15 };

    let settings = { ...defaultSettings };
    let state = null;
    let tickTimer = null;
    let audioContext = null;
    let hintTimer = null;

    function loadSettings() {
        try {
            const stored = JSON.parse(localStorage.getItem(SETTINGS_KEY) ?? '{}');
            for (const key of ['focus', 'short', 'long']) {
                const value = Number(stored[key]);
                if (Number.isInteger(value) && value >= 1 && value <= 180) {
                    settings[key] = value;
                }
            }
        } catch (error) {
            settings = { ...defaultSettings };
        }
    }

    function loadState() {
        const full = () => fullSeconds(state.mode);

        state = {
            mode: 'focus',
            remaining: null,
            running: false,
            endsAt: null,
            round: 1,
        };

        try {
            const stored = JSON.parse(localStorage.getItem(STATE_KEY) ?? '{}');
            if (MODE_LABELS[stored.mode]) {
                state.mode = stored.mode;
            }
            state.round = Number(stored.round) >= 1 && Number(stored.round) <= 4 ? Number(stored.round) : 1;
            state.running = stored.running === true && Number(stored.endsAt) > 0;

            if (state.running) {
                state.endsAt = Number(stored.endsAt);
                state.remaining = Math.max(0, Math.ceil((state.endsAt - Date.now()) / 1000));
            } else {
                state.remaining = full();
            }
        } catch (error) {
            state.running = false;
            state.endsAt = null;
            state.remaining = full();
        }

        if (state.running && state.remaining <= 0) {
            state.running = false;
            state.endsAt = null;
            finish(true);
        }
    }

    function persist() {
        localStorage.setItem(STATE_KEY, JSON.stringify({
            mode: state.mode,
            remaining: state.remaining,
            running: state.running,
            endsAt: state.endsAt,
            round: state.round,
        }));
    }

    function fullSeconds(mode) {
        return settings[SETTING_FOR_MODE[mode]] * 60;
    }

    function formatTime(totalSeconds) {
        const minutes = Math.floor(totalSeconds / 60);
        const seconds = totalSeconds % 60;

        return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
    }

    function render() {
        display.textContent = formatTime(state.remaining);
        statusLabel.textContent = MODE_LABELS[state.mode];
        roundInfo.textContent = state.mode === 'focus'
            ? `Ronde ${state.round} dari 4`
            : `Jeda ${state.mode === 'long_break' ? 'panjang' : 'pendek'}`;

        toggleIcon.textContent = state.running ? 'pause' : 'play_arrow';
        toggleLabel.textContent = state.running ? 'Pause' : 'Mulai';
        toggleBtn.setAttribute('aria-pressed', String(state.running));

        for (const btn of modeBtns) {
            const active = btn.dataset.mode === state.mode;
            btn.classList.toggle('bg-paper-raised', active);
            btn.classList.toggle('text-ink', active);
            btn.classList.toggle('border-ink', active);
            btn.classList.toggle('bg-white/10', !active);
            btn.classList.toggle('text-white', !active);
            btn.classList.toggle('border-white/80', !active);
            btn.setAttribute('aria-pressed', String(active));
        }
    }

    function tick() {
        if (!state.running) {
            return;
        }

        state.remaining = Math.max(0, Math.ceil((state.endsAt - Date.now()) / 1000));

        if (state.remaining <= 0) {
            finish(false);
            return;
        }

        render();
    }

    async function logFocusSession() {
        const duration = settings.focus;

        try {
            const response = await fetch('/pomodoro/sessions', {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                body: JSON.stringify({ type: 'focus', duration_minutes: duration }),
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                throw new Error('Gagal menyimpan sesi fokus.');
            }

            sessionCount.textContent = String(data.today.count);
            sessionMinutes.textContent = String(data.today.total_minutes);
            sessionEmpty.classList.add('hidden');
            sessionEmpty.classList.remove('flex');

            const item = document.createElement('li');
            item.className = 'flex items-center justify-between gap-3 rounded-xl border-2 border-ink bg-paper px-3 py-2';
            item.innerHTML = `
                <span class="flex items-center gap-2 font-body text-[14px] font-bold text-ink">
                    <span class="material-symbols-outlined ms-fill text-green-dark text-[16px]">check_circle</span>
                    Sesi fokus ${data.session.duration_minutes} menit
                </span>
                <span class="font-display text-[12px] font-semibold text-ink-soft">${data.session.completed_at}</span>
            `;
            sessionList.prepend(item);
        } catch (error) {
            showHint(error.message);
        }
    }

    function advanceAfterFinish() {
        let nextMode;

        if (state.mode === 'focus') {
            if (state.round >= 4) {
                state.round = 1;
                nextMode = 'long_break';
            } else {
                state.round += 1;
                nextMode = 'short_break';
            }
        } else {
            nextMode = 'focus';
        }

        state.mode = nextMode;
        state.running = false;
        state.endsAt = null;
        state.remaining = fullSeconds(nextMode);
        persist();
        render();
        showHint(`Waktu selesai. ${MODE_LABELS[nextMode]} berikutnya sudah siap.`);
    }

    function finish(silent = false) {
        const wasFocus = state.mode === 'focus';

        state.running = false;
        state.endsAt = null;
        state.remaining = 0;
        persist();

        if (!silent) {
            playChime();
        }

        if (wasFocus) {
            logFocusSession();
        }

        advanceAfterFinish();
    }

    function ensureAudio() {
        if (!window.DailySync.audioEnabled) {
            return;
        }

        if (!audioContext) {
            audioContext = new (window.AudioContext || window.webkitAudioContext)();
        }

        if (audioContext.state === 'suspended') {
            audioContext.resume();
        }
    }

    function playChime() {
        if (!window.DailySync.audioEnabled || !audioContext) {
            return;
        }

        const now = audioContext.currentTime;

        for (const [frequency, offset] of [[587.33, 0], [880, 0.18], [587.33, 0.36]]) {
            const oscillator = audioContext.createOscillator();
            const gain = audioContext.createGain();
            oscillator.type = 'sine';
            oscillator.frequency.setValueAtTime(frequency, now + offset);
            gain.gain.setValueAtTime(0.001, now + offset);
            gain.gain.exponentialRampToValueAtTime(0.35, now + offset + 0.02);
            gain.gain.exponentialRampToValueAtTime(0.001, now + offset + 0.55);
            oscillator.connect(gain);
            gain.connect(audioContext.destination);
            oscillator.start(now + offset);
            oscillator.stop(now + offset + 0.6);
        }
    }

    function showHint(message) {
        timerHint.textContent = message;
        window.clearTimeout(hintTimer);
        hintTimer = window.setTimeout(() => {
            timerHint.textContent = 'Notifikasi audio berbunyi saat timer selesai. Status timer tersimpan otomatis.';
        }, 5000);
    }

    function stopAndResetToMode(mode) {
        state.mode = mode;
        state.running = false;
        state.endsAt = null;
        state.remaining = fullSeconds(mode);
        persist();
        render();
    }

    function syncSettingsFromInputs() {
        for (const key of ['focus', 'short', 'long']) {
            const value = Math.min(180, Math.max(1, Math.round(Number(inputs[key].value) || 1)));
            inputs[key].value = String(value);
            settings[key] = value;
        }

        localStorage.setItem(SETTINGS_KEY, JSON.stringify(settings));

        for (const btn of modeBtns) {
            const labelEl = document.getElementById(`${btn.dataset.mode === 'short_break' ? 'short' : btn.dataset.mode === 'long_break' ? 'long' : 'focus'}-min`);
            labelEl.textContent = String(settings[SETTING_FOR_MODE[btn.dataset.mode]]);
        }

        if (!state.running) {
            state.remaining = fullSeconds(state.mode);
            persist();
        }

        render();
    }

    toggleBtn.addEventListener('click', () => {
        ensureAudio();

        if (state.running) {
            state.remaining = Math.max(0, Math.ceil((state.endsAt - Date.now()) / 1000));
            state.running = false;
            state.endsAt = null;
        } else {
            if (state.remaining <= 0) {
                state.remaining = fullSeconds(state.mode);
            }
            state.running = true;
            state.endsAt = Date.now() + state.remaining * 1000;
        }

        persist();
        render();
    });

    resetBtn.addEventListener('click', () => {
        state.running = false;
        state.endsAt = null;
        state.remaining = fullSeconds(state.mode);
        persist();
        render();
    });

    for (const btn of modeBtns) {
        btn.addEventListener('click', () => {
            ensureAudio();
            stopAndResetToMode(btn.dataset.mode);
        });
    }

    for (const key of ['focus', 'short', 'long']) {
        inputs[key].addEventListener('change', syncSettingsFromInputs);
    }

    loadSettings();
    loadState();

    for (const key of ['focus', 'short', 'long']) {
        inputs[key].value = String(settings[key]);
    }

    syncSettingsFromInputs();

    tickTimer = window.setInterval(tick, 250);

    render();
    persist();
});
