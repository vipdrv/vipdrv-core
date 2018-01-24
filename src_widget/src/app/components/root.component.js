(function () {
    angular.module('myApp')
        .component('root', {
            controller: function ($scope, $window, $location, globalState, bookingData, dealerData, widgetTabs, siteId, api) {

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
                // Widget Initialization
                // =======================================================================//

                $scope.$on('$locationChangeSuccess', function (event, newUrl, oldUrl) {
                    var hash = $location.hash();
                    self.bookingData.vehicle = self.parseVehicleDataFromHash(hash);

                    var clearBookingData = self.bookingData.vehicle.clearBookingData;
                    if (clearBookingData) {
                        for (var key in self.widgetTabs) {
                            self.widgetTabs[key].isActive = false;
                            self.widgetTabs[key].isLocked = true;
                            self.widgetTabs[key].isCompleted = false;
                        }
                        self.widgetTabs.time.isActive = true;
                        self.widgetTabs.time.isLocked = false;
                        self.widgetTabs.time.isCompleted = false;
                    }
                });

                self.$onInit = function () {
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

                self.parseVehicleDataFromHash = function (hash) {
                    var query = self.parseQuery(hash);

                    var vehicle = {
                        vin: null,
                        stock: null,
                        year: null,
                        make: null,
                        model: null,
                        body: null,
                        title: null,
                        engine: null,
                        exterior: null,
                        drivetrain: null,
                        transmission: null,
                        msrp: null,
                        imageUrl: null,
                        vdpUrl: null
                    };

                    vehicle.vin = query.vin || null;
                    vehicle.stock = query.stock || null;
                    vehicle.year = query.year || null;
                    vehicle.make = query.make || null;
                    vehicle.model = query.model || null;
                    vehicle.body = query.body || null;
                    vehicle.title = query.title || null;
                    vehicle.engine = query.engine || null;
                    vehicle.exterior = query.exterior || null;
                    vehicle.drivetrain = query.drivetrain || null;
                    vehicle.transmission = query.transmission || null;
                    vehicle.msrp = query.msrp || null;
                    vehicle.imageUrl = query.imageUrl || null;
                    vehicle.vdpUrl = query.vdpUrl || null;
                    vehicle.clearBookingData = query.clearBookingData || null;

                    return vehicle;
                };


                self.parseQuery = function (queryString) {
                    var query = {};
                    var pairs = (queryString[0] === '?' ? queryString.substr(1) : queryString).split('&');
                    for (var i = 0; i < pairs.length; i++) {
                        var pair = pairs[i].split('=');
                        query[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1] || '');
                    }
                    return query;
                }
            },
            templateUrl: 'src/app/components/root.tpl.html'
        });

    // =======================================================================//
    // Filters                                                                //
    // =======================================================================//

    angular.module('myApp')
        .filter('usaDateFormat', function () {
            return function (dateStr) {
                dateStr = dateStr || '';
                var chunks = dateStr.split("-");

                var out = chunks[1] + '/' + chunks[2] + '/' + chunks[0].slice(-2);

                return out;
            };
        });

    angular.module('myApp')
        .filter('vehicleTitleFormat', function () {
            return function (vehicleTitleStr) {
                if (!vehicleTitleStr) {
                    return 'Vehicle Title';
                }

                var arr = vehicleTitleStr.split(' ');
                if (arr.length < 3) {
                    return vehicleTitleStr;
                }

                return vehicleTitleStr.split(' ').splice(2).join(' ');

                // 'Pre-Owned 2011 Audi A5 2dr Cpe Auto quattro 2.0T Premium P AWD';
                // 0 - new|used
                // 1 - year
                // 2 - make
                // 3 - model ?
            };
        });
})();

