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
          <h4 class="card-title">Pendency Report
            <span class="">
              <div class="btn-group header-frm-btn">
                @if(auth()->user()->can(['customer_outstanting_download']))
                <form method="POST" action="{{url('customer_outstanting/download')}}">
                  @csrf
                  <div class="d-flex flex-wrap flex-row" style="align-items: self-end;">
                    <!-- Date filter -->
                    <div class="p-2" style="width:150px;"><label for="start_date">Start Date</label><input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly></div>
                    <div class="p-2" style="width:150px;"><label for="end_date">End Date</label><input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly></div>
                    <!-- PO Number filter -->
                    <div class="p-2" style="width:200px;">
                      <label for="po_no">PO Number</label>
                      <select class="select2" name="po_no[]" placeholder="po_no" multiple id="po_no" data-style="select-with-transition" title="PO Number">
                        <option value="" disabled>Select PO Number</option>
                        @if(@isset($po_nos ))
                        @foreach($po_nos as $po_no)
                        <option value="{!! $po_no->po_no !!}">{!! $po_no->po_no !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <!-- Party Name filter -->
                    <div class="p-2" style="width:200px;">
                      <label for="customer_id">Party Name</label>
                      <select class="select2" name="customer_id[]" placeholder="customer_id" multiple id="customer_id" data-style="select-with-transition" title="Party Name">
                        <option value="" disabled>Select Party Name</option>
                        @if(@isset($partys ))
                        @foreach($partys as $party)
                        <option value="{!! $party->id !!}">{!! $party->name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
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
                <!-- <th>Pending Dispatch QTY</th> -->
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
        initComplete: function () {
            let searchInput = $('div.dataTables_filter input');
            searchInput.attr('id', 'myCustomSearch');
        },
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

        // var pendisQty = api
        //   .column(5, {
        //     page: 'current'
        //   })
        //   .data()
        //   .reduce(function(a, b) {
        //     return intVal(a) + intVal(b);
        //   }, 0);

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
        // $(api.column(5).footer()).html(pendisQty.toFixed(2));
        $(api.column(5).footer()).html(disQty.toFixed(2));
        $(api.column(6).footer()).html(penQty.toFixed(2));
      },
      ajax: {
        url: "{{ route('reports.customer_outstanting') }}",
        data: function(d) {
            d.search = $('#myCustomSearch').val(),
            d.start_date = $('#start_date').val(),
            d.end_date = $('#end_date').val(),
            d.po_no = $('#po_no').val(),
            d.customer_id = $('#customer_id').val()
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
          // {
          //   data: 'pending_dispatch',
          //   name: 'pending_dispatch',
          //   orderable: false,
          //   "defaultContent": ''
          // },
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
      $('#start_date').change(function() {
        var selectedStartDate = $('#start_date').datepicker('getDate');
      $('#end_date').datepicker("option", "minDate", selectedStartDate);
        table.draw();
      });
      $('#end_date').change(function() {
        table.draw();
      });
      $('#po_no').change(function() {
        table.draw();
      });
      $('#customer_id').change(function() {
        table.draw();
      });
    });

    $('#reset-filter').on('click', function() {
      $('#prifilfrm').find('input:text, input:password, input:file, select, textarea').val('');
      $('#prifilfrm').find('select').change();
    })

  </script>
</x-app-layout>