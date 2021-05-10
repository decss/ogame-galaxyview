<nav aria-label="Page navigation example">
    <ul class="pagination justify-content-center">
        <li class="page-item {{ $galaxy == 1 ? 'disabled' : '' }}">
            <a class="page-link" href="?g={{ ($galaxy - 1) }}">Prev</a>
        </li>
        @for($i = 1; $i <= $lastGalaxy; $i++)
            <li class="page-item {{ $i == $galaxy ? 'active' : '' }}"><a class="page-link"
                                                                         href="?g={{ $i }}">{{ $i }}</a></li>
        @endfor
        <li class="page-item {{ $galaxy >= $lastGalaxy ? 'disabled' : '' }}">
            <a class="page-link" href="?g={{ ($galaxy + 1) }}">Next</a>
        </li>
    </ul>
</nav>
