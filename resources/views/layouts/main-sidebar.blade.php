<!-- =======================
  Veterinary Platform Sidebar
======================= -->

<div class="app-sidebar__overlay" data-toggle="sidebar"></div>

<aside class="app-sidebar sidebar-scroll">


    <!-- Logo -->
    <div class="main-sidebar-header active">

        <a class="desktop-logo logo-light active" href="{{ url('/') }}">
            <img src="{{ asset('assets/img/brand/vetpedia-logo.png') }}" alt="VetPedia" style="height:50px;width:auto;">
        </a>

        <a class="desktop-logo logo-dark active" href="{{ url('/') }}">
            <img src="{{ asset('assets/img/brand/vetpedia-logo-white.png') }}" alt="VetPedia"
                style="height:50px;width:auto;">
        </a>

        <a class="logo-icon mobile-logo icon-light active" href="{{ url('/') }}">
            <img src="{{ URL::asset('assets/img/brand/favicon.png') }}" class="logo-icon" alt="logo">
        </a>

        <a class="logo-icon mobile-logo icon-dark active" href="{{ url('/') }}">
            <img src="{{ URL::asset('assets/img/brand/favicon-white.png') }}" class="logo-icon dark-theme"
                alt="logo">
        </a>

    </div>

    <div class="main-sidemenu">

        <div class="text-center py-4">

            <h5 class="mb-1">
                VetPedia
            </h5>

            <small class="text-muted">
                Veterinary Knowledge Platform
            </small>

        </div>





        <ul class="side-menu">

            {{-- Dashboard --}}
            <li class="side-item side-item-category">
                Dashboard
            </li>

            <li class="slide">
                <a class="side-menu__item" href="{{ route('home') }}">
                    <i class="fe fe-home"></i>
                    <span class="side-menu__label">
                        Home
                    </span>
                </a>
            </li>

            <li class="slide">
                <a class="side-menu__item" href="{{ route('search') }}">
                    <i class="fe fe-search"></i>
                    <span class="side-menu__label">
                        Global Search
                    </span>
                </a>
            </li>


            {{-- Knowledge Base --}}
            <li class="side-item side-item-category">
                Knowledge Base
            </li>

            <li class="slide">
                <a class="side-menu__item" href="{{ route('products.index') }}">
                    <i class="fe fe-package"></i>

                    <span class="side-menu__label">
                        Products
                    </span>
                </a>
            </li>

            <li class="slide">
                <a class="side-menu__item" href="{{ route('active-ingredients.index') }}">
                    <i class="fe fe-droplet"></i>

                    <span class="side-menu__label">
                        Active Ingredients
                    </span>
                </a>
            </li>

            <li class="slide">
                <a class="side-menu__item" href="{{ route('diseases.index') }}">
                    <i class="fe fe-activity"></i>

                    <span class="side-menu__label">
                        Diseases
                    </span>
                </a>
            </li>

            <li class="slide">
                <a class="side-menu__item" href="{{ route('companies.index') }}">
                    <i class="fe fe-briefcase"></i>

                    <span class="side-menu__label">
                        Companies
                    </span>
                </a>
            </li>


            {{-- Platform --}}
            <li class="side-item side-item-category">
                Platform
            </li>

            <li class="slide">
                <a class="side-menu__item" href="{{ route('about') }}">
                    <i class="fe fe-info"></i>

                    <span class="side-menu__label">
                        About
                    </span>
                </a>
            </li>

            <li class="slide">
                <a class="side-menu__item" href="{{ route('contact') }}">
                    <i class="fe fe-mail"></i>

                    <span class="side-menu__label">
                        Contact
                    </span>
                </a>
            </li>

            <li class="slide">
                <a class="side-menu__item" href="{{ route('privacy-policy') }}">
                    <i class="fe fe-shield"></i>

                    <span class="side-menu__label">
                        Privacy Policy
                    </span>
                </a>
            </li>

            <li class="slide">
                <a class="side-menu__item" href="{{ route('terms-of-service') }}">
                    <i class="fe fe-file-text"></i>

                    <span class="side-menu__label">
                        Terms of Service
                    </span>
                </a>
            </li>

        </ul>

    </div>


</aside>
