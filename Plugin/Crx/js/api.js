var api = {
    "host": "http://api.hexi.com/",
    "setEnv": function (env) {
        if (env == 'dev') {
            api.host = 'http://api.hexi.com/';
        } else {
            api.host = 'http://api.fuxiaohei.com/'
        }
    },
    "authorize": function (check) {
        return api.host + 'authorize' + (check ? '?check=check' : '');
    },
    "pre": function () {
        return api.host + 'pre';
    }
};