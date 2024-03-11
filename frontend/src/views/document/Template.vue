<template>
  <div class="fullHeight">
    <v-card class="heightFull">
      <v-card-title class="ml-1 mb-4" v-if="templates && templates.length">
        <span
          class="titleStyle"
          @click="goBack"
          style="color: #1867c0; cursor: pointer"
          >{{ $t("created_documents") }}</span
        >
        <span class="titleStyle" style="color: grey"> <v-icon class="titleStyle" style="color: grey"
          >mdi-chevron-right</v-icon>{{ templateName }}</span>
        <div class="DIV_search d-flex align-center">
          <!-- <v-btn @click="goBack" outlined class="txt_searchBtn">
            <v-icon left>mdi-keyboard-backspace</v-icon>ortga
          </v-btn> -->
          <v-text-field
            class="txt_search1"
            v-model="search"
            @keyup.enter="getList"
            prepend-inner-icon="mdi-magnify"
            :placeholder="$t('search')"
            dense
            hide-details
            solo
          ></v-text-field>

          <v-btn class="txt_searchBtn" outlined @click="loader = 'loading3'">
            <v-icon color="green" left>mdi-filter-outline</v-icon>
            Filter
          </v-btn>

          <v-btn class="txt_searchBtn" outlined @click="listBoolens">
            <v-icon color="green" class="mr-2"
              >{{
                lists ? "mdi-view-grid-outline" : "mdi-format-list-bulleted"
              }}
            </v-icon>
          </v-btn>
        </div>
        <!-- </v-toolbar> -->
        <v-spacer></v-spacer>
      </v-card-title>
      <v-row class="mx-5 mr-1" v-if="splite">
        <v-col
          class="pa-0"
          
          md="3"
          sm="6"
          xs="12"
          v-for="(template, i) in templates.filter(
            (v) => v.text.toUpperCase().search(search.toUpperCase()) >= 0,
            (t) => $store.getters.checkPermission(t.visible)
          )"
          :key="i"
          :search="search"
        >
          <v-hover>
            <template v-slot="{ hover }">
              <v-card              
              :to="
                  template.id == 134
                    ? '/documentsidebar/document-folder/salary-cert'
                    : template.id == 15700 || template.id == 15800
                    ? '/documentsidebar/document-folder/firm-blank/' + template.id
                    : '/documentsidebar/document-folder/create/' + template.id
                "
                :class="`elevation-${hover ? 5 : 0}`"
                class="mr-4 mb-4 pa-3"
                style="
                  border: 1px solid #dce5ef;
                  background: #fbfcfe;
                  border-radius: 2px;
                "
              >
                <v-btn
                class="card_icon"
                  icon                  
                  @click.prevent="getDocFav(template.id)"
                 
                >
                  <v-icon color="#F8A300">
                    {{
                      template.favorites_count ? "mdi-star" : "mdi-star-outline"
                    }}
                  </v-icon>
                  
                </v-btn>
                <div>
                  <v-img src="img/svg/document1.svg" class="card_icon2">
                  </v-img>
                </div>
                <template>
                  <v-card-title
                    :title="template['name_' + $i18n.locale]"
                    class="text_nowrap pa-0"
                    style="color: #000000"
                  >
                    <!-- {{ i + 1 }}. -->
                    {{ template["name_" + $i18n.locale] }}
                  </v-card-title>
                  <!-- <v-card-subtitle
                    class="pa-0 department_text"
                    style="color: #6c869f"
                  >
                    {{ template.document_type["name_" + $i18n.locale] }}
                  </v-card-subtitle>                   -->
                  <div>
                    <div class="templateID_text">#{{ template.id }}</div>
                  </div>
                </template>
              </v-card>
            </template>
          </v-hover>
        </v-col>
      </v-row>

      <v-row class="mx-0" v-if="lists">
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
                <td
                  class="py-0 my-0 text-center"
                  v-if="$store.getters.checkPermission(item.visible)"
                  @click="$router.push('/documentsidebar/document-folder/create/' + item.id)"
                  style="cursor: pointer"
                >
                  {{ index + 1 }}
                </td>

                <td
                  class="py-0 my-0 text-center"                 
                >
                  <v-tooltip bottom>
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                        text
                        small
                        class="ma-0 pa-0"
                        min-width="0"
                        v-bind="attrs"
                        v-on="on"
                        @click="getDocFav(item.id)"
                      >
                        <v-icon color="#F8A300" small>
                          {{
                            item.favorites_count > 0
                              ? "mdi-star"
                              : "mdi-star-outline"
                          }}
                        </v-icon>
                      </v-btn>
                    </template>
                    <span>{{ $t("Tanlangan") }}</span>
                  </v-tooltip>
                </td>

                <td
                class="py-0 my-0"
                v-if="$store.getters.checkPermission(item.visible)"
                  @click="$router.push('../create/' + item.id)"
                  style="cursor: pointer"
                >
                  {{ item["name_" + $i18n.locale] }}
                </td>
                <td
                  class="py-0 my-0"
                  v-if="$store.getters.checkPermission(item.visible)"
                  @click="$router.push('../create/' + item.id)"
                  style="cursor: pointer"
                >
                  {{ item.document_type["name_" + $i18n.locale] }}
                </td>
              </tr>
            </template>
          </v-data-table>
        </v-col>
      </v-row>
      <v-col class="pa-0 mt-8" sm="12" v-if="!loading && templates.length == 0">
        <v-alert text outlined color="deep-orange" type="error">
          {{ $t("noDataText") }}
        </v-alert>
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
      templateName: null,
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
          text: this.$t("document.documentTypes"),
          value: "text",
          width: 200,
          class: "blue-grey lighten-5",
        },
        {
          text: this.$t("department.index"),
          value: "department",
          width: 200,
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
      if (template.id == 134) {
        this.$router.push("/documentsidebar/document-folder/salary-cert");
      } else if (template.id == 15700 || template.id == 15800) {
        this.$router.push(
          "/documentsidebar/document-folder/firm-blank/" + template.id
        );
      } else {
        this.$router.push(
          "/documentsidebar/document-folder/create/" + template.id
        );
      }
    },
    goBack() {
      this.$router.go(-1);
    },
    changeItemStar() {
      this.isSelected = !this.isSelected;
    },
    getList() {
      this.search = "";
      this.loading = true;
      axios
        .post(
          this.$store.state.backend_url + "api/document-templates/get-name",
          {
            document_type_id: this.$route.params.documentTypeId,
            language: this.$i18n.locale,
            search: this.search,
          }
        )
        .then((res) => {
          this.templates = res.data;
          this.templates.map((v) => {
            v.text = v["name_" + this.$i18n.locale];
            v.visible =
              v.name_uz_latin
                .toLowerCase()
                .trim()
                .replace(/ /g, "_")
                .replace(/'/g, "")
                .replace(/,/g, "")
                .replace("?", "")
                .replace("(", "")
                .replace(/`/g, "")
                .replace(/\\/g, "")
                .replace("/", "")
                .replace(/«/g, "")
                .replace(/»/g, "")
                .replace(/!/g, "")
                .replace(")", "") + "-create";
          });
          // this.getDocFav();
          this.loading = false;
          this.templateName =
            this.templates[0].document_type["name_" + this.$i18n.locale];
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
            $id,
          {
            // document_type_id: this.$route.params.documentTypeId,
            // language: this.$i18n.locale,
            // search: this.search,
          }
        )
        .then((res) => {
          console.log("DocFav=>", res);
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
  border-radius: 10px 10px 10px 10px;
}
.titleStyle {
  margin: -5px 0px 0px 0px;
  font-size: 18px;
  font-weight: 500;
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
/* .card_icon img {
  position: absolute;
  filter: brightness(1);
  bottom: 0px;
  width: 25px;
  height: 25px;
  margin: 0 auto;
  left: 0;
  right: 0;
  transform: translateY(-58%);
} */

/* ///////////////////////////// */
.txt_search {
  /*Qidiruv qatori uchun*/
  width: 100%;
  /* border: 1px solid red; */
  /* position: absolute; */
  /* height: 90px; */
  /* left: 30px; */
  /* top: 40px; */
  box-shadow: 0px 0px 0px 0px !important;
  /* border-radius: 5px 0px 0px 5px #E6E6E6; */
}
.txt_search1 {
  border: 1px solid #e6e6e6;
  box-shadow: none;
  max-height: 38px;
  overflow: hidden;
  border-radius: 0 0 0 0;
  color: #212529;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.txt_searchBtn {
  border: 1px solid #e6e6e6;
  height: 38px !important;
  border-radius: 0px;
}
.splite {
  /*V-card uchun */
  height: 165px;
  margin-top: 0px;
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
  text-overflow: ellipsis;
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
  margin: 5px 0px auto;
  font-size: 11px;
  font-weight: 600;
  line-height: 1.3;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
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
.DIV_search {
  /*Qidiruv qatori uchun*/
  width: 99.3%;
  height: 40px;
  margin: 2px 0px -10px 0px;
}
.v-data-table > .v-data-table__wrapper > table > tbody > tr > td {
  white-space: normal !important;
}
</style>