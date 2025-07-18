<x-app-layout>
  <style>
    #copyText {
      cursor: pointer;
      font-weight: 800;
      color: #000;
      text-shadow: 0 0 3px #fff;
    }
  </style>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">Booking {!! trans('panel.global.list') !!}
            <span class="">
              <div class="btn-group header-frm-btn">
                @if(auth()->user()->can(['order_download']))
                <form method="GET" action="{{ URL::to('orders-download') }}">
                  <div class="d-flex flex-wrap flex-row">
                    <div class="p-2" style="width:190px;">
                      <select class="select2" name="customer_id" id="customer_id">
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                        <option value="{{$customer->id}}">{{$customer->name}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="p-2"><input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly></div>
                    <div class="p-2"><input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly></div>
                    <div class="p-2"><button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} Bookings"><i class="material-icons">cloud_download</i></button></div>
                  </div>
                </form>
                @endif

                <div class="next-btn">

                  @if(auth()->user()->can(['order_upload']))
                  <form action="{{ URL::to('orders-upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="input-group">
                      <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                        <span class="btn btn-just-icon btn-theme btn-file">
                          <span class="fileinput-new"><i class="material-icons">attach_file</i></span>
                          <span class="fileinput-exists">Change</span>
                          <input type="hidden">
                          <input type="file" name="import_file" required accept=".xls,.xlsx" />
                        </span>
                      </div>
                      <div class="input-group-append">
                        <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} {!! trans('panel.order.title') !!}">
                          <i class="material-icons">cloud_upload</i>
                          <div class="ripple-container"></div>
                        </button>
                      </div>
                    </div>
                  </form>
                  @endif

                  <!-- @if(auth()->user()->can(['order_download']))
                  <a href="{{ URL::to('orders-download') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.order.title') !!}"><i class="material-icons">cloud_download</i></a>
                  @endif -->

                  @if(auth()->user()->can(['order_template']))
                  <a href="{{ URL::to('orders-template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} {!! trans('panel.order.title_singular') !!}"><i class="material-icons">text_snippet</i></a>
                  @endif
                  @if(auth()->user()->can(['order_create']))
                  <a href="{{ route('orders.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} {!! trans('panel.order.title_singular') !!}"><i class="material-icons">add_circle</i></a>
                  @endif
                </div>
              </div>
            </span>
          </h4>
        </div>
        <div class="card-body">
          @if(session()->has('message_success'))
          <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span>
              {!!session()->get('message_success') !!}
            </span>
          </div>
          @endif
          @if(session()->has('message_danger'))
          <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span>
              {!!session()->get('message_danger') !!}
            </span>
          </div>
          @endif
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
          <div class="alert " style="display: none;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span class="message"></span>
          </div>
          <div class="table-responsive">
            <table id="getorder" class="table table-striped- table-bordered table-hover table-checkable no-wrap">
              <thead class=" text-primary">
                <th>{!! trans('panel.global.action') !!}</th>
                <th>PO No.</th>
                <th>Distributor/Dealer Name</th>
                <th>Distributor/Dealer PO No</th>
                <!-- <th>Grade</th>
                <th>Size</th> -->
                <th>Quantity<small>(Tonn)</small></th>
                <th>Base Price<small>(1MT)</small></th>
                <th>Status </th>
                <!-- <th>{!! trans('panel.global.created_by') !!}</th> -->
                <th>{!! trans('panel.global.created_at') !!}</th>
              </thead>
              <tbody>
              </tbody>
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
      var table = $('#getorder').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: "{{ route('orders.index') }}",
          data: function(d) {
            d.customer_id = $('#customer_id').val();
            d.start_date = $('#start_date').val();
            d.end_date = $('#end_date').val();
          }
        },
        columns: [{
            data: 'action',
            name: 'action',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'po_no',
            name: 'po_no',
            orderable: false
          },
          {
            data: 'customer.name',
            name: 'customer.name',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'customer.customer_po_no',
            name: 'customer.customer_po_no',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'qty',
            name: 'qty',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'base_price',
            name: 'base_price',
            "defaultContent": '',
            orderable: false,
            render: function(data, type, row) {
              if (data && row.discount_amt !== undefined) {
                const discountedPrice = parseFloat(data) + parseFloat(row.discount_amt);
                return '₹ ' + discountedPrice.toLocaleString('en-US', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                });
              }
              return '';
            }
          },
          {
            data: 'status',
            name: 'status',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'created_at',
            name: 'created_at',
            orderable: false,
            "defaultContent": ''
          },
        ]
      });

      $('#customer_id').change(function() {
        table.draw();
      });

      $('#start_date').change(function() {
        var selectedStartDate = $('#start_date').datepicker('getDate');
        $('#end_date').datepicker("option", "minDate", selectedStartDate);
        table.draw();
      });
      $('#end_date').change(function() {
        table.draw();
      });

      $('body').on('click', '.activeRecord', function() {
        var id = $(this).attr("id");
        var active = $(this).attr("value");
        var status = '';
        if (active == 'Y') {
          status = 'Incative ?';
        } else {
          status = 'Ative ?';
        }
        var token = $("meta[name='csrf-token']").attr("content");
        if (!confirm("Are You sure want " + status)) {
          return false;
        }
        $.ajax({
          url: "{{ url('orders-active') }}",
          type: 'POST',
          data: {
            _token: token,
            id: id,
            active: active
          },
          success: function(data) {
            $('.message').empty();
            $('.alert').show();
            if (data.status == 'success') {
              $('.alert').addClass("alert-success");
            } else {
              $('.alert').addClass("alert-danger");
            }
            $('.message').append(data.message);
            table.draw();
          },
        });
      });

      $('body').on('click', '.delete', function() {
        var id = $(this).attr("value");
        var token = $("meta[name='csrf-token']").attr("content");
        if (!confirm("Are You sure want to delete ?")) {
          return false;
        }
        $.ajax({
          url: "{{ url('orders') }}" + '/' + id,
          type: 'DELETE',
          data: {
            _token: token,
            id: id
          },
          success: function(data) {
            $('.alert').show();
            if (data.status == 'success') {
              $('.alert').addClass("alert-success");
            } else {
              $('.alert').addClass("alert-danger");
            }
            $('.message').append(data.message);
            table.draw();
          },
        });
      });

    });

    $(document).ready(function() {
      $("#copyText").click(function() {
        var textToCopy = $("#copyText").text();
        var tempInput = $("<input>");
        $("body").append(tempInput);
        tempInput.val(textToCopy).select();
        document.execCommand("copy");
        tempInput.remove();
        const Toast = Swal.mixin({
          toast: true,
          position: "top-end",
          showConfirmButton: false,
          timer: 4000,
          timerProgressBar: true,
          didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
          }
        });
        Toast.fire({
          icon: "success",
          title: "PO Number copied to clipboard: " + textToCopy
        });
      });
    });
  </script>
</x-app-layout>