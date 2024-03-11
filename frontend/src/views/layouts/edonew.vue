<template>
  <v-app style="background-color: #f1f5f8">
    <edo-navbar></edo-navbar>
    <router-view class="" style="margin-top: 47px"></router-view>
    <v-footer fixed padless color="red">
      <edo-footer></edo-footer>
    </v-footer>
  </v-app>
</template>

<script>
import EdoNavbar from "@/views/layouts/EdoNavbar.vue";
import EdoFooter from "@/views/layouts/EdoFooter.vue";

export default {
  components: {
    EdoNavbar,
    EdoFooter,
  },
  data() {
    return {
      modules: [
        // {
        //   src: "img/svg/chart_01.svg",
        //   src2: "img/svg/chart_01.svg",
        //   text: "Charts",
        //   color: "orange",
        //   sidebar: false,
        //   visibled: this.$store.getters.checkPermission("adminvisible_charts"),
        //   route: "/chart",
        // },
        {
          src: "img/svg/presentation_icon.svg",
          src2: "img/svg/presentation_icon.svg",
          text: "Dashboards",
          is_always_visible: true,
          color: "#EEFF41",
          route: "/collection/charts",
          groups: [
            {
              name: "linestop.working_with_tickets",
              menus: [],
            },
          ],
        },
        {
          src: "img/svg/sign_icon.svg",
          src2: "img/svg/sign_icon.svg",
          text: "new_workflow.navbar_name",
          color: "#99ff33",
          is_always_visible: true,
          route: "/documentsidebar/home",
          groups: [
            {
              name: "new_workflow.create_document",
              menus: [
                {
                  icon: "mdi-file-plus-outline",
                  text: "new_workflow.created_documents",
                  route: "/documentsidebar/document-folder/1",
                  color: "green",
                  visible: true,
                },
                {
                  icon: "mdi-file-check-outline",
                  text: "new_workflow.favorit_documents",
                  route: "/documentsidebar/template-favorite",
                  color: "green",
                  visible: true,
                },
                {
                  icon: "mdi-file-document-outline",
                  text: "new_workflow.common_documents",
                  route: "/documentsidebar/document-often",
                  color: "green",
                  visible: true,
                },
              ],
            },
            {
              name: "new_workflow.folder_document",
              menus: [
                {
                  icon: "mdi-folder-download-outline",
                  text: "new_workflow.inbox",
                  route: "/documentsidebar/document-folder/2",
                  color: "#f8a300",
                  visible: true,
                },
                {
                  icon: "mdi-folder-upload-outline",
                  text: "new_workflow.outbox",
                  route: "/documentsidebar/document-folder/3",
                  color: "#f8a300",
                  visible: true,
                },
                {
                  icon: "mdi-folder-text-outline",
                  text: "new_workflow.draft",
                  route: "/documentsidebar/document-folder/4",
                  color: "#f8a300",
                  visible: true,
                },
                {
                  icon: "mdi-folder-remove-outline",
                  text: "new_workflow.cancel",
                  route: "/documentsidebar/document-folder/5",
                  color: "#f8a300",
                  visible: true,
                },
                {
                  icon: "mdi-folder-outline",
                  text: "new_workflow.all_documents",
                  route: "/documentsidebar/document-folder/list/all/0",
                  color: "#f8a300",
                  visible:
                    !!this.$store.getters.checkPermission("document-list-all"),
                },
              ],
            },
            {
              name: "new_workflow.folder_report",
              menus: [
                {
                  icon: "mdi-file-document-outline",
                  text: "new_workflow.my_documents",
                  route: "/documentsidebar/my-document-report",
                  color: "green",
                  visible: true,
                },
                {
                  icon: "mdi-file-document-outline",
                  text: "new_workflow.lsp_report",
                  route: "/documentsidebar/report/lsp",
                  visible: true,
                  color: "green",
                },
                {
                  icon: "mdi-file-document-outline",
                  text: "new_workflow.index",
                  route: "/documentsidebar/reports/department/0",
                  visible:
                    !!this.$store.getters.checkPermission("report-index"),
                  color: "green",
                },
                {
                  icon: "mdi-folder-open",
                  text: "new_workflow.report",
                  route: "/documentsidebar/control-punkt-report",
                  visible: !!this.$store.getters.checkPermission(
                    "control_punkt_report"
                  ),
                  color: "green",
                },
                {
                  icon: "mdi-file-check-outline",
                  text: "new_workflow.znz",
                  route: "/documentsidebar/report/znz",
                  visible: true,
                  color: "green",
                },
                {
                  icon: "mdi-file-document-outline",
                  text: "new_workflow.okd",
                  route: "/documentsidebar/okd-report-full",
                  color: "green",
                  visible:
                    !!this.$store.getters.checkPermission("okd-report-index"),
                },
                {
                  icon: "mdi-file-document-outline",
                  text: "new_workflow.myokddetail",
                  route: "/documentsidebar/okd-report-full-toshkent",
                  color: "green",
                  visible:
                    !!this.$store.getters.checkPermission("okd-report-index"),
                },
                {
                  icon: "mdi-file-document-outline",
                  text: "new_workflow.myokd",
                  route: "/documentsidebar/document-report-employee",
                  color: "green",

                  visible:
                    !!this.$store.getters.checkPermission("okd-report-index"),
                },
              ],
            },
            {
              name: "new_workflow.compliance",
              menus: [
                {
                  icon: "mdi-archive-outline",
                  text: "new_workflow.rejected_documents",
                  route: "/documentsidebar/complaens/cencel-document",
                  visible: !!this.$store.getters.checkRole("compliance"),
                },
                {
                  icon: "mdi-archive-outline",
                  text: "new_workflow.payment_register",
                  route: "/documentsidebar/complaens/control/payment_sheet",
                  visible: !!this.$store.getters.checkRole("compliance"),
                },
                {
                  icon: "mdi-archive-outline",
                  text: "new_workflow.lsp",
                  route: "/documentsidebar/complaens/control/lsp",
                  visible: !!this.$store.getters.checkRole("compliance"),
                },
                {
                  icon: "mdi-archive-outline",
                  text: "new_workflow.output",
                  route:
                    "/documentsidebar/complaens/control/compliance_incoming",
                  visible: !!this.$store.getters.checkRole("compliance"),
                },
                {
                  icon: "mdi-archive-outline",
                  text: "new_workflow.reisterone",
                  route:
                    "/documentsidebar/complaens/restr/compliance_restr_one",
                  visible: !!this.$store.getters.checkRole("compliance"),
                },
                {
                  icon: "mdi-archive-outline",
                  text: "new_workflow.reistertwo",
                  route:
                    "/documentsidebar/complaens/restr/compliance_restr_two",
                  visible: !!this.$store.getters.checkRole("compliance"),
                },
                {
                  icon: "mdi-archive-outline",
                  text: "new_workflow.reisterthree",
                  route: "/documentsidebar/complaens/restr-user",
                  visible: !!this.$store.getters.checkRole("compliance"),
                },
              ],
            },
            {
              name: "new_workflow.parametrs",
              menus: [
                {
                  icon: "mdi-clipboard-file-outline",
                  text: "new_workflow.document_template",
                  route: "/documentsidebar/document-templates/list",
                  color: "green",
                  visible: !!this.$store.getters.checkPermission(
                    "document_template-index"
                  ),
                },
                {
                  icon: "mdi-folder-open",
                  text: "new_workflow.requisites",
                  route: "/documentsidebar/company-requisites/list",
                  color: "green",
                  visible:
                    !!this.$store.getters.checkPermission("requisite-index"),
                },
                {
                  icon: "mdi-checkbox-multiple-marked-outline",
                  text: "new_workflow.signers_group",
                  route: "/documentsidebar/signers-group/list",
                  color: "green",
                  visible:
                    !!this.$store.getters.checkPermission("signer_group-index"),
                },
                {
                  icon: "mdi-checkbox-multiple-marked-outline",
                  text: "new_workflow.not_signers",
                  route: "/documentsidebar/signers-group/notsigner",
                  color: "green",
                  visible:
                    !!this.$store.getters.checkPermission("signer_group-index"),
                },
                {
                  icon: "mdi-checkbox-multiple-marked-outline",
                  text: "new_workflow.catalogs",
                  route: "/documentsidebar/purchase-catalogs/list",
                  color: "green",
                  visible: !!this.$store.getters.checkPermission(
                    "purchase_catalog-index"
                  ),
                },
                {
                  icon: "mdi-checkbox-multiple-marked-outline",
                  text: "new_workflow.index",
                  route: "/documentsidebar/partners/list",
                  color: "green",
                  visible:
                    !!this.$store.getters.checkPermission("partners-index"),
                },
                {
                  icon: "mdi-folder-open",
                  text: "new_workflow.document_types",
                  route: "/documentsidebar/document-types/list",
                  color: "green",
                  visible: !!this.$store.getters.checkPermission(
                    "document_type-index"
                  ),
                },
                {
                  icon: "mdi-folder-open",
                  text: "new_workflow.directory_index",
                  route: "/documentsidebar/directories/list",
                  color: "green",
                  visible: !!this.$store.getters.checkRole("directory"),
                },
                {
                  icon: "mdi-clipboard-account-outline",
                  text: "new_workflow.organization_index",
                  route: "/documentsidebar/organization/list",
                  color: "green",
                  visible:
                    !!this.$store.getters.checkPermission("organization-index"),
                },
                {
                  icon: "mdi-clipboard-account-outline",
                  text: "new_workflow.requestdoc_index",
                  route: "/documentsidebar/requestdoc/list",
                  color: "green",
                  visible:
                    !!this.$store.getters.checkPermission("requestdoc-index"),
                },
                {
                  icon: "mdi-folder-open",
                  text: "new_workflow.document_history",
                  route: "/documentsidebar/document-event/documents/list",
                  color: "green",
                  visible:
                    !!this.$store.getters.checkRole("superadministrator"),
                },
              ],
            },
            {
              name: "new_workflow.blank_templates",
              menus: [
                {
                  icon: "mdi-file-plus-outline",
                  text: "new_workflow.blank_templates",
                  route: "/documentsidebar/blank-homepage",
                  color: "green",
                  visible: !!this.$store.getters.checkPermission(
                    "blank-template-create"
                  ),
                },
                {
                  icon: "mdi-file-document-multiple-outline",
                  text: "new_workflow.blank_list",
                  route: "/documentsidebar/blank-templates/get-blank",
                  color: "green",
                  visible: !!this.$store.getters.checkPermission(
                    "blank-template-index"
                  ),
                },
              ],
            },
            {
              name: "new_workflow.archive",
              menus: [
                {
                  icon: "mdi-account-check",
                  text: "new_workflow.employee",
                  route: "/documentsidebar/documents/list/archive/employee",
                  color: "green",
                  visible: true,
                },
                {
                  icon: "mdi-source-fork",
                  text: "new_workflow.staff",
                  route: "/documentsidebar/documents/list/archive/staff",
                  color: "green",
                  visible: true,
                },
              ],
            },
          ],
        },
        {
          src: "img/svg/personControl.svg",
          src2: "img/svg/personControl.svg",
          text: "new_staffcontrol.navbar_name",
          color: "#3333cc",
          route: "/personcontrol/home",
          groups: [
            {
              name: "new_staffcontrol.navbar_name",
              menus: [
                {
                  icon: "mdi-account-multiple-outline",
                  text: "new_staffcontrol.staff",
                  route: "/personcontrol/employees/list",
                  color: "#00B950",
                  visible:
                    !!this.$store.getters.checkPermission("employee-index"),
                },
                {
                  icon: "mdi-account-multiple-minus",
                  text: "new_staffcontrol.dismissed_staff",
                  route: "/personcontrol/dismissed-employees/list",
                  color: "#00B950",
                  visible:
                    !!this.$store.getters.checkPermission("employee-index"),
                },
                {
                  icon: "mdi-file-outline",
                  text: "new_staffcontrol.vacation_registry",
                  route: "/personcontrol/vacation-registry",
                  color: "#00B950",
                  visible: !!this.$store.getters.checkPermission(
                    "vacation-registry-asaka"
                  ),
                },
                {
                  icon: "mdi-file-outline",
                  text: "new_staffcontrol.ishrejimi_registry",
                  route: "/personcontrol/ish-rejimi-registry",
                  color: "#00B950",
                  visible: !!this.$store.getters.checkPermission(
                    "ish-rejimi-registry"
                  ),
                },
                {
                  icon: "mdi-file-outline",
                  text: "new_staffcontrol.otgul_registry",
                  route: "/personcontrol/otgul-registry",
                  color: "#00B950",
                  visible:
                    !!this.$store.getters.checkPermission("otgul-registry"),
                },
                {
                  icon: "mdi-file-outline",
                  text: "new_staffcontrol.education_registry",
                  route: "/personcontrol/education-registry",
                  color: "#00B950",
                  visible: !!this.$store.getters.checkPermission(
                    "education-registry-asaka"
                  ),
                },
                {
                  icon: "mdi-file-outline",
                  text: "new_staffcontrol.businesstrip_registry",
                  route: "/personcontrol/business-trip-registry",
                  color: "#00B950",
                  visible:
                    !!this.$store.getters.checkPermission("business_trip"),
                },
                {
                  icon: "mdi-calendar",
                  text: "new_staffcontrol.work_calendar",
                  route: "/personcontrol/work-calendar",
                  color: "#00B950",
                  visible: true,
                },
              ],
            },
            {
              name: "new_staffcontrol.organizational_management",
              menus: [
                {
                  icon: "mdi-card-account-details-outline x-small",
                  text: this.$t("Tashlikiy tuzilma (rasmli)"),
                  route: "/personcontrol/test-orgchart/10",
                  color: "#F8A300",
                  visible: true,
                },
                {
                  icon: "mdi-card-account-details-outline x-small",
                  text: this.$t("Tashlikiy tuzilma (rasmsiz)"),
                  route: "/personcontrol/test-orgchart/11",
                  color: "#F8A300",
                  visible: true,
                },
                {
                  icon: "mdi-card-account-details-outline x-small",
                  text: this.$t("Tashlikiy tuzilma (shtat)"),
                  route: "/personcontrol/test-orgchart/12",
                  color: "#F8A300",
                  visible: true,
                },
                {
                  icon: "mdi-card-account-details-outline x-small",
                  text: "new_staffcontrol.staff_index",
                  route: "/personcontrol/staffs/list",
                  color: "#F8A300",
                  visible: this.$store.getters.checkPermission("shtat-korish"),
                },
                {
                  icon: "mdi-card-account-details-outline x-small",
                  text: "new_staffcontrol.staff_placement",
                  route: "/personcontrol/staffs/employeelist",
                  color: "#F8A300",
                  visible: this.$store.getters.checkPermission("shtat-korish"),
                },
                {
                  icon: "mdi-card-account-details-outline x-small",
                  text: "new_staffcontrol.staff_vacancy",
                  route: "/personcontrol/staffs/vacancy",
                  color: "#F8A300",
                  visible: this.$store.getters.checkPermission("shtat-korish"),
                },
                {
                  icon: "mdi-account-plus-outline",
                  text: "new_staffcontrol.critical_index",
                  color: "#F8A300",
                  route: "/personcontrol/staff-criticals/list",
                  visible:
                    this.$store.getters.checkPermission("critical-index"),
                },

                {
                  icon: "mdi-select-group x-small",
                  text: "new_staffcontrol.department_index",
                  route: "/personcontrol/departments/list",
                  color: "#F8A300",
                  visible: !!this.$store.getters.checkPermission(
                    "access-department-index"
                  ),
                },
                {
                  icon: "mdi-group x-small",
                  text: "new_staffcontrol.department_tree",
                  route: "/personcontrol/departments/tree",
                  color: "#F8A300",
                  visible: !!this.$store.getters.checkPermission(
                    "department-index_tree"
                  ),
                },
                {
                  icon: "mdi-file-alert-outline",
                  text: "new_staffcontrol.tariffScale",
                  route: "/personcontrol/tariff-scales/list",
                  color: "#F8A300",
                  visible:
                    this.$store.getters.checkPermission("tariff_scale-index"),
                },
                {
                  icon: "mdi-office-building-outline",
                  text: "new_staffcontrol.access_department",
                  route: "/personcontrol/access-departments/list",
                  color: "#F8A300",
                  visible: this.$store.getters.checkPermission(
                    "access-department-index"
                  ),
                },
                // {
                //   icon: "mdi-folder-table-outline",
                //   text: this.$t("Тарифная шкала"),
                //   route: "/personcontrol/working-on7",
                //   color: "#F8A300",
                //   visible: this.$store.getters.checkPermission(
                //     "access-department-index"
                //   ),
                // },
                {
                  icon: "mdi-cog-outline",
                  text: "new_staffcontrol.other_setting",
                  route: "/personcontrol/other-setting/list",
                  color: "#6C869F",
                  visible: this.$store.getters.checkPermission(
                    "access-department-index"
                  ),
                },
              ],
            },
            {
              name: "Рекрутинг",
              menus: [
                {
                  icon: "mdi-none",
                  text: this.$t("Kritik Xodimlar bilan ishlash"),
                  route: "/personcontrol/employees/critical",
                  color: "#4CAF50",
                  visible: true,
                },
                {
                  icon: "mdi-none",
                  text: this.$t("E`lon qilingan vakansiyalar"),
                  route: "/personcontrol/employees/candidates-index",
                  color: "#4CAF50",
                  visible: true,
                },
                {
                  icon: "mdi-none",
                  text: this.$t("Интеграция с jobs.uzauto.uz"),
                  route: "/personcontrol/working-on4",
                  color: "#4CAF50",
                  visible: true,
                },
                {
                  icon: "mdi-none",
                  text: this.$t("Другие настройки"),
                  route: "/personcontrol/working-on3",
                  color: "#4CAF50",
                  visible: true,
                },
              ],
            },
            {
              name: "СКУД и Табельный учёт",
              menus: [
                {
                  icon: "mdi-none",
                  text: this.$t("Табель"),
                  route: "/personcontrol/table",
                  color: "#4CAF50",
                  visible: true,
                },
              ],
            },
            {
              name: "salary",
              menus: [
                {
                  icon: "mdi-none",
                  text: this.$t("Подбор персонала"),
                  route: "/personcontrol/working-on1",
                  color: "#4CAF50",
                  visible: true,
                },
              ],
            },
            {
              name: "Общие настройки",
              menus: [
                {
                  icon: "mdi-console-line",
                  text: this.$t("Роль соглашения (SAP)"),
                  route: "/personcontrol/saptransaction/list",
                  visible: true,
                },
                {
                  icon: "mdi-home-modern",
                  text: this.$t("Предприятии отрасли"),
                  route: "/personcontrol/jointventure/list",
                  visible: true,
                },
              ],
            },
            {
              name: "Ma'lumotnomalar",
              menus: [
                {
                  icon: "mdi-file-document",
                  text: "new_staffcontrol.leaving_reasons",
                  route: "/personcontrol/leaving-reasons/list",
                  color: "#00B950",
                  visible: !!this.$store.getters.checkPermission(
                    "leaving_reasons-index"
                  ),
                },
                {
                  icon: "mdi-file-hidden",
                  text: "new_staffcontrol.appeal_content",
                  route: "/personcontrol/appeal-content/list",
                  color: "#00B950",
                  visible: !!this.$store.getters.checkPermission(
                    "appeal_content-index"
                  ),
                },
                {
                  icon: "mdi-account-star",
                  text: "new_staffcontrol.family_relative",
                  route: "/personcontrol/family-relative/list",
                  color: "#00B950",
                  visible: !!this.$store.getters.checkPermission(
                    "family-relative-index"
                  ),
                },
                {
                  icon: "mdi-flag-outline",
                  text: "new_staffcontrol.hr_language",
                  route: "/personcontrol/hr-language/list",
                  color: "#00B950",
                  visible:
                    !!this.$store.getters.checkPermission("hr-language-index"),
                },
                {
                  icon: "mdi-bank",
                  text: "new_staffcontrol.hr_party",
                  route: "/personcontrol/hr-party/list",
                  color: "#00B950",
                  visible:
                    !!this.$store.getters.checkPermission("hr-party-index"),
                },
                {
                  icon: "mdi-book-variant",
                  text: "new_staffcontrol.hrstudy_degree",
                  route: "/personcontrol/hr-study-degree/list",
                  color: "#00B950",
                  visible: !!this.$store.getters.checkPermission(
                    "hr-study-degree-index"
                  ),
                },
                {
                  icon: "mdi-book-open-variant",
                  text: "new_staffcontrol.hrstudy_type",
                  route: "/personcontrol/hr-study-type/list",
                  color: "#00B950",
                  visible: !!this.$store.getters.checkPermission(
                    "hr-study-type-index"
                  ),
                },
                {
                  icon: "mdi-book-variant",
                  text: "new_staffcontrol.hr_university",
                  route: "/personcontrol/hr-university/list",
                  color: "#00B950",
                  visible: !!this.$store.getters.checkPermission(
                    "hr-university-index"
                  ),
                },
                {
                  icon: "mdi-lead-pencil",
                  text: "new_staffcontrol.hr_major",
                  route: "/personcontrol/hr-major/list",
                  color: "#00B950",
                  visible:
                    !!this.$store.getters.checkPermission("hr-major-index"),
                },
                {
                  icon: "mdi-star-outline",
                  text: "new_staffcontrol.hrmilitary_rank",
                  route: "/personcontrol/hr-military-rank/list",
                  color: "#00B950",
                  visible: !!this.$store.getters.checkPermission(
                    "hr-military-rank-index"
                  ),
                },
                {
                  icon: "mdi-star",
                  text: "new_staffcontrol.hrstate_awards",
                  route: "/personcontrol/hr-state-awards/list",
                  color: "#00B950",
                  visible: !!this.$store.getters.checkPermission(
                    "hr-state-award-index"
                  ),
                },
                {
                  icon: "mdi-cash-usd",
                  text: this.$t("message.currency"),
                  route: "/personcontrol/currencies/list",
                  visible:
                    !!this.$store.getters.checkPermission("currency-index"),
                },
                {
                  icon: "mdi-currency-usd",
                  text: this.$t("message.currencyHistory"),
                  route: "/personcontrol/currencies/history",
                  visible:
                    !!this.$store.getters.checkPermission("currency-index"),
                },
              ],
            },
          ],
        },
        {
          src: "img/svg/adminControl.svg",
          src2: "img/svg/adminControl.svg",
          text: "admin_panel.navbar_name",
          color: "#9693f6",
          route: "/adminpanel/home",
          groups: [
            {
              name: "admin_panel.working_users",
              menus: [
                {
                  icon: "mdi-account-multiple-outline",
                  text: "admin_panel.user_index",
                  route: "/adminpanel/users/list",
                  color: "green",
                  visible: !!this.$store.getters.checkPermission("user-index"),
                },
                {
                  icon: "mdi-account-tie",
                  text: "admin_panel.diller_users",
                  route: "/adminpanel/users/diller",
                  color: "green",
                  visible: !!this.$store.getters.checkPermission("user-index"),
                },
                {
                  icon: "mdi-account-star",
                  text: "admin_panel.user_template",
                  route: "/adminpanel/user-template/list",
                  color: "green",
                  visible: !!this.$store.getters.checkPermission("user-index"),
                },
              ],
            },
            {
              name: "admin_panel.role_permision",
              menus: [
                {
                  icon: "mdi-account-lock-outline",
                  text: "admin_panel.user_rolepermission",
                  route: "/adminpanel/role-permission/list",
                  color: "#F8A300",
                  color: "green",
                  visible: !!this.$store.getters.checkPermission("user-index"),
                },
                {
                  icon: "mdi-account-key-outline",
                  text: "admin_panel.user_permission",
                  route: "/adminpanel/users/permission",
                  color: "#F8A300",
                  color: "green",
                  visible: !!this.$store.getters.checkPermission("user-index"),
                },
                {
                  icon: "mdi-account-check",
                  text: "admin_panel.role_permission_menu",
                  route: "/adminpanel/users/role-permission",
                  color: "#F8A300",
                  color: "green",
                  visible: !!this.$store.getters.checkPermission("user-index"),
                },
              ],
            },
            {
              name: "admin_panel.others",
              menus: [
                {
                  icon: "mdi-bank",
                  text: "admin_panel.company_index",
                  route: "/adminpanel/companies/list",
                  color: "#4CAF50",
                  visible:
                    !!this.$store.getters.checkPermission("company-index"),
                },
                {
                  icon: "mdi-map-marker-outline",
                  text: "admin_panel.countries",
                  route: "/adminpanel/countries/list",
                  visible:
                    !!this.$store.getters.checkPermission("country-index"),
                },
                {
                  icon: "mdi-map-marker-circle",
                  text: "admin_panel.regions",
                  route: "/adminpanel/regions/list",
                  color: "#4CAF50",
                  visible:
                    !!this.$store.getters.checkPermission("region-index"),
                },
                {
                  icon: "mdi-flag",
                  text: "admin_panel.natioanalities",
                  route: "/adminpanel/nationalities/list",
                  color: "#4CAF50",
                  visible:
                    !!this.$store.getters.checkPermission("nationality-index"),
                },
                {
                  icon: "mdi-google-maps",
                  text: "admin_panel.districts",
                  route: "/adminpanel/districts/list",
                  color: "#4CAF50",
                  visible:
                    !!this.$store.getters.checkPermission("district-index"),
                },
                {
                  icon: "mdi-bell",
                  text: "admin_panel.notifications",
                  route: "/adminpanel/notifications/list",
                  color: "#4CAF50",
                  visible:
                    !!this.$store.getters.checkPermission("notification-index"),
                },
                {
                  icon: "mdi-folder-open",
                  text: "admin_panel.refresh_document",
                  route: "/adminpanel/refresh-document/list",
                  color: "#4CAF50",
                  visible:
                    !!this.$store.getters.checkPermission("refresh-document"),
                },
                {
                  icon: "mdi-account-lock-outline",
                  text: "admin_panel.user_attachedtemplate",
                  route: "/adminpanel/template",
                  color: "#4CAF50",
                  visible: !!this.$store.getters.checkPermission("user-index"),
                },
              ],
            },
          ],
        },
        {
          src: "img/svg/analytics_chart_graph_icon.svg",
          src2: "img/svg/analytics_chart_graph_icon.svg",
          text: "KPI",
          color: "#86b300",
          route: "/kpi-sidebar/home",
          groups: [
            {
              name: "Asosiy menyu",
              menus: [
                {
                  icon: "mdi-finance",
                  text: this.$t("KPI"),
                  route: "/kpi-sidebar/kpi-new",
                  visible: true,
                },
                {
                  icon: "mdi-finance",
                  text: this.$t("KPI-Plan"),
                  route: "/kpi-sidebar/kpi-plan",
                  visible: true,
                },
                {
                  icon: "mdi-finance",
                  text: this.$t("kpi-settings"),
                  route: "/kpi-sidebar/kpi-settings",
                  visible: this.$store.getters.checkPermission("kpi-settings"),
                },
              ],
            },
            {
              name: "Hisobotlar",
              menus: [
                {
                  icon: "mdi-finance",
                  text: this.$t("kpi-report"),
                  route: "/kpi-sidebar/kpi-report",
                  visible:
                    !!this.$store.getters.checkPermission("kpi-comission"),
                },
                {
                  icon: "mdi-finance",
                  text: this.$t("kpi-report2"),
                  route: "/kpi-sidebar/kpi-report2",
                  visible:
                    !!this.$store.getters.checkPermission("kpi-comission"),
                },
                {
                  icon: "mdi-finance",
                  text: this.$t("kpi-report3"),
                  route: "/kpi-sidebar/kpi-report3",
                  visible:
                    !!this.$store.getters.checkPermission("kpi-comission"),
                },
                {
                  icon: "mdi-finance",
                  text: this.$t("kpi-report4"),
                  route: "/kpi-sidebar/kpi-report4",
                  visible:
                    !!this.$store.getters.checkPermission("kpi-comission"),
                },
                {
                  icon: "mdi-finance",
                  text: this.$t("kpi-report5"),
                  route: "/kpi-sidebar/kpi-report5",
                  visible:
                    !!this.$store.getters.checkPermission("kpi-comission"),
                },
              ],
            },
          ],
        },
        {
          src: "img/svg/storehouse_icon.svg",
          src2: "img/svg/storehouse_icon.svg",
          text: "new_inventory.navbar_name",
          color: "#AED581",
          route: "/inventory/home",
          groups: [
            {
              name: "new_inventory.material_content",
              menus: [
                {
                  icon: "mdi-select-inverse",
                  text: "new_inventory.filled_data",
                  route: "/inventory/mobile-inventory",
                  visible: !!this.$store.getters.checkPermission(
                    "mobile_inventory_report"
                  ),
                },
                {
                  icon: "mdi-tablet-dashboard",
                  text: "new_inventory.warehouse_report",
                  route: "/inventory/inventory-status",
                  visible: !!this.$store.getters.checkPermission(
                    "mobile_inventory_report"
                  ),
                },
                {
                  icon: "mdi-tablet-dashboard",
                  text: "new_inventory.adress_report",
                  route: "/inventory/location-report",
                  visible: !!this.$store.getters.checkPermission(
                    "mobile_inventory_report"
                  ),
                },
                {
                  icon: "mdi-paperclip",
                  text: "new_inventory.partnumber_report",
                  route: "/inventory/partnumber-report",
                  visible: !!this.$store.getters.checkPermission(
                    "mobile_inventory_report"
                  ),
                },
              ],
            },
            // {
            //   name: "Asosiy vositalar",
            //   menus: [
            //     {
            //       icon: "mdi-select-inverse",
            //       text: this.$t("Kiritilgan ma'lumotlar"),
            //       route: "/inventory/mobile-inventory",
            //       visible: !!this.$store.getters.checkPermission(
            //         "mobile_inventory_report"
            //       ),
            //     },
            //     {
            //       icon: "mdi-tablet-dashboard",
            //       text: this.$t("Ombor bo'yicha hisobot"),
            //       route: "/inventory/inventory-status",
            //       visible: !!this.$store.getters.checkPermission(
            //         "mobile_inventory_report"
            //       ),
            //     },
            //     {
            //       icon: "mdi-tablet-dashboard",
            //       text: this.$t("Adress bo'yicha hisobot"),
            //       route: "/inventory/location-report",
            //       visible: !!this.$store.getters.checkPermission(
            //         "mobile_inventory_report"
            //       ),
            //     },
            //     {
            //       icon: "mdi-paperclip",
            //       text: this.$t("Part number bo'yicha hisobot"),
            //       route: "/inventory/partnumber-report",
            //       visible: !!this.$store.getters.checkPermission(
            //         "mobile_inventory_report"
            //       ),
            //     },
            //   ],
            // },
            // {
            //   name: "Tayyor Mahsulot",
            //   menus: [
            //     {
            //       icon: "mdi-select-inverse",
            //       text: this.$t("Kiritilgan ma'lumotlar"),
            //       route: "/inventory/mobile-inventory",
            //       visible: !!this.$store.getters.checkPermission(
            //         "mobile_inventory_report"
            //       ),
            //     },
            //     {
            //       icon: "mdi-tablet-dashboard",
            //       text: this.$t("Ombor bo'yicha hisobot"),
            //       route: "/inventory/inventory-status",
            //       visible: !!this.$store.getters.checkPermission(
            //         "mobile_inventory_report"
            //       ),
            //     },
            //     {
            //       icon: "mdi-tablet-dashboard",
            //       text: this.$t("Adress bo'yicha hisobot"),
            //       route: "/inventory/location-report",
            //       visible: !!this.$store.getters.checkPermission(
            //         "mobile_inventory_report"
            //       ),
            //     },
            //     {
            //       icon: "mdi-paperclip",
            //       text: this.$t("Part number bo'yicha hisobot"),
            //       route: "/inventory/partnumber-report",
            //       visible: !!this.$store.getters.checkPermission(
            //         "mobile_inventory_report"
            //       ),
            //     },
            //   ],
            // },
            {
              name: "new_inventory.settings",
              menus: [
                {
                  icon: "mdi-human-child",
                  text: "new_inventory.inventory_users",
                  route: "/inventory/user-inventory",
                  visible: !!this.$store.getters.checkPermission(
                    "mobile_inventory_report"
                  ),
                },
              ],
            },
          ],
        },
        {
          src: "img/svg/certificate_icon.svg",
          src2: "img/svg/certificate_icon.svg",
          text: "regulatory_document.navbar_name",
          color: "#66BB6A",
          is_always_visible: true,
          route: "/regulatory-documents/mainpage",
          groups: [
            {
              name: "regulatory_document.main_page",
              menus: [
                {
                  icon: "mdi-book-multiple",
                  text: "Ichki mehnat tartib qoidalari",
                  route: "/regulatory-documents/rules-of-work",
                  color: "green",
                  visible: true,
                },
                {
                  icon: "mdi-book-multiple",
                  text: "regulatory_document.staff_documents",
                  route: "/regulatory-documents/staffs/table",
                  color: "green",
                  visible: true,
                },
              ],
            },
            {
              name: "regulatory_document.position_instruction",
              menus: [
                {
                  icon: "mdi-book-open",
                  text: "regulatory_document.all_documents",
                  route: "/regulatory-documents/documents/list/lavozim-y/57",
                  color: "green",
                  visible: true,
                },
                {
                  icon: "mdi-bookmark-remove",
                  text: "regulatory_document.rejected_documents",
                  route:
                    "/regulatory-documents/documents/list/lavozim-y-cancel/0",
                  color: "green",
                  visible: true,
                },
              ],
            },
            {
              name: "regulatory_document.professional_guidelines",
              menus: [
                {
                  icon: "mdi-book-open",
                  text: "regulatory_document.all_documents",
                  route: "/regulatory-documents/documents/list/kasbiy-y/59",
                  color: "green",
                  visible: true,
                },
                {
                  icon: "mdi-bookmark-remove",
                  text: "regulatory_document.rejected_documents",
                  route:
                    "/regulatory-documents/documents/list/kasbiy-y-cancel/0",
                  color: "green",
                  visible: true,
                },
              ],
            },
            {
              name: "regulatory_document.regulationon_structure",
              menus: [
                {
                  icon: "mdi-book-open",
                  text: "regulatory_document.all_documents",
                  route: "/regulatory-documents/documents/list/tarkibiy-t/58",
                  color: "green",
                  visible: true,
                },
                {
                  icon: "mdi-bookmark-remove",
                  text: "regulatory_document.rejected_documents",
                  route:
                    "/regulatory-documents/documents/list/tarkibiy-t-cancel/0",
                  color: "green",
                  visible: true,
                },
              ],
            },
            {
              name: "regulatory_document.standards",
              menus: [
                {
                  icon: "mdi-book-open",
                  text: "regulatory_document.all_documents",
                  route: "/regulatory-documents/documents/list/standard/0",
                  color: "green",
                  visible: true,
                  permission: true,
                },
                {
                  icon: "mdi-bookmark-remove",
                  text: "regulatory_document.rejected_documents",
                  route:
                    "/regulatory-documents/documents/list/standard-cancel/0",
                  color: "green",
                  visible: true,
                  permission: true,
                },
              ],
            },
            {
              name: "regulatory_document.process_map",
              menus: [
                {
                  icon: "mdi-book-open",
                  text: "regulatory_document.all_documents",
                  route: "/regulatory-documents/documents/list/karta-p/0",
                  color: "green",
                  visible: true,
                },
                {
                  icon: "mdi-bookmark-remove",
                  text: "regulatory_document.rejected_documents",
                  route:
                    "/regulatory-documents/documents/list/karta-p-cancel/0",
                  color: "green",
                  visible: true,
                },
              ],
            },
            {
              name: "regulatory_document.risks",
              menus: [
                {
                  icon: "mdi-book-open",
                  text: "regulatory_document.all_documents",
                  route: "/regulatory-documents/documents/list/risk/0",
                  color: "green",
                  visible: true,
                },
                {
                  icon: "mdi-bookmark-remove",
                  text: "regulatory_document.rejected_documents",
                  route: "/regulatory-documents/documents/list/risk-cancel/0",
                  color: "green",
                  visible: true,
                },
              ],
            },
            {
              name: "regulatory_document.annulirovan",
              menus: [
                {
                  icon: "mdi-book-open",
                  text: "regulatory_document.cancelled",
                  route: "/regulatory-documents/documents/list/annulirovan/0",
                  color: "green",
                  visible: true,
                },
              ],
            },
          ],
        },
        {
          src: "img/svg/cancel_close.svg",
          src2: "img/svg/cancel_close.svg",
          text: "Linestop",
          color: "#EEFF41",
          route: "/linestopsidebar/linestop-mainpage",
          groups: [
            {
              name: "linestop.working_with_tickets",
              menus: [
                {
                  icon: "mdi-file-plus-outline",
                  text: "linestop.tickets_from_autobots",
                  route: "/linestopsidebar/linestop-autotickets",
                  color: "green",
                  visible: !!this.$store.getters.checkPermission(
                    "linestop-tmpsending"
                  ),
                },
                {
                  icon: "mdi-file-plus-outline",
                  text: "linestop.create_new_ticket",
                  route: "/linestopsidebar/linestop-createpage",
                  color: "green",
                  visible: !!this.$store.getters.checkPermission(
                    "linestop-tmpsending"
                  ),
                },
                {
                  icon: "mdi-file-check-outline",
                  text: "linestop.all_tickets",
                  route: "/linestopsidebar/linestop-alltickets",
                  color: "green",
                  visible: !!this.$store.getters.checkPermission(
                    "linestop-tmpsending"
                  ),
                },
                {
                  icon: "mdi-file-document-outline",
                  text: "linestop.all_open_tickets",
                  route: "/linestopsidebar/linestop-allopentickets",
                  color: "green",
                  visible: !!this.$store.getters.checkPermission(
                    "linestop-tmpsending"
                  ),
                },
              ],
            },
            {
              name: "linestop.reports",
              menus: [
                {
                  icon: "mdi-chart-timeline",
                  text: "linestop.my_stops",
                  route: "/linestopsidebar/linestop-mylinestops",
                  color: "#F8A300",
                  visible: true,
                },
                {
                  icon: "mdi-timetable",
                  text: "linestop.report_line",
                  route: "/linestopsidebar/linestop-reportone",
                  color: "#F8A300",
                  visible: !!this.$store.getters.checkPermission(
                    "linestop-tmpsending"
                  ),
                },
                {
                  icon: "mdi-timetable",
                  text: "linestop.report_reason",
                  route: "/linestopsidebar/linestop-reporttwo",
                  color: "#F8A300",
                  visible: !!this.$store.getters.checkPermission(
                    "linestop-tmpsending"
                  ),
                },
              ],
            },
            {
              name: "linestop.administration",
              menus: [
                {
                  icon: "mdi-file-tree",
                  text: "linestop.line",
                  route: "/linestopsidebar/linestop-lines",
                  color: "#4CAF50",
                  visible: !!this.$store.getters.checkPermission(
                    "linestop-tmpsending"
                  ),
                },
                {
                  icon: "mdi-vector-point",
                  text: "linestop.shops",
                  route: "/linestopsidebar/linestop-shops",
                  color: "#4CAF50",
                  visible: !!this.$store.getters.checkPermission(
                    "linestop-tmpsending"
                  ),
                },
                {
                  icon: "mdi-format-list-bulleted",
                  text: "linestop.reason_stops",
                  route: "/linestopsidebar/linestop-reasons",
                  color: "#4CAF50",
                  visible: !!this.$store.getters.checkPermission(
                    "linestop-tmpsending"
                  ),
                },
                {
                  icon: "mdi-home-assistant",
                  text: "linestop.provider",
                  route: "/linestopsidebar/linestop-providers",
                  color: "#4CAF50",
                  visible: !!this.$store.getters.checkPermission(
                    "linestop-tmpsending"
                  ),
                },
                {
                  icon: "mdi-car-sports",
                  text: "linestop.model",
                  route: "/linestopsidebar/linestop-productmodel",
                  color: "#4CAF50",
                  visible: !!this.$store.getters.checkPermission(
                    "linestop-tmpsending"
                  ),
                },
              ],
            },
          ],
        },
        {
          src: "img/svg/covid19_icon.svg",
          src2: "img/svg/covid19_icon.svg",
          text: "Med punkt",
          color: "#EEFF41",
          route: "/medpunktsidebar/medpunkt-homepage",
          groups: [
            {
              name: "Asosiy oyna",
              menus: [
                {
                  icon: "mdi-engine",
                  text: this.$t("Bo'sh sahifa"),
                  route: "/medpunktsidebar/medpunkt-homepage",
                  color: "green",
                  visible: !!this.$store.getters.checkPermission("med_punkt"),
                },
                {
                  icon: "mdi-engine",
                  text: this.$t("Kod diagnostikasi"),
                  route: "/medpunktsidebar/medpunkt-diagnosis-codes",
                  color: "green",
                  visible: !!this.$store.getters.checkPermission("med_punkt"),
                },
                {
                  icon: "mdi-engine",
                  text: this.$t("MKBX"),
                  route: "/medpunktsidebar/medpunkt-hospital-diagnosis",
                  color: "green",
                  visible: !!this.$store.getters.checkPermission("med_punkt"),
                },
                {
                  icon: "mdi-engine",
                  text: this.$t("Kassalik varaqasi"),
                  route:
                    "/medpunktsidebar/medpunkt-registration-period-illness",
                  color: "green",
                  visible: !!this.$store.getters.checkPermission("med_punkt"),
                },
                {
                  icon: "mdi-engine",
                  text: this.$t("Dorilar"),
                  route: "/medpunktsidebar/medpunkt-medicines",
                  color: "green",
                  visible: !!this.$store.getters.checkPermission("med_punkt"),
                },
                {
                  icon: "mdi-engine",
                  text: this.$t("Qabul"),
                  route: "/medpunktsidebar/medpunkt-registration-patients",
                  color: "green",
                  visible: !!this.$store.getters.checkPermission("med_punkt"),
                },
                {
                  icon: "mdi-engine",
                  text: this.$t("Parxez taomlar"),
                  route: "/medpunktsidebar/medpunkt-diet-foods",
                  color: "green",
                  visible: !!this.$store.getters.checkPermission("med_punkt"),
                },
              ],
            },
          ],
        },

        {
          src: "img/svg/industrial_icon.svg",
          src2: "img/svg/industrial_icon.svg",
          text: "M E",
          color: "#EEFF41",
          route: "/manufacturingsidebar/manufacturing-mainpage",
          groups: [
            {
              name: "Yig'uv texnologiyasi",
              menus: [
                {
                  icon: "mdi-file-plus-outline",
                  text: this.$t("Texnologik jarayonlar tekshiruvlari"),
                  route: "/manufacturingsidebar/",
                  color: "green",
                  visible:
                    !!this.$store.getters.checkPermission("me-index_model"),
                },
                {
                  icon: "mdi-file-check-outline",
                  text: this.$t("QCOS ma’lumotlar"),
                  route: "/manufacturingsidebar/",
                  color: "green",
                  visible:
                    !!this.$store.getters.checkPermission("me-index_model"),
                },
                {
                  icon: "mdi-file-document-outline",
                  text: this.$t("Tex jarayonlar"),
                  route: "/manufacturingsidebar/",
                  color: "green",
                  visible:
                    !!this.$store.getters.checkPermission("me-index_model"),
                },
                {
                  icon: "mdi-file-document-outline",
                  text: this.$t("PPCR xujjatlar"),
                  route: "/manufacturingsidebar/",
                  color: "green",
                  visible:
                    !!this.$store.getters.checkPermission("me-index_model"),
                },
                {
                  icon: "mdi-file-document-outline",
                  text: this.$t("Method Quality Control"),
                  route: "/manufacturingsidebar/method-quality-control",
                  color: "green",
                  visible:
                    !!this.$store.getters.checkPermission("me-index_model"),
                },
                {
                  icon: "mdi-file-document-outline",
                  text: this.$t("Quality Control"),
                  route: "/manufacturingsidebar/quality-control",
                  color: "green",
                  visible:
                    !!this.$store.getters.checkPermission("me-index_model"),
                },
                {
                  icon: "mdi-file-document-outline",
                  text: this.$t("Model"),
                  route: "/manufacturingsidebar/model",
                  color: "green",
                  visible:
                    !!this.$store.getters.checkPermission("me-index_model"),
                },
                {
                  icon: "mdi-file-document-outline",
                  text: this.$t("Status"),
                  route: "/manufacturingsidebar/status",
                  color: "green",
                  visible:
                    !!this.$store.getters.checkPermission("me-index_model"),
                },
              ],
            },
          ],
        },
        {
          src: "img/svg/transport_icon.svg",
          src2: "img/svg/transport_icon.svg",
          text: "Uzlogistic",
          color: "#EEFF41",
          route: "/centrum-sidebar/home",
          groups: [
            {
              name: "Asosiy oyna",
              menus: [
                {
                  icon: "mdi-archive-outline",
                  text: "centrum.upload_files",
                  route: "/centrum-sidebar/import",
                  visible: !!this.$store.getters.checkPermission("centrum"),
                },
                {
                  icon: "mdi-file-document-outline",
                  text: "centrum.cancelled_acts",
                  route: "/documents/list/akt-cancel/0",
                  visible: !!this.$store.getters.checkPermission("centrum"),
                },
                {
                  icon: "mdi-archive-outline",
                  text: "centrum.act_status_report",
                  route: "/centrum-sidebar/attribute-report",

                  visible: !!this.$store.getters.checkPermission("centrum"),
                },
                {
                  icon: "mdi-archive-outline",
                  text: "centrum.delivery_report",
                  route: "/centrum-sidebar/act-report",
                  visible: !!this.$store.getters.checkPermission("centrum"),
                },
                // {
                //   icon: "mdi-archive-outline",
                //   text: this.$t("info"),
                //   route: "/centrum-sidebar/info",
                //   visible: !!this.$store.getters.checkPermission("centrum"),
                // },
                // {
                //   icon: "mdi-archive-outline",
                //   text: this.$t("Shablon"),
                //   route: "/centrum-sidebar/shablon",
                //   visible: !!this.$store.getters.checkPermission("centrum"),
                // },
                {
                  icon: "mdi-archive-outline",
                  text: "centrum.summ_report",
                  route: "/centrum-sidebar/report",
                  visible: !!this.$store.getters.checkPermission("centrum"),
                },
              ],
            },
          ],
        },
        {
          src: "img/svg/forklift-svgrepo-com.svg",
          src2: "img/svg/forklift-svgrepo-com.svg",
          text: "Ta’minot Transporti",
          color: "#EEFF41",
          route: "/supplytransport-sidebar/home",
          groups: [
            {
              name: "Asosiy oyna",
              menus: [
                {
                  icon: "mdi-car-battery",
                  text: this.$t("Ehtiyot qismlari"),
                  route: "/supplytransport-sidebar/supply-spare-parts",
                  visible:
                    !!this.$store.getters.checkPermission("supply-rep_model"),
                },
                {
                  icon: "mdi-car-sports",
                  text: this.$t("Transport vositalari"),
                  route: "/supplytransport-sidebar/transports",
                  visible:
                    !!this.$store.getters.checkPermission("supply-rep_model"),
                },
                {
                  icon: "mdi-account-plus",
                  text: this.$t("Ta’mirlovchi chilangarlar"),
                  route: "/supplytransport-sidebar/repair-plumber",
                  visible:
                    !!this.$store.getters.checkPermission("supply-rep_model"),
                },
                {
                  icon: "mdi-account-box-outline",
                  text: this.$t("Yuklagich haydovchilar"),
                  route: "/supplytransport-sidebar/loader-driver",
                  visible:
                    !!this.$store.getters.checkPermission("supply-rep_model"),
                },
                // {
                //   icon: "mdi-car-wash",
                //   text: this.$t("Nosoz texnikalar ta’mirlash"),
                //   route: "/supplytransport-sidebar/",
                //   visible: true,
                // },
                // {
                //   icon: "mdi-engine-outline",
                //   text: this.$t("Ta’mirlash ehtiyot qismlari"),
                //   route: "/supplytransport-sidebar/",
                //   visible: true,
                // },
                // {
                //   icon: "mdi-wrench",
                //   text: this.$t("Ta’mirdan chiqarish"),
                //   route: "/supplytransport-sidebar/",
                //   visible: true,
                // },
              ],
            },
            {
              name: "Admin",
              menus: [
                {
                  icon: "mdi-bank",
                  text: this.$t("Bo'limlar"),
                  route: "/supplytransport-sidebar/departments",
                  color: "green",
                  visible:
                    !!this.$store.getters.checkPermission("supply-rep_model"),
                },
                {
                  icon: "mdi-file-tree",
                  text: this.$t("Bo'linmalar"),
                  route: "/supplytransport-sidebar/sections",
                  color: "green",
                  visible:
                    !!this.$store.getters.checkPermission("supply-rep_model"),
                },
                {
                  icon: "mdi-car-sports",
                  text: this.$t("Transportlar"),
                  route: "/supplytransport-sidebar/transports-type",
                  color: "green",
                  visible:
                    !!this.$store.getters.checkPermission("supply-rep_model"),
                },
                {
                  icon: "mdi-view-list",
                  text: this.$t("Smenalar"),
                  route: "/supplytransport-sidebar/schedule",
                  color: "green",
                  visible:
                    !!this.$store.getters.checkPermission("supply-rep_model"),
                },
              ],
            },
          ],
        },
        {
          src: "img/svg/integration_magnifier_mobile_icon.svg",
          src2: "img/svg/integration_magnifier_mobile_icon.svg",
          text: "integration.navbar_name",
          color: "#EEFF41",
          route: "/integration-sidebar/home",
          groups: [
            {
              name: "integration.mail",
              menus: [
                {
                  icon: "mdi-archive-outline",
                  text: "integration.send",
                  route: "/integration-sidebar/post-order/list",
                  visible: !!this.$store.getters.checkPermission("pochta"),
                },
                {
                  icon: "mdi-archive-outline",
                  text: "integration.search",
                  route: "/integration-sidebar/post-order/info",
                  visible: !!this.$store.getters.checkPermission("pochta"),
                },
              ],
            },
            {
              name: "integration.tenge_bank",
              menus: [
                {
                  icon: "mdi-archive-outline",
                  text: "integration.tenge_reister",
                  route: "/integration-sidebar/tenge-document-registry",
                  visible: !!this.$store.getters.checkPermission("tenge"),
                },
              ],
            },
            {
              name: "integration.sapname",
              menus: [
                {
                  icon: "mdi-archive-outline",
                  text: "integration.creat_supuser",
                  route: "/integration-sidebar/sap/user-request",
                  visible:
                    !!this.$store.getters.checkRole("superadministrator") ||
                    !!this.$store.getters.checkPermission("sap-menu"),
                },
                {
                  icon: "mdi-archive-outline",
                  text: "integration.detail_serach",
                  route: "/integration-sidebar/sap/search-details",
                  visible:
                    !!this.$store.getters.checkRole("superadministrator") ||
                    !!this.$store.getters.checkPermission("sap-menu"),
                },
                {
                  icon: "mdi-archive-outline",
                  text: "integration.financial_liability",
                  route: "/integration-sidebar/sap/warehouse-search-details",
                  visible:
                    !!this.$store.getters.checkRole("superadministrator") ||
                    !!this.$store.getters.checkPermission("sap-menu"),
                },
                {
                  icon: "mdi-archive-outline",
                  text: "integration.illiqued_detail",
                  route: "/integration-sidebar/sap/nolikvid-details",
                  visible:
                    !!this.$store.getters.checkRole("superadministrator") ||
                    !!this.$store.getters.checkPermission("sap-menu"),
                },
              ],
            },
          ],
        },
        {
          src: "img/svg/helps.svg",
          src2: "img/svg/helps.svg",
          text: "Yordam olish",
          color: "#EEFF41",
          sidebar: false,
          route: "",
          groups: [
            {
              name: "linestop.working_with_tickets",
              menus: [],
            },
          ],
        },
      ],
    };
  },
  mounted() {
    // console.log(this.routes);
    localStorage.setItem("modules", JSON.stringify(this.modules));
  },
};
</script>
<style scoped>
.mainTopPadding {
  padding-top: 0px !important;
}
</style>
