<x-app-layout>
  <style>
    table tbody tr{
      font-size: 14px !important;
      font-weight: 100 !important;
    }
  </style>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title">Stock
            <span class="">
              <div class="btn-group header-frm-btn">
                @if(auth()->user()->can(['stock_download']))
                <form method="POST" action="{{url('stock/download')}}">
                  @csrf
                  <div class="d-flex flex-wrap flex-row">
                    <!-- Plant filter -->
                    <div class="p-2" style="width:180px;">
                      <select class="select2" name="plant_id" id="plant_id" data-style="select-with-transition" title="Plant">
                        <option value="" disabled selected>Plant</option>
                        @if(@isset($plants ))
                        @foreach($plants as $plant)
                        <option value="{!! $plant->id !!}">{!! $plant->plant_name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <!-- Brand filter -->
                    <div class="p-2" style="width:180px;">
                      <select class="select2" name="brand_id" id="brand_id" data-style="select-with-transition" title="Brand">
                        <option value="" disabled selected>Brand</option>
                        @if(@isset($brands ))
                        @foreach($brands as $brand)
                        <option value="{!! $brand->id !!}">{!! $brand->brand_name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <!-- Size filter -->
                    <div class="p-2" style="width:180px;">
                      <select class="select2" name="category_id" id="category_id" data-style="select-with-transition" title="Size">
                        <option value="" disabled selected>Size</option>
                        @if(@isset($sizes ))
                        @foreach($sizes as $size)
                        <option value="{!! $size->id !!}">{!! $size->category_name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <!-- Grade filter -->
                    <div class="p-2" style="width:180px;">
                      <select class="select2" name="unit_id" id="unit_id" data-style="select-with-transition" title="Grade">
                        <option value="" disabled selected>Grade</option>
                        @if(@isset($grades ))
                        @foreach($grades as $grade)
                        <option value="{!! $grade->id !!}">{!! $grade->unit_name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>

                    <div class="p-2" style="width:200px;">
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} Primary Sales">
                        <i class="material-icons">cloud_download</i>
                        <div class="ripple-container"></div>
                      </button>
                    </div>
                  </div>
                </form>
                @endif
                <div class="row next-btn">
                  @if(auth()->user()->can(['stock_upload']))
                  <form action="{{ URL::to('stock/upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="input-group" style="flex-wrap:nowrap;">
                      <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                        <span class="btn btn-just-icon btn-theme btn-file">
                          <span class="fileinput-new"><i class="material-icons">attach_file</i></span>
                          <span class="fileinput-exists">Change</span>
                          <input type="hidden">
                          <input title="Please select a file for upload data" type="file" title="Select file for upload data" name="import_file" style="flex-wrap: nowrap;" required accept=".xls,.xlsx" />
                        </span>
                      </div>
                      <div class="input-group-append">
                        <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} Stock">
                          <i class="material-icons">cloud_upload</i>
                          <div class="ripple-container"></div>
                        </button>
                      </div>
                    </div>
                  </form>
                  @endif
                  <!-- primary sales import -->
                  @if(auth()->user()->can(['stock_template']))
                  <!-- primary sales template creation -->
                  <a href="{{ URL::to('stock_template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} Stock"><i class="material-icons">text_snippet</i></a>
                  @endif
                </div>
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
          
          <div class="table-responsive">
            <table id="getprimarysales" class="table table-striped table-bordered table-hover table-checkable no-wrap">
              <thead class=" text-primary">
                <th>Plant Name</th>
                <th>Brand</th>
                <th>Size</th>
                <th>Grade</th>
                <th>Stock QTY</th>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script type="text/javascript">
    $(function() {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      var table = $('#getprimarysales').DataTable({
        processing: true,
        serverSide: true,
        columnDefs: [{
          className: 'control',
          orderable: false,
          targets: -1
        }],
        "order": [
          [0, 'desc']
        ],
        "retrieve": true,
        ajax: {
          url: "{{ route('stock') }}",
          data: function(d) {
              d.plant_id = $('#plant_id').val(),
              d.brand_id = $('#brand_id').val(),
              d.category_id = $('#category_id').val(),
              d.unit_id = $('#unit_id').val(),
              d.search = $('input[type="search"]').val()
          }
        },
        columns: [
          {
            data: 'plant.plant_name',
            name: 'plant.plant_name',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'brands.brand_name',
            name: 'brands.brand_name',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'sizes.category_name',
            name: 'sizes.category_name',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'grades.unit_name',
            name: 'grades.unit_name',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'stock',
            name: 'stock',
            searchable: false,
            "defaultContent": ''
          }
        ]
      });
      $('#plant_id').change(function() {
        table.draw();
      });
      $('#brand_id').change(function() {
        table.draw();
      });
      $('#category_id').change(function() {
        table.draw();
      });
      $('#unit_id').change(function() {
        table.draw();
      });
    });

    $('#reset-filter').on('click', function(){
      $('#prifilfrm').find('input:text, input:password, input:file, select, textarea').val('');
      $('#prifilfrm').find('select').change();
    })
  </script>
</x-app-layout>