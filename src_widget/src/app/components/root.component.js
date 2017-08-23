(function () {
    angular.module('myApp')
        .component('tdRoot', {
            controller: function ($scope, globalState, userData, widgetTabs) {
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

                    $scope.widgetTabs[self.currentTabId].isActive = false;
                    $scope.widgetTabs[tabId].isActive = true;
                };

            },
            templateUrl: 'src/app/components/root.tpl.html'
        });
})();

