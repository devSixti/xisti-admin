(function (window, $) {
    'use strict';

    function applyAdminDatatableDefaults() {
        if (!window.adminDatatablesDefaults || !$.fn || !$.fn.dataTable) {
            return false;
        }

        $.extend(true, $.fn.dataTable.defaults, window.adminDatatablesDefaults);

        if ($.fn.dataTable.ext && $.fn.dataTable.ext.buttons && $.fn.dataTable.ext.buttons.excelHtml5) {
            var excelDefaults = window.adminDatatablesDefaults.buttons && window.adminDatatablesDefaults.buttons.excel;
            if (excelDefaults && excelDefaults.text) {
                $.fn.dataTable.ext.buttons.excelHtml5.text = excelDefaults.text;
            }
        }

        return true;
    }

    window.adminDataTableOptions = function (options) {
        return $.extend(true, {}, window.adminDatatablesDefaults || {}, options || {});
    };

    $(function () {
        if (!applyAdminDatatableDefaults()) {
            var attempts = 0;
            var timer = setInterval(function () {
                attempts++;
                if (applyAdminDatatableDefaults() || attempts > 40) {
                    clearInterval(timer);
                }
            }, 100);
        }
    });
})(window, window.jQuery);
