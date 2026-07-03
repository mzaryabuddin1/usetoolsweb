$(function () {
    'use strict';

    $('#btn-regex-test').on('click', function () {
        var pattern = $('#regex-pattern').val();
        var flags = $('#regex-flags').val() || 'g';
        var testStr = $('#regex-test').val();

        if (!pattern) {
            $('#regex-error').text('Enter a regex pattern.').removeClass('hidden');
            $('#regex-results').addClass('hidden');
            return;
        }

        try {
            var regex = new RegExp(pattern, flags);
            var matches = [];
            var output = [];

            if (flags.indexOf('g') !== -1) {
                var m;
                while ((m = regex.exec(testStr)) !== null) {
                    matches.push(m);
                    if (m[0] === '') regex.lastIndex++;
                }
            } else {
                var single = testStr.match(regex);
                if (single) matches.push(single);
            }

            matches.forEach(function (match, i) {
                output.push('Match ' + (i + 1) + ': "' + match[0] + '" at index ' + match.index);
                for (var g = 1; g < match.length; g++) {
                    if (match[g] !== undefined) {
                        output.push('  Group ' + g + ': "' + match[g] + '"');
                    }
                }
            });

            $('#regex-count').text(matches.length);
            $('#regex-output').val(output.length ? output.join('\n') : 'No matches found.');
            $('#regex-error').addClass('hidden');
            $('#regex-results').removeClass('hidden');
        } catch (e) {
            $('#regex-error').text('Invalid regex: ' + e.message).removeClass('hidden');
            $('#regex-results').addClass('hidden');
        }
    });

    $('#btn-regex-clear').on('click', function () {
        $('#regex-pattern').val('');
        $('#regex-flags').val('g');
        $('#regex-test, #regex-output').val('');
        $('#regex-error').addClass('hidden');
        $('#regex-results').addClass('hidden');
    });
});
