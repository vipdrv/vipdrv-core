(function () {
    angular.module('myApp')
        .component('tdCard', {
            controller: function () {

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

