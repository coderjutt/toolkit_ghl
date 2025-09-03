<!-- Navbar -->
@php
    $modules = session('user_modules_' . auth()->id(), []);

    use Illuminate\Support\Facades\Route;
@endphp
<div class="navbar w-full h-16 bg-navy text-white fixed top-0 left-0 z-30">
    <div class="flex items-center justify-between px-5 h-full border-b border-gray-700">
        @php
            $logo = App\Models\Setting::where('key', 'logo')->first();
            $currentUrl = request()->url();
        @endphp
        <a href="/" class="flex items-center">
            @if ($logo)
                <img width="40" src="{{ asset('logo/' . $logo->value) }}" alt="Company Logo">
            @else
                <p>No logo uploaded.</p>
            @endif
        </a>

        <!-- Mobile Toggle Button -->
        <button class="mobile-toggle text-white text-2xl md:hidden" onclick="toggleMobileMenu()">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Navigation Menu -->
        <ul id="nav-links"
            class="hidden md:flex flex-col md:flex-row items-start md:items-center absolute top-16 left-0 w-full bg-navy md:static md:bg-transparent md:w-auto md:space-x-4 z-50">

            @if (is_role() == 'super_admin' || is_role() == 'admin')
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center p-2 w-full md:w-auto rounded
                           {{ $currentUrl == route('admin.dashboard') ? 'bg-white text-[#001b4c] font-semibold' : 'text-white hover:bg-blue-900 md:hover:bg-transparent' }}">
                        <i class="fas fa-home text-xl w-6 text-center
                            {{ $currentUrl == route('admin.dashboard') ? 'text-[#001b4c]' : 'text-white' }}"></i>
                        <span class="ml-2">Dashboard</span>
                    </a>
                </li>
            @endif

            @if (is_role() == 'super_admin')
                    <li>
                        <a href="{{ route('admin.subaccount.index') }}"
                            class="flex items-center p-2 w-full md:w-auto rounded
                               {{ $currentUrl == route('admin.subaccount.index') ? 'bg-white text-[#001b4c] font-semibold' : 'text-white hover:bg-blue-900 md:hover:bg-transparent' }}">
                            <i class="fa-solid fa-user text-xl w-6 text-center
                                {{ $currentUrl == route('admin.subaccount.index') ? 'text-[#001b4c]' : 'text-white' }}"></i>
                            <span class="ml-2">Sub Account</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.permissions.index') }}"
                            class="flex items-center p-2 w-full md:w-auto rounded
                   {{ $currentUrl == route('admin.permissions.index') ? 'bg-white text-[#001b4c] font-semibold' : 'text-white hover:bg-blue-900 md:hover:bg-transparent' }}">
                            <i class="fas fa-lock text-xl w-6 text-center
                    {{ $currentUrl == route('admin.permissions.index') ? 'text-[#001b4c]' : 'text-white' }}"></i>
                            <span class="ml-2">Permissions</span>
                        </a>
                    </li>
            @endif

            @if (is_role() == 'admin')
                @foreach ($modules as $module)
                    @php
                        $routeName = 'admin.' . strtolower($module) . '.index';

                    @endphp
                    
                    @if (Route::has($routeName))
                        <li>
                            <a href="{{ route($routeName) }}"
                                class="flex items-center p-2 w-full md:w-auto rounded
                                    {{ request()->routeIs($routeName) ? 'bg-white text-[#001b4c] font-semibold' : 'text-white hover:bg-blue-900 md:hover:bg-transparent' }}">

                                <i class="fas fa-dot-circle text-xl w-6 text-center
                                    {{ request()->routeIs($routeName) ? 'text-[#001b4c]' : 'text-white' }}"></i>

                                <span class="ml-2">{{ ucfirst($module) }}</span>
                            </a>
                        </li>
                    @endif
                @endforeach
            @endif


            @if (is_role() == 'super_admin' || is_role() == 'admin')
                <li>
                    <a href="{{ route('admin.setting.index') }}"
                        class="flex items-center p-2 w-full md:w-auto rounded
                           {{ $currentUrl == route('admin.setting.index') ? 'bg-white text-[#001b4c] font-semibold' : 'text-white hover:bg-blue-900 md:hover:bg-transparent' }}">
                        <i class="fas fa-cog text-xl w-6 text-center
                            {{ $currentUrl == route('admin.setting.index') ? 'text-[#001b4c]' : 'text-white' }}"></i>
                        <span class="ml-2">Setting</span>
                    </a>
                </li>
            @endif

            <li>
                <a onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="flex items-center p-2 w-full md:w-auto rounded hover:bg-blue-900 md:hover:bg-transparent text-white">
                    <i class="fas fa-sign-out-alt text-xl w-6 text-center"></i>
                    <span class="ml-2">Logout</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
            </li>
        </ul>
    </div>
</div>