<template>
  <div class="" style="position: relative">
    <!-- <v-row class="ma-0 pa-0" style="position: relative;"> -->
    <div class="ma-0" id="edoSidebar">
      <!-- <v-col cols="2" class="ma-0" id="edoSidebar"> -->
      <div style="height: 100%; overflow-y: auto; overflow-x: hidden">


        <v-card
          class="mt-0"
          elevation="0"
          style="border-radius: 10px; border: 1px solid #dce5ef"
        >
          <v-list
            class="py-0 px-3 list-group"
            style="background-color: #fbfcfe"
          >
            <v-list-item style="min-height: 40px">
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title>
                  <span>Работы с тикетами</span>
                </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list>
          <v-divider color="#DCE5EF" class></v-divider>
          <v-list class="py-2 px-3 list-group">
            <v-list-item
              style="min-height: 38px; border-bottom: 1px solid #dce5efcc"
            >
              <v-list-item-icon class="mr-2 my-auto list-icons">
                <v-icon class="mdi-24px" color="#FF3434">mdi-file-plus-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-content class="py-0 list-text">
                <router-link
                  to="/linestopsidebar/linestop-tickets"
                  style="text-decoration: none"
                >
                  <v-list-item-title>Тикеты с автоботов</v-list-item-title>
                </router-link>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              style="min-height: 38px; border-bottom: 1px solid #dce5efcc"
            >
              <v-list-item-icon class="mr-2 my-auto list-icons">
                <v-icon class="mdi-24px">mdi-file-plus-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-content class="py-0 list-text">
                <router-link
                  to="/linestopsidebar/linestop-createpage"
                  style="text-decoration: none"
                >
                  <v-list-item-title>Создать новый тикет</v-list-item-title>
                </router-link>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              style="min-height: 38px; border-bottom: 1px solid #dce5efcc"
            >
              <v-list-item-icon class="mr-2 my-auto list-icons">
                <v-icon class="mdi-24px">mdi-file-check-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title>Все тикеты</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item style="min-height: 38px">
              <v-list-item-icon class="mr-2 my-auto list-icons">
                <v-icon class="mdi-24px">mdi-file-document-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title>Все открытые тикеты</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-card>

        <v-card
          class="mt-5"
          elevation="0"
          style="
            border-radius: 10px;
            margin-top: 20px;
            border: 1px solid #dce5ef;
          "
        >
          <v-list
            class="py-0 px-3 list-group"
            style="background-color: #fbfcfe"
          >
            <v-list-item style="min-height: 40px">
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title>
                  <span>Отчеты</span>
                </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list>
          <v-divider color="#DCE5EF" class></v-divider>
          <v-list class="py-2 px-3 list-group">
            <v-list-item
              style="min-height: 38px; border-bottom: 1px solid #dce5efcc"
            >
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title>Отчет 1</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              style="min-height: 38px; border-bottom: 1px solid #dce5efcc"
            >
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title>Отчет 2</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              style="min-height: 38px; border-bottom: 1px solid #dce5efcc"
            >
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title>Отчет 3</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-card>

        <v-card
          class="mt-5"
          elevation="0"
          style="
            border-radius: 10px;
            margin-top: 20px;
            border: 1px solid #dce5ef;
            margin-bottom: 90px;
          "
        >
          <v-list
            class="py-0 px-3 list-group"
            style="background-color: #fbfcfe"
          >
            <v-list-item style="min-height: 40px">
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title>
                  <span>Администрирование</span>
                </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list>
          <v-divider color="#DCE5EF" class></v-divider>
          <v-list class="py-2 px-3 list-group">
            <v-list-item
              style="min-height: 38px; border-bottom: 1px solid #dce5efcc"
            >
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title>Производственная линия</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              style="min-height: 38px; border-bottom: 1px solid #dce5efcc"
            >
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title>Участки и секторы</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              style="min-height: 38px; border-bottom: 1px solid #dce5efcc"
            >
              <v-list-item-content class="py-0 list-text">
                <router-link
                  to="/linestopsidebar/linestop-reasons"
                  style="text-decoration: none"
                >
                <v-list-item-title>Причина остановок</v-list-item-title>
                </router-link>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              style="min-height: 38px; border-bottom: 1px solid #dce5efcc"
            >
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title>Меры</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              style="min-height: 38px; border-bottom: 1px solid #dce5efcc"
            >
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title>Поставщики</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              style="min-height: 38px; border-bottom: 1px solid #dce5efcc"
            >
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title>Управлении</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              style="min-height: 38px; border-bottom: 1px solid #dce5efcc"
            >
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title>Логи остановок</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              style="min-height: 38px; border-bottom: 1px solid #dce5efcc"
            >
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title>Роли</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-card>
      </div>
    </div>
    <!-- </v-col> -->
    <div id="edoContent">
      <!-- <v-col cols="10" id="edoContent"> -->
      <router-view></router-view>
      <!-- </v-col> -->
    </div>
    <!-- </v-row> -->
  </div>
</template>
<style scoped>
#edoSidebar {
  height: 94vh;
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
#edoContent {
  width: 80%;
  margin-left: 20%;
  background-color: rgb(241, 245, 248);
  padding: 20px 20px 20px 0px;
}
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

</style>
