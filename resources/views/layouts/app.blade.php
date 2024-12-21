<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'GermanTMX') }}</title>
  <!--  <link href="{{ url('/').'/'.asset('assets/css/testdash2.css') }}" rel="stylesheet" /> -->
  <link href="{{ url('/').'/'.asset('assets/css/materialdashboard2.css?v=' . now()->timestamp) }}" rel="stylesheet" />
  <link rel="stylesheet" type="text/css"
    href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
  <!-- CSS Files -->

  <link href="{{ url('/').'/'.asset('assets/css/newdesign.css?v=' . now()->timestamp) }}" rel="stylesheet" />
  <link href="{{ url('/').'/'.asset('assets/css/custom1.css?v=' . now()->timestamp) }}" rel="stylesheet" />
  <!-- CSS Just for demo purpose, don't include it in your project -->
  <link href="{{ url('/').'/'.asset('assets/demo/demo.css??') }}" rel="stylesheet" />
  <!-- <link href="{{ url('/').'/'.asset('assets/css/jquery-ui.css') }}" rel="stylesheet" /> -->
  <link href="{{ url('/').'/'.asset('assets/css/responsive.bootstrap4.css??') }}" rel="stylesheet" />
  <link rel="stylesheet" href="{{ url('/').'/'.asset('assets/plugins/select2/css/select2.css?v=' . now()->timestamp) }}">
  <link href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" rel="stylesheet">
  <script src="{{ url('/').'/'.asset('assets/js/core/jquery.min.js') }}"></script>
  <script src="{{ url('/').'/'.asset('assets/js/core/jquery-ui.js') }}"></script>
  <script src="{{ url('/').'/'.asset('assets/js/plugins/moment.min.js') }}"></script>
  <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
  <meta http-equiv="Cache-Control" content="no-store" />
  <style>
    .main-panel>.navbar {
      background: linear-gradient(45deg, #3694cc 0%, #3860a4 100%);
      padding-top: 7px;
    }

    /* Google Font Import - Poppins */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    :root {
      /* ===== Colors ===== */
      --body-color: #E4E9F7;
      --sidebar-color: #FFF;
      --primary-color: #3860a4;
      --primary-color-light: #F6F5FF;
      --toggle-color: #DDD;
      --text-color: #707070;

      /* ====== Transition ====== */
      --tran-03: all 0.2s ease;
      --tran-03: all 0.3s ease;
      --tran-04: all 0.3s ease;
      --tran-05: all 0.3s ease;
    }

    body {
      min-height: 100vh;
      background-color: var(--body-color);
      transition: var(--tran-05);
    }

    ::selection {
      background-color: us color: #fff;
    }

    .sidebar li.nav-link a {
      /* background-color: var(--primary-color); */
      color: #707070;
      font-weight: 500;
    }


    .sidebar li.nav-link:hover a {
      /* background-color: var(--primary-color); */
      color: #fff;
    }

    .sidebar li.nav-link ul.navd a {
      background: transparent;
      color: #fff;
      font-weight: 500;
      font-size: 16px;
    }

    .dropdown-menu .dropdown-item,
    .dropdown-menu li>a {
      padding: 5px !important;
      font-weight: 700 !important;
    }

    body.dark {
      --body-color: #18191a;
      --sidebar-color: #242526;
      --primary-color: #3a3b3c;
      --primary-color-light: #3a3b3c;
      --toggle-color: #fff;
      --text-color: #ccc;
    }

    /* ===== Sidebar ===== */
    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      height: 100%;
      width: 250px;
      padding: 10px 14px;
      background: var(--sidebar-color);
      transition: var(--tran-05);
      z-index: 100;
      background: linear-gradient(45deg, #3860a4 0%, #3694cc 100%);
    }

    .sidebar.close {
      width: 88px;
    }

    .sidebar li.nav-link a:hover>a {
      color: #fff;
    }

    /* ===== Reusable code - Here ===== */
    .sidebar li.nav-link {
      /* height: 50px;*/
      list-style: none;
      /* display: flex; */
      align-items: center;
      margin-top: 10px;
      margin-bottom: 10px;
      padding: 0;
    }

    .sidebar header .image,
    .sidebar .icon {
      min-width: 50px;
      border-radius: 6px;
    }

    .sidebar .icon {
      min-width: 50px;
      border-radius: 6px;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
    }

    .sidebar .text,
    .sidebar .icon {
      /* color: #fff;*/
      /* transition: var(--tran-03);*/
    }

    .sidebar .text {
      font-size: 17px;
      font-weight: 400;
      white-space: nowrap;
      opacity: 1;
      color: #fff;
      padding-left: 8px;

    }

    .sidebar .bottom-content i {
      color: #fff;
    }

    .sidebar.close .text {
      opacity: 0;
    }

    /* =========================== */

    .sidebar header {
      position: relative;
    }

    .sidebar header .image-text {
      display: flex;
      align-items: center;
    }

    .sidebar header .logo-text {
      display: flex;
      flex-direction: column;
    }

    header .image-text .name {
      margin-top: 2px;
      font-size: 18px;
      font-weight: 600;
      color: #fff;
    }

    header .image-text .profession {
      font-size: 16px;
      margin-top: -2px;
      display: block;
    }

    .sidebar header .image {
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .sidebar header .image img {
      width: 40px;
      border-radius: 40px;
    }

    .sidebar header .toggle {
      position: absolute;
      top: 88%;
      right: -25px;
      transform: translateY(-50%) rotate(180deg);
      height: 25px;
      width: 25px;
      /* background-color: var(--primary-color); */
      background: #fff;
      color: #3860a4;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
      cursor: pointer;
      transition: var(--tran-05);
    }

    body.dark .sidebar header .toggle {
      color: var(--text-color);
    }

    .sidebar.close .toggle {
      transform: translateY(-50%) rotate(0deg);
    }

    /*  .sidebar .menu {
      margin-top: 20px;
    }*/

    .sidebar li.search-box {
      border-radius: 6px;
      background-color: var(--primary-color-light);
      cursor: pointer;
      transition: var(--tran-05);
    }

    .sidebar li.search-box input {
      height: 100%;
      width: 100%;
      outline: none;
      border: none;
      background-color: var(--primary-color-light);
      color: var(--text-color);
      border-radius: 6px;
      font-size: 17px;
      font-weight: 500;
      transition: var(--tran-05);
    }

    .sidebar li.nav-link a {
      list-style: none;
      height: 100%;
      background-color: transparent;
      display: flex;
      align-items: center;
      height: 100%;
      width: 100%;
      border-radius: 6px;
      text-decoration: none;
      transition: var(--tran-03);
      /*   border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;*/
      padding: 8px 0px;
      position: relative;
    }


    .sidebar li.nav-link ul li a:after {
      position: unset;
      display: none
    }

    .sidebar li.nav-link a:after {
      position: absolute;
      content: "";
      width: 10px;
      height: 10px;
      background-color: transparent;
      border: 3px solid #fff;
      right: 15px;
      top: 15px;
      border-top: 0px;
      border-left: 0px;
      transform: rotate(45deg);
    }

    .sidebar li.nav-link.single-menu a:after {
      display: none;
    }

    .sidebar li.nav-link a:hover {
      /* background-color: var(--primary-color);
*/
      color: #fff;
      background: linear-gradient(90deg, #3860a4 0%, #1b4e6c 100%);
    }

    .sidebar li.nav-link ul li a:hover .icon,
    .sidebar li.nav-link a:hover .text,
    .sidebar li.nav-link ul li a:hover span {
      /* color: var(--primary-color);*/
      color: #fff;
    }

    .sidebar li.nav-link ul li a:hover {
      background: #3860a4a3;
    }

    body.dark .sidebar li.nav-link a:hover .icon,
    body.dark .sidebar li.nav-link a:hover .text {
      color: var(--text-color);
    }



    .sidebar li.nav-link.active ul li.nav-link-btn.active .icon {
      color: #fff;
      font-weight: 500;
    }

    .sidebar .menu-bar {
      height: calc(100% - 162px);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      overflow-y: visible;
      margin-top: 20px;
      overflow-x: hidden;
    }

    .sidebar li.nav-link.active ul li .icon {
      color: #fff;
    }


    .sidebar .menu-bar::-webkit-scrollbar {
      width: 2px;
      background-color: transparent;
    }

    .sidebar .menu-bar::-webkit-scrollbar-thumb {
      border-radius: 2px;
      -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, .3);
      /*  background-color: var(--primary-color);*/
      background: linear-gradient(45deg, #3860a4 0%, #3694cc 100%);
    }

    /* .menu-bar::-webkit-scrollbar {
      display: none;
    }
*/
    .sidebar .menu-bar .mode {
      border-radius: 6px;
      background-color: var(--primary-color-light);
      position: relative;
      transition: var(--tran-05);
    }

    .menu-bar .mode .sun-moon {
      height: 50px;
      width: 60px;
    }

    .mode .sun-moon i {
      position: absolute;
    }

    .mode .sun-moon i.sun {
      opacity: 0;
    }

    body.dark .mode .sun-moon i.sun {
      opacity: 1;
    }

    body.dark .mode .sun-moon i.moon {
      opacity: 0;
    }

    .menu-bar .bottom-content .toggle-switch {
      position: absolute;
      right: 0;
      height: 100%;
      min-width: 60px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 6px;
      cursor: pointer;
    }

    .toggle-switch .switch {
      position: relative;
      height: 22px;
      width: 40px;
      border-radius: 25px;
      background-color: var(--toggle-color);
      transition: var(--tran-05);
    }

    .switch::before {
      content: '';
      position: absolute;
      height: 15px;
      width: 15px;
      border-radius: 50%;
      top: 50%;
      left: 5px;
      transform: translateY(-50%);
      background-color: var(--sidebar-color);
      transition: var(--tran-04);
    }

    body.dark .switch::before {
      left: 20px;
    }

    ..main-panel {
      position: absolute;
      top: 0;
      top: 0;
      left: 250px;
      height: 100vh;
      width: calc(100% - 250px);
      background-color: var(--body-color);
      transition: var(--tran-05);
    }

    .home .text {
      font-size: 30px;
      font-weight: 500;
      color: var(--text-color);
      padding: 12px 60px;
    }

    .sidebar.close~..main-panel {
      left: 78px;
      height: 100vh;
      width: calc(100% - 78px);
    }

    body.dark .home .text {
      color: var(--text-color);
    }

    .logo-main img {
      width: 100%;
    }


    .sidebar li.nav-link.active .text,
    .sidebar li.nav-link.active .icon {
      color: #fff;
    }

    /* li.nav-link a {
    color: #707070;
    font-weight: 500;
} */


    .sidebar li.nav-link.active a {
      background-color: var(--primary-color);
      color: #ffff;
      font-weight: 500;
    }

    ul.navd {
      background: transparent;
      height: 100%;
      z-index: 9;
      position: relative;
      padding: 10px 6px;
      border-bottom-left-radius: 6px;
      border-bottom-right-radius: 6px;
      /* box-shadow: 0 16px 38px -12px rgba(0, 0, 0, 0.56), 0 4px 25px 0px rgba(0, 0, 0, 0.12), 0 8px 10px -5px rgba(0, 0, 0, 0.2);*/

    }

    .sidebar li.nav-link a.collapsed {
      color: #fff;
      font-weight: 500;
      background: transparent;
    }


    .sidebar li.nav-link a {
      /* background-color: var(--primary-color);
    color: #ffff;
    font-weight: 500;*/
      background-color: transparent;
      color: #fff;
      font-weight: 600;
    }


    .sidebar li.nav-link a.collapsed:hover,
    .sidebar li.nav-link.active a.collapsed {
      background: linear-gradient(90deg, #3860a4 0%, #1b4e6c 100%);
      color: #ffff;
      font-weight: 500;
    }


    .sidebar li.nav-link.active a {
      background: linear-gradient(45deg, #3860a4 0%, #3694cc 100%);
    }


    .sidebar li.nav-link.active ul.navd li.nav-link-btn.active a {
      background: #3860a4a3;
      color: #fff;
      font-weight: 500;

    }

    ul.navd li.nav-link-btn {
      margin: 0px 0;
    }

    .sidebar.close li.nav-link a:after {

      width: 5px;
      height: 5px;
    }

    /* .sidebar.close~.main-panel {
    left: 78px;
    height: 100vh;
    width: calc(100% - 78px);
}
.main-panel {

    position: absolute;
    top: 0;
    top: 0;
    left: 250px;
    height: 100vh;
    width: calc(100% - 250px);
    background-color: var(--body-color);
    transition: var(--tran-05);
} */

    body .main-panel {
      position: absolute;
      top: 0;
      float: unset;
      left: 250px;
      height: 100vh !important;
      width: calc(100% - 250px);
      max-height: unset !important;
    }


    body .sidebar.close~.main-panel {
      left: 87px;
      height: 100vh;
      width: calc(100% - 87px);
    }

    .sidebar {
      z-index: 9999;
    }

    li {
      list-style: none;
    }

    /*ul.navd li.nav-link-btn.active, ul.navd li.nav-link-btn:hover {
    background: #00000094;
    border-radius: 8px;
}
*/
    nav.sidebar.close {
      opacity: 1;
    }


    nav.sidebar.close .icon {
      min-width: 60px;
    }

    nav.sidebar.close .menu-links span {
      display: none;
    }

    nav.sidebar.close li.nav-link a:after {
      right: 5px;
      top: 12px;
    }



    nav.sidebar.close .menu-bar {
      height: calc(100% - 106px);
      overflow-y: clip;
      margin-top: 20px;
      overflow-x: visible;
    }

    nav.sidebar.close .bottom-content {
      text-align: center;
    }


    nav.sidebar.close .bottom-content span.text.nav-text {
      display: none;
    }



    nav.sidebar.close ul.navd {
      padding: 6px 0px;

    }



    nav.sidebar .bottom-content li a {
      background: linear-gradient(45deg, #3860a4 0%, #3694cc 100%);
      color: #fff;
      padding: 10px 18px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    nav.sidebar .mobile {
      display: none;
    }

    nav.sidebar.close .mobile {
      display: block;
    }

    .logo-main.mobile img {
      width: 39px;
      height: 100%;
      object-fit: contain;
    }

    nav.sidebar.close .desktop {
      display: none;
    }

    nav.sidebar.close li.nav-link a.hoveradd,
    nav.sidebar.close li.nav-link ul a.hoveradd2 {
      position: relative;
    }


    /*tablet css*/

    @media (max-width: 996px) {
      body nav.sidebar {
        /* display: none !important; */
      }

      body .main-panel {
        position: relative;
        top: 0;
        float: unset;
        left: 0;
        width: calc(100% - 0px);

      }
    }

    nav.sidebar.close li.nav-link .d-none.mobile_hide {
      background: linear-gradient(45deg, #3860a4 0%, #3694cc 100%);
      color: #ffff;
      font-weight: 300;
      font-size: 13px;
      padding: 10px 10px;
      border-radius: 12px;
      position: absolute;
      right: -293%;
      top: 6%;
      z-index: 9999999;
      border-top-left-radius: 0;
      border-bottom-left-radius: 0;
      transition: var(--tran-05);
      width: 100%;
      min-width: 161px
    }

    body nav.sidebar.close li.nav-link a.hoveradd:hover .d-none.mobile_hide,
    body nav.sidebar.close li.nav-link ul li a.hoveradd2:hover .d-none.mobile_hide {
      display: block !important;
    }
  </style>
  <!-- Scripts -->
</head>

<body class="" style="background-color: #f2fbff;">
  <!-- Loader -->
  <div class="loader-container" id="loader">
    <div class="loader"></div>
  </div>
  <div class="wrapper">


    <nav class="sidebar">
      <header>
        <div class="logo rounded"><a href="{{ url('customers') }}" class="simple-text logo-normal">
            <!-- GAJRA GEARS -->
            <div class="logo-main desktop">
              <img src="{{ url('/').'/'.asset('assets/img/brand_logo.png') }}" class="rounded" alt="...">
            </div>

            <div class="logo-main mobile">
              <img src="{{ url('/').'/'.asset('assets/img/mlogo.ico') }}" class="rounded" alt="...">
            </div>

          </a>
        </div>
        <div class="image-text mt-2">
          <span class="image">
            <img
              src="{!! (count(Auth::user()->getMedia('profile_image')) > 0 ? Auth::user()->getMedia('profile_image')[0]->getFullUrl() : asset('assets/img/profileuser.png?')) !!}"
              alt="">
          </span>
          <div class="text logo-text">
            <span class="name"> {!! Auth::user()->name !!}</span>

          </div>
        </div>
        <i class='bx bx-chevron-right toggle'></i>
      </header>
      <div class="menu-bar">
        <div class="menu">

          <ul class="menu-links">
            @if(auth()->user()->can(['dashboard_access']))
            <li class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
              <a href="#" href="{{ url('dashboard') }}">
                <i class="material-icons">store</i>
                <span class="text nav-text">{!! trans('panel.sidemenu.dashboard') !!}</span>
              </a>
            </li>
            @endif

            @if(auth()->user()->can(['customer_access']))
            <li
              class="nav-link {{ request()->is('customers*') || request()->is('customertype*') || request()->is('firmtype*') || request()->is('customersLogin*') || request()->is('customers-survey*') || request()->is('fields*') || request()->is('customer_outstanting*') ? 'active' : '' }}">
              <a class="collapsed hoveradd" data-toggle="collapse" href="#customerMenu" aria-expanded="false">
                <i class="material-icons icon">contact_emergency</i>
                <span> Distributor

                </span>
                <div class="d-none mobile_hide"> Distributor

                </div>
              </a>

              <div class="collapse {{ request()->is('customers*') || request()->is('customertype*') || request()->is('firmtype*') || request()->is('customer_outstanting*') ? 'show' : '' }}" id="customerMenu" style="">
                <ul class="navd">
                  @if(auth()->user()->can(['customer_access']))
                  <li class="nav-link-btn {{ request()->is('customers*') ? 'active' : '' }}">
                    <a class="hoveradd2" href="{{ url('customers') }}">
                      <i class="material-icons icon">diversity_3</i>
                      <span>{!! trans('panel.sidemenu.customers') !!}</span>
                      <div class="d-none mobile_hide">{!! trans('panel.sidemenu.customers') !!}

                      </div>
                    </a>

                  </li>
                  @endif

                  @if(auth()->user()->can(['distributor_access']))
                  <!-- <li class="nav-item {{ request()->is('distributors*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('distributors') }}">
                      <i class="material-icons">store</i>
                      <p>{!! trans('panel.sidemenu.distributors') !!}</p>
                    </a>
                  </li> -->
                  @endif

                  @if(auth()->user()->can('customertype_access'))
                  <li class="nav-link-btn {{ request()->is('customertype*') ? 'active' : '' }}" data-placement="top" title="{!! trans('panel.sidemenu.customertype') !!}">
                    <a class="hoveradd2" href="{{ url('customertype') }}">
                      <i class="material-icons icon">transcribe</i>
                      <span>{!! trans('panel.sidemenu.customertype') !!}</span>
                      <div class="d-none mobile_hide">{!! trans('panel.sidemenu.customertype') !!}

                      </div>
                    </a>
                  </li>
                  @endif

                  @if(auth()->user()->can('customer_outstanting'))
                  <li class="nav-link-btn {{ request()->is('customer_outstanting*') ? 'active' : '' }}">
                    <a class="hoveradd2" href="{{ url('customer_outstanting') }}">
                      <i class="material-icons icon">nature_people</i>
                      <span>Cutomer Outstanding</span>
                      <div class="d-none mobile_hide"> Cutomer Outstanding</div>
                    </a>
                  </li>
                  @endif



                  @if(auth()->user()->can('target_users_access'))
                  <li
                    class="nav-link-btn {{ request()->is('sales_users*') || request()->is('sales_dealer*') ? 'active' : '' }}" data-placement="top" title="{!! trans('panel.sidemenu.sales_users') !!}">
                    <a class="collapsed" data-toggle="collapse" href="#salesUserMenu" aria-expanded="false">
                      <i class="material-icons icon">store</i>
                      <span> {!! trans('panel.sidemenu.sales_users') !!} </span>
                    </a>
                    <div class="collapse" id="salesUserMenu" style="">
                      <ul class="navd">
                        @if(auth()->user()->can('target_users_access'))
                        <li class="nav-link-btn {{ request()->is('sales_users*') ? 'active' : '' }}">
                          <a class="" href="{{ url('sales_users/target_users') }}" data-placement="top" title="{!! trans('panel.sales_users.title') !!}">
                            <i class="material-icons icon">verified_user</i>
                            <span> {!! trans('panel.sales_users.title') !!}</span>
                          </a>
                        </li>
                        @endif
                        @if(auth()->user()->can('sales_target_dealers_access'))
                        <li class="nav-link-btn {{ request()->is('sales_dealer*') ? 'active' : '' }}" data-placement="top" title="{!! trans('panel.dealer_distributor_user.title') !!}">
                          <a class="" href="{{ url('sales_dealer/target_dealers') }}">
                            <i class="material-icons icon">verified_user</i>
                            <span> {!! trans('panel.dealer_distributor_user.title') !!}</span>
                          </a>
                        </li>
                        @endif
                        @if(auth()->user()->can('branch_wise_sales_target_access'))
                        <li class="nav-link-btn {{ request()->is('branches_sales_target') ? 'active' : '' }}" data-placement="top" title=" Branch Wise Sales Target">
                          <a class="" href="{{ url('branches_sales_target') }}">
                            <i class="material-icons icon">holiday_village</i>
                            <span> Branch Wise Sales Target</span>
                          </a>
                        </li>
                        @endif


                      </ul>
                    </div>
                  </li>
                  @endif



                </ul>
              </div>
            </li>
            @endif
            @if(auth()->user()->can('product_access'))
            <li

              class="nav-link {{ request()->is('categories*') || request()->is('subcategories*') || request()->is('brands*') || request()->is('products*') || request()->is('units*') || request()->is('production*') || request()->is('units*') || request()->is('plants*') || request()->is('stock*') ? 'active' : '' }}">
              <a class="collapsed hoveradd" data-toggle="collapse" href="#productMenu" aria-expanded="false">
                <i class="material-icons icon">conveyor_belt</i>
                <span> {!! trans('panel.sidemenu.product_master') !!}

                </span>
                <div class="d-none mobile_hide">{!! trans('panel.sidemenu.product_master') !!}

                </div>
              </a>
              <div class="collapse {{ request()->is('categories*') || request()->is('subcategories*') || request()->is('brands*') || request()->is('products*') || request()->is('units*') || request()->is('production*') || request()->is('units*') || request()->is('plants*') || request()->is('stock*') ? 'show' : '' }}" id="productMenu" style="">
                <ul class="navd">
                  @if(auth()->user()->can('subcategory_access'))
                  <li class="nav-link-btn {{ request()->is('subcategories*') ? 'active' : '' }}">
                    <a class="hoveradd2" href="{{ url('subcategories') }}">
                      <i class="material-icons icon">subtitles</i>
                      <span>{!! trans('panel.sidemenu.subcategories') !!}</span>
                      <div class="d-none mobile_hide">{!! trans('panel.sidemenu.subcategories') !!}

                      </div>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('product_access'))
                  <li class="nav-link-btn {{ request()->is('products*') ? 'active' : '' }}">
                    <a class="hoveradd2" href="{{ url('products') }}">
                      <i class="material-icons icon">widgets</i>
                      <span>{!! trans('panel.sidemenu.products') !!}</span>
                      <div class="d-none mobile_hide">{!! trans('panel.sidemenu.products') !!}

                      </div>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('brand_access'))
                  <li class="nav-link-btn {{ request()->is('brands*') ? 'active' : '' }}">
                    <a class="hoveradd2" href="{{ url('brands') }}">
                      <i class="material-icons icon">branding_watermark</i>
                      <span>{!! trans('panel.sidemenu.brands') !!}</span>
                      <div class="d-none mobile_hide">{!! trans('panel.sidemenu.brands') !!}

                      </div>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('gift_access'))
                  <!-- <li class="nav-item {{ request()->is('gifts*') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ url('gifts') }}">
                    <i class="material-icons">donut_large</i>
                    <p>{!! trans('panel.sidemenu.gifts') !!}</p>
                  </a>
                </li> -->
                  @endif
                  @if(auth()->user()->can('grade_access'))
                  <li class="nav-link-btn {{ request()->is('units*') ? 'active' : '' }}">
                    <a class="hoveradd2" href="{{ url('units') }}">
                      <i class="material-icons icon">apartment</i>
                      <span>Grade</span>
                      <div class="d-none mobile_hide">Grade

                      </div>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('unit_access'))
                  <li class="nav-link-btn {{ request()->is('plants*') ? 'active' : '' }}">
                    <a class="hoveradd2" href="{{ url('plants') }}">
                      <i class="material-icons icon">domain</i>
                      <span>Plants</span>
                      <div class="d-none mobile_hide">Plants

                      </div>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('category_access'))
                  <li class="nav-link-btn  {{ request()->is('categories*') ? 'active' : '' }}">
                    <a class="hoveradd2" href="{{ url('categories') }}">
                      <i class="material-icons icon">category</i>
                      <span>Size</span>
                      <div class="d-none mobile_hide">{!! trans('panel.sidemenu.categories') !!}

                      </div>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('stock_access'))
                  <li class="nav-link-btn {{ request()->is('stock*') ? 'active' : '' }}" data-placement="top" title="Stock">
                    <a class="" href="{{ url('stock') }}">
                      <i class="material-icons icon">donut_small</i>
                      <span>Stock</span>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('stockdetails_access'))
                  <!-- <li class="nav-item {{ request()->is('production*') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ url('production') }}">
                    <i class="material-icons">donut_small</i>
                    <p>Production</p>
                  </a>
                </li> -->
                  @endif
                </ul>
              </div>
            </li>
            @endif
            @if(auth()->user()->can('prices_access'))
            <li class="single-menu nav-link {{ request()->is('prices*') ? 'active' : '' }}">
              <a class="hoveradd" href="{{ url('prices/create') }}">
                <i class="material-icons icon">payments</i>
                <span>Prices</span>
                <div class="d-none mobile_hide">{!! trans('panel.sidemenu.prices') !!}

                </div>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('order_access'))
            <li class="nav-link {{ request()->is('orders*') ? 'active' : '' }}">
              <a class="collapsed hoveradd" data-toggle="collapse" href="#orderMenu" aria-expanded="false">
                <i class="material-icons icon">app_registration</i>
                <span> {!! trans('panel.sidemenu.orders') !!}

                </span>
                <div class="d-none mobile_hide">{!! trans('panel.sidemenu.orders') !!}

                </div>
              </a>
              <div class="collapse {{ request()->is('orders*') ? 'show' : '' }}" id="orderMenu" style="">
                <ul class="navd">
                  @if(auth()->user()->can('order_access'))
                  <li class="nav-link-btn {{ request()->is('orders') ? 'active' : '' }}">
                    <a class="hoveradd2" href="{{ url('orders') }}">
                      <i class="material-icons icon">list_alt</i>
                      <span>Booking</span>
                      <div class="d-none mobile_hide">Booking

                      </div>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('order_access'))
                  <li class="nav-link-btn {{ request()->is('orders_confirm') ? 'active' : '' }}">
                    <a class="hoveradd2" href="{{ url('orders_confirm') }}">
                      <i class="material-icons icon">app_registration</i>
                      <span>Final Orders</span>
                      <div class="d-none mobile_hide">Final Orders

                      </div>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('order_dispatch'))
                  <li class="nav-link-btn {{ request()->is('orders_dispatch') ? 'active' : '' }}">
                    <a class="hoveradd2" href="{{ url('orders_dispatch') }}">
                      <i class="material-icons icon">send_time_extension</i>
                      <span>Orders Dispatch</span>
                      <div class="d-none mobile_hide">Orders Dispatch

                      </div>
                    </a>
                  </li>
                  @endif
                </ul>
              </div>
            </li>
            @endif

            @if(auth()->user()->can('hr_access'))
            <li class="nav-link {{ request()->is('reports/attendancereport*') || request()->is('reports/attendancereportSummary*') || request()->is('holidays*') || request()->is('leaves*') || request()->is('appraisal*') || request()->is('sales_weightage*') || request()->is('users*') || request()->is('targets*') || request()->is('livelocation*') || request()->is('roles*') || request()->is('permissions*') || request()->is('tours*') || request()->is('usercity*') || request()->is('new-joinings*') || request()->is('reports/customervisit*') ? 'active' : '' }}">
              <a class="collapsed hoveradd" data-toggle="collapse" href="#hr" aria-expanded="false">
                <i class="material-icons icon">family_restroom</i>
                <span> {!! trans('panel.sidemenu.hr') !!}
                </span>
                <div class="d-none mobile_hide"> {!! trans('panel.sidemenu.hr') !!}</div>
              </a>
              <div class="collapse {{ request()->is('reports/attendancereport*') || request()->is('reports/attendancereportSummary*') || request()->is('holidays*') || request()->is('leaves*') || request()->is('appraisal*') || request()->is('sales_weightage*') || request()->is('users*') || request()->is('targets*') || request()->is('livelocation*') || request()->is('roles*') || request()->is('permissions*') || request()->is('tours*') || request()->is('usercity*') || request()->is('new-joinings*') || request()->is('reports/customervisit*') ? 'show' : '' }}" id="hr">
                <ul class="navd">
                  @if(auth()->user()->can('role_access'))
                  <li class="nav-link-btn {{ request()->is('roles*') ? 'active' : '' }}">
                    <a class="hoveradd2" href="{{ url('roles') }}">
                      <i class="material-icons icon">vertical_shades_closed</i>
                      <span>{!! trans('panel.sidemenu.roles') !!}</span>
                      <div class="d-none mobile_hide"> {!! trans('panel.sidemenu.roles') !!}</div>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('attendance_report'))
                  <li class="nav-link-btn {{ request()->is('reports/attendancereport') ? 'active' : '' }}">
                    <a class="hoveradd2" href="{{ url('reports/attendancereport') }}">
                      <i class="material-icons icon">report</i>
                      <span>Attendance </span>
                      <div class="d-none mobile_hide"> Attendance </div>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('attendance_summary_report'))
                  <li class="nav-link-btn {{ request()->is('reports/attendancereportSummary') ? 'active' : '' }}">
                    <a class="hoveradd2" href="{{ url('reports/attendancereportSummary') }}">
                      <i class="material-icons icon">summarize</i>
                      <span>Attendance Summary Report</span>
                      <div class="d-none mobile_hide">Attendance Summary Report</div>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('visit_report'))
                  <li class="nav-link-btn {{ request()->is('reports/customervisit*') ? 'active' : '' }}">
                    <a class="hoveradd2" href="{{ url('reports/customervisit') }}">
                      <i class="material-icons icon">dashboard_customize</i>
                      <span>Customer Visit</span>
                      <div class="d-none mobile_hide"> Customer Visit</div>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('holiday_access'))
                  <li class="nav-link-btn {{ request()->is('holidays*') ? 'active' : '' }}">
                    <a class="hoveradd2" href="{{ url('holidays') }}">
                      <i class="material-icons icon">holiday_village</i>
                      <span>Holidays</span>
                      <div class="d-none mobile_hide"> Holidays</div>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('leave_access'))
                  <li class="nav-link-btn {{ request()->is('leaves*') ? 'active' : '' }}">
                    <a class="hoveradd2" href="{{ url('leaves') }}">
                      <i class="material-icons icon">energy_savings_leaf</i>
                      <span>Leaves</span>
                      <div class="d-none mobile_hide"> Leaves</div>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('appraisal_pms'))
                  <li class="nav-link-btn {{ request()->is('appraisal*') ? 'active' : '' }}">
                    <a class="hoveradd2" href="{{ url('appraisal/index') }}">
                      <i class="material-icons icon">verified_user</i>
                      <span>Appraisal(PMS)</span>
                      <div class="d-none mobile_hide"> Appraisal(PMS)</div>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('sales_weightage'))
                  <li class="nav-link-btn {{ request()->is('sales_weightage*') ? 'active' : '' }}">
                    <a class="hoveradd2" href="{{ url('sales_weightage') }}">
                      <i class="material-icons icon">checkroom</i>
                      <span>{!! trans('panel.sales_weightage.title') !!}</span>
                      <div class="d-none mobile_hide"> {!! trans('panel.sales_weightage.title') !!}</div>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('branch'))
                  <li class="nav-link-btn {{ request()->is('branch*') ? 'active' : '' }}">
                    <a class="hoveradd2" href="{{ url('branches') }}">
                      <i class="material-icons icon">meeting_room</i>
                      <span>Branch</span>
                      <div class="d-none mobile_hide"> Branch</div>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('division'))
                  <li class="nav-link-btn {{ request()->is('division*') ? 'active' : '' }}">
                    <a class="hoveradd2" href="{{ url('division') }}">
                      <i class="material-icons icon">safety_divider</i>
                      <span>Division</span>
                      <div class="d-none mobile_hide"> Division</div>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('designation'))
                  <li class="nav-link-btn {{ request()->is('designation*') ? 'active' : '' }}">
                    <a class="hoveradd2" href="{{ url('designation') }}">
                      <i class="material-icons icon">shopping_bag</i>
                      <span>Designation</span>
                      <div class="d-none mobile_hide"> Designation</div>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('departments'))
                  <li class="nav-link-btn {{ request()->is('departments*') ? 'active' : '' }}">
                    <a class="hoveradd2" href="{{ url('departments') }}">
                      <i class="material-icons icon">local_fire_department</i>
                      <span>Departments</span>
                      <div class="d-none mobile_hide"> Departments</div>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('tasks_access'))
                  <li class="nav-link-btn {{ request()->is('tasks*') ? 'active' : '' }}">
                    <a class="hoveradd2" href="{{ url('tasks') }}">
                      <i class="material-icons icon">check_circle</i>
                      <span>{!! trans('panel.sidemenu.task') !!}</span>
                      <div class="d-none mobile_hide"> {!! trans('panel.sidemenu.task') !!}</div>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('user_access'))
                  <li class="nav-link-btn add_icon {{ request()->is('users*') || request()->is('targets*') || request()->is('livelocation*') || request()->is('roles*') || request()->is('permissions*') || request()->is('tours*') || request()->is('usercity*') || request()->is('new-joinings*') ? 'active' : '' }}">
                    <a class="hoveradd" data-toggle="collapse" href="#userMenu" aria-expanded="false">
                      <i class="material-icons icon">badge</i>
                      <span> {!! trans('panel.sidemenu.users_master') !!}
                      </span>
                      <div class="d-none mobile_hide"> {!! trans('panel.sidemenu.users_master') !!}</div>
                    </a>
                    <div class="collapse" id="userMenu" style="">
                      <ul class="navd">
                        <!--                 @if(auth()->user()->can('appraisal_pms'))
                                          <li class="nav-item {{ request()->is('appraisal/create') ? 'active' : '' }}">
                                            <a class="hoveradd2" href="{{ url('appraisal/index') }}">
                                              <i class="material-icons">verified_user</i>
                                              <p>Appraisal(PMS)</p>
                                            </a>
                                          </li>
                                          @endif
                                          @if(auth()->user()->can('sales_weightage'))
                                          <li class="nav-item {{ request()->is('sales_weightage') ? 'active' : '' }}">
                                            <a class="nav-link" href="{{ url('sales_weightage') }}">
                                              <i class="material-icons">check</i>
                                              <p>{!! trans('panel.sales_weightage.title') !!}</p>
                                            </a>
                                          </li>
                                          @endif -->
                        @if(auth()->user()->can('new_joining_access'))
                        <li class="nav-link-btn {{ request()->is('new-joinings*') ? 'active' : '' }}">
                          <a class="hoveradd2" href="{{ url('new-joinings') }}">
                            <i class="material-icons icon">verified_user</i>
                            <span>New Joining</span>
                            <div class="d-none mobile_hide"> New Joining</div>
                          </a>
                        </li>
                        @endif
                        @if(auth()->user()->can('user_access'))
                        <li class="nav-link-btn {{ request()->is('users*') ? 'active' : '' }}">
                          <a class="hoveradd2" href="{{ url('users') }}">
                            <i class="material-icons icon">assignment_ind</i>
                            <span>{!! trans('panel.sidemenu.users') !!}</span>
                            <div class="d-none mobile_hide"> {!! trans('panel.sidemenu.users') !!}</div>
                          </a>
                        </li>
                        @endif
                        @if(auth()->user()->can('user_app_details_access'))
                        <li class="nav-link-btn {{ request()->is('user_app_details*') ? 'active' : '' }}">
                          <a class="hoveradd2" href="{{ url('user_app_details') }}">
                            <i class="material-icons icon">details</i>
                            <span>User App details</span>
                            <div class="d-none mobile_hide"> User App details</div>
                          </a>
                        </li>
                        @endif
                        @if(auth()->user()->can('target_access'))
                        <li class="nav-link-btn {{ request()->is('targets*') ? 'active' : '' }}">
                          <a class="hoveradd2" href="{{ url('targets') }}">
                            <i class="material-icons icon">loupe</i>
                            <span>User Target</span>
                            <div class="d-none mobile_hide">User Target</div>
                          </a>
                        </li>
                        @endif
                        @if(auth()->user()->can('user_location'))
                        <li class="nav-link-btn {{ request()->is('livelocation*') ? 'active' : '' }}">
                          <a class="hoveradd2" href="{{ url('livelocation') }}">
                            <i class="material-icons icon">share_location</i>
                            <span>Sales team activities</span>
                            <div class="d-none mobile_hide"> Sales team activities</div>
                          </a>
                        </li>
                        @endif
                        @if(auth()->user()->can('tours'))
                        <li class="nav-link-btn {{ request()->is('tours*') ? 'active' : '' }}">
                          <a class="nav-link" href="{{ url('tours') }}">
                            <i class="material-icons icon">tour</i>
                            <span>Tours</span>
                            <div class="d-none mobile_hide">Tours</div>
                          </a>
                        </li>
                        @endif
                        @if(auth()->user()->can('city_assigned'))
                        <li class="nav-link-btn {{ request()->is('usercity*') ? 'active' : '' }}">
                          <a class="hoveradd2" href="{{ url('usercity') }}">
                            <i class="material-icons icon">location_city</i>
                            <span>City Assigned</span>
                            <div class="d-none mobile_hide">City Assigned</div>
                          </a>
                        </li>
                        @endif
                      </ul>
                    </div>
                  </li>
                  @endif
                </ul>
              </div>
            </li>
            @endif

          </ul>
        </div>
        <!--   <div class="bottom-content">
          <li class="">
            <a href="#">
              <i class='bx bx-log-out'></i>
              <span class="text nav-text">Logout</span>
            </a>
          </li>

        </div> -->
      </div>
    </nav>





    <div class="main-panel">
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top ">
        <div class="container-fluid  p-2" style="background: transparent; !important">
          <img class="rounded ml-2" src="{!! url('/').'/'.asset('assets/img/mini_logo.png?') !!}" width="60">
          <!-- <img src="{!! url('/').'/'.asset('assets/img/logo.png') !!}" width="50"> -->
          <div class="navbar-wrapper">
            <div class="navbar-minimize">
              <!--               <button id="minimizeSidebar" class="btn btn-just-icon btn-white btn-fab btn-round">
                <i class="material-icons text_align-center visible-on-sidebar-regular">more_vert</i>
                <i class="material-icons design_bullet-list-67 visible-on-sidebar-mini">view_list</i>
              <div class="ripple-container"></div></button> -->
            </div>
          </div>
          <button class="navbar-toggler gk" type="button" data-toggle="collapse" aria-controls="navigation-index"
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="sr-only">Toggle navigation</span>
            <span class="navbar-toggler-icon icon-bar"></span>
            <span class="navbar-toggler-icon icon-bar"></span>
            <span class="navbar-toggler-icon icon-bar"></span>
          </button>
          @auth
          <div class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav">
              <li class="nav-item dropdown notifications_section">
                <a class="nav-link" href="http://example.com" id="navbarDropdownMenuLink" data-toggle="dropdown"
                  aria-haspopup="true" aria-expanded="false">
                  <i class="material-icons">notifications</i>
                  <!-- <span class="notification">5</span> -->
                  <p class="d-lg-none d-md-block">
                    Some Actions
                  </p>
                </a>
                <div class="dropdown-menu dropdown-menu-right pt-0 " aria-labelledby="navbarDropdownMenuLink">
                  <h4 class="card-title  notifications_bg">Alerts</h4>
                  <!-- <hr> -->
                  <div class="icon_list">
                    @foreach(getAllNotification() as $k=>$val)
                    <a class="dropdown-item notifications_icon" href="#"><span>{{$k+1}}) {{ $val->data}}</span>
                      <div class="clock_icon">
                        <i style="font-size: 16px !important;" class="material-icons pr-2">schedule</i>{{date('d-M-Y h:i A', strtotime($val->created_at))}}
                      </div>
                    </a>
                    <!-- <hr> -->
                    @endforeach
                  </div>
                </div>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link" href="javascript:;" id="navbarDropdownProfile" data-toggle="dropdown"
                  aria-haspopup="true" aria-expanded="false">
                  <i class="material-icons">person</i>
                  <p class="d-lg-none d-md-block">
                    Account
                  </p>
                </a>
                @auth
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownProfile">
                  <a class="dropdown-item" href="{{ url('change-password') }}">Change Password</a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="{{ url('logout') }}">Log out</a>
                </div>
                @endauth
              </li>
            </ul>
          </div>
          @endauth
        </div>
      </nav>
      <!-- End Navbar -->
      <div class="content">
        <div class="container-fluid">
          {{ $slot }}
        </div>
      </div>
      <footer class="footer">
        <div class="baseurl" data-baseurl="{{ url('/')}}"></div>
        <div class="token" data-token="{{ csrf_token() }}"></div>
        <div class="container-fluid">
          <nav class="float-left">

          </nav>
          <div class="copyright float-right">
          </div>
        </div>
      </footer>
    </div>
  </div>
  <div class="modal fade bd-example-modal-lg" id="previewimageInModel" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content card">
        <div class="card-header">
          <span class="pull-right">
            <a href="javascript:void(0)" class="btn btn-just-icon btn-danger" data-dismiss="modal"><i
                class="material-icons icon">clear</i></a>
          </span>
        </div>
        <div class="modal-body"> <img class="modal-content" id="img01"> </div>
      </div>
    </div>
  </div>

  <script src="{{ url('/').'/'.asset('assets/js/core/jquery.validate.js') }}"></script>
  <!-- Bootstrap -->
  <script src="{{ url('/').'/'.asset('assets/js/core/popper.min.js') }}"></script>
  <!-- overlayScrollbars -->
  <script src="{{ url('/').'/'.asset('assets/js/core/bootstrap-material-design.min.js') }}"></script>
  <!-- DataTables -->
  <script src="{{ url('/').'/'.asset('assets/js/plugins/jquery.dataTables.min.js') }}"></script>
  <script src="{{ url('/').'/'.asset('assets/js/plugins/dataTables.responsive.min.js') }}"></script>
  <script src="{{ url('/').'/'.asset('assets/js/plugins/bootstrap-tagsinput.js') }}"></script>

  <!-- OPTIONAL SCRIPTS -->
  <!-- Select2 -->
  <script src="{{ url('/').'/'.asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>

  <script src="{{ url('/').'/'.asset('assets/js/plugins/perfect-scrollbar.jquery.min.js') }}"></script>
  <script src="{{ url('/').'/'.asset('assets/js/plugins/sweetalert2.js') }}"></script>
  <script src="{{ url('/').'/'.asset('assets/js/plugins/jquery.validate.min.js') }}"></script>
  <!-- jquery-validation -->
  <script src="{{ url('/').'/'.asset('assets/js/plugins/jquery.bootstrap-wizard.js') }}"></script>
  <script src="{{ url('/').'/'.asset('assets/js/plugins/bootstrap-selectpicker.js') }}"></script>
  <script src="{{ url('/').'/'.asset('assets/js/plugins/bootstrap-datetimepicker.min.js') }}"></script>
  <!-- OPTIONAL SCRIPTS -->

  <script src="{{ url('/').'/'.asset('assets/js/plugins/chartist.min.js') }}"></script>

  <script src="{{ url('/').'/'.asset('assets/js/plugins/bootstrap-notify.js') }}"></script>

  <script src="{{ url('/').'/'.asset('assets/js/material-dashboard.js?v=2.1.2') }}"></script>

  <script src="{{ url('/').'/'.asset('assets/demo/demo.js') }}"></script>
  <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css"
    integrity="sha512-hievggED+/IcfxhYRSr4Auo1jbiOczpqpLZwfTVL/6hFACdbI3WQ8S9NCX50gsM9QVE+zLk/8wb9TlgriFbX+Q=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"
    integrity="sha512-F636MAkMAhtTplahL9F6KmTfxTmYcAcjcCkyu0f0voT3N/6vzAuJ4Num55a0gEJ+hRLHhdz3vDvZpf6kqgEa5w=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script>
    $(function() {
      $('#toggle-one').bootstrapToggle();
      $('.datetimepicker').datetimepicker({
        format: 'YYYY-MM-DD HH:mm'
      });
      $(".datepicker").datepicker({
        createButton: false,
        displayClose: true,
        closeOnSelect: false,
        selectMultiple: true,
        dateFormat: 'yy-mm-dd',
        beforeShow: function(input) {
          $(input).css({
            "position": "relative",
            "z-index": 999999
          });
        },
        onClose: function() {
          $('.ui-datepicker').css({
            'z-index': 0
          });
        }
      });

      //Initialize Select2 Elements
      $('.select2').select2()

      //Initialize Select2 Elements
      $('.select2bs4').select2({
        theme: 'bootstrap4'
      })
      $('.timepicker').datetimepicker({
        format: 'HH:mm'
      });
    })
    $('body').on('click', '.imageDisplayModel', function() {
      var imgPath = $(this).attr("src");
      var modal = document.getElementById('previewimageInModel');
      $('#previewimageInModel').modal('show');
      document.getElementById("img01").src = imgPath;
    });

    document.addEventListener("DOMContentLoaded", function() {
      document.getElementById("loader").style.display = "block";
      document.querySelector(".content").style.display = "none";
    });

    // Hide loader and show content when the page is fully loaded
    window.addEventListener("load", function() {
      document.getElementById("loader").style.display = "none";
      document.querySelector(".content").style.display = "block";
    });
  </script>


  <!--  <script>
    $(document).ready(function(){
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
    });
</script> -->

  <script>
    const body = document.querySelector('body'),
      sidebar = body.querySelector('nav'),
      toggle = body.querySelector(".toggle"),
      // searchBtn = body.querySelector(".search-box"),
      modeSwitch = body.querySelector(".toggle-switch"),
      modeText = body.querySelector(".mode-text");
    toggle.addEventListener("click", () => {
      sidebar.classList.toggle("close");
    })
    // searchBtn.addEventListener("click", () => {
    //   sidebar.classList.remove("close");
    // })
    // modeSwitch.addEventListener("click", () => {
    //   body.classList.toggle("dark");

    //   if (body.classList.contains("dark")) {
    //     modeText.innerText = "Light mode";
    //   } else {
    //     modeText.innerText = "Dark mode";

    //   }
    // });
  </script>

</body>

</html>