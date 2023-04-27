<nav class="navbar navbar-expand navbar-light navbar-bg">
    <a class="sidebar-toggle js-sidebar-toggle">
        <i class="hamburger align-self-center"></i>
    </a>

    <div class="navbar-collapse collapse">
        <ul class="navbar-nav navbar-align">
            <li class="nav-item dropdown align-self-center">
                <div id="clock" class="fw-bold" onload="currentTime()"></div>
            </li>
            <li class=" mx-3 nav-item dropdown align-self-center">
                |
            </li>
            <li class="nav-item dropdown">
                <a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-bs-toggle="dropdown">
                    <i class="align-middle" data-feather="settings"></i>
                </a>

                <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
                    <span class="fw-bold"><i class='bx bxs-user-rectangle' ></i> {{ \Illuminate\Support\Facades\Auth::user()->name }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="{{ URL::to('logout') }}">Log out</a>
                </div>
            </li>
        </ul>
    </div>
</nav>
