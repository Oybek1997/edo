<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>Reports</span>
        <v-spacer></v-spacer>

        <v-text-field
          v-model="search"
          append-icon="mdi-magnify"
          class="mr-2"
          style="width: 100px !important"
          :placeholder="$t('search')"
          @keyup.native.enter="getList"
          outlined
          dense
          single-line
          hide-details
        ></v-text-field>
      </v-card-title>
      <v-simple-table dense :height="screenHeight">
        <template v-slot:default>
          <tbody v-for="(department, i) in getDepartments" :key="i">
            <tr v-if="department.show">
              <td class="th-text-code" @click="parent_id = department.id">
                {{ department.department_code }}
              </td>
              <td class="max-width--150 text-ellipsis-two--line">
                {{ department.name_ru }}
              </td>
              <td>
                <table>
                  <tr v-for="(staff, ind) in department.staff" :key="ind">
                    <td class="max-width--200 text-ellipsis-two--line">
                      {{ staff.position.name_ru }}
                    </td>
                    <td>
                      <table>
                        <tr
                          v-for="(employee, index) in staff.employees"
                          :key="index"
                        >
                          <td>{{ employee.firstname_uz_cyril }}</td>
                        </tr>
                      </table>
                    </td>
                    <td class="max-width--30">{{ staff.rate_count }}</td>
                    <td
                      class="max-width--30"
                      v-if="staff.employees.length == staff.rate_count"
                    >
                      {{ staff.rate_count }}
                    </td>
                    <td
                      class="max-width--50"
                      v-else-if="staff.rate_count > staff.employees.length"
                    >
                      {{ staff.rate_count - staff.employees.length }} ta
                      vakansiya
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>
    </v-card>
    <v-dialog v-model="loading" width="300" hide-overlay>
      <v-card color="primary" dark>
        <v-card-text>
          {{ $t("loadingText") }}
          <v-progress-linear
            indeterminate
            color="white"
            class="mb-0"
          ></v-progress-linear>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
const axios = require("axios").default;
import Swal from "sweetalert2";
export default {
  data() {
    return {
      loading: false,
      search: "",
      departments: [],
      parent_id: null,
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 180;
    },
    getDepartments() {
      if (this.parent_id) {
        this.departments.map((v) => {
          if (v.parent_id == this.parent_id) {
            v.show = true;
          }
        });
      }
      return this.departments;
    },
  },
  methods: {
    getList() {
      this.loading = true;
      axios
        .get(this.$store.state.backend_url + "api/reports")
        .then((response) => {
          this.departments = response.data;
          // this.staffs = response.data;
          this.departments.map((v) => {
            if (!v.parent_id) {
              v.show = true;
            } else {
              v.show = false;
            }
          });
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getDepartmentList(id) {
      this.departments.map((v) => {
        if (v.parent_id == id) {
          v.show = true;
        }
      });
    },
  },
  mounted() {
    this.getList();
  },
  created() {},
};
</script>
<style scoped>
.max-width--30 {
  max-width: 30px;
}
.max-width--50 {
  max-width: 50px;
}
.max-width--80 {
  max-width: 80px;
}
.max-width--200 {
  max-width: 200px;
}
.max-width--300 {
  max-width: 300px;
}
.text-ellipsis-two--line {
  white-space: normal;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>
