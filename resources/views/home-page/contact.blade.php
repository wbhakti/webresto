<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Kontak</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="{{ asset('assets/favicon.ico') }}" />
        <!-- Bootstrap icons-->
        <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
        <script src="{{ asset('vendor/jquery/jquery.min.js')}}"></script>
        <style>
            body {
                padding-top: 56px; /* Sesuaikan nilai ini dengan tinggi navbar */
            }
            .custom-navbar {
                background-color: #000; 
                color: white;
            }
            .custom-navbar .btn-outline-dark {
                color: #fff;
                border-color: #fff;
            }
            .custom-navbar .btn-outline-dark:hover {
                background-color: #fff;
                color: #000;
            }
        </style>
    </head>
    <body>
        <!-- Navigation-->
        <nav class="navbar custom-navbar fixed-top">
            <div class="container px-4 px-lg-5">
                <a class="navbar-brand" href="{{ url('/') }}" style="color: #fff;"><b>Home</b></a>                  
            </div>
        </nav>
        
        <div class="container px-4 px-lg-5 mt-0">
            <div class="text-left text-black">
                <br>
                <p dir="auto" class="body"><strong>Contact Us Now</strong></p>
                <p dir="auto" class="body"><span style="font-weight: 400">For more information about the Klajek application, please do not hesitate to contact us via the contact below.
                </span></p>
                <br>
            </div>
            <div class="text-left text-black">
                <br>
                <p dir="auto" class="body"><strong>Kopinggir Jalan</strong></p>
                <p dir="auto" class="body"><span style="font-weight: 400">08123456789
                </span></p>
                <br>
            </div>
            <div class="text-left text-black">
                <br>
                <p dir="auto" class="body"><strong>Email</strong></p>
                <p dir="auto" class="body"><span style="font-weight: 400">kopinggir.stargroup@gmail.com
                </span></p>
                <br>
            </div>
        
        <!-- Footer-->
        <footer class="py-5 bg-dark" style="background-color: #000 !important;">
            <div class="container"><p class="m-0 text-center" style="color: #fff">Copyright &copy; Kopinggir Jalan by Star Group 2025</p></div>
        </footer>
        <!-- Bootstrap core JS-->
        <script src="{{ asset('frontend-vendor/bootstrap-5.2.3-dist/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
        <!-- Core theme JS-->
        <script src="{{ asset('js/scripts.js') }}"></script>
    </body>
</html>