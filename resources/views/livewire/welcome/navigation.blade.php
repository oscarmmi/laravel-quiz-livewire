<nav class="flex items-center gap-3">
    @auth
        <a
            href="{{ url('/dashboard') }}"
            class="bg-primary text-on-primary px-6 py-2 rounded-full font-bold tactile-button-primary uppercase tracking-widest text-xs transition-all active:scale-95"
        >
            Dashboard
        </a>
    @else
        <a
            href="{{ route('login') }}"
            class="text-primary font-bold text-sm uppercase tracking-widest hover:bg-surface-container-low transition-colors px-4 py-2 rounded-full"
        >
            Log in
        </a>

        @if (Route::has('register'))
            <a
                href="{{ route('register') }}"
                class="bg-primary text-on-primary px-6 py-2 rounded-full font-bold tactile-button-primary uppercase tracking-widest text-xs transition-all active:scale-95"
            >
                Register
            </a>
        @endif
    @endauth
</nav>
