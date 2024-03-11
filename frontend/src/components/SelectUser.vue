<template>
  <div>
    <v-autocomplete
      :items="items"
      v-model="user_id"
      @update:search-input="getList($event)"
      no-filter
      clearable
      dense
      outlined
      hide-details="auto"
      full-width
      :placeholder="$t('Поиск...')"
      append-icon="mdi-account-plus-outline"
    >
      <!-- Make the v-icon clickable -->
      <template v-slot:append>
        <v-icon @click="addUser">mdi-account-plus-outline</v-icon>
      </template>
    </v-autocomplete>
  </div>
</template>

<script>
const axios = require("axios").default;

export default {
  props: ["value"],
  data: () => ({
    items: [],
  }),
  computed: {
    user_id: {
      get() {
        return this.value;
      },
      set(val) {
        this.$emit("input", val);
      },
    },
  },
  methods: {
    getList(search) {
      this.searchValue = search; // store the search value
      axios
        .post(this.$store.state.backend_url + "api/components/chat-users", {
          search: search,
        })
        .then((response) => {
          this.items = response.data;
          // console.log("Adding user...",search);
        })
        .catch((error) => {
          console.log(error);
        });
    },
    addUser() {
      axios
        .post(this.$store.state.backend_url + "api/create-conversation", {
          search: this.searchValue,
        })
        .then((response) => {
          this.items = response.data;
          console.log("Adding user...", this.searchValue);
        })
        .catch((error) => {
          console.log(error);
        });


    },
  },
  mounted() {
    this.getList(null);
  },
};
</script>
