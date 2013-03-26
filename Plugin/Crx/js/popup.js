$(document).ready(function () {
    ext.sendRequest('popup-init', {}, function (appData) {
        var $u = $('#user');
        $u.find('.avatar').attr('src', appData.user.user_avatar);
        $u.find('.name strong').text(appData.user.user_nickname);
        $u.find('.email').text(appData.user.user_email);
        if (appData.blogPrivateNumber > 0) {
            $('#node-private .num').text(appData.blogPrivateNumber).show();
        }
        if (appData.commentPrivateNumber) {
            $('#comment-private .num').text(appData.commentPrivateNumber).show();
        }
    });
    $('#update a[title]').tooltip({
        offset: [-8, 0]
    });
    $('#update').on('click', 'a', function (e) {
        e.preventDefault();
        ext.sendRequest('popup-update', {
            'page': $(this).attr('href')
        });
    });
});