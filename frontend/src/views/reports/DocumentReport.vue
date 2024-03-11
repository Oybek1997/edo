<template>
  <div class="fullHeight">
    <v-card
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{ $t("Отчеты по документам") }}</span>
        <div class="headerSearch d-flex align-center">
          <v-text-field
            class="inputSearch"
            @keyup.enter="getList"
            prepend-inner-icon="mdi-magnify"
            :placeholder="$t('search')"
            dense
            hide-details
            background="#fff"
            solo
            elevation="0"
          ></v-text-field>
          <v-menu
            v-model="menu"
            :close-on-content-click="false"
            :nudge-right="40"
            transition="scale-transition"
            offset-y
            min-width="auto"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-text-field
                prepend-inner-icon="mdi-calendar"
                size="18"
                class="inputSearch"
                v-model="filterForm.startDate"
                label="Sana boshi"
                dense
                hide-details
                solo
                v-bind="attrs"
                v-on="on"
              ></v-text-field>
            </template>
            <v-date-picker
              v-model="filterForm.startDate"
              @input="menu = false"
            ></v-date-picker>
          </v-menu>
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
                class="inputSearch"
                prepend-inner-icon="mdi-calendar"
                size="18"
                dense
                v-model="filterForm.endDate"
                label="Sana oxiri"
                v-bind="attrs"
                v-on="on"
                dense
                hide-details
                solo
              ></v-text-field>
            </template>
            <v-date-picker
              v-model="filterForm.endDate"
              @input="menu2 = false"
            ></v-date-picker>
          </v-menu>
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
                >
                  <v-list-item-title>
                    <v-icon size="18">mdi-plus</v-icon>
                    Добавить новую строку
                  </v-list-item-title></v-list-item
                >
                <v-list-item
                  style="margin: 0px; max-height: 34px; min-height: 34px"
                  @click="0"
                >
                  <v-list-item-title>
                    <v-icon color="#107C41" size="18"
                      >mdi-microsoft-excel</v-icon
                    >
                    Скачать таблицу Excel
                  </v-list-item-title></v-list-item
                >
              </v-list>
            </v-card>
          </v-menu>
          <!-- <v-btn color="#6ac82d" class="btn_class"  dark  @click="getList()">
           Izlash
         </v-btn> -->
        </div>
      </v-card-title>
      <v-row class="mx-0">
        <v-col class="ma-0 pa-0" xs="12">
          <v-simple-table
            style="padding: 0 20px"
            :height="screenHeight"
            ref="table"
            id="table"
            dense
            fixed-header
            fixed-footer
          >
            <thead class="text-center">
              <tr>
                <th class="text-center blue-grey lighten-5"  rowspan="2">#</th>
                <th class="text-center blue-grey lighten-5" rowspan="2">Xujaj turi</th>
                <th class="text-center blue-grey lighten-5" rowspan="2">Barchasi</th>
                <th class="text-center blue-grey lighten-5" rowspan="2">Yakunlangan</th>
                <th class="text-center blue-grey lighten-5" rowspan="2">Ijroda</th>
                <th class="text-center blue-grey lighten-5" rowspan="2">Muddati o’tgan</th>
              </tr>
            </thead>
            <tbody class="text-center">
              <template v-for="(item, keys) in items" :keys="key">
                <tr v-for="(itemM, index) in item.value" :key="index">
                  <td
                    class="text-center"
                    v-if="index === 0"
                    :rowspan="item.value.length"
                  >
                    {{ item.docStatus }}
                  </td>
                  <td class="text-left" color='red'>{{ itemM.docType }}</td>
                  <td class="text-center">{{ itemM.docAll }}</td>
                  <td class="text-center">{{ itemM.docClose }}</td>
                  <td class="text-center">{{ itemM.docJarayon }}</td>
                  <td class="text-center">{{ itemM.docUtgan }}</td>
                </tr>
              </template>
            </tbody>
          </v-simple-table>
        </v-col>
      </v-row>
    </v-card>
    <!-- <v-dialog
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
            <v-row>
              <v-col cols="12">
                <label for>{{ $t("document.comment") }}</label>
                <v-text-field
                  v-model="form.comment"
                  :rules="[(v) => !!v || $t('input_required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
            </v-row>
          </v-form>
          <small color="red">{{ $t("input_required") }}</small>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="save">{{ $t("save") }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog> -->
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
      from: 1,
      server_items_length: -1,
      dataTableOptions: { page: 1, itemsPerPage: 20 },
      dialog: false,
      editMode: null,
      statusCounts: 0,
      items: [],
      itemq: [],
      form: {},
      dialogHeaderText: "",
      filterForm: {
        doc_id: "",
        doc_num: "",
        doc_signer: "",
        comment: "",
        startDate: "",
        endDate: "",
        status: "",
      },

      date: null,
      menu: false,
      menu2: false,
      date2: null,
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    headers() {
      return [
        {
          text: "#",
          value: "docStatus",
          align: "center",
          class: "blue-grey lighten-5",
          width: 100,
        },
        {
          text: this.$t("Xujaj turi"),
          align: "center",
          class: "blue-grey lighten-5",
          value: "docType",
          width: 100,
        },
        {
          text: this.$t("Barcha"),
          align: "center",
          class: "blue-grey lighten-5",
          value: "docAll",
          width: 100,
        },
        {
          text: this.$t("Yakunlangan"),
          align: "center",
          class: "blue-grey lighten-5",
          value: "docClose",
          width: 100,
        },
        {
          text: this.$t("Ijroda"),
          align: "center",
          class: "blue-grey lighten-5",
          value: "docJarayon",
          width: 100,
        },
        {
          text: this.$t("Muddati o`tgan"),
          align: "center",
          class: "blue-grey lighten-5",
          value: "docUtgan",
          width: 100,
        },
      ];
    },
  },
  methods: {
    updatePage($event) {
      this.getList();
    },
    updatePerPage($event) {
      this.getList();
    },

    getList() {
      this.items = [
        {
          docStatus: "Кирувчи",
          value: [
            {
              docType: "Ариза",
              docAll: "10",
              docClose: "5",
              docJarayon: "3",
              docUtgan: "2",
            },
            {
              docType: "Рухсатнома",
              docAll: "11",
              docClose: "6",
              docJarayon: "3",
              docUtgan: "2",
            },
            {
              docType: "Буруқ",
              docAll: "12",
              docClose: "7",
              docJarayon: "2",
              docUtgan: "3",
            },
            {
              docType: "Кириш хужжатлари",
              docAll: "9",
              docClose: "5",
              docJarayon: "2",
              docUtgan: "2",
            },
            {
              docType: "Сўровнома",
              docAll: "15",
              docClose: "5",
              docJarayon: "8",
              docUtgan: "2",
            },
          ],
        },
        {
          docStatus: "Чиқувчи",
          value: [
            {
              docType: "Буруқ",
              docAll: "2",
              docClose: "0",
              docJarayon: "0",
              docUtgan: "2",
            },
            {
              docType: "Рухсатнома",
              docAll: "8",
              docClose: "5",
              docJarayon: "3",
              docUtgan: "0",
            },
            {
              docType: "Ариза",
              docAll: "3",
              docClose: "1",
              docJarayon: "1",
              docUtgan: "1",
            },
            {
              docType: "Баённома",
              docAll: "22",
              docClose: "15",
              docJarayon: "5",
              docUtgan: "2",
            },
            {
              docType: "Далолатнома",
              docAll: "10",
              docClose: "5",
              docJarayon: "3",
              docUtgan: "2",
            },
          ],
        },
      ];
      this.statusCounts = {};
      this.items.forEach((v) => {
        v.count = v.docStatus.length;
      });
      console.log(" this.items=", this.items);
      // this.items.forEach((item) => {
      //   if (this.statusCounts[item.docStatus]) {
      //     this.statusCounts[item.docStatus] += 1;
      //   } else {
      //     this.statusCounts[item.docStatus] = 1;
      //   }
      // });
      // console.log('this.statusCounts=',this.statusCounts);
    },
  },
  mounted() {
    this.getList();
  },
};
</script>
 <style scoped>
th {
  border: 1px solid #e71e10;
}
td {
  border: 1px solid #DCE5EF;
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
.inputSearch {
  border: 1px solid #e6e6e6;
  box-shadow: none;
  border-radius: 0px 0 0 0px;
  max-height: 36px;
  overflow: hidden;
  color: #212529;
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
</style>
 