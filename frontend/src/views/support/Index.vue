<template>
  <div class="fullHeight">
    <v-card class="heightFull">
      <template>
        <v-parallax
          dark
          height="200"
          src="https://cdn.vuetifyjs.com/images/backgrounds/vbanner.jpg"
        >
          <div class="headerSearch d-flex align-center">
            <v-row>
              <v-col cols="4" offset="4">
                <span class="font-weight-thin txt_ss">Welcome to the Help Center</span>
                <v-text-field
                  append-icon="mdi-magnify"
                  :placeholder="$t('search')"
                  dense
                  hide-details
                  solo
                ></v-text-field>
              </v-col>
            </v-row>
          </div>
        </v-parallax>
        <!--  -->
        <v-container>
          <v-row>
            <v-col cols="3" v-for="(item, i) in items" :key="i" >
              <v-card style="height: 150px" :to="item.path">
                <v-card-title> {{ item.value }}</v-card-title>
                <v-card-subtitle>{{ item.text }}</v-card-subtitle>
                <!-- <v-card-actions>
                  <v-btn text>{{ item.id }}</v-btn>
                </v-card-actions> -->
              </v-card>
            </v-col>
          </v-row>
        </v-container>
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
        path: '/staffs/employees/vacancy',
      },
      {
        value: "IT LIVE",
        text: "Welcome! You can raise a request for IT LIVE using the options provided",
        id: 1,
        path: '/support/itlife',
      },
      { value: "OTHER...", id: 2 ,path: '/staffs/employees/candidates/20',},
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
