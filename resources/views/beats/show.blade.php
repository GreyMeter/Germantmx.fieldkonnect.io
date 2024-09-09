<x-app-layout>
<div class="card card-body mx-3 mx-md-4 mt-n6">
	<div class="row gx-4">
		<div class="col-auto my-auto">
			<div class="h-100">
				<h5 class="mb-1">{!! $beats['beat_name'] !!}</h5> </div>
		</div>
	</div>
	<div class="row">
		<div class="row mt-3">
			<div class="col-12 col-md-6 col-xl-4 mt-md-0 mt-4 position-relative">
				<div class="card card-plain h-100">
					<div class="card-header pb-0 p-3">
						<div class="row">
							<div class="col-md-8 d-flex align-items-center">
								<h6 class="mb-0">Beat Information</h6> </div>
							<div class="col-md-4 text-end">
								<a href="javascript:;"> <i class="fas fa-user-edit text-secondary text-sm" data-bs-toggle="tooltip" data-bs-placement="top" aria-hidden="true" aria-label="Edit Profile"></i><span class="sr-only">Edit Profile</span> </a>
							</div>
						</div>
					</div>
					<div class="card-body p-3">
						<p class="text-sm"> {!! $beats['description'] !!} </p>
						<hr class="horizontal gray-light my-4">
						<ul class="list-group"> @if($beats['statename']['state_name'])
							<li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">State:</strong> &nbsp; {!! $beats['statename']['state_name'] !!}</li> @endif @if($beats['districtname']['district_name'])
							<li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">District:</strong> &nbsp; {!! $beats['districtname']['district_name'] !!}</li>@endif @if($beats['cityname']['city_name'])
							<li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">City:</strong> &nbsp; {!! $beats['cityname']['city_name'] !!}</li> @endif @if($beats['createdbyname']['name'])
							<li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Created By:</strong> &nbsp; {!! $beats['createdbyname']['name'] !!}</li> @endif </ul>
						<hr class="horizontal gray-light my-4">
						<h6 class="text-uppercase text-body text-xs font-weight-bolder">Users</h6>
						<ul class="list-group"> @if($beats->exists && isset($beats['beatusers'])) @foreach($beats['beatusers'] as $key => $user )
							<li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">{!! $user['users']['name'] !!}</strong> &nbsp; </li> @endforeach @endif </ul>
					</div>
				</div>
				<hr class="vertical dark"> </div>
			<div class="col-12 col-md-6 col-xl-4 position-relative">
				<div class="card card-plain h-100">
					<div class="card-header pb-0 p-3">
						<h6 class="mb-0">Customers</h6> </div>
					<div class="card-body p-3">
						<ul class="list-group"> 
              @if($customers) 
              @foreach($customers as $key => $customer )
							<li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">{!! $customer['customers']['name'] !!}</strong> &nbsp;</li> 
              @endforeach 
              @endif 
            </ul>
					</div>
				</div>
				<hr class="vertical dark"> </div>
			<div class="col-12 col-xl-4 mt-xl-0 mt-4">
				<div class="card card-plain h-100">
					<div class="card-header pb-0 p-3">
						<h6 class="mb-0">Beat Scheduled</h6> </div>
					<div class="card-body p-3">
						<ul class="list-group"> @if($beats->exists && isset($schedules)) @foreach($schedules as $key => $schedule )
							<li class="list-group-item border-0 d-flex align-items-center px-0 mb-2 pt-0">
								<div class="avatar me-3"> @if($schedule['users']['profile_image'])<img src="{!! $schedule['users']['profile_image'] !!}" alt="kal" class="rounded-circle" width="50px"> @endif </div>
								<div class="d-flex align-items-start flex-column justify-content-center">
									<h6 class="mb-0 text-sm">{!! $schedule['users']['name'] !!}</h6>
									<p class="mb-0 text-xs">{!! $schedule['users']['mobile'] !!}</p>
								</div>
								<p class="btn btn-link pe-3 ps-0 mb-0 ms-auto w-25 w-md-auto" href="javascript:;">{!! $schedule['beat_date']!!}</p>
							</li> @endforeach @endif </ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div> 
</x-app-layout>