(function () {
    angular.module('myApp')
        .component('tdCompleteStep', {
            controller: function () {

                console.log('completed');

            },
            templateUrl: 'app/components/complete-step/complete-step.tpl.html',
            bindings: {
                car: '<',
                userData: '<'
            }
        });
})();

