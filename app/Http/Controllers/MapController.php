<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use DB;
use JavaScript;

class MapController extends Controller
{
	//

	/**
	 * Return the subembayment & subwatershed for the point sent (x,y)
	 *
	 * @return void
	 * @author 
	 **/
	public function point($x, $y, $treatment)
	{
		DB::connection('sqlsrv')->statement('SET ANSI_NULLS, QUOTED_IDENTIFIER, CONCAT_NULL_YIELDS_NULL, ANSI_WARNINGS, ANSI_PADDING ON');
		$subembayment = DB::select("exec [CapeCodMA].[GET_Subembayment_from_Point] @x='$x', @y='$y'");
		// $subwatershed = DB::select("exec [CapeCodMA].[GET_Subwatershed_from_Point] @x='$x', @y='$y'");
		// dd($subembayment);
		
		// JavaScript::put([
		// 	// 'subembayment' => $subembayment[0],
		// 	// 'subwatershed' => $subwatershed[0]
		// ]);
		
  
		// need to create a new record in the treatment_wiz table with the destination of the Nitrogen and the parent_treatment_id
		// use point as the polygon value; use treatment as parent_treatment_id
		// need to add the Nitrogen to the selected destination and have it ADDED to that subembayment's total
		$scenarioid = session('scenarioid');
		$move = DB::select("exec CapeCodMA.CALC_MoveNitrogen '$x', '$y', $treatment, $scenarioid");
		// dd($move);

		return json_encode($subembayment[0]);
	}
}
