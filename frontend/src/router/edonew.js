const edonew = [{
    path: '/edonew',
    component: () =>
        import ('@/views/layouts/edonew'),
    children: [{
            path: '/edonew',
            component: () =>
                import ('@/views/edonew/Index.vue')
        },

    ]
}, ]

export default edonew