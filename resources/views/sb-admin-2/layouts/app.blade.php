<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin - Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <script src="{{ asset('vendor/jquery/jquery.min.js')}}"></script>
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <style>
    .sidebar {
        background-color: #c71a18;
        background-image: linear-gradient(180deg, #14162c 10%, #3657b6 100%);
        background-size: cover;
    }
    </style>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar bg-gradient-primary -->
        <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar">

            @if(Session::get('role') == 'admin')
            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('dashboard') }}">
                <div class="sidebar-brand-text mx-2">Food Dashboard<sup></sup></div>
            </a>
            @else
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('dashboard/dayTransaction') }}">
                <div class="sidebar-brand-text mx-2">Food Dashboard<sup></sup></div>
            </a>
            @endif

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Menu
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link" href="{{ url('dashboard') }}">
                    <i class="fas fa-fw fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <!-- Nav Item - Master -->
            <li class="nav-item">

            <a class="nav-link collapsed"
            href="#"
            data-toggle="collapse"
            data-target="#collapseMaster"
            aria-expanded="false"
            aria-controls="collapseMaster">

                <i class="fas fa-fw fa-database"></i>
                <span>Master</span>
            </a>

            <div id="collapseMaster"
                class="collapse"
                aria-labelledby="headingMaster"
                data-parent="#accordionSidebar">

                <div class="bg-white py-2 collapse-inner rounded">

                    <!-- Merchant -->
                    <a class="collapse-item"
                    href="{{ url('dashboard/masterMerchant') }}">
                        <i class="fas fa-store fa-fw mr-2"></i>
                        Merchant
                    </a>

                    <!-- Kategori -->
                    <a class="collapse-item"
                    href="{{ url('dashboard/masterCategories') }}">
                        <i class="fas fa-tags fa-fw mr-2"></i>
                        Kategori
                    </a>

                    <!-- Produk -->
                    <a class="collapse-item"
                    href="{{ url('dashboard/masterProducts') }}">
                        <i class="fas fa-utensils fa-fw mr-2"></i>
                        Produk
                    </a>

                    <!-- Promo -->
                    <a class="collapse-item"
                    href="{{ url('dashboard/masterPromotions') }}">
                        <i class="fas fa-bullhorn fa-fw mr-2"></i>
                        Promotion
                    </a>

                </div>
            </div>
            </li>
            <li class="nav-item">
                <!-- <a class="nav-link" href="{{ url('dashboard/transaction') }}"> -->
                <!-- <i class="fas fa-fw fa-chart-area"></i> -->
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseThree"
                aria-expanded="true" aria-controls="collapseThree">
                <i class="fas fa-fw fa-cog"></i>
                <span>Order</span>
                </a>
                <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                         <a class="collapse-item" href="{{ url('dashboard/transaction') }}">Transaksi</a>
                    </div>
                </div>
            </li>
            
            <!-- Nav Item - Laporan -->
            <li class="nav-item">

            <a class="nav-link collapsed"
            href="#"
            data-toggle="collapse"
            data-target="#collapseLaporan"
            aria-expanded="false"
            aria-controls="collapseLaporan">

                <i class="fas fa-fw fa-chart-bar"></i>
                <span>Laporan</span>
            </a>

            <div id="collapseLaporan"
                class="collapse"
                aria-labelledby="headingLaporan"
                data-parent="#accordionSidebar">

                <div class="bg-white py-2 collapse-inner rounded">

                    <!-- Dashboard Penjualan -->
                    <a class="collapse-item"
                    href="{{ url('laporan/dashboard') }}">
                        <i class="fas fa-chart-line fa-fw mr-2"></i>
                        Dashboard Penjualan
                    </a>


                    <!-- ========================= -->
                    <!-- PENJUALAN -->
                    <!-- ========================= -->

                    <h6 class="collapse-header">
                        Penjualan
                    </h6>

                    <a class="collapse-item"
                    href="{{ url('laporan/penjualan/periode') }}">
                        <i class="fas fa-calendar-alt fa-fw mr-2"></i>
                        Periode
                    </a>

                    <a class="collapse-item"
                    href="{{ url('laporan/penjualan/per-hari') }}">
                        <i class="fas fa-calendar-day fa-fw mr-2"></i>
                        Per Hari
                    </a>

                    <a class="collapse-item"
                    href="{{ url('laporan/penjualan/per-jam') }}">
                        <i class="fas fa-clock fa-fw mr-2"></i>
                        Per Jam
                    </a>

                    <a class="collapse-item"
                    href="{{ url('laporan/penjualan/per-kasir') }}">
                        <i class="fas fa-user fa-fw mr-2"></i>
                        Per Kasir
                    </a>


                    <!-- ========================= -->
                    <!-- PRODUK -->
                    <!-- ========================= -->

                    <h6 class="collapse-header">
                        Produk
                    </h6>

                    <a class="collapse-item"
                    href="{{ url('laporan/produk/terlaris') }}">
                        <i class="fas fa-trophy fa-fw mr-2"></i>
                        Produk Terlaris
                    </a>

                    <a class="collapse-item"
                    href="{{ url('laporan/produk/terendah') }}">
                        <i class="fas fa-arrow-down fa-fw mr-2"></i>
                        Produk Terendah
                    </a>

                    <a class="collapse-item"
                    href="{{ url('laporan/produk/per-kategori') }}">
                        <i class="fas fa-tags fa-fw mr-2"></i>
                        Per Kategori
                    </a>


                    <!-- ========================= -->
                    <!-- PEMBAYARAN -->
                    <!-- ========================= -->

                    <h6 class="collapse-header">
                        Pembayaran
                    </h6>

                    <a class="collapse-item"
                    href="{{ url('laporan/pembayaran/cash') }}">
                        <i class="fas fa-money-bill-wave fa-fw mr-2"></i>
                        Cash
                    </a>

                    <a class="collapse-item"
                    href="{{ url('laporan/pembayaran/qris') }}">
                        <i class="fas fa-qrcode fa-fw mr-2"></i>
                        QRIS
                    </a>

                    <a class="collapse-item"
                    href="{{ url('laporan/pembayaran/transfer') }}">
                        <i class="fas fa-university fa-fw mr-2"></i>
                        Transfer
                    </a>

                    <a class="collapse-item"
                    href="{{ url('laporan/pembayaran/edc') }}">
                        <i class="fas fa-credit-card fa-fw mr-2"></i>
                        EDC
                    </a>


                    <!-- ========================= -->
                    <!-- PROMO -->
                    <!-- ========================= -->

                    <h6 class="collapse-header">
                        Promo
                    </h6>

                    <a class="collapse-item"
                    href="{{ url('laporan/promo/penggunaan') }}">
                        <i class="fas fa-bullhorn fa-fw mr-2"></i>
                        Penggunaan Promo
                    </a>

                    <a class="collapse-item"
                    href="{{ url('laporan/promo/diskon') }}">
                        <i class="fas fa-percent fa-fw mr-2"></i>
                        Total Diskon
                    </a>


                    <!-- ========================= -->
                    <!-- OPERASIONAL -->
                    <!-- ========================= -->

                    <h6 class="collapse-header">
                        Operasional
                    </h6>

                    <a class="collapse-item"
                    href="{{ url('laporan/shift-kasir') }}">
                        <i class="fas fa-user-clock fa-fw mr-2"></i>
                        Shift Kasir
                    </a>

                    <a class="collapse-item"
                    href="{{ url('laporan/cancel-refund') }}">
                        <i class="fas fa-undo fa-fw mr-2"></i>
                        Cancel / Refund
                    </a>


                    <!-- ========================= -->
                    <!-- PROFIT -->
                    <!-- ========================= -->

                    <h6 class="collapse-header">
                        Profit
                    </h6>

                    <a class="collapse-item"
                    href="{{ url('laporan/profit/hpp') }}">
                        <i class="fas fa-boxes fa-fw mr-2"></i>
                        HPP
                    </a>

                    <a class="collapse-item"
                    href="{{ url('laporan/profit/gross-profit') }}">
                        <i class="fas fa-chart-pie fa-fw mr-2"></i>
                        Gross Profit
                    </a>

                </div>
            </div>
            </li>
            
            
            
            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- End of Sidebar -->
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            @if(Session::get('role') == 'admin')
                                <h5 style="color:black;"><b>Welcome Admin!</b></h5>
                            @else
                                <h5 style="color:black;"><b>Welcome Kasir!</b></h5>
                            @endif
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                       
                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            
                        </li>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Admin</span>
                                <img class="img-profile rounded-circle" src="{{ asset('img/undraw_profile.svg') }}">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->
                <!-- Begin Page Content -->
                <div class="container-fluid">

                    @yield('content')

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->
            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; 2026 (Version 231226)</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->
    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="{{ route('logout') }}">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

</body>

</html>