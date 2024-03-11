const attestation = [{
    path: '/attestation',
    component: () =>
        import ('@/views/layouts/attestation'),
    children: [{
            path: '/commissions',
            component: () =>
                import ('@/views/attestation/Commission')
        },
        {
            path: '/questions',
            component: () =>
                import ('@/views/attestation/Question')
        },
        {
            path: 'lock',
            component: () =>
                import ('@/views/layouts/lock')
        },
    ]
}, ]

export default attestation