<!-- Main Header -->
<div class="main-header sticky side-header nav nav-item">

    <div class="container-fluid">

        <!-- LEFT SIDE -->
        <div class="main-header-left">

            <!-- Responsive Logo -->
            <div class="responsive-logo">

                <a href="{{ route('home') }}">

                    <img src="{{ asset('assets/img/brand/logo.png') }}"
                        class="logo-1"
                        alt="VetPedia Logo">

                </a>

                <a href="{{ route('home') }}">

                    <img src="{{ asset('assets/img/brand/logo-white.png') }}"
                        class="dark-logo-1"
                        alt="VetPedia Logo">

                </a>

            </div>

            <!-- Sidebar Toggle -->
            <div class="app-sidebar__toggle"
                data-toggle="sidebar">

                <a class="open-toggle"
                    href="javascript:void(0)">

                    <i class="header-icon fe fe-align-left"></i>

                </a>

                <a class="close-toggle"
                    href="javascript:void(0)">

                    <i class="header-icon fe fe-x"></i>

                </a>

            </div>

            <!-- Universal Search -->
            <div class="main-header-center mr-3 d-none d-lg-block">

                <form action="{{ route('clinical.search') }}"
                    method="GET">

                    <div class="input-group">

                        <input
                            type="search"
                            name="q"
                            class="form-control"
                            placeholder="Search diseases, findings, lesions..."
                            autocomplete="off"
                            aria-label="Universal Search">

                        <div class="input-group-append">

                            <button class="btn btn-primary"
                                type="submit">

                                <i class="fas fa-search"></i>

                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

        <!-- RIGHT SIDE -->
        <div class="main-header-right">

            <div class="nav nav-item navbar-nav-right ml-auto align-items-center">

                <!-- Home -->

                <a href="{{ route('home') }}"
                    class="nav-link"
                    data-toggle="tooltip"
                    title="Home">

                    <i class="fe fe-home tx-20"></i>

                </a>


                <!-- Diagnostic Engine -->

                <a href="{{ url('/diagnostic') }}"
                    class="nav-link"
                    data-toggle="tooltip"
                    title="Diagnostic Engine">

                    <i class="fas fa-stethoscope tx-20"></i>

                </a>


                <!-- Clinical Search -->

                <a href="{{ route('clinical.search') }}"
                    class="nav-link"
                    data-toggle="tooltip"
                    title="Clinical Search">

                    <i class="fas fa-search-plus tx-20"></i>

                </a>


                <!-- Disease Comparison -->

                <a href="{{ route('disease.comparison') }}"
                    class="nav-link"
                    data-toggle="tooltip"
                    title="Compare Diseases">

                    <i class="fas fa-balance-scale tx-20"></i>

                </a>


                <!-- Anatomical Navigator -->

                <a href="{{ route('anatomy.index') }}"
                    class="nav-link"
                    data-toggle="tooltip"
                    title="Anatomical Navigator">

                    <i class="fas fa-heartbeat tx-20"></i>

                </a>


                <!-- Body Map -->

                @if(Route::has('body.map'))

                    <a href="{{ route('body.map') }}"
                        class="nav-link"
                        data-toggle="tooltip"
                        title="Interactive Body Map">

                        <i class="fas fa-dna tx-20"></i>

                    </a>

                @endif


                <!-- Full Screen -->

                <div class="nav-item full-screen fullscreen-button">

                    <a class="new nav-link full-screen-link"
                        href="javascript:void(0)"
                        data-toggle="tooltip"
                        title="Full Screen">

                        <i class="fas fa-expand tx-20"></i>

                    </a>

                </div>


                <!-- User / System Menu -->

                <div class="dropdown main-profile-menu nav nav-item nav-link">

                    <a class="profile-user d-flex"
                        href="javascript:void(0)"
                        data-toggle="dropdown">

                        <img src="{{ asset('assets/img/faces/6.jpg') }}"
                            alt="VetPedia">

                    </a>

                    <div class="dropdown-menu dropdown-menu-right">

                        <div class="main-header-profile bg-primary p-3">

                            <div class="d-flex">

                                <div class="main-img-user">

                                    <img src="{{ asset('assets/img/faces/6.jpg') }}"
                                        alt="User">

                                </div>

                                <div class="ml-3 my-auto text-white">

                                    <h6 class="mb-0 text-white">

                                        VetPedia User

                                    </h6>

                                    <small>

                                        Poultry Clinical Decision Support

                                    </small>

                                </div>

                            </div>

                        </div>

                        <a class="dropdown-item"
                            href="{{ route('home') }}">

                            <i class="bx bx-home"></i>

                            Dashboard

                        </a>

                        @if(Route::has('diseases.index'))

                            <a class="dropdown-item"
                                href="{{ route('diseases.index') }}">

                                <i class="bx bx-virus"></i>

                                Disease Explorer

                            </a>

                        @endif

                        @if(Route::has('cases.index'))

                            <a class="dropdown-item"
                                href="{{ route('cases.index') }}">

                                <i class="bx bx-folder"></i>

                                Clinical Cases

                            </a>

                        @endif

                        <a class="dropdown-item"
                            href="{{ route('disease.comparison') }}">

                            <i class="bx bx-git-compare"></i>

                            Compare Diseases

                        </a>

                        @if(Route::has('lesion.atlas'))

                            <a class="dropdown-item"
                                href="{{ route('lesion.atlas') }}">

                                <i class="bx bx-grid-alt"></i>

                                Lesion Atlas

                            </a>

                        @endif

                        @if(Route::has('body.map'))

                            <a class="dropdown-item"
                                href="{{ route('body.map') }}">

                                <i class="bx bx-map"></i>

                                Body Map

                            </a>

                        @endif

                        <div class="dropdown-divider"></div>

                        

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
<!-- /Main Header -->