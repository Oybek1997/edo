<template>
 <div class="fullHeight">
    <v-card class="heightFull" style="border-radius: 10px; border: 1px solid #dce5ef;" elevation="0">
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{ $t('Document History') }}</span>
        <div class="headerSearch d-flex align-center">
          <v-text-field
          class="inputSearch"
          v-model="searchText"
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
              <v-btn class="txt_searchBtn  ml-2" outlined v-bind="attrs" v-on="on">
                <v-icon size="18" color="white">mdi-format-list-bulleted</v-icon>
              </v-btn>
            </template>
            <v-card>
              <v-list class="dropdown-list pa-0">
                <v-list-item style="margin:0px; max-height: 34px; min-height: 34px;" @click="0">                  
                  <v-list-item-title>
                    <v-icon color='#107C41' size="18">mdi-microsoft-excel</v-icon>
                    Скачать таблицу Excel
                    </v-list-item-title
                  ></v-list-item>
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
          <v-data-table
            class="doc-template_data-table"
            dense
            style="
              width: 100%;
              height:100%;
              border-radius: 10px;
            "
            fixed-header
            :loading-text="$t('loadingText')"
            :no-data-text="$t('noDataText')"
            :height="screenHeight"
            :loading="loading"
            :headers="headers"
            :items="items"
            :search="search"
            :server-items-length="server_items_length"
            :options.sync="dataTableOptions"
            :disable-pagination="true"
            item-key="id"
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
            <template v-slot:[`body.prepend`]>
              <!-- <tr class="py-0 my-0">
                <td class="py-0 my-0 dense"></td>
                <td class="py-0 my-0 dense" style="width: 50px">
                  <v-text-field
                    v-model="filterForm.doc_id"
                    class="my-1"
                        type="text"
                        clearable
                    dense
                    outlined
                    hide-details
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filterForm.doc_num"
                    class="my-1"
                        type="text"
                        clearable
                    dense
                    outlined
                    hide-details
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filterForm.doc_signer"
                    class="my-1"
                        type="text"
                        clearable
                    dense
                    outlined
                    hide-details
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filterForm.comment"
                    class="my-1"
                        type="text"
                        clearable
                    dense
                    outlined
                    hide-details
                    @keyup.native.enter="getList()"
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense">
                  <v-autocomplete
                    class="my-1"
                    v-model="filterForm.status"
                    :items="actionTypes"
                    dense
                    clearable
                    outlined
                    hide-details
                    item-value="id"
                    @change="getList()"
                  >
                    <template v-slot:selection="{ item }">{{ item.text }}</template>
                    <template v-slot:item="{ item }">
                      <v-list-item-content>
                        <v-list-item-title v-text="item.text"></v-list-item-title>
                      </v-list-item-content>
                    </template>
                  </v-autocomplete>
                </td>
                <td class="py-0 my-0 dense"></td>
              </tr> -->
            </template>
            <template v-slot:item="{ item, index }">
              <tr>
                <td style="max-width: 30px">{{ index + from }}</td>
                <td style="max-width: 50px">{{ item.document_signer.documents.id }}</td>
                <td style="max-width: 100px">
                  <v-btn
                    style="border-radius: 5px; width: 100%"
                    dense
                    outlined
                    small
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
                    :to="'/document/' + item.document_signer.documents.pdf_file_name"
                  >
                    {{ item.document_signer.documents.document_number }}
                  </v-btn>
                </td>
                <td style="max-width: 150px">
                  <span>
                    {{ item.document_signer.fio }}
                  </span>
                </td>
                <td style="max-width: 300px">
                  <span>
                    {{ item.comment }}
                  </span>
                </td>
                <td style="max-width: 200px">
                  <span>
                    {{
                      item.status == 0
                        ? 'Yaratildi'
                        : item.status == 1
                        ? 'Tasdiqlandi'
                        : item.status == 11
                        ? 'E-imzo bilan tasdiqlandi'
                        : item.status == 2
                        ? 'Bekor qilindi'
                        : item.status == 3
                        ? 'Jarayonda'
                        : item.status == 4
                        ? 'Asoslab bering'
                        : item.status == 5
                        ? 'Fikr'
                        : item.status == 6
                        ? "Tasdiqlovchi qo'shildi"
                        : item.status == 7
                        ? 'Rezolyutsiya'
                        : item.status == 8
                        ? 'Tahrirlandi'
                        : item.status == 9
                        ? "E'lon qilindi"
                        : item.status == 10
                        ? 'Confirmation'
                        : item.status == 12
                        ? 'Ijrochiga qaytarildi'
                        : item.status == 13
                        ? 'Ijrodan chiqarildi'
                        : item.status == 14
                        ? 'Hujjat qaytarildi'
                        : ''
                    }}
                  </span>
                </td>
              </tr>
            </template>
            <!-- <template v-slot:item.id="{ item }">{{
              items.indexOf(item) + from
            }}</template> -->
            <!-- <template v-slot:item.document_id="{ item }">{{
              item.document_signer.documents.id
            }}</template> -->
            <!-- <template v-slot:item.document_number="{ item }">
              <v-btn
              style="border-radius: 5px; width: 100%"
                dense
                outlined
                small
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
                :to="'/document/' + item.document_signer.documents.pdf_file_name"
              >
                {{ item.document_signer.documents.document_number }}</v-btn>
            </template> -->
            <!-- <template v-slot:item.signer="{ item }">
              <span>
                {{ item.document_signer.fio }}
              </span>
            </template> -->
            <!-- <template v-slot:item.status="{ item }">
              <span>
                {{
                  item.status == 0
                    ? 'Yaratildi'
                    : item.status == 1
                    ? 'Tasdiqlandi'
                    : item.status == 11
                    ? 'E-imzo bilan tasdiqlandi'
                    : item.status == 2
                    ? 'Bekor qilindi'
                    : item.status == 3
                    ? 'Jarayonda'
                    : item.status == 4
                    ? 'Asoslab bering'
                    : item.status == 5
                    ? 'Fikr'
                    : item.status == 6
                    ? "Tasdiqlovchi qo'shildi"
                    : item.status == 7
                    ? 'Rezolyutsiya'
                    : item.status == 8
                    ? 'Tahrirlandi'
                    : item.status == 9
                    ? "E'lon qilindi"
                    : item.status == 10
                    ? 'Confirmation'
                    : item.status == 12
                    ? 'Ijrochiga qaytarildi'
                    : item.status == 13
                    ? 'Ijrodan chiqarildi'
                    : item.status == 14
                    ? 'Hujjat qaytarildi'
                    : ''
                }}
              </span>
            </template> -->
            <!-- <template v-slot:item.actions="{ item }">
              
            </template> -->
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
                <label for>{{ $t('document.comment') }}</label>
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
          <small color="red">{{ $t('input_required') }}</small>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="save">{{ $t('save') }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="loading" width="300" hide-overlay>
      <v-card color="primary" dark>
        <v-card-text>
          {{ $t('loadingText') }}
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
const axios = require('axios').default;
import Swal from 'sweetalert2';
export default {
  data() {
    return {
      loading: false,
      search: '',
      from: 1,
      server_items_length: -1,
      dataTableOptions: { page: 1, itemsPerPage: 20 },
      dialog: false,
      editMode: null,
      items: [],
      table_menu: null,
      searchText: null,
      form: {},
      dialogHeaderText: '',
      filterForm: {
        doc_id: '',
        doc_num: '',
        doc_signer: '',
        comment: '',
        startDate: '',
        endDate: '',
        status: '',
      },
      actionTypes: [
        { id: 0, text: 'Yaratildi' },
        { id: 1, text: 'Tasdiqlandi' },
        { id: 2, text: 'Bekor qilindi' },
        { id: 3, text: 'Jarayonda' },
        { id: 4, text: 'Asoslab bering' },
        { id: 5, text: 'Fikr' },
        { id: 6, text: "Tasdiqlovchi qo'shildi" },
        { id: 7, text: 'Rezolyutsiya' },
        { id: 8, text: 'Tahrirlandi' },
        { id: 9, text: "E'lon qilindi" },
        { id: 10, text: 'Confirmation' },
        { id: 11, text: 'E-imzo bilan tasdiqlandi' },
        { id: 12, text: 'Ijrochiga qaytarildi' },
        { id: 13, text: 'Ijrodan chiqarildi' },
        { id: 14, text: 'Hujjat qaytarildi' },
      ],
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
        { text: '#', value: 'id', align: 'center', class: "blue-grey lighten-5", width: 30 },
        { text: this.$t('ID'), class: "blue-grey lighten-5", value: 'document_id', width: 50 },
        { text: this.$t('document.document_number'),  class: "blue-grey lighten-5",value: 'document_number', width: 100 },
        { text: this.$t('document.department_send'), class: "blue-grey lighten-5", value: 'signer', width: 150 },
        { text: this.$t('document.comment'), class: "blue-grey lighten-5", value: 'comment', width: 300 },
        { text: this.$t('document.status'), class: "blue-grey lighten-5", value: 'status', width: 100 },
        // {
        //   text: this.$t('actions'),
        //   value: 'actions',
        //   width: 50, class: "blue-grey lighten-5",
        //   align: 'center',
        // },
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
      this.loading = true;
      axios
        .post('https://b-edo.uzautomotors.com/api/signer-events', {
          pagination: this.dataTableOptions,
          search: this.search,
          filter: this.filterForm,
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
    editItem(item) {
      if (this.$store.getters.checkPermission('requisite-update')) {
        this.dialogHeaderText = this.$t('company_requisites.edit');
        // this.formIndex = this.items.indexOf(item);
        this.form = Object.assign({}, item);
        this.dialog = true;
        // this.editMode = true;
        if (this.$refs.dialogForm) this.$refs.dialogForm.resetValidation();
      }
    },
    save() {
      console.log(this.form);
      if (this.$refs.dialogForm.validate())
        axios
          .post(
            'https://b-edo.uzautomotors.com/api/signer-events/update',
            this.form
          )
          .then((res) => {
            this.getList();
            this.dialog = false;
            const Toast = Swal.mixin({
              toast: true,
              requirement: 'top-end',
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true,
              onOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
              },
            });

            Toast.fire({
              icon: 'success',
              title: this.$t('create_update_operation'),
            });
          })
          .catch((err) => {
            console.log(err);
          });
    },
    deleteItem(id) {
      Swal.fire({
        title: this.$t('swal_title'),
        text: this.$t('swal_text'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: this.$t('swal_delete'),
      }).then((result) => {
        if (result.value) {
          axios
            .delete(
              'https://b-edo.uzautomotors.com/api/signer-events/delete/' + id
            )
            .then((res) => {
              this.items = this.items.filter((v) => v.id != id);
              this.getList(this.page, this.itemsPerPage);
              this.dialog = false;
              Swal.fire('Deleted!', this.$t('swal_deleted'), 'success');
            })
            .catch((err) => {
              Swal.fire({
                icon: 'error',
                title: this.$t('swal_error_title'),
                text: this.$t('swal_error_text'),
              });
              console.log(err);
            });
        }
      });
    },
  },
  mounted() {
    this.getList();
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
  background: #FF9F0E;
  border: 0.20px rgba(0, 0, 0, 0.28) solid;
  box-shadow: none;
  min-width: 25px!important;
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
