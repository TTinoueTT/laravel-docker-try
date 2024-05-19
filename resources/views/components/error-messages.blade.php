<div style="color:red">
    <ul>
        @foreach ($errors->take(2) as $error)
            <li>{{ $error }}</li>
        @endforeach

        @if ($has2MoreErrors())
            <li>...以下略</li>
        @endif
    </ul>
</div>
