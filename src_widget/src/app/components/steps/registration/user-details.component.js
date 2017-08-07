(function () {
    angular.module('myApp')
        .component('tdUserDetails', {
            controller: function () {
                var self = this;

                this.isSatisfy = null;

                this.$onInit = function () {
                    if (self.userData.user.email === null) {
                        this.isSatisfy = false;
                    }
                };

                this.satisfyStep = function () {
                    if (self.userData.user.email === null) {
                        this.isSatisfy = false;
                    } else {
                        this.isSatisfy = true;
                    }
                };

                this.createAccountAndMakeBooking = function () {
                    self.satisfyStep();

                    if (self.isSatisfy) {
                        self.completeStep({tabId: this.tabId});
                        self.completeForm();
                    }
                };
            },
            templateUrl: 'app/components/steps/registration/user-details.tpl.html',
            bindings: {
                userData: '=',
                tabId: '<',
                completeStep: '&',
                completeForm: '&'
            }
        });
})();

