  <!DOCTYPE html>
  <html>
  	<head>
  		<title>Place searches</title>
  		<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
  		<meta charset="utf-8">
  		<style type="text/css">
  			* {
  				-webkit-box-sizing: border-box;
          -moz-box-sizing: border-box;
          box-sizing: border-box;
  			}

  			html, body {
      			margin: 0px;
      			padding: 0px;
    		}

        body {
          text-align:center;
        }

        #top {
          width:90%;
          margin:auto;
        }

        #poiBox {
          position:absolute;
          right:10%;
          top:20px;
          z-index:1;
        }

        #map-canvas {
          width:90%;
          height:400px;
          border:1px solid #610585;
          border-radius: 3px;
          box-shadow:3px 3px 2px #888;
          margin:auto;
          margin-top:10px;
          margin-bottom:15px;
        }

        #content {
          text-align:center;
        }

        .submit {
          background-color:#610585;
          padding:7px;
          font-weight:bold;
          font-variant:small-caps;
          color:white;
          display:inline-block;
          border-radius:3px;
          font-size:16px;
          border:1px solid #610585;
          cursor:pointer;

        }

        #submit:hover {
          color:#CCC;
        }

        .red {
          color:red;
          cursor:pointer;
        }


        #searchByLocation, #searchByType {
          width:35em;
          height:200px;
          display:inline-block;
          border:2px solid #222;
          border-radius:5px;
          box-shadow: 1px 1px #888;
          float:left;
          margin:20px;
        }

        .searches {
          margin:auto;
          margin-bottom:5px;
          overflow:hidden;
          display:inline-block;
        }

        #directions {
          overflow: hidden; 
          border-top:2px dashed;
        }

        #showHidePOI {
          display:none;
        }
        
        #currentLocation, #currentLocationMobile {
          position:absolute;
          float:left;
          padding:5px;
          font-weight:bold;
          color:white;
          text-shadow:2px 2px 1px black;
          border:1px solid #D10823;
          background-color:#D10823;
          border-radius:3px;
          box-shadow:1px 1px 1px black;
          cursor:pointer;
          margin-left:10px;
        }

        #currentLocationMobile {
          display:none;
        }
  	  
  	    /* Smartphones (portrait and landscape) ----------- */
  		@media only screen 
  		and (min-device-width : 320px) 
  		and (max-device-width : 480px) {
  			#poiBox, #currentLocation {
  				display:none;
  			}

        #currentLocationMobile {
          display:static;
        }
  		}

  		/* iPhone 4 ----------- */
  		@media
  		only screen and (-webkit-min-device-pixel-ratio : 1.5),
  		only screen and (min-device-pixel-ratio : 1.5) {
  			#poiBox, #currentLocation {
  				display:none;
  			}

        #currentLocationMobile {
          display:inline;
        }
  		}


    	</style>
    		<!-- <link rel="stylesheet" href="/bootstrap/css/bootstrap.css"> -->
    		<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true&libraries=places,geometry"></script>
        <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
    		<script type="text/javascript">
          $(document).ready(function () {
          
          var resultList;
          var resultsList;
    			var map;
    			var infowindow = new google.maps.InfoWindow({content: ""});
    			var directionsService = new google.maps.DirectionsService();
  			  var directionsDisplay = new google.maps.DirectionsRenderer();
    			var pos;
    			var current_lat;
    			var current_lng;
          var markers = [];

          $("#submit2").on('click', function () {
             initialize();   
             clearMarkers();
          });

          $("#currentLocation, #currentLocationMobile").on('click', function () {
            map.setCenter(pos);
          });

          
          $("#showHidePOI").on('click', function () {
              showOrHidePOI();
          });

          function clearMarkers() {
            setAllMap(null);
            markers = [];
            var infowindow = new google.maps.InfoWindow({
                          map: map,
                          position: pos,
                          content: 'You are here.'
                        });
          }



          function showOrHidePOI() {

                $("#poi-list").toggle();

                  if ($("#showHidePOI").html() === "Hide") {
                    $("#showHidePOI").html("Show");
                  } else {
                    $("#showHidePOI").html("Hide");
                  }              
              
          }



    			function getDirections(place) {
    
    				var curLoc = pos;
    				var endLoc = new google.maps.LatLng(place.geometry.location.k, place.geometry.location.A);
    				directionsDisplay.setMap(map);
    				directionsDisplay.setPanel(document.getElementById("directions"));
    				var start = curLoc; //Harris Field
    				var end =  endLoc;	//Worksite address
    				var request = {
    					origin:start,
        				destination:end,
        				travelMode: google.maps.DirectionsTravelMode.WALKING,
        				unitSystem: google.maps.UnitSystem.IMPERIAL
          			};
          			console.log("dest lat and lng: " + place.geometry.location.k + " " + place.geometry.location.A);
    				directionsService.route(request, function(response, status) {
    					if (status == google.maps.DirectionsStatus.OK) {
    						directionsDisplay.setDirections(response);
    					}
    				});
    			}

    			function initialize() {
  				
    				// Try HTML5 geolocation
  				if (navigator.geolocation) {
  					navigator.geolocation.getCurrentPosition(function(position) {
      					pos = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
      					current_lat = position.coords.latitude;
      					current_lng = position.coords.longitude;
      					//weather applet
      						var weather = document.getElementById("weatherFrame");
							weather.setAttribute("src", "http://forecast.io/embed/#lat="+ current_lat +"&lon=" + current_lng );

     						var pyrmont = new google.maps.LatLng(current_lat, current_lng);
     						console.log("lat and lng: " + current_lat + " " +current_lng);

     						map = new google.maps.Map(document.getElementById('map-canvas'), {
      						center: pos,
      						zoom: 13
    						});



   						var request = {
   							location: pyrmont,
      						radius: 2000,
      						types: ['restaurant'], 
                  sensor: true
    						};
     						request.types[0] =  document.getElementById("cat1").value;

    						infowindow = new google.maps.InfoWindow();

    						var service = new google.maps.places.PlacesService(map);
    						service.nearbySearch(request, callback);


    						var infowindow = new google.maps.InfoWindow({
     											map: map,
      										position: pos,
      										content: 'You are here.'
    										});

    						map.setCenter(pos);
  					}, function() {
    						handleNoGeolocation(true);
  					});
  				} else {
      				// Browser doesn't support Geolocation
      				handleNoGeolocation(false);
    				}
  			  }

        

  			function callback(results, status) {
  				if (document.getElementById("poi-list")) {
  					document.getElementById("poi-list").remove();
  				}

          $("#POI").empty();
          $("#showHidePOI, #poi-list").css("display","none");
  		  		// You don't actually need this container to make it work
    				var listContainer = document.createElement("div");
    				listContainer.setAttribute("id", "poi-list");
  				// add it to the page

  				document.getElementById("POI").appendChild(listContainer);

  				// Make the list itself which is a <ul>
  				var listElement = document.createElement("ol");
  				// add it to the page
  				listContainer.appendChild(listElement);
  				// Set up a loop that goes through the items in listItems one at a time

  				if (status == google.maps.places.PlacesServiceStatus.OK) {
              resultList = new Array();
              resultsList = new Array();

  			  		for (var i = 0; i < results.length; i++) {
      					createMarker(results[i]);
      					console.log("Name of Place " + i + ": "+ results[i].name);

          	 			// create a <li> for each one.
           				var listItem = document.createElement("li");
                  listItem.setAttribute("name", i);
                  
                  resultList[i] = new google.maps.LatLng(results[i].geometry.location.k, results[i].geometry.location.A);
                  resultsList[i] = results[i];

                  $(listItem).on('click', function () {
            
                    map.setCenter(resultList[$(this).attr("name")]);
                    getDirections(resultsList[$(this).attr("name")]);

                  });


          				// add the item text
          				listItem.innerHTML = results[i].name;
          				// add listItem to the listElement
          				listElement.appendChild(listItem);
        				}

              //Create a clone of the container and append it to fixed div
              $(listContainer).appendTo($("#poiBox"));
              $("#poiBox").find("li").css({"font-weight":"bold"});
              $("#poiBox").find("li").hover(function () {
                $(this).toggleClass("red");
              });

              if($("#poi-list > ol > li").length > 0) {
                $("#poi-list").css({"background-color":"rgba(0, 0, 0, 0.3)", "border":"1px solid black"});
                $("#showHidePOI, #poi-list").css("display","inline-block");
                $("#showHidePOI").html("Hide");
              }
      			}
    			}

    			function createMarker(place) {
      			//var placeLoc = place.geometry.location;
      			var marker = new google.maps.Marker({
        				map: map,
        				position: place.geometry.location
      			});

      			google.maps.event.addListener(marker, 'click', function() {

              if (place.rating ===undefined) {
                  infowindow.setContent(place.name);
              } else {
                infowindow.setContent(place.name + "</br>" + place.rating + "/5");
              }
        				
        				infowindow.open(map, this);
                $("#search2").html(place.name);
        				getDirections(place);
      			});
    			}
  	
  			Element.prototype.remove = function() {
      			this.parentElement.removeChild(this);
  			}
  			
  			NodeList.prototype.remove = HTMLCollection.prototype.remove = function() {
      			for(var i = 0, len = this.length; i < len; i++) {
          			if (this[i] && this[i].parentElement) {
           	   			this[i].parentElement.removeChild(this[i]);
          			}
      			}
  			}
        

        

        initialize();

        });
  		</script>
  	</head>
  	<body>
    <div id="top">
      <div id="currentLocation">Current Location</div>
      <h2>Map</h2>
      <div id="poiBox">
        Points of Interest <button id="showHidePOI">Hide</button><br/>
        <hr />
      </div>
    </div>
  		<div id="map-canvas"></div>
  		<iframe id="weatherFrame" type="text/html" frameborder="0" height="245" width="100%" > </iframe>
  		<div id="currentLocationMobile">Current Location</div>
    		<div id="layout-middle">
      		<div class="wrapper">
        			<div id="content">
              <div class="searches">
          			  <div id="searchByLocation">
                  <h3>Search By Location</h3>
                    <textarea id="search1" placeholder="Enter Start Location"></textarea>
                    <textarea id="search2" placeholder="Enter End Location"></textarea><br/><br/>
                    <div id="submit1" class="submit">Submit</div>
                  </div>
                  <div id="searchByType">
                    <h3>Search By Type</h3>
                    <select id="cat1">
                      <option value="" selected>Select a Point of Interest</option>
                      <option value="museum">Museum</option>
                      <option value="park">Park</option>
                      <option value="restaurant">Restaurant</option>
                      <option value="shopping_mall">Shopping mall</option>
                    </select><br/><br/>
                    <div id="submit2" class="submit">Submit</div>
                  </div>
              </div>
  				
          			<h4 align="center">Directions</h4>

          			<div id="directions"></div>
          			<p class="spacer"></p>
          			<div id="POI" style="width:400px; height:800px;"></div>
        			</div>
      		</div>
    		</div>

  	</body>
  </html>
