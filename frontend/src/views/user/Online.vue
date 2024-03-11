<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ $t("user.online") }}</span>
        <v-spacer></v-spacer>
      </v-card-title>
      <v-data-table
        dense
        fixed-header
        :height="screenHeight"
        :loading="loading"
        :headers="headers"
        :items="users"
        :footer-props="{
          itemsPerPageOptions: [50, 100, 200, -1],
          itemsPerPageAllText: $t('itemsPerPageAllText'),
          itemsPerPageText: $t('itemsPerPageText'),
          showFirstLastPage: true,
          firstIcon: 'mdi-arrow-collapse-left',
          lastIcon: 'mdi-arrow-collapse-right',
          prevIcon: 'mdi-arrow-left',
          nextIcon: 'mdi-arrow-right',
        }"
      >
        <template v-slot:item.id="{ item }">
          {{
            users
              .map(function (x) {
                return x.id;
              })
              .indexOf(item.id) + 1
          }}
        </template>
        <template v-slot:item.employee="{ item }">
          {{
            item.employee["firstname_" + language] +
            " " +
            item.employee["lastname_" + language] +
            " " +
            item.employee["middlename_" + language]
          }}
        </template>
      </v-data-table>
    </v-card>
  </div>
</template>
<script>
const axios = require("axios").default;
export default {
  data() {
    return {
      loading: false,
      users: [],
      dialogHeaderText: "",
      dataTableOptions: { page: 1, itemsPerPage: 20 },
      page: 1,
      from: 0,
      server_items_length: -1,
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    headers() {
      return [
        { text: "#", value: "id", align: "center", width: 50, sortable: false },
        { text: this.$t("user.employee"), value: "employee" },
        { text: this.$t("user.online_at"), value: "online_at" },
      ];
    },
    language() {
      return this.$i18n.locale == "ru" ? "uz_cyril" : this.$i18n.locale;
    },
  },
  methods: {
    getOnline() {
      axios
        .get(this.$store.state.backend_url + "api/users/online")
        .then((res) => {
          console.log(res.data);
          this.users = res.data;
        })
        .catch((err) => {
          console.log(err);
        });
      this.signatories = false;
    },
  },
  mounted() {
    this.getOnline();
  },
};
</script>
