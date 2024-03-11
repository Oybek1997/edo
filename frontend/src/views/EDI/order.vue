<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ $t("edi.order_header") }}</span>
        <v-spacer></v-spacer>
        <v-toolbar elevation="0" dense max-width="500">
          <v-menu
            v-model="menu4"
            :close-on-content-click="false"
            :nudge-right="40"
            transition="scale-transition"
            offset-y
            left
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
                clearable
              ></v-text-field>
            </template>
            <v-date-picker
              v-model="filterForm.from_date"
              @input="menu4 = false"
              no-title
              scrollable
            ></v-date-picker>
          </v-menu>
          <v-menu
            v-model="menu5"
            :close-on-content-click="false"
            :nudge-right="40"
            transition="scale-transition"
            offset-y
            left
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
                clearable
              ></v-text-field>
            </template>
            <v-date-picker
              v-model="filterForm.to_date"
              @input="menu5 = false"
              no-title
              scrollable
            ></v-date-picker>
          </v-menu>
          <v-spacer></v-spacer>

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
            v-if="$store.getters.checkPermission('edi-order')"
          >
            <v-icon>mdi-plus</v-icon>
          </v-btn>
        </v-toolbar>

        <!-- <v-btn color="#6ac82d" x-small dark fab @click="newItem">
          <v-icon>mdi-plus</v-icon>
        </v-btn> -->
      </v-card-title>
      <v-data-table
        dense
        fixed-header
        :loading-text="$t('loadingText')"
        :no-data-text="$t('noDataText')"
        :height="screenHeight"
        :loading="loading"
        :headers="headers"
        :items="items"
        :search="search"
        :expanded.sync="expanded"
        show-expand
        class="mainTable ma-1"
        style="border: 1px solid #aaa"
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
                v-model="filterForm.contract_id"
                @keyup.enter="getList()"
              />
            </td>
            <td class="pa-1">
              <input
                v-model="filterForm.order_number"
                @keyup.enter="getList()"
              />
            </td>
            <td class="pa-1">
              <input v-model="filterForm.created_at" @keyup.enter="getList()" />
            </td>
            <td class="pa-1">
              <input v-model="filterForm.title" @keyup.enter="getList()" />
            </td>
            <td class="pa-1">
              <input
                v-model="filterForm.order_quantity"
                @keyup.enter="getList()"
              />
            </td>
            <td class="pa-1">
              <input
                v-model="filterForm.shipment_quantity"
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
              <input
                v-model="filterForm.shipment_price"
                @keyup.enter="getList()"
              />
            </td>
            <td class="pa-1">
              <input
                v-model="filterForm.total_price"
                @keyup.enter="getList()"
              />
            </td>
            <td class="pa-1"></td>
          </tr>
        </template>
        <template v-slot:expanded-item="{ headers, item }">
          <td :colspan="headers.length">
            <table style="width: 50%" class="mt-4">
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
            <v-card class="ma-2" outlined dense>
              <v-card-text>
                <div
                  :style="'max-width:' + screenWidth + 'px;'"
                  class="overflow-x-auto overflow-y-hidden"
                >
                  <v-card-title primary-title>
                    {{ $t("edi.orders_order_detail_id") }}
                    <v-spacer></v-spacer>
                    <v-btn
                      color="success"
                      outlined
                      x-small
                      fab
                      @click="newDetailItem(item)"
                      v-if="user().id == item.created_by.id"
                      ><v-icon>mdi-plus</v-icon></v-btn
                    >
                  </v-card-title>

                  <table
                    style="
                      border-collapse: collapse;
                      border-color: #ddd;
                      width: 100%;
                    "
                    class="mb-2"
                    border="1"
                  >
                    <tr>
                      <th class="text-center" rowspan="2">#</th>
                      <th class="text-center" colspan="3">
                        {{ $t("edi.order_details_contract_detail_id") }}
                      </th>
                      <th class="text-center" rowspan="2">
                        {{ $t("edi.order_details_quantity") }}
                      </th>
                      <th class="text-center" rowspan="2">
                        {{ $t("edi.order_details_applicable") }}
                      </th>
                      <th class="text-center" rowspan="2">
                        {{ $t("edi.order_details_order_start_date") }}
                      </th>
                      <th class="text-center" rowspan="2">
                        {{ $t("edi.order_details_order_finish_date") }}
                      </th>
                      <th rowspan="2">{{ $t("actions") }}</th>
                    </tr>
                    <tr>
                      <th class="text-center">
                        {{ $t("edi.orders_material") }}
                      </th>
                      <th class="text-center">
                        {{ $t("edi.orders_material_name") }}
                      </th>
                      <th class="text-center">
                        {{ $t("edi.orders_material_price") }}
                      </th>
                    </tr>
                    <tr
                      v-for="(order_detail, k) in item.order_details"
                      :key="k"
                    >
                      <td class="text-right">{{ k + 1 }}</td>
                      <td class="text-right">
                        {{
                          order_detail.contract_detail
                            ? order_detail.contract_detail.material
                                .material_number
                            : ""
                        }}
                      </td>
                      <td class="text-right">
                        {{
                          order_detail.contract_detail
                            ? order_detail.contract_detail.material.description
                            : ""
                        }}
                      </td>
                      <td class="text-right">
                        {{
                          order_detail.contract_detail
                            ? order_detail.contract_detail.price
                            : 0
                        }}
                      </td>
                      <td class="text-right">
                        {{ order_detail.order_quantity }}
                      </td>
                      <td class="text-right">
                        {{
                          item.asn_details.filter(
                            (v) =>
                              v.contract_detail_id ==
                              order_detail.contract_detail_id
                          ).length
                            ? item.asn_details.filter(
                                (v) =>
                                  v.contract_detail_id ==
                                  order_detail.contract_detail_id
                              )[0].shipment_quantity
                            : 0
                        }}
                      </td>
                      <td class="text-right">
                        {{ order_detail.order_start_date }}
                      </td>
                      <td class="text-right">
                        {{ order_detail.order_finish_date }}
                      </td>
                      <td class="text-right" style="width: 120px">
                        <v-btn
                          color="blue"
                          small
                          text
                          @click="editDetailItem(order_detail)"
                          v-if="
                            $store.getters.checkPermission('edi-admin') ||
                            user().id == item.created_by.id
                          "
                        >
                          <v-icon>mdi-pencil</v-icon>
                        </v-btn>
                        <v-btn
                          color="red"
                          small
                          text
                          @click="deleteDetailItem(order_detail)"
                          v-if="
                            $store.getters.checkPermission('edi-admin') ||
                            user().id == item.created_by.id
                          "
                        >
                          <v-icon>mdi-delete</v-icon>
                        </v-btn>
                      </td>
                    </tr>
                  </table>
                </div>
              </v-card-text>
            </v-card>
          </td>
        </template>
        <template v-slot:item.id="{ item }">{{
          items.indexOf(item) + from
        }}</template>
        <template v-slot:item.description="{ item }">
          <span style="white-space: normal; max-width: 100px">{{
            item.description
          }}</span>
        </template>
        <template v-slot:item.created_at="{ item }">
          <span style="white-space: normal; max-width: 100px">{{
            item.created_at.substr(0, 10)
          }}</span>
        </template>
        <template v-slot:item.title="{ item }">
          <div class="text-left">{{ item.title }}</div>
        </template>
        <template v-slot:item.contract="{ item }">
          <contract_view :data="item.contract"></contract_view>
        </template>
        <template v-slot:item.order_quantity="{ item }">
          <div class="text-right">{{ item.order_quantity }}</div>
        </template>
        <template v-slot:item.order_price="{ item }">
          <div class="text-right">{{ item.order_price }}</div>
        </template>
        <template v-slot:item.shipment_price="{ item }">
          <div class="text-right">{{ item.shipment_price }}</div>
        </template>
        <template v-slot:item.shipment_quantity="{ item }">
          <td
            :style="
              item.shipment_quantity > item.order_quantity
                ? 'background-color:#A00;color:#FFF;'
                : ''
            "
            :title="
              item.shipment_quantity > item.order_quantity
                ? 'Order quantity < Shipment Quantity'
                : ''
            "
          >
            <div class="text-right">{{ item.shipment_quantity }}</div>
          </td>
        </template>
        <template v-slot:item.total_price="{ item }">
          <div class="text-right">{{ item.total_price }}</div>
        </template>
        <template v-slot:item.actions="{ item }">
          <v-btn
            color="blue"
            small
            text
            @click="editItem(item)"
            v-if="
              $store.getters.checkPermission('edi-admin') ||
              user().id == item.created_by.id
            "
          >
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
          <v-btn
            color="red"
            small
            text
            @click="deleteItem(item)"
            v-if="
              $store.getters.checkPermission('edi-admin') ||
              user().id == item.created_by.id
            "
          >
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template>
      </v-data-table>
    </v-card>
    <v-dialog
      v-model="dialog"
      @keydown.esc="dialog = false"
      persistent
      max-width="600px"
    >
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">{{ dialogHeaderText }}</span>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="dialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form @keyup.native.enter="save" ref="dialogForm">
            <table style="width: 100%">
              <tr>
                <td class="pa-1 text-right">
                  <label for>{{ $t("edi.orders_title") }}</label>
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
                <td class="pa-1 text-right">
                  <label for>{{ $t("edi.orders_contract_id") }}</label>
                </td>
                <td class="pa-1">
                  <v-autocomplete
                    v-model="form.contract_id"
                    :items="contracts"
                    :rules="[(v) => !!v || $t('input_required')]"
                    hide-details="auto"
                    dense
                    outlined
                  ></v-autocomplete>
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
      max-width="600px"
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
            <table style="width: 100%">
              <tr>
                <td class="text-right pa-1">
                  <label for>{{
                    $t("edi.order_details_material_number")
                  }}</label>
                </td>
                <td class="pa-1">
                  <v-btn
                    color="primary"
                    block
                    outlined
                    @click="selectContractPosition"
                    >{{
                      detailForm.contract_detail
                        ? detailForm.contract_detail.material.material_number
                        : "Select"
                    }}</v-btn
                  >
                  <div
                    class="v-text-field__details ml-2 mt-1"
                    v-if="!detailForm.contract_detail_id"
                  >
                    <div
                      class="v-messages theme--light error--text"
                      role="alert"
                    >
                      <div class="v-messages__wrapper">
                        <div class="v-messages__message">
                          {{ $t("input_required") }}
                        </div>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
              <tr>
                <td class="text-right pa-1">
                  <label for>{{ $t("edi.order_details_material_name") }}</label>
                </td>
                <td class="pa-1">
                  <v-btn disabled block outlined>{{
                    detailForm.contract_detail
                      ? detailForm.contract_detail.material.description
                      : "Select"
                  }}</v-btn>
                </td>
              </tr>
              <tr>
                <td class="text-right pa-1">
                  <label for>{{
                    $t("edi.order_details_material_price")
                  }}</label>
                </td>
                <td class="pa-1">
                  <v-btn disabled block outlined>{{
                    detailForm.contract_detail
                      ? detailForm.contract_detail.price
                      : "Select"
                  }}</v-btn>
                </td>
              </tr>
              <tr>
                <td class="text-right pa-1">
                  <label for>{{ $t("edi.order_details_quantity") }}</label>
                </td>
                <td class="pa-1">
                  <v-text-field
                    v-model="detailForm.order_quantity"
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
                <td class="text-right pa-1">
                  <label for>{{
                    $t("edi.order_details_order_start_date")
                  }}</label>
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
                        v-model="detailForm.order_start_date"
                        readonly
                        outlined
                        dense
                        hide-details
                        v-bind="attrs"
                        v-on="on"
                      ></v-text-field>
                    </template>
                    <v-date-picker
                      v-model="detailForm.order_start_date"
                      @input="menu1 = false"
                    ></v-date-picker>
                  </v-menu>
                </td>
              </tr>
              <tr>
                <td class="text-right pa-1">
                  <label for>{{
                    $t("edi.order_details_order_finish_date")
                  }}</label>
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
                        v-model="detailForm.order_finish_date"
                        readonly
                        outlined
                        dense
                        hide-details
                        v-bind="attrs"
                        v-on="on"
                      ></v-text-field>
                    </template>
                    <v-date-picker
                      v-model="detailForm.order_finish_date"
                      @input="menu2 = false"
                    ></v-date-picker>
                  </v-menu>
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

    <v-dialog
      v-model="selectContractPositionDialog"
      @keydown.esc="selectContractPositionDialog = false"
      persistent
      max-width="1000px"
    >
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">{{
            $t("edi.select_contract_detail_id")
          }}</span>
          <v-spacer></v-spacer>
          <v-btn
            color="red"
            outlined
            x-small
            fab
            class
            @click="selectContractPositionDialog = false"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <table
            style="border-collapse: collapse; border-color: #ddd; width: 100%"
            border="1"
            class="my-4"
          >
            <tr>
              <th class="text-center">
                {{ $t("edi.contract_detail_position") }}
              </th>
              <th class="text-center">
                {{ $t("edi.contract_detail_material_id") }}
              </th>
              <th class="text-center">
                {{ $t("edi.contract_detail_material_description") }}
              </th>
              <th class="text-center">
                {{ $t("edi.contract_detail_quantity") }}
              </th>
              <th class="text-center">
                {{ $t("edi.contract_detail_price") }}
              </th>
              <th class="text-center">
                {{ this.$t("actions") }}
              </th>
            </tr>
            <tr
              class="hoverable_tr"
              v-for="(i, k) in detailForm &&
              items.find((v) => v.id == detailForm.order_id)
                ? items
                    .find((v) => v.id == detailForm.order_id)
                    .contract.contract_details.filter((v) => v.status == 1)
                : []"
              :key="k"
            >
              <td class="text-right pa-1">
                {{ i.position }}
              </td>
              <td class="text-left pa-1">
                {{ i.material.material_number }}
              </td>
              <td class="text-left pa-1">
                {{ i.material.description }}
              </td>
              <td class="text-right pa-1">{{ i.quantity }}</td>
              <td class="text-right pa-1">{{ i.price }}</td>
              <td class="text-center pa-1">
                <v-btn
                  color="primary"
                  outlined
                  x-small
                  @click="selectContractDetail(i)"
                  >{{ $t("select") }}</v-btn
                >
              </td>
            </tr>
          </table>
        </v-card-text>
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
import contractView from "@/views/EDI/contractView";
export default {
  components: {
    contract_view: contractView,
  },
  data() {
    return {
      selectedContract: null,
      loading: false,
      search: "",
      dialog: false,
      dialogHeaderText: "",
      dialogDetailHeaderText: "",
      selectContractPositionDialogHeaderText: "",
      selectContractPositionDialog: false,
      contractViewDialog: false,
      detailDialog: false,
      editMode: null,
      items: [],
      expanded: [],
      form: {},
      menu1: false,
      menu2: false,
      menu3: false,
      menu4: false,
      menu5: false,
      dialogDetailForm: {},
      detailForm: {},
      createdAtMenu2: false,
      dataTableOptions: { page: 1, itemsPerPage: 20 },
      page: 1,
      from: 0,
      server_items_length: -1,
      contracts: [],
      filterForm: {
        contract_id: null,
        order_number: null,
        order_date: null,
        title: null,
        total_price: null,
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
          text: this.$t("edi.orders_contract_id"),
          value: "contract",
          width: 150,
          align: "center",
        },
        {
          text: this.$t("edi.order_number"),
          value: "order_number",
          align: "center",
          width: 150,
        },
        {
          text: this.$t("edi.order_date"),
          value: "created_at",
          align: "center",
          width: 150,
        },
        {
          text: this.$t("edi.orders_title"),
          value: "title",
          align: "center",
        },
        {
          text: this.$t("edi.orders_order_quantity"),
          value: "order_quantity",
          align: "center",
        },
        {
          text: this.$t("edi.orders_shipment_quantity"),
          value: "shipment_quantity",
          align: "center",
        },
        {
          text: this.$t("edi.orders_total_price"),
          value: "total_price",
          align: "center",
          width: 150,
        },
        {
          text: this.$t("edi.orders_shipment_price"),
          value: "shipment_price",
          align: "center",
          width: 150,
        },
        {
          text: this.$t("actions"),
          value: "actions",
          width: 120,
          align: "center",
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
    viewContract(contract) {
      this.selectedContract = contract;
      this.contractViewDialog = true;
    },
    selectContractPosition() {
      this.selectContractPositionDialog = true;
    },
    selectContractDetail(item) {
      this.detailForm.contract_detail_id = item.id;
      this.detailForm.contract_detail = item;
      this.selectContractPositionDialog = false;
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
        .post(this.$store.state.backend_url + "api/edi/orders", {
          filter: this.filterForm,
          pagination: this.dataTableOptions,
          search: this.search,
        })
        .then((response) => {
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
        .post(this.$store.state.backend_url + "api/edi/orders/get-ref")
        .then((response) => {
          this.contracts = response.data.contracts;
          this.loading = false;
          //   console.log("data",response.data.contracts);
        })

        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    newDetailItem(item) {
      this.dialogDetailHeaderText = this.$t("edi.order_details_create");
      this.detailForm = {
        id: Date.now(),
        order_id: item.id,
        position: null,
        position: null,
        contract_detail_id: null,
        order_quantity: null,
        start_date: null,
        finish_date: null,
      };
      this.detailDialog = true;
      if (this.$refs.dialogDetailForm) this.$refs.dialogDetailForm.reset();
    },
    newItem() {
      this.dialogHeaderText = this.$t("edi.orders_create");
      this.form = {
        id: Date.now(),
        name: "",
      };
      this.dialog = true;
      this.editMode = false;
      if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
    },

    editDetailItem(item) {
      this.dialogDetailHeaderText = this.$t("edi.contract_details_edit");
      this.detailForm = Object.assign({}, item);
      this.detailDialog = true;
      if (this.$refs.dialogDetailForm)
        this.$refs.dialogDetailForm.resetValidation();
    },
    editItem(item) {
      this.dialogHeaderText = this.$t("edi.orders_edit");
      this.formIndex = this.items.indexOf(item);
      this.form = Object.assign({}, item);
      this.dialog = true;
      this.editMode = true;
      if (this.$refs.dialogForm) this.$refs.dialogForm.resetValidation();
    },
    save() {
      if (this.$refs.dialogForm.validate())
        axios
          .post(
            this.$store.state.backend_url + "api/edi/orders/update",
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
            this.$store.state.backend_url + "api/edi/orders/update-detail",
            this.detailForm
          )
          .then((res) => {
            if (res.data.status == 200) {
              this.detailDialog = false;
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
            } else {
              Swal.fire({
                icon: "error",
                title: this.$t(res.data.message),
              });
            }
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
                "api/edi/orders/delete-detail/" +
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
      if (this.$store.getters.checkPermission("personal_type-delete")) {
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
                  "api/edi/orders/delete/" +
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
      }
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
.hoverable_tr:hover {
  background-color: #e6e7ff;
}

input {
  border: 1px solid #aaa;
  padding: 3px !important;
  width: 100%;
}

input:focus {
  background-color: #fff;
}
</style>
