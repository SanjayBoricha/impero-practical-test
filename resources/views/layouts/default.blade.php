<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

    @stack('css')
</head>
<body>
    <nav class="navbar navbar-light bg-light">
        <div class="container">
            <span class="navbar-brand mb-0 h1">Navbar</span>
        </div>
    </nav>

    @yield('content')

    <script src="{{ mix('js/app.js') }}"></script>

    @stack('js')
</body>
</html>
