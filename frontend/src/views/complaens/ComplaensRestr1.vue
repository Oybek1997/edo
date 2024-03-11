<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span style=" text-transform: capitalize;"> {{ $t('message.' + $route.params.menu_item) }}</span>
        <v-btn to="/documents/control/murojaat_nazorat/4" style="color: green" class="ml-5"
          v-if="$route.params.menu_item == 'murojaat'">{{ $t('message.document_control') }}</v-btn>
        <v-btn to="/documents/control/kiruvchi_nazorat/24" style="color: green" class="ml-5"
          v-if="$route.params.menu_item == 'kiruvchi'">{{ $t('message.document_control') }}</v-btn>
        <v-spacer></v-spacer>
        <v-responsive max-width="300">
          <v-text-field v-model="filter.content" hide-details outlined dense :label="$t('searchInText')" class="mr-1"
            @keyup.native.enter="getFilter()"></v-text-field>
        </v-responsive>
        <v-btn class="mx-3" @click="uploadExcel('table', 'Lorem Table')">
          <v-icon color="green">mdi-download-multiple</v-icon>
          <span style="color: green">{{ $t("excel") }}</span>
        </v-btn>
      </v-card-title>
      <v-data-table id="table" dense fixed-header :loading-text="$t('loadingText')" :no-data-text="noDataText"
        :height="screenHeight" :loading="loading" :headers="headers.filter(v => v.visible)" :items="items"
        class="ma-1 mainTable" style="border: 1px solid #aaa" item-key="id" :server-items-length="server_items_length"
        :options.sync="dataTableOptions" :disable-pagination="true" :footer-props="{
          itemsPerPageOptions: [20, 100, 200, 1000 - 1],
          itemsPerPageAllText: $t('itemsPerPageAllText'),
          itemsPerPageText: $t('itemsPerPageText'),
          showFirstLastPage: true,
          firstIcon: 'mdi-arrow-collapse-left',
          lastIcon: 'mdi-arrow-collapse-right',
          prevIcon: 'mdi-arrow-left',
          nextIcon: 'mdi-arrow-right',
        }" @update:page="updatePage" @update:items-per-page="updatePerPage" :expanded.sync="expanded" single-expand
        @update:expanded="toggleExpand" @dblclick:row="rowClick">
        <!-- <template v-slot:body.prepend>
          <tr class="py-0 my-0">
            <td class="py-0 my-0 dense"></td>
            <td class="py-0 my-0 dense">
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
            </td>
            <td class="py-0 my-0 dense">
              <v-text-field v-model="filter.id" type="text" hide-details @keyup.native.enter="getFilter()"></v-text-field>
            </td>

            <td class="py-0 my-0 dense">
              <v-text-field v-model="filter.document_number" type="text" hide-details @keyup.native.enter="getFilter()"
                clearable></v-text-field>
            </td>

            <td class="py-0 my-0 dense">
              <v-autocomplete class="py-0" clearable v-model="filter.document_status" :items="document_status"
                hide-details item-value="id" @change="getFilter()">
                <template v-slot:selection="{ item }">{{ item["name_" + $i18n.locale] }}</template>
                <template v-slot:item="{ item }">
                  <v-list-item-content>
                    <v-list-item-title v-text="item['name_' + $i18n.locale]"></v-list-item-title>
                  </v-list-item-content>
                </template>
              </v-autocomplete>
            </td>
          </tr>
        </template> -->
        <template v-slot:item.id="{ item }">{{
          items
            .map(function (x) {
              return x.id;
            })
            .indexOf(item.id) + from
        }}</template>

        <template v-slot:item.document_number="{ item }">
          <v-btn class="success" small  :to="'/document/' + item.pdf_file_name">{{ item.document_number_reg ? item.document_number_reg :
    item.document_number }}</v-btn>
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
        <template v-slot:item.employee="{ item }">
          {{
            item.employee.fio
          }}
        </template>
        <template v-slot:item.tabel="{ item }">
          {{
            item.employee.tabel
          }}
        </template>
        <template v-slot:item.company="{ item }">
          {{
            ''
          }}
        </template>
        <template v-slot:item.dangers="{ item }">
          {{
            ''
          }}
        </template>
        <template v-slot:item.predmet="{ item }">
          {{
            ''
          }}
        </template>
        <template v-slot:item.potentsial="{ item }">
          {{
            ''
          }}
        </template>
        <template v-slot:item.choralar="{ item }">
          {{
            ''
          }}
        </template>
        <template v-slot:item.rekvizitlar="{ item }">
          {{
            ''
          }}
        </template>
        <template v-slot:item.masul_shaxs="{ item }">
          {{
            ''
          }}
        </template>
        <template v-slot:item.staff="{ item }">
          <span>
            {{
              item.employee && item.employee.employee_staff[0] && item.employee.employee_staff[0].staff.department
              ? item.employee.employee_staff[0].staff.department["name_" + $i18n.locale] : ''
            }}
          </span>
          {{ '-' }}
          <span>
            <b>
              {{
                item.employee && item.employee.employee_staff[0] && item.employee.employee_staff[0].staff.position
                ? item.employee.employee_staff[0].staff.position["name_" + $i18n.locale] : ''
              }}
            </b>
          </span>
        </template>
        <template v-slot:item.fish="{ item }">
          {{
            item.document_details[0] &&
            item.document_details[0].document_detail_contents.find(
              (v) => v.d_d_attribute_id == 2791)
            ? item.document_details[0].document_detail_contents.find(
              (v) => v.d_d_attribute_id == 2791).value : ""
          }}
        </template>
        <template v-slot:item.shtat="{ item }">
          <span>
            {{
              item.document_details[0] &&
              item.document_details[0].document_detail_contents.find(
                (v) => v.d_d_attribute_id == 2794)
              ? item.document_details[0].document_detail_contents.find(
                (v) => v.d_d_attribute_id == 2794).value : ""
            }}
          </span>
          {{ '-' }}
          <span>
            <b>
              {{
                item.document_details[0] &&
                item.document_details[0].document_detail_contents.find(
                  (v) => v.d_d_attribute_id == 2793)
                ? item.document_details[0].document_detail_contents.find(
                  (v) => v.d_d_attribute_id == 2793).value : ""
              }}
            </b>
          </span>
        </template>
        <template v-slot:item.document_type="{ item }">
          {{
            item.document_details[0] &&
            item.document_details[0].document_detail_contents.find(
              (v) =>
                v.d_d_attribute_id == 1043 || v.d_d_attribute_id == 678
            )
            ? item.document_details[0].document_detail_contents.find(
              (v) =>
                v.d_d_attribute_id == 1043 || v.d_d_attribute_id == 678
            ).value
            : ""
          }}
        </template>
        <template v-slot:item.qrcode="{ item }">
          <img :src="item.qrCode.qrCode">
        </template>
        <template v-slot:item.status="{ item }" style="padding: 0px">
          <span v-if="item.status == 0" style="
              display: block;
              background: #757575;
              color: #fff;
              border-radius: 10px;
              padding: 1px 5px;
            ">
            {{
              document_status[item.status]
              ? document_status[item.status]["name_" + $i18n.locale]
              : ""
            }}
          </span>
          <span v-if="item.status == 1" style="
              display: block;
              background: #00acc1;
              color: #fff;
              border-radius: 10px;
              padding: 1px 5px;
            ">
            {{
              document_status[item.status]
              ? document_status[item.status]["name_" + $i18n.locale]
              : ""
            }}
          </span>
          <span v-if="item.status == 2" style="
              display: block;
              background: #039be5;
              color: #fff;
              border-radius: 10px;
              padding: 1px 5px;
            ">
            {{
              document_status[item.status]
              ? document_status[item.status]["name_" + $i18n.locale]
              : ""
            }}
          </span>
          <span v-if="item.status == 3" style="
              display: block;
              background: teal;
              color: #fff;
              border-radius: 10px;
              padding: 1px 5px;
            ">
            {{
              document_status[item.status]
              ? document_status[item.status]["name_" + $i18n.locale]
              : ""
            }}
          </span>
          <span v-if="item.status == 4" style="
              display: block;
              background: #d8cd1d;
              color: #fff;
              border-radius: 10px;
              padding: 1px 5px;
            ">
            {{
              document_status[item.status]
              ? document_status[item.status]["name_" + $i18n.locale]
              : ""
            }}
          </span>
          <span v-if="item.status == 5" style="
              display: block;
              background: #00c853;
              color: #fff;
              border-radius: 10px;
              padding: 1px 5px;
            ">
            {{
              document_status[item.status]
              ? document_status[item.status]["name_" + $i18n.locale]
              : ""
            }}
          </span>
          <span v-if="item.status == 6" style="
              display: block;
              background: #ef6c00;
              color: #fff;
              border-radius: 10px;
              padding: 1px 5px;
            ">
            {{
              document_status[item.status]
              ? document_status[item.status]["name_" + $i18n.locale]
              : ""
            }}
          </span>
          <span v-if="item.status == 7" style="
              display: block;
              background: #8bc34a;
              color: #fff;
              border-radius: 10px;
              padding: 1px 5px;
            ">
            {{
              document_status[item.status]
              ? document_status[item.status]["name_" + $i18n.locale]
              : ""
            }}
          </span>
        </template>
        <template v-slot:item.pending_action="{ item }">
          <template v-for="document_signer in item.document_signers.filter((v) => {
                if (v.status == 0 || v.status == 3 || v.status == 4) return v;
              })">
            <div style="font-size:11px" :key="document_signer.id" class="ma-0" v-if="item.status != 6">
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
            <div style="font-size:11px" :key="document_signer.id" class="ma-0" v-if="item.status != 6">
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
            <div style="font-size:11px" :key="document_signer.id" class="ma-0" v-if="item.status != 6">
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
            <div style="font-size:11px" class="ma-0">
              <a style="text-decoration: none;" :href="$store.state.backend_url + 'staffs/file-download/' + file.id">
                {{
                  file.file_name
                }}
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
                  (v) =>
                    v.d_d_attribute_id == 2836
                )
                ? item.document_details[0].document_detail_contents.find(
                  (v) =>
                    v.d_d_attribute_id == 2836
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
                  (v) =>
                    v.d_d_attribute_id == 2837
                )
                ? (item.document_details[0].document_detail_contents.find(
                  (v) =>
                    v.d_d_attribute_id == 2837
                ).value.slice(0, 4) + '-' + item.document_details[0].document_detail_contents.find(
                  (v) => v.d_d_attribute_id == 2837
                ).value.slice(4))
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
                    v.d_d_attribute_id == 1147 || v.d_d_attribute_id == 715 || v.d_d_attribute_id == 1040 || v.d_d_attribute_id
                    == 674
                )
                ? item.document_details[0].document_detail_contents.find(
                  (v) =>
                    v.d_d_attribute_id == 1147 || v.d_d_attribute_id == 715 || v.d_d_attribute_id == 1040 || v.d_d_attribute_id
                    == 674
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
                v.d_d_attribute_id == 1133 || v.d_d_attribute_id == 1041 || v.d_d_attribute_id == 675 || v.d_d_attribute_id ==
                700
            )
            ? item.document_details[0].document_detail_contents.find(
              (v) =>
                v.d_d_attribute_id == 1133 || v.d_d_attribute_id == 1041 || v.d_d_attribute_id == 675 || v.d_d_attribute_id ==
                700
            ).value
            : ""
          }}
        </template>
        <template v-slot:item.company_name="{ item }">
          {{
            item.document_details[0] && item.document_details[0].document_detail_contents &&
            item.document_details[0].document_detail_contents.find(
              (v) =>
                v.d_d_attribute_id == 599 || v.d_d_attribute_id == 972
            )
            ? item.document_details[0].document_detail_contents.find(
              (v) =>
                v.d_d_attribute_id == 599 || v.d_d_attribute_id == 972
            ).value
            : ""
          }}
        </template>
        <template v-slot:item.date_out="{ item }">
          {{
            item.document_details[0] &&
            item.document_details[0].document_detail_contents.find(
              (v) =>
                v.d_d_attribute_id == 676 || v.d_d_attribute_id == 1042
            )
            ? item.document_details[0].document_detail_contents.find(
              (v) =>
                v.d_d_attribute_id == 676 || v.d_d_attribute_id == 1042
            ).value
            : ""
          }}
        </template>
        <template v-slot:item.registration_uz_as="{ item }">
          {{
            item.document_details[0] &&
            item.document_details[0].document_detail_contents.find(
              (v) =>
                v.d_d_attribute_id == 1136 || v.d_d_attribute_id == 704 || v.d_d_attribute_id == 1044 || v.d_d_attribute_id ==
                679
            )
            ? item.document_details[0].document_detail_contents.find(
              (v) =>
                v.d_d_attribute_id == 1136 || v.d_d_attribute_id == 704 || v.d_d_attribute_id == 1044 || v.d_d_attribute_id ==
                679
            ).value
            : ""
          }}
        </template>
        <template v-slot:item.korrespondent="{ item }">
          {{
            item.document_details[0] &&
            item.document_details[0].document_detail_contents.find(
              (v) =>
                v.d_d_attribute_id == 1137 || v.d_d_attribute_id == 1045 || v.d_d_attribute_id == 680 || v.d_d_attribute_id ==
                705
            )
            ? item.document_details[0].document_detail_contents.find(
              (v) =>
                v.d_d_attribute_id == 1137 || v.d_d_attribute_id == 1045 || v.d_d_attribute_id == 680 || v.d_d_attribute_id ==
                705
            ).value
            : ""
          }}
        </template>
        <template v-slot:item.fio="{ item }">
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
        </template>
        <template v-slot:item.region="{ item }">
          {{
            item.document_details[0] &&
            item.document_details[0].document_detail_contents.find(
              (v) =>
                v.d_d_attribute_id == 1139 || v.d_d_attribute_id == 707
            )
            ? item.document_details[0].document_detail_contents.find(
              (v) =>
                v.d_d_attribute_id == 1139 || v.d_d_attribute_id == 707
            ).value
            : ""
          }}
        </template>
        <template v-slot:item.appeal="{ item }">
          {{
            item.document_details[0] &&
            item.document_details[0].document_detail_contents.find(
              (v) =>
                v.d_d_attribute_id == 1140 || v.d_d_attribute_id == 708
            )
            ? item.document_details[0].document_detail_contents.find(
              (v) =>
                v.d_d_attribute_id == 1140 || v.d_d_attribute_id == 708
            ).value
            : ""
          }}
        </template>
        <template v-slot:item.model="{ item }">
          {{
            item.document_details[0] &&
            item.document_details[0].document_detail_contents.find(
              (v) =>
                v.d_d_attribute_id == 1141 || v.d_d_attribute_id == 709
            )
            ? item.document_details[0].document_detail_contents.find(
              (v) =>
                v.d_d_attribute_id == 1141 || v.d_d_attribute_id == 709
            ).value
            : ""
          }}
        </template>
        <template v-slot:item.due_date="{ item }">
          {{
            item.document_details[0] &&
            item.document_details[0].document_detail_contents.find(
              (v) =>
                v.d_d_attribute_id == 1131 || v.d_d_attribute_id == 698 || v.d_d_attribute_id == 1038 || v.d_d_attribute_id ==
                672
            )
            ? item.document_details[0].document_detail_contents.find(
              (v) =>
                v.d_d_attribute_id == 1131 || v.d_d_attribute_id == 698 || v.d_d_attribute_id == 1038 || v.d_d_attribute_id ==
                672
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
                    <p class="text-left font-weight-black my-1 pl-6" v-html="document_detail.content">
                    </p>
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
                            <td class="text-left px-2" width="50%">{{ item.attribute_value }}</td>
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
    </v-card>
    <v-dialog v-model="loading" width="300" hide-overlay>
      <v-card color="primary" dark>
        <v-card-text>
          {{ $t("loadingText") }}
          <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
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
      search: "",
      dialog: false,
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
            document_detail_attributes: {}
          }
        ]
      },
      document_templates: [],
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
        document_number: "",
        created_by: "",
        korrespondent: "",
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
        // { text: "", value: "data-table-expand", visible: true },
        // { text: "#", value: "id", align: "center", width: 30 },
        {
          text: this.$t("ID"),
          align: "center",
          width: 30,
          value: "id",
          sortable: false,
          visible: true
        },
        {
          text: this.$t("Ma'lumot olingan sana"),
          value: "document_date",
          align: "center",
          width: 90,
          sortable: false,
          visible: this.$route.params.menu_item == "compliance_restr_one"
        },
        {
          text: this.$t("Manfaatlar to'qnashuvi mavjud bo'lgan xodimning F.I.Sh"),
          value: "employee",
          align: "center",
          width: 90,
          sortable: false,
          visible: this.$route.params.menu_item == "compliance_restr_one"
        },
        {
          text: this.$t("Tabel raqami"),
          value: "tabel",
          align: "center",
          width: 90,
          sortable: false,
          visible: this.$route.params.menu_item == "compliance_restr_one"
        },
        {
          text: this.$t("Nomzodning FISH"),
          value: "fish",
          align: "center",
          width: 90,
          sortable: false,
          visible: this.$route.params.menu_item == "compliance_restr_two"
        },
        {
          text: this.$t("Jamiyat tarkibiy tuzilmasi va xodimining lavozimi"),
          value: "staff",
          // align: "center",
          width: 350,
          sortable: false,
          visible: this.$route.params.menu_item == "compliance_restr_one"
        },
        {
          text: this.$t("Manfaatlar to'qnashuvining predmeti"),
          value: "predmet",
          // align: "center",
          // width: 350,
          sortable: false,
          visible: this.$route.params.menu_item == "compliance_restr_one"
        },
        {
          text: this.$t("Manfaatlar to'qnashuvining turi (haqiqiy yoki potentsial)"),
          value: "potentsial",
          // align: "center",
          // width: 350,
          sortable: false,
          visible: this.$route.params.menu_item == "compliance_restr_one"
        },
        {
          text: this.$t("Manfaatlar to'qnashuvini tartibga solish bo'yicha choralar"),
          value: "choralar",
          // align: "center",
          // width: 350,
          sortable: false,
          visible: this.$route.params.menu_item == "compliance_restr_one"
        },
        {
          text: this.$t("Odob-ahloq komissiya yig'ilish bayonnomasi raqami va sanasi, adrlar bo'limi tomonidan qabul kilingan qaror rekvizitlari"),
          value: "rekvizitlar",
          // align: "center",
          // width: 350,
          sortable: false,
          visible: this.$route.params.menu_item == "compliance_restr_one"
        },
        {
          text: this.$t("Belgilangan choralarning amalga oshirilishi ustidan nazorat qilish bo'yicha mas'ul shaxs"),
          value: "masul_shaxs",
          // align: "center",
          // width: 350,
          sortable: false,
          visible: this.$route.params.menu_item == "compliance_restr_one"
        },
        {
          text: this.$t("Bo'sh lavozim nomi"),
          value: "shtat",
          align: "center",
          // width: 350,
          sortable: false,
          visible: this.$route.params.menu_item == "compliance_restr_two"
        },
        {
          text: this.$t("Jamiyat yoki uning filiallari va ofislarining bo'limlari nomi"),
          value: "company",
          align: "center",
          width: 250,
          sortable: false,
          visible: this.$route.params.menu_item == "compliance_restr_two"
        },
        {
          text: this.$t("Tekshiruv ijrochisi, Fish"),
          value: "employee",
          align: "center",
          width: 90,
          sortable: false,
          visible: this.$route.params.menu_item == "compliance_restr_two"
        },
        {
          text: this.$t("Nomzodni tekshirish bo'yicha hisobot sanasi"),
          value: "document_date",
          align: "center",
          width: 150,
          sortable: false,
          visible: this.$route.params.menu_item == "compliance_restr_two"
        },
        {
          text: this.$t("Nomzodni tekshirish bo'yicha hisobot raqami"),
          value: "document_number",
          align: "center",
          width: 150,
          sortable: false,
          visible: true,
          // visible: this.$route.params.menu_item == "compliance_restr_two"
        },
        {
          text: this.$t("Tekshiruv natijasi (musbat / manfiy / aniqlangan xatarlar)"),
          value: "dangers",
          width: 150,
          align: "center",
          sortable: false,
          visible: this.$route.params.menu_item == "compliance_restr_two"
        },
        {
          text: this.$t("Tekshiruv natijasini asoslovchi qisqa izohlar"),
          value: "note",
          // width: 150,
          align: "center",
          sortable: false,
          visible: this.$route.params.menu_item == "compliance_restr_two"
        },
        {
          text: this.$t("QrCode"),
          value: "qrcode",
          // width: 150,
          align: "center",
          sortable: false,
          visible: this.$route.params.menu_item == "compliance_restr_one"
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
    }
  },
  methods: {
    uploadExcel(table, name) {
      TableToExcel.convert(document.getElementById("table"));
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
        .post(this.$store.state.backend_url + "api/documents/filter", {
          pagination: this.dataTableOptions,
          language: this.$i18n.locale,
          filter: this.filter
        })
        .then(response => {
          // this.items = response.data.documents.data;
          if (response.data.documents.data) {
            let doc_ids = response.data.documents.data.map((v) => v.id);
            // console.log(doc_ids);
            axios
              .post(this.$store.state.backend_url + "api/complaens/qrcode", {
                doc_ids: doc_ids,
              })
              .then(resp => {
                this.items = response.data.documents.data;
                let array_qrcode = resp.data;
                this.items = response.data.documents.data.map((v) =>{
                  v.qrCode = array_qrcode.find((s) => s.id == v.id);
                  return v;
                });
                console.log(array_qrcode);
                console.log(this.items);
              })
              .catch(error => {
                console.log(error);
                this.loading = false;
              });
          }
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
    getBarn() {
      axios
        .get(this.$store.state.backend_url + "api/barn")
        .then(res => {
          // console.log(res.data);
          this.barn = res.data;
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
  }
};
</script>
<style scoped>
.v-item--active {
  background-color: #fff !important;
}

.dense {
  padding: 0px;
  height: 10px !important;
}

.dense .v-text-field__details {
  display: none !important;
}

.dense .v-text-field {
  padding: 0px;
  margin: 0px;
}

.dense div div div {
  margin-bottom: 0px !important;
}
</style>
