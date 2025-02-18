<x-app-layout>
   <style>
      label.form-control {
         border: 0 crimson;
         font-weight: bold !important;
         color: #000 !important;
      }

      .row.image_preview {
         border: 1px solid lightgrey;
         border-radius: 10px;
      }

      .img-div {
         position: relative;
      }

      span.delete-img {
         position: absolute;
         top: -8px;
         right: -14px;
         background: red;
         color: #fff;
         border-radius: 50%;
         width: 20px;
         height: 20px;
         text-align: center;
         font-size: 14px;
         line-height: 18px;
         font-weight: 900;
         cursor: pointer;
         display: flex;
         align-items: center;
         justify-content: center;
      }

      .select2-container--default .select2-selection--multiple .select2-selection__choice {
         background-color: #0080b8;

      }

      .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
         color: red;
      }

      img {
         transition: transform 0.3s ease-in-out;
         cursor: pointer;
      }

      img:hover {
         transform: scale(1.05);
         /* Zoom out */
      }
   </style>
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
               @if(isset($setting) && isset($setting['slider_image']) && count($setting['slider_image']) > 0 )
               <div class="image_preview p-4 mb-3">
                  <div class="row">

                     <div class="col-md-12">
                        <h4 class="bmd-label-floating mt-2">Slider Images</h4>
                     </div>
                     @foreach($setting['slider_image'] as $image)
                     @php
                     $index = count(explode('/', $image['value'])) - 1;
                     $image['name'] = explode('/', $image['value'])[$index];
                     @endphp
                     
                     <div class="col-md-3 mb-4">
                        <div class="img-div">
                           <img style="box-shadow: 0 0 15px #000;" class="rounded" width="100%" src="{{asset($image['value'])}}" alt="{{$image['name']}}" title="{{explode('-',$image['name'])[1]}}"><span title="Delete Image" class="delete-img" data-id="{{$image['id']}}">X</span>
                        </div>
                     </div>

                     @endforeach
                  </div>
               </div>
               <hr>
               @endif
               {!! Form::model($setting,[
               'route' => 'settings.store',
               'method' => 'POST',
               'id' => 'storeLoyaltyAppSetting',
               'files'=>true
               ]) !!}

               <div class="row">

                  <div class="col-md-6">
                     <div class="">
                        <div class="row">
                           <div class="col-md-4">
                              <label class="form-control">Slider Images </label>
                           </div>
                           <div class="col-md-7">
                              <input type="file" multiple accept="image/*" name="slider_image[]" class="form-control" id="slider_image">
                              @if ($errors->has('slider_image'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('slider_image') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                     </div>
                  </div>

                  <div class="col-md-6">
                     <div class="">
                        <div class="row">
                           <div class="col-md-4">
                              <label class="form-control">News </label>
                           </div>
                           <div class="col-md-7">
                              <input type="text" name="news" class="form-control" id="news" value="{{old('news', $setting['news']??'')}}" required>
                              @if ($errors->has('news'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('news') }}</p>
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
      $('body').on('click', '.delete-img', function() {
         var deleteButton = $(this);
         var id = $(this).data("id");
         var token = $("meta[name='csrf-token']").attr("content");
         if (!confirm("Are You sure want to delete ?")) {
            return false;
         }
         $.ajax({
            url: "{{ url('settings') }}" + '/' + id,
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