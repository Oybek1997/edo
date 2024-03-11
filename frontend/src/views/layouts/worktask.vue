<template>
  <v-app id="inspire">
    <v-app-bar
      app
      style="background: radial-gradient(circle, #35a2ff 0%, #014a88 100%)"
      dark
    >
      <v-app-bar-nav-icon @click.stop="drawer = !drawer" />
      <v-toolbar-title>Task Management</v-toolbar-title>
      <v-spacer></v-spacer>

      <v-btn color="danger" outlined small to="/">
        <v-icon class="mr-2">mdi-logout</v-icon>EDO
      </v-btn>
    </v-app-bar>
    <v-navigation-drawer v-model="drawer" app>
      <template>
        <v-card class="mx-auto" max-width="300" tile>
          <v-app-bar dark style="background: #014a88">
            <v-toolbar-title class="mx-auto">UzAuto Motors</v-toolbar-title>
          </v-app-bar>
          <v-list dense>
            <v-list-item-group color="rgb(63, 81, 181)">
              <v-list-item
                v-for="(item, i) in links.filter((v) => v.visible)"
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
import Navigation from "@/components/Navigation";
const axios = require("axios").default;
export default {
  data() {
    return {
      deviceBranches: [],
      device_branch_id: null,
      year: new Date().getFullYear(),
      loading: false,
      drawer: false,
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
          icon: "mdi-calendar-check",
          text: this.$t("Barcha topshiriqlar"),
          route: "/worktask/index",
          visible: true,
        },
      ];
    },
  },
  methods: {
    // changeDeviceBranch(item) {
    //   this.device_branch_id = item.id;
    //   window.localStorage.setItem("device_branch_id", this.device_branch_id);
    //   location.reload();
    // },
    // getBranches() {
    //   axios
    //     .get(this.$store.state.backend_url + "api/orgtex/device-branches/all")
    //     .then(res => {
    //       this.deviceBranches = res.data;
    //       if (!this.device_branch_id && res.data[0]) {
    //         this.device_branch_id = res.data[0].id;
    //         window.localStorage.setItem("device_branch_id", res.data[0].id);
    //         console.log(this.device_branch_id);
    //       }
    //       Swal.fire({
    //         position: "top-end",
    //         icon: "success",
    //         title: "Your E-IMZO has been saved",
    //         showConfirmButton: false,
    //         timer: 1500
    //       });
    //     })
    //     .catch(err => {});
    // }
  },
  mounted() {
    // this.device_branch_id = window.localStorage.getItem("device_branch_id");
    // this.getBranches();
  },
};
</script>
