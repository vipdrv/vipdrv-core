(function () {
    var app = angular.module('myApp', ['templates']);

    /* ========================================= App Data ========================================== */

    var apiBaseUrl = 'http://api.dev.test-drive.tech';
    // var apiBaseUrl = 'http://localhost:4220';
    var siteId = 'nissan-of-bowie';

    var widgetTabs = {
        time: {
            id: 'time',
            title: 'Select date & time',
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
            title: 'Select preferred route',
            isActive: false,
            isLocked: true,
            isCompleted: false
        },
        details: {
            id: 'details',
            icon: 'fa fa-handshake-o fa-2x',
            title: 'Your details',
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
            email: null
        },
        calendar: {
            date: null,
            time: null
        },
        expert: {
            id: null,
            title: null
        },
        beverage: {
            id: null,
            title: null
        },
        road: {
            id: null,
            title: null
        },
        car: {
            img: null,
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
    /* ======================================== Url Params ========================================= */

    // var url = new URL(window.location.href);

    var url = new FiltersFromUrl(window.location.search).get();

    var siteId = url.site_id || siteId;

    var vin = url.vin || null;
    var img = url.img || null;
    var title = url.title;
    var engine = url.engine;
    var year = url.year;
    var colour = url.colour;
    var transmission = url.transmission;
    var fuel = url.fuel;

    userData.car.vin = vin;
    userData.car.img = img;
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

// http://localhost:8080/?site_id=nissan-of-bowie&vin=5N1AA0NC9EN606049&img=http://vehiclephotos.vauto.com/ca/f0/97/9e-1ad8-43be-b64a-1e1635ee7099/image-1.jpg&title=Nissan%20GT-R&year=2016&colour=Orange&transmission=Manual&fuel=3.8%20litre%20twin-turbo%20V6