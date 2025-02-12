<x-app-layout>
   <div class="row">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header card-header-tabs card-header-warning">
               <div class="nav-tabs-navigation">
                  <div class="nav-tabs-wrapper">
                     <h4 class="card-title ">
                        Setting
                     </h4>
                  </div>
               </div>
            </div>
            @if (session('success'))
            <div class="alert alert-success mt-3">
               <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <i class="material-icons">close</i>
               </button>
               {{ session('success') }}
            </div>
            @endif
            <div class="alert mt-3" style="display: none;">
               <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <i class="material-icons">close</i>
               </button>
               <span class="message"></span>
            </div>
            @if (session('error'))
            <div class="alert alert-danger mt-3">
               <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <i class="material-icons">close</i>
               </button>
               {{ session('error') }}
            </div>
            @endif

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
               {!! Form::model($setting,[
               'route' => 'settings.store',
               'method' => 'POST',
               'id' => 'storeLoyaltyAppSetting',
               'files'=>true
               ]) !!}



               <div class="row">

                  <div class="col-md-6">
                     <div class="form-group">
                        <div class="row">
                           <div class="col-md-4">
                              <label class="form-control">Booking Start Time </label>
                           </div>
                           <div class="col-md-7">
                              <input type="time" name="booking_start_time" class="form-control" id="booking_start_time" value="{{old('booking_start_time', $setting['booking_start_time']??'')}}" required>
                              @if ($errors->has('booking_start_time'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('booking_start_time') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <div class="row">
                           <div class="col-md-4">
                              <label class="form-control">Booking End Time </label>
                           </div>
                           <div class="col-md-7">
                              <input type="time" name="booking_end_time" class="form-control" id="booking_end_time" value="{{old('booking_end_time', $setting['booking_end_time']??'')}}" required>
                              @if ($errors->has('booking_end_time'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('booking_end_time') }}</p>
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
   <script src="{{ url('/').'/'.asset('assets/js/validation_loyalty.js') }}"></script>
   <script>
         $('body').on('click', '.close-btn', function() {
            var deleteButton = $(this);
            var id = $(this).data("id");
            var token = $("meta[name='csrf-token']").attr("content");
            if (!confirm("Are You sure want to delete ?")) {
               return false;
            }
            $.ajax({
               url: "{{ url('field-konnect-app-setting') }}" + '/' + id,
               type: 'DELETE',
               data: {
                  _token: token,
                  id: id
               },
               success: function(data) {
                  $('.message').empty();
                  $('.alert').show();
                  if (data.status == 'success') {
                     deleteButton.parent('div').addClass('d-none');
                     $('.alert').addClass("alert-success");
                  } else {
                     $('.alert').addClass("alert-danger");
                  }
                  $('.message').append(data.message);
               },
            });
         });
      // 
   </script>
</x-app-layout>