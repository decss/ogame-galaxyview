<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GalaxyView</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">
    <style>
        body {
            background: #212529;
            background: #343a40;
            background: #2f2f2f;
            color: #fff;
        }

        .table > :not(caption) > * > * {
            padding: .3rem .4rem;
        }

        .smaller {
            font-size:.7em;
        }

        .color1, .color1 a {color: #a0faa0;}
        .color2, .color2 a {color: #70c070;}
        .color3, .color3 a {color: #327832;}
        .color4, .color4 a {color: yellow;}
        .color5, .color5 a {color: #FF9900;}
        .color6, .color6 a {color: #F00;}
        .color7, .color7 a {color: #333;}

        .color-a {color: #f48406 !important;}
        .color-o {color: #f3f !important;}
        .color-v {color: aqua !important;}
        .color-b {color: #fff !important; text-decoration: line-through;}
        .color-i {color: #6e6e6e !important;}
        .color-ii {color: #4f4f4f !important;}
        .color-hp {color: #ff6 !important;}
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
                            <a class="nav-link {{ request()->url() == route('main') || request()->is('*/players') ? 'active' : '' }}"
                               href="{{ route('main') }}">Main</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('*/galaxy*') ? 'active' : '' }}"
                               href="{{ route('galaxy', ['gal' => isset($galaxy) ? $galaxy : '']) }}">Galaxy view</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->url() == route('events') ? 'active' : '' }}"
                               href="{{ route('events') }}">Events</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('*/adm*') ? 'active' : '' }}"
                               href="{{ route('adm') }}">Administrative</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
@endsection
@yield('header')


@section('main')
    Main section
@endsection
<main class="py-4 container-fluid">
    @yield('main')
</main>

@yield('footer')


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8"
        crossorigin="anonymous"></script>


</body>
</html>
