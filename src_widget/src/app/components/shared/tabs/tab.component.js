(function () {
    angular.module('myApp')
        .component('tdTab', {
            controller: function () {
                var self = this;

                this.switchTabInner = function () {
                    self.switchTab({tabId: this.tabId});
                };
            },
            templateUrl: 'app/components/shared/tabs/tab.tpl.html',
            bindings: {
                title: '<',
                tabId: '<',
                tabData: '<',
                switchTab: '&'
            }
        });
})();

