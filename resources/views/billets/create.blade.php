<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">{{ trans('panel.global.create') }} Billet
            <span class="pull-right">
              <div class="btn-group">
                @if(auth()->user()->can(['billet_access']))
                <a href="{{ url('billets') }}" class="btn btn-just-icon btn-theme" title="Billets {!! trans('panel.global.list') !!}"><i class="material-icons">next_plan</i></a>
                @endif
              </div>
            </span>
          </h4>
        </div>
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
        <div class="card-body">

          {!! Form::model($billet,[
          'route' => $billet->exists ? ['billets.update', $billet->id] : 'billets.store',
          'method' => $billet->exists ? 'PUT' : 'POST',
          'id' => 'createBillet',
          'files'=>true
          ]) !!}
          <div class="row">
            <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">Date <span class="text-danger"> *</span></label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="date" class="form-control datepicker" value="{!! old( 'date', $billet['date']) !!}" required>
                    @if ($errors->has('date'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('date') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">From <span class="text-danger"> *</span></label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="from_is" class="form-control" value="Warehouse" readonly required>
                    @if ($errors->has('from_is'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('from_is') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">To <span class="text-danger"> *</span></label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <select name="to_is" id="to_is" class="form-control select2" required>
                      <option value=""> Please select </option>
                      @if($plants)
                      @foreach($plants as $plant)
                      <option value="{{$plant->id}}" {{ old( 'to_is', $billet['to_is']) == $plant->id ? 'selected' : ''}}>{{$plant->plant_name}}</option>
                      @endforeach
                      @endif
                    </select>
                    @if ($errors->has('to_is'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('to_is') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">Material <span class="text-danger"> *</span></label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="material" class="form-control" value="{!! old( 'material', $billet['material']) !!}" required>
                    @if ($errors->has('material'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('material') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">Quantity (T) Billet <span class="text-danger"> *</span></label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <input type="number" step="0.10" name="quantity" id="quantity" class="form-control" value="{!! old( 'quantity', $billet['quantity']) !!}" required>
                    @if ($errors->has('quantity'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('quantity') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">Output (T) TMT Bar <span class="text-danger"> *</span></label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <input type="number" step="0.01" name="output" id="output" class="form-control" value="{!! old( 'output', $billet['output']) !!}" required>
                    @if ($errors->has('output'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('output') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">Balance (T) <span class="text-danger"> *</span></label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <input type="number" step="0.10" id="balance" name="balance" class="form-control" value="{!! old( 'balance', $billet['balance']) !!}" readonly required>
                    @if ($errors->has('balance'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('balance') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">Rate <span class="text-danger"> *</span></label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <input type="number" step="0.01" name="rate" id="rate" class="form-control" value="{!! old( 'rate', $billet['rate']) !!}" required>
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
                <label class="col-md-3 col-form-label">Vehicle No <span class="text-danger"> *</span></label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="vehicle_no" id="vehicle_no" class="form-control" value="{!! old( 'vehicle_no', $billet['vehicle_no']) !!}" required>
                    @if ($errors->has('vehicle_no'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('vehicle_no') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="row">
                <label class="col-md-1 col-form-label">Remark <span class="text-danger"> *</span></label>
                <div class="col-md-11">
                  <div class="form-group has-default bmd-form-group">
                    <textarea name="remarks" class="form-control" id="remarks" rows="3">{!! old( 'remarks', $billet['remarks']) !!}</textarea>
                    @if ($errors->has('remarks'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('remarks') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer pull-right">
            {{ Form::submit('Submit', array('class' => 'btn btn-theme')) }}
          </div>
          {{ Form::close() }}
        </div>
      </div>
    </div>
  </div>
  <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
  <script>
    $(document).on('keyup', '#quantity, #output', function() {
      var quantity = $('#quantity').val();
      var output = $('#output').val();
      if(quantity > 0 && output > 0){
        var balance = quantity - output;
        $('#balance').val(balance.toFixed(2));
      }else{
        $('#balance').val(0.00);
      }
    });
  </script>
</x-app-layout>