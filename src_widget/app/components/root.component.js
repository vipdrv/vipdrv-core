(function () {
    angular.module('myApp')
        .component('tdRoot', {
            controller: function (widgetTabs, globalState, userData) {
                var self = this;
                self.userData = userData;
                self.globalState = globalState;
            },
            templateUrl: 'app/components/root.tpl.html'
        });
})();

