(function () {
    angular.module('myApp')
        .component('tdWizard', {
            controller: function (api, widgetTabs, userData, globalState) {
                var self = this;

                this.widgetTabs = widgetTabs;
                this.userData = userData;

                // this.isFormCompleted = false;

                this.completeForm = function () {
                    api.submitForm(userData);
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

                this.unCompleteStep = function (tabId) {};
                this.lockTab = function (tabId) {};

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
            templateUrl: 'src/app/components/wizard/wizard.tpl.html',
            bindings: {}
        });
})();

