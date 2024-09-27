<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">{{ trans('panel.global.create') }} {!! trans('panel.product.title_singular') !!}
            <span class="pull-right">
              <div class="btn-group">
                @if(auth()->user()->can(['product_access']))
                <a href="{{ url('products') }}" class="btn btn-just-icon btn-theme" title="{!! trans('panel.product.title_singular') !!}{!! trans('panel.global.list') !!}"><i class="material-icons">next_plan</i></a>
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
          {!! Form::model($products,[
          'route' => $products->exists ? ['products.update', encrypt($products->id) ] : 'products.store',
          'method' => $products->exists ? 'PUT' : 'POST',
          'id' => 'createProductForm',
          'files'=>true
          ]) !!}

          <div class="row">

            <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">Product Code <span class="text-danger"> *</span></label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <input type="hidden" name="id" id="id" value="{!! old( 'id', $products['id']) !!}">
                    <input type="text" name="product_code" id="product_code" class="form-control" value="{!! old( 'product_code', $products['product_code']) !!}" maxlength="200" required>
                    @if ($errors->has('product_code'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('product_code') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">Grade<span class="text-danger"> *</span></label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <select class="form-control select2" name="unit_id" style="width: 100%;" required>
                      <option value="">Select Grade</option>
                      @if(@isset($units ))
                      @foreach($units as $unit)
                      <option value="{!! $unit['id'] !!}" {{ old( 'unit_id' , (!empty($products->unit_id)) ? ($products->unit_id) :('') ) == $unit['id'] ? 'selected' : '' }}>{!! $unit['unit_name'] !!}</option>
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
                    <select class="form-control select2" name="brand_id" style="width: 100%;" required>
                      <option value="">Select {!! trans('panel.product.fields.brand_name') !!}</option>
                      @if(@isset($brands ))
                      @foreach($brands as $brand)
                      <option value="{!! $brand['id'] !!}" {{ old( 'brand_id' , (!empty($products->brand_id)) ? ($products->brand_id) :('') ) == $brand['id'] ? 'selected' : '' }}>{!! $brand['brand_name'] !!}</option>
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
                <label class="col-md-3 col-form-label">Size (mm)<span class="text-danger"> *</span></label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <select class="form-control select2" name="category_id" style="width: 100%;" required>
                      <option value="">Select Size</option>
                      @if(@isset($categories ))
                      @foreach($categories as $category)
                      <option value="{!! $category['id'] !!}" {{ old( 'category_id' , (!empty($products->category_id)) ? ($products->category_id) :('') ) == $category['id'] ? 'selected' : '' }}>{!! $category['category_name'] !!}</option>
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
                <label class="col-md-3 col-form-label">Standard Weight Kg/Mtr <span class="text-danger"> *</span></label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <input type="number" min="0" step="0.001" name="standard_weight" id="standard_weight" class="form-control" value="{!! old( 'standard_weight', $products['description']) !!}" maxlength="200" required>
                    @if ($errors->has('standard_weight'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('standard_weight') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">No. of Pcs. Per 40Ft. Bundle <span class="text-danger"> *</span></label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <input type="number" min="0" step="0.001" name="pcs_per_forty" id="pcs_per_forty" class="form-control" value="{!! old( 'pcs_per_forty', $products['product_no']) !!}" maxlength="200" required>
                    @if ($errors->has('pcs_per_forty'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('pcs_per_forty') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">Weight Per Bundle in Kg. <span class="text-danger"> *</span></label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <input type="number" min="0" step="0.001" name="weight_per_bundle" id="weight_per_bundle" class="form-control" value="{!! old( 'weight_per_bundle', $products['part_no']) !!}" maxlength="200" required>
                    @if ($errors->has('weight_per_bundle'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('weight_per_bundle') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">{!! trans('panel.product.fields.gst') !!}</label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <input type="number" name="gst" class="form-control" value="{!! old( 'gst', !empty($products['productdetails']->pluck('gst')->first() ) ? $products['productdetails']->pluck('gst')->first() :'18' ) !!}" min="0" step="0.01">
                    @if ($errors->has('gst'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('gst') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer">
          {{ Form::submit('Submit', array('class' => 'btn btn-theme pull-right')) }}
        </div>
        {{ Form::close() }}
      </div>
    </div>
  </div>
  </div>
  <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
  <script src="{{ url('/').'/'.asset('assets/js/validation_products.js?') }}"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      var $table = $('table.kvcodes-dynamic-rows-example'),
        counter = $('#tab_logic tr:last').attr('value');
      $('a.add-rows').click(function(event) {
        event.preventDefault();
        counter++;
        var newRow =
          '<tr value="' + counter + '"><td>' + counter + '</td>' +
          '<td><input type="text" name="detail[' + counter + '][detail_title]" class="form-control"/></td>' +
          '<td><input type="text" name="detail[' + counter + '][detail_description]" class="form-control"/></td>' +
          '<td><input type="number" name="detail[' + counter + '][mrp]" class="form-control" step="0.00" min="0"/></td>' +
          '<td><input type="number" name="detail[' + counter + '][price]" class="form-control" step="0.00" min="0"/></td>' +
          '<td><input type="number" name="detail[' + counter + '][selling_price]" class="form-control discount rowchange" step="0.00" min="0" /></td>' +
          '<td class="td-actions text-center"><div class="btn-group btn-group-sm" role="group" aria-label="Small button group"><a href="#" class="btn btn-danger btn-just-icon btn-sm remove-rows"><i class="material-icons">clear</i></a></div></td>' +
          '</tr>';
        $table.append(newRow);
      });

      $table.on('click', '.remove-rows', function() {
        $(this).closest('tr').remove();
      });
    });
  </script>
</x-app-layout>