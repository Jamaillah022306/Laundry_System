@if ($paginator->hasPages())
    <div style="display:flex; justify-content:center; align-items:center; gap:6px; margin-top: 20px; padding: 10px 0;">

        {{-- Previous Page --}}
        @if ($paginator->onFirstPage())
            <span style="padding:8px 14px; background:#7EB4E8; border-radius:6px; color:rgba(0,0,0,0.4); font-size:13px; font-weight:600; cursor:not-allowed;">
                &laquo; Prev
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
               style="padding:8px 14px; background:#1E5CB3; border-radius:6px; color:white; font-size:13px; font-weight:600; text-decoration:none; transition:background 0.2s;"
               onmouseover="this.style.background='#1a4f9e'" onmouseout="this.style.background='#1E5CB3'">
                &laquo; Prev
            </a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span style="padding:8px 10px; color:#1a1a1a; font-size:13px;">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span style="padding:8px 14px; background:#22C55E; border-radius:6px; color:#1a1a1a; font-size:13px; font-weight:700;">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                           style="padding:8px 14px; background:#7EB4E8; border-radius:6px; color:#1a1a1a; font-size:13px; font-weight:600; text-decoration:none;"
                           onmouseover="this.style.background='#5a9fd4'" onmouseout="this.style.background='#7EB4E8'">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               style="padding:8px 14px; background:#1E5CB3; border-radius:6px; color:white; font-size:13px; font-weight:600; text-decoration:none;"
               onmouseover="this.style.background='#1a4f9e'" onmouseout="this.style.background='#1E5CB3'">
                Next &raquo;
            </a>
        @else
            <span style="padding:8px 14px; background:#7EB4E8; border-radius:6px; color:rgba(0,0,0,0.4); font-size:13px; font-weight:600; cursor:not-allowed;">
                Next &raquo;
            </span>
        @endif

    </div>
@endif
