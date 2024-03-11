<template>
  <div class="fullHeight">
    <v-card class="heightFull" >
      <v-card-title class="ml-1 mb-4">
        <span class="titleStyle">{{ $t(title_name) }}</span>
        <div
            class="DIV_search d-flex align-center"            
          >
        <v-text-field
          class="inputSearch"
          v-model="searchText"
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

        <v-btn class="listBtn" disabled style="background: #fff">
          <v-icon color="green">
            {{ "mdi-view-grid-outline" }}
          </v-icon>
        </v-btn>
      </div>
      </v-card-title>
      <v-row class="mx-5 mr-1">
        <v-col
          class="pa-0"
          
          md="3"
          sm="6"
          xs="12"
          :key="i"
          v-for="(template, i) in create_document.filter(
            (v) => v.text.toUpperCase().search(searchText.toUpperCase()) >= 0,
            (t) => $store.getters.checkPermission(t.visible)
          )"
        >
          <v-hover>
            <template v-slot="{ hover }">              
              <v-card
                :to="template.route"
                :class="`elevation-${hover ? 5 : 0}`"
                class="mr-4 mb-4 pa-3"
                style="
                  border: 1px solid #dce5ef;
                  background: #fbfcfe;
                  border-radius: 2px;
                "
              >
                <template>
                  <v-card-title class="pa-0">
                    <v-icon
                      class="card_icon"
                      :color="template.color"
                      large
                      left
                    >
                      {{ hover ? template.icon1 : template.icon2 }}
                    </v-icon>
                    <span class="pa-0 cardTitle">{{
                      template["name_" + $i18n.locale]
                    }}</span>
                  </v-card-title>
                </template>
              </v-card>
            </template>
          </v-hover>
        </v-col>
      </v-row>
    </v-card>
  </div>
</template>

<script>
import { colors } from "vuetify/lib";
const axios = require("axios").default;
export default {
  data() {
    return {
      selection: [],
      searchText: "",
      templates: [],
      docFolderType: null,
      create_document: [],
      search: "",
      title_name: "",
      splite: true,
      isSelected: 0,
      lists: false,
      loading: false,
      folderTypes: {
        2: "inbox",
        3: "outbox",
        4: "draft",
        5: "canceled",
      },
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 175;
    },
    headers() {
      return [];
    },
  },

  watch: {
    $route(to, from) {
      this.getFolderType();
    },
  },
  methods: {
    changeItemStar() {
      this.isSelected = !this.isSelected;
    },
    async getFolderType() {
      const folderTypes = {
        2: "inbox",
        3: "outbox",
        4: "draft",
        5: "canceled",
      };

      // Common function to fetch data and populate create_document
      function fetchAndPopulateData(docFolderType, menu_item, context) {
        context.create_document = [];
        axios
          .get(context.$store.state.backend_url + "api/documents/list-new")
          .then((response) => {
            response.data.forEach((element) => {
              let document_list = response.data;
              document_list.map((v) => {
                v.visible = context.$store.getters.checkPermission(
                  "document-list-" + v.menu_item
                );
                return v;
              });

              if (
                docFolderType in folderTypes &&
                element.menu_item === menu_item
              ) {
                // console.log(docFolderType, element.menu_item);
                element.document_types.forEach((c) => {      
                    context.create_document.push({
                      id: c.id,
                      icon1: "mdi-folder-open-outline",
                      icon2: "mdi-folder-outline",
                      name_uz_latin: c.name_uz_latin,
                      name_uz_cyril: c.name_uz_cyril,
                      name_ru: c.name_ru,
                      text: c.name_uz_latin,
                      color: "orange",
                      route: `../document-folder/list/${element.menu_item!='canceled'?element.menu_item:'cancel'}/${c.id}`,
                      count: c.count,
                    });
                });
              }
            });
          })
          .catch((error) => {
            console.log(error);
          });
      }
      this.docFolderType = this.$route.params.docFolderType;
      if (this.docFolderType == 0) {
        this.getCreateNew();
        this.title_name = "all_documents";
      }
      if (this.docFolderType == 1) {
        this.getCreateNew();
        this.title_name = "created_documents";
      }
      if (this.docFolderType == 2) {
        fetchAndPopulateData(2, "inbox", this);
        this.title_name = "in_doc";
      }
      if (this.docFolderType == 3) {
        fetchAndPopulateData(3, "outbox", this);
        this.title_name = "document.outboxs";
      }
      if (this.docFolderType == 4) {
        fetchAndPopulateData(4, "draft", this);
        this.title_name = "document.drafts";
      }
      if (this.docFolderType == 5) {
        fetchAndPopulateData(5, "canceled", this);
        this.title_name = "document.cancels";
      }
    },
    getCreateNew() {
      this.create_document = [       
        {
          id: 0,
          icon1: "mdi-file-document-outline",
          icon2: "mdi-file-document-outline",
          text: "Erkin shablon",
          name_uz_latin: "Erkin shablon",
          name_uz_cyril: "Эркин шаблон",
          name_ru: "Произвольный шаблон",
          route: "/documentsidebar/document-folder/create/1",
          color: "green",
          count: 50,
          visible: false,
        },
      ];
      axios
        .get(this.$store.state.backend_url + "api/document-types")
        .then((response) => {
          response.data.forEach((element) => {
            let i = 0;
            element.permissions.forEach((permission) => {
              if (this.$store.getters.checkPermission(permission)) {
                i++;
              }
            });
            if(element.count>0){
              this.create_document.push({
              id: element.id,
              icon1: "mdi-folder-open-outline",
              icon2: "mdi-folder-outline",
              name_uz_latin: element.name_uz_latin,
              name_uz_cyril: element.name_uz_cyril,
              name_ru: element.name_ru,
              text: element.name_uz_latin,
              color: "orange",
              route: "template/" + element.id,
              count: element.count,
              visible: i == 0 ? false : true,
            });
            }
            
          });
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    // ------------------------->

    // ------------------------->
  },
  mounted() {
    this.getFolderType();
  },
};
</script>
<style scoped>
.fullHeight {
  height: calc(100% - 10px);
 
}
.heightFull {
  /* height: 100%; */
  border-radius: 10px 10px 10px 10px ;
}
.DIV_search {
  /*Qidiruv qatori uchun*/
  width: 99.3%;
  height: 40px;
  margin: 2px 0px -10px 0px;
}
.titleStyle
{
  margin: -5px 0px 0px 0px;
  font-size: 18px;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;  
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
.icon_folder {
  position: absolute;
  width: 50px;
  height: 50px;
}
.card_icon {
  margin: 0px 0px 0px 0px;
  width: 45px;
  height: 45px;
}
.cardTitle {
  color: #000;
  font-size: 14px;
  font-weight: 500;
  line-height: 1.5;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  width: 70%;
  margin: 0px 0px 0px 0px;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
</style>