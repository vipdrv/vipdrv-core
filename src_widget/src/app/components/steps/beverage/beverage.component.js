(function () {
    angular.module('myApp')
        .component('tdBeverage', {
            controller: function ($scope, api) {
                var self = this;

                self.isStepValid = null;

                self.$onInit = function () {
                    if (self.userData.beverage.title === null) {
                        this.isSatisfy = false;
                    }
                };

                $scope.itemChanged = function (expertTitle) {
                    self.userData.beverage.title = expertTitle;
                    self.validateStep();
                };

                this.validateStep = function () {
                    if (self.userData.beverage.title === null) {
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
            templateUrl: 'src/app/components/steps/beverage/beverage.tpl.html',
            bindings: {
                userData: '=',
                stepData: '<',
                tabId: '<',
                completeStep: '&'
            }
        });
})();

