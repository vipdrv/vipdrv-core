(function () {
    angular.module('myApp')
        .component('root', {
            controller: function ($scope,
                                  $window,
                                  $location,
                                  $timeout,
                                  globalState,
                                  bookingData,
                                  defaultBookingData,
                                  dealerData,
                                  widgetTabs,
                                  defaultWidgetTabs,
                                  siteId,
                                  api) {

                var self = this;
                self.siteId = siteId;
                self.bookingData = bookingData;
                self.defaultBookingData = defaultBookingData;
                self.dealerData = dealerData;
                self.widgetTabs = widgetTabs;
                self.defaultWidgetTabs = defaultWidgetTabs;
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
                    var closeWidgetFrame = self.parseQuery(hash).closeWidgetFrame;

                    if (closeWidgetFrame) {
                        $timeout(function () {
                            self.clearWidgetProgress();
                        }, 1000);
                    }
                });

                self.clearWidgetProgress = function () {
                    // reset widget tabs
                    for (var key in self.widgetTabs) {
                        self.widgetTabs[key].isActive = false;
                        self.widgetTabs[key].isLocked = true;
                        self.widgetTabs[key].isCompleted = false;
                    }
                    self.widgetTabs.time.isActive = true;
                    self.widgetTabs.time.isLocked = false;
                    self.widgetTabs.time.isCompleted = false;

                    // reset booking data
                    self.bookingData.user = Object.assign({}, self.defaultBookingData.user);
                    self.bookingData.calendar.time = null;
                    self.bookingData.calendar.dayOfWeek = null;
                    self.bookingData.calendar.isSkipped = null;
                    self.bookingData.expert = Object.assign({}, self.defaultBookingData.expert);
                    self.bookingData.beverage = Object.assign({}, self.defaultBookingData.beverage);
                    self.bookingData.road = Object.assign({}, self.defaultBookingData.road);
                    self.bookingData.vehicle = Object.assign({}, self.defaultBookingData.vehicle);
                    self.bookingData.closeWidgetFrame = self.defaultBookingData.closeWidgetFrame;

                    // reset global state
                    self.globalState.isBookingCompleted = false;
                };

                self.$onInit = function () {
                    api.retrieveSite().then(function (data) {
                        self.dealerData.siteId = data.site.id;
                        self.dealerData.name = data.site.dealerName;
                        self.dealerData.phone = data.site.dealerPhone;
                        self.dealerData.address = data.site.dealerAddress;
                        self.dealerData.siteUrl = data.site.url;
                        self.dealerData.workingHours = data.site.workingHours;

                        self.dealerData.experts.items = data.experts;
                        self.dealerData.experts.isStepEnabled = data.site.useExpertStep;
                        self.dealerData.beverages.items = data.beverages;
                        self.dealerData.beverages.isStepEnabled = data.site.useBeverageStep;
                        self.dealerData.roads.items = data.routes;
                        self.dealerData.roads.isStepEnabled = data.site.useRouteStep;
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

