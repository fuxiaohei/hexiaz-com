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
    <div id="content"><?php foreach($blog as $b){ ?>
        <div class="blog">
            <div class="blog-header">
                <a class="blog-comment" href="/<?php echo $b->link ?>"><?php echo $b->comment_count ?></a>
                <h2 class="blog-title"><a href="/<?php echo $b->link ?>" title="<?php echo $b->node_title ?>"><?php echo $b->node_title ?></a></h2>
                <span class="blog-create"><?php echo $b->create_time_format ?></span>
            </div>
            <div class="blog-desc"><?php echo $b->node_description ?></div>
            <div class="blog-meta">
                <span class="blog-author">作者：<a href="#">傅小黑</a></span>
                <span class="blog-category">分类：<a href="/<?php echo $b->category_link ?>" title="查看分类 <?php echo $b->category_name ?>"><?php echo $b->category_name ?></a></span>
                <a class="blog-more" href="/<?php echo $b->link ?>">阅读全文</a>
            </div>
        </div>
        <?php } ?>
        <?php if(!$blog){ ?>
        <div class="blog">
            <div class="blog-header">
                <h2 class="blog-title"><a href="?page=<?php echo $page-1 ?>" title="返回上一页">Oops，当前页没有内容了！！！</a></h2>
            </div>
        </div>
        <?php } ?>
        <div class="page clear">
            <?php if($page > 1){ ?><a class="prev left" href="?page=<?php echo $page-1 ?>" title="第<?php echo $page-1 ?>页">上一页</a><?php } ?>
            <?php if(count($blog) == 4){ ?><a class="next right" href="?page=<?php echo $page+1 ?>" title="第<?php echo $page+1 ?>页">下一页</a><?php } ?>
        </div>
    </div>
    <div id="side">
        <div class="side" id="search-side">
            <div class="side-container">
                <input id="search" class="ipt" type="search" name="q" placeholder="输入搜索词，目前没用"/>
                <label for="search">搜索</label>
            </div>
        </div>
        <div class="side" id="category-side">
            <h4 class="side-title">文章分类</h4>
            <ul class="side-container side-list">
                <?php foreach($category as $c){ ?>
                <li><span class="icon"></span><a href="/<?php echo $c->category_link ?>" title="查看分类 <?php echo $c->category_name ?>"><?php echo $c->category_name ?></a>
                    <span class="num">(<?php echo $c->node_count ?>)</span></li>
                <?php } ?>
            </ul>
        </div>
        <div class="side" id="pop-side">
            <h4 class="side-title">热评文章</h4>
            <ul class="side-container side-list">
                <?php foreach($pop as $p){ ?>
                <li><span class="icon"></span><a href="/<?php echo $p->link ?>" title="<?php echo $p->node_title ?>"><?php echo $p->node_title ?></a><span class="num">(<?php echo $p->comment_count ?>)</span></li>
                <?php } ?>
            </ul>
        </div>
        <div class="side" id="comment-side">
            <h4 class="side-title">最新评论</h4>
            <div class="container">
                <?php foreach($cmt as $c){ ?>
                <div class="comment-item"><a class="name" href="/<?php echo $c->node_link ?>"><?php echo $c->author ?></a>：<?php echo $c->content ?>
                </div>
                <?php } ?>
            </div>
        </div>
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