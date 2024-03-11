<template>
  <div>
    <v-form ref="documentBlankForm">
      <v-card class="ma-1 pa-1">
        <v-card-title class="pa-1" primary-title>
          {{ $t("documentBlank.index") }}
          <v-spacer></v-spacer>
          <v-btn color="success lighten-1" small @click="save()">{{
            $t("save")
          }}</v-btn>
        </v-card-title>
        <v-card-text class="pa-0">
          <v-row class="ma-1">
            <v-col class="pa-1">
              <v-autocomplete
                :label="$t('blankTemplate.index')"
                clearable
                v-model="form_blank_id"
                :items="blanks"
                hide-details
                dense
                item-value="id"
                item-text="blank_name"
                outlined
              >
              </v-autocomplete>
            </v-col>
            <v-col class="pa-1">
              <v-btn color="success" @click="addDocumentBlank">{{
                $t("add")
              }}</v-btn>
            </v-col>
          </v-row>
        </v-card-text>
        <v-card outlined v-for="blank in form" :key="blank.index" class="mb-2">
          <v-card-title class="py-1 grey lighten-5">
            {{ blank.blank_name }}
            <v-spacer></v-spacer>
            <v-btn icon small color="error" @click="itemDelete(blank.id)">
              <v-icon>mdi-delete</v-icon>
            </v-btn>
          </v-card-title>
          <v-divider></v-divider>
          <v-card-text>
            <v-row
              v-for="(
                blank_attribute, att_index
              ) in blank.document_blank_attribute"
              :key="att_index"
            >
              <v-col class="py-1">
                <v-text-field
                  v-model="blank_attribute.attribute_name"
                  readonly
                  dense
                  outlined
                  hide-details
                ></v-text-field>
              </v-col>
              <v-col class="py-1">
                <v-autocomplete
                  v-model="blank_attribute.relation_type"
                  :items="info_types"
                  dense
                  hide-details
                  outlined
                  :rules="[(v) => !!v || $t('input_required')]"
                ></v-autocomplete>
              </v-col>
              <v-col class="py-1">
                <v-autocomplete
                  v-model="blank_attribute.relation_attribute"
                  :items="
                    blank_attribute.relation_type == 1
                      ? attribute_emp
                      : blank_attribute.relation_type == 2
                      ? documentDetailAttributes
                      : blank_attribute.relation_type == 3
                      ? attribute_document
                      : []
                  "
                  dense
                  outlined
                  hide-details
                  :rules="[(v) => !!v || $t('input_required')]"
                ></v-autocomplete>
              </v-col>
              <v-col
                class="py-1"
                v-if="
                  (documentDetailAttributes.find((v) => {
                    if (v.id == blank_attribute.relation_attribute) return v;
                  }) &&
                    documentDetailAttributes.find((v) => {
                      if (v.id == blank_attribute.relation_attribute) return v;
                    }).data_type_id == 3) ||
                  (attribute_document.find((v) => {
                    if (v.value == blank_attribute.relation_attribute) return v;
                  }) &&
                    attribute_document.find((v) => {
                      if (v.value == blank_attribute.relation_attribute)
                        return v;
                    }).data_type_id == 3)
                "
              >
                <v-autocomplete
                  v-model="blank_attribute.date_format"
                  :items="date_format"
                  dense
                  hide-details
                  outlined
                  :rules="[(v) => !!v || $t('input_required')]"
                ></v-autocomplete>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-card>
    </v-form>

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
import Swal from "sweetalert2";
export default {
  props: ["attributes"],
  data() {
    return {
      loading: false,
      template_id: null,
      form_blank_id: null,
      form: [],
      documentDetailAttributes: [],
      attribute_emp: [
        { text: "full fio", value: "full_fio", data_type_id: 1 },
        { text: "short fio", value: "short_fio", data_type_id: 1 },
        { text: "tabel", value: "tabel", data_type_id: 1 },
        { text: "tel", value: "telefon", data_type_id: 1 },
        { text: "ip tel", value: "ip_telefon", data_type_id: 2 },
        { text: "pass_seria", value: "pass_seria", data_type_id: 1 },
        { text: "pass_number", value: "pass_number", data_type_id: 2 },
        { text: "staff", value: "staff", data_type_id: 1 },
        { text: "department", value: "department", data_type_id: 1 },
        { text: "position", value: "position", data_type_id: 1 },
      ],
      info_types: [
        { text: "employee", value: 1 },
        { text: "attribute", value: 2 },
        { text: "document", value: 3 },
      ],
      blanks: [],
      date_format: [
        { text: "sana", value: 1 },
        { text: "kun", value: 2 },
        { text: "Oy", value: 3 },
        { text: "Yil", value: 4 },
      ],
      attribute_document: [
        { text: "document number", value: "doc_number", data_type_id: 1 },
        { text: "document date", value: "doc_date", data_type_id: 3 },
        { text: "document signer", value: "doc_signer", data_type_id: 1 },
      ],
    };
  },
  methods: {
    addDocumentBlank() {
      if (
        this.form_blank_id &&
        !this.form.find((v) => {
          if (v.blank_id == this.form_blank_id) return true;
        })
      ) {
        let new_blank = this.blanks.find((v) => {
          if (v.id == this.form_blank_id) return v;
        });
        new_blank.blank_attribute_template.map((v) => {
          v.blank_attribute_id = v.id;
          v.id = Date.now();
        });
        this.form.push({
          id: Date.now(),
          document_template_id: this.$route.params.id,
          blank_id: this.form_blank_id,
          blank_name: new_blank.blank_name,
          document_blank_attribute: new_blank.blank_attribute_template,
        });
      }
    },
    getBlanks() {
      this.loading = true;
      axios
        .get(this.$store.state.backend_url + "api/blank-templates/get-blanks")
        .then((res) => {
          this.blanks = res.data;
          this.loading = false;
        })
        .catch((err) => {
          this.loading = false;
          console.log(err);
        });
    },
    getAttributes() {
      this.loading = true;
      axios
        .get(
          this.$store.state.backend_url +
            "api/document-detail-attributes/" +
            this.$route.params.id
        )
        .then((res) => {
          this.documentDetailAttributes = res.data;
          this.documentDetailAttributes.map((v) => {
            v.text = v["attribute_name_" + this.$i18n.locale];
            v.value = v.id;
          });
          this.loading = false;
        })
        .catch((err) => {
          this.loading = false;
          console.log(err);
        });
    },
    getDocumentBlankTemplate() {
      this.loading = true;
      axios
        .get(
          this.$store.state.backend_url +
            "api/document-blank-templates/edit/" +
            this.$route.params.id
        )
        .then((res) => {
          this.form = res.data;
          this.form.map((v) => {
            v.document_blank_attribute.map((va) => {
              va.relation_attribute =
                va.relation_type == 2
                  ? parseFloat(va.relation_attribute)
                  : va.relation_attribute;
              va.attribute_name = va.blank_attribute_template.attribute_name;
            });
            v.blank_name = v.blank_template.blank_name;
          });
          console.log(this.form);
          this.loading = false;
        })
        .catch((err) => {
          this.loading = false;
          console.log(err);
        });
    },
    save() {
      if (this.$refs.documentBlankForm.validate()) {
        this.loading = true;
        axios
          .post(
            this.$store.state.backend_url + "api/document-blank/update",
            this.form
          )
          .then((res) => {
            this.loading = false;
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
            this.loading = false;
            console.log(err);
          });
      }
    },
    itemDelete(id) {
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
              this.$store.state.backend_url +
                "api/document-blank-template/delete/" +
                id
            )
            .then((res) => {
              Swal.fire("Deleted!", this.$t("swal_deleted"), "success");
              this.form = this.form.filter((v) => {
                if (v.id != id) return v;
              });
            })
            .catch((err) => {
              Swal.fire({
                icon: "error",
                title: this.$t("swal_error_title"),
                text: this.$t("swal_error_text"),
                //footer: "<a href>Why do I have this issue?</a>"
              });
              console.log(err);
            });
        }
      });
    },
  },
  mounted() {
    this.getBlanks();
    this.getAttributes();
    this.getDocumentBlankTemplate();
  },
};
</script>
