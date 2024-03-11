<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ $t("registry.work_calendar") }}</span>
        <v-spacer></v-spacer>
        <v-btn
          v-if="$store.getters.checkPermission('company-create')"
          color="#6ac82d"
          x-small
          dark
          fab
          @click="newItem"
        >
          <v-icon>mdi-plus</v-icon>
        </v-btn>
        <v-spacer></v-spacer>
        <v-btn x-small text class="mr-1">{{ $t("work_day") }}</v-btn>
        <v-btn x-small text class="mr-1" color="#f00">{{
          $t("holiday")
        }}</v-btn>
        <v-btn x-small text class="mr-1" color="#00f">{{
          $t("weekend")
        }}</v-btn>
        <v-spacer></v-spacer>
        <span width="50">
          <v-autocomplete
            :items="[2023, 2022, 2021, 2020]"
            v-model="year"
            @change="getList"
          >
          </v-autocomplete>
        </span>
      </v-card-title>
      <v-card-text>
        <v-row class="ma-0 pa-0">
          <v-col
            v-for="(item, index) in items"
            :key="index"
            cols="1"
            class="ma-0 pa-0 pr-1"
          >
            <v-btn x-small block @click="calendarData(item)">
              {{ item[0].month }}
            </v-btn>
            <template v-for="(itm, key) in item">
              <v-btn
                :key="key"
                x-small
                text
                block
                v-if="itm.is_work_day"
                class="mt-1"
                @click="edit(itm)"
              >
                {{ itm.calendar_date.substring(8, 10) + " " + itm.week_day }}
              </v-btn>
              <v-btn
                :key="key"
                x-small
                block
                text
                v-else-if="itm.is_holiday"
                color="#F00"
                class="mt-1"
                @click="edit(itm)"
              >
                {{ itm.calendar_date.substring(8, 10) + " " + itm.week_day }}
              </v-btn>
              <v-btn
                :key="key"
                x-small
                block
                text
                v-else
                color="#00F"
                class="mt-1"
                @click="edit(itm)"
              >
                {{ itm.calendar_date.substring(8, 10) + " " + itm.week_day }}
              </v-btn>
            </template>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <v-dialog v-model="dialog" persistent width="600">
      <v-card v-if="form">
        <v-card-title primary-title>
          <b>{{ form.calendar_date }}</b>
          <v-spacer></v-spacer>
          <span v-if="form.is_work_day">
            {{ $t("sequence") }}: {{ form.work_day_sequence }}</span
          >
          <v-spacer></v-spacer>
          <v-btn
            color="error"
            x-small
            fab
            @click="
              dialog = false;
              form = null;
            "
            ><v-icon>mdi-close</v-icon></v-btn
          >
        </v-card-title>
        <v-divider></v-divider>
        <v-card-text>
          <v-row>
            <v-col cols="6">
              <v-checkbox
                v-model="form.is_holiday"
                :label="$t('holiday')"
              ></v-checkbox>
            </v-col>
            <v-col cols="6">
              <v-checkbox
                v-model="form.is_work_day"
                :label="$t('work_day')"
              ></v-checkbox>
            </v-col>
            <v-col cols="6" v-if="form.is_holiday">
              <v-text-field
                v-model="form.holiday_name"
                :label="$t('holiday_name')"
                hide-details
              ></v-text-field>
            </v-col>
            <v-col cols="6">
              <v-checkbox
                v-model="form.is_weekend"
                :label="$t('weekend')"
                disabled
              ></v-checkbox>
            </v-col>
          </v-row>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="success" @click="save()">{{ $t("save") }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
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
    <v-dialog v-model="dialogModel" persistent width="600">
      <v-card>
        <v-card-title>
          month
          <v-spacer></v-spacer>
          <v-btn
            color="error"
            x-small
            fab
            @click="
              dialogModel = false;
              form = null;
            "
            ><v-icon>mdi-close</v-icon></v-btn
          >
        </v-card-title>
        <v-card-text>
          <v-sheet height="400">
            <v-calendar :now="today" :value="calendar_item" color="primary">
              <template v-slot:day-label="{ date, day, present }">
                <v-btn fab dark color="error" small>{{ day }}</v-btn>
              </template>
            </v-calendar>
          </v-sheet>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
const axios = require("axios").default;
import Swal from "sweetalert2";
export default {
  data: () => ({
    year: 2023,
    loading: false,
    dialog: false,
    dialogModel: false,
    calendar_item: null,
    form: null,
    today: "2022-01-05",
    items: [],
  }),
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
  },
  methods: {
    calendarData(item) {
      this.dialogModel = true;
      this.calendar_item = item;
      this.calendar_item = this.calendar_item.map((v) => {
        return v.id, v.calendar_date;
      });
    },
    edit(itm) {
      if (this.$store.getters.checkPermission("work-calendar")) {
        this.form = itm;
        this.dialog = true;
      }
    },
    save() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/work-calendars", this.form)
        .then((response) => {
          // this.items = response.data;
          this.dialog = false;
          this.loading = false;
          // this.getList();
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getList() {
      this.loading = true;
      axios
        .get(this.$store.state.backend_url + "api/work-calendars/" + this.year)
        .then((response) => {
          this.items = response.data;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
  },
  mounted() {
    this.getList();
  },
};
</script>
