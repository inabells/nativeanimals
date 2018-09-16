@extends('layouts.swinedefault')

@section('title')
	Sow and Litter Record
@endsection

@section('content')
	<div class="container">
		{{-- <h4><a href="{{route('farm.pig.breeding_record')}}"><img src="{{asset('images/back.png')}}" width="4.5%"></a> Sow-Litter Record</h4> --}}
		<h4><a href="{{$_SERVER['HTTP_REFERER']}}"><img src="{{asset('images/back.png')}}" width="4.5%"></a> Sow and Litter Record</h4>
		<div class="divider"></div>
		{!! Form::open(['route' => 'farm.pig.get_sowlitter_record', 'method' => 'post']) !!}
    <div class="row">
    	<input type="hidden" name="grouping_id" value="{{ $family->id }}">
			<div class="col s12">
				<div class="row center">
					<div class="col s12">
						<div class="col s6 center">
							<h5>Sow used: <strong>{{ $family->getMother()->registryid }}</strong></h5>
						</div>
						<div class="col s6 center">
							<h5>Boar used: <strong>{{ $family->getFather()->registryid }}</strong></h5>
						</div>
					</div>
				</div>
				<div class="col s12 card-panel">
					<div class="row">
						<div class="col s6" style="padding-top: 10px;">
							{{-- GROUP PROPERTIES --}}
							<div class="row">

							</div>
							<div class="row">

							</div>
							<div class="row">

							</div>
							<div class="row">
								
							</div>
							<div class="row">
								
							</div>
							<div class="row">
								
							</div>
							<div class="row">
								
							</div>
							<div class="row">
								
							</div>
							<div class="row">
								<div class="col s6">
									Date Bred
								</div>
								<div class="col s6">
									{{ Carbon\Carbon::parse($family->getGroupingProperties()->where("property_id", 42)->first()->value)->format('j F, Y') }}
								</div>
							</div>
							<div class="row">
								@if(is_null($family->getGroupingProperties()->where("property_id", 3)->first()))
									<div class="col s6">
										<p>Date Farrowed</p>
									</div>
									<div class="col s6">
										<input id="date_farrowed" type="text" name="date_farrowed" placeholder="Pick date" class="datepicker">
									</div>
								@else
									<div class="col s6">
										Date Farrowed
									</div>
									<div class="col s6">
										{{ Carbon\Carbon::parse($family->getGroupingProperties()->where("property_id", 3)->first()->value)->format('j F, Y') }}
										<input id="hidden_date" type="hidden" name="date_farrowed" value="{{ $family->getGroupingProperties()->where("property_id", 3)->first()->value }}">
									</div>
								@endif
							</div>
							<div class="row">
								@if(!is_null($family->getGroupingProperties()->where("property_id", 3)->first()))
									@if(is_null($family->getGroupingProperties()->where("property_id", 6)->first()))
										<div class="col s6">
											<p>Date Weaned</p>
										</div>
										<div class="col s6">
											
										</div>
									@else
										<div class="col s6">
											Date Weaned
										</div>
										<div class="col s6">
											{{ Carbon\Carbon::parse($family->getGroupingProperties()->where("property_id", 6)->first()->value)->format('j F, Y') }}
											<input id="hidden_weaned" type="hidden" name="date_weaned" value="{{ $family->getGroupingProperties()->where("property_id", 6)->first()->value }}">
										</div>
									@endif
								@else
									<div class="col s6">
										<p>Date Weaned</p>
									</div>
									<div class="col s6">
										
									</div>
								@endif
							</div>
						</div>
						<div class="col s6">
							{{-- COMPUTED VALUES --}}
							<div class="row">

							</div>
							<div class="row">
								<div class="col s8">
									<p>Parity</p>
								</div>
								<div class="col s4">
									@if(is_null($family->getGroupingProperties()->where("property_id", 3)->first()))
										@if(is_null($family->getGroupingProperties()->where("property_id", 48)->first()))
											<input id="paritytext" type="text" name="parity"> 
										@else
											<input id="paritytext" type="text" name="parity" value="{{ $family->getGroupingProperties()->where("property_id", 48)->first()->value }}">
										@endif
									@else
										@if(is_null($family->getGroupingProperties()->where("property_id", 48)->first()))
											<input id="paritytext" type="text" name="parity"> 
										@else
											<input id="paritytext" type="text" name="parity" value="{{ $family->getGroupingProperties()->where("property_id", 48)->first()->value }}">
										@endif
									@endif
								</div>
							</div>
							<div class="row">
								<div class="col s8">
									Total Littersize Born
								</div>
								<div class="col s4">
									
								</div>
							</div>
							<div class="row">
								<div class="col s8">
									Total Littersize Born Alive
									@if($family->members == 1)

									@endif
								</div>
								<div class="col s4">
									@if($family->members == 1)
										{{ count($family->getGroupingMembers()) }}
									@endif
								</div>
							</div>
							<div class="row">
								<div class="col s8">
									Number weaned
								</div>
								<div class="col s4">
									@if($family->members == 1)
										
									@endif
								</div>
							</div>
							<div class="row">
								<div class="col s8">
									Average birth weight
								</div>
								<div class="col s4">
									@if($family->members == 1)
										{{ round($aveBirthWeight, 4) }}
									@endif
								</div>
							</div>
							<div class="row">
								<div class="col s8">
									Number of males
								</div>
								<div class="col s4">
									@if($family->members == 1)
										
									@endif
								</div>
							</div>
							<div class="row">
								<div class="col s8">
									Number of females
								</div>
								<div class="col s4">
									@if($family->members == 1)
										
									@endif
								</div>
							</div>
							<div class="row">
								<div class="col s8">
									Sex ratio (Male to Female)
								</div>
								<div class="col s4">
									@if($family->members == 1)
										
									@endif
								</div>
							</div>
							<div class="row">
								<div class="col s8">
									Average weaning weight
								</div>
								<div class="col s4">
									@if($family->members == 1)
										
									@endif
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row center">
					<div class="col s6">
						@if(is_null($family->getGroupingProperties()->where("property_id", 3)->first()))
							@if(is_null($family->getGroupingProperties()->where("property_id", 45)->first()))
								Number Stillborn:
								<div class="input-field inline">
									<input id="number_stillborn" type="text" name="number_stillborn">
								</div>
							@else
								Number Stillborn:
								<div class="input-field inline">
									<input id="number_stillborn" type="text" name="number_stillborn" value="{{ $family->getGroupingProperties()->where("property_id", 45)->first()->value }}">
								</div>
							@endif
						@else
							@if(is_null($family->getGroupingProperties()->where("property_id", 45)->first()))
								Number Stillborn:
								<div class="input-field inline">
									<input id="number_stillborn" type="text" name="number_stillborn">
								</div>
							@else
								Number Stillborn:
								<div class="input-field inline">
									<input id="number_stillborn" type="text" name="number_stillborn" value="{{ $family->getGroupingProperties()->where("property_id", 45)->first()->value }}">
								</div>
							@endif
						@endif
					</div>
					<div class="col s6">
						@if(is_null($family->getGroupingProperties()->where("property_id", 3)->first()))
							@if(is_null($family->getGroupingProperties()->where("property_id", 46)->first()))
								Number Mummified:
								<div class="input-field inline">
									<input id="number_mummified" type="text" name="number_mummified">
								</div>
							@else
								Number Mummified:
								<div class="input-field inline">
									<input id="number_mummified" type="text" name="number_mummified" value="{{ $family->getGroupingProperties()->where("property_id", 46)->first()->value }}">
								</div>
							@endif
						@else
							@if(is_null($family->getGroupingProperties()->where("property_id", 46)->first()))
								Number Mummified:
								<div class="input-field inline">
									<input id="number_mummified" type="text" name="number_mummified">
								</div>
							@else
								Number Mummified:
								<div class="input-field inline">
									<input id="number_mummified" type="text" name="number_mummified" value="{{ $family->getGroupingProperties()->where("property_id", 46)->first()->value }}">
								</div>
							@endif
						@endif
					</div>
					<div class="col s12 center">
						@if(is_null($family->getGroupingProperties()->where("property_id", 47)->first()))
							<div class="input-field col s8 offset-s2">
			          <textarea id="abnomalities" name="abnomalities" class="materialize-textarea" placeholder="Enter values separated by commas"></textarea>
			          <label for="abnomalities">Abnormalities</label>
			        </div>
			      @else
			      	<div class="input-field col s8 offset-s2">
			          <textarea id="abnomalities" name="abnomalities" class="materialize-textarea" value="{{ $family->getGroupingProperties()->where("property_id", 47)->first()->value }}"></textarea>
			          <label for="abnomalities">Abnormalities</label>
			        </div>
			      @endif
					</div>
					<div class="col s12 center">
						@if(is_null($family->getGroupingProperties()->where("property_id", 54)->first()))
							<div class="switch">
								<label>
									Group Weighing
									<input id="weighing_options" checked type="checkbox">
									<span class="lever"></span>
									Individual Weighing
								</label>
							</div>
						@else
							@if($family->getGroupingProperties()->where("property_id", 54)->first()->value == 0)
								<div class="switch">
									<label>
										Group Weighing
										<input id="weighing_options" type="checkbox">
										<span class="lever"></span>
										Individual Weighing
									</label>
								</div>
							@elseif($family->getGroupingProperties()->where("property_id", 54)->first()->value == 1)
								<div class="switch">
									<label>
										Group Weighing
										<input id="weighing_options" checked type="checkbox">
										<span class="lever"></span>
										Individual Weighing
									</label>
								</div>
							@endif
						@endif
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			@if(is_null($family->getGroupingProperties()->where("property_id", 54)->first()))
				{{-- INDIVIDUAL WEIGHING IS DISPLAYED, DEFAULT --}}
				<div id="individual_weighing1" class="col s12" style="display: block;">
					<h5  class="green darken-3 white-text center">Individual Weighing</h5>
					<h5 class="green lighten-1 center">Add offspring</h5>
					<div class="col s4">
	          <input id="offspring_earnotch" type="text" name="offspring_earnotch" class="validate">
	          <label for="offspring_earnotch">Offspring Earnotch</label>
	          <input type="hidden" name="option" value="1">
					</div>
					<div class="col s4">
						<select id="select_sex" name="sex" class="browser-default">
							<option disabled selected>Choose sex</option>
							<option value="M">Male</option>
							<option value="F">Female</option>
						</select>
					</div>
					<div class="col s4">
						<input id="birth_weight" type="text" name="birth_weight">
						<label for="birth_weight">Birth Weight, kg</label>
					</div>
				</div>
				<div id="individual_weighing2" class="row center" style="display: block;">
					<button class="btn waves-effect waves-light green darken-3" type="submit" onclick="Materialize.toast('Successfully added!', 4000)">Add
	          <i class="material-icons right">add</i>
	        </button>
	        {!! Form::close() !!}
				</div>
				<div id="individual_weighing3" class="row" style="display: block;">
					<div class="col s12">
						<table class="centered striped">
							<thead>
								<tr class="green lighten-1">
									<th>Offspring ID</th>
									<th>Sex</th>
									<th>Birth weight, kg</th>
									<th>Weaning weight, kg</th>
								</tr>
							</thead>
							<tbody>
								@forelse($offsprings as $offspring)
									<tr>
										<td>{{ $offspring->getChild()->registryid }}</td>
										<td>{{ $offspring->getAnimalProperties()->where("property_id", 2)->first()->value }}</td>
										<td>{{ $offspring->getAnimalProperties()->where("property_id", 5)->first()->value }}</td>
										{!! Form::open(['route' => 'farm.pig.get_weaning_weights', 'method' => 'post']) !!}
										@if(is_null($family->getGroupingProperties()->where("property_id", 6)->first()))
											@if(is_null($offspring->getAnimalProperties()->where("property_id", 7)->first()))
												<td>
													<a class="btn waves-effect waves-light green darken-3 modal-trigger" href="#weaning_weight_modal{{$offspring->getChild()->id}}">
								            Add <i class="material-icons right">add</i>
								          </a>
												</td>
											@else
												<td>{{ $offspring->getAnimalProperties()->where("property_id", 7)->first()->value }}</td>
											@endif
										@else
											@if(is_null($offspring->getAnimalProperties()->where("property_id", 7)->first()))
												<td>
													<a class="btn waves-effect waves-light green darken-3 modal-trigger" href="#weaning_weight_modal{{$offspring->getChild()->id}}">
								            Add <i class="material-icons right">add</i>
								          </a>
												</td>
											@else
												<td>{{ $offspring->getAnimalProperties()->where("property_id", 7)->first()->value }}</td>
											@endif
										@endif
										{{-- MODAL STRUCTURE --}}
										<div id="weaning_weight_modal{{$offspring->getChild()->id}}" class="modal">
											<div class="modal-content">
												<h5 class="center">Weaning Record: <strong>{{ $offspring->getChild()->registryid }}</strong></h5>
												<input type="hidden" name="offspring_id" value="{{ $offspring->getChild()->registryid }}">
												<input type="hidden" name="family_id" value="{{ $family->id }}">
												<div class="row center">
													<div class="col s8 offset-s2 center">
														Date Weaned:
														@if(is_null($family->getGroupingProperties()->where("property_id", 6)->first()))
															<div class="input-field inline">
																<input id="date_weaned" type="text" name="date_weaned" placeholder="Pick date" class="datepicker">
															</div>
														@else
															<div class="input-field inline">
																<input id="date_weaned" type="text" name="date_weaned" class="datepicker" value="{{ $family->getGroupingProperties()->where("property_id", 6)->first()->value }}">
															</div>
														@endif
													</div>
													<div class="col s8 offset-s2 center">
														Weaning Weight, kg:
														<div class="input-field inline">
															<input id="weaning_weight" type="text" name="weaning_weight">
														</div>
													</div>
												</div>
											</div>
											<div class="row center">
												<button class="btn waves-effect waves-light green darken-3" type="submit">
							            Submit <i class="material-icons right">send</i>
							          </button>
											</div>
										</div>
										{!! Form::close() !!}
									</tr>
								@empty
									<tr>
										<td colspan="4">No offspring data found</td>
									</tr>
								@endforelse
							</tbody>
						</table>
					</div>
				</div>
				{{-- GROUP WEIGHING IS HIDDEN --}}
				{!! Form::open(['route' => 'farm.pig.add_sowlitter_record_group', 'method' => 'post']) !!}
				<div id="group_weighing" class="row center" style="display: none;">
					<h5 class="green darken-3 white-text">Group Weighing</h5>
					<input type="hidden" name="grouping_id" value="{{ $family->id }}">
					@if(!is_null($family->getGroupingProperties()->where("property_id", 3)->first()))
						<input id="hidden_date" type="hidden" name="date_farrowed" value="{{ $family->getGroupingProperties()->where("property_id", 3)->first()->value }}">
					@endif
					@if(!is_null($family->getGroupingProperties()->where("property_id", 48)->first()))
						<input id="parity" type="hidden" name="parity" value="{{ $family->getGroupingProperties()->where("property_id", 48)->first()->value }}">
					@endif
					@if(!is_null($family->getGroupingProperties()->where("property_id", 45)->first()))
						<input id="number_stillborn" type="hidden" name="number_stillborn" value="{{ $family->getGroupingProperties()->where("property_id", 45)->first()->value }}">
					@endif
					@if(!is_null($family->getGroupingProperties()->where("property_id", 46)->first()))
						<input id="number_mummified" type="hidden" name="number_mummified" value="{{ $family->getGroupingProperties()->where("property_id", 46)->first()->value }}">
					@endif
					@if(!is_null($family->getGroupingProperties()->where("property_id", 47)->first()))
						<input type="hidden" name="abnomalities" value="{{ $family->getGroupingProperties()->where("property_id", 47)->first()->value }}">
					@endif
					<div class="row">
						<div class="col s4 offset-s2">
							Litter Birth Weight, kg
							@if(!is_null($family->getGroupingProperties()->where("property_id", 55)->first()))
								<div class="input-field inline">
									<input id="litter_birth_weight" type="text" name="litter_birth_weight" value="{{ $family->getGroupingProperties()->where("property_id", 55)->first()->value }}">
								</div>
							@else
								<div class="input-field inline">
									<input id="litter_birth_weight" type="text" name="litter_birth_weight">
								</div>
							@endif
						</div>
						<div class="col s4">
							Litter-size Born Alive
							@if(!is_null($family->getGroupingProperties()->where("property_id", 50)->first()))
								<div class="input-field inline">
									<input id="lsba" type="text" name="lsba" value="{{ $family->getGroupingProperties()->where("property_id", 50)->first()->value }}">
								</div>
							@else
								<div class="input-field inline">
									<input id="lsba" type="text" name="lsba">
								</div>
							@endif
						</div>
					</div>
					<h5 class="green lighten-1">Add offspring</h5>
					<div class="row">
						<div class="col s4 push-s2">
	            <input id="offspring_earnotch" type="text" name="offspring_earnotch" class="validate">
	            <label for="offspring_earnotch">Offspring Earnotch</label>
	            <input type="hidden" name="option" value="0">
						</div>
						<div class="col s4 push-s2">
							<select id="select_sex" name="sex" class="browser-default">
								<option disabled selected>Choose sex</option>
								<option value="M">Male</option>
								<option value="F">Female</option>
							</select>
						</div>
					</div>
					<div class="row center">
						<button class="btn waves-effect waves-light green darken-3" type="submit" onclick="Materialize.toast('Successfully added!', 4000)">Add
	            <i class="material-icons right">add</i>
	          </button>
	          {!! Form::close() !!}
					</div>
					<div class="row center">
						<div class="col s12">
							<table class="centered striped">
								<thead>
									<tr class="green lighten-1">
										<th>Offspring ID</th>
										<th>Sex</th>
										<th>Birth weight, kg</th>
										<th>Weaning weight, kg</th>
									</tr>
								</thead>
								<tbody>
									@forelse($offsprings as $offspring)
										{!! Form::open(['route' => 'farm.pig.edit_temporary_registryid', 'method' => 'post']) !!}
										<tr>
											<td>
												{{ $offspring->getChild()->registryid }} <a href="#edit_id{{$offspring->getChild()->id}}" class="modal-trigger"><i class="material-icons right">edit</i></a>
											</td>
											{{-- MODAL STRUCTURE --}}
											{{-- <div id="edit_id{{$offspring->getChild()->id}}" class="modal">
												<div class="modal-content">
													<h5 class="center">Edit Temporary Earnotch:<br><strong>{{ $offspring->getChild()->registryid }}</strong></h5>
													<input type="hidden" name="old_earnotch" value="{{ $offspring->getChild()->id }}">
													<div class="row center">
														<div class="input-field col s8 offset-s2">
															<input id="new_earnotch" type="text" name="new_earnotch" class="valideate">
															<label for="new_earnotch">New Earnotch</label>
														</div>
													</div>
												</div>
												<div class="row center">
													<button class="btn waves-effect waves-light green darken-3" type="submit">
								            Submit <i class="material-icons right">send</i>
								          </button>
												</div>
											</div> --}}
											{!! Form::close() !!}
											<td>{{ $offspring->getAnimalProperties()->where("property_id", 2)->first()->value }}</td>
											<td>{{ round($offspring->getAnimalProperties()->where("property_id", 5)->first()->value, 4) }}</td>
											{!! Form::open(['route' => 'farm.pig.get_weaning_weights', 'method' => 'post']) !!}
											@if(is_null($family->getGroupingProperties()->where("property_id", 6)->first()))
												@if(is_null($offspring->getAnimalProperties()->where("property_id", 7)->first()))
													<td>
														<a class="btn waves-effect waves-light green darken-3 modal-trigger" href="#weaning_weight_modal{{$offspring->getChild()->id}}">
									            Add <i class="material-icons right">add</i>
									          </a>
													</td>
												@else
													<td>{{ $offspring->getAnimalProperties()->where("property_id", 7)->first()->value }}</td>
												@endif
											@else
												@if(is_null($offspring->getAnimalProperties()->where("property_id", 7)->first()))
													<td>
														<a class="btn waves-effect waves-light green darken-3 modal-trigger" href="#weaning_weight_modal{{$offspring->getChild()->id}}">
									            Add <i class="material-icons right">add</i>
									          </a>
													</td>
												@else
													<td>{{ $offspring->getAnimalProperties()->where("property_id", 7)->first()->value }}</td>
												@endif
											@endif
											{{-- MODAL STRUCTURE --}}
											{{-- <div id="weaning_weight_modal{{$offspring->getChild()->id}}" class="modal">
												<div class="modal-content">
													<h5 class="center">Weaning Record: <strong>{{ $offspring->getChild()->registryid }}</strong></h5>
													<input type="hidden" name="offspring_id" value="{{ $offspring->getChild()->registryid }}">
													<input type="hidden" name="family_id" value="{{ $family->id }}">
													<div class="row center">
														<div class="col s8 offset-s2 center">
															Date Weaned:
															@if(is_null($family->getGroupingProperties()->where("property_id", 6)->first()))
																<div class="input-field inline">
																	<input id="date_weaned" type="text" name="date_weaned" placeholder="Pick date" class="datepicker">
																</div>
															@else
																<div class="input-field inline">
																	<input id="date_weaned" type="text" name="date_weaned" class="datepicker" value="{{ $family->getGroupingProperties()->where("property_id", 6)->first()->value }}">
																</div>
															@endif
														</div>
														<div class="col s8 offset-s2 center">
															Weaning Weight, kg:
															<div class="input-field inline">
																<input id="weaning_weight" type="text" name="weaning_weight">
															</div>
														</div>
													</div>
												</div>
												<div class="row center">
													<button class="btn waves-effect waves-light green darken-3" type="submit">
								            Submit <i class="material-icons right">send</i>
								          </button>
												</div>
											</div> --}}
											{!! Form::close() !!}
										</tr>
									@empty
										<tr>
											<td colspan="4">No offspring data found</td>
										</tr>
									@endforelse
								</tbody>
							</table>
						</div>
					</div>
				</div>
			@else
				@if($family->getGroupingProperties()->where("property_id", 54)->first()->value == 1)
					{{-- INDIVIDUAL WEIGHING IS DISPLAYED --}}
					{!! Form::open(['route' => 'farm.pig.add_sowlitter_record_individual', 'method' => 'post']) !!}
					<div id="individual_weighing1" class="col s12" style="display: block;">
						<h5  class="green darken-3 white-text center">Individual Weighing</h5>
						<h5 class="green lighten-1 center">Add offspring</h5>
						<input type="hidden" name="grouping_id" value="{{ $family->id }}">
						@if(!is_null($family->getGroupingProperties()->where("property_id", 3)->first()))
							<input id="hidden_date" type="hidden" name="date_farrowed" value="{{ $family->getGroupingProperties()->where("property_id", 3)->first()->value }}">
						@endif
						@if(!is_null($family->getGroupingProperties()->where("property_id", 48)->first()))
							<input id="parity" type="hidden" name="parity" value="{{ $family->getGroupingProperties()->where("property_id", 48)->first()->value }}">
						@endif
						@if(!is_null($family->getGroupingProperties()->where("property_id", 45)->first()))
							<input id="number_stillborn" type="hidden" name="number_stillborn" value="{{ $family->getGroupingProperties()->where("property_id", 45)->first()->value }}">
						@endif
						@if(!is_null($family->getGroupingProperties()->where("property_id", 46)->first()))
							<input id="number_mummified" type="hidden" name="number_mummified" value="{{ $family->getGroupingProperties()->where("property_id", 46)->first()->value }}">
						@endif
						@if(!is_null($family->getGroupingProperties()->where("property_id", 47)->first()))
							<input type="hidden" name="abnomalities" value="{{ $family->getGroupingProperties()->where("property_id", 47)->first()->value }}">
						@endif
						<div class="col s4">
              <input id="offspring_earnotch" type="text" name="offspring_earnotch" class="validate">
              <label for="offspring_earnotch">Offspring Earnotch</label>
              <input type="hidden" name="option" value="1">
						</div>
						<div class="col s4">
							<select id="select_sex" name="sex" class="browser-default">
								<option disabled selected>Choose sex</option>
								<option value="M">Male</option>
								<option value="F">Female</option>
							</select>
						</div>
						<div class="col s4">
							<input id="birth_weight" type="text" name="birth_weight">
							<label for="birth_weight">Birth Weight, kg</label>
						</div>
					</div>
					<div id="individual_weighing2" class="row center" style="display: block;">
						<button class="btn waves-effect waves-light green darken-3" type="submit" onclick="Materialize.toast('Successfully added!', 4000)">Add
	            <i class="material-icons right">add</i>
	          </button>
					</div>
					{!! Form::close() !!}
					<div id="individual_weighing3" class="row" style="display: block;">
						<div class="col s12">
							<table class="centered striped">
								<thead>
									<tr class="green lighten-1">
										<th>Offspring ID</th>
										<th>Sex</th>
										<th>Birth weight, kg</th>
										<th>Weaning weight, kg</th>
									</tr>
								</thead>
								<tbody>
									@forelse($offsprings as $offspring)
										<tr>
											<td>{{ $offspring->getChild()->registryid }}</td>
											<td>{{ $offspring->getAnimalProperties()->where("property_id", 2)->first()->value }}</td>
											<td>{{ $offspring->getAnimalProperties()->where("property_id", 5)->first()->value }}</td>
											{!! Form::open(['route' => 'farm.pig.get_weaning_weights', 'method' => 'post']) !!}
											@if(is_null($family->getGroupingProperties()->where("property_id", 6)->first()))
												@if(is_null($offspring->getAnimalProperties()->where("property_id", 7)->first()))
													<td>
														<a class="btn waves-effect waves-light green darken-3 modal-trigger" href="#weaning_weight_modal{{$offspring->getChild()->id}}">
									            Add <i class="material-icons right">add</i>
									          </a>
													</td>
												@else
													<td>{{ $offspring->getAnimalProperties()->where("property_id", 7)->first()->value }}</td>
												@endif
											@else
												@if(is_null($offspring->getAnimalProperties()->where("property_id", 7)->first()))
													<td>
														<a class="btn waves-effect waves-light green darken-3 modal-trigger" href="#weaning_weight_modal{{$offspring->getChild()->id}}">
									            Add <i class="material-icons right">add</i>
									          </a>
													</td>
												@else
													<td>{{ $offspring->getAnimalProperties()->where("property_id", 7)->first()->value }}</td>
												@endif
											@endif
											{{-- MODAL STRUCTURE --}}
											<div id="weaning_weight_modal{{$offspring->getChild()->id}}" class="modal">
												<div class="modal-content">
													<h5 class="center">Weaning Record: <strong>{{ $offspring->getChild()->registryid }}</strong></h5>
													<input type="hidden" name="offspring_id" value="{{ $offspring->getChild()->registryid }}">
													<input type="hidden" name="family_id" value="{{ $family->id }}">
													<div class="row center">
														<div class="col s8 offset-s2 center">
															Date Weaned:
															@if(is_null($family->getGroupingProperties()->where("property_id", 6)->first()))
																<div class="input-field inline">
																	<input id="date_weaned" type="text" name="date_weaned" placeholder="Pick date" class="datepicker">
																</div>
															@else
																<div class="input-field inline">
																	<input id="date_weaned" type="text" name="date_weaned" class="datepicker" value="{{ $family->getGroupingProperties()->where("property_id", 6)->first()->value }}">
																</div>
															@endif
														</div>
														<div class="col s8 offset-s2 center">
															Weaning Weight, kg:
															<div class="input-field inline">
																<input id="weaning_weight" type="text" name="weaning_weight">
															</div>
														</div>
													</div>
												</div>
												<div class="row center">
													<button class="btn waves-effect waves-light green darken-3" type="submit">
								            Submit <i class="material-icons right">send</i>
								          </button>
												</div>
											</div>
											{!! Form::close() !!}
										</tr>
									@empty
										<tr>
											<td colspan="4">No offspring data found</td>
										</tr>
									@endforelse
								</tbody>
							</table>
						</div>
					</div>
					{{-- GROUP WEIGHING IS HIDDEN --}}
					{!! Form::open(['route' => 'farm.pig.add_sowlitter_record_group', 'method' => 'post']) !!}
					<div id="group_weighing" class="row center" style="display: none;">
						<h5 class="green darken-3 white-text">Group Weighing</h5>
						<input type="hidden" name="grouping_id" value="{{ $family->id }}">
						@if(!is_null($family->getGroupingProperties()->where("property_id", 3)->first()))
							<input id="hidden_date" type="hidden" name="date_farrowed" value="{{ $family->getGroupingProperties()->where("property_id", 3)->first()->value }}">
						@endif
						@if(!is_null($family->getGroupingProperties()->where("property_id", 48)->first()))
							<input id="parity" type="hidden" name="parity" value="{{ $family->getGroupingProperties()->where("property_id", 48)->first()->value }}">
						@endif
						@if(!is_null($family->getGroupingProperties()->where("property_id", 45)->first()))
							<input id="number_stillborn" type="hidden" name="number_stillborn" value="{{ $family->getGroupingProperties()->where("property_id", 45)->first()->value }}">
						@endif
						@if(!is_null($family->getGroupingProperties()->where("property_id", 46)->first()))
							<input id="number_mummified" type="hidden" name="number_mummified" value="{{ $family->getGroupingProperties()->where("property_id", 46)->first()->value }}">
						@endif
						@if(!is_null($family->getGroupingProperties()->where("property_id", 47)->first()))
							<input type="hidden" name="abnomalities" value="{{ $family->getGroupingProperties()->where("property_id", 47)->first()->value }}">
						@endif
						<div class="row">
							<div class="col s4 offset-s2">
								Litter Birth Weight, kg
								@if(!is_null($family->getGroupingProperties()->where("property_id", 55)->first()))
									<div class="input-field inline">
										<input id="litter_birth_weight" type="text" name="litter_birth_weight" value="{{ $family->getGroupingProperties()->where("property_id", 55)->first()->value }}">
									</div>
								@else
									<div class="input-field inline">
										<input id="litter_birth_weight" type="text" name="litter_birth_weight">
									</div>
								@endif
							</div>
							<div class="col s4">
								Litter-size Born Alive
								@if(!is_null($family->getGroupingProperties()->where("property_id", 50)->first()))
									<div class="input-field inline">
										<input id="lsba" type="text" name="lsba" value="{{ $family->getGroupingProperties()->where("property_id", 50)->first()->value }}">
									</div>
								@else
									<div class="input-field inline">
										<input id="lsba" type="text" name="lsba">
									</div>
								@endif
							</div>
						</div>
						<h5 class="green lighten-1">Add offspring</h5>
						<div class="row">
							<div class="col s4 push-s2">
	              <input id="offspring_earnotch" type="text" name="offspring_earnotch" class="validate">
	              <label for="offspring_earnotch">Offspring Earnotch</label>
	              <input type="hidden" name="option" value="0">
							</div>
							<div class="col s4 push-s2">
								<select id="select_sex" name="sex" class="browser-default">
									<option disabled selected>Choose sex</option>
									<option value="M">Male</option>
									<option value="F">Female</option>
								</select>
							</div>
						</div>
						<div class="row center">
							<button class="btn waves-effect waves-light green darken-3" type="submit" onclick="Materialize.toast('Successfully added!', 4000)">Add
		            <i class="material-icons right">add</i>
		          </button>
		          {!! Form::close() !!}
						</div>
						<div class="row center">
							<div class="col s12">
								<table class="centered striped">
									<thead>
										<tr class="green lighten-1">
											<th>Offspring ID</th>
											<th>Sex</th>
											<th>Birth weight, kg</th>
											<th>Weaning weight, kg</th>
										</tr>
									</thead>
									<tbody>
										@forelse($offsprings as $offspring)
											{!! Form::open(['route' => 'farm.pig.edit_temporary_registryid', 'method' => 'post']) !!}
											<tr>
												<td>
													{{ $offspring->getChild()->registryid }} <a href="#edit_id{{$offspring->getChild()->id}}" class="modal-trigger"><i class="material-icons right">edit</i></a>
												</td>
												{{-- MODAL STRUCTURE --}}
												{{-- <div id="edit_id{{$offspring->getChild()->id}}" class="modal">
													<div class="modal-content">
														<h5 class="center">Edit Temporary Earnotch:<br><strong>{{ $offspring->getChild()->registryid }}</strong></h5>
														<input type="hidden" name="old_earnotch" value="{{ $offspring->getChild()->id }}">
														<div class="row center">
															<div class="input-field col s8 offset-s2">
																<input id="new_earnotch" type="text" name="new_earnotch" class="valideate">
																<label for="new_earnotch">New Earnotch</label>
															</div>
														</div>
													</div>
													<div class="row center">
														<button class="btn waves-effect waves-light green darken-3" type="submit">
									            Submit <i class="material-icons right">send</i>
									          </button>
													</div>
												</div> --}}
												{!! Form::close() !!}
												<td>{{ $offspring->getAnimalProperties()->where("property_id", 2)->first()->value }}</td>
												<td>{{ round($offspring->getAnimalProperties()->where("property_id", 5)->first()->value, 4) }}</td>
												{!! Form::open(['route' => 'farm.pig.get_weaning_weights', 'method' => 'post']) !!}
												@if(is_null($family->getGroupingProperties()->where("property_id", 6)->first()))
													@if(is_null($offspring->getAnimalProperties()->where("property_id", 7)->first()))
														<td>
															<a class="btn waves-effect waves-light green darken-3 modal-trigger" href="#weaning_weight_modal{{$offspring->getChild()->id}}">
										            Add <i class="material-icons right">add</i>
										          </a>
														</td>
													@else
														<td>{{ $offspring->getAnimalProperties()->where("property_id", 7)->first()->value }}</td>
													@endif
												@else
													@if(is_null($offspring->getAnimalProperties()->where("property_id", 7)->first()))
														<td>
															<a class="btn waves-effect waves-light green darken-3 modal-trigger" href="#weaning_weight_modal{{$offspring->getChild()->id}}">
										            Add <i class="material-icons right">add</i>
										          </a>
														</td>
													@else
														<td>{{ $offspring->getAnimalProperties()->where("property_id", 7)->first()->value }}</td>
													@endif
												@endif
												{{-- MODAL STRUCTURE --}}
												{{-- <div id="weaning_weight_modal{{$offspring->getChild()->id}}" class="modal">
													<div class="modal-content">
														<h5 class="center">Weaning Record: <strong>{{ $offspring->getChild()->registryid }}</strong></h5>
														<input type="hidden" name="offspring_id" value="{{ $offspring->getChild()->registryid }}">
														<input type="hidden" name="family_id" value="{{ $family->id }}">
														<div class="row center">
															<div class="col s8 offset-s2 center">
																Date Weaned:
																@if(is_null($family->getGroupingProperties()->where("property_id", 6)->first()))
																	<div class="input-field inline">
																		<input id="date_weaned" type="text" name="date_weaned" placeholder="Pick date" class="datepicker">
																	</div>
																@else
																	<div class="input-field inline">
																		<input id="date_weaned" type="text" name="date_weaned" class="datepicker" value="{{ $family->getGroupingProperties()->where("property_id", 6)->first()->value }}">
																	</div>
																@endif
															</div>
															<div class="col s8 offset-s2 center">
																Weaning Weight, kg:
																<div class="input-field inline">
																	<input id="weaning_weight" type="text" name="weaning_weight">
																</div>
															</div>
														</div>
													</div>
													<div class="row center">
														<button class="btn waves-effect waves-light green darken-3" type="submit">
									            Submit <i class="material-icons right">send</i>
									          </button>
													</div>
												</div> --}}
												{!! Form::close() !!}
											</tr>
										@empty
											<tr>
												<td colspan="4">No offspring data found</td>
											</tr>
										@endforelse
									</tbody>
								</table>
							</div>
						</div>
					</div>
				@elseif($family->getGroupingProperties()->where("property_id", 54)->first()->value == 0)
					{{-- INDIVIDUAL WEIGHING IS HIDDEN --}}
					{!! Form::open(['route' => 'farm.pig.add_sowlitter_record_individual', 'method' => 'post']) !!}
					<div id="individual_weighing1" class="col s12" style="display: none;">
						<h5  class="green darken-3 white-text center">Individual Weighing</h5>
						<h5 class="green lighten-1 center">Add offspring</h5>
						<input type="hidden" name="grouping_id" value="{{ $family->id }}">
						@if(!is_null($family->getGroupingProperties()->where("property_id", 3)->first()))
							<input id="hidden_date" type="hidden" name="date_farrowed" value="{{ $family->getGroupingProperties()->where("property_id", 3)->first()->value }}">
						@endif
						@if(!is_null($family->getGroupingProperties()->where("property_id", 48)->first()))
							<input id="parity" type="hidden" name="parity" value="{{ $family->getGroupingProperties()->where("property_id", 48)->first()->value }}">
						@endif
						@if(!is_null($family->getGroupingProperties()->where("property_id", 45)->first()))
							<input id="number_stillborn" type="hidden" name="number_stillborn" value="{{ $family->getGroupingProperties()->where("property_id", 45)->first()->value }}">
						@endif
						@if(!is_null($family->getGroupingProperties()->where("property_id", 46)->first()))
							<input id="number_mummified" type="hidden" name="number_mummified" value="{{ $family->getGroupingProperties()->where("property_id", 46)->first()->value }}">
						@endif
						@if(!is_null($family->getGroupingProperties()->where("property_id", 47)->first()))
							<input type="hidden" name="abnomalities" value="{{ $family->getGroupingProperties()->where("property_id", 47)->first()->value }}">
						@endif
						<div class="col s4">
              <input id="offspring_earnotch" type="text" name="offspring_earnotch" class="validate">
              <label for="offspring_earnotch">Offspring Earnotch</label>
              <input type="hidden" name="option" value="1">
						</div>
						<div class="col s4">
							<select id="select_sex" name="sex" class="browser-default">
								<option disabled selected>Choose sex</option>
								<option value="M">Male</option>
								<option value="F">Female</option>
							</select>
						</div>
						<div class="col s4">
							<input id="birth_weight" type="text" name="birth_weight">
							<label for="birth_weight">Birth Weight, kg</label>
						</div>
					</div>
					<div id="individual_weighing2" class="row center" style="display: none;">
						<button class="btn waves-effect waves-light green darken-3" type="submit" onclick="Materialize.toast('Successfully added!', 4000)">Add
	            <i class="material-icons right">add</i>
	          </button>
					</div>
					{!! Form::close() !!}
					<div id="individual_weighing3" class="row" style="display: none;">
						<div class="col s12">
							<table class="centered striped">
								<thead>
									<tr class="green lighten-1">
										<th>Offspring ID</th>
										<th>Sex</th>
										<th>Birth weight, kg</th>
										<th>Weaning weight, kg</th>
									</tr>
								</thead>
								<tbody>
									@forelse($offsprings as $offspring)
										<tr>
											<td>{{ $offspring->getChild()->registryid }}</td>
											<td>{{ $offspring->getAnimalProperties()->where("property_id", 2)->first()->value }}</td>
											<td>{{ $offspring->getAnimalProperties()->where("property_id", 5)->first()->value }}</td>
											{!! Form::open(['route' => 'farm.pig.get_weaning_weights', 'method' => 'post']) !!}
											@if(is_null($family->getGroupingProperties()->where("property_id", 6)->first()))
												@if(is_null($offspring->getAnimalProperties()->where("property_id", 7)->first()))
													<td>
														<a class="btn waves-effect waves-light green darken-3 modal-trigger" href="#weaning_weight_modal{{$offspring->getChild()->id}}">
									            Add <i class="material-icons right">add</i>
									          </a>
													</td>
												@else
													<td>{{ $offspring->getAnimalProperties()->where("property_id", 7)->first()->value }}</td>
												@endif
											@else
												@if(is_null($offspring->getAnimalProperties()->where("property_id", 7)->first()))
													<td>
														<a class="btn waves-effect waves-light green darken-3 modal-trigger" href="#weaning_weight_modal{{$offspring->getChild()->id}}">
									            Add <i class="material-icons right">add</i>
									          </a>
													</td>
												@else
													<td>{{ $offspring->getAnimalProperties()->where("property_id", 7)->first()->value }}</td>
												@endif
											@endif
											{{-- MODAL STRUCTURE --}}
											{{-- <div id="weaning_weight_modal{{$offspring->getChild()->id}}" class="modal">
												<div class="modal-content">
													<h5 class="center">Weaning Record: <strong>{{ $offspring->getChild()->registryid }}</strong></h5>
													<input type="hidden" name="offspring_id" value="{{ $offspring->getChild()->registryid }}">
													<input type="hidden" name="family_id" value="{{ $family->id }}">
													<div class="row center">
														<div class="col s8 offset-s2 center">
															Date Weaned:
															@if(is_null($family->getGroupingProperties()->where("property_id", 6)->first()))
																<div class="input-field inline">
																	<input id="date_weaned" type="text" name="date_weaned" placeholder="Pick date" class="datepicker">
																</div>
															@else
																<div class="input-field inline">
																	<input id="date_weaned" type="text" name="date_weaned" class="datepicker" value="{{ $family->getGroupingProperties()->where("property_id", 6)->first()->value }}">
																</div>
															@endif
														</div>
														<div class="col s8 offset-s2 center">
															Weaning Weight, kg:
															<div class="input-field inline">
																<input id="weaning_weight" type="text" name="weaning_weight">
															</div>
														</div>
													</div>
												</div>
												<div class="row center">
													<button class="btn waves-effect waves-light green darken-3" type="submit">
								            Submit <i class="material-icons right">send</i>
								          </button>
												</div>
											</div> --}}
											{!! Form::close() !!}
										</tr>
									@empty
										<tr>
											<td colspan="4">No offspring data found</td>
										</tr>
									@endforelse
								</tbody>
							</table>
						</div>
					</div>
					{{-- GROUP WEIGHING IS DISPLAYED --}}
					{!! Form::open(['route' => 'farm.pig.add_sowlitter_record_group', 'method' => 'post']) !!}
					<div id="group_weighing" class="row center" style="display: block;">
						<h5 class="green darken-3 white-text">Group Weighing</h5>
						<input type="hidden" name="grouping_id" value="{{ $family->id }}">
						@if(!is_null($family->getGroupingProperties()->where("property_id", 3)->first()))
							<input id="hidden_date" type="hidden" name="date_farrowed" value="{{ $family->getGroupingProperties()->where("property_id", 3)->first()->value }}">
						@endif
						@if(!is_null($family->getGroupingProperties()->where("property_id", 48)->first()))
							<input id="parity" type="hidden" name="parity" value="{{ $family->getGroupingProperties()->where("property_id", 48)->first()->value }}">
						@endif
						@if(!is_null($family->getGroupingProperties()->where("property_id", 45)->first()))
							<input id="number_stillborn" type="hidden" name="number_stillborn" value="{{ $family->getGroupingProperties()->where("property_id", 45)->first()->value }}">
						@endif
						@if(!is_null($family->getGroupingProperties()->where("property_id", 46)->first()))
							<input id="number_mummified" type="hidden" name="number_mummified" value="{{ $family->getGroupingProperties()->where("property_id", 46)->first()->value }}">
						@endif
						@if(!is_null($family->getGroupingProperties()->where("property_id", 47)->first()))
							<input type="hidden" name="abnomalities" value="{{ $family->getGroupingProperties()->where("property_id", 47)->first()->value }}">
						@endif
						<div class="row">
							<div class="col s4 offset-s2">
								Litter Birth Weight, kg
								@if(!is_null($family->getGroupingProperties()->where("property_id", 55)->first()))
									<div class="input-field inline">
										<input id="litter_birth_weight" type="text" name="litter_birth_weight" value="{{ $family->getGroupingProperties()->where("property_id", 55)->first()->value }}">
									</div>
								@else
									<div class="input-field inline">
										<input id="litter_birth_weight" type="text" name="litter_birth_weight">
									</div>
								@endif
							</div>
							<div class="col s4">
								Litter-size Born Alive
								@if(!is_null($family->getGroupingProperties()->where("property_id", 50)->first()))
									<div class="input-field inline">
										<input id="lsba" type="text" name="lsba" value="{{ $family->getGroupingProperties()->where("property_id", 50)->first()->value }}">
									</div>
								@else
									<div class="input-field inline">
										<input id="lsba" type="text" name="lsba">
									</div>
								@endif
							</div>
						</div>
						<h5 class="green lighten-1">Add offspring</h5>
						<div class="row">
							<div class="col s4 push-s2">
	              <input id="offspring_earnotch" type="text" name="offspring_earnotch" class="validate">
	              <label for="offspring_earnotch">Offspring Earnotch</label>
	              <input type="hidden" name="option" value="0">
							</div>
							<div class="col s4 push-s2">
								<select id="select_sex" name="sex" class="browser-default">
									<option disabled selected>Choose sex</option>
									<option value="M">Male</option>
									<option value="F">Female</option>
								</select>
							</div>
						</div>
						<div class="row center">
							<button class="btn waves-effect waves-light green darken-3" type="submit" onclick="Materialize.toast('Successfully added!', 4000)">Add
		            <i class="material-icons right">add</i>
		          </button>
		          {!! Form::close() !!}
						</div>
						<div class="row center">
							<div class="col s12">
								<table class="centered striped">
									<thead>
										<tr class="green lighten-1">
											<th>Offspring ID</th>
											<th>Sex</th>
											<th>Birth weight, kg</th>
											<th>Weaning weight, kg</th>
										</tr>
									</thead>
									<tbody>
										@forelse($offsprings as $offspring)
											{!! Form::open(['route' => 'farm.pig.edit_temporary_registryid', 'method' => 'post']) !!}
											<tr>
												<td>
													{{ $offspring->getChild()->registryid }} <a href="#edit_id{{$offspring->getChild()->id}}" class="modal-trigger"><i class="material-icons right">edit</i></a>
												</td>
												{{-- MODAL STRUCTURE --}}
												<div id="edit_id{{$offspring->getChild()->id}}" class="modal">
													<div class="modal-content">
														<h5 class="center">Edit Temporary Earnotch:<br><strong>{{ $offspring->getChild()->registryid }}</strong></h5>
														<input type="hidden" name="old_earnotch" value="{{ $offspring->getChild()->id }}">
														<div class="row center">
															<div class="input-field col s8 offset-s2">
																<input id="new_earnotch" type="text" name="new_earnotch" class="valideate">
																<label for="new_earnotch">New Earnotch</label>
															</div>
														</div>
													</div>
													<div class="row center">
														<button class="btn waves-effect waves-light green darken-3" type="submit">
									            Submit <i class="material-icons right">send</i>
									          </button>
													</div>
												</div>
												{!! Form::close() !!}
												<td>{{ $offspring->getAnimalProperties()->where("property_id", 2)->first()->value }}</td>
												<td>{{ round($offspring->getAnimalProperties()->where("property_id", 5)->first()->value, 4) }}</td>
												{!! Form::open(['route' => 'farm.pig.get_weaning_weights', 'method' => 'post']) !!}
												@if(is_null($family->getGroupingProperties()->where("property_id", 6)->first()))
													@if(is_null($offspring->getAnimalProperties()->where("property_id", 7)->first()))
														<td>
															<a class="btn waves-effect waves-light green darken-3 modal-trigger" href="#weaning_weight_modal{{$offspring->getChild()->id}}">
										            Add <i class="material-icons right">add</i>
										          </a>
														</td>
													@else
														<td>{{ $offspring->getAnimalProperties()->where("property_id", 7)->first()->value }}</td>
													@endif
												@else
													@if(is_null($offspring->getAnimalProperties()->where("property_id", 7)->first()))
														<td>
															<a class="btn waves-effect waves-light green darken-3 modal-trigger" href="#weaning_weight_modal{{$offspring->getChild()->id}}">
										            Add <i class="material-icons right">add</i>
										          </a>
														</td>
													@else
														<td>{{ $offspring->getAnimalProperties()->where("property_id", 7)->first()->value }}</td>
													@endif
												@endif
												{{-- MODAL STRUCTURE --}}
												<div id="weaning_weight_modal{{$offspring->getChild()->id}}" class="modal">
													<div class="modal-content">
														<h5 class="center">Weaning Record: <strong>{{ $offspring->getChild()->registryid }}</strong></h5>
														<input type="hidden" name="offspring_id" value="{{ $offspring->getChild()->registryid }}">
														<input type="hidden" name="family_id" value="{{ $family->id }}">
														<div class="row center">
															<div class="col s8 offset-s2 center">
																Date Weaned:
																@if(is_null($family->getGroupingProperties()->where("property_id", 6)->first()))
																	<div class="input-field inline">
																		<input id="date_weaned" type="text" name="date_weaned" placeholder="Pick date" class="datepicker">
																	</div>
																@else
																	<div class="input-field inline">
																		<input id="date_weaned" type="text" name="date_weaned" class="datepicker" value="{{ $family->getGroupingProperties()->where("property_id", 6)->first()->value }}">
																	</div>
																@endif
															</div>
															<div class="col s8 offset-s2 center">
																Weaning Weight, kg:
																<div class="input-field inline">
																	<input id="weaning_weight" type="text" name="weaning_weight">
																</div>
															</div>
														</div>
													</div>
													<div class="row center">
														<button class="btn waves-effect waves-light green darken-3" type="submit">
									            Submit <i class="material-icons right">send</i>
									          </button>
													</div>
												</div>
												{!! Form::close() !!}
											</tr>
										@empty
											<tr>
												<td colspan="4">No offspring data found</td>
											</tr>
										@endforelse
									</tbody>
								</table>
							</div>
						</div>
					</div>
				@endif
			@endif
		</div>
	</div>
@endsection

@section('scripts')
		<script>
		$(document).ready(function(){
		  $("#paritytext").change(function (event) {
		    event.preventDefault();
		    var familyidvalue = $('input[name=grouping_id]').val();
		    var parityvalue = $('input[name=parity]').val();
		    $.ajax({
		    	headers: {
          	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
		      url: '../fetch_parity/'+familyidvalue+'/'+parityvalue,
		      type: 'POST',
		      cache: false,
		      data: {familyidvalue, parityvalue},
		      success: function(data)
		      {
		        Materialize.toast('Parity successfully added!', 4000);
		      }
		    });
		  });
		  $("#weighing_options").change(function(event) {
		  	if($(this).is(":checked")){
		  		document.getElementById("individual_weighing1").style.display = "block";
		  		document.getElementById("individual_weighing2").style.display = "block";
		  		document.getElementById("individual_weighing3").style.display = "block";
		  		document.getElementById("group_weighing").style.display = "none";
		  		event.preventDefault();
		   		var familyidvalue = $('input[name=grouping_id]').val();
		    	var option = 1;
		    	$.ajax({
		    		headers: {
	          	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	          },
			      url: '../fetch_weighing_option/'+familyidvalue+'/'+option,
			      type: 'POST',
			      cache: false,
			      data: {familyidvalue, option},
			      success: function(data)
			      {
			        Materialize.toast('Enabled individual weighing!', 4000);
			      }
		    	});
		  	}
		  	if(!$(this).is(":checked")){
		  		document.getElementById("group_weighing").style.display = "block";
		  		document.getElementById("individual_weighing1").style.display = "none";
		  		document.getElementById("individual_weighing2").style.display = "none";
		  		document.getElementById("individual_weighing3").style.display = "none";
		  		event.preventDefault();
		   		var familyidvalue = $('input[name=grouping_id]').val();
		    	var option = 0;
		    	$.ajax({
		    		headers: {
	          	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	          },
			      url: '../fetch_weighing_option/'+familyidvalue+'/'+option,
			      type: 'POST',
			      cache: false,
			      data: {familyidvalue, option},
			      success: function(data)
			      {
			        Materialize.toast('Enabled group weighing!', 4000);
			      }
		    	});
		  	}
		  });
		});
	</script>
@endsection
