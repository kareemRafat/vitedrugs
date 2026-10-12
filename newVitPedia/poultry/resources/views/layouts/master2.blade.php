<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token"
          content="{{ csrf_token() }}">

    <meta name="description"
          content="VetPedia Poultry - Intelligent Poultry Clinical Decision Support System">

    <meta name="keywords"
          content="VetPedia, Poultry, Veterinary, Diagnosis, Diseases, Clinical Decision Support">

    <meta name="author"
          content="VetPedia">

    <meta name="robots"
          content="index,follow">

    <title>

        @yield('title', 'VetPedia Poultry')

    </title>

    @include('layouts.head')

    <style>
        :root {

            --primary-gradient:
                linear-gradient(135deg,
                    #1e3c72,
                    #2a5298);

            --body-overlay:
                linear-gradient(
                    rgba(15, 32, 39, .88),
                    rgba(32, 58, 67, .88)
                );

            --card-radius: 22px;

            --shadow-lg:
                0 20px 50px rgba(0, 0, 0, .18);
        }

        html,
        body {

            min-height: 100%;
        }

        body {

            min-height: 100vh;

            font-family:
                "Nunito",
                "Segoe UI",
                sans-serif;

            background:
                var(--body-overlay),
                url("{{ asset('assets/img/photos/poultry-bg.jpg') }}");

            background-size: cover;

            background-position: center;

            background-repeat: no-repeat;

            background-attachment: fixed;

            overflow-x: hidden;
        }

        .page-wrapper {

            min-height: 100vh;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 50px 20px;
        }

        .auth-card {

            width: 100%;

            max-width: 540px;

            background: #fff;

            border-radius: var(--card-radius);

            overflow: hidden;

            box-shadow: var(--shadow-lg);

            animation: fadeIn .5s ease;
        }

        .brand-header {

            background: var(--primary-gradient);

            color: #fff;

            text-align: center;

            padding: 40px 30px;

            position: relative;
        }

        .brand-header::after {

            content: '';

            position: absolute;

            bottom: 0;

            left: 50%;

            transform: translateX(-50%);

            width: 70px;

            height: 4px;

            border-radius: 50px;

            background: rgba(255, 255, 255, .65);
        }

        .brand-header h1 {

            font-size: 2rem;

            font-weight: 700;

            margin-bottom: 12px;

            color: #fff;
        }

        .brand-header p {

            margin: 0;

            font-size: 15px;

            opacity: .9;

            line-height: 1.7;
        }

        .auth-body {

            padding: 40px;
        }

        /* Forms */

        .auth-body .form-control {

            min-height: 48px;

            border-radius: 10px;

            transition: all .25s ease;
        }

        .auth-body .form-control:focus {

            border-color: #2a5298;

            box-shadow:
                0 0 0 .15rem rgba(42, 82, 152, .15);
        }

        .auth-body .btn {

            min-height: 48px;

            border-radius: 10px;

            font-weight: 600;
        }

        /* Breadcrumb */

        .breadcrumb-header {

            margin-bottom: 25px;
        }

        .breadcrumb-header a {

            text-decoration: none;

            transition: .2s;
        }

        .breadcrumb-header a:hover {

            color: #0162e8;
        }

        .breadcrumb-header .text-dark {

            font-size: 15px;
        }

        /* Loader */

        #global-loader {

            background: rgba(255,255,255,.95);
        }

        /* Animation */

        @keyframes fadeIn {

            from {

                opacity: 0;

                transform: translateY(25px);
            }

            to {

                opacity: 1;

                transform: translateY(0);
            }
        }

        /* Responsive */

        @media (max-width: 768px) {

            .page-wrapper {

                padding: 20px;
            }

            .auth-card {

                border-radius: 16px;
            }

            .brand-header {

                padding: 35px 20px;
            }

            .brand-header h1 {

                font-size: 1.7rem;
            }

            .brand-header p {

                font-size: 14px;
            }

            .auth-body {

                padding: 25px;
            }
        }

        @media (max-width: 576px) {

            body {

                background-attachment: scroll;
            }

            .brand-header h1 {

                font-size: 1.5rem;
            }

            .auth-body {

                padding: 22px 20px;
            }
        }
    </style>

    @yield('css')

</head>

<body>

    <!-- Global Loader -->

    <div id="global-loader">

        <img src="{{ asset('assets/img/loader.svg') }}"
             class="loader-img"
             alt="Loading">

    </div>

    <!-- Page Wrapper -->

    <div class="page-wrapper">

        <div class="auth-card">

            <div class="brand-header">

                <h1>

                    VetPedia Poultry

                </h1>

                <p>

                    Intelligent Poultry Clinical Decision Support System

                </p>

            </div>

            <div class="auth-body">

                @yield('content')

            </div>

        </div>

    </div>

    @include('layouts.footer-scripts')

    @yield('js')

</body>

</html>