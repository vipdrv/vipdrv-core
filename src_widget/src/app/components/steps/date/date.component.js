(function () {
    angular.module('myApp')
        .component('tdDate', {
            controller: function ($scope, api) {
                var self = this;

                self.isStepValid = true;


                this.validateStep = function () {

                };

                this.nextStepInner = function () {
                    if (self.isStepValid) {
                        self.completeStep({tabId: this.tabId});
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

