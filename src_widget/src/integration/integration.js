(function () {

//=======================================================================//
// Widget buttons injector                                               //
//=======================================================================//

    var TruckworldButtonsInjector = {
        /// initialization
        init: function (injectWidgetToVlp, injectWidgetToVdp) {
            var self = this;

            if (injectWidgetToVdp) {
                // self.appendTestdriveVdpButton();
                self.appendTestDriveVdpButtonStyles();
            }

            if (injectWidgetToVlp) {
                // self.initTestDriveVlpButtons();
                self.appendTestDriveVlpButtonStyles();
            }
        },

        appendTestDriveVdpButtonStyles: function () {
            var css = '.btn-testdrive-large { display: table !important; }';
            var head = document.head || document.getElementsByTagName('head')[0];
            var style = document.createElement('style');

            style.type = 'text/css';
            if (style.styleSheet) {
                style.styleSheet.cssText = css;
            } else {
                style.appendChild(document.createTextNode(css));
            }

            head.appendChild(style);
        },

        appendTestDriveVlpButtonStyles: function () {
            var css = '.btn-testdrive { display: table !important; }';
            var head = document.head || document.getElementsByTagName('head')[0];
            var style = document.createElement('style');

            style.type = 'text/css';
            if (style.styleSheet) {
                style.styleSheet.cssText = css;
            } else {
                style.appendChild(document.createTextNode(css));
            }

            head.appendChild(style);
        }
    };

    var MarinButtonsInjector = {

        /// initialization
        init: function (injectWidgetToVlp, injectWidgetToVdp) {
            var self = this;

            if (self.isCurrentPageVdp() && injectWidgetToVdp) {
                self.appendTestdriveVdpButton();
            }

            if (self.isCurrentPageVlp() && injectWidgetToVlp) {
                self.initTestDriveVlpButtons();
                self.initVlpListener();
            }
        },

        domChangesCounter: 0,
        domScrollCounter: 0,
        initVlpListener: function () {
            var self = this;

            // var resultsTable = document.getElementsByClassName("results_table")[0];
            // self.observeDOM(resultsTable, function () {
            //     console.log('dom changed');
            //     self.domChangesCounter++;
            //     if (self.domChangesCounter > 50) {
            //         return;
            //     }
            //     self.initTestDriveVlpButtons();
            // });

            window.onscroll = function () {
                self.domScrollCounter++;
                if (self.domScrollCounter > 15) {
                    self.domScrollCounter = 0;
                    self.initTestDriveVlpButtons();
                }
            };
        },

        isCurrentPageVlp: function () {
            var resultsPage = document.getElementById("results-page");
            var resultsTable = document.getElementsByClassName("results_table")[0];
            if (!resultsTable || !resultsPage) {
                return false;
            }
            return true;
        },

        initTestDriveVlpButtons: function () {
            var self = this;

            var resultsTable = document.getElementsByClassName("results_table")[0];
            var resultsTableNodes = resultsTable.childNodes;
            var tableBodyNode = null;
            for (var i = 0; i < resultsTableNodes.length; i++) {
                var node = resultsTableNodes[i];
                if (node.nodeName.toLowerCase() == "tbody") {
                    tableBodyNode = node;
                }
            }

            if (!tableBodyNode) {
                console.log('Could not find tableBodyNode');
                return false;
            }

            var tableBodyChildNodes = tableBodyNode.childNodes;
            for (var i = 0; i < tableBodyChildNodes.length; i++) {
                var node = tableBodyChildNodes[i];

                // desktop-buttons
                if (node.nodeName.toLowerCase() == "tr" &&
                    node.classList.contains("hidden-xs") &&
                    !node.classList.contains("vipdrv-added")) {
                    if (self.isVehicleNodeLoaded(node)) {
                        node.classList.add('vipdrv-added');
                        self.addTestDriveButtonToDesktopVehicleNode(node, self.parseVehicleDetailsFromDesktopNode(node));
                    }
                }

                // mobile-buttons
                if (node.nodeName.toLowerCase() == "tr" &&
                    node.classList.contains("visible-xs") &&
                    !node.classList.contains("vipdrv-added")) {
                    if (self.isVehicleNodeLoaded(node)) {
                        node.classList.add('vipdrv-added');
                        self.addTestDriveButtonToMobileVehicleNode(node, self.parseVehicleDetailsFromMobileNode(node));
                    }
                }
            }
        },

        isVehicleNodeLoaded: function (node) {
            var vehicleImageNode = node.querySelector('.vehicle-image img');
            if (vehicleImageNode) {
                var imageUrl = vehicleImageNode.getAttribute('src');
                var imageFileName = imageUrl.split('/').slice(-1)[0];

                if (imageFileName == 'loading.gif') {
                    return false;
                }
                return true;
            }
        },

        addTestDriveButtonToDesktopVehicleNode: function (node, vehicleObject) {
            var btn = document.createElement("BUTTON");
            btn.classList.add("vipdrv-marin-vlp-button", "button-bar-item", "primary-button", "button");
            var textNode = document.createTextNode("VIPdrv - Test Drive");
            btn.appendChild(textNode);
            btn.onclick = function () {
                window.TestDrive.openTestDrive(vehicleObject);
            };

            var priceBlock = node.querySelector('.vehicle-content .vehicle-price');
            if (priceBlock) {
                priceBlock.appendChild(btn);
            }
        },

        addTestDriveButtonToMobileVehicleNode: function (node, vehicleObject) {
            var btn = document.createElement("BUTTON");
            btn.classList.add("vipdrv-marin-vlp-button-mobile", "button-bar-item", "primary-button", "button");
            var textNode = document.createTextNode("VIPdrv - Test Drive");
            btn.appendChild(textNode);
            btn.onclick = function () {
                window.TestDrive.openTestDrive(vehicleObject);
            };

            var priceBlock = node.querySelector('.vehicle-card-price .vehicle-price');
            if (priceBlock) {
                priceBlock.appendChild(btn);
            }
        },

        parseVehicleDetailsFromMobileNode: function (node) {
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
                interior: null,
                drivetrain: null,
                transmission: null,
                msrp: null,
                imageUrl: null,
                vdpUrl: null
            };

            var vehicleImageNode = node.querySelector('.vehicle-image img');
            if (vehicleImageNode) {
                var imageUrl = vehicleImageNode.getAttribute('src');
                var imageFileName = imageUrl.split('/').slice(-1)[0];

                if (imageFileName == 'loading.gif') {
                    imageUrl = 'https://widget.testdrive.pw/img/default-car.png';
                }
                vehicle.imageUrl = imageUrl;
            }

            var vehicleTitleNode = node.querySelector('.vehicle-title h2 a');
            if (vehicleTitleNode) {
                vehicle.vdpUrl = vehicleTitleNode.getAttribute('href');
                vehicle.title = vehicleTitleNode.textContent;
            }

            if (vehicle.imageUrl) {
                var vehicleUrlData = vehicle.imageUrl.split('/').slice(-1)[0];
                vehicle.vin = vehicleUrlData.split('-').slice(-1)[0];
            }

            return vehicle;
        },

        parseVehicleDetailsFromDesktopNode: function (node) {
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
                interior: null,
                drivetrain: null,
                transmission: null,
                msrp: null,
                imageUrl: null,
                vdpUrl: null
            };

            var vehicleImageNode = node.querySelector('.vehicle-image img');
            if (vehicleImageNode) {
                var imageUrl = vehicleImageNode.getAttribute('src');
                var imageFileName = imageUrl.split('/').slice(-1)[0];

                if (imageFileName == 'loading.gif') {
                    imageUrl = 'https://widget.testdrive.pw/img/default-car.png';
                }
                vehicle.imageUrl = imageUrl;
            }

            var vehicleTitleNode = node.querySelector('.vehicle-title h2 a');
            if (vehicleTitleNode) {
                vehicle.vdpUrl = vehicleTitleNode.getAttribute('href');
                vehicle.title = vehicleTitleNode.textContent;
                vehicle.year = vehicle.title.split(' ')[1];
            }

            var vinNode = node.querySelector('.vinstock');
            if (vinNode) {
                var vinRegex = /VIN: (.*)/g;
                var vinMatch = vinRegex.exec(vinNode.textContent);
                vehicle.vin = vinMatch ? vinMatch[1] : null;
            }

            // TODO: add year

            var vehicleContent = node.querySelector(".vehicle-content");
            if (vehicleContent) {
                var textContent = vehicleContent.innerText;

                var bodyRegex = /Body: (.*)/g;
                var bodyMatch = bodyRegex.exec(textContent);
                vehicle.body = bodyMatch ? bodyMatch[1] : null; // TODO: add body parameter

                var exteriorRegex = /Exterior: (.*)/g;
                var exteriorMatch = exteriorRegex.exec(textContent);
                vehicle.exterior = exteriorMatch ? exteriorMatch[1] : null;

                var engineRegex = /Engine: (.*)/g;
                var engineMatch = engineRegex.exec(textContent);
                vehicle.engine = engineMatch ? engineMatch[1] : null;

                var transRegex = /Trans: (.*)/g;
                var transMatch = transRegex.exec(textContent);
                vehicle.transmission = transMatch ? transMatch[1] : null;
            }

            return vehicle;
        },

        /// vdp injector
        isCurrentPageVdp: function () {
            var element = document.getElementById('details-page-ctabox');

            if (element) {
                return true;
            }
            return false;
        },

        appendTestdriveVdpButton: function () {
            var self = this;

            var vdpVehicle = {
                vin: null,
                stock: null,
                year: null,
                make: null,
                model: null,
                body: null,
                title: null,
                engine: null,
                exterior: null,
                interior: null,
                drivetrain: null,
                transmission: null,
                msrp: null,
                imageUrl: null,
                vdpUrl: null
            };

            var title = document.getElementsByClassName("vehicle-title")[0];
            if (title) {
                vdpVehicle.title = title.innerHTML;
            }

            var image = document.querySelector(".owl-carousel .owl-item img");
            if (image) {
                vdpVehicle.imageUrl = image.getAttribute('src');
            }

            vdpVehicle.vdpUrl = window.location.href;

            var urlParams = self.vehicleDetailsFromUrl(window.location.pathname);
            vdpVehicle.vin = urlParams.vin;
            vdpVehicle.year = urlParams.year;

            var detailsPageCtabox = document.getElementById('details-page-ctabox');

            var btn = document.createElement("BUTTON");
            btn.classList.add("vipdrv-marin-vdp-button");

            var infoWrapper = document.querySelector(".details-page-row .basic-info-wrapper");
            var infoWrapperText = null;

            if (infoWrapper) {
                infoWrapperText = infoWrapper.innerText;
            }
            var carDetailsFromHtml = self.vehicleDetailsFromVdpHtml(infoWrapperText);

            vdpVehicle.engine = carDetailsFromHtml.engine;
            vdpVehicle.exterior = carDetailsFromHtml.exterior;
            vdpVehicle.transmission = carDetailsFromHtml.trans;

            btn.onclick = function () {
                window.TestDrive.openTestDrive(vdpVehicle);
            };
            detailsPageCtabox.appendChild(btn);
        },

        /// helpers
        vehicleDetailsFromUrl: function (vdpPageUrl) {
            var result = {
                year: null,
                vin: null
            };

            var vehicleUrl = vdpPageUrl.split('/')[2];
            var vehicleUrlParams;
            if (vehicleUrl) {
                vehicleUrlParams = vehicleUrl.split('-');
            }

            if (vehicleUrlParams[1]) {
                result.year = vehicleUrlParams[1];
            }
            result.vin = vehicleUrlParams[vehicleUrlParams.length - 1];

            return result;
        },

        vehicleDetailsFromVdpHtml: function (plainText) {
            var result = {
                body: null,
                exterior: null,
                engine: null,
                trans: null
            };

            var bodyRegex = /Body: (.*)/g;
            var bodyMatch = bodyRegex.exec(plainText);
            result.body = bodyMatch ? bodyMatch[1] : null;

            var exteriorRegex = /Exterior: (.*)/g;
            var exteriorMatch = exteriorRegex.exec(plainText);
            result.exterior = exteriorMatch ? exteriorMatch[1] : null;

            var engineRegex = /Engine: (.*)/g;
            var engineMatch = engineRegex.exec(plainText);
            result.engine = engineMatch ? engineMatch[1] : null;

            var transRegex = /Trans: (.*)/g;
            var transMatch = transRegex.exec(plainText);
            result.trans = transMatch ? transMatch[1] : null;

            return result;
        },

        /// extensions
        observeDOM: (function () {
            var MutationObserver = window.MutationObserver || window.WebKitMutationObserver,
                eventListenerSupported = window.addEventListener;

            return function (obj, callback) {
                if (MutationObserver) {
                    // define a new observer
                    var obs = new MutationObserver(function (mutations, observer) {
                        if (mutations[0].addedNodes.length || mutations[0].removedNodes.length)
                            callback();
                    });
                    // have the observer observe foo for changes in children
                    obs.observe(obj, {childList: true, subtree: true});
                }
                else if (eventListenerSupported) {
                    obj.addEventListener('DOMNodeInserted', callback, false);
                    obj.addEventListener('DOMNodeRemoved', callback, false);
                }
            };
        })()
    };

//=======================================================================//
// TestDrive Initialization                                              //
//=======================================================================//

    window.TestDrive = window.TestDrive || (function () {
        // variables
        var _siteId = '%siteId%'; // default siteId
        var _WidgetUrl = '%widgetUrl%'; // https://widget.testdrive.pw/ // %widgetUrl%
        var _useAutoIntegration = null;
        var _injectWidgetToVlp = null;
        var _injectWidgetToVdp = null;

        // methods
        var _appendTestDriveFrame = function (ulrParams, siteId) {
            ulrParams.hash = Math.random().toString(36).substring(7);
            ulrParams.siteId = siteId;
            var widgetUrl = _buildUrl(_WidgetUrl, ulrParams);

            var html =
                `<div class="test-drive__content">
                 <div class="frame-header">
                    <div class="frame-header__title">Test Drive Booking</div>
                    <div class="frame-header__cross" onclick="TestDrive.closeTestDrive()">&#10006;</div>
                 </div>
                 <img id="test-drive__frame-spinner" src="http://www.testdrive.pw/spinner.gif">
                 <iframe class="test-drive__frame" src="${widgetUrl}" frameBorder="0" onload="document.getElementById('test-drive__frame-spinner').style.display='none';"></iframe>
             </div>`;

            document.getElementsByClassName('test-drive')[0].innerHTML = html;
        };

        var _appendTestDriveFrameWrapper = function () {
            var elemDiv = document.createElement('div');
            elemDiv.className = "test-drive";

            if (!document.body) {
                document.body = document.createElement("body");
                document.body.style.display = 'table';
            }

            document.body.appendChild(elemDiv);
        };

        var _showTestDriveFrame = function () {
            document.getElementsByClassName('test-drive')[0].classList.add('test-drive-visible');
            document.documentElement.classList.add('vipdrv--disable-scroll');
            window.scrollTo(0, 0);

            setTimeout(function () {
                document.getElementsByClassName('test-drive__content')[0].classList.add('test-drive__content-visible');
            }, 200);
        };

        var _hideTestDriveFrame = function () {
            document.getElementsByClassName('test-drive__content')[0].classList.remove('test-drive__content-visible');
            document.documentElement.classList.remove('vipdrv--disable-scroll');

            setTimeout(function () {
                document.getElementsByClassName('test-drive')[0].classList.remove('test-drive-visible');
            }, 200);
        };

        var _changeMobileBrowserBarColor = function () {
            var head = document.head;

            var chromeMeta = document.createElement('meta');
            chromeMeta.id = 'TestDriveChromeMeta';
            chromeMeta.name = 'theme-color';
            chromeMeta.content = "#007bff";
            head.appendChild(chromeMeta);

            var windowsPhoneMeta = document.createElement('meta');
            windowsPhoneMeta.id = 'TestDriveWindowsPhoneMeta';
            windowsPhoneMeta.name = 'msapplication-navbutton-color';
            windowsPhoneMeta.content = "#007bff";
            head.appendChild(windowsPhoneMeta);

            var safariMeta = document.createElement('meta');
            safariMeta.id = 'TestDriveSafariMeta';
            safariMeta.name = 'apple-mobile-web-app-status-bar-style';
            safariMeta.content = "#007bff";

            head.appendChild(safariMeta);
        };

        var _restoreDefaultMobileBrowserBarColor = function () {
            var colorHeaders = ['TestDriveChromeMeta', 'TestDriveWindowsPhoneMeta', 'TestDriveSafariMeta'];

            for (var key in colorHeaders) {
                var id = colorHeaders[key];
                var element = document.getElementById(id);
                if (element) {
                    element.content = "#f2f2f2";
                    element.remove();
                }
            }
        };

        var _appendTestDriveFrameWrapperStyles = function () {
            var head = document.head;

            var link = document.createElement('link');
            link.type = 'text/css';
            link.rel = 'stylesheet';
            link.href = _WidgetUrl + '/Integration/integration.css';
            head.appendChild(link);
        };

        var _addTestDriveFrameEventListeners = function () {
            document.onkeydown = function (evt) {
                evt = evt || window.event;
                var isEscape = false;
                if ("key" in evt) {
                    isEscape = (evt.key == "Escape" || evt.key == "Esc");
                } else {
                    isEscape = (evt.keyCode == 27);
                }
                if (isEscape) {
                    _hideTestDriveFrame();
                }
            };

            window.onclick = function (event) {
                var modal = document.getElementsByClassName('test-drive')[0];

                if (event.target == modal) {
                    _hideTestDriveFrame();
                }
            };

            window.addEventListener("resize", function () {
                var div = document.getElementsByClassName('test-drive__frame')[0];
                if (div) {
                    div.style.height = 'calc(100% - 50px)';
                }
            });
        };

        var _injectWidgetButtons = function (siteId, injectWidgetToVlp, injectWidgetToVdp) {
            if (!injectWidgetToVlp && !injectWidgetToVdp) {
                return false;
            }

            // TODO: hardcoded site id's
            if (siteId == 28) {
                TruckworldButtonsInjector.init(injectWidgetToVlp, injectWidgetToVdp);
            }

            if (siteId == 32) {
                MarinButtonsInjector.init(injectWidgetToVlp, injectWidgetToVdp);
            }
        };

        // helpers

        var _parseArgumentsForOpenButtonEvent = function (Args) {
            var ulrParams = {};

            ulrParams.vin = Args.vin || null;
            ulrParams.stock = Args.stock || null;
            ulrParams.year = Args.year || null;
            ulrParams.make = Args.make || null;
            ulrParams.model = Args.model || null;
            ulrParams.body = Args.body || null;
            ulrParams.title = Args.title || null;
            ulrParams.engine = Args.engine || null;
            ulrParams.exterior = Args.exterior || null;
            ulrParams.interior = Args.interior || null;
            ulrParams.drivetrain = Args.drivetrain || null;
            ulrParams.transmission = Args.transmission || null;
            ulrParams.msrp = Args.msrp || null;
            ulrParams.imageUrl = Args.imageUrl || null;
            ulrParams.vdpUrl = Args.vdpUrl || null;
            ulrParams.siteId = Args.siteId || null;

            return ulrParams;
        };

        var _buildUrl = function (url, parameters) {
            var qs = "";
            for (var key in parameters) {
                var value = parameters[key];
                if (value) {
                    qs += encodeURIComponent(key) + "=" + encodeURIComponent(value) + "&";
                }
            }
            if (qs.length > 0) {
                qs = qs.substring(0, qs.length - 1); //chop off last "&"
                url = url + "?" + qs;
            }
            return url;
        };

        var _initExtensions = function () {
            Element.prototype.remove = function () {
                this.parentElement.removeChild(this);
            };
            NodeList.prototype.remove = HTMLCollection.prototype.remove = function () {
                for (var i = this.length - 1; i >= 0; i--) {
                    if (this[i] && this[i].parentElement) {
                        this[i].parentElement.removeChild(this[i]);
                    }
                }
            };
        };

        var _detectSiteIdAutomatically = function () {
            var hostName = window.location.hostname;
            var sitesDictionary = {
                'www.mbofmarin.com': 32,
                'inventory.testdrive.pw': 28,
                'www.testdrive.pw': 28,
                'localhost': 28,
            };

            return sitesDictionary[hostName] || null;
        };

        // output
        return {
            init: function (Args) {
                _siteId = Args.siteId || _detectSiteIdAutomatically();
                _useAutoIntegration = Args.useAutoIntegration || true;
                _injectWidgetToVlp = Args.injectWidgetToVlp || true;
                _injectWidgetToVdp = Args.injectWidgetToVdp || true;

                if (!_siteId) {
                    console.log('Automatic initialization for ' + window.location.hostname + ' is missing');
                    return;
                }

                _appendTestDriveFrameWrapper();
                _appendTestDriveFrameWrapperStyles();
                _addTestDriveFrameEventListeners();
                _initExtensions();
                _injectWidgetButtons(_siteId, _injectWidgetToVlp, _injectWidgetToVdp);
            },
            openTestDrive: function (Args) {
                _appendTestDriveFrame(_parseArgumentsForOpenButtonEvent(Args), _siteId);
                _changeMobileBrowserBarColor();
                _showTestDriveFrame();
            },
            closeTestDrive: function () {
                _restoreDefaultMobileBrowserBarColor();
                _hideTestDriveFrame();
            }
        };
    }());
})();

//=======================================================================//
// Debug                                                                 //
//=======================================================================//
//
// https://widget.testdrive.pw
//
//window.TestDrive.init({injectWidgetToVlp: true, injectWidgetToVdp: true});