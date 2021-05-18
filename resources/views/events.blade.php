@extends('layouts.ui')

@section('main')

    <div class="row">
        <div class="col-12 col-md-6">
            <h4>System changes</h4>
            <form class="row small" action="{{ route('events') }}" method="post">
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
                                <td>{!! $event['player']->name !!}</td>
                                <td><a href="{{ route('galaxy.view', ['gal' => $event['gal'], 'sys' => $event['sys'], 'p' => $event['pos']]) }}">{{ $event['coords'] }}</a></td>
                                <td>
                                    @foreach($event['rows'] as $row)
                                        <div>
                                        @if($row['type'] == 10)
                                            New planet <span class="text-warning">{{ $row['json']['name'] }}</span>
                                        @elseif($row['type'] == 11)
                                            Destroyed planet <span class="text-warning">{{ $row['json']['name'] }}</span>
                                        @elseif($row['type'] == 20)
                                            New moon <span class="text-warning">{{ $row['json']['name'] }}</span> ({{ $row['json']['size'] }} km)
                                        @elseif($row['type'] == 21)
                                            Destroyed moon <span class="text-warning">{{ $row['json']['name'] }}</span> {{ $row['json']['size'] }} km
                                        @elseif($row['type'] == 30)
                                            New debris field <span class="text-warning">{{ $row['json']['field'] }}</span> (me, cry)
                                        @elseif($row['type'] == 31)
                                            Removed debris field <span class="text-warning">{{ $row['json']['field'] }}</span> (me, cry)
                                        @elseif($row['type'] == 32)
                                            Increased debris field <span class="text-warning">{{ $row['json']['field'] }}</span> (me, cry)
                                        @elseif($row['type'] == 33)
                                            Decreased debris field <span class="text-warning">{{ $row['json']['field'] }}</span> (me, cry)
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
            <form class="row small" action="{{ route('events') }}" method="post">
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
                        <label><input name="player[]" class="form-check-input" type="checkbox" value="45" {{ in_array('45', $filters['player']) ? 'checked' : '' }}>Status <span class="color-hp">Honourable</span></label>
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
                                <td>{!! $event['player']->name !!}</td>
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
                                                    Player <u>left</u> alliance <span>{{ $alliance[$row['json']['old']]->name }}</span>
                                                @else
                                                    Player <u>joined</u> alliance <span>{{ $alliance[$row['json']['new']]->name }}</span>
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
