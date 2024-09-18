<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>
 <!--  <link href="{{ url('/').'/'.asset('assets/css/testdash2.css') }}" rel="stylesheet" /> -->
  <link href="{{ url('/').'/'.asset('assets/css/material-dashboard2.css') }}" rel="stylesheet" />
  <link rel="stylesheet" type="text/css"
    href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
  <!-- CSS Files -->

  <link href="{{ url('/').'/'.asset('assets/css/new_design.css') }}" rel="stylesheet" />
  <link href="{{ url('/').'/'.asset('assets/css/custom1.css') }}" rel="stylesheet" />
  <!-- CSS Just for demo purpose, don't include it in your project -->
  <link href="{{ url('/').'/'.asset('assets/demo/demo.css') }}" rel="stylesheet" />
  <!-- <link href="{{ url('/').'/'.asset('assets/css/jquery-ui.css') }}" rel="stylesheet" /> -->
  <link href="{{ url('/').'/'.asset('assets/css/responsive.bootstrap4.css') }}" rel="stylesheet" />
  <link rel="stylesheet" href="{{ url('/').'/'.asset('assets/plugins/select2/css/select2.css') }}">
  <link href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" rel="stylesheet">
  <script src="{{ url('/').'/'.asset('assets/js/core/jquery.min.js') }}"></script>
  <script src="{{ url('/').'/'.asset('assets/js/core/jquery-ui.js') }}"></script>
  <script src="{{ url('/').'/'.asset('assets/js/plugins/moment.min.js') }}"></script>
  <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
  <meta http-equiv="Cache-Control" content="no-store" />
  <style>
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
      color: #707070;
    }

    .sidebar li.nav-link ul.navd a {
      background: transparent;
      color: #00000094;
      font-weight: 500;
      font-size: 16px;
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
      transition: var(--tran-03);
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
      color: #707070;
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
      border-radius: 6px;
    }

    .sidebar header .toggle {
      position: absolute;
      top: 50%;
      right: -25px;
      transform: translateY(-50%) rotate(180deg);
      height: 25px;
      width: 25px;
      /*background-color: var(--primary-color);*/
      background: linear-gradient(45deg, #3860a4 0%, #3694cc 100%);
      color: var(--sidebar-color);
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

    .sidebar li.nav-link a:hover {
      /* background-color: var(--primary-color);
*/
      color: #fff;
      background: linear-gradient(45deg, #3860a4 0%, #3694cc 100%);
    }

    .sidebar li.nav-link ul li a:hover .icon,
    .sidebar li.nav-link a:hover .text,
    .sidebar li.nav-link ul li a:hover span {
      color: var(--primary-color);
    }

    body.dark .sidebar li.nav-link a:hover .icon,
    body.dark .sidebar li.nav-link a:hover .text {
      color: var(--text-color);
    }



    .sidebar li.nav-link.active ul li.nav-link-btn.active .icon {
      color: #3860a4;
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
      color: #00000094;
    }


    .sidebar .menu-bar::-webkit-scrollbar {
      width: 2px;
      background-color: transparent;
    }

    .sidebar .menu-bar::-webkit-scrollbar-thumb {
      border-radius: 2px;
      -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, .3);
      /*	background-color: var(--primary-color);*/
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
      color: #707070;
      font-weight: 500;
      background: transparent;
    }


    .sidebar li.nav-link a {
      /* background-color: var(--primary-color);
    color: #ffff;
    font-weight: 500;*/
      background-color: transparent;
      color: #00000094;
      font-weight: 600;
    }


    .sidebar li.nav-link a.collapsed:hover,
    .sidebar li.nav-link.active a.collapsed {
      background: linear-gradient(45deg, #3860a4 0%, #3694cc 100%);
      color: #ffff;
      font-weight: 500;
    }


    .sidebar li.nav-link.active a {
      background: linear-gradient(45deg, #3860a4 0%, #3694cc 100%);
    }


    .sidebar li.nav-link.active ul.navd li.nav-link-btn.active a {
      background: transparent;
      color: #3860a4;
      font-weight: 500;

    }

    ul.navd li.nav-link-btn {
      margin: 0px 0;
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
      left: 78px;
      height: 100vh;
      width: calc(100% - 78px);
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

nav.sidebar.close .menu-links span
{
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

nav.sidebar.close  li.nav-link a.hoveradd   ,
nav.sidebar.close  li.nav-link ul  a.hoveradd2 {
  position: relative;
}


    /*tablet css*/

    @media (max-width: 996px) {
      body nav.sidebar {
        display: none !important;
      }

      body .main-panel {
        position: relative;
        top: 0;
        float: unset;
        left: 0;
        width: calc(100% - 0px);

      }
    }
nav.sidebar.close  li.nav-link .d-none.mobile_hide{
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

body nav.sidebar.close  li.nav-link a.hoveradd:hover    .d-none.mobile_hide ,body nav.sidebar.close  li.nav-link ul li a.hoveradd2:hover    .d-none.mobile_hide{
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
        <div class="logo"><a href="{{ url('customers') }}" class="simple-text logo-normal">
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
              src="{!! (count(Auth::user()->getMedia('profile_image')) > 0 ? Auth::user()->getMedia('profile_image')[0]->getFullUrl() : asset('assets/img/placeholder.jpg')) !!}"
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
              class="nav-link {{ request()->is('customers*') || request()->is('customertype*') || request()->is('firmtype*') || request()->is('customersLogin*') || request()->is('customers-survey*') || request()->is('fields*') ? 'active' : '' }}">
              <a class="collapsed hoveradd" data-toggle="collapse" href="#customerMenu" aria-expanded="false">
                <i class="material-icons icon">contact_emergency</i>
                <span> {!! trans('panel.sidemenu.customers_master') !!}

                </span>
                  <div class="d-none mobile_hide"> {!! trans('panel.sidemenu.customers_master') !!}

                </div>
              </a>
               
              <div class="collapse {{ request()->is('customers*') || request()->is('customertype*') || request()->is('firmtype*') ? 'show' : '' }}" id="customerMenu" style="">
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
            
              class="nav-link {{ request()->is('categories*') || request()->is('subcategories*') || request()->is('brands*') || request()->is('products*') || request()->is('units*') || request()->is('production*') ? 'active' : '' }}">
              <a class="collapsed hoveradd2" data-toggle="collapse" href="#productMenu" aria-expanded="false">
                <i class="material-icons icon">conveyor_belt</i>
                <span> {!! trans('panel.sidemenu.product_master') !!}

                </span>
                 <div class="d-none mobile_hide">{!! trans('panel.sidemenu.product_master') !!}

                </div>
              </a>
              <div class="collapse {{ request()->is('categories*') || request()->is('subcategories*') || request()->is('brands*') || request()->is('products*') || request()->is('units*') || request()->is('production*') ? 'show' : '' }}" id="productMenu" style="">
                <ul class="navd">
                  @if(auth()->user()->can('category_access'))
                  <li class="nav-link-btn  {{ request()->is('categories*') ? 'active' : '' }}" >
                    <a class="hoveradd2" href="{{ url('categories') }}">
                      <i class="material-icons icon">category</i>
                      <span>{!! trans('panel.sidemenu.categories') !!}</span>
                        <div class="d-none mobile_hide">{!! trans('panel.sidemenu.categories') !!}

                </div>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('subcategory_access'))
                  <li class="nav-link-btn {{ request()->is('subcategories*') ? 'active' : '' }}" >
                    <a class="hoveradd2" href="{{ url('subcategories') }}">
                      <i class="material-icons icon">subtitles</i>
                      <span>{!! trans('panel.sidemenu.subcategories') !!}</span>
                        <div class="d-none mobile_hide">{!! trans('panel.sidemenu.subcategories') !!}

                </div>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('brand_access'))
                  <li class="nav-link-btn {{ request()->is('brands*') ? 'active' : '' }}" >
                    <a class="hoveradd2" href="{{ url('brands') }}">
                      <i class="material-icons icon">branding_watermark</i>
                      <span>{!! trans('panel.sidemenu.brands') !!}</span>
                        <div class="d-none mobile_hide">{!! trans('panel.sidemenu.brands') !!}

                </div>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('product_access'))
                  <li class="nav-link-btn {{ request()->is('products*') ? 'active' : '' }}" >
                    <a class="hoveradd2" href="{{ url('products') }}">
                      <i class="material-icons icon">widgets</i>
                      <span>{!! trans('panel.sidemenu.products') !!}</span>
                        <div class="d-none mobile_hide">{!! trans('panel.sidemenu.products') !!}

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
                  @if(auth()->user()->can('unit_access'))
                  <li class="nav-link-btn {{ request()->is('units*') ? 'active' : '' }}" >
                    <a class="hoveradd2" href="{{ url('units') }}">
                      <i class="material-icons icon">apartment</i>
                      <span>{!! trans('panel.sidemenu.units') !!}</span>
                        <div class="d-none mobile_hide">{!! trans('panel.sidemenu.units') !!}

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
        <div class="container-fluid bg-theme p-2" style="background: #fff !important">
          <img src="{!! url('/').'/'.asset('assets/img/mini_logo.png') !!}" width="250">
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
              <li class="nav-item dropdown">
                <a class="nav-link" href="http://example.com" id="navbarDropdownMenuLink" data-toggle="dropdown"
                  aria-haspopup="true" aria-expanded="false">
                  <i class="material-icons">notifications</i>
                  <span class="notification">5</span>
                  <p class="d-lg-none d-md-block">
                    Some Actions
                  </p>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                  @if(auth()->user()->can('visitor_log_access'))
                  <a href="{{route('visitor')}}" class="btn btn-info btn-sm float-right">Visitor Logs</a>
                  @endif
                  <a class="dropdown-item" href="#">Mike John responded to your email</a>
                  <a class="dropdown-item" href="#">You have 5 new tasks</a>
                  <a class="dropdown-item" href="#">You're now friend with Andrew</a>
                  <a class="dropdown-item" href="#">Another Notification</a>
                  <a class="dropdown-item" href="#">Another One</a>
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