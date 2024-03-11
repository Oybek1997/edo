<script src="../../../../backend/webpack.mix.js"></script>
<template>
  <div>
    <v-row class="mx-2 mt-1">
      <span>{{ $t('document.sent') }}</span>
      <v-spacer></v-spacer>
      <v-btn color="success" small fab class="mr-4 mt-2">
        <v-icon>mdi-plus</v-icon>
      </v-btn>
    </v-row>
  </div>
</template>

<script>
const axios = require("axios").default;
export default {
  data: () => ({
    loading: false,})
};
</script>


