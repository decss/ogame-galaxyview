@extends('layouts.ui')

@section('main')
    <ul class="pagination justify-content-center1 mb-1 small" style="font-size: 0.75em;">
        <li class="page-item disabled">
            <a class="page-link" href="#" tabindex="-1">Events period:</a>
        </li>
        <li class="page-item {{ $period == 'today' ? 'active' : '' }}">
            <a class="page-link" href="{{ route('events', ['period' => 'today']) }}">Today</a>
        </li>
        <li class="page-item {{ $period == 'yesterday' ? 'active' : '' }}">
            <a class="page-link" href="{{ route('events', ['period' => 'yesterday']) }}">Yesterday</a>
        </li>
        <li class="page-item {{ $period == '2-days' ? 'active' : '' }}">
            <a class="page-link" href="{{ route('events', ['period' => '2-days']) }}">2 days ago</a>
        </li>
        <li class="page-item {{ $period == '3-days' ? 'active' : '' }}">
            <a class="page-link" href="{{ route('events', ['period' => '3-days']) }}">3 days ago</a>
        </li>
        <li class="page-item {{ $period == 'last-3-days' ? 'active' : '' }}">
            <a class="page-link" href="{{ route('events', ['period' => 'last-3-days']) }}">Last 3 days</a>
        </li>
        <li class="page-item {{ $period == 'last-7-days' ? 'active' : '' }}">
            <a class="page-link" href="{{ route('events', ['period' => 'last-7-days']) }}">Last 7 days</a>
        </li>
    </ul>
    <div class="row">
        <div class="col-12 col-md-6">
            <h4>System changes</h4>
            <form class="row small" action="{{ route('events', ['period' => $period]) }}" method="post">
                <div class="col-4">
                    <div class="form-check">
                        <label><input name="system[]" class="form-check-input" type="checkbox" value="10" {{ in_array('10', $filters['system']) ? 'checked' : '' }}>New planet</label>
                    </div>
                    <div class="form-check">
                        <label><input name="system[]" class="form-check-input" type="checkbox" value="11" {{ in_array('11', $filters['system']) ? 'checked' : '' }}>Destroyed planet</label>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-check">
                        <label><input name="system[]" class="form-check-input" type="checkbox" value="20" {{ in_array('20', $filters['system']) ? 'checked' : '' }}>New moon</label>
                    </div>
                    <div class="form-check">
                        <label><input name="system[]" class="form-check-input" type="checkbox" value="22" {{ in_array('22', $filters['system']) ? 'checked' : '' }}>Destroyed moon</label>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-check">
                        <label><input name="system[]" class="form-check-input" type="checkbox" value="30,32" {{ in_array('30,32', $filters['system']) ? 'checked' : '' }}>New/Inc debris</label>
                    </div>
                    <div class="form-check">
                        <label><input name="system[]" class="form-check-input" type="checkbox" value="31,33" {{ in_array('31,33', $filters['system']) ? 'checked' : '' }}>Removed/Dec debris</label>
                    </div>
                </div>
                <div class="col-12 text-end mb-1">
                    <div class="input-group input-group-sm" style="">
                        <span class="input-group-text" id="inputGroup-sizing-sm">Threshold:</span>
                        <input type="text" name="systemTh" value="{{ ($filters['systemTh'] ? $filters['systemTh'] : '') }}" class="form-control" aria-label="Sizing example input" placeholder="Minimum moon or debris size">
                        <button type="submit" name="filterSystem" class="btn btn-primary btn-sm">Filter events</button>
                    </div>
                </div>
            </form>
            <table class="table caption-top table-dark table-bordered text-center small">
                <tr>
                    <th scope="col">Date</th>
                    <th scope="col">Player</th>
                    <th scope="col">System</th>
                    <th scope="col">Event</th>
                </tr>
                @if(isset($systemEvents))
                    @foreach($systemEvents as $event)
                            <tr>
                                <td>{{ $event['date'] }}</td>
                                <td>
                                    @if(isset($event['player']))
                                        {!! $event['player']->name !!}
                                        <i class="small text-secondary">
                                            ({{ $event['player']->rank }})
                                            <a href="{{ route('player', ['id' => $event['player']->id]) }}">link</a>
                                        </i>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('galaxy.view', ['gal' => $event['gal'], 'sys' => $event['sys'], 'p' => $event['pos']]) }}">{{ $event['coords'] }}</a>
                                    <i class="small"><a href="{{ env('OGV_GAME_URL') }}?page=ingame&component=galaxy&galaxy={{ $event['gal'] }}&system={{ $event['sys'] }}&position={{ $event['pos'] }}" target="_blank" rel="noreferrer">show</a></i>
                                </td>
                                <td>
                                    @foreach($event['rows'] as $row)
                                        <div>
                                        @if($row['type'] == 10)
                                            New planet <span class="text-warning">{{ $row['json']['name'] }}</span>
                                        @elseif($row['type'] == 11)
                                            Destroyed planet <span class="text-warning">{{ $row['json']['name'] }}</span>
                                        @elseif($row['type'] == 20)
                                            New moon <span class="text-warning">{{ $row['json']['name'] }}</span> ({{ number_format($row['json']['size'], 0, '', ' ') }} km)
                                        @elseif($row['type'] == 21)
                                            Destroyed moon <span class="text-warning">{{ $row['json']['name'] }}</span> {{ number_format($row['json']['size'], 0, '', ' ') }} km
                                        @elseif($row['type'] == 30)
                                            New debris field <span class="text-warning">{{ number_format($row['json']['field'], 0, '.', ' ') }}</span>
                                        @elseif($row['type'] == 31)
                                            Removed debris field <span class="text-warning">{{ number_format($row['json']['field'], 0, '.', ' ') }}</span>
                                        @elseif($row['type'] == 32)
                                            Increased debris field <span class="text-warning">{{ number_format($row['json']['field'], 0, '.', ' ') }}</span>
                                        @elseif($row['type'] == 33)
                                            Decreased debris field <span class="text-warning">{{ number_format($row['json']['field'], 0, '.', ' ') }}</span>
                                        @endif
                                        </div>
                                    @endforeach
                                </td>
                            </tr>
                    @endforeach
                @endif
            </table>
        </div>


        <div class="col-12 col-md-6">
            <h4>Player changes</h4>
            <form class="row small" action="{{ route('events', ['period' => $period]) }}" method="post">
                <div class="col-4">
                    <div class="form-check">
                        <label><input name="player[]" class="form-check-input" type="checkbox" value="40" {{ in_array('40', $filters['player']) ? 'checked' : '' }}>Name changed</label>
                    </div>
                    <div class="form-check">
                        <label><input name="player[]" class="form-check-input" type="checkbox" value="60" {{ in_array('60', $filters['player']) ? 'checked' : '' }}>Rank changed</label>
                    </div>
                    <div class="form-check">
                        <label><input name="player[]" class="form-check-input" type="checkbox" value="70" {{ in_array('70', $filters['player']) ? 'checked' : '' }}>Joined/left alliance</label>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-check">
                        <label><input name="player[]" class="form-check-input" type="checkbox" value="41" {{ in_array('41', $filters['player']) ? 'checked' : '' }}>Status <span class="color-o">Outlaw</span></label>
                    </div>
                    <div class="form-check">
                        <label><input name="player[]" class="form-check-input" type="checkbox" value="42" {{ in_array('42', $filters['player']) ? 'checked' : '' }}>Status <span class="color-v">Vacation</span></label>
                    </div>
                    <div class="form-check">
                        <label><input name="player[]" class="form-check-input" type="checkbox" value="43" {{ in_array('43', $filters['player']) ? 'checked' : '' }}>Status <span class="color-b">Banned</span></label>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-check">
                        <label><input name="player[]" class="form-check-input" type="checkbox" value="44" {{ in_array('44', $filters['player']) ? 'checked' : '' }}>Status <span class="color-i">Inactive</span></label>
                    </div>
                    <div class="form-check">
                        <label><input name="playerNovac" class="form-check-input" type="checkbox" value="1" {{ isset($filters['playerNovac']) && $filters['playerNovac'] == true ? 'checked' : '' }}>Ignore <span class="color-v">Vac</span> players</label>
                        <!--
                        <label><input name="player[]" class="form-check-input" type="checkbox" value="45" {{ in_array('45', $filters['player']) ? 'checked' : '' }}>Status <span class="color-hp">Honourable</span></label>
                        -->
                    </div>
                    <button type="submit" name="filterPlayer" class="btn btn-primary btn-sm">Filter events</button>
                </div>
                <div class="col-12 text-end mb-1">
                </div>
            </form>
            <table class="table caption-top table-dark table-bordered text-center small">
                <tr>
                    <th scope="col">Date</th>
                    <th scope="col">Player</th>
                    <th scope="col">Event</th>
                </tr>
                @if(isset($playerEvents))
                    @foreach($playerEvents as $date => $events)
                        @foreach($events as $playerId => $event)
                            <tr>
                                <td>{{ $event['date'] }}</td>
                                <td>
                                    @if(isset($event['player']))
                                        {!! $event['player']->name !!}
                                        <i class="small text-secondary">
                                            ({{ $event['player']->rank }})
                                            <a href="{{ route('player', ['id' => $event['player']->id]) }}">link</a>
                                        </i>
                                    @endif
                                </td>
                                <td>
                                    @foreach($event['rows'] as $row)
                                        <div>
                                            @if($row['type'] >= 40 && $row['type'] < 50 )
                                                Status @if ($row['json']['new'] != 0) changed to @endif

                                                @if ($row['type'] == 41)
                                                    <span class="color-o">Outlaw</span>
                                                @elseif ($row['type'] == 42)
                                                    <span class="color-v">Vacation</span>
                                                @elseif ($row['type'] == 43)
                                                    <span class="color-b">Banned</span>
                                                @elseif ($row['type'] == 44)
                                                    <span class="color-i">Inactive</span>
                                                @elseif ($row['type'] == 45)
                                                    <span class="color-hp">Honourable</span>
                                                @endif
                                                @if ($row['json']['new'] == 0) is terminated @endif

                                            @elseif($row['type'] == 50)
                                                Name changed to <b>{{ $row['json']['new'] }}</b>
                                            @elseif($row['type'] == 60)
                                                @php
                                                $diff = $row['json']['new'] - $row['json']['old'];
                                                @endphp
                                                Rank changed to
                                                @if($diff < 0)<span class="text-success">+{{ abs($diff) }}</span>
                                                @else<span class="text-danger">-{{ abs($diff) }}</span>@endif
                                                <span class="text-muted small">({{ $row['json']['new'] }})</span>

                                            @elseif($row['type'] == 70)
                                                @if(!$row['json']['new'] && $row['json']['old'])
                                                    Player <u>left</u> alliance <span class="text-warning">
                                                        {{ isset($alliances[$row['json']['old']]) ? $alliances[$row['json']['old']]->tag : "-"}}
                                                    </span>
                                                @else
                                                    Player <u>joined</u> alliance <span class="text-warning">
                                                        {{ isset($alliances[$row['json']['new']]) ? $alliances[$row['json']['new']]->tag : "-"}}
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                @endif
            </table>
        </div>
    </div>

@endsection
