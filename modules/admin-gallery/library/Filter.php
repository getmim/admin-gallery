<?php
/**
 * Filter
 * @package admin-gallery
 * @version 0.0.2
 */

namespace AdminGallery\Library;

use Gallery\Model\Gallery;

class Filter implements \Admin\Iface\ObjectFilter
{
    static function filter(array $cond): ?array{
        $cnd = [];
        if(isset($cond['q']) && $cond['q'])
            $cnd['q'] = (string)$cond['q'];
        $galleries = Gallery::get($cnd, 15, 1, ['title'=>true]);
        if(!$galleries)
            return [];

        $result = [];
        foreach($galleries as $gallery){
            $result[] = [
                'id'    => (int)$gallery->id,
                'label' => $gallery->title,
                'info'  => $gallery->title,
                'icon'  => NULL
            ];
        }

        return $result;
    }

    static function lastError(): ?string{
        return null;
    }
}