<template>
  <div id="app">
    <v-app>
      <div>
        <v-app-bar v-show="myApp" color="deep-purple accent-4" dense dark>
          <v-app-bar-nav-icon></v-app-bar-nav-icon>

          <v-toolbar-title>Page title</v-toolbar-title>

          <v-spacer></v-spacer>

          <v-btn icon>
            <v-icon>mdi-magnify</v-icon>
          </v-btn>

          <v-menu left bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-btn icon v-bind="attrs" v-on="on">
                <v-icon>mdi-dots-vertical</v-icon>
              </v-btn>
            </template>
          </v-menu>
        </v-app-bar>
        <div
          style="
            margin-top: 0px;
            border: 1px solid red;
            position: fixed;
            width: 100%;
            top: 0px;
            height: 50px;
          "
          v-on:mouseover="mouseover"
          v-on:mouseleave="mouseleave"
        ></div>
      </div>
    </v-app>
  </div>
</template>

<script>
// const axios = require("axios").default;
// import Swal from "sweetalert2";
export default {
  data() {
    return {
      myApp: false,
    };
  },
  computed: {
    // screenHeight() {
    //   return window.innerHeight - 175;
    // },
    headers() {
      return [];
    },
  },
  methods: {
    mouseover() {
      this.myApp = true;
    },
    mouseleave() {
      this.myApp = false;
    },
  },

  mounted() {},
};
</script>
