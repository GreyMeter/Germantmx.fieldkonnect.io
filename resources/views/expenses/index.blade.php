 <x-app-layout>
   <div class="row">
     <div class="col-md-12">
       <div class="card">
         <div class="card-header card-header-icon card-header-theme">
           <div class="card-icon">
             <i class="material-icons">perm_identity</i>
           </div>
           <h4 class="card-title ">{!! trans('panel.expenses.title_singular') !!} {!! trans('panel.global.list') !!}
             <span class="">
               <div class="btn-group header-frm-btn">


                 @if(auth()->user()->can(['expense_download']))
                 <form method="GET" action="{{ URL::to('expenses-download') }}">


                   <div class="d-flex flex-row">
                     <div class="p-2" style="width:195px;">
                       <select class="selectpicker" name="payroll" id="payroll" data-style="select-with-transition">

                         @foreach($pay_rolls as $key=>$payroll)
                         <option value="{!! $key !!}">{!! $payroll !!}</option>
                         @endforeach


                       </select>
                     </div>

                     <div class="p-2" style="width:195px;">
                       <select class="selectpicker" name="expenses_type" id="expenses_type" data-style="select-with-transition">
                       </select>
                     </div>

                     <div class="p-2" style="width:150px;">
                       <select class="selectpicker1 select2" name="expense_id" id="expense_id" data-style="select-with-transition" title="Select Expense">
                         <option value="">Select Expense Id</option>

                       </select>
                     </div>


                     <div class="p-2" style="width:150px;">
                       <select class="selectpicker" name="branch_id" id="branch_id" data-style="select-with-transition" title="Select Branch">
                         <option value="">Select Branch</option>
                         @if(@isset($branches ))
                         @foreach($branches as $branch)
                         <option value="{!! $branch['id'] !!}">{!! $branch['name'] !!}</option>
                         @endforeach
                         @endif
                       </select>
                     </div>


                     <div class="p-2" style="width:150px;">
                       <select class="selectpicker" name="division_id" id="division_id" data-style="select-with-transition" title="Select Division">
                         <option value="">Select Division</option>
                         @if(@isset($divisions ))
                         @foreach($divisions as $division)
                         <option value="{!! $division['id'] !!}">{!! $division['name'] !!}</option>
                         @endforeach
                         @endif
                       </select>
                     </div>
                     <div class="p-2" style="width:150px;">
                       <select class="select2" name="executive_id" id="executive_id" data-style="select-with-transition" title="Select User">
                         <option value="">Select User</option>
                       </select>
                     </div>

                     <div class="p-2" style="width:160px;">
                       <select class="selectpicker" name="status" id="status" data-style="select-with-transition" title="Select Status">
                         <option value="">Select Status</option>
                         <option value="4">Checked By Reporting</option>
                         <option value="3">Checked</option>
                         <option value="1">Approved</option>
                         <option value="2">Rejected</option>
                         <option value="0">Pending</option>
                       </select>
                     </div>


                     <div class="p-2" style="width:140px;">
                       <input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly>
                     </div>
                     <div class="p-2" style="width:140px;">
                       <input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly>
                     </div>

                     <div class="p-2"><button type="button" class="btn btn-just-icon btn-theme" title="Reset Fliter" onclick="resetFilter();"><i class="fa fa-refresh" aria-hidden="true"></i></button></div>

                     <div class="p-2"><button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.customers.title') !!}"><i class="material-icons">cloud_download</i></button></div>


                   </div>
                 </form>


                 @endif

                 <div class="next-btn">
                   @if(auth()->user()->can(['expenses_create']))
                   <a href="{{ route('expenses.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} {!! trans('panel.expenses.title_singular') !!}"><i class="material-icons">add_circle</i></a>
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
           @if(session('message_success'))
           <div class="alert alert-success">
             <button type="button" class="close" data-dismiss="alert" aria-label="Close">
               <i class="material-icons">close</i>
             </button>
             <span>
               <li>{{session('message_success')}}</li>
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
             <table id="getallexpenses" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
               <thead class=" text-primary">
                 <th>{!! trans('panel.expenses.fields.expense_id') !!}</th>
                 <th>Expense Date</th>
                 <th>{!! trans('panel.expenses.fields.user') !!}</th>
                 <!-- <th>{!! trans('panel.expenses.fields.designation') !!}</th> -->
                 <th>{!! trans('panel.expenses.fields.expense_type') !!}</th>
                 <th>{!! trans('panel.expenses.fields.claim_amount') !!}</th>
                 <th>{!! trans('panel.expenses.fields.approve_amount') !!}</th>
                 <th>{!! trans('panel.expenses.fields.expense_status') !!}</th>
                 <th>{!! trans('panel.expenses.fields.note') !!}</th>
                 <th>{!! trans('panel.expenses.fields.created_at') !!}</th>
                 <!-- <th>{!! trans('panel.expenses.fields.branch') !!}</th> -->
                 <th>{!! trans('panel.expenses.fields.total_km') !!}</th>
                 <th>{!! trans('panel.global.action') !!}</th>
                 <th>Attechments</th>
               </thead>
               <tbody>
               </tbody>
             </table>
           </div>
         </div>
       </div>
     </div>
   </div>

   <!-- Bootstrap Modal -->

   <div class="modal fade" id="expenseModal" tabindex="-1" role="dialog" aria-labelledby="expenseModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-fullscreen" role="document">
       <div class="modal-content">
         <div class="modal-header">
           <h5 class="modal-title" id="expenseModalLabel">Expense Details</h5>
           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
             <span aria-hidden="true">&times;</span>
           </button>
         </div>
         <div class="modal-body" id="expenseDetails">
           <!-- Expense details will be loaded here -->
         </div>
         <div class="modal-footer">
           <button type="button" class="close" data-dismiss="modal" aria-label="Close">Close</button>
         </div>
       </div>
     </div>
   </div>

   <style type="text/css">
     .flex-row .p-2 {
       width: 20% !important;
       /*   overflow: hidden;*/
     }

     .flex-row {
       flex-direction: row !important;
       flex-wrap: wrap;
     }

     span.select2.select2-container.select2-container--default.select2-container--below.select2-container--focus {}

     span#select2-executive_id-container {
       color: #000;
       line-height: 43px;
     }

     .modal-fullscreen {
       width: 90%;
       height: 100%;
       margin: auto;
       padding: 0;
       max-width: none;
     }

     .modal-fullscreen .modal-content {
       height: 100vh;
       /* Viewport height */
     }

     .modal-fullscreen .modal-body {
       overflow-y: auto;
     }
   </style>
   <script>
     var expensesIndexUrl = "{{ route('expenses.index') }}";
     var expensesTypeUrl = "{{ route('getexpenseType') }}";
     var expensesActiveUrl = "{{ url('expenses-active') }}";
     var expensesCheckedUrl = "{{ url('expenses-checked-by-reporting') }}";
     var expensesDataUrl = "{{ route('getExpensesData') }}";
     var expensesUncheckUrl = "{{ url('expenses-uncheck') }}";
     var expensesMainUrl = "{{ url('expenses') }}";
     var removeSessionUrl = "{{ route('remove.session') }}";
     var session_exec = "{{ session('executive_id') }}";
     var token = $("meta[name='csrf-token']").attr("content");


     function resetFilter() {
       localStorage.setItem("is_reset", '1');
       localStorage.setItem("executive_id", '');
       fetch(removeSessionUrl, {
         method: 'POST',
         headers: {
           'Content-Type': 'application/json',
           'X-CSRF-TOKEN': token
         }
       })
       window.location.href = expensesMainUrl;
     }

     function showExpense(eid) {
       $.ajax({
         url: '/expenses/' + eid, // The URL to the route that returns the expense details
         method: 'GET',
         success: function(response) {

           $('#expenseDetails').html(response);
           // Show the modal
           $('#expenseModal').modal('show');
         },
         error: function(xhr, status, error) {
           console.error('Error fetching expense details:', error);
         }
       });
     }
     $("#payroll").on("change", function() {
      localStorage.removeItem('executive_id');
       var payroll = $(this).val();
       $.ajax({
         url: "{{ url('getUserList') }}",
         dataType: "json",
         type: "POST",
         data: {
           _token: "{{csrf_token()}}",
           payroll: payroll
         },
         success: function(res) {
          var html = '<option value="">Select User</option>';
          $.each(res, function(k, v) {
            html += '<option value="'+v.id+'"> ('+v.employee_codes+') '+v.name+'</option>';
          });
          $("#executive_id").html(html);
         }
       });
     }).trigger("chnage");
   </script>
   <script src="{{ asset('assets/js/expense_filter.js') . '?v=' . time() }}"></script>
 </x-app-layout>