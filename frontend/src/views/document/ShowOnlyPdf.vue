<template>
  <div>
    <v-card class="ma-1 pa-1" :height="screenHeight" style="overflow: hidden">
      <v-card-title class="pa-0 pb-1" style="color: #000" color="black">
        <v-spacer></v-spacer>
        <v-btn class="" icon @click="rightMenu = !rightMenu">
          <v-icon>mdi-menu</v-icon>
        </v-btn>
      </v-card-title>
      <v-divider></v-divider>
      <v-card-text class="pa-1" style="color: #000">
        <v-row class="justify-center px-2">
          <v-col cols="12" :md="rightMenu ? '9' : '12'" class="pa-0">
            <iframe
              frameborder="0"
              v-if="base64"
              width="100%"
              :height="screenHeight - 80"
              :src="'data:application/pdf;base64,' + base64 + '#toolbar=0'"
            ></iframe>
          </v-col>
          <v-col cols="12" md="3" v-if="rightMenu" class="pa-0">
            <v-card
              class="ml-1 scrollbar"
              :height="screenHeight - 50"
              elevation="0"
              style="overflow: hidden; overflow-y: auto"
            >
              <v-card outlined class="mb-2">
                <v-system-bar class="pa-1 white">
                  <strong>{{ $t("document.status") }}: </strong>
                  <v-spacer></v-spacer>
                  <v-btn
                    x-small
                    depressed
                    rounded
                    dark
                    :color="
                      document_status[document.status ? document.status : 0][
                        'color'
                      ]
                    "
                    class="px-1"
                  >
                    {{
                      document &&
                      document_status[document.status ? document.status : 0][
                        "name_" + $i18n.locale
                      ]
                    }}
                  </v-btn>
                </v-system-bar>
                <v-divider></v-divider>
                <v-system-bar class="pa-1 white">
                  <strong>{{ $t("document.creator") }}: </strong>
                  <v-spacer></v-spacer>
                  {{
                    documentSigners.find((v) => {
                      if (v.action_type_id == 6) return v;
                    }) &&
                    documentSigners.find((v) => {
                      if (v.action_type_id == 6) return v;
                    }).fio
                  }}
                </v-system-bar>
              </v-card>

              <!-- biriktirilgan hujjatlar -->
              <v-card
                v-if="
                  document.document_relation &&
                  document.document_relation.length
                "
                class="mb-2"
                outlined
              >
                <v-system-bar
                  class="pa-1 justify-center"
                  style="background-color: #163e72; color: #fff"
                  elevation="3"
                  small
                >
                  <v-spacer></v-spacer>
                  {{ $t("document.add_parent_document") }}
                  <v-spacer></v-spacer>
                  <v-btn
                    icon
                    small
                    @click="active_parent_document = !active_parent_document"
                  >
                    <v-icon
                      class="ma-0"
                      color="white"
                      v-if="active_parent_document"
                      >mdi-menu-down</v-icon
                    >
                    <v-icon class="ma-0" color="white" v-else
                      >mdi-menu-up</v-icon
                    >
                  </v-btn>
                </v-system-bar>
                <v-card-text class="pa-1" v-if="active_parent_document">
                  <v-simple-table dense class="" style="border: 1px solid #aaa">
                    <template v-slot:default>
                      <thead>
                        <tr>
                          <th class="text-left">#</th>
                          <th class="text-left">
                            {{ $t("document.document_number") }}
                          </th>
                          <th class="text-left">
                            {{ $t("document.document_name") }}
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr
                          v-for="(item, index) in document.document_relation"
                          :key="index"
                        >
                          <td>{{ index + 1 }}</td>
                          <td>
                            <router-link
                              :to="'/documents/show-only-pdf/' + item.id"
                              target="_blank"
                              >{{ item.document_number }}</router-link
                            >
                          </td>
                          <td>
                            {{ item.document_template }}
                          </td>
                        </tr>
                      </tbody>
                    </template>
                  </v-simple-table>
                </v-card-text>
              </v-card>

              <!-- biriktirilgan quyi hujjatlar -->
              <v-card
                v-if="
                  document.document_children &&
                  document.document_children.length
                "
                class="mb-2"
                outlined
              >
                <v-system-bar
                  class="pa-1 justify-center"
                  style="background-color: #163e72; color: #fff"
                  elevation="3"
                  small
                >
                  <v-spacer></v-spacer>{{ $t("document.children_document")
                  }}<v-spacer></v-spacer>
                  <v-btn
                    icon
                    small
                    @click="active_child_document = !active_child_document"
                  >
                    <v-icon
                      class="ma-0"
                      color="white"
                      v-if="active_child_document"
                      >mdi-menu-down</v-icon
                    >
                    <v-icon class="ma-0" color="white" v-else
                      >mdi-menu-up</v-icon
                    >
                  </v-btn>
                </v-system-bar>
                <v-card-text class="pa-1" v-if="active_child_document">
                  <v-simple-table dense class="" style="border: 1px solid #aaa">
                    <template v-slot:default>
                      <thead>
                        <tr>
                          <th class="text-left">#</th>
                          <th class="text-left">
                            {{ $t("document.document_number") }}
                          </th>
                          <th class="text-left">
                            {{ $t("document.document_name") }}
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr
                          v-for="(item, index) in document.document_children"
                          :key="index"
                        >
                          <td>{{ index + 1 }}</td>
                          <td>
                            <router-link
                              :to="'/documents/show-only-pdf/' + item.id"
                              target="_blank"
                              >{{ item.document_number }}</router-link
                            >
                          </td>
                          <td>
                            {{ item.document_template }}
                          </td>
                        </tr>
                      </tbody>
                    </template>
                  </v-simple-table>
                </v-card-text>
              </v-card>

              <!-- biriktirilgan fayllar -->
              <v-card
                v-if="documentFiles && documentFiles.length"
                class="mb-2"
                outlined
              >
                <v-system-bar
                  class="pa-1 justify-center"
                  style="background-color: #163e72; color: #fff"
                  elevation="3"
                >
                  <v-spacer></v-spacer>{{ $t("files") }}<v-spacer></v-spacer>
                  <v-btn icon small @click="active_files = !active_files">
                    <v-icon class="ma-0" color="white" v-if="active_files"
                      >mdi-menu-down</v-icon
                    >
                    <v-icon class="ma-0" color="white" v-else
                      >mdi-menu-up</v-icon
                    >
                  </v-btn>
                </v-system-bar>
                <v-card-text class="ma-0 pa-1" v-if="active_files">
                  <v-simple-table dense class="" style="border: 1px solid #aaa">
                    <template v-slot:default>
                      <tbody>
                        <tr v-for="(item, index) in documentFiles" :key="index">
                          <td style="width: 100%; padding-left: 4px">
                            <v-chip outlined small @click="viewPdfFile(item)">
                              {{ item.file_name }}
                              <v-icon right color="green">mdi-eye</v-icon>
                            </v-chip>
                          </td>
                          <td>
                            <v-btn
                              text
                              small
                              :href="
                                $store.state.backend_url +
                                'staffs/file-download/' +
                                item.id
                              "
                            >
                              <v-icon color="green">mdi-download</v-icon>
                            </v-btn>
                          </td>
                        </tr>
                      </tbody>
                    </template>
                  </v-simple-table>
                </v-card-text>
              </v-card>
            </v-card>
          </v-col>
        </v-row>
      </v-card-text>
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

    <v-dialog v-model="pdfViewDialog" fullscreen>
      <v-card>
        <v-card-title primary-title>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined @click="pdfViewDialog = false">
            {{ $t("close") }}
          </v-btn>
        </v-card-title>
        <v-card-text class="d-flex justify-center">
          <iframe
            :width="screenWidth - 200"
            :height="screenHeight"
            :src="
              $store.state.backend_url + 'staffs/get-file/' + fileForView.id
            "
          ></iframe>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="red"
            outlined
            @click="pdfViewDialog = false"
            class="mr-4"
            >{{ $t("close") }}</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
const axios = require("axios").default;

export default {
  data() {
    return {
      active_files: true,
      active_child_document: true,
      active_parent_document: true,
      base64: null,
      pdf_file_name: "",
      loading: false,
      action_types: [
        {
          id: 2,
          name_uz_latin: "Tasdiq",
          name_uz_cyril: "Тасдиқ",
          name_ru: "Утверждение",
        },
        {
          id: 9,
          name_uz_latin: "Komissiya raisi",
          name_uz_cyril: "Комиссия раиси",
          name_ru: "Председатель комиссии",
        },
        {
          id: 8,
          name_uz_latin: "Komissiya a'zolari",
          name_uz_cyril: "Комиссия аъзолари",
          name_ru: "Члены комиссии",
        },
        {
          id: 12,
          name_uz_latin: "Kuzatuvchi",
          name_uz_cyril: "Кузатувчи",
          name_ru: "Наблюдатель",
        },
        {
          id: 10,
          name_uz_latin: "Komissiya kotibi",
          name_uz_cyril: "Комиссия котиби",
          name_ru: "Секретарь комиссии",
        },
        {
          id: 1,
          name_uz_latin: "Rozilik",
          name_uz_cyril: "Розилик",
          name_ru: "Согласование",
        },
        {
          id: 3,
          name_uz_latin: "Bo'lim ichida rozilik",
          name_uz_cyril: "Бўлим ичида розилик",
          name_ru: "Согласование внутри подразделение",
        },
        {
          id: 4,
          name_uz_latin: "Bajaruvchilar",
          name_uz_cyril: "Бажарувчилар",
          name_ru: "Исполнители",
        },
        {
          id: 11,
          name_uz_latin: "Kuzatuvchi",
          name_uz_cyril: "Кузатувчи",
          name_ru: "Наблюдатель",
        },
        {
          id: 13,
          name_uz_latin: "Hujjat yaratuvchisi",
          name_uz_cyril: "Ҳужжат яратувчиси",
          name_ru: "Создатель документа",
        },
      ],
      document: {},
      document_status: [
        {
          name_uz_latin: "yangi",
          name_uz_cyril: "янги",
          name_ru: "новый",
          color: "black",
        },
        {
          name_uz_latin: "E'lon qilish",
          name_uz_cyril: "Эьлон қилиш",
          name_ru: "опубликован",
          color: "cyan",
        },
        {
          name_uz_latin: "qayta ishlash",
          name_uz_cyril: "қайта ишлаш",
          name_ru: "обработка",
          color: "blue",
        },
        {
          name_uz_latin: "Imzolandi",
          name_uz_cyril: "Имзоланди",
          name_ru: "Подписано",
          color: "teal",
        },
        {
          name_uz_latin: "Bajarildi",
          name_uz_cyril: "Бажарилди",
          name_ru: "Выполнено",
          color: "amber",
        },
        {
          name_uz_latin: "Yakunlandi",
          name_uz_cyril: "Якунланди",
          name_ru: "Завершено",
          color: "success",
        },
        {
          name_uz_latin: "Bekor qilindi",
          name_uz_cyril: "Бекор қилинди",
          name_ru: "Отменен",
          color: "error",
        },
      ],
      documentSigners: [],
      document_locale: "",
      documentFiles: [],
      pdfViewDialog: false,
      rightMenu: true,
      substantiate: false,
      fileForView: { id: 0 },
    };
  },
  watch: {
    $route(to, from) {
      this.id = to.params.id;
      this.getList();
    },
  },
  computed: {
    screenWidth() {
      return window.innerWidth;
    },
    screenHeight() {
      return window.innerHeight - 72;
    },
    language() {
      return this.document_locale == "ru" ? "uz_cyril" : this.document_locale;
    },
    locale() {
      return this.$i18n.locale == "ru" ? "uz_cyril" : this.$i18n.locale;
    },
    user() {
      return this.$store.getters.getUser();
    },
  },
  methods: {
    getList() {
      this.loading = true;
      this.document = {};
      axios
        .post(this.$store.state.backend_url + "api/documents/show-only-pdf", {
          pdf_file_name: this.$route.params.pdf_file_name,
          language: this.$i18n.locale,
        })
        .then((res) => {
          this.base64 = res.data.document.pdf;
          this.document = res.data.document;
          this.documentSigners = this.document.document_signers;
          this.document_locale = this.document.locale;
          this.documentFiles = res.data.document_files;
          this.loading = false;
        })
        .catch((err) => {
          console.log(err);
          this.loading = false;
        });
    },
    momentTime(time) {
      return moment(time).format("DD.MM.YYYY hh:mm");
    },
    getDocumentList() {
      axios
        .get(this.$store.state.backend_url + "api/documents/list")
        .then((response) => {
          let document_list = response.data;
          document_list.map((v) => {
            v.visible = this.$store.getters.checkPermission(
              "document-list-" + v.menu_item
            );
            return v;
          });
          this.$store.dispatch("setDocumentList", document_list);
          // console.log(this.document_list);
        })
        .catch((error) => {
          console.log(error);
        });
    },
    viewPdfFile(item) {
      this.fileForView = item;
      this.pdfViewDialog = true;
    },
  },
  mounted() {
    this.pdf_file_name = this.$route.params.pdf_file_name;
    this.getList();
  },
};
</script>
