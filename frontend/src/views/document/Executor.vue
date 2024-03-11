<template>
  <div>
    salom
    {{ items }}
  </div>
</template>
<script>
import Axios from "axios";
export default {
  data() {
    return {
      items: [],
    };
  },
  methods: {
    getList() {
      Axios.get(this.$store.state.backend_url + "api/get-executer")
        .then((res) => {
          console.log(res);
        })
        .catch((err) => {
          console.log(err);
        });
    },
  },
  mounted() {
    this.getList();
  },
};
</script>