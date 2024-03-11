<template>
  <div class="fullHeight">
    <v-card class="heightFull">
      <v-card-title class="ml-1 mb-4" v-if="templates && templates.length">
        <span class="titleStyle" style>{{ $t("often_documents") }}</span>
        <div class="DIV_search d-flex align-center">
          <v-text-field
            class="inputSearch"
            v-model="search"
            @keyup.enter="getList"
            prepend-inner-icon="mdi-magnify"
            :placeholder="$t('search')"
            dense
            hide-details
            background="#fff"
            solo
            elevation="0"
          ></v-text-field>

          <v-btn class="filterBtn" style="background: #fff">
            <v-icon color="green" left>mdi-filter-outline</v-icon>Filter
          </v-btn>

          <v-btn class="listBtn" @click="listBoolens" style="background: #fff">
            <v-icon color="green" class>
              {{ lists ? "mdi-view-grid-outline" : "mdi-format-list-bulleted" }}
            </v-icon>
          </v-btn>
        </div>
      </v-card-title>
      <v-row class="mx-5 mr-1" v-if="splite">
        <v-col
          class="pa-0"
          md="3"
          sm="6"
          xs="12"
          v-for="(template, i) in templates.filter(
            (v) => v.template.toUpperCase().search(search.toUpperCase()) >= 0,
            (t) => $store.getters.checkPermission(t.visible)
          )"
          :key="i"
          :search="search"
        >
          <v-hover>
            <template v-slot="{ hover }">
              <v-card
                @click="navigateToTemplate(template)"
                :class="`elevation-${hover ? 5 : 0}`"
                class="mr-5 mb-4 pa-3 border_left"
                style="
                  border: 1px solid #dce5ef;
                  height: 60px;
                  background: #fbfcfe;
                "
              >
                <v-icon class="card_icon" color="#F8A300">
                  {{ template.df_id ? "mdi-star" : "mdi-star-outline" }}
                </v-icon>
                <div>
                  <v-img src="img/svg/document1.svg" class="card_icon2">
                  </v-img>
                </div>
                <template>
                  <v-card-title
                    :title="template.template"
                    class="text_nowrap pa-0"
                    style="color: #000000"
                    >{{ template.template }}</v-card-title
                  >
                  <div>
                    <div class="templateID_text" style>
                      #{{ template.template_id }}
                    </div>
                  </div>
                </template>
              </v-card>
            </template>
          </v-hover>
        </v-col>
      </v-row>

      <v-row class="ma-0" v-if="lists">
        <v-col>
          <v-data-table
            style="
              border: 1px solid #dce5ef;
              width: 100%;
              border-radius: 0 0 0 0;
            "
            dense
            hide-default-footer
            :headers="headers"
            :items="templates"
            :footer-props="{
              itemsPerPageOptions: [-1],
              itemsPerPageAllText: $t('itemsPerPageAllText'),
              itemsPerPageText: $t('itemsPerPageText'),
              showFirstLastPage: true,
              firstIcon: 'mdi-arrow-collapse-left',
              lastIcon: 'mdi-arrow-collapse-right',
              prevIcon: 'mdi-arrow-left',
              nextIcon: 'mdi-arrow-right',
            }"
          >
            <!-- <template
              v-slot:item.id="{ item }"
            >{{templates.map(function(x) {return x.id; }).indexOf(item.id) + 1}}</template>
            <template v-slot:item.text="{ item }">{{ item['name_'+$i18n.locale] }}</template>
            <template v-slot:item.department="{ item }">{{ item.department['name_'+$i18n.locale] }}</template>-->

            <template v-slot:item="{ item, index }">
              <tr>
                <!-- @click="$router.push('../create/' + item.template_id)" -->
                <td
                  class="py-0 my-0 text-center"
                  @click="navigateToTemplate(item)"
                  style="cursor: pointer"
                >
                  {{ index + 1 }}
                </td>
                <td
                  class="py-0 my-0 text-center"
                  @click="navigateToTemplate(item)"
                  style="cursor: pointer"
                >
                  <v-icon color="#F8A300" small>
                    {{ item.df_id ? "mdi-star" : "mdi-star-outline" }}
                  </v-icon>
                </td>
                <td
                  class="py-0 my-0"
                  @click="navigateToTemplate(item)"
                  style="cursor: pointer"
                >
                  {{ item.type }}
                </td>
                <td
                  class="py-0 my-0"
                  @click="navigateToTemplate(item)"
                  style="cursor: pointer"
                >
                  {{ item.template }}
                </td>
              </tr>
            </template>
          </v-data-table>
        </v-col>
      </v-row>
      <v-col class="pa-0 mt-8" sm="12" v-if="!loading && templates.length == 0">
        <v-alert text outlined color="deep-orange" type="error">{{
          $t("noDataText")
        }}</v-alert>
      </v-col>
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
export default {
  data() {
    return {
      items: [
        {
          src: "backgrounds/bg.jpg",
        },
        {
          src: "backgrounds/md.jpg",
        },
        {
          src: "backgrounds/bg-2.jpg",
        },
        {
          src: "backgrounds/md2.jpg",
        },
      ],
      selection: [],
      templates: [],
      search: "",
      splite: true,
      isSelected: 0,
      lists: false,
      loading: false,
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 175;
    },
    headers() {
      return [
        {
          text: "#",
          align: "center",
          sortable: false,
          width: 30,
          value: "id",
          class: "blue-grey lighten-5",
        },
        {
          text: "",
          align: "center",
          sortable: false,
          class: "blue-grey lighten-5",
          width: 30,
          value: "star",
        },
        {
          text: this.$t("type"),
          value: "template",
          class: "blue-grey lighten-5",
        },
        {
          text: this.$t("template"),
          value: "template",
          class: "blue-grey lighten-5",
        },
      ];
    },
  },

  watch: {
    $route(to, from) {
      this.getList();
    },
  },
  methods: {
    navigateToTemplate(template) {
      if (template.template_id == 134) {
        this.$router.push("/salary-cert");
      } else if (
        template.template_id == 15700 ||
        template.template_id == 15800
      ) {
        this.$router.push("/firm-blank/" + template.template_id);
      } else {
        this.$router.push("document-folder/create/" + template.template_id);
      }
    },
    changeItemStar() {
      this.isSelected = !this.isSelected;
    },
    getList() {
      let user = this.$store.getters.getUser();
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/documents/user-templates", {
          employee_id: user.employee_id,
          locale: this.$i18n.locale,
          limit: 20,
        })
        .then((res) => {
          this.templates = res.data;
          this.loading = false;
        })
        .catch((err) => {
          console.error(err);
          this.loading = false;
        });
    },
    getDocFav($id) {
      axios
        .get(
          this.$store.state.backend_url +
            "api/document-templates/get-favorite/" +
            $id
        )
        .then((res) => {
          this.getList();
        })
        .catch((err) => {
          console.error(err);
          this.loading = false;
        });
    },
    listBoolens() {
      this.splite = !this.splite;
      this.lists = !this.lists;
    },
  },
  mounted() {
    this.getList();
  },
};
</script>
<style scoped>
.fullHeight {
  height: calc(100% - 10px);
}
.heightFull {
  /* height: 100%; */
  border-radius: 10px 10px 10px 10px;
}
.titleStyle {
  margin: -5px 0px 0px 0px;
  font-size: 18px;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.DIV_search {
  /*Qidiruv qatori uchun*/
  width: 99.3%;
  height: 40px;
  margin: 2px 0px -10px 0px;
}
.headerTitle {
  color: #000;
  font-size: 24px;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.listBtn {
  border: 1px solid #e6e6e6;
  box-shadow: none;
  border-radius: 0 5px 5px 0px;
}
.filterBtn {
  color: #000;
  font-size: 14px;
  font-weight: 600;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
  border: 1px solid #e6e6e6;
  border-right: 0px;
  border-left: 0px;
  background: #fff;
  box-shadow: none;
  border-radius: 0;
  text-transform: none;
}
.inputSearch {
  border: 1px solid #e6e6e6;
  box-shadow: none;
  border-radius: 5px 0 0 5px;
  max-height: 36px;
  overflow: hidden;
  color: #212529;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.card_icon {
  /*Yulduzcha uchun */
  position: absolute;
  top: 10px;
  right: 10px;
  width: 20px;
  height: 20px;
}
.card_icon2 {
  position: absolute;
  width: 20px;
  size: large;
  /* height: 45px; */
}
.right_position {
  display: flex;
  justify-content: flex-end;
}
.text_nowrap {
  /*v- card ichidagi Tamplate name uchun */
  margin: -5px 0px 0px 30px;
  width: 75%;
  word-break: keep-all;
  display: -webkit-box;
  height: 40px;
  font-size: 14px;
  line-height: 1.4;
  -webkit-line-clamp: 2;
  overflow: hidden;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.department_text {
  /*v-card ичидаги бўлим номи */
  /* white-space: normal; */
  /* display: block; */
  display: -webkit-box;
  /* max-width: 50px; */
  height: 30px;
  /* color: #6C869F; */
  width: 87%;
  margin: 0px 0px auto;
  font-size: 11px;
  font-weight: 600;
  line-height: 1.3;
  -webkit-line-clamp: 2;
  overflow: hidden;
  text-overflow: ellipsis;
}

.templateID_text {
  /*v-card template ID number*/
  color: #41a754;
  right: 10px;
  margin-left: 93%;
  position: absolute;
  font-size: 14px;
  margin: -18px -1px auto;
  text-align: right;
}
</style>