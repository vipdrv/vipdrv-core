(function () {
    angular.module('myApp')
        .component('tdExpert', {
            controller: function ($scope, api) {
                var self = this;

                self.isStepValid = null;

                self.$onInit = function () {
                    self.validateStep();
                };

                var dummyExperts = [{
                    photo_url: '/img/dummy-expert-2.png',
                    title: 'George Reese',
                    description: 'As a certified automotive specialist, George will be happy to help you make the right decisions.'
                }, {
                    photo_url: '/img/dummy-expert-1.png',
                    title: 'Joe Rowe',
                    description: 'With over 10 years experience in the industry Joe has invaluable knowledge in give.'
                }, {
                    photo_url: '/img/dummy-expert-4.png',
                    title: 'Gregory May',
                    description: 'Head of sales'
                }, {
                    photo_url: '/img/dummy-expert-3.png',
                    title: 'Rhoda Hogan',
                    description: 'Rhoda has a bubbly personality to make any test drive a not to be missed experience.'
                }];

                $scope.experts = dummyExperts;

                self.expertChanged = function (expertTitle) {
                    self.userData.expert.title = expertTitle;
                    self.validateStep();
                };

                self.validateStep = function () {
                    if (self.userData.expert.title === null) {
                        this.isStepValid = false;
                    } else {
                        this.isStepValid = true;
                    }
                };

                $scope.nextStepInner = function () {
                    if (self.isStepValid) {
                        console.log('13');
                        self.completeStep({tabId: self.tabId});
                    }
                };
            },
            templateUrl: 'src/app/components/steps/expert/expert.tpl.html',
            bindings: {
                userData: '=',
                tabId: '<',
                completeStep: '&'
            }
        });
})();

