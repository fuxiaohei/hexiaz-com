$(document).ready(function () {
    $('#login-form').bind('submit', function (e) {
        e.preventDefault();
        ext.sendRequest('user-login', {
            'user': $('#name').val(),
            'password': $('#password').val()
        }, function (res) {
            if (res === false) {
                alert('登录失败');
            }
        })
    })
});
