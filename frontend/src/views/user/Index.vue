<template>
  <div class="fullHeight">
    <v-card 
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{ $t("user.index") }}</span>
        <v-spacer></v-spacer>
        <!-- <v-btn class="mr-2" color outlined x-small fab @click="filterDialog = true; search = '';">
          <v-icon>mdi-magnify</v-icon>
        </v-btn>-->
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
                  @click="newItem(BPermission)"
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
                        getUserExcel(1);
                        user_excel = [];
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
      </v-card-title>
      <v-row class="mx-0">
        <v-col class="ma-0 pa-0" xs="12">      
          <v-data-table
            class="doc-template_data-table"
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
            :expanded="[]"
            :server-items-length="server_items_length"
            :options.sync="dataTableOptions"
            :disable-pagination="true"
            item-key="id"
            show-expand
            :footer-props="{
              itemsPerPageOptions: [20, 50, 100],
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
          >
            <template v-slot:[`body.prepend`]>
              <tr class="py-0 my-0 prepend_height">
                <td class="py-0 my-0 dense"></td>
                <td class="py-0 my-0 dense"></td>
                <td class="py-0 my-0 dense" style="width: 50px">
                  <v-text-field
                    v-model="filterForm.username"
                    type="text"
                    hide-details
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filterForm.employee"
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
                    v-model="filterForm.department_code"
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
                    v-model="filterForm.eimzo_username"
                    type="text"
                    hide-details
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filterForm.role"
                    type="text"
                    hide-details
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense"></td>
              </tr>
            </template>

            <template v-slot:[`item.id`]="{ item }">{{
              items
                .map(function (x) {
                  return x.id;
                })
                .indexOf(item.id) + 1
            }}</template>
            <template v-slot:[`item.employee_id`]="{ item }">{{
              item.employee
                ? $i18n.locale == "uz_latin"
                  ? item.employee["lastname_uz_latin"] +
                    " " +
                    item.employee["firstname_uz_latin"]
                  : item.employee["lastname_uz_cyril"] +
                    " " +
                    item.employee["firstname_uz_cyril"]
                : ""
            }}</template>
            <template v-slot:[`item.username`]="{ item }">{{
              item.username.toUpperCase()
            }}</template>
            <template v-slot:[`item.position`]="{ item }">
              <td class=" text-truncate" style="max-width: 200px">
                {{
                  item.employee &&
                  item.employee.is_active &&
                  item.employee.employee_staff &&
                  item.employee.employee_staff.length &&
                  item.employee.employee_staff[0].staff.position
                    ? item.employee.employee_staff[0].staff.position[
                        "name_" + $i18n.locale
                      ]
                    : ""
                }}
              </td>
            </template>
            <template v-slot:[`item.department`]="{ item }">
              <td class=" text-truncate" style="max-width: 200px">
                {{
                  item.employee && item.employee.employee_staff.length > 0
                    ? item.employee.employee_staff[0].staff.department[
                        "name_" + $i18n.locale
                      ]
                    : ""
                }}
              </td>
            </template>
            <template v-slot:[`item.department_code`]="{ item }">{{
              item.employee &&
              item.employee.employee_staff.length > 0 &&
              item.employee.employee_staff[0].staff.department.department_code
            }}</template>
            <template v-slot:[`item.eimzo_username`]="{ item }"
              ><span v-if="item.eimzo_username"
                ><v-icon style="color: green">mdi-check-all</v-icon></span
              >
              <span v-if="!item.eimzo_username"
                ><v-icon style="color: red">mdi-minus</v-icon></span
              ></template
            >
            <template v-slot:[`item.roles`]="{ item }">
              <td class=" text-truncate" style="max-width: 300px">
                <span v-for="(item, idxRole) in item.roles" :key="idxRole">{{
                  item.name + ", "
                }}</span>
              </td>
            </template>
            <template v-slot:[`item.actions`]="{ item }">
              <v-btn
                class="px-0"
                color="#2C8DFF"
                style="min-width: 25px"
                small
                text
                @click="editPassword(item)"
              >
                <v-icon size="18">mdi-form-textbox-password</v-icon>
              </v-btn>
              <v-btn
                class="px-0"
                color="#2C8DFF"
                style="min-width: 25px"
                small
                text
                @click="editItem(item, BPermission)"
              >
                <v-icon size="18">mdi-pencil</v-icon>
              </v-btn>
              <v-btn
                class="px-0"
                color="#2C8DFF"
                style="min-width: 25px"
                small
                text
                @click="editItem(item, !BPermission)"
              >
                <v-icon size="18">mdi-pencil-lock-outline</v-icon>
              </v-btn>
              <v-btn 
                class="pl-0 pr-2"
                color="error"
                style="min-width: 25px"
                small
                text
                @click="deleteItem(item)"
              >
                <v-icon size="18">mdi-trash-can-outline</v-icon>
              </v-btn>
            </template>
            <template v-slot:expanded-item="{ headers, item }">
              <td :colspan="headers.length" class="pa-3">
                <v-card class="my-2">
                    <div>
                      <v-system-bar window color="#eee">
                        <b>{{ $t("user.index") }}</b>
                        <v-spacer></v-spacer>
                      </v-system-bar>
                    </div>
                    <!-- <span v-for="(i, idx) in item.roles" :key="idx">{{
                      i.display_name + ", "
                    }}</span> -->
                  <v-container fluid class="pa-1">
                    <table 
                      class="doc-template_data-table ma-0 pa-0"
                      style="width: 100%"
                    >
                      <tr>
                        <th style="text-align: left">{{ $t("employee.tabel") }}</th>
                        <th style="text-align: left">{{ $t("employee.email") }}</th>
                        <th style="text-align: left">{{ $t("employee.department_id") }}</th>
                      </tr>
                      <tr v-if="item.employee">
                        <td>{{ item.employee.tabel }}</td>
                        <td>{{ item.email }}</td>
                        <td>
                          <span v-for="(i, j) in item.employee.staff" :key="j">{{
                            i.department["name_" + $i18n.locale]
                          }}</span>
                        </td>
                      </tr>
                      <tr></tr>
                    </table>
                  </v-container>
                </v-card>
              </td>
            </template>
          </v-data-table>
        </v-col>
      </v-row>
    </v-card>
    <v-dialog v-model="employeeViewDialog" persistent max-width="800px">
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">{{ $t("user.employee") }}</span>
          <v-spacer></v-spacer>
          <v-btn
            color="red"
            outlined
            x-small
            fab
            class
            @click="employeeViewDialog = false"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-row>
            <v-col>{{ $t("employee.nationality_id") }}</v-col>
            <v-col>{{ employee.nationality_id }}</v-col>
          </v-row>
          <v-row>
            <v-col>{{ $t("employee.tabel") }}</v-col>
            <v-col>{{ employee.tabel }}</v-col>
          </v-row>
          <v-row>
            <v-col>{{ $t("employee.firstname_uz_latin") }}</v-col>
            <v-col>{{ employee.firstname_uz_latin }}</v-col>
          </v-row>
          <v-row>
            <v-col>{{ $t("employee.lastname_uz_latin") }}</v-col>
            <v-col>{{ employee.lastname_uz_latin }}</v-col>
          </v-row>
          <v-row>
            <v-col>{{ $t("employee.middlename_uz_latin") }}</v-col>
            <v-col>{{ employee.middlename_uz_latin }}</v-col>
          </v-row>
          <v-row>
            <v-col>{{ $t("employee.firstname_uz_cyril") }}</v-col>
            <v-col>{{ employee.firstname_uz_cyril }}</v-col>
          </v-row>
          <v-row>
            <v-col>{{ $t("employee.lastname_uz_cyril") }}</v-col>
            <v-col>{{ employee.lastname_uz_cyril }}</v-col>
          </v-row>
          <v-row>
            <v-col>{{ $t("employee.middlename_uz_cyril") }}</v-col>
            <v-col>{{ employee.middlename_uz_cyril }}</v-col>
          </v-row>
          <v-row>
            <v-col>{{ $t("employee.born_date") }}</v-col>
            <v-col>{{ employee.born_date }}</v-col>
          </v-row>
          <v-row>
            <v-col>{{ $t("employee.INN") }}</v-col>
            <v-col>{{ employee.INN }}</v-col>
          </v-row>
          <v-row>
            <v-col>{{ $t("employee.INPS") }}</v-col>
            <v-col>{{ employee.INPS }}</v-col>
          </v-row>
          <v-row>
            <v-col>{{ $t("employee.company_id") }}</v-col>
            <v-col>{{ employee.company ? employee.company.name : "" }}</v-col>
          </v-row>
          <v-row>
            <v-col>{{ $t("employee.country_id") }}</v-col>
            <v-col>{{
              employee.country ? employee.country.name_ru : ""
            }}</v-col>
          </v-row>
          <v-row>
            <v-col>{{ $t("employee.region_id") }}</v-col>
            <v-col>{{ employee.region ? employee.region.name_ru : "" }}</v-col>
          </v-row>
          <v-row>
            <v-col>{{ $t("employee.district_id") }}</v-col>
            <v-col>{{
              employee.district ? employee.district.name_ru : ""
            }}</v-col>
          </v-row>
          <v-row>
            <v-col>{{ $t("employee.address") }}</v-col>
            <v-col>{{ employee.address }}</v-col>
          </v-row>
          <v-row>
            <v-col>{{ $t("created_at") }}</v-col>
            <v-col>{{ employee.created_at }}</v-col>
          </v-row>
          <v-row>
            <v-col>{{ $t("updated_at") }}</v-col>
            <v-col>{{ employee.updated_at }}</v-col>
          </v-row>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
        </v-card-actions>
      </v-card>
    </v-dialog>

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
              <v-col cols="6" class="px-1 py-0">
                <label class="labelTitle" for>{{ $t("user.employee_id") }}</label>
                <v-autocomplete
                  clearable
                  v-model="form.employee_id"
                  @keyup="getEmployeeList()"
                  :search-input.sync="search"
                  :items="employees"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                  item-text="search"
                  item-value="id"
                >
                  <template v-slot:selection="data">{{
                    data.item.full_name
                  }}</template>
                  <template v-slot:item="data">
                    <template v-if="typeof data.item !== 'object'">
                      <v-list-item-content
                        v-text="data.item"
                      ></v-list-item-content>
                    </template>
                    <template v-else>
                      <v-list-item-content>
                        <v-list-item-title
                          v-html="data.item.full_name"
                        ></v-list-item-title>
                        <v-list-item-subtitle
                          v-html="data.item.tabel"
                        ></v-list-item-subtitle>
                      </v-list-item-content>
                    </template>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col cols="6" class="px-1 py-0">
                <label class="labelTitle" for>{{ $t("user.email") }}</label>
                <v-text-field
                  v-model="form.email"
                  :disabled="$store.state.COMPANY_ID != 4"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" class="px-1 py-0">
                <label class="labelTitle" for>{{ $t("user.username") }}</label>
                <v-text-field
                  v-model="form.username"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" class="px-1 py-0">
                <label class="labelTitle" for>{{ $t("user.password") }}</label>
                <v-text-field
                  v-model="form.password"
                  type="password"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" style="text-align: center">
                <v-card-title class="pa-0 text-center" style="display: block;" primary-title>
                  <span class="dialogTitle text-center">{{ $t("role.assign_role") }}</span>
                </v-card-title>
              </v-col>
              <v-col cols="12" sm="6" class="pa-1">
                <v-simple-table dense fixed-header height="300px">
                  <template v-slot:default>
                    <thead>
                      <tr>
                        <th class="font-weight-black">#</th>
                        <th class="text-left font-weight-black" colspan="3">
                          {{ $t("role.assigned_roles") }}
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <th colspan="3" class="pa-0 ma-0">
                          <v-text-field
                            v-model="assignedRoleSearch"
                            outlined
                            dense
                            hide-details
                            clearable
                            placeholder="Search"
                            class="ma-0 pa-0"
                          ></v-text-field>
                        </th>
                      </tr>
                      <tr
                        v-for="(item, index) in rolePermissionList"
                        :key="index"
                      >
                        <td>{{ index + 1 }}</td>
                        <td :title="item.display_name">
                          <p
                            class="text-truncate ma-0"
                            style="max-width: 280px"
                          >
                            {{ item.display_name }}
                          </p>
                        </td>
                        <td class="pa-0">
                          <v-icon
                            color="error"
                            class="my-1"
                            @click="removeRole(item)"
                            >mdi-minus-circle-outline</v-icon
                          >
                        </td>
                      </tr>
                    </tbody>
                  </template>
                </v-simple-table>
              </v-col>
              <v-col cols="12" sm="6" class="pa-1">
                <v-simple-table dense fixed-header height="300px">
                  <template v-slot:default>
                    <thead>
                      <tr>
                        <th class="font-weight-black">#</th>
                        <th class="text-left font-weight-black" colspan="3">
                          {{ $t("role.not_assigned_roles") }}
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <th colspan="3" class="pa-0">
                          <v-text-field
                            v-model="notAssignedRoleSearch"
                            outlined
                            dense
                            hide-details
                            clearable
                            placeholder="Search"
                            class="ma-0 pa-0"
                          ></v-text-field>
                        </th>
                      </tr>
                      <tr v-for="(item, index) in roleList" :key="index">
                        <td>{{ index + 1 }}</td>
                        <td :title="item.display_name">
                          <p
                            class="text-truncate ma-0"
                            style="max-width: 280px"
                          >
                            {{ item.display_name }}
                          </p>
                        </td>
                        <td class="pa-0">
                          <v-icon
                            color="primary"
                            class="my-1"
                            @click="addRole(item)"
                            >mdi-plus-circle-outline</v-icon
                          >
                        </td>
                      </tr>
                    </tbody>
                  </template>
                </v-simple-table>
                <v-divider></v-divider>
                <v-card-actions v-if="BPermission" class="pa-0 mt-5">
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
                </v-card-actions>
              </v-col>
              <v-col cols="12" style="text-align: center">
                <v-card-title class="pa-0 text-center" style="display: block;" primary-title>
                  <span class="dialogTitle text-center">{{ $t("role.assign_permission") }}</span>
                </v-card-title>
              </v-col>
              <v-col cols="12" sm="6" class="pa-1" v-if="BPermission">
                <v-simple-table dense fixed-header height="500px">
                  <template v-slot:default>
                    <thead>
                      <tr>
                        <th class="font-weight-black">#</th>
                        <th class="text-left font-weight-black" colspan="3">
                          {{ $t("role.assigned_permissions") }}
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <th colspan="3" class="pa-0 ma-0">
                          <v-text-field
                            v-model="assignedPermissionSearch"
                            outlined
                            dense
                            hide-details
                            clearable
                            placeholder="Search"
                            class="ma-0 pa-0"
                          ></v-text-field>
                        </th>
                      </tr>
                      <tr v-for="(itmm, idx) in userPermissionList" :key="idx">
                        <td>{{ idx + 1 }}</td>
                        <td :title="itmm.display_name">
                          <p
                            class="text-truncate ma-0"
                            style="max-width: 280px"
                          >
                            {{ itmm.display_name }}
                          </p>
                        </td>
                        <td class="pa-0">
                          <v-icon
                            color="error"
                            class="my-1"
                            @click="removePermission(itmm)"
                            >mdi-minus-circle-outline</v-icon
                          >
                        </td>
                      </tr>
                    </tbody>
                  </template>
                </v-simple-table>
              </v-col>
              <v-col cols="12" sm="6" class="pa-1" v-if="BPermission">
                <v-simple-table 
                  dense 
                  fixed-header 
                  height="500px"
                >
                  <template v-slot:default>
                    <thead>
                      <tr>
                        <th class="font-weight-black">#</th>
                        <th class="text-left font-weight-black" colspan="3">
                          {{ $t("role.not_assigned_permissions") }}
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <th colspan="3" class="pa-0">
                          <v-text-field
                            v-model="notAssignedPermissionSearch"
                            outlined
                            dense
                            hide-details
                            clearable
                            placeholder="Search"
                            class="ma-0 pa-0"
                          ></v-text-field>
                        </th>
                      </tr>
                      <tr v-for="(itm, indx) in permissionList" :key="indx">
                        <td>{{ indx + 1 }}</td>
                        <td :title="itm.display_name">
                          <p
                            class="text-truncate ma-0"
                            style="max-width: 280px"
                          >
                            {{ itm.display_name }}
                          </p>
                        </td>
                        <td class="pa-0">
                          <v-icon
                            color="primary"
                            class="my-1"
                            @click="addPermission(itm)"
                            >mdi-plus-circle-outline</v-icon
                          >
                        </td>
                      </tr>
                    </tbody>
                  </template>
                </v-simple-table>
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
      v-model="dialogPermission"
      @keydown.esc="dialogPermission = false"
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
              <v-col cols="12" style="text-align: center">
                <v-card-title class="pa-0 text-center" style="display: block;" primary-title>
                  <span class="dialogTitle text-center">{{ $t("role.assign_permission") }}</span>
                </v-card-title>
              </v-col>
              <v-col cols="12" sm="6" class="pa-1">
                <v-simple-table dense fixed-header height="500px">
                  <template v-slot:default>
                    <thead>
                      <tr>
                        <th class="font-weight-black">#</th>
                        <th class="text-left font-weight-black" colspan="3">
                          {{ $t("role.assigned_permissions") }}
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <th colspan="3" class="pa-0 ma-0">
                          <v-text-field
                            v-model="assignedPermissionSearch"
                            outlined
                            dense
                            hide-details
                            clearable
                            placeholder="Search"
                            class="ma-0 pa-0"
                          ></v-text-field>
                        </th>
                      </tr>
                      <tr v-for="(itmm, idx) in userPermissionList" :key="idx">
                        <td>{{ idx + 1 }}</td>
                        <td :title="itmm.display_name">
                          <p
                            class="text-truncate ma-0"
                            style="max-width: 280px"
                          >
                            {{ itmm.display_name }}
                          </p>
                        </td>
                        <td class="pa-0">
                          <v-icon
                            color="error"
                            class="my-1"
                            @click="removePermission(itmm)"
                            >mdi-minus-circle-outline</v-icon
                          >
                        </td>
                      </tr>
                    </tbody>
                  </template>
                </v-simple-table>
              </v-col>
              <v-col cols="12" sm="6" class="pa-1">
                <v-simple-table dense fixed-header height="500px">
                  <template v-slot:default>
                    <thead>
                      <tr>
                        <th class="font-weight-black">#</th>
                        <th class="text-left font-weight-black" colspan="3">
                          {{ $t("role.not_assigned_permissions") }}
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <th colspan="3" class="pa-0">
                          <v-text-field
                            v-model="notAssignedPermissionSearch"
                            outlined
                            dense
                            hide-details
                            clearable
                            placeholder="Search"
                            class="ma-0 pa-0"
                          ></v-text-field>
                        </th>
                      </tr>
                      <tr v-for="(itm, indx) in permissionList" :key="indx">
                        <td>{{ indx + 1 }}</td>
                        <td :title="itm.display_name">
                          <p
                            class="text-truncate ma-0"
                            style="max-width: 280px"
                          >
                            {{ itm.display_name }}
                          </p>
                        </td>
                        <td class="pa-0">
                          <v-icon
                            color="primary"
                            class="my-1"
                            @click="addPermission(itm)"
                            >mdi-plus-circle-outline</v-icon
                          >
                        </td>
                      </tr>
                    </tbody>
                  </template>
                </v-simple-table>
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
            @click="dialogPermission = false"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("Отменить") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="loading" width="300" hide-overlay>
      <v-card color="primary" dark>
        <v-card-text>
          {{ $t("loadingText") }}
          <v-progress-linear
            indeterminate
            color="white"
            class="mb-0"
          ></v-progress-linear>
        </v-card-text>
      </v-card>
    </v-dialog>

    <!-- begin Users filtr -->
    <v-dialog
      v-model="filterDialog"
      persistent
      max-width="800px"
      :fullscreen="fullscreen"
    >
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">{{ $t("filter") }}</span>
          <v-spacer></v-spacer>
          <v-btn
            class="mr-2"
            color
            outlined
            x-small
            fab
            @click="fullscreen = !fullscreen"
          >
            <v-icon>mdi-window-maximize</v-icon>
          </v-btn>
          <v-btn
            color="red"
            outlined
            x-small
            fab
            class
            @click="filterDialog = false"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form @keyup.native.enter="save">
            <v-row class="ma-0 pa-0">
              <v-col cols="6" sm="6" class="ma-0 pa-1">
                <label for>{{ $t("user.username") }}</label>
                <v-text-field
                  v-model="filterForm.username"
                  class="ma-0 pa-0"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>

              <v-col cols="6" sm="6" class="ma-0 pa-1">
                <label for>{{ $t("user.employee_id") }}</label>
                <v-text-field
                  v-model="filterForm.employee_id"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>

              <v-col cols="6" sm="4" class="ma-0 pa-1">
                <label for>{{ $t("user.position") }}</label>
                <v-text-field
                  v-model="filterForm.position"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>

              <!-- <v-col cols="6" sm="4" class="ma-0 pa-1">
                <label for>{{ $t('user.department_id') }}</label>
                <v-text-field v-model="filterForm.department_id" hide-details="auto" dense outlined></v-text-field>
              </v-col>-->

              <v-col cols="6" sm="4" class="ma-0 pa-1">
                <label for>{{ $t("user.department_id") }}</label>
                <v-autocomplete
                  clearable
                  v-model="filterForm.department_id"
                  :items="employee"
                  item-text="name"
                  item-value="id"
                  multiplehide-details="auto"
                  multiple
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>

              <!-- <v-col cols="6" sm="4" class="ma-0 pa-1">
                <label for>{{ $t('user.roles') }}</label>
                <v-text-field v-model="filterForm.roles" hide-details="auto" dense outlined></v-text-field>
              </v-col>-->

              <v-col cols="6" sm="4" class="ma-0 pa-1">
                <label for>{{ $t("user.role_id") }}</label>
                <v-autocomplete
                  clearable
                  v-model="filterForm.roleIds"
                  :items="roles"
                  multiplehide-details="auto"
                  multiple
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="getList()">
            <v-icon>mdi-magnify</v-icon>
            {{ $t("filter") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <!-- end Users filtr -->
    <v-dialog v-model="downloadExcel" hide-overlay persistent width="300">
      <v-card class="pa-3">
        <v-card-text class="text-center">
          <!-- <v-btn
            color="success"
            class="mx-10"
            @click="downloadExcel = false"
            text
          >
            <download-excel
              :data="user_excel"
              :name="'hodimlar_ruyxati_' + today + '.xls'"
            >
              <span style="color: #4caf50">{{ $t("download") }}</span>
              <v-icon color="success" height="20">mdi-download</v-icon>
            </download-excel>
          </v-btn>
          <v-btn class color="error" @click="downloadExcel = false" icon>
            <v-icon color="error" height="20">mdi-close</v-icon>
          </v-btn> -->
          <v-btn
              class="mr-3"
              color="#3FCB5D"
              right
              small
              dark
              elevation="0"
              style="text-transform: none; border-radius: 5px; padding: 5px 20px"
            >
              <download-excel
                :data="user_excel"
                :name="'hodimlar_ruyxati_' + today + '.xls'"
              >
                <span style="color: #fff">{{ $t("download") }}</span>
                <v-icon color="#fff" size="20">mdi-download</v-icon>
              </download-excel>
            </v-btn>
            <v-btn
              class="mr-3"
              color="red"
              right
              small
              dark
              @click="downloadExcel = false"
              elevation="0"
              style="text-transform: none; border-radius: 5px; padding: 5px 20px"
            >
              {{ $t("Cancel") }}
          </v-btn>
        </v-card-text>
      </v-card>
    </v-dialog>    
    <v-dialog v-model="passwordDialog" hide-overlay persistent width="300">
      <v-card>
        <v-card-text class="pa-0">
          <v-row class="dialog-form ma-0 pa-3">
            <v-col cols="12" class="text-right">
              <v-text-field
                name="passwordForm.new_password"
                label="New password"
                outlined
                dense
              ></v-text-field>
              <v-text-field
                name="passwordForm.new_password"
                label="Confirm password"
                outlined
                dense
              ></v-text-field>
              <v-btn
                  class="mr-3"
                  color="#3FCB5D"
                  right
                  small
                  dark
                  elevation="0"
                  style="text-transform: none; border-radius: 5px; padding: 5px 20px"
                >
                  {{ $t("Change") }}
                </v-btn>
                <v-btn
                  class=""
                  color="red"
                  right
                  small
                  dark
                  @click="passwordDialog = false"
                  elevation="0"
                  style="text-transform: none; border-radius: 5px; padding: 5px 20px"
                >
                  {{ $t("Cancel") }}
                </v-btn>
            </v-col>
        </v-row>
        </v-card-text>
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
    passwordForm: {
      new_password: "",
      confirm_password: "",
    },
    passwordDialog: false,
    assignedRoleSearch: "",
    notAssignedRoleSearch: "",
    assignedPermissionSearch: "",
    notAssignedPermissionSearch: "",
    loading: false,
    serachTermin: null,
    employeeViewDialog: false,
    dialog: false,
    dialogPermission: false,
    BPermission: false,
    editMode: null,
    filterDialog: false,
    items: [],
    employees: [],
    roles: [],
    permissions: [],
    form: { roles: [], permissions: [] },
    employee: {},
    search: "",
    fullscreen: false,
    dataTableOptions: { page: 1, itemsPerPage: 20 },
    page: 1,
    from: 0,
    server_items_length: -1,
    dialogHeaderText: "",
    filterForm: {
      username: "",
      employee: "",
      position: "",
      department_name: "",
      role: "",
    },
    today: moment().format("YYYY-MM-DD"),
    user_excel: [],
    downloadExcel: false,
  }),
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    roleList() {
      return this.roles
        .filter((v) => {
          return this.form.roles
            ? !this.form.roles.find((p) => p.id == v.id)
            : this.roles;
        })
        .filter((v) =>
          this.notAssignedRoleSearch
            ? v.display_name
                .toUpperCase()
                .search(this.notAssignedRoleSearch.toUpperCase()) >= 0
            : true
        );
    },
    rolePermissionList() {
      return this.form.roles
        ? this.form.roles.filter((v) =>
            this.assignedRoleSearch
              ? v.display_name
                  .toUpperCase()
                  .search(this.assignedRoleSearch.toUpperCase()) >= 0
              : true
          )
        : [];
    },
    permissionList() {
      return this.permissions
        .filter((v) => {
          return this.form.permissions
            ? !this.form.permissions.find((p) => p.id == v.id)
            : this.permissions;
        })
        .filter((v) =>
          this.notAssignedPermissionSearch
            ? v.display_name && v.display_name
                .toUpperCase()
                .search(this.notAssignedPermissionSearch.toUpperCase()) >= 0
            : true
        );
    },
    userPermissionList() {
      return this.form.permissions
        ? this.form.permissions.filter((v) =>
            this.assignedPermissionSearch
              ? v.display_name
                  .toUpperCase()
                  .search(this.assignedPermissionSearch.toUpperCase()) >= 0
              : true
          )
        : [];
    },
    headers() {
      return [
        { text: "#", value: "id", align: "center", width: "10px" },

        { text: this.$t("user.username"), value: "username" },

        { text: this.$t("user.employee"), value: "employee_id" },

        { text: this.$t("user.position"), value: "position" },

        { text: this.$t("user.department_code"), value: "department_code" },

        { text: this.$t("user.department_id"), value: "department" },
        { text: this.$t("ERI"), value: "eimzo_username", width: 50 },

        { text: this.$t("user.roles"), value: "roles" },
        {
          text: this.$t("actions"),
          value: "actions",
          // width: 180,
          align: "center",
        },
      ];
    },
    searchData() {
      return this.$store.getters.checkPermission;
    },
    filteredSearchData() {
      let searchData = this.searchData;
      if (this.serachTermin)
        searchData = searchData.filter(
          (b) =>
            b.tabel.toLowerCase().indexOf(this.serachTermin.toLowerCase()) >=
              0 ||
            b.description
              .toLowerCase()
              .indexOf(this.serachTermin.toLowerCase()) >= 0
        );

      if (this.level.length)
        searchData = searchData.filter(
          (b) =>
            this.level.filter((val) => b.level.indexOf(val) !== -1).length > 0
        );

      return searchData;
    },
  },
  methods: {
    updatePage($event) {
      this.getList();
    },
    updatePerPage($event) {
      this.getList();
    },
    toggleExpand($event) {},
    addRole(item) {
      this.form.roles.push(item);
    },
    removeRole(item) {
      this.form.roles = this.form.roles.filter((v) => v.id != item.id);
    },
    addPermission(item) {
      console.log(item);
      this.form.permissions.push(item);
    },
    removePermission(item) {
      this.form.permissions = this.form.permissions.filter(
        (v) => v.id != item.id
      );
    },
    getEmployeeList() {
      axios
        .post(this.$store.state.backend_url + "api/employee-search", {
          search: this.search,
        })
        .then((res) => {
          this.employees = res.data.data;
          this.employees = this.employees.map((v) => {
            v.full_name =
              this.$i18n.locale != "ru"
                ? v["firstname_" + this.$i18n.locale] +
                  " " +
                  v["lastname_" + this.$i18n.locale] +
                  " " +
                  v["middlename_" + this.$i18n.locale]
                : v.firstname_uz_cyril +
                  " " +
                  v.lastname_uz_cyril +
                  " " +
                  v.middlename_uz_cyril;
            v.search =
              v.firstname_uz_cyril +
              " " +
              v.lastname_uz_cyril +
              " " +
              v.middlename_uz_cyril +
              " " +
              v.firstname_uz_latin +
              " " +
              v.lastname_uz_latin +
              " " +
              v.middlename_uz_latin +
              " " +
              v.tabel;
            return v;
          });
        })
        .catch((err) => {
          console.error(err);
        });
    },
    getUserExcel(page) {
      let new_array = [];
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/users/get-excel", {
          locale: this.$i18n.locale,
          page: page,
          perPage: 1000,
        })
        .then((response) => {
          new_array = response.data;
          this.user_excel = this.user_excel.concat(new_array);
          if (response.data.length == 1000) {
            this.getUserExcel(++page);
          } else {
            this.loading = false;
            this.downloadExcel = true;
          }
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },

    getRef() {
      axios
        .get(
          this.$store.state.backend_url +
            "api/employees/get-ref/" +
            this.$i18n.locale
        )
        .then((response) => {
          this.employees = response.data.employees;
          this.companies = response.data.companies;
          this.staff = response.data.staff;
          this.tariffScales = response.data.tariff_scales;
          this.countries = response.data.countries;
          this.regions = response.data.regions;
          this.districts = response.data.districts;
          this.nationalities = response.data.nationalities;
          this.addressTypes = response.data.address_types.map((v) => ({
            value: v.id,
            text: v["name_" + this.$i18n.locale],
          }));
          this.coefficients = response.data.coefficients.map((v) => ({
            value: v.id,
            text: v.code + " " + v.description,
          }));
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/users-filter", {
          filter: this.filterForm,
          search: this.search,
          pagination: this.dataTableOptions,
        })
        .then((response) => {
          this.items = response.data.users.data;
          this.roles = response.data.roles;
          this.permissions = response.data.permissions;
          // .map((v) => {
          //   return { text: v.name, value: v.id };
          // });
          if (!this.items.length) {
            this.noDataText = this.$t("noDataText");
          }
          this.server_items_length = response.data.users.total;
          this.from = response.data.users.from;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    newItem() {
      this.dialogHeaderText = this.$t("user.newUser");
      this.form = {
        id: Date.now(),
        name: "",
        email: "",
        phone: "",
        employee_id: "",
        position: "",
        username: "",
        password: "",
        permissions: [],
        roles: [],
      };
      this.dialog = true;
      this.BPermission = true;
      this.editMode = false;
      if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
    },
    editPassword() {
      this.passwordForm = {
        new_password: "",
        confirm_password: "",
      };
      this.passwordDialog = true;
    },
    editItem(item, BPermission) {
      this.dialogHeaderText = this.$t("user.updateUser");
      this.formIndex = this.items.indexOf(item);
      this.form = Object.assign({}, item);
      // this.form.roleIds = item.roles.map((v) => v.id);
      if (BPermission != true) this.dialog = true;
      else this.dialogPermission = true;
      this.editMode = true;
      if (this.$refs.dialogForm) this.$refs.dialogForm.resetValidation();
      this.employees = [];
      if (this.form.employee_id) {
        if (!this.employees.some((v) => v.id == this.form.employee_id)) {
          this.employees = [this.form.employee].map((v) => {
            v.full_name =
              this.$i18n.locale != "ru"
                ? v["firstname_" + this.$i18n.locale] +
                  " " +
                  v["lastname_" + this.$i18n.locale] +
                  " " +
                  v["middlename_" + this.$i18n.locale]
                : v.firstname_uz_cyril +
                  " " +
                  v.lastname_uz_cyril +
                  " " +
                  v.middlename_uz_cyril;
            v.search =
              v.firstname_uz_cyril +
              " " +
              v.lastname_uz_cyril +
              " " +
              v.middlename_uz_cyril +
              " " +
              v.firstname_uz_latin +
              " " +
              v.lastname_uz_latin +
              " " +
              v.middlename_uz_latin +
              " " +
              v.tabel;
            return v;
          });
        }
      }
    },
    save() {
      if (this.$refs.dialogForm.validate())
        axios
          .post(this.$store.state.backend_url + "api/users/update", this.form)
          .then((res) => {
            this.getList();
            this.dialog = false;
            const Toast = Swal.mixin({
              toast: true,
              position: "top-end",
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true,
              onOpen: (toast) => {
                toast.addEventListener("mouseenter", Swal.stopTimer);
                toast.addEventListener("mouseleave", Swal.resumeTimer);
              },
            });

            Toast.fire({
              icon: "success",
              title: this.$t("create_update_operation"),
            });
          })
          .catch((err) => {
            console.log(err);
          });
    },
    deleteItem(item) {
      const index = this.items.indexOf(item);
      Swal.fire({
        title: this.$t("swal_title"),
        text: this.$t("swal_text"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: this.$t("swal_delete"),
      }).then((result) => {
        if (result.value) {
          axios
            .delete(
              this.$store.state.backend_url + "api/users/delete/" + item.id
            )
            .then((res) => {
              this.getList(this.page, this.itemsPerPage);
              this.dialog = false;
              Swal.fire("Deleted!", this.$t("swal_deleted"), "success");
            })
            .catch((err) => {
              Swal.fire({
                icon: "error",
                title: this.$t("swal_error_title"),
                text: this.$t("swal_error_text"),
                //footer: "<a href>Why do I have this issue?</a>"
              });
              console.log(err);
            });
        }
      });
    },
    viewEmployee(employee) {
      this.employee = employee;
      this.employeeViewDialog = true;
    },
  },
  mounted() {
    this.getList();
    // this.getEmployeeList();
    // Swal.fire({
    //   position: "top-end",
    //   icon: "success",
    //   title: "Your work has been saved",
    //   showConfirmButton: false,
    //   timer: 1500
    // });
  },
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
