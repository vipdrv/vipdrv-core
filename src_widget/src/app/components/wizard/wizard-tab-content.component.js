(function () {
    angular.module('myApp')
        .component('tdWizardTabContent', {
            controller: function (api, widgetTabs, userData, dealerData, globalState) {

                var self = this;
                self.dealerData = dealerData;
                self.widgetTabs = widgetTabs;
                self.userData = userData;

                // =======================================================================//
                // Wizard                                                                 //
                // =======================================================================//

                this.completeForm = function () {
                    globalState.isFormCompleted = true;
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

                // =======================================================================//
                // Init                                                                   //
                // =======================================================================//

                self.$onInit = function () {
                    self.site = [];
                    self.experts = [];
                    self.beverages = [];
                    self.roads = [];

                    api.retrieveSite().then(function (data) {
                        self.site = data;

                        self.dealerData.siteId = data.id;
                        self.dealerData.name = data.dealerName;
                        self.dealerData.phone = data.dealerPhone;
                        self.dealerData.address = data.dealerAddress;
                        self.dealerData.siteUrl = data.url;
                        self.dealerData.workingHours = data.workingHours;
                    });
                    api.retrieveExperts().then(function (data) {
                        self.dealerData.experts.items = data.items;
                        self.experts = data.items;
                    });
                    api.retrieveBeverages().then(function (data) {
                        self.dealerData.beverages.items = data.items;
                        self.beverages = data.items;
                    });
                    api.retrieveRoutes().then(function (data) {
                        self.dealerData.roads.items = data.items;
                        self.roads = data.items;
                    });
                };

            },
            templateUrl: 'src/app/components/wizard/wizard-tab-content.tpl.html',
            bindings: {}
        });
})();
