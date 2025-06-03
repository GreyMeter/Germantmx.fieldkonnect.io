<x-app-layout>
  <style>
    .error {
      color: red !important;
      font-size: 14px;
      margin-top: 5px;
      display: block;
    }

    input.is-invalid,
    select.is-invalid {
      border-color: red !important;
    }
  </style>
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-12">
        <div class="card">
          @if(session('message_error'))
          <div class="alert alert-danger">
            {{ session('message_error') }}
          </div>
          @endif
          <div class="card-body">

            <div class="row">
              <div class="col-12">
                <h3 class="card-title pb-3">{!! trans('panel.order.title_singular') !!} Detail</h3>
              </div>
              <!-- /.col -->


              <div class="col-12">
                {{-- @if($orders['status'] == '0')
                @if($orders->qty > $totalOrderDispatchQty)
                <a href="{{ url('orders_confirm/' . encrypt($orders->id) . '/edit?cnf=true') }}" class="btn btn-success">Dispatch Order</a>
                <!-- <a class="btn btn-danger bg-danger">Cancle Order</a> -->
                @else
                @if($orders->status == '0')
                <button type="button" class="btn btn-success">This order has fully dispatched</button>
                @endif
                @endif --}}
                <span class="pull-right">
                  <div class="btn-group">
                  @if(auth()->user()->can(['order_print']))
                  <button class="btn btn-just-icon btn-theme mr-2" title="Print" onclick="printDivByClass('invoice')"><i class="material-icons">print</i></button>
                  @endif
                    @if(auth()->user()->can(['order_access']))
                    <a href="{{ url('orders_confirm') }}" class="btn btn-just-icon btn-theme" title="{!! trans('panel.order.title_singular') !!} {!! trans('panel.global.list') !!}"><i class="material-icons">next_plan</i></a>
                    @endif
                  </div>
                </span>
              </div>
            </div>

            <div class="invoice p-3 mb-4">
              <!-- title row -->
              <!-- <div class="row">
                <div class="col-3">
                  <h4>
                    <small class="float-left">Soda PO Number # {!! $orders['po_no'] !!}</small>
                  </h4>
                </div>

                <div class="col-4">
                  <h4> -->
              <!-- <img src="" class="brand-image" width="70px" alt="Logo"> <span> </span> -->
              <!-- <small class="float-left">Date: {!! date("d-M-Y H:i A", strtotime($orders['created_at'])) !!}</small>
                  </h4>
                </div>
                <div class="col-4">
                  <h4> -->
              <!-- <img src="" class="brand-image" width="70px" alt="Logo"> <span> </span> -->
              <!-- <small class="float-left">Created By: {!! $orders['createdbyname']?$orders['createdbyname']['name']:'Self' !!}</small>
                  </h4>
                </div> -->
              <!-- /.col -->
              <!-- </div> -->
              <hr>
              <!-- info row -->
              <div class="row invoice-info">
                <div class="col-sm-4 invoice-col">
                  <h3 style="margin-bottom: 10px;font-weight: 500;">Customer Deatils:</h3>
                  <address style="border: 1px dashed #377ab8;padding: 15px 0px;border-radius: 8px;text-align: center;box-shadow:  -3px 3px 11px 0px #377ab8;">
                    <strong>Name:{!! isset($orders['order']['customer']['name']) ? $orders['order']['customer']['name'] :'' !!} </strong><br>
                    Address:{!! $orders['order']['customer']['customeraddress']['address1']??'' !!} ,{!! $orders['order']['customer']['customeraddress']['address2']??'' !!}<br>
                    {!! $orders['order']['customer']['customeraddress']['locality']??'' !!}, {!! $orders['order']['customer']['customeraddress']['cityname']['city_name']??'' !!} {!! $orders['order']['customer']['customeraddress']['pincodename']['pincode']??'' !!}<br>
                    Phone: {!! $orders['order']['customer']['mobile'] !!}<br>
                    Email: {!! $orders['order']['customer']['email'] !!}
                  </address>
                </div>
                <div class="col-sm-4 invoice-col">
                  <h3 style="margin-bottom: 10px;font-weight: 500;">Consignee Details:</h3>
                  <address style="border: 1px dashed #377ab8;padding: 15px 0px;border-radius: 8px;text-align: center;box-shadow:  -3px 3px 11px 0px #377ab8;">
                    <strong>{!! nl2br(e($orders['consignee_details'])) !!} </strong>
                  </address>
                </div>
                <div class="col-sm-4 invoice-col">
                  <h3 style="margin-bottom: 10px;font-weight: 500;">Booking Deatils:</h3>
                  <address style="border: 1px dashed #377ab8;padding: 15px 0px;border-radius: 8px;text-align: center;box-shadow:  -3px 3px 11px 0px #377ab8;">
                    PO Number # <span style="font-weight: 800; font-size:16px;"> {!! $orders['po_no'] !!}</span> <br>
                    Order Number # <span style="font-weight: 800; font-size:16px;"> {!! $orders['confirm_po_no'] !!}</span> <br>
                    Date: {!! date("d-M-Y H:i A", strtotime($orders['created_at'])) !!} <br>
                    Base Price: {!! $orders['order']['base_price'] + $orders['order']['discount_amt'] !!} <br>
                    Created By: {!! $orders['createdbyname']?$orders['createdbyname']['name']:'Self' !!}
                  </address>
                </div>
              </div>
              <!-- /.row -->

              <!-- Table row -->
              {!! Form::model($orders,[
              'route' => ['orders.dispatch_multi', encrypt($orders->confirm_po_no) ],
              'method' => 'POST',
              'id' => 'createProductFormMulti',
              'files' => true
              ]) !!}

              <div class="row">
                <div class="col-12">
                  <!-- New Row for Driver Details -->
                  <div class="card p-3 mb-3 bg-light">
                    <h5 class="mb-3"><strong>Driver Details</strong></h5>
                    <div class="row">
                      <div class="col-md-4">
                        <label>Driver Name</label>
                        <input type="text" name="driver_name" class="form-control">
                      </div>
                      <div class="col-md-4">
                        <label>Driver Contact</label>
                        <input type="text" name="driver_contact_number" class="form-control">
                      </div>
                      <div class="col-md-4">
                        <label>Vehicle Number</label>
                        <input type="text" name="vehicle_number" id="vehicle_number" class="form-control">
                      </div>
                      <div class="col-md-3 mt-3">
                        <label>TC Image</label>
                        <input type="file" accept="image/*" name="tc" id="tc" class="form-control">
                      </div>
                      <div class="col-md-3 mt-3">
                        <label>Invoice Image</label>
                        <input type="file" accept="image/*" name="invoice" id="invoice" class="form-control">
                      </div>
                      <div class="col-md-3 mt-3">
                        <label>E-way Bill Image</label>
                        <input type="file" accept="image/*" name="e_way_bill" id="e_way_bill" class="form-control">
                      </div>
                      <div class="col-md-3 mt-3">
                        <label>Wevrage Slip Image</label>
                        <input type="file" accept="image/*" name="wevrage_slip" id="wevrage_slip" class="form-control">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-12 table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>Brand</th>
                        <th>Grade</th>
                        <th>Random Cut</th>
                        <th>Size</th>
                        <th>Material</th>
                        <th class="text-center"> Loading-Add </th>
                        <th>Total Quantity<small>(Tonn)</small></th>
                        <th>Remaining Quantity<small>(Tonn)</small></th>
                        <th class="text-center"> Additional Rate </th>
                        <th class="text-center"> Special Cut </th>
                        <th>Base Price<small>(1MT)</small></th>
                        <th>Total</th>
                        <th>Plants</th>
                      </tr>
                    </thead>
                    <tbody>
                      @if($orders->exists)
                      @foreach ($order_chain as $order)
                      <tr>
                        <td>{{$order->brands ? $order->brands->brand_name : '-'}}</td>
                        <td>{{$order->grades ? $order->grades->unit_name : '-'}}</td>
                        <td>{{$order->random_cut ? $order->random_cut : '-'}}</td>
                        <td>{{$order->sizes ? $order->sizes->category_name : '-'}}</td>
                        <td>{{$order->material}}</td>
                        <td>{{$order->loading_add}}</td>
                        <td>{{$order->qty}}</td>
                        <td>
                          <input type="number" class="form-control dispatch_qty" value="{{ getOrderQuantity($order->id) }}" name="dispatch_qty[]" step="0.01">
                        </td>
                        <td>
                          <input type="number" class="form-control additional_rate" value="{{$order->additional_rate}}" name="additional_rate[]" step="0.01">
                          <span class="badge bg-info" style="font-size: 10px;font-weight: 800;padding: 3px;">{{$order->remark}}</span>
                        </td>
                        <td>
                          <input type="text" class="form-control special_cut" value="{{$order->special_cut}}" name="special_cut[]">
                        </td>
                        <td>
                          <input type="text" class="form-control dispatch_base_price" value="{{$order->base_price}}" name="dispatch_base_price[]" readonly>
                        </td>
                        <td>
                          <input type="text" class="form-control dispatch_soda_price" name="dispatch_soda_price[]" readonly>
                        </td>
                        <td>
                          <select class="form-control" name="plant_id[]" style="width: 100%;" {{isset($cnf) ? 'required' : ''}} required>
                            <option value="">Select Plant</option>
                            @if(@isset($plants))
                            @foreach($plants as $key => $plant)
                            <option value="{{ $plant['id'] }}" {{ $key+1 == 1 ? 'selected' : '' }}>
                              {{ $plant['plant_name'] }}
                            </option>
                            @endforeach
                            @endif
                          </select>
                        </td>
                      </tr>
                      @endforeach
                      @endif
                    </tbody>
                  </table>
                </div>
                <div class="col-12">
                  @if($orders['status'] == '0' || $orders->status == '4')
                  @if(!getOrderQuantityByPo($orders->confirm_po_no))
                  <button type="submit" class="btn btn-success">Dispatch Order</button>
                  {{-- <a href="{{ url('orders_confirm/' . encrypt($orders->id) . '/edit?cnf=true') }}" class="btn btn-success">Dispatch Order</a> --}}
                  <a class="btn btn-danger bg-danger" type="button" id="cancleButton" data-po_no="{{ $orders->confirm_po_no }}">Cancle Order</a>
                  @elseif($orders->qty == '0' && $orders->status == '4')
                  <button type="button" class="btn btn-danger">Canclled</button>
                  <span class="badge bg-info" style="font-size: 12px;font-weight: 600;padding: 3px;">Cancle Remark: {{ $orders->cancel_remark }}</span
                  @else
                  <button type="button" class="btn btn-success">This order has fully dispatched</button>
                  @endif
                  @endif
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
              {{ Form::close() }}
              <div class="row">
                <!-- accepted payments column -->
                <div class="col-6">
                  <p class="lead"></p>
                  <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">

                  </p>
                </div>
                <!-- /.col -->
              </div>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
  </section>
  <script>
    $("#cancleButton").on("click", function() {
      Swal.fire({
        title: "Are you sure?",
        text: "Enter remark:",
        icon: "warning",
        input: 'text',
        returnInputValueOnDeny: true,
        showCancelButton: true,
        confirmButtonText: 'Yes Cancle',
        cancelButtonText: 'No',
        inputValidator: (value) => {
          if (!value) {
            return "You need to write something!";
          }
        }
      }).then((result) => {
        if (result.value) {
          var token = $("meta[name='csrf-token']").attr("content");
          var base_url = $('.baseurl').data('baseurl');
          var orderId = $(this).data("po_no");
          $.ajax({
            url: base_url + '/orders_confirm_cancel',
            dataType: "json",
            type: "POST",
            data: {
              _token: token,
              order_confirm_id: orderId,
              remark: result.value
            },
            success: function(res) {
              Swal.fire({
                title: res.status,
                text: res.message,
              });
              // if (res.status == 'success') {
              //   window.location.href = base_url + '/orders_confirm';
              // }
            }
          });
        }
      });

    })

    $('disptch_qty_change').on('change', function() {
      var row = $(this).closest('tr');
    }).trigger('change');

    $(document).ready(function() {
      function calculateTotal(row) {
        var qty = parseFloat(row.find('.dispatch_qty').val()) || 0;
        var additionalRate = parseFloat(row.find('.additional_rate').val()) || 0;
        // var specialCut = parseFloat(row.find('.special_cut').val()) || 0;
      
        var basePrice = parseFloat(row.find('.dispatch_base_price').val()) || 0;
        var total = ((qty * basePrice)+(qty * additionalRate)).toFixed(2);
        row.find('.dispatch_soda_price').val(total);
      }

      $.validator.addMethod("vehicleFormat", function(value, element) {
        return value === "" || value === null || /^[A-Z]{2} \d{2} [A-Z]{2} \d{4}$/.test(value);
      }, "Invalid format! Example: MP 12 XX 1234");


      $('#createProductFormMulti').validate({
        rules: {
          driver_name: {
            minlength: 3
          },
          driver_contact: {
            digits: true,
            minlength: 10,
            maxlength: 10
          },
          vehicle_number: {
            vehicleFormat: true
          }
        },
        errorClass: "error", // Use the correct error class
        highlight: function(element) {
          $(element).addClass('is-invalid'); // Add red border
        },
        unhighlight: function(element) {
          $(element).removeClass('is-invalid'); // Remove red border
        }
      });

      $('#createProductFormMulti').validate({

      });

      // Run calculation on page load
      $('tr').each(function() {
        calculateTotal($(this));
      });

      // Trigger calculation when quantity or base price changes
      $(document).on('input', '.dispatch_qty, .additional_rate', function() {
        var row = $(this).closest('tr');
        calculateTotal(row);
      });
    });

    $(document).ready(function() {
      $('#vehicle_number').on('input', function() {
        var inputVal = $(this).val().replace(/\s+/g, '').toUpperCase(); // Remove spaces and convert to uppercase
        var formattedVal = '';

        if (inputVal.length > 0) {
          formattedVal = inputVal.substring(0, 2); // State Code
        }
        if (inputVal.length > 2) {
          formattedVal += ' ' + inputVal.substring(2, 4); // District Code
        }
        if (inputVal.length > 4) {
          formattedVal += ' ' + inputVal.substring(4, 6); // Two Alphabets
        }
        if (inputVal.length > 6) {
          formattedVal += ' ' + inputVal.substring(6, 10); // Four Digits
        }

        $(this).val(formattedVal); // Set formatted value

        // Validation: MP 12 AB 1234
        var vehiclePattern = /^[A-Z]{2} \d{2} [A-Z]{2} \d{4}$/;
        if (!vehiclePattern.test(formattedVal)) {
          $('#vehicle_error').removeClass('d-none'); // Show error message
          $(this).addClass('is-invalid'); // Add red border
        } else {
          $('#vehicle_error').addClass('d-none'); // Hide error message
          $(this).removeClass('is-invalid'); // Remove red border
        }
      });
    });
  </script>
  <!-- /.content -->
</x-app-layout>