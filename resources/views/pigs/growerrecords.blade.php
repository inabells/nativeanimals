@extends('layouts.swinedefault')

@section('title')
	Grower Records
@endsection

@section('content')
	<div class="container">
		<h4>Grower Records <a class="tooltipped" data-position="right" data-tooltip="All pigs except breeders"><i class="material-icons">info_outline</i></a></h4>
		<div class="divider"></div>
		<div class="row" style="padding-top: 10px;">
			{!! Form::open(['route' => 'farm.pig.search_growers', 'method' => 'post', 'role' => 'search']) !!}
      {{ csrf_field() }}
      <div class="input-field col s12">
        <input type="text" name="q" placeholder="Search grower" class="col s9">
        <button type="submit" class="btn green darken-4">Search <i class="material-icons right">search</i></button>
      </div>
      {!! Form::close() !!}
      @if(isset($details))
        <div class="row">
          <div class="col s12">
            <h5 class="center">Search results for <strong>{{ $query }}</strong>:</h5>
            <table class="centered">
							<thead class="green lighten-1">
								<tr>
									<th>Registration ID</th>
									<th>Weight Record</th>
									<th>Average Daily Gain</th>
									<th>Add as Candidate Breeder</th>
									<th>Add as Breeder</th>
								</tr>
							</thead>
							<tbody>
								@foreach($details as $grower)
									<tr id="{{ $grower->registryid }}">
										<td>{{ $grower->registryid }}</td>
										@if($grower->weightrecord == 0)
		                  <td>
		                    <a href="{{ URL::route('farm.pig.weight_records_page', [$grower->id]) }}" class="tooltipped" data-position="top" data-tooltip="Add"><i class="material-icons">add_circle_outline</i></a>
		                  </td>
		                @elseif($grower->weightrecord == 1)
		                  <td>
		                    <a href="{{ URL::route('farm.pig.edit_weight_records_page', [$grower->id]) }}" class="tooltipped" data-position="top" data-tooltip="Edit"><i class="material-icons">edit</i></a>
		                  </td>
		                @endif
		                <td><a href="{{ URL::route('farm.pig.view_adg', [$grower->id]) }}" class="tooltipped" data-position="top" data-tooltip="View ADG"><i class="material-icons">insert_chart_outlined</i></a></td>
		                @if(is_null($grower->getAnimalProperties()->where("property_id", 60)->first()))
			                <td>
			                	<div class="switch">
			                		<label>
			                			<input type="checkbox" class="sow_make_candidate_breeder" value="{{ $grower->registryid }}" />
			                			<span class="lever"></span>
			                		</label>
			                	</div>
			                </td>
			              @else
			              	@if($grower->getAnimalProperties()->where("property_id", 60)->first()->value == 1)
			              		<td>
				                	<div class="switch">
				                		<label>
				                			<input checked type="checkbox" class="sow_make_candidate_breeder" value="{{ $grower->registryid }}" />
				                			<span class="lever"></span>
				                		</label>
				                	</div>
				                </td>
			              	@elseif($grower->getAnimalProperties()->where("property_id", 60)->first()->value == 0)
			              		<td>
				                	<div class="switch">
				                		<label>
				                			<input type="checkbox" class="sow_make_candidate_breeder" value="{{ $grower->registryid }}" />
				                			<span class="lever"></span>
				                		</label>
				                	</div>
				                </td>
			              	@endif
			              @endif
										<td>
											@if((!is_null($grower->getAnimalProperties()->where("property_id", 35)->first()) && $grower->getAnimalProperties()->where("property_id", 35)->first()->value != "") || (!is_null($grower->getAnimalProperties()->where("property_id", 36)->first()) && $grower->getAnimalProperties()->where("property_id", 36)->first()->value != ""))
												<p>
										      <label>
										        <input type="checkbox" class="filled-in add_sow_breeder" value="{{ $grower->registryid }}" />
										        <span></span>
										      </label>
										    </p>
										  @else
										  	<p>
										      <label>
										        <input disabled type="checkbox" class="filled-in add_sow_breeder" value="{{ $grower->registryid }}" />
										        <span></span>
										      </label>
										    </p>
										  @endif
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
          </div>
        </div>
      @elseif(isset($message))
        <h5 class="center">{{ $message }}</h5>
      @endif
			<div class="col s12">
        <ul class="tabs tabs-fixed-width green lighten-1">
          <li class="tab"><a href="#femalegrowersview">Female Growers</a></li>
          <li class="tab"><a href="#malegrowersview">Male Growers</a></li>
        </ul>
      </div>
      <div id="femalegrowersview" class="col s12" style="padding-top: 10px;">
				<table class="centered">
					<thead class="green lighten-1">
						<tr>
							<th>Registration ID</th>
							<th>Weight Record</th>
							<th>Average Daily Gain</th>
							<th>Add as Candidate Breeder</th>
							<th>Add as Breeder</th>
						</tr>
					</thead>
					<tbody>
						@forelse($sows as $sow)
							<tr id="{{ $sow->registryid }}">
								<td>{{ $sow->registryid }}</td>
								@if($sow->weightrecord == 0)
                  <td>
                    <a href="{{ URL::route('farm.pig.weight_records_page', [$sow->id]) }}" class="tooltipped" data-position="top" data-tooltip="Add"><i class="material-icons">add_circle_outline</i></a>
                  </td>
                @elseif($sow->weightrecord == 1)
                  <td>
                    <a href="{{ URL::route('farm.pig.edit_weight_records_page', [$sow->id]) }}" class="tooltipped" data-position="top" data-tooltip="Edit"><i class="material-icons">edit</i></a>
                  </td>
                @endif
                <td><a href="{{ URL::route('farm.pig.view_adg', [$sow->id]) }}" class="tooltipped" data-position="top" data-tooltip="View ADG"><i class="material-icons">insert_chart_outlined</i></a></td>
                @if(is_null($sow->getAnimalProperties()->where("property_id", 60)->first()))
	                <td>
	                	<div class="switch">
	                		<label>
	                			<input type="checkbox" class="sow_make_candidate_breeder" value="{{ $sow->registryid }}" />
	                			<span class="lever"></span>
	                		</label>
	                	</div>
	                </td>
	              @else
	              	@if($sow->getAnimalProperties()->where("property_id", 60)->first()->value == 1)
	              		<td>
		                	<div class="switch">
		                		<label>
		                			<input checked type="checkbox" class="sow_make_candidate_breeder" value="{{ $sow->registryid }}" />
		                			<span class="lever"></span>
		                		</label>
		                	</div>
		                </td>
	              	@elseif($sow->getAnimalProperties()->where("property_id", 60)->first()->value == 0)
	              		<td>
		                	<div class="switch">
		                		<label>
		                			<input type="checkbox" class="sow_make_candidate_breeder" value="{{ $sow->registryid }}" />
		                			<span class="lever"></span>
		                		</label>
		                	</div>
		                </td>
	              	@endif
	              @endif
								<td>
									@if((!is_null($sow->getAnimalProperties()->where("property_id", 35)->first()) && $sow->getAnimalProperties()->where("property_id", 35)->first()->value != "") || (!is_null($sow->getAnimalProperties()->where("property_id", 36)->first()) && $sow->getAnimalProperties()->where("property_id", 36)->first()->value != ""))
										<p>
								      <label>
								        <input type="checkbox" class="filled-in add_sow_breeder" value="{{ $sow->registryid }}" />
								        <span></span>
								      </label>
								    </p>
								  @else
								  	<p>
								      <label>
								        <input disabled type="checkbox" class="filled-in add_sow_breeder" value="{{ $sow->registryid }}" />
								        <span></span>
								      </label>
								    </p>
								  @endif
								</td>
							</tr>
						@empty
              <tr>
                <td colspan="5">No female grower data found</td>
              </tr>
            @endforelse
					</tbody>
				</table>
			</div>
			<div id="malegrowersview" class="col s12" style="padding-top: 10px;">
				<table class="centered">
					<thead class="green lighten-1">
						<tr>
							<th>Registration ID</th>
							<th>Weight Record</th>
							<th>Average Daily Gain</th>
							<th>Add as Candidate Breeder</th>
							<th>Add as Breeder</th>
						</tr>
					</thead>
					<tbody>
						@forelse($boars as $boar)
							<tr id="{{ $boar->registryid }}">
								<td>{{ $boar->registryid }}</td>
								@if($boar->weightrecord == 0)
                  <td>
                    <a href="{{ URL::route('farm.pig.weight_records_page', [$boar->id]) }}" class="tooltipped" data-position="top" data-tooltip="Add"><i class="material-icons">add_circle_outline</i></a>
                  </td>
                @elseif($boar->weightrecord == 1)
                  <td>
                    <a href="{{ URL::route('farm.pig.edit_weight_records_page', [$boar->id]) }}" class="tooltipped" data-position="top" data-tooltip="Edit"><i class="material-icons">edit</i></a>
                  </td>
                @endif
                <td><a href="{{ URL::route('farm.pig.view_adg', [$boar->id]) }}" class="tooltipped" data-position="top" data-tooltip="View ADG"><i class="material-icons">insert_chart_outlined</i></a></td>
               	@if(is_null($boar->getAnimalProperties()->where("property_id", 60)->first()))
	                <td>
	                	<div class="switch">
	                		<label>
	                			<input type="checkbox" class="boar_make_candidate_breeder" value="{{ $boar->registryid }}" />
	                			<span class="lever"></span>
	                		</label>
	                	</div>
	                </td>
	              @else
	              	@if($boar->getAnimalProperties()->where("property_id", 60)->first()->value == 1)
	              		<td>
		                	<div class="switch">
		                		<label>
		                			<input checked type="checkbox" class="boar_make_candidate_breeder" value="{{ $boar->registryid }}" />
		                			<span class="lever"></span>
		                		</label>
		                	</div>
		                </td>
	              	@elseif($boar->getAnimalProperties()->where("property_id", 60)->first()->value == 0)
	              		<td>
		                	<div class="switch">
		                		<label>
		                			<input type="checkbox" class="boar_make_candidate_breeder" value="{{ $boar->registryid }}" />
		                			<span class="lever"></span>
		                		</label>
		                	</div>
		                </td>
	              	@endif
	              @endif
								<td>
									@if((!is_null($boar->getAnimalProperties()->where("property_id", 35)->first()) && $boar->getAnimalProperties()->where("property_id", 35)->first()->value != "") || (!is_null($boar->getAnimalProperties()->where("property_id", 36)->first()) && $boar->getAnimalProperties()->where("property_id", 36)->first()->value != ""))
										<p>
								      <label>
								        <input type="checkbox" class="filled-in add_boar_breeder" value="{{ $boar->registryid }}" />
								        <span></span>
								      </label>
								    </p>
								  @else
								  	<p>
								      <label>
								        <input disabled type="checkbox" class="filled-in add_boar_breeder" value="{{ $boar->registryid }}" />
								        <span></span>
								      </label>
								    </p>
								  @endif
								</td>
							</tr>
						@empty
              <tr>
                <td colspan="5">No male grower data found</td>
              </tr>
            @endforelse
					</tbody>
				</table>
			</div>
		</div>
	</div>
@endsection

@section('scripts')
	<script>
		$('.datepicker').pickadate({
		  selectMonths: true, // Creates a dropdown to control month
		  selectYears: 15, // Creates a dropdown of 15 years to control year,
		  today: 'Today',
		  clear: 'Clear',
		  close: 'Ok',
		  closeOnSelect: false, // Close upon selecting a date,
		  format: 'yyyy-mm-dd', 
		  max: new Date()
		});
		$(document).ready(function(){
		  $(".add_sow_breeder").change(function () {
		    if($(this).is(":checked")){
					event.preventDefault();
					var breederid = $(this).val();
					$.ajax({
						headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						},
						url: '../farm/fetch_breeders/'+breederid,
						type: 'POST',
						cache: false,
						data: {breederid},
						success: function(data)
						{
							Materialize.toast(breederid+' added as breeder!', 4000);
							$("#"+breederid).remove();
						}
					});
			  }
		  });
		  $(".add_boar_breeder").change(function () {
		    if($(this).is(":checked")){
					event.preventDefault();
					var breederid = $(this).val();
					$.ajax({
						headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						},
						url: '../farm/fetch_breeders/'+breederid,
						type: 'POST',
						cache: false,
						data: {breederid},
						success: function(data)
						{
							Materialize.toast(breederid+' added as breeder!', 4000);
							$("#"+breederid).remove();
						}
					});
			  }
		  });
		  $(".sow_make_candidate_breeder").change(function () {
		  	if($(this).is(":checked")){
		  		event.preventDefault();
		  		var growerid = $(this).val();
		  		var status = 1;
		  		$.ajax({
		  			headers: {
		  				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		  			},
		  			url: '../farm/make_candidate_breeder/'+growerid+'/'+status,
		  			type: 'POST',
		  			cache: false,
		  			data: {growerid, status},
		  			success: function(data)
		  			{
		  				Materialize.toast(growerid+' added as candidate breeder!', 4000);
		  			}
		  		});
		  	}
		  	if(!$(this).is(":checked")){
		  		event.preventDefault();
		  		var growerid = $(this).val();
		  		var status = 0;
		  		$.ajax({
		  			headers: {
		  				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		  			},
		  			url: '../farm/make_candidate_breeder/'+growerid+'/'+status,
		  			type: 'POST',
		  			cache: false,
		  			data: {growerid, status},
		  			success: function(data)
		  			{
		  				Materialize.toast(growerid+' removed as candidate breeder!', 4000);
		  			}
		  		});
		  	}
		  });
		  $(".boar_make_candidate_breeder").change(function () {
		  	if($(this).is(":checked")){
		  		event.preventDefault();
		  		var growerid = $(this).val();
		  		var status = 1;
		  		$.ajax({
		  			headers: {
		  				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		  			},
		  			url: '../farm/make_candidate_breeder/'+growerid+'/'+status,
		  			type: 'POST',
		  			cache: false,
		  			data: {growerid, status},
		  			success: function(data)
		  			{
		  				Materialize.toast(growerid+' added as candidate breeder!', 4000);
		  			}
		  		});
		  	}
		  	if(!$(this).is(":checked")){
		  		event.preventDefault();
		  		var growerid = $(this).val();
		  		var status = 0;
		  		$.ajax({
		  			headers: {
		  				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		  			},
		  			url: '../farm/make_candidate_breeder/'+growerid+'/'+status,
		  			type: 'POST',
		  			cache: false,
		  			data: {growerid, status},
		  			success: function(data)
		  			{
		  				Materialize.toast(growerid+' removed as candidate breeder!', 4000);
		  			}
		  		});
		  	}
		  });
		});
	</script>
@endsection