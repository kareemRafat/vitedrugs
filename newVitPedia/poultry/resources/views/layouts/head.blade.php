<!-- =========================================================
    VETPEDIA POULTRY
    Global Styles & Assets
========================================================== -->

<!-- =========================================================
    META
========================================================== -->

<meta charset="UTF-8">

<meta http-equiv="X-UA-Compatible"
      content="IE=edge">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<meta name="description"
      content="VetPedia Poultry - Intelligent Poultry Clinical Decision Support and Knowledge Platform">

<meta name="author"
      content="VetPedia Team">

<meta name="theme-color"
      content="#0162e8">

<!-- =========================================================
    TITLE
========================================================== -->

<title>
    @yield('title', 'VetPedia Poultry | Intelligent Poultry Diagnostic Platform')
</title>

<!-- =========================================================
    FAVICON
========================================================== -->

<link rel="shortcut icon"
      href="{{ asset('assets/img/brand/favicon.png') }}"
      type="image/x-icon">

<link rel="apple-touch-icon"
      href="{{ asset('assets/img/brand/favicon.png') }}">

<!-- =========================================================
    GOOGLE FONTS
========================================================== -->

<link rel="preconnect"
      href="https://fonts.googleapis.com">

<link rel="preconnect"
      href="https://fonts.gstatic.com"
      crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
      rel="stylesheet">

<!-- =========================================================
    CORE CSS
========================================================== -->

<!-- Icons -->
<link href="{{ asset('assets/css/icons.css') }}"
      rel="stylesheet">

<!-- Custom Scrollbar -->
<link href="{{ asset('assets/plugins/mscrollbar/jquery.mCustomScrollbar.css') }}"
      rel="stylesheet">

<!-- Sidebar -->
<link href="{{ asset('assets/plugins/sidebar/sidebar.css') }}"
      rel="stylesheet">

<!-- Side Menu -->
<link href="{{ asset('assets/css/sidemenu.css') }}"
      rel="stylesheet">

<!-- Main Theme -->
<link href="{{ asset('assets/css/style.css') }}"
      rel="stylesheet">

<!-- Dark Mode -->
<link href="{{ asset('assets/css/style-dark.css') }}"
      rel="stylesheet">

<!-- Skin Modes -->
<link href="{{ asset('assets/css/skin-modes.css') }}"
      rel="stylesheet">

<!-- =========================================================
    GLOBAL VETPEDIA DESIGN SYSTEM
========================================================== -->

<style>

:root{

    /* Colors */

    --vp-primary:#0162e8;
    --vp-primary-dark:#0152c3;

    --vp-success:#22c55e;
    --vp-danger:#ef4444;
    --vp-warning:#f59e0b;
    --vp-info:#06b6d4;

    /* Backgrounds */

    --vp-bg:#f5f7fb;
    --vp-card:#ffffff;
    --vp-border:#e9edf5;

    /* Typography */

    --vp-text:#1e293b;
    --vp-muted:#64748b;

    /* Effects */

    --vp-radius:14px;

    --vp-shadow-sm:0 2px 10px rgba(0,0,0,.04);

    --vp-shadow-md:0 8px 25px rgba(0,0,0,.06);

    --vp-transition:all .25s ease;
}

/* =========================================================
   Base
========================================================= */

html{

    scroll-behavior:smooth;
}

body{

    font-family:'Inter',sans-serif;

    background:var(--vp-bg);

    color:var(--vp-text);

    font-size:14px;

    line-height:1.7;

    overflow-x:hidden;
}

h1,h2,h3,h4,h5,h6{

    color:var(--vp-text);

    font-weight:700;
}

h1{

    font-size:2rem;
}

h2{

    font-size:1.75rem;
}

h3{

    font-size:1.5rem;
}

small,
.text-muted{

    color:var(--vp-muted)!important;
}

/* =========================================================
   Cards
========================================================= */

.card{

    border:0;

    border-radius:var(--vp-radius);

    box-shadow:var(--vp-shadow-sm);

    overflow:hidden;

    transition:var(--vp-transition);
}

.card:hover{

    box-shadow:var(--vp-shadow-md);
}

.card-header{

    background:#fff;

    border-bottom:1px solid var(--vp-border);

    font-weight:600;

    padding:1rem 1.25rem;
}

.card-body{

    padding:1.25rem;
}

/* =========================================================
   Buttons
========================================================= */

.btn{

    border-radius:10px;

    font-weight:500;

    transition:var(--vp-transition);
}

.btn:focus{

    box-shadow:none;
}

.btn-primary{

    background:var(--vp-primary);

    border-color:var(--vp-primary);
}

.btn-primary:hover{

    background:var(--vp-primary-dark);

    border-color:var(--vp-primary-dark);

    transform:translateY(-1px);
}

/* =========================================================
   Forms
========================================================= */

.form-control,
.custom-select{

    border-radius:10px;

    border:1px solid #dbe2ea;

    min-height:45px;

    box-shadow:none;

    transition:var(--vp-transition);
}

.form-control:focus,
.custom-select:focus{

    border-color:var(--vp-primary);

    box-shadow:0 0 0 .15rem rgba(1,98,232,.1);
}

/* =========================================================
   Navigation
========================================================= */

.nav-tabs{

    border-bottom:1px solid var(--vp-border);
}

.nav-tabs .nav-link{

    border:0;

    border-bottom:3px solid transparent;

    color:var(--vp-muted);

    font-weight:600;
}

.nav-tabs .nav-link.active{

    color:var(--vp-primary);

    border-bottom-color:var(--vp-primary);

    background:transparent;
}

/* =========================================================
   Links
========================================================= */

a{

    color:var(--vp-primary);

    transition:var(--vp-transition);
}

a:hover{

    color:var(--vp-primary-dark);

    text-decoration:none;
}

/* =========================================================
   Tables
========================================================= */

.table{

    color:var(--vp-text);
}

.table thead th{

    border-top:0;

    border-bottom:1px solid var(--vp-border);

    font-weight:700;

    background:#fafbfc;
}

.table td,
.table th{

    vertical-align:middle;
}

/* =========================================================
   Badges
========================================================= */

.badge{

    padding:8px 12px;

    border-radius:8px;

    font-weight:500;
}

/* =========================================================
   Alerts
========================================================= */

.alert{

    border:0;

    border-radius:12px;
}

/* =========================================================
   Dropdowns
========================================================= */

.dropdown-menu{

    border:0;

    border-radius:12px;

    box-shadow:var(--vp-shadow-md);
}

/* =========================================================
   Scrollbar
========================================================= */

::-webkit-scrollbar{

    width:8px;

    height:8px;
}

::-webkit-scrollbar-track{

    background:#edf2f7;
}

::-webkit-scrollbar-thumb{

    background:#cbd5e1;

    border-radius:50px;
}

::-webkit-scrollbar-thumb:hover{

    background:#94a3b8;
}

/* =========================================================
   Utilities
========================================================= */

.section-spacing{

    margin-bottom:30px;
}

.shadow-hover{

    transition:var(--vp-transition);
}

.shadow-hover:hover{

    transform:translateY(-3px);

    box-shadow:var(--vp-shadow-md);
}

/* =========================================================
   Responsive
========================================================= */

@media (max-width:991px){

    .card-body{

        padding:1rem;
    }

    h1{

        font-size:1.7rem;
    }

    h2{

        font-size:1.5rem;
    }

    h3{

        font-size:1.3rem;
    }
}

</style>

<!-- =========================================================
    PAGE SPECIFIC CSS
========================================================== -->

@yield('css')