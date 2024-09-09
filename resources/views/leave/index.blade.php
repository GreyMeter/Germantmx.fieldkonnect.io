<x-app-layout>
  <style>
    a.custom-btn.create {
      background-color: #00aadb !important;
      font-size: 12px;
      padding: 13px 7px;
      font-weight: 500;
      border-radius: 5px;
      color: #000 !important;
      margin: 2px;
      text-align: center;
      line-height: normal;
      cursor: pointer;
      height: 42px;
      margin-top: 7px;
    }
  </style>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title">Leaves
            <span class="">
              <div class="btn-group header-frm-btn">
                @if(auth()->user()->can(['attendance_download']))
                <!-- <form method="GET" action="{{ URL::to('attendance-download') }}">
                  <div class="d-flex flex-row">

                    <div class="p-2" style="width: 250px;">
                      <select class="selectpicker" multiple name="branch_id" id="branch_id" data-style="select-with-transition" title="Select Branch">
                        <option value="">Select Branch</option>
                        @if(@isset($branches ))
                        @foreach($branches as $branche)
                        <option value="{!! $branche['id'] !!}" {{ old( 'branch_id') == $branche['id'] ? 'selected' : '' }}>{!! $branche['name'] !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>

                    <div class="p-2" style="width: 250px;">
                      <select class="selectpicker" name="executive_id" id="executive_id" data-style="select-with-transition" title="Select User">
                        <option value="">Select User</option>
                        @if(@isset($users ))
                        @foreach($users as $user)
                        <option value="{!! $user['id'] !!}" {{ old( 'executive_id') == $user['id'] ? 'selected' : '' }}>{!! $user['name'] !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>

                    <div class="p-2">
                      <input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly>
                    </div>
                    <div class="p-2">
                      <input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly>
                    </div>
                    <div class="p-2">
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} Attendance">
                        <i class="material-icons">cloud_download</i>
                      </button>
                    </div>
                  </div>
                </form> -->
                @endif
                <div class="next-btn">
                @if(auth()->user()->can(['leave_create']))
                <a data-toggle="modal" data-target="#submitLeave" class="custom-btn create" title="Punch In">
                  Add Leave
                </a>
                @endif
                <a href="{{ URL::to('attendance-location') }}" class="btn btn-just-icon btn-theme  d-none" title="Update Location"><i class="material-icons">add_location</i></a>
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
          @if(session()->has('message_success'))
          <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span>
              {{ session()->get('message_success') }}
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
            <table id="getattendance" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
              <thead class=" text-primary">
                <th>No</th>
                <th>User ID</th>
                <th>Status</th>
                <th>User Name</th>
                <th>From Date</th>
                <th>To Address</th>
                <th>Reason</th>
                <th>Working Type</th>
                <th>Leave Status</th>
                <th>Action</th>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal -->
  <div class="modal fade bd-example-modal-lg" id="submitLeave" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title">
            <span class="modal-title">Add </span> Leave <span class="pull-right">
              <a href="javascript:void(0)" class="btn btn-just-icon btn-danger" data-dismiss="modal">
                <i class="material-icons">clear</i>
              </a>
            </span>
          </h4>
        </div>
        <div class="modal-body">
          <form method="POST" action="{{ route('leaves.store') }}" enctype="multipart/form-data" id="createleadstagesForm"> @csrf
            <div class="row">
              <div class="col-md-12">
                <div class="input-group input-group-outline my-3">
                  <label class="form-label">User</label>
                  <select class=" select2" name="user_id" id="user_id" style="width: 100%;" required>
                    <option value="">Select User</option>
                    @if(@isset($users))
                    @foreach($users as $user)
                    <option value="{!! $user['id'] !!}">{!! $user['name'] !!}</option>
                    @endforeach
                    @endif
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="input-group input-group-outline my-3">
                  <label class="form-label">From Date</label>
                  <input type="text" name="from_date" id="from_date" class="ml-2 datepicker" value="{!! old( 'from_date') !!}" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="input-group input-group-outline my-3">
                  <label class="form-label">To Date</label>
                  <input type="text" name="to_date" id="to_date" class="ml-2 datepicker" value="{!! old( 'to_date') !!}" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="input-group input-group-outline my-3">
                  <label class="form-label">Type</label>
                  <select class=" select2" name="type" id="type" style="width: 100%;" required>
                    <option value="">Select Type</option>
                    <option value="Leave" data-is-city="false">Leave</option>
                    <option value="Holiday" data-is-city="false">Holiday</option>
                    <option value="First half leave" data-is-city="false">First Half Leave</option>
                    <option value="Second Half Leave" data-is-city="false">Second Half Leave</option>
                    <option value="Full Day Leave" data-is-city="false">Full Day Leave</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="input-group input-group-outline my-3">
                  <label class="form-label">Reason</label>
                  <textarea name="reason" id="reason" cols="50" rows="5">{!! old( 'reason') !!}</textarea>
                </div>
              </div>
            </div>
            <button id="add_leave" class="btn btn-info save"> Submit</button>
          </form>
        </div>
      </div>
    </div>
  </div>


  <!-- new model for reject attendance -->


  <div class="modal fade bd-example-modal-lg" id="rejec_leave" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title">
            <span class="modal-title">Submit </span> Remark <span class="pull-right">
              <a href="javascript:void(0)" class="btn btn-just-icon btn-danger" data-dismiss="modal">
                <i class="material-icons">clear</i>
              </a>
            </span>
          </h4>
        </div>
        <div class="modal-body">
          <form method="POST" action="{{ route('rejectLeave') }}" enctype="multipart/form-data" id="createleadstagesForm_new"> @csrf
            <div class="row">
              <div class="col-md-6">
                <div class="input-group input-group-outline my-3">
                  <label class="form-label">Remark</label>
                  <input type="text" name="remark_status" id="remark_status" class="form-control" value="{!! old( 'remark_status') !!}" required> <br><br>
                  <input type="text" name="leave_id" id="leave_id" class="form-control" hidden>

                </div>
              </div>
            </div>
            <button class="btn btn-info save"> Submit</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- end model for attendance -->




  <script type="text/javascript">
    $(document).ready(function() {

      //new user filters  starts

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      var table = $('#getattendance').DataTable({
        processing: true,
        serverSide: true,
        "order": [
          [0, 'desc']
        ],
        "retrieve": true,
        ajax: {
          url: "{{url('leaves')}}",
          data: function(d) {
            d.executive_id = $('#executive_id').val(),
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
            data: 'user_id',
            name: 'user_id',
            "defaultContent": ''
          },
          {
            data: 'action_status',
            name: 'action_status',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'users.name',
            name: 'users.name',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'from_date',
            name: 'from_date',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'to_date',
            name: 'to_date',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'reason',
            name: 'reason',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'type',
            name: 'type',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'status',
            name: 'status',
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

      $('#executive_id').change(function() {
        table.draw();
      });

      $('#start_date').change(function() {
        table.draw();
      });
      $('#end_date').change(function() {
        table.draw();
      });


      //new user filters end

      $('body').on('click', '.deleteLeave', function() {
        var id = $(this).attr("value");
        var token = $("meta[name='csrf-token']").attr("content");
        if (!confirm("Are You sure want to delete ?")) {
          return false;
        }
        $.ajax({
          url: "{{ url('leaves') }}" + '/' + id,
          type: 'DELETE',
          data: {
            _token: token,
            id: id
          },
          success: function(data) {
            $('.alert').show();
            if (data.status == 'success') {
              $('.alert').addClass("alert-success");
            } else {
              $('.alert').addClass("alert-danger");
            }
            $('.message').append(data.message);
            //oTable.draw();
            table.draw();
          },
        });
      });



      //approve
      $('body').on('click', '.approve_status', function() {
        var id = $(this).attr("value");
        var token = $("meta[name='csrf-token']").attr("content");
        if (!confirm("Are You sure want Approve Leave")) {
          return false;
        }

        $.ajax({
          url: "{{ url('approveLeave')}}",
          type: 'POST',
          data: {
            _token: token,
            id: id
          },
          success: function(data) {
            $('.message').empty();
            $('.alert').show();
            if (data.status == 'success') {
              $('.alert').addClass("alert-success");
            } else {
              $('.alert').addClass("alert-danger");
            }
            $('.message').append(data.message);
            //oTable.draw();
            table.draw();
          },
        });
      });


      $('body').on('click', '.reject_status', function() {
        var id = $(this).attr("value");
        var token = $("meta[name='csrf-token']").attr("content");
        if (!confirm("Are You sure want reject Leave")) {
          return false;
        } else {
          $('#leave_id').val(id);
          $("#rejec_leave").modal();
        }

      });



    });

    $("#branch_id").on('change', function() {
      var search_branches = $(this).val();
      $.ajax({
        url: "{{ url('reports/attendancereport') }}",
        data: {
          "search_branches": search_branches
        },
        success: function(res) {
          if (res.status == true) {
            var select = $('#executive_id');
            select.empty();
            select.append('<option>Select User</option>');
            $.each(res.users, function(k, v) {
              select.append('<option value="' + v.id + '" >' + v.name + '</option>');
            });
            select.selectpicker('refresh');
          }
        }
      });

    })

    $(document).on("dp.change", "#punchin_date", function(e) {
      var formatedValue = moment(e.date).format('YYYY-MM-DD');
      var todayDate = moment().format('YYYY-MM-DD');

      var user_id = $("#user_id").val();
      if (user_id && user_id != null && user_id != '') {
        if (user_id == '{{auth()->user()->id}}') {
          if (formatedValue === todayDate) {
            $("#date_error").addClass('d-none');
            $("#add_attend").prop('disabled', false);
          } else {
            $("#date_error").removeClass('d-none');
            $("#add_attend").prop('disabled', true);
            return false;
          }
        }
        var tour_plan = getTourPlanByUserAndDate(formatedValue, user_id);
      }
    });

    $(document).on("change", "#user_id", function(e) {
      var selectedDate = $('#punchin_date').val();
      if (selectedDate && selectedDate != null && selectedDate != '') {
        var formatedValue = moment(selectedDate).format('YYYY-MM-DD');
        console.log(formatedValue);
        var todayDate = moment().format('YYYY-MM-DD');

        var user_id = $(this).val();
        if (user_id == '{{auth()->user()->id}}') {
          if (formatedValue === todayDate) {
            $("#date_error").addClass('d-none');
            $("#add_attend").prop('disabled', false);
          } else {
            $("#date_error").removeClass('d-none');
            $("#add_attend").prop('disabled', true);
            return false;
          }
        }
        var tour_plan = getTourPlanByUserAndDate(formatedValue, user_id);
      }
    });

    $("#working_type").on("change", function() {
      var selectedOption = $(this).find('option:selected');
      var is_city = selectedOption.data('is-city');

      if (is_city == true) {
        var user_id = $("#user_id").val();
        if (user_id && user_id != null && user_id != '') {
          $.ajax({
            url: "{{ url('userCityList') }}",
            dataType: "json",
            type: "POST",
            data: {
              _token: "{{csrf_token()}}",
              user_id: user_id
            },
            success: function(res) {
              if (res.status == 'success') {
                $("#city_div").show();
                var html = '<option value="">Select City</option>';
                $.each(res.data, function(index, element) {
                  console.log(element);
                  html += '<option value="' + element.id + '">' + element.city_name + '</option>';
                });
                $("#city").html(html);
              }

            }
          });
        }
      }
    });


    function getTourPlanByUserAndDate(date, user_id) {
      $.ajax({
        url: "{{ url('getTourPlanByUserAndDate') }}",
        dataType: "json",
        type: "POST",
        data: {
          _token: "{{csrf_token()}}",
          date: date,
          user_id: user_id
        },
        success: function(res) {
          if (res.status == true) {
            $("#tour_error").addClass("d-none");
            $("#add_attend").prop('disabled', false);
            $("#tour_name").val(res.data.town);
            $("#tourid").val(res.data.id);
          } else {
            $("#tour_error").removeClass("d-none");
            $("#add_attend").prop('disabled', true);
          }
        }
      });
    }
  </script>
</x-app-layout>