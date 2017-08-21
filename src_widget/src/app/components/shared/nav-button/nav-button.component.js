(function () {
    angular.module('myApp')
        .component('tdTab', {
            controller: function ($scope) {
                var self = this;

                $scope.nextStepInner = function () {
                    self.nextStep({tabId: this.tabId});
                };

                $scope.prevStepInner = function () {
                    self.prevStep({tabId: this.tabId});
                };
            },
            templateUrl: 'src/app/components/shared/nav-button/nav-button.tpl.html',
            bindings: {
                tabId: '<',
                nextStep: '&',
                prevStep: '&'
            }
        });
})();

