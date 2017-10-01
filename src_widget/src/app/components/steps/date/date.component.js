(function () {
    angular.module('myApp')
        .component('tdDate', {
            controller: function ($scope, api) {
                var self = this;

                self.isStepValid = true;

                this.validateStep = function () {

                };

                $scope.nextStepInner = function () {
                    if (self.isStepValid) {
                        self.completeStep({tabId: self.tabId});
                    }
                };
            },
            templateUrl: 'src/app/components/steps/date/date.tpl.html',
            bindings: {
                userData: '=',
                stepData: '<',
                tabId: '<',
                completeStep: '&'
            }
        });
})();

