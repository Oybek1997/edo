import Vue from 'vue'
import VueRouter from 'vue-router'
import store from '../store'
import Cookies from 'js-cookie'

// let Layout = () => import('@/views/layouts/Layout');
// let Employee = () => import('@/views/employee/Index');
// let Report = () => import('@/views/report/Index');
// let Staff = () => import('@/views/staff/Index');


Vue.use(VueRouter)

const routes = [
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
  {
    path: '/',
    component: () =>
      import('@/views/layouts/Layout'),
    hidden: true,
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
  {
    path: '/document-templates',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'create',
      component: () =>
        import('@/views/document/templates/Create'),
      meta: {
        permission: "document_template-create"
      }
    },
    {
      path: 'update/:id',
      component: () =>
        import('@/views/document/templates/Create'),
      meta: {
        permission: "document_template-update"
      }
    },
    {
      path: 'list',
      component: () =>
        import('@/views/document/templates/Index'),
      meta: {
        permission: "document_template-index"
      }
    },

    ]
  },
  {
    path: '/document',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'template/:documentTypeId',
      component: () =>
        import('@/views/document/Template'),
      meta: {
        permission: ''
      },
    },
    {
      path: 'create/:documentTemplateId',
      component: () =>
        import('@/views/document/DocumentCreate'),
      meta: {
        permission: ''
      },
    },
    {
      path: 'update/:documentId',
      component: () =>
        import('@/views/document/DocumentCreate'),
      meta: {
        permission: ''
      },
    },
    {
      path: 'signers/:pdf_file_name',
      component: () =>
        import('@/views/document/Signers.vue'),
    },
    {
      path: ':pdf_file_name',
      component: () =>
        import('@/views/document/ShowNew.vue'),
    },
    ]
  },
  {
    path: '/signers-group',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/signersGroup/Index'),
      meta: {
        permission: "signer_group-index"
      }
    }]
  },
  {
    path: '/koreshok',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/koreshok/Index'),
    },
    {
      path: 'open-month',
      component: () =>
        import('@/views/koreshok/OpenMonth'),
    }
    ]
  },
  {
    path: '/tabel',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: '',
      component: () =>
        import('@/views/tabel/Index'),
    }]
  },
  {
    path: '/phonebook',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: '',
      component: () =>
        import('@/views/koreshok/Phonebook'),
    }]
  },
  {
    path: '/purchase-catalogs',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/purchaseCatalog/Index'),
      meta: {
        permission: ""
      }
    }]
  },
  {
    path: '/company-requisites',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/companyRequisite/Index'),
      meta: {
        permission: ""
      }
    }]
  },
  {
    path: '/partners',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/partners/Index'),
      meta: {
        permission: "partners-index"
      }
    }]
  },
  {
    path: '/document-types',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/documentType/Index'),
      meta: {
        permission: "document_type-index"
      }
    }]
  },
  {
    path: '/requestdoc',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/requestdoc/Index'),
      meta: {
        permission: "requestdoc-index"
      }
    }]
  },

  {
    path: '/role-permission',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/user/rolePermission'),
      visible: true //store.getters.checkPermission("role_permission-index"}
    }]
  },
  {
    path: '/employees',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/employee/Index'),
      meta: {
        permission: "employee-index"
      }
    },
    {
      path: 'create-di-document',
      component: () =>
        import('@/views/employee/CreateDIDocument'),
      meta: {
        permission: "employee-index"
      }
    },
    {
      path: 'transfer',
      component: () =>
        import('@/views/employee/Transfer'),
      meta: {
        permission: "employee-index"
      }
    },
    {
      path: 'children',
      component: () =>
        import('@/views/childrenEmployee/Index'),
      meta: {
        permission: "employee-index"
      }
    },
    ]
  },
  {
    path: '/dismissed-employees',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/dismissedEmployees/Index'),
      meta: {
        permission: "employee-index"
      }
    }]
  },
  {
    path: '/document-employee',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/documentEmployee/Index'),
      meta: {
        permission: "document_employee-index"
      }
    }]
  },
  {
    path: '/reports',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/report/Index'),
      meta: {
        permission: "report-index"
      }
    },
    {
      path: 'department/:parent_id',
      component: () =>
        import('@/views/report/ReportDepartment'),
      meta: {
        permission: "report-index"
      }
    },
    {
      path: 'unv-report/:report_template_id',
      component: () =>
        import('@/views/report/UnvReport.vue'),
      meta: {
        permission: "report-okd-index"
      },
    },
    {
      path: 'template/create',
      component: () =>
        import('@/views/report/template/Create.vue'),
      meta: {
        permission: "report-okd-index"
      },
    },
    {
      path: 'template/update/:report_template_id',
      component: () =>
        import('@/views/report/template/Create.vue'),
      meta: {
        permission: "report-okd-index"
      },
    },
    {
      path: 'template',
      component: () =>
        import('@/views/report/template/Index.vue'),
      meta: {
        permission: ""
      },
    },
    {
      path: 'department-okd/:parent_id',
      component: () =>
        import('@/views/report/ReportDepartmentOkd'),
      meta: {
        permission: "report-okd-index"
      }
    }
    ]
  },
  {
    path: '/leaving-reasons',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/leavingReasons/Index'),
      meta: {
        permission: "leaving_reasons-index"
      }
    }]
  },
  {
    path: '/organization',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/organization/Index'),
      meta: {
        permission: "organization-index"
      }
    }]
  },
  {
    path: '/currencies',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/currency/Index'),
      meta: {
        permission: "currency-index"
      }
    },
    {
      path: 'history',
      component: () =>
        import('@/views/currencyHistory/Index'),
      meta: {
        permission: "currency-index"
      }
    },
    ]
  },

  {
    path: '/appeal-content',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/appealContent/Index'),
      meta: {
        permission: "appeal_content-index"
      }
    }]
  },
  {
    path: '/dashboard-registry',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: ':id',
      component: () =>
        import('@/views/registry/Index'),
      meta: {
        permission: ""
      }
    }]
  },
  {
    path: '/family-relative',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/familyRelatives/Index'),
      meta: {
        permission: "family-relative-index"
      }
    }]
  },
  {
    path: '/hr-language',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/hrLanguage/Index'),
      meta: {
        permission: "hr-language-index"
      }
    }]
  },
  {
    path: '/hr-party',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/hrParty/Index'),
      meta: {
        permission: "hr-party-index"
      }
    }]
  },
  {
    path: '/hr-study-degree',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/hrStudyDegree/Index'),
      meta: {
        permission: "hr-study-degree-index"
      }
    }]
  },
  {
    path: '/hr-university',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/hrUniversity/Index'),
      meta: {
        permission: "hr-university-index"
      }
    }]
  },
  {
    path: '/hr-major',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/hrMajor/Index'),
      meta: {
        permission: "hr-major-index"
      }
    }]
  },
  {
    path: '/hr-study-type',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/hrStudyType/Index'),
      meta: {
        permission: "hr-study-type-index"
      }
    }]
  },
  {
    path: '/hr-military-rank',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/hrMilitaryRank/Index'),
      meta: {
        permission: "hr-military-rank-index"
      }
    }]
  },
  {
    path: '/hr-state-awards',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/hrStateAward/Index'),
      meta: {
        permission: "hr-state-award-index"
      }
    }]
  },
  {
    path: '/work-calendar',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: '/',
      component: () =>
        import('@/views/workCalendar/Index'),
      meta: {
        permission: ""
      }
    }]
  },
  {
    path: '/salary-cert',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: '/',
      component: () =>
        import('@/views/calendar/salaryCert'),
    }]
  },
  {
    path: '/firm-blank/:document_template_id',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: '/',
      component: () =>
        import('@/views/document/FirmBlankCreate'),
    }]
  },
  {
    path: '/firm-blank-only-pdf',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: '/',
      component: () =>
        import('@/views/document/FirmBlank'),
    }]
  },
  {
    path: '/vacation-registry',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: '/',
      component: () =>
        import('@/views/registry/Vacation'),
      meta: {
        permission: "vacation-registry"
      }
    }]
  },
  {
    path: '/business-trip-registry',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: '/',
      component: () =>
        import('@/views/registry/BusinessTrip'),
      meta: {
        permission: "business_trip"
      }
    }]
  },
  {
    path: '/positions',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/position/Index'),
      meta: {
        permission: "position-index"
      }
    }]
  },
  {
    path: '/staffs',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/staff/Index'),
      meta: {
        permission: "staff-index"
      }
    }]
  },
  {
    path: '/staff-criticals',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/staffCritical/Index'),
      meta: {
        permission: "position-index"
      }
    }]
  },
  {
    path: '/coefficients',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/coefficient/Index'),
      meta: {
        permission: "coefficient-index"
      }
    }]
  },
  {
    path: '/access-departments',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/accessDepartment/Index'),
      meta: {
        permission: "coefficient-index"
      }
    }]
  },
  {
    path: '/sap-transaction',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/sapTransaction/Index'),
      meta: {
        permission: "sap-transactions-index"
      }
    }]
  },
  {
    path: '/ranges',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/range/Index'),
      meta: {
        permission: "range-index"
      }
    }]
  },
  {
    path: '/tariff-scales',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/tariffScale/Index'),
      meta: {
        permission: "tariff_scale-index"
      }
    }]
  },
  {
    path: '/countries',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/countries/Index'),
      meta: {
        permission: "country-index"
      }
    }]
  },
  {
    path: '/districts',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/districts/Index'),
      meta: {
        permission: "district-index"
      }
    }]
  },
  {
    path: '/regions',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/regions/Index'),
      meta: {
        permission: "region-index"
      }
    }]
  },
  {
    path: '/nationalities',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/nationalities/Index'),
      meta: {
        permission: "nationality-index"
      }
    }]
  },
  {
    path: '/department-types',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/departmentType/Index'),
      meta: {
        permission: "department_type-index"
      }
    }]
  },
  {
    path: '/position-types',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/positionType/Index'),
      meta: {
        permission: "position_type-index"
      }
    }]
  },
  {
    path: '/personal-types',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/personalType/Index'),
      meta: {
        permission: "personal_type-index"
      }
    }]
  },
  {
    path: '/access-types',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/accessType/Index'),
      meta: {
        permission: "access_type-index"
      }
    }]
  },
  {
    path: '/object-types',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/objectType/Index'),
      meta: {
        permission: "object_type-index"
      }
    }]
  },
  {
    path: '/expence-types',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/expenceType/Index'),
      meta: {
        permission: "expence_type-index"
      }
    }]
  },
  {
    path: '/requirement-types',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/requirementType/Index'),
      meta: {
        permission: "requirement_type-index"
      }
    }]
  },
  {
    path: '/car-purchases',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/carPurchase/Index'),
      meta: {
        permission: "car-purchase-index"
      }
    }]
  },
  {
    path: '/employee-info',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/employeeInfo/Index'),
      meta: {
        permission: "car-purchase-index"
      }
    }]
  },
  {
    path: '/requirements',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/requirements/Index'),
      meta: {
        permission: "requirement-index"
      }
    }]
  },
  {
    path: '/departments',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/department/Index'),
      meta: {
        permission: "department-index"
      }
    },
    {
      path: 'tree',
      component: () =>
        import('@/views/department/IndexTree'),
      meta: {
        permission: "department-index_tree"
      }
    }
    ]
  },
  {
    path: '/companies',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/company/Index'),
      meta: {
        permission: "company-index"
      }
    }]
  },
  {
    path: '/unblocked-users',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/unblockedUsers/Index'),
      meta: {
        permission: "user-index"
      }
    }]
  },
  {
    path: '/users',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/user/Index'),
      meta: {
        permission: "user-index"
      }
    },
    {
      path: "permission",
      component: () =>
        import('@/views/user/Permission'),
      meta: {
        permission: "user-index",
      }
    },
    {
      path: "profile/:id",
      component: () =>
        import('@/views/profile/Index'),
      // meta: {
      //   permission: "user-index",
      // }
    },
    {
      path: "online",
      component: () =>
        import('@/views/user/Online'),
      meta: {
        permission: "",
      }
    }
    ]
  },
  {
    path: '/timeline',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: '/',
      component: () =>
        import('@/views/Timeline/Index'),
      meta: {
        permission: ""
      }
    }]
  },
  {
    path: '/inbox',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: '/',
      component: () =>
        import('@/views/documentCreate/Inbox'),
      meta: {
        permission: ""
      }
    }]
  },
  {
    path: '/draft',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: '/',
      component: () =>
        import('@/views/documentCreate/Draft'),
      meta: {
        permission: ""
      }
    }]
  },
  {
    path: '/sent',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: '/',
      component: () =>
        import('@/views/documentCreate/Sent'),
      meta: {
        permission: ""
      }
    }]
  },
  {
    path: '/documents',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list/:menu_item/:document_type',
      component: () =>
        import('@/views/document/Index'),
      meta: {
        permission: ""
      }
    },
    {
      path: 'signed',
      component: () =>
        import('@/views/document/SignedDocuments'),
      meta: {
        permission: ""
      }
    },
    {
      path: 'index-new',
      component: () =>
        import('@/views/document/IndexNew'),
      meta: {
        permission: ""
      }
    },
    {
      path: 'show/:id',
      component: () =>
        import('@/views/document/Show'),
      meta: {
        permission: ""
      }
    },
    {
      path: 'show-oldd/:id',
      component: () =>
        import('@/views/document/Show'),
      meta: {
        permission: ""
      }
    },
    {
      path: 'show-new/:pdf_file_name',
      component: () =>
        import('@/views/document/ShowNew'),
      meta: {
        permission: ""
      }
    },
    {
      path: 'show-only-pdf/:id',
      component: () =>
        import('@/views/document/ShowOnlyPdf'),
      meta: {
        permission: ""
      }
    },
    {
      path: 'report',
      component: () =>
        import('@/views/document/Report'),
      meta: {
        permission: ""
      }
    },
    {
      path: 'report/znz',
      component: () =>
        import('@/views/document/ZnzReport'),
      meta: {
        permission: ""
      }
    },
    {
      path: 'report/lsp',
      component: () =>
        import('@/views/document/LspReport'),
      meta: {
        permission: ""
      }
    },
    {
      path: 'report/otz',
      component: () =>
        import('@/views/employee/OtzReport'),
      meta: {
        permission: ""
      }
    },
    {
      path: 'executor/:menu_item',
      component: () =>
        import('@/views/document/Executor'),
      meta: {
        permission: ""
      }
    },
    ]
  },
  {
    path: '/',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: '/',
      component: () =>
        import('@/views/Home')
    },
    {
      path: '/departments',
      component: () =>
        import('@/views/department/Index'),
      meta: {
        permission: "department-index"
      }
    },
    ]

  },
  {
    path: '/notifications',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/notification/Index.vue'),
      meta: {
        permission: "notification-index"
      }
    }]
  },
  {
    path: '/joint-venture',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/jointVenture/Index.vue'),
      meta: {
        permission: "joint-ventures-index"
      }
    }]
  },
  {
    path: '/directories',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/directory/Index.vue'),
    }]
  },
  {
    path: '/complaens',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'cencel-document',
      component: () =>
        import('@/views/complaens/cancelDocuments.vue'),
      meta: {
        permission: "complaens-cancel-documents"
      }
    }]
  },

  {
    path: '/inventory',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/inventory/Index.vue'),
    },
    {
      path: 'addresses',
      component: () =>
        import('@/views/inventoryAddress/Index.vue'),
    },
    {
      path: 'products',
      component: () =>
        import('@/views/inventoryProductList/Index.vue'),
    },
    {
      path: 'report',
      component: () =>
        import('@/views/inventoryReport/Index.vue'),
    },
    {
      path: 'report1',
      component: () =>
        import('@/views/inventory/IndexReport.vue'),
    },
    {
      path: 'commissions',
      component: () =>
        import('@/views/inventoryCommission/Index.vue'),
    },
    {
      path: 'blanks',
      component: () =>
        import('@/views/inventoryBlank/Index.vue'),
    },
    ]
  },

  {
    path: '/blank-templates',
    component: () =>
      import('@/views/layouts/Layout'),
    children: [{
      path: 'list',
      component: () =>
        import('@/views/blankTemplate/Index'),
      meta: {
        permission: "blank-template-create"
      }
    },
    {
      path: "edit/:id",
      component: () =>
        import('@/views/blankTemplate/Template'),
      meta: {
        permission: "blank-template-create"
      }
    },
    {
      path: "get-blank/",
      component: () =>
        import('@/views/blankTemplate/getBlank'),
      meta: {
        permission: "blank-template-index"
      }
    },
    {
      path: "get-file/:id",
      component: () =>
        import('@/views/blankTemplate/getFile'),
      meta: {
        permission: "blank-template-index"
      }
    },
    ]
  },

  {
    path: '/login',
    component: () =>
      import('@/views/layouts/Login'),
    visible: true, //store.getters.checkPermission("templates-index")
  },
  {
    path: '/sign-document/:agreement_hash/:user_id',
    component: () =>
      import('@/views/signDocument/Sign'),
    visible: true, //store.getters.checkPermission("templates-index")
  },
  {
    path: '/404',
    component: () =>
      import('@/views/layouts/404'),
    visible: true, //store.getters.checkPermission("templates-index")
  },
  {
    path: '/403',
    component: () =>
      import('@/views/layouts/403'),
  },
  {
    path: '*',
    redirect: '/404',
  },
]

const router = new VueRouter({
  mode: 'hash',
  // mode: 'history',
  base: process.env.BASE_URL,
  routes
})


//--------------------------------------------------------------------------------------
router.beforeEach((to, from, next) => {
  var access_token = store.getters.getAccessToken();
  let localStorage = window.localStorage;
  const now = new Date();
  let expire_token = localStorage.getItem('expire_token', now.getTime());
  let user = store.getters.getUser();
  if (user.username != 'QG95921') {
    if (!(expire_token == 'null' || expire_token == null) && expire_token < now.getTime() - 10800000 && to.fullPath != '/login') {
      store.dispatch("setUser", null);
      store.dispatch("setPermissions", null);
      store.dispatch("setRole", null);
      store.dispatch("setAccessToken", null);
      // Cookies.remove("access_token");
      next({
        path: '/login',
      })
    }
  }

  if (!access_token && to.fullPath != '/login') {
    store.dispatch('setRedirectUrl', to.fullPath);
    localStorage.setItem('expire_token', now.getTime());
    next({
      path: '/login',
    })
  } else if (!to.meta.permission || store.getters.checkPermission(to.meta.permission)) {
    localStorage.setItem('expire_token', now.getTime());
    next()
  } else {
    localStorage.setItem('expire_token', now.getTime());
    next('/403')
  }
})
export default router
