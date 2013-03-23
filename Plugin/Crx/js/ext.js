var ext = (function () {

    var events = {};

    return {
        sendRequest: function (name, data, result) {
            data._extName = name;
            chrome.extension.sendRequest(data, result);
        },
        listenResponse: function () {
            chrome.extension.onRequest.addListener(function (request, sender, response) {
                if (events[request._extName]) {
                    var func = events[request._extName];
                    delete request._extName;
                    response(func(request));
                } else {
                    console.log('no response for request: ' + request._extName);
                }
            });
        },
        regResponse: function (name, func) {
            events[name] = func;
        }
    }

}());