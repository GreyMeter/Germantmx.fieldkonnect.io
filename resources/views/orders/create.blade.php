<x-app-layout>
  <style>
    ul#ui-id-1 {
      z-index: 9999999 !important;
    }
  </style>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">{{ $orders->exists?($cnf?'Confirm':trans('panel.global.edit')):trans('panel.global.create') }} Booking
            <span class="pull-right">
              <div class="btn-group">
                @if(auth()->user()->can(['order_access']))
                <a href="{{ $orders->exists?url('orders/' . encrypt($orders->id)):url('orders') }}" class="btn btn-just-icon btn-theme" title="Booking {!! trans('panel.global.list') !!}"><i class="material-icons">next_plan</i></a>
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
          'id' => $orders->exists ?($cnf?'confirmForm':'updateForm') : 'orderForm',
          'files'=>true
          ]) !!}

          <div class="row">
            @if($orders->exists && $cnf)
            <div class="col-md-12">
              <div class="row">
                <label class="col-md-2 col-form-label">Consignee Details<span class="text-danger"> *</span></label>
                <div class="col-md-10">
                  <div class="form-group has-default bmd-form-group">
                    <textarea name="consignee_details" class="form-control" cols="30" rows="6" id="consignee_details" required></textarea>
                    @if ($errors->has('consignee_details'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('consignee_details') }}</p>
                    </div>
                    @endif
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
                      <option data-limit="{{$customer->order_limit}}" {{isset($cnf) && $cnf ?'disabled':''}} value="{!! $customer['id'] !!}" {{ old( 'customer_id' , (!empty($orders->customer_id)) ? ($orders->customer_id) :('') ) == $customer['id'] ? 'selected' : '' }}>{!! $customer['name'] !!}</option>
                      @endforeach
                      @endif
                    </select>
                    <input type="hidden" name="order_customer_id" id="order_customer_id" value="{!! old( 'customer_id' , (!empty($orders->customer_id)) ? ($orders->customer_id) :('') ) !!}">
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
                    <input type="number" name="qty" {{isset($cnf) && $cnf?'disabled':''}} id="qty" class="form-control" value="{!! $orders['qty']-$totalOrderConfirmQty !!}" min="0.01" max="{{isset($cnf)?$orders['qty']-$totalOrderConfirmQty:''}}" step="0.01" required>
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
                    <input readonly type="text" name="base_price" id="base_price" class="form-control" value="{!! $orders['base_price']?$orders['base_price']+$orders['discount_amt']:$base_price !!}" maxlength="200" required>
                    <input type="hidden" name="first_base_price" id="first_base_price" value="{{$base_price}}">
                    @if ($errors->has('base_price'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('base_price') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
            @if($orders->exists && $cnf)
            <span class="badge badge-danger" id="all-qty-errors"></span>
            <table class="table kvcodes-dynamic-rows-example" id="tab_logic">
              <thead>
                <tr>
                  <th class="text-center"> # </th>
                  <th class="text-center brand"> Brand </th>
                  <th class="text-center grade"> Grade</th>
                  <th class="text-center random_cut_th"> Random Cut</th>
                  <th class="text-center size"> Size </th>
                  <th class="text-center material"> Material </th>
                  <th class="text-center"> Loading-Add </th>
                  <th class="text-center"> QTY <small>(MT)</small> </th>
                  <th class="text-center"> Additional Rate </th>
                  <th class="text-center"> Special Cut </th>
                  <th class="text-center"> Remarks </th>
                  <th class="text-center"> Booking Price </th>
                  <th class="text-center"> Total Price </th>
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
          @if(isset($cnf) && $cnf)
          {{ Form::submit('Confirm Order', array('class' => 'btn btn-theme pull-right', 'id'=>'smt-btn')) }}
          @elseif($orders->exists)
          {{ Form::submit('Update', array('class' => 'btn btn-theme pull-right', 'id'=>'smt-btn')) }}
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
      var $table = $('table.kvcodes-dynamic-rows-example');
      $('a.add-rows').click(function(event) {
        event.preventDefault();
        counter++;

        var newRow =
          '<tr> <td>' + counter + '</td>' +
          '<td class="group" style="width:30%"><div class="input_section"><select required name="brand_id[]" class="form-control brand' + counter + ' brand_change"></select></div></td>' +
          '<td style="width:30%" class="subCat"><div class="input_section"><select name="grade_id[]" class="form-control grade' + counter + ' grade_change"></select></div></td>' +
          '<td style="width:30%" class="subCat"><div class="input_section"><select name="random_cut[]" class="form-control random_cut' + counter + ' random_cut_change"><option value="">Slecte Random Cut</option><option value="10-25">10-25</option><option value="25-35">23-35</option></select></div></td>' +
          '<td style="width:30%"><div class="input_section"><select required name="category_id[]" class="form-control allsizes size' + counter + ' size_change"></select></div></td>' +
          '<td style="width:30%"><div class="input_section"><select required name="material[]" class="form-control material' + counter + ' material_change"><option value="">Select Material</option><option value="Straight">Straight</option><option value="Bend">Bend</option></select></div></td>' +
          '<td style="width:30%"><div class="input_section"><select required name="loading_add[]" class="form-control loading_add' + counter + ' loading_add_change"><option value="">Select Loading</option><option value="Up">Up</option><option value="Down">Down</option></select></div></td>' +
          '<td><div class="input_section"><input required type="number" step="0.01" name="qty[]"class="form-control points rowchange" /></div></td>' +
          '<td><div class="input_section"><input type="number" step="0.01" name="additional_rate[]"class="form-control additional_rate rowchange" /></div></td>' +
          '<td><div class="input_section"><input type="text" name="special_cut[]"class="form-control special_cut rowchange" /></div></td>' +
          '<td><div class="input_section"><textarea type="text" name="remark[]"class="form-control remark rowchange"></textarea></div></td>' +
          '<td><div class="input_section"><input required type="number" name="booking_price[]"class="form-control  booking_price_change"  readonly/></div></td>' +
          '<td ><div class="input_section"><input style="width : 120px !important" required type="number" name="total_price[]"class="form-control  total_price_change" readonly/></div></td>' +
          '<td class="td-actions text-center"><a class="remove-rows btn btn-danger btn-just-icon btn-sm"><i class="fa fa-minus"></i></a></td> </tr>';
        $table.append(newRow);
        addJquery();
        $('.select2bs4').select2({
          theme: 'bootstrap4'
        })
      });
    })
    $(document).on('click', '.remove-rows', function() {
      $(this).closest('tr').remove(); // Remove the row
      counter--;
    });

    function addJquery() {
      $(document).on('change', '.brand_change, .grade_change, .size_change', function() {
        var row = $(this).closest('tr'); // Get the closest row of the changed input/select

        row.find('.total_price_change').val(''); // Update the booking price in the row
        row.find('.booking_price_change').val('');
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
          row.find('.total_price_change').text(''); // Update the booking price in the row
          row.find('.booking_price_change').text('');
        }
      });


      $('.points, .additional_rate, .special_cut').on('input', function() {
        var row = $(this).closest('tr'); // Get the closest row of the changed input/select

        row.find('.total_price_change').val(''); // Update the booking price in the row
        row.find('.booking_price_change').val('');
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
    }

    function getPrices(brand = '', grade = '', size = '', material = '', quantity = 1, row, additionalRate = 0) {
      var bookingPrice = $('#base_price').val();
      var totalPrice = '';
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
              console.log(res);
              bookingPrice = parseFloat(bookingPrice, 10) + parseFloat(res.additional_price, 10);
              row.find('.booking_price_change').val(bookingPrice);

              var qty = parseFloat(quantity || 1, 10); // default to 1 if quantity is not set
              var baseTotal = qty * bookingPrice;
              var additionalTotal = additionalRate ? parseFloat(additionalRate, 10) * qty : 0;
              // var cutTotal = specialCut ? parseFloat(specialCut, 10) * qty : 0;

              var total_value = baseTotal + additionalTotal;

              row.find('.total_price_change').val(total_value);
            }
          }
        });
      }
    }


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
              if (res.final_price != null) {
                $("#base_price").val(res.final_price);

              }
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

    $(document).on('keyup', '.points', function() {
      let totalQty = 0;
      let remainQty = {{$orders ? $orders['qty'] - $totalOrderConfirmQty : 0}};
      $('.points').each(function() {
        let qtyValue = parseFloat($(this).val()) || 0;
        totalQty += qtyValue;
      });
      if (totalQty > remainQty) {
        $("#smt-btn").prop("disabled", true);
        $("#all-qty-errors").html("* Quantity can not be greater then total soda quantity.");
      } else {
        $("#smt-btn").prop("disabled", false);
        $("#all-qty-errors").html("");
      }
    });

    $('#confirmForm').on('submit', function(e) {
      let isValid = true;
      let errorMessage = '';

      $('table.kvcodes-dynamic-rows-example tbody tr').each(function(index) {
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
</x-app-layout>