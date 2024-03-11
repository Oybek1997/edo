<template>
  <div class="fullHeight">
    <v-card class="heightFull">
      <template>
        <v-parallax
          dark
          height="200"
          src="https://cdn.vuetifyjs.com/images/backgrounds/vbanner.jpg"
        >
        </v-parallax>
        <!--  -->
        <v-col cols="6" offset="3">
  <v-card style="height: 700px" class="headerSearch">
    <v-card-title class="text-h5">IT LIVE</v-card-title>
    <v-card-subtitle>
      Welcome! You can raise a request for IT LIVE using the options provided
    </v-card-subtitle>
    <v-card-title class="text-h9">What can we help you with?</v-card-title>
    <v-card-subtitle>
      <v-list-item v-for="(item, i) in itemMessages" :key="i">
        <v-list-item-icon>
          <v-icon>{{ item.icon }}</v-icon>
        </v-list-item-icon>
        <v-list-item-content @click="$router.push(item.path)" style="cursor: pointer;">
          <div style="color: blue">{{ item.value }}*</div>
          <div>{{ item.text }}</div>
        </v-list-item-content>
      </v-list-item>
    </v-card-subtitle>
  </v-card>
</v-col>



        <!--  -->
      </template>
    </v-card>
  </div>
</template>
<script>
export default {
  data: () => ({
    items: [
      {
        value: "Demo service project",
        text:
          "Welcome to Demo EDO service project`s portal!\nTry the example forms(we call them Reques types)to practice requesting he...",
        id: 0,
        path: "/staffs/employees/vacancy",
      },
      {
        value: "IT LIVE",
        text: "Welcome! You can raise a request for IT LIVE using the options provided",
        id: 1,
        path: "/staffs/employees/candidates-index",
      },
      { value: "OTHER...", id: 2, path: "/staffs/employees/candidates/20" },
    ],
    itemMessages:[
      {
        id: 0,
        value: "Submit a request or incident",
        text:"Whitsunday Island, Whitsunday Islands",
        path: "/support/message/1",
        icon:'mdi-web'
      },
      {
        id: 1,
        value: "Ask a question",
        text:"Have aquestion? Submit it here",
        path: "/support/message/2",
        icon:'mdi-message-text-outline'
      },
    ],
  }),
  computed: {
    screenHeight() {
      return window.innerHeight - 180;
    },
    headers() {
      return [];
    },
  },
  methods: {},
  mounted() {},
};
</script>

<style scoped>
.fullHeight {
  height: calc(100% - 0px);
}
.heightFull {
  height: 100%;
  background: #fff;
}
.headerTitle {
  width: 100%;
  color: #000;
  font-size: 18px;
  line-height: 1.4;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.headerSearch {
  width: 100%;
  margin: -150px 0px 0px 0px;
  /* border:1px solid red; */
}
.txt_ss {
  /* border: 1px solid #e6e6e6; */
  box-shadow: none;
  max-height: 100%;
  overflow: hidden;
  border-radius: 5px 0px 0px 5px;
  font-size: 16px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.txt_search1 {
  border: 1px solid #e6e6e6;
  box-shadow: none;
  max-height: 100%;
  overflow: hidden;
  border-radius: 5px 0px 0px 5px;
  color: #212529;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
</style>
