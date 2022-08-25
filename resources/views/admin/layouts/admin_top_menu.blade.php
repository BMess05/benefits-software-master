<nav class="navbar navbar-default admin_top_menu">
    <div class="container-fluid">
        <ul class="nav navbar-nav">
            @guest
            <li class="nav-item">
                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
            </li>
            @if (Route::has('register'))
            <li class="nav-item">
                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
            </li>
            @endif
            @else
            <li><a href="{{url('/dashboard/cases')}}">Cases</a></li>
            <li><a href="{{url('/dashboard/advisors')}}">Advisors</a></li>
            <li><a href="{{url('/dashboard/disclaimers')}}">Disclaimers</a></li>
            <li><a href="{{url('/dashboard/configurations')}}">Configuration</a></li>
            @if(Auth::user()->role == 0)
            <li><a href="{{ route('listStandardUsers') }}">Users</a></li>
            @endif
            <li>
                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    {{ __('Logout') }}
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
            @endguest
        </ul>
    </div>
</nav>
