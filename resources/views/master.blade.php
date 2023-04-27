<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    @include('includes.css')
    @yield('custom-css')
</head>
<body>
    <div class="wrapper">
        @if(Route::current()->uri != 'login' )
            @include('includes.sidebar')
        @endif
        <div class="main">
            {{-- navbar --}}
            @if(Route::current()->uri != 'login' )
                @include('includes.navbar')
            @endif

            {{-- main content --}}
            <main class="content">
                @yield('content')
            </main>

            {{-- footer --}}
            <footer>
                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row text-muted">
                            <div class="d-flex justify-content-end">
                                <p class="mb-0">
                                    <a class="text-muted d-inline-flex">V.1.0 &copy; 2023 Tokyo Belle</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </footer>
            </footer>
        </div>
    </div>
    @include('includes.js')
    @yield('custom-js')
    <script>
        function currentTime() {
            let date = new Date();
            let hh = date.getHours();
            let mm = date.getMinutes();
            let ss = date.getSeconds();
            let dd = date.getDate();
            let MM = date.getMonth();
            let yyyy = date.getFullYear();

            let month = ['Jan', "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agus", "Sep", "Okt", "Nov", "Des"]

            hh = (hh < 10) ? "0" + hh : hh;
            mm = (mm < 10) ? "0" + mm : mm;
            ss = (ss < 10) ? "0" + ss : ss;

            let time = hh + ":" + mm + ":" + ss + "" + " " + dd + "/" + month[MM] + "/" + yyyy;

            document.getElementById("clock").innerText = time;
            let t = setTimeout(function(){ currentTime() }, 1000);

        }

        currentTime();
    </script>
</body>
</html>
