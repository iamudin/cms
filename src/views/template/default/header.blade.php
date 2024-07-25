<!DOCTYPE html>
<html lang="en">

<head>
    {{ init_header() }}
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ template_asset('vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ template_asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ template_asset('vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ template_asset('vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
    <link href="{{ template_asset('vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ template_asset('vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="{{ template_asset('css/style.css') }}" rel="stylesheet">
</head>

<body>
    <!-- ======= Header ======= -->
    <header id="header" class="fixed-top d-flex align-items-center">
        <div class="container d-flex align-items-center justify-content-between">

            <div class="logo">
                <h1 class="text-light"><a href="index.html">Serenity</a></h1>
                <!-- Uncomment below if you prefer to use an image logo -->
                <!-- <a href="index.html"><img src="assets/img/logo.png" alt="" class="img-fluid"></a>-->
            </div>

            <nav id="navbar" class="navbar">
                <ul>
                    @foreach (get_menu('header') as $row)
                        @if (get_menu('header', $row->id)->count() > 0)
                            <li class="dropdown"><a href="#"><span>{{ $row->name }}</span> <i
                                        class="bi bi-chevron-down"></i></a>
                                <ul>
                                    @foreach (get_menu('header', $row->id) as $row2)
                                        @if (get_menu('header', $row2->id)->count() > 0)
                                        <li class="dropdown"><a href="#"><span>{{ $row2->name }}</span>
                                          <i class="bi bi-chevron-right"></i></a>
                                            <ul>
                                            @foreach (get_menu('header', $row2->id) as $row3)
                                            <li><a href="about.html">{{ $row3->name }}</a></li>
                                            @endforeach
                                            </ul>
                                            </li>
                                            @else
                                                <li><a href="about.html">{{ $row2->name }}</a></li>
                                        @endif
                                    @endforeach
                                    {{-- <li class="dropdown"><a href="#"><span>Deep Drop Down</span> <i class="bi bi-chevron-right"></i></a>
                  <ul>
                    <li><a href="#">Deep Drop Down 1</a></li>
                    <li><a href="#">Deep Drop Down 2</a></li>
                    <li><a href="#">Deep Drop Down 3</a></li>
                    <li><a href="#">Deep Drop Down 4</a></li>
                    <li><a href="#">Deep Drop Down 5</a></li>
                  </ul>
                </li> --}}
                                </ul>
                            </li>
                        @else
                            <li><a href="index.html">{{ $row->name }}</a></li>
                        @endif
                    @endforeach



                </ul>
                <i class="bi bi-list mobile-nav-toggle"></i>
            </nav><!-- .navbar -->

        </div>
    </header><!-- End Header -->
