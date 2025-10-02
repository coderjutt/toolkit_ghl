<div class="header  flex flex-col md:flex-row justify-between items-start md:items-center pb-5 mb-6 border-b border-gray-200">
    <div class="flex items-center">
        <button class="mobile-toggle mr-4 text-navy text-xl md:hidden">
            <i class="fas fa-bars"></i>
        </button>
        <h1 class="text-2xl font-bold text-navy">Dashboard Overview</h1>
        <!-- <h1>pakisatn</h1> -->
    </div>

    <div class="header-right flex items-center w-full md:w-auto mt-4 md:mt-0">
        @if ((is_role() == 'company' && session()->has('company_admin')) || (is_role() == 'admin' && session()->has('super_admin')))
        <div class="search-bar flex items-center bg-white rounded-full p-2 shadow-sm w-full md:w-64">
            <a href="{{ route('backtoadmin') }}?{{ is_role() == 'company' ? 'company=1' : 'admin=1' }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-navy hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-full shadow transition-all duration-200">
                <i class="fas fa-arrow-left"></i>
                Back to Super Admin
            </a>
        </div>
        @endif

        <div class="user-actions flex items-center ml-4 relative group">
            <!-- Profile Image -->
            <div class="user-profile">
                <img
                    src="{{ Auth::user()->image ? asset(Auth::user()->image) : asset('images/no-image.png') }}"
                    alt="User Profile"
                    class="w-10 h-10 rounded-full border-2 border-[#001b4c] cursor-pointer"
                />
            </div>

            <!-- Dropdown (hidden by default, shown on hover) -->
            <div
                class="absolute right-0 mt-20 w-48 bg-white rounded-md shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50"
            >
                <ul class="py-2 text-sm text-gray-700">
                    <li><a href="#" class="block px-4 py-2 hover:bg-gray-100">{{ Auth::user()->name ?? '' }}</a></li>
                    <li><a href="{{ route('admin.user.profile') }}" class="block px-4 py-2 hover:bg-gray-100">My Profile</a></li>
                    <li>
                        <a onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="block px-4 py-2 text-red-600 hover:bg-gray-100">Logout</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
