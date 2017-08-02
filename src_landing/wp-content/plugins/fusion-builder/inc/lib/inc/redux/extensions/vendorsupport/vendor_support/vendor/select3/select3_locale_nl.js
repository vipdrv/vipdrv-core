/**
 * Select3 Dutch translation
 */
(function ($) {
    "use strict";

    $.fn.select3.locales['nl'] = {
        formatNoMatches: function () { return "Geen resultaten gevonden"; },
        formatInputTooShort: function (input, min) { var n = min - input.length; return "Vul nog " + n + " karakter" + (n == 1? "" : "s") + " in"; },
        formatInputTooLong: function (input, max) { var n = input.length - max; return "Haal " + n + " karakter" + (n == 1? "" : "s") + " weg"; },
        formatSelectionTooBig: function (limit) { return "Maximaal " + limit + " item" + (limit == 1 ? "" : "s") + " toegestaan"; },
        formatLoadMore: function (pageNumber) { return "Meer resultaten laden…"; },
        formatSearching: function () { return "Zoeken…"; }
    };

    $.extend($.fn.select3.defaults, $.fn.select3.locales['nl']);
})(jQuery);
