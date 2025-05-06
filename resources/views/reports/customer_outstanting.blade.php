<x-app-layout>
  <style>
    table tbody tr {
      font-size: 14px !important;
      font-weight: 100 !important;
    }

    @keyframes blink {
      0% {
        opacity: 1;
      }

      50% {
        opacity: 0;
      }

      100% {
        opacity: 1;
      }
    }

    .blink-text {
      animation: blink 1.5s infinite;
    }
  </style>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title">Customer Outstanding
            <span class="">
              <div class="btn-group header-frm-btn">
                @if(auth()->user()->can(['customer_outstanting_download']))
                <form method="POST" action="{{url('customer_outstanting/download')}}">
                  @csrf
                  <div class="d-flex flex-wrap flex-row">
                    <!-- division filter -->
                    {{-- <div class="p-2" style="width:200px;">
                      <label for="division">Division</label>
                      <select class="select2" name="division[]" placeholder="Division" multiple id="ps_division_id" data-style="select-with-transition" title="{!! trans('panel.secondary_dashboard.division') !!}">
                        <option value="" disabled>{!! trans('panel.secondary_dashboard.division') !!}</option>
                        @if(@isset($ps_divisions ))
                        @foreach($ps_divisions as $division)
                        <option value="{!! $division->division !!}">{!! $division->division !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <!-- branch filter -->
                    <div class="p-2" style="width:180px;">
                      <select class="select2" name="branch_id" id="ps_branch_id" data-style="select-with-transition" title="panel.sales_users.branch">
                        <option value="" disabled selected>{!! trans('panel.secondary_dashboard.branch') !!}</option>
                        @if(@isset($ps_branches ))
                        @foreach($ps_branches as $branch)
                        <option value="{!! $branch->final_branch !!}">{!! $branch->final_branch !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <!-- financial year filter -->
                    <div class="p-2" style="width:200px;">
                      <select class="select2" name="financial_year" id="ps_financial_year" required data-style="select-with-transition" title="Year">
                        <option value="" disabled selected>{!! trans('panel.secondary_dashboard.year') !!}</option>
                        @foreach($years as $year)
                        @php
                        $startYear = $year - 1;
                        $endYear = $year;
                        @endphp
                        <option value="{!!$startYear!!}-{!!$endYear!!}">{!! $startYear!!} - {!! $endYear !!}</option>
                        @endforeach
                      </select>
                    </div>
                    <!-- month filter-->
                    <div class="p-2" style="width:200px;">
                      <label for="month">Month </label>
                      <select class="selectpicker" name="month[]" multiple id="ps_month" disabled data-style="select-with-transition" title="Month">
                        <option value="" disabled>{!! trans('panel.secondary_dashboard.month') !!}</option>
                        @for ($month = 1; $month <= 12; $month++) <option value="{!! date('M', mktime(0, 0, 0, $month, 1)) !!}">{!! date('M', mktime(0, 0, 0, $month, 1)) !!}</option>
                          @endfor
                      </select>
                    </div>
                    <!-- dealer/distributors filter -->
                    <div class="p-2" style="width:200px;">
                      <select class="select2" name="dealer" id="ps_dealer_id" data-style="select-with-transition" title="{!! trans('panel.sales_users.user_name') !!}">
                        <option value="" selected>{!! trans('panel.secondary_dashboard.dealers_and_distibutors') !!}</option>
                        @if(@isset($ps_dealers ))
                        @foreach($ps_dealers as $dealer)
                        <option value="{!! $dealer->dealer !!}">{!! $dealer->dealer !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <!-- sales persons filter -->
                   <div class="p-2" style="width:200px;">
                      <select class="select2" name="sales_person" id="ps_executive_id" data-style="select-with-transition" title="{!! trans('panel.sales_users.user_name') !!}">
                        <option value="" selected>{!! trans('panel.secondary_dashboard.sales_person') !!}</option>
                        @if(@isset($ps_sales_persons ))
                        @foreach($ps_sales_persons as $sales_person)
                        <option value="{!! $sales_person->sales_person !!}">{!! $sales_person->sales_person !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <!-- product models filter -->
                    <div class="p-2" style="width:200px;">
                      <select class="select2" name="product_model" id="ps_product_model" data-style="select-with-transition" title="{!! trans('panel.secondary_dashboard.product_model') !!}">
                        <option value="" selected>{!! trans('panel.secondary_dashboard.product_model') !!}</option>
                        @if(@isset($ps_product_models ))
                        @foreach($ps_product_models as $product)
                        <option value="{!! $product->product_name !!}">{!! $product->product_name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <!-- new group name filter -->
                    <div class="p-2" style="width:200px;">
                      <select class="select2" name="new_group" id="ps_new_group" data-style="select-with-transition" title="{!! trans('panel.secondary_dashboard.new_group_name') !!}">
                        <option value="" selected>{!! trans('panel.secondary_dashboard.new_group_name') !!}</option>
                        @if(@isset($ps_new_group_names ))
                        @foreach($ps_new_group_names as $product)
                        <option value="{!! $product->new_group !!}">{!! $product->new_group !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>--}}
                    <div class="p-2" style="width:200px;">
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} Customer Outstanding">
                        <i class="material-icons">cloud_download</i>
                        <div class="ripple-container"></div>
                      </button>
                    </div>
                  </div>
                </form>
                @endif
                <div class="row next-btn">
                  @if(auth()->user()->can(['customer_outstanting_upload']))
                  {{-- <form action="{{ URL::to('customer_outstanting/upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                  {{ csrf_field() }}
                  <div class="input-group" style="flex-wrap:nowrap;">
                    <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                      <span class="btn btn-just-icon btn-theme btn-file">
                        <span class="fileinput-new"><i class="material-icons">attach_file</i></span>
                        <span class="fileinput-exists">Change</span>
                        <input type="hidden">
                        <input title="Please select a file for upload data" type="file" title="Select file for upload data" name="import_file" style="flex-wrap: nowrap;" required accept=".xls,.xlsx" />
                      </span>
                    </div>
                    <div class="input-group-append">
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} Customer Outstanding">
                        <i class="material-icons">cloud_upload</i>
                        <div class="ripple-container"></div>
                      </button>
                    </div>
                  </div>
                  </form> --}}
                  @endif
                  <!-- primary sales import -->
                  @if(auth()->user()->can(['customer_outstanting_template']))
                  <!-- primary sales template creation -->
                  {{--<a href="{{ URL::to('customer_outstanting_template') }}" class="btn btn-just-icon btn-theme" title="{!! trans('panel.global.template') !!} Customer Outstanding"><i class="material-icons">text_snippet</i></a>--}}
                  @endif
                </div>
              </div>
            </span>
          </h4>
        </div>
        <div class="card-body">
          @if(count($errors) > 0)
          <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span>
              @foreach($errors->all() as $error)
              <li>{{$error}}</li>
              @endforeach
            </span>
          </div>
          @endif

          <div class="table-responsive">
            <table id="getCustomerOutstanding" class="table table-striped table-bordered table-hover table-checkable no-wrap">
              <thead class=" text-primary">
                <th>Date</th>
                <th>PO Number</th>
                <th>Party Name</th>
                <th>Rate</th>
                <th>Order QTY</th>
                <th>Dispatch QTY</th>
                <th>Pending QTY</th>
                <th>Days</th>
              </thead>
              <tbody>
              </tbody>
              <tfoot>
                <tr>
                  <th colspan="4"><b>Total : </b></th>
                  <th id="totalQty"></th>
                  <th id="disQty"></th>
                  <th id="penQty"></th>
                  <th></th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script type="text/javascript">
    $(function() {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      var table = $('#getCustomerOutstanding').DataTable({
        processing: true,
        serverSide: true,
        columnDefs: [{
          className: 'control',
          orderable: false,
          targets: -1
        }],
        "retrieve": true,
        "footerCallback": function(row, data, start, end, display) {
        var api = this.api();

        var intVal = function(i) {
          return typeof i === 'string' ?
            parseFloat(i.replace(/[\$,]/g, '')) :
            typeof i === 'number' ? i : 0;
        };

        var totalQty = api
          .column(4, {
            page: 'current'
          })
          .data()
          .reduce(function(a, b) {
            return intVal(a) + intVal(b);
          }, 0);

        var disQty = api
          .column(5, {
            page: 'current'
          })
          .data()
          .reduce(function(a, b) {
            return intVal(a) + intVal(b);
          }, 0);

        var penQty = api
          .column(6, {
            page: 'current'
          })
          .data()
          .reduce(function(a, b) {
            return intVal(a) + intVal(b);
          }, 0);

        // Update footer
        $(api.column(4).footer()).html(totalQty.toFixed(2));
        $(api.column(5).footer()).html(disQty.toFixed(2));
        $(api.column(6).footer()).html(penQty.toFixed(2));
        $(api.column(7).footer()).html(penQty.toFixed(2));
      },
        ajax: {
          url: "{{ route('reports.customer_outstanting') }}",
          data: function(d) {
            d.new_group = $('#ps_new_group').val(),
              d.search = $('input[type="search"]').val()
          }
        },
        columns: [{
            data: 'date',
            name: 'date',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'po_no',
            name: 'po_no',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'customer.name',
            name: 'customer.name',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'base_price',
            name: 'base_price',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'qty',
            name: 'qty',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'dispatch',
            name: 'dispatch',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'pending',
            name: 'pending',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'days',
            name: 'days',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          }
        ]
      });
      $('#ps_month').change(function() {
        table.draw();
      });
    });

    $('#reset-filter').on('click', function() {
      $('#prifilfrm').find('input:text, input:password, input:file, select, textarea').val('');
      $('#prifilfrm').find('select').change();
    })

  </script>
</x-app-layout>