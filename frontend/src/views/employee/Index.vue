<template>
  <div class="fullHeight">
    <v-card 
      class="heightFull" 
      :disabled="loading" 
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0">
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{ $t("employee.index") }}</span>
        <div class="headerSearch d-flex align-center">
          <v-text-field
            v-model="search"
            append-icon="mdi-magnify"
            class="txt_search1"
            style="width: 100px !important"
            :placeholder="$t('search')"
            @keyup.native.enter="getList"
            dense
            hide-details
            solo
          ></v-text-field>
          <v-menu
            transition="slide-y-transition"
            left
            
            :close-on-content-click="false"
            :nudge-width="50"
            offset-y
          >
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                class="txt_searchBtn ml-2"
                outlined
                v-bind="attrs"
                v-on="on"
              >
                <v-icon size="18" color="white"
                  >mdi-format-list-bulleted</v-icon
                >
              </v-btn>
            </template>
            <v-card>
              <v-list class="dropdown-list pa-0">
                <v-list-item
                  style="margin: 0px; max-height: 34px; min-height: 34px"
                  @click="newItem"
                >
                  <v-list-item-title>
                    <v-icon size="18">mdi-plus</v-icon>Добавить новую строку
                  </v-list-item-title>
                </v-list-item>
                <v-list-item
                  style="margin: 0px; max-height: 34px; min-height: 34px"
                  @click="0"
                >
                  <v-list-item-title
                    @click="
                        getEmployeeExcel(1);
                        employee_excel = [];
                      "
                  >
                    <v-icon color="#107C41" size="18"
                      >mdi-microsoft-excel</v-icon
                    >
                    Скачать таблицу Excel
                  </v-list-item-title></v-list-item
                >
              </v-list>
            </v-card>
          </v-menu>
        </div>
       <!-- <v-btn
          class="mr-2"
          color
          outlined
          x-small
          fab
          @click="
            filterDialog = true;
            search = '';
          "
        >
          <v-icon>mdi-magnify</v-icon>
        </v-btn>
        <v-btn
          outlined
          x-small
          fab
          @click="
            getEmployeeExcel(1);
            employee_excel = [];
          "
          class="mr-2"
        >
          <v-icon>mdi-file-excel-outline</v-icon>
        </v-btn>
        <v-btn color="#6ac82d" x-small dark fab @click="newItem">
          <v-icon>mdi-plus</v-icon>
        </v-btn> -->
      </v-card-title>
      <!-- Main employee table -->
      <v-row class="mx-0">
        <v-col class="ma-0 pa-0" xs="12">
          <v-data-table
            class="doc-template_data-table noelevation"
            dense
            style="width: 100%; height: 100%; border-radius: 10px"
            fixed-header
            :loading-text="$t('loadingText')"
            :no-data-text="$t('noDataText')"
            :height="screenHeight"
            :loading="loading"
            :headers="headers"
            :items="items"
            single-expand
            :expanded="expanded"
            @item-expanded="getAvatar"
            item-key="id"
            show-expand
            :server-items-length="server_items_length"
            :options.sync="dataTableOptions"
            :disable-pagination="true"
            disable-sort
            :footer-props="{
              itemsPerPageOptions: [50, 100, 200],
              itemsPerPageAllText: $t('itemsPerPageAllText'),
              itemsPerPageText: $t('itemsPerPageText'),
              showFirstLastPage: true,
              firstIcon: 'mdi-arrow-collapse-left',
              lastIcon: 'mdi-arrow-collapse-right',
              prevIcon: 'mdi-arrow-left',
              nextIcon: 'mdi-arrow-right',
            }"
            @update:page="updatePage"
            @update:items-per-page="updatePerPage"
            @update:expanded="toggleExpand"
            @dblclick:row="rowClick"
          >
            <template v-slot:body.prepend>
              <tr class="py-0 my-0 prepend_height">
                <td class="py-0 my-0 dense"></td>
                <td class="py-0 my-0 dense"></td>
                <td class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filterForm.department_code"
                    type="number"
                    hide-details
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense" style="width: 50px">
                  <v-text-field
                    v-model="filterForm.tabel"
                    type="text"
                    hide-details
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filterForm.info"
                    type="text"
                    hide-details
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense" v-if="false">
                  <v-text-field
                    v-model="filterForm.category"
                    type="text"
                    hide-details
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filterForm.department_name"
                    type="text"
                    hide-details
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filterForm.position"
                    type="text"
                    hide-details
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filterForm.first_work_date"
                    type="text"
                    hide-details
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
              </tr>
            </template>
            <template v-slot:item.id="{ item }">
              {{
              items
              .map(function (x) {
              return x.id;
              })
              .indexOf(item.id) + from
              }}
            </template>

            <template v-slot:item.department_code="{ item }">
              <v-row class="mx-0" v-for="(itm, idx) in item.employee_staff" :key="idx" style="max-width: 100px">
                <v-col class="col-12 text-truncate py-0">
                  {{
                  itm.staff && itm.staff.department
                  ? itm.staff.department.department_code
                  : ""
                  }}
                </v-col>
              </v-row>
            </template>

            <template v-slot:item.info="{ item }">
              <span>
                {{ item["lastname_" + $i18n.locale] }}
                {{ item["firstname_" + $i18n.locale] }}
                {{ item["middlename_" + $i18n.locale] }}
              </span>
            </template>
            <template v-slot:header.tabel="{ header }">
              <span style="white-space: normal; width: 50px">{{ header.text }}</span>
            </template>
            <template v-slot:item.tabel="{ item }">
              <router-link
                :to="'/personcontrol/profile/' + item.id"
                style="text-decoration: none"
              >{{ item.tabel }}</router-link>
            </template>
            <template v-slot:header.department_code="{ header }">
              <div style="white-space: normal; max-width: 70px" class="text-truncate">{{ header.text }}</div>
            </template>

            <template v-slot:item.department_id="{ item }">
              <v-row class="mx-0" v-for="(itm, idx) in item.employee_staff" :key="idx" style="max-width: 350px">
                <v-col class="col-12 text-truncate py-0">
                  {{
                  itm.staff && itm.staff.department
                  ? itm.staff.department["name_" + $i18n.locale]
                  : ""
                  }}
                </v-col>
              </v-row>
            </template>

            <template v-slot:item.category="{ item }">
              <span
                style="white-space: normal; min-width: 50px"
                class="d-block ma-0 pa-0"
              >{{ item.tariff_scale ? item.tariff_scale.category : "" }}</span>
            </template>
            <template v-slot:item.employee_staff="{ item }">
              <span
                style="white-space: normal; min-width: 50px"
                class="d-block ma-0 pa-0"
                v-for="(itm, idx) in item.employee_staff"
                :key="idx"
              >{{ itm.first_work_date }}</span>
            </template>

            <template v-slot:item.staffs="{ item }">
              <v-row class="mx-0" v-for="(itm, idx) in item.employee_staff" :key="idx" style="max-width: 350px">
                <v-col
                  class="col-12 text-truncate py-0"
                  v-if="itm.staff && itm.staff.position"
                >{{ itm.staff.position["name_" + $i18n.locale] }}</v-col>
              </v-row>
            </template>

            <template v-slot:header.employee_staff="{ header }">
              <span style="white-space: normal">{{ header.text }}</span>
            </template>

            <template v-slot:header.born_date="{ header }">
              <span style="white-space: normal">{{ header.text }}</span>
            </template>

            <template elevation="0" v-slot:expanded-item="{ headers, item }">
              <td
                elevation="0"
                :colspan="headers.length"
                class="pa-3"
                v-if="item.tariff_scale && !item.tariff_scale.category.includes('E8') || $store.getters.checkPermission('E8') ? true : !item.tariff_scale ? true : false"
              >
                <v-card 
                  class="my-4" 
                  outlined
                  style="border-radius: 5px; border: 1px solid #dce5ef"
                  elevation="0">
                  <v-list-item three-line>
                    <v-list-item-content>
                      <v-list-item-title class="headerTitle mb-1">
                        {{ item.firstname_uz_cyril }} {{ item.lastname_uz_cyril }}
                        {{ item.middlename_uz_cyril }}
                        <v-btn 
                          v-if="$store.getters.checkRole('edit_employee')"
                          class="px-2 mx-1"
                          color="blue"
                          style="min-width: 25px"
                          small
                          text
                          @click="editItem(item)">
                          <v-icon size="18">mdi-pencil</v-icon>
                        </v-btn>
                        <v-btn
                          color="red"
                          right
                          small
                          dark
                          elevation="0"
                          @click="dismissalEmployee(item)"
                          style="text-transform: none; border-radius: 5px; padding: 5px 20px"
                        >
                          {{ $t("employee.dismissal") }}
                        </v-btn>
                      </v-list-item-title>
                      <v-list-item-subtitle>
                        <span class="font-weight-bold">{{ $t("employee.tabel") }}:</span>
                        {{ item.tabel }}
                      </v-list-item-subtitle>
                      <v-list-item-subtitle class="d-block" style="max-width: 250px">
                        <span class="font-weight-bold">{{ $t("employee.INN") }}:</span>
                        {{ item.INN }}
                        <v-btn
                          class="px-2 mx-1"
                          color="blue"
                          style="min-width: 25px"
                          small
                          text
                          @click="editEmployeeInn(item)">
                          <v-icon size="18">mdi-pencil</v-icon>
                        </v-btn>
                      </v-list-item-subtitle>
                      <v-list-item-subtitle class="d-block" style="max-width: 250px" v-if="false">
                        <span class="font-weight-bold">{{ $t("employee.INPS") }}:</span>
                        {{ item.INPS }}
                        <v-btn 
                          class="px-2 mx-1"
                          color="blue"
                          style="min-width: 25px"
                          small
                          text
                          @click="editEmployeeInn(item)">
                          <v-icon size="18">mdi-pencil</v-icon>
                        </v-btn>
                      </v-list-item-subtitle>
                    </v-list-item-content>
                    <v-avatar
                      style="cursor: pointer"
                      tile
                      width="103"
                      height="133"
                      color="grey"
                      @click="imageDialog = true"
                    >
                      <v-icon v-if="!item.base64">mdi-account-outline</v-icon>
                      <img v-if="item.base64" :src="'data:application/jpg;base64,' + item.base64" />
                    </v-avatar>
                  </v-list-item>
                </v-card>
                <v-dialog v-model="imageDialog" max-width="400px">
                  <v-card class="pa-5">
                    <v-card-title class="pa-0" primary-title>
                      <span class="dialogTitle">{{ $t("employee.image") }}</span>
                    </v-card-title>
                    <v-divider color="#DCE5EF" class="my-1"></v-divider>
                    <v-card-text>
                      <v-form @keyup.native.enter="savePhoto(item)" ref="imageDialogForm">
                        <v-row class="mx-0 mt-3 dialogForm">
                          <v-col cols="12" class="pa-0 mb-3">
                            <v-file-input
                              v-model="image"
                              :rules="[
                                (v) => {
                                  let allowedExtensions = /(\.jpg)$/i;
                                  let error = false;
                                  (v) => {
                                    if (!allowedExtensions.exec(image.name)) {
                                      error = true;
                                    }
                                  };
                                  return !error || $t('requiredformat');
                                },
                                (v) => !!v || $t('input.required'),
                              ]"
                              outlined
                              dense
                              prepend-icon
                              append-icon="mdi-image-outline"
                              accept=".jpg, .png, application/jpg, application/png"
                              small-chips
                              show-size
                              hide-details="auto"
                            ></v-file-input>
                          </v-col>
                        </v-row>
                      </v-form>
                    </v-card-text>
                    <v-card-actions class="pa-0 mt-3">
                      <v-spacer></v-spacer>
                      <v-btn
                        class="mr-3"
                        color="#3FCB5D"
                        right
                        small
                        dark
                        @click="savePhoto(item)"
                        elevation="0"
                        style="text-transform: none; border-radius: 5px; padding: 5px 20px"
                      >
                        {{ $t("save") }}
                      </v-btn>
                      <v-btn
                        class=""
                        color="red"
                        right
                        small
                        dark
                        @click="imageDialog = false"
                        elevation="0"
                        style="text-transform: none; border-radius: 5px; padding: 5px 20px"
                      >
                        {{ $t("Отменить") }}
                      </v-btn>
                    </v-card-actions>
                  </v-card>
                </v-dialog>

                <!-- Employee Staff view -->
                <v-card
                  class="my-4" 
                  style="border-radius: 5px; border: 1px solid #dce5ef"
                  elevation="0">
                  <div>
                    <v-system-bar window color="#f6f9fb">
                      <span class="font-weight-bold">{{ $t("staff.staff") }}</span>
                      <v-spacer></v-spacer>
                      <v-icon color="success" medium @click="newEmployeeStaff(item)">mdi-plus</v-icon>
                    </v-system-bar>
                  </div>
                  <v-container fluid class="pa-1">
                    <table
                      class="doc-template_data-table ma-0 pa-0"
                      style="width: 100%"
                      v-for="(itm, idx) in item.employee_staff"
                      :key="idx"
                    >
                      <tr>
                        <td colspan="7" class="font-weight-bold">
                          <v-row class="mx-0 pa-0">
                            {{
                            itm.staff && itm.staff.position
                            ? itm.staff.position["name_" + $i18n.locale]
                            : ""
                            }}
                            <v-spacer></v-spacer>
                            <v-switch
                              v-model="itm.is_main_staff"
                              readonly
                              dense
                              @click="changeMainStaff(itm)"
                              class="ma-0"
                              :label="
                                itm.is_main_staff
                                  ? $t('employee.main_staff')
                                  : $t('employee.loaded_staff')
                              "
                              hide-details
                            ></v-switch>
                            <v-btn
                              class="px-2 mx-1"
                              color="blue"
                              style="min-width: 25px"
                              small
                              text
                              @click="editEmployeeStaff(itm)"
                            >
                              <v-icon size="18">mdi-pencil</v-icon>
                            </v-btn>
                          </v-row>
                        </td>
                      </tr>
                      <tr>
                        <td class="font-weight-bold">{{ $t("employee.department_id") }}</td>
                        <td class="font-weight-bold">{{ $t("employee.category") }}</td>
                        <td class="font-weight-bold">{{ $t("employee.enter_order_number") }}</td>
                        <td class="font-weight-bold">{{ $t("employee.enter_order_date") }}</td>
                        <td class="font-weight-bold">{{ $t("employee.contract_number") }}</td>
                        <td class="font-weight-bold">{{ $t("employee.contract_date") }}</td>
                      </tr>
                      <tr v-if="itm.staff && itm.staff.department">
                        <td style="max-width: 200px; white-space: normal">
                          <span class>
                            {{
                            itm.staff.department
                            ? itm.staff.department.department_code
                            : ""
                            }}
                            -
                            {{
                            itm.staff.department
                            ? itm.staff.department["name_" + $i18n.locale]
                            : ""
                            }}
                          </span>
                        </td>
                        <td class>{{ itm.tariff_scale ? itm.tariff_scale.category : "" }}</td>
                        <td class>{{ itm.enter_order_number }}</td>
                        <td class>{{ itm.enter_order_date }}</td>
                        <td class>{{ itm.contract_number }}</td>
                        <td class>{{ itm.contract_date }}</td>
                      </tr>
                    </table>
                  </v-container>
                </v-card>

                <!-- Employee Staff Old History -->
                <v-card
                  class="my-4" 
                  style="border-radius: 5px; border: 1px solid #dce5ef"
                  elevation="0">
                  <div>
                    <v-system-bar window color="#f6f9fb">
                      <span class="font-weight-bold">{{ $t("staff.staff_history") }}</span>
                      <v-spacer></v-spacer>
                    </v-system-bar>
                  </div>
                  <v-container fluid class="pa-0">
                    <table class="infoTable ma-0 pa-0">
                      <tr style="padding-top: 5px">
                        <td colspan="7" class="font-weight-bold"></td>
                      </tr>
                      <tr>
                        <td class="font-weight-bold">{{ $t("department.parent_id") }}</td>
                        <td class="font-weight-bold">{{ $t("profile.department") }}</td>
                        <td class="font-weight-bold">{{ $t("profile.employee_position") }}</td>
                        <td class="font-weight-bold">{{ $t("employee.first_work_date") }}</td>
                        <td class="font-weight-bold">{{ $t("employee.leave_date") }}</td>
                      </tr>
                      <tr v-for="(history, hidx) in item.histories" :key="hidx">
                        <td class="text-ellipsis">
                          <span
                            :title="history.parent ? history.parent : ''"
                          >{{ history.parent ? history.parent : "" }}</span>
                        </td>
                        <td class="text-ellipsis" :title="history.department ? history.department : ''">
                          {{ history.department ? history.department_code : "" }}
                          {{ history.department ? history.department : "" }}
                        </td>
                        <td
                          class="text-ellipsis"
                          :title="history.position ? history.position : ''"
                        >{{ history.position ? history.position : "" }}</td>
                        <td>{{ history.enterOrderDate ? history.enterOrderDate : "" }}</td>
                        <td>{{ history.leaveOrderDate ? history.leaveOrderDate : "" }}</td>
                      </tr>
                    </table>
                  </v-container>
                </v-card>

                <!-- Employee Coefficients view -->
                <v-card
                  v-if="$store.getters.checkPermission('show_employee_coefficients')"
                  class="my-4" 
                  style="border-radius: 5px; border: 1px solid #dce5ef"
                  elevation="0">
                  <div>
                    <v-system-bar window color="#f6f9fb">
                      <span class="font-weight-bold">{{ $t("employee.coefficients") }}</span>
                      <v-spacer></v-spacer>
                      <v-icon color="success" medium @click="newEmployeeCoefficient(item)">mdi-plus</v-icon>
                    </v-system-bar>
                  </div>
                  <v-container fluid class="pa-0">
                    <table class="infoTable ma-0 pa-0" v-if="item.employee_coefficients.length">
                      <tr>
                        <td class="font-weight-bold">{{ $t("coefficient.type") }}</td>
                        <td class="font-weight-bold">{{ $t("coefficient.percent") }}</td>
                        <td class="font-weight-bold">{{ $t("coefficient.order_number") }}</td>
                        <td class="font-weight-bold">{{ $t("coefficient.order_date") }}</td>
                        <td class="font-weight-bold">{{ $t("coefficient.begin_date") }}</td>
                        <td class="font-weight-bold">{{ $t("coefficient.end_date") }}</td>
                        <td class="font-weight-bold">{{ $t("coefficient.status") }}</td>
                        <td class="font-weight-bold" style="max-width: 50px">{{ $t("actions") }}</td>
                      </tr>
                      <tr v-for="(itm, idx) in item.employee_coefficients" :key="idx">
                        <td class>
                          {{ itm.coefficient.code }}
                          {{ itm.coefficient.description }}
                        </td>
                        <td class>{{ itm.coefficient ? itm.percent : "" }}</td>
                        <td class>{{ itm.order_number }}</td>
                        <td class>{{ itm.order_date }}</td>
                        <td class>{{ itm.begin_date }}</td>
                        <td class>{{ itm.end_date }}</td>
                        <td class>
                          {{
                          itm.status
                          ? $t("employee.active")
                          : $t("employee.inactive")
                          }}
                        </td>
                        <td class style="max-width: 40px">
                          <v-btn
                            class="px-2"
                            color="blue"
                            style="min-width: 25px"
                            small
                            text
                            @click="editEmployeeCoefficient(itm)"
                          >
                            <v-icon size="18">mdi-pencil</v-icon>
                          </v-btn>
                          <v-btn
                            class="px-2"
                            color="error"
                            style="min-width: 25px"
                            small
                            text
                            @click="deleteEmployeeCoefficient(itm)"
                          >
                            <v-icon size="18">mdi-trash-can-outline</v-icon>
                          </v-btn>
                        </td>
                      </tr>
                    </table>
                  </v-container>
                </v-card>

                <!-- Employee Document view -->

                <v-card
                  class="my-4" 
                  style="border-radius: 5px; border: 1px solid #dce5ef"
                  elevation="0">
                  <div>
                    <v-system-bar window color="#f6f9fb">
                      <span class="font-weight-bold">{{ $t("employeeDocument.index") }}</span>
                      <v-spacer></v-spacer>
                      <v-icon color="success" medium @click="newEmployeeDocumentItem(item)">mdi-plus</v-icon>
                    </v-system-bar>
                  </div>
                  <v-container fluid class="pa-1" v-if="item.employee_official_document.length">
                    <v-simple-table
                      class="doc-template_data-table ma-0 pa-0"
                      style="width: 100%">
                      <template v-slot:default>
                            <tr>
                              <th class="text-left">{{ $t("type") }}</th>
                              <th class="text-left">{{ $t("employeeDocument.series") }}</th>
                              <th class="text-left">{{ $t("employeeDocument.number") }}</th>
                              <th class="text-left">{{ $t("employeeDocument.given_by") }}</th>
                              <th class="text-left">{{ $t("employeeDocument.date_issue") }}</th>
                              <th class="text-left">{{ $t("employeeDocument.valid_until") }}</th>
                              <th class="text-left">{{ $t("employeeDocument.status") }}</th>
                              <th class="text-center" style="width: 80px;">{{ $t("employeeDocument.actions") }}</th>
                            </tr>
                            <tr v-for="(itm, ind) in item.employee_official_document"
                                :key="ind">
                              <td>{{ itm.official_document_type["name_" + $i18n.locale] }}</td>
                              <td>{{ itm.series }}</td>
                              <td>{{ itm.number }}</td>
                              <td>{{ itm.given_organization }}</td>
                              <td>{{ itm.given_date }}</td>
                              <td>{{ itm.due_date }}</td>
                              <td>
                                {{
                                itm.is_active
                                ? $t("employee.active")
                                : $t("employee.inactive")
                                }}
                              </td>
                              <td width="80px">
                                <v-btn
                                  class="px-2"
                                  color="blue"
                                  style="min-width: 25px"
                                  small
                                  text
                                  @click="editEmployeeDocumentItem(itm)">
                                  <v-icon size="18">mdi-pencil</v-icon>
                                </v-btn>
                                <!-- <v-btn color="blue" small text @click="editItemFiles(itm, ind)">
                                  <v-icon>mdi-download</v-icon>
                                </v-btn> -->
                                <v-btn
                                  class="px-2"
                                  color="error"
                                  style="min-width: 25px"
                                  small
                                  text
                                  @click="deleteEmployeeDocumentItem(itm)">
                                  <v-icon size="18">mdi-trash-can-outline</v-icon>
                                </v-btn>
                              </td>
                            </tr>
                      </template>
                    </v-simple-table>
                  </v-container>
                </v-card>

                <!-- Employee Address view -->
                <v-card
                  class="my-4" 
                  style="border-radius: 5px; border: 1px solid #dce5ef"
                  elevation="0">
                  <div>
                    <v-system-bar window color="#f6f9fb">
                      <span class="font-weight-bold">{{ $t("employee.address") }}</span>
                      <v-spacer></v-spacer>
                      <v-icon color="success" medium @click="newEmployeeAddress(item)">mdi-plus</v-icon>
                    </v-system-bar>
                  </div>
                  <v-container fluid class="pa-0">
                    <table 
                      class="doc-template_data-table" 
                      style="width: 100%; height: 100%; border-radius: 10px"
                      v-if="item.employee_addresses.length"
                     
                     >
                      <tr>
                        <td class="font-weight-bold">{{ $t("employee.address_type_id") }}</td>
                        <td class="font-weight-bold">{{ $t("employee.country") }}</td>
                        <td class="font-weight-bold">{{ $t("employee.region") }}</td>
                        <td class="font-weight-bold">{{ $t("employee.district") }}</td>
                        <td class="font-weight-bold">{{ $t("employee.street_address") }}</td>
                        <td class="font-weight-bold">{{ $t("employee.home_address") }}</td>
                        <td class="font-weight-bold">{{ $t("employee.description") }}</td>
                        <td class="font-weight-bold text-center" style="width: 80px">{{ $t("actions") }}</td>
                      </tr>
                      <tr v-for="(itm, ind) in item.employee_addresses" :key="ind">
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
                        <td class>{{ itm.region ? itm.region["name_" + $i18n.locale] : "" }}</td>
                        <td class>
                          {{
                          itm.district ? itm.district["name_" + $i18n.locale] : ""
                          }}
                        </td>
                        <td
                          class="text-ellipsis"
                          :title="
                            itm.street_address_uz_latin
                              ? itm.street_address_uz_latin
                              : ''
                          "
                        >{{ itm.street_address_uz_latin }}</td>
                        <td
                          class="text-ellipsis"
                          :title="
                            itm.home_address_uz_latin
                              ? itm.home_address_uz_latin
                              : ''
                          "
                        >{{ itm.home_address_uz_latin }}</td>
                        <td
                          class="text-ellipsis"
                          :title="itm.description ? itm.description : ''"
                        >{{ itm.description ? itm.description : "" }}</td>
                        <td class style="width: 80px">
                          <v-btn
                            class="px-2"
                            color="blue"
                            style="min-width: 25px"
                            small
                            text
                            @click="editEmployeeAddress(itm)"
                          >
                            <v-icon size="18">mdi-pencil</v-icon>
                          </v-btn>
                          <v-btn
                            class="px-2"
                            color="error"
                            style="min-width: 25px"
                            small
                            text
                            @click="deleteEmployeeAddress(itm)"
                          >
                            <v-icon size="18">mdi-trash-can-outline</v-icon>
                          </v-btn>
                        </td>
                      </tr>
                    </table>
                  </v-container>
                </v-card>

                <!-- Employee Relative -->
                <v-card
                  class="my-4" 
                  style="border-radius: 5px; border: 1px solid #dce5ef"
                  elevation="0">
                  <v-system-bar window color="#f6f9fb">
                    <span class="font-weight-bold">{{ $t("employee.employee_relative") }}</span>
                    <v-spacer></v-spacer>
                    <v-icon color="success" medium @click="newEmployeeRelative(item)">mdi-plus</v-icon>
                  </v-system-bar>
                  <v-container fluid class="pa-0">
                    <table 
                      class="doc-template_data-table" 
                      style="width: 100%; height: 100%; border-radius: 10px"
                      v-if="item.employee_relative.length"
                      >
                      <tr>
                        <td class="font-weight-bold">{{ $t("Qarindosh") }}</td>
                        <td class="font-weight-bold">{{ $t("employee.lastname") }}</td>
                        <td class="font-weight-bold">{{ $t("employee.firstname") }}</td>
                        <td class="font-weight-bold">{{ $t("employee.middlename") }}</td>
                        <td class="font-weight-bold">{{ $t("employee.born_date") }}</td>
                        <td class="font-weight-bold">{{ $t("employee.born_place") }}</td>
                        <td class="font-weight-bold">{{ $t("employee.work_place") }}</td>
                        <td class="font-weight-bold">{{ $t("employee.living_place") }}</td>
                        <td class="font-weight-bold text-center" style="width: 80px">{{ $t("actions") }}</td>
                      </tr>
                      <tr v-for="(it, eRelative) in item.employee_relative" :key="eRelative">
                        <td class>{{ it.family_relative ? it.family_relative.name_uz_latin : ""}}</td>
                        <td class>{{ it.last_name ? it.last_name : "" }}</td>
                        <td class>{{ it.first_name ? it.first_name : "" }}</td>
                        <td class>{{ it.middle_name ? it.middle_name : "" }}</td>
                        <td class>{{ it.born_date ? it.born_date : "" }}</td>
                        <td
                          class="text-ellipsis"
                          :title="it.born_place ? it.born_place : ''"
                        >{{ it.born_place ? it.born_place : "" }}</td>
                        <td
                          class="text-ellipsis"
                          :title="it.work_place ? it.work_place : ''"
                        >{{ it.work_place ? it.work_place : "" }}</td>
                        <td
                          class="text-ellipsis"
                          :title="it.living_place ? it.living_place : ''"
                        >{{ it.living_place ? it.living_place : "" }}</td>
                        <td class style="width: 80px;">
                          <v-btn
                            class="px-2"
                            color="blue"
                            style="min-width: 25px"
                            small
                            text
                            @click="editEmployeeRelative(it)"
                          >
                            <v-icon size="18">mdi-pencil</v-icon>
                          </v-btn>
                          <v-btn
                            class="px-2"
                            color="error"
                            style="min-width: 25px"
                            small
                            text
                            @click="deleteEmployeeRelative(it)"
                          >
                            <v-icon size="18">mdi-trash-can-outline</v-icon>
                          </v-btn>
                        </td>
                      </tr>
                    </table>
                  </v-container>
                </v-card>
                <!-- Employee Phone -->
                <v-card
                  class="my-4" 
                  style="border-radius: 5px; border: 1px solid #dce5ef"
                  elevation="0">
                  <div>
                    <v-system-bar window color="#f6f9fb">
                      <span class="font-weight-bold">{{ $t("employee.employee_phones") }}</span>
                      <v-spacer></v-spacer>
                      <v-icon color="success" medium @click="newEmployeePhone(item)">mdi-plus</v-icon>
                    </v-system-bar>
                  </div>
                  <v-container fluid class="pa-0">
                    <table
                      class="doc-template_data-table" 
                      style="width: 100%; height: 100%; border-radius: 10px"
                      v-if="
                        item && item.employee_phones
                          ? item.employee_phones.length
                          : ''
                      "
                    >
                      <tr>
                        <td class="font-weight-bold">{{ $t("employee.phone_type") }}</td>
                        <td class="font-weight-bold">{{ $t("employee.phone_number") }}</td>
                        <td class="font-weight-bold">{{ $t("employee.description") }}</td>
                        <td class="font-weight-bold" style="width: 80px">{{ $t("actions") }}</td>
                      </tr>
                      <tr v-for="(itm, ind) in item.employee_phones" :key="ind">
                        <td class>{{ itm.phone_type }}</td>
                        <td class>{{ itm.phone_number }}</td>
                        <td class>{{ itm.description }}</td>
                        <td class style="max-width: 80px">
                          <v-btn
                            class="px-2"
                            color="blue"
                            style="min-width: 25px"
                            small
                            text
                            @click="editEmployeePhone(itm)">
                            <v-icon size="18">mdi-pencil</v-icon>
                          </v-btn>
                          <v-btn
                            class="px-2"
                            color="error"
                            style="min-width: 25px"
                            small
                            text
                            @click="deleteEmployeePhone(itm)"
                          >
                            <v-icon size="18">mdi-trash-can-outline</v-icon>
                          </v-btn>
                        </td>
                      </tr>
                    </table>
                  </v-container>
                </v-card>

                <!-- Employee Languages -->
                <v-card
                  class="my-4" 
                  style="border-radius: 5px; border: 1px solid #dce5ef"
                  elevation="0">
                  <div>
                    <v-system-bar window color="#f6f9fb">
                      <span class="font-weight-bold">{{ $t("employee.language_name") }}</span>
                      <v-spacer></v-spacer>
                      <v-icon color="success" medium @click="newEmployeeLanguageItem(item)">mdi-plus</v-icon>
                    </v-system-bar>
                  </div>
                  <v-container
                    fluid
                    class="pa-0"
                    v-if="
                      item && item.employee_languages
                        ? item.employee_languages.length
                        : ''
                    "
                  >
                      <v-simple-table 
                      class="doc-template_data-table" 
                      style="width: 100%; height: 100%; border-radius: 10px"
                      dense>
                        <template v-slot:default>
                            <tr>
                              <th class="text-left">{{ $t("#") }}</th>
                              <th class="text-left">{{ $t("employee.language") }}</th>
                              <th class="text-left">{{ $t("employee.level") }}</th>
                              <th class="text-left">{{ $t("employee.description") }}</th>
                              <th class="font-weight-bold text-center" style="width:80px;">{{ $t("employeeDocument.actions") }}</th>
                            </tr>
                            <tr v-for="(itm, ind) in item.employee_languages" :key="ind">
                              <td>{{ ind + 1 }}</td>
                              <td>
                                {{
                                itm.hr_language
                                ? itm.hr_language["name_" + $i18n.locale]
                                : ""
                                }}
                              </td>
                              <td>{{ itm.level ? itm.level : "" }}</td>
                              <td>{{ itm.description ? itm.description : "" }}</td>
                              <td style="width:80px;">
                                <v-btn
                                  class="px-2"
                                  color="blue"
                                  style="min-width: 25px"
                                  small
                                  text
                                  @click="editEmployeeLanguageItem(itm)">
                                  <v-icon size="18">mdi-pencil</v-icon>
                                </v-btn>
                                <v-btn
                                  class="px-2"
                                  color="error"
                                  style="min-width: 25px"
                                  small
                                  text
                                  @click="deleteEmployeeLanguageItem(itm)">
                                  <v-icon size="18">mdi-trash-can-outline</v-icon>
                                </v-btn>
                              </td>
                            </tr>
                        </template>
                      </v-simple-table>
                  </v-container>
                </v-card>

                <!-- Employee Parties -->
                <v-card
                  class="my-4" 
                  style="border-radius: 5px; border: 1px solid #dce5ef"
                  elevation="0">
                  <div>
                    <v-system-bar window color="#f6f9fb">
                      <span class="font-weight-bold">{{ $t("employee.parties") }}</span>
                      <v-spacer></v-spacer>
                      <v-icon color="success" medium @click="newEmployeePartyItem(item)">mdi-plus</v-icon>
                    </v-system-bar>
                  </div>
                  <v-container fluid class="pa-0" v-if="item && item.employee_parties">
                    <v-card class="pa-2 mb-2">
                      <v-simple-table 
                        class="doc-template_data-table" 
                        style="width: 100%; height: 100%; border-radius: 10px"
                        dense>
                        <template v-slot:default>
                          <thead>
                            <tr>
                              <th class="text-left">{{ $t("#") }}</th>
                              <th class="text-left">{{ $t("employee.party") }}</th>
                              <th class="text-left">{{ $t("employee.description") }}</th>
                              <th class="text-center" style="width:80px;">{{ $t("actions") }}</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td>{{ 1 }}</td>
                              <td
                                class="text-ellipsis"
                                :title="
                                  item.employee_parties
                                    ? item.employee_parties.hr_party[
                                        'name_' + $i18n.locale
                                      ]
                                    : ''
                                "
                              >
                                {{
                                item.employee_parties
                                ? item.employee_parties.hr_party[
                                "name_" + $i18n.locale
                                ]
                                : ""
                                }}
                              </td>
                              <td>{{ item.employee_parties.description }}</td>
                              <td style="width: 80px;">
                                <v-btn
                                  class="px-2"
                                  color="blue"
                                  style="min-width: 25px"
                                  small
                                  text
                                  @click="
                                    editEmployeePartyItem(item.employee_parties)
                                  "
                                >
                                  <v-icon size="18">mdi-pencil</v-icon>
                                </v-btn>
                                <v-btn
                                  class="px-2"
                                  color="error"
                                  style="min-width: 25px"
                                  small
                                  text
                                  @click="
                                    deleteEmployeePartyItem(item.employee_parties)
                                  "
                                >
                                  <v-icon size="18">mdi-trash-can-outline</v-icon>
                                </v-btn>
                              </td>
                            </tr>
                          </tbody>
                        </template>
                      </v-simple-table>
                    </v-card>
                  </v-container>
                </v-card>

                <!-- Employee Military Ranks-->
                <v-card
                  class="my-4" 
                  style="border-radius: 5px; border: 1px solid #dce5ef"
                  elevation="0">
                  <div>
                    <v-system-bar window color="#f6f9fb">
                      <span class="font-weight-bold">{{ $t("employee.military_ranks") }}</span>
                      <v-spacer></v-spacer>
                      <v-icon color="success" medium @click="newEmployeeMilitaryRankItem(item)">mdi-plus</v-icon>
                    </v-system-bar>
                  </div>
                  <v-container
                    fluid
                    class="pa-0"
                    v-if="
                      item && item.employee_military_ranks
                        ? item.employee_military_ranks.length
                        : ''
                    "
                  >
                    <v-card class="pa-2 mb-2">
                      <v-simple-table 
                        class="doc-template_data-table" 
                        style="width: 100%; height: 100%; border-radius: 10px"
                        dense>
                        <template v-slot:default>
                          <thead>
                            <tr>
                              <th class="text-left">{{ $t("#") }}</th>
                              <th class="text-left">{{ $t("employee.military_rank") }}</th>
                              <th class="text-left">{{ $t("employee.description") }}</th>
                              <th class="text-center" style="width:80px;">{{ $t("actions") }}</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr v-for="(itm, ind) in item.employee_military_ranks" :key="ind">
                              <td>{{ ind + 1 }}</td>
                              <td>
                                {{
                                itm.hr_military_rank
                                ? itm.hr_military_rank["name_" + $i18n.locale]
                                : ""
                                }}
                              </td>
                              <td>{{ itm.description ? itm.description : "" }}</td>
                              <td style="width:80px;">
                                <v-btn
                                  class="px-2"
                                  color="blue"
                                  style="min-width: 25px"
                                  small
                                  text
                                  @click="editEmployeeMilitaryRankItem(itm)"
                                >
                                  <v-icon size="18">mdi-pencil</v-icon>
                                </v-btn>
                                <v-btn
                                  class="px-2"
                                  color="error"
                                  style="min-width: 25px"
                                  small
                                  text
                                  @click="deleteEmployeeMilitaryRankItem(itm)"
                                >
                                  <v-icon size="18">mdi-trash-can-outline</v-icon>
                                </v-btn>
                              </td>
                            </tr>
                          </tbody>
                        </template>
                      </v-simple-table>
                    </v-card>
                  </v-container>
                </v-card>

                <!-- Employee State Award-->
                <v-card
                  class="my-4" 
                  style="border-radius: 5px; border: 1px solid #dce5ef"
                  elevation="0">
                  <div>
                    <v-system-bar window color="#f6f9fb">
                      <span class="font-weight-bold">{{ $t("employee.state_awards") }}</span>
                      <v-spacer></v-spacer>
                      <v-icon color="success" medium @click="newEmployeeStateAwardItem(item)">mdi-plus</v-icon>
                    </v-system-bar>
                  </div>
                  <v-container
                    fluid
                    class="pa-0"
                    v-if="
                      item && item.employee_state_awards
                        ? item.employee_state_awards.length
                        : ''
                    "
                  >
                    <v-card class="pa-2 mb-2">
                      <v-simple-table 
                        class="doc-template_data-table" 
                        style="width: 100%; height: 100%; border-radius: 10px"
                        dense>
                        <template v-slot:default>
                          <thead>
                            <tr>
                              <th class="text-left">{{ $t("#") }}</th>
                              <th class="text-left">{{ $t("employee.state_award") }}</th>
                              <th class="text-left">{{ $t("employee.description") }}</th>
                              <th class="text-center" style="width:80px;">{{ $t("actions") }}</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr v-for="(itm, ind) in item.employee_state_awards" :key="ind">
                              <td>{{ ind + 1 }}</td>
                              <td>
                                {{
                                itm.hr_state_award
                                ? itm.hr_state_award["name_" + $i18n.locale]
                                : ""
                                }}
                              </td>
                              <td>{{ itm.description ? itm.description : "" }}</td>
                              <td style="width:80px;">
                                <v-btn
                                  class="px-2"
                                  color="blue"
                                  style="min-width: 25px"
                                  small
                                  text 
                                  @click="editEmployeeStateAwardItem(itm)">
                                  <v-icon size="18">mdi-pencil</v-icon>
                                </v-btn>
                                <v-btn
                                  class="px-2"
                                  color="error"
                                  style="min-width: 25px"
                                  small
                                  text
                                  @click="deleteEmployeeStateAwardItem(itm)"
                                >
                                  <v-icon size="18">mdi-trash-can-outline</v-icon>
                                </v-btn>
                              </td>
                            </tr>
                          </tbody>
                        </template>
                      </v-simple-table>
                    </v-card>
                  </v-container>
                </v-card>

                <!-- Employee work history -->
                <v-card
                  class="my-4" 
                  style="border-radius: 5px; border: 1px solid #dce5ef"
                  elevation="0">
                  <v-system-bar window color="#f6f9fb">
                    <span class="font-weight-bold">{{ $t("profile.work_history") }}</span>
                    <v-spacer></v-spacer>
                    <v-icon color="success" medium @click="newEmployeeWorkHistory(item)">mdi-plus</v-icon>
                  </v-system-bar>
                  <v-container fluid class="pa-0">
                    <table 
                      class="doc-template_data-table" 
                      style="width: 100%; height: 100%; border-radius: 10px"
                      v-if="item.employee_work_histories.length">
                      <tr>
                        <td class="font-weight-bold">#</td>
                        <td class="font-weight-bold">{{ $t("start_date") }}</td>
                        <td class="font-weight-bold">{{ $t("end_date") }}</td>
                        <td class="font-weight-bold">{{ $t("employee.work_place") }}</td>
                        <td class="font-weight-bold">{{ $t("employee.position") }}</td>
                        <td class="font-weight-bold text-center" style="width: 80px">{{ $t("actions") }}</td>
                      </tr>
                      <tr v-for="(itmw, eWork) in item.employee_work_histories" :key="eWork">
                        <td class>{{ eWork + 1 }}</td>
                        <td class>{{ itmw.begin_date ? itmw.begin_date : "" }}</td>
                        <td class>{{ itmw.end_date ? itmw.end_date : "" }}</td>
                        <td
                          class="text-ellipsis"
                          :title="itmw.work_place ? itmw.work_place : ''"
                        >{{ itmw.work_place ? itmw.work_place : "" }}</td>
                        <td
                          class="text-ellipsis"
                          :title="itmw.position ? itmw.position : ''"
                        >{{ itmw.position ? itmw.position : "" }}</td>
                        <td class style="width: 80px">
                          <v-btn
                            class="px-2"
                            color="blue"
                            style="min-width: 25px"
                            small
                            text
                            @click="editEmployeeWorkHistory(itmw)"
                          >
                            <v-icon size="18">mdi-pencil</v-icon>
                          </v-btn>
                          <v-btn
                            class="px-2"
                            color="error"
                            style="min-width: 25px"
                            small
                            text
                            @click="deleteEmployeeWorkHistory(itmw)"
                          >
                            <v-icon size="18">mdi-trash-can-outline</v-icon>
                          </v-btn>
                        </td>
                      </tr>
                    </table>
                  </v-container>
                </v-card>

                <!-- Employee education history -->
                <v-card 
                  class="my-4" 
                  style="border-radius: 5px; border: 1px solid #dce5ef"
                  elevation="0">
                  <v-system-bar window color="#f6f9fb">
                    <span class="font-weight-bold">{{ $t("profile.education_history") }}</span>
                    <v-spacer></v-spacer>
                    <v-icon color="success" medium @click="newEmployeeEducationHistory(item)">mdi-plus</v-icon>
                  </v-system-bar>
                  <v-container fluid class="pa-0">
                    <table 
                      class="doc-template_data-table" 
                      style="width: 100%; height: 100%; border-radius: 10px"
                      v-if="item.employee_education_histories.length">
                      <tr>
                        <td class="font-weight-bold">#</td>
                        <td class="font-weight-bold">{{ $t("university") }}</td>
                        <td class="font-weight-bold">{{ $t("major") }}</td>
                        <td class="font-weight-bold">{{ $t("study_type") }}</td>
                        <td class="font-weight-bold">{{ $t("hr_study_degree.index") }}</td>
                        <td class="font-weight-bold">{{ $t("end_date") }}</td>
                        <td class="font-weight-bold">{{ $t("academic_title") }}</td>
                        <td class="font-weight-bold">{{ $t("academic_degree") }}</td>
                        <td class="font-weight-bold text-center" style="width: 80px">{{ $t("actions") }}</td>
                      </tr>
                      <tr v-for="(itme, eEdu) in item.employee_education_histories" :key="eEdu">
                        <td class>{{ eEdu + 1 }}</td>
                        <td
                          class="text-ellipsis"
                          :title="
                            itme.university
                              ? itme.university['name_' + $i18n.locale]
                              : ''
                          "
                        >
                          {{
                          itme.university
                          ? itme.university["name_" + $i18n.locale]
                          : ""
                          }}
                        </td>
                        <td
                          class="text-ellipsis"
                          :title="
                            itme.major ? itme.major['name_' + $i18n.locale] : ''
                          "
                        >{{ itme.major ? itme.major["name_" + $i18n.locale] : "" }}</td>
                        <td class>
                          {{
                          itme.study_type
                          ? itme.study_type["name_" + $i18n.locale]
                          : ""
                          }}
                        </td>
                        <td class>
                          {{
                          itme.study_degree
                          ? itme.study_degree["name_" + $i18n.locale]
                          : ""
                          }}
                        </td>
                        <td class>{{ itme.end_date ? itme.end_date : "" }}</td>
                        <td class>{{ itme.academic_title ? itme.academic_title : " - " }}</td>
                        <td class>{{ itme.academic_degree ? itme.academic_degree : " - " }}</td>
                        <td class style="width: 80px">
                          <v-btn
                            class="px-2"
                            color="blue"
                            style="min-width: 25px"
                            small
                            text
                            @click="editEmployeeEducationHistory(itme)"
                          >
                            <v-icon size="18">mdi-pencil</v-icon>
                          </v-btn>
                          <v-btn
                            class="px-2"
                            color="error"
                            style="min-width: 25px"
                            small
                            text
                            @click="deleteEmployeeEducationHistory(itme)"
                          >
                            <v-icon size="18">mdi-trash-can-outline</v-icon>
                          </v-btn>
                        </td>
                      </tr>
                    </table>
                  </v-container>
                </v-card>
              </td>
            </template>
          </v-data-table>
        </v-col>
      </v-row>
    </v-card>

    <v-dialog
      v-model="dialog"
      @keydown.esc="dialog = false"
      persistent
      max-width="800px"
    >
      <v-card class="pa-5">
        <v-card-title class="pa-0" primary-title>
          <span class="dialogTitle">{{ dialogHeaderText }}</span>
        </v-card-title>
        <v-divider color="#DCE5EF" class="my-1"></v-divider>
        <v-card-text>
          <v-form @keyup.native.enter="save" ref="dialogForm">
            <v-row class="mx-0 dialogForm">
              <v-col cols="6" sm="4" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("employee.firstname_uz_latin") }}</label>
                <v-text-field
                  v-model="form.firstname_uz_latin"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                  class="mt-1"
                ></v-text-field>
              </v-col>
              <v-col cols="6" sm="4" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("employee.firstname_uz_cyril") }}</label>
                <v-text-field
                  v-model="form.firstname_uz_cyril"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                  class="mt-1"
                ></v-text-field>
              </v-col>
              <v-col cols="6" sm="4" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("employee.lastname_uz_latin") }}</label>
                <v-text-field
                  v-model="form.lastname_uz_latin"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                  class="mt-1"
                ></v-text-field>
              </v-col>
              <v-col cols="6" sm="4" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("employee.lastname_uz_cyril") }}</label>
                <v-text-field
                  v-model="form.lastname_uz_cyril"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                  class="mt-1"
                ></v-text-field>
              </v-col>
              <v-col cols="6" sm="4" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("employee.middlename_uz_latin") }}</label>
                <v-text-field
                  v-model="form.middlename_uz_latin"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                  class="mt-1"
                ></v-text-field>
              </v-col>
              <v-col cols="6" sm="4" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("employee.middlename_uz_cyril") }}</label>
                <v-text-field
                  v-model="form.middlename_uz_cyril"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                  class="mt-1"
                ></v-text-field>
              </v-col>
              <v-col cols="6" sm="4" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("gender.gender") }}</label>
                <v-autocomplete
                  clearable
                  v-model="form.gender"
                  :items="[
                    { text: $t('gender.male'), value: 'M' },
                    { text: $t('gender.female'), value: 'F' },
                  ]"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                  class="mt-1"
                ></v-autocomplete>
              </v-col>
              <v-col cols="6" sm="4" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("employee.nationality_id") }}</label>
                <v-autocomplete
                  clearable
                  v-model="form.nationality_id"
                  :items="
                    nationalities.map((v) => ({
                      text: v['name_' + $i18n.locale],
                      value: v.id,
                    }))
                  "
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                  class="mt-1"
                ></v-autocomplete>
              </v-col>
              <v-col cols="6" sm="4" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("employee.tabel") }}</label>
                <v-text-field
                  v-model="form.tabel"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                  class="mt-1"
                ></v-text-field>
              </v-col>
              <v-col cols="6" sm="4" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("employee.INPS") }}</label>
                <v-text-field
                  v-model="form.INPS"
                  type="number"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                  class="mt-1"
                ></v-text-field>
              </v-col>
              <v-col cols="6" sm="4" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("employee.INN") }}</label>
                <v-text-field v-model="form.INN" type="number" hide-details="auto" class="mt-1" dense outlined></v-text-field>
              </v-col>
              <v-col cols="6" sm="4" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("employee.born_date") }}</label>
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
                      v-model="form.born_date"
                      :rules="[(v) => !!v || $t('input.required')]"
                      readonly
                      v-on="on"
                      hide-details="auto"
                      dense
                      outlined
                      class="mt-1"
                    ></v-text-field>
                  </template>
                  <v-date-picker v-model="form.born_date" @input="createdAtMenu1 = false"></v-date-picker>
                </v-menu>
              </v-col>
              <v-col cols="12" sm="4" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("employee.category") }}</label>
                <v-autocomplete
                  clearable
                  v-model="form.tariff_scale_id"
                  :items="
                    tariffScales.map((v) => ({ text: v.category, value: v.id }))
                  "
                  hide-details="auto"
                  dense
                  outlined
                  class="mt-1"
                ></v-autocomplete>
              </v-col>
              <v-col
                v-if="$store.getters.checkPermission('dr_employee')"
                cols="6"
                sm="4"
                class="px-1 py-0 mb-3"
              >
                <label class="labelTitle" for>{{ $t("employee.tabel_dr") }}</label>
                <v-text-field v-model="form.dr_tb" hide-details="auto" class="mt-1" dense outlined></v-text-field>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions class="pa-0 mt-5">
          <v-spacer></v-spacer>
          <!-- <v-btn color="primary" @click="save">{{ $t("save") }}</v-btn> -->
          <v-btn
            class="mr-3"
            color="#3FCB5D"
            right
            small
            dark
            @click="save"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("save") }}
          </v-btn>
          <v-btn
            class=""
            color="red"
            right
            small
            dark
            @click="dialog = false"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("Отменить") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="employeeInnDialog"
      @keydown.esc="employeeInnDialog = false"
      persistent
      max-width="600px"
    >
      <v-card class="pa-5">
        <v-card-title class="pa-0" primary-title>
          <span class="dialogTitle">{{ dialogHeaderText }}</span>
        </v-card-title>
        <v-divider color="#DCE5EF" class="my-1"></v-divider>
        <v-card-text>
          <v-form @keyup.native.enter="save" ref="dialogForm">
            <v-row class="mx-0 dialogForm">
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.INN") }}</label>
                <v-text-field
                  v-model="form.INN"
                  type="number"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.INPS") }}</label>
                <v-text-field
                  v-model="form.INPS"
                  type="number"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions class="pa-0 mt-5">
          <v-spacer></v-spacer>
          <v-btn
            class="mr-3"
            color="#3FCB5D"
            right
            small
            dark
            @click="save"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("save") }}
          </v-btn>
          <v-btn
            class=""
            color="red"
            right
            small
            dark
            @click="employeeInnDialog = false"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("Отменить") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="filterDialog" persistent max-width="800px">
      <v-card class="pa-5">
        <v-card-title class="pa-0" primary-title>
          <span class="dialogTitle">{{ $t("filter") }}</span>
        </v-card-title>
        <v-divider color="#DCE5EF" class="my-1"></v-divider>
        <v-card-text>
          <v-form @keyup.native.enter="save">
            <v-row class="mx-0 dialogForm">
              <v-col cols="6" sm="4" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.firstname") }}</label>
                <v-text-field
                  v-model="filterForm.firstname"
                  class="ma-0 pa-0"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" sm="4" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.lastname") }}</label>
                <v-text-field v-model="filterForm.lastname" hide-details="auto" dense outlined></v-text-field>
              </v-col>
              <v-col cols="6" sm="4" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.middlename") }}</label>
                <v-text-field v-model="filterForm.middlename" hide-details="auto" dense outlined></v-text-field>
              </v-col>
              <v-col cols="6" sm="4" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.nationality_id") }}</label>
                <v-autocomplete
                  clearable
                  v-model="filterForm.nationality_id"
                  :items="
                    nationalities.map((v) => ({
                      text: v['name_' + $i18n.locale],
                      value: v.id,
                    }))
                  "
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
              <v-col cols="6" sm="4" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.tabel") }}</label>
                <v-text-field v-model="filterForm.tabel" hide-details="auto" dense outlined></v-text-field>
              </v-col>
              <v-col cols="6" sm="4" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.INPS") }}</label>
                <v-text-field
                  v-model="filterForm.INPS"
                  type="number"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" sm="4" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.INN") }}</label>
                <v-text-field
                  v-model="filterForm.INN"
                  type="number"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" sm="4" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.born_date_from") }}</label>
                <v-menu
                  v-model="createdAtMenu11"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="290px"
                >
                  <template v-slot:activator="{ on }">
                    <v-text-field
                      v-model="filterForm.born_date_from"
                      readonly
                      v-on="on"
                      hide-details="auto"
                      dense
                      outlined
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    v-model="filterForm.born_date_from"
                    @input="createdAtMenu11 = false"
                  ></v-date-picker>
                </v-menu>
              </v-col>
              <v-col cols="6" sm="4" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.born_date_to") }}</label>
                <v-menu
                  v-model="createdAtMenu12"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="290px"
                >
                  <template v-slot:activator="{ on }">
                    <v-text-field
                      v-model="filterForm.born_date_to"
                      readonly
                      v-on="on"
                      hide-details="auto"
                      dense
                      outlined
                    ></v-text-field>
                  </template>
                  <v-date-picker v-model="filterForm.born_date_to" @input="createdAtMenu12 = false"></v-date-picker>
                </v-menu>
              </v-col>
              <v-col cols="6" sm="4" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("gender.gender") }}</label>
                <v-autocomplete
                  clearable
                  v-model="filterForm.gender"
                  :items="[
                    { text: $t('Male'), value: 'M' },
                    { text: $t('Female'), value: 'F' },
                  ]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions class="pa-0 mt-5">
          <v-spacer></v-spacer>
          <v-btn
            class=""
            color="primary"
            right
            small
            dark
            @click="getList()"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
          <v-icon>mdi-magnify</v-icon>
            {{ $t("filter") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="employeeStaffDialog" persistent max-width="800px">
      <v-card class="pa-5">
        <v-card-title class="pa-0" primary-title>
          <span class="dialogTitle">{{ employeeStaffDialogHeaderText }}</span>
        </v-card-title>
        <v-divider color="#DCE5EF" class="my-1"></v-divider>
        <v-card-text>
          <v-form @keyup.native.enter="saveEmployeeStaff" ref="employeeStaffDialogForm">
            <v-row class="mx-0 dialogForm">
              <v-col cols="12" class="pa-0 mb-3">
                <label class="labelTitle" for>{{ $t("employee.name") }}:</label>
                <!-- <label for>{{ $t('employee.name') }}:</label> -->
              </v-col>
              <v-col cols="12" class="pa-0 mb-3">
                <label class="labelTitle" for>{{ $t("employee.staff_id") }}</label>
                <v-autocomplete
                  clearable
                  v-model="employeeStaffForm.staff_id"
                  :items="staffList"
                  :item-text="'name_' + $i18n.locale"
                  item-value="id"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                  :full-width="true"
                >
                  <template v-slot:selection="{ item }">
                    <v-row class="mx-0 pa-2" style="font-size: 14px">
                      <v-col cols="12" class="pa-0 ma-0">
                        <b>
                          {{
                          item.department
                          ? item.department.department_code +
                          " " +
                          item.department["name_" + $i18n.locale]
                          : ""
                          }}
                        </b>
                      </v-col>
                      <v-col cols="12" class="pa-0 ma-0">
                        {{
                        item.position
                        ? item.position["name_" + $i18n.locale]
                        : ""
                        }}
                      </v-col>
                    </v-row>
                  </template>
                  <template v-slot:item="{ item }">
                    <v-row
                      style="
                        border-bottom: 1px solid #ccc;
                        font-size: 14px;
                        max-width: 700px;
                      "
                      class="mx-0 pa-0"
                    >
                      <v-col cols="12" class="pa-0 ma-0">
                        <b>
                          {{
                          item.department
                          ? item.department.department_code +
                          " " +
                          item.department["name_" + $i18n.locale]
                          : ""
                          }}
                        </b>
                      </v-col>
                      <v-col cols="12" class="pa-0 ma-0">
                        {{
                        item.position
                        ? item.position["name_" + $i18n.locale]
                        : ""
                        }}
                      </v-col>
                    </v-row>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col cols="6" sm="4" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.category") }}</label>
                <v-autocomplete
                  clearable
                  v-model="employeeStaffForm.tariff_scale_id"
                  :items="
                    tariffScales.map((v) => ({ text: v.category, value: v.id }))
                  "
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
              <v-col cols="6" sm="4" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.contract_number") }}</label>
                <v-text-field
                  v-model="employeeStaffForm.contract_number"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" sm="4" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.contract_date") }}</label>
                <v-text-field
                  v-model="employeeStaffForm.contract_date"
                  placeholder="YYYY-MM-DD"
                  :rules="dateRules"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" sm="4" class="py-0 px-1 ma-0">
                <label class="labelTitle" for>{{ $t("employee.enter_order_number") }}</label>
                <v-text-field
                  v-model="employeeStaffForm.enter_order_number"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" sm="4" class="py-0 px-1 ma-0">
                <label class="labelTitle" for>{{ $t("employee.enter_order_date") }}</label>
                <v-text-field
                  v-model="employeeStaffForm.enter_order_date"
                  hide-details="auto"
                  placeholder="YYYY-MM-DD"
                  :rules="dateRules"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" sm="4" class="py-0 px-1 ma-0">
                <label class="labelTitle" for>{{ $t("employee.first_work_date") }}</label>
                <v-text-field
                  v-model="employeeStaffForm.first_work_date"
                  hide-details="auto"
                  placeholder="YYYY-MM-DD"
                  :rules="dateRules"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" sm="4" class="py-0 px-1 ma-0" v-if="false">
                <label class="labelTitle" for>{{ $t("employee.leave_order_number") }}</label>
                <v-text-field
                  v-model="employeeStaffForm.leave_order_number"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" sm="4" class="py-0 px-1 ma-0" v-if="false">
                <label class="labelTitle" for>{{ $t("employee.leave_order_date") }}</label>
                <v-text-field
                  v-model="employeeStaffForm.leave_order_date"
                  hide-details="auto"
                  placeholder="YYYY-MM-DD"
                  :rules="dateRules"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" sm="4" class="py-0 px-1 ma-0">
                <v-checkbox v-model="employeeStaffForm.is_active" :label="$t('employee.active')"></v-checkbox>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions class="pa-0 mt-5">
          <v-spacer></v-spacer>
          <v-btn
            class="mr-3"
            color="#3FCB5D"
            right
            small
            dark
            @click="saveEmployeeStaff"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("save") }}
          </v-btn>
          <v-btn
            class=""
            color="red"
            right
            small
            dark
            @click="employeeStaffDialog = false"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("Отменить") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Employee History Staf -->
    <v-dialog
      v-model="employeeStaffHistoryDialog"
      persistent
      max-width="800px"
    >
      <v-card class="pa-5">
        <v-card-title class="pa-0" primary-title>
          <span class="dialogTitle">{{ employeeStaffDialogHeaderText }}</span>
        </v-card-title>
        <v-divider color="#DCE5EF" class="my-1"></v-divider>
        <v-card-text>
          <v-form @keyup.native.enter="saveEmployeeStaffHistory" ref="staffHistoryForm">
            <v-row class="mx-0 dialogForm">
              <v-col cols="12" class="pa-0 mb-3">
                <label class="labelTitle" for>{{ $t("employee.name") }}:</label>
                <!-- <label for>{{ $t('employee.name') }}:</label> -->
              </v-col>
              <v-col cols="6" sm="4" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("department.parent_id") }}</label>
                <v-text-field v-model="staffHistoryForm.parent" hide-details="auto" dense outlined></v-text-field>
              </v-col>
              <v-col cols="6" sm="4" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("profile.department") }}</label>
                <v-text-field
                  v-model="staffHistoryForm.department"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" sm="4" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("profile.employee_position") }}</label>
                <v-text-field
                  v-model="staffHistoryForm.position"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" sm="4" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.first_work_date") }}</label>
                <v-text-field
                  v-model="staffHistoryForm.enterOrderDate"
                  hide-details="auto"
                  placeholder="YYYY-MM-DD"
                  :rules="dateRules"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" sm="4" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.leave_date") }}</label>
                <v-text-field
                  v-model="staffHistoryForm.leaveOrderDate"
                  hide-details="auto"
                  placeholder="YYYY-MM-DD"
                  :rules="dateRules"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions class="pa-0 mt-5">
          <v-spacer></v-spacer>
          <v-btn
            class="mr-3"
            color="#3FCB5D"
            right
            small
            dark
            @click="saveEmployeeStaffHistory"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("save") }}
          </v-btn>
          <v-btn
            class=""
            color="red"
            right
            small
            dark
            @click="employeeStaffHistoryDialog = false"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("Отменить") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="employeeAddressDialog" persistent max-width="800px" :fullscreen="fullscreen">
      <v-card class="pa-5">
        <v-card-title class="pa-0" primary-title>
          <span class="dialogTitle">{{ employeeAddressDialogHeaderText }}</span>
        </v-card-title>
        <v-divider color="#DCE5EF" class="my-1"></v-divider>
        <v-card-text>
          <v-form @keyup.native.enter="saveEmployeeAddress" ref="employeeAddressDialogForm">
            <v-row class="mx-0 dialogForm">
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.address_type_id") }}</label>
                <v-autocomplete
                  clearable
                  v-model="employeeAddressForm.address_type_id"
                  :items="addressTypes"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.country_id") }}</label>
                <v-autocomplete
                  clearable
                  v-model="employeeAddressForm.country_id"
                  :items="countries"
                  :item-text="'name_' + $i18n.locale"
                  item-value="id"
                  @change="
                    employeeAddressForm.region_id = '';
                    employeeAddressForm.district_id = '';
                  "
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.region_id") }}</label>
                <v-autocomplete
                  clearable
                  v-model="employeeAddressForm.region_id"
                  :items="
                    regions.filter(
                      (v) => v.country_id == employeeAddressForm.country_id
                    )
                  "
                  :item-text="'name_' + $i18n.locale"
                  item-value="id"
                  @change="employeeAddressForm.district_id = ''"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.district_id") }}</label>
                <v-autocomplete
                  clearable
                  v-model="employeeAddressForm.district_id"
                  :items="
                    districts.filter(
                      (v) => v.region_id == employeeAddressForm.region_id
                    )
                  "
                  :item-text="'name_' + $i18n.locale"
                  item-value="id"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.street_address_uz_latin") }}</label>
                <v-text-field
                  v-model="employeeAddressForm.street_address_uz_latin"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.street_address_uz_cyril") }}</label>
                <v-text-field
                  v-model="employeeAddressForm.street_address_uz_cyril"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.street_address_ru") }}</label>
                <v-text-field
                  v-model="employeeAddressForm.street_address_ru"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.home_address_uz_latin") }}</label>
                <v-text-field
                  v-model="employeeAddressForm.home_address_uz_latin"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.home_address_uz_cyril") }}</label>
                <v-text-field
                  v-model="employeeAddressForm.home_address_uz_cyril"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.home_address_ru") }}</label>
                <v-text-field
                  v-model="employeeAddressForm.home_address_ru"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.description") }}</label>
                <v-text-field
                  v-model="employeeAddressForm.description"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions class="pa-0 mt-5">
          <v-spacer></v-spacer>
          <v-btn
            class="mr-3"
            color="#3FCB5D"
            right
            small
            dark
            @click="saveEmployeeAddress"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("save") }}
          </v-btn>
          <v-btn
            class=""
            color="red"
            right
            small
            dark
            @click="employeeAddressDialog = false"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("Отменить") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog
      v-model="employeeCoefficientDialog"
      persistent
      max-width="800px"
    >
      <v-card class="pa-5">
        <v-card-title class="pa-0" primary-title>
          <span class="dialogTitle">{{ employeeCoefficientDialogHeaderText }}</span>
        </v-card-title>
        <v-divider color="#DCE5EF" class="my-1"></v-divider>
        <v-card-text>
          <v-form @keyup.native.enter="saveEmployeeCoefficient" ref="employeeCoefficientDialogForm">
            <v-row class="mx-0 dialogForm">
              <v-col cols="6" class="py-0 px-1 mb-0">
                <label class="labelTitle" for>{{ $t("coefficient.type") }}</label>
                <v-autocomplete
                  clearable
                  v-model="employeeCoefficientForm.coefficient_id"
                  :items="coefficients"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-0">
                <label class="labelTitle" for>{{ $t("coefficient.percent") }}</label>
                <v-text-field
                  v-model="employeeCoefficientForm.percent"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-0">
                <label class="labelTitle" for>{{ $t("employee.begin_date") }}</label>
                <v-text-field
                  v-model="employeeCoefficientForm.begin_date"
                  placeholder="YYYY-MM-DD"
                  :rules="dateRules"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-0">
                <label class="labelTitle" for>{{ $t("employee.end_date") }}</label>
                <v-text-field
                  v-model="employeeCoefficientForm.end_date"
                  placeholder="YYYY-MM-DD"
                  :rules="dateRules"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-0">
                <label class="labelTitle" for>{{ $t("employee.order_date") }}</label>
                <v-text-field
                  v-model="employeeCoefficientForm.order_date"
                  placeholder="YYYY-MM-DD"
                  :rules="dateRules"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-0">
                <label class="labelTitle" for>{{ $t("coefficient.order_number") }}</label>
                <v-text-field
                  v-model="employeeCoefficientForm.order_number"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-0">
                <label class="labelTitle" for>{{ $t("employee.description") }}</label>
                <v-text-field
                  v-model="employeeCoefficientForm.description"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" sm="4" class="py-0 px-1 mb-0">
                <v-checkbox v-model="employeeCoefficientForm.status" :label="$t('employee.status')"></v-checkbox>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions class="pa-0 mt-5">
          <v-spacer></v-spacer>
          <v-btn
            class="mr-3"
            color="#3FCB5D"
            right
            small
            dark
            @click="saveEmployeeCoefficient"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("save") }}
          </v-btn>
          <v-btn
            class=""
            color="red"
            right
            small
            dark
            @click="employeeCoefficientDialog = false"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("Отменить") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="employeePhonesDialog" persistent max-width="800px" :fullscreen="fullscreen">
      <v-card class="pa-5">
        <v-card-title class="pa-0" primary-title>
          <span class="dialogTitle">{{ $t("employee.add_new_phone") }}</span>
        </v-card-title>
        <v-divider color="#DCE5EF" class="my-1"></v-divider>
        <v-card-text>
          <v-form @keyup.native.enter="saveEmployeePhones" ref="employeePhonesDialogForm">
            <v-row class="mx-0 dialogForm">
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.phone_type") }}</label>
                <v-text-field
                  placeholder="Mobile"
                  v-model="employeePhonesForm.phone_type"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.phone_number") }}</label>
                <v-text-field
                  v-model="employeePhonesForm.phone_number"
                  hide-details="auto"
                  type="number"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.description") }}</label>
                <v-text-field
                  v-model="employeePhonesForm.description"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions class="pa-0 mt-5">
          <v-spacer></v-spacer>
          <v-btn
            class="mr-3"
            color="#3FCB5D"
            right
            small
            dark
            @click="saveEmployeePhones"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("save") }}
          </v-btn>
          <v-btn
            class=""
            color="red"
            right
            small
            dark
            @click="employeePhonesDialog = false"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("Отменить") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="employeeDocumentDialog"
      @keydown.esc="dialog = false"
      persistent
      max-width="800px"
    >
      <v-card class="pa-5">
        <v-card-title class="pa-0" primary-title>
          <span class="dialogTitle">{{ dialogHeaderText }}</span>
        </v-card-title>
        <v-divider color="#DCE5EF" class="my-1"></v-divider>
        <v-card-text>
          <v-form ref="dialogForm">
            <v-row class="mx-0 dialogForm">
              <v-col cols="3" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employeeDocument.docType") }}</label>
                <v-autocomplete
                  outlined
                  class="pa-0"
                  clearable
                  v-model="employeeDocumentForm.official_document_type_id"
                  :items="docTypes"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details
                  dense
                  item-value="id"
                >
                  <template v-slot:selection="{ item }">
                    <v-chip color="white" class="pa-1 ma-0">
                      <span v-text="item.name_ru"></span>
                    </v-chip>
                  </template>
                  <template v-slot:item="{ item }">
                    <v-list-item-content>
                      <v-list-item-title v-text="item.name_ru"></v-list-item-title>
                    </v-list-item-content>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col cols="3" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employeeDocument.series") }}</label>
                <v-text-field
                  placeholder="AA"
                  v-model="employeeDocumentForm.series"
                  type="text"
                  hide-details="auto"
                  dense
                  outlined
                  :rules="[(v) => !!v || $t('input.required')]"
                ></v-text-field>
              </v-col>
              <v-col cols="3" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employeeDocument.number") }}</label>
                <v-text-field
                  placeholder="1234567"
                  v-model="employeeDocumentForm.number"
                  type="number"
                  hide-details="auto"
                  dense
                  outlined
                  :rules="[(v) => !!v || $t('input.required')]"
                ></v-text-field>
              </v-col>
              <v-col cols="3" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employeeDocument.date_issue") }}</label>
                <v-menu
                  v-model="menu"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="290px"
                >
                  <template v-slot:activator="{ on }">
                    <v-text-field
                      v-model="employeeDocumentForm.given_date"
                      readonly
                      v-on="on"
                      hide-details
                      class="white"
                      dense
                      outlined
                      :rules="[(v) => !!v || $t('input.required')]"
                      placeholder="YYYY-MM-DD"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    no-title
                    v-model="employeeDocumentForm.given_date"
                    @input="menu = false"
                  ></v-date-picker>
                </v-menu>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employeeDocument.given_by") }}</label>
                <v-text-field
                  placeholder="Asaka IIB"
                  v-model="employeeDocumentForm.given_organization"
                  type="text"
                  hide-details="auto"
                  dense
                  outlined
                  :rules="[(v) => !!v || $t('input.required')]"
                ></v-text-field>
              </v-col>
              <v-col cols="3" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employeeDocument.valid_until") }}</label>
                <v-menu
                  v-model="menu1"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="290px"
                >
                  <template v-slot:activator="{ on }">
                    <v-text-field
                      v-model="employeeDocumentForm.due_date"
                      readonly
                      v-on="on"
                      hide-details
                      class="white"
                      dense
                      outlined
                      :rules="[(v) => !!v || $t('input.required')]"
                      placeholder="YYYY-MM-DD"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    no-title
                    v-model="employeeDocumentForm.due_date"
                    @input="menu1 = false"
                  ></v-date-picker>
                </v-menu>
              </v-col>
              <v-col cols="3" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employeeDocument.title") }}</label>
                <v-text-field
                  placeholder="Pasport"
                  v-model="employeeDocumentForm.title"
                  type="text"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="3" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employeeDocument.status") }}</label>
                <v-autocomplete
                  v-model="employeeDocumentForm.is_active"
                  :items="selectStatus"
                  item-text="text"
                  item-value="value"
                  dense
                  outlined
                  :rules="[(v) => !!v || $t('input.required')]"
                ></v-autocomplete>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions class="pa-0 mt-5">
          <v-spacer></v-spacer>
          <v-btn
            class="mr-3"
            color="#3FCB5D"
            right
            small
            dark
            @click="saveEmployeeDocument"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("save") }}
          </v-btn>
          <v-btn
            class=""
            color="red"
            right
            small
            dark
            @click="employeeDocumentDialog = false"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("Отменить") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="employeeLanguageDialog"
      @keydown.esc="dialog = false"
      persistent
      max-width="800px"
    >
      <v-card class="pa-5">
        <v-card-title class="pa-0" primary-title>
          <span class="dialogTitle">{{ dialogHeaderText }}</span>
        </v-card-title>
        <v-divider color="#DCE5EF" class="my-1"></v-divider>
        <v-card-text>
          <v-form ref="dialogForm">
            <v-row class="mx-0 dialogForm">
              <v-col cols="4" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.select_language") }}</label>
                <v-autocomplete
                  outlined
                  class="pa-0"
                  clearable
                  v-model="employeeLanguageForm.hr_language_id"
                  :items="hrLanguages"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details
                  dense
                  item-value="id"
                >
                  <template v-slot:selection="{ item }">
                    <v-chip color="white" class="pa-1 ma-0">
                      <span v-text="item.name_ru"></span>
                    </v-chip>
                  </template>
                  <template v-slot:item="{ item }">
                    <v-list-item-content>
                      <v-list-item-title v-text="item.name_ru"></v-list-item-title>
                    </v-list-item-content>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col cols="3" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.level") }}</label>
                <v-text-field
                  placeholder="5"
                  v-model="employeeLanguageForm.level"
                  type="number"
                  hide-details="auto"
                  dense
                  outlined
                  :rules="[(v) => !!v || $t('input.required')]"
                ></v-text-field>
              </v-col>
              <v-col cols="5" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.description") }}</label>
                <v-text-field
                  v-model="employeeLanguageForm.description"
                  type="text"
                  hide-details="auto"
                  dense
                  outlined
                  :rules="[(v) => !!v || $t('input.required')]"
                ></v-text-field>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions class="pa-0 mt-5">
          <v-spacer></v-spacer>
          <v-btn
            class="mr-3"
            color="#3FCB5D"
            right
            small
            dark
            @click="saveEmployeeLanguage"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("save") }}
          </v-btn>
          <v-btn
            class=""
            color="red"
            right
            small
            dark
            @click="employeeLanguageDialog = false"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("Отменить") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog
      v-model="employeePartyDialog"
      @keydown.esc="dialog = false"
      persistent
      max-width="800px"
    >
      <v-card class="pa-5">
        <v-card-title class="pa-0" primary-title>
          <span class="dialogTitle">{{ dialogHeaderText }}</span>
        </v-card-title>
        <v-divider color="#DCE5EF" class="my-1"></v-divider>
        <v-card-text>
          <v-form ref="dialogForm">
            <v-row class="mx-0 dialogForm">
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.select_party") }}</label>
                <v-autocomplete
                  outlined
                  class="pa-0"
                  clearable
                  v-model="employeePartyForm.hr_party_id"
                  :items="hrParties"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details
                  dense
                  item-value="id"
                >
                  <template v-slot:selection="{ item }">
                    <v-chip color="white" class="pa-1 ma-0">
                      <span v-text="item.name_ru"></span>
                    </v-chip>
                  </template>
                  <template v-slot:item="{ item }">
                    <v-list-item-content>
                      <v-list-item-title v-text="item.name_ru"></v-list-item-title>
                    </v-list-item-content>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.description") }}</label>
                <v-text-field
                  v-model="employeePartyForm.description"
                  type="text"
                  hide-details="auto"
                  dense
                  outlined
                  :rules="[(v) => !!v || $t('input.required')]"
                ></v-text-field>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions class="pa-0 mt-5">
          <v-spacer></v-spacer>
          <v-btn
            class="mr-3"
            color="#3FCB5D"
            right
            small
            dark
            @click="saveEmployeeParty"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("save") }}
          </v-btn>
          <v-btn
            class=""
            color="red"
            right
            small
            dark
            @click="employeePartyDialog = false"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("Отменить") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog
      v-model="employeeMilitaryRankDialog"
      @keydown.esc="dialog = false"
      persistent
      max-width="800px"
    >
      <v-card class="pa-5">
        <v-card-title class="pa-0" primary-title>
          <span class="dialogTitle">{{ dialogHeaderText }}</span>
        </v-card-title>
        <v-divider color="#DCE5EF" class="my-1"></v-divider>
        <v-card-text>
          <v-form ref="dialogForm">
            <v-row class="mx-0 dialogForm">
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.select_military_rank") }}</label>
                <v-autocomplete
                  outlined
                  class="pa-0"
                  clearable
                  v-model="employeeMilitaryRankForm.hr_military_rank_id"
                  :items="hrMilitaryRanks"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details
                  dense
                  item-value="id"
                >
                  <template v-slot:selection="{ item }">
                    <v-chip color="white" class="pa-1 ma-0">
                      <span v-text="item.name_ru"></span>
                    </v-chip>
                  </template>
                  <template v-slot:item="{ item }">
                    <v-list-item-content>
                      <v-list-item-title v-text="item.name_ru"></v-list-item-title>
                    </v-list-item-content>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.description") }}</label>
                <v-text-field
                  v-model="employeeMilitaryRankForm.description"
                  type="text"
                  hide-details="auto"
                  dense
                  outlined
                  :rules="[(v) => !!v || $t('input.required')]"
                ></v-text-field>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions class="pa-0 mt-5">
          <v-spacer></v-spacer>
          <v-btn
            class="mr-3"
            color="#3FCB5D"
            right
            small
            dark
            @click="saveEmployeeMilitaryRank"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("save") }}
          </v-btn>
          <v-btn
            class=""
            color="red"
            right
            small
            dark
            @click="employeeMilitaryRankDialog = false"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("Отменить") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog
      v-model="employeeStateAwardDialog"
      @keydown.esc="dialog = false"
      persistent
      max-width="800px"
    >
      <v-card class="pa-5">
        <v-card-title class="pa-0" primary-title>
          <span class="dialogTitle">{{ dialogHeaderText }}</span>
        </v-card-title>
        <v-divider color="#DCE5EF" class="my-1"></v-divider>
        <v-card-text>
          <v-form ref="dialogForm">
            <v-row class="mx-0 dialogForm">
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.select_state_award") }}</label>
                <v-autocomplete
                  outlined
                  class="pa-0"
                  clearable
                  v-model="employeeStateAwardForm.hr_state_award_id"
                  :items="hrStateAwards"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details
                  dense
                  item-value="id"
                >
                  <template v-slot:selection="{ item }">
                    <v-chip color="white" class="pa-1 ma-0">
                      <span v-text="item.name_ru"></span>
                    </v-chip>
                  </template>
                  <template v-slot:item="{ item }">
                    <v-list-item-content>
                      <v-list-item-title v-text="item.name_ru"></v-list-item-title>
                    </v-list-item-content>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.description") }}</label>
                <v-text-field
                  v-model="employeeStateAwardForm.description"
                  type="text"
                  hide-details="auto"
                  dense
                  outlined
                  :rules="[(v) => !!v || $t('input.required')]"
                ></v-text-field>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions class="pa-0 mt-5">
          <v-spacer></v-spacer>
          <v-btn
            class="mr-3"
            color="#3FCB5D"
            right
            small
            dark
            @click="saveEmployeeStateAward"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("save") }}
          </v-btn>
          <v-btn
            class=""
            color="red"
            right
            small
            dark
            @click="employeeStateAwardDialog = false"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("Отменить") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog
      v-model="employeeRelativeDialog"
      @keydown.esc="dialog = false"
      persistent
      max-width="800px"
    >
      <v-card class="pa-5">
        <v-card-title class="pa-0" primary-title>
          <span class="dialogTitle">{{ dialogHeaderText }}</span>
        </v-card-title>
        <v-divider color="#DCE5EF" class="my-1"></v-divider>
        <v-card-text>
          <v-form ref="dialogForm">
            <v-row class="mx-0 dialogForm">
              <v-col cols="4" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.lastname") }}</label>
                <v-text-field
                  v-model="employeeRelativeForm.last_name"
                  type="text"
                  hide-details="auto"
                  dense
                  outlined
                  :rules="[(v) => !!v || $t('input.required')]"
                ></v-text-field>
              </v-col>
              <v-col cols="4" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.firstname") }}</label>
                <v-text-field
                  v-model="employeeRelativeForm.first_name"
                  type="text"
                  hide-details="auto"
                  dense
                  outlined
                  :rules="[(v) => !!v || $t('input.required')]"
                ></v-text-field>
              </v-col>
              <v-col cols="4" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.middlename") }}</label>
                <v-text-field
                  v-model="employeeRelativeForm.middle_name"
                  type="text"
                  hide-details="auto"
                  dense
                  outlined
                  :rules="[(v) => !!v || $t('input.required')]"
                ></v-text-field>
              </v-col>
              <v-col cols="4" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.born_date") }}</label>
                <v-text-field
                  placeholder="YYYY"
                  v-model="employeeRelativeForm.born_date"
                  type="text"
                  hide-details="auto"
                  dense
                  outlined
                  maxlength="4"
                  :rules="[(v) => !!v || $t('input.required')]"
                ></v-text-field>
              </v-col>
              <v-col cols="4" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.work_place") }}</label>
                <v-text-field
                  v-model="employeeRelativeForm.work_place"
                  type="text"
                  hide-details="auto"
                  dense
                  outlined
                  :rules="[(v) => !!v || $t('input.required')]"
                ></v-text-field>
              </v-col>
              <v-col cols="4" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.born_place") }}</label>
                <v-text-field
                  v-model="employeeRelativeForm.born_place"
                  type="text"
                  hide-details="auto"
                  dense
                  outlined
                  :rules="[(v) => !!v || $t('input.required')]"
                ></v-text-field>
              </v-col>
              <v-col cols="4" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.living_place") }}</label>
                <v-text-field
                  v-model="employeeRelativeForm.living_place"
                  type="text"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="4" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.family_relative") }}</label>
                <v-autocomplete
                  outlined
                  class="pa-0"
                  clearable
                  v-model="employeeRelativeForm.family_relative_id"
                  :items="familyRelativeTypes"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details
                  dense
                  item-value="id"
                >
                  <template v-slot:selection="{ item }">
                    <v-chip color="white" class="pa-1 ma-0">
                      <span v-text="item['name_' + $i18n.locale]"></span>
                    </v-chip>
                  </template>
                  <template v-slot:item="{ item }">
                    <v-list-item-content>
                      <v-list-item-title v-text="item['name_' + $i18n.locale]"></v-list-item-title>
                    </v-list-item-content>
                  </template>
                </v-autocomplete>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions class="pa-0 mt-5">
          <v-spacer></v-spacer>
          <v-btn
            class="mr-3"
            color="#3FCB5D"
            right
            small
            dark
            @click="saveEmployeeRelative"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("save") }}
          </v-btn>
          <v-btn
            class=""
            color="red"
            right
            small
            dark
            @click="employeeRelativeDialog = false"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("Отменить") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="fileDialog" persistent max-width="800px">
      <v-card class="pa-5">
        <v-card-title class="pa-0" primary-title>
          <span class="dialogTitle">{{ $t("employeeDocument.files") }}</span>
        </v-card-title>
        <v-divider color="#DCE5EF" class="my-1"></v-divider>
        <v-card-text>
          <v-form @keyup.native.enter="save" ref="dialogForm">
            <v-row class="mx-0 dialogForm">
              <v-col
                cols="10"
                style="min-width: 100px; max-width: 100%"
                class="flex-grow-1 flex-shrink-0 py-0 px-1 mb-3"
              >
                <label class="labelTitle" for>{{ $t("employeeDocument.uploadFiles") }}</label>
                <v-file-input
                  v-model="selectFiles"
                  multiple
                  outlined
                  dense
                  prepend-icon
                  append-icon="mdi-file-pdf-box-outline"
                  accept=".pdf"
                  small-chips
                  show-size
                  hide-details
                ></v-file-input>
              </v-col>
              <v-col cols="2" style="min-width: 100px" class="py-0 px-1 mb-3">
                <v-btn
                  :disabled="selectFiles.length == 0"
                  class="mt-6"
                  color="success"
                  block
                  @click="addFiles"
                >+</v-btn>
              </v-col>
            </v-row>

            <v-simple-table>
              <template v-slot:default>
                <thead>
                  <tr>
                    <th width="20" class="text-left">#</th>
                    <th class="text-left">{{ $t("employeeDocument.index") }}</th>
                    <th width="20" class="text-left"></th>
                    <th width="20" class="text-left"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in fileList" :key="index">
                    <td>{{ index + 1 }}</td>
                    <td style="max-width: 340px">{{ item.file_name }}</td>
                    <td>
                      <v-btn color="primary" text @click="viewPdfFile(item)">
                        <v-icon>mdi-download</v-icon>
                      </v-btn>
                    </td>
                    <td>
                      <v-icon color="error" @click="removeTmpFile(item.id)">mdi-minus-circle-outline</v-icon>
                    </td>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>
          </v-form>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog v-model="pdfViewDialog" width="800">
      <v-card class="pa-5">
        <v-card-title class="pa-0" primary-title>
          <span class="dialogTitle">{{ fileForView.file_name }}</span>
        </v-card-title>
        <v-divider color="#DCE5EF" class="my-1"></v-divider>        
        <v-card-text class>
          <pdf
            v-if="fileForView.id > 0"
            :src="
              $store.state.backend_url + 'staffs/get-file/' + fileForView.id
            "
          ></pdf>
        </v-card-text>
        <v-card-actions class="pa-0 mt-5">
          <v-spacer></v-spacer>
          <v-btn
            class="mr-3"
            color="#3FCB5D"
            right
            small
            dark
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
            text
            :href="
              $store.state.backend_url + 'staffs/get-file/' + fileForView.id
            "
          >{{ $t("download") }}</v-btn>
          <v-btn
            class=""
            color="red"
            right
            small
            dark
            @click="
              pdfViewDialog = false;
              fileForView.id = 0;
            "
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("close") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog
      v-model="dismissalEmployeeDialog"
      @keydown.esc="dismissalEmployeeDialog = false"
      persistent
      max-width="800px"
    >
      <v-card class="pa-5">
        <v-card-title class="pa-0" primary-title>
          <span class="dialogTitle">{{ $t("employee.dismissal") }}</span>
        </v-card-title>
        <v-divider color="#DCE5EF" class="my-1"></v-divider>
        <v-card-text>
          <v-form @keyup.native.enter="saveDismissalEmployee" ref="dismissalEmployeeStaffForm">
            <v-row class="mx-0 dialogForm">
              <v-col cols="12" sm="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.leave_date") }}</label>
                <v-menu
                  v-model="menuDismissalEmployee"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="290px"
                >
                  <template v-slot:activator="{ on }">
                    <v-text-field
                      v-model="dismissalEmployeeStaffForm.leave_date"
                      readonly
                      v-on="on"
                      hide-details
                      class="white"
                      dense
                      outlined
                      :rules="[(v) => !!v || $t('input.required')]"
                      placeholder="YYYY-MM-DD"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    no-title
                    v-model="dismissalEmployeeStaffForm.leave_date"
                    @input="menuDismissalEmployee = false"
                  ></v-date-picker>
                </v-menu>
              </v-col>
              <v-col cols="12" sm="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.leave_order_date") }}</label>
                <v-menu
                  v-model="menuDismissalEmployee1"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="290px"
                >
                  <template v-slot:activator="{ on }">
                    <v-text-field
                      v-model="dismissalEmployeeStaffForm.leave_order_date"
                      readonly
                      v-on="on"
                      hide-details
                      class="white"
                      dense
                      outlined
                      :rules="[(v) => !!v || $t('input.required')]"
                      placeholder="YYYY-MM-DD"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    no-title
                    v-model="dismissalEmployeeStaffForm.leave_order_date"
                    @input="menuDismissalEmployee1 = false"
                  ></v-date-picker>
                </v-menu>
              </v-col>
              <v-col cols="12" sm="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.leave_order_number") }}</label>
                <v-text-field
                  v-model="dismissalEmployeeStaffForm.leave_order_number"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" sm="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.leaving_reason_id") }}</label>
                <label class="labelTitle" for>{{ $t("purchase.measure") }}</label>
                <v-autocomplete
                  clearable
                  v-model="dismissalEmployeeStaffForm.leaving_reason_id"
                  :items="leavingReasons"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions class="pa-0 mt-5">
          <v-spacer></v-spacer>
          <v-btn
            class="mr-3"
            color="#3FCB5D"
            right
            small
            dark
            @click="saveDismissalEmployee"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("save") }}
          </v-btn>
          <v-btn
            class=""
            color="red"
            right
            small
            dark
            @click="dismissalEmployeeDialog = false"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("Отменить") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="dialogDocumentTransfer" hide-overlay persistent width="800">
      <v-card class="pa-5">
        <v-card-title class="pa-0" primary-title>
          <span class="dialogTitle"> {{ $t("document_transfer") }} </span>
        </v-card-title>
        <v-divider color="#DCE5EF" class="my-1"></v-divider>
        <v-card-text>
          <v-alert outlined icon="mdi-alert-outline" type="warning">
            {{ $t("it_is_impossible") }}
            <br />
            {{ $t("unfinished_documents") }}
          </v-alert>
          <v-btn
            block
            color="primary"
            @click="abc()"
          >Hodim dokumentlarini yangi shtatiga birga o'tsin.</v-btn>
          <br />
          <v-card outlined>
            <v-card-title class="pa-1 orange lighten-4">
              <v-spacer></v-spacer>
              {{ $t("documents_transferred") }}
              <v-spacer></v-spacer>
            </v-card-title>
            <v-card-text class="pb-0">
              <v-row class="mx-0" v-if="employeeDocumentTransfer.length">
                <v-col cols="9" class="py-0 px-1 mb-3">
                  <v-autocomplete
                    v-model="transfer_employee_id"
                    :items="employeeDocumentTransfer"
                    :label="$t('accessDepartment.employee')"
                    outlined
                    hide-details
                    dense
                    item-value="employee_id"
                    item-text="fio"
                    clearable
                  ></v-autocomplete>
                </v-col>
                <v-col cols="3" class="py-0 px-1 mb-3">
                  <v-btn
                    color="success"
                    outlined
                    block
                    @click="documentTransfer(1)"
                  >{{ $t("save") }}</v-btn>
                </v-col>
              </v-row>
              <v-row class="mx-0" v-else>
                <v-col cols="12" class="py-0 px-1 mb-3 text-center">{{ $t("noDataText") }}</v-col>
              </v-row>
            </v-card-text>
          </v-card>
          <v-card outlined>
            <v-card-title class="pa-1 orange lighten-4">
              <v-spacer></v-spacer>
              {{ $t("documents_transferred_employee") }}
              <v-spacer></v-spacer>
            </v-card-title>
            <v-card-text class="pb-0">
              <v-row class="mx-0" v-if="staffDocumentTransfer.length">
                <v-col cols="9" class="px-1">
                  <v-autocomplete
                    v-model="transfer_staff_id"
                    :items="staffDocumentTransfer"
                    :label="$t('staff.staff')"
                    outlined
                    hide-details
                    dense
                    item-value="id"
                    item-text="staff_name"
                    clearable
                  ></v-autocomplete>
                </v-col>
                <v-col cols="3" class="px-1">
                  <v-btn
                    color="success"
                    outlined
                    block
                    @click="documentTransfer(2)"
                  >{{ $t("save") }}</v-btn>
                </v-col>
              </v-row>
              <v-row v-else>
                <v-col class="text-center">{{ $t("noDataText") }}</v-col>
              </v-row>
            </v-card-text>
          </v-card>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog v-model="loading" width="300" hide-overlay>
      <v-card color="primary" dark>
        <v-card-text>
          {{ $t("loadingText") }}
          <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog v-model="downloadExcel" hide-overlay persistent width="300">
      <v-card>
        <v-card-text class="py-1 px-3">
          <v-btn color="success" class="mx-10" @click="downloadExcel = false" text>
            <download-excel :data="employee_excel" :name="'hodimlar_ruyxati_' + today + '.xls'">
              <span style="color: #4caf50">{{ $t("download") }}</span>
              <v-icon color="success" height="20">mdi-download</v-icon>
            </download-excel>
          </v-btn>
          <v-btn class color="error" @click="downloadExcel = false" icon>
            <v-icon color="error" height="20">mdi-close</v-icon>
          </v-btn>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog
      v-model="employeeWorkHistoryDialog"
      persistent
      max-width="800px"
    >
      <v-card class="pa-5">
        <v-card-title class="pa-0" primary-title>
          <span class="dialogTitle">{{ $t("employee.add_new_work_history") }}</span>
        </v-card-title>
        <v-divider color="#DCE5EF" class="my-1"></v-divider>
        <v-card-text>
          <v-form @keyup.native.enter="saveEmployeeWorkHistory" ref="employeeWorkHistoryDialogForm">
            <v-row class="mx-0 dialogForm">
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.begin_date") }}</label>
                <v-text-field
                  v-model="employeeWorkForm.begin_date"
                  hide-details="auto"
                  type="date"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.end_date") }}</label>
                <v-text-field                  
                  v-model="employeeWorkForm.end_date"
                  hide-details="auto"
                  type="date"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.work_place") }}</label>
                <v-text-field
                  placeholder="Uz Auto Motors"
                  v-model="employeeWorkForm.work_place"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.position") }}</label>
                <v-text-field
                  placeholder="Muxandis"
                  v-model="employeeWorkForm.position"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions class="pa-0 mt-5">
          <v-spacer></v-spacer>
          <v-btn
            class="mr-3"
            color="#3FCB5D"
            right
            small
            dark
            @click="saveEmployeeWorkHistory"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("save") }}
          </v-btn>
          <v-btn
            class=""
            color="red"
            right
            small
            dark
            @click="employeeWorkHistoryDialog = false"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("Отменить") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog
      v-model="employeeEducationHistoryDialog"
      persistent
      max-width="800px"
    >
      <v-card class="pa-5">
        <v-card-title class="pa-0" primary-title>
          <span class="dialogTitle">{{ $t("employee.add_new_education_history") }}</span>
        </v-card-title>
        <v-divider color="#DCE5EF" class="my-1"></v-divider>
        <v-card-text>
          <v-form
            @keyup.native.enter="saveEmployeeEducationHistory"
            ref="employeeEducationHistoryDialogForm"
          >
            <v-row class="mx-0 dialogForm">
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("Universities") }}</label>
                <v-autocomplete
                  clearable
                  v-model="employeeEducationForm.university_id"
                  :items="universities"
                  item-value="value"
                  item-text="text"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("Majors") }}</label>
                <v-autocomplete
                  clearable
                  v-model="employeeEducationForm.major_id"
                  :items="majors"
                  item-value="value"
                  item-text="text"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("hr_study_degree.index") }}</label>
                <v-autocomplete
                  clearable
                  v-model="employeeEducationForm.study_degree_id"
                  :items="study_degrees"
                  item-value="value"
                  item-text="text"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("study_type") }}</label>
                <v-autocomplete
                  clearable
                  v-model="employeeEducationForm.study_type_id"
                  :items="study_types"
                  item-value="value"
                  item-text="text"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("academic_title") }}</label>
                <v-text-field
                  placeholder="Yo'q"
                  v-model="employeeEducationForm.academic_title"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("academic_degree") }}</label>
                <v-text-field
                  placeholder="Magistr"
                  v-model="employeeEducationForm.academic_degree"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t("employee.end_date") }}</label>
                <v-text-field
                  v-model="employeeEducationForm.end_date"
                  hide-details="auto"
                  type="date"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions class="pa-0 mt-5">
          <v-spacer></v-spacer>
          <v-btn
            class="mr-3"
            color="#3FCB5D"
            right
            small
            dark
            @click="saveEmployeeEducationHistory"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("save") }}
          </v-btn>
          <v-btn
            class=""
            color="red"
            right
            small
            dark
            @click="employeeEducationHistoryDialog = false"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("Отменить") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
const axios = require("axios").default;
import Swal from "sweetalert2";
const moment = require("moment");
export default {
  data: () => ({
    dateRules: [
      v =>
        !v ||
        /^\d{4}[\-\-](0?[1-9]|1[012])[\-\-](0?[1-9]|[12][0-9]|3[01])$/.test(
          v
        ) ||
        "Date must be valid(YYYY-DD-MM)"
    ],
    document_transfer_new_staff: false,
    expanded: [],
    avatar: "",
    search: "",
    page: 1,
    from: 0,
    server_items_length: -1,
    today: moment().format("YYYY-MM-DD"),
    dataTableOptions: {
      page: 1,
      itemsPerPage: 50
    },
    dataTableValue: [],
    loading: false,
    image: null,
    downloadExcel: false,
    createdAtMenu1: false,
    createdAtMenu11: false,
    createdAtMenu12: false,
    createdAtMenu2: false,
    createdAtMenu3: false,
    createdAtMenu4: false,
    dialog: false,
    filterDialog: false,
    imageDialog: false,
    employeeStaffDialog: false,
    employeeStaffHistoryDialog: false,
    dismissalEmployeeDialog: false,
    fullscreen: false,
    editMode: null,
    items: [],
    companies: [],
    staff: [],
    tariffScales: [],
    positions: [],
    nationalities: [],
    countries: [],
    coefficients: [],
    regions: [],
    districts: [],
    employeeStaff: [],
    employeeCoefficients: [],
    employeeAddresses: [],
    employeePhones: [],
    employeeRelatives: [],
    addressTypes: [],
    form: {},
    dismissalEmployeeStaffForm: {},
    employeeStaffForm: {},
    employeeCoefficientForm: {},
    employeeAddressForm: {},
    employeePhonesForm: {},
    staffHistoryForm: {},
    employeeWorkForm: {},
    employeeEducationForm: {},
    employeeDocuments: "",
    employeeDocumentDialog: false,
    employeeDocumentForm: {
      files: []
    },
    employeeLanguages: [],
    employeeLanguageDialog: false,
    employeeLanguageForm: {},
    employeeParties: [],
    employeePartyDialog: false,
    employeePartyForm: {},
    employeeMilitaryRanks: [],
    employeeMilitaryRankDialog: false,
    employeeMilitaryRankForm: {},
    employeeStateAwards: [],
    employeeStateAwardDialog: false,
    employeeStateAwardForm: {},
    employeeRelativeForm: {},
    employeeRelativeDialog: false,
    employeeWorkHistoryDialog: false,
    employeeEducationHistoryDialog: false,
    menuRelative: "",
    menu: "",
    menu1: "",
    menuDismissalEmployee: "",
    menuDismissalEmployee1: "",
    leavingReasons: [],
    study_types: [],
    universities: [],
    majors: [],
    study_degrees: [],
    officialDocument: [],
    employeeRelative: [],
    familyRelativeTypes: "",
    disabled: false,
    selectStatus: [
      {
        text: "Not active",
        value: 0
      },
      {
        text: "Active",
        value: 1
      }
    ],
    docTypes: "",
    hrLanguages: "",
    hrParties: "",
    hrMilitaryRanks: "",
    hrStateAwards: "",
    fileDialog: false,
    pdfViewDialog: false,
    selectFiles: [],
    fileForView: {
      id: 0
    },
    objectTypesList: [],
    objectId: "",
    formData: null,
    filterForm: {
      id: Date.now(),
      company_id: "",
      country_id: "",
      nationality_id: "",
      region_id: "",
      district_id: "",
      address: "",
      tabel: "",
      firstname: "",
      lastname: "",
      middlename: "",
      born_date_from: "",
      born_date_to: "",
      INN: "",
      gender: "",
      INPS: "",
      department_code: "",
      info: "",
      category: "",
      department_name: "",
      position: "",
      employee_staff: "",
      first_work_date: ""
    },
    genders: [
      {
        name: "Erkak",
        value: "M"
      },
      {
        name: "Ayol",
        value: "F"
      }
    ],
    employee: {
      base64: null
    },
    dialogHeaderText: "",
    employeeStaffDialogHeaderText: "",
    employeeAddressDialogHeaderText: "",
    employeeCoefficientDialogHeaderText: "",
    employeePhonesDialogHeaderText: "",
    employeeDocumentDialogHeaderText: "",
    employeeRelativeDialogHeaderText: "",
    employeeCoefficientDialog: false,
    employeeAddressDialog: false,
    employeePhonesDialog: false,
    employeeInnDialog: false,
    employee_excel: [],
    dialogDocumentTransfer: false,
    staffDocumentTransfer: [],
    transfer_staff_id: null,
    employee_staff_id: null,
    employeeDocumentTransfer: [],
    transfer_employee_id: null,
    employee_staff_id: null
  }),
  computed: {
    screenHeight() {
      return window.innerHeight - 175;
    },
    fileList() {
      return this.employeeDocumentForm.files;
    },
    staffList() {
      if (this.staff && this.staff.length)
        return this.staff.map(value => {
          let v = value;
          v.name_uz_latin =
            (v.department
              ? v.department.department_code + v.department.name_uz_latin
              : "") +
            " " +
            (v.position ? v.position.name_uz_latin : "") +
            " " +
            (v.range ? v.range.code : "");
          v.name_uz_cyril =
            (v.department
              ? v.department.department_code + v.department.name_uz_cyril
              : "") +
            " " +
            (v.position ? v.position.name_uz_cyril : "") +
            " " +
            (v.range ? v.range.code : "");
          v.name_ru =
            (v.department
              ? v.department.department_code + v.department.name_ru
              : "") +
            " " +
            (v.position ? v.position.name_ru : "") +
            " " +
            (v.range ? v.range.code : "");
          return v;
        });
      else return [];
    },
    headers() {
      return [
        {
          text: "",
          value: "data-table-expand",
          width: 30
        },
        {
          text: "#",
          value: "id",
          align: "center",
          width: 30
        },
        {
          text: this.$t("department.department_code"),
          value: "department_code",
          width: 80
        },
        {
          text: this.$t("employee.tabel"),
          value: "tabel",
          align: "center",
          width: 50
        },
        {
          text: this.$t("employee.info"),
          value: "info"
        },
        {
          text: this.$t("employee.department_id"),
          value: "department_id"
        },
        {
          text: this.$t("employee.position"),
          value: "staffs"
        },
        {
          text: this.$t("employee.first_work_date"),
          value: "employee_staff",
          align: "center",
          width: 100
        }
        // {
        //   text: this.$t("employee.nationality_id"),
        //   value: "nationality.name_" + this.$i18n.locale,
        //   width: 30
        // },
        // { text: this.$t("employee.INN"), value: "INN", width: 30 },
        // { text: this.$t("employee.INPS"), value: "INPS", width: 30 },
      ];
    }
  },
  methods: {
    getHistory({ item, value }) {
      axios
        .get(
          this.$store.state.backend_url +
            "api/employees/get-history/" +
            item.id +
            "/" +
            this.$i18n.locale
        )
        .then(response => {
          this.items = this.items.map(v => {
            if (v.id == item.id) {
              v.histories = response.data;
            }
            return v;
          });
        })
        .catch(error => {
          console.log(error);
        });
    },
    getAvatar({ item, value }) {
      if (!item.base64 && value)
        axios
          .get(
            this.$store.state.backend_url +
              "api/employees/get-avatar/" +
              item.id
          )
          .then(response => {
            this.items = this.items.map(v => {
              if (v.id == item.id) {
                v.base64 = response.data;
              }
              return v;
            });
          })
          .catch(error => {
            console.log(error);
          });
      this.getHistory({ item, value });
    },
    toggleExpand($event) {},
    rowClick(item, row) {
      row.expand(!row.isExpanded);
      // if (this.expanded[0] && this.expanded[0].id == item.id) this.expanded = [];
      // else this.expanded = [item];
      this.employee = item;
      this.employeeStaff = item.employee_staff;
      this.employeeCoefficients = item.employee_coefficients;
      this.employeeAddresses = item.employee_addresses;
    },
    updatePage($event) {
      this.getList();
    },
    updatePerPage($event) {
      this.getList();
    },
    addPhoto() {
      this.imageDialog = true;
    },
    getRef() {
      let locale = this.$i18n.locale;
      axios
        .get(this.$store.state.backend_url + "api/employees/get-ref/" + locale)
        .then(response => {
          this.employees = response.data.employees;
          this.leavingReasons = response.data.leaving_reasons.map(v => {
            return {
              value: v.id,
              text: v["name_" + locale]
            };
          });
          this.companies = response.data.companies;
          this.staff = response.data.staff;
          this.tariffScales = response.data.tariff_scales;
          this.study_types = response.data.study_types.map(v => ({
            value: v.id,
            text: v["name_" + locale]
          }));
          this.universities = response.data.universities.map(v => ({
            value: v.id,
            text: v["name_" + locale]
          }));
          this.majors = response.data.majors.map(v => ({
            value: v.id,
            text: v["name_" + locale]
          }));
          this.study_degrees = response.data.study_degrees.map(v => ({
            value: v.id,
            text: v["name_" + locale]
          }));
          this.countries = response.data.countries;
          this.regions = response.data.regions;
          this.districts = response.data.districts;
          this.nationalities = response.data.nationalities;
          this.addressTypes = response.data.address_types.map(v => ({
            value: v.id,
            text: v["name_" + locale]
          }));
          this.coefficients = response.data.coefficients.map(v => ({
            value: v.id,
            text: v.code + " " + v.description
          }));
        })
        .catch(error => {
          console.log(error);
        });
    },
    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/employeesView", {
          pagination: this.dataTableOptions,
          filter: this.filterForm,
          search: this.search,
          locale: this.$i18n.locale
        })
        .then(response => {
          this.items = response.data.employees.data.map(v => {
            v.base64 = null;
            return v;
          });

          this.items = this.items.reduce((acc, current) => {
            const x = acc.find(item => item.id === current.id);
            if (!x) {
              return acc.concat([current]);
            } else {
              return acc;
            }
          }, []);
          this.from = response.data.employees.from;
          this.server_items_length = response.data.employees.total;
          // this.server_items_length = response.data.count;
          this.loading = false;
          this.getRef();
        })
        .catch(error => {
          console.log(error);
          this.loading = false;
        });
    },
    getEmployeeExcel(page) {
      let new_array = [];
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/employees/get-excel", {
          locale: this.$i18n.locale,
          page: page,
          perPage: 1000
        })
        .then(response => {
          new_array = response.data.data.map(v => {
            v.department_code =
              v.staff[0] && v.staff[0].department
                ? v.staff[0].department.department_code
                : "";
            v.tabel = "tab_" + v.tabel;
            v.department_name =
              v.staff[0] && v.staff[0].department
                ? v.staff[0].department["name_" + this.$i18n.locale]
                : "";
            v.position =
              v.staff[0] && v.staff[0].position
                ? v.staff[0].position["name_" + this.$i18n.locale]
                : "";
            // v.positions = 'sasa';
            return v;
          });
          // console.log()
          this.employee_excel = this.employee_excel.concat(new_array);
          if (response.data.data.length == 1000) {
            this.getEmployeeExcel(++page);
          } else {
            this.loading = false;
            this.downloadExcel = true;
          }
        })
        .catch(error => {
          console.log(error);
          this.loading = false;
        });
    },
    dismissalEmployee(item) {
      if (item.employee_staff.length) {
        Swal.fire(this.$t("employee.errorDismissalEmployee"));
      } else {
        this.employeeStaffDialogHeaderText = "Remove Employee";
        this.dismissalEmployeeStaffForm = {
          employee_id: item.id,
          leave_date: "",
          leave_order_date: "",
          leave_order_number: "",
          leaving_reason_id: null
        };
        this.dismissalEmployeeDialog = true;
      }
    },
    newEmployeeAddress(item) {
      this.employeeAddressDialogHeaderText = this.$t(
        "employee.newEmployeeAddress"
      );
      this.employeeAddressForm = {
        id: Date.now(),
        employee_id: item.id,
        address_type_id: "",
        country_id: "",
        region_id: "",
        district_id: "",
        street_address_ru: "",
        street_address_uz_cyril: "",
        street_address_uz_latin: "",
        home_address_ru: "",
        home_address_uz_cyril: "",
        home_address_uz_latin: "",
        description: "",
        created_by: "",
        updated_by: ""
      };
      this.employeeAddressDialog = true;

      if (this.$refs.EmployeeAddressDialogForm)
        this.$refs.EmployeeAddressDialogForm.reset();
    },
    newEmployeeCoefficient(item) {
      this.employeeCoefficientDialogHeaderText = this.$t(
        "employee.newEmployeeCoefficient"
      );
      this.employeeCoefficientForm = {
        id: Date.now(),
        employee_id: item.id,
        coefficient_id: "",
        begin_date: "",
        end_date: "",
        order_number: "",
        order_date: "",
        description: "",
        status: "",
        created_by: "",
        updated_by: "",
        percent: ""
      };
      this.employeeCoefficientDialog = true;

      if (this.$refs.employeeCoefficientDialogForm)
        this.$refs.employeeCoefficientDialogForm.reset();
    },
    newEmployeeStaff(item) {
      this.employeeStaffDialogHeaderText = this.$t("employee.newEmployeeStaff");
      this.employeeStaffForm = {
        id: Date.now(),
        employee_id: item.id,
        staff_id: "",
        tariff_scale_id: "",
        position_id: "",
        contract_number: "",
        contract_date: "",
        enter_order_number: "",
        enter_order_date: "",
        first_work_date: "",
        leave_order_number: "",
        leave_order_date: "",
        is_main_staff: true,
        is_active: true
      };
      this.employeeStaffDialog = true;

      if (this.$refs.EmployeeStaffDialogForm)
        this.$refs.EmployeeStaffDialogForm.reset();
    },
    newEmployeePhone(item) {
      this.employeePhonesDialogHeaderText = this.$t("employee.add_new_phone");
      this.employeePhonesForm = {
        id: Date.now(),
        employee_id: item.id,
        phone_type: "",
        phone_number: "",
        description: ""
      };
      this.employeePhonesDialog = true;

      if (this.$refs.EmployeePhonesDialogForm)
        this.$refs.EmployeePhonesDialogForm.reset();
    },
    newEmployeeDocumentItem(item) {
      this.dialogHeaderText = this.$t("employeeDocument.addEmployeeDocument");
      this.employeeDocumentForm = {
        id: Date.now(),
        series: "",
        number: "",
        given_organization: "",
        given_date: "",
        due_date: "",
        is_active: "",
        title: "",
        employee_id: item.id
      };
      this.employeeDocumentDialog = true;
    },
    newEmployeeLanguageItem(item) {
      this.dialogHeaderText = this.$t("employee.add_language");
      this.employeeLanguageForm = {
        id: Date.now(),
        hr_language_id: "",
        level: "",
        description: "",
        employee_id: item.id
      };
      this.employeeLanguageDialog = true;
    },
    newEmployeePartyItem(item) {
      this.dialogHeaderText = this.$t("hr_party.add");
      this.employeePartyForm = {
        id: Date.now(),
        hr_party_id: "",
        description: "",
        employee_id: item.id
      };
      this.employeePartyDialog = true;
    },
    newEmployeeMilitaryRankItem(item) {
      this.dialogHeaderText = this.$t("employee.add_military_rank");
      this.employeeMilitaryRankForm = {
        id: Date.now(),
        hr_military_rank_id: "",
        description: "",
        employee_id: item.id
      };
      this.employeeMilitaryRankDialog = true;
    },
    newEmployeeStateAwardItem(item) {
      this.dialogHeaderText = this.$t("employee.add_state_award");
      this.employeeStateAwardForm = {
        id: Date.now(),
        hr_state_award_id: "",
        description: "",
        employee_id: item.id
      };
      this.employeeStateAwardDialog = true;
    },
    newEmployeeRelative(item) {
      this.dialogHeaderText = this.$t("employee.add_employee_relative");
      this.employeeRelativeForm = {
        id: Date.now(),
        employee_id: item.id,
        family_relative_id: "",
        last_name: "",
        first_name: "",
        middle_name: "",
        born_date: "",
        born_place: "",
        work_place: "",
        living_place: ""
      };
      this.employeeRelativeDialog = true;
    },
    newEmployeeWorkHistory(item) {
      // this.employeeWorkHistoryDialogHeaderText = this.$t("employee.add_new_work_history");
      this.employeeWorkForm = {
        id: Date.now(),
        employee_id: item.id,
        begin_date: "",
        end_date: "",
        work_place: "",
        position: ""
      };
      this.employeeWorkHistoryDialog = true;

      if (this.$refs.EmployeeWorkHistoryDialogForm)
        this.$refs.EmployeeWorkHistoryDialogForm.reset();
    },
    newEmployeeEducationHistory(item) {
      // this.employeeEducationHistoryDialogHeaderText = this.$t("employee.add_new_work_history");
      this.employeeEducationForm = {
        id: Date.now(),
        employee_id: item.id,
        university_id: "",
        major_id: "",
        study_type_id: "",
        study_degree_id: "",
        academic_title: "",
        academic_degree: "",
        end_date: ""
      };
      this.employeeEducationHistoryDialog = true;

      if (this.$refs.EmployeeEducationHistoryDialogForm)
        this.$refs.EmployeeEducationHistoryDialogForm.reset();
    },
    newItem() {
      this.dialogHeaderText = this.$t("employee.new_employee");
      this.form = {
        id: Date.now(),
        company_id: "",
        country_id: "",
        nationality_id: "",
        region_id: "",
        district_id: "",
        address: "",
        tabel: "",
        firstname_uz_latin: "",
        lastname_uz_latin: "",
        middlename_uz_latin: "",
        firstname_uz_cyril: "",
        lastname_uz_cyril: "",
        middlename_uz_cyril: "",
        born_date: "",
        tariff_scale_id: "",
        INN: "",
        INPS: "",
        dr_tb: ""
      };
      this.dialog = true;

      if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
    },
    editItem(item) {
      this.dialogHeaderText = this.$t("employee.editEmployee");
      this.form = Object.assign({}, item);
      this.dialog = true;

      if (this.$refs.dialogForm) this.$refs.dialogForm.resetValidation();
    },
    editEmployeeInn(item) {
      this.dialogHeaderText = this.$t("edit");
      this.form = Object.assign({}, item);
      this.employeeInnDialog = true;

      if (this.$refs.dialogForm) this.$refs.dialogForm.resetValidation();
    },
    abc() {
      this.dialogDocumentTransfer = false;
      this.document_transfer_new_staff = true;
      this.employeeStaffForm.document_transfer_new_staff = this.document_transfer_new_staff;
      this.employeeStaffDialog = true;
      // this.editEmployeeStaff(this.item.find(v => v.id == this.employee_staff_id));
    },
    editEmployeeStaff(item) {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/count-document", {
          employee_id: item.employee_id,
          staff_id: item.staff_id,
          locale: this.$i18n.locale
        })
        .then(res => {
          this.employeeStaffDialogHeaderText = this.$t(
            "employee.editEmployeeStaff"
          );
          this.employeeStaffForm = Object.assign({}, item);
          if (
            res.data.document_count == 0 ||
            this.document_transfer_new_staff
          ) {
            this.employeeStaffDialog = true;
          } else {
            this.employee_staff_id = item.id;
            this.dialogDocumentTransfer = true;
            this.employeeDocumentTransfer = res.data.employees;
            this.staffDocumentTransfer = res.data.staffs;
            this.employeeDocumentTransfer.map(v => {
              if (this.$i18n.locale == "ru") {
                v.fio =
                  v.employee.tabel +
                  " " +
                  v.employee.firstname_uz_cyril +
                  " " +
                  v.employee.lastname_uz_cyril +
                  " " +
                  v.employee.middlename_uz_cyril;
              } else {
                v.fio =
                  v.employee.tabel +
                  " " +
                  v.employee["firstname_" + this.$i18n.locale] +
                  " " +
                  v.employee["lastname_" + this.$i18n.locale] +
                  " " +
                  v.employee["middlename_" + this.$i18n.locale];
              }
            });
            this.staffDocumentTransfer.map(v => {
              v.staff_name =
                v.department.department_code +
                " " +
                v.department["name_" + this.$i18n.locale] +
                " | " +
                v.position["name_" + this.$i18n.locale];
            });
          }
          this.loading = false;
        })
        .catch(err => {
          this.loading = false;
          console.log(err);
        });

      if (this.$refs.employeeStaffDialogForm)
        this.$refs.employeeStaffDialogForm.resetValidation();
    },
    editEmployeeStaffHistory(item) {
      this.loading = true;
      this.staffHistoryForm = Object.assign({}, item);
      this.employeeStaffHistoryDialog = true;
      this.loading = false;
      if (this.$refs.employeeStaffDialogForm)
        this.$refs.employeeStaffDialogForm.resetValidation();
    },
    editEmployeeAddress(item) {
      this.employeeAddressDialogHeaderText = this.$t(
        "employee.editEmployeeAddress"
      );
      this.employeeAddressForm = Object.assign({}, item);
      this.employeeAddressDialog = true;

      if (this.$refs.employeeAddressDialogForm)
        this.$refs.employeeAddressDialogForm.resetValidation();
    },
    editEmployeeCoefficient(item) {
      this.employeeCoefficientDialogHeaderText = this.$t(
        "employee.editemployeeCoefficient"
      );
      this.employeeCoefficientForm = Object.assign({}, item);
      this.employeeCoefficientDialog = true;
      if (this.$refs.employeeCoefficientDialogForm)
        this.$refs.employeeCoefficientDialogForm.resetValidation();
    },
    editEmployeePhone(item) {
      this.employeePhonesDialogHeaderText = this.$t("edit");
      this.employeePhonesForm = Object.assign({}, item);
      this.employeePhonesDialog = true;

      if (this.$refs.employeePhonesDialogForm)
        this.$refs.employeePhonesDialogForm.resetValidation();
    },
    editEmployeeWorkHistory(item) {
      this.employeeWorkHistoryDialogHeaderText = this.$t("edit");
      this.employeeWorkForm = Object.assign({}, item);
      this.employeeWorkHistoryDialog = true;

      if (this.$refs.employeeWorkHistoryDialogForm)
        this.$refs.employeeWorkHistoryDialogForm.resetValidation();
    },
    editEmployeeEducationHistory(item) {
      this.employeeEducationHistoryDialogHeaderText = this.$t("edit");
      this.employeeEducationForm = Object.assign({}, item);
      this.employeeEducationHistoryDialog = true;

      if (this.$refs.employeeEducationHistoryDialog)
        this.$refs.employeeEducationHistoryDialog.resetValidation();
    },
    editEmployeeDocumentItem(item) {
      this.dialogHeaderText = this.$t(
        "employeeDocument.updateEmployeeDocument"
      );
      this.employeeDocumentForm = {
        id: item.id,
        series: item.series,
        number: item.number,
        given_organization: item.given_organization,
        given_date: item.given_date,
        due_date: item.due_date,
        is_active: item.is_active,
        title: item.title,
        employee_id: item.employee_id,
        official_document_type_id: item.official_document_type_id
      };
      this.employeeDocumentDialog = true;
    },
    editEmployeeLanguageItem(item) {
      this.dialogHeaderText = this.$t("employee.edit_language");
      this.employeeLanguageForm = {
        id: item.id,
        level: item.level,
        description: item.description,
        employee_id: item.employee_id,
        hr_language_id: item.hr_language_id
      };
      this.employeeLanguageDialog = true;
    },
    editEmployeePartyItem(item) {
      this.dialogHeaderText = this.$t("edit");
      this.employeePartyForm = {
        id: item.id,
        description: item.description,
        employee_id: item.employee_id,
        hr_party_id: item.hr_party_id
      };
      this.employeePartyDialog = true;
    },
    editEmployeeMilitaryRankItem(item) {
      this.dialogHeaderText = this.$t("edit");
      this.employeeMilitaryRankForm = {
        id: item.id,
        description: item.description,
        employee_id: item.employee_id,
        hr_military_rank_id: item.hr_military_rank_id
      };
      this.employeeMilitaryRankDialog = true;
    },
    editEmployeeStateAwardItem(item) {
      this.dialogHeaderText = this.$t("edit");
      this.employeeStateAwardForm = {
        id: item.id,
        description: item.description,
        employee_id: item.employee_id,
        hr_state_award_id: item.hr_state_award_id
      };
      this.employeeStateAwardDialog = true;
    },
    editEmployeeRelative(item) {
      this.dialogHeaderText = this.$t("employee.edit_employee_relative");
      this.employeeRelativeForm = {
        id: item.id,
        employee_id: item.employee_id,
        family_relative_id: item.family_relative_id,
        last_name: item.last_name,
        first_name: item.first_name,
        middle_name: item.middle_name,
        born_date: item.born_date,
        born_place: item.born_place,
        work_place: item.work_place,
        living_place: item.living_place
      };
      this.employeeRelativeDialog = true;
    },
    changeMainStaff(item) {
      if (
        item.is_main_staff == 0 &&
        this.$store.getters.checkPermission("change-main-staff")
      ) {
        axios
          .post(this.$store.state.backend_url + "api/change-main-staff", {
            employee_id: item.employee_id,
            staff_id: item.staff_id
          })
          .then(res => {})
          .catch(err => {
            console.log(err);
          });

        this.items.find(v => {
          if (v.id == item.employee_id) {
            v.employee_staff.map(va => {
              if (va.staff_id == item.staff_id) {
                va.is_main_staff = 1;
              } else {
                va.is_main_staff = 0;
              }
            });
          }
        });
      }
    },
    documentTransfer(type) {
      if (this.transfer_employee_id && type == 1) {
        this.dialogDocumentTransfer = false;
        this.loading = true;
        axios
          .post(this.$store.state.backend_url + "api/transfer-document", {
            old_employee_staff_id: this.employee_staff_id,
            transfer_employee_id: this.transfer_employee_id,
            type: type
          })
          .then(res => {
            this.loading = false;
          })
          .catch(err => {
            this.loading = false;
            console.log(err);
          });
      }
      if (this.transfer_staff_id && type == 2) {
        this.dialogDocumentTransfer = false;
        this.loading = true;
        axios
          .post(this.$store.state.backend_url + "api/transfer-document", {
            old_employee_staff_id: this.employee_staff_id,
            transfer_staff_id: this.transfer_staff_id,
            type: type
          })
          .then(res => {
            this.loading = false;
          })
          .catch(err => {
            this.loading = false;
            console.log(err);
          });
      }
    },
    savePhoto(item) {
      if (this.$refs.imageDialogForm.validate()) this.loading = true;
      this.formData = new FormData();
      this.loading = true;
      this.formData.append("image", this.image);
      axios
        .post(
          this.$store.state.backend_url +
            "api/official-documents/update-avatar/" +
            item.tabel,
          this.formData,
          {
            headers: {
              "Content-Type": "multipart/form-data"
            }
          }
        )
        .then(response => {
          this.imageDialog = false;
          this.loading = false;
          this.items = this.items.map(v => {
            if (v.id == item.id) {
              v.base64 = response.data;
            }
            return v;
          });
          const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
          });
          Toast.fire({
            icon: "success",
            title: this.$t("create_update_operation")
          });
          this.loading = false;
        })
        .catch(function(error) {
          console.log(error);
          this.loading = false;
        });
    },
    saveEmployeeStaff() {
      if (this.$refs.employeeStaffDialogForm.validate())
        axios
          .post(
            this.$store.state.backend_url +
              "api/employees/update-employee-staff",
            this.employeeStaffForm
          )
          .then(res => {
            this.document_transfer_new_staff = false;
            if (res.data != 0) {
              this.items = this.items.map(v => {
                if (v.id == this.employeeStaffForm.employee_id) {
                  v.employee_staff = v.employee_staff.filter(
                    es => es.id != this.employeeStaffForm.id
                  );
                  if (res.data.is_active == 1) v.employee_staff.push(res.data);
                }
                return v;
              });
              this.employeeStaffDialog = false;
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
            } else {
              this.employeeStaffDialog = false;
              Swal.fire({
                icon: "error",
                title: this.$t("swal_error_text")
              });
            }
          })
          .catch(err => {
            console.log(err);
          });
    },
    //  Employee History Staff
    saveEmployeeStaffHistory() {
      if (this.$refs.staffHistoryForm.validate())
        axios
          .post(
            this.$store.state.backend_url +
              "api/employee-staff/update-employee-history-staff",
            {
              form: this.staffHistoryForm
            }
          )
          .then(res => {
            this.employeeStaffHistoryDialog = false;
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
    saveDismissalEmployee() {
      if (this.$refs.dismissalEmployeeStaffForm.validate())
        axios
          .post(
            this.$store.state.backend_url + "api/employee-staff/update",
            this.dismissalEmployeeStaffForm
          )
          .then(res => {
            this.dismissalEmployeeDialog = false;
          })
          .catch(err => {
            console.log(err);
          });
    },
    saveEmployeeAddress() {
      if (this.$refs.employeeAddressDialogForm.validate())
        axios
          .post(
            this.$store.state.backend_url +
              "api/employees/update-employee-address",
            this.employeeAddressForm
          )
          .then(res => {
            this.items = this.items.map(v => {
              if (v.id == this.employeeAddressForm.employee_id) {
                if (v.employee_addresses.find(v => v.id == res.data.id)) {
                  v.employee_addresses = v.employee_addresses.map(val => {
                    if (val.id == res.data.id) return res.data;
                    return val;
                  });
                } else {
                  v.employee_addresses.push(res.data);
                }
              }
              return v;
            });
            this.employeeAddressDialog = false;
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
    saveEmployeeCoefficient() {
      if (this.$refs.employeeCoefficientDialogForm.validate())
        axios
          .post(
            this.$store.state.backend_url +
              "api/employees/update-employee-coefficient",
            this.employeeCoefficientForm
          )
          .then(res => {
            this.items = this.items.map(v => {
              if (v.id == this.employeeCoefficientForm.employee_id) {
                if (v.employee_coefficients.find(v => v.id == res.data.id)) {
                  v.employee_coefficients = v.employee_coefficients.map(val => {
                    if (val.id == res.data.id) return res.data;
                    return val;
                  });
                } else {
                  v.employee_coefficients.push(res.data);
                }
              }
              return v;
            });
            this.employeeCoefficientDialog = false;
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
    saveEmployeePhones() {
      if (this.$refs.employeePhonesDialogForm.validate()) this.loading = true;
      axios
        .post(
          this.$store.state.backend_url + "api/employee-phones/update",
          this.employeePhonesForm
        )
        .then(res => {
          this.getList();
          this.employeePhonesDialog = false;
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
          this.loading = false;
        })
        .catch(err => {
          console.log(err);
          this.loading = false;
        });
    },
    saveEmployeeWorkHistory() {
      if (this.$refs.employeeWorkHistoryDialogForm.validate())
        this.loading = true;
      axios
        .post(
          this.$store.state.backend_url + "api/employee-work-history/update",
          this.employeeWorkForm
        )
        .then(res => {
          this.getList();
          this.employeeWorkHistoryDialog = false;
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
          this.loading = false;
        })
        .catch(err => {
          console.log(err);
          this.loading = false;
        });
    },
    saveEmployeeEducationHistory() {
      if (this.$refs.employeeEducationHistoryDialogForm.validate())
        this.loading = true;
      axios
        .post(
          this.$store.state.backend_url +
            "api/employee-education-history/update",
          this.employeeEducationForm
        )
        .then(res => {
          this.getList();
          this.employeeEducationHistoryDialog = false;
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
          this.loading = false;
        })
        .catch(err => {
          console.log(err);
          this.loading = false;
        });
    },
    saveEmployeeDocument() {
      axios
        .post(
          this.$store.state.backend_url + "api/official-documents/update",
          this.employeeDocumentForm
        )
        .then(res => {
          this.getList();
          this.employeeDocumentDialog = false;
          const Toast = Swal.mixin({
            toast: true,
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
    saveEmployeeLanguage() {
      axios
        .post(
          this.$store.state.backend_url + "api/employee-languages/update",
          this.employeeLanguageForm
        )
        .then(res => {
          this.getList();
          this.employeeLanguageDialog = false;
          const Toast = Swal.mixin({
            toast: true,
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
    saveEmployeeParty() {
      axios
        .post(
          this.$store.state.backend_url + "api/employee-parties/update",
          this.employeePartyForm
        )
        .then(res => {
          this.getList();
          this.employeePartyDialog = false;
          const Toast = Swal.mixin({
            toast: true,
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
    saveEmployeeMilitaryRank() {
      axios
        .post(
          this.$store.state.backend_url + "api/employee-military-ranks/update",
          this.employeeMilitaryRankForm
        )
        .then(res => {
          this.getList();
          this.employeeMilitaryRankDialog = false;
          const Toast = Swal.mixin({
            toast: true,
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
    saveEmployeeStateAward() {
      axios
        .post(
          this.$store.state.backend_url + "api/employee-state-awards/update",
          this.employeeStateAwardForm
        )
        .then(res => {
          this.getList();
          this.employeeStateAwardDialog = false;
          const Toast = Swal.mixin({
            toast: true,
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
    saveEmployeeRelative() {
      axios
        .post(
          this.$store.state.backend_url + "api/employee-relatives/update",
          this.employeeRelativeForm
        )
        .then(res => {
          this.getList();
          this.employeeRelativeDialog = false;
          const Toast = Swal.mixin({
            toast: true,
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
    save() {
      if (this.$refs.dialogForm.validate())
        axios
          .post(
            this.$store.state.backend_url + "api/employees/update",
            this.form
          )
          .then(res => {
            this.getList();
            this.dialog = false;
            this.employeeInnDialog = false;
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
    deleteItem(item) {
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
              this.$store.state.backend_url + "api/employees/delete/" + item.id
            )
            .then(res => {
              this.getList();
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
    deleteStaffHistory(item) {
      // if (this.$store.getters.checkPermission("hr-language-delete")) {
      const index = this.items.indexOf(item);
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
                "api/employee-staff/delete/" +
                item.id
            )
            .then(res => {
              this.getList();
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
      // }
    },
    deleteEmployeeAddress(item) {
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
                "api/employees/delete-address/" +
                item.id
            )
            .then(res => {
              this.items = this.items.map(v => {
                if (v.id == item.employee_id) {
                  v.employee_addresses = v.employee_addresses.filter(
                    ea => ea.id != item.id
                  );
                  return v;
                }
                return v;
              });
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
    deleteEmployeeCoefficient(item) {
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
                "api/employees/delete-coefficient/" +
                item.id
            )
            .then(res => {
              this.items = this.items.map(v => {
                if (v.id == item.employee_id) {
                  v.employee_coefficients = v.employee_coefficients.filter(
                    ea => ea.id != item.id
                  );
                  return v;
                }
                return v;
              });
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
    deleteEmployeePhone(item) {
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
                "api/employee-phones/delete/" +
                item.id
            )
            .then(res => {
              this.items = this.items.map(v => {
                if (v.id == item.employee_id) {
                  v.employee_phones = v.employee_phones.filter(
                    ea => ea.id != item.id
                  );
                  return v;
                }
                return v;
              });
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
    deleteEmployeeWorkHistory(item) {
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
                "api/employee-work-history/delete/" +
                item.id
            )
            .then(res => {
              this.items = this.items.map(v => {
                if (v.id == item.employee_id) {
                  v.employee_work_histories = v.employee_work_histories.filter(
                    ea => ea.id != item.id
                  );
                  return v;
                }
                return v;
              });
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
    deleteEmployeeEducationHistory(item) {
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
                "api/employee-education-history/delete/" +
                item.id
            )
            .then(res => {
              this.items = this.items.map(v => {
                if (v.id == item.employee_id) {
                  v.employee_education_histories = v.employee_education_histories.filter(
                    ee => ee.id != item.id
                  );
                  return v;
                }
                return v;
              });
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
    deleteEmployeeDocumentItem(item) {
      // if (this.$store.getters.checkPermission("position-delete")) {
      // const index = this.items.indexOf(item);
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
                "api/official-documents/delete/" +
                item.id
            )
            .then(res => {
              this.getList();
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
    deleteEmployeeLanguageItem(item) {
      // if (this.$store.getters.checkPermission("position-delete")) {
      // const index = this.items.indexOf(item);
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
                "api/employee-languages/delete/" +
                item.id
            )
            .then(res => {
              this.getList();
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
    deleteEmployeePartyItem(item) {
      // if (this.$store.getters.checkPermission("position-delete")) {
      // const index = this.items.indexOf(item);
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
                "api/employee-parties/delete/" +
                item.id
            )
            .then(res => {
              this.getList();
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
    deleteEmployeeMilitaryRankItem(item) {
      // if (this.$store.getters.checkPermission("position-delete")) {
      // const index = this.items.indexOf(item);
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
                "api/employee-military-ranks/delete/" +
                item.id
            )
            .then(res => {
              this.getList();
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
    deleteEmployeeStateAwardItem(item) {
      // if (this.$store.getters.checkPermission("position-delete")) {
      // const index = this.items.indexOf(item);
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
                "api/employee-state-awards/delete/" +
                item.id
            )
            .then(res => {
              this.getList();
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
    deleteEmployeeRelative(item) {
      // if (this.$store.getters.checkPermission("position-delete")) {
      // const index = this.items.indexOf(item);
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
                "api/employee-relatives/delete/" +
                item.id
            )
            .then(res => {
              this.getList();
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
    docTypeList() {
      axios
        .get(this.$store.state.backend_url + "api/official-document-types")
        .then(response => {
          this.docTypes = response.data;
        })
        .catch(error => {
          console.log(error);
          this.loading = false;
        });
    },
    hrLanguage() {
      axios
        .get(this.$store.state.backend_url + "api/hr-language")
        .then(response => {
          this.hrLanguages = response.data;
        })
        .catch(error => {
          console.log(error);
          this.loading = false;
        });
    },
    hrParty() {
      axios
        .get(this.$store.state.backend_url + "api/hr-party")
        .then(response => {
          this.hrParties = response.data;
        })
        .catch(error => {
          console.log(error);
          this.loading = false;
        });
    },
    hrMilitaryRank() {
      axios
        .get(this.$store.state.backend_url + "api/hr-military-rank")
        .then(response => {
          this.hrMilitaryRanks = response.data;
        })
        .catch(error => {
          console.log(error);
          this.loading = false;
        });
    },
    hrStateAward() {
      axios
        .get(this.$store.state.backend_url + "api/hr-state-awards")
        .then(response => {
          this.hrStateAwards = response.data;
        })
        .catch(error => {
          console.log(error);
          this.loading = false;
        });
    },
    familyRelativeList() {
      axios
        .get(this.$store.state.backend_url + "api/family-relative")
        .then(response => {
          this.familyRelativeTypes = response.data;
        })
        .catch(error => {
          console.log(error);
          this.loading = false;
        });
    },
    viewPdfFile(item) {
      this.fileForView = item;
      this.pdfViewDialog = true;
      // $store.state.backend_url + 'staffs/get-file/'+item.id
    },
    editItemFiles(item, i) {
      // this.employeeDocumentForm.files = this.employeeDocuments[i].files;
      {
        this.formData = new FormData();
        this.objectId = item.id;
        this.fileDialog = true;
      }
    },
    addFiles() {
      this.formData = new FormData();

      this.selectFiles.forEach((v, i) => {
        this.formData.append("files[]", v);
      });
      axios
        .post(
          this.$store.state.backend_url +
            "api/official-documents/update-files/" +
            this.objectId,
          this.formData,
          {
            headers: {
              "Content-Type": "multipart/form-data"
            }
          }
        )
        .then(res => {
          this.selectFiles = [];
          this.selectObjectType = "";
          this.employeeDocumentForm.files = res.data.files;
          // editItemFiles();
        })
        .catch(function(e) {});
    },
    removeTmpFile(id) {
      axios
        .delete(
          this.$store.state.backend_url +
            "api/official-documents/delete-files/" +
            id
        )
        .then(res => {
          this.employeeDocumentForm.files = this.employeeDocumentForm.files.filter(
            v => v.id != id
          );
          // editItemFiles(document, i);
        })
        .catch(function(e) {});
    }
  },
  mounted() {
    this.getList();
    this.docTypeList();
    this.hrLanguage();
    this.hrParty();
    this.hrMilitaryRank();
    this.hrStateAward();
    this.familyRelativeList();
  }
};
</script>

<style scoped>
.doc-template_data-table table > tbody > tr > td {
  white-space: normal;
  max-width: 50px;
  height: 43px;
  margin: 0 auto;
  font-size: 14px;
  line-height: 1.4;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}
.fullHeight {
  height: calc(100% - 0px);
}
.heightFull {
  height: 100%;
  background: #fff;
}
.headerTitle {
  width: 100%;
  color: #000;
  font-size: 18px;
  line-height: 1.4;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.dialogTitle {
  color: #000;
  font-size: 16px;
  line-height: 1.4;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.labelTitle {
  color: #676768;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.headerSearch {
  width: 100%;
  height: 34px;
}
.txt_search1 {
  border: 1px solid #e6e6e6;
  box-shadow: none;
  max-height: 100%;
  overflow: hidden;
  border-radius: 5px 0px 0px 5px;
  color: #212529;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.txt_searchBtn {
  background: #ff9f0e;
  border: 0.2px rgba(0, 0, 0, 0.28) solid;
  box-shadow: none;
  min-width: 25px !important;
  height: 34px !important;
  border-radius: 1px;
  width: 25px;
  padding: 0 13px;
}
.v-data-table {
  line-height: 13px !important;
}
.doc-template_data-table > .v-data-table__wrapper > table > tbody > tr > td {
  color: #676768;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.dropdown-list .v-list-item .v-list-item__title {
  color: #000;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.dialog-head_title {
  color: #000;
  font-size: 14px;
  font-weight: 600;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.v-dialog > .v-card > .v-card__text {
  padding: 0px 0px 0px 0px;
}
</style>
