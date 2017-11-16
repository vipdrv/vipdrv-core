(function () {
    angular.module('myApp')
        .component('tdRoad', {
            controller: function ($scope) {
                var self = this;

                self.isStepValid = null;

                self.$onInit = function () {
                    if (self.userData.road.name === null) {
                        this.isStepValid = false;
                    }
                };

                $scope.itemChanged = function (id, name) {
                    self.userData.road.id = id;
                    self.userData.road.name = name;
                    self.validateStep();
                };

                this.validateStep = function () {
                    if (self.userData.road.title === null) {
                        self.isStepValid = false;
                    } else {
                        self.isStepValid = true;
                    }
                };

                $scope.nextStep = function () {
                    self.validateStep();
                    if (self.isStepValid) {
                        self.completeStep({tabId: self.tabId});
                    }
                };
                $scope.skipStep = function () {
                    self.userData.road.id = 0;
                    self.userData.road.name = "Skipped";
                    self.completeStep({tabId: self.tabId});
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

