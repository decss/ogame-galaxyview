@extends('layouts.ui')

@section('main')
    <h4>Search player</h4>
    <form class="mb-4" method="post" action="{{ route('main.players') }}">
        <div class="row mb-2">
            <div class="col-sm-4 col-md-3">
                <input type="text" name="name" value="{{ request()->get('name') }}" class="form-control"
                       aria-describedby="name" placeholder="Player name">
                <div id="name" class="form-text">Part of full player name</div>
            </div>
            <div class="col-sm-4 col-md-3">
                <div class="row">
                    <div class="col-6 pe-1">
                        <input type="text" name="rankMin" value="{{ request()->get('rankMin') }}" class="form-control"
                               aria-describedby="rank" placeholder="Min rank">
                    </div>
                    <div class="col-6 ps-1">
                        <input type="text" name="rankMax" value="{{ request()->get('rankMax') }}" class="form-control"
                               aria-describedby="rank" placeholder="Max rank">
                    </div>
                    <div id="rank" class="form-text">Player rank range</div>
                </div>
            </div>
            {{--
            <div class="col-sm-4 col-md-3">
                <input type="text" name="moonMin" value="{{ request()->get('moonMin') }}" class="form-control"
                       aria-describedby="moon" placeholder="Moon size">
                <div id="moon" class="form-text">Minimum moon size</div>
            </div>
            --}}
        </div>

        <div class="mb-2">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="status[o]" id="stauts-o" value="1"
                    {{ isset(request()->get('status')['o']) ? 'checked' : '' }}>
                <label class="form-check-label color-o" for="stauts-o">Outlaw</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="status[v]" id="stauts-v" value="1"
                    {{ isset(request()->get('status')['v']) ? 'checked' : '' }}>
                <label class="form-check-label color-v" for="stauts-v">Vacation</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="status[i]" id="stauts-i" value="1"
                    {{ isset(request()->get('status')['i']) ? 'checked' : '' }}>
                <label class="form-check-label color-i" for="stauts-i">Inactive (i)</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="status[ii]" id="stauts-ii" value="1"
                    {{ isset(request()->get('status')['ii']) ? 'checked' : '' }}>
                <label class="form-check-label color-ii" for="stauts-ii">Inactive (I)</label>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <table class="table caption-top table-dark table-bordered text-center">
        <tr>
            <th scope="col">#</th>
            <th scope="col">Player</th>
            <th scope="col">Rank</th>
            <th scope="col">Alliance</th>
            <th scope="col">Planets</th>
            <th scope="col">Activity</th>
            <th scope="col">Links</th>
        </tr>
        @if(isset($players))
            @foreach($players as $i => $player)
                <tr>
                    <td>{{ ($i + 1) }}</td>
                    <td>{!! $player->name !!}</td>
                    <td>{{ $player->rank }}</td>
                    <td>
                        @if($player->alliance)
                            <a href="{{ route('alliance', ['id' => $player->alliance->id]) }}">{{ $player->alliance->tag }}</a>
                        @endif
                    </td>
                    <td>//TODO:</td>
                    <td>//TODO:</td>
                    <td>
                        <a href="{{ route('player', ['id' => $player->id]) }}">Player</a>
                    </td>
                </tr>
            @endforeach
        @endif
    </table>

    {{--
    <h4>Search alliance</h4>
    <form class="row_DEL mb-4" method="post" action="">
        // TODO:
    </form>

    <h4>Search debris</h4>
    <form class="row_DEL mb-4" method="post" action="">
        // TODO:
    </form>
    --}}

@endsection
