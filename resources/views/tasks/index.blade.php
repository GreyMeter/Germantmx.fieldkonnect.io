<x-app-layout>
<div class="row">
   <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title ">{!! trans('panel.task.title_singular') !!}{!! trans('panel.global.list') !!}
              <span class="">
                <div class="btn-group header-frm-btn">
                  <div class="next-btn">
                  @if(auth()->user()->can(['tasks_upload']))
                  <form action="{{ URL::to('tasks-upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
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
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} {!! trans('panel.task.title') !!}">
                        <i class="material-icons">cloud_upload</i>
                        <div class="ripple-container"></div>
                      </button>
                    </div>
                  </div>
                  </form>
                  @endif
                  
                  @if(auth()->user()->can(['tasks_download']))
                  <a href="{{ URL::to('tasks-download') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.task.title') !!}"><i class="material-icons">cloud_download</i></a>
                  @endif
                  @if(auth()->user()->can(['tasks_template']))
                  <a href="{{ URL::to('tasks-template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} {!! trans('panel.task.title_singular') !!}"><i class="material-icons">text_snippet</i></a>
                  @endif
                  @if(auth()->user()->can(['tasks_create']))
                  <a href="{{ route('tasks.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} {!! trans('panel.task.title_singular') !!}"><i class="material-icons">add_circle</i></a>
                  @endif
                  <a href="{{ route('tasks.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} {!! trans('panel.task.title_singular') !!}"><i class="material-icons">add_circle</i></a>
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
            <table id="gettasks" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
                <thead class="text-rose">
                <tr>
                  <th>{!! trans('panel.global.no') !!}</th>
                  <th width="12%">{!! trans('panel.global.action') !!}</th>
                  <th>{!! trans('panel.global.name') !!}</th>
                  <th>{!! trans('panel.task.task_title') !!}</th>
                  <th>{!! trans('panel.task.start_time') !!}</th>
                  <th>{!! trans('panel.task.due_time') !!}</th>
                  <th>{!! trans('panel.global.status') !!}</th>
                  <th>{!! trans('panel.global.created_at') !!}</th>
                </tr>
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
<div class="modal fade bd-example-modal-lg" id="showTaskData" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title"><span class="modal-title">{!! trans('panel.global.show') !!}</span> {!! trans('panel.task.title_singular') !!}
          <span class="pull-right" >
            <a href="javascript:void(0)" class="btn btn-just-icon btn-danger" data-dismiss="modal"><i class="material-icons">clear</i></a>
          </span>
        </h4>
      </div>
      <div class="modal-body">
          <h4 class="title"></h4>
          <p class="datetime"></p>
          <div class="descriptions"></div>
      </div>
    </div>
  </div>
</div>
<script src="{{ url('/').'/'.asset('assets/js/jquery.tasks.js') }}"></script>
<script type="text/javascript">
$(document).ready(function() {
    oTable = $('#gettasks').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [ [0, 'desc'] ],
        //"dom": 'Bfrtip',
        "ajax": "{{ route('tasks.index') }}",
        "columns": [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {data: 'action', name: 'action',"defaultContent": '',className: 'td-actions text-center', orderable: false, searchable: false},
            {data: 'users.name', name: 'users.name',"defaultContent": ''},
            {data: 'title', name: 'title',"defaultContent": ''},
            {data: 'datetime', name: 'datetime',"defaultContent": ''},
            {data: 'reminder', name: 'reminder',"defaultContent": ''},
            {data: 'statusname.status_name', name: 'statusname.status_name',"defaultContent": ''},
            {data: 'created_at', name: 'created_at',"defaultContent": ''},
        ]
    });
    $(document).on('click', '.show', function(){
      var base_url =$('.baseurl').data('baseurl');
      var id = $(this).attr('value');
      $.ajax({
        url: base_url + '/tasks/'+id,
       dataType:"json",
       success:function(data)
       {
        $('.task_type').html(data.task_type);
        $('.priority').html(data.priority_name);
        $('.title').html(data.title);
        $('.descriptions').html(data.descriptions);
        $('.datetime').html(data.datetime);
        $('.due_time').html(data.due_date +' '+data.due_day+' '+data.due_time);
        $('#showTaskData').modal('show');
       }
      })
     });
});
</script>
</x-app-layout>
