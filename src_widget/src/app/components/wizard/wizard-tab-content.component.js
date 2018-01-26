(function () {
    angular.module('myApp')
        .component('tdWizardTabContent', {
            controller: function (api, widgetTabs, bookingData, dealerData, globalState) {

                var self = this;
                self.dealerData = dealerData;
                self.widgetTabs = widgetTabs;
                self.bookingData = bookingData;

                // =======================================================================//
                // Wizard                                                                 //
                // =======================================================================//

                this.completeForm = function () {
                    globalState.isBookingCompleted = true;
                };

                this.completeStep = function (tabId) {
                    widgetTabs[tabId].isActive = false;
                    widgetTabs[tabId].isCompleted = true;

                    var nextTabId = getNext(self.widgetTabs, tabId);

                    if (nextTabId) {
                        widgetTabs[nextTabId].isActive = true;
                        widgetTabs[nextTabId].isLocked = false;
                    }
                };

                this.unCompleteStep = function (tabId) {
                };

                this.lockTab = function (tabId) {
                };

                var getNext = function (collection, key) {
                    var next = false;

                    for (var i in collection) {
                        if (next === true) {
                            return i;
                        }
                        if (i === key) {
                            next = true;
                        }
                    }
                    return null;
                };

                var getPrev = function (collection, key) {
                    var prev = null;

                    for (var i in collection) {
                        if (i === key) {
                            return prev;
                        }
                        prev = i;
                    }
                    return null;
                };



            },
            templateUrl: 'src/app/components/wizard/wizard-tab-content.tpl.html',
            bindings: {}
        });
})();
