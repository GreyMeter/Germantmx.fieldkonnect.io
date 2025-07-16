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
                <h3 class="card-title pb-3">{!! trans('panel.order.title_singular') !!} Edit</h3>
              </div>
              <!-- /.col -->


              <div class="col-12">
                {{-- @if($orders['status'] == '0')
                @if($orders->qty > $totalOrderDispatchQty)
                <a href="{{ url('orders_confirm/' . encrypt($orders->id) . '/edit?cnf=true') }}" class="btn btn-success">Dispatch Order</a>
                <!-- <a class="btn btn-danger bg-danger">Cancle Order</a> -->
                @else
                <button type="button" class="btn btn-success">This order has fully dispatched</button>
                @endif
                @endif --}}
                <span class="pull-right">
                  <div class="btn-group">
                    @if(auth()->user()->can(['order_access']))
                    <a href="{{ url('orders_confirm') }}" class="btn btn-just-icon btn-theme" title="{!! trans('panel.order.title_singular') !!}{!! trans('panel.global.list') !!}"><i class="material-icons">next_plan</i></a>
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
              {!! Form::model($orders,[
              'route' => ['confirm_order.update'],
              'method' => 'POST',
              'id' => 'UpdateOrderForm',
              ]) !!}
              <div class="row invoice-info mb-3">
                <div class="col-sm-4 invoice-col">
                  <h3 style="margin-bottom: 10px;font-weight: 500;">Customer Deatils:</h3>
                  <address style="border: 1px dashed #377ab8;padding: 20px 15px;border-radius: 8px;text-align: center;box-shadow:  -3px 3px 11px 0px #377ab8;">
                    <strong>Name:{!! isset($orders['order']['customer']['name']) ? $orders['order']['customer']['name'] :'' !!} </strong><br>
                    Address:{!! $orders['order']['customer']['customeraddress']['address1']??'' !!} ,{!! $orders['order']['customer']['customeraddress']['address2']??'' !!}<br>
                    {!! $orders['order']['customer']['customeraddress']['locality']??'' !!}, {!! $orders['order']['customer']['customeraddress']['cityname']['city_name']??'' !!} {!! $orders['order']['customer']['customeraddress']['pincodename']['pincode']??'' !!}<br>
                    Phone: {!! $orders['order']['customer']['mobile'] !!}<br>
                    Email: {!! $orders['order']['customer']['email'] !!}
                  </address>
                </div>
                <div class="col-sm-4 invoice-col">
                  <h3 style="margin-bottom: 10px; font-weight: 500;">Consignee Details:</h3>
                  <div style="border: 1px dashed #377ab8; padding: 10px; border-radius: 8px; box-shadow: -3px 3px 11px 0px #377ab8;">
                    <div class="d-flex align-items-center mb-1">
                      <label for="consignee_details" class="text-dark m-0" style="width: 160px; font-weight: bold;">Consignee Name:</label>
                      <input type="text" name="consignee_details" id="consignee_details" class="form-control" value="{!! $orders['consignee_details'] !!}">
                    </div>
                    <div class="d-flex align-items-center mb-1">
                      <label for="gst_number" class="text-dark m-0" style="width: 160px; font-weight: bold;">GST Number:</label>
                      <input type="text" name="gst_number" id="gst_number" class="form-control" value="{!! $orders['gst_number'] !!}">
                    </div>
                    <div class="d-flex align-items-center mb-1">
                      <label for="delivery_address" class="text-dark m-0" style="width: 160px; font-weight: bold;">Delivery Address:</label>
                      <input type="text" name="delivery_address" id="delivery_address" class="form-control" value="{!! $orders['delivery_address'] !!}">
                    </div>
                    <div class="d-flex align-items-center">
                      <label for="supervisor_number" class="text-dark m-0" style="width: 160px; font-weight: bold;">Supervisor Contact Number:</label>
                      <input type="text" name="supervisor_number" id="supervisor_number" class="form-control" value="{!! $orders['supervisor_number'] !!}">
                    </div>
                  </div>
                </div>

                <div class="col-sm-4 invoice-col">
                  <h3 style="margin-bottom: 10px;font-weight: 500;">Booking Deatils:</h3>
                  <address style="border: 1px dashed #377ab8;padding: 20px 15px;border-radius: 8px;text-align: center;box-shadow:  -3px 3px 11px 0px #377ab8;">
                    PO Number # <span style="font-weight: 800; font-size:16px;"> {!! $orders['po_no'] !!}</span> <br>
                    Order Number # <span style="font-weight: 800; font-size:16px;"> {!! $orders['confirm_po_no'] !!}</span> <br>
                    Base Price # <span style="font-weight: 800; font-size:16px;"> {!! $orders->order->base_price+$orders->order->discount_amt !!}</span> <br><br>
                    Date: {!! date("d-M-Y H:i A", strtotime($orders['created_at'])) !!} <br>
                    Created By: {!! $orders['createdbyname']?$orders['createdbyname']['name']:'Self' !!}
                  </address>
                </div>
              </div>
              <!-- /.row -->

              <!-- Table row -->

              <div class="row">
                {{--<div class="col-12">
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
                        <input type="text" name="vehicle_number" id="vehicle_number" class="form-control" value="{{old('vehicle_number')}}">
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
                </div>--}}
                <div class="col-12 table-responsive">
                  <span class="badge badge-danger" id="all-qty-errors"></span>
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
                        <!-- <th>Remaining Quantity<small>(Tonn)</small></th> -->
                        <th class="text-center"> Additional Rate </th>
                        <th class="text-center"> Special Cut </th>
                        <th>Base Price<small>(1MT)</small></th>
                        <th>Total</th>
                        <!-- <th>Plants</th> -->
                      </tr>
                    </thead>
                    <tbody>
                      @if($orders->exists)
                      @foreach ($order_chain as $order)
                      <input type="hidden" name="base_price" id="base_price" value="{{$order->order->base_price+$order->order->discount_amt}}">
                      <input type="hidden" name="order_customer_id" id="order_customer_id" value="{!! $order->order->customer_id !!}">
                      <tr>
                        <input type="hidden" name="order_ids[]" value="{{$order->id}}">
                        <td>
                          <select required name="brand_id[]" class="form-control brand' + counter + ' brand_change">
                            <option value="">Select Brand</option>
                            @if(@isset($brands))
                            @foreach($brands as $key => $brand)
                            <option value="{{ $brand['id'] }}" {{ $order->brand_id == $brand['id'] ? 'selected' : '' }}>
                              {{ $brand['brand_name'] }}
                            </option>
                            @endforeach
                            @endif
                          </select>
                        </td>
                        <td>
                          <select name="grade_id[]" class="form-control grade_change">
                            <option value="">Select Grade</option>
                            @if(@isset($units))
                            @foreach($units as $key => $grade)
                            <option value="{{ $grade['id'] }}" {{ $order->unit_id == $grade['id'] ? 'selected' : '' }}>
                              {{ $grade['unit_name'] }}
                            </option>
                            @endforeach
                            @endif
                          </select>
                        </td>
                        <td>
                          <select name="random_cut[]" class="form-control random_cut' + counter + ' random_cut_change">
                            <option value="">Slecte Random Cut</option>
                            <option {{ $order->random_cut == '10-25' ? 'selected' : '' }} value="10-25">10-25</option>
                            <option {{ $order->random_cut == '25-35' ? 'selected' : '' }} value="25-35">25-35</option>
                          </select>
                        </td>
                        <td>
                          <select required name="category_id[]" class="form-control allsizes size_change">
                            <option value="">Select Size</option>
                            @if(@isset($sizes))
                            @foreach($sizes as $key => $size)
                            <option value="{{ $size['id'] }}" {{ $order->category_id == $size['id'] ? 'selected' : '' }}>
                              {{ $size['category_name'] }}
                            </option>
                            @endforeach
                            @endif
                          </select>
                        </td>
                        <td>
                          <select required name="material[]" class="form-control material' + counter + ' material_change">
                            <option value="">Select Material</option>
                            <option {{$order->material == 'Straight' ? 'selected' : ''}} value="Straight">Straight</option>
                            <option {{$order->material == 'Bend' ? 'selected' : ''}} value="Bend">Bend</option>
                          </select>
                        </td>
                        <td><select name="loading_add[]" class="form-control loading_add' + counter + ' loading_add_change">
                            <option value="">Select Loading</option>
                            <option {{$order->loading_add == 'Up' ? 'selected' : ''}} value="Up">Up</option>
                            <option {{$order->loading_add == 'Down' ? 'selected' : ''}} value="Down">Down</option>
                          </select></td>
                        <td><input required type="number" step="0.01" name="qty[]" value="{{ $order->qty }}" class="form-control points rowchange" /></td>
                        <input type="hidden" class="form-control dispatch_qty" value="{{ getOrderQuantity($order->id) }}" name="dispatch_qty[]" step="1">
                        <td>
                          <input type="number" step="0.01" name="additional_rate[]" value="{{ $order->additional_rate }}" class="form-control additional_rate rowchange" />
                          <span class="badge bg-info" style="font-size: 10px;font-weight: 800;padding: 3px;">{{$order->remark}}</span>
                        </td>
                        <td>
                          <input type="text" name="special_cut[]" value="{{ $order->special_cut }}" class="form-control special_cut rowchange" />
                        </td>
                        <td>
                          <input type="text" class="form-control dispatch_base_price" value="{{$order->base_price}}" name="dispatch_base_price[]" readonly>
                        </td>
                        <td>
                          <input type="text" class="form-control dispatch_soda_price" name="dispatch_soda_price[]" readonly>
                        </td>
                        {{--<td>
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
                        </td>--}}
                      </tr>
                      @endforeach
                      @endif
                    </tbody>
                  </table>
                </div>
                <div class="col-12">
                  @if($orders['status'] == '0')
                  @if(!getOrderQuantityByPo($orders->confirm_po_no))
                  <button type="submit" class="btn btn-success">Update Order</button>
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
          var orderId = $(this).data("orderid");
          $.ajax({
            url: base_url + '/order-cancle/' + orderId,
            dataType: "json",
            type: "POST",
            data: {
              _token: token,
              remark: result.value
            },
            success: function(res) {
              Swal.fire({
                title: res.status,
                text: res.message,
              });
              if (res.status == 'success') {
                window.location.href = base_url + '/orders';
              }
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
        var total = ((qty * basePrice) + (qty * additionalRate)).toFixed(2);
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
          // vehicle_number: {
          //   vehicleFormat: true
          // }
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
      $(document).on('input', '.dispatch_qty', function() {
        var row = $(this).closest('tr');
        calculateTotal(row);
      });
    });

    // $(document).ready(function() {
    //   $('#vehicle_number').on('input', function() {
    //     var inputVal = $(this).val().replace(/\s+/g, '').toUpperCase(); // Remove spaces and convert to uppercase
    //     var formattedVal = '';

    //     if (inputVal.length > 0) {
    //       formattedVal = inputVal.substring(0, 2); // State Code
    //     }
    //     if (inputVal.length > 2) {
    //       formattedVal += ' ' + inputVal.substring(2, 4); // District Code
    //     }
    //     if (inputVal.length > 4) {
    //       formattedVal += ' ' + inputVal.substring(4, 6); // Two Alphabets
    //     }
    //     if (inputVal.length > 6) {
    //       formattedVal += ' ' + inputVal.substring(6, 10); // Four Digits
    //     }

    //     $(this).val(formattedVal); // Set formatted value

    //     // Validation: MP 12 AB 1234
    //     var vehiclePattern = /^[A-Z]{2} \d{2} [A-Z]{2} \d{4}$/;
    //     if (!vehiclePattern.test(formattedVal)) {
    //       $('#vehicle_error').removeClass('d-none'); // Show error message
    //       $(this).addClass('is-invalid'); // Add red border
    //     } else {
    //       $('#vehicle_error').addClass('d-none'); // Hide error message
    //       $(this).removeClass('is-invalid'); // Remove red border
    //     }
    //   });
    // });

    let syncingBrands = false;

    $(document).on('change', '.brand_change, .grade_change, .size_change, .material_change', function() {
      if (syncingBrands) return;

      // Sync brand values
      let firstValue = null;
      let firstValueM = null;
      $('.brand_change').each(function() {
        let val = $(this).val();
        if (val) {
          firstValue = val;
          return false; // break
        }
      });

      if (firstValue !== null) {
        syncingBrands = true; // Prevent recursion
        $('.brand_change').each(function() {
          $(this).val(firstValue);
        });
        syncingBrands = false;
      }

      // Sync material values
      $('.material_change').each(function() {
        let val = $(this).val();
        if (val) {
          firstValueM = val;
          return false; // break
        }
      });
      if (firstValueM !== null) {
        syncingBrands = true; // Prevent recursion
        $('.material_change').each(function() {
          $(this).val(firstValueM);
        });
        syncingBrands = false;
      }
      // Now do row-specific logic
      var tBody = $(this).closest('tbody');
      tBody.find('tr').each(function() {
        var row = $(this);

        row.find('.dispatch_soda_price').val(''); // Update the booking price in the row
        row.find('.dispatch_base_price').val('');
        var brand = row.find('.brand_change').val();
        var grade = row.find('.grade_change').val();
        var size = row.find('.size_change').val();
        var material = row.find('.material_change').val();
        var quantity = row.find('.points').val();
        var additionalRate = row.find('.additional_rate').val();
        // var specialCut = row.find('.special_cut').val();

        if (brand && size) {
          getPrices(brand, grade, size, material, quantity, row, additionalRate);
        } else {
          row.find('.dispatch_soda_price').text(''); // Update the booking price in the row
          row.find('.dispatch_base_price').text('');
        }
      });
    });


    $('.points, .additional_rate').on('input', function() {
      var row = $(this).closest('tr'); // Get the closest row of the changed input/select

      row.find('.dispatch_soda_price').val(''); // Update the booking price in the row
      row.find('.dispatch_base_price').val('');
      var brand = row.find('.brand_change').val();
      var grade = row.find('.grade_change').val();
      var size = row.find('.size_change').val();
      var material = row.find('.material_change').val();
      var quantity = row.find('.points').val();
      var additionalRate = row.find('.additional_rate').val();
      // var specialCut = row.find('.special_cut').val();
      if (brand && size) {
        getPrices(brand, grade, size, material, quantity, row, additionalRate);
      }
    });

    function getPrices(brand = '', grade = '', size = '', material = '', quantity = 1, row, additionalRate = 0) {
      var bookingPrice = $('#base_price').val(); // Example booking price
      var totalPrice = ''; // Example total price
      var additional_charge = ''
      var customer_id = $('#order_customer_id').val();
      if (brand && size) {
        // Simulate price calculations (replace with your actual logic)
        $.ajax({
          url: "{{ url('getPricesOfOrder') }}",
          data: {
            "brand": brand,
            "grade": grade,
            "size": size,
            "customer_id": customer_id,
          },
          success: function(res) {
            if (res.status == true) {
              bookingPrice = parseFloat(bookingPrice, 10) + parseFloat(res.additional_price, 10);
              row.find('.dispatch_base_price').val(bookingPrice.toFixed(2));

              var qty = parseFloat(quantity || 1, 10); // default to 1 if quantity is not set
              var baseTotal = qty * bookingPrice;
              var additionalTotal = additionalRate ? parseFloat(additionalRate, 10) * qty : 0;
              // var cutTotal = specialCut ? parseFloat(specialCut, 10) * qty : 0;

              var total_value = baseTotal + additionalTotal;

              row.find('.dispatch_soda_price').val(total_value.toFixed(2));
            }

          }
        });
      }
    }

    $('#UpdateOrderForm').on('submit', function(e) {
      let isValid = true;
      let errorMessage = '';

      $('table.table tbody tr').each(function(index) {
        const grade = $(this).find('select[name="grade_id[]"]').val();
        const randomCut = $(this).find('select[name="random_cut[]"]').val();
        if ((!grade && !randomCut) || (grade && randomCut)) {
          isValid = false;
          errorMessage = `In row ${index + 1}, select either Grade or Random Cut, not both or none.`;
          return false; // Stop the loop on first error
        }
      });

      if (!isValid) {
        e.preventDefault();
        $('#all-qty-errors').html(errorMessage);
      }
    });

    $(document).ready(function() {
      $("#consignee_details").autocomplete({
        source: function(request, response) {
          $.ajax({
            url: "/consignee-suggestions",
            data: {
              term: request.term
            },
            success: function(data) {
              response(data.results);
            }
          });
        },
        minLength: 1
      });
    });
  </script>
  <!-- /.content -->
</x-app-layout>