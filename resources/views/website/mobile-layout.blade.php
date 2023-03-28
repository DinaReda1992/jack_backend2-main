<div class="sidenav canvas-sidebar bg-white">
    <div class="canvas-overlay">
    </div>
    <div class="pt-5 pb-7 card border-0 h-100">
        <span class="canvas-close d-inline-block text-right fs-24 ml-auto lh-1 text-primary"><i
                class="fal fa-times"></i></span>
        <div class="d-flex align-items-center card-header border-0 py-0 pl-8 pr-7 mb-9 bg-transparent">
            <a href="index.html" class="d-block w-179px">
                <img src="/asset/images/logogogogogo.png" alt="Jak">
            </a>

        </div>
        <div class="overflow-y-auto pb-6 pl-8 pr-7 card-body pt-0">
            <ul class="navbar-nav main-menu px-0 ">
                <li aria-haspopup="true" aria-expanded="false" class="nav-item dropdown py-1 px-0">
                    <a class="nav-link p-0" href="/">
                        {{__('dashboard.home')}}
                    </a>
                </li>
                <li aria-haspopup="true" aria-expanded="false" class="nav-item dropdown py-1 px-0">
                    <a class="nav-link p-0" href="/search">
                        {{__('dashboard.shopping')}}
                    </a>
                </li>
                <li aria-haspopup="true" aria-expanded="false" class="nav-item dropdown py-1 px-0">
                    <a class="nav-link p-0" href="/page/about-us">
                        {{ __('trans.About Us') }}
                    </a>
                </li>
                <li aria-haspopup="true" aria-expanded="false"class="nav-item dropdown py-1 px-0">
                    <a class="nav-link p-0" href="/contact-us">
                        {{ __('dashboard.contact_us') }}
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-footer bg-transparent border-0 mt-auto pl-8 pr-7 pb-0 pt-4">
            <ul class="list-inline d-flex align-items-center mb-3">
                @if ($twitter)
                    <li class="list-inline-item mr-4">
                        <a class="fs-20 lh-1" href="{{ $twitter }}"><i class="fab fa-twitter"></i></a>
                    </li>
                @endif
                @if ($facebook)
                    <li class="list-inline-item mr-4">
                        <a class="fs-20 lh-1" href="{{ $facebook }}"><i class="fab fa-facebook-f"></i></a>
                    </li>
                @endif
                @if ($instagram)
                    <li class="list-inline-item mr-4">
                        <a class="fs-20 lh-1" href="{{ $instagram }}"><i class="fab fa-instagram"></i></a>
                    </li>
                @endif
                @if ($youtube)
                    <li class="list-inline-item mr-4">
                        <a class="fs-20 lh-1" href="{{ $youtube }}"><i class="fab fa-youtube"></i></a>
                    </li>
                @endif
            </ul>
            <p class="mb-0 text-gray">
                © {{ date('Y') }} Jak<br>
                جميع الحقوق محفوظة.
            </p>
        </div>
    </div>
</div>
