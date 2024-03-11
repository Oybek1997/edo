<template>
  <div>
    <v-row class="mx-2 mt-1">
      <span>{{ $t('document.create') }}</span>
      <v-spacer></v-spacer>
    </v-row>
    <v-card class="mx-2 mb-3">
      <v-card-text>
        <v-row>
          <v-col cols="12" sm="6">
            <v-row>
              <v-col class="pt-0">
                <label>{{ $t('document.document_type_id') }}</label>
                <v-autocomplete
                  clearable
                  :items="documentTypes.map(v => {return {value:v.id, text:v.name}})"
                  v-model="form.document_type_id"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
            </v-row>
            <v-row>
              <v-col class="pt-0">
                <label>{{ $t('document.document_number') }}</label>
                <v-text-field
                  v-model="form.document_number"
                  :rules="[v => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
            </v-row>
            <v-row>
              <v-col class="pt-0">
                <label>{{ $t('document.document_sending_department') }}</label>
                <v-autocomplete
                  clearable
                  :items="departments.map(v => {return {value:v.id, text:v.department_name}})"
                  v-model="form.document_sending_department"
                  readonly
                  :rules="[v => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
            </v-row>
            <v-row>
              <v-col class="pt-0">
                <label>{{ $t('document.document_created_by') }}</label>
                <v-autocomplete
                  clearable
                  :items="[{text: user.name, value:user.id}]"
                  v-model="form.document_created_by"
                  readonly
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
            </v-row>
            <v-row>
              <v-col class="pt-0">
                <label>{{ $t('document.created_at') }}</label>
                <v-menu
                  v-model="createdAtMenu2"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="290px"
                >
                  <template v-slot:activator="{ on }">
                    <v-text-field
                      v-model="form.created_at"
                      readonly
                      v-on="on"
                      hide-details="auto"
                      dense
                      outlined
                    ></v-text-field>
                  </template>
                  <v-date-picker v-model="form.created_at" @input="createdAtMenu2 = false"></v-date-picker>
                </v-menu>
              </v-col>
            </v-row>
          </v-col>
          <v-col cols="12" sm="6">
            <v-row>
              <v-col class="pt-0">
                <label>{{ $t('document.confirmers') }}</label>
                <v-autocomplete
                  clearable
                  :items="chiefs.map(v => ({text:v.name + '(' + v.position + ')', value:v.id}))"
                  v-model="form.confirmers"
                  multiple
                  chips:rules="[v => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
            </v-row>
            <v-row>
              <v-col class="pt-0">
                <v-checkbox
                  v-model="form.should_receive_all_departments"
                  :label="$t('document.should_receive_all_departments')"
                ></v-checkbox>
              </v-col>
            </v-row>
            <v-row v-if="!form.should_receive_all_departments">
              <v-col class="pt-0">
                <label>{{ $t('document.document_receiving_department') }}</label>
                <v-autocomplete
                  clearable
                  :items="departments.map(v => ({text:v.department_name, value: v.id}))"
                  v-model="form.document_receiving_department"
                  :rules="[v => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
            </v-row>
            <v-row>
              <v-col class="pt-0">
                <label>{{ $t('document.attachments') }}</label>
                <v-file-input
                  v-model="form.attachments"
                  small-chips
                  multiple
                  show-size
                  counter
                  dense
                  outlined
                ></v-file-input>
              </v-col>
            </v-row>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12" class="pt-0">
            <label>{{ $t('document.title') }}</label>
            <v-text-field
              v-model="form.title"
              :rules="[v => !!v || $t('input.required')]"
              hide-details="auto"
              dense
              outlined
            ></v-text-field>
          </v-col>
          <v-col cols="12" class="pt-0">
            <quillEditor :options="editorOption" v-model="form.document_content"></quillEditor>
          </v-col>
          <v-col cols="12" class="pt-0">
            <v-spacer></v-spacer>
            <v-btn color="success" class="float-right">{{ $t('save') }}</v-btn>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>
  </div>
</template>

<script>
const axios = require("axios").default;
const moment = require("moment");
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";
import { quillEditor } from "vue-quill-editor";

export default {
  components: {
    quillEditor
  },
  data: () => ({
    loading: false,
    editorOption: {
      modules: {
        toolbar: [
          ["bold", "italic", "underline"],
          [{ align: "" }],
          [{ align: "center" }],
          [{ align: "justify" }],
          [{ align: "right" }],
          [
            {
              color: [
                "black",
                "red",
                "green",
                "blue",
                "yellow",
                "orange",
                "white"
              ]
            },
            { background: [] }
          ],
          [{ indent: "-1" }, { indent: "+1" }],
          [{ list: "ordered" }, { list: "bullet" }],
          ["blockquote", "code-block"],
          [{ header: 1 }, { header: 2 }],
          [{ script: "sub" }, { script: "super" }],
          // [{ direction: "rtl" }],
          [{ size: ["small", false, "large", "huge"] }],
          [{ header: [1, 2, 3, 4, 5, 6, false] }],
          [{ font: [] }],
          ["clean"],
          ["image"]
          // ["link", "image", "video"]
        ],
        syntax: {
          highlight: text => hljs.highlightAuto(text).value
        }
      }
    },
    form: {
      title: null,
      document_content: null,
      document_type_id: null,
      document_number: null,
      document_sending_department: null,
      document_receiving_department: null,
      document_created_by: null,
      created_at: moment().format("YYYY-MM-DD"),
      should_receive_all_departments: false,
      confirmers: null,
      attachments: null
    },
    departments: [],
    chiefs: [],
    users: [],
    documentTypes: [],
    createdAtMenu2: false
  }),
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    user() {
      return this.$store.getters.getUser();
    }
  },
  methods: {
    getList() {
      this.loading = true;
      let user = this.$store.getters.getUser();
      axios
        .get(this.$store.state.backend_url + "api/documents/create/" + user.id)
        .then(res => {
          this.chiefs = res.data.chiefs;
          this.departments = res.data.departments;
          this.documentTypes = res.data.documentTypes;
        });
    }
  },
  mounted() {
    this.getList();
    this.form.document_sending_department = this.user.department_id;
    this.form.document_created_by = this.user.id;
  }
};
</script>
