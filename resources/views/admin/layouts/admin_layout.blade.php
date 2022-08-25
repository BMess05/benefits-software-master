<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <link rel="shortcut icon" href="{{URL::asset('images/logov1/fcropped-FAVICON-10-04-17-32x32.png')}}" type="image/x-icon">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Profeds') }}</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{URL::asset('css/admin_common.css')}}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css">
    <style>
.text-red {
    color: #e13e3e;
}
i.fa.fa-dot-circle-o.text-red {
    font-size: 10px;
    vertical-align: middle;
}
    </style>
    @yield('style')
</head>

<body>
    <header class="site-header">
        <nav class="navbar navbar-default admin_navbar">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a href="{{url('/')}}"><img alt="Profeds" src="{{asset('images/logov1/profeds-logo-260x100.png')}}" /></a>
                </div>

            </div>
        </nav>
    </header>
    <section class="top-gap">
        @yield('content')
    </section>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="{{ URL::asset('vendor/jsvalidation/js/jsvalidation.min.js') }}"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    @yield('scripts')
    <script>
        setTimeout(function() {
            $('#common_message').fadeOut();
        }, 2000);

        $('button.btns-configurations').on('click', function(e) {
            e.preventDefault();
            history.go(-1);
        })
        $(document).ready(function() {
            $('#sortTable').DataTable({
                "bPaginate": false
            });
        });
        $('a.btns-configurations').on('click', function() {
            $('input[name=next]').val(1);
            $('input.btns-configurations').trigger('click');
        });
        // 1530

        /*******************************
         * ACCORDION WITH TOGGLE ICONS
         *******************************/
        function toggleIcon(e) {
            $(e.target)
                .prev('.panel-heading')
                .find(".more-less")
                .toggleClass('glyphicon-plus glyphicon-minus');
        }
        $('.panel-group').on('hidden.bs.collapse', toggleIcon);
        $('.panel-group').on('shown.bs.collapse', toggleIcon);
    </script>

</body>

</html>
