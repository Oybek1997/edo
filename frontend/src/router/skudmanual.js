const skud = [
  {
    path: '/skud',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [
      {
        path: '/skud/swodmanual',
        component: () =>
          import('@/views/skud/swodsmanual'),
        meta: {
          permission: "skud_swoodmanual"
        }
      },
    ]
  },
]
export default skud
