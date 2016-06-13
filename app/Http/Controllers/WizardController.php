<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Embayment;
use DB;
use JavaScript;
use App\Treatment;
use Session;

class WizardController extends Controller
{
	//
	public function start($id, $scenarioid = null)
	{
		$embayment = Embayment::find($id);
		// Need to create a new scenario or find existing one that the user is editing
		// Is user logged in?	
		if(!$scenarioid)
		{
			$scenarioid = DB::select('exec CapeCodMA.CreateScenario ' . $id);
		// dd($scenarioid[0]->scenarioid);
			$scenarioid = $scenarioid[0]->scenarioid;
			Session::put('scenarioid', $scenarioid);
			Session::put('embay_id', $id);
			Session::put('n_removed', 0);
		}
		else
		{
			Session::put('scenarioid', $scenarioid);
		}

		$treatments = DB::select('select * from CapeCodMA.Treatment_Wiz where scenarioid = '. $scenarioid);
			
		$subembayments = DB::select('exec CapeCodMA.GET_SubembaymentNitrogen ' . $id);
		$total_goal = 0;
		foreach ($subembayments as $key) {
			$total_goal += $key->n_load_target;
		}

		$nitrogen = DB::select('exec CapeCodMA.GET_AreaNitrogen_Unattenuated ' . $id);
		$nitrogen_att = DB::select('exec CapeCodMA.GET_AreaNitrogen_attenuated ' . $id);
		// dd($nitrogen);
		JavaScript::put([
				'nitrogen_unatt' => $nitrogen[0],
				'nitrogen_att' => $nitrogen_att[0]
			]);
		

		return view('layouts/wizard', ['embayment'=>$embayment, 'subembayments'=>$subembayments, 'nitrogen_att'=>$nitrogen_att[0], 'nitrogen_unatt'=>$nitrogen[0], 'goal'=>$total_goal, 'treatments'=>$treatments]);

	}

	/**
	 * Test page to show all Nitrogen values for Embayment
	 *
	 * @return void
	 * @author 
	 **/
	

	public function test($id)
	{
		$embayment = Embayment::find($id);
		// $subembayments = DB::table('CapeCodMA.SubEmbayments')
		// 	->select('SUBEM_NAME', 'SUBEM_DISP', 'Nload_Total', 'Total_Tar_Kg', 'MEP_Total_Tar_Kg')
		// 	->where('EMBAY_ID', $embayment->EMBAY_ID)->get();
			$subembayments = DB::select('exec CapeCodMA.GET_SubembaymentNitrogen ' . $id);
		$nitrogen = DB::select('exec CapeCodMA.GET_EmbaymentNitrogen ' . $id);

		 JavaScript::put([
				'nitrogen' => $nitrogen[0]
			]);

		return view('layouts/test', ['embayment'=>$embayment, 'subembayments'=>$subembayments]);
	}


	/**
	 * Get Nitrogen Totals from a polygon string
	 *
	 * @return void
	 * @author 
	 **/
	public function getPolygon($treatment_id, $poly)
	{
	   DB::connection('sqlsrv')->statement('SET ANSI_NULLS, QUOTED_IDENTIFIER, CONCAT_NULL_YIELDS_NULL, ANSI_WARNINGS, ANSI_PADDING, NOCOUNT ON');

		// $nitrogen_totals = DB::select('exec CapeCodMA.GET_NitrogenFromPolygon \'' . $poly . '\'');
		// dd($nitrogen_totals[0]);
		$scenarioid = session('scenarioid');
		$embay_id = session('embay_id');
		// dd($embay_id, $scenarioid);
		$parcels = DB::select('exec CapeCodMA.GET_PointsFromPolygon ' . $embay_id . ', ' . $scenarioid . ', ' . $treatment_id . ', \'' . $poly . '\'');
		// dd($parcels);
		$poly_nitrogen = $parcels[0]->Septic;

		// dd($parcels);
		JavaScript::put([
				'poly_nitrogen' => $parcels
			]);


		/**********************************************
		*	We need to get the total Nitrogen for the custom polygon that this technology will treat 
		*	(fertilizer, stormwater, septic, groundwater, etc.)
		*	and report that back to the technology pop-up. After the user adjusts the treatment settings
		*	we need to save that as "treated_nitrogen" and be able to attenuate it 
		*	If this is a collection & treat (sewer) then we will need to 
		*	create a new treatment record with a parent_treatment_id so we 
		*	can store the N load and the destination point where it will be treated.
		*
		**********************************************/

		// $treatment = Treatment::find($treatment_id);
		// $treatment->POLY_STRING = $poly;
		// $treatment->Custom_POLY = 1;
		// $treatment->save();
		// dd($treatment);
		// $total_septic_nitrogen = $parcels;
		// foreach ($parcels as $parcel) 
		// {
		// 	$total_septic_nitrogen += $parcel->wtp_nload_septic;
		// }

		return $parcels;
		// return view ('layouts/test_septic', ['parcels'=>$parcels, 'poly_nitrogen'=>$poly_nitrogen]);
	}


	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function getScenarioResults($scenarioid)
	{	
		// $scenarioid = session('scenarioid');
		$embay_id = session('embay_id');
		$results = DB::select('exec CapeCodMA.Get_ScenarioResults '. $scenarioid);
		$towns = DB::select('select wtt.*, t.town from dbo.wiz_treatment_towns wtt inner join capecodma.matowns t on t.town_id = wtt.wtt_town_id
  where wtt.wtt_scenario_id = ' . $scenarioid);
		$subembayments = DB::select('exec CapeCodMA.Calc_ScenarioNitrogen_Subembayments ' . $scenarioid);
		// Need to calculate all the treatments applied and Nitrogen removed from this scenario

		return view('layouts/results', ['results'=>$results, 'scenarioid'=>$scenarioid, 'embay_id'=>$embay_id, 'towns'=>$towns, 'subembayments'=>$subembayments]);

	}

	/**
	 * GetScenarioNitrogen
	 *
	 * @return void
	 * @author 
	 **/
	function GetScenarioNitrogen()
	{
		$scenarioid = session('scenarioid');
		$Nitrogen = DB::select('exec capecodma.calc_scenarioNitrogen ' . $scenarioid);
		return $Nitrogen;
	}
	
}
