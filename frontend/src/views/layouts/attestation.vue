<template>
  <v-app id="inspire">
    <v-app-bar app color="primary" dark>
      <v-app-bar-nav-icon @click.stop="drawer = !drawer" />
      <v-toolbar-title>Workflow Attestation</v-toolbar-title>
      <v-spacer></v-spacer>
      <v-menu
        offset-y
        v-if="user && (user.username == 'test' || user.username == 'ak7948')"
        :close-on-content-click="false"
      >
        <template v-slot:activator="{ on }">
          <v-btn text v-on="on" class="ml-2">
            <v-icon>mdi-apps</v-icon>
          </v-btn>
        </template>
        <!-- <template v-slot:default class="mr-12">
          <v-card width="300">
            <v-item-group>
              <v-container>
                <v-row>
                  <v-col cols="4">
                    <v-item v-slot="{ toggle }">
                      <v-card
                        class="text-center pa-2"
                        height="80"
                        width="80"
                        @click="toggle"
                        flat
                        to="/"
                      >
                        <v-icon color="blue" x-large>mdi-text-box-check-outline</v-icon>EDO
                      </v-card>
                    </v-item>
                  </v-col>
                  <v-col cols="4">
                    <v-item v-slot="{ toggle }">
                      <v-card class="text-center pa-2" height="80" width="80" @click="toggle" flat>
                        <v-icon color="blue" x-large>mdi-account-box-multiple</v-icon>HR
                      </v-card>
                    </v-item>
                  </v-col>
                  <v-col cols="4">
                    <v-item v-slot="{ toggle }">
                      <v-card class="text-center pa-2" height="80" width="80" @click="toggle" flat>
                        <v-icon color="blue" x-large>mdi-card-account-details-outline</v-icon>Tabel
                      </v-card>
                    </v-item>
                  </v-col>
                </v-row>
              </v-container>
            </v-item-group>
          </v-card>
        </template>-->
      </v-menu>
    </v-app-bar>
    <v-navigation-drawer v-model="drawer" app>
      <template>
        <v-card class="mx-auto" max-width="300" tile>
          <v-app-bar dark color="primary">
            <v-toolbar-title class="mx-auto">UzAuto Motors HR</v-toolbar-title>
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
                  <v-list-item-title>{{ $t('message.logout') + ' ('+username+')' }}</v-list-item-title>
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
      <span class="white--text">&copy; {{ year }}</span>
    </v-footer>
  </v-app>
</template>

<script>
import Navigation from "@/components/Navigation";
const axios = require("axios").default;
export default {
  data() {
    return {
      year: new Date().getFullYear(),
      loading: false,
      links: [
        {
          icon: "mdi-home",
          text: this.$t("message.home"),
          route: "/",
          roles: []
        },
        {
          icon: "mdi-account-multiple-plus-outline",
          text: this.$t("attestation.commissions"),
          route: "/commissions",
          roles: []
        },
        {
          icon: "mdi-comment-question-outline",
          text: this.$t("attestation.questions"),
          route: "/questions",
          roles: []
        }
      ],
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

