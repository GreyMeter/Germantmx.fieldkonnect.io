<x-app-layout>
<div class="row">
   <div class="col-md-12">
      <div class="card">
         <div class="card-header card-header-warning card-header-icon">
            <div class="row">
               <div class="col-6">
                  <div class="card-icon">
                     <i class="material-icons">category</i>
                  </div>
                  <h4 class="card-title"> {!! trans('panel.task.create_title') !!}</h4>
               </div>
               <div class="col-6 text-right">
                  <a href="{{ url('tasks') }}" class="btn btn-sm btn-info">{!! trans('panel.task.title') !!}</a>
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
            {!! Form::model($tasks,[
            'route' => $tasks->exists ? ['tasks.update', $tasks->id] : 'tasks.store',
            'method' => $tasks->exists ? 'PUT' : 'POST',
            'id' => 'storeTaskData',
            'files'=>true
            ]) !!}
            <input type="hidden" name="action" value="create">
            <div class="row">
               <div class="col-md-12">
                  <div class="row">
                     <label class="col-md-2 col-form-label">{!! trans('panel.task.task_title') !!} <span class="text-danger"> *</span></label>
                     <div class="col-md-10">
                        <div class="form-group has-default bmd-form-group">
                           <input type="text" name="title" id="title" class="form-control" value="{!! old( 'title', $tasks['title']) !!}" maxlength="200" required>
                           @if ($errors->has('title'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('title') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="row">
                     <label class="col-sm-4 col-form-label">{!! trans('panel.task.user_id') !!}<span class="text-danger"> *</span></label>
                     <div class="col-sm-8">
                        <div class="form-group bmd-form-group">
                           <select class="form-control select2" name="user_id" id="user_id" style="width: 100%;" required>
                              <option value="">Select {!! trans('panel.task.user_id') !!}</option>
                              @if(@isset($users ))
                              @foreach($users as $user)
                              <option value="{!! $user['id'] !!}" {{ old( 'user_id' , (!empty($tasks['user_id']))?($tasks['user_id']):('') ) == $user['id'] ? 'selected' : '' }}>{!! $user['id'].' '.$user['name'] !!}</option>
                              @endforeach
                              @endif
                           </select>
                        </div>
                        @if ($errors->has('users'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('users') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="row">
                     <label class="col-sm-4 col-form-label">Customer</label>
                     <div class="col-sm-8">
                        <div class="form-group bmd-form-group">
                           <select class="form-control select2" name="customer_id" id="customer_id" style="width: 100%;" required>
                              <option value="">Select Customer</option>
                              @if(@isset($customers ))
                              @foreach($customers as $customer)
                              <option value="{!! $customer['id'] !!}" {{ old( 'user_id' , (!empty($tasks['user_id']))?($tasks['user_id']):('') ) == $customer['id'] ? 'selected' : '' }}>{!! $customer['id'].' '.$customer['name'] !!}</option>
                              @endforeach
                              @endif
                           </select>
                        </div>
                        @if ($errors->has('customer_id'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('customer_id') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="row">
                     <label class="col-sm-4 col-form-label">Date Time<span class="text-danger"> *</span></label>
                     <div class="col-sm-8">
                        <div class="form-group bmd-form-group">
                           <input type="text" name="datetime" id="datetime" class="form-control datetimepicker" value="{!! old( 'datetime', $tasks['datetime']) !!}">
                        </div>
                        @if ($errors->has('datetime'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('datetime') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="row">
                     <label class="col-sm-4 col-form-label">Reminder</label>
                     <div class="col-sm-8">
                        <div class="form-group bmd-form-group">
                           <input type="text" name="reminder" id="reminder" class="form-control datetimepicker" value="{!! old( 'reminder', $tasks['reminder']) !!}">
                        </div>
                        @if ($errors->has('reminder'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('reminder') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
               </div>
            </div>
            <hr class="my-3">
            <h4 class="section-heading mb-3  h4 mt-0 text-center text-rose">{!! trans('panel.task.descriptions') !!}</h4>
            <div class="row">
               <div class="col-md-12">
                  <div class="form-group has-default bmd-form-group">
                     <textarea class="ckeditor form-control" name="descriptions" id="descriptions">{!! old( 'descriptions', $tasks['descriptions']) !!}</textarea>
                     @if ($errors->has('descriptions'))
                     <div class="error col-lg-12">
                        <p class="text-danger">{{ $errors->first('descriptions') }}</p>
                     </div>
                     @endif
                  </div>
               </div>
               <div class="pull-right">
                  {{ Form::submit('Submit', array('class' => 'btn btn-theme')) }}
               </div>
            </div>
         </div>
     
         {{ Form::close() }} 
      </div>
   </div>
</div>
</div>
<script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
<script src="{{ url('/').'/'.asset('assets/js/jquery.tasks.js') }}"></script>
</x-app-layout>