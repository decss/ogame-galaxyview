@extends('layouts.ui')

@section('main')

    <div class="row">
        <div class="col-sm-6 col-md-4">
            <h4>Player Info</h4>
            <table class="table table-dark table-bordered">
                <tr>
                    <td>Name:</td>
                    <td>{!! $player->name !!}</td>
                </tr>
                <tr>
                    <td>Rank:</td>
                    <td>{{ $player->rank }}</td>
                </tr>
                <tr>
                    <td>Alliance:</td>
                    <td>
                        @if ($player->alliance)
                            {{ $player->alliance->tag }}&nbsp; <a href="#">{{ $player->alliance->name }}</a>
                        @endif
                    </td>
                </tr>
            </table>

            <h4>Planets</h4>
            <table class="table table-dark table-bordered">
                <tr>
                    <th>Coords</th>
                    <th>Name</th>
                    <th>Moon</th>
                </tr>
                @foreach($items as $item)
                    <tr>
                        <td class="{{ $item->updatedArray()['color'] }}">
                            <a href="{{ route('galaxy.view', ['gal' => $item->gal, 'sys' => $item->sys, 'p' => $item->pos]) }}">
                                {{ $item->gal }}:{{ $item->sys }}:{{ $item->pos }}
                            </a>
                        </td>
                        <td>{{ $item->planet_name }}</td>
                        <td>
                            @if($item->moon_size)
                                {{ $item->moon_name }} [<span class="small">{{ $item->moon_size }} km]</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>

            <h4 class="text-start">Activity</h4>
            // TODO: Add activity chart
        </div>
    </div>

@endsection
