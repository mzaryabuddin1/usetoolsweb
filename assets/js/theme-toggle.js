/**
 * Theme toggle — follows system preference by default, saves manual choice.
 */
(function () {
    'use strict';

    var STORAGE_KEY = 'qt-theme';

    function systemPrefersDark() {
        return window.matchMedia('(prefers-color-scheme: dark)').matches;
    }

    function isDark() {
        return document.documentElement.getAttribute('data-theme') === 'dark';
    }

    function applyTheme(mode) {
        var root = document.documentElement;
        var dark;

        if (mode === 'light') {
            localStorage.setItem(STORAGE_KEY, 'light');
            dark = false;
        } else if (mode === 'dark') {
            localStorage.setItem(STORAGE_KEY, 'dark');
            dark = true;
        } else {
            localStorage.removeItem(STORAGE_KEY);
            dark = systemPrefersDark();
        }

        if (dark) {
            root.setAttribute('data-theme', 'dark');
        } else {
            root.removeAttribute('data-theme');
        }

        root.style.colorScheme = dark ? 'dark' : 'light';
        syncToggleButton();
    }

    function syncToggleButton() {
        var btn = document.getElementById('theme-toggle');
        if (!btn) return;

        var dark = isDark();
        btn.setAttribute('aria-pressed', dark ? 'true' : 'false');
        btn.setAttribute('aria-label', dark ? 'Switch to light mode' : 'Switch to dark mode');
        btn.setAttribute('title', dark ? 'Switch to light mode' : 'Switch to dark mode');
        btn.classList.toggle('is-dark', dark);
    }

    function init() {
        var saved = localStorage.getItem(STORAGE_KEY);
        if (saved === 'light' || saved === 'dark') {
            applyTheme(saved);
        } else {
            applyTheme('system');
        }

        var btn = document.getElementById('theme-toggle');
        if (btn) {
            btn.addEventListener('click', function () {
                applyTheme(isDark() ? 'light' : 'dark');
            });
        }

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function () {
            if (!localStorage.getItem(STORAGE_KEY)) {
                applyTheme('system');
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
