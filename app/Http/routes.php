<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
// TODO: Apply standard Laravel naming convention to routes


Route::group(['middleware' => 'auth'], function () {
   
Route::get('/start', 'StartController@index');

Route::get('/map/{embayment}/{scenarioid?}', 'WizardController@start');

Route::post('/poly', 'WizardController@getPolygon2');

// TODO: Rename MarioConroller to represent Welcome blade, eg. WelcomeController
// TODO: Rename /poly# routes to represent actual function
Route::post('/sumTotalsWithinPolygon', 'WelcomeController@sumTotalsWithinPoly');
Route::post('/getIDArrayWithinPolygon', 'WelcomeController@getIDArrayWithinPoly');

Route::post('/update_polygon', 'TechnologyController@updatePolygon');

Route::get('/save/{id}', 'ScenarioController@saveScenario');

// TODO: Is this route real?
// Just leaving this here in case it is referenced somewhere in the code
Route::get('/testmap/Nitrogen/{treatment}/{poly}', 'WizardController@getPolygon');

// TODO: Fix 'delete_treatment' to delete/{treatment}/{type?}
Route::get('/tech/{type}/{tech}', 'TechnologyController@get');
Route::get('/edit/{treatment}', 'TechnologyController@edit');
Route::get('/update/{type}/{treatment}/{rate}/{units?}/{subemid?}', 'TechnologyController@update');
Route::get('/delete_treatment/{treatment}/{type?}', 'TechnologyController@delete');
Route::get('/cancel/{treatment}', 'TechnologyController@cancel');


Route::get('/delete_scenario/{scenarioid}', 'ScenarioController@deleteScenario');

Route::get('/apply_percent/{treatment}/{rate}/{type}/{units?}', 'TechnologyController@ApplyTreatment_Percent');

// TODO: Is location optional for Stormwater Management?
// NO: It applies to SW treatments and not Management
Route::get('/apply_storm/{treatment}/{rate}/{units}/{location}', 'TechnologyController@ApplyTreatment_Storm');

Route::get('/apply_septic/{treatment}/{rate}', 'TechnologyController@ApplyTreatment_Septic');
Route::get('/apply_embayment/{treatment}/{rate}/{units}/{subemid?}', 'TechnologyController@ApplyTreatment_Embayment');
Route::get('/apply_groundwater/{treatment}/{rate}/{units}', 'TechnologyController@ApplyTreatment_Groundwater');

Route::get('/tech-collect/{tech}', 'TechnologyController@getCollection');

Route::get('/polygon/{type}/{treatment}/{polygon}', 'TechnologyController@getPolygon');

Route::get('/map/point/{x}/{y}/{treatment}', 'MapController@point');
Route::get('/map/move/{x}/{y}/{treatment}', 'MapController@moveNitrogen');

Route::get('/getScenarioNitrogen', 'ScenarioController@GetScenarioNitrogen');
Route::get('/getScenarioProgress', 'ScenarioController@getCurrentProgress');

Route::get('/results/{scenarioid}', 'ScenarioController@getScenarioResults');
Route::get('/download/{scenarioid}', 'ScenarioController@downloadScenarioResults');

Route::get('progress', 'ScenarioController@getProgress');

Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index');
});



Route::auth();


Route::get('/help', function(){
	return view('help');
});

