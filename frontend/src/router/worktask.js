const worktask = [
  {
    path: "/worktask",
    component: () => import("@/views/layouts/worktask"),
    children: [
      {
        path: "/worktask/create",
        component: () => import("@/views/worktask/Create"),
      },
      {
        path: "/worktask/index",
        component: () => import("@/views/worktask/Index"),
      },
      {
        path: "/worktask/category",
        component: () => import("@/views/worktask/Category"),
      },
      {
        path: "show/:documentId",
        component: () => import("@/views/worktask/Show"),
        meta: {
          permission: "",
        },
      },
      {
        path: "update/:documentId",
        component: () => import("@/views/worktask/Create"),
        meta: {
          permission: "",
        },
      },
      // {
      //     path: '/worktask/my-tasks',
      //     component: () =>
      //         import('@/views/worktask/myTask')
      // },
    ],
  },
];

export default worktask;
