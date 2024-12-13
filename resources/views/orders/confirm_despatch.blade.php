<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">Dispatch Order
            <span class="pull-right">
              <div class="btn-group">
                @if(auth()->user()->can(['order_access']))
                <a href="{{ $orders->exists?url('orders_confirm/' . encrypt($orders->id)):url('orders') }}" class="btn btn-just-icon btn-theme" title="{!! trans('panel.order.title_singular') !!}{!! trans('panel.global.list') !!}"><i class="material-icons">next_plan</i></a>
                @endif
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
          {!! Form::model($orders,[
          'route' => ['orders.dispatch', encrypt($orders->id) ],
          'method' => 'POST',
          'id' => 'createProductForm',
          'files'=>true
          ]) !!}

          <div class="row">

            <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">Customer<span class="text-danger"> *</span></label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <select class="form-control select2" name="customer_id" id="customer_id" style="width: 100%;" required>
                      <option value="">Select Customer</option>
                      @if(@isset($customers ))
                      @foreach($customers as $customer)
                      <option data-limit="{{$customer->order_limit}}" {{isset($cnf)?'disabled':''}} value="{!! $customer['id'] !!}" {{ old( 'customer_id' , (!empty($orders->order->customer_id)) ? ($orders->order->customer_id) :('') ) == $customer['id'] ? 'selected' : '' }}>{!! $customer['name'] !!}</option>
                      @endforeach
                      @endif
                    </select>
                  </div>
                  @if ($errors->has('customer_id'))
                  <div class="error col-lg-12">
                    <p class="text-danger">{{ $errors->first('customer_id') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">PO No. <span class="text-danger"> *</span></label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <input type="hidden" name="id" id="id" value="{!! old( 'id', $orders['id']) !!}">
                    <input readonly type="text" name="po_no" id="po_no" class="form-control" value="{!! $orders['po_no']??$po_no !!}" maxlength="200">
                    @if ($errors->has('po_no'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('po_no') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">Quantity<small>(Tonn)</small> <span class="text-danger"> *</span></label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <input type="number" name="qty" id="qty" class="form-control" value="{!! old( 'qty', $orders['qty']-$totalOrderDispatchQty) !!}" min="1" max="{{$orders['qty']-$totalOrderDispatchQty}}" step="1" required>
                    @if ($errors->has('qty'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('qty') }}</p>
                    </div>
                    @endif
                    <span id="qty-errors"></span>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">Grade<span class="text-danger"> *</span></label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <input type="hidden" id="add_grade_price">
                    <select class="form-control select2" name="unit_id" id="grade_id" style="width: 100%;" {{isset($cnf)?'required':''}}>
                      <option value="">Select Grade</option>
                      @if(@isset($units ))
                      @foreach($units as $unit)
                      <option value="{!! $unit['id'] !!}" {{ old( 'unit_id' , (!empty($orders->unit_id)) ? ($orders->unit_id) :('') ) == $unit['id'] ? 'selected' : '' }}>{!! $unit['unit_name'] !!}</option>
                      @endforeach
                      @endif
                    </select>
                  </div>
                  @if ($errors->has('unit_id'))
                  <div class="error col-lg-12">
                    <p class="text-danger">{{ $errors->first('unit_id') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">{!! trans('panel.product.fields.brand_name') !!}<span class="text-danger"> *</span></label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                  <input type="hidden" id="add_brand_price">
                    <select class="form-control select2" name="brand_id" id="brand_id" style="width: 100%;" {{isset($cnf)?'required':''}}>
                      <option value="">Select {!! trans('panel.product.fields.brand_name') !!}</option>
                      @if(@isset($brands ))
                      @foreach($brands as $brand)
                      <option value="{!! $brand['id'] !!}" {{ old( 'brand_id' , (!empty($orders->brand_id)) ? ($orders->brand_id) :('') ) == $brand['id'] ? 'selected' : '' }}>{!! $brand['brand_name'] !!}</option>
                      @endforeach
                      @endif
                    </select>
                  </div>
                  @if ($errors->has('brand_id'))
                  <div class="error col-lg-12">
                    <p class="text-danger">{{ $errors->first('brand_id') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">Size<small>(mm)</small><span class="text-danger"> *</span></label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                  <input type="hidden" id="add_size_price">
                    <select class="form-control select2" name="category_id" id="size_id" style="width: 100%;" {{isset($cnf)?'required':''}}>
                      <option value="">Select Size</option>
                      @if(@isset($categories ))
                      @foreach($categories as $category)
                      <option value="{!! $category['id'] !!}" {{ old( 'category_id' , (!empty($orders->category_id)) ? ($orders->category_id) :('') ) == $category['id'] ? 'selected' : '' }}>{!! $category['category_name'] !!}</option>
                      @endforeach
                      @endif
                    </select>
                  </div>
                  @if ($errors->has('category_id'))
                  <div class="error col-lg-12">
                    <p class="text-danger">{{ $errors->first('category_id') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">Base Price<small>(1MT)</small> <span class="text-danger"> *</span></label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <input readonly type="text" name="base_price" id="base_price" class="form-control" value="{!! $orders['base_price']??$base_price !!}" maxlength="200" required>
                    @if ($errors->has('base_price'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('base_price') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">Total Price<small>(₹)</small> <span class="text-danger"> *</span></label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <input readonly type="text" name="soda_price" id="soda_price" class="form-control" value="{!! old( 'soda_price', $orders['soda_price']) !!}" required>
                    @if ($errors->has('soda_price'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('soda_price') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
            @if(isset($cnf))
            <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">Freight Price </label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="rate" id="rate" class="form-control" value="{!! old( 'rate', $orders['rate'])??0 !!}">
                    @if ($errors->has('rate'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('rate') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">Final Rate <span class="text-danger"> *</span></label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <input readonly type="text" name="final_rate" id="final_rate" class="form-control" value="{!! old( 'final_rate', $orders['soda_price']) !!}">
                    @if ($errors->has('final_rate'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('final_rate') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
            @endif
          </div>
        </div>
        <div class="card-footer">
          {{ Form::submit('Dispatch', array('class' => 'btn btn-theme pull-right', 'id'=>'smt-btn')) }}
        </div>
        {{ Form::close() }}
      </div>
    </div>
  </div>
  </div>
  <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
  <script src="{{ url('/').'/'.asset('assets/js/validation_orders.js?') }}"></script>
  <script type="text/javascript">
    $('#customer_id').on('select2:select', function(e) {
      var customerId = $(this).val();
      if(customerId != ''){
        var selectedOption = $(this).find(':selected');
        var orderLimit = selectedOption.data('limit');
        if (orderLimit < 1) {
          $("#smt-btn").prop("disabled", true);
          $("#qty-errors").html("order limit is zero, Don't hesitate to get in touch with the administrator.");
          $("#qty-errors").removeClass("text-info");
          $("#qty-errors").addClass("text-danger");
          $("#qty").val('0').trigger('keyup');
        } else {
          $.ajax({
            url: "{{ url('getOrderLimit') }}",
            data: {
              "customer_id": customerId,
            },
            success: function(res) {
              orderLimit = orderLimit - res.today_order_qty;
              if (orderLimit < 1) {
                $("#smt-btn").prop("disabled", true);
                $("#qty-errors").html("Today's order limit is zero, Don't hesitate to get in touch with the administrator.");
                $("#qty-errors").removeClass("text-info");
                $("#qty-errors").addClass("text-danger");
                $("#qty").val('0').trigger('keyup');
              } else {
                var conversionFactor = 0.90718474;
                mtLimit = orderLimit * conversionFactor;
                $("#smt-btn").prop("disabled", false);
                $("#qty-errors").html("Customer today's order limit remaining is " + orderLimit);
                $("#qty-errors").addClass("text-info");
                $("#qty-errors").removeClass("text-danger");
                $("#qty").prop('max', orderLimit);
                $("#qty").val(orderLimit).trigger('keyup');

                $("#soda_price").val((mtLimit * {{$base_price}}).toFixed(2));
              }
            }
          });
        }
      }else{
        $("#qty-errors").html("");
      }
    });

    $('#qty').on('keyup', function() {
      calcualteSodaPrice();
    })

    $('#grade_id').on('change', function() {
      var id = $(this).val();
      $.ajax({
        url: "{{ url('getAdditionalPrice') }}",
        data: {
          "model_id": id,
          "model_name": "grade",
        },
        success: function(res) {
          $('#add_grade_price').val(res.additional_price);
          calcualteSodaPrice();
        }
      });
    }).trigger('change');

    $('#brand_id').on('change', function() {
      var id = $(this).val();
      $.ajax({
        url: "{{ url('getAdditionalPrice') }}",
        data: {
          "model_id": id,
          "model_name": "brand",
        },
        success: function(res) {
          $('#add_brand_price').val(res.additional_price);
          calcualteSodaPrice();
        }
      });
    }).trigger('change');

    $('#size_id').on('change', function() {
      var id = $(this).val();
      $.ajax({
        url: "{{ url('getAdditionalPrice') }}",
        data: {
          "model_id": id,
          "model_name": "size",
        },
        success: function(res) {
          $('#add_size_price').val(res.additional_price);
          calcualteSodaPrice();
        }
      });
    }).trigger('change');

    function calcualteSodaPrice(){
      var additionalSizePrice = parseFloat($('#add_size_price').val()) || 0.00;
      var additionalBrandPrice = parseFloat($('#add_brand_price').val()) || 0.00;
      var additionalGradePrice = parseFloat($('#add_grade_price').val()) || 0.00;

      
      var newQty = $('#qty').val();
      // var conversionFactor = 0.90718474;
      mtLimit = newQty;
      var fRate = $("#rate").val();
      var bp = {{$base_price}} - fRate;
      var sodaPrice = parseFloat((mtLimit * (bp+additionalBrandPrice+additionalGradePrice+additionalSizePrice)).toFixed(2)) || 0.00;
      $("#soda_price").val(sodaPrice);
      $("#final_rate").val(sodaPrice);
    }

    $("#rate").on('keyup', function(){
      var fRate = $(this).val();
      var additionalSizePrice = parseFloat($('#add_size_price').val()) || 0.00;
      var additionalBrandPrice = parseFloat($('#add_brand_price').val()) || 0.00;
      var additionalGradePrice = parseFloat($('#add_grade_price').val()) || 0.00;

      
      var newQty = $('#qty').val();
      // var conversionFactor = 0.90718474;
      mtLimit = newQty;
      var bp = {{$base_price}} - fRate;
      var sodaPrice = parseFloat((mtLimit * (bp+additionalBrandPrice+additionalGradePrice+additionalSizePrice)).toFixed(2)) || 0.00;
      $("#soda_price").val(sodaPrice);
      $("#final_rate").val(sodaPrice);
    })
  </script>
</x-app-layout>