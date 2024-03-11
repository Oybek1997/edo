<template>
  <div style="position: relative">
    <div class="ma-0" id="edoSidebar">
      <div style="height: 95%; overflow-y: auto; overflow-x: hidden">
        <v-card
          class="mt-0"
          elevation="0"
          :style="{
            borderRadius: '10px',
            border: '1px solid #dce5ef',
            margin: '0px 0px 15px 0px',
          }"
          v-for="(item, index) in filteredGroups"
          :key="index"
        >
          <v-list class="px-1 list-group">
            <v-list-item style="min-height: 40px">
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title>
                  <v-icon left @click="toggleVisibility(item)">{{
                    item.visible ? "mdi-chevron-up" : "mdi-chevron-down"
                  }}</v-icon>
                  <span>{{ item.name }}</span>
                </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list>
          <v-divider color="#DCE5EF"></v-divider>
          <v-list class="py-0 list-group list-group_child" flat>
            <v-list-item-group v-model="item.model" color="indigo">
              <v-list-item
                v-for="(menu, i) in item.menus.filter((m) => m.visible)"
                :key="i"
                :to="menu.route"
                color="blue"
                style="min-height: 38px"
                :class="{ 'no-border': i === item.menus.length - 1 }"
                active-class="active-link"
              >
                <v-list-item-icon class="px-2 mr-2 my-auto list-icons">
                  <v-icon :color="menu.color">{{ menu.icon }}</v-icon>
                </v-list-item-icon>
                <v-list-item-content class="py-0 list-text">
                  <v-list-item-title>{{ menu.text }}</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list-item-group>
          </v-list>
        </v-card>
      </div>
    </div>
    <div id="edoContent">
      <router-view></router-view>
    </div>
  </div>
</template>
<script>
// const axios = require("axios").default;
export default {
  data() {
    return {
      model: null,
      selected_module: {},
    };
  },
  computed: {
    filteredGroups() {
      if (!this.selected_module.groups) return [];
      return this.selected_module.groups.filter((g) =>
        g.menus.some((m) => m.visible)
      );
    },
  },
  methods: {
    toggleVisibility(item) {
      item.menus.forEach((menu) => {
        console.log(menu)
      });
    },
  },
  mounted() {
    setInterval(() => {
      this.selected_module = JSON.parse(
        localStorage.getItem("selected_module")
      );
    }, 100);
  },
};
</script>
<style scoped>
#edoSidebar {
  height: 93vh;
  top: 93px;
  max-height: calc(100% - 0px);
  transform: translateX(0%);
  width: 20%;
  background-color: rgb(241, 245, 248);
  position: fixed;
  padding: 20px;
}
::-webkit-scrollbar {
  width: 5px;
  display: none;
}

.no-border .v-list-item {
  border-bottom: none !important;
}

#edoContent {
  width: 80%;
  position: fixed;
  max-height: calc(80%);
  max-height: calc(85% - 0px);
  margin-left: 20%;
  background-color: rgb(241, 245, 248);
  padding: 0px 20px 20px 0px;
  margin-top: 20px;
  margin-bottom: 30px;
  overflow-y: auto;
  overflow-x: hidden;
}

.left-aside .v-list-item--link {
  border-bottom: 1px solid #e5e5e9;
}

.left-aside .v-list-item--link:hover {
  background: #f0f0f5;
}

.left-aside .v-list-group {
  border-bottom: 1px solid #e5e5e9;
  max-width: 90%;
}

#edonew-header {
  /* height: 90px !important; */
  left: 0 !important;
  background: #fff;
  /* box-shadow: none !important; */
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
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
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

.left-footer {
  width: 200px;
  padding: 6px;
  text-align: center;
}

.left-footer .left-footer_button i {
  color: #0298e9;
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
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
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
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
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
  white-space: nowrap; /* This prevents text from wrapping */
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
/* width */
::-webkit-scrollbar {
  width: 5px;
}
#navbar {
  background: #f5fafa;
}
/* Track */
::-webkit-scrollbar-track {
  background: #f1f1f1;
}
/* Handle */
::-webkit-scrollbar-thumb {
  background: rgb(50, 133, 189);
}

/* Handle on hover */
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
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
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
  margin-left: -30px;
  margin-top: 1px;
  /* background-color: rgba(163, 24, 24, 0.0); */
}
.text-truncate {
  color: #fff !important;
  font-size: 18px;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
  text-align: center;
}
.v-item--active,
.v-list-item--active {
  color: red;
}
.list-group .v-list-item--active {
  color: red;
}

.list-group .list-icons i {
  color: #00b950;
  font-size: 20px;
}

.list-group .list-text div {
  color: #6c869f;
  font-size: 13px;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.list-group .list-text span {
  color: #000;
  font-size: 14px;
  font-weight: 600;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.v-navigation-drawer__border {
  border: none !important;
}
.v-navigation-drawer > .v-navigation-drawer__border {
  display: none;
}
.active-link {
  background-color: #e0e0e0;
  color: #000;
}
</style>
