<x-app-layout>
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
          <div class="card-body">
            <div class="row">
              <div class="col-12">
                <h3 class="card-title pb-3"> Detail</h3>
              </div>
              <!-- /.col -->
              <div class="col-12">
                @if($orders['status'] == '0')
                @if($orders->qty > $totalOrderConfirmQty)
                <a href="{{ url('orders/' . encrypt($orders->id) . '/edit?cnf=true') }}" class="btn btn-success">Confirm Order</a>
                <a class="btn btn-danger bg-danger">Cancle Order</a>
                @else
                <button type="button" class="btn btn-success">This order has fully confirm</button>
                @endif
                @endif
                <span class="pull-right">
                  <div class="btn-group">
                    @if(auth()->user()->can(['order_access']))
                    <a href="{{ url('orders') }}" class="btn btn-just-icon btn-theme" title="{!! trans('panel.order.title_singular') !!}{!! trans('panel.global.list') !!}"><i class="material-icons">next_plan</i></a>
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
                <div class="col-sm-5 invoice-col">
                  <h3 style="margin-bottom: 10px;font-weight: 500;">Customer Deatils:</h3>
                  <address style="border: 1px dashed #377ab8;padding: 15px 0px;border-radius: 8px;text-align: center;box-shadow:  -3px 3px 11px 0px #377ab8;">
                    <strong>Name:{!! isset($orders['customer']['name']) ? $orders['customer']['name'] :'' !!} </strong><br>
                    Address:{!! $orders['customer']['customeraddress']['address1'] !!} ,{!! $orders['customer']['customeraddress']['address2'] !!}<br>
                    {!! $orders['customer']['customeraddress']['locality'] !!}, {!! $orders['customer']['customeraddress']['cityname']['city_name'] !!} {!! $orders['customer']['customeraddress']['pincodename']['pincode'] !!}<br>
                    Phone: {!! $orders['customer']['mobile'] !!}<br>
                    Email: {!! $orders['customer']['email'] !!}
                  </address>
                </div>
                <div class="col-sm-2 invoice-col"></div>
                <!-- /.col -->
                <div class="col-sm-5 invoice-col">
                <h3 style="margin-bottom: 10px;font-weight: 500;">Soda Deatils:</h3>
                  <address style="border: 1px dashed #377ab8;padding: 15px 0px;border-radius: 8px;text-align: center;box-shadow:  -3px 3px 11px 0px #377ab8;">
                  PO Number # <span style="font-weight: 800; font-size:16px;"> {!! $orders['po_no'] !!}</span> <br><br>
                  Date: {!! date("d-M-Y H:i A", strtotime($orders['created_at'])) !!} <br><br>
                  Created By: {!! $orders['createdbyname']?$orders['createdbyname']['name']:'Self' !!}
                  </address>
                </div>
              </div>
              <!-- /.row -->

              <!-- Table row -->
              <div class="row">
                <div class="col-12 table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <!-- <th>Brand</th>
                        <th>Grade</th>
                        <th>Size</th> -->
                        <th>Quantity<small>(Tonn)</small></th>
                        <th>Base Price<small>(1MT)</small></th>
                        <!-- <th>Soda Price</th> -->
                      </tr>
                    </thead>
                    <tbody>
                      @if($orders->exists )
                      <tr>
                        <!-- <td>{{$orders->brands?$orders->brands->brand_name:'-'}}</td>
                        <td>{{$orders->grades?$orders->grades->unit_name:'-'}}</td>
                        <td>{{$orders->sizes?$orders->sizes->category_name:'-'}}</td> -->
                        <td>{{$orders->qty}}</td>
                        <td>{{$orders->base_price}}</td>
                        <!-- <td>{{$orders->soda_price}}</td> -->
                      </tr>
                      @endif
                    </tbody>
                  </table>
                </div>
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
  </script>
  <!-- /.content -->
</x-app-layout>