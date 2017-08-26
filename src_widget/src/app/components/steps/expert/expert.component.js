(function () {
    angular.module('myApp')
        .component('tdExpert', {
            controller: function ($scope, api) {
                var self = this;
                var api = api;
                this.isSatisfy = null;

                function didLoadExperts(json) {
                    console.log(json);
                    self.experts = json.experts.splice(0);
                }

                this.$onInit = function () {

                    if (self.userData.expert.title === null) {
                        this.isSatisfy = false;
                    }

                    console.log('123');

                    // api.loadExperts().then(didLoadExperts)
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

                this.expertChanged = function (expertTitle) {
                    self.userData.expert.title = expertTitle;
                    self.satisfyStep();
                };


                this.satisfyStep = function () {
                    if (self.userData.expert.title === null) {
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
            templateUrl: 'src/app/components/steps/expert/expert.tpl.html',
            bindings: {
                userData: '=',
                tabId: '<',
                completeStep: '&'
            }
        });
})();

