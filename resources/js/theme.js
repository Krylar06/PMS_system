/**
 * resources/js/theme.js
 * ----------------------
 * Theme switcher logic: Light / Dark / System.
 * Saves preference to localStorage so it persists across future sessions,
 * and re-applies it automatically on load — including after Livewire
 * wire:navigate page swaps (which don't do a full page reload).
 *
 * Import this in resources/js/app.js:
 *   import './theme';
 */

const THEME_KEY = 'theme';

function getSavedTheme() {
    return localStorage.getItem(THEME_KEY) || 'system';
}

function systemPrefersDark() {
    return window.matchMedia('(prefers-color-scheme: dark)').matches;
}

function resolveIsDark(theme) {
    return theme === 'dark' || (theme === 'system' && systemPrefersDark());
}

function applyTheme(theme) {
    const isDark = resolveIsDark(theme);
    document.documentElement.classList.toggle('dark', isDark);
    return isDark;
}

function setTheme(theme) {
    localStorage.setItem(THEME_KEY, theme);
    applyTheme(theme);
    window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme } }));
}

// Apply on first load
applyTheme(getSavedTheme());

// Re-apply after Livewire navigates to a new page (wire:navigate),
// since the <html> class can otherwise be lost on partial swaps.
document.addEventListener('livewire:navigated', function () {
    applyTheme(getSavedTheme());
});

// If the user picked "System" and their OS theme changes while the app
// is open, follow it live.
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function () {
    if (getSavedTheme() === 'system') {
        applyTheme('system');
    }
});

// Expose globally so Alpine components (x-data) can call window.setTheme('dark') etc.
window.setTheme = setTheme;
window.getSavedTheme = getSavedTheme;