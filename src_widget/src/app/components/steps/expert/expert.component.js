(function () {
    angular.module('myApp')
        .component('tdExpert', {
            controller: function ($scope, api) {
                var self = this;

                self.isStepValid = null;

                self.$onInit = function () {
                    self.validateStep();
                };

                self.expertChanged = function (expertTitle) {
                    self.userData.expert.title = expertTitle;
                    self.validateStep();
                };

                self.validateStep = function () {
                    if (self.userData.expert.title === null) {
                        this.isStepValid = false;
                    } else {
                        this.isStepValid = true;
                    }
                };

                $scope.nextStepInner = function () {
                    self.validateStep();
                    if (self.isStepValid) {
                        self.completeStep({tabId: self.tabId});
                    }
                };
            },
            templateUrl: 'src/app/components/steps/expert/expert.tpl.html',
            bindings: {
                userData: '=',
                stepData: '<',
                tabId: '<',
                completeStep: '&'
            }
        });
})();

