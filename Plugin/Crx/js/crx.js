var crx = (function () {

    var appData = {};

    var initAction = {
        act: function () {
            var user = localStorage.getItem('user');
            if (user) {
                appData.user = JSON.parse(user);
                this.setPopup();
            } else {
                this.setWindow();
            }
        },
        setPopup: function () {
            chrome.browserAction.setPopup({
                popup: "popup.html"
            });
        },
        setWindow: function () {
            chrome.browserAction.onClicked.addListener(function () {
                chrome.windows.getAll({}, function (windows) {
                    var i;
                    if (appData.loginWindow > 0) {
                        for (i in windows) {
                            if (appData.loginWindow == windows[i].id) {
                                chrome.windows.update(appData.loginWindow, {'focused': true});
                                console.log('window had created');
                                return true;
                            }
                        }
                    }
                    var loginUrl = chrome.extension.getURL('login.html');
                    var loginWidth = 280;
                    var loginHeight = 320;
                    chrome.windows.create({
                        url: loginUrl,
                        left: parseInt(screen.availWidth / 2 - loginWidth / 2),
                        top: parseInt(screen.availHeight / 2 - loginHeight / 2),
                        width: loginWidth,
                        height: loginHeight,
                        type: 'popup'
                    }, function (window) {
                        appData.loginWindow = window.id;
                    });
                    return true;
                });
            });
        }
    };

    return {
        run: function () {
            ext.listenResponse();
            ext.regResponse('user-login', function (arg) {
                console.log(arg);
                return {'token': '66666'};
            });
            initAction.act();
        },
        get: function (name) {
            return appData[name];
        }
    }

}());
