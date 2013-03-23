<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,user-scalable=1"/>
    <?php if($category_current){ ?>
    <title><?php echo $category_current->category_name ?> - 傅小黑.COM<?php echo $page>1?' - 页 '.$page:'' ?></title>
    <?php }elseif($blog->node_id){ ?>
    <title><?php echo $blog->node_title ?> - 傅小黑.COM</title>
    <?php }elseif($page > 1){ ?>
    <title>傅小黑.COM<?php echo $page>1?' - 页 '.$page:'' ?></title>
    <?php }elseif($all){ ?>
    <title>归档 - 傅小黑.COM</title>
    <?php }else{ ?>
    <title>傅小黑.COM</title>
    <?php } ?>
    <link rel="stylesheet" href="/public/css/reset.css" type="text/css" media="screen"/>
    <link rel="stylesheet" href="/public/css/style.css" type="text/css" media="screen"/>
    <link rel="stylesheet" href="/public/css/responsive.css" type="text/css" media="screen and (max-width:800px)"/>
</head>
<body>
<div id="header" class="clear">
    <h1 id="logo" class="left"><a href="/">傅小黑.COM</a></h1>
    <ul id="nav" class="left">
        <li><a href="/">首页</a></li>
        <li><a href="/all/">归档</a></li>
        <li><a href="/message/">留言</a></li>
    </ul>
    <ul id="sub-nav" class="right">
        <li><a class="sina" href="http://weibo.com/fuxiaohei" target="_blank" rel="nofollow external"></a></li>
        <li><a class="osc" href="http://my.oschina.net/fuxiaohei" target="_blank" rel="nofollow external"></a></li>
        <li><a class="git" href="https://github.com/fuxiaohei/" target="_blank" rel="nofollow external"></a></li>
        <li><a class="feed" href="/feed.xml"></a></li>
    </ul>
</div>
<div id="main">
    <div id="all"><?php foreach($all as $m=>$blog){ ?>
        <div class="blog-month">
            <span class="blog-month-name"><?php echo $m ?></span>
            <ul class="blog-month-list"><?php foreach($blog as $b){ ?>
                <li><a href="/<?php echo $b->link ?>"><?php echo $b->node_title ?></a><span class="blog-comment">(<?php echo $b->comment_count
                     ?>)</span></li>
                <?php } ?>
            </ul>
        </div><?php } ?>
    </div>
</div>
<div id="footer" class="clear">
    <p class="links"><a href="/">首页</a><a href="/all/">归档</a><a href="/message/">留言</a><a href="/sitemap.xml">地图</a><a href="/feed.xml">订阅</a><a href="#">主站</a></p>
    <p class="right">Copyright © 2012-2013 FuXiaoHei. All rights reserved.</p>
    <p><strong>Not</strong> Powered by <a href="/" rel="external">WordPress</a>, hosted on <a href="http://www.paulhost.com" rel="nofollow external">PaulHost</a>. | Theme is a private theme.</p>
</div>
<script type="text/javascript" src="/public/js/jquery.min.js?v=1.8.3"></script>
<script type="text/javascript" src="/public/js/main.js?v=2013.3.18"></script>
<script type="text/javascript" src="http://tajs.qq.com/stats?sId=22978993" charset="UTF-8"></script>
</body>
</html>