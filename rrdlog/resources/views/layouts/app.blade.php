<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>RRD LOG</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" integrity="sha384-XdYbMnZ/QjLh6iI4ogqCTaIjrFk87ip+ekIjefZch0Y+PvJ8CDYtEs1ipDmPorQ+" crossorigin="anonymous">


    <!-- Styles -->

    <link  href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />

    {{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}
    
    {{-- <style>
        body {
            font-family: 'Itim', cursive;
        }

        .fa-btn {
            margin-right: 6px;
        }
    </style>--}}

    @yield('header')

</head>
<body id="app-layout">
    <div class="container" style="margin-top: 100px;">
        <div class="row">
    

        @yield('content')

        </div>
    </div>

    <!-- JavaScripts -->
    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js" integrity="sha384-I6F5OKECLVtK/BL+8iSLDEHowSAfUo76ZL9+kGAgTRdiByINKJaqTPH/QVNS1VDb" crossorigin="anonymous"></script>-->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

    
    <!-- sweetalert -->
    <script src="{{ asset('js/plugins/sweetalert/sweetalert.min.js')}}"></script>

    <!-- data table -->
    <!--
    <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.colVis.min.js"></script>
    -->

		<link href="{{ asset('css/plugins/datatables/jquery.dataTables.css') }}" rel="stylesheet">
		<link href="{{ asset('js/plugins/datatables/extensions/Buttons/css/buttons.dataTables.css') }}" rel="stylesheet">
		<script src="{{ asset('js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
		<script src="{{ asset('js/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
		<script src="{{ asset('js/plugins/datatables/extensions/Buttons/js/dataTables.buttons.min.js') }}"></script>
		<script src="{{ asset('js/plugins/datatables/jszip.min.js') }}"></script>
		<script src="{{ asset('js/plugins/datatables/pdfmake.min.js') }}"></script>
		<script src="{{ asset('js/plugins/datatables/vfs_fonts.js') }}"></script>
		<script src="{{ asset('js/plugins/datatables/extensions/Buttons/js/buttons.html5.js') }}"></script>
		<script src="{{ asset('js/plugins/datatables/extensions/Buttons/js/buttons.colVis.js') }}"></script>
    





    @yield('footer')
</body>
</html>
