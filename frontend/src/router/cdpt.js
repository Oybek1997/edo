const cdpt = [{
    path: '/cdpt',
    component: () =>
        import ('@/views/layouts/cdpt'),
    children: [{
            path: '/cdpt',
            component: () =>
                import ('@/views/cdpt/Index.vue')
        },
        {
            path: '/cdpt/competence-types',
            component: () =>
                import ('@/views/cdpt/competenceType')
        },
        {
            path: '/cdpt/specific-skills/:id',
            component: () =>
                import ('@/views/cdpt/specificSkills')
        },
        {
            path: '/cdpt/department-types',
            component: () =>
                import ('@/views/cdpt/departmentType')
        },
        {
            path: '/cdpt/priority-fields',
            component: () =>
                import ('@/views/cdpt/priorityField')
        },
        {
            path: '/cdpt/career-development-plans',
            component: () =>
                import ('@/views/cdpt/careerDevelopmentPlan')
        }

    ]
}, ]

export default cdpt