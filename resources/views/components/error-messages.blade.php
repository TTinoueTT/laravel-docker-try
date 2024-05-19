<div style="color:red">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            {{-- blade-formatter-disable --}}
            @if ($loop->iteration >= 2)
                @break
            @endif
            {{-- blade-formatter-enable --}}
        @endforeach

        @if ($has2MoreErrors())
            ...以下略
        @endif
    </ul>
</div>
