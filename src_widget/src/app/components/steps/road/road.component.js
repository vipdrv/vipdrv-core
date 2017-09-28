(function () {
    angular.module('myApp')
        .component('tdRoad', {
            controller: function ($scope, api) {
                var self = this;

                self.isStepValid = null;

                self.$onInit = function () {
                    if (self.userData.road.title === null) {
                        this.isSatisfy = false;
                    }
                };

                $scope.itemChanged = function (itemTitle) {
                    self.userData.road.title = itemTitle;
                    self.validateStep();
                };

                this.validateStep = function () {
                    if (self.userData.road.title === null) {
                        self.isStepValid = false;
                    } else {
                        self.isStepValid = true;
                    }
                };

                $scope.nextStepInner = function () {
                    self.validateStep();
                    if (self.isStepValid) {
                        self.completeStep({tabId: self.tabId});
                    }
                };
            },
            templateUrl: 'src/app/components/steps/road/road.tpl.html',
            bindings: {
                stepData: '<',
                userData: '=',
                tabId: '<',
                completeStep: '&'
            }
        });
})();

