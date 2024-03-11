<template>
  <v-app id="inspire">
    <v-app-bar
      v-show="isShown"
      id="edonew-header"
      app
      height="93"
      class="pa-0"
      :prominent="false"
    >
      <router-link to="/" tag="button">
        <div class="left-header1" style="width: 180px; text-align: center">
          <span
            class="text-h5 text-center"
            style="color: #163e72; font-weight: 600"
            v-text="$t('UzAuto')"
            @click="makeVisableSidebar"
          ></span>
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
            v-for="(item, index) in items"
            :key="index"
            v-slot="{ active, toggle }"
            class="ma-2"
          >
            <div
              @click="
                updateProgressa(item.color);
                sideBodyStatus(item.sidebar);
                handleItemClick(item);
              "
              v-if="item.visibled"
            >
              <v-card outlined color="white" round @click="toggle">
                <span
                  @mouseenter="mouseOn(item)"
                  @mouseleave="mouseLeave(item)"
                >
                  <v-hover
                    close-delay="10"
                    open-delay="10"
                    style="margin: auto"
                  >
                    <template v-slot="{ hover }">
                      <v-img
                        style="width: 45px"
                        :src="active ? item.src : item.src2"
                        :class="[
                          active
                            ? 'greyscale-image'
                            : hover
                            ? 'greyscale-image'
                            : 'ungreyscale-image',
                        ]"
                      >
                      </v-img>
                    </template>
                  </v-hover>
                </span>
                <p class="center-header_text">{{ item.text }}</p>
              </v-card>
            </div>
          </v-slide-item>
        </v-slide-group>
      </v-sheet>
      <!-- ////////////////////////////////////// -->
      <template>
        <v-card
          style="
            position: fixed;
            left: 5%;
            right: 5%;
            top: 90px;
            width: 90%;
            background-color: rgb(255, 255, 255);
          "
          v-if="tabVisable"
        >
          <v-tabs
            show-arrows
            background-color="transparent"
            slider-color="white"
          >
            <v-tab
              v-for="(itemTab, index) in tabsItem"
              :key="index"
              :to="itemTab.route"
              @click="tabsVisable(itemTab.route)"
              :style="{ 'min-width': calculateMinWidth(itemTab.text) }"
              :class="{ 'inactive-tab': !itemTab.isActive }"
            >
              <v-menu offset-y open-on-hover :close-on-content-click="false">
                <template v-slot:activator="{ on, attrs }">
                  <div
                    v-bind="attrs"
                    v-on="on"
                    class="mt-4 pb-2"
                    style="z-index: 0"
                    :class="{
                      'active-tab': itemTab.isActive,
                      'nav-menu': !itemTab.isActive,
                    }"
                  >
                    <v-icon :color="itemTab.color">{{ itemTab.icon }}</v-icon>
                    {{ itemTab.text }}
                  </div>
                </template>
                <!-- style="" -->
                <v-card
                  style="max-height: 500px"
                  v-if="itemTab.listBar"
                  @mouseenter="tabsVisable(false)"
                >
                  <v-text-field
                    v-if="itemTab.document_types.length > 8"
                    v-model="searchText"
                    placeholder="Qidirish"
                    dense
                    outlined
                    hide-details
                  >
                    <!-- class="ma-2" -->
                  </v-text-field>
                  <v-list dense>
                    <v-list-item
                      v-for="(tabItem, index) in itemTab.document_types.filter(
                        (v) =>
                          v.text
                            .toUpperCase()
                            .search(searchText.toUpperCase()) >= 0
                      )"
                      :key="index"
                      link
                      :to="tabItem.route"
                      @click="tabsVisable(tabItem.route)"
                      v-if="tabItem.count"
                    >
                      <v-list-item-icon>
                        <v-icon
                          :color="tabItem.color"
                          v-text="tabItem.icon"
                        ></v-icon>
                      </v-list-item-icon>
                      <v-list-item-title class="nav-menu">{{
                        tabItem.text
                      }}</v-list-item-title>
                      <v-list-item-action icon color="grey lighten-1">
                        <v-list-item-title>{{
                          tabItem.count != 9999 ? tabItem.count : ""
                        }}</v-list-item-title>
                      </v-list-item-action>
                    </v-list-item>
                  </v-list>
                </v-card>
              </v-menu>
            </v-tab>
          </v-tabs>
        </v-card>
      </template>
      <!-- ////////////////////////////////////// -->
      <v-spacer></v-spacer>
      <div class="text-center">
        <v-menu offset-y>
          <template v-slot:activator="{ on }">
            <v-btn
              text
              v-on="on"
              color="Black"
              style="width: 160px; height: 50px"
              @click="tabsVisable(true)"
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
          <v-list dense>
            <v-list-item>
              <router-link
                :to="'/personcontrol/profile/' + employee.id"
                style="text-decoration: none; color: inherit"
              >
                <v-list-item-title
                  ><v-icon>mdi-account-box-outline</v-icon>Shaxsiy
                  sahifa</v-list-item-title
                >
              </router-link>
            </v-list-item>
            <v-list-item>
              <router-link
                to="/my-setting"
                style="text-decoration: none; color: inherit"
              >
                <v-list-item-title
                  ><v-icon>mdi-account-edit</v-icon
                  >Sozlamalar</v-list-item-title
                >
              </router-link>
            </v-list-item>
            <v-list-item @click="logout">
              <!-- <v-list-item-title>{{ $t("message.logout") }}</v-list-item-title> -->
              <v-list-item-title>
                <v-icon>mdi-export</v-icon> Chiqish</v-list-item-title
              >
            </v-list-item>
          </v-list>
        </v-menu>
      </div>
    </v-app-bar>
    <!--Прогресбар ишлайди-->
    <template>
      <div :class="isShown ? 'prog_bar_close' : 'prog_bar_open'">
        <v-progress-linear value="100" :color="navBarColor" height="3" />
      </div>
      <button
        @click="toggleShow"
        :class="isShown ? 'toggleClass_open' : 'toggleClass_close'"
      >
        <v-icon style="font-size: 35px" :color="navBarColor">{{
          isShown ? "mdi-menu-up" : "mdi-menu-down"
        }}</v-icon>
      </button>
    </template>
    <!-- Sidebar qism boshlandi -->
    <template>
      <v-navigation-drawer
        v-if="sideBarVisable"
        v-model="drawer"
        app
        style="
          width: 380px;
          background-color: #f1f5f8;
          z-index: 4;
          height: 95vh;
        "
        id="edonew-sidebar"
        class="pl-12 pr-1 pt-6"
      >
        <!-- <template v-for="(item, i) in sideBarItem">
            <v-list-item
              router
              :to="item.route"
              class="pl-4"
              style
              :title="item.text"
              v-if="item.count !== 0"
            >
              <v-list-item-icon class="mr-1" :title="item.text">
                <v-icon small v-text="item.icon"></v-icon>{{ item.text }}
              </v-list-item-icon>
  
              <v-list-item-content>
                <v-list-item-title
                  class="side-bar-textstyle"
                  v-text="
                    item.id == 27
                      ? item['name_' + $i18n.locale] + ' (Кадр)'
                      : item['name_' + $i18n.locale]
                  "
                ></v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </template> -->
        <v-card
          class="mt-1"
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
                  <span>Создание документов</span>
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
                <v-icon class="mdi-24px">mdi-file-plus-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title> Создать новый документ </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              style="min-height: 38px; border-bottom: 1px solid #dce5efcc"
            >
              <v-list-item-icon class="mr-2 my-auto list-icons">
                <v-icon class="mdi-24px">mdi-file-check-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title> Избранные </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item style="min-height: 38px">
              <v-list-item-icon class="mr-2 my-auto list-icons">
                <v-icon class="mdi-24px">mdi-file-document-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title> Часто используемые </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-card>

        <v-card
          class="mt-5"
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
                  <span>Папки с документами</span>
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
                <v-icon class="mdi-24px" style="color: #f8a300"
                  >mdi-folder-download-outline</v-icon
                >
              </v-list-item-icon>
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title> Входящие </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              style="min-height: 38px; border-bottom: 1px solid #dce5efcc"
            >
              <v-list-item-icon class="mr-2 my-auto list-icons">
                <v-icon class="mdi-24px" style="color: #f8a300"
                  >mdi-folder-upload-outline</v-icon
                >
              </v-list-item-icon>
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title> Исходящие </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item style="min-height: 38px">
              <v-list-item-icon class="mr-2 my-auto list-icons">
                <v-icon class="mdi-24px" style="color: #f8a300"
                  >mdi-folder-remove-outline</v-icon
                >
              </v-list-item-icon>
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title> Черновики </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item style="min-height: 38px">
              <v-list-item-icon class="mr-2 my-auto list-icons">
                <v-icon class="mdi-24px" style="color: #f8a300"
                  >mdi-folder-outline</v-icon
                >
              </v-list-item-icon>
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title> Все документы </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-card>

        <v-card
          class="mt-5"
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
                <v-list-item-title> Отчет 1 </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              style="min-height: 38px; border-bottom: 1px solid #dce5efcc"
            >
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title> Отчет 2 </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item style="min-height: 38px">
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title> Отчет 3 </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-card>

        <v-card
          class="mt-5"
          elevation="0"
          style="
            border-radius: 10px;
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
                <v-list-item-title> Шаблоны документов </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              style="min-height: 38px; border-bottom: 1px solid #dce5efcc"
            >
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title> Реквизиты </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              style="min-height: 38px; border-bottom: 1px solid #dce5efcc"
            >
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title> Группа подписантов </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              style="min-height: 38px; border-bottom: 1px solid #dce5efcc"
            >
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title> Торговые каталоги </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              style="min-height: 38px; border-bottom: 1px solid #dce5efcc"
            >
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title> Поставщики </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              style="min-height: 38px; border-bottom: 1px solid #dce5efcc"
            >
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title> Тип документа </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              style="min-height: 38px; border-bottom: 1px solid #dce5efcc"
            >
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title> ChiefEmployee </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              style="min-height: 38px; border-bottom: 1px solid #dce5efcc"
            >
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title> Журналы </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              style="min-height: 38px; border-bottom: 1px solid #dce5efcc"
            >
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title> Организации </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              style="min-height: 38px; border-bottom: 1px solid #dce5efcc"
            >
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title>
                  Организации, запрашиваемые инфо...
                </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item style="min-height: 38px">
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title> История документа </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-card>
      </v-navigation-drawer>
      <v-navigation-drawer
        v-if="sideBarLinestop"
        v-model="drawer"
        app
        style="
          width: 380px;
          background-color: #f1f5f8;
          z-index: 4;
          height: 95vh;
        "
        id="edonew-sidebar"
        class="pl-12 pr-1 pt-6"
      >
        <v-card
          class="mt-1"
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
                  <span>Ticket actions</span>
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
                <v-icon class="mdi-24px">mdi-file-plus-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title> Tickets </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              style="min-height: 38px; border-bottom: 1px solid #dce5efcc"
            >
              <v-list-item-icon class="mr-2 my-auto list-icons">
                <v-icon class="mdi-24px">mdi-file-plus-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title> Create a ticket </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              style="min-height: 38px; border-bottom: 1px solid #dce5efcc"
            >
              <v-list-item-icon class="mr-2 my-auto list-icons">
                <v-icon class="mdi-24px">mdi-file-check-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title> All tickets </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item style="min-height: 38px">
              <v-list-item-icon class="mr-2 my-auto list-icons">
                <v-icon class="mdi-24px">mdi-file-document-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title> All open tickets </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-card>

        <v-card
          class="mt-5"
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
                  <span>Reports</span>
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
                <v-list-item-title> Report 1 </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              style="min-height: 38px; border-bottom: 1px solid #dce5efcc"
            >
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title> Report 2 </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              style="min-height: 38px; border-bottom: 1px solid #dce5efcc"
            >
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title> Report 3 </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-card>

        <v-card
          class="mt-5"
          elevation="0"
          style="
            border-radius: 10px;
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
                  <span>Administrators</span>
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
                <v-list-item-title> Production line </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              style="min-height: 38px; border-bottom: 1px solid #dce5efcc"
            >
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title> Shops and sectors </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              style="min-height: 38px; border-bottom: 1px solid #dce5efcc"
            >
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title> Linestop reasons </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              style="min-height: 38px; border-bottom: 1px solid #dce5efcc"
            >
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title> measures </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              style="min-height: 38px; border-bottom: 1px solid #dce5efcc"
            >
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title> Logs </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              style="min-height: 38px; border-bottom: 1px solid #dce5efcc"
            >
              <v-list-item-content class="py-0 list-text">
                <v-list-item-title> Roles </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-card>
      </v-navigation-drawer>
    </template>

    <!-- Sidebar qism tugadi -->

    <v-main
      :class="{ mainTopPadding: hasNewClass }"
      v-bind:style="
        sideBarVisabled
          ? 'padding: 90px 0px 0px 380px; background-color: #f1f5f8'
          : 'padding: 90px 0px 0px 380px; background-color: #f1f5f8'
      "
    >
      <router-view></router-view>
    </v-main>
    <v-footer class="footer pa-0">
      <div class="left-footer">
        <button class="left-footer_button">
          <router-link to="/phonebook" tag="button">
            <v-icon>mdi-phone</v-icon>
          </router-link>
        </button>
      </div>

      <div class="center-footer">
        <div>
          <p>
            {{ year }} yil - «UzAuto Motors» AJ Murojaat uchun: IT: 3056, 3078
            HR: 3923 Kadr(Toshkent): 1703
          </p>
        </div>
      </div>
      <!-- ------Footer Noteficatio------>
      <div class="right-footer">
        <v-btn
          v-if="user && !user.type"
          text
          small
          color="white"
          class="px-2 mx-1"
          to="/timeline"
          :title="$t('timeline.index')"
        >
          <v-icon color="blue" large>mdi-forum-outline</v-icon>

          <v-badge
            v-show="timeline_count"
            color="red"
            :content="timeline_count"
            class="py-2"
          ></v-badge>
        </v-btn>
        <!-- <span>
          <v-badge content="2" color="green">
            <v-icon>mdi-information-outline</v-icon>
          </v-badge>
        </span>
        <span>
          <v-badge content="5" color="red">
            <v-icon>mdi-fire</v-icon>
          </v-badge>
        </span>
        <span>
          <v-badge content="7" color="blue">
            <v-icon>mdi-bell-outline</v-icon>
          </v-badge>
        </span>
        <span>
          <v-badge content="9" color="#808080">
            <v-icon>mdi-comment-text-outline</v-icon>
          </v-badge>
        </span>
        <span>
          <v-badge content="0" color="#808080">
          <v-icon>mdi-star-outline</v-icon>
          </v-badge>
        </span>
        <span>
          <v-badge  color="#606060">
          <v-icon>mdi-comment-alert-outline</v-icon>
          </v-badge>
        </span> -->
      </div>
      <!-- ------Footer Noteficatio------>
    </v-footer>
  </v-app>
</template>

<script>
const axios = require("axios").default;
import Cookies from "js-cookie";
import Swal from "sweetalert2";
import { colors } from "vuetify/lib";
export default {
  data() {
    return {
      progressValue: 0,
      searchText: "",
      myApp: false,
      model: null,
      timeline_count: null,
      year: new Date().getFullYear(),
      loading: false,
      MYsTORE: [],

      itemsKPI: [],
      itemsHisobot: [],
      itemsSingIn: [],
      sideBarItem: [],
      workflowLinks: [],
      hrLinks: [],
      archiveLinks: [],
      inventoryLinks: [],
      adminLinks: [],
      drawer: true,
      tabVisable: false,
      isMouseOn: false,
      tabsItem: [],
      allDocItems: [],
      navBarColor: "#ff6347",
      callDialog: false,
      isShown: true,
      hasNewClass: false,
      sideBarVisabled: true,
      sideBarVisable: true,
      sideBarLinestop: false,
      items: [],
      dropdownItems: ["Dropdown Item 1", "Dropdown Item 2", "Dropdown Item 3"],
      create_document: [
        {
          id: 0,
          icon: "mdi-file-multiple",
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
      // return this.notifications ? this.notifications : "";
    },

    document_list() {
      return this.$store.state.document_list;
    },
    screenHeight() {
      return window.innerHeight - 170;
    },
    // myList(item) {
    //   console.log('item=',item);
    //   // return item.filter(
    //   //   (v) => v.text.toUpperCase().search(this.searchText.toUpperCase()) >= 0
    //   // );
    // },
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
    makeVisableSidebar() {
      this.sideBarVisable = true;
      this.sideBarLinestop = false;
    },
    tabsVisable(item) {
      this.tabVisable = item ? false : true;
    },
    mouseover() {
      this.myApp = true;
    },
    mouseleave() {
      this.myApp = false;
    },
    calculateMinWidth(text) {
      // Masofa ma'lumotlarining uzunligini o'rganish
      const textLength = text.length;
      // Masofa uzunligini aniqlovchi qiymat (masofa uzunligi o'zgartiring)
      const minWidth = textLength * 10 + 100; // 10 - harfning bo'lgan o'rnini, 100 - minimum uzunlik
      return `${minWidth}px`;
    },
    handleDropdownItemClick(tabNumber) {
      // Handle dropdown item click for a specific tab
      console.log(`Clicked on dropdown item for tab ${tabNumber}`);
    },
    mouseOn(item) {
      item.tabBar == true
        ? ((this.tabVisable = true), this.handleItemClick(item))
        : "";
      item.tabBar == false ? (this.tabVisable = false) : "";
    },
    mouseLeave(item) {
      item.tabBar == true ? (this.tabVisable = true) : "";
      item.tabBar == false ? (this.tabVisable = false) : "";
    },
    handleItemClick(item) {
      if (item.commands && typeof this[item.commands] === "function") {
        this[item.commands]();
      }
    },
    toggleShow() {
      this.isShown = !this.isShown;
      this.hasNewClass = !this.hasNewClass;
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
          this.nationality = res.data.nationality;
          this.company = res.data.company;
          this.employee_addresses = res.data.employee_addresses;
          this.employee_official_document = res.data.employee_official_document;
          this.employee_phones = res.data.employee_phones;
          this.staff = res.data.staff[0];
          this.tariff_scale = res.data.employee_staff[0].tariff_scale;
          this.roles =
            res.data.user && res.data.user.roles ? res.data.user.roles : [];
          this.employee_relatives = res.data.employee_relative;
          this.employee_work_histories = res.data.employee_work_histories;
          this.employee_education_histories =
            res.data.employee_education_histories;
          this.employee_id = res.data.user ? res.data.user.employee_id : 0;
          this.employee_tabel = res.data.tabel;
          // console.log("Tabel", this.employee_tabel);
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
          icon: "mdi-file-multiple",
          text: "Erkin shablon",
          name_uz_latin: "Erkin shablon",
          name_uz_cyril: "Эркин шаблон",
          name_ru: "Произвольный шаблон",
          route: "/document/create/1",
          count: "",
          visible: false,
        },
      ];
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
              icon:
                element.count > 1
                  ? "mdi-file-document-multiple-outline"
                  : "mdi-file-document-outline",
              name_uz_latin: element.name_uz_latin,
              text: element.name_uz_latin,
              name_uz_cyril: element.name_uz_cyril,
              name_ru: element.name_ru,
              color: "green",
              route: "/document/template/" + element.id,
              count: element.count,
              visible: i == 0 ? false : true,
            });
          });
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getDocList() {
      this.allDocItems = [
        {
          icon: "mdi-folder-multiple-outline",
          text: this.$t("Yangi hujjat"),
          hoeves: "items",
          color: "green",
          document_types: this.create_document,
          // route: "/documents/list/lavozim-y/57",
          listBar: true,
          visible: true,
        },
        {
          icon: "mdi-star-plus",
          color: "indigo",
          text: this.$t("Tanlanganlar"),
          route: "/document/template-favorite",
          document_types: null,
          visible: true,
          listBar: false,
        },
        {
          icon: "mdi-text-box-multiple-outline",
          color: "blue",
          text: this.$t("Ko'p foydalanilgan"),
          // route: "/documents/list/lavozim-y/57",
          document_types: null,
          visible: true,
          listBar: false,
        },
        // {
        //   icon: "mdi-folder-open",
        //   color:"orange",
        //   text: this.$t("Imzolangan hujjatlar"),
        //   // route: "/documents/list/lavozim-y/57",
        //   document_types: this.itemsSingIn,
        //   visible: true,
        //   listBar: true,
        // },
      ];
      if (this.user.id > 0) {
        axios
          .get(this.$store.state.backend_url + "api/documents/list-new")
          .then((response) => {
            response.data.forEach((element) => {
              let document_list = response.data;
              document_list.map((v) => {
                v.visible = this.$store.getters.checkPermission(
                  "document-list-" + v.menu_item
                );
                return v;
              });

              this.allDocItems.push({
                icon: "mdi-folder-open",
                color: "orange",
                text: this.$t("document." + element.menu_item),
                // route: element.route,
                count: element.count,
                document_types: element.document_types.map((c) => {
                  c.icon = "mdi-folder-open";
                  c.color = "orange";
                  c.text = c.name_uz_latin;
                  c.route = element.route + "/" + c.id;
                  return c;
                }),
                listBar: element.document_types.length > 0 ? true : false,
                visible: true,
              });
              this.$store.dispatch("setDocumentList", document_list);
            });
            this.allDocItems.push({
              icon: "mdi-folder-open",
              color: "orange",
              text: this.$t("Imzolangan hujjatlar"),
              // route: "/documents/list/lavozim-y/57",
              document_types: this.itemsSingIn,
              visible: true,
              listBar: true,
            });
          })
          .catch((error) => {
            console.log(error);
          });
      } else {
        axios
          .get(this.$store.state.backend_url + "api/documents/list")
          .then((response) => {
            response.data.forEach((element) => {
              let document_list = response.data;
              document_list.map((v) => {
                v.visible = this.$store.getters.checkPermission(
                  "document-list-" + v.menu_item
                );
                return v;
              });

              this.allDocItems.push({
                icon: "mdi-folder-open",
                text: this.$t("document." + element.menu_item),
                route: element.route,
                count: element.count,
                document_types: element.document_types,
                listBar: element.document_types.length > 0 ? true : false,
                visible: true,
              });
              this.$store.dispatch("setDocumentList", document_list);
            });
          })
          .catch((error) => {
            console.log(error);
          });
      }
    },

    getAllDoc() {
      this.tabsItem = this.allDocItems;
    },
    getKPI() {
      this.tabsItem = this.itemsKPI;
    },
    getHisobot() {
      this.tabsItem = this.itemsHisobot;
    },
    gethrLinks() {
      this.tabsItem = this.hrLinks;
    },
    getarchiveLinks() {
      this.tabsItem = this.archiveLinks;
    },
    getinventoryLinks() {
      this.tabsItem = this.inventoryLinks;
    },
    getadminLinks() {
      this.tabsItem = this.adminLinks;
    },
    getHujjatlarAy() {
      this.tabsItem = this.workflowLinks;
    },
    // getsingIn() {
    //   this.tabsItem = this.itemsSingIn;
    // },
    redirectSettingAccount() {
      this.$router.push("/my-setting/acount");
    },
    riderectLinestop() {
      this.$router.push("/linestop");
      this.sideBarVisable = false;
      this.sideBarLinestop = true;
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
      this.adminLinks = [
        {
          icon: "mdi-account-tie",
          text: this.$t("user.index"),
          route: "/users/list",
          visible: this.$store.getters.checkPermission("user-index"),
        },
        {
          icon: "mdi-account-tie",
          text: this.$t("user.indexDiller"),
          route: "/users/diller",
          visible: this.$store.getters.checkRole("superadministrator"),
        },
        {
          icon: "mdi-account-lock-outline",
          text: this.$t("user.role-permission"),
          route: "/role-permission/list",
          visible: this.$store.getters.checkPermission("role_permission-index"),
        },
        {
          icon: "mdi-account-key-outline",
          text: this.$t("user.permission"),
          route: "/users/permission",
          visible: this.$store.getters.checkPermission("permission-index"),
        },
        {
          icon: "mdi-account-key-outline",
          text: this.$t("user.role_permission_menu"),
          route: "/users/role-permission",
          visible: this.$store.getters.checkPermission("permission-index"),
        },
        {
          icon: "mdi-account-tie",
          text: this.$t("unblocked_users"),
          route: "/unblocked-users/list",
          visible: this.$store.getters.checkPermission(
            "show_unblocked_user_list"
          ),
        },
        {
          icon: "mdi-account-tie",
          text: this.$t("userTemplate.navigation_main"),
          route: "/user-template/list",
          visible: this.$store.getters.checkPermission(
            "show_unblocked_user_list"
          ),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("company.index"),
          route: "/companies/list",
          visible: this.$store.getters.checkPermission("company-index"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("message.countries"),
          route: "/countries/list",
          visible: this.$store.getters.checkPermission("country-index"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("message.regions"),
          route: "/regions/list",
          visible: this.$store.getters.checkPermission("region-index"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("message.natioanalities"),
          route: "/nationalities/list",
          visible: this.$store.getters.checkPermission("nationality-index"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("message.districts"),
          route: "/districts/list",
          visible: this.$store.getters.checkPermission("district-index"),
        },
        {
          icon: "mdi-bell",
          text: this.$t("message.notifications"),
          route: "/notifications/list",
          visible: this.$store.getters.checkPermission("notification-index"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("refreshDocument"),
          route: "/refresh-document/list",
          visible: this.$store.getters.checkPermission("refresh-document"),
        },
        {
          icon: "mdi-account-lock-outline",
          text: this.$t("Template Report"),
          route: "/template",
          visible: this.$store.getters.checkRole("superadministrator"),
        },
      ];
      this.inventoryLinks = [
        {
          icon: "mdi-select-inverse",
          text: this.$t("inventory.index1"),
          route: "/inventory/list/1",
          visible:
            this.$store.getters.checkRole("inventory_operator") ||
            this.$store.getters.checkRole("inventory_controller") ||
            this.$store.getters.checkRole("inventory_report"),
        },
        // {
        //   icon: "mdi-select-inverse",
        //   text: this.$t("inventory.index2"),
        //   route: "/inventory/list/2",
        //   visible:
        //     this.$store.getters.checkRole("inventory_operator") ||
        //     this.$store.getters.checkRole("inventory_controller") ||
        //     this.$store.getters.checkRole("inventory_report"),
        // },
        // {
        //   icon: "mdi-warehouse",
        //   text: this.$t("inventory.address"),
        //   route: "/inventory/addresses",
        //   visible:
        //     // this.$store.getters.checkRole("inventory_operator") ||
        //     this.$store.getters.checkRole("inventory_controller"),
        //   // this.$store.getters.checkRole("inventory_report"),
        // },
        // {
        //   icon: "mdi-car-battery",
        //   text: this.$t("inventory.product"),
        //   route: "/inventory/products",
        //   visible: false,
        //   // this.$store.getters.checkRole("inventory_operator") ||
        //   // this.$store.getters.checkRole("inventory_controller"),
        //   // this.$store.getters.checkRole("inventory_report"),
        // },
        // {
        //   icon: "mdi-tablet-dashboard",
        //   text: this.$t("Hisobot"),
        //   route: "/inventory/report",
        //   visible:
        //     this.$store.getters.checkRole("inventory_operator") ||
        //     this.$store.getters.checkRole("inventory_controller") ||
        //     this.$store.getters.checkRole("inventory_report")
        // },
        // {
        //   icon: "mdi-tablet-dashboard",
        //   text: this.$t("Hisobot"),
        //   route: "/inventory/report1",
        //   visible:
        //     this.$store.getters.checkRole("inventory_operator") ||
        //     this.$store.getters.checkRole("inventory_controller") ||
        //     this.$store.getters.checkRole("inventory_report"),
        // },
        {
          icon: "mdi-tablet-dashboard",
          text: this.$t("Hisobot"),
          route: "/inventory/report2",
          visible:
            this.$store.getters.checkRole("inventory_operator") ||
            this.$store.getters.checkRole("inventory_controller") ||
            this.$store.getters.checkRole("inventory_report"),
        },
        {
          icon: "mdi-paperclip",
          text: this.$t("Biriktirish"),
          route: "/inventory/attaching",
          visible: this.$store.getters.checkRole("inventory_controller"),
        },
      ];
      this.archiveLinks = [
        {
          icon: "mdi-archive-outline",
          text: this.$t("employee.name"),
          route: "/documents/list/archive/employee",
          visible: true,
        },
        {
          icon: "mdi-archive-outline",
          text: this.$t("position.index"),
          route: "/documents/list/archive/staff",
          visible: true,
        },
      ];
      this.hrLinks = [
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("employee.index"),
          route: "/employees/list",
          visible: this.$store.getters.checkPermission("employee-index"),
        },
        // {
        //   icon: "mdi-clipboard-account",
        //   text: this.$t("employee.index"),
        //   route: "/employees/children",
        //   visible: this.$store.getters.checkPermission("employee-index")
        // },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("dismissed.employee"),
          route: "/dismissed-employees/list",
          visible: this.$store.getters.checkPermission("employee-index"),
        },
        // {
        //   icon: "mdi-clipboard-account-outline",
        //   text: this.$t("employeeDocument.index"),
        //   route: "/document-employee/list",
        //   visible: this.$store.getters.checkPermission(
        //     "document_employee-index"),
        // },
        // {
        //   icon: "mdi-clipboard-account-outline",
        //   text: "Reports",
        //   route: "/reports/list",
        //   visible: this.$store.getters.checkPermission("report-index")
        // },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("reasons.index"),
          route: "/leaving-reasons/list",
          visible: this.$store.getters.checkPermission("leaving_reasons-index"),
        },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("appeal_content.index"),
          route: "/appeal-content/list",
          visible: this.$store.getters.checkPermission("appeal_content-index"),
        },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("registry.vacation"),
          route: "/vacation-registry",
          visible:
            this.$store.getters.checkPermission("vacation-registry-asaka") ||
            this.$store.getters.checkPermission("vacation-registry-angren") ||
            this.$store.getters.checkPermission("vacation-registry-toshkent") ||
            this.$store.getters.checkPermission("vacation-registry-xorazm"),
        },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("registry.education"),
          route: "/education-registry",
          visible:
            this.$store.getters.checkPermission("education-registry-asaka") ||
            this.$store.getters.checkPermission(
              "education-registry-toshkent"
            ) ||
            this.$store.getters.checkPermission("education-registry-xorazm"),
        },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("registry.business_trip"),
          route: "/business-trip-registry",
          visible: this.$store.getters.checkPermission("business_trip"),
        },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("registry.work_calendar"),
          route: "/work-calendar",
          visible: true,
        },
        {
          icon: "mdi-account-network-outline",
          text: this.$t("structure_company"),
          route: "/structure-company",
          visible: this.$store.getters.checkPermission(
            "structure-company-index"
          ),
        },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("familyRelatives.index"),
          route: "/family-relative/list",
          visible: this.$store.getters.checkPermission("family-relative-index"),
        },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("hr_language.index"),
          route: "/hr-language/list",
          visible: this.$store.getters.checkPermission("hr-language-index"),
        },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("hr_party.index"),
          route: "/hr-party/list",
          visible: this.$store.getters.checkPermission("hr-party-index"),
        },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("hr_study_degree.index"),
          route: "/hr-study-degree/list",
          visible: this.$store.getters.checkPermission("hr-study-degree-index"),
        },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("hr_study_type.index"),
          route: "/hr-study-type/list",
          visible: this.$store.getters.checkPermission("hr-study-type-index"),
        },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("hr_university.index"),
          route: "/hr-university/list",
          visible: this.$store.getters.checkPermission("hr-university-index"),
        },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("hr_major.index"),
          route: "/hr-major/list",
          visible: this.$store.getters.checkPermission("hr-major-index"),
        },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("hr_military_rank.index"),
          route: "/hr-military-rank/list",
          visible: this.$store.getters.checkPermission(
            "hr-military-rank-index"
          ),
        },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("hr_state_award.index"),
          route: "/hr-state-awards/list",
          visible: this.$store.getters.checkPermission("hr-state-award-index"),
        },
      ];
      this.itemsSingIn = [
        {
          icon: "mdi-checkbox-marked-circle-outline",
          color: "green",
          text: this.$t("lavozim_yuriqnomasi"),
          route: "/documents/list/lavozim-y/57",
          count: 9999,
          visible: false,
        },
        {
          icon: "mdi-close-box-outline",
          color: "red",
          text: this.$t("lavozim_yuriqnomasi"),
          route: "/documents/list/lavozim-y-cancel/0",
          count: 9999,
          visible: false,
        },
        {
          icon: "mdi-checkbox-marked-circle-outline",
          text: this.$t("kasbiy_yuriqnomasi"),
          color: "green",
          route: "/documents/list/kasbiy-y/59",
          count: 9999,
          visible: false,
        },
        {
          icon: "mdi-close-box-outline",
          color: "red",
          text: this.$t("kasbiy_yuriqnomasi"),
          route: "/documents/list/kasbiy-y-cancel/0",
          count: 9999,
          visible: false,
        },
        {
          icon: "mdi-checkbox-marked-circle-outline",
          text: this.$t("tarkibiy_tuzilma"),
          color: "green",
          route: "/documents/list/tarkibiy-t/58",
          count: 9999,
          visible: false,
        },
        {
          icon: "mdi-close-box-outline",
          color: "red",
          text: this.$t("tarkibiy_tuzilma"),
          route: "/documents/list/tarkibiy-t-cancel/0",
          count: 9999,
          visible: false,
        },
        {
          icon: "mdi-checkbox-marked-circle-outline",
          text: this.$t("standard"),
          color: "green",
          route: "/documents/list/standard/0",
          count: 9999,
          visible: false,
        },
        {
          icon: "mdi-close-box-outline",
          color: "red",
          text: this.$t("standard"),
          route: "/documents/list/standard-cancel/0",
          count: 9999,
          visible: false,
        },
        {
          icon: "mdi-checkbox-marked-circle-outline",
          text: this.$t("process_card"),
          color: "green",
          route: "/documents/list/karta-p/0",
          count: 9999,
          visible: false,
        },
        {
          icon: "mdi-close-box-outline",
          color: "red",
          text: this.$t("process_card"),
          route: "/documents/list/karta-p-cancel/0",
          count: 9999,
          visible: false,
        },
      ];
      this.itemsHisobot = [//Tayyorlandi
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
      this.workflowLinks = [
        {
          icon: "mdi-clipboard-file-outline",
          text: this.$t("message.document_template"),
          route: "/document-templates/list",
          visible: this.$store.getters.checkPermission(
            "document_template-index"
          ),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("message.requisites"),
          route: "/company-requisites/list",
          visible: this.$store.getters.checkPermission("requisite-index"),
        },
        {
          icon: "mdi-checkbox-multiple-marked-outline",
          text: this.$t("message.signers_group"),
          route: "/signers-group/list",
          visible: this.$store.getters.checkPermission("signer_group-index"),
        },
        {
          icon: "mdi-checkbox-multiple-marked-outline",
          text: this.$t("purchase_catalogs.catalogs"),
          route: "/purchase-catalogs/list",
          visible: this.$store.getters.checkPermission(
            "purchase_catalog-index"
          ),
        },
        {
          icon: "mdi-checkbox-multiple-marked-outline",
          text: this.$t("partners.index"),
          route: "/partners/list",
          visible: this.$store.getters.checkPermission("partners-index"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("message.document_types"),
          route: "/document-types/list",
          visible: this.$store.getters.checkPermission("document_type-index"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("ChiefEmployee"),
          route: "/chief-employee/list",
          visible: this.$store.getters.checkPermission("document_type-index"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("message.unioncom"),
          route: "/unioncom/list",
          visible: this.$store.getters.checkPermission("unioncom-index"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("message.bank"),
          route: "/bank/list",
          visible: this.$store.getters.checkPermission("bank-index"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("message.tmib"),
          route: "/tmib/list",
          visible: this.$store.getters.checkPermission("tmib-index"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("message.tmig"),
          route: "/tmig/list",
          visible: this.$store.getters.checkPermission("tmig-index"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("message.embassy"),
          route: "/embassy/list",
          visible: this.$store.getters.checkPermission("embassy-index"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("directory.index"),
          route: "/directories/list",
          visible: this.$store.getters.checkRole("directory"),
        },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("organization.index"),
          route: "/organization/list",
          visible: this.$store.getters.checkPermission("organization-index"),
        },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("requestdoc.index"),
          route: "/requestdoc/list",
          visible: this.$store.getters.checkPermission("requestdoc-index"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("Document History"),
          route: "/documents/document-event",
          visible: this.$store.getters.checkRole("superadministrator"),
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
          tabBar: false,
          visibled: false,
        },
        // {
        //   src: "img/svg/1add_blue_create.svg",
        //   src2: "img/svg/1add_blue_create.svg",
        //   text: "Hujjat yaratish",
        //   color: "#3949AB",
        //   commands: "getCreateDocument",
        //   sidebar: false,
        //   visibled: true,
        // },
        {
          src: "img/svg/folder_open2.svg",
          src2: "img/svg/folder_open2.svg",
          text: "Hujjatlar",
          color: "#99ff33",
          commands: "getAllDoc",
          sidebar: false,
          tabBar: true,
          visibled: true,
        },
        {
          src: "img/svg/chart_01.svg",
          src2: "img/svg/chart_03.svg",
          text: "KPI",
          color: "#86b300",
          commands: "getKPI",
          sidebar: false,
          tabBar: true,
          visibled: true,
        },
        // {
        //   src: "img/svg/signeddoc2.svg",
        //   src2: "img/svg/signeddoc.svg",
        //   text: "Imzolangan hujjatlar",
        //   color: "#ff4dff",
        //   commands: "getsingIn",
        //   sidebar: false,
        //   tabBar: true,
        //   visibled: true,
        // },
        {
          src: "img/svg/report.svg",
          src2: "img/svg/report.svg",
          text: "Hisobotlar",
          color: "#00cccc",
          commands: "getHisobot",
          sidebar: false,
          tabBar: true,
          visibled: true,
        },
        {
          src: "img/svg/docOborot2.svg",
          src2: "img/svg/docOborot2.svg",
          text: "Hujjatlar aylanmasi",
          color: "#00ACC1",
          commands: "getHujjatlarAy",
          sidebar: false,
          tabBar: true,
          visibled: true,
        },
        {
          src: "img/svg/personControl.svg",
          src2: "img/svg/personControl.svg",
          text: "Xodimlar boshqaruvi",
          commands: "gethrLinks",
          color: "#3333cc",
          sidebar: false,
          tabBar: true,
          visibled: true,
        },
        {
          src: "img/svg/adminControl.svg",
          src2: "img/svg/adminControl.svg",
          text: "Admin Panel",
          commands: "getadminLinks",
          color: "#9693f6",
          sidebar: false,
          tabBar: true,
          visibled: true,
        },
        {
          src: "img/svg/storehouse_icon.svg",
          src2: "img/svg/storehouse_icon.svg",
          text: "Inventarizatsiya",
          commands: "getinventoryLinks",
          color: "#AED581",
          sidebar: false,
          tabBar: true,
          visibled: true,
        },
        {
          src: "img/svg/docShablon.svg",
          src2: "img/svg/docShablon.svg",
          text: "Blanka shablonlari",
          color: "#9c99ec",
          // commands: "getadminLinks",
          sidebar: false,
          tabBar: false,
          visibled: true,
        },
        {
          src: "img/svg/archive.svg",
          src2: "img/svg/archive.svg",
          text: "Arxiv",
          commands: "getarchiveLinks",
          color: "#FDD835",
          sidebar: false,
          tabBar: true,
          visibled: true,
        },
        {
          src: "img/svg/sign_icon.svg",
          src2: "img/svg/sign_icon.svg",
          text: "Tasdiqlangan hujjatlar",
          color: "#66BB6A",
          sidebar: false,
          tabBar: false,
          visibled: true,
          commands: "redirectSettingAccount",
        },
        {
          src: "img/svg/helps.svg",
          src2: "img/svg/helps.svg",
          text: "Yordam",
          color: "#EEFF41",
          sidebar: false,
          tabBar: false,
          visibled: true,
        },
        {
          src: "img/svg/cancel_close.svg",
          src2: "img/svg/cancel_close.svg",
          text: "Linestop",
          commands: "riderectLinestop",
          color: "#EEFF41",
          sidebar: false,
          tabBar: false,
          visibled: true,
        },
      ];
      this.getDocList();
    },
    getTimelineCount() {
      axios
        .get(this.$store.state.backend_url + "api/timeline/get-count")
        .then((response) => {
          this.timeline_count = response.data;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    logout() {
      this.$store.dispatch("setUser", null);
      // this.$store.dispatch("setEimzoKey", null);
      this.$store.dispatch("setPermissions", null);
      this.$store.dispatch("setRole", null);
      this.$store.dispatch("setAccessToken", null);
      window.localStorage.clear();
      Cookies.remove("access_token");
      this.$router.push("/login");
    },
  },
  mounted() {
    this.updateProgress();
    this.sideBodyStatus();
    this.getCreateDocument();
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
