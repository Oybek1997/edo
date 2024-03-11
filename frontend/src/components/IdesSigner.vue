<template>
  <div>
    <v-card outlined class="mb-2">
      <v-system-bar
        class="pa-1"
        style="background-color: #163e72; color: #fff"
        elevation="3"
        small
      >
        <v-spacer></v-spacer>
        {{ $t("profile.control_punkt") }}
        <v-spacer></v-spacer>
        <v-btn icon small @click="control_punkt_drop = !control_punkt_drop">
          <v-icon class="ma-0" color="white" v-if="control_punkt_drop"
            >mdi-menu-down</v-icon
          >
          <v-icon class="ma-0" color="white" v-else>mdi-menu-up</v-icon>
        </v-btn>
      </v-system-bar>
    </v-card>
    <v-card class="pa-1" v-if="control_punkt_drop">
      <v-btn
        v-if="
          $store.getters.checkPermission('add_control_punkt') ||
            $store.getters.checkPermission('ides')
        "
        color="info"
        outlined
        x-small
        @click="showReaction('comment')"
        >{{ $t("document.comment") }}</v-btn
      >

      <fieldset>
        <legend>
          {{ $t("document.content") }}
        </legend>
        <v-card-title class="pa-1 font-italic">
          {{ document ? document.content : "" }}
        </v-card-title>
      </fieldset>

      <!-- History -->
      <v-card outlined class="mx-0 my-2 pa-0">
        <v-card-text class="pa-0" v-if="document">
          <fieldset
            v-for="(signer, key) in document.document_signers.filter(
              v => v.action_type_id != 6
            )"
            :key="key"
          >
            <legend>
              {{ signer.organization ? signer.organization.name_uz_latin : "" }}
            </legend>
            <v-alert
              border="left"
              dense
              icon="mdi-check-bold"
              text
              type="success"
              class="ma-1"
            >
              <div style="display: flex; justify-content: space-between">
                <span v-if="signer.user" style="font-weight: bold">
                  {{ signer.user.fio.uz_latin }}
                </span>
                <span
                  v-else-if="signer.organization.some_user"
                  style="font-weight: bold"
                >
                  {{ signer.organization.some_user.fio.uz_latin }}
                </span>
                <span v-else style="font-weight: bold; text-color: red">
                  {{ $t("message.erroruser_ides") }}
                </span>
                <span>
                  <i>{{ signer.due_datetime }}</i>
                </span>
              </div>
              <hr />

              <div
                style="display: flex; align-items: center; justify-content: space-between;"
              >
                <div
                  v-for="(action, i) in action_types.filter(v => {
                    if (signer.action_type_id == v.id) return v;
                  })"
                  :key="i"
                >
                  <span class="mr-5">{{ action.name_uz_latin }}</span>
                  <span>{{ signer.taken_datetime }}</span>
                </div>
                <v-btn
                  @click="recent(signer.id)"
                  text
                  v-if="signer.status == 1 || signer.status == 2"
                >
                  <v-icon color="error">mdi-redo-variant</v-icon>
                </v-btn>
              </div>
             </v-alert>
            <div v-for="(comment, k) in signer.comments" :key="k">
              <v-alert
                v-if="comment.status == 1"
                border="left"
                dense
                icon="mdi-check-bold"
                text
                type="success"
                class="ma-1"
              >
                <div style="display: flex; justify-content: space-between">
                  <span v-if="comment.created_by" style="font-weight: bold">
                    {{ comment.created_by.fio.uz_latin }}
                  </span>
                  <span>
                    <i>{{ comment.created_at }}</i>
                  </span>
                </div>
                <hr />
                <div
                  style="display: flex; justify-content: space-between;
                      align-items: center;
                    "
                >
                  {{ comment.comment }}
                </div>
                <v-simple-table
                  :disable-pagination="true"
                  dense
                  style="border: none"
                  v-if="comment.comment != 'Hujjat yaratildi.'"
                >
                  <template v-slot:default>
                    <tbody>
                      <tr
                        v-for="(item, index) in comment.comment_files"
                        :key="index"
                        style="background-color: #fff"
                      >
                        <td style="width: 100%; padding-left: 4px">
                          <v-chip
                            outlined
                            small
                            style="color: #000"
                            @click="viewPdfFile(item)"
                            >{{ item.file_name }}</v-chip
                          >
                        </td>
                        <td>
                          <a
                            style="text-decoration: none"
                            :href="
                              $store.state.backend_url +
                                'api/ides/file/' +
                                item.id
                            "
                          >
                            <v-icon color="green">mdi-download</v-icon>
                          </a>
                        </td>
                      </tr>
                    </tbody>
                  </template>
                </v-simple-table>
              </v-alert>
              <v-alert
                v-if="comment.status == 3"
                border="left"
                dense
                icon="mdi-comment-outline"
                text
                type="info"
                class="ma-1"
              >
                <div style="display: flex; justify-content: space-between">
                  <span v-if="comment.created_by" style="font-weight: bold">
                    {{ comment.created_by.fio.uz_latin }}
                  </span>
                  <span>
                    <i>{{ comment.created_at }}</i>
                  </span>
                </div>
                <hr />
                <div style="text-align: justify">
                  {{ comment.comment }}
                </div>
                <v-simple-table
                  :disable-pagination="true"
                  dense
                  style="border: none"
                  v-if="comment.comment != 'Hujjat yaratildi.'"
                >
                  <template v-slot:default>
                    <tbody>
                      <tr
                        v-for="(item, index) in comment.comment_files"
                        :key="index"
                        style="background-color: #fff"
                      >
                        <td style="width: 100%; padding-left: 4px">
                          <v-chip
                            outlined
                            small
                            style="color: #000"
                            @click="viewPdfFile(item)"
                            >{{ item.file_name }}</v-chip
                          >
                        </td>
                        <td>
                          <a
                            style="text-decoration: none"
                            :href="
                              $store.state.backend_url +
                                'api/ides/file/' +
                                item.id
                            "
                          >
                            <v-icon color="green">mdi-download</v-icon>
                          </a>
                        </td>
                      </tr>
                    </tbody>
                  </template>
                </v-simple-table>
              </v-alert>
              <v-alert
                v-if="comment.status == 2"
                border="left"
                dense
                icon="mdi-close"
                text
                type="error"
                class="ma-1"
              >
                <div style="display: flex; justify-content: space-between">
                  <span v-if="comment.created_by" style="font-weight: bold">
                    {{ comment.created_by.fio.uz_latin }}
                  </span>
                  <span>
                    <i>{{ comment.created_at }}</i>
                  </span>
                </div>
                <hr />
                <div
                  style="
                      display: flex;
                      justify-content: space-between;
                      align-items: center;
                    "
                >
                  {{ comment.comment }}
                </div>
                <v-simple-table
                  :disable-pagination="true"
                  dense
                  style="border: none"
                  v-if="comment.comment != 'Hujjat yaratildi.'"
                >
                  <template v-slot:default>
                    <tbody>
                      <tr
                        v-for="(item, index) in comment.comment_files"
                        :key="index"
                        style="background-color: #fff"
                      >
                        <td style="width: 100%; padding-left: 4px">
                          <v-chip
                            outlined
                            small
                            style="color: #000"
                            @click="viewPdfFile(item)"
                            >{{ item.file_name }}</v-chip
                          >
                        </td>
                        <td>
                          <a
                            style="text-decoration: none"
                            :href="
                              $store.state.backend_url +
                                'api/ides/file/' +
                                item.id
                            "
                          >
                            <v-icon color="green">mdi-download</v-icon>
                          </a>
                        </td>
                      </tr>
                    </tbody>
                  </template>
                </v-simple-table>
              </v-alert>
            </div>
           </fieldset>
        </v-card-text>
      </v-card>
    </v-card>
    <v-dialog
      v-model="loading"
      hide-overlay
      persistent
      width="300"
      transition="dialog-top-transition"
    >
      <v-card color="primary" dark>
        <v-card-text>
          Please waiting!
          <v-progress-linear
            indeterminate
            color="white"
            class="mb-0"
          ></v-progress-linear>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="reactionDialog"
      persistent
      max-width="500px"
      transition="dialog-top-transition"
    >
      <v-card>
        <v-card-title primary-title>
          {{ $t("actions") }}
          <v-spacer></v-spacer>
          <v-btn color="error" small @click="reactionDialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form ref="signForm">
            <v-row>
              <v-col cols="12">
                <v-select
                  :items="pendingSigners"
                  v-model="document_signer_id"
                  :rules="[v => !!v || 'Выберите подписанта']"
                  item-text="organization.name_uz_latin"
                  item-value="id"
                  :label="$t('document.pending_action')"
                  hide-details
                  outlined
                  dense
                ></v-select>
              </v-col>
              <v-col cols="12">
                <v-textarea
                  :label="$t('document.comment')"
                  :rules="[v => !!v || 'Required']"
                  rows="2"
                  v-model="reactionComment"
                  hide-details
                  outlined
                ></v-textarea>
              </v-col>
              <v-col cols="12">
                <label for>{{ $t("document.files") }}</label>
                <v-file-input
                  v-model="selectFiles"
                  outlined
                  dense
                  multiple
                  prepend-icon
                  append-icon="mdi-file-pdf-box-outline"
                  accept=".pdf, application/pdf"
                  small-chips
                  show-size
                  hide-details="auto"
                ></v-file-input>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions class="pb-4">
          <v-spacer></v-spacer>
          <v-btn
            v-if="reactionStatus == 'accept'"
            color="success"
            class="mx-1"
            outlined
            @click="documentReaction('accept')"
            >{{ $t("document.accept") }}</v-btn
          >
          <v-btn
            v-else-if="reactionStatus == 'publish'"
            color="primary"
            class="mx-1"
            outlined
            @click="documentReaction('publish')"
            >{{ $t("publish") }}</v-btn
          >
          <v-btn
            v-else-if="reactionStatus == 'reject'"
            color="error"
            class="mx-1"
            outlined
            @click="documentReaction('reject')"
            >{{ $t("document.reject") }}</v-btn
          >
          <v-btn
            v-else-if="reactionStatus == 'comment'"
            color="warning"
            class="mx-1"
            outlined
            @click="documentReaction('comment')"
            >{{ $t("document.comment") }}</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="pdfViewDialog" fullscreen>
      <v-card>
        <v-card-title primary-title>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined @click="pdfViewDialog = false">{{
            $t("close")
          }}</v-btn>
        </v-card-title>
        <v-card-text class="d-flex justify-center">
          <iframe
            :width="screenWidth"
            :height="screenHeight"
            :src="
              $store.state.backend_url + 'api/ides/getFile/' + fileForView.id
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
          >
            {{ $t("close") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
const axios = require("axios").default;
import pdf from "vue-pdf";
import { message } from "./languages/ru";
const moment = require("moment");
export default {
  props: ["documentId"],
  watch: {
    documentId: function(id, oldVal) {
      // watch it
      this.getDocument(id);
    },
    recentMethod: function(id) {
      this.recent(id);
    }
  },
  components: {
    pdf
  },
  data() {
    return {
      status_event: false,
      reactionComment: null,
      document_signer_id: null,
      reactionStatus: null,
      numPages: [],
      document: null,
      documentFiles: null,
      fileForView: {
        id: 0
      },
      pdfViewDialog: false,
      control_punkt_drop: true,
      document_status: [
        {
          name_uz_latin: "yangi",
          name_uz_cyril: "янги",
          name_ru: "новый",
          color: "black"
        },
        {
          name_uz_latin: "E'lon qilingan",
          name_uz_cyril: "Эьлон қилинган",
          name_ru: "опубликован",
          color: "cyan"
        },
        {
          name_uz_latin: "qayta ishlashda",
          name_uz_cyril: "қайта ишлашда",
          name_ru: "Обработка",
          color: "blue"
        },
        {
          name_uz_latin: "Imzolangan",
          name_uz_cyril: "Имзоланган",
          name_ru: "Подписано",
          color: "teal"
        },
        {
          name_uz_latin: "Bajarilgan",
          name_uz_cyril: "Бажарилган",
          name_ru: "Выполнено",
          color: "amber"
        },
        {
          name_uz_latin: "Yakunlangan",
          name_uz_cyril: "Якунланган",
          name_ru: "Завершено",
          color: "success"
        },
        {
          name_uz_latin: "Bekor qilingan",
          name_uz_cyril: "Бекор қилинган",
          name_ru: "Отменен",
          color: "error"
        }
      ],
      action_types: [
        {
          id: 4,
          name_uz_latin: "Bajaruvchilar",
          name_uz_cyril: "Бажарувчилар",
          name_ru: "Исполнители"
        },
        {
          id: 15,
          name_uz_latin: "Yig'uvchi",
          name_uz_cyril: "Yig'uvchi",
          name_ru: "Yig'uvchi"
        },
        {
          id: 11,
          name_uz_latin: "Nazoratchi",
          name_uz_cyril: "Nazoratchi",
          name_ru: "Nazoratchi"
        },
        {
          id: 5,
          name_uz_latin: "Ma'lumot uchun",
          name_uz_cyril: "Маълумот учун",
          name_ru: "Для информации"
        }
      ],
      reactionDialog: false,
      enableAcceptButton: false,
      enableRejectButton: false,
      enableCommentButton: false,

      loading: false,
      pendingSigners: [],
      commentSigners: [],
      selectFiles: []
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 85;
    },
    screenWidth() {
      return window.innerWidth;
    },
    user() {
      return this.$store.getters.getUser();
    }
    // user() {
    //   let localStorage = window.localStorage;
    //   return JSON.parse(localStorage.getItem("user"));
    // }
  },
  methods: {
    recent(id) {
      this.loading = true;
      axios
        .get(this.$store.state.backend_url + "api/ides/recent-signer/" + id)
        .then(response => {
          if (response.status == 200) {
            this.status_event = true;
          }
          this.getDocument(this.document.id);
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
        });
      // console.log(id);
    },
    publish() {
      this.loading = true;
      axios
        .get(
          this.$store.state.backend_url + "api/ides/publish/" + this.document.id
        )
        .then(response => {
          this.loading = false;
          this.getDocument(this.document.id);
        })
        .catch(error => {
          console.log(error);
        });
    },
    getDocument(id) {
      // console.log(this.user);
      this.loading = true;
      axios
        .get(this.$store.state.backend_url + "api/ides/show-new/" + id)
        .then(response => {
          this.loading = false;

          this.document = response.data;
          this.documentFiles = response.data.document_files;
          this.document.document_signers.map(v => {
            v.taken_datetime = v.taken_datetime
              ? moment(v.taken_datetime).format("DD.MM.YYYY hh:mm")
              : null;
            v.due_datetime = v.due_datetime
              ? moment(v.due_datetime).format("DD.MM.YYYY hh:mm")
              : null;

            v.comments.map(e => {
              e.created_at = e.created_at
                ? moment(e.created_at).format("DD.MM.YYYY hh:mm")
                : null;
            });
          });
          this.loadingTask = pdf.createLoadingTask(
            "data:application/pdf;base64," +
              this.document.document_base64.base64
          );
          this.loadingTask.promise.then(pdf => {
            this.numPages = pdf.numPages;
          });
          this.showButtons();
          // this.getComments();
        })
        .catch(error => {
          console.log(error);
        });
    },
    getComments() {
      this.document.document_signers.forEach(v => {
        if (v.comments.filter(c => c.parent_id == null).length > 0) {
          this.comments = this.comments.concat(
            v.comments.filter(c => c.parent_id == null)
          );
        }
      });
    },
    showButtons() {
      this.enableAcceptButton = false;
      this.enableRejectButton = false;
      this.enableCommentButton = false;
      if (
        ![0, 6, 5].includes(this.document.status) &&
        this.document.document_signers.filter(
          v => v.taken_datetime && v.status == 0
        ).length > 0
      ) {
        this.enableAcceptButton = true;
      }
      if (
        ![0, 6, 5].includes(this.document.status) &&
        (this.document.document_signers.filter(
          v => v.taken_datetime && v.status == 0
        ).length > 0 ||
          this.document.creator.user.id == this.user.id)
      ) {
        this.enableRejectButton = true;
      }
      if (
        ![0, 6, 5].includes(this.document.status) &&
        this.document.document_signers.length > 0
      ) {
        this.enableCommentButton = true;
      }
    },
    showReaction(status) {
      this.reactionStatus = status;
      this.reactionDialog = true;
      this.getPendingSigners();
    },
    documentReaction(status) {
      if (this.$refs.signForm.validate()) {
        let formData = new FormData();
        this.selectFiles.forEach((v, i) => {
          formData.append("files[]", v);
        });
        formData.append("comment[]", "comm");

        this.loading = true;
        axios
          .post(this.$store.state.backend_url + "api/ides/reaction", {
            id: this.document.id,
            status: status,
            comment: this.reactionComment,
            document_signer_id: this.document_signer_id
          })
          .then(response => {
            this.loading = false;
            this.reactionDialog = false;
            this.reactionComment = null;
            this.reactionStatus = null;
            axios.post(
              this.$store.state.backend_url +
                "api/ides/updatefiles/" +
                response.data,
              formData,
              {
                headers: {
                  "Content-Type": "multipart/form-data"
                }
              }
            );
            this.getDocument(this.document.id);
          })
          .catch(error => {
            console.log(error);
          });
      }
    },
    viewPdfFile(item) {
      // console.log(item);
      this.fileForView = item;
      this.pdfViewDialog = true;
    },
    getPendingSigners() {
      if (
        ["publish"].includes(this.reactionStatus) &&
        this.document.status == 0
      ) {
        this.pendingSigners = this.document.document_signers
          .filter(v => v.action_type_id == 6)
          .map((v, k) => {
            v.organization.name_uz_latin =
              v.organization.name_uz_latin + " " + (k + 1);
            v.organization.name_uz_cyril =
              v.organization.name_uz_cyril + " " + (k + 1);
            v.organization.name_uz_ru =
              v.organization.name_uz_ru + " " + (k + 1);
            return v;
          });
      } else if (
        ["accept", "reject"].includes(this.reactionStatus) &&
        ![0, 5, 6].includes(this.document.status)
      ) {
        this.pendingSigners = this.document.document_signers
          .filter(v => v.taken_datetime && v.status == 0)
          .map((v, k) => {
            v.organization.name_uz_latin =
              v.organization.name_uz_latin + " " + (k + 1);
            v.organization.name_uz_cyril =
              v.organization.name_uz_cyril + " " + (k + 1);
            v.organization.name_uz_ru =
              v.organization.name_uz_ru + " " + (k + 1);
            return v;
          });
      } else if (
        ["comment"].includes(this.reactionStatus) &&
        ![0, 5, 6].includes(this.document.status)
      ) {
        this.pendingSigners = this.document.document_signers
          .filter(v => v.action_type_id != 6)
          .map((v, k) => {
            v.organization.name_uz_latin = v.organization.name_uz_latin;
            v.organization.name_uz_cyril = v.organization.name_uz_cyril;
            v.organization.name_uz_ru = v.organization.name_uz_ru;
            return v;
          });
      } else this.pendingSigners = [];
    }
  },
  mounted() {
    // this.getDocument(this.documentId);
    // this.user = storage.get("user");
  }
};
</script>
