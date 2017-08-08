(function () {
    angular.module('myApp')
        .component('tdCompleteStep', {
            controller: function () {

                console.log('completed');

            },
            templateUrl: 'src/app/components/complete-step/complete-step.tpl.html',
            bindings: {
                car: '<',
                userData: '<'
            }
        });
})();

