$(document).ready(function () {
    setTimeout(function () {
        if ($('#res-config').length && !$.fn.DataTable.isDataTable('#res-config')) {
            $('#res-config').DataTable({ responsive: true });
        }
        if ($('#new-cons').length && !$.fn.DataTable.isDataTable('#new-cons')) {
            var newcs = $('#new-cons').DataTable();
            new $.fn.dataTable.Responsive(newcs);
        }
        if ($('#show-hide-res').length && !$.fn.DataTable.isDataTable('#show-hide-res')) {
            $('#show-hide-res').DataTable({
                responsive: {
                    details: {
                        display: $.fn.dataTable.Responsive.display.childRowImmediate,
                        type: ''
                    }
                }
            });
        }
    }, 350);
});
