const skud = [
  {
    path: '/skud',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [
      {
        path: '/skud/fullmanual',
        component: () =>
          import('@/views/skud/swodsfullmanual'),
        meta: {
          permission: "skud_fullmanual"
        }
      },
    ]
  },
]
export default skud
