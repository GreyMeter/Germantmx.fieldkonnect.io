<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
  <!-- CSS Files -->
  <link href="{{ url('/').'/'.asset('assets/css/material-dashboard2.css') }}" rel="stylesheet" />
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
  <meta http-equiv="Cache-Control" content="no-store" />
  <style>
    .toggle.ios,
    .toggle-on.ios,
    .toggle-off.ios {
      border-radius: 20px;
    }

    .toggle.ios .toggle-handle {
      border-radius: 20px;
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
    <div class="sidebar" data-color="yellow" data-background-color="black">
      <div class="logo"><a href="{{ url('dashboard') }}" class="simple-text logo-normal">
          <!-- GAJRA GEARS -->
          <div class="text-center logo-main">
            <img width="250" src="{{ url('/').'/'.asset('assets/img/brand_logo.png') }}" class="rounded" alt="...">
          </div>

        </a>
      </div>
      <div class="sidebar-wrapper">
        <div class="user">
          <div class="photo">
            <img src="{!! (count(Auth::user()->getMedia('profile_image')) > 0 ? Auth::user()->getMedia('profile_image')[0]->getFullUrl() : asset('assets/img/placeholder.jpg')) !!}">
          </div>
          <div class="user-info">
            <a data-toggle="collapse" href="#collapseExample" class="username">
              <span>
                {!! Auth::user()->name !!}
              </span>
            </a>
          </div>
        </div>
        <ul class="nav">
          @if(auth()->user()->can(['dashboard_access']))
          <li class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ url('dashboard') }}">
              <i class="material-icons">dashboard</i>
              <p>{!! trans('panel.sidemenu.dashboard') !!}</p>
            </a>
          </li>
          @endif
          @if(auth()->user()->can(['customer_access']))
          <li class="nav-item {{ request()->is('customers*') || request()->is('customertype*') || request()->is('firmtype*') || request()->is('customersLogin*') || request()->is('customers-survey*') || request()->is('fields*') ? 'active' : '' }}">
            <a class="nav-link collapsed" data-toggle="collapse" href="#customerMenu" aria-expanded="false">
              <i class="material-icons">store</i>
              <p> {!! trans('panel.sidemenu.customers_master') !!}

              </p>
            </a>
            <div class="collapse" id="customerMenu" style="">
              <ul class="nav">
                @if(auth()->user()->can(['customer_access']))
                <li class="nav-item {{ request()->is('customers*') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ url('customers') }}">
                    <i class="material-icons">store</i>
                    <p>{!! trans('panel.sidemenu.customers') !!}</p>
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
                <li class="nav-item {{ request()->is('customertype*') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ url('customertype') }}">
                    <i class="material-icons">library_books</i>
                    <p>{!! trans('panel.sidemenu.customertype') !!}</p>
                  </a>
                </li>
                @endif
                @if(auth()->user()->can('firmtype_access'))
                <li class="nav-item {{ request()->is('firmtype*') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ url('firmtype') }}">
                    <i class="material-icons">bubble_chart</i>
                    <p>{!! trans('panel.sidemenu.firmtype') !!}</p>
                  </a>
                </li>
                @endif
                @if(auth()->user()->can('customer_login'))
                <li class="nav-item {{ request()->is('customersLogin*') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ url('customersLogin') }}">
                    <i class="material-icons">location_ons</i>
                    <p>{!! trans('panel.sidemenu.customersLogin') !!}</p>
                  </a>
                </li>
                @endif
                @if(auth()->user()->can('survey_access'))
                <li class="nav-item {{ request()->is('customers-survey*') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ url('customers-survey') }}">
                    <i class="material-icons">location_ons</i>
                    <p>Customers Survey</p>
                  </a>
                </li>
                @endif
                @if(auth()->user()->can('field_access'))
                <li class="nav-item {{ request()->is('fields*') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ url('fields') }}">
                    <i class="material-icons">location_ons</i>
                    <p>Servey Field</p>
                  </a>
                </li>
                @endif
                @if(auth()->user()->can('country_access'))
                <li class="nav-item {{ request()->is('country*') || request()->is('state*') || request()->is('district*') || request()->is('city*') || request()->is('pincode*') ? 'active' : '' }}">
                  <a class="nav-link collapsed" data-toggle="collapse" href="#addressMenu" aria-expanded="false">
                    <i class="material-icons">room</i>
                    <p> {!! trans('panel.sidemenu.address_master') !!}

                    </p>
                  </a>
                  <div class="collapse" id="addressMenu" style="">
                    <ul class="nav">
                      @if(auth()->user()->can('country_access'))
                      <li class="nav-item {{ request()->is('country*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('country') }}">
                          <i class="material-icons">room</i>
                          <p>{!! trans('panel.sidemenu.address_country') !!}</p>
                        </a>
                      </li>
                      @endif
                      @if(auth()->user()->can('state_access'))
                      <li class="nav-item {{ request()->is('state*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('state') }}">
                          <i class="material-icons">room</i>
                          <p>{!! trans('panel.sidemenu.address_state') !!}</p>
                        </a>
                      </li>
                      @endif
                      @if(auth()->user()->can('district_access'))
                      <li class="nav-item {{ request()->is('district*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('district') }}">
                          <i class="material-icons">room</i>
                          <p>{!! trans('panel.sidemenu.address_district') !!}</p>
                        </a>
                      </li>
                      @endif
                      @if(auth()->user()->can('city_access'))
                      <li class="nav-item {{ request()->is('city') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('city') }}">
                          <i class="material-icons">room</i>
                          <p>{!! trans('panel.sidemenu.address_city') !!}</p>
                        </a>
                      </li>
                      @endif
                      @if(auth()->user()->can('pincode_access'))
                      <li class="nav-item {{ request()->is('pincode*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('pincode') }}">
                          <i class="material-icons">room</i>
                          <p>{!! trans('panel.sidemenu.address_pincode') !!}</p>
                        </a>
                      </li>
                      @endif
                    </ul>
                  </div>
                </li>
                @endif
                @if(auth()->user()->can('dealer_appointment'))
                <li class="nav-item {{ request()->is('dealer-appointment*') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ url('dealer-appointments') }}">
                    <i class="material-icons">pending_actions</i>
                    <p>Dealer / Distributor Appointment</p>
                  </a>
                </li>
                @endif
              </ul>
            </div>
          </li>
          @endif


          @if(auth()->user()->can(['expenses_type']))
          <!-- <li class="nav-item {{ request()->is('expenses_type') ? 'active' : '' }}">
            <a class="nav-link" href="{{ url('expenses_type') }}">
              <i class="material-icons">dashboard</i>
              <p>{!! trans('panel.sidemenu.expenses_type') !!}</p>
            </a>
          </li> -->
          @endif
          @if(auth()->user()->can('product_access'))
          <li class="nav-item {{ request()->is('categories*') || request()->is('subcategories*') || request()->is('brands*') || request()->is('products*') || request()->is('units*') || request()->is('production*') ? 'active' : '' }}">
            <a class="nav-link collapsed" data-toggle="collapse" href="#productMenu" aria-expanded="false">
              <i class="material-icons">star</i>
              <p> {!! trans('panel.sidemenu.product_master') !!}

              </p>
            </a>
            <div class="collapse" id="productMenu" style="">
              <ul class="nav">
                @if(auth()->user()->can('category_access'))
                <li class="nav-item {{ request()->is('categories*') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ url('categories') }}">
                    <i class="material-icons">outlet</i>
                    <p>{!! trans('panel.sidemenu.categories') !!}</p>
                  </a>
                </li>
                @endif
                @if(auth()->user()->can('subcategory_access'))
                <li class="nav-item {{ request()->is('subcategories*') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ url('subcategories') }}">
                    <i class="material-icons">flaky</i>
                    <p>{!! trans('panel.sidemenu.subcategories') !!}</p>
                  </a>
                </li>
                @endif
                @if(auth()->user()->can('brand_access'))
                <li class="nav-item {{ request()->is('brands*') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ url('brands') }}">
                    <i class="material-icons">group_work</i>
                    <p>{!! trans('panel.sidemenu.brands') !!}</p>
                  </a>
                </li>
                @endif
                @if(auth()->user()->can('product_access'))
                <li class="nav-item {{ request()->is('products*') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ url('products') }}">
                    <i class="material-icons">play_for_work</i>
                    <p>{!! trans('panel.sidemenu.products') !!}</p>
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
                <li class="nav-item {{ request()->is('units*') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ url('units') }}">
                    <i class="material-icons">donut_small</i>
                    <p>{!! trans('panel.sidemenu.units') !!}</p>
                  </a>
                </li>
                @endif
                @if(auth()->user()->can('stock_access'))
                <li class="nav-item {{ request()->is('stock*') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ url('stock') }}">
                    <i class="material-icons">donut_small</i>
                    <p>Stock</p>
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
          @if(auth()->user()->can('target_users_access'))
          <li class="nav-item {{ request()->is('sales_users*') || request()->is('sales_dealer*') ? 'active' : '' }}">
            <a class="nav-link collapsed" data-toggle="collapse" href="#salesUserMenu" aria-expanded="false">
              <i class="material-icons">store</i>
              <p> {!! trans('panel.sidemenu.sales_users') !!} </p>
            </a>
            <div class="collapse" id="salesUserMenu" style="">
              <ul class="nav">
                @if(auth()->user()->can('target_users_access'))
                <li class="nav-item {{ request()->is('sales_users*') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ url('sales_users/target_users') }}">
                    <i class="material-icons">verified_user</i>
                    <p> {!! trans('panel.sales_users.title') !!}</p>
                  </a>
                </li>
                @endif
                @if(auth()->user()->can('sales_target_dealers_access'))
                <li class="nav-item {{ request()->is('sales_dealer*') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ url('sales_dealer/target_dealers') }}">
                    <i class="material-icons">verified_user</i>
                    <p> {!! trans('panel.dealer_distributor_user.title') !!}</p>
                  </a>
                </li>
                @endif
                @if(auth()->user()->can('branch_wise_sales_target_access'))
                <li class="nav-item {{ request()->is('branches_sales_target') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ url('branches_sales_target') }}">
                    <i class="material-icons">holiday_village</i>
                    <p> Branch Wise Sales Target</p>
                  </a>
                </li>
                @endif
              </ul>
            </div>
          </li>
          @endif
          @if(auth()->user()->can('hr_access'))
          <li class="nav-item {{ request()->is('reports/attendancereport*') || request()->is('reports/attendancereportSummary*') || request()->is('holidays*') || request()->is('leaves*') || request()->is('appraisal*') || request()->is('sales_weightage*') || request()->is('users*') || request()->is('targets*') || request()->is('livelocation*') || request()->is('roles*') || request()->is('permissions*') || request()->is('tours*') || request()->is('usercity*') || request()->is('new-joinings*') ? 'active' : '' }}">
            <a class="nav-link collapsed" data-toggle="collapse" href="#hr" aria-expanded="false">
              <i class="material-icons">star</i>
              <p> {!! trans('panel.sidemenu.hr') !!}

              </p>
            </a>
            <div class="collapse" id="hr">
              <ul class="nav">
                @if(auth()->user()->can('attendance_report'))
                <li class="nav-item {{ request()->is('reports/attendancereport') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ url('reports/attendancereport') }}">
                    <i class="material-icons">check_circle</i>
                    <p>Attendance Detail Report</p>
                  </a>
                </li>
                @endif

                @if(auth()->user()->can('attendance_summary_report'))
                <li class="nav-item {{ request()->is('reports/attendancereportSummary') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ url('reports/attendancereportSummary') }}">
                    <i class="material-icons">flaky</i>
                    <p>Attendance Summary Report</p>
                  </a>
                </li>
                @endif

                @if(auth()->user()->can('holiday_access'))
                <li class="nav-item {{ request()->is('holidays*') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ url('holidays') }}">
                    <i class="material-icons">holiday_village</i>
                    <p>Holidays</p>
                  </a>
                </li>
                @endif

                @if(auth()->user()->can('leave_access'))
                <li class="nav-item {{ request()->is('leaves*') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ url('leaves') }}">
                    <i class="material-icons">holiday_village</i>
                    <p>Leaves</p>
                  </a>
                </li>
                @endif

                @if(auth()->user()->can('appraisal_pms'))
                <li class="nav-item {{ request()->is('appraisal*') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ url('appraisal/index') }}">
                    <i class="material-icons">verified_user</i>
                    <p>Appraisal(PMS)</p>
                  </a>
                </li>
                @endif
                @if(auth()->user()->can('sales_weightage'))
                <li class="nav-item {{ request()->is('sales_weightage*') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ url('sales_weightage') }}">
                    <i class="material-icons">check</i>
                    <p>{!! trans('panel.sales_weightage.title') !!}</p>
                  </a>
                </li>
                @endif
                @if(auth()->user()->can('branch'))
                <li class="nav-item {{ request()->is('branch*') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ url('branches') }}">
                    <i class="material-icons">shopping_bag</i>
                    <p>Branch</p>
                  </a>
                </li>
                @endif
                @if(auth()->user()->can('division'))
                <li class="nav-item {{ request()->is('division*') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ url('division') }}">
                    <i class="material-icons">shopping_bag</i>
                    <p>Division</p>
                  </a>
                </li>
                @endif
                @if(auth()->user()->can('designation'))
                <li class="nav-item {{ request()->is('designation*') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ url('designation') }}">
                    <i class="material-icons">shopping_bag</i>
                    <p>Designation</p>
                  </a>
                </li>
                @endif
                @if(auth()->user()->can('departments'))
                <li class="nav-item {{ request()->is('departments*') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ url('departments') }}">
                    <i class="material-icons">shopping_bag</i>
                    <p>Departments</p>
                  </a>
                </li>
                @endif
                @if(auth()->user()->can('tasks_access'))
                <li class="nav-item {{ request()->is('tasks*') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ url('tasks') }}">
                    <i class="material-icons">check_circle</i>
                    <p>{!! trans('panel.sidemenu.task') !!}</p>
                  </a>
                </li>
                @endif
                @if(auth()->user()->can('user_access'))
                <li class="nav-item {{ request()->is('users*') || request()->is('targets*') || request()->is('livelocation*') || request()->is('roles*') || request()->is('permissions*') || request()->is('tours*') || request()->is('usercity*') || request()->is('new-joinings*') ? 'active' : '' }}">
                  <a class="nav-link collapsed" data-toggle="collapse" href="#userMenu" aria-expanded="false">
                    <i class="material-icons">people</i>
                    <p> {!! trans('panel.sidemenu.users_master') !!}

                    </p>
                  </a>
                  <div class="collapse" id="userMenu" style="">
                    <ul class="nav">
                      <!--                 @if(auth()->user()->can('appraisal_pms'))
                      <li class="nav-item {{ request()->is('appraisal/create') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('appraisal/index') }}">
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
                      <li class="nav-item {{ request()->is('new-joinings*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('new-joinings') }}">
                          <i class="material-icons">verified_user</i>
                          <p>New Joining</p>
                        </a>
                      </li>
                      @endif
                      @if(auth()->user()->can('user_access'))
                      <li class="nav-item {{ request()->is('users*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('users') }}">
                          <i class="material-icons">verified_user</i>
                          <p>{!! trans('panel.sidemenu.users') !!}</p>
                        </a>
                      </li>
                      @endif
                      @if(auth()->user()->can('user_app_details_access'))
                      <li class="nav-item {{ request()->is('user_app_details*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('user_app_details') }}">
                          <i class="material-icons">login</i>
                          <p>User App details</p>
                        </a>
                      </li>
                      @endif
                      @if(auth()->user()->can('target_access'))
                      <li class="nav-item {{ request()->is('targets*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('targets') }}">
                          <i class="material-icons">verified_user</i>
                          <p>User Target</p>
                        </a>
                      </li>
                      @endif
                      @if(auth()->user()->can('user_location'))
                      <li class="nav-item {{ request()->is('livelocation*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('livelocation') }}">
                          <i class="material-icons">input</i>
                          <p>User Live Location</p>
                        </a>
                      </li>
                      @endif
                      @if(auth()->user()->can('tours'))
                      <li class="nav-item {{ request()->is('tours*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('tours') }}">
                          <i class="material-icons">flight</i>
                          <p>Tours</p>
                        </a>
                      </li>
                      @endif
                      @if(auth()->user()->can('city_assigned'))
                      <li class="nav-item {{ request()->is('usercity*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('usercity') }}">
                          <i class="material-icons">flight</i>
                          <p>City Assigned</p>
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


          @if(auth()->user()->can(['account_access']))
          <li class="nav-item {{ request()->is('expenses*') ? 'active' : '' }}">
            <a class="nav-link collapsed" data-toggle="collapse" href="#accountMenu" aria-expanded="false">
              <i class="material-icons">store</i>
              <p> {!! trans('panel.sidemenu.account') !!}

              </p>
            </a>
            <div class="collapse" id="accountMenu" style="">
              <ul class="nav">
                @if(auth()->user()->can(['expenses_type']))
                <li class="nav-item {{ request()->is('expenses_type*') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ url('expenses_type') }}">
                    <i class="material-icons">dashboard</i>
                    <p>{!! trans('panel.sidemenu.expenses_type') !!}</p>
                  </a>
                </li>
                @endif

                @if(auth()->user()->can('expense_access'))
                <li class="nav-item {{ request()->is('expenses') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ url('expenses') }}">
                    <i class="material-icons">outlet</i>
                    <p>Expense</p>
                  </a>
                </li>
                @endif
                @if(auth()->user()->can('payments_access'))
                <li class="nav-item ">
                  <a class="nav-link collapsed" data-toggle="collapse" href="#paymentManu" aria-expanded="false">
                    <i class="material-icons">paid</i>
                    <p>Payments</p>
                  </a>
                <li class="nav-item ">
                  <div class="collapse" id="paymentManu" style="">
                    <ul class="nav">
                      @if(auth()->user()->can('payments_create'))
                      <li class="nav-item {{ request()->is('payments*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('payments/create') }}">
                          <i class="material-icons">currency_exchange</i>
                          <p>Payment Recieved</p>
                        </a>
                      </li>
                      @endif
                      <li class="nav-item {{ request()->is('payments*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('payments') }}">
                          <i class="material-icons">currency_rupee</i>
                          <p>Payments</p>
                        </a>
                      </li>
                    </ul>
                  </div>
                </li>
          </li>
          @endif
        </ul>
      </div>
      </li>
      @endif




      @if(auth()->user()->can('services_access'))
      <li class="nav-item {{ request()->is('services*') || request()->is('warranty_activation*') || request()->is('complaint-type*') || request()->is('complaints*') || request()->is('service-charge*') || request()->is('service_bills*') ? 'active' : '' }}">
        <a class="nav-link collapsed" data-toggle="collapse" href="#serviceMenu" aria-expanded="false">
          <i class="material-icons">design_services</i>
          <p> {!! trans('panel.sidemenu.services') !!}

          </p>
        </a>
        <div class="collapse" id="serviceMenu" style="">
          <ul class="nav">
            @if(auth()->user()->can('serial_number_transaction'))
            <li class="nav-item {{ request()->is('services/serial_number_transaction*') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('services/serial_number_transaction') }}">
                <i class="material-icons">receipt_long</i>
                <p>{!! trans('panel.sidemenu.serial_number_transaction') !!}</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('serial_number_history'))
            <li class="nav-item {{ request()->is('services/serial_number_history*') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('services/serial_number_history') }}">
                <i class="material-icons">history</i>
                <p>{!! trans('panel.sidemenu.serial_number_history') !!}</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('complaint_type_access'))
            <li class="nav-item {{ request()->is('complaint-type*') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('complaint-type') }}">
                <i class="material-icons">checklist</i>
                <p>{!! trans('panel.sidemenu.complaint_type') !!}</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('complaint_access'))
            <li class="nav-item {{ request()->is('complaints*') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('complaints') }}">
                <i class="material-icons">editor_choice</i>
                <p>{!! trans('panel.sidemenu.complaint') !!}</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('service_bill_access'))
            <li class="nav-item {{ request()->is('service_bills*') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('service_bills') }}">
                <i class="material-icons">history</i>
                <p>Service Bill</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('services_product_access'))
            <li class="nav-item {{ request()->is('service-charge*') ? 'active' : '' }}">
              <a class="nav-link collapsed" data-toggle="collapse" href="#serviceProductMenu" aria-expanded="false">
                <i class="material-icons">home_repair_service</i>
                <p> Service Charge Products</p>
              </a>
              <div class="collapse" id="serviceProductMenu" style="">
                <ul class="nav">
                  @if(auth()->user()->can('services_product_division'))
                  <li class="nav-item {{ request()->is('service-charge/dividsions*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('service-charge/dividsions') }}">
                      <i class="material-icons">receipt_long</i>
                      <p>Division</p>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('services_product_category'))
                  <li class="nav-item {{ request()->is('service-charge/categories*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('service-charge/categories') }}">
                      <i class="material-icons">category</i>
                      <p>Categories</p>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('services_product_products'))
                  <li class="nav-item {{ request()->is('service-charge/products*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('service-charge/products') }}">
                      <i class="material-icons">storefront</i>
                      <p>Products</p>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('services_product_chargetype'))
                  <li class="nav-item {{ request()->is('service-charge/chargetype*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('service-charge/chargetype') }}">
                      <i class="material-icons">power</i>
                      <p>Charge Type</p>
                    </a>
                  </li>
                  @endif
                </ul>
              </div>
            </li>
            @endif
            @if(auth()->user()->can('warranty_activation_access'))
            <li class="nav-item {{ request()->is('warranty_activation*') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('warranty_activation') }}">
                <i class="material-icons">history</i>
                <p>{!! trans('panel.sidemenu.warranty_activation') !!}</p>
              </a>
            </li>
            @endif
          </ul>
        </div>
      </li>
      @endif

      <!--           @if(auth()->user()->can('order_access')) 
          <li class="nav-item {{ request()->is('orders*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ url('orders') }}">
              <i class="material-icons">shopping_bag</i>
              <p>{!! trans('panel.sidemenu.orders') !!}</p>
            </a>
          </li>
          @endif -->

      @if(auth()->user()->can('order_access'))
      <li class="nav-item {{ request()->is('orders*') || request()->is('orderschemes*') || request()->is('sales') ? 'active' : '' }}">
        <a class="nav-link collapsed" data-toggle="collapse" href="#orderMenu" aria-expanded="false">
          <i class="material-icons">star</i>
          <p>{!! trans('panel.sidemenu.orders') !!}</p>
        </a>

        <div class="collapse" id="orderMenu" style="">
          <ul class="nav">
            @if(auth()->user()->can('order_access'))
            <li class="nav-item {{ request()->is('orders*') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('orders') }}">
                <i class="material-icons">shopping_bag</i>
                <p>{!! trans('panel.sidemenu.orders') !!}</p>
              </a>
            </li>
            @endif

            @if(auth()->user()->can('orderscheme'))
            <li class="nav-item {{ request()->is('orderschemes*') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('orderschemes') }}">
                <i class="material-icons">flaky</i>
                <p>Order Schemes</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('sale_access'))
            <li class="nav-item {{ request()->is('sales') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('sales') }}">
                <i class="material-icons">shopping_cart</i>
                <p>{!! trans('panel.sidemenu.order_dispatch') !!}</p>
              </a>
            </li>
            @endif
          </ul>
        </div>
      </li>
      @endif

      @if(auth()->user()->can('wallet_access'))
      <!-- <li class="nav-item ">
            <a class="nav-link collapsed" data-toggle="collapse" href="#walletMenu" aria-expanded="false">
              <i class="material-icons">account_balance_wallet</i>
              <p> {!! trans('panel.sidemenu.wallet_master') !!}

              </p>
            </a>
            <div class="collapse" id="walletMenu" style="">
              <ul class="nav">
                @if(auth()->user()->can('wallet_access'))
                <li class="nav-item ">
                  <a class="nav-link" href="{{ url('wallets') }}">
                    <i class="material-icons">account_balance_wallet</i>
                    <p>{!! trans('panel.sidemenu.wallet') !!}</p>
                  </a>
                </li>
                @endif
                @if(auth()->user()->can('redeemedpoint_access'))
                <li class="nav-item ">
                  <a class="nav-link" href="{{ url('redeemedPoint') }}">
                    <i class="material-icons">redeem</i>
                    <p>{!! trans('panel.sidemenu.redeemedPoint') !!}</p>
                  </a>
                </li>
                @endif
              </ul>
            </div>
          </li> -->
      @endif
      @if(auth()->user()->can('scheme_access'))
      <li class="nav-item {{ request()->is('schemes*') || request()->is('transaction_history*') || request()->is('gifts*') || request()->is('gift-categories*') || request()->is('gift-subcategories*') || request()->is('gift-model*') || request()->is('gift-brands*') || request()->is('redemptions*') || request()->is('damage_entries*') || request()->is('mobile_user_login*') || request()->is('customer-kyc*') ? 'active' : '' }}">
        <a class="nav-link collapsed" data-toggle="collapse" href="#schemesMenu" aria-expanded="false">
          <i class="material-icons">loyalty</i>
          <p> Loyalty Engine </p>
        </a>
        <div class="collapse" id="schemesMenu">
          <ul class="nav">
            @if(auth()->user()->can('scheme_access_list'))
            <li class="nav-item {{ request()->is('schemes*') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('schemes') }}">
                <i class="material-icons">create</i>
                <p>Loyalty {!! trans('panel.sidemenu.scheme_master') !!}</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('transaction_history_access'))
            <li class="nav-item {{ request()->is('transaction_history*') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('transaction_history') }}">
                <i class="material-icons">history</i>
                <p>Transaction Coupon History</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('loyalty_mobile_app_users_access'))
            <li class="nav-item {{ request()->is('mobile_user_login') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('mobile_user_login') }}">
                <i class="material-icons">verified_user</i>
                <p>Mobile App Users</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('damage_entry_access'))
            <li class="nav-item {{ request()->is('damage_entries*') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('damage_entries') }}">
                <i class="material-icons">insert_page_break</i>
                <p>Damage QR Entries</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('redemption_access'))
            <li class="nav-item {{ request()->is('redemptions*') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('redemptions') }}">
                <i class="material-icons">mp</i>
                <p>Redemption</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('gift_access'))
            <li class="nav-item {{ request()->is('gifts*') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('gifts') }}">
                <i class="material-icons">redeem</i>
                <p>Gift Catalogue</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('gift_category_access'))
            <li class="nav-item {{ request()->is('gift-categories*') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('gift-categories') }}">
                <i class="material-icons">redeem</i>
                <p>Gift Categories</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('gift_subcategory_access'))
            <li class="nav-item {{ request()->is('gift-subcategories*') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('gift-subcategories') }}">
                <i class="material-icons">redeem</i>
                <p>Gift Sub Categories</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('gift_model_access'))
            <li class="nav-item {{ request()->is('gift-model*') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('gift-model') }}">
                <i class="material-icons">redeem</i>
                <p>Gift Model</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('gift_brand_access'))
            <li class="nav-item {{ request()->is('gift-brands*') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('gift-brands') }}">
                <i class="material-icons">redeem</i>
                <p>Gift Brand</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('customer_kyc_access'))
            <li class="nav-item {{ request()->is('customer-kyc*') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('customer-kyc') }}">
                <i class="material-icons">verified</i>
                <p>Customer KYC</p>
              </a>
            </li>
            @endif
          </ul>
        </div>
      </li>
      @endif
      @if(auth()->user()->can('setting_access'))
      <li class="nav-item {{request()->is('loyalty-app-setting*') ? 'active' : '' }}">
        <a class="nav-link collapsed" data-toggle="collapse" href="#settingMenu" aria-expanded="false">
          <i class="material-icons">settings</i>
          <p> {!! trans('panel.sidemenu.setting_master') !!}

          </p>
        </a>
        <div class="collapse" id="settingMenu" style="">
          <ul class="nav">
            @if(auth()->user()->can('setting_access'))
            <!-- <li class="nav-item ">
                  <a class="nav-link" href="{{ url('settings') }}">
                    <i class="material-icons">settings</i>
                    <p>{!! trans('panel.sidemenu.setting') !!}</p>
                  </a>
                </li> -->
            @endif
            @if(auth()->user()->can('loyalty_app_setting_access'))
            <li class="nav-item {{request()->is('loyalty-app-setting*') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('loyalty-app-setting') }}">
                <i class="material-icons">settings</i>
                <p>Loyalty App {!! trans('panel.sidemenu.setting') !!}</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('loyalty_app_setting_access'))
            <li class="nav-item {{request()->is('field-konnect-app-setting*') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('field-konnect-app-setting') }}">
                <i class="material-icons">settings</i>
                <p>FieldKonnect App  {!! trans('panel.sidemenu.setting') !!}</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('status_access'))
            <li class="nav-item {{ request()->is('status*') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('status') }}">
                <i class="material-icons">reorder</i>
                <p>{!! trans('panel.sidemenu.status') !!}</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('role_access'))
            <li class="nav-item {{ request()->is('roles*') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('roles') }}">
                <i class="material-icons">person_pin</i>
                <p>{!! trans('panel.sidemenu.roles') !!}</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('permission_access'))
            <li class="nav-item {{ request()->is('permissions*') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('permissions') }}">
                <i class="material-icons">check_circle</i>
                <p>{!! trans('panel.sidemenu.permissions') !!}</p>
              </a>
            </li>
            @endif
          </ul>
        </div>
      </li>
      @endif
      @if(auth()->user()->can('supports_access'))
      <li class="nav-item ">
        <a class="nav-link collapsed" data-toggle="collapse" href="#supportMenu" aria-expanded="false">
          <i class="material-icons">headset</i>
          <p> {!! trans('panel.sidemenu.support_master') !!}
          </p>
        </a>
        <div class="collapse" id="supportMenu" style="">
          <ul class="nav">
            @if(auth()->user()->can('supports_access'))
            <li class="nav-item ">
              <a class="nav-link" href="{{ url('supports') }}">
                <i class="material-icons">headset_mic</i>
                <p>{!! trans('panel.sidemenu.support') !!}</p>
              </a>
            </li>
            @endif
          </ul>
        </div>
      </li>
      @endif
      @if(auth()->user()->can('reports'))
      <li class="nav-item {{ request()->is('reports*') ? 'active' : '' }}">
        <a class="nav-link collapsed" data-toggle="collapse" href="#tasksMenu" aria-expanded="false">
          <i class="material-icons">airplay</i>
          <p> Reports
          </p>
        </a>
        <div class="collapse" id="tasksMenu" style="">
          <ul class="nav">
            <!--                 @if(auth()->user()->can('attendance_report'))
                <li class="nav-item {{ request()->is('reports/attendancereport*') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ url('reports/attendancereport') }}">
                    <i class="material-icons">check_circle</i>
                    <p>Attendance Report</p>
                  </a>
                </li>
                @endif -->
                @if(auth()->user()->can('reports_sale'))
            <li class="nav-item {{ request()->is('reports/reports_sale*') || request()->is('reports/fos_rating*') || request()->is('reports/primary_sales*') || request()->is('reports/secondary_sales*') || request()->is('reports/product_analysis_qty*') || request()->is('reports/product_analysis_branch*') || request()->is('reports/product_analysis_value*') || request()->is('reports/group_wise_analysis*') ? 'active' : '' }}">
              <a class="nav-link collapsed" data-toggle="collapse" href="#salesReportsMenu" aria-expanded="false">
                <i class="material-icons">check_circle</i>
                <p>Sales</p>
              </a>
            <li class="nav-item ">
              <div class="collapse" id="salesReportsMenu" style="">
                <ul class="nav">
                  @if(auth()->user()->can('user_working_report'))
                  <li class="nav-item {{ request()->is('reports/reports_sale*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('reports/reports_sale') }}">
                      <i class="material-icons">check_circle</i>
                      <p>User working report</p>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('fos_rating_report'))
                  <li class="nav-item {{ request()->is('reports/fos_rating*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('reports/fos_rating') }}">
                      <i class="material-icons">store</i>
                      <p>FOS Rating Report</p>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('dashboard_primary_sales_access'))
                  <li class="nav-item {{ request()->is('reports/primary_sales*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('reports/primary_sales') }}">
                      <i class="material-icons">store</i>
                      <p>Primary Sales</p>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('dashboard_secondary_sales_access'))
                  <li class="nav-item {{ request()->is('reports/secondary_sales*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('reports/secondary_sales') }}">
                      <i class="material-icons">store</i>
                      <p>Secondary Sales </p>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('product_analysis_branch_access'))
                  <li class="nav-item {{ request()->is('reports/product_analysis_branch*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('reports/product_analysis_branch') }}">
                      <i class="material-icons">store</i>
                      <p>Product Analysis Branch Wise</p>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('product_analysis_qty_access'))
                  <li class="nav-item {{ request()->is('reports/product_analysis_qty*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('reports/product_analysis_qty') }}">
                      <i class="material-icons">store</i>
                      <p>Product Analysis Qty</p>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('product_analysis_value_access'))
                  <li class="nav-item {{ request()->is('reports/product_analysis_value*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('reports/product_analysis_value') }}">
                      <i class="material-icons">store</i>
                      <p>Product Analysis Value</p>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('group_wise_analysis_access'))
                  <li class="nav-item {{ request()->is('reports/group_wise_analysis*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('reports/group_wise_analysis') }}">
                      <i class="material-icons">store</i>
                      <p>Group Wise Analysis</p>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('per_employee_costing_access'))
                  <li class="nav-item {{ request()->is('reports/per_employee_costing*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('reports/per_employee_costing') }}">
                      <i class="material-icons">store</i>
                      <p>Per Employee Costing</p>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('top_dealer_access'))
                  <li class="nav-item {{ request()->is('reports/top_dealer*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('reports/top_dealer') }}">
                      <i class="material-icons">store</i>
                      <p>Top Dealer</p>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('dealer_growth_access'))
                  <li class="nav-item {{ request()->is('reports/dealer_growth*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('reports/dealer_growth') }}">
                      <i class="material-icons">store</i>
                      <p>Dealer Growth</p>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('new_dealer_sale_access'))
                  <li class="nav-item {{ request()->is('reports/new_dealer_sale*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('reports/new_dealer_sale') }}">
                      <i class="material-icons">store</i>
                      <p>New Dealer Sale</p>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('user_incentive_access'))
                  <li class="nav-item {{ request()->is('reports/user_incentive*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('reports/user_incentive') }}">
                      <i class="material-icons">store</i>
                      <p>User Incentive</p>
                    </a>
                  </li>
                  @endif
                </ul>
              </div>
            </li>
            </li>
            @endif
            @if(auth()->user()->can('daily_visit_report'))
            <li class="nav-item " style="display:none;">
              <a class="nav-link" href="{{ url('reports/per_day_counter_visit_report') }}">
                <i class="material-icons">store</i>
                <p>Perday Counter Visit Report</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('fielda_ctivity_report'))
            <li class="nav-item " style="display:none;">
              <a class="nav-link" href="{{ url('reports/fieldactivity') }}">
                <i class="material-icons">store</i>
                <p>Field Activity Report</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('tour_programme_report'))
            <li class="nav-item " style="display:none;">
              <a class="nav-link" href="{{ url('reports/tourprogramme') }}">
                <i class="material-icons">store</i>
                <p>Tour Programme Report</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('monthly_movement_report'))
            <li class="nav-item " style="display:none;">
              <a class="nav-link" href="{{ url('reports/monthlymovement') }}">
                <i class="material-icons">store</i>
                <p>Monthly Movement Report</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('point_collections_report'))
            <li class="nav-item " style="display:none;">
              <a class="nav-link" href="{{ url('reports/pointcollections') }}">
                <i class="material-icons">store</i>
                <p>Point Collections Report</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('territory_coverage_report'))
            <li class="nav-item " style="display:none;">
              <a class="nav-link" href="{{ url('reports/territorycoverage') }}">
                <i class="material-icons">store</i>
                <p>Territory Coverage Report</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('performance_parameter_report'))
            <li class="nav-item " style="display:none;">
              <a class="nav-link" href="{{ url('reports/performanceparameter') }}">
                <i class="material-icons">store</i>
                <p>Performance Parameter</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('mechanics_points_report'))
            <li class="nav-item " style="display:none;">
              <a class="nav-link" href="{{ url('reports/asmwisemechanicspoints') }}">
                <i class="material-icons">store</i>
                <p>Mechanics Points Report</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('targetvs_sales_report'))
            <li class="nav-item " style="display:none;">
              <a class="nav-link" href="{{ url('reports/targetvssales') }}">
                <i class="material-icons">store</i>
                <p>Target Vs Sales Report</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('survey_analysis_report'))
            <li class="nav-item " style="display:none;">
              <a class="nav-link" href="{{ url('reports/surveyanalysis') }}">
                <i class="material-icons">store</i>
                <p>Survey Analysis Report</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('customers_report_access'))
            <li class="nav-item ">
              <a class="nav-link collapsed" data-toggle="collapse" href="#customerReportsMenu" aria-expanded="false">
                <i class="material-icons">loyalty</i>
                <p>Customers</p>
              </a>
            <li class="nav-item ">
              <div class="collapse" id="customerReportsMenu" style="">
                <ul class="nav">
                  @if(auth()->user()->can('visit_report'))
                  <li class="nav-item {{ request()->is('reports/customervisit*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('reports/customervisit') }}">
                      <i class="material-icons">check_circle</i>
                      <p>Customer Visit</p>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('customers_report'))
                  <li class="nav-item ">
                    <a class="nav-link" href="{{ url('reports/customersreport') }}">
                      <i class="material-icons">store</i>
                      <p>Customer Master</p>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('calling_report'))
                  <li class="nav-item ">
                    <a class="nav-link" href="{{ url('notes') }}">
                      <i class="material-icons">store</i>
                      <p>Calling Report</p>
                    </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('customer_outstanting'))
                  <li class="nav-item {{ request()->is('reports/customer_outstanting*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('reports/customer_outstanting') }}">
                      <i class="material-icons">store</i>
                      <p>Cutomer Outstanding</p>
                    </a>
                  </li>
                  @endif
                </ul>
              </div>
            </li>
      </li>
      @endif
      @if(auth()->user()->can('loyalty_report_access'))
      <li class="nav-item ">
        <a class="nav-link collapsed" data-toggle="collapse" href="#loyaltyMenu" aria-expanded="false">
          <i class="material-icons">loyalty</i>
          <p>Loyalty</p>
        </a>
      <li class="nav-item ">
        <div class="collapse" id="loyaltyMenu" style="">
          <ul class="nav">
            @if(auth()->user()->can('loyalty_summary_report'))
            <li class="nav-item {{ request()->is('reports/loyalty_summary_report') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('reports/loyalty_summary_report') }}">
                <i class="material-icons">airplay</i>
                <p>Loyalty Summary Report</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('loyalty_dealer_wise_summary_report'))
            <li class="nav-item {{ request()->is('reports/loyalty_dealer_wise_summary_report') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('reports/loyalty_dealer_wise_summary_report') }}">
                <i class="material-icons">airplay</i>
                <p>Loyalty Dealer Wise Summary Report</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('loyalty_retailer_wise_summary_report'))
            <li class="nav-item {{ request()->is('reports/loyalty_retailer_wise_summary_report') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('reports/loyalty_retailer_wise_summary_report') }}">
                <i class="material-icons">airplay</i>
                <p>Retailer Wise Loyalty Summary Report</p>
              </a>
            </li>
            @endif
          </ul>
        </div>
      </li>
      </li>
      @endif
      @if(auth()->user()->can('visitreport_access') || auth()->user()->can('beat_access'))
      <li class="nav-item ">
        <a class="nav-link collapsed" data-toggle="collapse" href="#beatMenu" aria-expanded="false">
          <i class="material-icons">schedule</i>
          <p> Beats
          </p>
        </a>
        <div class="collapse" id="beatMenu" style="">
          <ul class="nav">
            @if(auth()->user()->can('beat_access'))
            <li class="nav-item {{ request()->is('beats*') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('beats') }}">
                <i class="material-icons">rowing</i>
                <p>{!! trans('panel.sidemenu.beats') !!}</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('beatdetail_access'))
            <li class="nav-item {{ request()->is('beatdetail*') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('beatdetail') }}">
                <i class="material-icons">opacity</i>
                <p>{!! trans('panel.sidemenu.beatdetail') !!}</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('checkin_access'))
            <li class="nav-item {{ request()->is('checkin*') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('checkin') }}">
                <i class="material-icons">assignment_turned_in</i>
                <p>{!! trans('panel.sidemenu.checkin') !!}</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('visitreport_access'))
            <li class="nav-item {{ request()->is('visitreports*') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('visitreports') }}">
                <i class="material-icons">analytics</i>
                <p>{!! trans('panel.sidemenu.visitreport') !!}</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('visittype_access'))
            <li class="nav-item {{ request()->is('visittypes*') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('visittypes') }}">
                <i class="material-icons">input</i>
                <p>{!! trans('panel.sidemenu.visittype') !!}</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('visitreport_access'))
            <li class="nav-item ">
              <a class="nav-link" href="{{ url('mastervisitreport') }}">
                <i class="material-icons">store</i>
                <p>Master VisitReport</p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('adherence_report'))
            <li class="nav-item {{ request()->is('reports/beatadherence*') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('reports/beatadherence') }}">
                <i class="material-icons">check_circle</i>
                <p>Beat Adherence </p>
              </a>
            </li>
            @endif
            @if(auth()->user()->can('summary_report'))
            <li class="nav-item {{ request()->is('reports/adherencesummary*') ? 'active' : '' }}" >
              <a class="nav-link" href="{{ url('reports/adherencesummary') }}">
                <i class="material-icons">check_circle</i>
                <p>Adherence Summary</p>
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
  </div>
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
        <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
          <span class="sr-only">Toggle navigation</span>
          <span class="navbar-toggler-icon icon-bar"></span>
          <span class="navbar-toggler-icon icon-bar"></span>
          <span class="navbar-toggler-icon icon-bar"></span>
        </button>
        @auth
        <div class="collapse navbar-collapse justify-content-end">
          <ul class="navbar-nav">
            <li class="nav-item dropdown">
              <a class="nav-link" href="http://example.com" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
              <a class="nav-link" href="javascript:;" id="navbarDropdownProfile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
  <div class="modal fade bd-example-modal-lg" id="previewimageInModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content card">
        <div class="card-header">
          <span class="pull-right">
            <a href="javascript:void(0)" class="btn btn-just-icon btn-danger" data-dismiss="modal"><i class="material-icons">clear</i></a>
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" integrity="sha512-hievggED+/IcfxhYRSr4Auo1jbiOczpqpLZwfTVL/6hFACdbI3WQ8S9NCX50gsM9QVE+zLk/8wb9TlgriFbX+Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js" integrity="sha512-F636MAkMAhtTplahL9F6KmTfxTmYcAcjcCkyu0f0voT3N/6vzAuJ4Num55a0gEJ+hRLHhdz3vDvZpf6kqgEa5w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
</body>

</html>