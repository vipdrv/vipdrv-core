(function () {
    var app = angular.module('myApp', ['templates', 'moment-picker']);

    app.factory('clientId', function clientIdFactory(api) {
        return 'a12345654321x';
    });

    // =======================================================================//
    // App Configs                                                            //
    // =======================================================================//

    var apiBaseUrl = '%apiBaseUrl%';
    var siteId = '%siteId%';

    // =======================================================================//
    // Global Variables                                                       //
    // =======================================================================//

    var widgetTabs = {
        time: {
            id: 'time',
            title: 'Select Date & Time',
            icon: 'fa fa-clock-o fa-2x',
            isActive: true,
            isLocked: false,
            isCompleted: false
        },
        expert: {
            id: 'expert',
            icon: 'fa fa-users fa-2x',
            title: 'Select Expert',
            isActive: false,
            isLocked: true,
            isCompleted: false
        },
        beverage: {
            id: 'beverage',
            icon: 'fa fa-coffee fa-2x',
            title: 'Select Beverage',
            isActive: false,
            isLocked: true,
            isCompleted: false
        },
        road: {
            id: 'road',
            icon: 'fa fa-road fa-2x',
            title: 'Select Preferred Route',
            isActive: false,
            isLocked: true,
            isCompleted: false
        },
        // music: {
        //     id: 'road',
        //     icon: 'fa fa-music fa-2x',
        //     title: 'Select music',
        //     isActive: false,
        //     isLocked: true,
        //     isCompleted: false
        // },
        details: {
            id: 'details',
            icon: 'fa fa-handshake-o fa-2x',
            title: 'Your Details',
            isActive: false,
            isLocked: true,
            isCompleted: false
        }
    };

    var userData = {
        user: {
            firstName: null,
            lastName: null,
            phone: null,
            email: null,
            comment: null
        },
        calendar: {
            date: null,
            time: null
        },
        expert: {
            id: null,
            img: null,
            name: null,
            description: null
        },
        beverage: {
            id: null,
            img: null,
            name: null,
            description: null
        },
        road: {
            id: null,
            img: null,
            name: null,
            description: null
        },
        car: {
            imageUrl: null,
            title: null,
            engine: null,
            year: null,
            colour: null,
            transmission: null,
            fuel: null
        }
    };

    var globalState = {
        isFormCompleted: false
    };

    // =======================================================================//
    // Widget Initialization                                                  //
    // =======================================================================//

    var url = new FiltersFromUrl(window.location.search).get();

    var siteId = url.site_id || siteId;

    var vin = url.vin || null;
    var imageUrl = url.imageUrl || null;
    var title = url.title || null;
    var engine = url.engine || null;
    var year = url.year || null;
    var colour = url.colour || null;
    var transmission = url.transmission || null;
    var fuel = url.fuel || null;

    userData.car.vin = vin;
    userData.car.imageUrl = imageUrl;
    userData.car.title = title;
    userData.car.engine = engine;
    userData.car.year = year;
    userData.car.colour = colour;
    userData.car.transmission = transmission;
    userData.car.fuel = fuel;

    app.value('widgetTabs', widgetTabs);
    app.value('globalState', globalState);
    app.value('userData', userData);
    app.value('apiBaseUrl', apiBaseUrl);
    app.value('siteId', siteId);
})();
