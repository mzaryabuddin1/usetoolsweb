$(function () {
    'use strict';

    // Mobile nav toggle
    $('.nav-toggle').on('click', function () {
        var $nav = $('.main-nav');
        var expanded = $(this).attr('aria-expanded') === 'true';
        $(this).attr('aria-expanded', !expanded);
        $nav.toggleClass('open');
    });

    // Close mobile nav on link click
    $('.main-nav a').on('click', function () {
        $('.main-nav').removeClass('open');
        $('.nav-toggle').attr('aria-expanded', 'false');
    });
});
