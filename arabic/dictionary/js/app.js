(function(){
  var app = angular.module('dictionary', []);
  
  app.controller("ResultsController", function($scope){
    $scope.results = [];
    $scope.searchstring = "";
    $scope.searchedOnce = false;
    $scope.resultsLoading = false;
    this.findData = function() {
        // Had to do this to fix an issue with AngularJS
        // not detecting the keyboard plugin being typed.
        // The ng-model tag did not seem to update the
        // value if the user clicked the buttons on the
        // keyboard plugin.
        function myTrim(x) {
            return x.replace(/^\s+|\s+$/gm,'');
        }

        $scope.searchstring = myTrim($("#search_input").val());
        if ($scope.searchstring !== "")
        {
            $scope.resultsLoading = true;
        	$.get(
        		"/arabic/dictionary/search.php", 
        		{searchstring: $scope.searchstring.toLowerCase()},
        		function( data ) { 
                    $scope.resultsLoading = false;
                    $scope.searchedOnce = true;
    				$scope.results = data; 
    				//alert($scope.results); 
    				$scope.$apply();
        		},
        		"json"
        		);
        }
    };
  });
})()