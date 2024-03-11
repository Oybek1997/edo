const orgtex = [{
    path: '/orgtex',
    component: () =>
        import('@/views/layouts/orgtex'),
    children: [{
        path: '/orgtex/devices',
        component: () =>
            import('@/views/orgtex/devices')
    },
    {
        path: '/orgtex/my-devices',
        component: () =>
            import('@/views/orgtex/myDevices')
    },
    {
        path: '/orgtex/status',
        component: () =>
            import('@/views/orgtex/status')
    },
    {
        path: '/orgtex/status11',
        component: () =>
            import('@/views/orgtex/status11')
    },
    {
        path: '/orgtex/device-histories',
        component: () =>
            import('@/views/orgtex/deviceHistories')
    },
    {
        path: '/orgtex/device-types',
        component: () =>
            import('@/views/orgtex/deviceTypes')
    },
    {
        path: '/orgtex/device-branches',
        component: () =>
            import('@/views/orgtex/branches')
    },

    ]
},]

export default orgtex
