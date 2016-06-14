var map;

var watershed;
var embay_shape;
var treatment;
var func;
var treatment_polygons = new Array();
require([
		"esri/map",
		"esri/dijit/BasemapGallery",
		"esri/arcgis/utils",
		"dojo/parser",
		"esri/layers/ArcGISDynamicMapServiceLayer",
		"esri/layers/ImageParameters",
		"esri/layers/FeatureLayer",

		"esri/toolbars/draw",
		// "esri/toolbars/edit",
		"esri/symbols/SimpleFillSymbol",
		"esri/symbols/SimpleLineSymbol",
		"esri/graphic",
		"esri/Color",

		"esri/tasks/query",
		"esri/tasks/QueryTask",
		"esri/dijit/LayerList",
		"esri/geometry/Extent",


		// "esri/SpatialReference",
		// "dijit/layout/BorderContainer", 
		// "dijit/layout/ContentPane", 
		// "dijit/TitlePane",

		"dojo/dom",
		"dojo/on",
		"dojo/dom-construct",
		"dojo/domReady!"
	],
	function(
		Map,
		BasemapGallery, 
		arcgisUtils,
		parser,
		ArcGISDynamicMapServiceLayer,
		ImageParameters,
		FeatureLayer,

		Draw,
		// Edit,
		SimpleFillSymbol,
		SimpleLineSymbol,
		Graphic,
		Color,

		Query,
		QueryTask,
		LayerList,
		Extent,
		dom, on,
		domConstruct
	) {
		parser.parse();

		var initialExtent = new Extent({ "xmin": -7980970.14, "ymin": 5033003.02, "xmax": -7705796.84, "ymax": 5216451.89, "spatialReference": { "wkid": 102100 } });
		map = new Map("map", {
			center: [-70.35, 41.68],
			// extent: initialExtent,
			zoom: 11,
			basemap: "gray",
			slider: true,
			sliderOrientation: "horizontal"
		});
		// map.on("load", createToolbar);
		map.on("load", initToolbar);

		var fillSymbol = new SimpleFillSymbol();

		function initToolbar() {
			tb = new Draw(map);
			tb.on("draw-end", addGraphic);


// map.graphics.add(new Graphic(treat, polygon));
			
			on(dom.byId("info"), "click", function(evt) {
				if (evt.target.id === "info") {
					return;
				}

				var tool = evt.target.id.toLowerCase();
				map.disableMapNavigation();
				tb.activate(tool);
			});
		}


		/***********************************
			Need to have an array of custom polygons so we can access them later
			See 208 viewer for example with technology icon & color coding

		************************************/
		function addGraphic(evt) {
			//deactivate the toolbar and clear existing graphics 
			tb.deactivate();
			map.enableMapNavigation();

			// figure out which symbol to use
			var symbol;
			symbol = fillSymbol;
			var polystring = '';
			map.graphics.add(new Graphic(evt.geometry, symbol));
			// console.log(evt.geometry);
			// console.log('entering loop');


			for (var i = 0; i < evt.geometry.rings[0].length; i++) {
				polystring += evt.geometry.rings[0][i][0] + ' ';
				polystring += evt.geometry.rings[0][i][1] + ', ';
			}
			var len = polystring.length;
			polystring = polystring.substring(0, len - 2);
			treatment_polygons[treatment] = polystring;
			// console.log('exec CapeCodMa.Get_NitrogenFromPolygon \'' + polystring + '\'');

			// console.log(polystring);
			var url = "/testmap/Nitrogen" + '/' + treatment + '/' + polystring;
			// var url = '/polygon/' + func + '/' + treatment + '/' + polystring;
			// console.log(url);
			$.ajax({
					method: 'GET',
					url: url
				})
				.done(function(msg) {

					$('#total_nitrogen_polygon').text(msg);
					$('#popdown-opacity').show();
					
				});

			// console.log(symbol);
			var area = evt.geometry.getExtent();
			// console.log(area);
			// map.centerAndZoom(area, 11);
			console.log(treatment_polygons);
		}

	
		/*******************************
		 *
		 *	This is the ArcGIS Basemap Gallery which (used to) breaks everything
		 *
		 *********************************/

		var basemapGallery = new BasemapGallery({
		       showArcGISBasemaps: true,
		       map: map
		     }, "basemapGallery");
		     basemapGallery.startup();

		     basemapGallery.on("error", function(msg) {
		       console.log("basemap gallery error:  ", msg);
		     });


		var extent;

		/*******************************
		 *
		 *	We will need to add any existing treatment polygons to the map
		 *  We need to break the custom polygon string into an array of x, y points
		 *  see http://www.cch2o.org/TBL/208viewer_wiz.php?id=234 code below:
		 * 

		 $poly = explode(',', $poly);

			foreach($poly as $test){
				$x = strstr($test, " ", true);
				$y = strstr($test, " ");
				$node = [$x,$y];
				$geometry[] = $node;
		 *

		 *	Then see 208 viewer (index-1.html example file)
		 *	function buildScenarioPolygons(thisScenario, i)  starting on line 729
		 *
		 *	Line 1130 starts the custom polygon for the treatment
		 *

		 *********************************/



		var embayments = new FeatureLayer('http://gis-services.capecodcommission.org/arcgis/rest/services/wMVP/wMVP3/MapServer/4', {
			mode: FeatureLayer.MODE_ONDEMAND,
			outFields: ["*"],
			// maxAllowableOffset: map.extent,
			opacity: 1
		});
		embayments.setDefinitionExpression('EMBAY_ID = ' + selectlayer);

		map.addLayer(embayments);
		// var point = (embayments.X_Centroid, embayments.Y_Centroid);
		// map.centerAndZoom(point, 11);
		// map.setExtent(embayments.fullExtent);


		var Subwatersheds = new FeatureLayer("http://gis-services.capecodcommission.org/arcgis/rest/services/wMVP/wMVP3/MapServer/6", {
			mode: FeatureLayer.MODE_ONDEMAND,
			outFields: ["*"],
			// maxAllowableOffset: map.extent,
			opacity: 1
		});
		Subwatersheds.setDefinitionExpression('EMBAY_ID = ' + selectlayer);

		Subwatersheds.hide();
		// Subwatersheds.setExtent(extent);
		map.addLayer(Subwatersheds);


		var Subembayments = new FeatureLayer("http://gis-services.capecodcommission.org/arcgis/rest/services/wMVP/wMVP3/MapServer/11", {
			mode: FeatureLayer.MODE_ONDEMAND,
			outFields: ["*"],
			opacity: 1
		});
		Subembayments.setDefinitionExpression('EMBAY_ID = ' + selectlayer);

		Subembayments.hide();
		// console.log(Subembayments);
		map.addLayer(Subembayments);

		var NitrogenLayer = new FeatureLayer('http://gis-services.capecodcommission.org/arcgis/rest/services/wMVP/wMVP3/MapServer/0', {
				mode: FeatureLayer.MODE_ONDEMAND,
				outFields: ["*"],
				opacity: 1
			}

		);
		NitrogenLayer.setDefinitionExpression('Embay_id = ' + selectlayer);
		NitrogenLayer.hide();
		map.addLayer(NitrogenLayer);


		var WasteWater = new FeatureLayer('http://gis-services.capecodcommission.org/arcgis/rest/services/wMVP/wMVP3/MapServer/1', {
				mode: FeatureLayer.MODE_ONDEMAND,
				outFields: ["*"],
				opacity: 1
			}

		);
		WasteWater.setDefinitionExpression('EMBAY_ID = ' + selectlayer);

		WasteWater.hide();
		map.addLayer(WasteWater);


		var Towns = new FeatureLayer('http://gis-services.capecodcommission.org/arcgis/rest/services/wMVP/wMVP3/MapServer/5', {
				mode: FeatureLayer.MODE_ONDEMAND,
				outFields: ["*"],
				opacity: .4,
				// styling: false,
				color: [255, 0, 0, 1],
				width: 3
			}

		);
		Towns.hide();
		map.addLayer(Towns);


		var TreatmentType = new FeatureLayer('http://gis-services.capecodcommission.org/arcgis/rest/services/wMVP/wMVP3/MapServer/10', {
				mode: FeatureLayer.MODE_ONDEMAND,
				outFields: ["*"],
				opacity: 1
			}

		);
		TreatmentType.setDefinitionExpression('EMBAY_ID = ' + selectlayer);

		TreatmentType.hide();
		map.addLayer(TreatmentType);


		var TreatmentFacilities = new FeatureLayer('http://gis-services.capecodcommission.org/arcgis/rest/services/wMVP/wMVP3/MapServer/9', {
				mode: FeatureLayer.MODE_ONDEMAND,
				outFields: ["*"],
				opacity: 1
			}

		);
		TreatmentFacilities.hide();
		map.addLayer(TreatmentFacilities);


		var EcologicalIndicators = new FeatureLayer('http://gis-services.capecodcommission.org/arcgis/rest/services/Projects/208_Plan/MapServer/10', {
				mode: FeatureLayer.MODE_ONDEMAND,
				outFields: ["*"],
				opacity: 1
			}

		);
		EcologicalIndicators.hide();
		map.addLayer(EcologicalIndicators);

		var ShallowGroundwater = new FeatureLayer('http://gis-services.capecodcommission.org/arcgis/rest/services/Projects/208_Plan/MapServer/32', {
		        mode: FeatureLayer.MODE_ONDEMAND,
		        outFields: ["*"],
		        opacity: .5
		    }

		);
		ShallowGroundwater.hide();
		map.addLayer(ShallowGroundwater);


		var LandUse = new FeatureLayer('http://gis-services.capecodcommission.org/arcgis/rest/services/wMVP/wMVP3/MapServer/3', {
				mode: FeatureLayer.MODE_ONDEMAND,
				outFields: ["*"],
				opacity: .5
			}

		);
		LandUse.setDefinitionExpression('EMBAY_ID = ' + selectlayer);

		LandUse.hide();
		map.addLayer(LandUse);

		var FlowThrough = new FeatureLayer('http://gis-services.capecodcommission.org/arcgis/rest/services/wMVP/wMVP3/MapServer/12', {
			mode: FeatureLayer.MODE_ONDEMAND,
			outFields: ["*"]
				// opacity: 1	
		});
		FlowThrough.hide();
		map.addLayer(FlowThrough);


		// console.log('testing');
		// Turn on/off each layer when the user clicks the link in the sidebar.

		$('#nitrogen').on('click', function(e) {
			e.preventDefault();
			// console.log(NitrogenLayer);
			if ($(this).attr('data-visible') == 'off') {
				NitrogenLayer.show();
				$(this).attr('data-visible', 'on');
			} else {
				NitrogenLayer.hide();
				$(this).attr('data-visible', 'off');
			}
			// 
		});


		$('#subembayments').on('click', function(e) {
			e.preventDefault();

			if ($(this).attr('data-visible') == 'off') {
				Subembayments.show();
				// console.log(Subembayments);
				$(this).attr('data-visible', 'on');
			} else {
				Subembayments.hide();
				$(this).attr('data-visible', 'off');
			}
			// 
		});

		$('#subwatersheds').on('click', function(e) {
			e.preventDefault();

			if ($(this).attr('data-visible') == 'off') {
				Subwatersheds.show();
				$(this).attr('data-visible', 'on');
			} else {
				Subwatersheds.hide();
				$(this).attr('data-visible', 'off');
			}
			// 
		});

		$('#wastewater').on('click', function(e) {
			e.preventDefault();

			if ($(this).attr('data-visible') == 'off') {
				WasteWater.show();
				$(this).attr('data-visible', 'on');
			} else {
				WasteWater.hide();
				$(this).attr('data-visible', 'off');
			}
			// 
		});


		$('#towns').on('click', function(e) {
			e.preventDefault();

			if ($(this).attr('data-visible') == 'off') {
				Towns.show();
				$(this).attr('data-visible', 'on');
			} else {
				Towns.hide();
				$(this).attr('data-visible', 'off');
			}
			// 
		});

		$('#treatmenttype').on('click', function(e) {
			e.preventDefault();

			if ($(this).attr('data-visible') == 'off') {
				TreatmentType.show();
				$(this).attr('data-visible', 'on');
			} else {
				TreatmentType.hide();
				$(this).attr('data-visible', 'off');
			}
			// 
		});

		$('#treatmentfacilities').on('click', function(e) {
			e.preventDefault();

			if ($(this).attr('data-visible') == 'off') {
				TreatmentFacilities.show();
				$(this).attr('data-visible', 'on');
			} else {
				TreatmentFacilities.hide();
				$(this).attr('data-visible', 'off');
			}
			// 
		});


		$('#ecologicalindicators').on('click', function(e) {
			e.preventDefault();

			if ($(this).attr('data-visible') == 'off') {
				EcologicalIndicators.show();
				$(this).attr('data-visible', 'on');
			} else {
				EcologicalIndicators.hide();
				$(this).attr('data-visible', 'off');
			}
			// 
		});

		$('#shallowgroundwater').on('click', function(e) {
			e.preventDefault();

			if ($(this).attr('data-visible') == 'off') {
				ShallowGroundwater.show();
				$(this).attr('data-visible', 'on');
			} else {
				ShallowGroundwater.hide();
				$(this).attr('data-visible', 'off');
			}
			// 
		});

		$('#landuse').on('click', function(e) {
			e.preventDefault();

			if ($(this).attr('data-visible') == 'off') {
				LandUse.show();
				$(this).attr('data-visible', 'on');
			} else {
				LandUse.hide();
				$(this).attr('data-visible', 'off');
			}
			// 
		});


		$('#flowthrough').on('click', function(e) {
			e.preventDefault();

			if ($(this).attr('data-visible') == 'off') {
				FlowThrough.show();
				$(this).attr('data-visible', 'on');
			} else {
				FlowThrough.hide();
				$(this).attr('data-visible', 'off');
			}
			// 
		});

	});
