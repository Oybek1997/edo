<template>
  <div class="fullHeight" style="background: #fff !important">
    <div class="profile_header">
      <v-row class="mx-0 pa-3 background-overlay">
        <v-col sm="12" md="6" class="pa-0">
          <v-list-item two-line class="px-0">
            <v-list-item-avatar width="96" height="96">
              <img v-if="base64" :src="'data:application/jpg;base64,' + base64" contain />
              <img v-else src="../../assets/User-Default.jpg" />
            </v-list-item-avatar>

            <v-list-item-content>
              <v-list-item-title class="profile_name mb-1">
                {{
                employee["lastname_" + $i18n.locale]
                }}
                {{
                employee["firstname_" + $i18n.locale]
                }}
                {{
                employee["middlename_" + $i18n.locale]
                }}
              </v-list-item-title>
              <v-list-item-subtitle class="profile_staff" v-if="staff.department && staff.position">
                <v-icon class="mr-1" small color="white">mdi-briefcase-outline</v-icon>
                <span class="mr-4">
                  {{
                  staff.department
                  ? staff.department["name_" + $i18n.locale]
                  : ""
                  }}
                </span>
              </v-list-item-subtitle>
              <v-card-text class="px-0 pb-0 pt-2" style="display: flex; align-items: center">
                <v-btn class="mx-1" fab dark x-small color="#354759">
                  <v-icon small dark>mdi-telegram</v-icon>
                </v-btn>
                <v-btn class="mx-1" fab dark x-small color="#354759">
                  <v-icon small dark>mdi-facebook-messenger</v-icon>
                </v-btn>
                <v-btn class="mx-1" fab dark x-small color="#354759">
                  <v-icon small dark>mdi-instagram</v-icon>
                </v-btn>
                <v-btn class="mx-1" fab dark x-small color="#354759">
                  <v-icon small dark>mdi-twitter</v-icon>
                </v-btn>

                <router-link
                  :to="'/users/all-users/' + employee_tabel"
                  style="text-decoration: none"
                >
                  <v-btn class="mx-1" fab dark x-small color="#6ac82d">
                    <v-icon small dark>mdi-plus</v-icon>
                  </v-btn>
                </router-link>
                <span class="ma-2 pa-2" fab dark x-small>
                  <img
                    v-if="employee && user && employee.id == user.employee_id"
                    src="img/imzo.png"
                    height="25"
                    style="cursor: pointer; border: 2px solid #faa"
                    @click="eImzoDialog = true"
                  />
                </span>
              </v-card-text>
            </v-list-item-content>
          </v-list-item>
        </v-col>
        <v-col sm="12" md="6" class="pa-0 mx-0 d-flex align-center">
          <v-row class="mx-0">
            <v-col
              sm="12"
              md="6"
              class="pa-0 mx-0"
              v-if="
                (staff.department && staff.department.department_code) ||
                employee.tabel ||
                staff.position
              "
            >
              <v-list-item class="px-0">
                <v-list-item-content>
                  <v-list-item-subtitle v-if="staff.position" class="profile_staff">
                    <span
                      class
                      :title="
                        staff.position
                          ? staff.position['name_' + $i18n.locale]
                          : ''
                      "
                    >
                      {{ $t("profile.employee_position") + ":" }}
                      {{
                      staff.position
                      ? staff.position["name_" + $i18n.locale]
                      : ""
                      }}
                    </span>
                  </v-list-item-subtitle>
                  <v-list-item-subtitle
                    v-if="staff.department.department_code"
                    class="profile_staff my-2"
                  >
                    <span class>
                      {{ $t("profile.department_code") + ":" }}
                      {{
                      staff.department.department_code
                      ? staff.department.department_code
                      : ""
                      }}
                    </span>
                  </v-list-item-subtitle>
                  <v-list-item-subtitle v-if="employee.tabel" class="profile_staff">
                    <span class>
                      {{ $t("profile.tabel") + ":" }}
                      {{ employee.tabel ? employee.tabel : "" }}
                    </span>
                  </v-list-item-subtitle>
                  <v-list-item-subtitle
                    class="profile_staff my-2 text-truncate d-inline-block"
                    style="max-width: 250px; cursor: pointer"
                    @click="roleDialog = true"
                  >
                    {{ $t("user.roles") + ":" }}
                    <span
                      class
                      v-for="(role, rIndx) in roles"
                      :key="rIndx"
                    >{{ role.display_name ? role.display_name + "," : "" }}</span>
                  </v-list-item-subtitle>
                </v-list-item-content>
              </v-list-item>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </div>
    <v-row class="mx-0" v-if="employee || employee.user && !employee.user.type">
      <v-col md="12" class="pa-0">
        <v-tabs v-model="tab" background-color="transparent" color="basil" grow icons-and-text>
          <v-tabs-slider></v-tabs-slider>

          <v-tab href="#tab-1">
            {{ $t("profile.personal_information") }}
            <v-icon>mdi-account-box-outline</v-icon>
          </v-tab>

          <v-tab href="#tab-0">
            {{ $t("profile.tasks") }}
            <v-icon>mdi-account-box-outline</v-icon>
          </v-tab>

          <v-tab href="#tab-2">
            {{ $t("profile.all_documents") }}
            <v-icon>mdi-file-chart-outline</v-icon>
          </v-tab>
          <v-tab href="#tab-3">
            {{ $t("profile.family_status") }}
            <v-icon>mdi-home-account</v-icon>
          </v-tab>
          <v-tab href="#tab-4">
            {{ $t("profile.information") }}
            <v-icon>mdi-certificate-outline</v-icon>
          </v-tab>
          <v-tab href="#tab-5">
            {{ $t("profile.work_history") }}
            <v-icon>mdi-hammer-wrench</v-icon>
          </v-tab>
          <v-tab href="#tab-6">
            {{ $t("profile.education_history") }}
            <v-icon>mdi-school</v-icon>
          </v-tab>
          <v-tab href="#tab-7">
            {{ $t("Ma'lumotnoma") }}
            <v-icon>mdi-card-account-details-outline</v-icon>
          </v-tab>
        </v-tabs>
        <v-tabs-items v-model="tab">
          <v-tab-item value="tab-0" class="pa-4">
            <v-card class="pa-2 mb-5 mx-4">
              <v-card-title class="pa-1">
                <span>{{ $t("profile.tasks") }}</span>
                <v-spacer></v-spacer>

                <v-btn
                  v-if="$store.getters.checkPermission('task-create')"
                  color="#6ac82d"
                  x-small
                  dark
                  fab
                  @click="newEmployeeTask"
                >
                  <v-icon>mdi-plus</v-icon>
                </v-btn>
              </v-card-title>

              <v-data-table
                fixed-header
                dense
                :loading-text="$t('loadingText')"
                :no-data-text="$t('noDataText')"
                :loading="loading"
                :headers="headers"
                :items="items"
                :single-expand="singleExpand"
                :expanded.sync="expanded"
                class="mainTable ma-1"
                style="border: 1px solid #aaa"
                item-key="id"
                show-expand
                :footer-props="{
                  itemsPerPageOptions: [20, 50, 100, -1],
                  itemsPerPageAllText: $t('itemsPerPageAllText'),
                  itemsPerPageText: $t('itemsPerPageText'),
                  showFirstLastPage: true,
                  firstIcon: 'mdi-arrow-collapse-left',
                  lastIcon: 'mdi-arrow-collapse-right',
                  prevIcon: 'mdi-arrow-left',
                  nextIcon: 'mdi-arrow-right',
                }"
              >
                <template v-slot:expanded-item="{ headers, item }">
                  <td :colspan="headers.length" class="pa-3">
                    <table class="infoTable ma-0 pa-0" v-if="item.comments.length">
                      <tr>
                        <td class="font-weight-bold">{{ $t("#") }}</td>
                        <td class="font-weight-bold">{{ $t("profile.task_comment_index") }}</td>
                        <td class="font-weight-bold">{{ $t("profile.file") }}</td>
                        <td class="font-weight-bold" style="max-width: 50px">{{ $t("actions") }}</td>
                      </tr>
                      <tbody>
                        <tr v-for="(comment, ind) in item.comments" :key="ind">
                          <td>{{ ind + 1 }}</td>
                          <td>{{ comment.description }}</td>
                          <td>
                            <v-btn color="green" small text @click="viewFile(item.file.id)">
                              <v-icon>mdi-eye</v-icon>
                            </v-btn>
                            {{ item.file ? item.file.file_name : "" }}
                          </td>
                          <td class style="max-width: 40px">
                            <v-btn
                              v-if="
                                $store.getters.checkPermission(
                                  'critical-delete'
                                )
                              "
                              color="red"
                              class="my-1"
                              x-small
                              text
                              @click="deleteTaskComment(comment)"
                            >
                              <v-icon>mdi-delete</v-icon>
                            </v-btn>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <!-- </v-container> -->
                    <span
                      v-else
                      style="display: block; text-align: center; color: red"
                    >{{ $t("noDataText") }}</span>
                  </td>
                </template>
                <template v-slot:item.id="{ item }">
                  {{
                  items
                  .map(function (x) {
                  return x.id;
                  })
                  .indexOf(item.id) + 1
                  }}
                </template>
                <template v-slot:item.task="{ item }">
                  <span>{{ item.task }}</span>
                </template>
                <template v-slot:item.begin_date="{ item }">
                  <span>{{ item.begin_date }}</span>
                </template>
                <template v-slot:item.due_date="{ item }">
                  <span>{{ item.due_date }}</span>
                </template>
                <template v-slot:item.actions="{ item }">
                  <v-btn
                    v-if="$store.getters.checkPermission('task-edit')"
                    color="blue"
                    small
                    text
                    @click="editEmployeeTask(item)"
                  >
                    <v-icon>mdi-pencil</v-icon>
                  </v-btn>
                  <v-btn color="success" small text @click="newTaskComment(item.id)">
                    <v-icon>mdi-file-upload</v-icon>
                  </v-btn>
                  <v-btn
                    v-if="$store.getters.checkPermission('task-delete')"
                    color="red"
                    small
                    text
                    @click="deleteEmployeeTask(item)"
                  >
                    <v-icon>mdi-delete</v-icon>
                  </v-btn>
                </template>
              </v-data-table>
            </v-card>
          </v-tab-item>
          <v-tab-item value="tab-1">
            <v-card color="basil" flat>
              <v-row class="ma-0 pa-4">
                <v-col v-if="company.name" cols="12" sm="3" xs="12" class="pa-0">
                  <v-card class="ma-2 elevation-0 border_left">
                    <v-list-item class="px-4 py-1">
                      <v-list-item-content class="align-self-center py-0">
                        <v-card-text
                          class="mx-0 px-0 py-1 text-color text_nowrap"
                        >{{ $t("employee.Company") }}</v-card-text>
                        <v-card-text
                          class="mx-0 px-0 py-1 font-weight-bold text-color"
                        >{{ company.name ? company.name : "" }}</v-card-text>
                      </v-list-item-content>
                    </v-list-item>
                  </v-card>
                </v-col>
                <v-col v-if="employee.user !== null" cols="12" sm="3" xs="12" class="pa-0">
                  <v-card class="ma-2 elevation-0 border_left">
                    <v-list-item class="px-4 py-1">
                      <v-list-item-content class="align-self-center py-0">
                        <v-card-text
                          class="mx-0 px-0 py-1 text-color text_nowrap"
                        >{{ $t("profile.user_name") }}</v-card-text>
                        <v-card-text class="mx-0 px-0 py-1 font-weight-bold text-color">
                          {{
                          employee.user && employee.user.username
                          ? employee.user.username
                          : ""
                          }}
                        </v-card-text>
                      </v-list-item-content>
                    </v-list-item>
                  </v-card>
                </v-col>
                <v-col v-if="employee.born_date" cols="12" sm="3" xs="12" class="pa-0">
                  <v-card class="ma-2 elevation-0 border_left">
                    <v-list-item class="px-4 py-1">
                      <v-list-item-content class="align-self-center py-0">
                        <v-card-text
                          class="mx-0 px-0 py-1 text-color text_nowrap"
                        >{{ $t("profile.born_date") }}</v-card-text>
                        <v-card-text class="mx-0 px-0 py-1 font-weight-bold text-color">
                          {{
                          employee.born_date ? employee.born_date : ""
                          }}
                        </v-card-text>
                      </v-list-item-content>
                    </v-list-item>
                  </v-card>
                </v-col>
                <v-col v-if="nationality" cols="12" sm="3" xs="12" class="pa-0">
                  <v-card class="ma-2 elevation-0 border_left">
                    <v-list-item class="px-4 py-1">
                      <v-list-item-content class="align-self-center py-0">
                        <v-card-text
                          class="mx-0 px-0 py-1 text-color text_nowrap"
                        >{{ $t("employee.Nationality") }}</v-card-text>
                        <v-card-text class="mx-0 px-0 py-1 font-weight-bold text-color">
                          {{
                          nationality
                          ? nationality["name_" + $i18n.locale]
                          : ""
                          }}
                        </v-card-text>
                      </v-list-item-content>
                    </v-list-item>
                  </v-card>
                </v-col>
                <v-col
                  cols="12"
                  sm="3"
                  xs="12"
                  class="pa-0"
                  v-for="(employee_phone, index) in employee_phones"
                  :key="'employee_phone' + index"
                >
                  <v-card class="ma-2 elevation-0 border_left">
                    <v-list-item class="px-4 py-1">
                      <v-list-item-content class="align-self-center py-0">
                        <v-card-text
                          class="mx-0 px-0 py-1 text-color text_nowrap"
                        >{{ $t("employee.phone_number") }}</v-card-text>
                        <v-card-text class="mx-0 px-0 py-1 font-weight-bold text-color">
                          {{
                          employee_phone.phone_number
                          ? employee_phone.phone_number
                          : ""
                          }}
                        </v-card-text>
                      </v-list-item-content>
                    </v-list-item>
                  </v-card>
                </v-col>
                <v-col v-if="employee.user !== null" cols="12" sm="3" xs="12" class="pa-0">
                  <v-card class="ma-2 elevation-0 border_left">
                    <v-list-item class="px-4 py-1">
                      <v-list-item-content class="align-self-center py-0">
                        <v-card-text
                          class="mx-0 px-0 py-1 text-color text_nowrap"
                        >{{ $t("profile.email") }}</v-card-text>
                        <v-card-text class="mx-0 px-0 py-1 font-weight-bold text-color">
                          {{
                          employee.user && employee.user.email
                          ? employee.user.email
                          : ""
                          }}
                        </v-card-text>
                      </v-list-item-content>
                    </v-list-item>
                  </v-card>
                </v-col>
                <v-col v-if="employee.INN" cols="12" sm="3" xs="12" class="pa-0">
                  <v-card class="ma-2 elevation-0 border_left">
                    <v-list-item class="px-4 py-1">
                      <v-list-item-content class="align-self-center py-0">
                        <v-card-text class="mx-0 px-0 py-1 text-color text_nowrap">INN</v-card-text>
                        <v-card-text
                          class="mx-0 px-0 py-1 font-weight-bold text-color"
                        >{{ employee.INN ? employee.INN : "" }}</v-card-text>
                      </v-list-item-content>
                    </v-list-item>
                  </v-card>
                </v-col>
                <v-col v-if="employee.INPS" cols="12" sm="3" xs="12" class="pa-0">
                  <v-card class="ma-2 elevation-0 border_left">
                    <v-list-item class="px-4 py-1">
                      <v-list-item-content class="align-self-center py-0">
                        <v-card-text class="mx-0 px-0 py-1 text-color text_nowrap">INPS</v-card-text>
                        <v-card-text
                          class="mx-0 px-0 py-1 font-weight-bold text-color"
                        >{{ employee.INPS ? employee.INPS : "" }}</v-card-text>
                      </v-list-item-content>
                    </v-list-item>
                  </v-card>
                </v-col>
                <v-col v-if="employee.enter_order_date" cols="12" sm="3" xs="12" class="pa-0">
                  <v-card class="ma-2 elevation-0 border_left">
                    <v-list-item class="px-4 py-1">
                      <v-list-item-content class="align-self-center py-0">
                        <v-card-text
                          class="mx-0 px-0 py-1 text-color text_nowrap"
                        >{{ $t("profile.order_date") }}</v-card-text>
                        <v-card-text class="mx-0 px-0 py-1 font-weight-bold text-color">
                          {{
                          employee.enter_order_date
                          ? employee.enter_order_date
                          : ""
                          }}
                        </v-card-text>
                      </v-list-item-content>
                    </v-list-item>
                  </v-card>
                </v-col>
                <v-col v-if="employee.enter_order_number" cols="12" sm="3" xs="12" class="pa-0">
                  <v-card class="ma-2 elevation-0 border_left">
                    <v-list-item class="px-4 py-1">
                      <v-list-item-content class="align-self-center py-0">
                        <v-card-text
                          class="mx-0 px-0 py-1 text-color text_nowrap"
                        >{{ $t("profile.order_number") }}</v-card-text>
                        <v-card-text class="mx-0 px-0 py-1 font-weight-bold text-color">
                          {{
                          employee.enter_order_number
                          ? employee.enter_order_number
                          : ""
                          }}
                        </v-card-text>
                      </v-list-item-content>
                    </v-list-item>
                  </v-card>
                </v-col>
                <v-col v-if="employee.first_work_date" cols="12" sm="3" xs="12" class="pa-0">
                  <v-card class="ma-2 elevation-0 border_left">
                    <v-list-item class="px-4 py-1">
                      <v-list-item-content class="align-self-center py-0">
                        <v-card-text
                          class="mx-0 px-0 py-1 text-color text_nowrap"
                        >{{ $t("profile.first_work_date") }}</v-card-text>
                        <v-card-text class="mx-0 px-0 py-1 font-weight-bold text-color">
                          {{
                          employee.first_work_date
                          ? employee.first_work_date
                          : ""
                          }}
                        </v-card-text>
                      </v-list-item-content>
                    </v-list-item>
                  </v-card>
                </v-col>
              </v-row>
            </v-card>
            <v-card class="pa-2 mb-5 mx-4" v-if="employee_addresses.length">
              <v-card-text
                class="font-weight-bold py-0 pr-0 pl-2"
                color="black"
              >{{ $t("profile.adress") }}</v-card-text>
              <v-simple-table>
                <template v-slot:default>
                  <thead>
                    <tr>
                      <th class="font-weight-bold">{{ $t("employee.address_type_id") }}</th>
                      <th class="font-weight-bold">{{ $t("employee.country") }}</th>
                      <th class="font-weight-bold">{{ $t("employee.region") }}</th>
                      <th class="font-weight-bold">{{ $t("employee.district") }}</th>
                      <th class="font-weight-bold">{{ $t("employee.street_address") }}</th>
                      <th class="font-weight-bold">{{ $t("employee.home_address") }}</th>
                      <th class="font-weight-bold">{{ $t("employee.description") }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(itm, ind) in employee_addresses" :key="ind">
                      <td class v-if="itm.address_type_id == 1">{{ $t("employee.birth_address") }}</td>
                      <td
                        class
                        v-else-if="itm.address_type_id == 2"
                      >{{ $t("employee.residence_address") }}</td>
                      <td class>
                        {{
                        itm.country ? itm.country["name_" + $i18n.locale] : ""
                        }}
                      </td>
                      <td class>
                        {{
                        itm.region ? itm.region["name_" + $i18n.locale] : ""
                        }}
                      </td>
                      <td class>
                        {{
                        itm.district
                        ? itm.district["name_" + $i18n.locale]
                        : ""
                        }}
                      </td>
                      <td class>{{ itm["street_address_" + $i18n.locale] }}</td>
                      <td class>{{ itm["home_address_" + $i18n.locale] }}</td>
                      <td class>{{ itm.description ? itm.description : "" }}</td>
                    </tr>
                  </tbody>
                </template>
              </v-simple-table>
            </v-card>
            <v-card
              class="pa-2 mb-5 mx-4"
              v-for="(itm, ind) in employee.employee_official_document"
              :key="'official_document' + ind"
            >
              <v-card-text
                class="font-weight-bold py-0 pr-0 pl-2"
                color="black"
              >{{ itm.official_document_type["name_" + $i18n.locale] }}</v-card-text>
              <v-simple-table>
                <template v-slot:default>
                  <thead>
                    <tr>
                      <th class="text-left">{{ $t("employeeDocument.series") }}</th>
                      <th class="text-left">{{ $t("employeeDocument.number") }}</th>
                      <th class="text-left">{{ $t("employeeDocument.given_by") }}</th>
                      <th class="text-left">{{ $t("employeeDocument.date_issue") }}</th>
                      <th class="text-left">{{ $t("employeeDocument.valid_until") }}</th>
                      <th class="text-left">{{ $t("employeeDocument.status") }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>{{ itm.series ? itm.series : "" }}</td>
                      <td>{{ itm.number ? itm.number : "" }}</td>
                      <td>
                        {{
                        itm.given_organization ? itm.given_organization : ""
                        }}
                      </td>
                      <td>{{ itm.given_date ? itm.given_date : "" }}</td>
                      <td>{{ itm.due_date ? itm.due_date : "" }}</td>
                      <td>
                        {{
                        itm.is_active
                        ? $t("employee.active")
                        : $t("employee.inactive")
                        }}
                      </td>
                    </tr>
                  </tbody>
                </template>
              </v-simple-table>
            </v-card>
          </v-tab-item>
          <v-tab-item value="tab-2" class="background_tab-2 pa-4">
            <v-simple-table v-if="allDocuments" dense fixed-header>
              <template v-slot:default>
                <thead>
                  <tr>
                    <th class="text-left">№</th>
                    <th class="text-left">{{ $t("document.document_number") }}</th>
                    <th class="text-left">{{ $t("document.document_date") }}</th>
                    <th class="text-left">{{ $t("Template turi") }}</th>
                    <th class="text-left">{{ $t("document.document_type_id") }}</th>
                    <th class="text-left">{{ $t("document.status") }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, indAllDoc) in allDocuments" :key="indAllDoc">
                    <td v-if="item.status !== 0">{{ indAllDoc + 1 }}</td>
                    <td v-if="item.status !== 0">
                      <router-link
                        :to="'/documents/show-only-pdf/' + item.pdf_file_name"
                        style="text-decoration: none"
                      >{{ item.document_number }}</router-link>
                    </td>
                    <td v-if="item.status !== 0">{{ item.document_date }}</td>
                    <td
                      v-if="item.status !== 0"
                    >{{ item.document_template["name_" + $i18n.locale] }}</td>
                    <td v-if="item.status !== 0">{{ item.document_type["name_" + $i18n.locale] }}</td>
                    <td v-if="item.status !== 0">
                      <span v-if="item.status == 0">
                        {{
                        document_status[item.status]
                        ? document_status[item.status][
                        "name_" + $i18n.locale
                        ]
                        : ""
                        }}
                      </span>
                      <span v-if="item.status == 1">
                        {{
                        document_status[item.status]
                        ? document_status[item.status][
                        "name_" + $i18n.locale
                        ]
                        : ""
                        }}
                      </span>
                      <span v-if="item.status == 2">
                        {{
                        document_status[item.status]
                        ? document_status[item.status][
                        "name_" + $i18n.locale
                        ]
                        : ""
                        }}
                      </span>
                      <span v-if="item.status == 3">
                        {{
                        document_status[item.status]
                        ? document_status[item.status][
                        "name_" + $i18n.locale
                        ]
                        : ""
                        }}
                      </span>
                      <span v-if="item.status == 4">
                        {{
                        document_status[item.status]
                        ? document_status[item.status][
                        "name_" + $i18n.locale
                        ]
                        : ""
                        }}
                      </span>
                      <span v-if="item.status == 5">
                        {{
                        document_status[item.status]
                        ? document_status[item.status][
                        "name_" + $i18n.locale
                        ]
                        : ""
                        }}
                      </span>
                      <span v-if="item.status == 6">
                        {{
                        document_status[item.status]
                        ? document_status[item.status][
                        "name_" + $i18n.locale
                        ]
                        : ""
                        }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>
            <v-card v-else class="background_tab-3 py-8" color="basil" flat>
              <v-alert
                class="d-flex justify-center rounded-0 py-5"
                text
                type="error"
                icon="mdi-alert-outline"
              >{{ $t("noDataText") }}</v-alert>
            </v-card>
          </v-tab-item>
          <v-tab-item value="tab-3" class="background_tab-3 pa-4">
            <v-simple-table v-if="employee_relatives" fixed-header>
              <template v-slot:default>
                <thead>
                  <tr>
                    <th class="text-left">#</th>
                    <th class="text-left">{{ $t("employee.lastname") }}</th>
                    <th class="text-left">{{ $t("employee.firstname") }}</th>
                    <th class="text-left">{{ $t("employee.middlename") }}</th>
                    <th class="text-left">{{ $t("employee.born_date") }}</th>
                    <th class="text-left">{{ $t("employee.work_place") }}</th>
                    <th class="text-left">{{ $t("employee.living_place") }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(it, eRelative) in employee_relatives" :key="eRelative">
                    <td class>{{ eRelative + 1 }}</td>
                    <td class>{{ it.last_name ? it.last_name : "" }}</td>
                    <td class>{{ it.first_name ? it.first_name : "" }}</td>
                    <td class>{{ it.middle_name ? it.middle_name : "" }}</td>
                    <td class>{{ it.born_date ? it.born_date : "" }}</td>
                    <td class>{{ it.work_place ? it.work_place : "" }}</td>
                    <td class>{{ it.living_place ? it.living_place : "" }}</td>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>
            <v-card v-else class="background_tab-3 py-8" color="basil" flat>
              <v-alert
                class="d-flex justify-center rounded-0 py-5"
                text
                type="error"
                icon="mdi-alert-outline"
              >{{ $t("noDataText") }}</v-alert>
            </v-card>
          </v-tab-item>
          <v-tab-item value="tab-4" class="background_tab-4">
            <v-card
              v-if="historys.length"
              class="py-8"
              color="basil"
              flat
              style="background: transparent"
            >
              <v-row class="ma-0">
                <v-col cols="12" md="2"></v-col>
                <v-col cols="12" sm="6" md="8">
                  <template>
                    <v-timeline>
                      <v-timeline-item v-for="(history, index) in historys" :key="index" small>
                        <template v-slot:icon>
                          <v-avatar class="elevation-4">
                            <img
                              v-if="employee.base64"
                              :src="
                                'data:application/jpg;base64,' + employee.base64
                              "
                            />
                            <img v-else src="../../assets/User-Default.jpg" />
                          </v-avatar>
                        </template>
                        <template v-slot:opposite>
                          <span>
                            {{ history.enter_order_date }}
                            {{ history.leave_order_date }}
                          </span>
                        </template>
                        <v-card class="elevation-2" v-if="history.staff.department">
                          <v-card-title class="headline subtitle-1 font-weight-bold pb-2">
                            <span class="font-weight-medium mr-2">
                              {{
                              history.staff.department.department_code
                              ? history.staff.department.department_code
                              : ""
                              }}
                            </span>
                            {{
                            history.staff.department["name_" + $i18n.locale]
                            }}
                          </v-card-title>
                          <v-card-text
                            class="subtitle-2 font-weight-medium"
                            v-if="history.staff.position"
                          >
                            {{
                            history.staff.position["name_" + $i18n.locale]
                            }}
                          </v-card-text>
                        </v-card>
                      </v-timeline-item>
                    </v-timeline>
                  </template>
                </v-col>
                <v-col cols="12" md="2"></v-col>
              </v-row>
            </v-card>
            <v-card
              v-else
              class="py-8"
              color="basil"
              flat
              style="background: transparent; color:red; text-align:center;"
            >{{ $t("noDataText") }}</v-card>
          </v-tab-item>
          <v-tab-item value="tab-5" class="background_tab-3 pa-4">
            <v-simple-table v-if="employee_work_histories" fixed-header>
              <template v-slot:default>
                <thead>
                  <tr>
                    <th class="text-left">#</th>
                    <th class="text-left">{{ $t("start_date") }}</th>
                    <th class="text-left">{{ $t("end_date") }}</th>
                    <th class="text-left">{{ $t("employee.work_place") }}</th>
                    <th class="text-left">{{ $t("employee.position") }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(itmw, eWork) in employee_work_histories" :key="eWork">
                    <td class>{{ eWork + 1 }}</td>
                    <td class>{{ itmw.begin_date ? itmw.begin_date : "" }}</td>
                    <td class>{{ itmw.end_date ? itmw.end_date : "" }}</td>
                    <td class>{{ itmw.work_place ? itmw.work_place : "" }}</td>
                    <td class>{{ itmw.position ? itmw.position : "" }}</td>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>
            <!-- <v-card v-else class="background_tab-3 py-8" color="basil" flat>
              <v-alert
                class="d-flex justify-center rounded-0 py-5"
                text
                type="error"
                icon="mdi-alert-outline"
                >{{ $t("noDataText") }}</v-alert
              >
            </v-card>-->
          </v-tab-item>
          <v-tab-item value="tab-6" class="background_tab-3 pa-4">
            <v-simple-table v-if="employee_education_histories" fixed-header>
              <template v-slot:default>
                <thead>
                  <tr>
                    <th class="text-left">#</th>
                    <th class="text-left">{{ $t("university") }}</th>
                    <th class="text-left">{{ $t("study_type") }}</th>
                    <th class="text-left">{{ $t("major") }}</th>
                    <th class="text-left">{{ $t("start_date") }}</th>
                    <th class="text-left">{{ $t("end_date") }}</th>
                    <th class="text-left">{{ $t("university_address") }}</th>
                    <th class="text-left">{{ $t("academic_title") }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(itme, eEdu) in employee_education_histories" :key="eEdu">
                    <td class>{{ eEdu + 1 }}</td>
                    <td class>
                      {{
                      itme.university
                      ? itme.university["name_" + $i18n.locale]
                      : ""
                      }}
                    </td>
                    <td class>
                      {{
                      itme.study_type
                      ? itme.study_type["name_" + $i18n.locale]
                      : ""
                      }}
                    </td>
                    <td class>{{ itme.major ? itme.major["name_" + $i18n.locale] : "" }}</td>
                    <td class>{{ itme.begin_date ? itme.begin_date : "" }}</td>
                    <td class>{{ itme.end_date ? itme.end_date : "" }}</td>
                    <td class>
                      {{
                      itme.university_address ? itme.university_address : ""
                      }}
                    </td>
                    <td class>{{ itme.academic_title ? itme.academic_title : "" }}</td>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>
            <!-- <v-card v-else class="background_tab-3 py-8" color="basil" flat>
              <v-alert
                class="d-flex justify-center rounded-0 py-5"
                text
                type="error"
                icon="mdi-alert-outline"
                >{{ $t("noDataText") }}</v-alert
              >
            </v-card>-->
          </v-tab-item>
          <v-tab-item value="tab-7" class="pa-4" v-if="pdfBase64">
            <iframe :src="'data:application/pdf;base64,' + pdfBase64" height="1000" width="100%"></iframe>
          </v-tab-item>
        </v-tabs-items>
      </v-col>
    </v-row>
    <v-dialog
      v-model="eImzoDialog"
      scrollable
      persistent
      @keydown.esc="eImzoDialog = false"
      :overlay="false"
      max-width="650px"
      transition="dialog-transition"
    >
      <v-card style="height: 100%">
        <v-card-title class="headline grey lighten-2" primary-title>
          {{ $t("profile.choose_key") }}
          <v-spacer></v-spacer>
          <v-btn color="red" dark x-small fab class @click="eImzoDialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-row class="ma-0">
          <v-col cols="12" md="12">
            <v-card-text>
              <v-card-text>
                <label v-show="false" id="message"></label>
                <v-form name="testform">
                  <v-row>
                    <v-col cols="12" class="my-0 py-0">
                      {{ $t("profile.select_key") }}
                      <br />
                      <select
                        name="key"
                        @change="cbChanged(this)"
                        style="border: 1px solid black"
                        class="pa-2 v-input__control"
                      ></select>
                      <br />
                      <label v-show="false" id="keyId"></label>
                    </v-col>
                    <v-col cols="12" class="mt-0 mb-6 py-0">
                      <v-btn block color="#203d5b" dark @click="push()">
                        {{ $t("profile.push") }}
                        <v-progress-circular v-if="loading" indeterminate :width="3" :size="18"></v-progress-circular>
                      </v-btn>
                    </v-col>
                  </v-row>
                </v-form>
              </v-card-text>
            </v-card-text>
          </v-col>
        </v-row>
        <v-card-actions>
          <v-spacer></v-spacer>
          <!-- <v-btn color="primary" text @click="dialog = false">{{$t('save')}}</v-btn> -->
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="roleDialog"
      scrollable
      persistent
      @keydown.esc="roleDialog = false"
      :overlay="false"
      max-width="650px"
      transition="dialog-transition"
    >
      <v-card style="height: 100%">
        <v-card-title class="headline grey lighten-2" primary-title>
          {{ $t("user.roles") }}
          <v-spacer></v-spacer>
          <v-btn color="red" dark x-small fab class @click="roleDialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-row class="ma-0">
          <v-col cols="12" md="12">
            <v-card-text class="pa-2">
              <!-- {{ $t("profile.role") }} -->
              <span v-for="(role, i) in roles" :key="i" class="font-weight-bold">
                <v-chip color="white" class="pa-1 ma-0">
                  {{
                  role.name + "," + " "
                  }}
                </v-chip>
              </span>
            </v-card-text>
          </v-col>
        </v-row>
        <v-card-actions>
          <v-spacer></v-spacer>
          <!-- <v-btn color="primary" text @click="dialog = false">{{$t('save')}}</v-btn> -->
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="dialogTask" @keydown.esc="dialogTask = false" persistent max-width="800px">
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">{{ dialogHeaderText }}</span>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="dialogTask = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form @keyup.native.enter="save" ref="dialogForm">
            <v-row class="ma-0 pa-0">
              <v-col cols="6" sm="4" class="ma-0 pa-1">
                <label for>{{ $t("profile.task_name") }}</label>
                <v-text-field
                  v-model="form.task"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" sm="4" class="ma-0 pa-1">
                <label for>{{ $t("profile.begin_date") }}</label>
                <v-menu
                  v-model="createdAtMenu"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="290px"
                >
                  <template v-slot:activator="{ on }">
                    <v-text-field
                      v-model="form.begin_date"
                      :rules="[(v) => !!v || $t('input.required')]"
                      readonly
                      v-on="on"
                      hide-details="auto"
                      dense
                      outlined
                    ></v-text-field>
                  </template>
                  <v-date-picker v-model="form.begin_date" @input="createdAtMenu = false"></v-date-picker>
                </v-menu>
              </v-col>
              <v-col cols="6" sm="4" class="ma-0 pa-1">
                <label for>{{ $t("profile.end_date") }}</label>
                <v-menu
                  v-model="createdAtMenu1"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="290px"
                >
                  <template v-slot:activator="{ on }">
                    <v-text-field
                      v-model="form.due_date"
                      :rules="[(v) => !!v || $t('input.required')]"
                      readonly
                      v-on="on"
                      hide-details="auto"
                      dense
                      outlined
                    ></v-text-field>
                  </template>
                  <v-date-picker v-model="form.due_date" @input="createdAtMenu1 = false"></v-date-picker>
                </v-menu>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="save">{{ $t("save") }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="dialogTaskComment"
      @keydown.esc="dialogTaskComment = false"
      persistent
      max-width="600"
    >
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">{{ dialogHeaderText }}</span>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="dialogTaskComment = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form @keyup.native.enter="saveTaskComment">
            <v-row>
              <v-col cols="12">
                <label for>{{ $t("profile.description") }}</label>
                <v-text-field
                  v-model="form.description"
                  :rules="[(v) => !!v || $t('input_required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col
                cols="12"
                style="min-width: 100px; max-width: 100%"
                class="flex-grow-1 flex-shrink-0"
              >
                <label for>{{ $t("uploadFiles") }}</label>
                <v-file-input
                  v-model="file"
                  :rules="[
                    (v) => {
                      let allowedExtensions = /(\.pdf)$/i;
                      let error = false;
                      (v) => {
                        if (!allowedExtensions.exec(file.name)) {
                          error = true;
                        }
                      };
                      return !error || $t('requiredpdf');
                    },
                    (v) => !!v || $t('input.required'),
                  ]"
                  outlined
                  dense
                  prepend-icon
                  append-icon="mdi-file-pdf-box-outline"
                  accept=".pdf, application/pdf"
                  small-chips
                  show-size
                  hide-details="auto"
                ></v-file-input>
              </v-col>
            </v-row>
          </v-form>
          <small color="red">{{ $t("input_required") }}</small>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="saveTaskComment">
            {{
            $t("save")
            }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="pdfViewDialog" fullscreen>
      <v-card>
        <v-card-title primary-title>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined @click="pdfViewDialog = false">
            {{
            $t("close")
            }}
          </v-btn>
        </v-card-title>
        <v-card-text class="d-flex justify-center">
          <iframe
            width="100%"
            :height="830"
            :src="$store.state.backend_url + 'staffs/get-file/' + fileForView"
          ></iframe>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined @click="pdfViewDialog = false" class="mr-4">{{ $t("close") }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
const axios = require("axios").default;
import Swal from "sweetalert2";
export default {
  data() {
    return {
      user: null,
      eImzoDialog: false,
      base64: "",
      employee: {
        base64: null
      },
      pdfBase64: null,
      staff: {},
      roles: [],
      employee_addresses: [],
      employee_coefficients: [],
      employee_official_document: [],
      employee_phones: [],
      employee_relatives: [],
      employee_work_histories: [],
      employee_education_histories: [],
      employee_id: "",
      employee_tabel: "",
      dialogHeaderText: "",
      tariff_scale: {},
      nationality: {},
      company: {},
      historys: [],
      allDocuments: [],
      form: {},
      employeeTasks: [],
      dialogTask: false,
      selectFiles: [],
      file: null,
      documentFiles: [],
      formData: [],
      dialogTaskComment: false,
      fileForView: null,
      pdfViewDialog: false,
      singleExpand: false,
      expanded: [],
      items: [],
      createdAtMenu: false,
      createdAtMenu1: false,
      loading: false,
      tab: null,
      eimzo_username: "",
      eimzo_name: "",
      eimzo_password: "",
      eimzo_inn: "",
      eimzo_given_date: "",
      eimzo_expere_date: "",
      roleDialog: false,
      document_status: [
        {
          id: 0,
          name_uz_latin: "Yangi",
          name_uz_cyril: "Янги",
          name_ru: "Новый"
        },
        {
          id: 1,
          name_uz_latin: "E'lon qilindi",
          name_uz_cyril: "Эьлон қилинди",
          name_ru: "Опубликованный"
        },
        {
          id: 2,
          name_uz_latin: "Qayta ishlash",
          name_uz_cyril: "қайта ишлаш",
          name_ru: "Обработка"
        },
        {
          id: 3,
          name_uz_latin: "Imzolandi",
          name_uz_cyril: "Имзоланди",
          name_ru: "Подписано"
        },
        {
          id: 4,
          name_uz_latin: "Bajarildi",
          name_uz_cyril: "Бажарилди",
          name_ru: "Выполнено"
        },
        {
          id: 5,
          name_uz_latin: "Yakunlandi",
          name_uz_cyril: "Якунланди",
          name_ru: "Завершено"
        },
        {
          id: 6,
          name_uz_latin: "Bekor qilindi",
          name_uz_cyril: "Бекор қилинди",
          name_ru: "Отменен"
        }
      ]
    };
  },
  computed: {
    // screenHeight() {
    //   return window.innerHeight - 175;
    // },
    headers() {
      return [
        {
          text: "",
          value: "data-table-expand",
          width: 30
        },
        { text: "#", value: "id", width: 30 },
        {
          text: this.$t("profile.task_name"),
          value: "task"
        },
        { text: this.$t("profile.begin_date"), value: "begin_date" },
        { text: this.$t("profile.end_date"), value: "due_date" },
        {
          text: this.$t("actions"),
          value: "actions",
          width: 180,
          align: "center"
        }
      ].filter(
        v =>
          v.value != "actions" ||
          this.$store.getters.checkPermission("critical-update") ||
          this.$store.getters.checkPermission("critical-delete")
      );
    }
  },
  methods: {
    viewFile(file_id) {
      this.fileForView = file_id;
      this.pdfViewDialog = true;
    },
    getUser(id) {
      axios
        .get(
          this.$store.state.backend_url + "api/employees/show-employee/" + id
        )
        .then(res => {
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
        .catch(e => {
          console.error(e);
        });
    },
    getAvatar(id) {
      axios
        .get(this.$store.state.backend_url + "api/employees/get-avatar/" + id)
        .then(response => {
          this.employee.base64 = response.data;
          this.base64 = response.data;
        })
        .catch(error => {
          console.log(error);
        });
    },
    getUserDocument() {
      axios
        .get(
          this.$store.state.backend_url +
            "api/user-documents/" +
            this.$route.params.id
        )
        .then(res => {
          this.allDocuments = res.data;
          // console.log("allDocuments", this.allDocuments);
        })
        .catch(e => {
          console.error(e);
        });
    },
    getEmployeeTask() {
      axios
        .get(
          this.$store.state.backend_url +
            "api/employee-traning-tasks/" +
            this.$route.params.id
        )
        .then(response => {
          // this.employeeTasks = response.data;
          this.items = response.data;
          console.log("111", this.employeeTasks);
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
          this.loading = false;
        });
    },
    newEmployeeTask() {
      this.dialogTask = true;
      this.dialogHeaderText = this.$t("profile.add_task");
      this.form = {
        id: Date.now(),
        employee_id: this.employee_id,
        task: "",
        begin_date: "",
        due_date: ""
      };

      this.editMode = false;
    },
    editEmployeeTask(item) {
      this.dialogHeaderText = this.$t("Tahrirlash");
      // this.formIndex = this.items.indexOf(item);
      this.form = Object.assign({}, item);
      this.dialogTask = true;
      this.editMode = true;
    },
    save() {
      axios
        .post(
          this.$store.state.backend_url + "api/employee-traning-tasks/update",
          this.form
        )
        .then(res => {
          this.getEmployeeTask();
          this.dialogTask = false;
          const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            onOpen: toast => {
              toast.addEventListener("mouseenter", Swal.stopTimer);
              toast.addEventListener("mouseleave", Swal.resumeTimer);
            }
          });

          Toast.fire({
            icon: "success",
            title: this.$t("create_update_operation")
          });
        })
        .catch(err => {
          console.log(err);
        });
    },
    deleteEmployeeTask(item) {
      Swal.fire({
        title: this.$t("swal_title"),
        text: this.$t("swal_text"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: this.$t("swal_delete")
      }).then(result => {
        if (result.value) {
          axios
            .delete(
              this.$store.state.backend_url +
                "api/employee-traning-tasks/delete/" +
                item.id
            )
            .then(res => {
              this.getEmployeeTask();
              this.dialog = false;
              Swal.fire("Deleted!", this.$t("swal_deleted"), "success");
            })
            .catch(err => {
              Swal.fire({
                icon: "error",
                title: this.$t("swal_error_title"),
                text: this.$t("swal_error_text")
                //footer: "<a href>Why do I have this issue?</a>"
              });
              console.log(err);
            });
        }
      });
    },
    newTaskComment(item) {
      this.dialogHeaderText = this.$t("Topshiriq bo'yicha qilingan ishlar");
      this.form = {
        id: Date.now(),
        employee_traning_task_id: item,
        description: ""
      };
      this.dialogTaskComment = true;
      this.editMode = false;
      if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
    },
    saveTaskComment() {
      this.formData = new FormData();
      this.loading = true;
      this.formData.append("file", this.file);
      this.formData.append("id", this.form.id);
      this.formData.append(
        "employee_traning_task_id",
        this.form.employee_traning_task_id
      );
      this.formData.append("description", this.form.description);
      axios
        .post(
          this.$store.state.backend_url + "api/task-comment/update",
          this.formData,
          {
            headers: {
              "Content-Type": "multipart/form-data"
            }
          }
        )
        .then(res => {
          this.loading = false;
          this.dialogTaskComment = false;
          this.getEmployeeTask();
          this.getList();
        })
        .catch(err => {
          console.log("err =" + err);
          this.loading = false;
        });
    },
    deleteTaskComment(item) {
      Swal.fire({
        title: this.$t("swal_title"),
        text: this.$t("swal_text"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: this.$t("swal_delete")
      }).then(result => {
        if (result.value) {
          axios
            .delete(
              this.$store.state.backend_url +
                "api/task-comment/delete/" +
                item.id
            )
            .then(res => {
              this.getEmployeeTask();
              this.dialog = false;
              Swal.fire("Deleted!", this.$t("swal_deleted"), "success");
            })
            .catch(err => {
              Swal.fire({
                icon: "error",
                title: this.$t("swal_error_title"),
                text: this.$t("swal_error_text")
                //footer: "<a href>Why do I have this issue?</a>"
              });
              console.log(err);
            });
        }
      });
    },
    staffHistory(employee_id) {
      // console.log(this.employee_id);
      axios
        .post(this.$store.state.backend_url + "api/employe-staff/history", {
          employee_id: employee_id
        })
        .then(res => {
          this.historys = res.data;
        })
        .catch(err => {
          console.log(err);
        });
    },
    cbChanged(c) {
      if (document.getElementById("keyId"))
        document.getElementById("keyId").innerHTML = "";
      this.getUserAuth();
    },
    getUserAuth() {
      var itm = document.testform.key.value;
      var id = document.getElementById(itm);
      if (id && id.hasAttribute("vo")) {
        var vo = JSON.parse(id.getAttribute("vo"));
        this.eimzo_username = vo.name;
        this.eimzo_name = vo.CN;
        this.eimzo_password = vo.serialNumber;
        this.eimzo_inn = vo.TIN;
        this.eimzo_given_date = vo.validFrom;
        this.eimzo_expere_date = vo.validTo;
      } else {
        this.eimzo_username = "";
        this.eimzo_name = "";
        this.eimzo_password = "";
        this.eimzo_inn = "";
        this.eimzo_given_date = "";
        this.eimzo_expere_date = "";
      }
    },
    push() {
      axios
        .post(this.$store.state.backend_url + "api/users/eimzo-push", {
          eimzo_username: this.eimzo_username,
          eimzo_name: this.eimzo_name,
          eimzo_password: this.eimzo_password,
          eimzo_inn: this.eimzo_inn,
          eimzo_given_date: this.eimzo_given_date,
          eimzo_expere_date: this.eimzo_expere_date
        })
        .then(res => {
          Swal.fire({
            position: "top-end",
            icon: "success",
            title: "Your E-IMZO has been saved",
            showConfirmButton: false,
            timer: 1500
          });
        })
        .catch(err => {});
      this.eImzoDialog = false;
    },
    AppLoad() {
      // EIMZOClient.API_KEYS = [
      //   "edo.uzautomotors.com",
      //   "79DC56F42765A0017C31309DB9600EA924684ED023A8079460454768331626AB94CFFF8FC2D4007976D4A6C56F11D56DFA962276DC54AE8C0F39E8A8EBDFA10B"
      // ];
      EIMZOClient.API_KEYS = [
        this.$store.state.EIMZO_DOMAIN,
        this.$store.state.EIMZO_API_KEY
      ];
      this.uiLoading();
      let EIMZO_MAJOR = this.EIMZO_MAJOR;
      let EIMZO_MINOR = this.EIMZO_MINOR;
      let uiLoadKeys = this.uiLoadKeys;
      EIMZOClient.checkVersion(
        function(major, minor) {
          var newVersion = EIMZO_MAJOR * 100 + EIMZO_MINOR;
          var installedVersion = parseInt(major) * 100 + parseInt(minor);
          if (installedVersion < newVersion) {
            this.uiUpdateApp();
          } else {
            EIMZOClient.installApiKeys(
              function() {
                uiLoadKeys();
              },
              function(e, r) {
                if (r) {
                  this.uiShowMessage(r);
                } else {
                  this.wsError(e);
                }
              }
            );
          }
        },
        function(e, r) {
          if (r) {
            this.uiShowMessage(r);
          } else {
            this.uiNotLoaded(e);
          }
        }
      );
    },
    uiLoading() {
      var l = document.getElementById("message");
      if (l) {
        l.innerHTML = "Загрузка ...";
        l.style.color = "red";
      }
    },
    uiClearCombo() {
      var combo = document.testform.key;
      combo.length = 0;
    },
    uiCreateItem(itmkey, vo) {
      var now = new Date();
      vo.expired = dates.compare(now, vo.validTo) > 0;
      var itm = document.createElement("option");
      itm.value = itmkey;
      itm.text = vo.CN;
      if (!vo.expired) {
      } else {
        itm.style.color = "gray";
        itm.text = itm.text + " (срок истек)";
      }
      itm.setAttribute("vo", JSON.stringify(vo));
      itm.setAttribute("id", itmkey);
      return itm;
    },
    uiUpdateApp() {
      var l = document.getElementById("message");
      if (l) l.innerHTML = this.errorUpdateApp;
    },
    uiLoadKeys() {
      this.uiClearCombo();
      let uiCreateItem = this.uiCreateItem;
      let uiShowMessage = this.uiShowMessage;
      let eimzo_password = this.employee.eimzo_password;
      let getUserAuth = this.getUserAuth;
      EIMZOClient.listAllUserKeys(
        function(o, i) {
          var itemId = "itm-" + o.serialNumber + "-" + i;
          return itemId;
        },
        function(itemId, v) {
          return uiCreateItem(itemId, v);
        },
        function(items, firstId) {
          var combo = document.testform.key;
          var option = document.createElement("option");
          option.text = "select";
          combo.add(option);
          // combo.append(<option value="">Select</option>);
          for (var itm in items) {
            var vo = items[itm].getAttribute("vo");
            if (!JSON.parse(vo).expired) {
              combo.append(items[itm]);
            }
            if (vo.includes(eimzo_password)) {
              items[itm].setAttribute("selected", "true");
              getUserAuth();
            }
          }
          // if (firstId) {
          //   var id = document.getElementById(firstId);
          //   id.setAttribute("selected", "true");
          // }
        },
        function(e, r) {
          // uiShowMessage(this.errorCAPIWS);
        }
      );
    },
    uiShowMessage(message) {
      alert(message);
    },
    wsError(e) {
      if (e) {
        this.uiShowMessage(this.errorCAPIWS + " : " + e);
      } else {
        this.uiShowMessage(this.errorBrowserWS);
      }
    },
    uiNotLoaded(e) {
      var l = document.getElementById("message");
      if (l) {
        l.innerHTML = "";
        if (e) {
          this.wsError(e);
        } else {
          this.uiShowMessage(this.errorBrowserWS);
        }
      }
    }
  },
  watch: {
    eImzoDialog(value) {
      if (value) this.AppLoad();
    }
  },
  mounted() {
    this.user = JSON.parse(window.localStorage.getItem("user"));
    if (this.$route.params.id) {
      this.getUser(this.$route.params.id);
      this.getAvatar(this.$route.params.id);

      axios
        .get(
          this.$store.state.backend_url +
            "api/employees/get-pdf/" +
            this.$route.params.id +
            "/" +
            this.$i18n.locale
        )
        .then(res => {
          this.pdfBase64 = res.data;
        })
        .catch(e => {
          console.error(e);
        });
    }
    this.getUserDocument();
    this.getEmployeeTask();
    // this.staffHistory();
  }
};
</script>
<style scoped>
.profile_header {
  background-image: url("../../assets/bgoverly.jpg");
  background-repeat: no-repeat;
  background-size: cover;
  background-position: center;
  background-color: rgba(0, 0, 0, 0.5);
}
.profile_name {
  color: #fff;
  font-size: 28px;
  line-height: 1.1;
  font-weight: 500;
  font-family: "Roboto", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.profile_staff {
  color: #ffffee !important;
  /* opacity: 0.6; */
  font-size: 15px;
  font-weight: 500;
  font-family: "Roboto", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.fullHeight {
  height: 100%;
}
.border_left {
  border-left: 3px solid #009688;
  border-radius: 0px;
  background: #f7f7f7;
}
.text-color {
  color: rgba(0, 0, 0, 0.87);
}
.background_tab-2 {
  background-image: url("../../assets/background.jpg");
  background-repeat: no-repeat;
  background-size: cover;
  background-position: center;
  /* height: calc(100vh - 288px); */
  border-radius: 0;
}
.background_tab-3 {
  background-image: url("../../assets/bg.jpg");
  background-repeat: no-repeat;
  background-size: cover;
  background-position: center;
  height: calc(100vh - 288px);
  border-radius: 0;
}
.background_tab-4 {
  background-image: url("../../assets/background.jpg");
  background-repeat: no-repeat;
  background-size: cover;
  background-position: center;
  height: calc(100vh - 288px);
  border-radius: 0;
}
.background-overlay {
  background-color: rgba(0, 0, 0, 0.4);
}
</style>
