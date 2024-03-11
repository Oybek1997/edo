<template>
  <div>
    props: ["drawer"],

  </div>
</template>

<script>
export default {
  data() {
    return {
      links: [
        {
          icon: "mdi-home",
          text: this.$t("message.home"),
          route: "/",
          roles: []
        },
        {
          icon: "mdi-arrow-down",
          text: this.$t("message.create"),
          route: "/create",
          roles: []
        },
        {
          icon: "mdi-arrow-down",
          text: this.$t("message.inbox"),
          route: "/inbox",
          roles: []
        },
        {
          icon: "mdi-email-open",
          text: this.$t("message.draft"),
          route: "/draft",
          roles: []
        },
        {
          icon: "mdi-arrow-up",
          text: this.$t("message.sent"),
          route: "/sent",
          roles: []
        },
        {
          icon: "mdi-account",
          text: this.$t("user.index"),
          route: "/users/list",
          roles: [""]
        },
        {
          icon: "mdi-arrow-up",
          text: this.$t("department.index"),
          route: "/departments/list",
          roles: [""]
        },
       /* {
          icon: "mdi-arrow-up",
          text: this.$t("document.index"),
          route: "/document/list",
          roles: [""]
        },*/
        {
          icon: "mdi-arrow-up",
          text: this.$t("company.index"),
          route: "/companies/list",
          roles: [""]
        },
        {
          icon: "mdi-arrow-up",
          text: this.$t("documentTypes.index"),
          route: "/documentTypes/list",
          roles: [""]
        }
      ]
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 195;
    },
    username(){
      var user = JSON.parse(this.$store.state.user);
      if(user)
        return user.username
      else ''
    }
  },
  methods: {
    logout() {
      this.$store.dispatch("setUser", null);
      this.$store.dispatch("setAccessToken", null);
      this.$router.push("/login");
    }
  }
};
</script>
