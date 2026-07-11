<!-- main-header opened -->
<div class="main-header sticky side-header nav nav-item">

    <div class="container-fluid">

        <div class="main-header-left">

            <!-- Logo -->
            <div class="responsive-logo">

                <a href="{{ route('home') }}">
                    <img src="{{ asset('assets/img/brand/vetpedia-logo.png') }}" class="logo-1" alt="VetPedia">
                </a>

                <a href="{{ route('home') }}">
                    <img src="{{ asset('assets/img/brand/vetpedia-logo-white.png') }}" class="dark-logo-1"
                        alt="VetPedia">
                </a>

                <a href="{{ route('home') }}">
                    <img src="{{ URL::asset('assets/img/brand/favicon.png') }}" class="logo-2" alt="VetPedia">
                </a>

                <a href="{{ route('home') }}">
                    <img src="{{ URL::asset('assets/img/brand/favicon-white.png') }}" class="dark-logo-2"
                        alt="VetPedia">
                </a>

            </div>

            <!-- Sidebar Toggle -->
            <div class="app-sidebar__toggle" data-toggle="sidebar">

                <a class="open-toggle" href="#">
                    <i class="header-icon fe fe-align-left"></i>
                </a>

                <a class="close-toggle" href="#">
                    <i class="header-icons fe fe-x"></i>
                </a>

            </div>


        </div>

        <div class="main-header-right">

            <div class="d-flex align-items-center">

                <a href="{{ route('products.index') }}" class="btn btn-light btn-sm mr-2">
                    Products
                </a>

                <a href="{{ route('diseases.index') }}" class="btn btn-light btn-sm mr-2">
                    Diseases
                </a>

                <a href="{{ route('active-ingredients.index') }}" class="btn btn-light btn-sm mr-2">
                    Ingredients
                </a>

                <a href="{{ route('companies.index') }}" class="btn btn-light btn-sm">
                    Companies
                </a>

            </div>

        </div>

    </div>

</div>
<!-- /main-header -->
