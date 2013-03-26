var crx = (function () {

    var appData = {};

    var initAction = {
        act: function () {
            var user = localStorage.getItem('user');
            if (user) {
                appData.user = JSON.parse(user);
                if (this.checkToken()) {
                    this.setPopup();
                    this.loadPreData();
                } else {
                    this.setWindow();
                }
            } else {
                this.setWindow();
                ext.regResponse('user-login', function (data) {
                    $.ajax({
                        url: api.authorize(false),
                        data: data,
                        type: "post",
                        dataType: "json",
                        async: false,
                        success: function (res) {
                            if (res.auth == false) {
                                return false;
                            }
                            appData.user = res.user;
                            localStorage.setItem('user', JSON.stringify(res.user));
                            chrome.windows.remove(appData.loginWindow, function () {
                                initAction.setPopup();
                                initAction.loadPreData();
                            });
                        }
                    });
                    return false;
                });
            }
        },
        setPopup: function () {
            chrome.browserAction.setPopup({
                popup: "popup.html"
            });
            ext.regResponse('popup-init', function () {
                return appData;
            });
            ext.regResponse('popup-update', function (data) {
                var url = chrome.extension.getURL(data.page);
                var created = false;
                chrome.tabs.getAllInWindow(function (tabs) {
                    $.each(tabs, function (i, tab) {
                        if (tab.url == url) {
                            created = true;
                        }
                    });
                    if (!created) {
                        chrome.tabs.create({
                            url: url
                        });
                    }
                });
            });
        },
        setWindow: function () {
            chrome.browserAction.onClicked.addListener(function () {
                chrome.windows.getAll({}, function (windows) {
                    var i = 0;
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
        },
        checkToken: function () {
            var res = $.ajax({
                url: api.authorize(true),
                data: {
                    user: appData.user.user_id,
                    token: appData.user.token
                },
                type: "post",
                dataType: "json",
                async: false
            }).responseText;
            return JSON.parse(res).auth;
        },
        loadPreData: function () {
            $.ajax({
                url: api.pre(),
                dataType: "json",
                async: false,
                success: function (json) {
                    appData.blogPrivateNumber = json.nodeCount;
                    appData.blogPrivate = json.node;
                    appData.commentPrivateNumber = json.commentCount;
                    appData.commentPrivate = json.comment;
                }
            });
        }
    };

    var pageAction = {
        act: function () {

        }
    };

    return {
        run: function () {
            ext.listenResponse();
            initAction.act();
            pageAction.act();
        },
        get: function (name) {
            return appData[name];
        }
    }

}());
