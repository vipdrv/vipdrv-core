(function () {
    angular.module('myApp')
        .component('tdDate', {
            controller: function ($scope, api) {
                var self = this;

                self.isStepValid = true;

                this.validateStep = function () {


                    api.retrieveExperts();

                };

                $scope.nextStepInner = function () {

                    api.retrieveExperts().then((data) => {
                        console.log(11, data);
                    });
                    return;

                    if (self.isStepValid) {
                        self.completeStep({tabId: self.tabId});
                    }
                };
            },
            templateUrl: 'src/app/components/steps/date/date.tpl.html',
            bindings: {
                userData: '=',
                tabId: '<',
                completeStep: '&'
            }
        });
})();

