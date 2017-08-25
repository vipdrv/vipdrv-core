(function () {
    angular.module('myApp')
        .component('tdWizardNavigationTab', {
            controller: function ($scope) {
                var self = this;

                $scope.switchTabInner = function () {
                    self.switchTab({tabId: this.tabId});
                };
            },
            templateUrl: 'src/app/components/wizard/wizard-navigation-tab.tpl.html',
            bindings: {
                title: '<',
                tabId: '<',
                tabData: '<',
                switchTab: '&'
            }
        });
})();

