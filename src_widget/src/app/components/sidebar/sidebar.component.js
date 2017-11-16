(function () {
    angular.module('myApp')
        .component('tdSidebar', {
            controller: function () {
                var self = this;





            },
            templateUrl: 'src/app/components/sidebar/sidebar.tpl.html',
            bindings: {
                car: '<',
                userData: '<'
            }
        });
})();

