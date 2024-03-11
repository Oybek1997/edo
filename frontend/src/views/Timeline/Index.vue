<template>
  <div class="fullHeight">
    <v-card
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{ $t("timeline.index") }}</span>
        <div class="headerSearch d-flex align-center">
          <v-text-field
            class="txt_search1"
            v-model="search"
            prepend-inner-icon="mdi-magnify"
            style="width: 100px !important"
            :placeholder="$t('search')"
            @keyup.native.enter="getList"
            dense
            hide-details
            solo
          ></v-text-field>
          <v-btn class="filterBtn px-2" style="background: #fff; height: 34px;">
            <v-icon color="#00B950" left>mdi-filter-outline</v-icon>Фильтр
          </v-btn>
          <v-btn class="filterBtn px-2" style="background: #fff; height: 34px;">
              Столбцы <v-icon color="#00B950" right>mdi-checkbox-marked-outline</v-icon>
          </v-btn>
          <v-menu
            transition="slide-y-transition"
            left
            
            :close-on-content-click="false"
            :nudge-width="50"
            offset-y
          >
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                class="txt_searchBtn ml-2"
                outlined
                v-bind="attrs"
                v-on="on"
              >
                <v-icon size="18" color="white"
                  >mdi-format-list-bulleted</v-icon
                >
              </v-btn>
            </template>
            <v-card>
              <v-list class="dropdown-list pa-0">
                <v-list-item
                  v-if="$store.getters.checkPermission('timeline-create')"
                  style="margin: 0px; max-height: 34px; min-height: 34px"
                  @click="newItem"
                >
                  <v-list-item-title>
                    <v-icon size="18">mdi-plus</v-icon>
                    Добавить новую строку
                  </v-list-item-title></v-list-item
                >
                <v-list-item
                  style="margin: 0px; max-height: 34px; min-height: 34px"
                  @click="0"
                >
                  <v-list-item-title
                  @click="
                    getUserExcel(1);
                    user_excel = [];
                  "
                  >
                    <v-icon color="#107C41" size="18"
                      >mdi-microsoft-excel</v-icon
                    >
                    Скачать таблицу Excel
                  </v-list-item-title></v-list-item
                >
              </v-list>
            </v-card>
          </v-menu> 
        </div>
      </v-card-title>
      <v-row class="mx-0">
        <v-col class="mx-3">
          <v-text-field :label="$t('search')" onautocomplete="off" clearable @click:clear="clear_search()"
                        v-model="search" @keyup.enter="getList()"></v-text-field>
        </v-col>
        <v-spacer></v-spacer>
        <v-col class="mx-3">
          <v-autocomplete
            :items="tags" :label="$t('search')" v-model="search_tag" multiple clearable @change="getList()">
          </v-autocomplete>
        </v-col>
      </v-row>
      <template v-for="(item, index) in items">
        <v-card v-if="item.is_active || auth_id == item.created_by.id" class="my-4   pa-2" :key="index">
          <v-card-title>
            <div class="d-inline pa-2 blue accent-4 white--text" style="">
              {{ item.title ? item.title : $t("timeline.no_title") }}
            </div>
            <v-spacer></v-spacer>
            <v-chip
              v-if="auth_id == item.created_by.id"
              class="ma-2"
              :color="item.is_active == 1 ? 'green' : 'red'"
              outlined
            >
              {{
                item.is_active == 1
                  ? $t("timeline.active")
                  : $t("timeline.inactive")
              }}
            </v-chip>
          </v-card-title>
          <v-divider></v-divider>
          <v-card-text>
            {{ item.name }}
          </v-card-text>

          <v-row class="justify-center" v-for="(file, index) in item.files" :key="index">
            <v-col v-if="file.file_type">
              <div class="subheading text-center ma-1" title="">
                <a v-if="auth_id == item.created_by.id" @click="deleteFile(file)">
                  <v-icon color="red">mdi-minus-circle-outline</v-icon>
                  {{ file.file_name }}
                </a>
              </div>
              <a
                class="text-decoration-none"
                target="_blank"
                @click="open_image(file.id)"
              >
                <v-img
                  :src="$store.state.backend_url + 'timeline-file/' + file.id"
                  :max-width="image_max_width * 0.7"
                  style="margin: 0 auto"
                ></v-img>
              </a>
            </v-col>
          </v-row>

          <v-simple-table
            class="pa-3"
            dense
            v-show="item.files.filter((v) => v.file_type == false).length"
          >
            <template v-slot:default>
              <thead>
              <tr>
                <th class="text-left">
                  {{ $t("name") }}
                </th>
                <th class="text-center">
                  {{ $t("actions") }}
                </th>
              </tr>
              </thead>
              <tbody>
              <template v-for="(file, index) in item.files">
                <tr v-if="!file.file_type" :key="index">
                  <td>{{ file.file_name }}</td>
                  <td width="10%" class="text-center">
                    <v-btn
                      icon
                      target="_blank"
                      :href="
                      $store.state.backend_url + 'timeline-file/' + file.id
                    "
                    >
                      <v-icon color="blue"> mdi-download</v-icon>
                    </v-btn>
                    <v-btn
                      icon
                      v-if="auth_id == item.created_by.id"
                      @click="deleteFile(file)"
                    >
                      <v-icon color="red">mdi-minus-circle-outline</v-icon>
                    </v-btn>
                  </td>
                </tr>
              </template>
              </tbody>
            </template>
          </v-simple-table>

          <v-divider></v-divider>
          <v-card-actions>
            <v-chip class="ma-2" outlined>
              <v-icon left> mdi-account-outline</v-icon>
              <i
              >{{
                  item.created_by.employee.lastname_uz_cyril +
                  " " +
                  item.created_by.employee.firstname_uz_cyril +
                  " " +
                  item.created_by.employee.middlename_uz_cyril
                }}
              </i>
            </v-chip>
            <v-chip class="ma-2" color="white">
              <i>{{ item.created_at }}</i>
            </v-chip>
            <v-badge
              bordered
              color="indigo"
              :content="item.comments.length"
              :value="item.comments.length"
              overlap
            >
              <v-btn icon class="ml-1">
                <v-icon @click="collapse('comment' + item.id)" color="indigo"
                >mdi-comment-multiple-outline
                </v-icon
                >
              </v-btn>
            </v-badge>
            <v-badge
              bordered
              color="blue"
              :content="item.like_count"
              :value="item.like_count"
              overlap
            >
              <template>
                <div class="text-center">
                  <v-menu
                    open-on-hover
                    top
                    offset-y
                  >
                    <template v-slot:activator="{ on, attrs }">

                      <v-btn
                        icon
                        v-bind="attrs"
                        v-on="on"
                        class="ml-1"
                      >
                        <v-icon @click="likeComment(item.id, true)" color="blue">mdi-thumb-up-outline</v-icon>
                      </v-btn>
                    </template>

                    <v-card class="pa-2" v-if="(item.likers.length > 0 && auth_id == item.created_by.id)">
                      <span v-for="(likers, index) in item.likers" :key="index" v-if="index < 3"
                            style="font-size: 0.8rem">
                          {{
                          likers.who_like ? likers.who_like.employee.lastname_uz_cyril + ' ' + likers.who_like.employee.firstname_uz_cyril : "-"
                        }}
                        <br>
                      </span>
                      <span v-if="item.likers.length >= 3" style="font-size: 0.8rem">
                          <a href="#" @click="openLikeModal(item.likers)">{{ $t('all') }} ...</a>
                      </span>
                    </v-card>
                  </v-menu>
                </div>
              </template>

            </v-badge>
            <v-badge
              color="red"
              :content="item.dislike_count"
              :value="item.dislike_count"
              overlap
            >
              <template>
                <div class="text-center">
                  <v-menu
                    open-on-hover
                    top
                    offset-y
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                        icon
                        v-bind="attrs"
                        v-on="on"
                        class="ml-1"
                      >
                        <v-icon @click="likeComment(item.id, false)" color="red">mdi-thumb-down-outline</v-icon>
                      </v-btn>
                    </template>

                    <v-list v-if="(item.dislikers.length > 0 && auth_id == item.created_by.id)" dense max-width="300"
                            max-height="400">
                      <v-list-item dense class="py-0 px-1 ma-0"
                                   v-for="(dislikers, index) in item.dislikers"
                                   :key="index" v-if="index < 3"
                      >
                        <v-list-item-title style="font-size: 0.8rem">
                          {{
                            dislikers.who_like ? dislikers.who_like.employee.lastname_uz_cyril + ' ' + dislikers.who_like.employee.firstname_uz_cyril : "-"
                          }}
                        </v-list-item-title>
                      </v-list-item>
                      <v-list-item dense v-if="item.dislikers.length >= 3">
                        <v-list-item-title style="font-size: 0.8rem">
                          <a href="#" @click="openLikeModal(item.dislikers)">{{ $t('all') }} ...</a>
                        </v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-menu>
                </div>
              </template>
            </v-badge>
            <v-spacer></v-spacer>
            <div class="text-center" v-if="item.timeline_tags.length">

              <v-icon right dense color="indigo">
                mdi-tag-multiple
              </v-icon>
              {{ $t('tags') }}:
              <template v-for="(tag, index) in item.timeline_tags">
                <v-chip class="mr-1" small outlined :key="index" color="indigo darken-3">
                  {{ tag ? tag.tag.tag_name : "" }}
                </v-chip>
              </template>
            </div>
            <v-spacer></v-spacer>
            <v-btn
              v-if="auth_id == item.created_by.id"
              icon
              @click="editItem(item, true, true)"
            >
              <v-icon>mdi-pencil</v-icon>
            </v-btn>
            <v-btn
              icon
              v-if="auth_id == item.created_by.id"
              @click="deleteItem(item)"
            >
              <v-icon color="red">mdi-trash-can</v-icon>
            </v-btn>
          </v-card-actions>
          <v-card-text
            hidden
            :id="'comment' + item.id"
            style="
            margin: 10px auto;
            width: 98%;
            background: #fcfcfc;
            border: 1px solid #eee;
            border-radius: 5px;
          "
          >
            <v-row>
            <span class="fontSize20 font-weight-bold ml-10 mt-1">
              {{ $t("timeline.comments") }}
            </span>
              <v-spacer></v-spacer>
              <v-btn class="" small @click="commentUpdate(item)">
                <v-icon left> mdi-pencil-outline</v-icon>
                {{ $t("timeline.leave_comment") }}
              </v-btn>
            </v-row>
            <v-timeline v-if="item.comments.length" align-top dense>
              <template>
                <v-timeline-item v-for="(comment, index) in item.comments" small :key="index">
                  <u style="text-underline-position: under">
                  <span v-if="$i18n.locale == 'ru' || $i18n.locale == 'en'">{{
                      $t("timeline.to_comment")
                    }}</span>
                    <i
                    >"{{ comment.title }}"
                      <span
                        v-if="
                        $i18n.locale == 'uz_latin' || $i18n.locale == 'uz_cyril'
                      "
                      >{{ $t("timeline.to_comment") }}</span
                      >
                    </i>
                  </u>

                  <strong><p>{{ comment.name }}</p></strong>
                  <v-divider></v-divider>
                  <i>{{ comment.created_by ? comment.created_by.eimzo_name : '' }} -
                    <v-icon x-small>mdi-clock-outline</v-icon>
                    {{ comment.created_at }}</i>
                  <v-btn icon>
                    <v-icon @click="commentToComment(comment, item.id)"
                    >mdi-comment-outline
                    </v-icon
                    >
                  </v-btn>
                  <span class="float-right">
                  <v-btn
                    v-if="comment.created_by && auth_id == comment.created_by.id"
                    icon
                    @click="editItem(comment, false, false)"
                  >
                    <v-icon>mdi-pencil-outline</v-icon>
                  </v-btn>
                  <v-btn
                    v-if="comment.created_by && 
                      (auth_id == comment.created_by.id ||
                      auth_id == item.created_by.id)
                    "
                    icon
                    @click="deleteItem(comment)"
                  >
                    <v-icon color="red">mdi-trash-can-outline</v-icon>
                  </v-btn>
                </span>
                </v-timeline-item>
              </template>
            </v-timeline>
            <i class="ml-7 mt-2" v-else>{{ $t("timeline.no_comment") }}</i>
          </v-card-text>
        </v-card>
      </template>
      <observer v-on:intersect="intersected"/>
    </v-card>
    <v-dialog v-model="dialog" @keydown.esc="dialog = false" persistent max-width="800px">
      <v-card>
        <v-card-title>
          <span>{{ dialogHeaderText }}</span>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="dialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form ref="dialogForm">
            <v-row>
              <v-col cols="12">
                <v-text-field
                  v-model="form.title"
                  hide-details="auto"
                  :readonly="disableInput"
                  dense
                  outlined
                  placeholder="Названия"
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-textarea
                  v-model="form.name"
                  dense
                  hide-details="auto"
                  outlined
                  auto-grow
                  rows="3"
                  :rules="[(v) => !!v || $t('input.required')]"
                  label="Текст"
                ></v-textarea>
              </v-col>
              <v-col
                v-show="show"
                cols="12" class="ma-0"
                style="min-width: 100px; max-width: 100%"
              >
                <v-file-input
                  v-model="form.files"
                  :label='$t("blankTemplate.file")'
                  :rules="[
                    (v) => {
                      let allowedExtensions =
                        /(\.docx)$/i ||
                        /(\.doc)$/i ||
                        /(\.xlsx)$/i ||
                        /(\.xls)$/i ||
                        /(\.pdf)$/i ||
                        /(\.gif)$/i ||
                        /(\.jpg)$/i ||
                        /(\.jpeg)$/i ||
                        /(\.png)$/i;
                      let error = false;
                      (v) => {
                        if (!allowedExtensions.exec(file.name)) {
                          error = true;
                        }
                      };
                      return !error || $t('requiredformat');
                    },
                  ]"
                  outlined
                  dense
                  multiple
                  prepend-icon
                  append-icon="mdi-file-document"
                  accept=".docx, .xlsx, .doc, .xls, .pdf, .jpg, .jpeg, .png, .gif"
                  small-chips
                  show-size
                  hide-details="auto"
                ></v-file-input>
              </v-col>
              <v-col cols="12" v-if="col_tags">
                <v-row>
                  <v-col cols="10">
                    <v-autocomplete
                      hide-details="auto"
                      v-model="form.tags"
                      multiple
                      dense
                      :label='$t("tags")'
                      :items="tags"
                      outlined
                    ></v-autocomplete>
                  </v-col>
                  <v-col cols="2">
                    <v-btn style="height: 40px" color="blue" outlined @click="add_tag = true; tag_name=''">
                      Qo'shish
                    </v-btn>
                  </v-col>
                </v-row>
              </v-col>
              <v-col v-if="col_is_active" cols="12">
                <v-autocomplete
                  v-model="form.is_active"
                  :items="[
                    { text: 'Aktiv', value: 1 },
                    { text: 'Noaktiv', value: 0 },
                  ]"
                  :rules="[(v) => v >= 0 || $t('input.required')]"
                  hide-details="auto"
                  :label='$t("blankTemplate.status")'
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn class="success" @click="save">{{ $t("save") }}</v-btn>
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
    <v-dialog v-model="image_modal" @keydown.esc="image_modal = false">
      <v-img :src="url_image_modal" @click="image_modal = false"></v-img>
    </v-dialog>
    <v-dialog v-model="like_modal" width="400" height="600" @keydown.esc="like_modal = false">
      <v-card>
        <v-card-title>
          <span>{{ $t('user.index') }}</span>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="like_modal = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-simple-table dense>
            <tbody>
            <tr v-for="(item, index) in likers_list" :key="index">
              <td class="text-center">{{ index + 1 }}</td>
              <td>{{ item.who_like.employee.lastname_uz_cyril + ' ' + item.who_like.employee.firstname_uz_cyril }}</td>
            </tr>
            </tbody>
          </v-simple-table>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog v-model="add_tag" max-width="500px">
      <v-card>
        <v-row class="ma-1">
          <v-col cols="10">
            <v-text-field hide-details="auto" outlined v-model="tag_name" dense label="Tag nomi"></v-text-field>
          </v-col>
          <v-col cols="2">
            <v-btn hide-details="auto" outlined style="height: 40px;" @click="insertTag()">OK</v-btn>
          </v-col>
        </v-row>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
const axios = require("axios").default;
import Swal from "sweetalert2";
import Observer from "@/components/Observer";

const moment = require("moment");
export default {
  name: "Index",
  components: {
    Observer,
  },
  data: () => ({
    loading: false,
    search: "",
    search_tag: "",
    dialog: false,
    editMode: null,
    like_modal: false,
    likers_list: {},
    items: [],
    departments: [],
    formData: [],
    form: {
      parent: null,
      parent_comment: null,
    },
    dialogHeaderText: "",
    expand: false,
    disableInput: false,
    observer: null,
    page: 1,
    auth_id: "",
    show: true,
    col_is_active: true,
    col_tags: true,
    image_modal: false,
    add_tag: false,
    url_image_modal: "",
    image_max_width: window.innerWidth - 100,
    image_max_height: window.innerHeight - 100,
    tags: [],
    tag_name: "",
  }),
  methods: {
    insertTag() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/timeline/inset-tag", {'tag_name': this.tag_name})
        .then((res) => {
          this.loading = false;
          this.add_tag = false;
          this.getRef();
        })
        .catch((e) => {
          this.loading = false;
          console.error(e);
        });
    },
    clear_search() {
      this.search = "";
      this.getList();
    },
    openLikeModal(list) {
      this.like_modal = true;
      this.likers_list = list;
    },
    open_image(id) {
      this.image_modal = true;
      this.url_image_modal =
        this.$store.state.backend_url + "timeline-file/" + id;
    },
    intersected() {
      this.getList();
      this.page++;
    },
    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/timeline", {
          page: this.page, search: this.search, search_tag: this.search_tag
        })
        .then((response) => {
          this.items = response.data.timeline.data;
          this.items = this.items.filter((v) => v.parent == null);
          this.items = this.items.map((v) => {
            v.files.map((f) => {
              f.file_ext = f.file_name.substring(
                f.file_name.lastIndexOf(".") + 1
              );
              f.file_ext = f.file_ext.toLowerCase();
              if (
                f.file_ext == "jpg" ||
                f.file_ext == "jpeg" ||
                f.file_ext == "png" ||
                f.file_ext == "gif"
              )
                f.file_type = true;
              else f.file_type = false;
            });

            return v;
          });
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    newItem() {
      this.form = {
        id: Date.now(),
        name: "",
        title: "",
        is_active: "",
        tags: [],
        parent: null,
        parent_comment: null,
      };
      this.dialogHeaderText = "Добавить новый лента";
      this.dialog = true;
      this.editMode = false;
      this.disableInput = false;
      this.show = true;
      this.col_is_active = true;
      this.col_tags = true;
    },
    editItem(item, show_file, show_status) {
      this.formIndex = this.items.indexOf(item);
      this.form = Object.assign({}, item);
      this.form.tags = item.timeline_tags.map(v => v.tag.id);
      this.dialogHeaderText = "Редактировать лента";
      this.dialog = true;
      this.editMode = false;
      this.disableInput = false;
      this.col_tags = true;
      this.show = show_file;
      this.col_is_active = show_status;
    },
    commentUpdate(item) {
      this.form = {
        id: Date.now(),
        parent: item.id,
        name: "",
        title: item.title,
        is_active: 1,
      };
      this.dialogHeaderText = "Комментировать";
      this.dialog = true;
      this.editMode = false;
      this.disableInput = true;
      this.show = false;
      this.col_is_active = false;
      this.col_tags = false;
    },
    commentToComment(comment, item_id) {
      this.form = {
        id: Date.now(),
        parent: item_id,
        name: "",
        parent_comment: comment.id,
        title: comment.name,
        is_active: 1,
      };
      this.dialogHeaderText = "Комментировать";
      this.dialog = true;
      this.editMode = false;
      this.disableInput = true;
      this.show = false;
      this.col_is_active = false;
      this.col_tags = false;
    },
    save() {
      if (this.$refs.dialogForm.validate()) {
        this.loading = true;
        this.formData = new FormData();
        this.formData.append("id", this.form.id);
        if (this.form.files) {
          for (var i = 0; i < this.form.files.length; i++) {
            let file = this.form.files[i];
            this.formData.append("file[" + i + "]", file);
          }
        }
        this.formData.append("parent", this.form.parent);
        this.formData.append("parent_comment", this.form.parent_comment);
        this.formData.append("name", this.form.name);
        this.formData.append("title", this.form.title);
        this.formData.append('tags', JSON.stringify(this.form.tags));
        this.formData.append("is_active", this.form.is_active);
        axios
          .post(
            this.$store.state.backend_url + "api/timeline/update", this.formData
          )
          .then((res) => {
            this.getList();
            this.dialog = false;
            const Toast = Swal.mixin({
              toast: true,
              position: "top-end",
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true,
              onOpen: (toast) => {
                toast.addEventListener("mouseenter", Swal.stopTimer);
                toast.addEventListener("mouseleave", Swal.resumeTimer);
              },
            });

            Toast.fire({
              icon: "success",
              title: this.$t("create_update_operation"),
            });
          })
          .catch((err) => {
            console.log(err);
          });
      }
    },
    collapse(id) {
      document.getElementById(id).hidden = !document.getElementById(id).hidden;
      this.expand = !this.expand;
    },
    deleteItem(item) {
      const index = this.items.indexOf(item);
      Swal.fire({
        title: this.$t("swal_title"),
        text: this.$t("swal_text"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: this.$t("swal_delete"),
      }).then((result) => {
        if (result.value) {
          axios
            .delete(
              this.$store.state.backend_url + "api/timeline/delete/" + item.id
            )
            .then((res) => {
              this.getList();
              this.dialog = false;
              Swal.fire("Deleted!", this.$t("swal_deleted"), "success");
            })
            .catch((err) => {
              Swal.fire({
                icon: "error",
                title: this.$t("swal_error_title"),
                text: this.$t("swal_error_text"),
              });
              console.log(err);
            });
        }
      });
    },
    deleteFile(file) {
      Swal.fire({
        title: this.$t("swal_title"),
        text: this.$t("swal_text"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: this.$t("swal_delete"),
      }).then((result) => {
        if (result.value) {
          axios
            .post(this.$store.state.backend_url + "api/timeline/delete-file", {
              file: file,
            })
            .then((res) => {
              this.getList();
              this.dialog = false;
              Swal.fire("Deleted!", this.$t("swal_deleted"), "success");
            })
            .catch((err) => {
              Swal.fire({
                icon: "error",
                title: this.$t("swal_error_title"),
                text: this.$t("swal_error_text"),
              });
              console.log(err);
            });
        }
      });
    },
    getUser() {
      this.auth_id = this.$store.getters.getUser().id;
      // axios
      //   .get(this.$store.state.backend_url + "api/users/show")
      //   .then((res) => {
      //   })
      //   .catch((e) => {
      //     console.error(e);
      //   });
    },
    likeComment(item_id, type) {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/timeline/like/" + item_id, {'type': type})
        .then((response) => {
          this.loading = false;
          this.getList();
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getRef() {
      axios
        .get(this.$store.state.backend_url + "api/timeline/get-ref")
        .then((res) => {
          this.tags = res.data;
          this.tags = this.tags.map(v => ({
            value: v.id,
            text: v.tag_name
          }));
        })
        .catch((e) => {
          console.error(e);
        });
    }
  },
  mounted() {
    this.getUser();
    this.getList();
    this.getRef();
  },
};
</script>

<style scoped>
.fullHeight {
  height: calc(100% - 0px);
}
.heightFull {
  height: 100%;
  background: #fff;
}
.headerTitle {
  width: 100%;
  color: #000;
  font-size: 18px;
  line-height: 1.4;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.dialogTitle {
  color: #000;
  font-size: 16px;
  line-height: 1.4;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.labelTitle {
  color: #676768;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.headerSearch {
  width: 100%;
  height: 34px;
}
.txt_search1 {
  border: 1px solid #e6e6e6;
  box-shadow: none;
  max-height: 100%;
  overflow: hidden;
  border-radius: 5px 0px 0px 5px;
  color: #212529;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.txt_searchBtn {
  background: #ff9f0e;
  border: 0.2px rgba(0, 0, 0, 0.28) solid;
  box-shadow: none;
  min-width: 25px !important;
  height: 34px !important;
  border-radius: 1px;
  width: 25px;
  padding: 0 13px;
}
.filterBtn {
  color: #000;
  font-size: 12px;
  font-weight: 600;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
  border: 1px solid #e6e6e6;
  /* border-right: 0px; */
  border-left: 0px;
  background: #fff;
  box-shadow: none;
  border-radius: 0;
  text-transform: none;
}
.v-data-table {
  line-height: 13px !important;
}
.doc-template_data-table > .v-data-table__wrapper > table > tbody > tr > td {
  color: #676768;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.dropdown-list .v-list-item .v-list-item__title {
  color: #000;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.dialog-head_title {
  color: #000;
  font-size: 14px;
  font-weight: 600;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.v-dialog > .v-card > .v-card__text {
  padding: 0px 0px 0px 0px;
}
.doc-template_data-table table > tbody > tr > td {
  white-space: normal;
  max-width: 50px;
  height: 43px;
  margin: 0 auto;
  font-size: 14px;
  line-height: 1.4;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>
