<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GalaxyView</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">
    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }
	    .color1, .color1 a {color:#A7F9A7;}
		.color2, .color2 a {color:#70C070;}
		.color3, .color3 a {color:#409040;}
		.color4, .color4 a {color:yellow;}
		.color5, .color5 a {color:#FF9900;}
		.color6, .color6 a {color:red;}
		.color7, .color7 a {color:#666;}
        main {
            /*background-color: #2f2f2f;*/
        }
    </style>
</head>
<body>

@section('header')
    <header>
        <nav class="navbar navbar-expand-sm navbar-dark bg-dark" aria-label="Third navbar example">
            <div class="container-xl">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbar" aria-controls="navbar" aria-expanded="false"
                        aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbar">
                    <ul class="navbar-nav me-auto mb-2 mb-sm-0">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->url() == route('main') ? 'active' : '' }}" href="{{ route('main') }}">Main</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->url() == route('galaxy') ? 'active' : '' }}" href="{{ route('galaxy') }}">Galaxy view</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->url() == route('changelog') ? 'active' : '' }}" href="{{ route('changelog') }}">Changelog</a>
                        </li>
                    </ul>
                    {{--<form>
                        <input class="form-control" type="text" placeholder="Search" aria-label="Search">
                    </form>--}}
                </div>
            </div>
        </nav>
    </header>
@endsection
@yield('header')


@section('main')
    Main section
@endsection
@yield('main')


@section('main')
    <footer class="text-muted py-5">
        <div class="container">
            <p class="float-end mb-1">
                <a href="#">Back to top</a>
            </p>
            <p class="mb-1">Album example is &copy; Bootstrap, but please download and customize it for yourself!</p>
            <p class="mb-0">New to Bootstrap? <a href="/">Visit the homepage</a> or read our <a
                    href="/docs/5.0/getting-started/introduction/">getting started guide</a>.</p>
        </div>
    </footer>
@endsection
@yield('footer')


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8"
        crossorigin="anonymous"></script>


</body>
</html>
