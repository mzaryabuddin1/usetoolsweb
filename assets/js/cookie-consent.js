(function () {
    'use strict';

    var KEY = 'utw_cookie_consent';
    var banner = document.getElementById('cookie-consent');
    var gaLoaded = false;

    if (!banner) return;

    function loadAnalytics() {
        if (gaLoaded || !window.UTW_GA_ID) return;
        gaLoaded = true;

        window.dataLayer = window.dataLayer || [];
        window.gtag = window.gtag || function () { window.dataLayer.push(arguments); };

        var script = document.createElement('script');
        script.async = true;
        script.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(window.UTW_GA_ID);
        script.onload = function () {
            gtag('js', new Date());
            gtag('config', window.UTW_GA_ID, { anonymize_ip: true });
        };
        document.head.appendChild(script);
    }

    function hideBanner() {
        banner.classList.add('hidden');
    }

    var consent = localStorage.getItem(KEY);
    if (consent === '1') {
        loadAnalytics();
        banner.remove();
        return;
    }
    if (consent === '0') {
        banner.remove();
        return;
    }

    banner.classList.remove('hidden');

    document.getElementById('cookie-accept').addEventListener('click', function () {
        localStorage.setItem(KEY, '1');
        loadAnalytics();
        hideBanner();
    });

    var rejectBtn = document.getElementById('cookie-reject');
    if (rejectBtn) {
        rejectBtn.addEventListener('click', function () {
            localStorage.setItem(KEY, '0');
            hideBanner();
        });
    }
})();
