(function () {
    angular.module('myApp')
        .component('tdBeverage', {
            controller: function (api) {
                var self = this;
                self.beverages = [];
                this.isSatisfy = null;

                function didLoadBeverages(json) {
                    self.beverages = json.beverages;
                }

                this.$onInit = function () {
                    if (self.userData.beverage.title === null) {
                        this.isSatisfy = false;
                    }

                    api.loadBeverages().then(didLoadBeverages)
                };

                this.beverageChanged = function (expertTitle) {
                    self.userData.beverage.title = expertTitle;
                    self.satisfyStep();
                };

                this.satisfyStep = function () {
                    if (self.userData.beverage.title === null) {
                        this.isSatisfy = false;
                    } else {
                        this.isSatisfy = true;
                    }
                };

                this.completeStepInner = function () {
                    if (self.isSatisfy) {
                        self.completeStep({tabId: this.tabId});
                    }
                };
            },
            templateUrl: 'app/components/steps/beverage/beverage.tpl.html',
            bindings: {
                userData: '=',
                tabId: '<',
                completeStep: '&'
            }
        });
})();

