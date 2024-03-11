<template>
  <div>
    <v-card>
      <v-card-title class="pa-1">
        <span>{{ $t("employee.index") }}</span>
        <v-spacer></v-spacer>
      </v-card-title>
      <!-- <v-simple-table>
        <template v-slot:default>
          <thead>
            <tr>
              <th rowspan="2" class="text-center">
                {{ $t("department.index") }}
              </th>
              <th rowspan="2" class="text-center">
                {{ $t("staff.employees_count") }}
              </th>
              <th colspan="2" style="text-align: center">
                {{ $t("department.management") }}
              </th>
              <th rowspan="2" class="text-center">
                {{ $t("department.restitr") }}
              </th>
              <th rowspan="2" class="textRotate text-center">
                {{ $t("department.master") }}
              </th>
              <th rowspan="2" class="textRotate text-center">
                {{ $t("department.brigadir") }}
              </th>
              <th rowspan="2" class="textRotate text-center">
                {{ $t("department.worker") }}
              </th>
              <th colspan="2" style="text-align: center">
                {{ $t("department.internship") }}
              </th>
              <th rowspan="2" class="textRotate text-center">
                {{ $t("department.foreigner") }}
              </th>
              <th rowspan="2" class="textRotate text-center">
                {{ $t("department.maternity") }}
              </th>
              <th rowspan="2" class="textRotate text-center">
                {{ $t("department.women") }}
              </th>
            </tr>
            <tr>
              <th class="text-center">{{ $t("department.director") }}</th>
              <th class="text-center">{{ $t("department.itr") }}</th>
              <th class="text-center">{{ $t("department.itr") }}</th>
              <th class="text-center">{{ $t("department.rab") }}</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Pitnak</td>
              <td>{{ employees[4] ? employees[4].count : "" }}</td>
              <td>{{ directors[3] ? directors[3].count : "" }}</td>
              <td>{{ itrs[3] ? itrs[3].count : "" }}</td>
              <td>{{ restItrs[3] ? restItrs[3].count : "" }}</td>
              <td>{{ masters[2] ? masters[2].count : "" }}</td>
              <td>{{ brigadirs[2] ? brigadirs[2].count : "" }}</td>
              <td>{{ workers[4] ? workers[4].count : "" }}</td>
              <td>{{ "" }}</td>
              <td>{{ "" }}</td>
              <td>{{ "" }}</td>
              <td>{{ "" }}</td>
              <td>{{ women[3] ? women[3].count : "" }}</td>
            </tr>
            <tr>
              <td>Tashkent</td>
              <td>
                {{
                  employees[(2, 3)]
                    ? employees[2].count + employees[3].count
                    : ""
                }}
              </td>
              <td>
                {{
                  directors[(1, 2)]
                    ? directors[1].count + directors[2].count
                    : ""
                }}
              </td>
              <td>
                {{ itrs[(1, 2)] ? itrs[1].count + itrs[2].count : "" }}
              </td>
              <td>
                {{
                  restItrs[(1, 2)] ? restItrs[1].count + restItrs[2].count : ""
                }}
              </td>
              <td>
                {{ masters[1] ? masters[1].count : "" }}
              </td>
              <td>
                {{ brigadirs[1] ? brigadirs[1].count : "" }}
              </td>
              <td>
                {{ workers[(2, 3)] ? workers[2].count + workers[3].count : "" }}
              </td>
              <td>{{ "" }}</td>
              <td>{{ "" }}</td>
              <td>{{ "" }}</td>
              <td>{{ "" }}</td>
              <td>
                {{ women[(1, 2)] ? women[1].count + women[2].count : "" }}
              </td>
            </tr>
            <tr>
              <td>Asaka</td>
              <td>{{ employees[1] ? employees[1].count : "" }}</td>
              <td>{{ directors[0] ? directors[0].count : "" }}</td>
              <td>{{ itrs[0] ? itrs[0].count : "" }}</td>
              <td>{{ restItrs[0] ? restItrs[0].count : "" }}</td>
              <td>{{ masters[0] ? masters[0].count : "" }}</td>
              <td>{{ brigadirs[0] ? brigadirs[0].count : "" }}</td>
              <td>{{ workers[1] ? workers[1].count : "" }}</td>
              <td>{{ "" }}</td>
              <td>{{ "" }}</td>
              <td>{{ "" }}</td>
              <td>{{ "" }}</td>
              <td>{{ women[0] ? women[0].count : "" }}</td>
            </tr>
            <tr>
              <td>Total</td>
              <td>
                {{
                  employees
                    ? employees[1].count +
                      employees[2].count +
                      employees[3].count +
                      employees[4].count
                    : ""
                }}
              </td>
              <td>
                {{
                  directors
                    ? directors[0].count +
                      directors[1].count +
                      directors[2].count +
                      directors[3].count
                    : ""
                }}
              </td>
              <td>
                {{
                  itrs
                    ? itrs[0].count +
                      itrs[1].count +
                      itrs[2].count +
                      itrs[3].count
                    : ""
                }}
              </td>
              <td>
                {{
                  restItrs
                    ? restItrs[0].count +
                      restItrs[1].count +
                      restItrs[2].count +
                      restItrs[3].count
                    : ""
                }}
              </td>
              <td>
                {{
                  masters
                    ? masters[0].count + masters[1].count + masters[2].count
                    : ""
                }}
              </td>
              <td>
                {{
                  brigadirs
                    ? brigadirs[0].count +
                      brigadirs[1].count +
                      brigadirs[2].count
                    : ""
                }}
              </td>
              <td>
                {{
                  workers
                    ? workers[1].count +
                      workers[2].count +
                      workers[3].count +
                      workers[4].count
                    : ""
                }}
              </td>
              <td>{{ "" }}</td>
              <td>{{ "" }}</td>
              <td>{{ "" }}</td>
              <td>{{ "" }}</td>
              <td>
                {{
                  women
                    ? women[0].count +
                      women[1].count +
                      women[2].count +
                      women[3].count
                    : ""
                }}
              </td>
            </tr>
          </tbody>
        </template>
      </v-simple-table> -->

      <v-simple-table>
        <template v-slot:default>
          <thead>
            <tr style="height:150px;">
              <th style="height:150px;" class="text-center">
                #
              </th>
              <th
                v-for="(employee, index) in employees"
                :key="index"
                class="text-center"
                style="height:150px;"
              >
                {{ employee["name_" + $i18n.locale] }}
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(b, id) in branch" :key="id">
              <td>{{ b.name }}</td>
              <td v-for="(employee, idx) in employees" :key="idx">
                {{
                  employees[idx].employee_count.filter(
                    v => v.branch == b.name
                  )[0].data[0].count
                }}
              </td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>
    </v-card>
  </div>
</template>

<script>
let axios = require("axios").default;
import Swal from "sweetalert2";

export default {
  data: () => ({
    employees: [],
    branch: [],
    brigadirs: [],
    directors: [],
    itrs: [],
    restItrs: [],
    masters: [],
    women: [],
    workers: [],
    loading: false
  }),
  computed: {},

  methods: {
    getList() {
      this.loading = true;
      axios
        .get(this.$store.state.backend_url + "api/get-employee-reports")
        .then(res => {
          this.employees = res.data.position_types;
          // this.counts = this.employees.map(v => {
          //   return v.employee_count;
          // });
          // console.log("count", this.counts);
          this.branch = res.data.branch;
          console.log("res", this.branch);

          // this.employees = res.data.employees;
          // this.brigadirs = res.data.brigadirs;
          // this.directors = res.data.directors;
          // this.itrs = res.data.itrs;
          // this.restItrs = res.data.restItrs;
          // this.masters = res.data.masters;
          // this.women = res.data.women;
          // this.workers = res.data.workers;
          this.loading = false;
        })
        .catch(function(error) {
          console.log(error);
          this.loading = false;
        });
    }
  },
  mounted() {
    this.getList();
  }
};
</script>
<style scoped>
.textRotate {
  text-align: center;
  transform: rotate(270deg);
  transition: transform 1s ease-in-out;
}
</style>
