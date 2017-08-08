(function () {
    angular.module('myApp')
        .component('tdCard', {
            controller: function () {
                var vm = this;
            },
            templateUrl: 'src/app/components/shared/card/card.tpl.html',
            bindings: {
                cardTitle: '<',
                cardDesc: '<',
                cardImage: '<',
                isActiveCard: '<'
            }
        });
})();

