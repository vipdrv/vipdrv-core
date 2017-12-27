(function () {
    angular.module('myApp')
        .component('root', {
            controller: function ($scope, $window, globalState, bookingData, dealerData, widgetTabs, siteId, api) {

                var self = this;
                self.siteId = siteId;
                self.bookingData = bookingData;
                self.dealerData = dealerData;
                self.widgetTabs = widgetTabs;
                self.globalState = globalState;

                // =======================================================================//
                // Wizard                                                                 //
                // =======================================================================//

                self.switchTab = function (tabId) {
                    if (self.widgetTabs[tabId].isActive) {
                        return;
                    }

                    if (self.widgetTabs[tabId].isLocked) {
                        return;
                    }

                    for (var key in self.widgetTabs) {
                        self.widgetTabs[key].isActive = false;
                    }

                    for (var key in self.widgetTabs) {
                        self.widgetTabs[key].isActive = false;
                    }
                    self.widgetTabs[tabId].isActive = true;
                };

                // =======================================================================//
                // Init                                                                   //
                // =======================================================================//

                self.$onInit = function () {
                    parseWidgetParams();

                    api.retrieveSite().then(function (data) {
                        self.dealerData.siteId = data.id;
                        self.dealerData.name = data.dealerName;
                        self.dealerData.phone = data.dealerPhone;
                        self.dealerData.address = data.dealerAddress;
                        self.dealerData.siteUrl = data.url;
                        self.dealerData.workingHours = data.workingHours;
                    });
                    api.retrieveExperts().then(function (data) {
                        self.dealerData.experts.items = data.items;
                    });
                    api.retrieveBeverages().then(function (data) {
                        self.dealerData.beverages.items = data.items;
                    });
                    api.retrieveRoutes().then(function (data) {
                        self.dealerData.roads.items = data.items;
                    });
                };

                // =======================================================================//
                // Helpers                                                                //
                // =======================================================================//

                function parseWidgetParams() {
                    var url = new FiltersFromUrl($window.location.search).get();

                    var vin = url.vin || null;
                    var imageUrl = url.imageUrl || null;
                    var vdpUrl = url.vdpUrl || null;
                    var title = url.title || null;
                    var engine = url.engine || null;
                    var year = url.year || null;
                    var colour = url.colour || null;
                    var transmission = url.transmission || null;
                    var fuel = url.fuel || null;

                    self.bookingData.car.vin = vin;
                    self.bookingData.car.imageUrl = imageUrl;
                    self.bookingData.car.vdpUrl = vdpUrl;
                    self.bookingData.car.title = title;
                    self.bookingData.car.engine = engine;
                    self.bookingData.car.year = year;
                    self.bookingData.car.colour = colour;
                    self.bookingData.car.transmission = transmission;
                    self.bookingData.car.fuel = fuel;
                }

                // =======================================================================//
                // Resize                                                                 //
                // =======================================================================//

                $window.addEventListener("resize", function () {

                    console.log('Widget = ', $window.document.documentElement.clientHeight);



                });

            },
            templateUrl: 'src/app/components/root.tpl.html'
        });
})();

