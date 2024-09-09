<x-app-layout>
   <style>
      .select2-results__options {
         overflow: auto;
         max-height: 200px !important;
      }

      .select2-results,
      .select2-search--dropdown,
      .select2-dropdown--above {
         min-width: 250px !important;
      }

      .select2-container {
         border-bottom: 1px solid lightgray;
      }
   </style>
   <div class="row">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header card-header-tabs card-header-warning">
               <div class="nav-tabs-navigation">
                  <div class="nav-tabs-wrapper">
                     <h4 class="card-title ">
                        Warranty Activation Creation
                        @if(auth()->user()->can(['district_access']))
                        <ul class="nav nav-tabs pull-right" data-tabs="tabs">
                           <li class="nav-item">
                              <a class="nav-link" href="javascript:void(0);" onclick="window.history.back();">
                                 <i class="material-icons">next_plan</i> Back
                                 <div class="ripple-container"></div>
                              </a>
                           </li>
                        </ul>
                        @endif
                     </h4>
                  </div>
               </div>
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
               {!! Form::model($warranty_activation,[
               'route' => $warranty_activation->exists ? ['warranty_activation.update', encrypt($warranty_activation->id) ] : 'warranty_activation.store',
               'method' => $warranty_activation->exists ? 'PUT' : 'POST',
               'id' => 'storeTransactionHistoryData',
               'files'=>true
               ]) !!}
               <div class="form-group">
                  <div class="row">
                     <div class="col-md-3">
                        <label for="status" class="form-control"><b>Warranty Activation Status</b></label>
                        <input type="hidden" name="previous_url" value="{{ strtok(url()->previous(), '?') }}">
                     </div>
                     <div class="col-md-3">
                        <select name="status" id="status" class="select2" required>
                           <option value="">Select Status</option>
                           <option value="0" {{($warranty_activation->exists && $warranty_activation->status == '0')?'selected':''}}>In Verification</option>
                           <option value="1" {{($warranty_activation->exists && $warranty_activation->status == '1')?'selected':''}}>Activated</option>
                           <option value="2" {{($warranty_activation->exists && $warranty_activation->status == '2')?'selected':''}}>Pending Activated</option>
                           <option value="3" {{($warranty_activation->exists && $warranty_activation->status == '3')?'selected':''}}>Rejected</option>
                        </select>
                     </div>
                     <div class="col-md-2 reject-remark d-none">
                        <label for="remark" class="form-control"><b>Reject Remark <span class="text-danger">*</span></b></label>
                     </div>
                     <div class="col-md-4 reject-remark d-none">
                        <textarea name="remark" id="remark" class="form-control">{{$warranty_activation->remark??""}}</textarea>
                     </div>
                  </div>
                  <div class="row">
                  </div>
                  <div class="mt-5">
                     <h5>Warranty Details</h5>
                     <input type="hidden" name="warranty_id" value="{{$warranty_activation->id}}">
                     <div class="row">
                        <div class="col-md-2">
                           <label for="product_serail_number" class="form-control">Product Serial Number</label>
                        </div>
                        <div class="col-md-4">
                           <input type="text" name="product_serail_number" id="product_serail_number" class="form-control" value="{!! old( 'product_serail_number' , $warranty_activation['product_serail_number'])??(isset($request)?$request->serial_no:'') !!}">
                           @if ($errors->has('product_serail_number'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('product_serail_number') }}</p>
                           </div>
                           @endif
                        </div>
                        <div class="col-md-2">
                           <label for="product_id" class="form-control">Product</label>
                        </div>
                        <div class="col-md-4">
                           <select name="select_product_id" id="select_product_id" placeholder="Select Product" class="select2 form-control">
                              @if($warranty_activation->exists && $warranty_activation->product_details)
                              <option value="{{$warranty_activation->product_details->id}}" selected>{{$warranty_activation->product_details->product_name}}</option>
                              @endif
                           </select>
                           <input type="hidden" name="product_id" id="product_id">
                           @if ($errors->has('product_id'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('product_id') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-2">
                           <label for="branch_id" class="form-control">Branch</label>
                        </div>
                        <div class="col-md-4">
                           <select name="branch_id" placeholder="Select Branch" class="select2 form-control" required>
                              <option value="" disabled selected>Select Branch</option>
                              @if($branches && count($branches) > 0)
                              @foreach($branches as $branche)
                              <option value="{{$branche->id}}" {!! old( 'branch_id' , $warranty_activation['branch_id'])==$branche->id?'selected':'' !!}>{{$branche->branch_name}}({{$branche->branch_code}})</option>
                              @endforeach
                              @endif
                           </select>
                           @if ($errors->has('branch_id'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('branch_id') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                  </div>
                  <div class="pt-4">
                     <h5>Contact Details</h5>
                     <div class="row">
                        <div class="col-md-2">
                           <label for="customer_number" class="form-control">Customer Number Search</label>
                        </div>
                        <div class="col-md-4">
                           <input type="number" name="customer_number" id="customer_number" class="form-control" value="{!! old( 'customer_number' , $warranty_activation['customer']?$warranty_activation['customer']['customer_number']:'') !!}" required>
                           <input type="hidden" name="end_user_id" id="end_user_id" value="{!! old( 'end_user_id' , $warranty_activation['end_user_id']) !!}">
                           @if ($errors->has('customer_number'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('customer_number') }}</p>
                           </div>
                           @endif
                        </div>
                        <div class="col-md-2">
                           <label for="customer_name" class="form-control">Customer Name</label>
                        </div>
                        <div class="col-md-4">
                           <input type="text" name="customer_name" id="customer_name" class="form-control" value="{!! old( 'customer_name' , $warranty_activation['customer']?$warranty_activation['customer']['customer_name']:'') !!}" required>
                           @if ($errors->has('customer_name'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('customer_name') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-2">
                           <label for="customer_email" class="form-control">Email</label>
                        </div>
                        <div class="col-md-4">
                           <input type="text" name="customer_email" id="customer_email" class="form-control" value="{!! old( 'customer_email' , $warranty_activation['customer']?$warranty_activation['customer']['customer_email']:'') !!}">
                           @if ($errors->has('customer_email'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('customer_email') }}</p>
                           </div>
                           @endif
                        </div>
                        <div class="col-md-2">
                           <label for="customer_place" class="form-control">Place</label>
                        </div>
                        <div class="col-md-4">
                           <input type="text" name="customer_place" id="customer_place" class="form-control" value="{!! old( 'customer_place' , $warranty_activation['customer']?$warranty_activation['customer']['customer_place']:'') !!}">
                           @if ($errors->has('customer_place'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('customer_place') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-2">
                           <label for="customer_address" class="form-control">Address</label>
                        </div>
                        <div class="col-md-4">
                           <input type="text" name="customer_address" id="customer_address" class="form-control" value="{!! old( 'customer_address' , $warranty_activation['customer']?$warranty_activation['customer']['customer_address']:'') !!}" required>
                           @if ($errors->has('customer_address'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('customer_address') }}</p>
                           </div>
                           @endif
                        </div>
                        <div class="col-md-2">
                           <label for="customer_pindcode" class="form-control">Pincode</label>
                        </div>
                        <div class="col-md-4">
                           <select name="customer_pindcode" id="customer_pindcode" placeholder="Select Pincode" class="select2 form-control">
                              <option value="" disabled selected>Select Pincode</option>
                              @if($pincodes && count($pincodes) > 0)
                              @foreach($pincodes as $pincode)
                              <option value="{{$pincode->id}}" {!! old( 'customer_pindcode' , $warranty_activation['customer']?$warranty_activation['customer']['customer_pindcode']:'')==$pincode->id?'selected':'' !!}>{{$pincode->pincode}}</option>
                              @endforeach
                              @endif
                           </select>
                           @if ($errors->has('customer_pindcode'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('customer_pindcode') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-1">
                           <label for="customer_country" class="form-control">Country</label>
                        </div>
                        <div class="col-md-2">
                           <input type="text" readonly name="customer_country" id="customer_country" class="form-control">
                           @if ($errors->has('customer_country'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('customer_country') }}</p>
                           </div>
                           @endif
                        </div>
                        <div class="col-md-1">
                           <label for="customer_state" class="form-control">State</label>
                        </div>
                        <div class="col-md-2">
                           <select name="customer_state" id="customer_state" placeholder="Select State" class="select2 form-control" required>
                              <option value="" disabled selected>Select State</option>
                              @if($states && count($states) > 0)
                              @foreach($states as $state)
                              <option value="{{$state->id}}" {!! old( 'customer_state' , $warranty_activation['customer']?$warranty_activation['customer']['customer_state']:'')==$state->id?'selected':'' !!}>{{$state->state_name}}</option>
                              @endforeach
                              @endif
                           </select>
                           @if ($errors->has('customer_state'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('customer_state') }}</p>
                           </div>
                           @endif
                        </div>
                        <div class="col-md-1">
                           <label for="customer_district" class="form-control">District</label>
                        </div>
                        <div class="col-md-2">
                           <input type="text" readonly name="customer_district" id="customer_district" class="form-control">
                           @if ($errors->has('customer_district'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('customer_district') }}</p>
                           </div>
                           @endif
                        </div>
                        <div class="col-md-1">
                           <label for="customer_city" class="form-control">City</label>
                        </div>
                        <div class="col-md-2">
                           <input type="text" readonly name="customer_city" id="customer_city" class="form-control">
                           @if ($errors->has('customer_city'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('customer_city') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-2">
                           <label for="customer_status" class="form-control">Customer Statsu</label>
                        </div>
                        <div class="col-md-4">
                           <input type="radio" name="customer_status" id="inactive" value="0"> Inactive
                           <input type="radio" checked name="customer_status" id="active" value="1"> active
                        </div>
                     </div>
                  </div>
                  <div class="pt-4">
                     <h5>Other Details</h5>
                     <div class="row">
                        <div class="col-md-2">
                           <label for="customer_id" class="form-control">Seller</label>
                        </div>
                        <div class="col-md-4">
                           <select name="customer_id" placeholder="Select Customers" class="select2 form-control" required>
                              <option value="" disabled selected>Select Customer</option>
                              @if($customers && count($customers) > 0)
                              @foreach($customers as $customer)
                              <option value="{{$customer->id}}" {!! old( 'customer_id' , $warranty_activation['customer_id'])==$customer->id?'selected':'' !!}>{{$customer->name}}({{$customer->mobile}})</option>
                              @endforeach
                              @endif
                           </select>
                           @if ($errors->has('customer_id'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('customer_id') }}</p>
                           </div>
                           @endif
                        </div>
                        <div class="col-md-2">
                           <label for="sale_bill_date" class="form-control">Sale Bill Date</label>
                        </div>
                        <div class="col-md-4">
                           <input type="text" name="sale_bill_date" id="sale_bill_date" class="datepicker form-control" placeholder="Sale Bill Date" autocomplete="off" value="{!! old( 'sale_bill_date' , $warranty_activation['sale_bill_date']) !!}" required>
                           @if ($errors->has('sale_bill_date'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('sale_bill_date') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-2">
                           <label for="warranty_date" class="form-control">Warranty Date</label>
                        </div>
                        <div class="col-md-4">
                           <input type="text" name="warranty_date" id="warranty_date" class="datepicker form-control" placeholder="Warranty Date" autocomplete="off" value="{!! old( 'warranty_date' , $warranty_activation['warranty_date']) !!}" required>
                           @if ($errors->has('warranty_date'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('warranty_date') }}</p>
                           </div>
                           @endif
                        </div>
                        <div class="col-md-2">
                           <label for="sale_bill_no" class="form-control">Co Sale Bill No.</label>
                        </div>
                        <div class="col-md-4">
                           <input type="text" name="sale_bill_no" id="sale_bill_no" class="form-control" value="{!! old( 'sale_bill_no' , $warranty_activation['sale_bill_no']) !!}" required>
                           @if ($errors->has('sale_bill_no'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('sale_bill_no') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-2">
                     <label for="warranty_activation_attach" class="form-control">Attachment</label>
                  </div>
                  <div class="col-md-3 col-sm-3 mt-4">
                     <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                        <div class="selectThumbnail">
                           <span class="btn btn-just-icon btn-round btn-file">
                              <span class="fileinput-new"><i class="fa fa-pencil"></i></span>
                              <span class="fileinput-exists">Change</span>
                              <input type="file" name="warranty_activation_attach" class="getimage1" accept="image/*,application/pdf">
                           </span>
                           <br>
                           <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Remove</a>
                        </div>
                        <div class="fileinput-new thumbnail">
                           @if($warranty_activation->exists && $warranty_activation->getMedia('warranty_activation_attach')->count() > 0)
                           @php
                           $media = $warranty_activation->getFirstMedia('warranty_activation_attach');
                           $filePath = $media->getPath();
                           @endphp

                           @if(Storage::disk('s3')->exists($filePath))
                           @if($media->mime_type == 'application/pdf')
                           <a href="{!! $media->getFullUrl() !!}" target="_blank">{{ $media->file_name }}</a>
                           @else
                           <img src="{!! $media->getFullUrl() !!}" class="imagepreview1">
                           @endif
                           @else
                           <img src="{!! url('/').'/'.asset('assets/img/placeholder.jpg') !!}" class="imagepreview1">
                           @endif
                           @else
                           <img src="{!! url('/').'/'.asset('assets/img/placeholder.jpg') !!}" class="imagepreview1">
                           @endif
                        </div>

                        <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div>
                        <!-- <label class="bmd-label-floating">Attachment 1st</label> -->
                        @if ($errors->has('warranty_activation_attach'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('warranty_activation_attach') }}</p>
                        </div>
                        @endif
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
      $(document).ready(function() {
         var sr_no = '{{$request->serial_no??""}}';
         if (sr_no != '') {
            $("#product_serail_number").keyup();
         }
      })
      $("#product_serail_number").on("keyup", function() {
         var serial_no = $(this).val();
         $.ajax({
            url: "{{ url('getProductByCoupon') }}",
            dataType: "json",
            type: "POST",
            data: {
               _token: "{{csrf_token()}}",
               serial_no: serial_no
            },
            success: function(res) {
               if (res.status == true) {
                  $('#select_product_id').html(res.html);
                  $('#product_id').val($('#select_product_id').val());
                  if (res.slected === true) {
                     $('#select_product_id').prop('disabled', true);
                  } else {
                     $('#select_product_id').prop('disabled', false);
                  }
               }
            }
         });
      }).trigger('change');

      $("#select_product_id").on("change", function() {
         $('#product_id').val($(this).val());
      })
      $("#customer_pindcode").on("change", function() {
         var customer_pindcode = $(this).val();
         $.ajax({
            url: "{{ url('getAddressData') }}",
            dataType: "json",
            type: "POST",
            data: {
               _token: "{{csrf_token()}}",
               pincode_id: customer_pindcode
            },
            success: function(res) {
               $("#customer_country").val(res.country_name);
               $("#customer_state").val(res.state_id);
               $("#customer_state").change();
               $("#customer_district").val(res.district_name);
               $("#customer_city").val(res.city_name);
            }
         });
      }).trigger('change');

      $("#customer_number").on("keyup", function() {
         var customer_number = $(this).val();
         $.ajax({
            url: "{{ url('getEndUserData') }}",
            dataType: "json",
            type: "POST",
            data: {
               _token: "{{csrf_token()}}",
               customer_number: customer_number
            },
            success: function(res) {
               if (res.status === true) {
                  if (res.data.status == '0') {
                     $('#inactive').prop('checked', true);
                  } else {
                     $('#active').prop('checked', true);
                  }
                  $("#customer_name").val(res.data.customer_name);
                  $("#end_user_id").val(res.data.id);
                  $("#customer_email").val(res.data.customer_email);
                  $("#customer_address").val(res.data.customer_address);
                  $("#customer_place").val(res.data.customer_place);
                  $("#customer_pindcode").val(res.data.customer_pindcode).trigger("change");;
                  $("#customer_state").val(res.data.customer_id);
                  $("#customer_state").change();
                  $("#customer_district").val(res.data.customer_district);
                  $("#customer_city").val(res.data.customer_city);

                  $("#customer_name").prop('readonly', true);
                  $("#customer_email").prop('readonly', true);
                  $("#customer_address").prop('readonly', true);
                  $("#customer_place").prop('readonly', true);
                  $("#customer_pindcode").prop('disabled', true);
                  $("#customer_state").prop('readonly', true);
                  $("#customer_district").prop('readonly', true);
                  $("#customer_city").prop('readonly', true);

               } else {
                  $("#customer_name").val("");
                  $("#end_user_id").val("");
                  $("#customer_email").val("");
                  $("#customer_address").val("");
                  $("#customer_place").val("");
                  $("#customer_pindcode").val("").trigger("change");;
                  $("#customer_state").val("");
                  $("#customer_state").change("");
                  $("#customer_district").val("");
                  $("#customer_city").val("");

                  $("#customer_name").prop('readonly', false);
                  $("#customer_email").prop('readonly', false);
                  $("#customer_address").prop('readonly', false);
                  $("#customer_place").prop('readonly', false);
                  $("#customer_pindcode").prop('disabled', false);
                  $("#customer_state").prop('readonly', false);
                  $("#customer_district").prop('readonly', false);
                  $("#customer_city").prop('readonly', false);
                  $("#customer_name").prop('readonly', false);
                  $("#customer_email").prop('readonly', false);
               }
            }
         });
      }).trigger('change');

      $('#status').on('change', function() {
         if ($(this).val() == '3') {
            $('.reject-remark').removeClass('d-none');
         } else {
            $('.reject-remark').addClass('d-none');
         }
      }).trigger('change');
   </script>
</x-app-layout>