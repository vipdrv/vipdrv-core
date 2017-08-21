(function () {
    angular.module('myApp')
        .component('tdCompleteStep', {
            controller: function () {

            },
            templateUrl: 'src/app/components/steps/complete-step.tpl.html',
            bindings: {
                car: '<',
                userData: '<'
            }
        });
})();

