(function () {
    angular.module('myApp')
        .component('tdRoot', {
            controller: function ($scope, globalState, userData, widgetTabs, api) {
                $scope.userData = userData;
                $scope.globalState = globalState;

                var self = this;
                $scope.widgetTabs = widgetTabs;

                $scope.switchTab = function (tabId) {

                    if ($scope.widgetTabs[tabId].isActive) {
                        // console.log('Tab is already active');
                        return;
                    }

                    if ($scope.widgetTabs[tabId].isLocked) {
                        // console.log('Tab is locked');
                        return;
                    }

                    for (var key in self.widgetTabs) {
                        $scope.widgetTabs[key].isActive = false;
                    }

                    for (var key in $scope.widgetTabs) {
                        $scope.widgetTabs[key].isActive = false;
                    }
                    $scope.widgetTabs[tabId].isActive = true;
                };

                // =======================================================================//
                // Widget Initialization                                                  //
                // =======================================================================//

                self.$onInit = function () {
                    self.site = null;

                    api.retrieveSite().then(function (data) {
                        self.site = data;
                    });
                };


            },
            templateUrl: 'src/app/components/root.tpl.html'
        });
})();

