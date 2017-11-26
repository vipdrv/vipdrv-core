(function () {
    angular.module('myApp')
        .component('tdCompleteStep', {
            controller: function () {

                var self = this;

            },
            templateUrl: 'src/app/components/steps/complete-step/complete-step.tpl.html',
            bindings: {
                car: '<',
                userData: '<'
            }
        });
})();

