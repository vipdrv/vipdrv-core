(function () {
    angular.module('myApp')
        .component('tdRoad', {
            controller: function ($scope, dealerData, userData) {

                var self = this;
                self.isStepValid = null;
                self.dealerData = dealerData;
                self.userData = userData;

                self.$onInit = function () {
                    if (self.userData.road.name === null) {
                        this.isStepValid = false;
                    }
                };

                $scope.itemChanged = function (id, img, name, description) {
                    self.userData.road.id = id;
                    self.userData.road.img = img;
                    self.userData.road.name = name;
                    self.userData.road.description = description;
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
                tabId: '<',
                completeStep: '&'
            }
        });
})();

