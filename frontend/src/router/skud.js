const skud = [
  {
    path: '/skud',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [
      {
        path: '/skud/swod',
        component: () =>
          import('@/views/skud/swods'),
        meta: {
          permission: "skud_swood"
        }
      },
    ]
  },
]
export default skud
