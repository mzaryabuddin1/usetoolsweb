(function () {
    'use strict';

    var KEY = 'utw_cookie_consent';
    var banner = document.getElementById('cookie-consent');

    if (!banner) return;

    if (localStorage.getItem(KEY) === '1') {
        banner.remove();
        return;
    }

    banner.classList.remove('hidden');

    document.getElementById('cookie-accept').addEventListener('click', function () {
        localStorage.setItem(KEY, '1');
        banner.classList.add('hidden');
    });
})();
