import './habits.js';
import './pomodoro.js';
import './notes.js';

const THEME_KEY = 'daily-sync.theme';
const AUDIO_KEY = 'daily-sync.audio';

const root = document.documentElement;

function setAudioIcon(enabled) {
    const icon = document.getElementById('audio-icon');
    const label = document.getElementById('audio-label');

    if (!icon || !label) return;

    icon.textContent = enabled ? 'volume_up' : 'volume_off';
    icon.classList.toggle('text-green-dark', enabled);
    icon.classList.toggle('text-ink-soft', !enabled);
    label.textContent = enabled ? 'Audio On' : 'Audio Off';
}

function initThemeToggle() {
    const toggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const themeLabel = document.getElementById('theme-label');

    if (!toggle || !themeIcon) return;

    const applyTheme = (dark) => {
        root.classList.toggle('dark', dark);
        toggle.setAttribute('aria-pressed', String(dark));
        themeIcon.textContent = dark ? 'dark_mode' : 'light_mode';
        if (themeLabel) {
            themeLabel.textContent = dark ? 'Gelap' : 'Terang';
        }
    };

    applyTheme(root.classList.contains('dark'));

    toggle.addEventListener('click', () => {
        const dark = !root.classList.contains('dark');
        applyTheme(dark);
        localStorage.setItem(THEME_KEY, dark ? 'dark' : 'light');
    });
}

function initAudioToggle() {
    const toggle = document.getElementById('audio-toggle');
    if (!toggle) return;

    const stored = localStorage.getItem(AUDIO_KEY);
    const enabled = stored === null ? true : stored === 'on';

    setAudioIcon(enabled);

    toggle.addEventListener('click', () => {
        const next = !window.DailySync.audioEnabled;
        window.DailySync.audioEnabled = next;
        setAudioIcon(next);
        localStorage.setItem(AUDIO_KEY, next ? 'on' : 'off');
    });
}

window.DailySync = {
    audioEnabled: localStorage.getItem(AUDIO_KEY) !== 'off',
    get isDark() {
        return root.classList.contains('dark');
    },
};

document.addEventListener('DOMContentLoaded', () => {
    initThemeToggle();
    initAudioToggle();
});
