@php
    $dtLang = \App\Helpers\AdminUi::datatablesLanguage();
@endphp
<script>
    window.adminDatatablesDefaults = {
        language: @json($dtLang),
        buttons: {
            excel: {
                text: @json(__('admin.datatables.download_excel'))
            }
        }
    };
    window.adminExcelButtonText = @json(__('admin.datatables.download_excel'));
    window.adminSwal = {
        confirmTitle: @json(__('admin.swal.are_you_sure')),
        confirmText: @json(__('admin.swal.cannot_recover')),
        yesDelete: @json(__('admin.swal.yes_delete')),
        noCancel: @json(__('admin.swal.no_cancel')),
        cancelled: @json(__('admin.swal.cancelled')),
        dataSafe: @json(__('admin.swal.data_safe')),
        success: @json(__('admin.swal.success')),
        warning: @json(__('admin.swal.warning')),
        customerRemoved: @json(__('admin.swal.customer_removed')),
        disableUserTitle: @json(__('admin.swal.disable_user_title')),
        disableUserText: @json(__('admin.swal.disable_user_text')),
        enableUserTitle: @json(__('admin.swal.enable_user_title')),
        enableUserText: @json(__('admin.swal.enable_user_text')),
        enableUserSuccess: @json(__('admin.swal.enable_user_success')),
        disableUserSuccess: @json(__('admin.swal.disable_user_success')),
        userStillEnabled: @json(__('admin.swal.user_still_enabled')),
        userStillDisabled: @json(__('admin.swal.user_still_disabled')),
        yes: @json(__('admin.common.yes')),
        serverError: @json(__('admin.swal.server_error'))
    };
</script>
<script src="{{ asset('assets/js/admin-datatables-init.js') }}"></script>
