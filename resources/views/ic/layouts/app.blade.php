<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="//fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <!-- Styles -->
    @include('ic.layouts.partials.css')
    @yield('css')
</head>
<body class="layout-top-nav">
    <div id="app" class="wrapper">
        <div class="content-wrapper">
            <section class="content">
                @yield('content')
            </section>
            <div class="modal fade" id="modal_div" tabindex="-1" role="dialog" 
            aria-labelledby="gridSystemModalLabel">
            </div>
        </div>
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
            <div class="p-3">
                <h5>Title</h5>
                <p>Sidebar content</p>
            </div>
        </aside>
    </div>
    @include('ic.layouts.partials.javascript')
    @yield('footer')
</body>
</html>
