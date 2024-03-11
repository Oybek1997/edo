<?php

return [
    'role_structure' => [
        'superadministrator' => [
            'users' => 'c,r,u,d',
            'acl' => 'c,r,u,d',
            'profile' => 'r,u'
        ],
        'administrator' => [
            'users' => 'c,r,u,d',
            'profile' => 'r,u'
        ],
        'user' => [
            'profile' => 'r,u'
        ],
        'director' => [
            'profile' => 'r,u'
        ],
        'deputy_director' => [
            'profile' => 'r,u'
        ],
        'general_manager' => [
            'profile' => 'r,u'
        ],
        'assistant_general_manager' => [
            'profile' => 'r,u'
        ],
        'general_director' => [
            'profile' => 'r,u'
        ],
        'deputy_general_director' => [
            'profile' => 'r,u'
        ],
        'executive_director' => [
            'profile' => 'r,u'
        ],
        'engineer' => [
            'profile' => 'r,u'
        ],
        'staff' => [
            'profile' => 'r,u'
        ],
    ],
    'permission_structure' => [
        'cru_user' => [
            'profile' => 'c,r,u'
        ],
    ],
    'permissions_map' => [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete'
    ]
];

// superadministrator,
// administrator,
// user,
// director,
// deputy_director,
// general_manager,
// assistant_general_manager,
// general_director,
// deputy_general_director,
// executive_director,
// engineer,
// staff,
