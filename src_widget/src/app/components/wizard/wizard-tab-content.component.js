(function () {
    angular.module('myApp')
        .component('tdWizardTabContent', {
            controller: function (api, widgetTabs, userData, globalState) {

                var self = this;

                // =======================================================================//
                // Wizard                                                                 //
                // =======================================================================//

                this.widgetTabs = widgetTabs;
                this.userData = userData;

                this.completeForm = function () {
                    // api.submitForm(userData);
                    globalState.isFormCompleted = true;
                    // this.isFormCompleted = true;
                };

                this.completeStep = function (tabId) {
                    widgetTabs[tabId].isActive = false;
                    widgetTabs[tabId].isCompleted = true;

                    var nextTabId = getNext(this.widgetTabs, tabId);

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
                // Load Data from Api                                                     //
                // =======================================================================//

                self.openHours = [];
                self.experts = [];
                self.beverages = [];
                self.roads = [];

                api.retrieveOpenHours().then((data) => {
                    self.openHours = data.workingIntervals;
                });
                api.retrieveExperts().then((data) => {
                    self.experts = data.items;
                });
                api.retrieveBeverages().then((data) => {
                    self.beverages = data.items;
                });
                api.retrieveRoutes().then((data) => {
                    self.roads = data.items;
                });

            },
            templateUrl: 'src/app/components/wizard/wizard-tab-content.tpl.html',
            bindings: {}
        });
})();

