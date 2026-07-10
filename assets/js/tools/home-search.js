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
    var $trending = $('#trending-tools-section');
    var $catNav = $('#tool-category-nav');
    var $catBar = $('#cat-nav-bar');
    var $catScroll = $('#cat-nav-scroll');
    var $catPrev = $('#cat-nav-prev');
    var $catNext = $('#cat-nav-next');
    var listening = false;
    var recognition = null;
    var activeCat = 'all';
    var PREVIEW_COUNT = 6;

    if (!$search.length) return;

    function updateCatNavArrows() {
        var el = $catScroll[0];
        if (!el) return;

        var overflow = el.scrollWidth > el.clientWidth + 2;
        $catBar.toggleClass('has-overflow', overflow);

        if (!overflow) {
            $catPrev.prop('disabled', true);
            $catNext.prop('disabled', true);
            return;
        }

        $catPrev.prop('disabled', el.scrollLeft <= 2);
        $catNext.prop('disabled', el.scrollLeft + el.clientWidth >= el.scrollWidth - 2);
    }

    function scrollCatNavBy(delta) {
        var el = $catScroll[0];
        if (!el) return;
        el.scrollBy({ left: delta, behavior: 'smooth' });
    }

    function scrollActiveCatBtnIntoView() {
        var $active = $catNav.find('.cat-nav-btn.active');
        if (!$active.length) return;
        $active[0].scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
    }

    function setCategoryFilter(cat) {
        activeCat = cat;
        $catNav.find('.cat-nav-btn').removeClass('active');
        $catNav.find('.cat-nav-btn[data-cat="' + cat + '"]').addClass('active');

        $('.tool-category').each(function () {
            var $category = $(this);
            var key = $category.data('cat-key');
            var show = cat === 'all' || key === cat;
            $category.toggleClass('cat-filter-hidden', !show);
        });

        if (cat !== 'all') {
            var $target = $('#cat-' + cat);
            $target.removeClass('is-collapsed');
            updateToggleLabel($target);
        }

        updateVisibleCount();
        scrollActiveCatBtnIntoView();
        setTimeout(updateCatNavArrows, 300);
    }

    function updateVisibleCount() {
        var visible = 0;
        $('.tool-category').each(function () {
            if ($(this).hasClass('cat-filter-hidden') || $(this).hasClass('hidden')) {
                return;
            }
            visible += $(this).find('.tool-card:not(.hidden)').length;
        });
        $count.text(visible);
    }

    function updateToggleLabel($category) {
        var $btn = $category.find('.cat-toggle-btn');
        if (!$btn.length) return;

        var total = parseInt($category.data('tool-count'), 10) || 0;
        var collapsed = $category.hasClass('is-collapsed');
        $btn.text(collapsed ? 'Show all ' + total + ' tools' : 'Show fewer');
        $btn.attr('aria-expanded', collapsed ? 'false' : 'true');
    }

    function filterTools() {
        var query = $.trim($search.val()).toLowerCase();
        var visible = 0;
        var searching = query !== '';

        $clear.toggleClass('hidden', !searching);
        $bar.toggleClass('has-clear', searching);

        if (searching) {
            setCategoryFilter('all');
            $('.tool-category').removeClass('is-collapsed');
            $('.tool-category').each(function () {
                updateToggleLabel($(this));
            });
        }

        $('.tool-category').each(function () {
            var $category = $(this);
            if ($category.hasClass('cat-filter-hidden')) {
                return;
            }

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

        $label.text(query ? ' matching "' + $search.val() + '"' : '');
        $empty.toggleClass('hidden', visible > 0);
        $trending.toggleClass('hidden', searching);
        $catBar.toggleClass('hidden', searching);
        updateVisibleCount();
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

    $catPrev.on('click', function () {
        scrollCatNavBy(-220);
    });

    $catNext.on('click', function () {
        scrollCatNavBy(220);
    });

    $catScroll.on('scroll', updateCatNavArrows);
    $(window).on('resize', updateCatNavArrows);
    updateCatNavArrows();

    $catNav.on('click', '.cat-nav-btn', function () {
        var cat = $(this).data('cat');
        setCategoryFilter(cat);

        if (cat !== 'all') {
            var $target = $('#cat-' + cat);
            if ($target.length) {
                $target[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        } else {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    $('#tools').on('click', '.cat-toggle-btn', function () {
        var $category = $(this).closest('.tool-category');
        $category.toggleClass('is-collapsed');
        updateToggleLabel($category);
    });

    $('.tool-category-collapsible').each(function () {
        updateToggleLabel($(this));
    });

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
