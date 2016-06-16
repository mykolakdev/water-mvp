<!-- This would be nice as a Vue component -->
<?php 
	$percent = $goal/$nitrogen_unatt->Total_UnAtt;
	// echo 'percent: ' . $percent;
	$percent = round($percent);

	$total = number_format(round($nitrogen_unatt->Total_UnAtt, 0));
	$att_total = number_format(round($nitrogen_att->Total_Att,0));
?>


<iframe src="{{url('/progress')}}" frameborder="0" width="200" height="200" id="embayment_progress" style="background:transparent"></iframe>





<!-- <div id="embayment_progress" style="background: #2caae4; background: -moz-linear-gradient(bottom,  #2caae4 {{$percent}}%, #f9ae1b 100%); 
	background: -webkit-linear-gradient(bottom,  #2caae4 {{$percent}}%, #f9ae1b 100%); 
	background: linear-gradient(to top,  #2caae4 {{$percent}}%, #f9ae1b 100%); 
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#2caae4', endColorstr='#f9ae1b',GradientType=1 );">
	<span>
	{{-- Original Unattenuated N: {{$total}}kg <br /> --}}
	Starting (Att) N: {{$att_total}}kg <br />
	Scenario Total: @{{total_treated|round}}kg <br />
	Goal: {{number_format($goal)}}kg<br />
		<span id="n_removed"><?php echo number_format(round(session('n_removed'))); ?></span>kg Unattenuated N Removed<br />
	<p>Testing Fertilizer: @{{fert_treated}}</p>

	</span>
	
</div> -->