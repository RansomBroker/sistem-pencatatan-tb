<nav id="sidebar" class="sidebar js-sidebar collapsed">
    <div class="sidebar-content js-simplebar ps-2">
        <a class="sidebar-brand" href="{{ URL::to('') }}">
            <span class="align-middle">Advance Receive System</span>
        </a>

        <ul class="sidebar-nav">

            <li class="sidebar-item  {{ Route::current()->uri == '/' ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ URL::to('') }}">
                    <i class="align-middle" data-feather="sliders"></i> <span class="align-middle">Dashboard</span>
                </a>
            </li>

            <li class="sidebar-header">
                MASTER DATA
            </li>

            <li class="sidebar-item {{ Route::current()->uri == 'branch' ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ URL::to('branch') }}">
                    <i class='bx bx-git-branch'></i>Branch
                </a>
            </li>

            <li class="sidebar-item {{ Route::current()->uri == 'customer' ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ URL::to('customer') }}">
                    <i class='bx bxs-group' ></i>Customer
                </a>
            </li>

            <li class="sidebar-item {{ Route::current()->uri == 'category' ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ URL::to('category') }}">
                    <i class='bx bxs-category'></i>Category
                </a>
            </li>

            <li class="sidebar-item {{ Route::current()->uri == 'product' ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ URL::to('product') }}">
                    <i class='bx bxl-product-hunt' ></i>Product
                </a>
            </li>

            <li class="sidebar-header">
                REPORT
            </li>

            <li class="sidebar-item {{ Route::current()->uri == 'advance-receive' ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ URL::to('advance-receive') }}">
                    <i class='bx bxs-cart-alt'></i>Advance Receive
                </a>
            </li>

            <li class="sidebar-item {{ Route::current()->uri == 'consumption' ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ URL::to('consumption') }}">
                    <i class='bx bxs-pie-chart'></i>Consumption
                </a>
            </li>

            <li class="sidebar-item {{ Route::current()->uri == 'expired' ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ URL::to('expired') }}">
                    <i class='bx bxs-calendar'></i>Expired
                </a>
            </li>

            <li class="sidebar-item {{ Route::current()->uri == 'refund' ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ URL::to('refund') }}">
                    <i class='bx bx-money' ></i>Refund
                </a>
            </li>

            <li class="sidebar-item {{ Route::current()->uri == 'outstanding' ? 'active' : '' }} @if(\Illuminate\Support\Facades\Auth::user()->role == 1) mb-5 @endif">
                <a class="sidebar-link" href="{{ URL::to('outstanding') }}">
                    <i class='bx bx-notepad'></i>Outstanding
                </a>
            </li>

            @if(\Illuminate\Support\Facades\Auth::user()->role == 0)
                <li class="sidebar-header">
                    OTHERS
                </li>

                <li class="sidebar-item {{ Route::current()->uri == 'setting' ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ URL::to('setting') }}">
                        <i class='bx bxs-wrench'></i>Setting
                    </a>
                </li>

                <li class="sidebar-item {{ Route::current()->uri == 'user' ? 'active' : '' }} mb-5">
                    <a class="sidebar-link" href="{{ URL::to('user') }}">
                        <i class='bx bxs-user' ></i>Users
                    </a>
                </li>
            @endif
        </ul>
    </div>
</nav>
