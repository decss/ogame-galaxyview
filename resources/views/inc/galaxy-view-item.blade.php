<tr @if(request('p') == $i) class="table-active" @endif>
    <td>{{ $i }}</td>
    @if (isset($items[$i]))
        <td>{{ $items[$i]->planet_name }}</td>
        <td>
            @if ($items[$i]->moon_size)
                {{ $items[$i]->moon_name }} [<span class="small">{{ $items[$i]->moon_size }}' km]</span>
            @endif
        </td>
        <td>{{ $items[$i]->debrisField() }}</td>
        <td>{!! $items[$i]->player->name !!}</td>
        <td>{{ $items[$i]->player->rank }}</td>
        <td>
            @if($items[$i]->player->alliance)
                {{ $items[$i]->player->alliance->tag }}
            @endif
        </td>
        <td>
            <a href="{{ route('player', ['id' => $items[$i]->player->id]) }}">Player</a>
        </td>
    @else
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    @endif
</tr>

