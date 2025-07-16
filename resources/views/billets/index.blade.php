<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title ">Billets {!! trans('panel.global.list') !!}
              <span class="">
                <div class="btn-group header-frm-btn">
                   <div class="next-btn">
                  @if(auth()->user()->can(['billet_upload']))
                  <form action="{{ URL::to('billet-upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                  {{ csrf_field() }}
                  <div class="input-group">
                      <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                        <span class="btn btn-just-icon btn-theme btn-file">
                          <span class="fileinput-new"><i class="material-icons">attach_file</i></span>
                          <span class="fileinput-exists">Change</span>
                          <input type="hidden">
                          <input type="file" name="import_file" required accept=".xls,.xlsx" />
                        </span>
                      </div>
                    <div class="input-group-append">
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} Billets">
                        <i class="material-icons">cloud_upload</i>
                        <div class="ripple-container"></div>
                      </button>
                    </div>
                  </div>
                  </form>
                  @endif
                 
                  @if(auth()->user()->can(['billet_download']))
                  <a href="{{ URL::to('billet-download') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} Billets"><i class="material-icons">cloud_download</i></a>
                  @endif
                  @if(auth()->user()->can(['billet_template']))
                  <a href="{{ URL::to('billet-template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} Billet"><i class="material-icons">text_snippet</i></a>
                  @endif
                  @if(auth()->user()->can(['billet_create']))
                   <a href="{{route('billets.create')}}" class="btn btn-just-icon btn-theme create" title="{!!  trans('panel.global.add') !!} Billet"><i class="material-icons">add_circle</i></a>
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
        <div class="alert " style="display: none;">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <i class="material-icons">close</i>
          </button>
          <span class="message"></span>
        </div>
        <div class="table-responsive">
          <table id="getBillet" class="table table-striped- table-bordered table-hover table-checkable no-wrap">
            <thead class=" text-primary">
              <th>{!! trans('panel.global.no') !!}</th>
              <th>Action</th>
              <th>Date</th>
              <th>From</th>
              <th>To</th>
              <th>Material</th>
              <th>Quantity (T) Billet</th>
              <th>Output (T) TMT Bar</th>
              <th>Balance (T)</th>
              <th>Rate</th>
              <th>Vehicle No</th>
              <th>Remarks</th>
              <th>{!! trans('panel.global.created_by') !!}</th>
              <th>{!! trans('panel.global.created_at') !!}</th>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
<script src="{{ url('/').'/'.asset('assets/js/validation_products.js') }}"></script>
<script type="text/javascript">
  $(function () {
    $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
    var table = $('#getBillet').DataTable({
        processing: true,
        serverSide: true,
        "order": [ [0, 'desc'] ],
        ajax: "{{ route('billets.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'action', name: 'action',"defaultContent": '', orderable: false, searchable: false },
            { data: 'date', name: 'date', orderable: false, searchable: false },
            {data: 'from_is', name: 'from_is',"defaultContent": '', orderable: false, searchable: false},
             {data: 'to_name.plant_name', name: 'to_name.plant_name',"defaultContent": '', orderable: false, searchable: false},
            {data: 'material', name: 'material',"defaultContent": '', orderable: false},
            {data: 'quantity', name: 'quantity',"defaultContent": '', orderable: false},
            {data: 'output', name: 'output',"defaultContent": '', orderable: false},
            {data: 'balance', name: 'balance',"defaultContent": '', orderable: false},
            {data: 'rate', name: 'rate',"defaultContent": '', orderable: false},
            {data: 'vehicle_no', name: 'vehicle_no',"defaultContent": '', orderable: false},
            {data: 'remarks', name: 'remarks',"defaultContent": '', orderable: false},
            {data: 'createdbyname.name', name: 'createdbyname.name',"defaultContent": '', orderable: false},
            {data: 'created_at', name: 'created_at',"defaultContent": '', orderable: false},
        ]
    });
         
    $('body').on('click', '.activeRecord', function () {
        var id = $(this).attr("id");
        var active = $(this).attr("value");
        var status = '';
        if(active == 'Y')
        {
          status = 'Incative ?';
        }
        else
        {
           status = 'Ative ?';
        }
        var token = $("meta[name='csrf-token']").attr("content");
        if(!confirm("Are You sure want "+status)) {
           return false;
        }
        $.ajax({
            url: "{{ url('units-active') }}",
            type: 'POST',
            data: {_token: token,id: id,active:active},
            success: function (data) {
              $('.message').empty();
              $('.alert').show();
              if(data.status == 'success')
              {
                $('.alert').addClass("alert-success");
              }
              else
              {
                $('.alert').addClass("alert-danger");
              }
              $('.message').append(data.message);
              table.draw();
            },
        });
    });
    });
</script>

</x-app-layout>
