import Vue from "vue";
import VueRouter from "vue-router";
import store from "../store";
import ism from "../router/ismIndex";
import attestation from "../router/attestation";
import orgtex from "../router/orgtex";
import worktask from "../router/worktask";
import cdpt from "../router/cdpt";
import skud from "../router/skud";
import skudmanual from "../router/skudmanual";
import skudfull from "../router/skudfull";
import swodsfullmanual from "../router/swodsfullmanual";
import medpunkt from "../router/medpunkt";
import edonew from "../router/edonew";
import EdoSidebar from "../views/layouts/EdoSidebar.vue";

// let Layout = () => import('@/views/layouts/edonew');
// let Employee = () => import('@/views/employee/Index');
// let Report = () => import('@/views/report/Index');
// let Staff = () => import('@/views/staff/Index');

Vue.use(VueRouter);

let routes = [
  {
    path: "/",
    component: () => import("@/views/layouts/edonew"),
    hidden: true,
    children: [
      {
        path: "/",
        component: () => import("@/views/layouts/edonew"),
        hidden: true,
        children: [
          {
            path: "/",
            component: () => import("@/views/Home"),
          },
          {
            path: "support/index",
            component: () => import("@/views/support/Index"),
          },
          {
            path: "support/itlife",
            component: () => import("@/views/support/itsupport"),
          },
          {
            path: "support/message/:messageType",
            component: () => import("@/views/support/supportMessage"),
          },
          {
            path: "support/queues/custom",
            component: () => import("@/views/support/Queues"),
          },         
          {
            path: "collection/charts",
            component: () => import("@/views/HomeCharts"),
          },
          {
            path: "/linestopsidebar",
            component: EdoSidebar,
            children: [
              {
                path: "linestop-mainpage",
                component: () => import("@/views/linestop/LinestopMainpage"),
              },
              {
                path: "linestop-alltickets",
                component: () => import("@/views/linestop/LinestopAllTickets"),
                meta: {
                  permission: "linestop-tmpsending",
                },
              },
              {
                path: "linestop-allopentickets",
                component: () =>
                  import("@/views/linestop/LinestopAllOpenTickets"),
                meta: {
                  permission: "linestop-tmpsending",
                },
              },
              {
                path: "linestop-autotickets",
                component: () => import("@/views/linestop/LinestopAutoTickets"),
                meta: {
                  permission: "linestop-tmpsending",
                },
              },
              {
                path: "linestop-ticket/:id",
                component: () =>
                  import("@/views/linestop/LinestopTicketviewpage"),
              },
              {
                path: "linestop-createpage",
                component: () => import("@/views/linestop/LinestopCreatepage"),
                meta: {
                  permission: "linestop-tmpsending",
                },
              },
              {
                path: "linestop-reasons",
                component: () => import("@/views/linestop/LinestopReasons"),
                meta: {
                  permission: "linestop-tmpsending",
                },
              },
              {
                path: "linestop-departments",
                component: () =>
                  import("@/views/linestop/LinestopDepartmentCategory"),
                meta: {
                  permission: "linestop-tmpsending",
                },
              },
              {
                path: "linestop-providers",
                component: () => import("@/views/linestop/LinestopProviders"),
                meta: {
                  permission: "linestop-tmpsending",
                },
              },
              {
                path: "linestop-lines",
                component: () => import("@/views/linestop/LinestopLines"),
                meta: {
                  permission: "linestop-tmpsending",
                },
              },
              {
                path: "linestop-shops",
                component: () => import("@/views/linestop/LinestopShops"),
                meta: {
                  permission: "linestop-tmpsending",
                },
              },
              {
                path: "linestop-mylinestops",
                component: () => import("@/views/linestop/LinestopMylinestops"),
              },
              {
                path: "linestop-reportone",
                component: () => import("@/views/linestop/LinestopReportone"),
                meta: {
                  permission: "linestop-tmpsending",
                },
              },
              {
                path: "linestop-reporttwo",
                component: () => import("@/views/linestop/LinestopReporttwo"),
                meta: {
                  permission: "linestop-tmpsending",
                },
              },
              {
                path: "linestop-reportthree",
                component: () => import("@/views/linestop/LinestopReportthree"),
                meta: {
                  permission: "linestop-tmpsending",
                },
              },
              {
                path: "linestop-productmodel",
                component: () =>
                  import("@/views/linestop/LinestopProductionModel"),
                meta: {
                  permission: "linestop-tmpsending",
                },
              },
            ],
          },
          {
            path: "/manufacturingsidebar",
            component: EdoSidebar,
            children: [
              {
                path: "manufacturing-mainpage",
                component: () => import("@/views/Home"),
              },
              {
                path: "method-quality-control",
                component: () => import("@/views/ME/methodQualityControl.vue"),
              },
              {
                path: "quality-control",
                component: () => import("@/views/ME/qualityControl.vue"),
              },
              {
                path: "status",
                component: () => import("@/views/ME/status.vue"),
              },
              {
                path: "model",
                component: () => import("@/views/ME/model.vue"),
              },
            ],
          },
          // Med punkt qismi boshladni
          {
            path: "/medpunktsidebar",
            component: EdoSidebar,
            children: [
              {
                path: "medpunkt-homepage",
                component: () => import("@/views/medpunkt/Index.vue"),
              },
              {
                path: "medpunkt-medicines",
                component: () => import("@/views/medpunkt/medicine.vue"),
              },
              {
                path: "medpunkt-diagnosis-codes",
                component: () => import("@/views/medpunkt/diagnosisCode.vue"),
              },
              {
                path: "medpunkt-hospital-diagnosis",
                component: () =>
                  import("@/views/medpunkt/hospitalDiagnosis.vue"),
              },
              {
                path: "medpunkt-registration-period-illness",
                component: () =>
                  import("@/views/medpunkt/registrationPeriodIllness.vue"),
              },
              {
                path: "medpunkt-registration-patients",
                component: () =>
                  import("@/views/medpunkt/registrationPatient.vue"),
              },
              {
                path: "medpunkt-diet-foods",
                component: () => import("@/views/medpunkt/dietFood.vue"),
              },
            ],
          },
          // Med punkt qismi tugadi
          // Supply transport qismi boshlandi
          {
            path: "/supplytransport-sidebar",
            component: EdoSidebar,
            children: [
              {
                path: "home",
                component: () =>
                  import("@/views/dashboards/SupplyTansportHome.vue"),
              },
              {
                path: "supply-spare-parts",
                component: () =>
                  import("@/views/supplyTransports/SupplySpareParts"),
              },
              {
                path: "transports",
                component: () =>
                  import("@/views/supplyTransports/SupplyTransports"),
              },
              {
                path: "transports-type",
                component: () =>
                  import("@/views/supplyTransports/SupplyTransportsType"),
              },
              {
                path: "departments",
                component: () =>
                  import("@/views/supplyTransports/SupplyDepartments"),
              },
              {
                path: "repair-plumber",
                component: () =>
                  import("@/views/supplyTransports/SupplyRepairPlumber"),
              },
              {
                path: "loader-driver",
                component: () =>
                  import("@/views/supplyTransports/SupplyLoaderDriver"),
              },
              {
                path: "sections",
                component: () =>
                  import("@/views/supplyTransports/SupplySections"),
              },
              {
                path: "schedule",
                component: () =>
                  import("@/views/supplyTransports/SupplySchedules"),
              },
            ],
          },
          // Supply transport qismi tugadi
          // centrum qismi boshlandi
          {
            path: "/centrum-sidebar",
            component: EdoSidebar,
            children: [
              {
                path: "/documents/list/:menu_item/:document_type",
                component: () => import("@/views/document/Index"),
                meta: {
                  permission: "centrum",
                },
              },
              {
                path: "home",
                component: () => import("@/views/Home"),
              },
              {
                path: "act-report",
                component: () => import("@/views/centrum/ActReport"),
                meta: {
                  permission: "centrum",
                },
              },
              {
                path: "attribute-report",
                component: () => import("@/views/centrum/AttributeReport"),
                meta: {
                  permission: "centrum",
                },
              },
              {
                path: "import",
                component: () => import("@/views/centrum/Index"),
                meta: {
                  permission: "centrum",
                },
              },
              {
                path: "info",
                component: () => import("@/views/centrum/Info"),
                meta: {
                  permission: "centrum",
                },
              },
              {
                path: "shablon",
                component: () => import("@/views/centrum/Shablon.vue"),
                meta: {
                  permission: "centrum",
                },
              },
              {
                path: "report",
                component: () => import("@/views/centrum/Report"),
                meta: {
                  permission: "centrum",
                },
              },
            ],
          },
          // centrum qismi tugadi
          // integration qismi boshlandi
          {
            path: "/integration-sidebar",
            component: EdoSidebar,
            children: [
              {
                path: "home",
                component: () => import("@/views/dashboards/IntigretionHome"),
              },
              {
                path: "post-order/list",
                component: () => import("@/views/postOrder/Index"),
                meta: {
                  permission: "",
                },
              },
              {
                path: "post-order/info",
                component: () => import("@/views/postOrder/Info"),
                meta: {
                  permission: "",
                },
              },
              {
                path: "tenge-document-registry",
                component: () => import("@/views/registry/TengeRegistry"),
                meta: {
                  permission: "tenge",
                },
              },
              {
                path: "sap/user-request",
                component: () => import("@/views/sap/Index.vue"),
              },
              {
                path: "sap/search-details",
                component: () => import("@/views/sap/FindPart.vue"),
              },
              {
                path: "sap/warehouse-search-details",
                component: () => import("@/views/sap/WarehouseFindPart.vue"),
              },
              {
                path: "sap/nolikvid-details",
                component: () => import("@/views/sap/Nelekvid.vue"),
              },
              {
                path: "sap/warehouse-responsible",
                component: () => import("@/views/sap/WarehouseResponsible.vue"),
              },
            ],
          },
          // integration qismi tugadi
          // me'yorit hujjatlar qismi boshlandi
          {
            path: "/regulatory-documents",
            component: EdoSidebar,
            children: [
              {
                path: "rules-of-work",
                component: () => import("@/views/employee/RulesOfWork"),
              },
              {
                path: "mainpage",
                component: () => import("@/views/dashboards/RegulatoryDocumentHome"),
              },
              {
                path: "staffs/table",
                component: () => import("@/views/staff/Table"),
              },
              {
                path: "documents/list/:menu_item/:document_type",
                component: () => import("@/views/document/Index"),
              },
            ],
          },
          // me'yorit hujjatlar qismi tugadiF
          {
            path: "/documentsidebar",
            component: EdoSidebar,
            children: [
              {
                path: "/mailing",
                component: () => import("@/views/layouts/edonew"),
                meta: {
                  permission: "okd-report-index",
                },
              },
              {
                path: "mailing/list",
                component: () => import("@/views/report/Mailing"),
                meta: {
                  permission: "okd-report-index",
                },
              },
              {
                path: "home",
                component: () => import("@/views/dashboards/DocumentHome"),
              },
              {
                path: "/my-setting",
                component: () => import("@/views/layouts/edonew"),
                children: [
                  {
                    path: "/my-setting",
                    component: () => import("@/views/layouts/settingsVeiw"),
                    meta: {
                      permission: "",
                    },
                  },
                  {
                    path: "/my-setting/acount",
                    component: () => import("@/views/newEdoFiles/Index"),
                    meta: {
                      permission: "",
                    },
                  },
                ],
              },
              {
                path: "edo-mainpage",
                component: () => import("@/views/Home"),
                meta: {
                  permission: "document_template-create",
                },
              },
              {
                path: "document/:pdf_file_name",
                component: () => import("@/views/document/ShowNew.vue"),
              },
              {
                path: "my-document-report-item/:type",
                component: () => import("@/views/report/MyDocumentReportItem"),
                meta: {
                  permission: "",
                },
              },
              {
                path: "document-folder/:docFolderType",
                component: () => import("@/views/document/DocumentFolder"),
                meta: {
                  // permission: "document_template-create",
                },
              },
              {
                path: "document-folder/list/:menu_item/:document_type",
                component: () => import("@/views/document/Index"),
                meta: {
                  permission: "",
                },
              },
              {
                path: "document-folder/create/:documentTemplateId",
                component: () => import("@/views/document/DocumentCreate"),
                meta: {
                  // permission: "document_template-create",
                },
              },
              {
                path: "document-folder/template/:documentTypeId",
                component: () => import("@/views/document/Template"),
                meta: {
                  // permission: "document_template-create",
                },
              },
              {
                path: "document-often",
                component: () => import("@/views/document/TemplateOften"),
                meta: {
                  // permission: "document_template-create",
                },
              },
              {
                path: "template-favorite",
                component: () => import("@/views/document/TemplateFavorite"),
              },
              ///////////////////////////////
              {
                path: "report",
                component: () => import("@/views/document/Report"),
                meta: {
                  permission: "",
                },
              },
              {
                path: "report/znz",
                component: () => import("@/views/document/ZnzReport"),
                meta: {
                  permission: "",
                },
              },
              {
                path: "report/lsp",
                component: () => import("@/views/document/LspReport"),
                meta: {
                  permission: "",
                },
              },
              {
                path: "reports/department/:parent_id",
                component: () => import("@/views/report/ReportDepartment"),
                meta: {
                  permission: "report-index",
                },
              },
              {
                path: "okd-report-full",
                component: () => import("@/views/report/OkdReportFull"),
                meta: {
                  permission: "okd-report-index",
                },
              },
              {
                path: "document-report-my-item/:type",
                component: () => import("@/views/report/DocumentReportMyItem"),
                meta: {
                  permission: "",
                },
              },
              {
                path: "okd-report-full-toshkent",
                component: () => import("@/views/report/OkdReportFullToshkent"),
                meta: {
                  permission: "okd-report-index",
                },
              },
              {
                path: "document-report-employee",
                component: () =>
                  import("@/views/report/DocumentReportEmployee"),
                meta: {
                  permission: "",
                },
              },
              {
                path: "document-attribute-report/:menu_item",
                component: () => import("@/views/report/AttributeReport"),
                meta: {
                  permission: "",
                },
              },
              {
                path: "my-document-report",
                component: () => import("@/views/report/MyDocumentReport"),
                meta: {
                  permission: "",
                },
              },
              {
                path: "control-punkt-report",
                component: () => import("@/views/report/ControlPunktReport"),
                meta: {
                  permission: "control_punkt_report",
                },
              },
              /////////////////////////////////
              {
                path: "document-templates/create",
                component: () => import("@/views/document/templates/Create"),
                meta: {
                  permission: "document_template-create",
                },
              },
              {
                path: "document-templates/update/:id",
                component: () => import("@/views/document/templates/Create"),
                meta: {
                  permission: "document_template-update",
                },
              },
              {
                path: "document-templates/list",
                component: () => import("@/views/document/templates/Index"),
                meta: {
                  permission: "document_template-index",
                },
              },
              {
                path: "company-requisites/list",
                component: () => import("@/views/companyRequisite/Index"),
                meta: {
                  permission: "requisite-index",
                },
              },
              {
                path: "signers-group/list",
                component: () => import("@/views/signersGroup/Index"),
                meta: {
                  permission: "signer_group-index",
                },
              },
              {
                path: "signers-group/notsigner",
                component: () => import("@/views/signersGroup/Notsigner"),
                meta: {
                  permission: "signer_group-index",
                },
              },
              {
                path: "purchase-catalogs/list",
                component: () => import("@/views/purchaseCatalog/Index"),
                meta: {
                  permission: "purchase_catalog-index",
                },
              },
              {
                path: "partners/list",
                component: () => import("@/views/partners/Index"),
                meta: {
                  permission: "partners-index",
                },
              },
              {
                path: "document-types/list",
                component: () => import("@/views/documentType/Index"),
                meta: {
                  permission: "document_type-index",
                },
              },
              {
                path: "directories/list",
                component: () => import("@/views/directory/Index.vue"),
                meta: {
                  permission: "document_type-index",
                },
              },
              {
                path: "organization/list",
                component: () => import("@/views/organization/Index"),
                meta: {
                  permission: "organization-index",
                },
              },
              {
                path: "requestdoc/list",
                component: () => import("@/views/requestdoc/Index"),
                meta: {
                  permission: "requestdoc-index",
                },
              },
              {
                path: "document-event/documents/list",
                component: () => import("@/views/document/DocumentHistory"),
                meta: {
                  permission: "",
                },
              },
              /////////////
              {
                path: "phonebook/list",
                component: () => import("@/views/koreshok/Phonebook"),
              },
              {
                path: "timeline/list",
                component: () => import("@/views/Timeline/Index"),
              },
              {
                path: "blank-homepage",
                component: () => import("@/views/blankTemplate/Index.vue"),
                meta: {
                  permission: "blank-template-create",
                },
              },
              {
                path: "blank-templates/get-blank",
                component: () => import("@/views/blankTemplate/getBlank.vue"),
                meta: {
                  permission: "blank-template-index",
                },
              },
              {
                path: "mainpage",
                component: () => import("@/views/Home"),
              },
              {
                path: "documents/list/:menu_item/:document_type",
                component: () => import("@/views/document/Index"),
              },
              {
                path: "complaens/cencel-document",
                component: () =>
                  import("@/views/complaens/cancelDocuments.vue"),
                meta: {
                  permission: "complaens-cancel-documents",
                },
              },
              {
                path: "complaens/control/:menu_item",
                component: () =>
                  import("@/views/complaens/DocumentControlCompliance.vue"),
                meta: {
                  permission: "complaens-cancel-documents",
                },
              },
              {
                path: "complaens/restr/:menu_item",
                component: () => import("@/views/complaens/ComplaensRestr.vue"),
                meta: {
                  permission: "complaens-cancel-documents",
                },
              },
              {
                path: "complaens/restr-user",
                component: () => import("@/views/complaens/ReestrUser.vue"),
                meta: {
                  permission: "complaens-cancel-documents",
                },
              },
              {
                path: "complaens/resume_employee/:menu_item",
                component: () => import("@/views/complaens/ResumeEmployee.vue"),
                // meta: {
                //   permission: true,
                // },
              },
              {
                path: "complaens/summ",
                component: () => import("@/views/complaens/summ.vue"),
                meta: {
                  permission: "complaens-cancel-documents",
                },
              },
              {
                path: "complaens/questions",
                component: () =>
                  import("@/views/complaens/ComplaensQuestion.vue"),
                // meta: {
                //   permission: "complaens-cancel-documents",
                // },
              },
              {
                path: "chat",
                component: () =>
                  import("@/views/chat/ChatPage"),
              },
            ],
          },
          {
            path: "/personcontrol",
            component: EdoSidebar,
            children: [
              {
                path: "home",
                component: () => import("@/views/dashboards/PersonalControlHome"),
              },
              ,
              // Xodimlar boshqaruv qismi boshlandi
              {
                path: "employees/list",
                component: () => import("@/views/employee/Index"),
                // meta: {
                //   permission: "document_template-create",
                // },
              },
              {
                path: "dismissed-employees/list",
                component: () => import("@/views/dismissedEmployees/Index"),
                // meta: {
                //   permission: "document_template-create",
                // },
              },
              {
                path: "leaving-reasons/list",
                component: () => import("@/views/leavingReasons/Index"),
                meta: {
                  permission: "leaving_reasons-index",
                },
              },
              {
                path: "appeal-content/list",
                component: () => import("@/views/appealContent/Index"),
                meta: {
                  permission: "appeal_content-index",
                },
              },
              {
                path: "vacation-registry",
                component: () => import("@/views/registry/Vacation"),
                meta: {
                  // permission: "vacation-registry-asaka",
                },
              },
              {
                path: "ish-rejimi-registry",
                component: () => import("@/views/registry/IshRejimi"),
                meta: {
                  // permission: "vacation-registry-asaka",
                },
              },
              {
                path: "otgul-registry",
                component: () => import("@/views/registry/Otgul"),
                meta: {
                  // permission: "vacation-registry-asaka",
                },
              },
              {
                path: "education-registry",
                component: () => import("@/views/registry/Education"),
                meta: {
                  // permission: "vacation-registry-asaka",
                },
              },
              {
                path: "business-trip-registry",
                component: () => import("@/views/registry/BusinessTrip"),
                meta: {
                  permission: "business_trip",
                },
              },
              {
                path: "work-calendar",
                component: () => import("@/views/workCalendar/Index"),
                meta: {
                  permission: "",
                },
              },
              {
                path: "family-relative/list",
                component: () => import("@/views/familyRelatives/Index"),
              },

              {
                path: "hr-language/list",
                component: () => import("@/views/hrLanguage/Index"),
                meta: {
                  permission: "hr-language-index",
                },
              },
              {
                path: "hr-party/list",
                component: () => import("@/views/hrParty/Index"),
                meta: {
                  permission: "hr-party-index",
                },
              },
              {
                path: "hr-study-degree/list",
                component: () => import("@/views/hrStudyDegree/Index"),
                meta: {
                  permission: "hr-study-degree-index",
                },
              },
              {
                path: "hr-study-type/list",
                component: () => import("@/views/hrStudyType/Index"),
                meta: {
                  permission: "hr-study-type-index",
                },
              },
              {
                path: "hr-university/list",
                component: () => import("@/views/hrUniversity/Index"),
                meta: {
                  permission: "hr-university-index",
                },
              },
              {
                path: "hr-major/list",
                component: () => import("@/views/hrMajor/Index"),
                meta: {
                  permission: "hr-major-index",
                },
              },
              {
                path: "hr-military-rank/list",
                component: () => import("@/views/hrMilitaryRank/Index"),
                meta: {
                  permission: "hr-military-rank-index",
                },
              },
              {
                path: "hr-state-awards/list",
                component: () => import("@/views/hrStateAward/Index"),
                meta: {
                  permission: "hr-state-award-index",
                },
              },
              {
                path: "currencies/list",
                component: () => import("@/views/currency/Index"),
                meta: {
                  permission: "currency-index",
                },
              },
              {
                path: "currencies/history",
                component: () => import("@/views/currencyHistory/Index"),
                meta: {
                  permission: "currency-index",
                },
              },
              {
                path: "saptransaction/list",
                component: () => import("@/views/sapTransaction/Index"),
                // meta: {
                //   permission: "currency-index",
                // },
              },
              {
                path: "jointventure/list",
                component: () => import("@/views/jointVenture/Index"),
                // meta: {
                //   permission: "currency-index",
                // },
              },
              // Xodimlar boshqaruv qismi tugadi

              // Tashkiliy boshqaruv qismi boshlandi
              
              {
                path: "test-orgchart/:id",
                component: () => import("@/views/test/test"),
              },
              {
                path: "staffs/list",
                component: () => import("@/views/staff/Index"),
                // meta: {
                //   permission: "staff-index",
                // },
              },
              {
                path: "staffs/employeelist",
                component: () => import("@/views/staff/EmployeeIndex"),
                // meta: {
                //   permission: "shtat-korish",
                // },
              },
              {
                path: "staffs/vacancy",
                component: () => import("@/views/staff/EmployeesVacancy"),
                // meta: {
                //   permission: "shtat-korish",
                // },
              },
              {
                path: "staff-criticals/list",
                component: () => import("@/views/staffCritical/Index"),
                // meta: {
                //   permission: "position-index",
                // },
              },
              {
                path: "coefficients/list",
                component: () => import("@/views/coefficient/Index"),
                // meta: {
                //   permission: "coefficient-index",
                // },
              },
              {
                path: "departments/list",
                component: () => import("@/views/department/Index"),
                // meta: {
                //   permission: "department-index",
                // },
              },
              {
                path: "departments/tree",
                component: () => import("@/views/department/IndexTree"),
                // meta: {
                //   permission: "department-index_tree",
                // },
              },
              {
                path: "/department-types/list",
                component: () => import("@/views/departmentType/Index"),
                // meta: {
                //   permission: "department_type-index",
                // },
              },
              {
                path: "access-departments/list",
                component: () => import("@/views/accessDepartment/Index"),
                // meta: {
                //   permission: "coefficient-index",
                // },
              },
              {
                path: "sap-transaction/list",
                component: () => import("@/views/sapTransaction/Index"),
                // meta: {
                //   permission: "sap-transactions-index",
                // },
              },
              {
                path: "ranges/list",
                component: () => import("@/views/range/Index"),
                // meta: {
                //   permission: "range-index",
                // },
              },
              {
                path: "tariff-scales/list",
                component: () => import("@/views/tariffScale/Index"),
                // meta: {
                //   permission: "tariff_scale-index",
                // },
              },
              {
                path: "positions/list",
                component: () => import("@/views/position/Index"),
                // meta: {
                //   permission: "tariff_scale-index",
                // },
              },
              {
                path: "position-types/list",
                component: () => import("@/views/positionType/Index"),
                // meta: {
                //   permission: "tariff_scale-index",
                // },
              },
              {
                path: "currencies/list",
                component: () => import("@/views/currency/Index"),
                // meta: {
                //   permission: "tariff_scale-index",
                // },
              },
              {
                path: "currencies/history",
                component: () => import("@/views/currencyHistory/Index"),
                // meta: {
                //   permission: "tariff_scale-index",
                // },
              },
              {
                path: "access-departments/list",
                component: () => import("@/views/accessDepartment/Index"),
                // meta: {
                //   permission: "tariff_scale-index",
                // },
              },
              {
                path: "access-types/list",
                component: () => import("@/views/accessType/Index"),
                // meta: {
                //   permission: "tariff_scale-index",
                // },
              },
              {
                path: "ranges/list",
                component: () => import("@/views/range/Index"),
                // meta: {
                //   permission: "range-index",
                // },
              },
              {
                path: "personal-types/list",
                component: () => import("@/views/personalType/Index"),
                // meta: {
                //   permission: "personal_type-index",
                // },
              },
              {
                path: "expence-types/list",
                component: () => import("@/views/expenceType/Index"),
                // meta: {
                //   permission: "expence_type-index",
                // },
              },
              {
                path: "requirements/list",
                component: () => import("@/views/requirements/Index"),
                // meta: {
                //   permission: "requirement-index",
                // },
              },
              {
                path: "requirement-types/list",
                component: () => import("@/views/requirementType/Index"),
                // meta: {
                //   permission: "requirement_type-index",
                // },
              },
              {
                path: "object-types/list",
                component: () => import("@/views/objectType/Index"),
                // meta: {
                //   permission: "object_type-index",
                // },
              },
              {
                path: "sap-transaction/list",
                component: () => import("@/views/sapTransaction/Index"),
                // meta: {
                //   permission: "sap-transactions-index",
                // },
              },
              {
                path: "joint-venture/list",
                component: () => import("@/views/jointVenture/Index.vue"),
                // meta: {
                //   permission: "joint-ventures-index",
                // },
              },
              {
                path: "other-setting/list",
                component: () => import("@/views/otherSetting/Index.vue"),
                // meta: {
                //     permission: "other-setting-index",
                // },
              },
              {
                path: "profile/:id",
                component: () => import("@/views/profile/Index"),
              },
              // Tashkiliy boshqaruv qismi tugadi
              {
                path: "working-on",
                component: () => import("@/views/layouts/new404"),
              },
              {
                path: "working-on1",
                component: () => import("@/views/layouts/new404"),
              },
              {
                path: "working-on2",
                component: () => import("@/views/layouts/new404"),
              },
              {
                path: "working-on3",
                component: () => import("@/views/layouts/new404"),
              },
              {
                path: "working-on4",
                component: () => import("@/views/layouts/new404"),
              },
              {
                path: "employees/candidates-index",
                component: () => import("@/views/staff/UzAutoJobs/Index"),
                meta: {
                  // permission: "staff-index",
                },
              },
              {
                path: "employees/critical",
                component: () => import("@/views/staff/UzAutoJobs/EmployeesCritical"),
                meta: {
                  // permission: "staff-index",
                },
              },
              {
                path: "working-on7",
                component: () => import("@/views/layouts/new404"),
              },
              {
                path: "table",
                component: () => import("@/views/tabel/Index"),
              },
            ],
          },
          {
            path: "/chart",
            component: EdoSidebar,
            children: [
              {
                path: "home",
                component: () => import("@/views/Home"),
              },
              //   Работа с пользователями qismi boshlandi
              {
                path: "1",
                component: () => import("@/views/charts/1.vue"),
              },
              {
                path: "2",
                component: () => import("@/views/charts/2.vue"),
              },
              {
                path: "3",
                component: () => import("@/views/charts/3.vue"),
              },
              {
                path: "4",
                component: () => import("@/views/charts/4.vue"),
              },
              {
                path: "5",
                component: () => import("@/views/charts/5.vue"),
              },
              {
                path: "6",
                component: () => import("@/views/charts/6.vue"),
              },
              {
                path: "7",
                component: () => import("@/views/charts/7.vue"),
              },
              {
                path: "8",
                component: () => import("@/views/charts/8.vue"),
              },
              {
                path: "9",
                component: () => import("@/views/charts/9.vue"),
              },
              {
                path: "10",
                component: () => import("@/views/charts/10.vue"),
              },
              {
                path: "11",
                component: () => import("@/views/charts/11.vue"),
              },
              {
                path: "12",
                component: () => import("@/views/charts/12.vue"),
              },
              {
                path: "13",
                component: () => import("@/views/charts/13.vue"),
              },
              {
                path: "14",
                component: () => import("@/views/charts/14.vue"),
              },
              {
                path: "15",
                component: () => import("@/views/charts/15.vue"),
              },
            ],
          },
          {
            path: "/adminpanel",
            component: EdoSidebar,
            children: [
              {
                path: "home",
                component: () => import("@/views/dashboards/AdminPanelHome"),
              },
              //   Работа с пользователями qismi boshlandi
              {
                path: "users/list",
                component: () => import("@/views/user/Index"),
              },
              {
                path: "users/diller",
                component: () => import("@/views/user/DillerUsers"),
              },
              {
                path: "user-template/list",
                component: () => import("@/views/userTemplates/Index"),
              },
              //   Работа с пользователями qismi tugadi
              // Правила и разрешения qismi boshlandi
              {
                path: "role-permission/list",
                component: () => import("@/views/user/rolePermission"),
              },
              {
                path: "users/permission",
                component: () => import("@/views/user/Permission"),
              },
              {
                path: "users/role-permission",
                component: () => import("@/views/user/userRolePermission"),
              },
              // Правила и разрешения qismi tugadi

              //   Другие qismi boshlandi
              {
                path: "companies/list",
                component: () => import("@/views/company/Index"),
              },
              {
                path: "countries/list",
                component: () => import("@/views/countries/Index"),
              },
              {
                path: "regions/list",
                component: () => import("@/views/regions/Index"),
              },
              {
                path: "nationalities/list",
                component: () => import("@/views/nationalities/Index"),
              },
              {
                path: "districts/list",
                component: () => import("@/views/districts/Index"),
              },
              {
                path: "notifications/list",
                component: () => import("@/views/notification/Index.vue"),
              },
              {
                path: "refresh-document/list",
                component: () => import("@/views/refreshDocument/Index"),
              },
              {
                path: "template/attach",
                component: () =>
                  import("@/views/attributePermission/Attach.vue"),
              },
              {
                path: "template",
                component: () =>
                  import("@/views/attributePermission/Index.vue"),
              },
              //   Другие qismi tugadi
            ],
          },

          //  KPI qismi boshlandi
          {
            path: "/kpi-sidebar",
            component: EdoSidebar,
            children: [
              {
                path: "home",
                component: () => import("@/views/dashboards/KpiHome"),
              },
              {
                path: "kpi-new",
                component: () => import("@/views/kpi/indexNew"),
              },
              {
                path: "kpi-plan",
                component: () => import("@/views/kpi/indexNewPlan"),
              },
              {
                path: "kpi-settings",
                component: () => import("@/views/kpi/kpisettings"),
                meta: {
                  permission: "kpi-settings",
                },
              },
              {
                path: "kpi-report",
                component: () => import("@/views/kpi/kpiReport"),
                meta: {
                  permission: "kpi-comission",
                },
              },
              {
                path: "kpi-report2",
                component: () => import("@/views/kpi/kpiReport2"),
                meta: {
                  permission: "kpi-comission",
                },
              },
              {
                path: "kpi-report3",
                component: () => import("@/views/kpi/kpiReport3"),
                meta: {
                  permission: "kpi-comission",
                },
              },
              {
                path: "kpi-report4",
                component: () => import("@/views/kpi/kpiReport4"),
                meta: {
                  permission: "kpi-comission",
                },
              },
              {
                path: "kpi-report5",
                component: () => import("@/views/kpi/kpiReport5"),
                meta: {
                  permission: "kpi-comission",
                },
              },
            ],
          },
          //  KPI qismi tugadi

          //  yangi inventory qismi boshlandi
          {
            path: "/inventory",
            component: EdoSidebar,
            children: [
              {
                path: "home",
                component: () => import("@/views/dashboards/KpiHome"),
              },
              {
                path: "list/:status",
                component: () => import("@/views/inventory/Index.vue"),
              },
              {
                path: "report2",
                component: () => import("@/views/inventoryReport/Index2.vue"),
              },
              {
                path: "attaching",
                component: () => import("@/views/inventoryAttaching/Index.vue"),
              },
              //   ---------------
              {
                path: "adress-product",
                component: () =>
                  import("@/views/pgInventory/addressProduct.vue"),
              },
              {
                path: "adress",
                component: () => import("@/views/pgInventory/adress.vue"),
              },
              {
                path: "commission",
                component: () => import("@/views/pgInventory/commission.vue"),
              },
              {
                path: "index1",
                component: () => import("@/views/pgInventory/index1.vue"),
              },
              {
                path: "location-report",
                component: () => import("@/views/pgInventory/LocationReport"),
                visible: store.getters.checkPermission(
                  "mobile_inventory_report"
                ),
              },
              {
                path: "mobile-inventory",
                component: () => import("@/views/pgInventory/MobileInventory"),
                visible: store.getters.checkPermission(
                  "mobile_inventory_report"
                ),
              },
              {
                path: "partnumber-report",
                component: () => import("@/views/pgInventory/PartnumberReport"),
                visible: store.getters.checkPermission(
                  "mobile_inventory_report"
                ),
              },
              {
                path: "product",
                component: () => import("@/views/pgInventory/product.vue"),
              },
              {
                path: "quarter",
                component: () => import("@/views/pgInventory/quarter.vue"),
              },
              {
                path: "inventory-status",
                component: () => import("@/views/pgInventory/Report"),
                visible: store.getters.checkPermission(
                  "mobile_inventory_report"
                ),
              },
              {
                path: "ReportBlanka",
                component: () => import("@/views/pgInventory/ReportBlanka.vue"),
              },
              {
                path: "user-inventory",
                component: () => import("@/views/pgInventory/userInventory"),
                visible: store.getters.checkPermission(
                  "mobile_inventory_report"
                ),
              },
              {
                path: "warehouse",
                component: () => import("@/views/pgInventory/warehouse.vue"),
              },
            ],
          },
          //  yangi inventory qismi tugadi
          {
            path: "/documents-flow",
            component: EdoSidebar,
            children: [],
          },
          // {
          //     path: "/document",
          //     component: EdoSidebar,
          //     children: [
          //         {
          //             path: "template/:documentTypeId",
          //             component: () =>
          //                 import("@/views/document/Template"),
          //             meta: {
          //                 permission: "",
          //             },
          //         },
          //         {
          //             path: "template-favorite",
          //             component: () =>
          //                 import("@/views/document/TemplateFavorite"),
          //             meta: {
          //                 permission: "",
          //             },
          //         },
          //         {
          //             path: "create/:documentTemplateId",
          //             component: () =>
          //                 import("@/views/document/DocumentCreate"),
          //             meta: {
          //                 permission: "",
          //             },
          //         },
          //         {
          //             path: "update/:documentId",
          //             component: () =>
          //                 import("@/views/document/DocumentCreate"),
          //             meta: {
          //                 permission: "",
          //             },
          //         },
          //         {
          //             path: "signers/:pdf_file_name",
          //             component: () =>
          //                 import("@/views/document/Signers.vue"),
          //         },
          //         {
          //             path: ":pdf_file_name",
          //             component: () =>
          //                 import("@/views/document/ShowNew.vue"),
          //         },
          //         {
          //             path: "act/:pdf_file_name",
          //             component: () =>
          //                 import("@/views/document/ShowNew55.vue"),
          //         },
          //         {
          //             path: "test/:pdf_file_name",
          //             component: () =>
          //                 import("@/views/document/ShowNewTest.vue"),
          //         },
          //     ],
          // },

          // -------------------->
          {
            path: "perosonalVocation",
            component: () => import("@/views/documents/personalVocation"),
          },
          {
            path: "test",
            component: () => import("@/views/Test"),
          },
          {
            path: "kpi",
            component: () => import("@/views/kpi/index"),
          },
          {
            path: "kpi-ay2275sd6566",
            component: () => import("@/views/kpi/indexAY"),
            meta: {
              permission: "kpi-change",
            },
          },
          {
            path: "kpi-assistant",
            component: () => import("@/views/kpi/assistant"),
          },
          {
            path: "kpi-report",
            component: () => import("@/views/kpi/kpiReport"),
          },
          {
            path: "kpi-report2",
            component: () => import("@/views/kpi/kpiReport2"),
          },
          {
            path: "kpi-report3",
            component: () => import("@/views/kpi/kpiReport3"),
          },
          {
            path: "kpi-report4",
            component: () => import("@/views/kpi/kpiReport4"),
          },
          {
            path: "kpi-report5",
            component: () => import("@/views/kpi/kpiReport5"),
          },
          {
            path: "test2",
            component: () => import("@/views/Test2"),
          },
          {
            path: "test3",
            component: () => import("@/views/Test3"),
          },
          {
            path: "lock",
            component: () => import("@/views/layouts/lock"),
          },
        ],
      },
      {
        path: "perosonalVocation",
        component: () => import("@/views/documents/personalVocation"),
      },
      {
        path: "test",
        component: () => import("@/views/Test"),
      },
      {
        path: "kpi",
        component: () => import("@/views/kpi/index"),
      },
      {
        path: "kpi-ay2275sd6566",
        component: () => import("@/views/kpi/indexAY"),
        meta: {
          permission: "kpi-change",
        },
      },
      {
        path: "kpi-assistant",
        component: () => import("@/views/kpi/assistant"),
      },
      {
        path: "kpi-report",
        component: () => import("@/views/kpi/kpiReport"),
      },
      {
        path: "kpi-report2",
        component: () => import("@/views/kpi/kpiReport2"),
      },
      {
        path: "kpi-report3",
        component: () => import("@/views/kpi/kpiReport3"),
      },
      {
        path: "kpi-report4",
        component: () => import("@/views/kpi/kpiReport4"),
      },
      {
        path: "kpi-report5",
        component: () => import("@/views/kpi/kpiReport5"),
      },
      {
        path: "test2",
        component: () => import("@/views/Test2"),
      },
      {
        path: "test3",
        component: () => import("@/views/Test3"),
      },
      {
        path: "lock",
        component: () => import("@/views/layouts/lock"),
      },
    ],
  },
  // {
  // path: "/document-templates",
  // component: () =>
  //     import("@/views/layouts/edonew"),
  // children: [
  //     {
  //     path: "create",
  //     component: () =>
  //         import("@/views/document/templates/Create"),
  //     meta: {
  //         permission: "document_template-create",
  //     },
  // },
  // {
  //     path: "update/:id",
  //     component: () =>
  //         import("@/views/document/templates/Create"),
  //     meta: {
  //         permission: "document_template-update",
  //     },
  // },
  // {
  //     path: "list",
  //     component: () =>
  //         import("@/views/document/templates/Index"),
  //     meta: {
  //         permission: "document_template-index",
  //     },
  // },

  // ],
  // },

  // {
  //     path: "/signers-group",
  //     component: () =>
  //         import("@/views/layouts/edonew"),
  //     children: [
  //         {
  //         path: "list",
  //         component: () =>
  //             import("@/views/signersGroup/Index"),
  //         meta: {
  //             permission: "signer_group-index",
  //         },
  //     },
  // ],
  // },
  {
    path: "/template",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "/",
        component: () => import("@/views/attributePermission/Index.vue"),
      },
      {
        path: "attach",
        component: () => import("@/views/attributePermission/Attach.vue"),
      },
    ],
  },
  {
    path: "/koreshok",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/koreshok/Index"),
      },
      {
        path: "open-month",
        component: () => import("@/views/koreshok/OpenMonth"),
      },
    ],
  },
  {
    path: "/tabel",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "",
        component: () => import("@/views/tabel/Index"),
      },
    ],
  },
  {
    path: "/phonebook",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "",
        component: () => import("@/views/koreshok/Phonebook"),
      },
    ],
  },
  // {
  //     path: "/purchase-catalogs",
  //     component: () =>
  //         import("@/views/layouts/edonew"),
  //     children: [{
  //         path: "list",
  //         component: () =>
  //             import("@/views/purchaseCatalog/Index"),
  //         meta: {
  //             permission: "",
  //         },
  //     },]
  //     ,
  // },
  {
    path: "/my-setting/acount",
    component: () => import("@/views/newEdoFiles/Index"),
    meta: {
      permission: "",
    },
  },
  {
    path: "/my-setting",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "/my-setting",
        component: () => import("@/views/layouts/settingsVeiw"),
        meta: {
          permission: "",
        },
      },
      {
        path: "/my-setting/acount",
        component: () => import("@/views/newEdoFiles/Index"),
        meta: {
          permission: "",
        },
      },
    ],
  },
  // {
  //     path: "/company-requisites",
  //     component: () =>
  //         import("@/views/layouts/edonew"),
  //     children: [
  //         {
  //         path: "list",
  //         component: () =>
  //             import("@/views/companyRequisite/Index"),
  //         meta: {
  //             permission: "",
  //         },
  //     },],
  // },
  // {
  //     path: "/partners",
  //     component: () =>
  //         import("@/views/layouts/edonew"),
  //     children: [
  //         {
  //         path: "list",
  //         component: () =>
  //             import("@/views/partners/Index"),
  //         meta: {
  //             permission: "partners-index",
  //         },
  //     },],
  // },
  // {
  //     path: "/document-types",
  //     component: () =>
  //         import("@/views/layouts/edonew"),
  //     children: [{
  //         path: "list",
  //         component: () =>
  //             import("@/views/documentType/Index"),
  //         meta: {
  //             permission: "document_type-index",
  //         },
  //     },],
  // },
  // {
  //     path: "/requestdoc",
  //     component: () =>
  //         import("@/views/layouts/edonew"),
  //     children: [{
  //         path: "list",
  //         component: () =>
  //             import("@/views/requestdoc/Index"),
  //         meta: {
  //             permission: "requestdoc-index",
  //         },
  //     },],
  // },

  {
    path: "/role-permission",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/user/rolePermission"),
        visible: true, //store.getters.checkPermission("role_permission-index"}
      },
    ],
  },
  {
    path: "/employees",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/employee/Index"),
        meta: {
          permission: "employee-index",
        },
      },
      {
        path: "create-di-document",
        component: () => import("@/views/employee/CreateDIDocument"),
        meta: {
          permission: "employee-index",
        },
      },
      {
        path: "transfer",
        component: () => import("@/views/employee/Transfer"),
        meta: {
          permission: "employee-index",
        },
      },
      {
        path: "children",
        component: () => import("@/views/childrenEmployee/Index"),
        meta: {
          permission: "employee-index",
        },
      },
    ],
  },
  {
    path: "/dismissed-employees",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/dismissedEmployees/Index"),
        meta: {
          permission: "employee-index",
        },
      },
    ],
  },
  {
    path: "/document-employee",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/documentEmployee/Index"),
        meta: {
          permission: "document_employee-index",
        },
      },
    ],
  },
  {
    path: "/reports",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/report/Index"),
        meta: {
          permission: "report-index",
        },
      },
      {
        path: "as400-to-excel",
        component: () => import("@/views/report/As400ToExcel"),
        meta: {
          permission: "report-index",
        },
      },
      // {
      //     path: "department/:parent_id",
      //     component: () =>
      //         import("@/views/report/ReportDepartment"),
      //     meta: {
      //         permission: "report-index",
      //     },
      // },
      {
        path: "unv-report/:report_template_id",
        component: () => import("@/views/report/UnvReport.vue"),
        meta: {
          permission: "report-okd-index",
        },
      },
      {
        path: "template/create",
        component: () => import("@/views/report/template/Create.vue"),
        meta: {
          permission: "report-okd-index",
        },
      },
      {
        path: "template/update/:report_template_id",
        component: () => import("@/views/report/template/Create.vue"),
        meta: {
          permission: "report-okd-index",
        },
      },
      {
        path: "template",
        component: () => import("@/views/report/template/Index.vue"),
        meta: {
          permission: "",
        },
      },
      {
        path: "department-okd/:parent_id",
        component: () => import("@/views/report/ReportDepartmentOkd"),
        meta: {
          permission: "report-okd-index",
        },
      },
      {
        path: "okd-report",
        component: () => import("@/views/report/OkdReport"),
        meta: {
          permission: "okd-report-index",
        },
      },
      // {
      //     path: "okd-report-full",
      //     component: () =>
      //         import("@/views/report/OkdReportFull"),
      //     meta: {
      //         permission: "okd-report-index",
      //     },
      // },
      // {
      //     path: "okd-report-full-toshkent",
      //     component: () =>
      //         import("@/views/report/OkdReportFullToshkent"),
      //     meta: {
      //         permission: "okd-report-index",
      //     },
      // },
      // {
      //     path: "document-report-employee",
      //     component: () =>
      //         import("@/views/report/DocumentReportEmployee"),
      //     meta: {
      //         permission: "",
      //     },
      // },
      {
        path: "okd-report-item/:type",
        component: () => import("@/views/report/OkdReportItem"),
        meta: {
          permission: "okd-report-index",
        },
      },
      {
        path: "report-item-full/:type",
        component: () => import("@/views/report/ReportItemFull"),
        meta: {
          permission: "okd-report-index",
        },
      },
      {
        path: "okd-report-item-full-toshkent/:type",
        component: () => import("@/views/report/OkdReportItemFullToshkent"),
        meta: {
          permission: "okd-report-index",
        },
      },
      {
        path: "okd-report-tab/:type",
        component: () => import("@/views/report/OkdReportTab"),
        meta: {
          permission: "okd-report-index",
        },
      },
      {
        path: "okd-report-tab-full/:type",
        component: () => import("@/views/report/OkdReportTabFull"),
        meta: {
          permission: "okd-report-index",
        },
      },
      {
        path: "document-report-employee-item/:type",
        component: () => import("@/views/report/DocumentReportEmployeeItem"),
        meta: {
          permission: "",
        },
      },

      // {
      //     path: "document-attribute-report/:menu_item",
      //     component: () =>
      //         import("@/views/report/AttributeReport"),
      //     meta: {
      //         permission: "",
      //     },
      // },
      // {
      //     path: "my-document-report",
      //     component: () =>
      //         import("@/views/report/MyDocumentReport"),
      //     meta: {
      //         permission: "",
      //     },
      // },

      {
        path: "executor-report",
        component: () => import("@/views/report/ExecutorReport"),
        meta: {
          permission: "",
        },
      },
      {
        path: "avia-report",
        component: () => import("@/views/report/AviaReport"),
        meta: {
          permission: "",
        },
      },
      {
        path: "signer-report",
        component: () => import("@/views/report/SignerReport"),
        meta: {
          permission: "",
        },
      },
      // {
      //     path: "control-punkt-report",
      //     component: () =>
      //         import("@/views/report/ControlPunktReport"),
      //     meta: {
      //         permission: "",
      //     },
      // },
    ],
  },

  {
    path: "/currencies",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/currency/Index"),
        meta: {
          permission: "currency-index",
        },
      },
      {
        path: "history",
        component: () => import("@/views/currencyHistory/Index"),
        meta: {
          permission: "currency-index",
        },
      },
    ],
  },

  {
    path: "/post-order",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/postOrder/Index"),
        meta: {
          permission: "",
        },
      },
      {
        path: "info",
        component: () => import("@/views/postOrder/Info"),
        meta: {
          permission: "",
        },
      },
    ],
  },
  {
    path: "/centrum",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "import",
        component: () => import("@/views/centrum/Index"),
        meta: {
          permission: "",
        },
      },
      {
        path: "info",
        component: () => import("@/views/postOrder/Info"),
        meta: {
          permission: "",
        },
      },
      {
        path: "shablon",
        component: () => import("@/views/centrum/Shablon"),
        meta: {
          permission: "",
        },
      },
      {
        path: "report",
        component: () => import("@/views/centrum/Report"),
        meta: {
          permission: "",
        },
      },
      {
        path: "attribute-report",
        component: () => import("@/views/centrum/AttributeReport"),
        meta: {
          permission: "",
        },
      },
      {
        path: "act-report",
        component: () => import("@/views/centrum/ActReport"),
        meta: {
          permission: "",
        },
      },
    ],
  },
  {
    path: "/dashboard-registry",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: ":id",
        component: () => import("@/views/registry/Index"),
        meta: {
          permission: "",
        },
      },
    ],
  },
  // {
  //     path: "/organization",
  //     component: () =>
  //         import("@/views/layouts/edonew"),
  //     children: [{
  //         path: "list",
  //         component: () =>
  //             import("@/views/organization/Index"),
  //         meta: {
  //             permission: "organization-index",
  //         },
  //     },],
  // },
  {
    path: "/salary-cert",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "/",
        component: () => import("@/views/calendar/salaryCert"),
      },
    ],
  },
  {
    path: "/firm-blank/:document_template_id",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "/",
        component: () => import("@/views/document/FirmBlankCreate"),
      },
    ],
  },
  {
    path: "/firm-blank-only-pdf",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "/",
        component: () => import("@/views/document/FirmBlank"),
      },
    ],
  },
  {
    path: "/lsp-registry",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "/",
        component: () => import("@/views/registry/LSPRegistry"),
        meta: {
          // permission: "vacation-registry-asaka",
        },
      },
    ],
  },
  {
    path: "/diler-registry",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "/",
        component: () => import("@/views/registry/Diler"),
        meta: {
          // permission: "vacation-registry-asaka",
        },
      },
    ],
  },
  {
    path: "/tenge-document-registry",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "/",
        component: () => import("@/views/registry/TengeRegistry"),
        meta: {
          permission: "tenge",
        },
      },
    ],
  },
  {
    path: "/positions",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/position/Index"),
        meta: {
          permission: "position-index",
        },
      },
    ],
  },
  {
    path: "/staffs",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/staff/Index"),
        meta: {
          permission: "staff-index",
        },
      },
    ],
  },
  {
    path: "/staff-criticals",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/staffCritical/Index"),
        meta: {
          permission: "position-index",
        },
      },
    ],
  },
  {
    path: "/coefficients",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/coefficient/Index"),
        meta: {
          permission: "coefficient-index",
        },
      },
    ],
  },
  {
    path: "/access-departments",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/accessDepartment/Index"),
        meta: {
          permission: "coefficient-index",
        },
      },
    ],
  },
  {
    path: "/sap-transaction",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/sapTransaction/Index"),
        meta: {
          permission: "sap-transactions-index",
        },
      },
    ],
  },
  {
    path: "/ranges",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/range/Index"),
        meta: {
          permission: "range-index",
        },
      },
    ],
  },
  {
    path: "/tariff-scales",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/tariffScale/Index"),
        meta: {
          permission: "tariff_scale-index",
        },
      },
    ],
  },
  {
    path: "/countries",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/countries/Index"),
        meta: {
          permission: "country-index",
        },
      },
    ],
  },
  {
    path: "/districts",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/districts/Index"),
        meta: {
          permission: "district-index",
        },
      },
    ],
  },
  {
    path: "/regions",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/regions/Index"),
        meta: {
          permission: "region-index",
        },
      },
    ],
  },
  // {
  //     path: "/mailing",
  //     component: () =>
  //         import("@/views/layouts/edonew"),
  //     children: [{
  //         path: "list",
  //         component: () =>
  //             import("@/views/report/Mailing"),
  //         meta: {
  //             permission: "okd-report-index",
  //         },
  //     },],
  // },
  {
    path: "/refresh-document",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/refreshDocument/Index"),
        meta: {
          permission: "refresh-document",
        },
      },
    ],
  },
  {
    path: "/nationalities",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/nationalities/Index"),
        meta: {
          permission: "nationality-index",
        },
      },
    ],
  },
  {
    path: "/department-types",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/departmentType/Index"),
        meta: {
          permission: "department_type-index",
        },
      },
    ],
  },
  {
    path: "/position-types",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/positionType/Index"),
        meta: {
          permission: "position_type-index",
        },
      },
    ],
  },
  {
    path: "/personal-types",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/personalType/Index"),
        meta: {
          permission: "personal_type-index",
        },
      },
    ],
  },
  {
    path: "/access-types",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/accessType/Index"),
        meta: {
          permission: "access_type-index",
        },
      },
    ],
  },
  {
    path: "/object-types",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/objectType/Index"),
        meta: {
          permission: "object_type-index",
        },
      },
    ],
  },
  {
    path: "/expence-types",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/expenceType/Index"),
        meta: {
          permission: "expence_type-index",
        },
      },
    ],
  },
  {
    path: "/requirement-types",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/requirementType/Index"),
        meta: {
          permission: "requirement_type-index",
        },
      },
    ],
  },
  {
    path: "/car-purchases",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/carPurchase/Index"),
        meta: {
          permission: "car-purchase-index",
        },
      },
    ],
  },
  {
    path: "/employee-info",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/employeeInfo/Index"),
        meta: {
          permission: "car-purchase-index",
        },
      },
    ],
  },
  {
    path: "/requirements",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/requirements/Index"),
        meta: {
          permission: "requirement-index",
        },
      },
    ],
  },
  {
    path: "/departments",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/department/Index"),
        meta: {
          permission: "department-index",
        },
      },
      {
        path: "tree",
        component: () => import("@/views/department/IndexTree"),
        meta: {
          permission: "department-index_tree",
        },
      },
    ],
  },
  {
    path: "/companies",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/company/Index"),
        meta: {
          permission: "company-index",
        },
      },
    ],
  },
  {
    path: "/user-template",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/userTemplates/Index"),
        meta: {
          permission: "user-index",
        },
      },
    ],
  },
  {
    path: "/users",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/user/Index"),
        meta: {
          permission: "user-index",
        },
      },
      {
        path: "diller",
        component: () => import("@/views/user/DillerUsers"),
        meta: {
          permission: "user-index",
        },
      },
      {
        path: "permission",
        component: () => import("@/views/user/Permission"),
        meta: {
          permission: "user-index",
        },
      },
      {
        path: "role-permission",
        component: () => import("@/views/user/userRolePermission"),
        meta: {
          permission: "user-index",
        },
      },
      {
        path: "profile/:id",
        component: () => import("@/views/profile/Index"),
        // meta: {
        //   permission: "user-index",
        // }
      },
      {
        path: "all-users/:id",
        component: () => import("@/views/usersAll/index"),
        // meta: {
        //   permission: "user-index",
        // }
      },
      {
        path: "online",
        component: () => import("@/views/user/Online"),
        meta: {
          permission: "",
        },
      },
    ],
  },
  {
    path: "/helps",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "/",
        component: () => import("@/views/help/Index"),
        meta: {
          permission: "",
        },
      },
    ],
  },
  {
    path: "/qrcode",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "/",
        component: () => import("@/views/QRcode/Index"),
        meta: {
          permission: "",
        },
      },
    ],
  },
  {
    path: "/trial-period",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "/",
        component: () => import("@/views/trialPeriod/Index"),
        meta: {
          permission: "",
        },
      },
    ],
  },
  {
    path: "/timeline",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "/",
        component: () => import("@/views/Timeline/Index"),
        meta: {
          permission: "",
        },
      },
    ],
  },
  {
    path: "/inbox",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "/",
        component: () => import("@/views/documentCreate/Inbox"),
        meta: {
          permission: "",
        },
      },
    ],
  },
  {
    path: "/draft",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "/",
        component: () => import("@/views/documentCreate/Draft"),
        meta: {
          permission: "",
        },
      },
    ],
  },
  {
    path: "/sent",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "/",
        component: () => import("@/views/documentCreate/Sent"),
        meta: {
          permission: "",
        },
      },
    ],
  },
  {
    path: "/documents",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list/:menu_item/:document_type",
        component: () => import("@/views/document/Index"),
        meta: {
          permission: "",
        },
      },
      {
        path: "control/:menu_item/:document_type",
        component: () => import("@/views/document/IndexDocumentControl"),
        meta: {
          permission: "",
        },
      },
      {
        path: "murojaat-va-kiruvchi",
        component: () => import("@/views/document/MurojaatVaKiruvchi"),
        meta: {
          permission: "",
        },
      },
      {
        path: "signed",
        component: () => import("@/views/document/SignedDocuments"),
        meta: {
          permission: "",
        },
      },
      {
        path: "index-new",
        component: () => import("@/views/document/IndexNew"),
        meta: {
          permission: "",
        },
      },
      {
        path: "index-2023",
        component: () => import("@/views/document/Index2023"),
        meta: {
          permission: "",
        },
      },
      {
        path: "show/:id",
        component: () => import("@/views/document/Show"),
        meta: {
          permission: "",
        },
      },
      {
        path: "show-oldd/:id",
        component: () => import("@/views/document/Show"),
        meta: {
          permission: "",
        },
      },
      {
        path: "show-new/:pdf_file_name",
        component: () => import("@/views/document/ShowNew"),
        meta: {
          permission: "",
        },
      },
      {
        path: "show-only-pdf/:pdf_file_name",
        component: () => import("@/views/document/ShowOnlyPdf"),
        meta: {
          permission: "",
        },
      },
      // {
      //     path: "report",
      //     component: () =>
      //         import("@/views/document/Report"),
      //     meta: {
      //         permission: "",
      //     },
      // },
      // {
      //     path: "report/znz",
      //     component: () =>
      //         import("@/views/document/ZnzReport"),
      //     meta: {
      //         permission: "",
      //     },
      // },
      // {
      //     path: "report/lsp",
      //     component: () =>
      //         import("@/views/document/LspReport"),
      //     meta: {
      //         permission: "",
      //     },
      // },
      {
        path: "report/compliance_incoming",
        component: () => import("@/views/document/LspReport"),
        meta: {
          permission: "",
        },
      },
      {
        path: "report/otz",
        component: () => import("@/views/employee/OtzReport"),
        meta: {
          permission: "",
        },
      },
      {
        path: "executor/:menu_item",
        component: () => import("@/views/document/Executor"),
        meta: {
          permission: "",
        },
      },
      // {
      //     path: "document-event",
      //     component: () =>
      //         import("@/views/document/DocumentHistory"),
      //     meta: {
      //         permission: "",
      //     },
      // },
    ],
  },
  {
    path: "/",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "/",
        component: () => import("@/views/Home"),
      },
      {
        path: "/departments",
        component: () => import("@/views/department/Index"),
        meta: {
          permission: "department-index",
        },
      },
    ],
  },
  {
    path: "/notifications",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/notification/Index.vue"),
        meta: {
          permission: "notification-index",
        },
      },
    ],
  },
  {
    path: "/joint-venture",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/jointVenture/Index.vue"),
        meta: {
          permission: "joint-ventures-index",
        },
      },
    ],
  },
  // {
  //     path: "/directories",
  //     component: () =>
  //         import("@/views/layouts/edonew"),
  //     children: [
  //         {
  //         path: "list",
  //         component: () =>
  //             import("@/views/directory/Index.vue"),
  //     },
  // ],
  // },
  {
    path: "/complaens",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "cencel-document",
        component: () => import("@/views/complaens/cancelDocuments.vue"),
        meta: {
          permission: "complaens-cancel-documents",
        },
      },
      {
        path: "control/:menu_item",
        component: () =>
          import("@/views/complaens/DocumentControlCompliance.vue"),
        meta: {
          permission: "complaens-cancel-documents",
        },
      },
      {
        path: "summ",
        component: () => import("@/views/complaens/summ.vue"),
        meta: {
          permission: "complaens-cancel-documents",
        },
      },
    ],
  },

  {
    path: "/inventory",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list/:status",
        component: () => import("@/views/inventory/Index.vue"),
      },
      {
        path: "list",
        component: () => import("@/views/inventory/Index.vue"),
      },
      {
        path: "addresses",
        component: () => import("@/views/inventoryAddress/Index.vue"),
      },
      {
        path: "products",
        component: () => import("@/views/inventoryProductList/Index.vue"),
      },
      {
        path: "report",
        component: () => import("@/views/inventoryReport/Index.vue"),
      },
      {
        path: "report1",
        component: () => import("@/views/inventory/IndexReport.vue"),
      },
      {
        path: "report2",
        component: () => import("@/views/inventoryReport/Index2.vue"),
      },
      {
        path: "report3",
        component: () => import("@/views/inventoryReport/Index3.vue"),
      },
      {
        path: "commissions",
        component: () => import("@/views/inventoryCommission/Index.vue"),
      },
      {
        path: "blanks",
        component: () => import("@/views/inventoryBlank/Index.vue"),
      },
      {
        path: "attaching",
        component: () => import("@/views/inventoryAttaching/Index.vue"),
      },
    ],
  },

  {
    path: "/blank-templates",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/blankTemplate/Index"),
        meta: {
          permission: "blank-template-create",
        },
      },
      {
        path: "edit/:id",
        component: () => import("@/views/blankTemplate/Template"),
        meta: {
          permission: "blank-template-create",
        },
      },
      {
        path: "get-blank/",
        component: () => import("@/views/blankTemplate/getBlank"),
        meta: {
          permission: "blank-template-index",
        },
      },
      {
        path: "get-file/:id",
        component: () => import("@/views/blankTemplate/getFile"),
        meta: {
          permission: "blank-template-index",
        },
      },
    ],
  },

  {
    path: "/as400toexcel",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "list",
        component: () => import("@/views/as400ToExcel/Index.vue"),
      },
      {
        path: "history",
        component: () => import("@/views/as400ToExcel/History.vue"),
      },
    ],
  },
  {
    path: "/ides",
    component: () => import("@/views/layouts/edonew"),
    children: [
      {
        path: "index/:menu_item",
        component: () => import("@/views/ides/InboxNew"),
        meta: {
          permission: "ides",
        },
      },
      {
        path: "index-new/:menu_item",
        component: () => import("@/views/ides/InboxNew"),
        meta: {
          permission: "ides",
        },
      },
      {
        path: "show/:id",
        component: () => import("@/views/ides/ShowNew"),
        meta: {
          permission: "ides",
        },
      },
    ],
  },
  {
    path: "/login",
    component: () => import("@/views/layouts/Login"),
    visible: true, //store.getters.checkPermission("templates-index")
  },
  {
    path: "/login2",
    component: () => import("@/views/layouts/Login2"),
    visible: true, //store.getters.checkPermission("templates-index")
  },
  {
    path: "/login1",
    component: () => import("@/views/layouts/Login1"),
    visible: true, //store.getters.checkPermission("templates-index")
  },
  {
    path: "/sign-document/:agreement_hash/:user_id",
    component: () => import("@/views/signDocument/Sign"),
    visible: true, //store.getters.checkPermission("templates-index")
  },
  {
    path: "/404",
    component: () => import("@/views/layouts/404"),
    visible: true, //store.getters.checkPermission("templates-index")
  },
  {
    path: "/403",
    component: () => import("@/views/layouts/403"),
  },
  {
    path: "*",
    redirect: "/404",
  },
];

routes = routes.concat(attestation);
routes = routes.concat(orgtex);
routes = routes.concat(worktask);
routes = routes.concat(cdpt);
routes = routes.concat(skud);
routes = routes.concat(skudmanual);
routes = routes.concat(skudfull);
routes = routes.concat(swodsfullmanual);
routes = routes.concat(medpunkt);
routes = routes.concat(edonew);

const router = new VueRouter({
  mode: "hash",
  // mode: 'history',
  base: process.env.BASE_URL,
  routes,
});

//--------------------------------------------------------------------------------------
router.beforeEach((to, from, next) => {
  if (
    window.localStorage.getItem("selected_module") &&
    window.localStorage.getItem("selected_module") != "undefined"
  ) {
    let modules = JSON.parse(window.localStorage.getItem("modules"));
    let selected_module = JSON.parse(
      window.localStorage.getItem("selected_module")
    );
    if (
      selected_module !=
      modules.find((m) => m.route.search(to.fullPath.split("/")[1]) > 0)
    ) {
      window.localStorage.setItem(
        "selected_module",
        JSON.stringify(
          modules.find((m) => m.route.search(to.fullPath.split("/")[1]) > 0)
        )
      );
    }
  }

  var access_token = store.getters.getAccessToken();
  let localStorage = window.localStorage;
  const now = new Date();
  let expire_token = localStorage.getItem("expire_token", now.getTime());
  let user = store.getters.getUser();
  if (user.username != "QG95921") {
    if (
      !(expire_token == "null" || expire_token == null) &&
      expire_token < now.getTime() - 10800000 &&
      to.fullPath != "/login"
    ) {
      store.dispatch("setUser", null);
      store.dispatch("setPermissions", null);
      store.dispatch("setRole", null);
      store.dispatch("setAccessToken", null);
      // Cookies.remove("access_token");
      next({
        path: "/login",
      });
    }
  }

  if (!access_token && to.fullPath != "/login" && to.fullPath != "/login1") {
    store.dispatch("setRedirectUrl", to.fullPath);
    localStorage.setItem("expire_token", now.getTime());
    next({
      path: "/login",
    });
  } else if (
    !to.meta.permission ||
    store.getters.checkPermission(to.meta.permission)
  ) {
    localStorage.setItem("expire_token", now.getTime());
    next();
  } else {
    localStorage.setItem("expire_token", now.getTime());
    next("/403");
  }
});
export default router;
