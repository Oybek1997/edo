<template>
  <div class="fullHeight">
    <v-card 
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2"> {{ $t("report." + $route.params.menu_item) }} </span>
        <div class="headerSearch d-flex align-center">
          <v-text-field
            class="txt_search1"
            v-model="search"
            prepend-inner-icon="mdi-magnify"
            style="width: 100px !important"
            :placeholder="$t('search')"
            @keyup.native.enter="getList"
            dense
            hide-details
            solo
          ></v-text-field>
          <v-btn 
            class="filterBtn px-2" 
            style="background: #fff; height: 34px;"
            to="/complaens/summ"
            v-if="$route.params.menu_item == 'lsp'">
              {{$t('Amount')}}
            </v-btn>
          <v-btn class="filterBtn px-2" style="background: #fff; height: 34px;">
            <v-icon color="#00B950" left>mdi-filter-outline</v-icon>Фильтр
          </v-btn>
          <v-btn class="filterBtn px-2" style="background: #fff; height: 34px;">
              Столбцы <v-icon color="#00B950" right>mdi-checkbox-marked-outline</v-icon>
          </v-btn>
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
                  @click="0"
                >
                  <v-list-item-title
                  @click="uploadExcel('table', 'Lorem Table')"
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
        <!-- <v-spacer></v-spacer> -->
        <!-- <v-responsive max-width="300">
          <v-text-field
            class="mx-2 mt-1"
            v-model="search"
            :label="$t('search')"
            @keydown.enter="getList()"
            outlined
            dense
            hide-details
          ></v-text-field>
        </v-responsive> -->
        <!-- <v-btn class="mx-3" @click="uploadExcel('table', 'Lorem Table')">
          <v-icon color="green">mdi-download-multiple</v-icon>
          <span style="color: green">{{ $t("excel") }}</span>
        </v-btn> -->
      </v-card-title>
      <v-row class="mx-0">
        <v-col class="ma-0 pa-0" xs="12">
          <v-data-table
            id="table"
            class="doc-template_data-table"
            dense
            style="width: 100%; height: 100%; border-radius: 10px"
            fixed-header
            :loading-text="$t('loadingText')"
            :no-data-text="noDataText"
            :height="screenHeight"
            :loading="loading"
            :headers="headers.filter((v) => v.visible)"
            :items="items"
            item-key="id"
            :server-items-length="server_items_length"
            :options.sync="dataTableOptions"
            :disable-pagination="true"
            :footer-props="{
              itemsPerPageOptions: [20, 50, 100, 200, -1],
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
            @dblclick:row="rowClick"
          >
            <template v-slot:body.prepend>
              <tr class="py-0 my-0 prepend_height">
                <td v-if="$route.params.menu_item == 'compliance_incoming'" class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filter.document_type"
                    type="text"
                    hide-details
                    @keyup.native.enter="getFilter()"
                    clearable
                  ></v-text-field>
                </td>
                <td v-if="$route.params.menu_item == 'compliance_incoming'" class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filter.title"
                    type="text"
                    hide-details
                    @keyup.native.enter="getFilter()"
                    clearable
                  ></v-text-field>
                </td>
                <td v-if="$route.params.menu_item == 'compliance_incoming'" class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filter.korrespondent"
                    type="text"
                    hide-details
                    @keyup.native.enter="getFilter()"
                    clearable
                  ></v-text-field>
                </td>
                <td v-if="$route.params.menu_item == 'compliance_incoming'" class="py-0 my-0 dense"></td>
                <td v-if="$route.params.menu_item == 'compliance_incoming'" class="py-0 my-0 dense"></td>
                <td v-if="$route.params.menu_item == 'compliance_incoming'" class="py-0 my-0 dense"></td>
                <td
                  v-if="
                    $route.params.menu_item == 'lsp' ||
                    $route.params.menu_item == 'payment_sheet'
                  "
                  class="py-0 my-0 dense"
                >
                  <v-text-field
                    v-model="filter.id"
                    type="text"
                    hide-details
                    @keyup.native.enter="getFilter()"
                  ></v-text-field>
                </td>
                <td
                  v-if="
                    $route.params.menu_item == 'lsp' ||
                    $route.params.menu_item == 'payment_sheet'
                  "
                  class="py-0 my-0 dense"
                >
                  <v-text-field
                    v-model="filter.document_number"
                    type="text"
                    hide-details
                    @keyup.native.enter="getFilter()"
                    clearable
                  ></v-text-field>
                </td>
                <td v-if="$route.params.menu_item == 'lsp'" class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filter.from_department"
                    type="text"
                    hide-details
                    @keyup.native.enter="getFilter()"
                    clearable
                  ></v-text-field>
                </td>
                <td
                  v-if="
                    $route.params.menu_item == 'lsp' ||
                    $route.params.menu_item == 'payment_sheet'
                  "
                  class="py-0 my-0 dense"
                >
                  <v-menu
                    ref="rangeMenu"
                    :close-on-content-click="false"
                    :return-value.sync="filter.document_range"
                    offset-y
                    min-width="290px"
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field
                        v-model="filter.document_range"
                        v-bind="attrs"
                        @keyup.native.enter="getFilter()"
                        v-on="on"
                        hide-details
                        clearable
                      ></v-text-field>
                    </template>
                    <v-date-picker v-model="date" range no-title scrollable>
                      <v-spacer></v-spacer>
                      <v-btn text color="primary" @click="$refs.rangeMenu.isActive = false">Cancel</v-btn>
                      <v-btn
                        text
                        color="primary"
                        @click="
                          $refs.rangeMenu.save(date);
                          filter.document_range = date;
                          getFilter();
                        "
                      >OK</v-btn>
                    </v-date-picker>
                  </v-menu>
                </td>

                <td v-if="$route.params.menu_item == 'payment_sheet'" class="py-0 my-0 dense">
                  <v-autocomplete
                    class="py-0"
                    clearable
                    v-model="filter.branchs"
                    :items="branchs"
                    hide-details
                    item-value="id"
                    @change="getFilter()"
                  >
                    <template v-slot:selection="{ item }">{{ item["name_" + $i18n.locale] }}</template>
                    <template v-slot:item="{ item }">
                      <v-list-item-content>
                        <v-list-item-title style="font-size:15px" v-text="item['name_' + $i18n.locale]"></v-list-item-title>
                      </v-list-item-content>
                    </template>
                  </v-autocomplete>
                </td>

                <td v-if="$route.params.menu_item == 'lsp'" class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filter.partners_name"
                    type="text"
                    hide-details
                    @keyup.native.enter="getFilter()"
                    clearable
                  ></v-text-field>
                </td>
                <td v-if="$route.params.menu_item == 'payment_sheet'" class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filter.organization"
                    type="text"
                    hide-details
                    @keyup.native.enter="getFilter()"
                    clearable
                  ></v-text-field>
                </td>
                <td v-if="$route.params.menu_item == 'payment_sheet'" class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filter.expence_name"
                    type="text"
                    hide-details
                    @keyup.native.enter="getFilter()"
                    clearable
                  ></v-text-field>
                </td>
                <td v-if="$route.params.menu_item == 'payment_sheet'" class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filter.contract_number"
                    type="text"
                    hide-details
                    @keyup.native.enter="getFilter()"
                    clearable
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense"></td>
                <td class="py-0 my-0 dense"></td>
                <!-- <td v-if="$route.params.menu_item == 'lsp'" class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filter.currency"
                    type="text"
                    hide-details
                    @keyup.native.enter="getFilter()"
                    clearable
                  ></v-text-field>
                </td> -->
                <td v-if="$route.params.menu_item == 'lsp'" class="py-0 my-0 dense">
                  <v-autocomplete
                    class="py-0"
                    clearable
                    v-model="filter.currency"
                    :items="currency"
                    hide-details
                    @change="getFilter()"
                  >               
                  </v-autocomplete>
                </td>
                <td class="py-0 my-0"></td>
                <td v-if="$route.params.menu_item == 'compliance_incoming'" class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filter.id"
                    type="text"
                    hide-details
                    @keyup.native.enter="getFilter()"
                  ></v-text-field>
                </td>
                <td v-if="$route.params.menu_item == 'compliance_incoming'" class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filter.document_number"
                    type="text"
                    hide-details
                    @keyup.native.enter="getFilter()"
                    clearable
                  ></v-text-field>
                </td>
                <td v-if="$route.params.menu_item == 'compliance_incoming'" class="py-0 my-0 dense"></td>
                <td v-if="$route.params.menu_item == 'compliance_incoming'" class="py-0 my-0 dense"></td>
                <td v-if="$route.params.menu_item == 'compliance_incoming'" class="py-0 my-0 dense"></td>
                <td v-if="$route.params.menu_item == 'compliance_incoming'" class="py-0 my-0 dense"></td>            
                <td v-if="$route.params.menu_item == 'lsp'" class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filter.contract_number_date"
                    type="text"
                    hide-details
                    @keyup.native.enter="getFilter()"
                    clearable
                  ></v-text-field>
                </td>            
                <td v-if="$route.params.menu_item == 'lsp'" class="py-0 my-0 dense"></td>
                <td v-if="$route.params.menu_item == 'lsp'" class="py-0 my-0 dense"></td>
                <td v-if="$route.params.menu_item == 'lsp'" class="py-0 my-0"></td>
                <td v-if="$route.params.menu_item == 'payment_sheet'" class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filter.approval_sheet"
                    type="text"
                    hide-details
                    @keyup.native.enter="getFilter()"
                    clearable
                  ></v-text-field>
                </td>
              </tr>
            </template>

            <template v-slot:item.document_number="{ item }">
              <v-btn
                style="height: 21px;"
                outlined
                small
                :dark="item.reaction_status == 0 ? false : true"
                rounded
                :class="
                  item.action_type_id == 5
                    ? 'info'
                    : item.reaction_status == 1
                    ? 'success'
                    : item.reaction_status == 2
                    ? 'error'
                    : item.reaction_status == 3
                    ? 'deep-purple'
                    : item.reaction_status == 4
                    ? 'orange lighten-1'
                    : ''
                "
                :to="'/document/' + item.pdf_file_name"
              >
                {{
                item.document_number_reg
                ? item.document_number_reg
                : item.document_number
                }}
              </v-btn>
            </template>
            <template v-slot:item.branch="{ item }">
              {{
              branchs.find((v)=>v.id == item.document_template_id)["name_" + $i18n.locale]
              }}
            </template>
            <template v-slot:item.organization="{ item }">
              {{
              item.document_details[0] &&
              item.document_details[0].document_detail_contents.find(
              (v) => v.d_d_attribute_id == 965 || v.d_d_attribute_id == 1229
              )
              ? item.document_details[0].document_detail_contents.find(
              (v) => v.d_d_attribute_id == 965 || v.d_d_attribute_id == 1229
              ).value
              : ""
              }}
            </template>
            <template v-slot:item.expence_name="{ item }">
              {{
              item.document_details[0] &&
              item.document_details[0].document_detail_contents.find(
              (v) => v.d_d_attribute_id == 966 || v.d_d_attribute_id == 1230
              )
              ? item.document_details[0].document_detail_contents.find(
              (v) => v.d_d_attribute_id == 966 || v.d_d_attribute_id == 1230
              ).value
              : ""
              }}
            </template>
            <template v-slot:item.document_type="{ item }">
              {{
              item.document_details[0] &&
              item.document_details[0].document_detail_contents.find(
              (v) =>
              v.d_d_attribute_id == 678 ||
              v.d_d_attribute_id == 1043 ||
              v.d_d_attribute_id == 703 ||
              v.d_d_attribute_id == 1135
              )
              ? item.document_details[0].document_detail_contents.find(
              (v) =>
              v.d_d_attribute_id == 678 ||
              v.d_d_attribute_id == 1043 ||
              v.d_d_attribute_id == 703 ||
              v.d_d_attribute_id == 1135
              ).value
              : ""
              }}
            </template>
            <template v-slot:item.summary="{ item }">
              <v-row
                style="max-width: 200px"
                @click="
                  modalText(
                    item.document_details[0].document_detail_contents.find(
                      (v) =>
                        v.d_d_attribute_id == 674 ||
                        v.d_d_attribute_id == 1040 ||
                        v.d_d_attribute_id == 715 ||
                        v.d_d_attribute_id == 1147
                    ).value
                  )
                "
              >
                <v-col class="col-12 text-truncate py-0">
                  {{
                  item.document_details[0] &&
                  item.document_details[0].document_detail_contents.find(
                  (v) =>
                  v.d_d_attribute_id == 674 ||
                  v.d_d_attribute_id == 1040 ||
                  v.d_d_attribute_id == 715 ||
                  v.d_d_attribute_id == 1147
                  )
                  ? item.document_details[0].document_detail_contents.find(
                  (v) =>
                  v.d_d_attribute_id == 674 ||
                  v.d_d_attribute_id == 1040 ||
                  v.d_d_attribute_id == 715 ||
                  v.d_d_attribute_id == 1147
                  ).value
                  : ""
                  }}
                </v-col>
              </v-row>
            </template>
            <template v-slot:item.korrespondent="{ item }">
              <v-row
                style="max-width: 200px"
                @click="
                  modalText(
                    item.document_details[0].document_detail_contents.find(
                      (v) =>
                        v.d_d_attribute_id == 680 ||
                        v.d_d_attribute_id == 1045 ||
                        v.d_d_attribute_id == 705 ||
                        v.d_d_attribute_id == 1137
                    ).value
                  )
                "
              >
                <v-col class="col-12 text-truncate py-0">
                  {{
                  item.document_details[0] &&
                  item.document_details[0].document_detail_contents.find(
                  (v) =>
                  v.d_d_attribute_id == 680 ||
                  v.d_d_attribute_id == 1045 ||
                  v.d_d_attribute_id == 705 ||
                  v.d_d_attribute_id == 1137
                  )
                  ? item.document_details[0].document_detail_contents.find(
                  (v) =>
                  v.d_d_attribute_id == 680 ||
                  v.d_d_attribute_id == 1045 ||
                  v.d_d_attribute_id == 705 ||
                  v.d_d_attribute_id == 1137
                  ).value
                  : ""
                  }}
                </v-col>
              </v-row>
            </template>
            <template v-slot:item.journal="{ item }">
              <v-row
                @click="
                  modalText(
                    item.document_details[0].document_detail_contents.find(
                      (v) =>
                        v.d_d_attribute_id == 683 ||
                        v.d_d_attribute_id == 1048 ||
                        v.d_d_attribute_id == 712 ||
                        v.d_d_attribute_id == 1144
                    ).value
                  )
                "
                style="max-width: 200px"
              >
                <v-col class="col-12 text-truncate py-0">
                  {{
                  item.document_details[0] &&
                  item.document_details[0].document_detail_contents.find(
                  (v) =>
                  v.d_d_attribute_id == 683 ||
                  v.d_d_attribute_id == 1048 ||
                  v.d_d_attribute_id == 712 ||
                  v.d_d_attribute_id == 1144
                  )
                  ? item.document_details[0].document_detail_contents.find(
                  (v) =>
                  v.d_d_attribute_id == 683 ||
                  v.d_d_attribute_id == 1048 ||
                  v.d_d_attribute_id == 712 ||
                  v.d_d_attribute_id == 1144
                  ).value
                  : ""
                  }}
                </v-col>
              </v-row>
            </template>
            <template v-slot:item.date_out="{ item }">
              {{
              item.document_details[0] &&
              item.document_details[0].document_detail_contents.find(
              (v) =>
              v.d_d_attribute_id == 676 ||
              v.d_d_attribute_id == 1042 ||
              v.d_d_attribute_id == 701 ||
              v.d_d_attribute_id == 1134
              )
              ? item.document_details[0].document_detail_contents.find(
              (v) =>
              v.d_d_attribute_id == 676 ||
              v.d_d_attribute_id == 1042 ||
              v.d_d_attribute_id == 701 ||
              v.d_d_attribute_id == 1134
              ).value
              : ""
              }}
            </template>
            <template v-slot:item.doers="{ item }">
              <template
                v-for="document_signer in item.document_signers.filter((v) => {
                  if (v.action_type_id == 4) return v;
                })"
              >
                <div
                  style="font-size: 11px"
                  :key="document_signer.id"
                  class="ma-0"
                  v-if="item.status != 6"
                >
                  <div v-if="document_signer.signer_employee">
                    {{
                    document_signer.signer_employee &&
                    document_signer.signer_employee["lastname_" + language] +
                    " " +
                    document_signer.signer_employee[
                    "firstname_" + language
                    ].substr(0, 1) +
                    "." +
                    document_signer.signer_employee[
                    "middlename_" + language
                    ].substr(0, 1) +
                    "."
                    }}
                  </div>
                  <div v-else>
                    {{
                    document_signer.employee_staffs &&
                    document_signer.employee_staffs.employee[
                    "lastname_" + language
                    ] +
                    " " +
                    document_signer.employee_staffs.employee[
                    "firstname_" + language
                    ].substr(0, 1) +
                    "." +
                    document_signer.employee_staffs.employee[
                    "middlename_" + language
                    ].substr(0, 1) +
                    "."
                    }}
                  </div>
                </div>
              </template>
            </template>
            <template v-slot:item.registration="{ item }">
              {{
              item.document_details[0] &&
              item.document_details[0].document_detail_contents.find(
              (v) =>
              v.d_d_attribute_id == 675 ||
              v.d_d_attribute_id == 1041 ||
              v.d_d_attribute_id == 700 ||
              v.d_d_attribute_id == 1133
              )
              ? item.document_details[0].document_detail_contents.find(
              (v) =>
              v.d_d_attribute_id == 675 ||
              v.d_d_attribute_id == 1041 ||
              v.d_d_attribute_id == 700 ||
              v.d_d_attribute_id == 1133
              ).value
              : ""
              }}
            </template>
            <template v-slot:item.creator="{ item }">
              {{
              $i18n.locale == "uz_latin"
              ? item.employee.lastname_uz_latin +
              " " +
              item.employee.firstname_uz_latin.substr(0, 1) +
              "." +
              item.employee.middlename_uz_latin.substr(0, 1) +
              "."
              : item.employee.lastname_uz_cyril +
              " " +
              item.employee.firstname_uz_cyril.substr(0, 1) +
              "." +
              item.employee.middlename_uz_cyril.substr(0, 1) +
              "."
              }}
            </template>
            <!-- <template v-slot:item.from_department_code="{ item }">
              <span
                class="d-inline-block text-truncate"
                style="max-width: 150px"
                :title="item.from_department_code"
              >{{
              ( departments.find((v) =>
                v.name_ru == item.from_department ||
                v.name_uz_cyril == item.from_department ||
                v.name_uz_latin == item.from_department
                ))? 
                departments.find((v) =>
                v.name_ru == item.from_department ||
                v.name_uz_cyril == item.from_department ||
                v.name_uz_latin == item.from_department
                ).department_code : '-'
                }}</span>             
            </template> -->       
            <template v-slot:item.from_department="{ item }">
              <span
                class="d-inline-block text-truncate"
                style="max-width: 150px"
                :title="item.from_department"
              >{{ item.from_department }}</span>
              <!-- <span
                class="d-inline-block text-truncate"
                style="max-width: 150px"
                :title="item.from_manager"
              >
                <b>
                  {{
                  item.from_manager &&
                  item.from_manager
                  .split(" ")
                  .map((v, k) => {
                  if (k == 0) return v;
                  else return v[0] + ".";
                  })
                  .join(" ")
                  }}
                </b>
              </span> -->
            </template>
            <template v-slot:item.to_department="{ item }">
              <span
                class="d-inline-block text-truncate"
                style="max-width: 150px"
                :title="item.to_department"
              >{{ item.to_department }}</span>
              <span
                class="d-inline-block text-truncate"
                style="max-width: 150px"
                :title="item.to_manager"
              >
                <b>
                  {{
                  item.to_manager &&
                  item.to_manager
                  .split(" ")
                  .map((v, k) => {
                  if (k == 0) return v;
                  else return v[0] + ".";
                  })
                  .join(" ")
                  }}
                </b>
              </span>
            </template>
            <template v-slot:item.file="{ item }">
              <template v-for="file in item.files">
                <div style="font-size: 11px" :key="file.id" class="ma-0">
                  <a
                    style="text-decoration: none"
                    :href="
                      $store.state.backend_url + 'staffs/file-download/' + file.id
                    "
                  >{{ file.file_name }}</a>
                </div>
              </template>
            </template>
            <template v-slot:item.due_date="{ item }">
              {{
              item.document_details[0] &&
              item.document_details[0].document_detail_contents.find(
              (v) =>
              v.d_d_attribute_id == 672 ||
              v.d_d_attribute_id == 1038 ||
              v.d_d_attribute_id == 698 ||
              v.d_d_attribute_id == 1131
              )
              ? item.document_details[0].document_detail_contents.find(
              (v) =>
              v.d_d_attribute_id == 672 ||
              v.d_d_attribute_id == 1038 ||
              v.d_d_attribute_id == 698 ||
              v.d_d_attribute_id == 1131
              ).value
              : ""
              }}
            </template>
            <template v-slot:item.contract_number="{ item }">
              {{
              item.document_details[0] &&
              item.document_details[0].document_detail_contents.find(
              (v) => v.d_d_attribute_id == 967 || v.d_d_attribute_id == 1231
              )
              ? item.document_details[0].document_detail_contents.find(
              (v) => v.d_d_attribute_id == 967 || v.d_d_attribute_id == 1231
              ).value
              : ""
              }}
            </template>
            <template v-slot:item.contract_amount="{ item }">
              {{
              item.document_details[0] &&
              item.document_details[0].document_detail_contents.find(
              (v) => v.d_d_attribute_id == 968 || v.d_d_attribute_id == 1232
              )
              ? item.document_details[0].document_detail_contents.find(
              (v) => v.d_d_attribute_id == 968 || v.d_d_attribute_id == 1232
              ).value
              : ""
              }}
            </template>
            <template v-slot:item.amount_payments="{ item }">
              {{
              item.document_details[0] &&
              item.document_details[0].document_detail_contents.find(
              (v) => v.d_d_attribute_id == 969 || v.d_d_attribute_id == 1233
              )
              ? item.document_details[0].document_detail_contents.find(
              (v) => v.d_d_attribute_id == 969 || v.d_d_attribute_id == 1233
              ).value
              : ""
              }}
            </template>
            <template v-slot:item.payments_amount="{ item }">
              {{
              item.document_details[0] &&
              item.document_details[0].document_detail_contents.find(
              (v) => v.d_d_attribute_id == 970 || v.d_d_attribute_id == 1234
              )
              ? item.document_details[0].document_detail_contents.find(
              (v) => v.d_d_attribute_id == 970 || v.d_d_attribute_id == 1234
              ).value
              : ""
              }}
            </template>
            <template v-slot:item.approval_sheet="{ item }">
              {{
              item.document_details[0] &&
              item.document_details[0].document_detail_contents.find(
              (v) => v.d_d_attribute_id == 971 || v.d_d_attribute_id == 1235
              )
              ? item.document_details[0].document_detail_contents.find(
              (v) => v.d_d_attribute_id == 971 || v.d_d_attribute_id == 1235
              ).value
              : ""
              }}
            </template>
            <template v-slot:item.partners_name="{ item }">
              <v-row
                @click="
                  modalText(
                    item.document_details[0].document_detail_contents.find(
                      (v) =>
                        v.d_d_attribute_id == 199 ||
                        v.d_d_attribute_id == 167 ||
                        v.d_d_attribute_id == 274 ||
                        v.d_d_attribute_id == 225 ||
                        v.d_d_attribute_id == 240 ||
                        v.d_d_attribute_id == 260 ||
                        v.d_d_attribute_id == 275 ||
                        v.d_d_attribute_id == 288 ||
                        v.d_d_attribute_id == 308 ||
                        v.d_d_attribute_id == 366 ||
                        v.d_d_attribute_id == 381 ||
                        v.d_d_attribute_id == 404 ||
                        v.d_d_attribute_id == 506 ||
                        v.d_d_attribute_id == 528 ||
                        v.d_d_attribute_id == 491 ||
                        v.d_d_attribute_id == 547 ||
                        v.d_d_attribute_id == 563 ||
                        v.d_d_attribute_id == 605 ||
                        v.d_d_attribute_id == 632 ||
                        v.d_d_attribute_id == 806 ||
                        v.d_d_attribute_id == 844 ||
                        v.d_d_attribute_id == 928
                    ).value
                  )
                "
                style="max-width: 200px"
              >
                <v-col class="col-12 text-truncate py-0">
                  {{
                  item.document_details[0] &&
                  item.document_details[0].document_detail_contents.find(
                  (v) =>
                  v.d_d_attribute_id == 199 ||
                  v.d_d_attribute_id == 167 ||
                  v.d_d_attribute_id == 274 ||
                  v.d_d_attribute_id == 225 ||
                  v.d_d_attribute_id == 240 ||
                  v.d_d_attribute_id == 260 ||
                  v.d_d_attribute_id == 275 ||
                  v.d_d_attribute_id == 288 ||
                  v.d_d_attribute_id == 308 ||
                  v.d_d_attribute_id == 366 ||
                  v.d_d_attribute_id == 381 ||
                  v.d_d_attribute_id == 404 ||
                  v.d_d_attribute_id == 506 ||
                  v.d_d_attribute_id == 528 ||
                  v.d_d_attribute_id == 491 ||
                  v.d_d_attribute_id == 547 ||
                  v.d_d_attribute_id == 563 ||
                  v.d_d_attribute_id == 605 ||
                  v.d_d_attribute_id == 632 ||
                  v.d_d_attribute_id == 806 ||
                  v.d_d_attribute_id == 844 ||
                  v.d_d_attribute_id == 928
                  )
                  ? item.document_details[0].document_detail_contents.find(
                  (v) =>
                  v.d_d_attribute_id == 199 ||
                  v.d_d_attribute_id == 167 ||
                  v.d_d_attribute_id == 274 ||
                  v.d_d_attribute_id == 225 ||
                  v.d_d_attribute_id == 240 ||
                  v.d_d_attribute_id == 260 ||
                  v.d_d_attribute_id == 275 ||
                  v.d_d_attribute_id == 288 ||
                  v.d_d_attribute_id == 308 ||
                  v.d_d_attribute_id == 366 ||
                  v.d_d_attribute_id == 381 ||
                  v.d_d_attribute_id == 404 ||
                  v.d_d_attribute_id == 506 ||
                  v.d_d_attribute_id == 528 ||
                  v.d_d_attribute_id == 491 ||
                  v.d_d_attribute_id == 547 ||
                  v.d_d_attribute_id == 563 ||
                  v.d_d_attribute_id == 605 ||
                  v.d_d_attribute_id == 632 ||
                  v.d_d_attribute_id == 806 ||
                  v.d_d_attribute_id == 844 ||
                  v.d_d_attribute_id == 928
                  ).value
                  : ""
                  }}
                </v-col>
              </v-row>
            </template>
            <template v-slot:item.currency="{ item }">
              <v-row>
                <v-col class="col-12 text-truncate py-0">
                  {{
                  item.document_details[0] &&
                  item.document_details[0].document_detail_contents.find(
                  (v) =>
                  v.d_d_attribute_id == 1372 ||
                  v.d_d_attribute_id == 1373 ||
                  v.d_d_attribute_id == 1374 ||
                  v.d_d_attribute_id == 1375 ||
                  v.d_d_attribute_id == 1376 ||
                  v.d_d_attribute_id == 1377 ||
                  v.d_d_attribute_id == 1378 ||
                  v.d_d_attribute_id == 1379 ||
                  v.d_d_attribute_id == 1380 ||
                  v.d_d_attribute_id == 1381 ||
                  v.d_d_attribute_id == 1382 ||
                  v.d_d_attribute_id == 1383 ||
                  v.d_d_attribute_id == 1384 ||
                  v.d_d_attribute_id == 1385 ||
                  v.d_d_attribute_id == 1386 ||
                  v.d_d_attribute_id == 1387 ||
                  v.d_d_attribute_id == 1388 ||
                  v.d_d_attribute_id == 1389 ||
                  v.d_d_attribute_id == 1390 ||
                  v.d_d_attribute_id == 1391 ||
                  v.d_d_attribute_id == 1392
                  )
                  ? item.document_details[0].document_detail_contents.find(
                  (v) =>
                  v.d_d_attribute_id == 1372 ||
                  v.d_d_attribute_id == 1373 ||
                  v.d_d_attribute_id == 1374 ||
                  v.d_d_attribute_id == 1375 ||
                  v.d_d_attribute_id == 1376 ||
                  v.d_d_attribute_id == 1377 ||
                  v.d_d_attribute_id == 1378 ||
                  v.d_d_attribute_id == 1379 ||
                  v.d_d_attribute_id == 1380 ||
                  v.d_d_attribute_id == 1381 ||
                  v.d_d_attribute_id == 1382 ||
                  v.d_d_attribute_id == 1383 ||
                  v.d_d_attribute_id == 1384 ||
                  v.d_d_attribute_id == 1385 ||
                  v.d_d_attribute_id == 1386 ||
                  v.d_d_attribute_id == 1387 ||
                  v.d_d_attribute_id == 1388 ||
                  v.d_d_attribute_id == 1389 ||
                  v.d_d_attribute_id == 1390 ||
                  v.d_d_attribute_id == 1391 ||
                  v.d_d_attribute_id == 1392
                  ).value
                  : ""
                  }}
                </v-col>
              </v-row>
            </template>
            <template v-slot:item.purpose_payment="{ item }">
              <v-row
                @click="
                  modalText(
                    item.document_details[0].document_detail_contents.find(
                      (v) =>
                        v.d_d_attribute_id == 200 ||
                        v.d_d_attribute_id == 168 ||
                        v.d_d_attribute_id == 213 ||
                        v.d_d_attribute_id == 226 ||
                        v.d_d_attribute_id == 241 ||
                        v.d_d_attribute_id == 261 ||
                        v.d_d_attribute_id == 276 ||
                        v.d_d_attribute_id == 289 ||
                        v.d_d_attribute_id == 309 ||
                        v.d_d_attribute_id == 367 ||
                        v.d_d_attribute_id == 382 ||
                        v.d_d_attribute_id == 405 ||
                        v.d_d_attribute_id == 507 ||
                        v.d_d_attribute_id == 488 ||
                        v.d_d_attribute_id == 548 ||
                        v.d_d_attribute_id == 564 ||
                        v.d_d_attribute_id == 606 ||
                        v.d_d_attribute_id == 629 ||
                        v.d_d_attribute_id == 807 ||
                        v.d_d_attribute_id == 845 ||
                        v.d_d_attribute_id == 929
                    ).value
                  )
                "
                style="max-width: 200px"
              >
                <v-col class="col-12 text-truncate py-0">
                  {{
                  item.document_details[0] &&
                  item.document_details[0].document_detail_contents.find(
                  (v) =>
                  v.d_d_attribute_id == 200 ||
                  v.d_d_attribute_id == 168 ||
                  v.d_d_attribute_id == 213 ||
                  v.d_d_attribute_id == 226 ||
                  v.d_d_attribute_id == 241 ||
                  v.d_d_attribute_id == 261 ||
                  v.d_d_attribute_id == 276 ||
                  v.d_d_attribute_id == 289 ||
                  v.d_d_attribute_id == 309 ||
                  v.d_d_attribute_id == 367 ||
                  v.d_d_attribute_id == 382 ||
                  v.d_d_attribute_id == 405 ||
                  v.d_d_attribute_id == 507 ||
                  v.d_d_attribute_id == 488 ||
                  v.d_d_attribute_id == 548 ||
                  v.d_d_attribute_id == 564 ||
                  v.d_d_attribute_id == 606 ||
                  v.d_d_attribute_id == 629 ||
                  v.d_d_attribute_id == 807 ||
                  v.d_d_attribute_id == 845 ||
                  v.d_d_attribute_id == 929
                  )
                  ? item.document_details[0].document_detail_contents.find(
                  (v) =>
                  v.d_d_attribute_id == 200 ||
                  v.d_d_attribute_id == 168 ||
                  v.d_d_attribute_id == 213 ||
                  v.d_d_attribute_id == 226 ||
                  v.d_d_attribute_id == 241 ||
                  v.d_d_attribute_id == 261 ||
                  v.d_d_attribute_id == 276 ||
                  v.d_d_attribute_id == 289 ||
                  v.d_d_attribute_id == 309 ||
                  v.d_d_attribute_id == 367 ||
                  v.d_d_attribute_id == 382 ||
                  v.d_d_attribute_id == 405 ||
                  v.d_d_attribute_id == 507 ||
                  v.d_d_attribute_id == 488 ||
                  v.d_d_attribute_id == 548 ||
                  v.d_d_attribute_id == 564 ||
                  v.d_d_attribute_id == 606 ||
                  v.d_d_attribute_id == 629 ||
                  v.d_d_attribute_id == 807 ||
                  v.d_d_attribute_id == 845 ||
                  v.d_d_attribute_id == 929
                  ).value
                  : ""
                  }}
                </v-col>
              </v-row>
            </template>
            <template v-slot:item.amount_paid="{ item }">
              {{
              item.document_details[0] &&
              item.document_details[0].document_detail_contents.find(
              (v) =>
              v.d_d_attribute_id == 201 ||
              v.d_d_attribute_id == 169 ||
              v.d_d_attribute_id == 214 ||
              v.d_d_attribute_id == 227 ||
              v.d_d_attribute_id == 242 ||
              v.d_d_attribute_id == 262 ||
              v.d_d_attribute_id == 277 ||
              v.d_d_attribute_id == 290 ||
              v.d_d_attribute_id == 310 ||
              v.d_d_attribute_id == 368 ||
              v.d_d_attribute_id == 383 ||
              v.d_d_attribute_id == 406 ||
              v.d_d_attribute_id == 508 ||
              v.d_d_attribute_id == 489 ||
              v.d_d_attribute_id == 549 ||
              v.d_d_attribute_id == 565 ||
              v.d_d_attribute_id == 607 ||
              v.d_d_attribute_id == 630 ||
              v.d_d_attribute_id == 808 ||
              v.d_d_attribute_id == 846 ||
              v.d_d_attribute_id == 930
              )
              ? item.document_details[0].document_detail_contents.find(
              (v) =>
              v.d_d_attribute_id == 201 ||
              v.d_d_attribute_id == 169 ||
              v.d_d_attribute_id == 214 ||
              v.d_d_attribute_id == 227 ||
              v.d_d_attribute_id == 242 ||
              v.d_d_attribute_id == 262 ||
              v.d_d_attribute_id == 277 ||
              v.d_d_attribute_id == 290 ||
              v.d_d_attribute_id == 310 ||
              v.d_d_attribute_id == 368 ||
              v.d_d_attribute_id == 383 ||
              v.d_d_attribute_id == 406 ||
              v.d_d_attribute_id == 508 ||
              v.d_d_attribute_id == 489 ||
              v.d_d_attribute_id == 549 ||
              v.d_d_attribute_id == 565 ||
              v.d_d_attribute_id == 607 ||
              v.d_d_attribute_id == 630 ||
              v.d_d_attribute_id == 808 ||
              v.d_d_attribute_id == 846 ||
              v.d_d_attribute_id == 930
              ).value
              : ""
              }}
            </template>
            <template v-slot:item.amount_words="{ item }">
              <v-row
                @click="
                  modalText(
                    item.document_details[0].document_detail_contents.find(
                      (v) =>
                        v.d_d_attribute_id == 202 ||
                        v.d_d_attribute_id == 170 ||
                        v.d_d_attribute_id == 215 ||
                        v.d_d_attribute_id == 228 ||
                        v.d_d_attribute_id == 243 ||
                        v.d_d_attribute_id == 263 ||
                        v.d_d_attribute_id == 278 ||
                        v.d_d_attribute_id == 291 ||
                        v.d_d_attribute_id == 311 ||
                        v.d_d_attribute_id == 369 ||
                        v.d_d_attribute_id == 384 ||
                        v.d_d_attribute_id == 407 ||
                        v.d_d_attribute_id == 509 ||
                        v.d_d_attribute_id == 490 ||
                        v.d_d_attribute_id == 550 ||
                        v.d_d_attribute_id == 566 ||
                        v.d_d_attribute_id == 608 ||
                        v.d_d_attribute_id == 631 ||
                        v.d_d_attribute_id == 809 ||
                        v.d_d_attribute_id == 847 ||
                        v.d_d_attribute_id == 931
                    ).value
                  )
                "
                style="max-width: 150px"
              >
                <v-col class="col-12 text-truncate py-0">
                  {{
                  item.document_details[0] &&
                  item.document_details[0].document_detail_contents.find(
                  (v) =>
                  v.d_d_attribute_id == 202 ||
                  v.d_d_attribute_id == 170 ||
                  v.d_d_attribute_id == 215 ||
                  v.d_d_attribute_id == 228 ||
                  v.d_d_attribute_id == 243 ||
                  v.d_d_attribute_id == 263 ||
                  v.d_d_attribute_id == 278 ||
                  v.d_d_attribute_id == 291 ||
                  v.d_d_attribute_id == 311 ||
                  v.d_d_attribute_id == 369 ||
                  v.d_d_attribute_id == 384 ||
                  v.d_d_attribute_id == 407 ||
                  v.d_d_attribute_id == 509 ||
                  v.d_d_attribute_id == 490 ||
                  v.d_d_attribute_id == 550 ||
                  v.d_d_attribute_id == 566 ||
                  v.d_d_attribute_id == 608 ||
                  v.d_d_attribute_id == 631 ||
                  v.d_d_attribute_id == 809 ||
                  v.d_d_attribute_id == 847 ||
                  v.d_d_attribute_id == 931
                  )
                  ? item.document_details[0].document_detail_contents.find(
                  (v) =>
                  v.d_d_attribute_id == 202 ||
                  v.d_d_attribute_id == 170 ||
                  v.d_d_attribute_id == 215 ||
                  v.d_d_attribute_id == 228 ||
                  v.d_d_attribute_id == 243 ||
                  v.d_d_attribute_id == 263 ||
                  v.d_d_attribute_id == 278 ||
                  v.d_d_attribute_id == 291 ||
                  v.d_d_attribute_id == 311 ||
                  v.d_d_attribute_id == 369 ||
                  v.d_d_attribute_id == 384 ||
                  v.d_d_attribute_id == 407 ||
                  v.d_d_attribute_id == 509 ||
                  v.d_d_attribute_id == 490 ||
                  v.d_d_attribute_id == 550 ||
                  v.d_d_attribute_id == 566 ||
                  v.d_d_attribute_id == 608 ||
                  v.d_d_attribute_id == 631 ||
                  v.d_d_attribute_id == 809 ||
                  v.d_d_attribute_id == 847 ||
                  v.d_d_attribute_id == 931
                  ).value
                  : ""
                  }}
                </v-col>
              </v-row>
            </template>
            <template v-slot:item.contract_number_date="{ item }">
              <td style="max-width: 250px" class="text-truncate">
                {{
                item.document_details[0] &&
                item.document_details[0].document_detail_contents.find(
                (v) =>
                v.d_d_attribute_id == 204 ||
                v.d_d_attribute_id == 172 ||
                v.d_d_attribute_id == 217 ||
                v.d_d_attribute_id == 230 ||
                v.d_d_attribute_id == 245 ||
                v.d_d_attribute_id == 265 ||
                v.d_d_attribute_id == 280 ||
                v.d_d_attribute_id == 293 ||
                v.d_d_attribute_id == 313 ||
                v.d_d_attribute_id == 371 ||
                v.d_d_attribute_id == 386 ||
                v.d_d_attribute_id == 409 ||
                v.d_d_attribute_id == 511 ||
                v.d_d_attribute_id == 552 ||
                v.d_d_attribute_id == 568 ||
                v.d_d_attribute_id == 610 ||
                v.d_d_attribute_id == 633 ||
                v.d_d_attribute_id == 811 ||
                v.d_d_attribute_id == 849 ||
                v.d_d_attribute_id == 933
                )
                ? item.document_details[0].document_detail_contents.find(
                (v) =>
                v.d_d_attribute_id == 204 ||
                v.d_d_attribute_id == 172 ||
                v.d_d_attribute_id == 217 ||
                v.d_d_attribute_id == 230 ||
                v.d_d_attribute_id == 245 ||
                v.d_d_attribute_id == 265 ||
                v.d_d_attribute_id == 280 ||
                v.d_d_attribute_id == 293 ||
                v.d_d_attribute_id == 313 ||
                v.d_d_attribute_id == 371 ||
                v.d_d_attribute_id == 386 ||
                v.d_d_attribute_id == 409 ||
                v.d_d_attribute_id == 511 ||
                v.d_d_attribute_id == 552 ||
                v.d_d_attribute_id == 568 ||
                v.d_d_attribute_id == 610 ||
                v.d_d_attribute_id == 633 ||
                v.d_d_attribute_id == 811 ||
                v.d_d_attribute_id == 849 ||
                v.d_d_attribute_id == 933
                ).value
                : ""
                }}
              </td>
            </template>
            <template v-slot:item.payment_terms="{ item }">
              <v-row
                @click="
                  modalText(
                    item.document_details[0].document_detail_contents.find(
                      (v) =>
                        v.d_d_attribute_id == 205 ||
                        v.d_d_attribute_id == 173 ||
                        v.d_d_attribute_id == 266 ||
                        v.d_d_attribute_id == 281 ||
                        v.d_d_attribute_id == 314 ||
                        v.d_d_attribute_id == 372 ||
                        v.d_d_attribute_id == 410 ||
                        v.d_d_attribute_id == 512 ||
                        v.d_d_attribute_id == 553 ||
                        v.d_d_attribute_id == 569 ||
                        v.d_d_attribute_id == 812 ||
                        v.d_d_attribute_id == 850 ||
                        v.d_d_attribute_id == 934
                    ).value
                  )
                "
                style="max-width: 150px"
              >
                <v-col class="col-12 text-truncate py-0">
                  {{
                  item.document_details[0] &&
                  item.document_details[0].document_detail_contents.find(
                  (v) =>
                  v.d_d_attribute_id == 205 ||
                  v.d_d_attribute_id == 173 ||
                  v.d_d_attribute_id == 266 ||
                  v.d_d_attribute_id == 281 ||
                  v.d_d_attribute_id == 314 ||
                  v.d_d_attribute_id == 372 ||
                  v.d_d_attribute_id == 410 ||
                  v.d_d_attribute_id == 512 ||
                  v.d_d_attribute_id == 553 ||
                  v.d_d_attribute_id == 569 ||
                  v.d_d_attribute_id == 812 ||
                  v.d_d_attribute_id == 850 ||
                  v.d_d_attribute_id == 934
                  )
                  ? item.document_details[0].document_detail_contents.find(
                  (v) =>
                  v.d_d_attribute_id == 205 ||
                  v.d_d_attribute_id == 173 ||
                  v.d_d_attribute_id == 266 ||
                  v.d_d_attribute_id == 281 ||
                  v.d_d_attribute_id == 314 ||
                  v.d_d_attribute_id == 372 ||
                  v.d_d_attribute_id == 410 ||
                  v.d_d_attribute_id == 512 ||
                  v.d_d_attribute_id == 553 ||
                  v.d_d_attribute_id == 569 ||
                  v.d_d_attribute_id == 812 ||
                  v.d_d_attribute_id == 850 ||
                  v.d_d_attribute_id == 934
                  ).value
                  : ""
                  }}
                </v-col>
              </v-row>
            </template>
            <template v-slot:item.payment_details="{ item }">
              <td style="max-width: 250px" class="text-truncate">
                {{
                item.document_details[0] &&
                item.document_details[0].document_detail_contents.find(
                (v) =>
                v.d_d_attribute_id == 206 ||
                v.d_d_attribute_id == 174 ||
                v.d_d_attribute_id == 218 ||
                v.d_d_attribute_id == 232 ||
                v.d_d_attribute_id == 247 ||
                v.d_d_attribute_id == 267 ||
                v.d_d_attribute_id == 294 ||
                v.d_d_attribute_id == 315 ||
                v.d_d_attribute_id == 387 ||
                v.d_d_attribute_id == 411 ||
                v.d_d_attribute_id == 554 ||
                v.d_d_attribute_id == 611 ||
                v.d_d_attribute_id == 634
                )
                ? item.document_details[0].document_detail_contents.find(
                (v) =>
                v.d_d_attribute_id == 206 ||
                v.d_d_attribute_id == 174 ||
                v.d_d_attribute_id == 218 ||
                v.d_d_attribute_id == 232 ||
                v.d_d_attribute_id == 247 ||
                v.d_d_attribute_id == 267 ||
                v.d_d_attribute_id == 294 ||
                v.d_d_attribute_id == 315 ||
                v.d_d_attribute_id == 387 ||
                v.d_d_attribute_id == 411 ||
                v.d_d_attribute_id == 554 ||
                v.d_d_attribute_id == 611 ||
                v.d_d_attribute_id == 634
                ).value
                : ""
                }}
              </td>
            </template>
            <template v-slot:item.description="{ item }">
              <v-row
                @click="
                  modalText(
                    item.document_details[0].document_detail_contents.find(
                      (v) =>
                        v.d_d_attribute_id == 207 ||
                        v.d_d_attribute_id == 175 ||
                        v.d_d_attribute_id == 219 ||
                        v.d_d_attribute_id == 233 ||
                        v.d_d_attribute_id == 248 ||
                        v.d_d_attribute_id == 268 ||
                        v.d_d_attribute_id == 282 ||
                        v.d_d_attribute_id == 295 ||
                        v.d_d_attribute_id == 316 ||
                        v.d_d_attribute_id == 373 ||
                        v.d_d_attribute_id == 388 ||
                        v.d_d_attribute_id == 412 ||
                        v.d_d_attribute_id == 513 ||
                        v.d_d_attribute_id == 492 ||
                        v.d_d_attribute_id == 555 ||
                        v.d_d_attribute_id == 570 ||
                        v.d_d_attribute_id == 612 ||
                        v.d_d_attribute_id == 635 ||
                        v.d_d_attribute_id == 851 ||
                        v.d_d_attribute_id == 935
                    ).value
                  )
                "
                style="max-width: 150px"
              >
                <v-col class="col-12 text-truncate py-0">
                  {{
                  item.document_details[0] &&
                  item.document_details[0].document_detail_contents.find(
                  (v) =>
                  v.d_d_attribute_id == 207 ||
                  v.d_d_attribute_id == 175 ||
                  v.d_d_attribute_id == 219 ||
                  v.d_d_attribute_id == 233 ||
                  v.d_d_attribute_id == 248 ||
                  v.d_d_attribute_id == 268 ||
                  v.d_d_attribute_id == 282 ||
                  v.d_d_attribute_id == 295 ||
                  v.d_d_attribute_id == 316 ||
                  v.d_d_attribute_id == 373 ||
                  v.d_d_attribute_id == 388 ||
                  v.d_d_attribute_id == 412 ||
                  v.d_d_attribute_id == 513 ||
                  v.d_d_attribute_id == 492 ||
                  v.d_d_attribute_id == 555 ||
                  v.d_d_attribute_id == 570 ||
                  v.d_d_attribute_id == 612 ||
                  v.d_d_attribute_id == 635 ||
                  v.d_d_attribute_id == 851 ||
                  v.d_d_attribute_id == 935
                  )
                  ? item.document_details[0].document_detail_contents.find(
                  (v) =>
                  v.d_d_attribute_id == 207 ||
                  v.d_d_attribute_id == 175 ||
                  v.d_d_attribute_id == 219 ||
                  v.d_d_attribute_id == 233 ||
                  v.d_d_attribute_id == 248 ||
                  v.d_d_attribute_id == 268 ||
                  v.d_d_attribute_id == 282 ||
                  v.d_d_attribute_id == 295 ||
                  v.d_d_attribute_id == 316 ||
                  v.d_d_attribute_id == 373 ||
                  v.d_d_attribute_id == 388 ||
                  v.d_d_attribute_id == 412 ||
                  v.d_d_attribute_id == 513 ||
                  v.d_d_attribute_id == 492 ||
                  v.d_d_attribute_id == 555 ||
                  v.d_d_attribute_id == 570 ||
                  v.d_d_attribute_id == 612 ||
                  v.d_d_attribute_id == 635 ||
                  v.d_d_attribute_id == 851 ||
                  v.d_d_attribute_id == 935
                  ).value
                  : ""
                  }}
                </v-col>
              </v-row>
            </template>
            <!-- <template
              v-slot:item.debet="{ item }"
            >
              {{
                item.document_details[0] &&
                item.document_details[0].document_detail_contents.find(
                  (v) =>                
                    v.d_d_attribute_id == 207
                )
                  ? item.document_details[0].document_detail_contents.find(
                      (v) =>                    
                        v.d_d_attribute_id == 207
                    ).value
                  : ""
              }}
            </template>-->
          </v-data-table>
        </v-col>
      </v-row>
    </v-card>
    <v-dialog v-model="loading" width="300" hide-overlay>
      <v-card color="primary" dark>
        <v-card-text>
          {{ $t("loadingText") }}
          <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog v-model="text" width="300" hide-overlay>
      <v-card color="white" style="text-align: center" class="pa-2">
        <template>
          <div class="ma-0">{{ textVal }}</div>
        </template>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
import TableToExcel from "@linways/table-to-excel";
const axios = require("axios").default;
const moment = require("moment");
import Swal from "sweetalert2";
import Axios from "axios";
const Cookies = require("js-cookie");
// import moment from 'moment';
export default {
  data() {
    return {
      date: null,
      loading: false,
      text: false,
      textVal: "",
      isLoading: false,
      expanded: [],
      search: "",
      dialog: false,
      editMode: null,
      items: [],
      form: {},
      dialogHeaderText: "",
      dataTableOptions: { page: 1, itemsPerPage: 20 },
      page: 1,
      from: 0,
      server_items_length: -1,
      document_type: [],
      document_template: {
        document_detail_templates: [
          {
            document_detail_attributes: {}
          }
        ]
      },
      branchs: [
        {
          id: 397,
          name_uz_latin: "Toshkent",
          name_uz_cyril: "Toshkent",
          name_ru: "Тошкент"
        },
        {
          id: 263,
          name_uz_latin: "Asaka",
          name_uz_cyril: "Asaka",
          name_ru: "Асака"
        }
      ],
      currency: [
       'UZS','CNY','EUR','JPY','KRW','RUB','USD','CHF'
      ],
      document_templates: [],
      departments: [],
      filter: {
        title: "",
        id: "",
        document_type_id: 0,
        document_type: "",
        document_template_id: 0,
        document_start_date: "",
        document_end_date: "",
        attributes: [],
        menu_item: "",
        currency: "",
        document_number: "",
        created_by: "",
        korrespondent: "",
        partners_name: "",
        contract_number_date: "",
        organization: "",
        expence_name: "",
        contract_number: "",
        approval_sheet: "",
        type: "",
        company_name: "",
        fio: "",
        appeal: "",
        model: "",
        summary: "",
        region: "",
        registration: "",
        registration_uz_as: "",
        send_by: "",
        receive_by: "",
        content: "",
        pending_action: "",
        signers: "",
        reaction_status: [0, 1, 2, 3, 4],
        staff_id: null
      },
      showFilter: false,
      menu: [],
      tableLists: [],
      table_name: [],
      column_name: [],
      is_locale: [],
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
        },
        {
          id: 7,
          name_uz_latin: "Oldindan kelishuv",
          name_uz_cyril: "Олдидан килишув",
          name_ru: "Предсогласование"
        }
      ],
      reaction_status: [
        {
          text: this.$t("document.new"),
          value: 0
        },
        {
          text: this.$t("document.ok"),
          value: 1
        },
        {
          text: this.$t("document.cancel"),
          value: 2
        },
        {
          text: this.$t("document.process"),
          value: 3
        },
        {
          text: this.$t("substantiate"),
          value: 4
        }
      ],
      noDataText: "",
      staffs: []
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 175;
    },
    user() {
      let localStorage = window.localStorage;
      return JSON.parse(localStorage.getItem("user"));
    },
    headers() {
      let headers = [
        // { text: "", value: "data-table-expand",visible: true },
        // { text: "#", value: "id", align: "center", width: 30 },
        {
          text: this.$t("document.document_type"),
          value: "document_type",
          // width: 100,
          sortable: false,
          visible: this.$route.params.menu_item == "compliance_incoming"
        },
        {
          text: this.$t("document.title"),
          value: "title",
          //  width: 100,
          sortable: false,
          visible: this.$route.params.menu_item == "compliance_incoming"
        },
        {
          text: this.$t("document.correspondent"),
          value: "korrespondent",
          // width: 150,
          sortable: false,
          visible: this.$route.params.menu_item == "compliance_incoming"
        },
        {
          text: this.$t("document.short_content"),
          value: "summary",
          // width: 200,
          sortable: false,
          visible: this.$route.params.menu_item == "compliance_incoming"
        },
        {
          text: this.$t("document.files"),
          value: "file",
          //  width: 80,
          sortable: false,
          visible: this.$route.params.menu_item == "compliance_incoming"
        },
        {
          text: this.$t("control_punkt.journal"),
          value: "journal",
          //  width: 80,
          sortable: false,
          visible: this.$route.params.menu_item == "compliance_incoming"
        },
        {
          text: this.$t("document.creator"),
          value: "creator",
          sortable: false,
          visible: this.$route.params.menu_item == "compliance_incoming"
        },

        {
          text: this.$t("document.department_receiver"),
          value: "to_department",
          sortable: false,
          visible: this.$route.params.menu_item == "compliance_incoming"
        },
        {
          text: this.$t("ID"),
          value: "id",
          // width: 50,
          sortable: false,
          visible: true
        },
        {
          text: this.$t("document.document_number"),
          value: "document_number",
          align: "center",
          // width: 150,
          sortable: false,
          visible: true
          // this.$route.params.menu_item == "payment_sheet" ||
          // this.$route.params.menu_item == "lsp",
        },
        {
          text: this.$t("document.number"),
          value: "registration",
          sortable: false,
          visible: this.$route.params.menu_item == "compliance_incoming"
        },
        {
          text: this.$t("document.date_out"),
          value: "date_out",
          // width: 90,
          sortable: false,
          visible: this.$route.params.menu_item == "compliance_incoming"
        },
        {
          text: this.$t("document.due_date"),
          value: "due_date",
          // width: 90,
          sortable: false,
          visible: this.$route.params.menu_item == "compliance_incoming"
        },
        {
          text: this.$t("document.doers"),
          value: "doers",
          // width: 120,
          sortable: false,
          visible: this.$route.params.menu_item == "compliance_incoming"
        },
        // {
        //   text: this.$t("document.department_send"),
        //   value: "from_department_code",
        //   sortable: false,
        //   visible: this.$route.params.menu_item == "lsp"
        // },
        {
          text: this.$t("document.department_send"),
          value: "from_department",
          sortable: false,
          visible:
            this.$route.params.menu_item == "compliance_incoming" ||
            this.$route.params.menu_item == "lsp"
        },
        {
          text: this.$t("document.document_date"),
          value: "document_date",
          // width: 90,
          sortable: false,
          visible:
            this.$route.params.menu_item == "payment_sheet" ||
            this.$route.params.menu_item == "lsp"
        },
        {
          text: this.$t("department.branch"),
          value: "branch",
          // width: 90,
          sortable: false,
          visible: this.$route.params.menu_item == "payment_sheet"
        },
        {
          text: this.$t("report.organization"),
          value: "organization",
          // width: 90,
          sortable: false,
          visible: this.$route.params.menu_item == "payment_sheet"
        },
        {
          text: this.$t("report.expence_name"),
          value: "expence_name",
          // width: 90,
          sortable: false,
          visible: this.$route.params.menu_item == "payment_sheet"
        },
        {
          text: this.$t("report.contract_number"),
          value: "contract_number",
          // width: 90,
          sortable: false,
          visible: this.$route.params.menu_item == "payment_sheet"
        },
        {
          text: this.$t("report.contract_amount"),
          value: "contract_amount",
          // width: 90,
          sortable: false,
          visible: this.$route.params.menu_item == "payment_sheet"
        },
        {
          text: this.$t("report.amount_payments"),
          value: "amount_payments",
          // width: 90,
          sortable: false,
          visible: this.$route.params.menu_item == "payment_sheet"
        },
        {
          text: this.$t("report.payments_amount"),
          value: "payments_amount",
          // width: 90,
          sortable: false,
          visible: this.$route.params.menu_item == "payment_sheet"
        },
        {
          text: this.$t("report.approval_sheet"),
          value: "approval_sheet",
          // width: 90,
          sortable: false,
          visible: this.$route.params.menu_item == "payment_sheet"
        },
        {
          text: this.$t("partners.name"),
          value: "partners_name",
          // width: 90,
          sortable: false,
          visible: this.$route.params.menu_item == "lsp"
        },
        {
          text: this.$t("partners.purpose_payment"),
          value: "purpose_payment",
          // width: 120,
          sortable: false,
          visible: this.$route.params.menu_item == "lsp"
        },
        {
          text: this.$t("partners.amount_paid"),
          value: "amount_paid",
          // width: 90,
          sortable: false,
          visible: this.$route.params.menu_item == "lsp"
        },
        {
          text: this.$t("message.currency"),
          value: "currency",
          // width: 90,
          sortable: false,
          visible: this.$route.params.menu_item == "lsp"
        },
        {
          text: this.$t("partners.amount_words"),
          value: "amount_words",
          // width: 90,
          sortable: false,
          visible: this.$route.params.menu_item == "lsp"
        },
        {
          text: this.$t("partners.contract_number_date"),
          value: "contract_number_date",
          // width: 90,
          sortable: false,
          visible: this.$route.params.menu_item == "lsp"
        },
        {
          text: this.$t("partners.payment_terms"),
          value: "payment_terms",
          // width: 90,
          sortable: false,
          visible: this.$route.params.menu_item == "lsp"
        },
        {
          text: this.$t("partners.payment_details"),
          value: "payment_details",
          // width: 90,
          sortable: false,
          visible: this.$route.params.menu_item == "lsp"
        },
        {
          text: this.$t("partners.description"),
          value: "description",
          // width: 90,
          sortable: false,
          visible: this.$route.params.menu_item == "lsp"
        }
        // {
        //   text: this.$t("partners.debet"),
        //   value: "debet",
        //   // width: 90,
        //   sortable: false,
        //   visible: this.$route.params.menu_item == 'lsp'
        // },
      ];
      let localStorage = window.localStorage;

      return headers;
    },
    language() {
      return this.$i18n.locale == "ru" ? "uz_cyril" : this.$i18n.locale;
    }
  },
  methods: {
    modalText(val) {
      this.text = true;
      this.textVal = val;
    },
    format_date(value) {
      if (value) {
        return moment(String(value)).format("DD.MM.YYYY");
      }
    },
    updatePage($event) {
      this.getList();
    },
    updatePerPage($event) {
      this.getList();
    },
    toggleExpand($event) {},
    rowClick(item, row) {
      row.expand(!row.isExpanded);
      // if (this.expanded[0] && this.expanded[0].id == item.id) this.expanded = [];
      // else this.expanded = [item];
    },
    uploadExcel(table, name) {
      TableToExcel.convert(document.getElementById("table"));
    },
    getList() {
      // console.log(this.$route);
      this.loading = true;
      this.noDataText = this.$t("loadingText");
      axios
        .post(this.$store.state.backend_url + "api/compliance/filter", {
          pagination: this.dataTableOptions,
          language: this.$i18n.locale,
          filter: this.filter
        })
        .then(response => {
          this.items = response.data.documents.data;
          this.table_list_value = response.data.table_list_value;
          this.items.map((document, index) => {
            document.reaction_status = 0;
            document.action_type_id = 0;
            document.document_signers.map(document_signer => {
              if (this.user.employee_id == document_signer.signer_employee_id) {
                document.reaction_status = document_signer.status;
                document.action_type_id = document_signer.action_type_id;
              }
            });
            return document;
          });
          if (!this.items.length) {
            this.noDataText = this.$t("noDataText");
          }
          this.server_items_length = response.data.documents.total;
          this.from = response.data.documents.from;
          this.loading = false;
          this.getStaffs();
        })
        .catch(error => {
          console.log(error);
          this.loading = false;
        });
    },
    getDocumentTemplate() {
      axios
        .post(
          this.$store.state.backend_url + "api/document-templates/get-list",
          {
            language: this.$i18n.locale
          }
        )
        .then(res => {
          this.document_templates = res.data;
        })
        .catch(err => {
          console.error(err);
        });
    },
    getDepartment() {
      axios
        .get(
          this.$store.state.backend_url + "api/departments/dep"
        )
        .then(res => {
          this.departments = res.data;
          console.log(res);
        })
        .catch(err => {
          console.error(err);
        });
    },
    getDocumentType() {
      this.filter.attributes = [];
      axios
        .get(this.$store.state.backend_url + "api/document-types")
        .then(response => {
          this.document_type = response.data;
        })
        .catch(error => {
          console.log(error);
          this.loading = false;
        });
    },
    getFilter() {
      this.showFilter = true;
      this.document_template = this.document_templates.find(v => {
        return v.id == this.filter.document_template_id;
      });
      // console.log(this.filter);
      Cookies.set("filter", this.filter);
      this.getList();
    },
    cleanCookies() {
      Cookies.set("filter", null);
    },
    getTableList(id) {
      this.isLoading = true;
      let columnName =
        this.is_locale[id] == 1
          ? this.column_name[id] + "_" + this.$i18n.locale
          : this.column_name[id];
      axios
        .post(this.$store.state.backend_url + "api/documents/table-list", {
          table_name: this.table_name[id],
          column_name: columnName,
          is_locale: this.is_locale[id],
          search: this.search
        })
        .then(response => {
          this.tableLists["table_" + id] = response.data.data;
          this.isLoading = false;
        })
        .catch(error => {
          console.log(error);
          this.isLoading = false;
        });
    },
    getStaffs() {
      this.staffs = [];
      if (this.$route.params.menu_item == "archive") {
        if (this.$route.params.document_type == "employee") {
          this.isLoading = true;
          axios
            .post(this.$store.state.backend_url + "api/get-staffs", {
              search: this.search,
              employee: true
            })
            .then(res => {
              this.staffs = res.data.data;
              this.staffs.map(v => {
                v.search =
                  v.department_code +
                  " " +
                  v.department_name_ru +
                  " " +
                  v.department_name_uz_cyril +
                  " " +
                  v.department_name_uz_latin +
                  " " +
                  v.position_name_ru +
                  " " +
                  v.position_name_uz_cyril +
                  " " +
                  v.position_name_uz_latin;
              });
              this.isLoading = false;
            })
            .catch(err => {
              console.log(err);
              this.isLoading = false;
            });
        } else {
          if (this.$store.getters.checkPermission("all-document-show")) {
            this.isLoading = true;
            axios
              .post(this.$store.state.backend_url + "api/get-staffs", {
                search: this.search,
                employee: false
              })
              .then(res => {
                this.staffs = res.data.data;
                this.staffs.map(v => {
                  v.search =
                    v.department_code +
                    " " +
                    v.department_name_ru +
                    " " +
                    v.department_name_uz_cyril +
                    " " +
                    v.department_name_uz_latin +
                    " " +
                    v.position_name_ru +
                    " " +
                    v.position_name_uz_cyril +
                    " " +
                    v.position_name_uz_latin;
                });
                this.isLoading = false;
              })
              .catch(err => {
                console.log(err);
                this.isLoading = false;
              });
          } else {
            user.employee.employee_staff.map(v => {
              this.staffs.push(v.staff);
            });

            this.staffs.map(v => {
              v.department_code = v.department.department_code;
              v["department_name_" + this.$i18n.locale] =
                v.department["name_" + this.$i18n.locale];
              v["position_name_" + this.$i18n.locale] =
                v.position["name_" + this.$i18n.locale];
              v.search =
                v.department_code +
                " " +
                v.department.name_ru +
                " " +
                v.department.name_uz_cyril +
                " " +
                v.department.name_uz_latin +
                " " +
                v.position.name_ru +
                " " +
                v.position.name_uz_cyril +
                " " +
                v.position.name_uz_latin;
            });
          }
        }
      }
      // console.log(this.staffs);
    },
    editItem(item) {},
    documentCopy(id) {
      axios
        .post(this.$store.state.backend_url + "api/document/copy", {
          document_id: id,
          restore: false
        })
        .then(res => {
          this.$router.push("/document/update/" + res.data);
        })
        .catch(err => {
          console.log(err);
        });
    },
    restoreDocument(id) {
      axios
        .post(this.$store.state.backend_url + "api/document/copy", {
          document_id: id,
          restore: true
        })
        .then(res => {
          this.$router.push("/document/update/" + res.data);
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
          if (item.status == 0) {
            axios
              .delete(
                this.$store.state.backend_url +
                  "api/documents/delete/" +
                  item.id
              )
              .then(res => {
                Swal.fire("Deleted!", this.$t("swal_deleted"), "success");
                this.getList();
              })
              .catch(err => {
                Swal.fire({
                  icon: "error",
                  title: this.$t("swal_error_title"),
                  text: this.$t("swal_error_text")
                  // footer: "<a href>Why do I have  this issue ?</a>"
                });
                console.log(err);
              });
          }
        }
      });
    }
  },
  watch: {
    $route(to, from) {
      this.filter.menu_item = this.$route.params.menu_item;
      this.filter.document_type_id = this.$route.params.document_type;
      this.filter.staff_id = null;
      Cookies.set("filter", this.filter);
      this.getList();
    }
  },
  mounted() {
    // if (Cookies.get("filter")) {
    //   this.filter = JSON.parse(Cookies.get("filter"));
    // } else {
    //   this.filter.reaction_status = [0, 1, 2, 3, 4];
    // }
    if (this.$route.params.menu_item) {
      this.filter.menu_item = this.$route.params.menu_item;
      this.filter.document_type_id = this.$route.params.document_type;
      Cookies.set("filter", this.filter);
    }
    this.getDocumentTemplate();
    this.getList();
    this.getDepartment();
  }
};
</script>
<style scoped>
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
.filterBtn {
  color: #000;
  font-size: 12px;
  font-weight: 600;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
  border: 1px solid #e6e6e6;
  /* border-right: 0px; */
  border-left: 0px;
  background: #fff;
  box-shadow: none;
  border-radius: 0;
  text-transform: none;
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
</style>
