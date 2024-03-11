<template>
  <div class="fullHeight">
    <v-card 
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2"> {{ $t("cancel_documents") }}</span>
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
        <!-- <v-btn outlined fab x-small>
          <v-icon>mdi-file-excel-outline</v-icon>
        </v-btn> -->
      </v-card-title>
      <v-row class="mx-0">
        <v-col class="ma-0 pa-0" xs="12">
          <v-data-table
            class="doc-template_data-table table_thead_two_line"
            dense
            style="width: 100%; height: 100%; border-radius: 10px"
            fixed-header
            :loading-text="$t('loadingText')"
            :no-data-text="$t('noDataText')"
            :height="screenHeight"
            :loading="loading"
            :headers="headers"
            :items="items"
            :search="search"
            item-key="id"
            :server-items-length="server_items_length"
            :options.sync="dataTableOptions"
            :disable-pagination="true"
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
            @update:page="updatePage"
            @update:items-per-page="updatePerPage"
          >
            <template v-slot:item.document_number="{ item }">
              <v-btn
                style="height: 21px;"
                outlined
                small
                rounded
                class=""
                :to="'/document/' + item.pdf_file_name"
                >{{ item.document_number }}</v-btn
              >
            </template>
            <template v-slot:item.actions="{ item }">
              <v-btn
                small
                icon
                @click="makeDocument(item)"
                v-if="
                  item.complaens_cencel_document &&
                  !item.complaens_cencel_document.reason_document_id &&
                  item.complaens_cencel_document.directory_id != 38 &&
                  item.complaens_cencel_document.directory_id != 39
                "
              >
                <v-icon color="primary">mdi-text-box-plus-outline</v-icon>
              </v-btn>
              <v-btn
                small
                icon
                @click="makeDocument(item)"
                :href="
                  '/#/document/' +
                  item.complaens_cencel_document.reason_document.pdf_file_name
                "
                v-else-if="
                  item.complaens_cencel_document &&
                  item.complaens_cencel_document.reason_document
                "
              >
                <v-icon color="primary">mdi-text-box-check-outline</v-icon>
              </v-btn>
              <v-btn
                small
                icon
                class="float-right"
                @click="edit(item)"
                v-if="item.complaens_cencel_document"
              >
                <v-icon color="primary">mdi-pencil</v-icon>
              </v-btn>
              <v-btn small icon @click="edit(item)" v-else>
                <v-icon color="primary">mdi-plus</v-icon>
              </v-btn>
            </template>
            <template v-slot:item.document_status="{ item }">
              <span
                v-if="
                  item.complaens_cencel_document &&
                  item.complaens_cencel_document.reason_document
                "
                >{{
                  document_status[
                    item.complaens_cencel_document.reason_document.status
                  ]["name_uz_cyril"]
                }}</span
              >
              <span
                v-else-if="
                  item.complaens_cencel_document &&
                  (item.complaens_cencel_document.directory_id == 38 ||
                    item.complaens_cencel_document.directory_id == 39)
                "
                >{{ "Якунланган" }}</span
              >
            </template>
          </v-data-table>
        </v-col>
      </v-row>
    </v-card>

    <v-dialog
      v-model="dialog"
      @keydown.esc="dialog = false"
      persistent
      max-width="800"
    >
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline"
            >{{ $t("edit") }} {{ form.document_number }}</span>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="dialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form ref="dialogForm">
            <v-row>
              <v-col cols="12" sm="6">
                <v-autocomplete
                  label="Rад қилиш сабаби (қисқа)"
                  v-model="form.complaens_cencel_document.directory_id"
                  :items="short_reasons"
                  :item-text="'name_' + $i18n.locale"
                  item-value="id"
                  outlined
                  hide-details
                  dense
                  :rules="[(v) => !!v || $t('input_required')]"
                >
                </v-autocomplete>
              </v-col>
              <v-col
                cols="12"
                sm="6"
                v-if="
                  short_reasons.find((v) => {
                    if (
                      v.id == form.complaens_cencel_document.directory_id &&
                      v.code == '237'
                    )
                      return v;
                  })
                "
              >
                <v-autocomplete
                  label="Комплаенс дастурига асосан аниқланган хавфлар."
                  v-model="form.complaens_cencel_document.identified_risks"
                  :items="_237"
                  :item-text="'name_' + $i18n.locale"
                  item-value="id"
                  outlined
                  hide-details
                  dense
                  :rules="[(v) => !!v || $t('input_required')]"
                >
                </v-autocomplete>
              </v-col>
              <v-col
                cols="12"
                sm="6"
                v-if="
                  short_reasons.find((v) => {
                    if (
                      v.id == form.complaens_cencel_document.directory_id &&
                      v.code == '237'
                    )
                      return v;
                  })
                "
              >
                <v-autocomplete
                  label="Комплаенс хавфлар бандлари"
                  v-model="form.complaens_cencel_document.risk_item"
                  :items="c024"
                  :item-text="'name_' + $i18n.locale"
                  item-value="id"
                  outlined
                  hide-details
                  dense
                  :rules="[(v) => !!v || $t('input_required')]"
                >
                </v-autocomplete>
              </v-col>
              <v-col
                cols="12"
                sm="6"
                v-if="
                  short_reasons.find((v) => {
                    if (
                      v.id == form.complaens_cencel_document.directory_id &&
                      (v.code == '231' || v.code == '232' || v.code == '237')
                    )
                      return v;
                  })
                "
              >
                <v-text-field
                  label="Иқтисод қилинган сумма миқдори"
                  v-model="form.complaens_cencel_document.amount_sum"
                  type="number"
                  hide-details
                  dense
                  outlined
                  :rules="[(v) => !!v || $t('input_required')]"
                ></v-text-field>
              </v-col>

              <v-col cols="12">
                <v-textarea
                  v-model="form.complaens_cencel_document.detailed_reason"
                  outlined
                  dense
                  label="Pад қилиш сабаби (батафсил)"
                  rows="3"
                  hide-details
                ></v-textarea>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions class="px-6">
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="save">{{ $t("save") }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="dialogAddReason" max-width="500px">
      <v-card class="pa-5">
        <v-card-title class="pa-0" primary-title>
         <span class="dialogTitle"> {{ $t("add") }} </span>
          <v-spacer></v-spacer>
          <v-btn color="success" class="mx-2" small text @click="saveReason()">
            {{ $t("save") }}
          </v-btn>
          <v-btn
            color="red"
            outlined
            x-small
            fab
            class
            @click="dialogAddReason = false"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form ref="dialogAddReason">
            <v-row>
              <v-col>
                <v-text-field
                  :label="$t('name_uz_latin')"
                  v-model="newReason.name_uz_latin"
                  :rules="[(v) => !!v || $t('input_required')]"
                  hide-details
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-text-field
                  :label="$t('name_uz_cyril')"
                  v-model="newReason.name_uz_cyril"
                  :rules="[(v) => !!v || $t('input_required')]"
                  hide-details
                  dense
                  outlined
                ></v-text-field>
              </v-col>

              <v-col cols="12">
                <v-text-field
                  :label="$t('name_ru')"
                  v-model="newReason.name_ru"
                  :rules="[(v) => !!v || $t('input_required')]"
                  hide-details
                  dense
                  outlined
                ></v-text-field>
              </v-col>
            </v-row>
          </v-form>
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
export default {
  data: () => ({
    search: "",
    document_status: [
      {
        name_uz_latin: "yangi",
        name_uz_cyril: "янги",
        name_ru: "новый",
        color: "black",
      },
      {
        name_uz_latin: "E'lon qilish",
        name_uz_cyril: "Эьлон қилиш",
        name_ru: "опубликован",
        color: "cyan",
      },
      {
        name_uz_latin: "qayta ishlash",
        name_uz_cyril: "қайта ишлаш",
        name_ru: "обработка",
        color: "blue",
      },
      {
        name_uz_latin: "Imzolandi",
        name_uz_cyril: "Имзоланди",
        name_ru: "Подписано",
        color: "teal",
      },
      {
        name_uz_latin: "Bajarildi",
        name_uz_cyril: "Бажарилди",
        name_ru: "Выполнено",
        color: "amber",
      },
      {
        name_uz_latin: "Yakunlandi",
        name_uz_cyril: "Якунланди",
        name_ru: "Завершено",
        color: "success",
      },
      {
        name_uz_latin: "Bekor qilindi",
        name_uz_cyril: "Бекор қилинди",
        name_ru: "Отменен",
        color: "error",
      },
      {
        name_uz_latin: "Oldindan kelishuv",
        name_uz_cyril: "Олдидан килишув",
        name_ru: "Предсогласование",
        color: "light-green",
      },
    ],
    loading: false,
    search: "",
    dialog: false,
    dialogAddReason: false,
    editMode: null,
    items: [],
    departments: [],
    roles: [],
    form: {
      complaens_cencel_document: {},
    },
    dialogHeaderText: "",
    dataTableOptions: { page: 1, itemsPerPage: 20 },
    page: 1,
    from: 0,
    server_items_length: -1,
    short_reasons: [],
    _237: [],
    c024: [],
    newReason: {
      directory_type_id: 3,
      code: "SHR",
    },
  }),
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    headers() {
      return [
        {
          text: "#",
          value: "id",
          width: 30,
          sortable: false,
        },
        {
          text: this.$t("name"),
          value: "document_number",
          align: "center",
        },
        {
          text: "рад қилиш сабаби (қисқа)",
          value: "complaens_cencel_document.short_reason",
        },
        {
          text: "рад қилиш сабаби (батафсил)",
          value: "complaens_cencel_document.detailed_reason",
        },
        {
          text: "иқтисод қилинган сумма миқдори",
          value: "complaens_cencel_document.amount_sum",
        },
        {
          text: "Комплаенс дастурига асосан аниқланган хавфлар",
          value: "complaens_cencel_document.identified_risks_text",
        },
        {
          text: "Комплаенс хавфлар бандлари",
          value: "complaens_cencel_document.risk_item_text",
        },
        {
          text: "ҳужжат типи",
          value: "document_type",
        },
        {
          text: "ҳужжат санаси киритилади",
          value: "document_date",
        },
        {
          text: this.$t("actions"),
          value: "actions",
          width: 50,
          align: "center",
          sortable: false,
        },
        {
          text: "ҳужжат статуси",
          value: "document_status",
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
    addShortReason() {
      this.dialogAddReason = true;
      // console.log('sdsdsd');
    },
    edit(item) {
      this.form = item;
      this.form.complaens_cencel_document = this.form.complaens_cencel_document
        ? this.form.complaens_cencel_document
        : {};
      this.dialog = true;
      // console.log(item);
    },
    getList() {
      this.loading = true;
      axios
        .post(
          this.$store.state.backend_url + "api/complaens-cencel-documents",
          {
            pagination: this.dataTableOptions,
            search: this.search,
          }
        )
        .then((response) => {
          this.items = response.data.data;
          this.from = response.data.from;
          this.server_items_length = response.data.total;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    makeDocument(item) {
      this.loading = true;
      axios
        .post(
          this.$store.state.backend_url + "api/complaens-make-document",
          item
        )
        .then((response) => {
          // this.items = response.data.data;
          // this.from = response.data.from;
          // this.server_items_length = response.data.total;
          this.$router.push("/document/" + response.data.pdf_file_name);
          this.loading = false;
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
            "api/complaens-cencel-documents/get-ref"
        )
        .then((response) => {
          this.short_reasons = response.data.short_reasons;
          this._237 = response.data._237;
          this.c024 = response.data.c024;
          // console.log(response);
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    save() {
      if (this.$refs.dialogForm.validate()) {
        this.loading = true;
        axios
          .post(
            this.$store.state.backend_url +
              "api/complaens-cencel-documents/update",
            this.form
          )
          .then((response) => {
            this.loading = false;
            console.log(response);
            this.$refs.dialogForm.reset();
            this.dialog = false;
            this.getList();
          })
          .catch((error) => {
            console.log(error);
            this.loading = false;
          });
      }
      console.log(this.form);
    },
    saveReason() {
      if (this.$refs.dialogAddReason.validate()) {
        this.loading = true;
        axios
          .post(
            this.$store.state.backend_url + "api/directories/update",
            this.newReason
          )
          .then((response) => {
            console.log(response);
            this.newReason = {
              directory_type_id: 3,
              code: "SHR",
            };
            this.$refs.dialogAddReason.reset();
            this.dialogAddReason = false;
            this.getRef();
            this.loading = false;
          })
          .catch((error) => {
            console.log(error);
            this.loading = false;
          });
      }
      console.log(this.newReason);
    },
  },
  mounted() {
    this.getList();
    this.getRef();
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
.doc-template_data-table table > thead > tr > th {
  white-space: normal!important;
  max-width: 200px;
}
</style>