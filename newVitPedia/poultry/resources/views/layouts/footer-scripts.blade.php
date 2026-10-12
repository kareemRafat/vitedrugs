<!-- =======================================================
| Back To Top
======================================================= -->

<a href="#top" id="back-to-top" aria-label="Back to top">
    <i class="las la-angle-double-up"></i>
</a>

<!-- =======================================================
| Core Libraries
======================================================= -->

<!-- jQuery -->
<script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>

<!-- Bootstrap -->
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<!-- Moment -->
<script src="{{ asset('assets/plugins/moment/moment.js') }}"></script>

<!-- Ionicons -->
<script src="{{ asset('assets/plugins/ionicons/ionicons.js') }}"></script>

<!-- =======================================================
| Scroll & Layout Plugins
======================================================= -->

<!-- Perfect Scrollbar -->
<script src="{{ asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('assets/plugins/perfect-scrollbar/p-scroll.js') }}"></script>

<!-- Custom Scrollbar -->
<script src="{{ asset('assets/plugins/mscrollbar/jquery.mCustomScrollbar.concat.min.js') }}"></script>

<!-- Sidebar -->
<script src="{{ asset('assets/plugins/sidebar/sidebar-rtl.js') }}"></script>
<script src="{{ asset('assets/plugins/sidebar/sidebar-custom.js') }}"></script>

<!-- Side Menu -->
<script src="{{ asset('assets/plugins/side-menu/sidemenu.js') }}"></script>

<!-- Sticky Header -->
<script src="{{ asset('assets/js/sticky.js') }}"></script>

<!-- =======================================================
| UI Components
======================================================= -->

<!-- Rating -->
<script src="{{ asset('assets/plugins/rating/jquery.rating-stars.js') }}"></script>
<script src="{{ asset('assets/plugins/rating/jquery.barrating.js') }}"></script>

<!-- Sparkline -->
<script src="{{ asset('assets/plugins/jquery-sparkline/jquery.sparkline.min.js') }}"></script>

<!-- Icons -->
<script src="{{ asset('assets/js/eva-icons.min.js') }}"></script>

<!-- =======================================================
| Page Specific Scripts
======================================================= -->

@stack('scripts')

@yield('js')

<!-- =======================================================
| Application Scripts
======================================================= -->

<script src="{{ asset('assets/js/custom.js') }}"></script>