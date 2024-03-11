<template>
  <div>
    <v-btn color="primary" text small @click="dialog = true">
      {{ data.order_number }}</v-btn
    >
    <v-dialog
      v-model="dialog"
      @keydown.esc="dialog = false"
      persistent
      max-width="1000px"
    >
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">{{ $t("edi.order_view") }}</span>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="dialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <table
            style="border-collapse: collapse; border-color: #ddd; width: 100%"
            border="0"
            class="ma-2 infoTable"
          >
            <tr>
              <th class="text-right">
                {{ this.$t("edi.orders_contract_id") }} :
              </th>
              <td class="text-left">{{ data.contract.contract_number }}</td>
              <th class="text-right">
                {{ this.$t("edi.orders_order_quantity") }} :
              </th>
              <td class="text-left">{{ data.order_quantity }}</td>
            </tr>
            <tr>
              <th class="text-right">{{ this.$t("edi.order_number") }} :</th>
              <td class="text-left">{{ data.order_number }}</td>
              <th class="text-right">
                {{ this.$t("edi.orders_shipment_quantity") }} :
              </th>
              <td class="text-left">{{ data.shipment_quantity }}</td>
            </tr>
            <tr>
              <th class="text-right">{{ this.$t("edi.order_date") }} :</th>
              <td class="text-left">{{ data.created_at.substring(0, 10) }}</td>
              <th class="text-right">
                {{ this.$t("edi.orders_total_price") }} :
              </th>
              <td class="text-left">{{ data.total_price }}</td>
            </tr>
            <tr>
              <th class="text-right">{{ this.$t("edi.orders_title") }} :</th>
              <td class="text-left">{{ data.title }}</td>
              <th class="text-right">
                {{ this.$t("edi.orders_shipment_price") }} :
              </th>
              <td class="text-left">{{ data.shipment_price }}</td>
            </tr>
          </table>

          <h2 class="mt-4">{{ $t("edi.orders_order_detail_id") }}</h2>
          <table
            style="border-collapse: collapse; border-color: #ddd; width: 100%"
            class="my-2"
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
                {{ $t("edi.order_details_order_start_date") }}
              </th>
              <th class="text-center" rowspan="2">
                {{ $t("edi.order_details_order_finish_date") }}
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
            <tr v-for="(order_detail, k) in data.order_details" :key="k">
              <td class="text-right">{{ k + 1 }}</td>
              <td class="text-right">
                {{
                  order_detail.contract_detail
                    ? order_detail.contract_detail.material.material_number
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
                {{ order_detail.order_start_date }}
              </td>
              <td class="text-right">
                {{ order_detail.order_finish_date }}
              </td>
            </tr>
          </table>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
export default {
  props: ["data"],
  data() {
    return {
      dialog: false,
    };
  },
  mounted() {},
  created() {},
};
</script>
<style scoped>
.infoTable > tr > td,
.infoTable > tr > th {
  width: 200px;
}

table > tr > td,
table > tr > th {
  white-space: normal;
  padding: 5px;
}
</style>
