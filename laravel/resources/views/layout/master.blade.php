<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cloud Project</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
    @include('layout.nav')
    <section class="section">
        <div class="container">
            @yield('body')
        </div>
    </section>
    <script>var xrf_token = "{{ csrf_token() }}";</script>
    <script src="/js/app.js"></script>
</html>
