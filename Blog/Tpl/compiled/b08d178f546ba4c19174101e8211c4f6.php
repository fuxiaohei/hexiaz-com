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
    <div id="wrapper">
        <div id="blog-<?php echo $blog->node_id ?>" class="blog-wrapper">
            <div id="blog-meta">
                <p class="blog-time"><?php echo $blog->create_time_format ?></p>
                <p class="blog-author">作者： <a href="#">傅小黑</a></p>
                <p class="blog-category">分类： <a href="/<?php echo $blog->category_link ?>" title="查看分类 <?php echo $blog->category_name ?>"><?php echo $blog->category_name ?></a></p>
                <p class="blog-comment">评论： <a class="num" href="#comment-list"><?php echo $blog->comment_count ?></a></p>
                <p class="blog-view">阅读： <span class="num"><?php echo $blog->view_count ?></span></p>
            </div>
            <div id="blog-main">
                <h2 class="blog-title"><a href="/<?php echo $blog->link ?>" title="<?php echo $blog->node_title ?>"><?php echo $blog->node_title ?></a></h2>
                <div class="blog-content">
                    <?php echo $blog->node_content ?>
                </div>
            </div>
        </div>
        <div class="comment-wrapper">
            <div class="comment-meta"><?php if($blog->comment_count < 1){ ?>
                <h4 class="comment-title">还没有评论</h4>
                <?php }else{ ?>
                <h4 class="comment-title"><strong><?php echo $blog->comment_count ?></strong>条评论</h4>
                <?php } ?>
            </div>
            <div class="comment-main"><?php if($comments){ ?>
                <ul id="comment-list">
                    <?php  function children($comments){  ?>
                    <ul class="comment-children"><?php foreach($comments as $comment){ ?>
                        <li class="comment-item" id="comment-<?php echo $comment->comment_id ?>">
                            <img class="comment-avatar" src="<?php echo $comment->author_avatar ?>" alt="<?php echo $comment->author ?>"/>
                            <p class="comment-info"><a class="comment-author" href="<?php echo $comment->author_url ?>" target="_blank" title="<?php echo $comment->author ?>"><?php echo $comment->author ?></a>
                                <span class="comment-time"><?php echo $comment->create_time_diff ?></span>
                                <a class="comment-reply" href="#comment-<?php echo $comment->comment_id ?>">回复</a>
                            </p>
                            <p class="comment-content"><?php echo $comment->content ?></p>
                            <?php if($comment->children){ ?>
                            <?php  children($comment->children);  ?>
                            <?php } ?>
                        </li><?php } ?>
                    </ul>
                    <?php  }  ?>
                    <?php foreach($comments as $k=>$comment){ ?>
                    <li class="comment-item comment-top" id="comment-<?php echo $comment->comment_id ?>">
                        <img class="comment-avatar" src="<?php echo $comment->author_avatar ?>" alt="<?php echo $comment->author ?>"/>
                        <p class="comment-info"><a class="comment-author" href="<?php echo $comment->author_url ?>" target="_blank" title="<?php echo $comment->author ?>"><?php echo $comment->author ?></a>
                            <span class="comment-time"><?php echo $comment->create_time_diff ?></span>
                            <a class="comment-reply" href="#comment-<?php echo $comment->comment_id ?>">回复</a>
                            <span class="comment-floor">#<?php echo $k+1 ?></span>
                        </p>
                        <p class="comment-content"><?php echo $comment->content ?></p>
                        <?php if($comment->children){ ?>
                        <?php  children($comment->children);  ?>
                        <?php } ?>
                    </li><?php } ?>
                </ul><?php } ?>
                <script id="comment-item-tpl" type="text/template">
                    <!--
                        <img class="comment-avatar" src="{img}" alt=""/>
                        <p class="comment-info"><a class="comment-author" href="{url}">{name}</a>
                        <span class="comment-time">{time}</span>
                        </p>
                        <p class="comment-content">{content}</p>
                    -->
                </script>
                <div id="comment-new" class="comment-item"></div>
                <form id="comment-form" action="">
                    <div class="comment-form-info">
                        <p class="item"><label for="comment-author">称呼<span class="req">*</span></label>
                            <input id="comment-author" class="ipt" type="text" name="author" required="required" placeholder="输入您的称呼"/></p>
                        <p class="item"><label for="comment-email">邮箱<span class="req">*</span></label>
                            <input id="comment-email" class="ipt" type="email" name="email" required="required" placeholder="输入联系邮箱，不会公开！"/></p>
                        <p class="item"><label for="comment-url">网址</label>
                            <input id="comment-url" type="url" class="ipt" placeholder="输入联系网址，爱写不写"/></p>
                    </div>
                    <div class="comment-form-content">
                        <textarea name="content" id="comment-content" class="ipt" required="required"></textarea>
                        <input id="comment-submit" type="submit" value="Post Comment"/>
                        <input type="hidden" name="node" value="<?php echo $blog->node_id ?>"/>
                        <input id="comment-parent" type="hidden" name="parent" value="0"/>
                    </div>
                </form>
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