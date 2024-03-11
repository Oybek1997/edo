<template>
  <div class="fullHeight">
    <div class="profile_header">
      <v-list-item class="pa-7" two-line style="background-color: rgba(0, 0, 0, 0.27);">
        <v-list-item-avatar width="96" height="96">
          <img src="../assets/User-Default.jpg" />
        </v-list-item-avatar>

        <v-list-item-content>
          <v-list-item-title
            v-if="$i18n.locale=='uz_latin'"
            class="profile_name mb-1"
          >{{ employee.employee && employee.employee.lastname_uz_latin }} {{ employee.employee && employee.employee.firstname_uz_latin }} {{ employee.employee && employee.employee.middlename_uz_latin }}</v-list-item-title>
          <v-list-item-title
            v-else
            class="profile_name mb-1"
          >{{ employee.employee && employee.employee.firstname_uz_cyril }} {{ employee.employee && employee.employee.lastname_uz_cyril }} {{ employee.employee && employee.employee.middlename_uz_cyril }}</v-list-item-title>
          <v-list-item-subtitle class="profile_staff">
            <span class="mr-4">{{ staff.position && staff.position["name_"+ $i18n.locale] }}</span>
            <span>
              <v-icon small>mdi-map-marker</v-icon>London, UK
            </span>
          </v-list-item-subtitle>
          <v-card-text class="px-0">
            <v-btn class="mx-1" fab dark x-small color="#354759">
              <v-icon small dark>mdi-telegram</v-icon>
            </v-btn>
            <v-btn class="mx-1" fab dark x-small color="#354759">
              <v-icon small dark>mdi-facebook-messenger</v-icon>
            </v-btn>
            <v-btn class="mx-1" fab dark x-small color="#354759">
              <v-icon small dark>mdi-instagram</v-icon>
            </v-btn>
            <v-btn class="mx-1" fab dark x-small color="#354759">
              <v-icon small dark>mdi-twitter</v-icon>
            </v-btn>
          </v-card-text>
          <v-btn small color="#0cc2aa" dark style="max-width: fit-content;">Follow</v-btn>
        </v-list-item-content>
      </v-list-item>
      <!-- <v-row>
        <v-col sm="6" class="mt-12">
        </v-col>
        <v-col sm="6" class="mt-12">
        </v-col>
      </v-row>-->
    </div>
    <v-row class="mx-0">
      <v-col md="12">
        <v-tabs v-model="tab" background-color="transparent" color="basil" grow>
          <v-tab v-for="item in items" :key="item">{{ item }}</v-tab>
        </v-tabs>

        <v-tabs-items v-model="tab">
          <v-tab-item v-for="item in items" :key="item">
            <v-card color="basil" flat>
              <v-card-text>
                <v-row class="ma-0">
                  <v-col cols="12" sm="6">
                    <v-card>
                      <v-card-title>Telefon</v-card-title>
                      <v-card-subtitle>+99891-288-55-05</v-card-subtitle>
                    </v-card>
                  </v-col>
                  <v-col cols="12" sm="6">
                    <v-card>
                      <v-card-title>Telefon</v-card-title>
                      <v-card-subtitle>+99891-288-55-05</v-card-subtitle>
                    </v-card>
                  </v-col>
                  <v-col cols="12" sm="6">
                    <v-card>
                      <v-card-title>Telefon</v-card-title>
                      <v-card-subtitle>+99891-288-55-05</v-card-subtitle>
                    </v-card>
                  </v-col>
                  <v-col cols="12" sm="6">
                    <v-card>
                      <v-card-title>Telefon</v-card-title>
                      <v-card-subtitle>+99891-288-55-05</v-card-subtitle>
                    </v-card>
                  </v-col>
                  <v-col cols="12" sm="6">
                    <v-card>
                      <v-card-title>Telefon</v-card-title>
                      <v-card-subtitle>+99891-288-55-05</v-card-subtitle>
                    </v-card>
                  </v-col>
                  <v-col cols="12" sm="6">
                    <v-card>
                      <v-card-title>Telefon</v-card-title>
                      <v-card-subtitle>+99891-288-55-05</v-card-subtitle>
                    </v-card>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>
          </v-tab-item>
        </v-tabs-items>
      </v-col>
      <v-col md="7" sm="12">
        <v-row class="ma-0">
          <v-col cols="12" sm="6">
            <v-card>
              <v-card-title>Telefon</v-card-title>
              <v-card-subtitle>+99891-288-55-05</v-card-subtitle>
            </v-card>
          </v-col>
          <v-col cols="12" sm="6">
            <v-card>
              <v-card-title>Telefon</v-card-title>
              <v-card-subtitle>+99891-288-55-05</v-card-subtitle>
            </v-card>
          </v-col>
          <v-col cols="12" sm="6">
            <v-card>
              <v-card-title>Telefon</v-card-title>
              <v-card-subtitle>+99891-288-55-05</v-card-subtitle>
            </v-card>
          </v-col>
          <v-col cols="12" sm="6">
            <v-card>
              <v-card-title>Telefon</v-card-title>
              <v-card-subtitle>+99891-288-55-05</v-card-subtitle>
            </v-card>
          </v-col>
          <v-col cols="12" sm="6">
            <v-card>
              <v-card-title>Telefon</v-card-title>
              <v-card-subtitle>+99891-288-55-05</v-card-subtitle>
            </v-card>
          </v-col>
          <v-col cols="12" sm="6">
            <v-card>
              <v-card-title>Telefon</v-card-title>
              <v-card-subtitle>+99891-288-55-05</v-card-subtitle>
            </v-card>
          </v-col>
        </v-row>
      </v-col>
      <v-col md="5" sm="12" class>
        <template>
          <v-timeline>
            <v-timeline-item v-for="n in 2" :key="n" small>
              <template v-slot:icon>
                <v-avatar>
                  <img src="../assets/User-Default.jpg" />
                </v-avatar>
              </template>
              <template v-slot:opposite>
                <span>Tus eu perfecto</span>
              </template>
              <v-card class="elevation-2">
                <v-card-title class="headline">Lorem ipsum</v-card-title>
                <v-card-text>Commune scaevola imperdiet nec ut, sed euismod convenire principes at.</v-card-text>
              </v-card>
            </v-timeline-item>
          </v-timeline>
        </template>
      </v-col>
    </v-row>
  </div>
</template>
<script>
const axios = require("axios").default;
export default {
  name: "Profile",
  data() {
    return {
      employee: {},
      staff: [],
      roles: [],
      loading: false,
      tab: null,
      items: ["Appetizers", "Entrees", "Deserts", "Cocktails"]
    };
  },
  methods: {
    getUser() {
      axios
        .get(this.$store.state.backend_url + "api/users/show")
        .then(res => {
          this.employee = res.data;
          this.staff = res.data.employee.staff[0];
          console.log(res.data.employee.staff[0]);
          this.roles = res.data.roles;
        })
        .catch(e => {
          console.error(e);
        });
    }
  },
  mounted() {
    axios
      .get(
        this.$store.state.backend_url +
          "api/employees/get-pdf/{employee_id}/{locale}"
      )
      .then(res => {
        this.employee = res.data;
        this.staff = res.data.employee.staff[0];
        console.log(res.data.employee.staff[0]);
        this.roles = res.data.roles;
      })
      .catch(e => {
        console.error(e);
      });
    this.getUser();
  }
};
</script>
<style scoped>
.profile_header {
  background-image: url("../assets/User-Default.jpg");
  background-repeat: no-repeat;
  background-size: cover;
  background-position: center;
  /* background-color: rgba(0, 0, 0, 0.5); */
}
.profile_name {
  color: #fff;
  font-size: 28px;
  line-height: 1.1;
  font-weight: 500;
  font-family: "Roboto", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.profile_staff {
  color: #ffffde !important;
  opacity: 0.6;
  font-size: 14px;
  font-weight: 500;
  font-family: "Roboto", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
/* .fullHeight {
  height: calc(100% - 0px);
}
.heightFull {
  height: 100%;
}
.fontSize35 {
  font-size: 30px;
} */
</style>