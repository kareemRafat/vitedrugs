<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="description" content="VetPedia Poultry - Intelligent Poultry Clinical Decision Support System">

    <meta name="author" content="VetPedia Team">

    <meta name="theme-color" content="#0162e8">

    <title>
        @yield('title', 'VetPedia Poultry')
    </title>

    @include('layouts.head')

    @stack('styles')

    <style>
        :root {
            --vp-primary: #0162e8;
            --vp-primary-dark: #0052c2;
            --vp-shadow: 0 8px 30px rgba(0, 0, 0, .08);
            --vp-border-radius: 12px;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            overflow-x: hidden;
        }

        /* ===========================================
        | Cards
        =========================================== */

        .custom-card {
            border: 0;
            border-radius: var(--vp-border-radius);
            box-shadow: var(--vp-shadow);
        }

        /* ===========================================
        | Floating Quick Actions
        =========================================== */

        .fab-container {

            position: fixed;

            right: 30px;

            bottom: 30px;

            z-index: 1050;
        }

        .fab-main {

            width: 60px;

            height: 60px;

            border: 0;

            border-radius: 50%;

            background: linear-gradient(135deg,
                    var(--vp-primary),
                    var(--vp-primary-dark));

            color: #fff;

            font-size: 24px;

            cursor: pointer;

            box-shadow:
                0 10px 30px rgba(1, 98, 232, .35);

            transition: .3s;
        }

        .fab-main:hover {

            transform: scale(1.08);
        }

        .fab-main.active {

            transform: rotate(45deg);
        }

        .fab-menu {

            position: absolute;

            right: 5px;

            bottom: 75px;

            opacity: 0;

            visibility: hidden;

            transform: translateY(20px);

            transition: .3s;
        }

        .fab-menu.active {

            opacity: 1;

            visibility: visible;

            transform: translateY(0);
        }

        .fab-item {

            width: 50px;

            height: 50px;

            border-radius: 50%;

            border: 0;

            margin-bottom: 12px;

            display: flex;

            align-items: center;

            justify-content: center;

            text-decoration: none;

            cursor: pointer;

            background: #fff;

            color: var(--vp-primary);

            box-shadow: var(--vp-shadow);

            transition: .25s;
        }

        .fab-item:hover {

            background: var(--vp-primary);

            color: #fff;

            transform: translateY(-3px);
        }

        /* ===========================================
        | Mobile
        =========================================== */

        @media(max-width:768px) {

            .fab-container {

                right: 20px;

                bottom: 20px;
            }

            .fab-main {

                width: 55px;

                height: 55px;
            }

            .fab-item {

                width: 46px;

                height: 46px;
            }

        }
    </style>

</head>

<body class="main-body app sidebar-mini">

    <!-- Loader -->

    <div id="global-loader">

        <img src="{{ asset('assets/img/loader.svg') }}" class="loader-img" alt="Loading">

    </div>

    <!-- Sidebar -->

    @include('layouts.main-sidebar')

    <!-- Main Content -->

    <div class="main-content app-content">

        @include('layouts.main-header')

        <div class="container-fluid">

            @yield('page-header')

            @yield('content')

        </div>

    </div>

    <!-- Layout Components -->

    @include('layouts.sidebar')

    @include('layouts.models')

    @include('layouts.footer')

    @include('layouts.footer-scripts')

    <!-- Floating Quick Actions -->

    <div class="fab-container">

        <button class="fab-main" id="fab-main">

            <i class="fa fa-plus"></i>

        </button>

        <div class="fab-menu" id="fab-menu">

            <a href="{{ url('/diagnostic') }}" class="fab-item" title="Start Diagnosis">

                <i class="fa fa-stethoscope"></i>

            </a>

            <a href="{{ route('clinical.search') }}" class="fab-item" title="Clinical Search">

                <i class="fa fa-search"></i>

            </a>

            <a href="{{ route('disease.comparison') }}" class="fab-item" title="Compare Diseases">

                <i class="fa fa-balance-scale"></i>

            </a>

            @if (Route::has('anatomy.index'))
                <a href="{{ route('anatomy.index') }}" class="fab-item" title="Anatomical Navigator">

                    <i class="fa fa-heartbeat"></i>

                </a>
            @endif

            @if (Route::has('body.map'))
                <a href="{{ route('body.map') }}" class="fab-item" title="Body Map">

                    <i class="fa fa-map"></i>

                </a>
            @endif

            <a href="{{ route('home') }}" class="fab-item" title="Home">

                <i class="fa fa-home"></i>

            </a>

            <button class="fab-item" id="scrollTopBtn" title="Back To Top">

                <i class="fa fa-arrow-up"></i>

            </button>

        </div>

    </div>

    @stack('scripts')

    @yield('js')

    <script>
        /* ===========================================
                    | Local Storage Helpers
                    =========================================== */

        function getStorage(key) {
            try {

                return JSON.parse(
                    localStorage.getItem(key)
                ) || [];

            } catch {

                return [];
            }
        }

        function setStorage(key, value) {
            localStorage.setItem(
                key,
                JSON.stringify(value)
            );
        }


        /* ===========================================
        | Floating Menu
        =========================================== */

        document.addEventListener(
            'DOMContentLoaded',
            function() {

                


                const mainBtn =
                    document.getElementById(
                        'fab-main'
                    );

                const menu =
                    document.getElementById(
                        'fab-menu'
                    );

                const topBtn =
                    document.getElementById(
                        'scrollTopBtn'
                    );

                if (mainBtn) {

                    mainBtn.addEventListener(
                        'click',
                        function(e) {

                            e.stopPropagation();

                            menu.classList.toggle(
                                'active'
                            );

                            this.classList.toggle(
                                'active'
                            );
                        }
                    );
                }

                document.addEventListener(
                    'click',
                    function(e) {

                        if (
                            !e.target.closest(
                                '.fab-container'
                            )
                        ) {

                            menu.classList.remove(
                                'active'
                            );

                            mainBtn.classList.remove(
                                'active'
                            );
                        }
                    }
                );

                if (topBtn) {

                    topBtn.addEventListener(
                        'click',
                        function() {

                            window.scrollTo({

                                top: 0,

                                behavior: 'smooth'
                            });

                        }
                    );
                }

            }
        );
    </script>

</body>

</html>
