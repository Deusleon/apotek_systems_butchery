<script>
    $(document).ready(function () {

        function notify(message, from, align, type) {
            $.growl({
                message: message,
                url: ''
            }, {
                element: 'body',
                type: type,
                allow_dismiss: true,
                placement: {
                    from: from,
                    align: align
                },
                offset: {
                    x: 30,
                    y: 30
                },
                spacing: 10,
                z_index: 999999,
                delay: 2500,
                timer: 3000,
                url_target: '_blank',
                mouse_over: false,

                icon_type: 'class',
                template: '<div data-growl="container" class="alert" role="alert">' +
                    '<button type="button" class="close" data-growl="dismiss">' +
                    '<span aria-hidden="true">&times;</span>' +
                    '<span class="sr-only">Close</span>' +
                    '</button>' +
                    '<span data-growl="icon"></span>' +
                    '<span data-growl="title"></span>' +
                    '<span data-growl="message"></span>' +
                    '<a href="#!" data-growl="url"></a>' +
                    '</div>'
            });
        }

        // Helper function to show notifications with proper styling
        function showNotification(message, type) {
            notify(message, 'top', 'right', type);
        }
        @if($flash = session("alert-success"))
            notify('{{session("alert-success")}}', 'top', 'right', 'success');
        @endif

        @if($flash = session("success"))
            notify('{{session("success")}}', 'top', 'right', 'success');
        @endif

        @if($flash = session("alert-danger"))
            notify('{{session("alert-danger")}}', 'top', 'right', 'danger');
        @endif

        @if($flash = session("danger"))
            notify('{{session("danger")}}', 'top', 'right', 'danger');
        @endif

        @if($flash = session("alert-warning"))
            notify('{{session("alert-warning")}}', 'top', 'right', 'warning');
        @endif

        @if($flash = session("warning"))
            notify('{{session("warning")}}', 'top', 'right', 'warning');
        @endif

        @if($flash = session("alert-info"))
            notify('{{session("alert-info")}}', 'top', 'right', 'info');
        @endif

        @if($flash = session("info"))
            notify('{{session("info")}}', 'top', 'right', 'info');
        @endif

        @if(isset($alert_success))
            notify($alert_success, 'top', 'right', 'success');
        @endif

            @if($errors->any())
                var delay = 5000;
                @foreach($errors->all() as $error)
                    notify('{{$error}}', 'top', 'right', 'danger');

                    delay = delay + 1000;
                @endforeach
            @endif


});


</script>