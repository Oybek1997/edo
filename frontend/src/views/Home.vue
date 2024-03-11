<template>
  <div>
    <v-card class="heightFull">
      <!-- <v-card-title primary-title>
        <h3>Dashboard</h3>
      </v-card-title> -->
      <!-- <v-card-text>
        <v-row> -->
      <!-- <v-col cols="6">
            <v-card>
              <v-card-title primary-title
                >Korxona xodimlari soni [Filiallar bo'yicha]</v-card-title
              >
              <v-card-text>
                <apexchart
                  v-if="chart2.visible"
                  height="315"
                  type="pie"
                  :options="chart2.options"
                  :series="chart2.series"
                ></apexchart>
              </v-card-text>
            </v-card>
          </v-col> -->
      <!-- <v-col cols="6">
            <v-card>
              <v-card-title primary-title
                >Korxona xodimlari soni [Bo'limlar bo'yicha]</v-card-title
              >
              <v-card-text>
                <apexchart
                  v-if="chart4.visible"
                  height="300"
                  type="bar"
                  :options="chart4.options"
                  :series="chart4.series"
                ></apexchart>
              </v-card-text>
            </v-card>
          </v-col>
          <v-col cols="6">
            <v-card>
              <v-card-title primary-title
                >EDO da kiritilgan xujjarlar [TOP 10]</v-card-title
              >
              <v-card-text>
                <apexchart
                  v-if="chart5.visible"
                  height="300"
                  type="bar"
                  :options="chart5.options"
                  :series="chart5.series"
                ></apexchart>
              </v-card-text>
            </v-card>
          </v-col> -->
      <!-- <v-col cols="6">
            <v-card min-height="350">
              <v-card-title primary-title
                >Tavallud ayyom. [Xodimlar soni {{ countBirthday }}]
              </v-card-title>
              <v-simple-table
                class="doc-template_data-table"
                dense
                height="330"
                id="table"
                fixed-header
              >
                <template v-slot:default>
                  <tbody>
                    <tr v-for="(item, index) in itemBirthday" :key="item.fio">
                      <td>{{ index + 1 }}</td>
                      <td>{{ item.fio }}</td>
                      <td>
                        {{ item.department }} <br /><span>{{ item.position }}</span>
                      </td>
                    </tr>
                  </tbody>
                </template>
              </v-simple-table>
            </v-card>
          </v-col> -->

      <h2 class="text-center mb-6" v-if="my_reysters.length > 0">{{ $t("reysters") }}</h2>
      <v-row class="mx-0 mb-10 top-chart justify-center" v-if="my_reysters.length > 0">
        <v-col
          class="pa-0"
          md="3"
          sm="6"
          xs="12"
          v-for="(value, key) in my_reysters"
          :key="key"
        >
          <v-hover>
            <template v-slot="{ hover }">
              <v-card
                class="ma-2 text-center"
                :class="`elevation-${hover ? 16 : 3}`"
                :title="value['name_' + $i18n.locale]"
                :to="'/dashboard-registry/' + value.id"
              >
                <v-card
                  color="green"
                  class="white--text align-center elevation-0"
                  style="
                    position: relative;
                    border-bottom-right-radius: 0;
                    border-bottom-left-radius: 0;
                    height: 72px;
                  "
                >
                  <v-card-title class="top-chart_title text-ellipsis px-8">{{
                    value["name_" + $i18n.locale]
                  }}</v-card-title>
                  <v-btn
                    class
                    dark
                    absolute
                    fab
                    bottom
                    left
                    color="#fff"
                    style="
                      color: #0b4c84;
                      margin-left: -10px;
                      font-size: 28px;
                      font-weight: 600;
                    "
                  >
                    {{ value.documents_count }}
                    <!-- <v-icon>mdi-plus</v-icon> -->
                  </v-btn>
                </v-card>
                <v-list-item class="px-0">
                  <v-list-item-content
                    class="px-8 align-self-center"
                    style="height: 60px"
                  >
                    <v-list-item-title
                      :title="value['name_' + $i18n.locale]"
                      class="top-chart_btn text-ellipsis text_nowrap mt-4"
                    >
                      {{ value.count }}
                      {{ value.department["name_" + $i18n.locale] }}
                    </v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-card>
            </template>
          </v-hover>
        </v-col>
      </v-row>
      <v-divider v-show="false" class="mb-10"></v-divider>
      <v-row v-if="false">
        <v-col sm="6" class="pl-6">
          <template>
            <v-simple-table>
              <template v-slot:default>
                <tbody>
                  <tr>
                    <td>{{ $t("total_user") }}</td>
                    <td style="font-size: 18px; padding-left: 10px">
                      {{
                        user_all_count.user_all_count ? user_all_count.user_all_count : ""
                      }}
                    </td>
                  </tr>
                  <tr>
                    <td>{{ $t("user_eri") }}</td>
                    <td style="font-size: 18px; color: green; padding-left: 10px">
                      {{ user_eri.user_eri ? user_eri.user_eri : "" }}
                    </td>
                  </tr>
                  <tr>
                    <td>{{ $t("user_ad") }}</td>
                    <td style="font-size: 18px; color: red; padding-left: 10px">
                      {{ user_ad.user_ad ? user_ad.user_ad : "" }}
                    </td>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>
          </template>
        </v-col>
        <v-col sm="6" class="pr-6">
          <template>
            <v-simple-table>
              <template v-slot:default>
                <tbody>
                  <tr>
                    <td>{{ $t("total_documentation") }}</td>
                    <td style="font-size: 18px; padding-left: 10px">
                      {{ all_count.all_count ? all_count.all_count : "" }}
                    </td>
                  </tr>
                  <tr>
                    <td>{{ $t("EDS_with") }}</td>
                    <td style="font-size: 18px; padding-left: 10px">
                      {{ doc_eri.doc_eri ? doc_eri.doc_eri : "" }} -
                      <span class>
                        {{
                          ((100 * doc_eri.doc_eri) / all_count.all_count).toFixed(2) +
                          " %"
                        }}
                      </span>
                    </td>
                  </tr>
                  <tr>
                    <td>{{ $t("EDS_without") }}</td>
                    <td style="font-size: 18px; padding-left: 10px">
                      {{ doc_ad.doc_ad ? doc_ad.doc_ad : "" }} -
                      <span class>
                        {{
                          ((100 * doc_ad.doc_ad) / all_count.all_count).toFixed(2) + " %"
                        }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>
          </template>
        </v-col>
      </v-row>
    </v-card>
    <v-card
      class="heightFull ma-2"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-row class="mx-0">
        <v-col cols="1" class="ma-0 pa-5" style="">
        </v-col>
        <v-col cols="2" class="ma-0 pa-5" style="">
          <v-list class="mx-0 mt-0 mb-5 pa-0">
            <v-list-item-group class="home_left_sidebar py-0">
              <v-list-item class="px-2">
                <v-list-item-icon>
                  <v-icon style="font-size: 22px">mdi-home-outline</v-icon>
                </v-list-item-icon>
                <v-list-item-content class="py-2">
                  <v-list-item-title class="home_left_sidebar-text"
                    >Overview</v-list-item-title
                  >
                </v-list-item-content>
              </v-list-item>
              <v-list-item class="px-2">
                <v-list-item-icon>
                  <v-icon style="font-size: 22px">mdi-clock-outline</v-icon>
                </v-list-item-icon>
                <v-list-item-content class="py-2">
                  <v-btn
                    text
                    small
                    class="ma-0 pa-0"
                    min-width="0"
                    @click="$router.push('/support/index')"
                  >
                    <v-list-item-title class="home_left_sidebar-text"
                      >IT LIVE</v-list-item-title
                    >
                  </v-btn>
                </v-list-item-content>
              </v-list-item>
              <v-list-item class="px-2">
                <v-list-item-icon>
                  <v-icon style="font-size: 22px">mdi-star-outline</v-icon>
                </v-list-item-icon>
                <v-list-item-content class="py-2">
                  <v-list-item-title class="home_left_sidebar-text"
                    >Corporate chat</v-list-item-title
                  >
                </v-list-item-content>
              </v-list-item>
              <v-list-item class="px-2">
                <v-list-item-icon>
                  <v-icon style="font-size: 22px">mdi-file-document-outline</v-icon>
                </v-list-item-icon>
                <v-list-item-content class="py-2">
                  <v-list-item-title class="home_left_sidebar-text"
                    >Document repository</v-list-item-title
                  >
                </v-list-item-content>
              </v-list-item>
              <v-list-item class="px-2">
                <v-list-item-icon>
                  <v-icon style="font-size: 22px">mdi-check-box-outline</v-icon>
                </v-list-item-icon>
                <v-list-item-content class="py-2">
                  <v-list-item-title class="home_left_sidebar-text"
                    >Task management</v-list-item-title
                  >
                </v-list-item-content>
              </v-list-item>
            </v-list-item-group>
          </v-list>
          <v-list class="ma-0 pa-0">
            <span
              class="home_content_title ml-2"
              style="font-size: 12px; font-weight: 600; color: #626f86"
              >SPACES</span
            >
            <v-list-item-group class="home_left_sidebar left_sidebar_icons py-0">
              <v-list-item class="px-2">
                <v-list-item-icon>
                  <v-icon style="font-size: 16px; color: #fff">mdi-folder</v-icon>
                </v-list-item-icon>
                <v-list-item-content class="py-2">
                  <v-btn
                    text
                    small
                    class="ma-0 pa-0"
                    min-width="0"
                    @click="$router.push('/support/index')"
                  >
                  <v-list-item-title class="home_left_sidebar-text"
                    >IT LIVE</v-list-item-title
                  ></v-btn>
                </v-list-item-content>
              </v-list-item>
              <v-list-item class="px-2">
                <v-list-item-icon>
                  <v-icon style="font-size: 16px; color: #fff">mdi-folder</v-icon>
                </v-list-item-icon>
                <v-list-item-content class="py-2">
                  <v-list-item-title class="home_left_sidebar-text"
                    >Human Resources</v-list-item-title
                  >
                </v-list-item-content>
              </v-list-item>
              <v-list-item class="px-2">
                <v-list-item-icon>
                  <v-icon style="font-size: 16px; color: #fff">mdi-folder</v-icon>
                </v-list-item-icon>
                <v-list-item-content class="py-2">
                  <v-list-item-title class="home_left_sidebar-text"
                    >Announcements</v-list-item-title
                  >
                </v-list-item-content>
              </v-list-item>
              <v-list-item class="px-2">
                <v-list-item-icon>
                  <v-icon style="font-size: 16px; color: #fff">mdi-folder</v-icon>
                </v-list-item-icon>
                <v-list-item-content class="py-2">
                  <v-list-item-title class="home_left_sidebar-text"
                    >SAP S/4HANA</v-list-item-title
                  >
                </v-list-item-content>
              </v-list-item>
              <v-list-item class="px-2">
                <v-list-item-icon>
                  <v-icon style="font-size: 16px; color: #fff">mdi-folder</v-icon>
                </v-list-item-icon>
                <v-list-item-content class="py-2">
                  <v-list-item-title class="home_left_sidebar-text"
                    >Product Engineering</v-list-item-title
                  >
                </v-list-item-content>
              </v-list-item>
              <v-list-item class="px-2">
                <v-list-item-icon>
                  <v-icon style="font-size: 16px; color: #fff">mdi-folder</v-icon>
                </v-list-item-icon>
                <v-list-item-content class="py-2">
                  <v-list-item-title class="home_left_sidebar-text"
                    >Production</v-list-item-title
                  >
                </v-list-item-content>
              </v-list-item>
              <v-list-item class="px-2">
                <v-list-item-icon>
                  <v-icon style="font-size: 16px; color: #fff">mdi-folder</v-icon>
                </v-list-item-icon>
                <v-list-item-content class="py-2">
                  <v-list-item-title class="home_left_sidebar-text"
                    >Medical center</v-list-item-title
                  >
                </v-list-item-content>
              </v-list-item>
              <v-list-item class="px-2">
                <v-list-item-icon>
                  <v-icon style="font-size: 16px; color: #fff">mdi-folder</v-icon>
                </v-list-item-icon>
                <v-list-item-content class="py-2">
                  <v-list-item-title class="home_left_sidebar-text"
                    >Yoshlar portali</v-list-item-title
                  >
                </v-list-item-content>
              </v-list-item>
            </v-list-item-group>
          </v-list>
        </v-col>
        <v-col cols="6" class="ma-0 pa-0" style="">
          <v-row class="mx-0">
            <v-col cols="12" class="px-3 pt-5 pb-0" style="">
              <span class="home_content_title">PICK UP WHERE YOU LEFT OFF </span>
            </v-col>
            <v-col cols="6" class="pa-3" style="">
              <v-card
                class="mx-0 pa-3"
                outlined
                style="border: 1px solid #ced4da; border-radius: 10px"
              >
                <v-list-item class="px-0">
                  <v-list-item-icon class="my-0 mr-2">
                    <v-icon
                      size="30"
                      style="
                        background: #2c8dff;
                        color: #fff;
                        border-radius: 5px;
                        padding: 3px;
                      "
                      >mdi-menu</v-icon
                    >
                  </v-list-item-icon>
                  <v-list-item-content class="pt-0">
                    <v-list-item-title class="home_content_title mb-1">
                      SUPPORT CENTER
                    </v-list-item-title>
                    <v-list-item-subtitle class="home_content_subtitle"
                      >Support center</v-list-item-subtitle
                    >
                  </v-list-item-content>
                </v-list-item>

                <v-card-actions class="px-0 pb-0">
                  <v-card-subtitle class="pa-0 home_content_subtitle"
                    >Visited 5 minutes ago</v-card-subtitle
                  >
                </v-card-actions>
              </v-card>
            </v-col>
            <v-col cols="6" class="pa-3" style="">
              <v-card
                class="mx-0 pa-3"
                outlined
                style="border: 1px solid #ced4da; border-radius: 10px"
              >
                <v-list-item class="px-0">
                  <v-list-item-icon class="my-0 mr-2">
                    <v-icon
                      size="30"
                      style="
                        background: #2c8dff;
                        color: #fff;
                        border-radius: 5px;
                        padding: 3px;
                      "
                      >mdi-menu</v-icon
                    >
                  </v-list-item-icon>
                  <v-list-item-content class="pt-0">
                    <v-list-item-title class="home_content_title mb-1">
                      Corporate chat
                    </v-list-item-title>
                    <v-list-item-subtitle class="home_content_subtitle"
                      >IT Support</v-list-item-subtitle
                    >
                  </v-list-item-content>
                </v-list-item>

                <v-card-actions class="px-0 pb-0">
                  <v-card-subtitle class="pa-0 home_content_subtitle"
                    >Visited 5 minutes ago</v-card-subtitle
                  >
                </v-card-actions>
              </v-card>
            </v-col>
            <v-col cols="6" class="pa-3" style="">
              <v-card
                class="mx-0 pa-3"
                outlined
                style="border: 1px solid #ced4da; border-radius: 10px; cursor: pointer"
                @click="$router.push('/support/index')"
              >
                <v-list-item class="px-0">
                  <v-list-item-icon class="my-0 mr-2">
                    <v-icon
                      size="30"
                      style="
                        background: #2c8dff;
                        color: #fff;
                        border-radius: 5px;
                        padding: 3px;
                      "
                      >mdi-menu</v-icon
                    >
                  </v-list-item-icon>
                  <v-list-item-content class="pt-0">
                    <v-list-item-title class="home_content_title mb-1">
                      IT LIVE
                    </v-list-item-title>
                    <v-list-item-subtitle class="home_content_subtitle"
                      >IT Support</v-list-item-subtitle
                    >
                  </v-list-item-content>
                </v-list-item>

                <v-card-actions class="px-0 pb-0">
                  <v-card-subtitle class="pa-0 home_content_subtitle"
                    >Visited 5 minutes ago</v-card-subtitle
                  >
                </v-card-actions>
              </v-card>
            </v-col>
            <v-col cols="6" class="pa-3" style="">
              <v-card
                class="mx-0 pa-3"
                outlined
                style="border: 1px solid #ced4da; border-radius: 10px"
              >
                <v-list-item class="px-0">
                  <v-list-item-icon class="my-0 mr-2">
                    <v-icon
                      size="30"
                      style="
                        background: #2c8dff;
                        color: #fff;
                        border-radius: 5px;
                        padding: 3px;
                      "
                      >mdi-menu</v-icon
                    >
                  </v-list-item-icon>
                  <v-list-item-content class="pt-0">
                    <v-list-item-title class="home_content_title mb-1">
                      SAP LIVE
                    </v-list-item-title>
                    <v-list-item-subtitle class="home_content_subtitle"
                      >IT Support</v-list-item-subtitle
                    >
                  </v-list-item-content>
                </v-list-item>

                <v-card-actions class="px-0 pb-0">
                  <v-card-subtitle class="pa-0 home_content_subtitle"
                    >Visited 5 minutes ago</v-card-subtitle
                  >
                </v-card-actions>
              </v-card>
            </v-col>
            <v-col cols="6" class="pa-3" style="">
              <v-card
                class="mx-0 pa-3"
                outlined
                style="border: 1px solid #ced4da; border-radius: 10px"
              >
                <v-list-item class="px-0">
                  <v-list-item-icon class="my-0 mr-2">
                    <v-icon
                      size="30"
                      style="
                        background: #2c8dff;
                        color: #fff;
                        border-radius: 5px;
                        padding: 3px;
                      "
                      >mdi-menu</v-icon
                    >
                  </v-list-item-icon>
                  <v-list-item-content class="pt-0">
                    <v-list-item-title class="home_content_title mb-1">
                      IT Support
                    </v-list-item-title>
                    <v-list-item-subtitle class="home_content_subtitle"
                      >IT Support</v-list-item-subtitle
                    >
                  </v-list-item-content>
                </v-list-item>

                <v-card-actions class="px-0 pb-0">
                  <v-card-subtitle class="pa-0 home_content_subtitle"
                    >Visited 5 minutes ago</v-card-subtitle
                  >
                </v-card-actions>
              </v-card>
            </v-col>
            <v-col cols="6" class="pa-3" style="">
              <v-card
                class="mx-0 pa-3"
                outlined
                style="border: 1px solid #ced4da; border-radius: 10px"
              >
                <v-list-item class="px-0">
                  <v-list-item-icon class="my-0 mr-2">
                    <v-icon
                      size="30"
                      style="
                        background: #2c8dff;
                        color: #fff;
                        border-radius: 5px;
                        padding: 3px;
                      "
                      >mdi-menu</v-icon
                    >
                  </v-list-item-icon>
                  <v-list-item-content class="pt-0">
                    <v-list-item-title class="home_content_title mb-1">
                      SAP Support
                    </v-list-item-title>
                    <v-list-item-subtitle class="home_content_subtitle"
                      >IT Support</v-list-item-subtitle
                    >
                  </v-list-item-content>
                </v-list-item>

                <v-card-actions class="px-0 pb-0">
                  <v-card-subtitle class="pa-0 home_content_subtitle"
                    >Visited 5 minutes ago</v-card-subtitle
                  >
                </v-card-actions>
              </v-card>
            </v-col>
            <v-col cols="12" class="pa-3" style="text-align: right">
              <span class="home_content_title" style="color: #2c8dff">Edit feed</span>
            </v-col>
            <v-col cols="12" class="pa-3" style="">
              <span
                class="home_content_title"
                style="font-size: 14px; margin-bottom: 20px"
                >Discover what’s happening</span
              >
              <div class="mt-5" style="display: flex">
                <v-btn
                  class="home_content_btn mr-5"
                  text
                  style="
                    height: 25px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    text-transform: none;
                  "
                  @click="$router.push('/support/index')"
                >
                  IT LIVE*
                </v-btn>
                <v-btn
                  class="home_content_btn mr-5"
                  text
                  style="
                    height: 25px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    text-transform: none;
                  "
                >
                  Human Resources
                </v-btn>
                <v-btn
                  class="home_content_btn mr-5"
                  text
                  style="
                    height: 25px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    text-transform: none;
                  "
                >
                  Announcements
                </v-btn>
              </div>
            </v-col>
            <v-col cols="12" class="pa-3 mb-10" style="">
              <v-card
                class="mx-0 pa-3"
                outlined
                style="border: 1px solid #ced4da; border-radius: 10px"
              >
                <p
                  class="home_content_title text-center"
                  style="color: #676768; font-size: 15px"
                >
                  Welcome to Corporate board
                </p>
                <p class="text-center">
                  <v-icon
                    size="40"
                    style="background: ; color: #2c8dff; border-radius: 5px; padding: 3px"
                    >mdi-chart-bar-stacked</v-icon
                  >
                </p>
                <p
                  class="home_content_title text-center"
                  style="color: #676768; font-size: 15px"
                >
                  Corporate board is where your team collaborates and shares knowledge -
                  create, share and discuss your files, ideas, minutes, specs, mockups,
                  diagrams and projects
                </p>
                <span class="home_content_title" style="color: #2c8dff; font-size: 14px"
                  >Customize</span
                >
              </v-card>
            </v-col>
          </v-row>
        </v-col>
        <v-col cols="2" class="pa-5 mt-8 mb-8" style="">
          <v-card
            class="mx-auto"
            outlined
            max-width="350"
            style="border: 1px solid #ced4da; border-radius: 10px"
          >
            <v-card-actions class="pa-3">
              <span class="home_content_title" style="color: #000"
                >Yoshlar va biznesni qo’llab quvvatlash</span
              >

              <v-spacer></v-spacer>

              <!-- @click="show = !show" -->
              <v-btn icon>
                <v-icon style="color: #000">{{
                  show ? "mdi-chevron-up" : "mdi-chevron-down"
                }}</v-icon>
              </v-btn>
            </v-card-actions>
            <!-- https://img.freepik.com/free-vector/youth-day-event-with-jumping-people_23-2148600297.jpg -->
            <!-- https://c8.alamy.com/comp/DMMXRN/people-solve-problems-to-build-business-startup-DMMXRN.jpg -->
            <v-img
              src="https://img.freepik.com/free-vector/youth-day-event-with-jumping-people_23-2148600297.jpg"
              height="200px"
            ></v-img>

            <v-card-title class="home_right_sidebar-text">
              Yoshlar bilan ishlash portali
            </v-card-title>

            <v-card-subtitle class="home_right_sidebar-subtitle">
              2024-yil O‘zbekistonda "Yoshlar va biznesni qo‘llab-quvvatlash yili" deb
              e’lon qilindi. Kopaniyamizda yoshlarni qo’llab quvvtlash bo’yicha portal
              ishga tushdi, Uni Beta versiyada ishlatishingiz mumkin.
            </v-card-subtitle>
            <v-card-actions class="pa-5 mb-2">
              <v-btn
                class="home_right_sidebar_btn"
                text
                style="
                  height: 28px;
                  display: flex;
                  align-items: center;
                  justify-content: center;
                  text-transform: none;
                "
              >
                Portalga o'tish
              </v-btn>
            </v-card-actions>

            <v-expand-transition>
              <div v-show="show">
                <v-divider></v-divider>

                <v-card-text>
                  I'm a thing. But, like most politicians, he promised more than he could
                  deliver. You won't have time for sleeping, soldier, not with all the bed
                  making you'll be doing. Then we'll go with that data file! Hey, you add
                  a one and two zeros to that or we walk! You're going to do his laundry?
                  I've got to find a way to escape.
                </v-card-text>
              </div>
            </v-expand-transition>
          </v-card>
          <v-card
            class="mx-auto pa-5 mt-5"
            outlined
            max-width="350"
            style="border: 1px solid #ced4da; border-radius: 10px"
          >
            <vue-cal
              class="vuecal--rounded-theme vuecal--green-theme"
              xsmall
              hide-view-selector
              :time="false"
              active-view="month"
              :disable-views="['week']"
              style="max-width: 100%; height: 300px; margin: auto"
            >
            </vue-cal>
            <div class="py-3 px-0">
                    <v-divider class="mb-2" color="#DCE5EF"></v-divider>
                    <span class="mr-2" style="padding: 3px 8px; font-size: 13px; background: #2C8DFF; color: #fff;">Дни рождения</span>
                    <span style="font-size: 11px; color: #F8A300;">Сегодня 25.09.2024 г.  </span>
                </div>
                <v-list class="py-0">
                    <v-list-item-group>
                        <v-list-item class="px-0">
                            <v-list-item-avatar style="width: 30px; height: 30px;">
                                <img style="border: 2px solid #B5C3CF;" src="../assets/User-Default.jpg" />
                            </v-list-item-avatar>
                            <v-list-item-content>
                                <v-list-item-title class="chat_item_title" style="font-size: 12px; color:#000; font-weight: 600;">Аллаберганов А</v-list-item-title>
                                <v-list-item-subtitle></v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item class="px-0">
                            <v-list-item-avatar style="width: 30px; height: 30px;">
                                <img style="border: 2px solid #B5C3CF;" src="../assets/User-Default.jpg" />
                            </v-list-item-avatar>
                            <v-list-item-content>
                                <v-list-item-title class="chat_item_title" style="font-size: 12px; color:#000; font-weight: 500;">Юсупов Кобилжон</v-list-item-title>
                                <v-list-item-subtitle></v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item class="px-0">
                            <v-list-item-avatar style="width: 30px; height: 30px;">
                                <img style="border: 2px solid #B5C3CF;" src="../assets/User-Default.jpg" />
                            </v-list-item-avatar>
                            <v-list-item-content>
                                <v-list-item-title class="chat_item_title" style="font-size: 12px; color:#000; font-weight: 500;">Ниёзов Хуршид </v-list-item-title>
                                <v-list-item-subtitle></v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item class="px-0">
                            <v-list-item-avatar style="width: 30px; height: 30px;">
                                <img style="border: 2px solid #B5C3CF;" src="../assets/User-Default.jpg" />
                            </v-list-item-avatar>
                            <v-list-item-content>
                                <v-list-item-title class="chat_item_title" style="font-size: 12px; color:#000; font-weight: 500;">Аллаберганов А</v-list-item-title>
                                <v-list-item-subtitle></v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                        <span class="ml-14 chat_item_title" style="cursor: pointer">Все...</span>
                    </v-list-item-group>
                </v-list>
          </v-card>
        </v-col>
        <v-col cols="1" class="ma-0 pa-5" style="">
        </v-col>
      </v-row>
    </v-card>
  </div>
</template>
<script>
const axios = require("axios").default;
import DoughnutChart from "@/components/DoughnutChart";
import PieChart from "@/components/PieChart";
import Profile from "@/components/Profile";
import Cookies from "js-cookie";
import "vue-cal/dist/vuecal.css";
import VueCal from "vue-cal";

export default {
  components: {
    PieChart,
    DoughnutChart,
    Profile,
    VueCal,
  },
  name: "Home",
  data() {
    return {
      show: false,
      itemBirthday: [],
      countBirthday: 0,
      charts0: {
        visible: false,
      },
      catalog: {
        inbox: { count: 0, new_count: 0 },
        outbox: { count: 0, new_count: 0 },
        draft: { count: 0, new_count: 0 },
        cancel: { count: 0, new_count: 0 },
      },
      filter: {
        today: null,
        yesterday: null,
        week: null,
        month: null,
        between_dates: null,
        department_id: "",
        employee_id: "",
      },
      my_reysters: [],
      document_types: [],
      document_lists: [],
      source: "/",
      count_document: 0,
      loading: false,
      menu: "",
      start: "",
      select: ["4 Menendjer"],
      states: ["Alabama", "Alaska", "Arizona"],
      chartData1: [],
      chartData2: null,

      focus: "",
      type: "month",
      typeToLabel: {
        month: "Month",
        week: "Week",
        day: "Day",
        "4day": "4 Days",
      },
      selectedEvent: {},
      selectedElement: null,
      selectedOpen: false,
      events: [],
      colors: [
        "blue",
        "indigo",
        "deep-purple",
        "cyan",
        "green",
        "orange",
        "grey darken-1",
      ],
      names: [
        "Meeting",
        "Holiday",
        "PTO",
        "Travel",
        "Event",
        "Birthday",
        "Conference",
        "Party",
      ],
      alert_news: null,
      notifications: [],

      focus: "",
      type: "month",
      typeToLabel: {
        month: "Month",
        week: "Week",
        day: "Day",
        "4day": "4 Days",
      },
      items: [
        {
          action: "mdi-home-outline",
          active: true,
          items: [
            { title: "IT LIVE", icon: "mdi-clock-outline" },
            { title: "Corporate chat", icon: "mdi-star-outline" },
            { title: "Document repository", icon: "mdi-file-document-outline" },
            { title: "Task management", icon: "mdi-check-box-outline" },
          ],
          title: "Overview",
        },
      ],
    };
  },
  methods: {
    changeFilter(arg) {},
    getRandomColor() {
      var letters = "0123456789ABCDEF";
      var color = "#";
      for (var i = 0; i < 3; i++) {
        color += letters[Math.floor(Math.random() * 16)];
      }
      return color;
    },
    dashboard() {
      this.loading = true;
      axios
        .get(this.$store.state.backend_url + "api/dashboard/" + this.$i18n.locale)
        .then((res) => {
          let data = res.data.boxes;
          this.report_by_date = res.data.report_by_date;
          this.my_reysters = res.data.my_reysters.filter((v) => v.documents_count > 0);
          this.chartData1 = data.map((v) => ({
            count: v.count,
            new_count: v.new_count,
            name: this.$t("message." + v.name),
            originalName: v.name,
          }));
          this.catalog.inbox = this.chartData1[0];
          this.catalog.outbox = this.chartData1[1];
          this.catalog.draft = this.chartData1[2];
          this.catalog.cancel = this.chartData1[3];
          let labels = data.map((v) => this.$t("message." + v.name));
          let counts = data.map((v) => v.count);
          let colors = data.map((v) => this.getRandomColor());
          this.chartData2 = {
            labels: labels,
            datasets: [
              {
                backgroundColor: ["teal", "indigo", "blue", "orange", "grey"],
                data: counts,
              },
            ],
          };
          //---ma-1
          this.document_types = res.data.document_types;
          this.departments = res.data.departments;
          this.document_types.forEach((element) => {
            this.count_document += element.counter;
          });
          this.loading = false;
          // this.getUserReport();
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getDocumentList() {
      axios
        .get(this.$store.state.backend_url + "api/documents/list")
        .then((res) => {
          this.document_lists = res.data;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getUserReport() {
      axios
        .get(this.$store.state.backend_url + "api/users/report")
        .then((res) => {
          this.all_count = res.data.all_count;
          this.doc_eri = res.data.doc_eri;
          this.doc_ad = res.data.doc_ad;
          this.user_all_count = res.data.user_all_count;
          this.user_eri = res.data.user_eri;
          this.user_ad = res.data.user_ad;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    viewDay({ date }) {
      this.focus = date;
      this.type = "day";
    },
    getEventColor(event) {
      return event.color;
    },
    setToday() {
      this.focus = "";
    },
    prev() {
      this.$refs.calendar.prev();
    },
    next() {
      this.$refs.calendar.next();
    },
    showEvent({ nativeEvent, event }) {},
    updateRange({ start, end }) {},
    calendarData() {
      alert("working..");
      this.calendarSection = true;
    },
  },
  mounted() {
    // this.$refs.calendar.checkChange();
    this.user = this.$store.getters.getUser();
    this.dashboard();
    if (this.$store.state.notifications.alert) {
      this.alert_news = "| ";
      this.$store.state.notifications.alert.forEach((element) => {
        this.alert_news += element.content + " | ";
      });
    }
    let myInterval = setInterval(() => {
      if (this.$store.state.notifications["canceled"]) {
        // this.notifications.push({key:'negative', icon:'mdi-file-check-outline', name:'agreement',value:this.$store.state.notifications['agreement']});
        // this.notifications.push({key:'negative', icon:'mdi-file-cancel-outline', name:'canceled',value:this.$store.state.notifications['canceled']});
        // this.notifications.push({key:'notification.document_out_one', icon:'mdi-numeric-1-box-multiple', name:'document_out_one',value:this.$store.state.notifications['document_out_one']});
        // this.notifications.push({key:'notification.document_out_two', icon:'mdi-numeric-2-box-multiple', name:'document_out_two',value:this.$store.state.notifications['document_out_two']});
        // this.notifications.push({key:'notification.document_out_three', icon:'mdi-numeric-3-box-multiple', name:'document_out_three',value:this.$store.state.notifications['document_out_three']});
        // this.notifications.push({key:'expected', icon:'', name:'expected',value:this.$store.state.notifications['expected']});
        // this.notifications.push({key:'resolution_results', icon:'mdi-bell-check-outline', name:'resolution_results', len: this.$store.state.notifications['length_resolution_results'] value:this.$store.state.notifications['resolution_results']});
        this.notifications.push({
          key: "executor",
          icon: "mdi-lightning-bolt-outline",
          name: "executor",
          len: this.$store.state.notifications["length_executor"],
          value: this.$store.state.notifications["executor"],
        });
        this.notifications.push({
          key: "expired",
          icon: "mdi-fire",
          name: "expired",
          len: this.$store.state.notifications["length_expired"],
          value: this.$store.state.notifications["expired"],
        });
        this.notifications.push({
          key: "notification.nazorat",
          icon: "mdi-order-bool-ascending-variant",
          name: "nazorat",
          len: this.$store.state.notifications["length_nazorat"],
          value: this.$store.state.notifications["nazorat"],
        });
        this.notifications.push({
          key: "pending",
          icon: "mdi-timer-sand",
          name: "prosesing",
          len: this.$store.state.notifications["length_prosesing"],
          value: this.$store.state.notifications["prosesing"],
        });
        this.notifications.push({
          key: "resolutions",
          icon: "mdi-bell-plus-outline",
          name: "resolutions",
          len: this.$store.state.notifications["length_resolutions"],
          value: this.$store.state.notifications["resolutions"],
        });
        this.notifications.push({
          key: "substantiate",
          icon: "mdi-alert-outline",
          name: "substantiate",
          len: this.$store.state.notifications["length_substantiate"],
          value: this.$store.state.notifications["substantiate"],
        });
        // this.notifications.push({key:'for_info', icon:'mdi-information-variant', name:'information', len: this.$store.state.notifications['length_info'], value:this.$store.state.notifications['information']});
        // this.notifications.push({key:'star', icon:'mdi-star-outline', name:'star', len: this.$store.state.notifications['length_star'], value:this.$store.state.notifications['star']});
        // this.notifications.push({key:'expired', icon:'mdi-magnify', name:'watcher',value:this.$store.state.notifications['watcher']});
        // console.log(this.notifications);
        clearInterval(myInterval);
      }
    }, 1000);
  },
};
</script>
<style scoped>
/* Add your component's styles here */
.home_left_sidebar .home_left_sidebar-text {
  color: #676768;
  font-size: 14px;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.home_content_title {
  color: #000;
  font-size: 13px;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.home_content_subtitle {
  color: #676768;
  font-size: 11px;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.home_content_btn {
  display: block;
  max-width: 150px;
  min-width: 140px;
  padding: 3px 12px;
  background: #e3e3e3;
  border-radius: 15px;
  text-transform: normal;
  color: #676768;
  font-size: 12px;
  font-weight: 500;
  border: 1px solid #c3c3c3;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.home_content_btn:hover {
  border: 1px solid #2c8dff;
  color: #2c8dff;
  background: #daebff;
}
.home_right_sidebar-text {
  color: #000;
  font-size: 14px;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.home_right_sidebar-subtitle {
  color: #676768;
  font-size: 12px;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.home_right_sidebar_btn {
  background: #2c8dff;
  border-radius: 8px;
  text-transform: normal;
  height: 30px;
  color: #fff;
  font-size: 12px;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
</style>
