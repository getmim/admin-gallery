<?php
/**
 * GalleryController
 * @package admin-gallery
 * @version 0.0.1
 */

namespace AdminGallery\Controller;

use LibFormatter\Library\Formatter;
use LibForm\Library\Form;
use LibForm\Library\Combiner;
use LibPagination\Library\Paginator;
use Gallery\Model\Gallery as Gallery;

class GalleryController extends \Admin\Controller
{
    private function getParams(string $title): array{
        return [
            '_meta' => [
                'title' => $title,
                'menus' => ['gallery']
            ],
            'subtitle' => $title,
            'pages' => null
        ];
    }

    public function editAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_gallery)
            return $this->show404();

        $gallery = (object)[];

        $id = $this->req->param->id;
        if($id){
            $gallery = Gallery::getOne(['id'=>$id]);
            if(!$gallery)
                return $this->show404();
            $params = $this->getParams('Edit Gallery');
        }else{
            $params = $this->getParams('Create New Gallery');
        }

        $form           = new Form('admin.gallery.edit');
        $params['form'] = $form;

        $c_opts = [
            'meta' => [null, null, 'json']
        ];

        $combiner = new Combiner($id, $c_opts, 'gallery');
        $gallery = $combiner->prepare($gallery);

        if(!($valid = $form->validate($gallery))|| !$form->csrfTest('noob'))
            return $this->resp('gallery/edit', $params);

        $valid = $combiner->finalize($valid);

        if($id){
            if(!Gallery::set((array)$valid, ['id'=>$id]))
                deb(Gallery::lastError());
        }else{
            $valid->user = $this->user->id;
            if(!Gallery::create((array)$valid))
                deb(Gallery::lastError());
        }

        // add the log
        $this->addLog([
            'user'   => $this->user->id,
            'object' => $id,
            'parent' => 0,
            'method' => $id ? 2 : 1,
            'type'   => 'gallery',
            'original' => $gallery,
            'changes'  => $valid
        ]);

        $next = $this->router->to('adminGallery');
        $this->res->redirect($next);
    }

    public function indexAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_gallery)
            return $this->show404();

        $cond = $pcond = [];
        if($q = $this->req->getQuery('q'))
            $pcond['q'] = $cond['q'] = $q;

        list($page, $rpp) = $this->req->getPager(25, 50);

        $galleries = Gallery::get($cond, $rpp, $page, ['title'=>true]) ?? [];
        if($galleries)
            $galleries = Formatter::formatMany('gallery', $galleries, ['user']);

        $params              = $this->getParams('Gallery');
        $params['galleries'] = $galleries;
        $params['form']      = new Form('admin.gallery.index');

        $params['form']->validate( (object)$this->req->get() );

        // pagination
        $params['total'] = $total = Gallery::count($cond);
        if($total > $rpp){
            $params['pages'] = new Paginator(
                $this->router->to('adminGallery'),
                $total,
                $page,
                $rpp,
                10,
                $pcond
            );
        }
        
        $this->resp('gallery/index', $params);
    }

    public function removeAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_gallery)
            return $this->show404();

        $id      = $this->req->param->id;
        $gallery = Gallery::getOne(['id'=>$id]);
        $next    = $this->router->to('adminGallery');
        $form    = new Form('admin.gallery.index');

        if(!$form->csrfTest('noob'))
            return $this->res->redirect($next);

        // add the log
        $this->addLog([
            'user'   => $this->user->id,
            'object' => $id,
            'parent' => 0,
            'method' => 3,
            'type'   => 'gallery',
            'original' => $gallery,
            'changes'  => null
        ]);

        Gallery::remove(['id'=>$id]);

        // TODO
        // we may need to remove reference from other object

        $this->res->redirect($next);
    }
}