<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use DB;
use App\Treatment;
use App\Embayment;
use Session;

class TechnologyController extends Controller
{

	/**
	 * Get the technology requested
	 *
	 * @return void
	 * @author 
	 **/
	public function get($type, $id)
	{

		DB::connection('sqlsrv')->statement('SET ANSI_NULLS, QUOTED_IDENTIFIER, CONCAT_NULL_YIELDS_NULL, ANSI_WARNINGS, ANSI_PADDING ON');
		$tech = DB::table('dbo.Technology_Matrix')->select('*')->where('TM_ID', $id)->first();
		$scenarioid = session('scenarioid');
		$treatment = Treatment::create(['ScenarioID' => $scenarioid, 'TreatmentType_ID'=>$tech->TM_ID, 'TreatmentType_Name'=>$tech->Technology_Strategy]);

		if ($tech->Show_In_wMVP == 4) 
		{
			// this is embayment-wide, need to get the embayment_area and use that as the custom polygon for the Get_PointsfromPolygon
			$embay_id = session('embay_id');
			$parcels = DB::select('exec CapeCodMA.GET_PointsFromPolygon ' . $embay_id . ', ' . $scenarioid . ', ' . $treatment->TreatmentID . ', \'embayment\'');
		}
		// create a new record in the treatment_wiz table for this scenario & technology
		// get the treatmentID back and use that for the treatment_parcels table

		switch ($type) {
			case 'fert':
				return view('common/technology-fertilizer', ['tech'=>$tech, 'treatment'=>$treatment, 'type'=>$type]);
				break;
			case 'storm':
				return view('common/technology-stormwater', ['tech'=>$tech, 'treatment'=>$treatment, 'type'=>$type]);
				break;
			case 'collect':
				return view('common/technology-collection', ['tech'=>$tech, 'treatment'=>$treatment, 'type'=>$type]);
				break;		
			case 'septic':
				return view('common/technology-septic', ['tech'=>$tech, 'treatment'=>$treatment, 'type'=>$type]);
				break;
			case 'groundwater':
				return view('common/technology-groundwater', ['tech'=>$tech, 'treatment'=>$treatment, 'type'=>$type]);
				break;
			case 'embayment':
				return view('common/technology-embayment', ['tech'=>$tech, 'treatment'=>$treatment, 'type'=>$type]);
				break;
			default:
				return view('common/technology', ['tech'=>$tech, 'treatment'=>$treatment, 'type'=>$type]);
				break;
		}
		

		
	}

	public function getCollection($id)
	{
		$scenarioid = session('scenarioid');
		// dd($scenarioid);
		$tech = DB::table('dbo.Technology_Matrix')->select('*')->where('TM_ID', $id)->get();
		
		$treatment = Treatment::create(['ScenarioID' => $scenarioid, 'TreatmentType_ID'=>$tech[0]->TM_ID]);

		return view('common/technology-collection', ['tech'=>$tech[0], 'treatment'=>$treatment]);
	}


	/**
	 * Apply Treatment Percent
	 *
	 * @return void
	 * @author 
	 **/
	public function ApplyTreatment_Percent($treat_id, $rate, $type, $units = null)
	{
		
		$scenarioid = session('scenarioid');
		$rate = round($rate, 2);
		// $rate = number_format($rate, 2, "." , "" );
		// dd($rate);
		
		if ($type == 'fert') {
			$updated = DB::select('exec [CapeCodMA].[CALC_ApplyTreatment_Fert] ' . $treat_id . ', ' . $rate );
			Session::put('fert_applied', 1);
		}

		// Storm treatments with a unit metric (acres) need a different calculation
		// Also need to store the point where the user indicated the treatment would be located
		else if ($type == 'storm' && $units > 0) 
		{
			$updated = DB::select('exec [CapeCodMA].[CALC_ApplyTreatment_Storm] ' . $treat_id . ', ' . $rate . ', ' . $units );

		}

		// this is probably stormwater management policies, flat percent
		else 	
		{
			$updated = DB::select('exec CapeCodMA.CALC_ApplyTreatment_Percent ' . $treat_id . ', ' . $rate . ', storm' );
			Session::put('storm_applied', 1);
		}

		$n_removed = session('n_removed');
		$n_removed += $updated[0]->removed;
		Session::put('n_removed', $n_removed);
		
		return $n_removed;

	}



	/**
	 * Apply Treatment Percent
	 *
	 * @return void
	 * @author 
	 **/
	public function ApplyTreatment_Storm($treat_id, $rate, $units = null, $location = null)
	{
		
		$scenarioid = session('scenarioid');

		// Storm treatments with a unit metric (acres) need a different calculation
		// Also need to store the point where the user indicated the treatment would be located
	 if ( $units > 0) 
		{
			$updated = DB::select('exec [CapeCodMA].[CALC_ApplyTreatment_Storm] ' . $treat_id . ', ' . $rate . ', ' . $units . ', ' . $location );
		}

		// this is probably stormwater management policies, flat percent
		else 	
		{
			$updated = DB::select('exec CapeCodMA.CALC_ApplyTreatment_Percent ' . $treat_id . ', ' . $rate);
		}

		$n_removed = session('n_removed');
		$n_removed += $updated[0]->removed;
		Session::put('n_removed', $n_removed);
		
		return $n_removed;

	}







	/**
	 * Apply Septic Treatment
	 *
	 * @return void
	 * @author 
	 **/
	public function ApplyTreatment_Septic($treat_id, $rate)
	{		
		//$scenarioid = session('scenarioid');
		
		$updated = DB::select('exec [CapeCodMA].[CALC_ApplyTreatment_Septic] ' . $treat_id . ', ' . $rate );
		
		$n_removed = session('n_removed');
		$n_removed += $updated[0]->removed;
		Session::put('n_removed', $n_removed);
		
		return $n_removed;

	}

	/**
	 * Based on the type of treatment, use the polygon to determine the Nitrogen being treated
	 *
	 * @return void
	 * @author 
	 **/
	public function getPolygon($type, $treatment_id, $poly)
	{
		

		$scenarioid = session('scenarioid');
		dd($scenarioid);

		$embay_id = session('embay_id');
		DB::connection('sqlsrv')->statement('SET ANSI_NULLS, QUOTED_IDENTIFIER, CONCAT_NULL_YIELDS_NULL, ANSI_WARNINGS, ANSI_PADDING ON');
		if ($type == 'septic') 
		{
			// we need to know how many toilets/parcels will be implemented
			$parcels = DB::select('exec CapeCodMA.GET_PointsFromPolygon_Septic ' . $embay_id . ', ' . $scenarioid . ', ' . $treatment_id . ', \'' . $poly . '\'');

			return $parcels[0];
		}
		else if ($type == 'collect') 
		{
			// we need to know how many toilets/parcels will be implemented
			$parcels = DB::select('exec CapeCodMA.GET_PointsFromPolygon ' . $embay_id . ', ' . $scenarioid . ', ' . $treatment_id . ', \'' . $poly . '\'');
			dd($parcels);
			return $parcels[0];
		}

		// dd($embay_id, $scenarioid);
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

		return $poly_nitrogen;
		// return view ('layouts/test_septic', ['parcels'=>$parcels, 'poly_nitrogen'=>$poly_nitrogen]);		
	}


	/**
	 * User wants to cancel a treatment for this scenario
	 *
	 * @return void
	 * @author 
	 **/
	public function cancel($treat_id)
	{

		DB::connection('sqlsrv')->statement('SET ANSI_NULLS, QUOTED_IDENTIFIER, CONCAT_NULL_YIELDS_NULL, ANSI_WARNINGS, ANSI_PADDING ON');
		$del = DB::select('exec CapeCodMA.DEL_Treatment '. $treat_id);

		return 1;
	}


	/**
	 * User wants to edit a treatment for this scenario
	 *
	 * @return void
	 * @author 
	 **/
	public function edit($treat_id)
	{
		$treatment = Treatment::find($treat_id);
		$tech = DB::table('dbo.Technology_Matrix')->select('*')->where('TM_ID', $treatment->TreatmentType_ID)->first();
		$type = $tech->Technology_Sys_Type;
				$toilets = [21, 22, 23, 24];
		if (in_array($treatment->TreatmentType_ID, $toilets) ) 
		{
			return view('common/technology-septic-edit', ['tech'=>$tech, 'treatment'=>$treatment, 'type'=>$type]);
			break;
		}
		else 
		{
			switch ($type) 
			{
				case 'Fertilization':
					return view('common/technology-fertilizer-edit', ['tech'=>$tech, 'treatment'=>$treatment, 'type'=>$type]);
					break;
				case 'Stormwater':
					return view('common/technology-stormwater-edit', ['tech'=>$tech, 'treatment'=>$treatment, 'type'=>$type]);
					break;
				case 'Septic/Sewer':
					return view('common/technology-collection-edit', ['tech'=>$tech, 'treatment'=>$treatment, 'type'=>$type]);
					break;		
				// case 'septic':
				// 	return view('common/technology-septic-edit', ['tech'=>$tech, 'treatment'=>$treatment, 'type'=>$type]);
				// 	break;
				case 'groundwater':
					return view('common/technology-groundwater', ['tech'=>$tech, 'treatment'=>$treatment, 'type'=>$type]);
					break;
				case 'embayment':
					return view('common/technology-embayment', ['tech'=>$tech, 'treatment'=>$treatment, 'type'=>$type]);
					break;
				default:
					return view('common/technology', ['tech'=>$tech, 'treatment'=>$treatment, 'type'=>$type]);
					break;
			}
		}


		return view('common/technology-septic-edit', ['treatment'=>$treatment, 'tech'=>$tech]);

	}

	/**
	 * User updated an existing treatment for this scenario
	 *
	 * @return void
	 * @author 
	 **/
	public function update($type, $treat_id, $rate, $units=null)
	{
		$treatment = Treatment::find($treat_id);
			switch ($type) 
			{
				case 'fert':
					$updated = DB::select('exec [CapeCodMA].[CALC_ApplyTreatment_Fert] ' . $treat_id . ', ' . $rate );
					return $updated;	
					break;

				case 'storm-percent':
					$updated = DB::select('exec CapeCodMA.CALC_ApplyTreatment_Percent ' . $treat_id . ', ' . $rate . ', storm' );
					return $updated;
					break;

				case 'storm':
					$updated = DB::select('exec [CapeCodMA].[CALC_UpdateTreatment_Storm] ' . $treat_id . ', ' . $rate . ', ' . $units );
					return $updated;
					break;

				case 'toilets':
					$updated = DB::select('exec [CapeCodMA].[CALC_ApplyTreatment_Septic] '. $treat_id . ', '. $rate);
					return $updated;
					break;

				case 'collect':
					
					break;		
				case 'septic':
					
					break;
				case 'groundwater':
					
					break;
				case 'embayment':
					
					break;
				default:
					
					break;
			}


	}

		/**
	 * User wants to delete a treatment for this scenario
	 *
	 * @return void
	 * @author 
	 **/
	public function delete($treat_id)
	{
		DB::connection('sqlsrv')->statement('SET ANSI_NULLS, QUOTED_IDENTIFIER, CONCAT_NULL_YIELDS_NULL, ANSI_WARNINGS, ANSI_PADDING ON');
		// Need to remove all records in wiz_treatment_parcels and wiz_treatment_towns for this treatment_id
		$del = DB::select('exec CapeCodMA.DEL_Treatment '. $treat_id);
		// Treatment::destroy($treat_id);
		return 1;
		// return view('common/technology-septic-edit', ['treatment'=>$treatment]);

	}


}