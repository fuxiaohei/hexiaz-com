<?php

/**
 * 默认控制器
 * Class indexController
 */
class indexController extends Controller {

    /**
     * 显示博客列表
     * @param string $condition
     * @param int $page
     */
    private function blogList($condition, $page) {
        $this->assign('category', Model::exec('category', 'getAll'))
            ->assign('page', $page)
            ->assign('blog', Model::exec('blog', 'getList', array($condition, $page, 4)))
            ->assign('pop', Model::exec('blog', 'getPop'))
            ->assign('cmt', Model::exec('comment', 'getPop', array(4)))
            ->display('index.html');
    }

    /**
     * 默认动作
     */
    public function indexAction() {
        if (Router::$param[0] && Router::$param[0] != 'index') {
            #处理404错误
            $this->redirect('/e/404.html');
            return;
        }
        #处理页码
        $page = Input::get('page');
        $page = $page < 1 ? 1 : $page;
        $this->blogList(null, $page);
    }

    /**
     * 分类列表动作
     */
    public function cAction() {
        $categorySlug = Router::$param[1];
        $category = Model::exec('category', 'getOne', array('category_key', $categorySlug));
        if (!$category) {
            $this->redirect('/e/404.html');
            return;
        }
        $this->assign('category_current', $category);
        $page = Input::get('page');
        $page = $page < 1 ? 1 : $page;
        $this->blogList('category_key = "' . $category->category_key . '"', $page);
    }

    //------------------------------------

    /**
     * 显示博客动作
     */
    public function pAction() {
        if ($this->is('post') && $this->is('ajax')) {
            $this->blogComment();
            return;
        }
        #获取博客
        $slug = Router::$param[1];
        $blog = Model::exec('blog', 'getOne', array('node_key', $slug));
        if (!$blog) {
            $this->redirect('/e/404.html');
            return;
        }
        $this->assign('blog', $blog)
            ->assign('comments', Model::exec('comment', 'getTree', array($blog->node_id)))
            ->display('blog.html');
        #更新博客信息
        $this->blogViewUpdate((int)$blog->node_id);
        $this->blogCommentUpdate((int)$blog->node_id);
    }

    /**
     * 更新博客评论统计
     * @param int $id
     */
    private function blogCommentUpdate($id) {
        if (rand(1, 100) < 10) {
            Model::exec('comment', 'updateCommentCount', array($id));
        }
    }

    /**
     * 更新博客浏览统计
     * @param int $id
     */
    private function blogViewUpdate($id) {
        if (rand(1, 100) < 50) {
            Model::exec('blog', 'updateViewCount', array($id));
        }
    }

    /**
     * 处理评论
     */
    private function blogComment() {
        #处理时间戳
        $reqTime = Request::http('X_Comment_Timestamp');
        if (time() - $reqTime < 3 || !$reqTime) {
            $this->json(array('res' => false, 'msg' => '您评论太快了!!'));
            return;
        }
        #验证数据
        $author = Input::get('author');
        if (!$author) {
            $this->json(array('res' => false, 'msg' => '用户名为空!!'));
            return;
        }
        $email = Input::get('email');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json(array('res' => false, 'msg' => '邮箱格式错误!!'));
            return;
        }
        $url = Input::get('url');
        if ($url && !filter_var($url, FILTER_VALIDATE_URL)) {
            $this->json(array('res' => false, 'msg' => '网站格式错误!!'));
            return;
        }
        $content = Input::get('content');
        if (!$content) {
            $this->json(array('res' => false, 'msg' => '评论内容为空!!'));
            return;
        }
        #处理评论内容
        $content = strip_tags(nl2br($content, true), '<p><br><a><img>');
        $content = preg_replace("/(<[^>]*)on[a-zA-Z]+\s*=([^>]*>)/isU", "", $content);
        #保存评论
        $comment = Model::exec('comment', 'addOne', array(
            $author, $email, $url, $content, (int)Input::get('node'), (int)Input::get('parent')
        ));
        $this->json(array('res' => true, 'comment' => $comment));
    }

    //-----------------------------------------

    /**
     * 归档页面动作
     */
    public function allAction() {
        $this->assign('all', Model::exec('blog', 'getAll'))
            ->display('all.html');
    }

    //----------------------------------------

    /**
     * Feed页面动作
     */
    public function feedAction() {
        if (Router::$format != 'xml') {
            $this->redirect('/e/404.html');
            return;
        }
        Widget::setCache();
        Response::make(Widget::exec('feed'),200,'application/rss+xml;charset=UTF-8');
    }

    /**
     * 网站地图动作
     */
    public function sitemapAction() {
        if (Router::$format != 'xml') {
            $this->redirect('/e/404.html');
            return;
        }
        Widget::setCache();
        Response::make(Widget::exec('sitemap'),200,'application/xml;charset=UTF-8');
    }


    //-----------------------------------------

    /**
     * 处理错误页面
     * @param int $code
     */
    private function errorPage($code) {
        if ($code == 404) {
            $this->display('404.html');
            Response::status($code);
            return;
        }
    }

    /**
     * 错误动作
     */
    public function eAction() {
        $code = Router::$param[1];
        $this->errorPage($code);
    }

    //---------------------

    /**
     * 调试动作
     */
    public function debugAction() {

    }

}
