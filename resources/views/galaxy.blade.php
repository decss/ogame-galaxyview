@extends('layouts.ui')

@section('main')
    @include('inc.pager')

    <table class="table caption-top table-dark table-bordered text-center">
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
                        <td>
                            <a class="{{ $cell['cls'] }}" href="{{ route('galaxy.view', ['gal' => $galaxy, 'sys' => $cell['s']]) }}">
                                {{ $galaxy }}:{{ $cell['s'] }}
                            </a></td>
                    @endforeach
                    @for($i = count($row); $i < $len; $i++)<td></td>@endfor
                </tr>
            @endforeach

            </tbody>
        @endif
    </table>

    @include('inc.pager')

    <table class="table caption-top table-dark table-bordered" style="width:auto">
        <tr><th>Data relevance </th></tr>
        <tr><td class="color1">Less than 1 day</td></tr>
        <tr><td class="color2">1 - 4 days</td></tr>
        <tr><td class="color3">4 - 7 days</td></tr>
        <tr><td class="color4">1 - 2 weeks</td></tr>
        <tr><td class="color5">2 - 4 weeks</td></tr>
        <tr><td class="color6">More than month</td></tr>
        <tr><td class="color7">No data</td></tr>
    </table>

@endsection
