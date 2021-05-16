@extends('layouts.ui')

@section('main')
        <table class="table caption-top table-dark table-bordered text-center">
            <thead>
            <tr>
                <td colspan="8">
                    <form class="row g-2 needs-validation" method="post"
                          action="{{ route('galaxy.view', ['gal' => $galaxy, 'sys' => $system]) }}">
                        <div class="col">
                            @if(isset($date))
                                @php
                                @endphp
                                <div class="{{ $date['color'] }}">Relevance: {{ $date['text'] }}</div>
                            @endif
                        </div>
                        <div class="col-sm-3 col-md-2">
                            <div class="input-group input-group-sm">
                                <a href="{{ route('galaxy.view', ['gal' => ($galaxy - 1), 'sys' => $system]) }}"
                                   type="button" class="btn btn-primary">&laquo;</a>
                                <input type="number" name="gal" value="{{ $galaxy }}" class="form-control"
                                       placeholder="Galaxy" aria-label="Galaxy" required>
                                <a href="{{ route('galaxy.view', ['gal' => ($galaxy + 1), 'sys' => $system]) }}"
                                   type="button" class="btn btn-primary">&raquo;</a>
                            </div>
                        </div>
                        <div class="col-sm-2 col-md-1 d-grid gap-2">
                            <button class="btn btn-primary btn-sm" type="submit">Ok</button>
                        </div>
                        <div class="col-sm-3 col-md-2">
                            <div class="input-group input-group-sm">
                                <a href="{{ route('galaxy.view', ['gal' => $galaxy, 'sys' => ($system - 1)]) }}"
                                   type="button" class="btn btn-primary">&laquo;</a>
                                <input type="number" name="sys" value="{{ $system }}" class="form-control"
                                       placeholder="System" aria-label="System" required>
                                <a href="{{ route('galaxy.view', ['gal' => $galaxy, 'sys' => ($system + 1)]) }}"
                                   type="button" class="btn btn-primary">&raquo;</a>
                            </div>
                        </div>
                        <div class="col"></div>
                    </form>
                </td>
            </tr>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Planet</th>
                <th scope="col">Moon</th>
                <th scope="col">Debris</th>
                <th scope="col">Player</th>
                <th scope="col">Rank</th>
                <th scope="col">Alliance</th>
                <th scope="col">Links</th>
            </tr>
            </thead>
            <tbody>
            @for ($i = 1; $i <= 15; $i++)
                @include('inc.galaxy-view-item')
            @endfor
            </tbody>
        </table>

@endsection
