(function () {
    angular.module('myApp')
        .component('tdSidebar', {
            controller: function () {
                var self = this;


                self.asd = "123";

            },
            templateUrl: 'src/app/components/sidebar/sidebar.tpl.html',
            bindings: {
                car: '<',
                userData: '<'
            }
        });
})();

