<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ $t("edi.asn_header") }}</span>
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
            v-if="$store.getters.checkPermission('edi-asn')"
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
        <template v-slot:[`body.prepend`]>
          <tr class="py-0 my-0 filter-input prepend_height">
            <td class="pa-1"></td>
            <td class="pa-1"></td>

            <td class="pa-1">
              <input v-model="filterForm.asn_number" @keyup.enter="getList()" />
            </td>
            <td class="pa-1">
              <input v-model="filterForm.created_at" @keyup.enter="getList()" />
            </td>
            <td class="pa-1">
              <input
                v-model="filterForm.contract_id"
                @keyup.enter="getList()"
              />
            </td>
            <td class="pa-1">
              <input v-model="filterForm.order_id" @keyup.enter="getList()" />
            </td>
            <td class="pa-1">
              <input v-model="filterForm.invoice" @keyup.enter="getList()" />
            </td>
            <td class="pa-1">
              <input
                v-model="filterForm.shipment_type_id"
                @keyup.enter="getList()"
              />
            </td>
            <td class="pa-1">
              <input
                v-model="filterForm.shipment_date"
                @keyup.enter="getList()"
              />
            </td>
            <td class="pa-1">
              <input
                v-model="filterForm.container_number"
                @keyup.enter="getList()"
              />
            </td>
            <td class="py-0 my-0 dense" style="width: 50px"></td>
          </tr>
        </template>

        <template v-slot:expanded-item="{ headers, item }">
          <td :colspan="headers.length">
            <table style="width: 50%" class="mt-4">
              <tr>
                <td class="text-right">
                  <b>{{ $t("edi.created_at") }}</b>
                </td>
                <td class="text-left">
                  {{ item.created_at }}
                </td>
                <td class="text-right">
                  <b>{{ $t("edi.created_by") }}</b>
                </td>
                <td class="text-left">
                  {{
                    item.created_by && item.created_by.employee
                      ? item.created_by.employee.fio
                      : ""
                  }}
                </td>
              </tr>
              <tr>
                <td class="text-right">
                  <b>{{ $t("edi.updated_at") }}</b>
                </td>
                <td class="text-left">
                  {{ item.updated_at }}
                </td>
                <td class="text-right">
                  <b>{{ $t("edi.updated_by") }}</b>
                </td>
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
                    {{ $t("edi.asn_asn_detail_id") }}
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
                    border="1"
                  >
                    <tr>
                      <th class="text-center" colspan="3">
                        {{ $t("edi.asn_detail_contract_position") }}
                      </th>
                      <th class="text-center" rowspan="2">
                        {{ $t("edi.asn_detail_shipment_quantity") }}
                      </th>
                      <th
                        class="text-center"
                        rowspan="2"
                        style="width: 120px"
                        v-if="user().id == item.created_by.id"
                      >
                        {{ $t("edi.contract_detail_actions") }}
                      </th>
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
                    <tr v-for="(item, key) in item.asn_details" :key="key">
                      <td class="text-right">
                        {{
                          item.order_detail.contract_detail
                            ? item.order_detail.contract_detail.material
                                .material_number
                            : ""
                        }}
                      </td>
                      <td class="text-right">
                        {{
                          item.order_detail.contract_detail
                            ? item.order_detail.contract_detail.material
                                .description
                            : ""
                        }}
                      </td>
                      <td class="text-right">
                        {{
                          item.order_detail.contract_detail
                            ? item.order_detail.contract_detail.price
                            : ""
                        }}
                      </td>
                      <td class="text-right">{{ item.shipment_quantity }}</td>
                      <td
                        class="text-right"
                        v-if="user().id == item.created_by"
                      >
                        <v-btn
                          color="blue"
                          small
                          text
                          @click="editDetailItem(item)"
                        >
                          <v-icon>mdi-pencil</v-icon>
                        </v-btn>
                        <v-btn
                          color="red"
                          small
                          text
                          @click="deleteDetailItem(item)"
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
        <template v-slot:item.contract="{ item }">
          <contract_view :data="item.order.contract"></contract_view>
        </template>
        <template v-slot:item.order="{ item }">
          <order_view :data="item.order"></order_view>
        </template>
        <template v-slot:item.created_at="{ item }">
          <span style="white-space: normal; max-width: 100px">{{
            item.created_at.substr(0, 10)
          }}</span>
        </template>
        <template v-slot:item.actions="{ item }">
          <v-btn
            color="blue"
            small
            text
            :href="$store.state.backend_url + 'api/edi/asns/print/' + item.id"
            target="_blank"
          >
            <!-- @click="downloadPdf(item)" -->
            <v-icon>mdi-printer</v-icon>
          </v-btn>
          <v-btn
            color="blue"
            small
            text
            @click="editItem(item)"
            v-if="user().id == item.created_by.id"
          >
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
          <v-btn
            color="red"
            small
            text
            @click="deleteItem(item)"
            v-if="user().id == item.created_by.id"
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
                  <label for>{{ $t("edi.order_id") }}</label>
                </td>
                <td class="pa-1">
                  <v-autocomplete
                    v-model="form.order_id"
                    :items="orders"
                    hide-details="auto"
                    dense
                    outlined
                  ></v-autocomplete>
                </td>
              </tr>
              <tr>
                <td class="pa-1 text-right">
                  <label for>{{ $t("edi.invoice") }}</label>
                </td>
                <td class="pa-1">
                  <v-text-field
                    v-model="form.invoice"
                    :rules="[(v) => !!v || $t('input_required')]"
                    hide-details="auto"
                    dense
                    outlined
                  ></v-text-field>
                </td>
              </tr>
              <tr>
                <td class="pa-1 text-right">
                  <label for>{{ $t("edi.container_number") }}</label>
                </td>
                <td class="pa-1">
                  <v-text-field
                    v-model="form.container_number"
                    :rules="[(v) => !!v || $t('input_required')]"
                    hide-details="auto"
                    dense
                    outlined
                  ></v-text-field>
                </td>
              </tr>
              <tr>
                <td class="pa-1 text-right">
                  <label for>{{ $t("edi.shipment_type_id") }}</label>
                </td>
                <td class="pa-1">
                  <v-autocomplete
                    v-model="form.shipment_type_id"
                    :items="shipmentTypes"
                    hide-details="auto"
                    dense
                    outlined
                  ></v-autocomplete>
                </td>
              </tr>

              <tr>
                <td class="text-right pa-1">
                  <label for>{{ $t("edi.shipment_date") }}</label>
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
                        v-model="form.shipment_date"
                        readonly
                        outlined
                        dense
                        hide-details
                        v-bind="attrs"
                        v-on="on"
                      ></v-text-field>
                    </template>
                    <v-date-picker
                      v-model="form.shipment_date"
                      @input="menu1 = false"
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
                    @click="selectOrderDetailDialog = true"
                    >{{
                      detailForm.order_detail
                        ? detailForm.order_detail.contract_detail.material
                            .material_number
                        : "Select"
                    }}</v-btn
                  >
                  <div
                    class="v-text-field__details ml-2 mt-1"
                    v-if="!detailForm.order_detail_id"
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
                <td class="pa-1 text-right">
                  <label for>{{
                    $t("edi.asn_details_shipment_quantity")
                  }}</label>
                </td>
                <td class="pa-1">
                  <v-text-field
                    v-model="detailForm.shipment_quantity"
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
          <v-btn color="primary" @click="saveDetail">{{ $t("save") }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="selectOrderDetailDialog"
      @keydown.esc="selectOrderDetailDialog = false"
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
            @click="selectOrderDetailDialog = false"
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
              items.find((v) => v.id == detailForm.asn_id)
                ? items.find((v) => v.id == detailForm.asn_id).order
                    .order_details
                : []"
              :key="k"
            >
              <td class="text-left pa-1">
                {{ i.contract_detail.material.material_number }}
              </td>
              <td class="text-left pa-1">
                {{ i.contract_detail.material.description }}
              </td>
              <td class="text-right pa-1">{{ i.contract_detail.quantity }}</td>
              <td class="text-right pa-1">{{ i.contract_detail.price }}</td>
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
import orderView from "@/views/EDI/orderView";
export default {
  components: {
    contract_view: contractView,
    order_view: orderView,
  },
  data() {
    return {
      loading: false,
      search: "",
      dialog: false,
      editMode: null,
      items: [],
      expanded: [],
      form: {},
      detailForm: {},
      dialogHeaderText: "",
      dialogDetailHeaderText: "",
      selectOrderDetailDialogHeaderText: "",
      selectOrderDetailDialog: false,
      detailDialog: false,
      createdAtMenu2: false,
      dataTableOptions: { page: 1, itemsPerPage: 20 },
      page: 1,
      from: 0,
      server_items_length: -1,
      shipmentTypes: [],
      contracts: [],
      orders: [],
      selectedDropDownValue: "",
      menu4: false,
      menu5: false,
      form: {
        shipment_date: null,
      },
      filterForm: {
        asn_number: "",
        invoice: "",
        container_number: "",
        order_id: "",
        shipment_type_id: "",
        contract_id: "",
        shipment_date: "",
      },
      menu1: false,
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
          text: this.$t("edi.asn_number"),
          value: "asn_number",
          align: "center",
        },
        {
          text: this.$t("edi.asn_date"),
          value: "created_at",
          align: "center",
        },
        {
          text: this.$t("edi.contract_id"),
          value: "contract",
          align: "center",
        },
        {
          text: this.$t("edi.order_id"),
          value: "order",
          align: "center",
        },
        {
          text: this.$t("edi.invoice"),
          value: "invoice",
          align: "center",
        },
        {
          text: this.$t("edi.shipment_type_id"),
          value: "shipment_type.name",
          align: "center",
        },
        {
          text: this.$t("edi.shipment_date"),
          value: "shipment_date",
          align: "center",
        },
        {
          text: this.$t("edi.container_number"),
          value: "container_number",
          align: "center",
        },
        {
          text: this.$t("actions"),
          value: "actions",
          width: 180,
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
    downloadPdf(item) {
      const link = document.createElement("a");
      link.href =
        this.$store.state.backend_url + "api/edi/asns/print/" + item.id;
      link.download = item.asn_number + ".pdf";
      link.click();
      // download("data:text/html,HelloWorld!", "helloWorld.txt");
    },
    selectContractDetail(item) {
      this.detailForm.order_detail_id = item.id;
      this.detailForm.order_detail = item;
      this.selectOrderDetailDialog = false;
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
        .post(this.$store.state.backend_url + "api/edi/asns", {
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
        .post(this.$store.state.backend_url + "api/edi/asns/get-ref")
        .then((response) => {
          this.shipmentTypes = response.data.shipmentTypes;
          this.contracts = response.data.contracts;
          this.orders = response.data.orders;

          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    newItem() {
      this.dialogHeaderText = this.$t("edi.business_partner_create");
      this.form = {
        id: Date.now(),
        asn_number: null,
        invoice: null,
        container_number: null,
        shipment_type_id: null,
        contract_id: null,
        shipment_date: null,
        order_id: null,
      };
      this.dialog = true;
      this.editMode = false;
      if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
    },
    editItem(item) {
      this.dialogHeaderText = this.$t("edi.business_partner_edit");
      this.formIndex = this.items.indexOf(item);
      this.form = Object.assign({}, item);
      this.dialog = true;
      this.editMode = true;
      if (this.$refs.dialogForm) this.$refs.dialogForm.resetValidation();
    },

    newDetailItem(item) {
      this.dialogDetailHeaderText = this.$t("edi.asn_details_create");
      this.detailForm = {
        id: Date.now(),
        asn_id: item.id,
        position: null,
        order_detail_id: null,
        shipment_quantity: null,
      };
      this.detailDialog = true;
      if (this.$refs.dialogDetailForm) this.$refs.dialogDetailForm.reset();
    },
    editDetailItem(item) {
      this.dialogDetailHeaderText = this.$t("edi.asn_details_edit");
      this.detailForm = Object.assign({}, item);
      this.detailDialog = true;
      if (this.$refs.dialogDetailForm)
        this.$refs.dialogDetailForm.resetValidation();
    },
    save() {
      if (this.$refs.dialogForm.validate())
        axios
          .post(
            this.$store.state.backend_url + "api/edi/asns/update",
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
            this.$store.state.backend_url + "api/edi/asns/update-detail",
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
              this.detailDialog = false;
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
                "api/edi/asns/delete-detail/" +
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
              this.$store.state.backend_url + "api/edi/asns/delete/" + item.id
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

    toggleDatePicker(field) {
      if (field === "shipment_date") {
        this.showDatePicker = !this.showDatePicker;
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
</style>
