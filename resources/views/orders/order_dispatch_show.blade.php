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
                    @if(auth()->user()->can(['order_access']))
                    <a href="{{ url('orders_dispatch') }}" class="btn btn-just-icon btn-theme" title="{!! trans('panel.order.title_singular') !!}{!! trans('panel.global.list') !!}"><i class="material-icons">next_plan</i></a>
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
                  <h3 style="margin-bottom: 10px;font-weight: 500;">Soda Deatils:</h3>
                  <address style="border: 1px dashed #377ab8;padding: 15px 0px;border-radius: 8px;text-align: center;box-shadow:  -3px 3px 11px 0px #377ab8;">
                    PO Number # <span style="font-weight: 800; font-size:16px;"> {!! $orders['po_no'] !!}</span> <br>
                    Order Number # <span style="font-weight: 800; font-size:16px;"> {!! $orders['confirm_po_no'] !!}</span> <br>
                    Order Dispatch Number # <span style="font-weight: 800; font-size:16px;"> {!! $orders['dispatch_po_no'] !!}</span> <br>
                    Date: {!! date("d-M-Y H:i A", strtotime($orders['created_at'])) !!} <br><br>
                    Created By: {!! $orders['createdbyname']?$orders['createdbyname']['name']:'Self' !!}
                  </address>
                </div>
              </div>
              <!-- /.row -->

              <!-- Table row -->

              <div class="row">
                <div class="col-12">
                  <!-- New Row for Driver Details -->
                  <div class="card p-3 mb-3 bg-light">
                    <h5 class="mb-3"><strong>Driver Details</strong></h5>
                    <div class="row">
                      <div class="col-md-4">
                        <label>Driver Name</label>
                        <p class="form-control-plaintext" style="color: black"><strong>{{ isset($dispatch_orders[0]->order_dispatch_details->driver_name) ? $dispatch_orders[0]->order_dispatch_details->driver_name : '' }}</strong></p>
                      </div>
                      <div class="col-md-4">
                        <label>Driver Contact</label>
                        <p class="form-control-plaintext" style="color: black"><strong>{{ isset($dispatch_orders[0]->order_dispatch_details->driver_contact_number) ? $dispatch_orders[0]->order_dispatch_details->driver_contact_number : '' }}</strong></p>
                      </div>
                      <div class="col-md-4">
                        <label>Vehicle Number</label>
                        <p class="form-control-plaintext" style="color: black"><strong>{{ isset($dispatch_orders[0]->order_dispatch_details->vehicle_number) ? $dispatch_orders[0]->order_dispatch_details->vehicle_number : '' }}</strong></p>
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
                        <th>Size</th>
                        <th>Material</th>
                        <th>Total Quantity<small>(Tonn)</small></th>
                        <th>Base Price<small>(1MT)</small></th>
                        <th>Total</th>
                        <th>Plants</th>
                      </tr>
                    </thead>
                    <tbody>
                      @if($orders->exists)
                      @foreach ($dispatch_orders as $order)
                      <tr>
                        <td>{{$order->brands ? $order->brands->brand_name : '-'}}</td>
                        <td>{{$order->grades ? $order->grades->unit_name : '-'}}</td>
                        <td>{{$order->sizes ? $order->sizes->category_name : '-'}}</td>
                        <td>{{$order->order_confirm->material ?? ''}}</td>
                        <td>{{$order->qty}}</td>
                        <td>
                          {{$order->base_price ?? ''}}
                        </td>
                        <td>
                          {{$order->soda_price ?? ''}}
                        </td>
                        <td>
                          {{$order->plant->plant_name ?? ''}}
                        </td>
                      </tr>
                      @endforeach
                      @endif
                    </tbody>
                  </table>
                </div>

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

  <!-- /.content -->
</x-app-layout>