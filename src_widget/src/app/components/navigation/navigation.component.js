(function () {
    angular.module('myApp')
        .component('tdNavigation', {
            controller: function (widgetTabs) {
                var self = this;
                self.widgetTabs = widgetTabs;
                self.currentTabId = 'expert';

                this.switchTab = function (tabId) {
                    if (self.widgetTabs[tabId].isActive) {
                        // console.log('Tab is already active');
                        return;
                    }

                    if (self.widgetTabs[tabId].isLocked) {
                        // console.log('Tab is locked');
                        return;
                    }

                    for (var key in self.widgetTabs) {
                        self.widgetTabs[key].isActive = false;
                    }

                    self.widgetTabs[self.currentTabId].isActive = false;
                    self.widgetTabs[tabId].isActive = true;
                };
            },
            templateUrl: 'src/app/components/navigation/navigation.tpl.html'
        });
})();

