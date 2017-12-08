(function () {
    angular.module('myApp')
        .component('tdBeverage', {
            controller: function ($scope) {
                var self = this;

                self.isStepValid = null;

                self.$onInit = function () {
                    if (self.userData.beverage.name === null) {
                        self.isStepValid = false;
                    }
                };

                $scope.itemChanged = function (id, img, name, description) {
                    self.userData.beverage.id = id;
                    self.userData.beverage.img = img;
                    self.userData.beverage.name = name;
                    self.userData.beverage.description = description;
                    self.validateStep();
                };

                this.validateStep = function () {
                    if (self.userData.beverage.name === null) {
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
                    self.userData.beverage.id = 0;
                    self.userData.beverage.name = "Skipped";
                    self.completeStep({tabId: self.tabId});
                };
            },
            templateUrl: 'src/app/components/steps/beverage/beverage.tpl.html',
            bindings: {
                userData: '=',
                stepData: '<',
                tabId: '<',
                completeStep: '&'
            }
        });
})();

