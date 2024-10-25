<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">{{ $orders->exists?($cnf?'Confirm':trans('panel.global.edit')):trans('panel.global.create') }} Soda
            <span class="pull-right">
              <div class="btn-group">
                @if(auth()->user()->can(['order_access']))
                <a href="{{ $orders->exists?url('orders/' . encrypt($orders->id)):url('orders') }}" class="btn btn-just-icon btn-theme" title="{!! trans('panel.order.title_singular') !!}{!! trans('panel.global.list') !!}"><i class="material-icons">next_plan</i></a>
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
          'route' => $orders->exists ? ($cnf?['orders.confirm', encrypt($orders->id) ]:['orders.update', encrypt($orders->id) ]) : 'orders.store',
          'method' => $orders->exists ? 'PUT' : 'POST',
          'id' => 'createProductForm',
          'files'=>true
          ]) !!}

          <div class="row">
            @if($orders->exists)
            <div class="col-md-12">
              <div class="row">
                <label class="col-md-2 col-form-label">Consignee Details<span class="text-danger"> *</span></label>
                <div class="col-md-10">
                  <div class="form-group has-default bmd-form-group">
                    <textarea name="consignee_details" class="form-control" cols="30" rows="6" id="consignee_details" required></textarea>
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
            @endif

            <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">Customer<span class="text-danger"> *</span></label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <select class="form-control select2" name="customer_id" id="customer_id" style="width: 100%;" required>
                      <option value="">Select Customer</option>
                      @if(@isset($customers ))
                      @foreach($customers as $customer)
                      <option data-limit="{{$customer->order_limit}}" {{isset($cnf)?'disabled':''}} value="{!! $customer['id'] !!}" {{ old( 'customer_id' , (!empty($orders->customer_id)) ? ($orders->customer_id) :('') ) == $customer['id'] ? 'selected' : '' }}>{!! $customer['name'] !!}</option>
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
                    <input type="number" name="qty" id="qty" class="form-control" value="{!! old( 'qty', $orders['qty']-$totalOrderConfirmQty) !!}" min="1" max="{{isset($cnf)?$orders['qty']-$totalOrderConfirmQty:''}}" step="1" required>
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
            @if($orders->exists)
            <table class="table kvcodes-dynamic-rows-example" id="tab_logic">
              <thead>
                <tr>
                  <th class="text-center"> # </th>
                  <th class="text-center brand"> Brand </th>
                  <th class="text-center grade"> Grade</th>
                  <th class="text-center size"> Size </th>
                  <th class="text-center"> QTY <small>(MT)</small> </th>
                  <th class="text-center"> </th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
            <div class="row clearfix">
              <div class="col-md-12">
                <table class="table">
                  <tbody>
                    <tr>
                      <td class="td-actions">
                        <a href="#" title="" class="btn btn-success btn-just-icon btn-sm add-rows" onclick="getAlllist()"> <i class="fa fa-plus"></i> </a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <!-- <div class="col-md-6">
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
            </div> -->
            @endif
            <!-- <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">Soda Price (₹) <span class="text-danger"> *</span></label>
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
            </div> -->
          </div>
        </div>
        <div class="card-footer">
          @if(isset($cnf))
          {{ Form::submit('Confirm Order', array('class' => 'btn btn-theme pull-right', 'id'=>'smt-btn')) }}
          @else
          {{ Form::submit('Submit', array('class' => 'btn btn-theme pull-right', 'id'=>'smt-btn')) }}
          @endif
        </div>
        {{ Form::close() }}
      </div>
    </div>
  </div>
  </div>
  <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
  <script src="{{ url('/').'/'.asset('assets/js/validation_orders.js?') }}"></script>
  <script type="text/javascript">
    var counter = 0;
    $(document).ready(function() {
      calcualteSodaPrice();
      var $table = $('table.kvcodes-dynamic-rows-example');
      $('a.add-rows').click(function(event) {
        event.preventDefault();
        counter++;

        var newRow =
          '<tr> <td>' + counter + '</td>' +
          '<td class="group" style="width:30%"><div class="input_section"><select required name="brand_id[]" class="form-control brand' + counter + '"></select></div></td>' +
          '<td style="width:30%" class="subCat"><div class="input_section"><select required name="grade_id[]" class="form-control grade' + counter + '"></select></div></td>' +
          '<td style="width:30%"><div class="input_section"><select required name="category_id[]" class="form-control size' + counter + '"></select></div></td>' +
          '<td><div class="input_section"><input required type="number" name="qty[]"class="form-control points rowchange" /></div></td>' +
          '<td class="td-actions text-center"><a class="remove-rows btn btn-danger btn-just-icon btn-sm"><i class="fa fa-minus"></i></a></td> </tr>';
        $table.append(newRow);
        $('.select2bs4').select2({
          theme: 'bootstrap4'
        })
      });
    })
    $('#customer_id').on('select2:select', function(e) {
      var customerId = $(this).val();
      if (customerId != '') {
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
      } else {
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
    })

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
    })

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
    })

    function calcualteSodaPrice() {
      var additionalSizePrice = parseFloat($('#add_size_price').val()) || 0.00;
      var additionalBrandPrice = parseFloat($('#add_brand_price').val()) || 0.00;
      var additionalGradePrice = parseFloat($('#add_grade_price').val()) || 0.00;

      var newQty = $('#qty').val();
      var conversionFactor = 0.90718474;
      mtLimit = newQty * conversionFactor;
      var sodaPrice = parseFloat((mtLimit * {{$base_price}}).toFixed(2)) || 0.00;
      $("#soda_price").val(sodaPrice + additionalBrandPrice + additionalGradePrice + additionalSizePrice);
    }

    function getAlllist() {
      $.ajax({
        url: "{{url('/getBrand')}}",
        success: function(data) {
          var html = '<option value="">Select Brand</option>';
          $.each(data.brands, function(k, v) {
            html += '<option value="' + v.id + '">' + v.brand_name + '</option>';
          });
          $('.brand' + counter).html(html);
        }
      });
      $.ajax({
        url: "{{url('/getGrade')}}",
        success: function(data) {
          var html = '<option value="">Select Grade</option>';
          $.each(data.grade, function(k, v) {
            html += '<option value="' + v.id + '">' + v.unit_name + '</option>';
          });
          $('.grade' + counter).html(html);
        }
      });
      $.ajax({
        url: "{{url('/getSize')}}",
        success: function(data) {
          var html = '<option value="">Select Size</option>';
          $.each(data.size, function(k, v) {
            html += '<option value="' + v.id + '">' + v.category_name + '</option>';
          });
          $('.size' + counter).html(html);
        }
      });
    }
  </script>
</x-app-layout>