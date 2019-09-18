<html>
	<head>
		<title>WatershedMVP Scenario Results</title>
		<?php if( env('APP_ENV') == 'production' ) : ?>
			<link rel="stylesheet" href="{{secure_url('/css/app.css')}}">
		<?php else :?>
			<link rel="stylesheet" href="{{url('/css/app.css')}}">
		<?php endif; ?>
		<!-- <link rel="stylesheet" href="{{secure_url('/css/app.css')}}"> -->
		<script src="https://code.jquery.com/jquery-3.0.0.min.js"></script>
		<script>window.name = 'wmvp_results_{{$scenario->ScenarioID}}';</script>
	</head>
	<body>
		<div class="wrapper">
		<div class="content full-width">
			@include('common.navigation')

			<h1>Scenario ID: {{$scenario->ScenarioID}}</h1>
	      	<h1>Embayment: {{$scenario->AreaName}}</h1>
			<h2 class="author">Created by: {{$scenario->user->name}} on {{date('Y-m-d', strtotime($scenario->CreateDate))}}</h2>
			<div id="app">
			<?php
				// TODO: Can we get/set from global variables?
				$scenario_cost = 0;
				$n_removed = 0;
				$n_att_total = 0;
				$n_att_rem_total = 0;
				$n_scen_total = 0;
				$n_target_total = 0;
				$n_rem_total = 0;
				setlocale(LC_MONETARY, 'en_US');
			?>


			@if(count($scenario->treatments) > 0)


			<table>
				<thead>
					<tr>
						<th colspan="2">Technology</th>
						<th>Parcels Affected</th>
						<th>Nitrogen Removed (Unatt)</th>
						<th>Total Cost</th>
						<th>Cost per kg N removed</th>
						<th>Delete</th>
					</tr>
				</thead>
				<tbody>

					@foreach($scenario->treatments as $result)
						<tr id="treat_{{$result->TreatmentID}}">
							@if(!$result->Parent_TreatmentId)
								<td>
									<div class="technology">
										<img src="http://www.cch2o.org/Matrix/icons/{{$result->technology->icon}}" alt="">
									</div>
								</td>
								<td>{{$result->technology->Technology_Strategy}} ({{$result->TreatmentID}})</td>
								<td>{{$result->Treatment_Parcels}}</td>
								<td>{{number_format(round($result->Nload_Reduction))}}kg</td> <?php $n_removed += $result->Nload_Reduction; ?>
								<td><?php echo '$'.number_format($result->Cost_Total,0,'.',',');?></td>
								<!-- money_format('%10.0n', $result->Cost_Total);?> -->
									<?php $scenario_cost += $result->Cost_Total; ?>
								<td><?php if ($result->Nload_Reduction > 0) {
								echo '$'.number_format(($result->Cost_Total/$result->Nload_Reduction)/12.46,0,'.',',');}?></td>
								<!-- money_format('%10.0n', ($result->Cost_Total/$result->Nload_Reduction)/12.46);}?> -->
								<td><a data-treatment="{{$result->TreatmentID}}" data-treatmenttype= "{{$result->TreatmentType_ID}}" class="deletetreatment button--cta"><i class="fa fa-trash-o"></i> Delete</a></td>

							@endif

					</tr>
					@endforeach
					<tr id="totals">
						<td>Scenario Totals:</td>
						<td></td>
						<td></td>
						<td><strong><?php echo number_format(round($n_removed));?>kg</strong></td>
						<td><strong><?php echo '$'.number_format($scenario_cost,0,'.',',');?></strong></td>
						<!-- money_format('%10.0n', $scenario_cost);?> -->
						<td colspan="2"><strong><?php if ($result->Nload_Reduction > 0) {
							echo '$'.number_format(($scenario_cost/$n_removed)/12.46,0,'.',',');}?></strong></td>
							<!-- money_format('%10.0n', (($scenario_cost/$n_removed)/12.46));?> -->
					</tr>
				</tbody>
			</table>
			<h2>Towns Affected</h2>
			<table>
				<thead>
					<tr>
						<th>Town</th>
						<th>Treatment</th>
						<th>Parcels Affected</th>
						<th>Nitrogen Removed (unattenuated)</th>
					</tr>
				</thead>
				<tbody>
					@foreach($towns as $town)
						<tr>
							<td>{{$town->town}}</td>
							<td>{{$town->wtt_treatment_id}}</td>
							<td>{{$town->wtt_tot_parcels}}</td>
							<td>{{number_format(round($town->wtt_unatt_n_removed))}}kg</td>
						</tr>

					@endforeach
				</tbody>
			</table>
			<h2>Subembayments</h2>
			<table>
				<thead>
					<tr>
						<th>Subembayment</th>
						<th>Original N<sup>1</sup></th>
						<th>N Removed (Attenuated)<sup>2</sup></th>
						<th>Scenario N</th>
						<th>Threshold N</th>
						<th>N Remaining to Threshold <sup>3</sup></th>
					</tr>
				</thead>
				<tbody>

					@foreach($subembayments as $sub)
					<tr>
						<td>{{$sub->SUBEM_DISP}}</td>
						<td>{{number_format(round($sub->n_load_att))}}kg</td> <?php $n_att_total += $sub->n_load_att; ?>
						<td>{{number_format(round($sub->n_load_att_removed))}}kg</td> <?php $n_att_rem_total += $sub->n_load_att_removed; ?>
						<td>{{number_format(round($sub->n_load_scenario))}}kg</td> <?php $n_scen_total += $sub->n_load_scenario; ?>
						<td>{{number_format(round($sub->n_load_target))}}kg</td> <?php $n_target_total += $sub->n_load_target; ?>
						<td>{{number_format(round($sub->n_load_scenario - $sub->n_load_target))}}kg</td> <?php $n_rem_total += $sub->n_load_scenario - $sub->n_load_target; ?>
					</tr>
					@endforeach

					<tr>
						<td><strong>Subembayment Totals</strong></td>
						<td><strong><?php echo number_format(round($n_att_total));?>kg</strong></td>
						<td><strong><?php echo number_format(round($n_att_rem_total));?>kg</strong></td>
						<td><strong><?php echo number_format(round($n_scen_total));?>kg</strong></td>
						<td><strong><?php echo number_format(round($n_target_total));?>kg</strong></td>
						<td><strong><?php echo number_format(round($n_rem_total));?>kg</strong></td>
					</tr>
				</tbody>

			</table>
			<p><sup>1</sup> The "Original N" value is calculated (attenuated) total Nitrogen for the subembayment. </p>
			<p><sup>2</sup>A negative number in this column represents Nitrogen added to a subembayment as part of a collection treatment.</p>
			<p><sup>3</sup>A negative number in this column means the user has exceeded the threshold for this subembayment.</p>

			<p>
				<a href="{{url('map', [$scenario->AreaID, $scenario->ScenarioID])}}" class="button" target="wmvp_scenario_{{$scenario->ScenarioID}}">back to map</a> 
				<a href="{{url('download', $scenario->ScenarioID)}}" class="button--cta right" target="_blank"><i class="fa fa-download"></i> Download Results (.xls)</a>
				<a id = 'saved' class="save button">Save Changes</a> 
			</p>

		@else
		<p>No treatments have been applied to this scenario yet.</p>
		<p><a href="{{url('map', [$scenario->AreaID, $scenario->ScenarioID])}}" class="button" target="wmvp_scenario_{{$scenario->ScenarioID}}">Return to map</a> </p>
		@endif

		</div>
		</div>
	</div>

	<script>
	$(document).ready(function(){
		scenario = {{$scenario->ScenarioID}};
		$('.deletetreatment').on('click', function(e){

			e.preventDefault();
			var treat = $(this).data('treatment');
			var treatType = $(this).data('treatmenttype');
			var url = "{{url('delete_treatment')}}" + '/' + treat;

			if (treatType === 400) {
				url = "{{url('delete_treatment')}}" + '/' + treat + '/' + 'fert';
			} 
			else if (treatType === 401) {
				url = "{{url('delete_treatment')}}" + '/' + treat + '/' + 'storm';
			}

			$.ajax({
				method: 'GET',
				url: url
			})
			.done(function(msg){
				$('#treat_'+treat).remove();
			});
		});

		// href="{{url('save', $scenario->ScenarioID)}}"
		$('.save').on('click', function(e){

			e.preventDefault();
			var url = "{{url('save')}}" + '/' + scenario;
			$.ajax({
				method: 'GET',
				url: url
			})
				.done(function(msg){
					$('#saved').addClass('button--cta')
				});
			});
	});
	</script>
	</body>
</html>
