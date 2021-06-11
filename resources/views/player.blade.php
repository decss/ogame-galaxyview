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
    </div>

@endsection
