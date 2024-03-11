const linestop = [{
    path: '/linestop',
    component: () =>
        import ('@/views/layouts/linestop'),
    children: [{
            path: '/linestop',
            component: () =>
                import ('@/views/linestop/mainpage.vue')
        },
	    {
            path: '/linestop/linedashboard',
            component: () =>
                import ('@/views/linestop/linedashboard.vue')
        },
    ]
}, ]

export default linestop