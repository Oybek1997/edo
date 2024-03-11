<template>
  <div class="fullHeight">
    <v-card class="pa-5">
      <v-card-title class="pa-0" primary-title>
        <span class="dialogTitle">{{ $t("Userga Templateni biriktirish") }}</span>
      </v-card-title>
      <v-divider color="#DCE5EF" class="my-1"></v-divider>
      <v-card-text
        class="px-0 pt-3 pb-0"
        v-if="$store.getters.checkRole('superadministrator')"
      >
        <v-form ref="inventorCreateForm">
          <v-row class="mx-0 dialogForm">
            <v-col class="px-1 py-0 mb-3" cols="6">
              <v-autocomplete
                v-model="selectedUser"
                :items="usersList"
                item-value="id"
                item-text="username"
                outlined
                dense
                label="User"
              ></v-autocomplete>
            </v-col>
            <v-col class="px-1 py-0 mb-3" cols="6">
              <v-autocomplete
                v-model="selectedTemplate"
                :items="templatesList"
                outlined
                dense
                chips
                item-value="id"
                item-text="name_uz_latin"
                small-chips
                label="Template"
                multiple
              ></v-autocomplete>
            </v-col>
            <v-col class="pa-0" cols="12" style="display: flex; justify-content: end;">
              <v-btn
                class="mr-3"
                color="#3FCB5D"
                small
                dark
                @click="save"
                elevation="0"
                style="text-transform: none; border-radius: 5px; padding: 5px 20px"
              >
                {{ $t("save") }}
              </v-btn>
              <v-btn
                class=""
                color="red"
                small
                dark
                @click="dialog = false"
                elevation="0"
                style="text-transform: none; border-radius: 5px; padding: 5px 20px"
              >
                {{ $t("Отменить") }}
              </v-btn>
            </v-col>
          </v-row>
        </v-form>
      </v-card-text>
    </v-card>
  </div>
</template>
<script>
import Swal from 'sweetalert2'
const axios = require('axios').default
export default {
  data () {
    return {
      commission: '',
      selectedTemplate: [],
      selectedUser: [],
      templatesList: {},
      usersList: {},
      letter: '',
      loading: false
    }
  },
  computed: {
    newAddressess () {
      const arr = [
        {
          text: 'Xato adres',
          value: 27779
        }
      ]
      return arr.concat(
        this.selectedProduct ? this.selectedProduct.addresses : []
      )
    },
    screenHeight () {
      return window.innerHeight - 250
    },
    headers () {
      return [
        { text: '#', value: 'id', align: 'center', width: 30, sortable: false },
        { text: this.$t('tr'), value: 'id', sortable: false },
        { text: this.$t('User Name'), value: 'user.name', sortable: false },
        { text: this.$t('Template Name'), value: 'template.name', sortable: false },
        {
          text: 'Amallar',
          align: 'center',
          value: 'options',
          sortable: false,
          width: 80
        }
      ]
    }
  },
  methods: {
    getMainData () {
      // console.log('salom')
      this.loading = true
      axios
        .get(this.$store.state.backend_url + 'api/getMainData')
        .then((res) => {
          this.templatesList = res.data.documenttemplates
          this.usersList = res.data.users
          // console.log(this.documenttemplatesList);
          this.loading = false
        })
        .catch(function (error) {
          console.log(error)
          this.loading = false
        })
    },

    updatePage ($event) {
      this.getList()
    },
    updatePerPage ($event) {
      this.getList()
    },
    editItem (item) {
      this.changeProductsCount(1)
      this.formInv.id = item.id
      this.formInv.product_count = item.id
      this.getProduct()
    },
    save () {
      axios
        .post(this.$store.state.backend_url + 'api/templateUser/add', {
          documenttemplates: this.selectedTemplate,
          users: this.selectedUser
        })
        .then(() => {
          this.AttachUserModal = false
          const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            onOpen: (toast) => {
              toast.addEventListener('mouseenter', Swal.stopTimer)
              toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
          })
          Toast.fire({
            icon: 'success',
            title: this.$t('create_update_operation')
          })
        })
    },
    changeProductsCount (count) {
      this.$refs.inventorCreateForm.resetValidation()
      this.formInv.products = []
      for (let i = 0; i < count; i++) {
        this.formInv.products.push({
          real_stock: null,
          inventory_address_id: null
        })
      }
    }
  },
  // watch: {
  //   $route (to, from) {
  //     this.status = to.params.status
  //   }
  // },
  mounted () {
    this.getMainData()
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

