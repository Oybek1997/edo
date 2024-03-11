<template>
  <v-app id="inspire">
    <v-app-bar app color="primary" dark>
      <v-app-bar-nav-icon @click.stop="drawer = !drawer" />
      <v-toolbar-title>Workflow Admin Panel</v-toolbar-title>
      <v-spacer></v-spacer>
    </v-app-bar>
    <v-navigation-drawer v-model="drawer" app>
      <template>
        <v-card class="mx-auto" max-width="300" tile>
          <v-app-bar dark color="primary">
            <v-toolbar-title class="mx-auto">UzAuto Motors</v-toolbar-title>
          </v-app-bar>
          <v-list dense>
            <v-subheader>{{$t('message.menu')}}</v-subheader>
            <v-list-item-group color="rgb(63, 81, 181)">
              <v-list-item v-for="(item, i) in links" :key="i" router :to="item.route">
                <v-list-item-icon>
                  <v-icon v-text="item.icon"></v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title v-text="item.text"></v-list-item-title>
                </v-list-item-content>
              </v-list-item>
              <v-divider></v-divider>
              <v-list-item @click="logout">
                <v-list-item-icon>
                  <v-icon color="red">mdi-logout</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>{{ $t('message.logout') + '('+username+')' }}</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list-item-group>
          </v-list>
        </v-card>
      </template>
    </v-navigation-drawer>
    <v-content>
      <router-view></router-view>
    </v-content>

    <v-footer color="indigo" app>
      <span class="white--text">&copy; 2019</span>
    </v-footer>
  </v-app>
</template>

<script>
import Navigation from "@/components/Navigation";
const axios = require("axios").default;
export default {
  data() {
    return { loading:false,
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
      ],
      drawer: true
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    username() {
      var user = this.$store.getters.getUser();
      if (user) return user.username;
      else "";
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

