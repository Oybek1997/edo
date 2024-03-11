const medpunkt = [{
    path: '/medpunkt',
    component: () =>
        import ('@/views/layouts/medpunkt'),
    children: [
        {
            path: '/medpunkt',
            component: () =>
                import ('@/views/medpunkt/Index.vue')
        },
	    {
            path: '/medpunkt/medicines',
            component: () =>
                import ('@/views/medpunkt/medicine.vue')
        },
        {
            path: '/medpunkt/diagnosis-codes',
            component: () =>
                import ('@/views/medpunkt/diagnosisCode.vue')
        },
        {
            path: '/medpunkt/hospital-diagnosis',
            component: () =>
                import ('@/views/medpunkt/hospitalDiagnosis.vue')
        },
        {
            path: '/medpunkt/registration-period-illness',
            component: () =>
                import ('@/views/medpunkt/registrationPeriodIllness.vue')
        },
        {
            path: '/medpunkt/registration-patients',
            component: () =>
                import ('@/views/medpunkt/registrationPatient.vue')
        },
        {
                    path: '/medpunkt/diet-foods',
                    component: () =>
                        import ('@/views/medpunkt/dietFood.vue')
        },

    ]
}, ]

export default medpunkt