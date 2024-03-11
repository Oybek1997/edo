<template>
  <div>
    <v-app-bar
      v-show="isShown"
      id="edonew-header"
      app
      height="93"
      class="pa-0"
      :prominent="false"
    >
      <router-link to="/" tag="button">
        <div class="left-header1 mb-3" style="width: 180px; text-align: center">
          <!-- <span
            class="text-h5 text-center"
            style="color: #163e72; font-weight: 600;"
            v-text="$t('UzAuto')"
          ></span> -->
          <v-img src="img/gm/logo2.png" height="70" contain></v-img>
        </div>
      </router-link>
      <v-spacer></v-spacer>
      <v-sheet
        class="mb-0 mt-1 center-header"
        max-width="70%"
        style="display: flex; justify-content: center; align-items: center"
      >
        <v-slide-group v-model="model" mandatory show-arrows>
          <template v-slot:next>
            <v-icon color="grey">mdi-chevron-right</v-icon>
          </template>
          <template v-slot:prev>
            <v-icon color="grey">mdi-chevron-left</v-icon>
          </template>
          <v-slide-item
            v-for="(item, index) in navbarItems"
            :key="index"
            v-slot="{ active, toggle }"
            class="pa-2"
          >
            <router-link style="text-decoration: none" :to="item.route">
              <div>
                <v-card
                  outlined
                  color="white"
                  round
                  @click="
                    toggle();
                    selectModule(item);
                  "
                >
                  <span>
                    <v-hover
                      close-delay="10"
                      open-delay="10"
                      style="margin: auto"
                    >
                      <template v-slot="{ hover }">
                        <v-img
                          style="width: 45px"
                          :src="item.active ? item.src : item.src2"
                          :class="[
                            item.active
                              ? 'greyscale-image'
                              : hover
                              ? 'greyscale-image'
                              : 'ungreyscale-image',
                          ]"
                        >
                        </v-img>
                      </template>
                    </v-hover>
                    <p
                      class="center-header_text mt-1"
                      :class="[
                        item.active ? 'greyscale-text' : 'ungreyscale-text',
                      ]"
                    >
                      {{ $t(item.text) }}
                    </p>
                  </span>
                </v-card>
              </div>
            </router-link>
          </v-slide-item>
        </v-slide-group>
      </v-sheet>
      <v-spacer></v-spacer>
      <div class="text-center">
        <v-menu offset-y>
          <template v-slot:activator="{ on }">
            <v-btn
              text
              v-on="on"
              color="Black"
              style="width: 160px; height: 50px"
            >
              <img
                v-if="base64"
                :src="'data:application/jpg;base64,' + base64"
                contain
                class="avatarNavbar"
                style="width: 40px !important; height: 40px !important"
              />
              <strong>{{ user && user.username ? user.username : "" }}</strong>
            </v-btn>
          </template>

          <v-list dense class="headerTitle">
            <v-list-item>
              <router-link
                :to="'/personcontrol/profile/' + employee.id"
                style="text-decoration: none; color: inherit"
              >
                <v-list-item-title>
                  <v-icon>mdi-account-box-outline</v-icon>
                  {{ $t("nabar_top.my_page") }}
                </v-list-item-title>
              </router-link>
            </v-list-item>
            <v-list-item>
              <router-link
                to="/my-setting"
                style="text-decoration: none; color: inherit"
              >
                <v-list-item-title>
                  <v-icon>mdi-account-edit</v-icon
                  >{{ $t("nabar_top.my_settings") }}
                </v-list-item-title>
              </router-link>
            </v-list-item>
            <v-list-item @click="logout">
              <v-list-item-title>
                <v-icon>mdi-export</v-icon>{{ $t("nabar_top.logout") }}
              </v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </div>
    </v-app-bar>
    <template>
      <div :class="isShown ? 'prog_bar_close' : 'prog_bar_open'">
        <v-progress-linear value="100" :color="navBarColor" height="3" />
      </div>
    </template>
  </div>
</template>

<script>
const axios = require("axios").default;
import Cookies from "js-cookie";
import Swal from "sweetalert2";
import { colors } from "vuetify/lib";
export default {
  data() {
    return {
      model: null,
      timeline_count: null,
      year: new Date().getFullYear(),
      navBarColor: "#ff6347",
      isShown: true,
      items: [],
      modules: [],
      base64: "",
      employee: {
        base64: null,
      },
      employee: {},
      staff: null,
    };
  },
  computed: {
    notification() {
      if (
        this.$store.state.notifications.alert &&
        this.$store.state.notifications.alert.length
      ) {
        let alert = "";
        this.$store.state.notifications.alert.map((v) => {
          alert = "| " + v.content + " |" + alert;
        });
        this.alert_news = alert;
      }
      return this.$store.state.notifications;
    },
    navbarItems() {
      return this.modules
        .filter(
          (v) =>
            v.is_always_visible ||
            (!!v.groups && v.groups.some((g) => g.menus.some((m) => m.visible)))
        )
        .map((v) => {
          if (JSON.stringify(v) == localStorage.getItem("selected_module")) {
            v.active = true;
          } else v.active = false;
          return v;
        });
    },
    screenHeight() {
      return window.innerHeight - 170;
    },
    user() {
      return this.$store.getters.getUser();
    },
  },
  methods: {
    selectModule(module) {
      localStorage.setItem("selected_module", JSON.stringify(module));
    },
    getUser() {
      let user = this.$store.getters.getUser();
      this.employee = user.employee;
      this.staff = user.employee.employee_staff[0].staff;
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
    logout() {
      this.$store.dispatch("setUser", null);
      this.$store.dispatch("setPermissions", null);
      this.$store.dispatch("setRole", null);
      this.$store.dispatch("setAccessToken", null);
      window.localStorage.clear();
      Cookies.remove("access_token");
      this.$router.push("/login");
    },
  },
  mounted() {
    setInterval(() => {
      this.modules = JSON.parse(localStorage.getItem("modules"));
    }, 100);
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
  left: 0 !important;
  background: #fff;
  box-shadow: 0 5px 4px rgba(0, 0, 0, 0.2);
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

.v-menu {
  background-color: red !important;
}

.zIndex {
  z-index: 0 !important;
}

#edonew-sidebar {
  top: 90px !important;
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

.greyscale-image {
  cursor: pointer;
  transform: scale(1.1);
  transition: transform 0.3s ease;
}

.ungreyscale-image {
  filter: grayscale(100%);
}

.greyscale-text {
  cursor: pointer;
  /* transform: scale(1.1); */
  transition: transform 0.3s ease;
  font-weight: bold;
}

.ungreyscale-text {
  filter: grayscale(100%);
  font-weight: 100;
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

.toggleClass_open {
  top: 70px;
  position: fixed;
  z-index: 990;
}

.toggleClass_close {
  position: fixed;
  z-index: 990;
  top: -12px;
}

.prog_bar_open {
  z-index: 7;
  width: 100%;
  position: fixed;
  top: 0px;
}

.prog_bar_close {
  z-index: 7;
  width: 100%;
  position: fixed;
  top: 90px;
}

.mainTopPadding {
  padding-top: 0px !important;
}

.tab-btn {
  white-space: nowrap;
  /* This prevents text from wrapping */
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

.avatarNavbar {
  border-radius: 90%;
}

::-webkit-scrollbar {
  width: 5px;
}

#navbar {
  background: #f5fafa;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
  background: rgb(50, 133, 189);
}

::-webkit-scrollbar-thumb:hover {
  background: #555;
}

.nav-menu {
  line-height: 1.4em;
  overflow: hidden;
  text-decoration: inherit;
  -o-text-overflow: ellipsis;
  text-overflow: ellipsis;
  white-space: nowrap;
  font-family: Roboto, Trebuchet MS, Helvetica, sans-serif;
  font-size: 12px;
  text-align: left;
  margin-left: -20px;
  color: black !important;
}

.tab-icon {
  font-size: 18px !important;
  color: black !important;
}

.v-menu__content {
  /* margin-left: -30px; */
  margin-top: 1px;
}

.text-truncate {
  color: #fff !important;
  font-size: 18px;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
  text-align: center;
}

.list-group .list-icons i {
  color: #00b950;
  font-size: 16px;
}

.list-group .list-text div {
  color: #6c869f;
  font-size: 13px;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}

.list-group .list-text span {
  color: #333;
  font-size: 14px;
  font-weight: 600;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}

.v-navigation-drawer__border {
  border: none !important;
}

.v-navigation-drawer >>> .v-navigation-drawer__border {
  display: none;
}

.headerTitle {
  width: 100%;
  color: #000;
  font-size: 18px;
  line-height: 1.4;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
</style>
