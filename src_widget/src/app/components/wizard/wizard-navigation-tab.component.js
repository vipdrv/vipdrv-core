(function () {
    angular.module('myApp')
        .component('tdWizardNavigationTab', {
            controller: function () {
                var self = this;

                self.switchTabInner = function () {
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

