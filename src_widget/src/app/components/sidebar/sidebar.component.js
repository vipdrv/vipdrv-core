(function () {
    angular.module('myApp')
        .component('tdSidebar', {
            controller: function () {

            },
            templateUrl: 'src/app/components/sidebar/sidebar.tpl.html',
            bindings: {
                car: '<',
                userData: '<'
            }
        });
})();

