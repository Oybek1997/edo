<template>
  <div class="fullHeight">
    <v-card
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">Template biriktirilgan Userlar</span>
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
                  style="margin: 0px; max-height: 34px; min-height: 34px; text-decoration: none;"
                  @click="newItem"
                >
                <router-link :to="{ path: '/adminpanel/template/attach' }" style="text-decoration: none;">
                  <v-list-item-title>
                    <v-icon size="18">mdi-plus</v-icon>
                    Добавить новую строку
                    </v-list-item-title>
                </router-link>
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

        <!-- <router-link :to="{ path: '/template/attach' }"> <v-btn color="success">Create</v-btn></router-link> -->

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
            :search="search"
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
            <template v-slot:item="{ item, index }">
              <tr>
                <td>{{ index + from }}</td>
                <td>{{  item.user.username}}</td>
                <td style="max-width: 200px">{{ item.document_template.name_uz_cyril }}</td>
                <td>
                  <v-btn
                    v-if="$store.getters.checkPermission('partners-delete')"
                    class="pl-0 pr-2"
                    color="error"
                    style="min-width: 25px"
                    small
                    text
                    @click="deleteItem(item)"
                  >
                    <v-icon size="18">mdi-trash-can-outline</v-icon>
                  </v-btn>
                </td>
              </tr>
            </template>
          </v-data-table>
        </v-col>
      </v-row>
    </v-card>
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
import Swal from 'sweetalert2'
const axios = require('axios').default
export default {
  data () {
    return {
      selectObjectType: '',
      objectTypesList: [],
      fileDialog: false,
      pdfViewDialog: false,
      fileForView: { id: 0 },
      selectFiles: [],
      loading: false,
      search: '',
      page: 1,
      from: 0,
      server_items_length: -1,
      dataTableOptions: { page: 1, itemsPerPage: 20 },
      dialog: false,
      editMode: null,
      items: [],
      companies: [],
      measure_name: [],
      form: {},
      dialogHeaderText: '',
      formData: null
    }
  },
  computed: {
    screenHeight () {
      return window.innerHeight - 170
    },
    headers () {
      return [
        { text: '#', value: 'id', align: 'center', width: 30 },
        { text: 'Foydalanuvchi', value: 'name' },
        { text: 'Template', value: 'adress' },
        {
          text: this.$t('actions'),
          value: 'actions',
          width: 50,
          align: 'center'
        }
      ]
    },
    getFormDataValues () {
      return this.staffTmp.files
    },
    user () {
      return this.$store.getters.getUser()
    }
  },
  methods: {
    updatePage ($event) {
      this.getList()
    },
    updatePerPage ($event) {
      this.getList()
    },

    getList () {
      this.loading = true
      axios
        .post(this.$store.state.backend_url + 'api/template/show', {
          pagination: this.dataTableOptions,
          search: this.search
        })
        .then((response) => {
          this.items = response.data.data
          this.from = response.data.from
          this.server_items_length = response.data.total
          // this.measure_name = response.data.data.map(v => {
          //   return { shortName: v.measure.short_name, id: v.measure.id };
          // });
          this.loading = false
        })
        .catch((error) => {
          console.log(error)
          this.loading = false
        })
    },




    deleteItem (item) {
      if (this.$store.getters.checkPermission('partners-delete')) {
        const index = this.items.indexOf(item)
        Swal.fire({
          title: this.$t('swal_title'),
          text: this.$t('swal_text'),
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: this.$t('swal_delete')
        }).then((result) => {
          if (result.value) {
            axios
              .delete(
                this.$store.state.backend_url + 'api/template/delete/' + item.id
              )
              .then((res) => {
                this.getList(this.page, this.itemsPerPage)
                this.dialog = false
                Swal.fire('Deleted!', this.$t('swal_deleted'), 'success')
              })
              .catch((err) => {
                Swal.fire({
                  icon: 'error',
                  title: this.$t('swal_error_title'),
                  text: this.$t('swal_error_text')
                  // footer: "<a href>Why do I have this issue?</a>"
                })
                console.log(err)
              })
          }
        })
      }
    }
  },
  mounted () {
    this.getList()
  }
}
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

