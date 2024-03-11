<template>
  <div >
    <v-card>
      Salom {{depid}}
    </v-card>
  </div>
</template>

<script>
const axios = require("axios").default;
export default {
  name: "viewDepartment",
  props:{
    depid:''
  },
  data() {
    return {

    };
  },
  methods: {

  },
  mounted() {

  }
};
</script>
<style scoped>

</style>
