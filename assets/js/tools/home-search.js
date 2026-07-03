$(function () {
    'use strict';

    var $search = $('#tool-search');
    var $bar = $('#tool-search-bar');
    var $clear = $('#tool-search-clear');
    var $voice = $('#tool-search-voice');
    var $voiceStatus = $('#tool-search-voice-status');
    var $empty = $('#tool-search-empty');
    var $count = $('#tools-visible-count');
    var $label = $('#tools-search-label');
    var totalTools = $('.tool-card').length;
    var listening = false;
    var recognition = null;

    if (!$search.length) return;

    function filterTools() {
        var query = $.trim($search.val()).toLowerCase();
        var visible = 0;

        $clear.toggleClass('hidden', query === '');
        $bar.toggleClass('has-clear', query !== '');

        $('.tool-category').each(function () {
            var $category = $(this);
            var categoryVisible = 0;

            $category.find('.tool-card').each(function () {
                var $card = $(this);
                var match = !query || ($card.data('search') || '').indexOf(query) !== -1;

                $card.toggleClass('hidden', !match);
                if (match) categoryVisible++;
            });

            $category.toggleClass('hidden', categoryVisible === 0);
            visible += categoryVisible;
        });

        $count.text(visible);
        $label.text(query ? ' matching "' + $search.val() + '"' : '');
        $empty.toggleClass('hidden', visible > 0);
    }

    function setVoiceStatus(msg) {
        $voiceStatus.text(msg || '');
    }

    function setListening(active) {
        listening = active;
        $voice.toggleClass('is-listening', active);
        $voice.attr('aria-pressed', active ? 'true' : 'false');
        $voice.attr('aria-label', active ? 'Stop voice search' : 'Search by voice');
        $voice.attr('title', active ? 'Stop listening' : 'Search by voice');
        if (active) {
            setVoiceStatus('Listening… speak a tool name or keyword.');
        }
    }

    function initVoiceSearch() {
        var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

        if (!SpeechRecognition) {
            $voice.prop('disabled', true);
            $voice.attr('title', 'Voice search not supported in this browser');
            $voice.attr('aria-label', 'Voice search not supported in this browser');
            return;
        }

        recognition = new SpeechRecognition();
        recognition.continuous = false;
        recognition.interimResults = false;
        recognition.lang = document.documentElement.lang || 'en-US';

        recognition.onstart = function () {
            setListening(true);
        };

        recognition.onresult = function (event) {
            var transcript = event.results[0][0].transcript;
            $search.val(transcript);
            filterTools();
            setVoiceStatus('Searching for: ' + transcript);
            setTimeout(function () { setVoiceStatus(''); }, 2500);
        };

        recognition.onerror = function (event) {
            setListening(false);
            if (event.error === 'not-allowed') {
                setVoiceStatus('Microphone access denied.');
            } else if (event.error !== 'aborted') {
                setVoiceStatus('Voice search failed. Try again.');
            }
            setTimeout(function () { setVoiceStatus(''); }, 3000);
        };

        recognition.onend = function () {
            setListening(false);
        };

        $voice.on('click', function () {
            if (listening) {
                recognition.stop();
                return;
            }
            try {
                recognition.start();
            } catch (e) {
                setVoiceStatus('Voice search is already active.');
                setTimeout(function () { setVoiceStatus(''); }, 2000);
            }
        });
    }

    $search.on('input', filterTools);

    $clear.on('click', function () {
        $search.val('').trigger('focus');
        filterTools();
    });

    $search.on('keydown', function (e) {
        if (e.key === 'Escape') {
            if (listening && recognition) {
                recognition.stop();
            }
            $search.val('');
            filterTools();
        }
    });

    initVoiceSearch();
});
