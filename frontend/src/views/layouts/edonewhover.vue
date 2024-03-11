<template>
  <div>
    <v-card class="mt-10">
      <v-card-title>{{ "Title" }}</v-card-title>
      <v-card-text>
        <template>
          <div>
            <div v-for="(s, i) in 6" :key="i" class="dropdown">
              <button class="dropbtn">Profile</button>
              <div class="dropdown-content">
                <v-list>
                  <v-list-item v-for="(item, index) in itemsa" :key="index">
                    <v-list-item-title>{{ item.title }}</v-list-item-title>
                  </v-list-item>
                </v-list>
              </div>
            </div>
          </div>
        </template>
      </v-card-text>
    </v-card>
    <!-- --------------------- -->
    
    <v-card>
      <v-tabs dark background-color="teal darken-3" show-arrows>
        <v-tabs-slider color="teal lighten-3"></v-tabs-slider>
        <div v-for="i in 100" :key="i" :href="'#tab-' + i"  class="dropdown">
              <button class="dropbtn">Profile</button>
              <div class="dropdown-content">
                <v-list>
                  <v-list-item v-for="(item, index) in itemsa" :key="index">
                    <v-list-item-title>{{ item.title }}</v-list-item-title>
                  </v-list-item>
                </v-list>
              </div>
            </div>
        <!-- <div v-for="i in 100" :key="i" :href="'#tab-' + i" class="dropdown">
          {{ 'sasas' }}
        </div> -->
      </v-tabs>
    </v-card>
    <!-- --------------------- -->
  </div>
</template>

<script>
const axios = require("axios").default;
import Swal from "sweetalert2";
export default {
  data() {
    return {
      progressValue: 0,
      myApp: false,
      model: null,
      year: new Date().getFullYear(),
      loading: false,
      MYsTORE: [],
      itemsKPI: [],
      itemsHisobot: [],
      itemsSingIn: [],
      sideBarItem: [],
      drawer: true,
      tabVisable: false,
      tabsItem: [],
      allDocItems: [],
      navBarColor: "#ff6347",
      callDialog: false,
      isShown: true,
      hasNewClass: false,
      sideBarVisabled: true,
      items: [],
      itemsa: [
        { title: "Click Me" },
        { title: "Click Me" },
        { title: "Click Me" },
        { title: "Click Me 2" },
      ],
      create_document: [
        {
          id: 0,
          icon: "mdi-folder-open",
          name_uz_latin: "Erkin shablon",
          name_uz_cyril: "Эркин шаблон",
          name_ru: "Произвольный шаблон",
          route: "/document/create/1",
          count: "",
          visible: false,
        },
      ],
      base64: "",
      employee: {
        base64: null,
      },
      employee: {},
      staff: null,
      roles: [],
      //
      admins: [
        ["Management", "mdi-account-multiple-outline"],
        ["Settings", "mdi-cog-outline"],
      ],
      cruds: [
        ["Create", "mdi-plus-outline"],
        ["Read", "mdi-file-outline"],
        ["Update", "mdi-update"],
        ["Delete", "mdi-delete"],
      ],
      //
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    user() {
      return this.$store.getters.getUser();
    },
    links() {
      return [
        {
          icon: "mdi-briefcase-account-outline",
          text: this.$t("EdoNew"),
          route: "/edonew",
          visible: true,
        },
      ];
    },
  },
  methods: {
    mouseover() {
      this.myApp = true;
    },
    mouseleave() {
      this.tabVisable = false;
    },
    toggleShow() {
      this.isShown = !this.isShown;
      this.hasNewClass = !this.hasNewClass;
    },
    handleItemClick(item) {
      if (item.commands && typeof this[item.commands] === "function") {
        this[item.commands]();
      }
    },
    getAllDoc() {
      this.tabsItem = this.allDocItems;
      this.tabVisable = true;
    },
    getUser() {
      let user = this.$store.getters.getUser();
      this.employee = user.employee;
      this.staff = user.employee.employee_staff[0].staff;
      this.roles = this.$store.getters.getRoles();
    },
    getUserInfo() {
      const id = this.employee.id;
      axios
        .get(
          this.$store.state.backend_url + "api/employees/show-employee/" + id
        )
        .then((res) => {
          this.employee = res.data;
          // console.log("qwe", this.employee);
          this.nationality = res.data.nationality;
          this.company = res.data.company;
          this.employee_addresses = res.data.employee_addresses;
          this.employee_official_document = res.data.employee_official_document;
          this.employee_phones = res.data.employee_phones;
          this.staff = res.data.staff[0];
          this.tariff_scale = res.data.employee_staff[0].tariff_scale;
          this.roles =
            res.data.user && res.data.user.roles ? res.data.user.roles : [];
          // console.log("roles", this.roles);
          this.employee_relatives = res.data.employee_relative;
          this.employee_work_histories = res.data.employee_work_histories;
          this.employee_education_histories =
            res.data.employee_education_histories;
          this.employee_id = res.data.user ? res.data.user.employee_id : 0;
          this.employee_tabel = res.data.tabel;
          console.log("Tabel", this.employee_tabel);
          this.staffHistory(this.employee_id);
        })
        .catch((e) => {
          console.error(e);
        });
    },
    getAvatar() {
      const id = this.employee.id;
      axios
        .get(this.$store.state.backend_url + "api/employees/get-avatar/" + id)
        .then((response) => {
          this.employee.base64 = response.data;
          this.base64 = response.data;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getCreateDocument() {
      this.create_document = [
        {
          id: 0,
          icon: "mdi-folder-open",
          text: "Erkin shablon",
          name_uz_latin: "Erkin shablon",
          name_uz_cyril: "Эркин шаблон",
          name_ru: "Произвольный шаблон",
          route: "/document/create/1",
          count: "",
          visible: false,
        },
      ];

      // console.log(this.$store);
      axios
        .get(this.$store.state.backend_url + "api/document-types")
        .then((response) => {
          response.data.forEach((element) => {
            let i = 0;
            element.permissions.forEach((permission) => {
              if (this.$store.getters.checkPermission(permission)) {
                i++;
              }
            });
            this.create_document.push({
              id: element.id,
              icon: "mdi-folder-open",
              name_uz_latin: element.name_uz_latin,
              text: element.name_uz_latin,
              name_uz_cyril: element.name_uz_cyril,
              name_ru: element.name_ru,
              route: "/document/template/" + element.id,
              count: element.count,
              visible: i == 0 ? false : true,
            });
          });
          this.tabsItem = this.create_document;
          this.tabVisable = true;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    redirectSetting() {
      this.$router.push("/my-setting");
    },
    getKPI() {
      this.sideBarItem = this.itemsKPI;
    },
    getHisobot() {
      this.sideBarItem = this.itemsHisobot;
    },
    getsingIn() {
      this.sideBarItem = this.itemsSingIn;
    },
    redirectSettingAccount() {
      this.$router.push("/my-setting/acount");
    },
    sideBodyStatus(item) {
      this.sideBarVisabled = item;
      // this.getCreateDocument();
    },
    lodingBar() {
      this.sideBarVisabled = item;
    },
    updateProgressa(color) {
      this.navBarColor = color;
      this.progressValue = -10;
      this.updateProgress();
    },
    updateProgress() {
      if (this.progressValue < 100) {
        setTimeout(() => {
          this.progressValue += 1;
          this.updateProgress();
        }, 30);
      }
    },
    fullImets() {
      this.allDocItems = [
        {
          icon: "mdi-checkbox-marked-circle-outline",
          text: this.$t("Yangi hujjat"),
          // route: "/documents/list/lavozim-y/57",
          commands: "getCreateDocument",
          visible: true,
        },
        {
          icon: "mdi-checkbox-marked-circle-outline",
          text: this.$t("Tanlanganlar"),
          route: "/documents/list/lavozim-y/57",
          visible: true,
        },
        {
          icon: "mdi-checkbox-marked-circle-outline",
          text: this.$t("Ko'p foydalanilgan"),
          route: "/documents/list/lavozim-y/57",
          visible: true,
        },
        {
          icon: "mdi-checkbox-marked-circle-outline",
          text: this.$t("Kiruvchi hujjat"),
          route: "/documents/list/lavozim-y/57",
          visible: true,
        },
        {
          icon: "mdi-checkbox-marked-circle-outline",
          text: this.$t("chiquvchi hujjat"),
          route: "/documents/list/lavozim-y/57",
          visible: true,
        },
        {
          icon: "mdi-checkbox-marked-circle-outline",
          text: this.$t("bekorqilngan hujjat"),
          route: "/documents/list/lavozim-y/57",
          visible: true,
        },
        {
          icon: "mdi-checkbox-marked-circle-outline",
          text: this.$t("barcha hujjat"),
          route: "/documents/list/all/0",
          visible: true,
        },
      ];
      this.itemsSingIn = [
        {
          icon: "mdi-checkbox-marked-circle-outline",
          text: this.$t("lavozim_yuriqnomasi"),
          route: "/documents/list/lavozim-y/57",
          visible: false,
        },
        {
          icon: "mdi-close-box-outline",
          text: this.$t("lavozim_yuriqnomasi"),
          route: "/documents/list/lavozim-y-cancel/0",
          visible: false,
        },
        {
          icon: "mdi-checkbox-marked-circle-outline",
          text: this.$t("kasbiy_yuriqnomasi"),
          route: "/documents/list/kasbiy-y/59",
          visible: false,
        },
        {
          icon: "mdi-close-box-outline",
          text: this.$t("kasbiy_yuriqnomasi"),
          route: "/documents/list/kasbiy-y-cancel/0",
          visible: false,
        },
        {
          icon: "mdi-checkbox-marked-circle-outline",
          text: this.$t("tarkibiy_tuzilma"),
          route: "/documents/list/tarkibiy-t/58",
          visible: false,
        },
        {
          icon: "mdi-close-box-outline",
          text: this.$t("tarkibiy_tuzilma"),
          route: "/documents/list/tarkibiy-t-cancel/0",
          visible: false,
        },
        {
          icon: "mdi-checkbox-marked-circle-outline",
          text: this.$t("standard"),
          route: "/documents/list/standard/0",
          visible: false,
        },
        {
          icon: "mdi-close-box-outline",
          text: this.$t("standard"),
          route: "/documents/list/standard-cancel/0",
          visible: false,
        },
        {
          icon: "mdi-checkbox-marked-circle-outline",
          text: this.$t("process_card"),
          route: "/documents/list/karta-p/0",
          visible: false,
        },
        {
          icon: "mdi-close-box-outline",
          text: this.$t("process_card"),
          route: "/documents/list/karta-p-cancel/0",
          visible: false,
        },
      ];
      this.itemsHisobot = [
        // {
        //   icon: "mdi-folder-open",
        //   text: this.$t("message.report"),
        //   route: "/reports/template",
        //   visible: true
        // },
        {
          icon: "mdi-folder-open",
          text: this.$t("message.report"),
          route: "/documents/report",
          visible: false,
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("message.znz"),
          route: "/documents/report/znz",
          visible: false,
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("message.lsp_report"),
          route: "/documents/report/lsp",
          visible: false,
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("department.index"),
          route: "/reports/department/0",
          visible: false,
        },
        // {
        //   icon: "mdi-folder-open",
        //   text: this.$t("department.okd"),
        //   route: "/reports/department-okd/0",
        //   visible: this.$store.getters.checkPermission("okd-report-index"),
        // },

        {
          icon: "mdi-folder-open",
          text: this.$t("department.okd"),
          route: "/reports/okd-report-full",
          visible: this.$store.getters.checkPermission("okd-report-index"),
        },
        {
          icon: "mdi-folder-open",
          text: "OKD (T)",
          route: "/reports/okd-report-full-toshkent",
          visible: this.$store.getters.checkPermission("okd-report-index"),
        },
        // {
        //   icon: "mdi-folder-open", 96525
        //   text: this.$t("department.okd_asaka"),
        //   route: "/reports/okd-report",
        //   visible: this.$store.getters.checkPermission("okd-report-index"),
        // },
        {
          icon: "mdi-folder-open",
          text: this.$t("department.myokd"),
          route: "/reports/document-report-employee",
          visible: true,
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("my_documents"),
          route: "/reports/document-attribute-report/my",
          visible: this.$store.getters.checkPermission("my_documents_report"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("my_documents_2"),
          route: "/reports/document-attribute-report/my-inbox",
          visible: this.$store.getters.checkPermission("my_documents_report"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("report.my_documents"),
          route: "/reports/my-document-report",
          visible: true,
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("all_documents"),
          route: "/reports/document-attribute-report/all",
          visible: this.$store.getters.checkRole("superadministrator"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("Template reports"),
          route: "/reports/document-attribute-report/selected",
          visible: this.$store.getters.checkPermission("attribute-report"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("control_punkt.report"),
          route: "/reports/control-punkt-report",
          visible: this.$store.getters.checkPermission("control_punkt_report"),
        },
        // {
        //   icon: "mdi-folder-open",
        //   text: this.$t("Avia report"),
        //   route: "/reports/avia-report",
        //   visible: this.$store.getters.checkPermission("attribute-report"),
        // },
      ];
      this.itemsKPI = [
        {
          icon: "mdi-finance",
          text: this.$t("KPI"),
          route: "/kpi",
          visible: true,
        },
        {
          icon: "mdi-finance",
          text: this.$t("KPI-report"),
          route: "/kpi-report",
          visible: this.$store.getters.checkPermission("kpi-comission"),
        },
        {
          icon: "mdi-finance",
          text: this.$t("KPI-report2"),
          route: "/kpi-report2",
          visible: this.$store.getters.checkPermission("kpi-comission"),
        },
        {
          icon: "mdi-finance",
          text: this.$t("KPI-report3"),
          route: "/kpi-report3",
          visible: this.$store.getters.checkPermission("kpi-comission"),
        },
        {
          icon: "mdi-finance",
          text: this.$t("KPI-report4"),
          route: "/kpi-report4",
          visible: this.$store.getters.checkPermission("kpi-comission"),
        },
        {
          icon: "mdi-finance",
          text: this.$t("KPI-report5"),
          route: "/kpi-report5",
          visible: this.$store.getters.checkPermission("kpi-comission"),
        },
      ];
      this.items = [
        {
          src: "img/svg/1add_blue_create.svg",
          src2: "img/svg/1add_blue_create.svg",
          text: "Hujjat yaratish",
          color: "#3949AB",
          commands: "getCreateDocument",
          sidebar: false,
          visibled: false,
        },
        {
          src: "img/svg/1add_blue_create.svg",
          src2: "img/svg/1add_blue_create.svg",
          text: "Hujjat yaratish",
          color: "#3949AB",
          commands: "getCreateDocument",
          sidebar: false,
          visibled: true,
        },
        {
          src: "img/svg/folder_open2.svg",
          src2: "img/svg/folder_open2.svg",
          text: "HUJJATLAR",
          color: "#99ff33",
          commands: "getAllDoc",
          sidebar: false,
          visibled: true,
        },
        {
          src: "img/svg/chart_01.svg",
          src2: "img/svg/chart_03.svg",
          text: "KPI",
          color: "#86b300",
          commands: "getKPI",
          sidebar: false,
          visibled: true,
        },
        {
          src: "img/svg/signeddoc2.svg",
          src2: "img/svg/signeddoc.svg",
          text: "Imzolangan hujjatlar",
          color: "#ff4dff",
          commands: "getsingIn",
          sidebar: false,
          visibled: true,
        },
        {
          src: "img/svg/report.svg",
          src2: "img/svg/report.svg",
          text: "Hisobotlar",
          color: "#00cccc",
          commands: "getHisobot",
          sidebar: true,
          visibled: true,
        },
        {
          src: "img/svg/docOborot2.svg",
          src2: "img/svg/docOborot2.svg",
          text: "Hujjatlar aylanmasi",
          color: "#00ACC1",
          sidebar: false,
          visibled: true,
        },
        {
          src: "img/svg/personControl.svg",
          src2: "img/svg/personControl.svg",
          text: "Xodimlar boshqaruvi",
          color: "#3333cc",
          sidebar: false,
          visibled: true,
        },
        {
          src: "img/svg/adminControl.svg",
          src2: "img/svg/adminControl.svg",
          text: "Admin Panel",
          color: "#9693f6",
          sidebar: false,
          visibled: true,
        },
        {
          src: "img/svg/storehouse_icon.svg",
          src2: "img/svg/storehouse_icon.svg",
          text: "Inventarizatsiya",
          color: "#AED581",
          sidebar: false,
          visibled: true,
        },
        {
          src: "img/svg/docShablon.svg",
          src2: "img/svg/docShablon.svg",
          text: "Blanka shablonlari",
          color: "#9c99ec",
          sidebar: false,
          visibled: true,
        },
        {
          src: "img/svg/archive.svg",
          src2: "img/svg/archive.svg",
          text: "Arxiv",
          color: "#FDD835",
          sidebar: false,
          visibled: true,
        },
        {
          src: "img/svg/sign_icon.svg",
          src2: "img/svg/sign_icon.svg",
          text: "Tasdiqlangan hujjatlar",
          color: "#66BB6A",
          sidebar: false,
          visibled: true,
          commands: "redirectSettingAccount",
        },
        {
          src: "img/svg/setting_settings_icon.svg",
          src2: "img/svg/setting_settings_icon.svg",
          text: "Sozlamalar",
          color: "#11BB6A",
          commands: "redirectSetting",
          sidebar: false,
          visibled: true,
        },
        {
          src: "img/svg/helps.svg",
          src2: "img/svg/helps.svg",
          text: "Yordam",
          color: "#EEFF41",
          sidebar: false,
          visibled: true,
        },
      ];
    },
  },
  mounted() {
    this.updateProgress();
    this.sideBodyStatus();
    this.fullImets();
    this.getUser();
    this.getAvatar();
  },
};
</script>


<style scoped>
.left-aside .v-list-item--link {
  border-bottom: 1px solid #e5e5e9;
}

.left-aside .v-list-item--link:hover {
  background: #f0f0f5;
}

.left-aside .v-list-group {
  border-bottom: 1px solid #e5e5e9;
}

#edonew-header {
  height: 90px !important;
  left: 0 !important;
  background: #fff;
  box-shadow: none !important;
}

.left-header {
  -webkit-box-pack: center;
  -ms-flex-pack: center;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  justify-content: center;
  width: 200px;
}

.center-header {
  left: 0 !important;
  height: 80px;
}
.v-slide-group__wrapper {
  height: 100px;
}
.center-header_text {
  text-align: center;
  width: auto;
  line-height: 1.4em;
  overflow: hidden;
  text-decoration: inherit;
  -o-text-overflow: ellipsis;
  text-overflow: ellipsis;
  white-space: nowrap;
  cursor: pointer;
  font-family: Roboto, Trebuchet MS, Helvetica, sans-serif;
  font-size: 12px;
  color: #474747 !important;
}
.right-header {
  position: absolute;
  width: 5%;
  right: 0 !important;
}

#edonew-sidebar {
  top: 101px !important;
}

.footer {
  background: #e7e5dd;
  bottom: 0;
  height: 38px;
  left: 0;
  position: fixed;
  width: 100%;
  z-index: 1000;
  -webkit-box-pack: justify;
  -ms-flex-pack: justify;
  justify-content: space-between;
  -webkit-box-align: start;
  -ms-flex-align: start;
  align-items: flex-start;
}

.left-footer {
  width: 200px;
  padding: 6px;
  text-align: center;
}

.left-footer .left-footer_button i {
  color: #788d98;
  box-sizing: border-box;
  font-size: 24px;
  display: inline-block;
  height: 24px;
  line-height: 1;
  position: relative;
  width: 24px;
}

.right-footer {
  width: 484px;
  background-color: #e7e5dd;
  padding: 0 26px;
  position: relative;
  white-space: nowrap;
  z-index: 999;
  height: 38px;
  display: flex;
  align-items: center;
}

.right-footer span {
  display: inline-block;
  padding: 0 12px;
  text-align: center;
}

.greyscale-image {
  cursor: pointer;
  transform: scale(1.1);
  transition: transform 0.3s ease;
}

.ungreyscale-image {
  filter: grayscale(100%);
}
.theme--light.v-card {
  background-color: #ffffff;
  color: rgba(255, 255, 255, 0.87);
}

.v-toolbar__content {
  padding: 0 !important;
}
.center-footer {
  line-height: 1.4em;
  overflow: hidden;
  text-decoration: inherit;
  -o-text-overflow: ellipsis;
  text-overflow: ellipsis;
  white-space: nowrap;
  font-family: Roboto, Trebuchet MS, Helvetica, sans-serif;
  font-size: 14px;
  text-align: center;
  margin-top: 10px;
}
.side-bar-textstyle {
  line-height: 1.4em;
  overflow: hidden;
  text-decoration: inherit;
  -o-text-overflow: ellipsis;
  text-overflow: ellipsis;
  white-space: nowrap;
  font-family: Roboto, Trebuchet MS, Helvetica, sans-serif;
  font-size: 12px;
  text-align: left;
  margin: 0;
  padding: 0;
}
.v-list {
  display: block;
  padding: 0px 0;
  position: static;
  -webkit-transition: -webkit-box-shadow 280ms cubic-bezier(0.4, 0, 0.2, 1);
  transition: -webkit-box-shadow 280ms cubic-bezier(0.4, 0, 0.2, 1);
  transition: box-shadow 280ms cubic-bezier(0.4, 0, 0.2, 1);
  transition: box-shadow 280ms cubic-bezier(0.4, 0, 0.2, 1),
    -webkit-box-shadow 280ms cubic-bezier(0.4, 0, 0.2, 1);
}
.toggleClass {
  top: 0px;
  right: 0px;
  position: absolute;
  z-index: 990;
}
.mainTopPadding {
  padding-top: 0px !important;
}

.avatar-image {
  cursor: pointer;
  transform: scale(1.1);
  transition: transform 0.3s ease;
  box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
}

.unavatar-image {
  filter: none;
}
</style>

<!-- sardor -->
<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}

.dropbtn {
  background-color: rgb(54, 14, 231);
  color: white;
  padding: 16px;
  font-size: 16px;
  border: none;
  /* width: 150px; */
}

.dropdown {
  position: relative;
  display: inline-block;
}

.dropdown-content {
  display: none;
  position: absolute;
  background-color: #f1f1f1;
  min-width: auto;
  box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
  z-index: 1;
}

.dropdown-content a {
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
}

.dropdown-content a:hover {
  background-color: #ddd;
}

.dropdown:hover .dropdown-content {
  display: block;
}

.dropdown:hover .dropbtn {
  background-color: #4d1c33;
}
</style>
