<template>
  <v-app id="inspire">
    <v-app-bar
      app
      style="background: radial-gradient(circle, #35a2ff 0%, #014a88 100%);"
      dark
    >
      <v-app-bar-nav-icon @click.stop="drawer = !drawer" />
      <v-toolbar-title>Med Punkt</v-toolbar-title>
      <v-spacer></v-spacer>

      <v-btn color="danger" outlined small to="/">
        <v-icon class="mr-2">mdi-logout</v-icon>EDO
      </v-btn>
    </v-app-bar>
    <v-navigation-drawer v-model="drawer" app>
      <template>
        <v-card class="mx-auto" max-width="300" tile>
          <v-app-bar dark style="background: #014a88;">
            <v-toolbar-title class="mx-auto">UzAuto Motors</v-toolbar-title>
          </v-app-bar>
          <v-list dense>
            <v-list-item-group color="rgb(63, 81, 181)">
              <v-list-item
                v-for="(item, i) in links.filter(v => v.visible)"
                :key="i"
                router
                :to="item.route"
              >
                <v-list-item-icon>
                  <v-icon v-text="item.icon"></v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title v-text="item.text"></v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list-item-group>
          </v-list>
        </v-card>
      </template>
    </v-navigation-drawer>
    <v-main>
      <router-view></router-view>
    </v-main>
  </v-app>
</template>

<script>
// import Navigation from "@/components/Navigation";
const axios = require("axios").default;
export default {
  data() {
    return {
      year: new Date().getFullYear(),
      loading: false,
      drawer: true
    };
  },
  computed: {
    user() {
      return this.$store.getters.getUser();
    },
    screenHeight() {
      return window.innerHeight - 170;
    },
    username() {
      var user = this.$store.getters.getUser();
      if (user) return user.username;
      else "";
    },
    links() {
      return [
        {
          icon: "mdi-briefcase-account-outline",
          text: this.$t("Medpunkt"),
          route: "/medpunkt",
          visible: true
        },
        {
          icon: "mdi-briefcase-account-outline",
          text: this.$t("Код диагностикаси"),
          route: "/medpunkt/diagnosis-codes",
          visible: true
        },
        {
          icon: "mdi-briefcase-account-outline",
          text: this.$t("МКБХ"),
          route: "/medpunkt/hospital-diagnosis",
          visible: true
        },
        {
          icon: "mdi-briefcase-account-outline",
          text: this.$t("Касаллик варақаси"),
          route: "/medpunkt/registration-period-illness",
          visible: true
        },
        {
          icon: "mdi-briefcase-account-outline",
          text: this.$t("Дорилар"),
          route: "/medpunkt/medicines",
          visible: true
        }, 
        {
          icon: "mdi-briefcase-account-outline",
          text: this.$t("Қабул"),
          route: "/medpunkt/registration-patients",
          visible: true
        },
        {
          icon: "mdi-briefcase-account-outline",
          text: this.$t("Пархез таомлар"),
          route: "/medpunkt/diet-foods",
          visible: true
        },
      ];
    }
  },
  methods: {
  },
  mounted() {
  }
};
</script>
