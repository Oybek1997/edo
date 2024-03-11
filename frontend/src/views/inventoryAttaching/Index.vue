<template>
  <div class="fullHeight">
    <v-card
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{ $t("Yangi Guruh biriktirish") }}</span>
      </v-card-title>
      <v-card-text
        class="pb-1"
        v-if="$store.getters.checkRole('inventory_operator')"
      >
        <v-form ref="inventorCreateForm">
          <v-row class="ma-0">
            <v-col class="pa-1 inventory_input" cols="6">
              <v-text-field
                :label="$t('Komissiya ')"
                v-model="commission"
                hide-details
                outlined
                dense
              ></v-text-field>
            </v-col>

            <v-col class="pa-1 inventory_input" cols="6">
              <v-text-field
                :label="$t('Harf')"
                v-model="letter"
                hide-details
                outlined
                dense
              ></v-text-field>
            </v-col>
            <v-col class="pa-1" cols="12">
              <v-btn color="#3FCB5D"
                    right
                    small
                    dark
                    elevation="0"
                    style="text-transform: none; border-radius: 5px; padding: 5px 20px"
                    @click="save()">Saqlash</v-btn>
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
      letter: '',
      loading: false,
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
        {
          text: this.$t('Komissiya'),
          value: 'warehouse.name',
          sortable: false
        },
        {
          text: this.$t('Harf'),
          value: 'commission.commission_number',
          sortable: false
        }
      ]
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
        .post(this.$store.state.backend_url + 'api/attaching', {
          pagination: this.dataTableOptions,
          filter: this.filter,
          type: 0
        })
        .then((res) => {
          this.items = res.data.data
          this.from = res.data.from
          this.server_items_length = res.data.total
          this.loading = false
        })
        .catch((err) => {
          console.log(err)
          this.loading = false
        })
    },
    editItem (item) {
      this.changeProductsCount(1)
      this.formInv.id = item.id
      this.formInv.product_count = item.id
      this.getProduct()
    },
    save () {
      let res;
      console.log(this.commission)
      if (this.$refs.inventorCreateForm.validate()) {
        this.loading = true
        axios
          .post(
            this.$store.state.backend_url + 'api/inventory/attaching',
            { "commission": this.commission, "letter": this.letter }
          )

          .then((response) => {
            var result = response.data.commissionStatus
            // console.log(result)

            if (result === 0) {
              Swal.fire({
                title: 'Bu Komissiya Mavjud'
              })
            } else if (result === 1) {
              Swal.fire({
                title: 'Komissiya Muvaffaqiyatli Yartildi'
              })
            } else if (result === 500) {
              Swal.fire({
                title: 'Yordamchi Komissiyani yaratish uchun Avval Shu raqamli Komissiyani Yarating'
              })
            }
          })

          .then((res) => {
            if (res.data.code == 500) {
              Swal.fire({
                icon: 'error',
                title: res.data.message
              })
            }
          })
          .catch((err) => {
            console.log(err)
            this.loading = false
          })
      }
      console.log(this.formInv)
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
    },
    changeAddress (id) {
      const address = this.inventoryAddresses.filter((v) => {
        if (v.value == id) return v
      })
      this.inventoryAddresses = this.inventoryAddresses.filter((v) => {
        if (v.value != id) return v
      })
      this.inventoryAddresses = address.concat(this.inventoryAddresses)

      console.log(address)
    },
    changeStatusItem (item) {
      Swal.fire({
        title: 'Blanka qatorini bekor qilish?',
        text: "Ushbu Blanka raqamiga tegishli tanlangan qator ma'lumotlari bekor qilinadi.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ha!',
        cancelButtonText: "Yo'q!"
      }).then((result) => {
        if (result.isConfirmed) {
          axios
            .post(
              this.$store.state.backend_url + 'api/inventory/change-status',
              {
                id: item.id
              }
            )
            .then((res) => {
              if (res.data == 200) {
                Swal.fire('Bekor qilindi!', '', 'success')
                this.getList()
              } else {
                Swal.fire("Ma'lumot topilmadi!", '', 'error')
              }
            })
            .catch((err) => {
              Swal.fire('Xatolik!', 'Serverda hatolik bor.', 'error')
            })
        }
      })
    }
  },
  watch: {
    $route (to, from) {
      this.status = to.params.status
    }
  },
  mounted () {
    this.status = this.$route.params.status
    this.getList()
  }
}
</script>
<style scoped>
/* .v-content .v-card.v-sheet.theme--light {
  background-color: #ffffff !important;
  border-left: 5px solid red !important;
}

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
} */

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
