function auto_size() {
    var $side = $('#side');
    if ($side.length > 0) {
        $('#content').css('min-height', $side.height());
    }
}

function auto_ie_compatible() {
    if ($.browser.msie) {
        $('body').addClass('ms-ie');
    }
    if ('localStorage' in window) {
        window.commentLocalEnv = true;
    } else {
        var $browserNotice = $('<div id="browser" class="clear">您的浏览器版本太低，功能无法完全支持！</div>');
        $browserNotice.css({
            padding: '4px 36px',
            background: '#FEFFEE',
            borderBottom: '1px solid #DDD',
            color: "#888"
        });
        var $icon = $('<span class="icon"></span>')
            .css({
                marginRight: '8px',
                display: 'inline-block',
                width: '8px',
                height: '8px',
                marginTop: '8px',
                background: '#E32429',
                borderRadius: '4px',
                verticalAlign: 'top'
            });
        $browserNotice.prepend($icon);
        var browser = [
            {
                title: '火狐浏览器',
                link: 'http://www.mozilla.org/en-US/',
                image: '/public/img/browser_ff.png'
            },
            {
                title: '谷歌浏览器',
                link: 'http://www.google.com/chrome/',
                image: '/public/img/browser_chrome.png'
            }
        ];
        $(browser).each(function (i, item) {
            var $a = $('<a></a>', {
                href: item.link,
                title: item.title,
                target: '_blank',
                class: 'right'
            }).text(item.title).css({
                    marginLeft: '12px',
                    background: 'url("' + item.image + '") left center no-repeat',
                    paddingLeft: '30px',
                    fontSize: '12px',
                    color: '#888'
                });
            $browserNotice.append($a);
        });
        $('body').prepend($browserNotice);
        window.commentLocalEnv = false;
    }
}

function init_comment_event() {
    var $form = $('#comment-form');
    if ($form.length < 1) {
        return false;
    }
    if (!window.commentLocalEnv) {
        $('#comment-submit').css({
            backgroundColor: "#E32529",
            cursor: "default"
        }).val('Not Supported');
        return false;
    }
    $form.submit(function (e) {
        e.preventDefault();
        var reg;
        var $author = $('#comment-author');
        if ($author.val().length < 2) {
            $author.focus();
            return false;
        }
        var $mail = $('#comment-email');
        reg = /^([a-z0-9_\.\-\+]+)@([\da-z\.\-]+)\.([a-z\.]{2,6})$/i;
        if (!reg.test($mail.val())) {
            $mail.focus();
            return false;
        }
        var $content = $('#comment-content');
        if ($content.val().length < 2) {
            $content.focus();
            return false;
        }
        var $url = $('#comment-url');
        if ($url.val()) {
            reg = /^(https?:\/\/)?[\da-z\.\-]+\.[a-z\.]{2,6}[#&+_\?\/\w \.\-=]*$/i;
            if (!reg.test($url.val())) {
                $url.focus();
                return false;
            }
        }
        $.ajax({
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-Comment-Timestamp', localStorage.commentTimeStamp);
            },
            type: "post",
            data: $form.serialize(),
            dataType: 'json',
            success: function (json) {
                if (json.res) {
                    localStorage.commentTimeStamp = parseInt((new Date()).getTime() / 1000);
                    var tpl = $('#comment-item-tpl')[0].innerHTML.replace('<!--', '').replace('-->', '');
                    tpl = tpl.replace('{img}', json.comment.author_avatar)
                        .replace('{url}', json.comment.author_url)
                        .replace('{name}', json.comment.author)
                        .replace('{time}', json.comment.create_time_diff)
                        .replace('{content}', json.comment.content);
                    $('#comment-new').html(tpl).show();
                } else {
                    $('#comment-submit').val(json.msg).addClass('invalid');
                    setTimeout(function () {
                        $('#comment-submit').removeClass('invalid').val('Post Comment');
                    }, 2500);
                }
            }
        });
        return false;
    });
    localStorage.commentTimeStamp = parseInt((new Date()).getTime() / 1000);
    return true;
}

function init_comment_reply() {
    var $list = $('#comment-list');
    if ($list.length < 1) {
        return false;
    }
    var $new = $('#comment-new');
    $list.on('click', '.comment-reply', function (e) {
        e.preventDefault();
        var comment_id = $(this).attr('href');
        var $comment = $(comment_id).clone(false);
        $comment.find('ul').remove();
        $comment.find('.comment-reply').text('取消回复').show();
        $new.html($comment.html()).show();
        $('body').animate({
            'scrollTop': $new.offset().top - 100
        });
        $('#comment-parent').val(comment_id.replace('#comment-', ''));
    });
    $new.on('click', '.comment-reply', function (e) {
        e.preventDefault();
        $new.empty().hide();
        $('#comment-parent').val(0);
    });
    return true;
}

function init_comment_tab() {

}

function init_code_highlight() {
    var pre = $('.prettyprint');
    if (pre.length > 0) {
        pre.addClass('linenums');
        $("<link>")
            .attr({ rel: "stylesheet",
                type: "text/css",
                href: "/public/css/prettify.css"
            })
            .appendTo("head");
        $.getScript('/public/js/prettify.js',function(){
            prettyPrint();
        });
    }
}

$(document).ready(function () {
    auto_ie_compatible();
    auto_size();
    init_comment_reply();
    init_comment_event();
    init_comment_tab();
    init_code_highlight();
});
