<template>
  <div class="fullHeight">
    <v-card 
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2"> {{ 'Manfaatlar to\'qnashuvi bo\'yicha reystr' }} </span>
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
          <v-btn v-if="!filterBtn" class="filterBtn px-2" style="background: #fff; height: 34px;" @click="filter.select = 1; getList(); filterBtn = !filterBtn"
            >{{ 'Hujjati yo\'qlar' }}
          </v-btn>
          <v-btn v-if="filterBtn" class="filterBtn px-2" style="background: #fff; height: 34px;" @click="filter.select = 0; getList(); filterBtn = !filterBtn"
            >{{ 'Barchasi' }}
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
        <!-- {{ $t("message." + $route.params.menu_item) }}</span> -->  
        <!-- <v-responsive max-width="300">
          <v-text-field
            v-model="filter.content"
            hide-details
            outlined
            dense
            :label="$t('searchInText')"
            class="mr-1"
            @keyup.native.enter="getFilter()"
          ></v-text-field>
        </v-responsive> -->
        <!-- <v-btn class="mx-3" @click="uploadExcel(1); complaens_excel = [];">
          <v-icon color="green">mdi-download-multiple</v-icon>
          <span style="color: green">{{ $t("excel") }}</span>
        </v-btn> -->
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
              itemsPerPageOptions: [20, 100, 200, 500],
              itemsPerPageAllText: $t('itemsPerPageAllText'),
              itemsPerPageText: $t('itemsPerPageText'),
              showFirstLastPage: true,
              firstIcon: 'mdi-arrow-collapse-left',
              lastIcon: 'mdi-arrow-collapse-right',
              prevIcon: 'mdi-arrow-left',
              nextIcon: 'mdi-arrow-right',
            }" @update:page="updatePage" 
            @update:items-per-page="updatePerPage" 
            :expanded.sync="expanded" 
            single-expand
            @update:expanded="toggleExpand" 
            @dblclick:row="rowClick">
            <template v-slot:body.prepend>
              <tr class="py-0 my-0 prepend_height">
                <td class="py-0 my-0 dense"></td>
                <td class="py-0 my-0 dense"></td>
                <td class="py-0 my-0 dense">
                  <v-text-field v-model="filter.tabel" type="text" hide-details
                    @keyup.native.enter="getList()"></v-text-field>
                </td>
                <td class="py-0 my-0 dense"></td>
                <td class="py-0 my-0 dense"></td>
                <td class="py-0 my-0 dense"></td>
                <td class="py-0 my-0 dense">
                  <v-text-field v-model="filter.doc_number" type="text" hide-details
                    @keyup.native.enter="getList()"></v-text-field>
                </td>
                <td class="py-0 my-0 dense"></td>
                <!-- <td class="py-0 my-0 dense">
                  <v-menu ref="rangeMenu" :close-on-content-click="false" :return-value.sync="filter.document_range" offset-y
                    min-width="290px">
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field v-model="filter.document_range" v-bind="attrs" @keyup.native.enter="getFilter()" v-on="on"
                        hide-details clearable></v-text-field>
                    </template>
                    <v-date-picker v-model="date" range no-title scrollable>
                      <v-spacer></v-spacer>
                      <v-btn text color="primary" @click="$refs.rangeMenu.isActive = false">Cancel</v-btn>
                      <v-btn text color="primary" @click="
                        $refs.rangeMenu.save(date);
                      filter.document_range = date;
                      getFilter();
                      ">OK</v-btn>
                    </v-date-picker>
                  </v-menu>
                </td> -->
                <!-- <td class="py-0 my-0 dense">
                  <v-text-field v-model="filter.id" type="text" hide-details @keyup.native.enter="getFilter()"></v-text-field>
                </td> -->

                <!-- <td class="py-0 my-0 dense">
                  <v-text-field v-model="filter.document_number" type="text" hide-details @keyup.native.enter="getFilter()"
                    clearable></v-text-field>
                </td> -->

                <!-- <td class="py-0 my-0 dense">
                  <v-autocomplete class="py-0" clearable v-model="filter.document_status" :items="document_status"
                    hide-details item-value="id" @change="getFilter()">
                    <template v-slot:selection="{ item }">{{ item["name_" + $i18n.locale] }}</template>
                    <template v-slot:item="{ item }">
                      <v-list-item-content>
                        <v-list-item-title v-text="item['name_' + $i18n.locale]"></v-list-item-title>
                      </v-list-item-content>
                    </template>
                  </v-autocomplete>
                </td> -->
              </tr>
            </template>
            <!-- <template v-slot:item.id="{ item }">{{
              items
                .map(function (x) {
                  return x.id;
                })
                .indexOf(item.id) + from
            }}</template> -->

            <!-- <template v-slot:item.fio="{ item }">
              {{
                item.create_document.length > 0 ? item.create_document[0].document_date : ''
              }}
            </template> -->
            <template v-slot:item.document_number="{ item }">
              <v-btn style="height: 21px; border-radius: 10px;" v-if="item.create_document.length > 0" class="success" small
                :to="'/document/' + item.create_document[0].pdf_file_name">
                {{
                  item.create_document[0].document_number_reg
                  ? item.create_document[0].document_number_reg
                  : item.create_document[0].document_number
                }}
              </v-btn>
            </template>

            <template v-slot:item.fio="{ item }">
              {{
                item.employee
                ? item.employee.lastname_uz_latin +
                " " +
                item.employee.firstname_uz_latin.substr(0, 1) +
                "." +
                item.employee.middlename_uz_latin.substr(0, 1) +
                "."
                : ""
              }}
            </template>
            <!-- <template v-slot:item.fio="{ item }">
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
            </template> -->
            <template v-slot:item.document_date="{ item }">
              {{
                item.create_document.length > 0
                ? item.create_document[0].document_date
                : ""
              }}
            </template>
            <template v-slot:item.employee="{ item }">
              {{
                item.create_document.length > 0
                ? item.create_document[0].employee.fio
                : ""
              }}
            </template>
            <template v-slot:item.tabel="{ item }">
              {{ item.employee ? item.employee.tabel : "" }}
            </template>
            <template v-slot:item.shtati="{ item }">
              <td style="min-width: 200px; max-width: 300px; white-space: normal;">
                {{
                  item.employee && item.employee.staff && item.employee.staff[0]
                  ? "(" +
                  item.employee.staff[0].department.department_code +
                  ") " +
                  item.employee.staff[0].department.name_uz_latin
                  : ""
                }}
                {{ " - " }}
                {{
                  item.employee && item.employee.staff && item.employee.staff[0]
                  ? item.employee.staff[0].position.name_uz_latin
                  : ""
                }}
              </td>
            </template>
            <!-- <template v-slot:item.tabel="{ item }">
              {{
                item.create_document.length > 0  ? item.create_document[0].employee.tabel : ''
              }}
            </template> -->
            <template v-slot:item.company="{ item }">
              {{ "" }}
            </template>
            <template v-slot:item.dangers="{ item }">
              {{ "" }}
            </template>
            <template v-slot:item.predmet="{ item }">
              {{
                item.create_document.length > 0 && item.create_document[0].compleans_answer &&
                item.create_document[0].compleans_answer[0] ?
                (item.create_document[0].compleans_answer[0].answer == 1 ? 'Ha' : 'Yo\'q') : ''
              }}
            </template>
            <template v-slot:item.potentsial="{ item }">
              {{ "" }}
            </template>
            <template v-slot:item.choralar="{ item }">
              {{ "" }}
            </template>
            <template v-slot:item.rekvizitlar="{ item }">
              {{ "" }}
            </template>
            <template v-slot:item.masul_shaxs="{ item }">
              {{ "" }}
            </template>
            <template v-slot:item.staff="{ item }">
              <span>
                {{
                  item.create_document.length > 0 &&
                  item.create_document[0].employee &&
                  item.create_document[0].employee.employee_staff[0] &&
                  item.create_document[0].employee.employee_staff[0].staff
                    .department
                  ? item.create_document[0].employee.employee_staff[0].staff
                    .department["name_" + $i18n.locale]
                  : ""
                }}
              </span>
              {{ "-" }}
              <span>
                <b>
                  {{
                    item.create_document.length > 0 &&
                    item.create_document[0].employee &&
                    item.create_document[0].employee.employee_staff[0] &&
                    item.create_document[0].employee.employee_staff[0].staff
                      .position
                    ? item.create_document[0].employee.employee_staff[0].staff
                      .position["name_" + $i18n.locale]
                    : ""
                  }}
                </b>
              </span>
            </template>
            <template v-slot:item.fish="{ item }">
              {{
                item.create_document.length > 0 &&
                item.create_document[0].document_details[0] &&
                item.create_document[0].document_details[0].document_detail_contents.find(
                  (v) => v.d_d_attribute_id == 2791
                )
                ? item.create_document[0].document_details[0].document_detail_contents.find(
                  (v) => v.d_d_attribute_id == 2791
                ).value
                : ""
              }}
            </template>
            <template v-slot:item.shtat="{ item }">
              <span>
                {{
                  item.create_document.length > 0 &&
                  item.create_document[0].document_details[0] &&
                  item.create_document[0].document_details[0].document_detail_contents.find(
                    (v) => v.d_d_attribute_id == 2794
                  )
                  ? item.create_document[0].document_details[0].document_detail_contents.find(
                    (v) => v.d_d_attribute_id == 2794
                  ).value
                  : ""
                }}
              </span>
              {{ "-" }}
              <span>
                <b>
                  {{
                    item.create_document.length > 0 &&
                    item.create_document[0].document_details[0] &&
                    item.create_document[0].document_details[0].document_detail_contents.find(
                      (v) => v.d_d_attribute_id == 2793
                    )
                    ? item.create_document[0].document_details[0].document_detail_contents.find(
                      (v) => v.d_d_attribute_id == 2793
                    ).value
                    : ""
                  }}
                </b>
              </span>
            </template>
            <template v-slot:item.document_type="{ item }">
              {{
                item.create_document.length > 0 &&
                item.create_document[0].document_details[0] &&
                item.create_document[0].document_details[0].document_detail_contents.find(
                  (v) => v.d_d_attribute_id == 1043 || v.d_d_attribute_id == 678
                )
                ? item.create_document[0].document_details[0].document_detail_contents.find(
                  (v) => v.d_d_attribute_id == 1043 || v.d_d_attribute_id == 678
                ).value
                : ""
              }}
            </template>
            <template v-slot:item.qrcode="{ item }">
              <img :src="(item.create_document.length > 0 && item.create_document[0].qrCode)
                ? item.create_document[0].qrCode.qrCode
                : ''
                " />
            </template>

            <template v-slot:item.pending_action="{ item }">
              <template v-for="document_signer in item.document_signers.filter((v) => {
                    if (v.status == 0 || v.status == 3 || v.status == 4) return v;
                  })">
                <div style="font-size: 11px" :key="document_signer.id" class="ma-0" v-if="item.status != 6">
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
            <template v-slot:item.doers="{ item }">
              <template v-for="document_signer in item.document_signers.filter((v) => {
                    if (v.action_type_id == 4) return v;
                  })">
                <div style="font-size: 11px" :key="document_signer.id" class="ma-0" v-if="item.status != 6">
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
            <template v-slot:item.signers="{ item }">
              <template v-for="document_signer in item.document_signers.filter((v) => {
                    if (v.action_type_id == 2) return v;
                  })">
                <div style="font-size: 11px" :key="document_signer.id" class="ma-0" v-if="item.status != 6">
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
            <template v-slot:item.file="{ item }">
              <template v-for="file in item.files">
                <div style="font-size: 11px" class="ma-0">
                  <a style="text-decoration: none" :href="$store.state.backend_url + 'staffs/file-download/' + file.id
                    ">
                    {{ file.file_name }}
                  </a>
                </div>
              </template>
            </template>
            <template v-slot:item.barn="{ item }">
              <v-row>
                <v-col class="col-12 py-0">
                  {{
                    item.document_details[0] &&
                    item.document_details[0].document_detail_contents.find(
                      (v) => v.d_d_attribute_id == 2836
                    )
                    ? item.document_details[0].document_detail_contents.find(
                      (v) => v.d_d_attribute_id == 2836
                    ).value
                    : ""
                  }}
                </v-col>
              </v-row>
            </template>
            <template v-slot:item.reporting_month="{ item }">
              <v-row style="max-width: 150px">
                <v-col class="col-12 text-truncate py-0">
                  {{
                    item.document_details[0] &&
                    item.document_details[0].document_detail_contents.find(
                      (v) => v.d_d_attribute_id == 2837
                    )
                    ? item.document_details[0].document_detail_contents
                      .find((v) => v.d_d_attribute_id == 2837)
                      .value.slice(0, 4) +
                    "-" +
                    item.document_details[0].document_detail_contents
                      .find((v) => v.d_d_attribute_id == 2837)
                      .value.slice(4)
                    : ""
                  }}
                </v-col>
              </v-row>
            </template>
            <template v-slot:item.summary="{ item }">
              <v-row style="max-width: 150px">
                <v-col class="col-12 text-truncate py-0">
                  {{
                    item.document_details[0] &&
                    item.document_details[0].document_detail_contents.find(
                      (v) =>
                        v.d_d_attribute_id == 1147 ||
                        v.d_d_attribute_id == 715 ||
                        v.d_d_attribute_id == 1040 ||
                        v.d_d_attribute_id == 674
                    )
                    ? item.document_details[0].document_detail_contents.find(
                      (v) =>
                        v.d_d_attribute_id == 1147 ||
                        v.d_d_attribute_id == 715 ||
                        v.d_d_attribute_id == 1040 ||
                        v.d_d_attribute_id == 674
                    ).value
                    : ""
                  }}
                </v-col>
              </v-row>
            </template>
            <template v-slot:item.registration="{ item }">
              {{
                item.document_details[0] &&
                item.document_details[0].document_detail_contents.find(
                  (v) =>
                    v.d_d_attribute_id == 1133 ||
                    v.d_d_attribute_id == 1041 ||
                    v.d_d_attribute_id == 675 ||
                    v.d_d_attribute_id == 700
                )
                ? item.document_details[0].document_detail_contents.find(
                  (v) =>
                    v.d_d_attribute_id == 1133 ||
                    v.d_d_attribute_id == 1041 ||
                    v.d_d_attribute_id == 675 ||
                    v.d_d_attribute_id == 700
                ).value
                : ""
              }}
            </template>
            <template v-slot:item.company_name="{ item }">
              {{
                item.document_details[0] &&
                item.document_details[0].document_detail_contents &&
                item.document_details[0].document_detail_contents.find(
                  (v) => v.d_d_attribute_id == 599 || v.d_d_attribute_id == 972
                )
                ? item.document_details[0].document_detail_contents.find(
                  (v) => v.d_d_attribute_id == 599 || v.d_d_attribute_id == 972
                ).value
                : ""
              }}
            </template>
            <template v-slot:item.date_out="{ item }">
              {{
                item.document_details[0] &&
                item.document_details[0].document_detail_contents.find(
                  (v) => v.d_d_attribute_id == 676 || v.d_d_attribute_id == 1042
                )
                ? item.document_details[0].document_detail_contents.find(
                  (v) => v.d_d_attribute_id == 676 || v.d_d_attribute_id == 1042
                ).value
                : ""
              }}
            </template>
            <template v-slot:item.registration_uz_as="{ item }">
              {{
                item.document_details[0] &&
                item.document_details[0].document_detail_contents.find(
                  (v) =>
                    v.d_d_attribute_id == 1136 ||
                    v.d_d_attribute_id == 704 ||
                    v.d_d_attribute_id == 1044 ||
                    v.d_d_attribute_id == 679
                )
                ? item.document_details[0].document_detail_contents.find(
                  (v) =>
                    v.d_d_attribute_id == 1136 ||
                    v.d_d_attribute_id == 704 ||
                    v.d_d_attribute_id == 1044 ||
                    v.d_d_attribute_id == 679
                ).value
                : ""
              }}
            </template>
            <template v-slot:item.korrespondent="{ item }">
              {{
                item.document_details[0] &&
                item.document_details[0].document_detail_contents.find(
                  (v) =>
                    v.d_d_attribute_id == 1137 ||
                    v.d_d_attribute_id == 1045 ||
                    v.d_d_attribute_id == 680 ||
                    v.d_d_attribute_id == 705
                )
                ? item.document_details[0].document_detail_contents.find(
                  (v) =>
                    v.d_d_attribute_id == 1137 ||
                    v.d_d_attribute_id == 1045 ||
                    v.d_d_attribute_id == 680 ||
                    v.d_d_attribute_id == 705
                ).value
                : ""
              }}
            </template>
            <!-- <template v-slot:item.fio="{ item }">
              {{
                item.document_details[0] &&
                item.document_details[0].document_detail_contents.find(
                  (v) =>
                    v.d_d_attribute_id == 1138 || v.d_d_attribute_id == 706
                )
                ? item.document_details[0].document_detail_contents.find(
                  (v) =>
                    v.d_d_attribute_id == 1138 || v.d_d_attribute_id == 706
                ).value
                : ""
              }}
            </template> -->
            <template v-slot:item.region="{ item }">
              {{
                item.document_details[0] &&
                item.document_details[0].document_detail_contents.find(
                  (v) => v.d_d_attribute_id == 1139 || v.d_d_attribute_id == 707
                )
                ? item.document_details[0].document_detail_contents.find(
                  (v) => v.d_d_attribute_id == 1139 || v.d_d_attribute_id == 707
                ).value
                : ""
              }}
            </template>
            <template v-slot:item.appeal="{ item }">
              {{
                item.document_details[0] &&
                item.document_details[0].document_detail_contents.find(
                  (v) => v.d_d_attribute_id == 1140 || v.d_d_attribute_id == 708
                )
                ? item.document_details[0].document_detail_contents.find(
                  (v) => v.d_d_attribute_id == 1140 || v.d_d_attribute_id == 708
                ).value
                : ""
              }}
            </template>
            <template v-slot:item.model="{ item }">
              {{
                item.document_details[0] &&
                item.document_details[0].document_detail_contents.find(
                  (v) => v.d_d_attribute_id == 1141 || v.d_d_attribute_id == 709
                )
                ? item.document_details[0].document_detail_contents.find(
                  (v) => v.d_d_attribute_id == 1141 || v.d_d_attribute_id == 709
                ).value
                : ""
              }}
            </template>
            <template v-slot:item.due_date="{ item }">
              {{
                item.document_details[0] &&
                item.document_details[0].document_detail_contents.find(
                  (v) =>
                    v.d_d_attribute_id == 1131 ||
                    v.d_d_attribute_id == 698 ||
                    v.d_d_attribute_id == 1038 ||
                    v.d_d_attribute_id == 672
                )
                ? item.document_details[0].document_detail_contents.find(
                  (v) =>
                    v.d_d_attribute_id == 1131 ||
                    v.d_d_attribute_id == 698 ||
                    v.d_d_attribute_id == 1038 ||
                    v.d_d_attribute_id == 672
                ).value
                : ""
              }}
            </template>
            <template v-slot:expanded-item="{ headers, item }">
              <td :colspan="headers.length">
                <v-row class="justify-center">
                  <v-col cols="12" md="10" lg="9" xl="7" class="py-1 ma-2">
                    <v-card class="pa-3">
                      <div v-for="(
                          document_detail, detail_index
                        ) in item.document_details" :key="detail_index">
                        <strong style="float: left" v-if="item.document_details.length > 1">{{ detail_index + 1 }}.</strong>
                        <p class="text-left font-weight-black my-1 pl-6" v-html="document_detail.content"></p>
                        <v-simple-table dense>
                          <template v-slot:default>
                            <tbody>
                              <tr v-for="item in document_detail.document_detail_attribute_values" :key="item.index">
                                <td class="text-right px-2">
                                  <b>
                                    {{
                                      item.document_detail_attributes[
                                      "attribute_name_" + $i18n.locale
                                      ]
                                    }}:
                                  </b>
                                </td>
                                <td class="text-left px-2" width="50%">
                                  {{ item.attribute_value }}
                                </td>
                              </tr>
                            </tbody>
                          </template>
                        </v-simple-table>
                      </div>
                    </v-card>
                  </v-col>
                </v-row>
              </td>
            </template>
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

    <v-dialog v-model="downloadExcel" hide-overlay persistent width="300">
      <v-card>
        <v-card-text class="py-1 px-3">
          <v-btn color="success" class="mx-10" @click="downloadExcel = false" text>
            <download-excel :data="complaens_excel" :name="'compleans' + today + '.xls'">
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
      isLoading: false,
      expanded: [],
      complaens_excel: [],
      search: "",
      dialog: false,
      today: moment().format("YYYY-MM-DD"),
      downloadExcel: false,
      filterBtn: false,
      editMode: null,
      items: [],
      barn: [],
      yearmonth: [],
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
            document_detail_attributes: {},
          },
        ],
      },
      document_templates: [],
      filter: {
        title: "",
        id: ""
      },
      showFilter: false,
      menu: [],
      tableLists: [],
      table_name: [],
      column_name: [],
      is_locale: [],
      reaction_status: [
        {
          text: this.$t("document.new"),
          value: 0,
        },
        {
          text: this.$t("document.ok"),
          value: 1,
        },
        {
          text: this.$t("document.cancel"),
          value: 2,
        },
        {
          text: this.$t("document.process"),
          value: 3,
        },
        {
          text: this.$t("substantiate"),
          value: 4,
        },
      ],
      noDataText: "",
      staffs: [],
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
        // { text: "", value: "data-table-expand", visible: true },
        // { text: "#", value: "id", align: "center", width: 30 },
        {
          text: this.$t("ID"),
          align: "center",
          width: 30,
          value: "id",
          sortable: false,
          visible: true,
        },
        {
          text: this.$t("FIO"),
          value: "fio",
          align: "center",
          // width: 90,
          sortable: false,
          visible: true,
        },
        {
          text: this.$t("Tabel raqami"),
          value: "tabel",
          align: "center",
          // width: 90,
          sortable: false,
          visible: true,
        },
        {
          text: this.$t("Xodimining lavozimi"),
          value: "shtati",
          // align: "center",
          // width: 350,
          sortable: false,
          visible: true,
        },
        {
          text: this.$t("Ma'lumot olingan sana"),
          value: "document_date",
          align: "center",
          // width: 90,
          sortable: false,
          visible: true,
        },
        // {
        //   text: this.$t("Manfaatlar to'qnashuvi mavjud bo'lgan xodimning F.I.Sh"),
        //   value: "employee",
        //   align: "center",
        //   width: 90,
        //   sortable: false,
        //   visible: true
        // },
        // {
        //   text: this.$t("Nomzodning FISH"),
        //   value: "fish",
        //   align: "center",
        //   width: 90,
        //   sortable: false,
        //   visible: true
        // },
        // {
        //   text: this.$t("Jamiyat tarkibiy tuzilmasi va xodimining lavozimi"),
        //   value: "staff",
        //   // align: "center",
        //   // width: 350,
        //   sortable: false,
        //   visible: true
        // },
        {
          text: this.$t("Manfaatlar to'qnashuvining predmeti"),
          value: "predmet",
          // align: "center",
          // width: 350,
          sortable: false,
          visible: true,
        },
        // {
        //   text: this.$t("Manfaatlar to'qnashuvining turi (haqiqiy yoki potentsial)"),
        //   value: "potentsial",
        //   // align: "center",
        //   // width: 350,
        //   sortable: false,
        //   visible: true
        // },
        // {
        //   text: this.$t("Manfaatlar to'qnashuvini tartibga solish bo'yicha choralar"),
        //   value: "choralar",
        //   // align: "center",
        //   // width: 350,
        //   sortable: false,
        //   visible: true
        // },
        // {
        //   text: this.$t("Odob-ahloq komissiya yig'ilish bayonnomasi raqami va sanasi, adrlar bo'limi tomonidan qabul kilingan qaror rekvizitlari"),
        //   value: "rekvizitlar",
        //   // align: "center",
        //   // width: 350,
        //   sortable: false,
        //   visible: true
        // },
        // {
        //   text: this.$t("Belgilangan choralarning amalga oshirilishi ustidan nazorat qilish bo'yicha mas'ul shaxs"),
        //   value: "masul_shaxs",
        //   // align: "center",
        //   // width: 350,
        //   sortable: false,
        //   visible: true
        // },
        {
          text: this.$t("Nomzodni tekshirish bo'yicha hisobot raqami"),
          value: "document_number",
          align: "center",
          // width: 150,
          sortable: false,
          visible: true,
        },

        {
          text: this.$t("QrCode"),
          value: "qrcode",
          // width: 150,
          align: "center",
          sortable: false,
          visible: true,
        },
        // {
        //   text: this.$t("document.status"),
        //   value: "status",
        //   align: "center",
        //   sortable: false,
        //   visible: true
        // },
        // {
        //   text: this.$t("document.barn"),
        //   value: "barn",
        //   align: "center",
        //   sortable: false,
        //   visible: true
        // },
        // {
        //   text: this.$t("document.reporting_month"),
        //   value: "reporting_month",
        //   align: "center",
        //   sortable: false,
        //   visible: true
        // },
      ];
      let localStorage = window.localStorage;
      return headers;
    },
    language() {
      return this.$i18n.locale == "ru" ? "uz_cyril" : this.$i18n.locale;
    },
  },
  methods: {

    uploadExcel(page) {
      let new_array = [];
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/complaens/get-excel", {
          filter: this.filter,
          locale: this.$i18n.locale,
          page: page,
          perPage: 500
        })
        .then(response => {
          new_array = response.data.data.map(v => {
            v.fio = v.employee ? v.employee.fio : '';
            v.tabel = v.employee ? v.employee.tabel : '';
            v.staff = v.employee && v.employee.employee_staff && v.employee.employee_staff[0] && v.employee.employee_staff[0].staff ?
              (v.employee.employee_staff[0].staff.department ? v.employee.employee_staff[0].staff.department.name_uz_latin : '') + ' - ' +
              (v.employee.employee_staff[0].staff.position ? v.employee.employee_staff[0].staff.position.name_uz_latin : '')
              : '';
            v.document_date = v.create_document && v.create_document[0] ? v.create_document[0].document_date : '';
            v.predmet = v.create_document && v.create_document[0] && v.create_document[0].compleans_answer && v.create_document[0].compleans_answer[0] ?
              (v.create_document[0].compleans_answer[0].answer == 1 ? 'Ha' : 'Yo\'q') : '';
            v.doc_number = v.create_document && v.create_document[0] ? v.create_document[0].document_number : '';

            return v;
          });
          this.complaens_excel = this.complaens_excel.concat(new_array);

          for (let i = 0; i < this.complaens_excel.length; i++) {
            delete this.complaens_excel[i].employee;
            delete this.complaens_excel[i].create_document;
          }
          console.log(response);
          if (response.data.data.length == 500) {
            this.uploadExcel(++page);
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
    // uploadExcel(table, name) {
    //   TableToExcel.convert(document.getElementById("table"));
    // },
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
    toggleExpand($event) { },
    rowClick(item, row) {
      row.expand(!row.isExpanded);
      // if (this.expanded[0] && this.expanded[0].id == item.id) this.expanded = [];
      // else this.expanded = [item];
    },
    getList() {
      // console.log(this.$route);
      this.loading = true;
      this.noDataText = this.$t("loadingText");
      axios
        .post(this.$store.state.backend_url + "api/complaens/users", {
          pagination: this.dataTableOptions,
          language: this.$i18n.locale,
          filter: this.filter,
        })
        .then((response) => {
          // console.log(response);
          if (response.data.data) {
            let doc_ids = response.data.data.map((v) =>
              (v.create_document.length > 0 && v.create_document[0].id) ? v.create_document[0].id : ""
            );
            doc_ids = doc_ids.filter((v) => v);
            // console.log(doc_ids);
            axios
              .post(this.$store.state.backend_url + "api/complaens/qrcode", {
                doc_ids: doc_ids,
              })
              .then((resp) => {
                // console.log(resp)
                this.items = response.data.data;
                let array_qrcode = resp.data;
                this.items = response.data.data.map((v) => {
                  if (v.create_document.length > 0) {
                    v.create_document[0].qrCode = array_qrcode.find(
                      (s) => s.id == v.create_document[0].id
                    );
                  }
                  return v;
                });
                // console.log(array_qrcode);
                // console.log(this.items);
              })
              .catch((error) => {
                console.log(error);
                this.loading = false;
              });
          }
          this.table_list_value = response.data.table_list_value;

          if (!this.items.length) {
            this.noDataText = this.$t("noDataText");
          }
          this.server_items_length = response.data.total;
          this.from = response.data.from;
          this.loading = false;
          this.getStaffs();
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });

      // axios
      //   .post(this.$store.state.backend_url + "api/mobile/document-list", {
      //     pagination: this.dataTableOptions,
      //     language: this.$i18n.locale,
      //     filter: this.filter,
      //   })
      //   .then((res) => {
      //     console.log(res);
      //   })
      //   .catch((err) => {
      //     console.log(err);
      //   });
    },
    getDocumentTemplate() {
      axios
        .post(
          this.$store.state.backend_url + "api/document-templates/get-list",
          {
            language: this.$i18n.locale,
          }
        )
        .then((res) => {
          this.document_templates = res.data;
        })
        .catch((err) => {
          console.error(err);
        });
    },
    getBarn() {
      axios
        .get(this.$store.state.backend_url + "api/barn")
        .then((res) => {
          // console.log(res.data);
          this.barn = res.data;
        })
        .catch((err) => {
          console.error(err);
        });
    },
    getDocumentType() {
      this.filter.attributes = [];
      axios
        .get(this.$store.state.backend_url + "api/document-types")
        .then((response) => {
          this.document_type = response.data;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getFilter() {
      this.showFilter = true;
      this.document_template = this.document_templates.find((v) => {
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
          search: this.search,
        })
        .then((response) => {
          this.tableLists["table_" + id] = response.data.data;
          this.isLoading = false;
        })
        .catch((error) => {
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
              employee: true,
            })
            .then((res) => {
              this.staffs = res.data.data;
              this.staffs.map((v) => {
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
            .catch((err) => {
              console.log(err);
              this.isLoading = false;
            });
        } else {
          if (this.$store.getters.checkPermission("all-document-show")) {
            this.isLoading = true;
            axios
              .post(this.$store.state.backend_url + "api/get-staffs", {
                search: this.search,
                employee: false,
              })
              .then((res) => {
                this.staffs = res.data.data;
                this.staffs.map((v) => {
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
              .catch((err) => {
                console.log(err);
                this.isLoading = false;
              });
          } else {
            user.employee.employee_staff.map((v) => {
              this.staffs.push(v.staff);
            });

            this.staffs.map((v) => {
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
  },
  watch: {
    $route(to, from) {
      this.filter.menu_item = this.$route.params.menu_item;
      this.filter.document_type_id = this.$route.params.document_type;
      this.filter.staff_id = null;
      Cookies.set("filter", this.filter);
      this.getList();
    },
  },
  mounted() {
    // this.getBarn();
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
  },
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