const skud = [
  {
    path: '/skud',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [
      {
        path: '/skud/full',
        component: () =>
          import('@/views/skud/swodsfull'),
        meta: {
          permission: "skud_full"
        }
      },
    ]
  },
]
export default skud
