(function () {
    angular.module('myApp')
        .component('tdExpert', {
            controller: function ($scope) {
                var self = this;

                self.isStepValid = null;

                self.$onInit = function () {
                    self.validateStep();
                };

                self.expertChanged = function (id, name) {
                    self.userData.expert.id = id;
                    self.userData.expert.name = name;

                    self.validateStep();
                };

                self.validateStep = function () {
                    if (self.userData.expert.name === null) {
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

