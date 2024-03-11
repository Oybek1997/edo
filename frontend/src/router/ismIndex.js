const ism = [
  {
    path: '/ism',
    component: () =>
      import('@/views/layouts/ismLayout'),
    children: [{
      path: '/',
      component: () =>
        import('@/views/Home')
    },
    {
      path: 'test',
      component: () =>
        import('@/views/Test')
    },
    {
      path: 'lock',
      component: () =>
        import('@/views/layouts/lock')
    },
    ]
  },
]

export default ism
