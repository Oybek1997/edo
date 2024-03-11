<template>
  <div>
    <v-card class="ma-1 pa-1 mr-0" style="overflow: hidden">
      <v-card-title class="pa-0 pb-1" style="color: #000" color="black">
        {{ document.document_number }} / {{ document.document_date
        }}<v-spacer></v-spacer>
        <v-btn
          color="success"
          fab
          icon
          outlined
          small
          class="ma-1"
          @click="newItem"
          ><v-icon>mdi-plus</v-icon></v-btn
        >
      </v-card-title>
      <v-card-text>
        <v-simple-table class="mainTable">
          <template v-slot:default>
            <thead>
              <tr>
                <th class="text-left">#</th>
                <th class="text-left">Signers</th>
                <th class="text-left">Department</th>
                <th class="text-left">Position</th>
                <th class="text-left">Action</th>
                <th class="text-left">Sequence</th>
                <th class="text-left" width="150">Taken/Due/Signed datetime</th>
                <th class="text-left">Status</th>
                <th class="text-left">Rezolutsiya</th>
                <th class="text-left"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, key) in document.document_signers" :key="key">
                <td>{{ key + 1 }}</td>
                <td v-if="item.signer_employee_id">
                  {{
                    item.signer_employee["firstname_" + $i18n.locale] +
                    " " +
                    item.signer_employee["lastname_" + $i18n.locale]
                  }}
                </td>
                <td v-else>
                  <div v-for="(e, ei) in item.staff.employees" :key="ei">
                    {{
                      e[
                        "firstname_" +
                          ($i18n.locale == "uz_latin" ? "uz_latin" : "uz_cyril")
                      ] +
                      " " +
                      e[
                        "lastname_" +
                          ($i18n.locale == "uz_latin" ? "uz_latin" : "uz_cyril")
                      ]
                    }}
                  </div>
                </td>
                <td>
                  {{
                    item.staff.department
                      ? item.staff.department["name_" + $i18n.locale]
                      : ""
                  }}
                </td>
                <td>{{ item.staff.position["name_" + $i18n.locale] }}</td>
                <td>{{ item.action_type["name_" + $i18n.locale] }}</td>
                <td>{{ item.sequence }}</td>
                <td
                  v-html="
                    item.taken_at +
                    '<br>' +
                    item.due_at +
                    '<br>' +
                    item.signed_at
                  "
                ></td>
                <td>
                  <v-icon
                    :color="
                      item.status == 0
                        ? ''
                        : item.status == 1
                        ? 'success'
                        : item.status == 2
                        ? 'danger'
                        : item.status == 4
                        ? 'warning'
                        : 'info'
                    "
                  >
                    {{
                      item.status == 0
                        ? ""
                        : item.status == 1
                        ? "mdi-check-all"
                        : item.status == 2
                        ? "mdi-close"
                        : item.status == 4
                        ? "mdi-alert-outline"
                        : "mdi-timer-sand"
                    }}
                  </v-icon>
                </td>
                <td>
                  <v-icon>{{
                    item.is_done == 2
                      ? "mdi-check"
                      : item.is_done == 1
                      ? "mdi-check-all"
                      : ""
                  }}</v-icon>
                </td>
                <td>
                  <v-btn icon color="primary" @click="editItem(item)">
                    <v-icon>mdi-pencil</v-icon>
                  </v-btn>
                  <v-btn icon color="error" @click="deleteItem(item)">
                    <v-icon>mdi-delete</v-icon>
                  </v-btn>
                </td>
              </tr>
            </tbody>
          </template>
        </v-simple-table>
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
    <v-dialog
      v-model="dialog"
      scrollable
      persistent
      :overlay="false"
      max-width="700px"
      transition="dialog-transition"
    >
      <v-card>
        <v-card-title primary-title>
          Edit
          <v-spacer></v-spacer>
          <v-select
            :items="[
              { value: 0, text: 'New' },
              { value: 1, text: 'Published' },
              { value: 2, text: 'Processing' },
              { value: 3, text: 'Signed' },
              { value: 4, text: 'Ready' },
              { value: 5, text: 'Complated' },
              { value: 6, text: 'Cancel' },
            ]"
            v-model="form.document_status"
            outlined
            hide-details
            dense
            label="Document status"
          ></v-select>
          <v-spacer></v-spacer>
          <v-btn color="error" @click="dialog = !dialog" icon
            ><v-icon>mdi-close</v-icon></v-btn
          >
        </v-card-title>
        <v-card-text>
          <v-row>
            <v-col cols="6">
              <v-autocomplete
                label="Action type"
                :items="actionTypes"
                item-value="id"
                :item-text="'name_' + $i18n.locale"
                v-model="form.action_type_id"
                clearable
                hide-details
                dense
                outlined
              ></v-autocomplete>
            </v-col>
            <v-col cols="6">
              <v-autocomplete
                label="Signed employee"
                :items="form.staff ? form.staff.employees : []"
                item-value="id"
                v-model="form.signer_employee_id"
                clearable
                hide-details
                dense
                outlined
              >
                <template v-slot:item="data">
                  {{
                    data.item["firstname_" + $i18n.locale] +
                    " " +
                    data.item["lastname_" + $i18n.locale]
                  }}
                </template>
                <template v-slot:selection="data">
                  {{
                    data.item["firstname_" + $i18n.locale] +
                    " " +
                    data.item["lastname_" + $i18n.locale]
                  }}
                </template>
              </v-autocomplete>
            </v-col>
            <v-col cols="12">
              <v-autocomplete
                clearable
                v-model="form.staff_id"
                :items="staff"
                item-text="staffInfo"
                item-value="id"
                hide-details
                label="Staff"
                dense
                outlined
                full-width
                class="my-1"
                @keyup="getStaff()"
                :search-input.sync="search_staff"
              >
                <template v-slot:selection="{ item }">
                  <v-row class="ma-0 pa-0" style="font-size: 12px">
                    <v-col cols="12" class="ma-0 pa-0">
                      <b>{{
                        item && item.department
                          ? item.department.code + " " + item.department.text
                          : ""
                      }}</b>
                    </v-col>
                    <v-col cols="12" class="ma-0 pa-0">
                      <b>{{
                        item && item.position ? item.position.text : ""
                      }}</b>
                    </v-col>
                  </v-row>
                </template>
                <template v-slot:item="{ item }">
                  <v-row
                    style="
                      border-bottom: 1px solid #ccc;
                      font-size: 14px;
                      max-width: 700px;
                    "
                    class="ma-0 pa-0"
                  >
                    <v-col cols="12" class="ma-0 pa-0">
                      <b>{{
                        item && item.department
                          ? item.department.code + " " + item.department.text
                          : ""
                      }}</b>
                    </v-col>
                    <v-col cols="12" class="ma-0 pa-0">
                      <b>{{
                        item && item.position ? item.position.text : ""
                      }}</b>
                    </v-col>
                  </v-row>
                </template>
              </v-autocomplete>
            </v-col>
            <v-col cols="6">
              <v-autocomplete
                label="Parent employee"
                :items="form.parent_employee_id ? [form.parent_employee] : []"
                item-value="id"
                v-model="form.parent_employee_id"
                clearable
                hide-details
                dense
                outlined
              >
                <template v-slot:item="data">
                  {{
                    data.item["firstname_" + $i18n.locale] +
                    " " +
                    data.item["lastname_" + $i18n.locale]
                  }}
                </template>
                <template v-slot:selection="data">
                  {{
                    data.item["firstname_" + $i18n.locale] +
                    " " +
                    data.item["lastname_" + $i18n.locale]
                  }}
                </template>
              </v-autocomplete>
            </v-col>

            <v-col cols="6">
              <v-autocomplete
                label="Sequence"
                :items="sequences"
                item-value="id"
                v-model="form.sequence"
                clearable
                hide-details
                dense
                outlined
              >
                <template v-slot:item="{ item }">
                  {{ item }}
                </template>
                <template v-slot:selection="{ item }">
                  {{ item }}
                </template>
              </v-autocomplete>
            </v-col>
            <v-col cols="6">
              <v-autocomplete
                label="Status"
                :items="[
                  { text: 'New', value: 0 },
                  { text: 'Signed', value: 1 },
                  { text: 'Processing', value: 3 },
                  { text: 'Asoslab berilsin', value: 4 },
                  { text: 'Canceled', value: 2 },
                ]"
                item-text="text"
                item-value="value"
                v-model="form.status"
                clearable
                hide-details
                dense
                outlined
              >
              </v-autocomplete>
            </v-col>
            <v-col cols="6">
              <v-autocomplete
                label="Rezolutsiya"
                :items="[
                  { text: 'Yo\'q', value: 0 },
                  { text: 'Berildi.', value: 2 },
                  { text: 'Bajarildi', value: 1 },
                ]"
                item-text="text"
                item-value="value"
                v-model="form.is_done"
                clearable
                hide-details
                dense
                outlined
              >
              </v-autocomplete>
            </v-col>
            <v-col cols="6">
              <v-text-field
                label="Taken date time"
                v-model="form.taken_datetime"
                placeholder="yyyy-mm-dd hh:mm:ss"
                clearable
                hide-details
                dense
                outlined
                type="datetime"
              ></v-text-field>
            </v-col>
            <v-col cols="6">
              <v-text-field
                label="Due date time"
                v-model="form.due_date"
                placeholder="yyyy-mm-dd hh:mm:ss"
                clearable
                hide-details
                dense
                outlined
              ></v-text-field>
            </v-col>
          </v-row>
          <v-row>
            <v-spacer></v-spacer>
            <v-btn color="success" @click="save" class="ma-1">Save</v-btn>
          </v-row>

          <!--id:null,
          // action_type_id:null,
          // signer_employee_id:null,
          // parent_employee_id:null,
          // staff_id:null,
          // sequence:null,
          // status:null
          taken_datetime:null,
          due_date:null,
          -->
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
const axios = require("axios").default;
import Swal from "sweetalert2";
export default {
  data() {
    return {
      sequences: [],
      search_staff: "",
      loading: false,
      actionTypes: [],
      document: {},
      signers: [],
      staff: [],
      dialog: false,
      form: {
        id: null,
        action_type_id: null,
        taken_datetime: null,
        due_date: null,
        parent_employee_id: null,
        sequence: null,
        sign_type: null,
        signed_date: null,
        signer_employee_id: null,
        staff_id: null,
        status: null,
      },
      pdf_file_name: "",
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
          id: 5,
          name_uz_latin: "Ma'lumot uchun",
          name_uz_cyril: "Маълумот учун",
          name_ru: "Для информации",
        },
        // {
        //   id: 13,
        //   name_uz_latin: "Hujjat yaratuvchisi",
        //   name_uz_cyril: "Ҳужжат яратувчиси",
        //   name_ru: "Создатель документа",
        // },
        {
          id: 14,
          name_uz_latin: "Taqatuvchi",
          name_uz_cyril: "Тарқатувчи",
          name_ru: "Рассылки",
        },
      ],
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
        {
          name_uz_latin: "Oldindan kelishuv",
          name_uz_cyril: "Олдидан килишув",
          name_ru: "Предсогласование",
          color: "light-green",
        },
      ],
    };
  },
  methods: {
    newItem() {
      this.form = {
        id: null,
        document_id: this.document.id,
        action_type_id: null,
        taken_datetime: null,
        due_date: null,
        parent_employee_id: null,
        sequence: null,
        sign_type: null,
        signed_date: null,
        signer_employee_id: null,
        staff_id: null,
        status: null,
        document_status: null,
      };
      this.form.document_status = this.document.status;
      this.dialog = true;
    },
    editItem(item) {
      this.form = item;
      this.staff = [item.staff].map((v) => {
        v.staffInfo = "";
        v.staffInfo += v.department.department_code;
        v.department.code = v.department.department_code;
        v.staffInfo += " ";
        v.staffInfo += v.department["name_" + this.$i18n.locale];
        v.department.text = v.department["name_" + this.$i18n.locale];
        v.staffInfo += " ";
        v.staffInfo += v.position["name_" + this.$i18n.locale];
        v.position.text = v.position["name_" + this.$i18n.locale];
        return v;
      });
      this.form.document_status = this.document.status;
      this.dialog = true;
    },
    getList() {
      this.loading = true;
      this.pdf_file_name = this.$route.params.pdf_file_name;
      axios
        .post(
          this.$store.state.backend_url + "api/documents/show-document-signers",
          {
            pdf_file_name: this.pdf_file_name,
          }
        )
        .then((res) => {
          this.loading = false;
          this.document = res.data.document;
          this.actionTypes = res.data.action_types;
        });
    },
    save(item) {
      this.loading = true;
      axios
        .post(
          this.$store.state.backend_url + "api/document-signers/update",
          this.form
        )
        .then((res) => {
          this.loading = false;
          this.getList();
        });
    },
    deleteItem(item) {
      this.loading = true;
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
                "api/document-signers/delete/" +
                item.id
            )
            .then((res) => {
              this.loading = false;
              this.getList();
            });
        } else this.loading = false;
      });
    },
    getStaff() {
      this.isLoading = true;
      axios
        .post(this.$store.state.backend_url + "api/get-staffs", {
          search: this.search_staff,
          language: this.$i18n.locale,
        })
        .then((res) => {
          let staff = res.data.data.map((v) => {
            v.staffInfo = "";
            v.department = {};
            v.position = {};
            v.staffInfo += v.department_code;
            v.department.code = v.department_code;
            v.staffInfo += " ";
            v.staffInfo += v["department_name_" + this.$i18n.locale];
            v.department.text = v["department_name_" + this.$i18n.locale];
            v.staffInfo += " ";
            v.staffInfo += v["position_name_" + this.$i18n.locale];
            v.position.text = v["position_name_" + this.$i18n.locale];
            return v;
          });
          this.staff = this.staff.concat(staff);
          this.isLoading = false;
        })
        .catch((err) => {
          this.isLoading = false;
          console.error(err);
        });
    },
  },
  mounted() {
    this.getList();
    for (let i = 100; i >= 0; i--) {
      this.sequences.push(i);
    }
  },
};
</script>
