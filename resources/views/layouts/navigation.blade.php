<nav class="nav w-full z-50">
  <div class="logo w-2/12 flex justify-end items-center p-4">
    <img src="{{ URL('images/flg_logo11079.png')}}" alt="Logo Image">
  </div>
  <div class="hamburger p-4 cursor-pointer">
    <div class="line1 bg-white h-1 w-6 mb-1"></div>
    <div class="line2 bg-white h-1 w-6 mb-1"></div>
    <div class="line3 bg-white h-1 w-6"></div>
  </div>
  <ul class="nav-links w-2/6 flex justify-between items-center p-4 mx-auto">
    <li><a class="text-white font-semibold hover:text-[#E0AA87]" href="{{route('home')}}">HOME</a></li>
    <li><a class="text-white font-semibold hover:text-[#E0AA87]" href="{{route('carlist')}}">VEHICLE LIST</a></li>
    <li><a class="text-white font-semibold hover:text-[#E0AA87]" href="{{route('about')}}">ABOUT US</a></li>
    <li><a class="text-white font-semibold hover:text-[#E0AA87]" href="{{route('contact')}}">CONTACT US</a></li>
    @if(auth()->check())
    <li><a class="text-white font-semibold hover:text-[#E0AA87]" href="{{ route('dashboard')}}">DASHBOARD</a></li>
    @endif

    @if(auth()->guest())
    <li><a class="text-white font-semibold login-button" href="{{ route('login') }}">Login</a></li>
    @endif

    @if(auth()->check())
    <li><a class="text-white font-semibold hover:text-[#E0AA87]" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">LogOut</a></li>
    @endif
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
      @csrf
    </form>
  </ul>
</nav>