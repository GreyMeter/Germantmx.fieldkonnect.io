<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title ">Zone {!! trans('panel.global.list') !!}
              <span class="">
                <div class="btn-group header-frm-btn">
                   <div class="next-btn">
                  @if(auth()->user()->can(['zone_upload']))
                  <form action="{{ URL::to('zone-upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
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
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} Zones">
                        <i class="material-icons">cloud_upload</i>
                        <div class="ripple-container"></div>
                      </button>
                    </div>
                  </div>
                  </form>
                  @endif
                 
                  @if(auth()->user()->can(['zone_download']))
                  <a href="{{ URL::to('zone-download') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} Zones"><i class="material-icons">cloud_download</i></a>
                  @endif
                  @if(auth()->user()->can(['zone_template']))
                  <a href="{{ URL::to('zone-template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} Zone"><i class="material-icons">text_snippet</i></a>
                  @endif
                  @if(auth()->user()->can(['zone_create']))
                   <a data-toggle="modal" data-target="#createzone" class="btn btn-just-icon btn-theme create" title="{!!  trans('panel.global.add') !!} Zone"><i class="material-icons">add_circle</i></a>
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
          <table id="getunit" class="table table-striped- table-bordered table-hover table-checkable no-wrap">
            <thead class=" text-primary">
              <th>{!! trans('panel.global.no') !!}</th>
              <th>ID</th>
              <th>{!! trans('panel.global.action') !!}</th>
              <!-- <th>{!! trans('panel.global.active') !!}</th> -->
              <th>Zone Name</th>
              <!-- <th>{!! trans('panel.unit.fields.zone_code') !!}</th> -->
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
<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="createzone" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title"><span class="modal-title">{!! trans('panel.global.add') !!}</span> Zone
          <span class="pull-right" >
            <a href="javascript:void(0)" class="btn btn-just-icon btn-danger" data-dismiss="modal"><i class="material-icons">clear</i></a>
          </span>
        </h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{ route('zones.store') }}" enctype="multipart/form-data" id="createzoneForm">
        @csrf
        <div class="row">
            <div class="col-md-10">
                <div class="row">
                  <label class="col-md-3 col-form-label">Zone Name <span class="text-danger"> *</span></label>
                  <div class="col-md-8">
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="name" id="name" class="form-control" value="{!! old( 'name') !!}" maxlength="200" required>
                      @if ($errors->has('name'))
                        <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('name') }}</p></div>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
              <!-- <div class="col-md-6">
                <div class="row">
                  <label class="col-md-3 col-form-label">{!! trans('panel.unit.fields.zone_code') !!} <span class="text-danger"> *</span></label>
                  <div class="col-md-9">
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="zone_code" id="zone_code" class="form-control" value="{!! old( 'zone_code') !!}" maxlength="200" required>
                      @if ($errors->has('zone_code'))
                        <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('zone_code') }}</p></div>
                      @endif
                    </div>
                  </div>
                </div>
              </div> -->
          </div>
        <div class="clearfix"></div>
        <div class="modal-footer">
          <input type="hidden" name="id" id="zone_id" />
          <button class="btn btn-info save"> Submit</button>
        </form>
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
    var table = $('#getunit').DataTable({
        processing: true,
        serverSide: true,
        "order": [ [0, 'desc'] ],
        ajax: "{{ route('zones.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'id', name: 'id', orderable: false, searchable: false },
            {data: 'action', name: 'action',"defaultContent": '',className: 'remove-sort-icon', orderable: false, searchable: false},
            //  {data: 'active', name: 'active',"defaultContent": '',className: 'remove-sort-icon', orderable: false, searchable: false},
            {data: 'name', name: 'name',"defaultContent": '', orderable: false},
            // {data: 'zone_code', name: 'zone_code',"defaultContent": ''},
            {data: 'createdbyname.name', name: 'createdbyname.name',"defaultContent": '', orderable: false},
            {data: 'created_at', name: 'created_at',"defaultContent": '', orderable: false},
        ]
    });
         
    $(document).on('click', '.edit', function(){
      var base_url =$('.baseurl').data('baseurl');
      var id = $(this).attr('id');
      $.ajax({
        url: base_url + '/zones/'+id,
       dataType:"json",
       success:function(data)
       {
        $('#name').val(data.name);
        $('#zone_id').val(data.id);
        var title = '{!! trans('panel.global.edit') !!}' ;
        $('.modal-title').text(title);
        $('#action_button').val('Edit');
        $('#createzone').modal('show');
       }
      })
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
    $('.create').click(function () {
        $('#zone_id').val('');
        $('#createzoneForm').trigger("reset");
        $("#zone_image").attr({ "src": '{!! asset('assets/img/placeholder.jpg') !!}' });
        $('.modal-title').text('{!! trans('panel.global.add') !!}');
    });
    
    $('body').on('click', '.delete', function () {
        var id = $(this).attr("value");
        var token = $("meta[name='csrf-token']").attr("content");
        if(!confirm("Are You sure want to delete ?")) {
           return false;
        }
        $.ajax({
            url: "{{ url('units') }}"+'/'+id,
            type: 'DELETE',
            data: {_token: token,id: id},
            success: function (data) {
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
