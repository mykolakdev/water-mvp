		<title>{{$tech->Technology_Strategy}}</title>
		<link rel="stylesheet" href="{{url('/css/jquery.popdown.css')}}">
<meta name="csrf-token" id="token" content="{{ csrf_token() }}">
		

<div class="popdown-content" id="app">
	<header><h2>{{$tech->Technology_Strategy}}</h2></header>
	<section class="body">

			<div class="technology">
				<a href="http://www.cch2o.org/Matrix/detail.php?treatment={{$tech->id}}" target="_blank">
					<img src="http://2016.watershedmvp.org/images/SVG/{{$tech->Icon}}" width="75">
				 {{$tech->Technology_Strategy}}&nbsp;<i class="fa fa-question-circle"></i>
				</a>			
			</div>

			<!-- 
					This needs to be a case/switch based on the show_in_wmvp field
					0 => (this shouldn't ever appear because this technology shouldn't have been listed)
					1 => user will enter a unit metric to use for calculations (acres, linear feet, etc)
					2 => user will need to select a polygon for the treatment area
					3 => user will select a polygon and enter the unit metric for the treatment area calculation
						unit metric is used to calculate cost
					4 => user does not enter a treatment area (Fertilizer Mgmt or Stormwater BMPs)
			 -->

				@if($tech->Show_In_wMVP == 1)
					<!-- <p class="select"><button id="select_area">Select a location</button> <span>@{{subembayment}}</span></p> -->
					<p class="select"><button id="select_area_{{$treatment->TreatmentID}}">Select a location</button> <span>@{{subembayment}}</span></p>
					<p>
						<label for="unit_metric">Enter number of {{$tech->Unit_Metric}} to be treated: 
						<input type="text" id="unit_metric" name="unit_metric" size="3" style="width: auto;"></label>
					</p>
				@elseif($tech->Show_In_wMVP == 2)
					<!-- <div id="info">Select a polygon for the treatment area:  -->
						<button id="select_polygon_{{$treatment->TreatmentID}}">Draw Polygon</button>
					<!-- </div> -->
					<!-- <p class="select"><button id="select_area">Select a polygon</button> <span>@{{subembayment}}</span></p> -->
				@elseif($tech->Show_In_wMVP == 3)
					<p class="select"><button id="select_polygon_{{$treatment->TreatmentID}}">Select a polygon</button> <span>@{{subembayment}}</span></p>
					<p>
						<label for="unit_metric">Enter number of {{$tech->Unit_Metric}} to be treated: 
						<input type="text" id="unit_metric" name="unit_metric" size="3" style="width: auto;"></label>
					</p>

				@elseif($tech->Show_In_wMVP == 4)
					<table>
						<thead>
							<tr>
								<th colspan="2">Stormwater Nitrogen</th>
								<th colspan="2">After Treatment</th>
								<th></th>
							</tr>
							<tr>
								<th>Unattenuated</th>
								<th>Attenuated</th>
								<th>Unattenuated</th>
								<th>Attenuated</th>
								<th>N Removed</th>
							</tr>
						</thead>
						<tbody>
							<tr>
							
							 		<td>@{{storm_unatt | round}}kg</td>
									<td>@{{storm_att | round }}kg</td>
									<td>@{{storm_unatt_treated | round }}kg</td>
									<td>@{{storm_att_treated | round }}kg</td>
									<td>@{{storm_difference | round }}kg</td>
							</tr>
							
						</tbody>
					</table>
				@endif
		

			<p>
				Enter a valid reduction rate between {{$tech->Nutri_Reduc_N_Low}} and {{$tech->Nutri_Reduc_N_High}} percent.<br />
				
				<input type="range" id="storm-percent" min="{{$tech->Nutri_Reduc_N_Low}}" max="{{$tech->Nutri_Reduc_N_High}}" v-model="storm_percent" value="{{$tech->Nutri_Reduc_N_Low}}"> @{{storm_percent}}%
			</p>
			<p>
				<button id="apply_treatment_{{$treatment->TreatmentID}}">Apply</button>
				<button id="cancel_treatment_{{$treatment->TreatmentID}}" class="button--cta right"><i class="fa fa-ban"></i> Cancel</button>
			</p>


	</section>
</div>


<script src="{{url('/js/main.js')}}"></script>
<!-- <script src="{{url('/js/app.js')}}"></script> -->


<script>
	$(document).ready(function(){
	 treatment = {{$treatment->TreatmentID}};
	 @if($tech->Show_In_wMVP < 4)
		 var location1;
			$('#select_area_'+treatment).on('click', function(f){
				f.preventDefault();
			destination_active = 1;
			$('#popdown-opacity').hide();

			map.on('click', function(e)
			{		
				if (destination_active > 0) 
				{
						// console.log('map clicked');
						// console.log(e.mapPoint.x, e.mapPoint.y);
					
						var url = "{{url('/map/point/')}}"+'/'+e.mapPoint.x+'/'+ e.mapPoint.y + '/' + treatment;
						$.ajax({
							method: 'GET',
							url: url
						})
							.done(function(msg){
								msg = $.parseJSON(msg);
								// console.log(msg.SUBEM_DISP);
								// console.log(msg);
								location1 = msg.SUBEM_ID;
								$('#'+msg.SUBEM_NAME+'> .stats').show();
								// $('.notification_count').remove();
								$('#popdown-opacity').show();
								$('.select > span').text('Selected: '+msg.SUBEM_DISP);
								$('.select > span').show();
								$('#select_area_'+treatment).hide();
								destination_active = 0;
							})
				}

				});
			});

			$('#select_polygon_'+treatment).on('click', function(f){
				f.preventDefault();
				$('#popdown-opacity').hide();
				// $( "#info" ).trigger( "click" );
				// dom.byId("info")

				map.disableMapNavigation();
				tb.activate('polygon');

			});
			$('#apply_treatment_'+treatment).on('click', function(e){
				// need to save the treated N values and update the subembayment progress
				e.preventDefault();
				// console.log('clicked');
				var percent = $('#storm-percent').val();
				var units = 1;
				if ('{{$tech->Show_In_wMVP}}' == '1' || '{{$tech->Show_In_wMVP}}' == '3' )
				{
					units = $('#unit_metric').val();
				}
				else if ('{{$tech->Unit_Metric}}' == 'Each')
				{
					units = 1;
				}
				else
				{
					units = 0.00000000;
				}

				var url = "{{url('/apply_storm')}}" + '/' +  treatment + '/' + percent + '/' + units + '/' + location1;
				// console.log(url);
				$.ajax({
					method: 'GET',
					url: url
				})
					.done(function(msg){
						// console.log(msg);
						msg = Math.round(msg);
						$('#n_removed').text(msg);
						$('#popdown-opacity').hide();
						$( "#update" ).trigger( "click" );
						var newtreatment = '<li class="technology" data-treatment="{{$treatment->TreatmentID}}"><a href="{{url('/edit', $treatment->TreatmentID)}}" class="popdown"><img src="http://2016.watershedmvp.org/images/SVG/{{$tech->Icon}}" alt=""></a></li>';
						$('ul.selected-treatments').append(newtreatment);
						$('ul.selected-treatments li[data-treatment="{{$treatment->TreatmentID}}"] a').popdown();
					});
			});
			@else
				$('#apply_treatment_'+treatment).on('click', function(e){
				// need to save the treated N values and update the subembayment progress
				e.preventDefault();
				// console.log('clicked');
				var percent = $('#storm-percent').val();
				// var units = $('#unit_metric').val();
				var url = "{{url('/apply_percent')}}" + '/' +  treatment + '/' + percent + '/storm';
				// console.log(url);
				$.ajax({
					method: 'GET',
					url: url
				})
					.done(function(msg){
						// console.log(msg);
						msg = Math.round(msg);
						$('#n_removed').text(msg);
						$('#popdown-opacity').hide();
						$( "#update" ).trigger( "click" );
						var newtreatment = '<li class="technology" data-treatment="{{$treatment->TreatmentID}}"><a href="{{url('/edit', $treatment->TreatmentID)}}" class="popdown"><img src="http://2016.watershedmvp.org/images/SVG/{{$tech->Icon}}" alt=""></a></li>';
						$('ul.selected-treatments').append(newtreatment);
						$('ul.selected-treatments li[data-treatment="{{$treatment->TreatmentID}}"] a').popdown();	
					});
			});

			@endif



		$('#cancel_treatment_'+treatment).on('click', function(e){
		var url = "{{url('cancel', $treatment->TreatmentID)}}";
		$.ajax({
			method: 'GET',
			url: url
		})
			.done(function(msg){
				$('#popdown-opacity').hide();
				location.reload()
			});
		});


		});
</script>