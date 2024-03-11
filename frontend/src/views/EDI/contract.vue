<template>
  <div class="fullHeight">
    <v-card 
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{ $t("edi.contract_header") }}</span>
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
        <v-toolbar elevation="0" dense max-width="400">
          <v-menu
            v-model="menu4"
            :close-on-content-click="false"
            :nudge-right="40"
            transition="scale-transition"
            offset-y
            min-width="auto"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-text-field
                v-model="filterForm.from_date"
                class="mr-1"
                :label="$t('edi.from_date')"
                readonly
                outlined
                dense
                hide-details
                v-bind="attrs"
                v-on="on"
              ></v-text-field>
            </template>
            <v-date-picker
              v-model="filterForm.from_date"
              @input="menu4 = false"
            ></v-date-picker>
          </v-menu>
          <v-menu
            v-model="menu5"
            :close-on-content-click="false"
            :nudge-right="40"
            transition="scale-transition"
            offset-y
            min-width="auto"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-text-field
                v-model="filterForm.to_date"
                class="mr-1"
                :label="$t('edi.to_date')"
                readonly
                outlined
                dense
                hide-details
                v-bind="attrs"
                v-on="on"
              ></v-text-field>
            </template>
            <v-date-picker
              v-model="filterForm.to_date"
              @input="menu5 = false"
            ></v-date-picker>
          </v-menu>
          <v-btn
            color="#6ac82d"
            x-small
            outlined
            fab
            @click="getList"
            class="mr-4"
          >
            <v-icon>mdi-magnify</v-icon>
          </v-btn>
          <v-btn
            color="#6ac82d"
            x-small
            dark
            fab
            @click="newItem"
            class="mx-2"
            v-if="$store.getters.checkPermission('edi-contract')"
          >
            <v-icon>mdi-plus</v-icon>
          </v-btn>
        </v-toolbar>
      </v-card-title>
      <v-row class="mx-0">
        <v-col class="ma-0 pa-0" xs="12">
          <v-data-table
            class="doc-template_data-table"
            dense
            style="width: 100%; height: 100%; border-radius: 10px"
            fixed-header
            :height="screenHeight"
            :loading="loading"
            :headers="headers"
            :items="items"
            :expanded.sync="expanded"
            show-expand
            single-expand
            :search="search"
            :loading-text="$t('loadingText')"
            :no-data-text="$t('noDataText')"
            item-key="id"
            :server-items-length="server_items_length"
            :options.sync="dataTableOptions"
            :disable-pagination="true"
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
            <template v-slot:body.prepend>
              <tr class="py-0 my-0 filter-input prepend_height">
                <td class="pa-1"></td>
                <td class="pa-1"></td>
                <td class="pa-1">
                  <input
                    v-model="filterForm.contract_number"
                    @keyup.enter="getList()"
                  />
                </td>
                <td class="pa-1">
                  <input
                    v-model="filterForm.contract_date"
                    @keyup.enter="getList()"
                  />
                </td>
                <td class="pa-1">
                  <input v-model="filterForm.title" @keyup.enter="getList()" />
                </td>
                <td class="pa-1">
                  <select v-model="filterForm.bp_id" @change="getList()">
                    <option value>Select</option>
                    <option v-for="bp in businessPartners" :value="bp.value">
                      <div>{{ bp.text }}</div>
                    </option>
                  </select>
                </td>
                <td class="pa-1">
                  <input
                    v-model="filterForm.active_from"
                    @keyup.enter="getList()"
                  />
                </td>
                <td class="pa-1">
                  <input v-model="filterForm.active_to" @keyup.enter="getList()" />
                </td>
                <td class="pa-1">
                  <select v-model="filterForm.status" @change="getList()">
                    <option value>Select</option>
                    <option value="1">
                      <div>{{ $t("edi.status_active") }}</div>
                    </option>
                    <option value="0">
                      <div>{{ $t("edi.status_inactive") }}</div>
                    </option>
                  </select>
                </td>
                <td class="pa-1">
                  <input
                    v-model="filterForm.total_amount"
                    @keyup.enter="getList()"
                  />
                </td>
                <td class="pa-1">
                  <input
                    v-model="filterForm.total_price"
                    @keyup.enter="getList()"
                  />
                </td>
                <td class="pa-1">
                  <select v-model="filterForm.currency_id" @change="getList()">
                    <option value>Select</option>
                    <option v-for="curr in currencies" :value="curr.value">
                      <div>{{ curr.text }}</div>
                    </option>
                  </select>
                </td>
                <td class="pa-1"></td>
                <!-- <td class="pa-1"></td> -->
              </tr>
            </template>

            <template v-slot:expanded-item="{ headers, item }">
              <td :colspan="headers.length" class="pa-3">
                <v-card class="my-2">
                  <div>
                    <v-system-bar window color="#eee">
                      <b>{{ $t("signerGroup.staff_list") }}</b>
                      <v-spacer></v-spacer>
                    </v-system-bar>
                  </div>
                  <v-container fluid class="pa-1">
                    <table 
                      class="doc-template_data-table ma-0 pa-0"
                      style="width: 100%"
                    >
                      <tr>
                        <th class="text-right">{{ $t("edi.created_at") }}:</th>
                        <td class="text-left">
                          {{ item.created_at }}
                        </td>
                        <th class="text-right">{{ $t("edi.created_by") }}:</th>
                        <td class="text-left">
                          {{
                            item.created_by && item.created_by.employee
                              ? item.created_by.employee.fio
                              : ""
                          }}
                        </td>
                      </tr>
                      <tr>
                        <th class="text-right">{{ $t("edi.updated_at") }}:</th>
                        <td class="text-left">
                          {{ item.updated_at }}
                        </td>
                        <th class="text-right">{{ $t("edi.updated_by") }}:</th>
                        <td class="text-left">
                          {{
                            item.updated_by && item.updated_by.employee
                              ? item.updated_by.employee.fio
                              : ""
                          }}
                        </td>
                      </tr>
                    </table>
                  </v-container>
                </v-card>
                <v-card class="my-2">
                  <div>
                    <v-system-bar window color="#eee">
                      <b>{{ $t("edi.contract_contract_detail_id") }}</b>
                      <v-spacer></v-spacer>
                      <v-icon color="success" medium @click="newDetailItem(item)" v-if="user().id == item.created_by.id"
                        >mdi-plus</v-icon
                      >
                    </v-system-bar>
                  </div>
                  <v-container fluid class="pa-1">
                    <table
                        class="doc-template_data-table ma-0 pa-0"
                        style="width: 100%"
                      >
                        <tr>
                          <th class="text-center">
                            {{ $t("edi.contract_detail_position") }}
                          </th>
                          <th class="text-center">
                            {{ $t("edi.contract_detail_material_id") }}
                          </th>
                          <th class="text-center">
                            {{ $t("edi.contract_detail_quantity") }}
                          </th>
                          <th class="text-center">
                            {{ $t("edi.contract_detail_price") }}
                          </th>
                          <th class="text-center">
                            {{ $t("edi.contract_detail_target_warehouse_id") }}
                          </th>
                          <th class="text-center">
                            {{ $t("edi.contract_detail_tranzit_time") }}
                          </th>
                          <th class="text-center">
                            {{ $t("edi.contract_detail_frozen_period") }}
                          </th>
                          <th class="text-center">
                            {{ $t("edi.contract_detail_forecast_period") }}
                          </th>
                          <th class="text-center">
                            {{ $t("edi.contract_detail_net_weight") }}
                          </th>
                          <th class="text-center">
                            {{ $t("edi.contract_detail_brutto_weight") }}
                          </th>
                          <th class="text-center">
                            {{ $t("edi.contract_detail_moq") }}
                          </th>
                          <th class="text-center">
                            {{ $t("edi.contract_detail_ruq") }}
                          </th>
                          <th class="text-center">
                            {{ $t("edi.contract_detail_status") }}
                          </th>
                          <th class="text-center">
                            {{ $t("edi.contract_detail_actions") }}
                          </th>
                        </tr>
                        <tr v-for="(i, k) in item.contract_details" :key="k">
                          <td class="text-right">
                            {{ i.position }}
                          </td>
                          <td class="text-left">
                            {{ i.material.material_number }}
                          </td>
                          <td class="text-right">{{ i.quantity }}</td>
                          <td class="text-right">{{ i.price }}</td>
                          <td class="text-left">
                            {{ i.target_warehouse.warehouse_number }}
                          </td>
                          <td class="text-right">{{ i.tranzit_time }}</td>
                          <td class="text-right">{{ i.frozen_period }}</td>
                          <td class="text-right">{{ i.forecast_period }}</td>
                          <td class="text-right">{{ i.net_weight }}</td>
                          <td class="text-right">{{ i.brutto_weight }}</td>

                          <td class="text-right">{{ i.moq }}</td>
                          <td class="text-right">{{ i.ruq }}</td>
                          <td class="text-right">
                            <v-switch
                              v-model="i.status"
                              readonly
                              hide-details
                              class="mt-0"
                            ></v-switch>
                          </td>
                          <td class="text-right">
                            <v-btn
                              class="px-0"
                              color="#3FCB5D"
                              style="min-width: 25px"
                              x-small
                              text
                              @click="editDetailItem(i)"
                              v-if="
                                $store.getters.checkPermission('edi-admin') ||
                                user().id == item.created_by.id
                              "
                            >
                              <v-icon size="18">mdi-pencil</v-icon>
                            </v-btn>
                            <v-btn
                              class="px-1"
                              color="error"
                              style="min-width: 25px"
                              small
                              text
                              @click="deleteDetailItem(i)"
                              v-if="
                                $store.getters.checkPermission('edi-admin') ||
                                user().id == item.created_by.id
                              "
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
            <template v-slot:item.id="{ item }">{{
              items.indexOf(item) + from
            }}</template>
            <template v-slot:item.status="{ item }">
              <v-switch
                v-model="item.status"
                readonly
                hide-details="auto"
                class="mt-0 ml-2"
              ></v-switch>
            </template>
            <template v-slot:item.description="{ item }">
              <span style="white-space: normal; max-width: 100px">{{
                item.description
              }}</span>
            </template>
            <template v-slot:item.total_amount="{ item }">
              <div class="text-right">{{ item.total_amount }}</div>
            </template>
            <template v-slot:item.total_price="{ item }">
              <div class="text-right">{{ item.total_price }}</div>
            </template>
            <template v-slot:item.actions="{ item }">
              <v-btn
                class="px-0"
                color="#3FCB5D"
                style="min-width: 25px"
                x-small
                text
                @click="editItem(item)"
                v-if="
                  $store.getters.checkPermission('edi-admin') ||
                  user().id == item.created_by.id
                "
              >
                <v-icon size="18">mdi-pencil</v-icon>
              </v-btn>
              <v-btn
                class="pl-0 pr-2"
                color="error"
                style="min-width: 25px"
                small
                text
                @click="deleteItem(item)"
                v-if="
                  $store.getters.checkPermission('edi-admin') ||
                  user().id == item.created_by.id
                "
              >
                <v-icon size="18">mdi-trash-can-outline</v-icon>
              </v-btn>
            </template>
          </v-data-table>
        </v-col>
      </v-row>
    </v-card>
    <v-dialog
      v-model="dialog"
      @keydown.esc="dialog = false"
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
            <table style="width: 100%">
              <tr>
                <td class="text-right pa-1">
                  <label for>{{ $t("edi.contracts_contract_number") }}</label>
                </td>
                <td class="pa-1">
                  <v-text-field
                    v-model="form.contract_number"
                    :rules="[(v) => !!v || $t('input_required')]"
                    hide-details="auto"
                    dense
                    outlined
                  ></v-text-field>
                </td>
              </tr>
              <tr>
                <td class="text-right pa-1">
                  <label for>{{
                    $t("edi.contracts_contract_number_date")
                  }}</label>
                </td>
                <td class="pa-1">
                  <v-menu
                    v-model="menu3"
                    :close-on-content-click="false"
                    :nudge-right="40"
                    transition="scale-transition"
                    offset-y
                    min-width="auto"
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field
                        v-model="form.contract_date"
                        readonly
                        outlined
                        dense
                        hide-details
                        v-bind="attrs"
                        v-on="on"
                      ></v-text-field>
                    </template>
                    <v-date-picker
                      v-model="form.contract_date"
                      @input="menu3 = false"
                    ></v-date-picker>
                  </v-menu>
                </td>
              </tr>
              <tr>
                <td class="text-right pa-1">
                  <label for>{{ $t("edi.contracts_title") }}</label>
                </td>
                <td class="pa-1">
                  <v-text-field
                    v-model="form.title"
                    :rules="[(v) => !!v || $t('input_required')]"
                    hide-details="auto"
                    dense
                    outlined
                  ></v-text-field>
                </td>
              </tr>
              <tr>
                <td class="text-right pa-1">
                  <label for>{{ $t("edi.contracts_bp_id") }}</label>
                </td>
                <td class="pa-1">
                  <v-autocomplete
                    v-model="form.bp_id"
                    :items="businessPartners"
                    dense
                    outlined
                    hide-details
                  ></v-autocomplete>
                </td>
              </tr>
              <tr>
                <td class="text-right pa-1">
                  <label for>{{ $t("edi.contracts_active_from") }}</label>
                </td>
                <td class="pa-1">
                  <v-menu
                    v-model="menu1"
                    :close-on-content-click="false"
                    :nudge-right="40"
                    transition="scale-transition"
                    offset-y
                    min-width="auto"
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field
                        v-model="form.active_from"
                        readonly
                        outlined
                        dense
                        hide-details
                        v-bind="attrs"
                        v-on="on"
                      ></v-text-field>
                    </template>
                    <v-date-picker
                      v-model="form.active_from"
                      @input="menu1 = false"
                    ></v-date-picker>
                  </v-menu>
                </td>
              </tr>
              <tr>
                <td class="text-right pa-1">
                  <label for>{{ $t("edi.contracts_active_to") }}</label>
                </td>
                <td class="pa-1">
                  <v-menu
                    v-model="menu2"
                    :close-on-content-click="false"
                    :nudge-right="40"
                    transition="scale-transition"
                    offset-y
                    min-width="auto"
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field
                        v-model="form.active_to"
                        readonly
                        outlined
                        dense
                        hide-details
                        v-bind="attrs"
                        v-on="on"
                      ></v-text-field>
                    </template>
                    <v-date-picker
                      v-model="form.active_to"
                      @input="menu2 = false"
                    ></v-date-picker>
                  </v-menu>
                </td>
              </tr>
              <tr>
                <td class="text-right pa-1">
                  <label for>{{ $t("edi.contracts_currency_id") }}</label>
                </td>
                <td class="pa-1">
                  <v-autocomplete
                    v-model="form.currency_id"
                    :items="currencies"
                    hide-details
                    dense
                    outlined
                  ></v-autocomplete>
                </td>
              </tr>
              <tr>
                <td class="text-right pa-1">
                  <label for>{{ $t("edi.contracts_total_amount") }}</label>
                </td>
                <td class="pa-1">
                  <v-text-field
                    v-model="form.total_amount"
                    :rules="[(v) => !!v || $t('input_required')]"
                    hide-details="auto"
                    dense
                    outlined
                  ></v-text-field>
                </td>
              </tr>
            </table>
          </v-form>
          <!-- <small color="red">{{ $t("input_required") }}</small> -->
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="save">{{ $t("save") }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="detailDialog"
      @keydown.esc="detailDialog = false"
      persistent
      max-width="1000px"
    >
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">{{ dialogDetailHeaderText }}</span>
          <v-spacer></v-spacer>
          <v-btn
            color="red"
            outlined
            x-small
            fab
            class
            @click="detailDialog = false"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form @keyup.native.enter="saveDetail" ref="dialogDetailForm">
            <table style="width: 100%" class="dialog-table">
              <tr>
                <td class="text-right pa-1 pt-3">
                  <label for
                    >{{ $t("edi.contracts_details_material_id")
                    }}<span class="required">*</span></label
                  >
                </td>
                <td class="pa-1">
                  <v-autocomplete
                    v-model="detailForm.material_id"
                    :items="materials"
                    :rules="[(v) => !!v || $t('input_required')]"
                    dense
                    outlined
                    hide-details="auto"
                  ></v-autocomplete>
                </td>
                <td class="text-right pa-1 pt-3">
                  <label for class="pt-3"
                    >{{ $t("edi.contracts_details_quantity")
                    }}<span class="required">*</span></label
                  >
                </td>
                <td class="pa-1">
                  <v-text-field
                    v-model="detailForm.quantity"
                    :rules="[(v) => !!v || $t('input_required')]"
                    type="number"
                    step="1"
                    hide-details="auto"
                    dense
                    outlined
                  ></v-text-field>
                </td>
              </tr>
              <tr>
                <td class="text-right pa-1 pt-3">
                  <label for
                    >{{ $t("edi.contracts_details_price")
                    }}<span class="required">*</span></label
                  >
                </td>
                <td class="pa-1">
                  <v-text-field
                    v-model="detailForm.price"
                    :rules="[(v) => !!v || $t('input_required')]"
                    type="number"
                    step="0.01"
                    hide-details="auto"
                    dense
                    outlined
                  ></v-text-field>
                </td>
                <td class="text-right pa-1 pt-3">
                  <label for
                    >{{ $t("edi.contracts_details_target_warehouse_id")
                    }}<span class="required">*</span></label
                  >
                </td>
                <td class="pa-1">
                  <v-autocomplete
                    v-model="detailForm.target_warehouse_id"
                    :rules="[(v) => !!v || $t('input_required')]"
                    :items="warehouses"
                    dense
                    outlined
                    hide-details
                  ></v-autocomplete>
                </td>
              </tr>
              <tr>
                <td class="text-right pa-1 pt-3">
                  <label for
                    >{{ $t("edi.contracts_details_tranzit_time")
                    }}<span class="required">*</span></label
                  >
                </td>
                <td class="pa-1">
                  <v-text-field
                    v-model="detailForm.tranzit_time"
                    :rules="[(v) => !!v || $t('input_required')]"
                    type="number"
                    step="1"
                    hide-details="auto"
                    dense
                    outlined
                  ></v-text-field>
                </td>
                <td class="text-right pa-1 pt-3">
                  <label for>{{
                    $t("edi.contract_detail_frozen_period")
                  }}</label>
                </td>
                <td class="pa-1">
                  <v-text-field
                    v-model="detailForm.frozen_period"
                    type="number"
                    step="1"
                    hide-details="auto"
                    dense
                    outlined
                  ></v-text-field>
                </td>
              </tr>
              <tr>
                <td class="text-right pa-1 pt-3">
                  <label for>{{
                    $t("edi.contracts_details_forecast_period")
                  }}</label>
                </td>
                <td class="pa-1">
                  <v-text-field
                    v-model="detailForm.forecast_period"
                    type="number"
                    step="1"
                    hide-details="auto"
                    dense
                    outlined
                  ></v-text-field>
                </td>
                <td class="text-right pa-1 pt-3">
                  <label for>{{
                    $t("edi.contracts_details_net_weight")
                  }}</label>
                </td>
                <td class="pa-1">
                  <v-text-field
                    v-model="detailForm.net_weight"
                    type="number"
                    step="1"
                    hide-details="auto"
                    dense
                    outlined
                  ></v-text-field>
                </td>
              </tr>
              <tr>
                <td class="text-right pa-1 pt-3">
                  <label for>{{
                    $t("edi.contracts_details_brutto_weight")
                  }}</label>
                </td>
                <td class="pa-1">
                  <v-text-field
                    v-model="detailForm.brutto_weight"
                    type="number"
                    step="1"
                    hide-details="auto"
                    dense
                    outlined
                  ></v-text-field>
                </td>
                <td class="text-right pa-1 pt-3">
                  <label for>{{ $t("edi.contracts_details_moq") }}</label>
                </td>
                <td class="pa-1">
                  <v-text-field
                    v-model="detailForm.moq"
                    type="number"
                    step="1"
                    hide-details="auto"
                    dense
                    outlined
                  ></v-text-field>
                </td>
              </tr>
              <tr>
                <td class="text-right pa-1 pt-3">
                  <label for>{{ $t("edi.contracts_details_ruq") }}</label>
                </td>
                <td class="pa-1">
                  <v-text-field
                    v-model="detailForm.ruq"
                    type="number"
                    step="1"
                    hide-details="auto"
                    dense
                    outlined
                  ></v-text-field>
                </td>
                <td class="text-right pa-1 pt-3">
                  <label for v-if="detailForm.created_at">{{
                    $t("edi.contracts_details_status")
                  }}</label>
                </td>
                <td class="pa-1">
                  <v-switch
                    v-if="detailForm.created_at"
                    v-model="detailForm.status"
                    hide-details="auto"
                    class="mt-0 ml-2"
                  ></v-switch>
                </td>
              </tr>
            </table>
          </v-form>
          <!-- <small color="red">{{ $t("input_required") }}</small> -->
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="saveDetail">{{ $t("save") }}</v-btn>
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
  </div>
</template>
<script>
const axios = require("axios").default;
import Swal from "sweetalert2";
export default {
  data() {
    return {
      loading: false,
      search: "",
      dialog: true,
      dialogHeaderText: "",
      dialogDetailHeaderText: "",
      detailDialog: false,
      items: [],
      materials: [],
      warehouses: [],
      expanded: [],
      form: {},
      detailForm: {},
      createdAtMenu2: false,
      dataTableOptions: { page: 1, itemsPerPage: 20 },
      page: 1,
      from: 0,
      server_items_length: -1,
      businessPartners: [],
      currencies: [],
      selectedDropDownValue: "",
      selectedDropDownValue2: "",
      menu1: false,
      menu2: false,
      menu3: false,
      menu4: false,
      menu5: false,
      form: {
        active_from: null,
        active_to: null,
      },
      filterForm: {
        contract_number: null,
        contract_date: null,
        title: null,
        bp_id: null,
        active_from: null,
        active_to: null,
        currency_id: null,
        total_amount: null,
        total_price: null,
        from_date: null,
        to_date: null,
        status: null,
      },
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    screenWidth() {
      return window.innerWidth - 365;
    },
    headers() {
      return [
        { text: "#", value: "id", align: "center", width: 30 },
        {
          text: this.$t("edi.contracts_contract_number"),
          value: "contract_number",
          width: 100,
          align: "center",
          sortable: false,
        },
        {
          text: this.$t("edi.contracts_contract_number_date"),
          value: "contract_date",
          align: "center",
          sortable: false,
        },
        {
          text: this.$t("edi.contracts_title"),
          value: "title",
          align: "center",
          sortable: false,
        },
        {
          text: this.$t("edi.contracts_bp_id"),
          value: "business_partner.name",
          align: "center",
          sortable: false,
        },
        {
          text: this.$t("edi.contracts_active_from"),
          value: "active_from",
          width: 100,
          align: "center",
          sortable: false,
        },
        {
          text: this.$t("edi.contracts_active_to"),
          value: "active_to",
          width: 100,
          align: "center",
          sortable: false,
        },
        {
          text: this.$t("edi.contract_status"),
          value: "status",
          width: 85,
          align: "center",
          sortable: false,
        },
        {
          text: this.$t("edi.contracts_total_amount"),
          value: "total_amount",
          width: 100,
          align: "center",
          sortable: false,
        },
        {
          text: this.$t("edi.contracts_total_price"),
          value: "total_price",
          width: 140,
          align: "center",
          sortable: false,
        },
        {
          text: this.$t("edi.contracts_currency_id"),
          value: "currency.name",
          width: 140,
          align: "center",
          sortable: false,
        },
        {
          text: this.$t("edi.contracts_actions"),
          value: "actions",
          width: 120,
          align: "center",
          sortable: false,
        },
      ];
    },
  },
  methods: {
    user() {
      try {
        return JSON.parse(localStorage.getItem("user"));
      } catch (error) {
        return {};
      }
    },
    positionChange(item, type) {
      console.log(type);
    },
    updatePage($event) {
      this.getList();
    },
    updatePerPage($event) {
      this.getList();
    },
    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/edi/contracts", {
          filter: this.filterForm,
          pagination: this.dataTableOptions,
          search: this.search,
        })
        .then((response) => {
          // console.log(response);
          this.items = response.data.data;
          this.server_items_length = response.data.total;
          this.from = response.data.from;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getRef() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/edi/contracts/get-ref")
        .then((response) => {
          this.businessPartners = response.data.businessPartners;
          this.currencies = response.data.currencies;
          this.materials = response.data.materials;
          this.warehouses = response.data.warehouses;
          this.loading = false;
        })

        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },

    newItem() {
      this.dialogHeaderText = this.$t("edi.contracts_contract_create");
      this.form = {
        id: Date.now(),
        contract_number: null,
        contract_date: null,
        title: null,
        bp_id: null,
        active_from: null,
        active_to: null,
        currency_id: null,
        total_amount: null,
      };
      this.dialog = true;
      if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
    },
    editItem(item) {
      this.dialogHeaderText = this.$t("edi.contracts_contract_edit");
      this.formIndex = this.items.indexOf(item);
      this.form = Object.assign({}, item);
      this.dialog = true;
      if (this.$refs.dialogForm) this.$refs.dialogForm.resetValidation();
    },
    newDetailItem(item) {
      this.dialogDetailHeaderText = this.$t("edi.contract_details_create");
      this.detailForm = {
        id: Date.now(),
        contract_id: item.id,
        position: null,
        material_id: null,
        status: true,
        tranzit_time: null,
        frozen_period: null,
        forecast_period: null,
        net_weight: null,
        brutto_weight: null,
        target_warehouse_id: null,
        moq: null,
        ruq: null,
      };
      this.detailDialog = true;
      if (this.$refs.dialogDetailForm) this.$refs.dialogDetailForm.reset();
    },
    editDetailItem(item) {
      this.dialogDetailHeaderText = this.$t("edi.contract_details_edit");
      this.detailForm = Object.assign({}, item);
      this.detailDialog = true;
      if (this.$refs.dialogDetailForm)
        this.$refs.dialogDetailForm.resetValidation();
    },
    save() {
      if (this.$refs.dialogForm.validate())
        axios
          .post(
            this.$store.state.backend_url + "api/edi/contracts/update",
            this.form
          )
          .then((res) => {
            if (res.data.status == 200) {
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
            } else {
              Swal.fire({
                icon: "error",
                title: "Xatolik",
                text: this.$t(res.data.message),
              });
            }
          })
          .catch((err) => {
            console.log(err);
          });
    },
    saveDetail() {
      if (this.$refs.dialogDetailForm.validate())
        axios
          .post(
            this.$store.state.backend_url + "api/edi/contracts/update-detail",
            this.detailForm
          )
          .then((res) => {
            if (res.data.status == 200) {
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
                title: this.$t(res.data.message),
              });
            } else if (res.data.status == 200) {
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
                icon: "error",
                title: this.$t(res.data.message),
              });
            }
            this.detailDialog = false;
          })
          .catch((err) => {
            console.log(err);
          });
    },
    deleteDetailItem(item) {
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
              this.$store.state.backend_url +
                "api/edi/contracts/delete-detail/" +
                item.id
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
              this.$store.state.backend_url +
                "api/edi/contracts/delete/" +
                item.id
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
  },
  mounted() {
    this.getRef();
    this.getList();
  },
  created() {},
};
</script>
<style scoped>
table > tr > td,
table > tr > th {
  white-space: normal;
  max-width: 200px;
}

input,
select {
  border: 1px solid #aaa;
  padding: 3px !important;
  width: 100%;
}

input:focus,
select:focus {
  background-color: #fff;
}
.required {
  color: red;
  padding-left: 5px;
}
.dialog-table tr td {
  vertical-align: top;
}
</style>
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