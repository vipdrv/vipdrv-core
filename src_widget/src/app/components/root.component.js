(function () {
    angular.module('myApp')
        .component('root', {
            controller: function ($scope, globalState, userData, widgetTabs, api, clientId) {
                var self = this;

                self.userData = userData;
                self.clientId = clientId;
                self.widgetTabs = widgetTabs;
                self.globalState = globalState;

                self.switchTab = function (tabId) {
                    if (self.widgetTabs[tabId].isActive) {
                        return;
                    }

                    if (self.widgetTabs[tabId].isLocked) {
                        return;
                    }

                    for (var key in self.widgetTabs) {
                        self.widgetTabs[key].isActive = false;
                    }

                    for (var key in self.widgetTabs) {
                        self.widgetTabs[key].isActive = false;
                    }
                    self.widgetTabs[tabId].isActive = true;
                };

            },
            templateUrl: 'src/app/components/root.tpl.html'
        });
})();

