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
                    description: 'With over 10 years experience in the industry Joe has invaluable knowledge in give.'
                }, {
                    photo_url: '/img/dummy-expert-1.png',
                    title: 'Joe Rowe',
                    description: 'With over 10 years experience in the industry Joe has invaluable knowledge in give.'
                }, {
                    photo_url: '/img/dummy-expert-4.png',
                    title: 'Gregory May',
                    description: 'With over 10 years experience in the industry Joe has invaluable knowledge in give.'
                }, {
                    photo_url: '/img/dummy-expert-3.png',
                    title: 'Rhoda Hogan',
                    description: 'With over 10 years experience in the industry Joe has invaluable knowledge in give.'
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

