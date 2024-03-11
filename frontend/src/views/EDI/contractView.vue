<template>
  <div>
    <v-btn color="primary" text small @click="dialog = true">
      {{ data.contract_number }}</v-btn
    >
    <v-dialog
      v-model="dialog"
      @keydown.esc="dialog = false"
      persistent
      max-width="1000px"
    >
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">{{ $t("edi.contract_view") }}</span>
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
              <th class="text-right pa-1">
                <label for>{{ $t("edi.contracts_contract_number") }}</label>
              </th>
              <td class="text-left pa-1">
                {{ data.contract_number }}
              </td>
              <th class="text-right pa-1">
                <label for>{{ $t("edi.contracts_bp_id") }}</label>
              </th>
              <td class="text-left pa-1">
                {{ data.business_partner.name }}
              </td>
            </tr>
            <tr>
              <th class="text-right pa-1">
                <label for>{{ $t("edi.contract_date") }}</label>
              </th>
              <td class="text-left pa-1">
                {{ data.contract_date }}
              </td>
              <th class="text-right pa-1">
                <label for>{{ $t("edi.contracts_total_price") }}</label>
              </th>
              <td class="text-left pa-1">
                {{ data.total_price }}
              </td>
            </tr>
            <tr>
              <th class="text-right pa-1">
                <label for>{{ $t("edi.contracts_title") }}</label>
              </th>
              <td class="text-left pa-1">
                {{ data.title }}
              </td>
              <th class="text-right pa-1">
                <label for>{{ $t("edi.contracts_currency_id") }}</label>
              </th>
              <td class="text-left pa-1">
                {{ data.currency.name }}
              </td>
            </tr>
            <tr>
              <th class="text-right pa-1">
                <label for>{{ $t("edi.contracts_active_from") }}</label>
              </th>
              <td class="text-left pa-1">
                {{ data.active_from }}
              </td>
              <th class="text-right pa-1">
                <label for>{{ $t("edi.contracts_status") }}</label>
              </th>
              <td class="text-left pa-1">
                <v-switch
                  v-model="data.status"
                  readonly
                  hide-details
                  class="my-0"
                ></v-switch>
              </td>
            </tr>
            <tr>
              <th class="text-right pa-1">
                <label for>{{ $t("edi.contracts_active_to") }}</label>
              </th>
              <td class="text-left pa-1">
                {{ data.active_to }}
              </td>
              <th class="text-right pa-1"></th>
              <td class="text-left pa-1"></td>
            </tr>
          </table>

          <h2 class="mt-4">{{ $t("edi.contract_contract_detail_id") }}</h2>
          <table
            style="border-collapse: collapse; border-color: #ddd; width: 100%"
            border="1"
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
            </tr>
            <tr v-for="(i, k) in data.contract_details" :key="k">
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
