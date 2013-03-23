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
        <li><a href="/all/" title="查看文章归档">归档</a></li>
        <li><a href="/message/" title="直接给我留言">留言</a></li>
    </ul>
    <ul id="sub-nav" class="right">
        <li><a class="sina" href="http://weibo.com/fuxiaohei" target="_blank" rel="nofollow external" title="新浪微博 @傅小黑 "></a></li>
        <li><a class="osc" href="http://my.oschina.net/fuxiaohei" target="_blank" rel="nofollow external" title="开源中国社区 @傅小黑 "></a></li>
        <li><a class="git" href="https://github.com/fuxiaohei/" target="_blank" rel="nofollow external" title="GitHub @fuxiaohei"></a></li>
        <li><a class="feed" href="/feed.xml" title="订阅文章"></a></li>
    </ul>
</div>
<div id="main">
    <script type="text/javascript" src="http://www.qq.com/404/search_children.js?edition=small" charset="utf-8"></script>
</div>
</body>
</html>