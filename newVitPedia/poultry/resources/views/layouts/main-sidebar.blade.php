<!-- main-sidebar -->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>

<aside class="app-sidebar sidebar-scroll">

    <div class="main-sidebar-header active">

        <a class="desktop-logo logo-light active" href="{{ route('home') }}">

            <img src="{{ asset('assets/img/brand/logo.png') }}" class="main-logo" alt="VetPedia">

        </a>

        <a class="desktop-logo logo-dark active" href="{{ route('home') }}">

            <img src="{{ asset('assets/img/brand/logo-white.png') }}" class="main-logo dark-theme" alt="VetPedia">

        </a>

        <a class="logo-icon mobile-logo icon-light active" href="{{ route('home') }}">

            <img src="{{ asset('assets/img/brand/favicon.png') }}" class="logo-icon" alt="VetPedia">

        </a>

    </div>


    <div class="main-sidemenu">

        {{-- PLATFORM INFO --}}

        <div class="app-sidebar__user clearfix">

            <div class="dropdown user-pro-body">

                <div>

                    <img src="{{ asset('assets/img/faces/6.jpg') }}" class="avatar avatar-xl brround" alt="VetPedia">

                </div>

                <div class="user-info">

                    <h4 class="font-weight-semibold mt-3 mb-1">

                        VetPedia

                    </h4>

                    <span class="text-muted">

                        Poultry Clinical Intelligence Platform

                    </span>

                </div>

            </div>

        </div>


        <ul class="side-menu">

            {{-- DASHBOARD --}}

            <li class="side-item side-item-category">

                Dashboard

            </li>

            <li class="slide">

                <a class="side-menu__item {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">

                    <i class="side-menu__icon fe fe-home"></i>

                    <span class="side-menu__label">

                        Home

                    </span>

                </a>

            </li>


            {{-- DIAGNOSTICS --}}

            <li class="side-item side-item-category">

                Diagnostics

            </li>

            <li class="slide">

                <a class="side-menu__item {{ request()->routeIs('diagnostic.*') ? 'active' : '' }}"
                    href="{{ route('diagnostic.index') }}">

                    <i class="side-menu__icon fe fe-activity"></i>

                    <span class="side-menu__label">

                        Diagnostic Engine

                    </span>

                </a>

            </li>

            <li class="slide">

                <a class="side-menu__item {{ request()->routeIs('clinical.search') ? 'active' : '' }}"
                    href="{{ route('clinical.search') }}">

                    <i class="side-menu__icon fe fe-search"></i>

                    <span class="side-menu__label">

                        Clinical Search

                    </span>

                </a>

            </li>


            {{-- KNOWLEDGE EXPLORER --}}

            <li class="side-item side-item-category">

                Knowledge Explorer

            </li>

            <li class="slide">

                <a class="side-menu__item {{ request()->routeIs('body.map') ? 'active' : '' }}"
                    href="{{ route('body.map') }}">

                    <i class="side-menu__icon fe fe-map"></i>

                    <span class="side-menu__label">

                        Interactive Body Map

                    </span>

                </a>

            </li>

            <li class="slide">

                <a class="side-menu__item {{ request()->routeIs('anatomy.*') ? 'active' : '' }}"
                    href="{{ route('anatomy.index') }}">

                    <i class="side-menu__icon fe fe-heart"></i>

                    <span class="side-menu__label">

                        Anatomical Navigator

                    </span>

                </a>

            </li>

            <li class="slide">

                <a class="side-menu__item {{ request()->routeIs('lesion.atlas') ? 'active' : '' }}"
                    href="{{ route('lesion.atlas') }}">

                    <i class="side-menu__icon fe fe-grid"></i>

                    <span class="side-menu__label">

                        Lesion Atlas

                    </span>

                </a>

            </li>


            {{-- CLINICAL KNOWLEDGE BASE --}}

            <li class="side-item side-item-category">

                Clinical Knowledge Base

            </li>

            <li class="slide">

                <a class="side-menu__item {{ request()->routeIs('diseases.*') ? 'active' : '' }}"
                    href="{{ route('diseases.index') }}">

                    <i class="side-menu__icon fe fe-book-open"></i>

                    <span class="side-menu__label">

                        Disease Explorer

                    </span>

                </a>

            </li>

            <li class="slide">

                <a class="side-menu__item {{ request()->routeIs('findings.*') ? 'active' : '' }}"
                    href="{{ route('home') }}">

                    <i class="side-menu__icon fe fe-list"></i>

                    <span class="side-menu__label">

                        Finding Explorer

                    </span>

                </a>

            </li>

            <li class="slide">

                <a class="side-menu__item {{ request()->routeIs('cases.*') ? 'active' : '' }}"
                    href="{{ route('cases.index') }}">

                    <i class="side-menu__icon fe fe-folder"></i>

                    <span class="side-menu__label">

                        Clinical Cases

                    </span>

                </a>

            </li>


            {{-- CLINICAL TOOLS --}}

            <li class="side-item side-item-category">

                Clinical Tools

            </li>

            <li class="slide">

                <a class="side-menu__item {{ request()->routeIs('disease.comparison') ? 'active' : '' }}"
                    href="{{ route('disease.comparison') }}">

                    <i class="side-menu__icon fe fe-layers"></i>

                    <span class="side-menu__label">

                        Disease Comparison

                    </span>

                </a>

            </li>

        </ul>



    </div>

</aside>
<!-- main-sidebar -->
