<?php

return [
    '__name' => 'admin-gallery',
    '__version' => '0.0.1',
    '__git' => 'git@github.com:getmim/admin-gallery.git',
    '__license' => 'MIT',
    '__author' => [
        'name' => 'Iqbal Fauzi',
        'email' => 'iqbalfawz@gmail.com',
        'website' => 'http://iqbalfn.com/'
    ],
    '__files' => [
        'modules/admin-gallery' => ['install','update','remove'],
        'theme/admin/gallery' => ['install','update','remove'],
        'theme/admin/static/js/gallery.js' => ['install','update','remove']
    ],
    '__dependencies' => [
        'required' => [
            [
                'admin' => NULL
            ],
            [
                'lib-form' => NULL
            ],
            [
                'lib-pagination' => NULL
            ],
            [
                'lib-formatter' => NULL
            ],
            [
                'gallery' => NULL
            ],
            [
                'admin-site-meta' => NULL
            ]
        ],
        'optional' => []
    ],
    'autoload' => [
        'classes' => [
            'AdminGallery\\Controller' => [
                'type' => 'file',
                'base' => 'modules/admin-gallery/controller'
            ]
        ],
        'files' => []
    ],
    'routes' => [
        'admin' => [
            'adminGallery' => [
                'path' => [
                    'value' => '/gallery'
                ],
                'method' => 'GET',
                'handler' => 'AdminGallery\\Controller\\Gallery::index'
            ],
            'adminGalleryEdit' => [
                'path' => [
                    'value' => '/gallery/(:id)',
                    'params' => [
                        'id' => 'number'
                    ]
                ],
                'method' => 'GET|POST',
                'handler' => 'AdminGallery\\Controller\\Gallery::edit'
            ],
            'adminGalleryRemove' => [
                'path' => [
                    'value' => '/gallery/(:id)/remove',
                    'params' => [
                        'id' => 'number'
                    ]
                ],
                'method' => 'GET',
                'handler' => 'AdminGallery\\Controller\\Gallery::remove'
            ]
        ]
    ],
    'adminUi' => [
        'sidebarMenu' => [
            'items' => [
                'gallery' => [
                    'label' => 'Gallery',
                    'icon' => '<i class="fas fa-images"></i>',
                    'priority' => 0,
                    'perms' => 'manage_gallery',
                    'route' => ['adminGallery']
                ]
            ]
        ]
    ],
    'libForm' => [
        'forms' => [
            'admin.gallery.index' => [
                'q' => [
                    'label' => 'Search',
                    'type' => 'search',
                    'nolabel' => TRUE,
                    'rules' => []
                ]
            ],
            'admin.gallery.edit' => [
                '@extends' => ['std-site-meta'],
                'title' => [
                    'label' => 'Title',
                    'type' => 'text',
                    'rules' => [
                        'required' => TRUE
                    ]
                ],
                'slug' => [
                    'label' => 'Slug',
                    'type' => 'text',
                    'slugof' => 'title',
                    'rules' => [
                        'required' => TRUE,
                        'empty' => FALSE,
                        'unique' => [
                            'model' => 'Gallery\\Model\\Gallery',
                            'field' => 'slug',
                            'self' => [
                                'service' => 'req.param.id',
                                'field' => 'id'
                            ]
                        ]
                    ]
                ],
                'images' => [
                    'label' => 'Image List',
                    'type' => 'textarea',
                    'nolabel' => true,
                    'rules' => [
                        'json' => true
                    ]
                ],
                'content' => [
                    'label' => 'About',
                    'type' => 'summernote',
                    'rules' => []
                ]
            ]
        ]
    ]
];