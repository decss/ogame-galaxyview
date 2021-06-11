@extends('layouts.ui')

@section('main')

    <div class="row">
        <div class="col-12 col-md-6 col-lg-4">
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

                @if(isset($player->items))
                    @foreach($player->items as $item)
                        <tr>
                            <td>
                                <a class="{{ $item->updatedArray()['color'] }}" href="{{ route('galaxy.view', ['gal' => $item->gal, 'sys' => $item->sys, 'p' => $item->pos]) }}">{{ $item->gal }}:{{ $item->sys }}:{{ $item->pos }}</a>
                                <i class="small"><a href="{{ env('OGV_GAME_URL') }}?page=ingame&component=galaxy&galaxy={{ $item->gal }}&system={{ $item->sys }}&position={{ $item->pos }}" target="_blank" rel="noreferrer">show</a></i>
                            </td>
                            <td>{{ $item->planet_name }}</td>
                            <td>
                                @if($item->moon_size)
                                    {{ $item->moon_name }} [<span class="small">{{ $item->moon_size }} km]</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
            </table>
        </div>


        <div class="col-12 col-md-6 col-lg-8">
            <h4 class="text-start">Activity</h4>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <div>
                <canvas id="activityChart" style="max-height: auto;"></canvas>
                <div class="pt-2 text-warning small"><i>Chart timezone is <b>UTC+3</b>. Server time: <b>{!! date('H:i:s') !!}</b></i></div>
            </div>
            <script>
                const data = {
                    labels: [{!! $activity['labels'] !!}],
                    datasets: [
                        {
                            label: 'Esp.action',
                            data: {!! json_encode($activity['data'][3]) !!},
                            backgroundColor: '#ff9600',
                        },
                        {
                            label: 'Planet',
                            data: {!! json_encode($activity['data'][1]) !!},
                            backgroundColor: '#495057',
                        },
                        {
                            label: 'Moon',
                            data: {!! json_encode($activity['data'][2]) !!},
                            // backgroundColor: '#adb5bd',
                            backgroundColor: '#6c757d',
                        }
                    ]
                };
                const config = {
                    type: 'bar',
                    data: data,
                    options: {
                        plugins: {
                            tooltip: {
                                enabled: false
                            }
                        },
                        responsive: true,
                        scales: {
                            x: {
                                stacked: true,
                            },
                            y: {
                                stacked: true,
                                grace: '5%',
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                };

                var activityChart = new Chart(
                    document.getElementById('activityChart'),
                    config
                );
            </script>
        </div>

        <div class="col">
            <h4>Espionage reports history</h4>
            <table class="table table-dark table-bordered small">
                <tr>
                    <th>Coords</th>
                    <th>Res</th>
                    <th>Fleet</th>
                    <th>Def</th>
                    <th>Api</th>
                    <th>Date, <span class="fw-light">d.m h:m</span></th>
                    <th>Links</th>
                </tr>
                @if(isset($player->items))
                    @foreach($player->items as $item)
                        @php
                        if (isset($drawContent) && isset($tmp) && $tmp != $item->coords) {
                            echo '<tr><td colspan="7" style="background: #2f2f2f;"></td></tr>';
                            unset($drawContent);
                        }
                        $tmp = $item->coords;
                        @endphp

                        @foreach($item->api as $api)
                            @php $drawContent = true; @endphp
                            <tr>
                                <td>
                                    @if($api->type == 2) Moon on @endif
                                    <span class="{{ $item->updatedArray()['color'] }}">{{ $item->coords }}</span>
                                </td>

                                @if(strlen($api->res))
                                    <td class="@if(stristr($api->res, 'Mn')) color5 @endif">{{ $api->res }}</td>
                                @else
                                    <td class="color7">no data</td>
                                @endif

                                @if(strlen($api->fleet))
                                    <td class="@if(stristr($api->fleet, 'Mn')) color5 @endif">{{ $api->fleet }}</td>
                                @else
                                    <td class="color7">no data</td>
                                @endif

                                @if(strlen($api->def))
                                    <td class="@if(stristr($api->def, 'Mn')) color5 @endif">{{ $api->def }}</td>
                                @else
                                    <td class="color7">no data</td>
                                @endif

                                <td class="small">{{ $api->api }}</td>
                                <td>{{ \Carbon\Carbon::parse($api->date)->format('d.m H:i') }}</td>
                                <td><a href="https://trashsim.universeview.be/?SR_KEY={{ $api->api }}" target="_blank" rel="noreferrer">TrashSim</a></td>
                            </tr>
                        @endforeach
                    @endforeach
                @endif
            </table>
        </div>
    </div>

@endsection
