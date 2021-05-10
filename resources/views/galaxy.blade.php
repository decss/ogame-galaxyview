@extends('layouts.ui')

@section('main')
    <main>
        <section class="py-5 text-center container">
            @include('galaxyPager')

            <table class="table caption-top table-dark table-bordered">
                {{--<thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">First</th>
                    <th scope="col">Last</th>
                    <th scope="col">Handle</th>
                </tr>
                </thead>--}}
                @if (isset($table))
                    <tbody>
                    @foreach ($table as $row)
                        <tr>
                            @php
                                $len = (!isset($len) || $len < count($row)) ? count($row) : $len
                            @endphp
                            @foreach ($row as $cell)
                                <td><a class="{{ $cell['cls'] }}" href="?g={{ $galaxy }}&s={{ $cell['s'] }}">
                                    {{ $galaxy }}:{{ $cell['s'] }}
                                </a></td>
                            @endforeach
                            @for($i = count($row); $i < $len; $i++)<td></td>@endfor
                        </tr>
                    @endforeach

                    </tbody>
                @endif
            </table>

            @include('galaxyPager')

        </section>
    </main>
@endsection
