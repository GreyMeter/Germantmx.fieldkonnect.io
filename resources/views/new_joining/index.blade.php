<x-app-layout>
  <style>
    span.select2-dropdown.select2-dropdown--below {
      z-index: 99999 !important;
    }
  </style>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">New Joining {!! trans('panel.global.list') !!}
            <span class="">
              <div class="btn-group header-frm-btn">
                @if(auth()->user()->can(['new_joining_download']))
                <form method="POST" action="{{ URL::to('new-joining/download') }}" class="form-horizontal">
                  @csrf 
                  <div class="d-flex flex-wrap flex-row">                   
                    <div class="p-2" style="width:160px;"><input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly></div>
                    <div class="p-2" style="width:160px;"><input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly></div>
                    <div class="p-2"><button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} Damage Entries"><i class="material-icons">cloud_download</i></button></div>
                </div>
                </form>
                @endif
                <!-- </div> -->
                <!-- <button class="btn btn-just-icon btn-theme" type="button" data-toggle="collapse" data-target="#multiCollapseExample2" aria-expanded="false" aria-controls="multiCollapseExample2"><i class="material-icons">menu</i></button> -->
                <!-- <div class="row">
                <div class="col"> -->
                <!-- <div class="collapse multi-collapse" id="multiCollapseExample2">
                    <div class="d-flex" style="font-size: 14px;align-items: center;justify-content: space-between;"> -->
                @if(auth()->user()->can(['transaction_history_upload']))
                <!-- <p>Upload Manual Transaction</p>
                      <form action="{{ URL::to('transaction_history_upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="d-flex">
                          <div class="fileinput-new text-center" data-provides="fileinput">
                            <span class="btn btn-just-icon btn-theme btn-file">
                              <span class="fileinput-new"><i class="material-icons">attach_file</i></span>
                              <span class="fileinput-exists">Change</span>
                              <input type="hidden">
                              <input type="file" name="import_file" required accept=".xls,.xlsx" />
                            </span>
                          </div>
                          <div class="input-group-append">
                            <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} Manual Transaction">
                              <i class="material-icons">cloud_upload</i>
                              <div class="ripple-container"></div>
                            </button>
                          </div>
                        </div>
                      </form> -->
                @endif
                <div class="next-btn">
                  @if(auth()->user()->can(['transaction_history_template']))
                  <!-- <p>{!!  trans('panel.global.template') !!} Manual Transaction</p>
                      <a href="{{ URL::to('transaction_history_template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} Manual Transaction"><i class="material-icons">text_snippet</i></a> -->
                  @endif
                  @if(auth()->user()->can(['new_joining_create']))
                  <!-- <p>{!!  trans('panel.global.add') !!} Transaction</p> -->
                  <!-- <a href="{{ route('damage_entries.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} Damage Entry"><i class="material-icons">add_circle</i></a> -->
                  <!-- <p>{!!  trans('panel.global.add') !!} Manual Transaction</p> -->
                      <a href="{{ route('joining-form') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} New Joining"><i class="material-icons">add_circle</i></a>
                  @endif
                  <!-- </div>
                  </div> -->
                 
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
          @if(session('message_success'))
          <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span>
              {{ session('message_success') }}
            </span>
          </div>
          @endif
          @if(session('message_info'))
          <div class="alert alert-info">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span>
              {{ session('message_info') }}
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
            <table id="getDamageEntries" class="table table-striped- table-bschemeed table-hover table-checkable no-wrap">
              <thead class=" text-primary">
                <th>{!! trans('panel.global.no') !!}</th>
                <th>{!! trans('panel.expenses.fields.date') !!}</th>
                <th>Email</th>
                <th>Name</th>
                <th>Mobile Number</th>
                <th>Branch</th>
                <th>Department</th>
                <th>Designation</th>
                <th>Show Detais</th>
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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
  <link rel="stylesheet" href="{{ url('/').'/'.asset('lightboxx/css/lightbox.min.css') }}">

  <script type="text/javascript">
    $(function() {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      var table = $('#getDamageEntries').DataTable({
        processing: true,
        serverSide: true,
        "order": [
          [0, 'desc']
        ],
        "ajax": {
          'url': "{{ route('new-joining') }}",
          'data': function(d) {
            d.status = $('#status').val(),
              d.parent_customer = $('#parent_customer').val(),
              d.scheme_name = $('#scheme_name').val(),
              d.start_date = $('#start_date').val(),
              d.end_date = $('#end_date').val()
          }
        },
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
          },
          {
            data: 'created_at',
            name: 'created_at',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'email',
            name: 'email',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'name',
            name: 'first_name',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'mobile_number',
            name: 'mobile_number',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'branch_details.branch_name',
            name: 'branch_details.branch_name',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'department_details.name',
            name: 'department_details.name',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'designation_details.designation_name',
            name: 'designation_details.designation_name',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'action',
            name: 'action',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
        ]
      });
      $('#start_date').change(function() {
        table.draw();
      });
      $('#end_date').change(function() {
        table.draw();
      });
    });
  </script>
</x-app-layout>