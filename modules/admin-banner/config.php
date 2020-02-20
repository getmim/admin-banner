<?php

return [
    '__name' => 'admin-banner',
    '__version' => '0.0.4',
    '__git' => 'git@github.com:getmim/admin-banner.git',
    '__license' => 'MIT',
    '__author' => [
        'name' => 'Iqbal Fauzi',
        'email' => 'iqbalfawz@gmail.com',
        'website' => 'http://iqbalfn.com/'
    ],
    '__files' => [
        'modules/admin-banner' => ['install','update','remove'],
        'theme/admin/banner' => ['install','update','remove']
    ],
    '__dependencies' => [
        'required' => [
            [
                'banner' => NULL
            ],
            [
                'admin' => NULL
            ],
            [
                'lib-form' => NULL
            ],
            [
                'lib-formatter' => NULL
            ],
            [
                'lib-pagination' => NULL
            ]
        ],
        'optional' => []
    ],
    'autoload' => [
        'classes' => [
            'AdminBanner\\Controller' => [
                'type' => 'file',
                'base' => 'modules/admin-banner/controller'
            ]
        ],
        'files' => []
    ],
    'routes' => [
        'admin' => [
            'adminBanner' => [
                'path' => [
                    'value' => '/component/banner'
                ],
                'method' => 'GET',
                'handler' => 'AdminBanner\\Controller\\Banner::index'
            ],
            'adminBannerEdit' => [
                'path' => [
                    'value' => '/component/banner/(:id)',
                    'params' => [
                        'id'  => 'number'
                    ]
                ],
                'method' => 'GET|POST',
                'handler' => 'AdminBanner\\Controller\\Banner::edit'
            ],
            'adminBannerRemove' => [
                'path' => [
                    'value' => '/component/banner/(:id)/remove',
                    'params' => [
                        'id'  => 'number'
                    ]
                ],
                'method' => 'GET',
                'handler' => 'AdminBanner\\Controller\\Banner::remove'
            ]
        ]
    ],
    'adminUi' => [
        'sidebarMenu' => [
            'items' => [
                'component' => [
                    'label' => 'Component',
                    'icon' => '<i class="fas fa-puzzle-piece"></i>',
                    'priority' => 0,
                    'children' => [
                        'banner' => [
                            'label' => 'Banner',
                            'icon'  => '<i></i>',
                            'route' => ['adminBanner'],
                            'perms' => 'manage_banner'
                        ]
                    ]
                ]
            ]
        ]
    ],
    'libForm' => [
        'forms' => [
            'admin.component-banner.index' => [
                'q' => [
                    'label' => 'Search',
                    'type' => 'search',
                    'nolabel' => true,
                    'rules' => []
                ],
                'placement' => [
                    'label' => 'Placement',
                    'type' => 'text',
                    'nolabel' => true,
                    'rules' => []
                ],
                'active' => [
                    'label' => 'Status',
                    'type' => 'select',
                    'nolabel' => true,
                    'options' => [ 'All', 'Active', 'Expires' ],
                    'rules' => []
                ]
            ],
            'admin.component-banner.edit' => [
                'name' => [
                    'label' => 'Name',
                    'type' => 'text',
                    'rules' => [
                        'required' => true 
                    ]
                ],
                'placement' => [
                    'label' => 'Placement',
                    'type' => 'text',
                    'rules' => [
                        'required' => true 
                    ]
                ],
                'expires' => [
                    'label' => 'Expires',
                    'type' => 'datetime',
                    'rules' => [
                        'required' => true
                    ]
                ],
                'type' => [
                    'label' => 'Type',
                    'type' => 'select',
                    'rules' => [
                        'required' => true,
                        'enum' => 'banner.type'
                    ]
                ],

                'img-title' => [
                    'label' => 'Link Title',
                    'type' => 'text',
                    'rules' => [
                        'required_on' => [
                            'type' => [
                                'operator' => '=',
                                'expected' => 1
                            ]
                        ]
                    ]
                ],
                'img-url' => [
                    'label' => 'Image URL',
                    'type' => 'image',
                    'form' => 'std-image',
                    'rules' => [
                        'upload' => true,
                        'required_on' => [
                            'type' => [
                                'operator' => '=',
                                'expected' => 1
                            ]
                        ]
                    ]
                ],
                'img-link' => [
                    'label' => 'Link',
                    'type' => 'url',
                    'rules' => [
                        'url' => true,
                        'required_on' => [
                            'type' => [
                                'operator' => '=',
                                'expected' => 1
                            ]
                        ]
                    ]
                ],
                
                'html-content' => [
                    'label' => 'HTML',
                    'type' => 'textarea',
                    'monospace' => true,
                    'rules' => [
                        'required_on' => [
                            'type' => [
                                'operator' => '=',
                                'expected' => 2
                            ]
                        ]
                    ]
                ],
                
                'gads-code' => [
                    'label' => 'Adsense Code',
                    'type' => 'textarea',
                    'monospace' => true,
                    'rules' => [
                        'required_on' => [
                            'type' => [
                                'operator' => '=',
                                'expected' => 3
                            ]
                        ]
                    ]
                ],

                'iframe-url' => [
                    'label' => 'URL',
                    'type' => 'url',
                    'rules' => [
                        'url' => true,
                        'required_on' => [
                            'type' => [
                                'operator' => '=',
                                'expected' => 4
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];
