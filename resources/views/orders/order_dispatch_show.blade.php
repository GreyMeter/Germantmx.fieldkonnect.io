<x-app-layout>
  <style>
    .attachment-heading {
      font-size: 22px;
      font-weight: bold;
      color: #343a40;
      margin-bottom: 20px;
      border-bottom: 3px solid #007bff;
      display: inline-block;
      padding-bottom: 5px;
    }

    .image-card {
      text-align: center;
      background: #ffffff;
      padding: 10px;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      margin-bottom: 15px;
      transition: transform 0.3s ease-in-out;
    }

    .image-card:hover {
      transform: scale(1.05);
    }

    .collection-name {
      font-size: 14px;
      font-weight: bold;
      text-transform: uppercase;
      color: #007bff;
      margin-bottom: 8px;
    }

    .img-thumbnail {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border: 2px solid #ddd;
      border-radius: 5px;
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
                <h3 class="card-title pb-3">{!! trans('panel.order_dispatch.title') !!} Detail</h3>
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
                  @if(auth()->user()->can(['dispatch_print']))
                  <button class="btn btn-just-icon btn-theme mr-2" title="Print" onclick="printDivByClass('invoice')"><i class="material-icons">print</i></button>
                  @endif
                    @if(auth()->user()->can(['order_access']))
                    <a href="{{ url('orders_dispatch') }}" class="btn btn-just-icon btn-theme" title="Dispatch {!! trans('panel.global.list') !!}"><i class="material-icons">next_plan</i></a>
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
                    <strong>{!! nl2br(e($orders->order_confirm['consignee_details'])) !!} </strong>
                  </address>
                </div>
                <div class="col-sm-4 invoice-col">
                  <h3 style="margin-bottom: 10px;font-weight: 500;">Booking Deatils:</h3>
                  <address style="border: 1px dashed #377ab8;padding: 15px 0px;border-radius: 8px;text-align: center;box-shadow:  -3px 3px 11px 0px #377ab8;">
                    PO Number # <span style="font-weight: 800; font-size:16px;"> {!! $orders['po_no'] !!}</span> <br>
                    Order Number # <span style="font-weight: 800; font-size:16px;"> {!! $orders['confirm_po_no'] !!}</span> <br>
                    Order Dispatch Number # <span style="font-weight: 800; font-size:16px;"> {!! $orders['dispatch_po_no'] !!}</span> <br>
                    Date: {!! date("d-M-Y H:i A", strtotime($orders['created_at'])) !!} <br>
                    Base Price: {!! $orders['order']['base_price'] + $orders['order']['discount'] !!} <br>
                    Created By: {!! $orders['createdbyname']?$orders['createdbyname']['name']:'Self' !!}
                  </address>
                </div>
              </div>
              <!-- /.row -->

              <!-- Table row -->

              <div class="row">
                <form id="updateOrderDispatch" action="{{route('order_dispatch_update', $dispatch_orders[0]->order_dispatch_details->id)}}" method="post" enctype="multipart/form-data">
                  <div class="col-12">
                    <!-- New Row for Driver Details -->
                    <div class="card p-3 mb-3 bg-light">
                      <h5 class="mb-3"><strong>Driver Details</strong></h5>
                      @if(session()->has('message_success'))
                      <div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <i class="material-icons">close</i>
                        </button>
                        <span>
                          {{ session()->get('message_success') }}
                        </span>
                      </div>
                      @endif
                      @csrf
                      <div class="row">
                        <div class="col-md-4">
                          <label>Driver Name</label>
                          <input type="text" name="driver_name" class="form-control" value="{{ isset($dispatch_orders[0]->order_dispatch_details->driver_name) ? $dispatch_orders[0]->order_dispatch_details->driver_name : '' }}" {{auth()->user()->can('order_dispatch_update') ? '' : 'readonly'}}>
                        </div>
                        <div class="col-md-4">
                          <label>Driver Contact</label>
                          <input type="text" name="driver_contact_number" class="form-control" value="{{ isset($dispatch_orders[0]->order_dispatch_details->driver_contact_number) ? $dispatch_orders[0]->order_dispatch_details->driver_contact_number : '' }}" {{auth()->user()->can('order_dispatch_update') ? '' : 'readonly'}}>
                        </div>
                        <div class="col-md-4">
                          <label>Vehicle Number</label>
                          <input type="text" name="vehicle_number" id="vehicle_number" class="form-control" value="{{ isset($dispatch_orders[0]->order_dispatch_details->vehicle_number) ? $dispatch_orders[0]->order_dispatch_details->vehicle_number : '' }}" {{auth()->user()->can('order_dispatch_update') ? '' : 'readonly'}}>
                        </div>
                        @if(auth()->user()->can('order_dispatch_update'))
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
                        @endif
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
                          <th>Total Quantity<small>(Tonn)</small></th>
                          <th>Loading-Add </th>
                          <th>Base Price<small>(1MT)</small></th>
                          <th>Additional Rate</th>
                          <th>Special Cut</th>
                          <th>Total</th>
                          <th>Plants</th>
                        </tr>
                      </thead>
                      <tbody>
                        @if($orders->exists)
                        @foreach ($dispatch_orders as $order)
                        <tr>
                          <input type="hidden" name="order_id[]" value="{{$order->id}}">
                          <input type="hidden" name="brand_id[]" value="{{$order->brand_id}}">
                          <input type="hidden" name="unit_id[]" value="{{$order->unit_id}}">
                          <input type="hidden" name="category_id[]" value="{{$order->category_id}}">
                          <td>{{$order->brands ? $order->brands->brand_name : '-'}}</td>
                          <td>{{$order->grades ? $order->grades->unit_name : '-'}}</td>
                          <td>{{$order->order_confirm ? $order->order_confirm->random_cut : '-'}}</td>
                          <td>{{$order->sizes ? $order->sizes->category_name : '-'}}</td>
                          <td>{{$order->order_confirm->material ?? ''}}</td>
                          <td><input type="number" {{auth()->user()->can('order_dispatch_update') ? '' : 'readonly'}} class="form-control quantitys" step="0.01" min="0.01" max="{{$order->order_confirm->qty - $order->order_confirm->orderDispatch->reject(function ($dispatch) use ($order) {return $dispatch->id === $order->id;})->sum('qty') }}" name="qty[]" value="{{$order->qty}}"></td>
                          <td>{{$order->order_confirm->loading_add}}</td>
                          <td>
                            {{$order->base_price ?? ''}}
                          </td>
                          <td>
                            {{$order->rate ?? ''}} <br>
                            <span class="badge bg-info" style="font-size: 10px;font-weight: 800;padding: 3px;">{{$order->order_confirm->remark}}</span>
                          </td>
                          <td>
                            {{$order->order_confirm->special_cut ?? ''}}
                          </td>
                          <td>
                            {{$order->soda_price ?? ''}}
                          </td>
                          <td>
                            <select name="plant_id[]" {{auth()->user()->can('order_dispatch_update') ? '' : 'disabled'}} class="form-control plant_id_select">
                              @foreach($plants as $plant)
                              <option value="{{$plant->id}}" {{ $order->plant_id == $plant->id ? 'selected' : '' }}>{{$plant->plant_name ?? ''}}</option>
                              @endforeach
                            </select>
                          </td>
                        </tr>
                        @endforeach
                        @endif
                      </tbody>
                    </table>
                  </div>
                  <div class="col-md-4">
                    <input type="submit" class="btn btn-primary" value="Update">
                  </div>
                </form>

                @if(isset($dispatch_orders[0]->order_dispatch_details) && $dispatch_orders[0]->order_dispatch_details->media->count() > 0)
                <div class="container mt-4">
                  <h4 class="attachment-heading">Attachment's</h4>
                  <div class="row">
                    @foreach($dispatch_orders[0]->order_dispatch_details->media as $image)
                    <div class="col-md-3">
                      <div class="image-card">
                        <h6 class="collection-name">{{ strtoupper(str_replace('_', '-', $image->collection_name)) }}</h6>
                        <a href="{{ $image->getFullUrl() }}" target="_blank">
                          <img src="{{ $image->getFullUrl() }}" class="img-fluid img-thumbnail">
                        </a>
                      </div>
                    </div>
                    @if($loop->iteration % 4 == 0) {{-- Ensures 4 images per row --}}
                  </div>
                  <div class="row">
                    @endif
                    @endforeach
                  </div>
                </div>
                @endif

                <!-- /.col -->
              </div>
              <!-- /.row -->

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
    $(document).ready(function() {
      $.validator.addMethod("vehicleFormat", function(value, element) {
        return value === "" || value === null || /^[A-Z]{2} \d{2} [A-Z]{2} \d{4}$/.test(value);
      }, "Invalid format! Example: MP 12 XX 1234");


      $('#updateOrderDispatch').validate({
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
          },
          qty: {
            required: true,
            max: function() {
              return parseFloat($('#qty').attr('max'));
            }
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

    $(".plant_id_select, .quantitys").on("input", function() {
      var tr = $(this).closest("tr");
      var plant_id = tr.find("select[name='plant_id[]']").val();
      var order_id = tr.find("input[name='order_id[]']").val();
      var brand_id = tr.find("input[name='brand_id[]']").val();
      var unit_id = tr.find("input[name='unit_id[]']").val();
      var category_id = tr.find("input[name='category_id[]']").val();
      var qty = tr.find("input[name='qty[]']").val();

      $.ajax({
        url: "{{ url('check-stock') }}",
        type: "POST",
        data: {
          "_token": "{{ csrf_token() }}",
          "plant_id": plant_id,
          "order_id": order_id,
          "brand_id": brand_id,
          "unit_id": unit_id,
          "category_id": category_id,
          "qty": qty
        },
        success: function(response) {
          if (!response) {
            Swal.fire({
              title: "Selected plant is out of stock.",
              icon: "error",
              showCancelButton: true,
              cancelButtonText: "Cancel",
              cancelButtonColor: '#d33',
              confirmButtonColor: '#3085d6'
            });
            // setTimeout(function() {
            //   location.reload();
            // }, 1000);
          }
        }
      });

    });
  </script>

  <!-- /.content -->
</x-app-layout>