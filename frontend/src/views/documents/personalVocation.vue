<template>
  <div>
    <v-row justify="center">
      <v-col cols="12" class="d-flex align-content-start flex-wrap">
        <v-card class="ma-2" v-for="(item, index) in items" :key="index">
          <v-card-title class="blue darken-2">
            <span class="text-h8 white--text">{{ item.tabN }}</span>
            <v-spacer></v-spacer>
          </v-card-title>
          <v-list>
            <v-list-item>
              <v-list-item-content>
                <v-list-item-title>
                  Иш бошлаган санаси:
                  {{ item.extra.doDate }}
                </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item>
              <v-list-item-content>
                <v-list-item-title
                  >Жами ишстажи: {{ item.extra.experience }}
                </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item>
              <v-list-item-content>
                <v-list-item-title
                  >Қўшимча таътил куни:
                  {{ item.extra.extra_leave }}</v-list-item-title
                >
              </v-list-item-content>
            </v-list-item>
          </v-list>
          <v-divider inset></v-divider>
          <template v-for="(itemVoc, indexVoc) in item.usedVocation">
            <v-list :keys="indexVoc">
              <v-list-item>
                <v-list-item-content>
                  <v-list-item-title> йил учун: 
                    {{ itemVoc.interval1.substring(0, 4) + "-" + itemVoc.interval2.substring(0, 4) }}
                  </v-list-item-title>
                </v-list-item-content>
              </v-list-item>
              <v-list-item>
                <v-list-item-content>
                  <v-list-item-title>
                    таътил тури: 
                    {{ vocationType.find((c)=>c.value==itemVoc.vocationtype).text
                     }}
                  </v-list-item-title>
                </v-list-item-content>
              </v-list-item>
              <v-list-item>
                <v-list-item-content>
                  <v-list-item-title>
                    хужжат рақами:
                    {{ itemVoc.docnum }}
                  </v-list-item-title>
                </v-list-item-content>
              </v-list-item>
              <v-list-item>
                <v-list-item-content>
                  <v-list-item-title>
                    таътил санаси:
                    {{ itemVoc.take1 + "-" + itemVoc.take1 }}
                  </v-list-item-title>
                </v-list-item-content>
              </v-list-item>
              <v-list-item>
                <v-list-item-content>
                  <v-list-item-title>
                    таътил куни: 
                    {{ itemVoc.takedate}}
                  </v-list-item-title>
                </v-list-item-content>
              </v-list-item>
              <v-list-item>
                <v-list-item-content>
                  <v-list-item-title>
                    моддий ёрдам: 
                    {{ itemVoc.materilhelp}}
                  </v-list-item-title>
                </v-list-item-content>
              </v-list-item>
              <v-divider inset></v-divider>
            </v-list>
          </template>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>
<script>
const axios = require("axios").default;
import Swal from "sweetalert2";
export default {
  data: () => ({
    loading: false,
    search: "",
    dialog: false,
    editMode: null,
    items: [],
    test: [],
    vocationType: [
      {value:'T',text:'Мехнати'},
      {value:'S',text:'Қўшимча таътил йил учун'},
      {value:'D',text:'Қўшимча таътил '},
      {value:'M',text:'Оммавий таътил'},
    ],
    form: {},
    dialogHeaderText: "",
  }),
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    headers() {
      return [];
    },
  },
  methods: {
    getList() {
        this.loading = true;
        this.test = {
          tabel: ['6931'],
          month1: '2023',
          month2: '2024',
        };
        axios
          .post(
            this.$store.state.backend_url +
              "api/document-templates/used-vacation-days",
            { filter: this.test }
          )
          .then((response) => {
            this.items = response.data;
            console.log("this.items =", this.items);
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
