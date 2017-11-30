// =======================================================================//
// Widget Integration logic                                               //
// =======================================================================//

var TestDrive = TestDrive || (function () {

    var _SiteId = '%siteId%';
    var _WidgetUrl = '%widgetUrl%';

    var _appendTestDriveFrame = function (vin, img, title, engine, year, colour, transmission, fuel) {
        var hash = Math.random().toString(36).substring(7);
        var url = `${_WidgetUrl}?site_id=${_SiteId}&vin=${vin}&imageUrl=${img}&title=${title}&engine=${engine}&year=${year}&colour=${colour}&transmission=${transmission}&fuel=${fuel}&hash=${hash}`;

        var html =
            `<div class="test-drive__content">
                 <div class="frame-header">
                    <div class="frame-header__title">Test Drive Booking</div>
                    <div class="frame-header__cross" onclick="TestDrive.closeTestDrive()">&#10006;</div>
                 </div>
                 <iframe class="test-drive__frame" src="${encodeURI(url)}" frameBorder="0"></iframe>
             </div>`;

        document.getElementsByClassName('test-drive')[0].innerHTML = html;
    };

    var _appendWidgetFrame = function() {
        var elemDiv = document.createElement('div');
        elemDiv.className = "test-drive";

        if (!document.body) {
            document.body = document.createElement("body");;
        }

        document.body.appendChild(elemDiv);
    };

    var _showTestDrive = function () {
        document.getElementsByClassName('test-drive')[0].classList.add('test-drive-visible');
        document.documentElement.classList.add('vipdrv--disable-scroll');
        window.scrollTo(0, 0);

        setTimeout(function () {
            document.getElementsByClassName('test-drive__content')[0].classList.add('test-drive__content-visible');
        }, 200);
    };
    var _hideTestDrive = function () {
        document.getElementsByClassName('test-drive__content')[0].classList.remove('test-drive__content-visible');
        document.documentElement.classList.remove('vipdrv--disable-scroll');

        setTimeout(function () {
            document.getElementsByClassName('test-drive')[0].classList.remove('test-drive-visible');
        }, 200);
    };

    var _addCss = function () {
        var head = document.head;
        var link = document.createElement('link');

        link.type = 'text/css';
        link.rel = 'stylesheet';
        link.href = _WidgetUrl + '/Integration/integration.css';

        head.appendChild(link)
    };

    var _escapeListener = function () {
        document.onkeydown = function (evt) {
            evt = evt || window.event;
            var isEscape = false;
            if ("key" in evt) {
                isEscape = (evt.key == "Escape" || evt.key == "Esc");
            } else {
                isEscape = (evt.keyCode == 27);
            }
            if (isEscape) {
                _hideTestDrive();
            }
        };
    };

    var _outOfModalClickListener = function () {
        window.onclick = function (event) {
            var modal = document.getElementsByClassName('test-drive')[0];

            if (event.target == modal) {
                _hideTestDrive();
            }
        }
    };

    return {
        init: function (Args) {
            _SiteId = Args.SiteId || _SiteId;

            _appendWidgetFrame();
            _addCss();
            _escapeListener();
            _outOfModalClickListener();
        },
        openTestDrive: function (Args) {
            var carVin = Args.carVin || "";
            var carImageUrl = Args.carImageUrl || "";
            var carTitle = Args.carTitle || "";
            var carEngine = Args.carEngine || "";
            var carYear = Args.carYear || "";
            var carColor = Args.carColor || "";
            var carTransmission = Args.carTransmission || "";
            var carFuel = Args.carFuel || "";

            _appendTestDriveFrame(carVin, carImageUrl, carTitle, carEngine, carYear, carColor, carTransmission, carFuel);
            _showTestDrive();
        },
        closeTestDrive: _hideTestDrive
    };
}());

// =======================================================================//
// Widget Initialization                                                  //
// =======================================================================//

window.openTestDrive = TestDrive.openTestDrive;
