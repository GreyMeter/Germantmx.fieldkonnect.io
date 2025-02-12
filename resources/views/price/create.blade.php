<x-app-layout>
  <style>
    td.brand_msg:before {
      position: absolute;
      content: "base item";
      width: 66px;
      height: 23px;
      background: #17a2b8;
      top: 12px;
      color: #fff;
      padding: 3px 5px;
      border-radius: 8px 8px 8px 1px;
      opacity: 0;
      font-weight: 600;
      transition: opacity 0.6s ease;
      font-size: 12px;
      left: 13px;
    }

    .show-before td.brand_msg:before {
      opacity: 1;
      transition: opacity 0.6s ease;
      z-index: 999;
    }
  </style>
  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>Success!</strong> {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
  <div class="row">
    <div class="col-md-12">
      {!! Form::model($price,[
      'route' => $price->exists ? ['prices.update', $price->id] : 'prices.store',
      'method' => $price->exists ? 'PUT' : 'POST',
      'id' => 'createProductForm',
      'files'=>true
      ]) !!}
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">
            Price
            <span class="pull-right">
              <!-- <div class="btn-group">
                @if(auth()->user()->can(['prices_access']))
                <a href="{{ url('prices') }}" class="btn btn-just-icon btn-theme" title="{!! trans('panel.product.title_singular') !!}{!! trans('panel.global.list') !!}">
                  <i class="material-icons">next_plan</i></a>
                @endif
              </div> -->
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

          <!-- Base Price Fields -->
          <div class="row">
            <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">Base Brand<span class="text-danger"> *</span></label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <select class="form-control select2" multiple id="base_brand" name="brand_id[]" required>
                      <option value="">Select Base Brand</option>
                      @foreach($brands as $brand)
                      <option value="{{ $brand->id }}" {{ ($price->exists && in_array($brand->id, explode(',',$price->brand_id))) ? 'selected' : '' }}>{{ $brand->brand_name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">Base Grade<span class="text-danger"> *</span></label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <select class="form-control select2" multiple id="base_grade" name="grade_id[]" required>
                      <option value="">Select Base Grade</option>
                      @foreach($grades as $grade)
                      <option value="{{ $grade->id }}" {{ ($price->exists && in_array($grade->id, explode(',',$price->grade_id))) ? 'selected' : '' }}>{{ $grade->unit_name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <!-- <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">Base Zone<span class="text-danger"> *</span></label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <select class="form-control select2" id="base_zone" name="zone_id" required>
                      <option value="">Select Base Zone</option>
                      @foreach($zones as $zone)
                      <option value="{{ $zone->id }}" {{ old('zone_id', $price->exists ? $price->zone_id : '') == $zone->id ? 'selected' : '' }}>{{ $zone->city_name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
            </div> -->
            <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">Base Size<span class="text-danger"> *</span></label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <select class="form-control select2" id="base_size" name="size_id" required>
                      <option value="">Select Base Size</option>
                      @foreach($sizes as $size)
                      <option value="{{ $size->id }}" {{ old('size_id', $price->exists ? $price->size_id : '') == $size->id ? 'selected' : '' }}>{{ $size->category_name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">Base Price<span class="text-danger"> *</span></label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <input type="number" class="form-control" value="{{old('base_price', $price->base_price)}}" name="base_price" placeholder="Enter Base Price" required>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-body">

          @php
          $add_size = $price->additionalPrices->where('model_name', 'size')->pluck('price_adjustment','model_id')->toArray();
          $add_grade = $price->additionalPrices->where('model_name', 'grade')->pluck('price_adjustment','model_id')->toArray();
          $add_brand = $price->additionalPrices->where('model_name', 'brand')->pluck('price_adjustment','model_id')->toArray();
          $add_distributor = $price->additionalPrices->where('model_name', 'distributor')->pluck('price_adjustment','model_id')->toArray();
          @endphp
          <div class="row mt-4">
            <div class="col-md-3">
              <h5>Additional Prices Size</h5>
              <table class="table table-striped" id="size_table">
                <thead>
                  <tr>
                    <th>Size</th>
                    <th>Price (+/-)</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($sizes as $category)
                  <tr id="size_{{$category->id}}">
                    <td>{{$category->category_name}} MM</td>
                    <input type="hidden" name="size[id][]" value="{{$category->id}}">
                    <td class="brand_msg"><input type="number" class="form-control" name="size[price][]" value="{{($price->exists && count($add_size)>0) ? ($add_size[$category->id]??'0.00'):'0.00'}}"></td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <div class="col-md-3">
              <h5>Additional Prices Grade</h5>
              <table class="table table-striped" id="grade_table">
                <thead>
                  <tr>
                    <th>Grade</th>
                    <th>Price (+/-)</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($grades as $grade)
                  <tr id="grade_{{$grade->id}}">
                    <td>{{$grade->unit_name}}</td>
                    <input type="hidden" name="grade[id][]" value="{{$grade->id}}">
                    <td class="brand_msg"><input type="number" class="form-control" name="grade[price][]" value="{{($price->exists && count($add_grade)>0) ? ($add_grade[$grade->id]??'0.00'):'0.00'}}"></td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <div class="col-md-3">
              <h5>Additional Prices Brand</h5>
              <table class="table table-striped" id="brand_table">
                <thead>
                  <tr>
                    <th>Brand</th>
                    <th>Price (+/-)</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($brands as $brand)
                  <tr id="brand_{{$brand->id}}" class="show-before">
                    <td>{{$brand->brand_name}}</td>
                    <input type="hidden" name="brand[id][]" value="{{$brand->id}}">
                    <td class="brand_msg"><input type="number" class="form-control" name="brand[price][]" value="{{($price->exists && count($add_brand)>0) ? ($add_brand[$brand->id]??'0.00'):'0.00'}}"></td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <div class="col-md-3">
              <h5>Additional Prices Distributor</h5>
              <table class="table table-striped" id="brand_table">
                <thead>
                  <tr>
                    <th>Distributor</th>
                    <th>Price (+/-)</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($distributors as $distributor)
                  <tr id="distributor_{{$distributor->id}}" class="show-before">
                    <td>{{$distributor->name}}</td>
                    <input type="hidden" name="distributor[id][]" value="{{$distributor->id}}">
                    <td class="distributor_msg"><input type="number" class="form-control" name="distributor[price][]" value="{{($price->exists && count($add_distributor)>0) ? ($add_distributor[$distributor->id]??'0.00'):'0.00'}}"></td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

          </div>

          <div class="row mt-4">
            <div class="col-md-12">
              <button type="submit" class="btn btn-primary">{{$price->exists ? 'Update Price':'Save Price'}}</button>
            </div>
          </div>
        </div>
      </div>
      {!! Form::close() !!}
    </div>
  </div>
  </div>

  <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
  <script>
    function toggleAdditionalInputs(baseSelector, additionalPrefix) {
      var baseIds = $(baseSelector).val();

      $('#' + additionalPrefix + '_table input[type="number"]').prop('readonly', false);
      $('#' + additionalPrefix + '_table tr').removeClass('show-before');

      if ($.isArray(baseIds)) {
        $.each(baseIds, function(index, value) {
          var row = $('#' + additionalPrefix + '_' + value);
          row.find('input[type="number"]').prop('readonly', true); // Disable number input
          row.find('input[type="number"]').val('0.00'); // Set number input value to 0.00
          row.addClass('show-before'); // Add class to show the before element
        });
      } else if (baseIds) {
        var row = $('#' + additionalPrefix + '_' + baseIds);
        row.find('input[type="number"]').prop('readonly', true); // Disable number input
        row.find('input[type="number"]').val('0.00'); // Set number input value to 0.00
        row.addClass('show-before'); // Add class to show the before element
      }
    }

    // Apply filtering when base fields change
    $('#base_brand').change(function() {
      toggleAdditionalInputs('#base_brand', 'brand');
    }).trigger('change');
    $('#base_grade').change(function() {
      toggleAdditionalInputs('#base_grade', 'grade');
    }).trigger('change');;
    // $('#base_zone').change(function() {
    //   toggleAdditionalInputs('#base_zone', '.additional-zone');
    // }).trigger('change');;
    $('#base_size').change(function() {
      toggleAdditionalInputs('#base_size', 'size');
    }).trigger('change');;
  </script>
</x-app-layout>