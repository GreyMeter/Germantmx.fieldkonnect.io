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

      button.delete-img-btn {
         position: absolute;
         cursor: pointer;
         right: 0;
      }

      .img-div {
         width: 25%;
         text-align: center;
         border: 1px solid #ab9a9a;
         margin: 2px 10px;
         border-radius: 5px;
         background: radial-gradient(#c5b0b0, transparent);
      }

      .row {
         align-items: end !important;
      }
   </style>
   <div class="row">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header card-header-tabs card-header-warning">
               <div class="nav-tabs-navigation">
                  <div class="nav-tabs-wrapper">
                     <h4 class="card-title ">
                        Complaint Creation
                        @if(auth()->user()->can(['district_access']))
                        <ul class="nav nav-tabs pull-right" data-tabs="tabs">
                           <li class="nav-item">
                              <a class="nav-link" href="{{ url('complaints') }}">
                                 <i class="material-icons">next_plan</i> {!! trans('panel.complaint.title') !!}
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
               {!! Form::model($complaints,[
               'route' => $complaints->exists ? ['complaints.update', $complaints->id ] : 'complaints.store',
               'method' => $complaints->exists ? 'PUT' : 'POST',
               'id' => 'storeComplaintData',
               'files'=>true
               ]) !!}
               <div class="row">
                  @if(!$complaints->exists)
                  <div class="col-md-4">
                     <div class="form-group">
                        <label class="bmd-label-floating">Search By Searial Number </label>
                        <input type="text" name="serail_number" id="serail_number" class="form-control" value="{!! old( 'serail_number', $complaints['serail_number']) !!}">
                        <p class="text-danger d-none" id="search_error"></p>
                        @if ($errors->has('serail_number'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('serail_number') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-2">
                     <button type="button" class="btn btn-success" id="go-search">Go Search</button>
                  </div>
                  @endif
                  <div class="col-md-3">
                     <div class="">
                        <label>Assign User </label>
                        <select name="assign_user" id="assign_user" class="select2 form-control">
                           <option value="">Select User</option>
                           @if($assign_users)
                           @foreach($assign_users as $assign_user)
                           <option value="{{$assign_user->id}}" {!! old('assign_user', $complaints['assign_user'])==$assign_user->id ? 'selected':'' !!} >[{{$assign_user->id}}] {{$assign_user->name}}</option>
                           @endforeach
                           @endif
                        </select>
                        @if ($errors->has('assign_user'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('assign_user') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="">
                        <label>Service Center </label>
                        <select name="service_center" id="service_center" class="select2 form-control">
                           <option value="">Select Service Center</option>
                           @if($service_centers)
                           @foreach($service_centers as $service_center)
                           <option value="{{$service_center->id}}" {!! old('service_center', $complaints['service_center'])==$service_center->id ? 'selected':'' !!} >[{{$service_center->id}}] {{$service_center->name}}</option>
                           @endforeach
                           @endif
                        </select>
                        @if ($errors->has('service_center'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('service_center') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
               </div>
               <div class="row d-none  mt-2 mb-2" id="search_data">
                  <h3>Complaint History</h3>
                  <table class="table table-striped table-bordered">
                     <thead>
                        <tr>
                           <th scope="col">Complaint No</th>
                           <th scope="col">Complaint date</th>
                           <th scope="col">Product Serial No</th>
                           <th scope="col">Claim Amount</th>
                           <th scope="col">Complaint Status</th>
                           <th scope="col">Service Center</th>
                           <th scope="col">Seller (Company billed Party )</th>
                           <th scope="col">Purchased Party Name </th>
                           <th scope="col">Close Remark </th>
                        </tr>
                     </thead>
                     <tbody>

                     </tbody>
                  </table>
               </div>
               <div class="row mt-3">
                  <div class="col-md-3">
                     <div class="form-group">
                        <label class="bmd-label-floating">Complaint Number </label>
                        <input type="text" readonly name="complaint_number" class="form-control" value="{!! old( 'complaint_number', $complaints['complaint_number']) ?? $newComplaintNumber !!}">
                        @if ($errors->has('complaint_number'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('complaint_number') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label class="bmd-label-floating">Complaint Date </label>
                        <input type="text" readonly name="complaint_date" id="complaint_date" class="form-control datepicker" value="{!! old( 'complaint_date', $complaints['complaint_date'])?? Carbon\Carbon::now()->toDateString() !!}">
                        @if ($errors->has('complaint_date'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('complaint_date') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label class="bmd-label-floating">Seller (Company billed Party )</label>
                        <input type="text" name="seller" id="seller" class="form-control" value="{{old('seller', $complaints['seller'])}}">
                        @if ($errors->has('seller'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('seller') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label class="bmd-label-floating">Purchased Party Name </label>
                        <select name="party_name" id="party_name" class="select2 form-control">
                           @if(old('party_name', $complaints['party_name']))
                           <option selected value="{{$complaints['party_name']}}">{{$complaints['party']['name']}}</option>
                           @endif
                        </select>
                        @if ($errors->has('party_name'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('party_name') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-3">
                     <div class="form-group">
                        <label class="bmd-label-floating">Product Laying at </label>
                        <select name="product_laying" id="product_laying" class="select2 form-control">
                           <option value="">Product Laying at</option>
                           <option value="Customer" {!! old('product_laying', $complaints['product_laying'])=='Customer' ? 'selected' :'' !!}>Customer</option>
                           <option value="Dealer" {!! old('product_laying', $complaints['product_laying'])=='Dealer' ? 'selected' :'' !!}>Dealer</option>
                           <option value="Distributor" {!! old('product_laying', $complaints['product_laying'])=='Distributor' ? 'selected' :'' !!}>Distributor</option>
                           <option value="Retailer" {!! old('product_laying', $complaints['product_laying'])=='Retailer' ? 'selected' :'' !!}>Retailer</option>
                           <option value="ASC" {!! old('product_laying', $complaints['product_laying'])=='ASC' ? 'selected' :'' !!}>ASC</option>
                           <option value="Branch" {!! old('product_laying', $complaints['product_laying'])=='Branch' ? 'selected' :'' !!}>Branch</option>
                        </select>
                        @if ($errors->has('product_laying'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('product_laying') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <!-- <div class="col-md-3">
                     <div class="form-group">
                        <label class="bmd-label-floating">Claim Amount </label>
                        <input type="number" name="claim_amount" id="claim_amount" class="form-control" value="{!! old( 'claim_amount', $complaints['claim_amount']) !!}">
                        @if ($errors->has('claim_amount'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('claim_amount') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label class="bmd-label-floating">Assign User </label>
                        <select name="complaint_status" id="complaint_status" class="select2 form-control">
                           <option value="">Complaint Status</option>
                           <option value="0" {!! old('complaint_status', $complaints['complaint_status'])=='0' ? 'selected' :'' !!}>Open</option>
                           <option value="1" {!! old('complaint_status', $complaints['complaint_status'])=='1' ? 'selected' :'' !!}>Pendding</option>
                           <option value="2" {!! old('complaint_status', $complaints['complaint_status'])=='2' ? 'selected' :'' !!}>Work Done</option>
                           <option value="3" {!! old('complaint_status', $complaints['complaint_status'])=='3' ? 'selected' :'' !!}>Completed</option>
                           <option value="3" {!! old('complaint_status', $complaints['complaint_status'])=='4' ? 'selected' :'' !!}>Closed</option>
                           <option value="3" {!! old('complaint_status', $complaints['complaint_status'])=='5' ? 'selected' :'' !!}>Cancel</option>
                        </select>
                        @if ($errors->has('complaint_status'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('complaint_status') }}</p>
                        </div>
                        @endif
                     </div>
                  </div> -->
               </div>
               <div class="basic_details mt-1">
                  <h3><b>Complaint Registration :</b></h3>
                  <div class="border border-dark rounded p-4">
                     <div class="row mt-3">
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="bmd-label-floating">Product Searial Number </label>
                              <input {{($complaints->exists && empty($complaints['product_serail_number']))?'':'readonly'}} type="text" name="product_serail_number" id="product_serail_number" class="form-control" value="{!! old( 'product_serail_number', $complaints['product_serail_number']) !!}">
                              @if ($errors->has('product_serail_number'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('product_serail_number') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="bmd-label-floating">Product/Model(Code) </label>
                              <input readonly type="text" name="product_code" id="product_code" class="form-control" value="{!! old( 'product_code', $complaints['product_code']) !!}">
                              @if ($errors->has('product_code'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('product_code') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="bmd-label-floating">Product Name </label>
                              <select name="product_id" id="product_id" class="select2">
                                 <option value="">Select Product</option>
                                 @if(count($products) > 0)
                                 @foreach($products as $product)
                                 <option value="{{$product->id}}" {{($complaints && $complaints['product_id']==$product->id)?'selected':''}}>{{$product->product_name}}</option>
                                 @endforeach
                                 @endif
                              </select>
                              <input readonly type="hidden" name="product_name" id="product_name" class="form-control" value="{!! old( 'product_name', $complaints['product_name']) !!}">
                              @if ($errors->has('product_name'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('product_name') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="bmd-label-floating">Division </label>
                              <input readonly type="text" name="category" id="category" class="form-control" value="{!! old( 'category', $complaints['category']) !!}">
                              @if ($errors->has('category'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('category') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                     </div>
                     <div class="row mt-3">
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="bmd-label-floating">HP </label>
                              <input readonly type="text" name="specification" id="specification" class="form-control" value="{!! old( 'specification', $complaints['specification']) !!}">
                              @if ($errors->has('specification'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('specification') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="bmd-label-floating">Stage </label>
                              <input readonly type="text" name="product_no" id="product_no" class="form-control" value="{!! old( 'product_no', $complaints['product_no']) !!}">
                              @if ($errors->has('product_no'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('product_no') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="bmd-label-floating">Phase </label>
                              <input readonly type="text" name="phase" id="phase" class="form-control" value="{!! old( 'phase', $complaints['phase']) !!}">
                              @if ($errors->has('phase'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('phase') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-3">
                           <!-- <div class="form-group">
                              <label class="bmd-label-floating">Seller Branch </label>
                              <select disabled name="seller_branch" id="seller_branch" class="select2 form-control">
                                 <option value="">Seller Branch</option>
                                 @if($branchs)
                                 @foreach($branchs as $branch)
                                 <option value="{{$branch->id}}">[{{$branch->branch_code}}] {{$branch->branch_name}}</option>
                                 @endforeach
                                 @endif
                              </select>
                              @if ($errors->has('seller_branch'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('seller_branch') }}</p>
                              </div>
                              @endif
                           </div> -->
                        </div>
                     </div>
                     <div class="row mt-3">
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="bmd-label-floating">Service Branch </label>
                              <select name="purchased_branch" id="purchased_branch" class="select2 form-control">
                                 <option value="">Service Branch</option>
                                 @if($branchs)
                                 @foreach($branchs as $branch)
                                 <option value="{{$branch->id}}" {!! old('purchased_branch', $complaints['purchased_branch'])==$branch->id ? 'selected':'' !!}>[{{$branch->branch_code}}] {{$branch->branch_name}}</option>
                                 @endforeach
                                 @endif
                              </select>
                              @if ($errors->has('purchased_branch'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('purchased_branch') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="bmd-label-floating">Product Group </label>
                              <input readonly type="text" name="product_group" class="form-control" value="{!! old( 'product_group', $complaints['product_group']) !!}">
                              @if ($errors->has('product_group'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('product_group') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="bmd-label-floating">Company Sale Bill NO </label>
                              <input type="text" name="company_sale_bill_no" id="company_sale_bill_no" class="form-control" value="{!! old( 'company_sale_bill_no', $complaints['company_sale_bill_no']) !!}">
                              @if ($errors->has('company_sale_bill_no'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('company_sale_bill_no') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="bmd-label-floating">Company Sale Bill Date </label>
                              <input type="text" name="company_sale_bill_date" id="company_sale_bill_date" class="form-control datepicker" value="{!! old( 'company_sale_bill_date', $complaints['company_sale_bill_date']) !!}">
                              @if ($errors->has('company_sale_bill_date'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('company_sale_bill_date') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                     </div>
                     <div class="row mt-3">
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="bmd-label-floating">Warranty / Customer Bill Date </label>
                              <input type="text" name="customer_bill_date" id="customer_bill_date" class="form-control datepicker" value="{!! old( 'customer_bill_date', $complaints['customer_bill_date']) !!}">
                              @if ($errors->has('customer_bill_date'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('customer_bill_date') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="bmd-label-floating">Customer Bill No.</label>
                              <input type="text" name="customer_bill_no" id="customer_bill_no" class="form-control" value="{!! old( 'customer_bill_no', $complaints['customer_bill_no']) !!}">
                              @if ($errors->has('customer_bill_no'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('customer_bill_no') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="bmd-label-floating">Company Bill Date Month </label>
                              <input readonly type="text" name="company_bill_date_month" id="company_bill_date_month" class="form-control" value="{!! old( 'company_bill_date_month', $complaints['company_bill_date_month']) !!}">
                              @if ($errors->has('company_bill_date_month'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('company_bill_date_month') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="bmd-label-floating">Under Warranty </label>
                              <select name="under_warranty" id="under_warranty" class="select2 form-control">
                                 <option value="">Under Warranty</option>
                                 <option value="Yes" {!! old('under_warranty', $complaints['under_warranty'])=='Yes' ? 'selected' :'' !!}>Yes</option>
                                 <option value="No" {!! old('under_warranty', $complaints['under_warranty'])=='No' ? 'selected' :'' !!}>No</option>
                              </select>
                              @if ($errors->has('under_warranty'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('under_warranty') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                     </div>
                     <div class="row mt-3">
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="bmd-label-floating">Service Paid/Free </label>
                              <select name="service_type" id="service_type" class="select2 form-control">
                                 <option value="">Service Paid/Free</option>
                                 <option value="Paid" {!! old('service_type', $complaints['service_type'])=='Paid' ? 'selected' :'' !!}>Paid</option>
                                 <option value="Free" {!! old('service_type', $complaints['service_type'])=='Free' ? 'selected' :'' !!}>Free</option>
                                 <option value="later_update" {!! old('service_type', $complaints['service_type'])=='later_update' ? 'selected' :'' !!}>F&A Later Update</option>
                              </select>
                              @if ($errors->has('service_type'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('service_type') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="bmd-label-floating">Customer Bill Date Month </label>
                              <input readonly type="text" name="customer_bill_date_month" id="customer_bill_date_month" class="form-control" value="{!! old( 'customer_bill_date_month', $complaints['customer_bill_date_month']) !!}">
                              @if ($errors->has('customer_bill_date_month'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('customer_bill_date_month') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="bmd-label-floating">Warranty/Bill </label>
                              <select name="warranty_bill" id="warranty_bill" class="select2 form-control">
                                 <option value="">Warranty/Bill</option>
                                 <option value="Yes" {!! old('warranty_bill', $complaints['warranty_bill'])=='Yes' ? 'selected' :'' !!}>Yes</option>
                                 <option value="No" {!! old('warranty_bill', $complaints['warranty_bill'])=='No' ? 'selected' :'' !!}>No</option>
                              </select>
                              @if ($errors->has('warranty_bill'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('warranty_bill') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        <!-- <div class="col-md-3">
                        <div class="form-group">
                           <label class="bmd-label-floating">Assign User </label>
                           <select name="fault_type" id="fault_type" class="select2 form-control">
                              <option value="">Fault Type</option>
                              <option value="Site" {!! old('fault_type', $complaints['fault_type'])=='Site' ? 'selected' :'' !!}>Site</option>
                              <option value="Company" {!! old('fault_type', $complaints['fault_type'])=='Company' ? 'selected' :'' !!}>Company</option>
                           </select>
                           @if ($errors->has('fault_type'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('fault_type') }}</p>
                           </div>
                           @endif
                        </div>
                     </div> -->
                     </div>
                     <!-- <div class="row mt-3">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="bmd-label-floating">Service Centre Remarks </label>
                           <textarea type="text" name="service_centre_remark" cols="20" rows="3" class="form-control"> {!! old( 'service_centre_remark', $complaints['service_centre_remark']) !!} </textarea>
                           @if ($errors->has('service_centre_remark'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('service_centre_remark') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="bmd-label-floating">Remarks </label>
                           <textarea type="text" name="remark" cols="20" rows="3" class="form-control"> {!! old( 'remark', $complaints['remark']) !!} </textarea>
                           @if ($errors->has('remark'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('remark') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                  </div> -->
                  </div>
               </div>
               <div class="contact_details mt-1">
                  <h3><b>Contact Details :</b></h3>
                  <div class="border border-dark rounded p-4">
                     <div class="row mt-3">
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="bmd-label-floating">Customer Number Search</label>
                              <input type="number" name="customer_number" id="customer_number" class="form-control" value="{!! old( 'customer_number', $complaints['customer']?$complaints['customer']['customer_number']:'') !!}" required>
                              <input type="hidden" name="end_user_id" id="end_user_id">
                              @if ($errors->has('customer_number'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('customer_number') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>

                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="bmd-label-floating"> Customer Name </label>
                              <input type="text" readonly name="customer_name" id="customer_name" class="form-control" value="{!! old( 'customer_name', $complaints['customer_name']) !!}">
                              @if ($errors->has('customer_name'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('customer_name') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>

                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="bmd-label-floating">Email </label>
                              <input type="text" readonly name="customer_email" id="customer_email" class="form-control" value="{!! old( 'customer_email', $complaints['customer_email']) !!}">
                              @if ($errors->has('customer_email'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('customer_email') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="bmd-label-floating">Address </label>
                              <input type="text" name="customer_address" id="customer_address" class="form-control" value="{!! old( 'customer_address', $complaints['customer_address']) !!}">
                              @if ($errors->has('customer_address'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('customer_address') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                     </div>
                     <div class="row mt-3">
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="bmd-label-floating">Place </label>
                              <input type="text" name="customer_place" id="customer_place" class="form-control" value="{!! old( 'customer_place', $complaints['customer_place']) !!}">
                              @if ($errors->has('customer_place'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('customer_place') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="bmd-label-floating">Pincode </label>
                              <select name="customer_pindcode" id="customer_pindcode" placeholder="Select Pincode" class="select2 form-control" required>
                                 <option value="" disabled selected>Select Pincode</option>
                                 @if($pincodes && count($pincodes) > 0)
                                 @foreach($pincodes as $pincode)
                                 <option value="{{$pincode->id}}">{{$pincode->pincode}}</option>
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
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="bmd-label-floating">Country </label>
                              <input type="text" readonly name="customer_country" id="customer_country" class="form-control" value="{!! old( 'customer_country', $complaints['customer_country']) !!}">
                              @if ($errors->has('customer_country'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('customer_country') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="bmd-label-floating">State </label>
                              <input type="text" readonly name="customer_state" id="customer_state" class="form-control" value="{!! old( 'customer_state', $complaints['customer_state']) !!}">
                              @if ($errors->has('customer_state'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('customer_state') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                     </div>
                     <div class="row mt-3">
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="bmd-label-floating">District </label>
                              <input type="text" readonly name="customer_district" id="customer_district" class="form-control" value="{!! old( 'customer_district', $complaints['customer_district']) !!}">
                              @if ($errors->has('customer_district'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('customer_district') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="bmd-label-floating">City </label>
                              <input type="text" readonly name="customer_city" id="customer_city" class="form-control" value="{!! old( 'customer_city', $complaints['customer_city']) !!}">
                              @if ($errors->has('customer_city'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('customer_city') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        <!-- <div class="col-md-3">
                        <div class="form-group">
                           <label class="bmd-label-floating">Pincode </label>
                           <select name="division" id="division" placeholder="Select Division" class="select2 form-control">
                              <option value="" disabled selected>Division</option>
                              @if($divisions && count($divisions) > 0)
                              @foreach($divisions as $division)
                              <option value="{{$division->id}}" {!! old( 'division' , $complaints['division'])==$division->id?'selected':'' !!} >{{$division->division_name}}</option>
                              @endforeach
                              @endif
                           </select>
                           @if ($errors->has('division'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('division') }}</p>
                           </div>
                           @endif
                        </div>
                     </div> -->
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="bmd-label-floating">Complaint Register By </label>
                              <select name="register_by" id="register_by" class="select2 form-control">
                                 <option value="">Complaint Register By</option>
                                 <option value="Dealer" {!! old( 'register_by' , $complaints['register_by'])=='Dealer' ?'selected':'' !!}>Dealer</option>
                                 <option value="Distributor" {!! old( 'register_by' , $complaints['register_by'])=='Distributor' ?'selected':'' !!}>Distributor</option>
                                 <option value="Retailer" {!! old( 'register_by' , $complaints['register_by'])=='Retailer' ?'selected':'' !!}>Retailer</option>
                                 <option value="Marketing Team" {!! old( 'register_by' , $complaints['register_by'])=='Marketing Team' ?'selected':'' !!}>Marketing Team</option>
                                 <option value="ASC" {!! old( 'register_by' , $complaints['register_by'])=='ASC' ?'selected':'' !!}>ASC</option>
                                 <option value="Service Enginer" {!! old( 'register_by' , $complaints['register_by'])=='Service Enginer' ?'selected':'' !!}>Service Enginer</option>
                              </select>
                              @if ($errors->has('register_by'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('register_by') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                     </div>
                     <div class="row mt-3">
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="bmd-label-floating">Description / CRM Remark </label>
                              <textarea type="text" name="description" cols="30" rows="7" class="form-control"> {!! old( 'description', $complaints['description']) !!} </textarea>
                              @if ($errors->has('description'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('description') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="bmd-label-floating">Customer Complaint Type </label>
                              <select name="complaint_type" id="complaint_type" placeholder="Select Pincode" class="select2 form-control" required>
                                 <option value="" disabled selected>Complaint Type</option>
                                 @if($complaint_types && count($complaint_types) > 0)
                                 @foreach($complaint_types as $complaint_type)
                                 <option value="{{$complaint_type->id}}" {!! old( 'complaint_type' , $complaints['complaint_type'])==$complaint_type->id?'selected':'' !!}>{{$complaint_type->name}}</option>
                                 @endforeach
                                 @endif
                              </select>
                              @if ($errors->has('complaint_type'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('complaint_type') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                     </div>
                     <div class="row mt-3">
                        <div class="col-md-3 d-none" id="invoice-div">
                           <div class="">
                              <h6 class="bmd-label-floating">Invoice </h6>
                              <a href="" download target="_blank"><img class="ml-2 rounded" id="invoice-img" style="width: 200px;height:220px;border: 3px solid #5a5252;" src=""></a>
                           </div>
                        </div>
                        <div class="col-md-9">
                           <div class="">
                              <label class="bmd-label-floating">Attachments </label>
                              <input type="file" multiple name="images[]" class="form-controll">
                              <div class="row mt-3">
                                 @if($complaints->exists && $complaints->getMedia('complaint_attach')->count() > 0 && Storage::disk('s3')->exists($complaints->getMedia('complaint_attach')[0]->getPath()))
                                 @foreach($complaints->getMedia('complaint_attach') as $k=>$media)
                                 <div style="position: relative;" class="img-div">
                                    <button title="Delete Image" type="button" class="badge badge-danger delete-img-btn" data-mediaid="{{$media->id}}">X</button>
                                    <a href="{{$media->getFullUrl()}}" download target="_blank">
                                    @if($media->mime_type == 'application/pdf')
                                       <img class="m-2 rounded img-fluid" src="{{url('/public/assets/img/pdf-icon.jpg')}}" style="width: 170px;height:170px;">
                                       @else
                                       <img class="m-2 rounded img-fluid" src="{!! $media->getFullUrl() !!}" style="width: 170px;height:170px;">
                                       @endif
                                    </a>
                                 </div>
                                 @endforeach
                                 @endif
                              </div>
                              @if ($errors->has('description'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('description') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="card-footer pull-right mt-5">
                  {{ Form::submit('Submit', array('class' => 'btn btn-theme','id' => 'submit-btn')) }}
               </div>
               {{ Form::close() }}
            </div>
         </div>
      </div>
   </div>

   <script>
      setTimeout(() => {
         var $customerSelect = $('#party_name').select2({
            placeholder: 'Purchased Party Name ',
            allowClear: true,
            ajax: {
               url: "{{ route('getRetailerDataSelect') }}",
               dataType: 'json',
               delay: 250,
               data: function(params) {
                  return {
                     term: params.term || '',
                     page: params.page || 1
                  }
               },
               cache: true
            }
         });
      }, 1000);
      $(document).ready(function() {
         $("#serail_number").trigger("keyup");
         $('#company_sale_bill_date').datepicker({
            maxDate: 0,
            dateFormat: 'yy-mm-dd',
         });
         $('#customer_bill_date').datepicker({
            maxDate: 0,
            dateFormat: 'yy-mm-dd',
         });
         $('#complaint_date').datepicker({
            minDate: 0,
            maxDate: 0,
            dateFormat: 'yy-mm-dd',
         });
      });
      $("#company_sale_bill_date").on('change', function() {
         var selectedDate = moment($(this).val());
         var today = moment();
         var diffMonths = today.diff(selectedDate, 'months');
         selectedDate.add(diffMonths, 'months');
         var diffDays = today.diff(selectedDate, 'days');

         $("#company_bill_date_month").focus();
         $("#company_bill_date_month").val(diffMonths + " Month " + diffDays + " Day");
      });
      $("#customer_bill_date").on('change', function() {
         var selectedDate = moment($(this).val());
         var today = moment();
         var diffMonths = today.diff(selectedDate, 'months');
         selectedDate.add(diffMonths, 'months');
         var diffDays = today.diff(selectedDate, 'days');

         $("#customer_bill_date_month").focus();
         $("#customer_bill_date_month").val(diffMonths + " Month " + diffDays + " Day");
      });
      $("#product_serail_number").on("keyup", function() {
         var serial_no = $(this).val();
         var chekcPro = '{{($complaints && $complaints->product_id)?$complaints->product_id:""}}'
         $.ajax({
            url: "{{ url('getProductInfoBySerialNo') }}",
            dataType: "json",
            type: "POST",
            data: {
               _token: "{{csrf_token()}}",
               serial_no: serial_no
            },
            success: function(res) {
               if (res.status === true) {
                  $("#product_code").val(res.data.product_code);
                  $("#product_name").val(res.data.product_name);
                  $("#product_id").val(res.data.id);
                  $("#product_id").change();
                  $("#category").val(res.data.categories.category_name);
                  $("#specification").val(res.data.specification);
                  $("#product_no").val(res.data.product_no);
                  $("#phase").val(res.data.phase);
                  $("#product_code").prop('readonly', true);
                  $("#category").prop('readonly', true);
                  $("#specification").prop('readonly', true);
                  $("#product_no").prop('readonly', true);
                  $("#phase").prop('readonly', true);
                  $("#company_sale_bill_date").val(res.data_all.invoice_date);
                  $("#company_sale_bill_no").val(res.data_all.invoice_no);
                  $("#seller").val(res.data_all.party_name);
                  $("#seller").prop('readonly', true);
                  $("#company_sale_bill_date").change();
                  if (res.check_Warranty != null) {
                     $("#customer_bill_date").val(res.check_Warranty.sale_bill_date);
                     $("#customer_bill_no").val(res.check_Warranty.sale_bill_no);
                     $("#customer_number").val(res.check_Warranty.customer.customer_number);
                     $("#customer_number").keyup();
                     $("#customer_bill_date").change();
                     var warrantyDate = new Date(res.check_Warranty.warranty_date);
                     var today = new Date();
                     today.setHours(0, 0, 0, 0);

                     if(res.check_Warranty.seller_details && res.check_Warranty.seller_details != null){
                        console.log(res.check_Warranty.seller_details.id);
                        var newOption = new Option(res.check_Warranty.seller_details.name, res.check_Warranty.seller_details.id, false, false);
                        $('#party_name').append(newOption).trigger('change');
                        $("#party_name").option(res.check_Warranty.seller_details.id);
                        $("#party_name").trigger('change');
                     }else{
                        $("#party_name").val('');
                        $("#party_name").trigger('change');
                     }

                     if (warrantyDate > today) {
                        $("#under_warranty").val('Yes');
                        $("#under_warranty").change();
                     } else {
                        $("#under_warranty").val('No');
                        $("#under_warranty").change();
                     }

                     if (res.check_Warranty.media.length > 0) {
                        var attaExt = res.check_Warranty.media[0].original_url.split('.').pop().toLowerCase();
                        $("#invoice-div").removeClass('d-none');
                        $("#invoice-div a").prop('href', res.check_Warranty.media[0].original_url);
                        if(attaExt == 'pdf')
                        {
                           $("#invoice-img").attr('src', '{{url("/public/assets/img/pdf-icon.jpg")}}');
                        }else{
                           $("#invoice-img").attr('src', res.check_Warranty.media[0].original_url);
                        }
                     } else {
                        $("#invoice-div").addClass('d-none');
                     }
                     $("#submit-btn").prop("disabled", false);
                  } else {
                     var active_url = "{{ route('warranty_activation.create') }}?serial_no=" + serial_no;
                     Swal.fire({
                        title: "Warranty is not active of " + serial_no + " serial number. Please activate the warranty first.",
                        icon: "error",
                        showCancelButton: true,
                        confirmButtonText: '<a href="' + active_url + '" style="color: white; text-decoration: none;">Activate Warranty</a>',
                        cancelButtonText: "Cancel",
                        cancelButtonColor: '#d33',
                        confirmButtonColor: '#3085d6'
                     });

                     $("#submit-btn").prop("disabled", true);
                     $("#customer_bill_date").val(" ");
                     $("#customer_number").val(" ");
                  }
               } else {
                  $("#party_name").val('');
                  $("#party_name").change();
                  $("#company_sale_bill_date").val(" ");
                  $("#company_sale_bill_no").val(" ");
                  $("#product_code").val(" ");
                  $("#product_name").val(" ");
                  $("#product_id").val(" ");
                  $("#product_id").change();
                  $("#category").val(" ");
                  $("#specification").val(" ");
                  $("#product_no").val(" ");
                  $("#phase").val(" ");
                  $("#company_sale_bill_date").val(" ");
                  $("#company_sale_bill_no").val(" ");
                  $("#seller").val(" ");
                  $("#product_code").prop('readonly', false);
                  $("#product_name").prop('readonly', false);
                  $("#category").prop('readonly', false);
                  $("#specification").prop('readonly', false);
                  $("#product_no").prop('readonly', false);
                  $("#phase").prop('readonly', false);
                  $("#seller").prop('readonly', false);
                  $("#company_sale_bill_date").change();
                  if (chekcPro != "") {
                     $("#product_id").val(chekcPro);
                     $("#product_id").change();
                  }
               }
            }
         });
      }).trigger('keyup');

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
                  $("#customer_name").val(res.data.customer_name);
                  $("#end_user_id").val(res.data.id);
                  $("#customer_email").val(res.data.customer_email);
                  $("#customer_address").val(res.data.customer_address);
                  $("#customer_place").val(res.data.customer_place);
                  $("#customer_pindcode").val(res.data.customer_pindcode).trigger("change");;
                  $("#customer_state").val(res.data.customer_state);
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
      }).trigger('keyup');
      $(document).on("change", "#customer_pindcode", function() {
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
               $("#customer_state").val(res.state_name);
               $("#customer_district").val(res.district_name);
               $("#customer_city").val(res.city_name);
            }
         });
      });
      $("#go-search").on('click', function() {
         var search = $("#serail_number").val();
         if (!search || search == '' || search == null) {
            $("#serail_number").focus();
            $("#search_error").html("Please enter Contact Number or Searial Number");
            $("#search_error").removeClass("d-none");
         } else {
            $("#search_error").addClass("d-none");
            $("#search_data").removeClass("d-none");
            $.ajax({
               url: "{{ url('getComplaintsData') }}",
               dataType: "json",
               type: "POST",
               data: {
                  _token: "{{csrf_token()}}",
                  search: search
               },
               success: function(res) {
                  if (res.status === true) {
                     $("#search_data table tbody").html(res.data);
                  } else {
                     if (res.data) {
                        $("#search_data table tbody").html(res.data);
                     }
                  }
               }
            });
         }
      });
      $(document).on("click", ".delete-img-btn", function() {
         var id = $(this).data('mediaid');
         Swal.fire({
            title: "ARE YOU SURE TO DELETE ATTACHMENT ?",
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: "YES",
            denyButtonText: `Don't`
         }).then((result) => {
            if (result.value) {
               $(this).closest('.img-div').remove();
               $.ajax({
                  url: "{{ url('complaint-attach-delete') }}",
                  dataType: "json",
                  type: "POST",
                  data: {
                     _token: "{{csrf_token()}}",
                     id: id
                  },
                  success: function(res) {
                     if (res.status === true) {
                        Swal.fire("Attachment delete successfully !", res.msg, "success");
                     } else {
                        Swal.fire("Somthing went wrong", "", "error");
                     }
                  }
               });
            }
         });
      })

      $("#serail_number").on("keyup", function(event) {
         $("#product_serail_number").val($(this).val());
         var key = event.key || event.which || event.keyCode;

         var ctrlKey = 'Control';
         var shiftKey = 'Shift';
         var altKey = 'Alt';

         if (typeof key === 'string') {
            if (key === ctrlKey || key === shiftKey || key === altKey || key === 'c' || key === 'a') {
               return;
            }
         }
         if ($(this).val().length > 4) {
            $("#product_serail_number").keyup();
         }
      }).trigger();
      $("#product_id").on("change", function() {
         var product_id = $(this).val();
         if (product_id != null && product_id != '') {
            $.ajax({
               url: "{{ url('getProductInfo') }}",
               dataType: "json",
               type: "POST",
               data: {
                  _token: "{{csrf_token()}}",
                  product_id: product_id
               },
               success: function(res) {

                  $("#product_code").val(res.product_code);
                  $("#product_name").val(res.product_name);
                  $("#category").val(res.categories.category_name);
                  $("#specification").val(res.specification);
                  $("#product_no").val(res.product_no);
                  $("#phase").val(res.phase);
                  $("#product_code").prop('readonly', true);
                  $("#category").prop('readonly', true);
                  $("#specification").prop('readonly', true);
                  $("#product_no").prop('readonly', true);
                  $("#phase").prop('readonly', true);
               }
            });
         } else {
            $("#product_code").val("");
            $("#product_name").val("");
            $("#category").val("");
            $("#specification").val("");
            $("#product_no").val("");
            $("#phase").val("");
            $("#product_code").prop('readonly', false);
            $("#category").prop('readonly', false);
            $("#specification").prop('readonly', false);
            $("#product_no").prop('readonly', false);
            $("#phase").prop('readonly', false);
         }
      });
   </script>
</x-app-layout>