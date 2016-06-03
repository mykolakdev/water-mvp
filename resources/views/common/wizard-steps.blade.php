	<div class="accordion">
		<span id="closeACC">X</span>

<section class="row accHorizontal">
	<aside class="accHorizontal__item">
	<input type="radio" name="group-1" class="state" id="acc-0" />
	<label class="backdrop" for="acc-0"></label>
	<article class="acc_cBox">
		<div class="acc_cImg">
			<header>
				<h3>Overview</h3>
			</header>
		</div>
		<div class="acc_cCont">
		
		<p>Total Unattenuated Nitrogen: @{{unatt|round}}kg; Total Attenuated Nitrogen: @{{att|round}}kg</p>
			<!-- <p><a href="{{url('test', $embayment->EMBAY_ID)}}">See query and test the values</a></p> -->
			<p>Nitrogen is treated at different entrance points:</p>
			<ul>
				<li>Fertilizer (applied to the ground directly)</li>
				<li>Stormwater Runoff</li>
				<li>Septic</li>
				<li>Groundwater</li>
				<li>Embayment</li>
			</ul>
			<p>For each of these stages, you can select technologies to remove Nitrogen from the embayment. For some, you can select the area that will be treated by drawing a polygon on the map. </p>
			<p>Your progress towards the embayment's Target Nitrogen Removal will be displayed in the graph to the left. In addition to the overall target, each sub-embayment will have its own individual Nitrogen load and target, which you can track using the graphs in the left sidebar.</p>
			<p>At any time, you can <a href="{{url('results')}}" target="_blank">view a summary of your scenario</a>.</p>
		
		</div>
	</article>
	</aside>


	<aside class="accHorizontal__item">
	<input type="radio" name="group-1" class="state" id="acc-1" />
	<label class="backdrop" for="acc-1"></label>
	<article class="acc_cBox">
		<div class="acc_cImg">
		<header>
			<h3>Fertilizer</h3><img src="http://www.cch2o.org/Matrix/icons/reduction.svg" alt="" width="60" style="display: inline;">
		</header>
		</div>
		<div class="acc_cCont">
			<p>Total Unattenuated Load from Fertilizer: @{{fert_unatt|round}}kg</p>
			<p>Total Attenuated Load from Fertilizer: @{{fert_att | round }}kg</p>
			<p>Choose a technology to treat Nitrogen from Fertilizer in your watershed.</p>
			
			
				 <div class="technology">
					<a href="/tech/fert/25" class="popdown"><img src="http://www.cch2o.org/Matrix/icons/npk_mgt.svg"></a><br />Fertilizer Management			
				</div>
			
		
		</div>
	</article>
	</aside>
	
	<aside class="accHorizontal__item">
		<input type="radio" name="group-1" class="state" id="acc-2" />
		<label class="backdrop" for="acc-2"></label>
		<article class="acc_cBox">
			<div class="acc_cImg">
				<header>
					<h3>Stormwater</h3><img src="http://www.cch2o.org/Matrix/icons/remediation.svg" alt="">
				</header>
			</div>
			<div class="acc_cCont">
				<p>Unattenuated Nitrogen from Stormwater: @{{storm_unatt|round}}kg; Attenuated Nitrogen from Stormwater: @{{storm_att|round}}kg</p>
				<!-- <p>Choose a technology to treat Nitrogen from Stormwater in your watershed.</p> -->
				<div class="technology_list">
					<div class="technology">
						<a href="{{url('/tech/storm/26')}}" class="popdown">
							<img src="http://www.cch2o.org/Matrix/icons/stormwater_bmps.svg"><br />
							 Stormwater Management
						</a>
					</div>
					<div class="technology">
						<a href="{{url('/tech/storm/8')}}" class="popdown">
							<img src="http://www.cch2o.org/Matrix/icons/stormwater_bmps.svg"><br />
							 Gravel Wetland
						</a>
					</div>
					<div class="technology">
						<a href="{{url('/tech/storm/9')}}"  class="popdown">
							<img src="http://www.cch2o.org/Matrix/icons/stormwater_bmps.svg"><br />
							 Bioretention/Soil Media Filters
						</a>
					</div>

					<div class="technology">
						<a href="{{url('/tech/storm/6')}}"  class="popdown">
							<img src="http://www.cch2o.org/Matrix/icons/stormwater_bmps.svg"><br />
							Phytobuffers
						</a>
					</div>
					<div class="technology">
						<a href="{{url('/tech/storm/7')}}"  class="popdown">
							<img src="http://www.cch2o.org/Matrix/icons/stormwater_bmps.svg"><br />
							Vegetated Swale
						</a>
					</div>	 
					<div class="technology">
						<a href="{{url('/tech/storm/10')}}"  class="popdown">
							<img src="http://www.cch2o.org/Matrix/icons/stormwater_bmps.svg"><br />
							Constructed Wetlands
						</a>
					</div>		
									<div id="info">
	  <!-- <button id="Polygon">Polygon</button> -->

	</div>
				</div>
							 
			</div>
		</article>
	</aside>
	
	<aside class="accHorizontal__item">
	<input type="radio" name="group-1" class="state" id="acc-3" />
	<label class="backdrop" for="acc-3"><!-- <i class="fa fa-times"></i> --></label>
	<article class="acc_cBox">
		<div class="acc_cImg">
		<!-- <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/308355/img-3.jpg" alt="" /> -->
		<header>
			<h3>Septic</h3><img src="http://www.cch2o.org/Matrix/icons/reduction.svg" alt="" width="60" style="display: inline;">
			<!-- <p>Web Designer</p> -->
		</header>
		</div>
		<div class="acc_cCont">
		<p>Unattenuated Nitrogen from Septic: -- kg; Attenuated Nitrogen from Septic: -- kg</p>
			<!-- <p><a href="" id="show_modal">Show Treatment Window</a></p> -->

			<div class="technology">
				<a href="{{url('/tech/collect/40')}}" class="popdown">
					<img src="http://www.cch2o.org/Matrix/icons/cluster_3.svg" alt="" ><br />
					Single-Stage Cluster</a>
			</div>
			<div class="technology">
				<a href="{{url('/tech/collect/41')}}" class="popdown">
					<img src="http://www.cch2o.org/Matrix/icons/cluster_3.svg" alt="" ><br />
					Two-Stage Cluster</a>
			</div>
			<div class="technology">
				<a href="{{url('/tech/collect/42')}}" class="popdown">
					<img src="http://www.cch2o.org/Matrix/icons/conventional_treatment.svg" alt="" ><br />
					Conventional Treatment</a>
			</div>			
			<div class="technology">
				<a href="{{url('/tech/collect/43')}}" class="popdown">
					<img src="http://www.cch2o.org/Matrix/icons/advanced_treatment.svg" alt="" ><br />
					Advanced Treatment</a>
			</div>			

			<div class="technology">
				<a href="{{url('/tech/collect/44')}}" class="popdown">
					<img src="http://www.cch2o.org/Matrix/icons/cluster_3.svg" alt="" ><br />
					Satellite Treatment</a>
			</div>	
			<div class="technology">
				<a href="{{url('/tech/collect/45')}}" class="popdown">
					<img src="http://www.cch2o.org/Matrix/icons/cluster_3.svg" alt="" ><br />
					Satellite Treatment - Enahnced</a>
			</div>	
			<div class="technology">
				<a href="{{url('/tech/septic/21')}}" class="popdown">
					<img src="http://www.cch2o.org/Matrix/icons/toilet.svg" alt="" ><br />
					Composting Toilets</a>
			</div>	
			<div class="technology">
				<a href="{{url('/tech/septic/22')}}" class="popdown">
					<img src="http://www.cch2o.org/Matrix/icons/toilet.svg" alt="" ><br />
					Incinerating Toilets</a>
			</div>	

			<div class="technology">
				<a href="{{url('/tech/septic/23')}}" class="popdown">
					<img src="http://www.cch2o.org/Matrix/icons/toilet.svg" alt="" ><br />
					Packaging Toilets</a>
			</div>		
			<div class="technology">
				<a href="{{url('/tech/septic/24')}}" class="popdown">
					<img src="http://www.cch2o.org/Matrix/icons/toilet.svg" alt="" ><br />
					Urine Diverting Toilets</a>
			</div>					
		</div>
	</article>
	</aside>
	
	<aside class="accHorizontal__item">
	<input type="radio" name="group-1" class="state" id="acc-4" />
	<label class="backdrop" for="acc-4"><!-- <i class="fa fa-times"></i> --></label>
	<article class="acc_cBox">
		<div class="acc_cImg">
		<!-- <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/308355/img-4.jpg" alt="" /> -->
		<header>
			<h3>Groundwater</h3>
			<img src="http://www.cch2o.org/Matrix/icons/remediation.svg" alt="">
			<!-- <p>Web Designer</p> -->
		</header>
		</div>
		<div class="acc_cCont">
			<p>Existing Nitrogen from Groundwater: (<span id="getNitrogen">Calculate Groundwater</span>)</p>
			<p>Choose a technology to treat Groundwater Nitrogen in your watershed.</p>
			<div class="technology_list">
				<div class="technology">
					<a href="{{url('/tech/groundwater/3')}}"  class="popdown">
						<img src="http://www.cch2o.org/Matrix/icons/constructed_wetland.svg" alt=""><br />
						Constructed Wetlands - Surface
					</a>
				</div>
				<div class="technology">
					<a href="{{url('/tech/groundwater/2')}}"  class="popdown">
						<img src="http://www.cch2o.org/Matrix/icons/constructed_wetland.svg" alt=""><br />
						Constructed Wetlands - Subsurface Flow
					</a>
				</div>
				<div class="technology">
					<a href="{{url('/tech/groundwater/3')}}"  class="popdown">
						<img src="http://www.cch2o.org/Matrix/icons/constructed_wetland.svg" alt=""><br />
						Constructed Wetlands - Groundwater Flow
					</a>
				</div>	
				<div class="technology">
					<a href="{{url('/tech/groundwater/14')}}"  class="popdown">
						<img src="http://www.cch2o.org/Matrix/icons/phytoremediation.svg" alt=""><br />
						Phytoremediation
					</a>
				</div>	
				<div class="technology">
					<a href="{{url('/tech/groundwater/16')}}"  class="popdown">
						<img src="http://www.cch2o.org/Matrix/icons/prb.svg" alt=""><br />
						PRB - Injection Well
					</a>
				</div>		

				<div class="technology">
					<a href="{{url('/tech/groundwater/19')}}"  class="popdown">
						<img src="http://www.cch2o.org/Matrix/icons/fertigation_wells.svg" alt=""><br />
						Fertigation Wells
					</a>
				</div>																	
			</div>		
		</div>
	</article>
	</aside>
	<aside class="accHorizontal__item">
	<input type="radio" name="group-1" class="state" id="acc-5" />
	<label class="backdrop" for="acc-5"><!-- <i class="fa fa-times"></i> --></label>
	<article class="acc_cBox">
		<div class="acc_cImg">
		<!-- <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/308355/img-4.jpg" alt="" /> -->
		<header>
			<h3>Embayment</h3>
			<img src="http://www.cch2o.org/Matrix/icons/restoration.svg" alt="">
			<!-- <p>Web Designer</p> -->
		</header>
		</div>
		<div class="acc_cCont">

			<div class="technology">
				<a href="{{url('/tech/embayment/30')}}"  class="popdown">
					<img src="http://www.cch2o.org/Matrix/icons/inlet_culvert_widening.svg" alt=""><br />
					Inlet/Culvert Widening
				</a>
			</div>
			<div class="technology">
				<a href="{{url('/tech/embayment/31')}}"  class="popdown">
					<img src="http://www.cch2o.org/Matrix/icons/coastal_habitat_restoration.svg" alt=""><br />
					Coastal Habitat Restoration
				</a>
			</div>


		</div>
	</article>
	</aside>
	
	<aside class="accHorizontal__item">
	<input type="radio" name="group-1" class="state" id="acc-6" />
	<label class="backdrop" for="acc-6"><!-- <i class="fa fa-times"></i> --></label>
	<article class="acc_cBox">
		<div class="acc_cImg">
		<!-- <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/308355/img-4.jpg" alt="" /> -->
		<header>
			<h3>Summary</h3>
			<!-- <p>Web Designer</p> -->
		</header>
		</div>
		<div class="acc_cCont">
		<!-- <p>Existing Nitrogen Load: kg</p> -->
		<p>Overall Nitrogen Reduction: 9862kg</p>
			<p>Overall Cost: $374,985</p>
			<p>Download Results: <a href="#">.xls</a> or <a href="">.xml</a></p>
			<p><a href="/results" class="button">View detailed results</a></p>
		</div>
	</article>
	</aside>
</section>
</div>