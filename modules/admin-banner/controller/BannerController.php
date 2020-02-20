<?php
/**
 * BannerController
 * @package admin-banner
 * @version 0.0.1
 */

namespace AdminBanner\Controller;

use LibFormatter\Library\Formatter;
use LibForm\Library\Form;
use LibPagination\Library\Paginator;
use Banner\Model\Banner;

class BannerController extends \Admin\Controller
{
    private function getParams(string $title): array{
        return [
            '_meta' => [
                'title' => $title,
                'menus' => ['component', 'banner']
            ],
            'subtitle' => $title,
            'pages' => null
        ];
    }

    public function editAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_banner)
            return $this->show404();

        $c_conv = [
            1 => [ 'img-url'        => 'url', 'img-link' => 'link', 'img-title' => 'title'],
            2 => [ 'html-content'   => 'html' ],
            3 => [ 'gads-code'      => 'code' ],
            4 => [ 'iframe-url'     => 'url' ]
        ];

        $banner = (object)[
            'content' => ''
        ];

        $id = $this->req->param->id;
        if($id){
            $banner = Banner::getOne(['id'=>$id]);
            if(!$banner)
                return $this->show404();
            $params = $this->getParams('Edit Banner');

            $banner_content = json_decode($banner->content);
            foreach($c_conv[$banner->type] as $fkey => $ckey)
                $banner->{$fkey} = $banner_content->{$ckey};
        }else{
            $params = $this->getParams('Create New Banner');
        }

        $form                 = new Form('admin.component-banner.edit');
        $params['form']       = $form;
        $params['placements'] = $this->config->banner->placements ?? [];
        
        if(!($valid = $form->validate($banner)) || !$form->csrfTest('noob'))
            return $this->resp('banner/edit', $params);

        $valid->content = (object)[];
        foreach($c_conv[$valid->type] as $fkey => $ckey)
            $valid->content->{$ckey} = $valid->{$fkey};

        foreach($c_conv as $type => $keys){
            foreach($keys as $fkey => $ckey){
                if(isset($banner->$fkey))
                    unset($banner->$fkey);
                if(isset($valid->$fkey))
                    unset($valid->$fkey);
            }
        }

        $valid->content  = json_encode($valid->content);

        if($id){
            if(!Banner::set((array)$valid, ['id'=>$id]))
                deb(Banner::lastError());
        }else{
            $valid->user = $this->user->id;
            if(!Banner::create((array)$valid))
                deb(Banner::lastError());
        }

        // add the log
        $this->addLog([
            'user'   => $this->user->id,
            'object' => $id,
            'parent' => 0,
            'method' => $id ? 2 : 1,
            'type'   => 'banner',
            'original' => $banner,
            'changes'  => $valid
        ]);

        $next = $this->router->to('adminBanner');
        $this->res->redirect($next);
    }

    public function indexAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_banner)
            return $this->show404();

        $pcond = $cond = $this->req->getCond(['placement']);
        if($q = $this->req->getQuery('q'))
            $pcond['q'] = $cond['q'] = $q;
        if($active = $this->req->getQuery('active')){
            $pcond['active'] = $active;
            if($active == 1){
                $cond['expires'] = ['__op', '>', date('Y-m-d H:i:s')];
            }elseif($active == 2){
                $cond['expires'] = ['__op', '<', date('Y-m-d H:i:s')];
            }
        }

        list($page, $rpp) = $this->req->getPager(20, 50);

        $banners = Banner::get($cond, $rpp, $page, ['expires'=>false,'name'=>true]) ?? [];
        if($banners)
            $banners = Formatter::formatMany('banner', $banners, ['user']);

        $params             = $this->getParams('Site Banners');
        $params['banners']  = $banners;
        $params['form']     = new Form('admin.component-banner.index');

        $params['form']->validate( (object)$this->req->get() );

        $placements = Banner::countGroup('placement') ?? [];
        if($placements)
            $placements = array_keys($placements);
        $placements = array_merge($placements, ( $this->config->banner->placements ?? [] ));
        $placements = array_unique($placements);

        $params['placements'] = $placements;

        // pagination
        $params['total'] = $total = Banner::count($cond);
        if($total > $rpp){
            $params['pages'] = new Paginator(
                $this->router->to('adminBanner'),
                $total,
                $page,
                $rpp,
                10,
                $pcond
            );
        }

        $this->resp('banner/index', $params);
    }

    public function removeAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_banner)
            return $this->show404();

        $id     = $this->req->param->id;
        $banner = Banner::getOne(['id'=>$id]);
        $next   = $this->router->to('adminBanner');
        $form   = new Form('admin.component-banner.index');

        if(!$form->csrfTest('noob'))
            return $this->res->redirect($next);

        // add the log
        $this->addLog([
            'user'   => $this->user->id,
            'object' => $id,
            'parent' => 0,
            'method' => 3,
            'type'   => 'banner',
            'original' => $banner,
            'changes'  => null
        ]);

        Banner::remove(['id'=>$id]);
        $this->res->redirect($next);
    }
}