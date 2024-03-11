<template>
  <div>
    <v-form ref="documentCreateForm">
      <v-card class="ma-1 pa-1">
        <v-card-title class="pa-1">
          <span>{{ documentTitle }}</span>
          <!-- <v-btn color="success" small dark @click="test()">{{
            $t("test")
          }}</v-btn> -->
          <v-spacer></v-spacer>
          <Help />
          <v-btn color="success" small dark @click="save()">{{
            $t("save")
          }}</v-btn>
        </v-card-title>
        <v-divider class="ma-2"></v-divider>
        <v-card class="mt-3">
          <v-card-title class="grey lighten-3 pa-1 pl-3">
            {{ documentName }}
            <v-spacer></v-spacer>
            <v-text-field
              v-model="document.responsible_contact"
              dense
              outlined
              :label="$t('document.responsible_contact')"
              :rules="[(v) => !!v || $t('input.required')]"
              hide-details="auto"
              class="white mr-1"
            ></v-text-field>
            <v-text-field
              v-model="document.title"
              dense
              outlined
              :label="$t('document.title')"
              hide-details="auto"
              class="white mr-1"
              :rules="[(v) => !!v || $t('input.required')]"
            ></v-text-field>
          </v-card-title>

          <v-card-text v-if="model">
            <v-card
              class="mt-2 mx-auto"
              outlined
              v-for="(
                document_detail, detail_index
              ) in document.document_details"
              :key="detail_index"
            >
              <v-card-title class="white lighten-3 pa-1 pl-3">
                {{ $t("document.punkt") }} {{ detail_index + 1 }}
                <v-spacer></v-spacer>
                <div
                  class="d-flex"
                  v-if="
                    document.document_template_id == 558 &&
                    $route.params.documentTemplateId
                  "
                >
                  <v-text-field
                    v-model="mehmonPassport"
                    @keyup.native.enter="getGuestInformation()"
                    dense
                    outlined
                    type="text"
                    hide-details
                    label="Passport seriya va raqami"
                    style="max-width: 170px; min-width: 140px"
                    class="mr-3 white"
                  >
                  </v-text-field>
                  <v-btn color="success" icon @click="getGuestInformation">
                    <v-icon>mdi-account-search</v-icon>
                  </v-btn>
                </div>
                <v-btn
                  color="error"
                  v-if="detail_index != 0"
                  icon
                  @click="deleteDocumentDetail(document_detail)"
                >
                  <v-icon>mdi-delete</v-icon>
                </v-btn>
                <v-btn
                  v-else-if="
                    document.document_template_id != 157 &&
                    document.document_template_id != 158 &&
                    document.document_template_id != 12 &&
                    document.document_template_id != 474 &&
                    document.document_template_id != 622 &&
                    document.document_template_id != 558 &&
                    document.document_template_id != 597
                  "
                  color="success"
                  small
                  outlined
                  @click="addDocumentDetail"
                  >{{ $t("document.add_punkt") }}</v-btn
                >
              </v-card-title>
              <hr />

              <v-card-text class="pa-1">
                <v-row v-if="model">
                  <v-col
                    cols="12"
                    md="12"
                    v-if="document.is_content_visible && detail_index == 0"
                  >
                    <vue2-tinymce-editor
                      v-model="document_detail['content']"
                      :options="options"
                      v-if="true"
                    ></vue2-tinymce-editor>
                  </v-col>
                </v-row>

                <v-card
                  v-if="
                    model &&
                    (document.has_employee ||
                      document.document_template.change_staff == 1)
                  "
                  class="mt-1"
                  outlined
                >
                  <v-card-title class="white lighten-3 pa-1 pl-3">
                    {{ $t("employee.index") }}
                    <v-spacer></v-spacer>
                    <p class="my-1 mx-2">
                      <span
                        v-if="addEmpBtn[detail_index]"
                        @click="addEmployee(detail_index)"
                        style="
                          color: #1e88e5;
                          cursor: pointer;
                          text-decoration: underline;
                        "
                        >{{ successMessage }}</span
                      >
                      <span
                        class="ml-1"
                        v-if="errorEmp[detail_index]"
                        style="color: #e53935"
                        >{{ errorEmpMessage }}</span
                      >
                      <v-btn
                        color="success"
                        class="ml-2"
                        dark
                        @click="addEmployee(detail_index)"
                        v-if="
                          addEmpBtn[detail_index] && !errorEmp[detail_index]
                        "
                      >
                        <!-- <v-icon outlined left>mdi-plus</v-icon> -->
                        {{ $t("document.add_employee") }}
                      </v-btn>
                    </p>
                    <v-form
                      @keyup.native.enter="addEmployee(detail_index)"
                      v-if="
                        (document.document_details[0].document_detail_employees
                          .length < 1 ||
                          (document.document_template_id != 597 &&
                            document.document_template_id != 636)) &&
                        document.document_template_id != 622 &&
                        document.document_template_id != 615
                      "
                    >
                      <v-text-field
                        @keyup="getEmployee(detail_index)"
                        v-model="employeeTabel[detail_index]"
                        :id="'addEmployeeTabel' + detail_index"
                        dense
                        outlined
                        type="text"
                        hide-details
                        label="Tabel raqami"
                        style="max-width: 170px; min-width: 140px"
                        class="mr-3 white"
                      >
                      </v-text-field>
                    </v-form>
                  </v-card-title>
                  <hr />
                  <v-card-text class="pa-1">
                    <v-data-table
                      dense
                      :headers="headers.filter((v) => v.visible)"
                      :items="document_detail.document_detail_employees"
                      class="mainTable mt-2"
                      style="border: 1px solid #aaa"
                      :disable-pagination="true"
                      hide-default-footer
                    >
                      <template v-slot:item.id="{ item }">
                        {{
                          document_detail.document_detail_employees
                            .map(function (x) {
                              return x.id;
                            })
                            .indexOf(item.id) + 1
                        }}
                      </template>
                      <template v-slot:item.tariff_scale_id="{ item }">
                        {{ item.tariff_scale.category }}
                      </template>
                      <template v-slot:item.coefficients="{ item }">
                        <div v-for="(coef, i) in item.coefficients" :key="i">
                          {{
                            coef.coefficient.description +
                            ": " +
                            coef.percent +
                            "%"
                          }}
                        </div>
                      </template>
                      <template v-slot:item.range_id="{ item }">
                        {{ item.range ? item.range.code : "" }}
                      </template>
                      <template v-slot:item.actions="{ item }">
                        <v-btn
                          color="red"
                          small
                          text
                          @click="deleteEmployee(item, detail_index)"
                        >
                          <v-icon>mdi-delete</v-icon>
                        </v-btn>
                      </template>
                    </v-data-table>
                  </v-card-text>
                  <v-card-text v-if="document.document_template_id==12 || document.document_template_id==474 || document.document_template_id==592" class="pa-1">
                    <v-simple-table class="mainTable pa-1" style="border: 1px solid #aaa">
                      <template v-slot:default>
                      <!-- <thead>
                        <th>Joriy yil</th>
                        <th>Davr</th>
                        <th>Mehnat ta'tili</th>
                        <th>Moddiy yordam</th>
                        <th>Qo'shimcha ta'til</th>
                        <th>Ish staji uchun ta'til</th>
                      </thead> -->
                      <tbody>
                        <tr style="font-weight: bold">
                          <td>Joriy yil</td>
                          <td>Davr</td>
                          <td>Mehnat ta'tili</td>
                          <td>Moddiy yordam</td>
                          <td>Qo'shimcha ta'til</td>
                          <td>Ish staji uchun ta'til</td>
                        </tr>
                        <tr v-for="item in employeeVacations.usedVocation">
                          <td>{{item[0].interval2.substr(0,4)}}</td>
                          <td>{{item[0].interval1.substr(6,2) + "." + item[0].interval1.substr(4,2) + "." + item[0].interval1.substr(0,4) + " - " + item[0].interval2.substr(6,2) + "." + item[0].interval2.substr(4,2) + "." + item[0].interval2.substr(0,4)}}</td>
                          <td>{{ item.filter(v=>v.vocationtype=="T"  && v.max_day!=v.takedate).length>0 ? item.filter(v=>v.vocationtype=="T")[0].takedate + '/' + item.filter(v=>v.vocationtype=="T")[0].max_day : item.filter(v=>v.vocationtype=="T").length>0 ? '+' : '-' }}</td>
                          <td>{{ item.filter(v=>v.vocationtype=="T" && v.materilhelp.substr(0,1)==1).length>0 ? '+' : '-' }}</td>
                          <td>{{ item.filter(v=>v.vocationtype=="D").length>0 ? '+' : '-' }}</td>
                          <td>{{ item.filter(v=>v.vocationtype=="S").length>0 ? '+' : '-' }}</td>
                        </tr>
                      </tbody>
                    </template>
                    </v-simple-table>
                  </v-card-text>
                </v-card>

                <v-row>
                  <v-col
                    cols="6"
                    class="py-0"
                    v-for="(
                      attribute, i
                    ) in document_detail.document_detail_attribute_values"
                    :key="i"
                  >
                    <v-row
                      v-if="
                        user &&
                        user.employee &&
                        user.employee.employee_staff.find((v) => {
                          if (
                            v.staff_id == attribute.signer_staff_id ||
                            attribute.signer_staff_id == null
                          ) {
                            return true;
                          }
                        })
                      "
                    >
                      <v-col
                        cols="12"
                        md="12"
                        class="py-1"
                        v-if="attribute.type == 'string'"
                      >
                        <v-text-field
                          v-model.lazy="attribute.attribute_value"
                          dense
                          :label="attribute.attribute_name"
                          outlined
                          type="text"
                          hide-details="auto"
                          :rules="
                            attribute.required
                              ? [
                                  (v) => !!v || $t('input.required'),
                                  (v) =>
                                    (v &&
                                      v.length >= attribute.value_max_length) ||
                                    attribute.value_max_length +
                                      ' ' +
                                      $t('more_than_s'),
                                  (v) =>
                                    (v &&
                                      v.length <= attribute.value_min_length) ||
                                    attribute.value_min_length +
                                      ' ' +
                                      $t('and_less_s'),
                                ]
                              : []
                          "
                        ></v-text-field>
                      </v-col>
                      <v-col
                        cols="12"
                        md="12"
                        class="py-1"
                        v-if="attribute.type == 'text'"
                      >
                        <v-textarea
                          list="browsers"
                          v-model.lazy="attribute.attribute_value"
                          :label="attribute.attribute_name"
                          rows="1"
                          row-height="15"
                          dense
                          outlined
                          auto-grow
                          hide-details="auto"
                          :rules="
                            attribute.required
                              ? [
                                  (v) => !!v || $t('input.required'),
                                  (v) =>
                                    (v &&
                                      v.length >= attribute.value_max_length) ||
                                    attribute.value_max_length +
                                      ' ' +
                                      $t('more_than_s'),
                                  (v) =>
                                    (v &&
                                      v.length <= attribute.value_min_length) ||
                                    attribute.value_min_length +
                                      ' ' +
                                      $t('and_less_s'),
                                ]
                              : []
                          "
                        ></v-textarea>
                      </v-col>
                      <v-col
                        cols="12"
                        md="12"
                        class="py-1"
                        v-if="attribute.type == 'number'"
                      >
                        <v-text-field
                          v-model.lazy="attribute.attribute_value"
                          dense
                          :label="attribute.attribute_name"
                          outlined
                          type="number"
                          hide-details="auto"
                          :rules="
                            attribute.required
                              ? [
                                  (v) => !!v || $t('input.required'),
                                  (v) =>
                                    (!!v &&
                                      v >=
                                        parseInt(attribute.value_max_length)) ||
                                    attribute.value_max_length + ' dan k\'op',
                                  (v) =>
                                    (!!v &&
                                      v <=
                                        parseInt(attribute.value_min_length)) ||
                                    attribute.value_min_length + ' dan kam',
                                ]
                              : [
                                  (v) =>
                                    (!!v &&
                                      v >=
                                        parseInt(attribute.value_max_length)) ||
                                    attribute.value_max_length + ' dan k\'op',
                                  (v) =>
                                    (!!v &&
                                      v <=
                                        parseInt(attribute.value_min_length)) ||
                                    attribute.value_min_length + ' dan kam',
                                ]
                          "
                        ></v-text-field>
                      </v-col>
                      <v-col
                        cols="12"
                        md="12"
                        class="py-1"
                        v-if="attribute.type == 'date'"
                      >
                        <v-menu
                          v-model.lazy="menu['menu' + i + detail_index]"
                          :close-on-content-click="false"
                          :nudge-right="40"
                          transition="scale-transition"
                          offset-y
                          min-width="290px"
                        >
                          <template v-slot:activator="{ on }">
                            <v-text-field
                              v-model.lazy="attribute.attribute_value"
                              :label="attribute.attribute_name"
                              readonly
                              v-on="on"
                              hide-details="auto"
                              dense
                              outlined
                              :rules="
                                attribute.required
                                  ? [(v) => !!v || $t('input.required')]
                                  : attribute.d_d_attribute_id == 2382 &&
                                    attribute.attribute_value > '2024-12-31'
                                  ? [$t('Keyingi yil mumkin emas')]
                                  : []
                              "
                              placeholder="YYYY-MM-DD"
                            ></v-text-field>
                          </template>
                          <v-date-picker
                            no-title
                            v-model="attribute.attribute_value"
                            @input="menu['menu' + i + detail_index] = false"
                          ></v-date-picker>
                        </v-menu>
                      </v-col>
                      <v-col
                        cols="12"
                        md="12"
                        class="py-1"
                        v-if="attribute.type == 'datetime'"
                      >
                        <v-text-field
                          v-model.lazy="attribute.attribute_value"
                          :label="attribute.attribute_name"
                          hide-details="auto"
                          type="datetime-local"
                          dense
                          outlined
                          :rules="
                            attribute.required
                              ? [(v) => !!v || $t('input.required')]
                              : []
                          "
                        ></v-text-field>
                      </v-col>
                      <v-col
                        cols="12"
                        md="12"
                        class="py-1"
                        v-if="attribute.type == 'money'"
                      >
                        <v-text-field
                          v-model.lazy="attribute.attribute_value"
                          :label="attribute.attribute_name"
                          hide-details="auto"
                          dense
                          v-money="money"
                          outlined
                          :rules="
                            attribute.required
                              ? [(v) => !!v || $t('input.required')]
                              : []
                          "
                        ></v-text-field>
                      </v-col>
                      <v-col
                        cols="12"
                        md="12"
                        class="py-1"
                        v-if="attribute.type == 'checkbox'"
                      >
                        <v-checkbox
                          class="ma-1"
                          v-model.lazy="attribute.attribute_value"
                          hide-details="auto"
                          :label="attribute.attribute_name"
                        ></v-checkbox>
                      </v-col>
                      <v-col
                        cols="12"
                        md="12"
                        class="py-1"
                        v-if="attribute.type == 'list'"
                      >
                        <v-autocomplete
                          clearable
                          :label="attribute.attribute_name"
                          v-model.lazy="attribute.attribute_value"
                          @keyup="
                            getTableList(
                              i + '_' + detail_index,
                              attribute.table_list_id
                            )
                          "
                          @click="
                            getTableList(
                              i + '_' + detail_index,
                              attribute.table_list_id
                            )
                          "
                          :search-input.sync="
                            searchTable[i + '_' + detail_index]
                          "
                          :items="tableLists[i + '_' + detail_index]"
                          :rules="
                            attribute.required
                              ? [(v) => !!v || $t('input.required')]
                              : []
                          "
                          hide-details
                          dense
                          outlined
                          full-width
                          item-text="search"
                          item-value="id"
                          :loading="isLoading"
                        >
                          <template v-slot:item="{ item }">
                            <v-list-item-content>
                              <v-list-item-title
                                v-text="item.search"
                              ></v-list-item-title>
                            </v-list-item-content>
                          </template>
                        </v-autocomplete>
                      </v-col>
                      <v-col
                        cols="12"
                        md="12"
                        class="py-1"
                        v-if="attribute.type == 'staffs'"
                      >
                        <v-autocomplete
                          clearable
                          :label="attribute.attribute_name"
                          v-model="attribute.attribute_value"
                          :items="staffs"
                          :rules="
                            attribute.required
                              ? [(v) => !!v || $t('input.required')]
                              : []
                          "
                          hide-details
                          dense
                          outlined
                          full-width
                          item-text="search"
                          item-value="id"
                          :loading="isLoading"
                        >
                          <template v-slot:selection="{ item }">
                            <v-row class="ma-0 pa-0" style="font-size: 16px">
                              <v-col cols="12" class="ma-0 pa-0">{{
                                item["department_name_" + $i18n.locale]
                              }}</v-col>
                              <v-col cols="12" class="ma-0 pa-0">{{
                                item["position_name_" + $i18n.locale]
                              }}</v-col>
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
                              <v-col cols="12" class="ma-0 pa-0">{{
                                item["department_name_" + $i18n.locale]
                              }}</v-col>
                              <v-col cols="12" class="ma-0 pa-0">{{
                                item["position_name_" + $i18n.locale]
                              }}</v-col>
                            </v-row>
                          </template>
                        </v-autocomplete>
                      </v-col>
                      <v-col
                        cols="12"
                        md="12"
                        class="py-1"
                        v-if="attribute.type == 'staffs_all'"
                      >
                        <v-autocomplete
                          clearable
                          :label="attribute.attribute_name"
                          v-model.lazy="attribute.attribute_value"
                          :items="staffs_all"
                          :rules="
                            attribute.required
                              ? [(v) => !!v || $t('input.required')]
                              : []
                          "
                          hide-details
                          dense
                          outlined
                          full-width
                          item-text="search"
                          item-value="id"
                          :loading="isLoading"
                        >
                          <template v-slot:selection="{ item }">
                            <v-row class="ma-0 pa-0" style="font-size: 16px">
                              <v-col cols="12" class="ma-0 pa-0">{{
                                item["department_name_" + $i18n.locale]
                              }}</v-col>
                              <v-col cols="12" class="ma-0 pa-0">{{
                                item["position_name_" + $i18n.locale]
                              }}</v-col>
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
                              <v-col cols="12" class="ma-0 pa-0">{{
                                item["department_name_" + $i18n.locale]
                              }}</v-col>
                              <v-col cols="12" class="ma-0 pa-0">{{
                                item["position_name_" + $i18n.locale]
                              }}</v-col>
                            </v-row>
                          </template>
                        </v-autocomplete>
                      </v-col>
                    </v-row>
                  </v-col>
                </v-row>

                <v-row
                  v-if="
                    document.document_template_id == 615 ||
                    document.document_template_id == 622 ||
                    document.document_template_id == 619
                  "
                >
                  <v-col cols="12" class="py-0">
                    <v-row>
                      <v-col
                        cols="12"
                        md="12"
                        class="py-1"
                        v-for="(item, i) in complaens_answers.filter(
                          (v) => v.question_type != 5
                        )"
                        :key="i"
                      >
                        <p
                          style="color: #000; font-weight: 700; font-size: 18px"
                        >
                          {{ item.question }}
                        </p>
                        <v-row>
                          <v-col
                            cols="3"
                            md="3"
                            v-if="
                              item.question_type != 3 && item.question_type != 4
                            "
                          >
                            {{ "1" }}
                            <v-radio-group
                              dense
                              hide-details
                              style="margin: 0"
                              v-model="item.answer"
                              @change="
                                item.description = null;
                                result(item);
                              "
                              row
                            >
                              <v-radio
                                v-for="radio in complaens_variants"
                                :label="radio.text"
                                :value="radio.id"
                                :key="radio.id"
                              ></v-radio>
                            </v-radio-group>
                          </v-col>
                          <v-col cols="3" md="3" v-if="item.question_type == 4">
                            {{ "2" }}
                            <v-radio-group
                              dense
                              hide-details
                              style="margin: 0"
                              v-model="item.answer"
                              @change="item.description = null"
                              row
                            >
                              <v-radio
                                v-for="radio in complaens_variants2"
                                :label="radio.text"
                                :value="radio.id"
                                :key="5 + radio.id"
                              ></v-radio>
                            </v-radio-group>
                          </v-col>
                          <v-col cols="6" md="6" v-if="item.question_type == 3">
                            <v-text-field
                              v-model.lazy="item.description"
                              dense
                              label="Ma'lumot"
                              outlined
                              type="text"
                              hide-details="auto"
                            ></v-text-field>
                          </v-col>
                          <v-col
                            cols="6"
                            md="6"
                            v-if="item.answer == 1 && !item.question_type"
                          >
                            <v-text-field
                              v-model.lazy="item.description"
                              dense
                              label="Izoh"
                              outlined
                              type="text"
                              hide-details="auto"
                              :rules="[(v) => !!v || $t('input.required')]"
                            ></v-text-field>
                          </v-col>
                          <v-col
                            cols="4"
                            md="4"
                            v-if="item.question_type == 1 && item.answer == 1"
                          >
                            <v-autocomplete
                              v-if="item.question_type == 1 && item.answer == 1"
                              v-model="relativesForm.relative_id"
                              hide-details
                              dense
                              outlined
                              :label="$t('*Hodimni tanlang')"
                              :items="complaensEmployeesList"
                              @update:search-input="
                                getComplaensEmployees($event)
                              "
                              clearable
                              no-filter
                            >
                              <template
                                slot="item"
                                slot-scope="{ parent, item, tile }"
                              >
                                {{
                                  item.tabel +
                                  " " +
                                  item["firstname_" + $i18n.locale] +
                                  " " +
                                  item["lastname_" + $i18n.locale] +
                                  " " +
                                  item["middlename_" + $i18n.locale]
                                }}
                              </template>
                              <template
                                slot="selection"
                                slot-scope="{ parent, item, tile }"
                              >
                                {{
                                  item.tabel +
                                  " " +
                                  item["firstname_" + $i18n.locale] +
                                  " " +
                                  item["lastname_" + $i18n.locale] +
                                  " " +
                                  item["middlename_" + $i18n.locale]
                                }}
                              </template>
                            </v-autocomplete>
                          </v-col>
                          <v-col
                            cols="4"
                            md="4"
                            v-if="item.question_type == 1 && item.answer == 1"
                          >
                            <v-autocomplete
                              v-if="item.question_type == 1 && item.answer"
                              clearable
                              label="Qarindoshliligi"
                              v-model.lazy="relativesForm.relative_type"
                              :items="relativesList"
                              hide-details
                              dense
                              outlined
                              full-width
                              search-input.sync="
                                relativeSearch
                                "
                              :loading="isLoading"
                            >
                              <template
                                slot="item"
                                slot-scope="{ parent, item, tile }"
                              >
                                {{ item["name_" + $i18n.locale] }}
                              </template>
                              <template
                                slot="selection"
                                slot-scope="{ parent, item, tile }"
                              >
                                {{ item["name_" + $i18n.locale] }}
                              </template>
                            </v-autocomplete>
                          </v-col>
                          <v-col
                            cols="1"
                            md="1"
                            v-if="item.question_type == 1 && item.answer == 1"
                          >
                            <v-btn
                              color="success"
                              @click="addRelative(item)"
                              dense
                            >
                              <v-icon> mdi-plus</v-icon>
                            </v-btn>
                          </v-col>
                          <v-col
                            cols="12"
                            md="12"
                            v-if="item.question_type == 1 && item.answer == 1"
                          >
                            <v-simple-table class="mainTable">
                              <template v-slot:default>
                                <thead>
                                  <tr>
                                    <th class="text-left">#</th>
                                    <th>FIO</th>
                                    <th>Qarindoshliligi</th>
                                    <th>Amal</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr
                                    v-for="(relative, rIndex) in item.relatives"
                                  >
                                    <td>{{ rIndex + 1 }}</td>
                                    <td>
                                      {{
                                        relative &&
                                        relative.employee &&
                                        relative.employee.main_staff
                                          ? relative.employee[
                                              "lastname_" + $i18n.locale
                                            ] +
                                            " " +
                                            relative.employee[
                                              "firstname_" + $i18n.locale
                                            ] +
                                            " " +
                                            relative.employee[
                                              "middlename_" + $i18n.locale
                                            ] +
                                            " (" +
                                            relative.employee.main_staff[0]
                                              .department.department_code +
                                            " " +
                                            relative.employee.main_staff[0]
                                              .department.name_uz_latin +
                                            " " +
                                            relative.employee.main_staff[0]
                                              .position.name_uz_latin +
                                            ")"
                                          : ""
                                      }}
                                    </td>
                                    <td>
                                      {{ relative.relative.name_uz_latin }}
                                    </td>
                                    <td style="max-width: 20px">
                                      <v-btn
                                        color="error"
                                        small
                                        text
                                        @click="deleteRelative(relative.id)"
                                        ><v-icon>mdi-delete</v-icon></v-btn
                                      >
                                    </td>
                                  </tr>
                                </tbody>
                              </template>
                            </v-simple-table>
                          </v-col>
                        </v-row>
                      </v-col>
                      <v-col v-if="document.document_template_id == 619">
                        <v-row>
                          <v-col>
                            <table style="width: 100%">
                              <tr>
                                <td
                                  colspan="3"
                                  style="
                                    border: 1px solid black;
                                    text-align: center;
                                    color: #000;
                                  "
                                >
                                  {{ "Xulosa" }}
                                </td>
                              </tr>
                              <tr
                                v-for="(item, i) in complaens_answers.filter(
                                  (v) => v.question_type == 5
                                )"
                                :key="i"
                                style="color: #000"
                              >
                                <td
                                  style="
                                    width: 30%;
                                    border: 1px solid black;
                                    white-space: normal;
                                  "
                                >
                                  {{ item.question }}
                                </td>
                                <td
                                  style="
                                    width: 30%;
                                    border: 1px solid black;
                                    text-align: center;
                                    white-space: normal;
                                  "
                                >
                                  {{
                                    i == 0
                                      ? result_answer1 || item.answer == 1
                                        ? "Xa"
                                        : "Yo`q"
                                      : result_answer2 || item.answer == 1
                                      ? "Xa"
                                      : "Yo`q"
                                  }}
                                </td>
                                <td
                                  style="
                                    width: 30%;
                                    border: 1px solid black;
                                    white-space: normal;
                                  "
                                >
                                  {{
                                    complaens_questions.find(
                                      (v) => v.id == item.question_id
                                    ) &&
                                    complaens_questions.find(
                                      (v) => v.id == item.question_id
                                    ).description
                                      ? complaens_questions.find(
                                          (v) => v.id == item.question_id
                                        ).description
                                      : item.question_description
                                  }}
                                </td>
                              </tr>
                            </table>

                            <!-- <xulosa></xulosa> -->
                          </v-col>
                        </v-row>
                      </v-col>
                    </v-row>
                  </v-col>
                </v-row>

                <!-- <v-sheet outlined color="rgb(11, 25, 143)" rounded> -->
                <v-card
                  outlined
                  elevation="0"
                  v-if="document.document_template.change_staff"
                >
                  <v-card-text>
                    <v-row>
                      <v-col cols="6">Coefficients*</v-col>
                      <v-col cols="3">
                        <v-autocomplete
                          v-model="coefficientsForm.tariff_scale_id"
                          :label="$t('tariffScale.index')"
                          :items="coefficients"
                          item-value="id"
                          :item-text="'description_' + $i18n.locale"
                          clearable
                          outlined
                          hide-details
                          dense
                        ></v-autocomplete>
                      </v-col>
                      <v-col cols="2">
                        <v-text-field
                          v-model="coefficientsForm.value"
                          :label="$t('coefficient.percent')"
                          clearable
                          outlined
                          hide-details
                          dense
                        ></v-text-field>
                      </v-col>
                      <v-col cols="1">
                        <v-btn
                          color="success"
                          class="ml-2"
                          small
                          icon
                          title
                          outlined
                          @click="addCoefficient(document_detail)"
                        >
                          <v-icon>mdi-plus</v-icon>
                        </v-btn>
                      </v-col>
                    </v-row>
                    <v-simple-table class="mainTable">
                      <template v-slot:default>
                        <thead>
                          <tr>
                            <th class="text-left">#</th>
                            <th class="text-left">
                              {{ $t("tariffScale.index") }}
                            </th>
                            <th class="text-left">
                              {{ $t("coefficient.percent") }}
                            </th>
                            <th class="text-left">{{ $t("actions") }}</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr
                            v-for="(
                              coefficient, coefficientIndex
                            ) in document_detail.document_detail_coefficients.filter(
                              (v) => v.type == 1
                            )"
                            :key="coefficientIndex"
                          >
                            <td>{{ coefficientIndex + 1 }}</td>
                            <td>
                              {{
                                coefficients.find(
                                  (v) => v.id == coefficient.tariff_scale_id
                                )["description_" + $i18n.locale]
                              }}
                            </td>
                            <td>{{ coefficient.value }}</td>
                            <td>
                              <v-btn
                                color="error"
                                @click="
                                  document_detail.document_detail_coefficients =
                                    document_detail.document_detail_coefficients.filter(
                                      (v) => v.id != coefficient.id
                                    )
                                "
                                small
                                text
                              >
                                <v-icon>mdi-delete</v-icon>
                              </v-btn>
                            </td>
                          </tr>
                        </tbody>
                      </template>
                    </v-simple-table>
                  </v-card-text>
                </v-card>
                <!-- </v-sheet> -->
              </v-card-text>
            </v-card>

            <v-card
              class="mt-2 mx-auto"
              outlined
              v-if="document.document_template.select_department"
            >
              <v-card-title class="white lighten-3 pa-1 pl-3">
                <v-responsive min-width="400">
                  <v-autocomplete
                    class="my-2"
                    clearable
                    v-model="document.department2_id"
                    :items="departmentList"
                    item-text="name"
                    item-value="id"
                    :rules="[(v) => !!v || $t('input.required')]"
                    hide-details="auto"
                    :label="$t('department_id')"
                    dense
                    outlined
                    v-if="document.document_template.select_department"
                  >
                  </v-autocomplete>
                </v-responsive>
                <!-- <span class="ml-1" v-if="errorDocRel" style="color: #e53935">{{
                  errorDocRelMessage
                }}</span> -->
              </v-card-title>
            </v-card>

            <v-card
              class="my-2 mx-auto"
              outlined
              v-if="document && document.document_template.select_staff"
            >
              <v-card-title class="white lighten-3 pa-1 pl-3">
                {{ $t("document.add_staff") }}
                <v-spacer></v-spacer>
                <!-- <span class="ml-1" v-if="errorDocRel" style="color: #e53935">{{
                  errorDocRelMessage
                }}</span> -->
                <div style="max-width: 450px; width: 450px">
                  <span
                    class="mx-1"
                    v-if="errorStaffMessage"
                    style="color: #e53935"
                    >{{ errorStaffMessage }}</span
                  >
                  <v-autocomplete
                    clearable
                    v-model="select_staff_id"
                    :items="staffs"
                    :label="$t('staff_id')"
                    item-text="search"
                    return-object
                    hide-details="auto"
                    dense
                    outlined
                    class="my-2"
                  >
                    <template v-slot:selection="{ item }">
                      <v-row
                        class="ma-0 pa-0"
                        style="font-size: 16px; max-width: 400px"
                      >
                        <v-col cols="12" class="ma-0 pa-0">{{
                          item["department_name_" + $i18n.locale]
                        }}</v-col>
                        <v-col cols="12" class="ma-0 pa-0 font-weight-black">{{
                          item["position_name_" + $i18n.locale]
                        }}</v-col>
                      </v-row>
                    </template>
                    <template v-slot:item="{ item }">
                      <v-row
                        style="
                          border-bottom: 1px solid #ccc;
                          font-size: 14px;
                          max-width: 400px;
                        "
                        class="ma-0 pa-0"
                      >
                        <v-col cols="12" class="ma-0 pa-0">{{
                          item["department_name_" + $i18n.locale]
                        }}</v-col>
                        <v-col cols="12" class="ma-0 pa-0 font-weight-black">{{
                          item["position_name_" + $i18n.locale]
                        }}</v-col>
                      </v-row>
                    </template>
                  </v-autocomplete>
                </div>
                <v-btn
                  color="success"
                  class="ml-2"
                  small
                  icon
                  title
                  outlined
                  @click="addDocumentStaff"
                >
                  <v-icon>mdi-plus</v-icon>
                </v-btn>
              </v-card-title>
              <hr />
              <v-card-text class="pt-0">
                <v-simple-table
                  v-if="document.document_staff.length"
                  dense
                  class="mt-2"
                  style="border: 1px solid #aaa"
                >
                  <template v-slot:default>
                    <thead>
                      <tr>
                        <th class="text-left">#</th>
                        <th class="text-left">
                          {{ $t("document.department_code") }}
                        </th>
                        <th class="text-left">
                          {{ $t("document.department2") }}
                        </th>
                        <th class="text-left">
                          {{ $t("document.position") }}
                        </th>
                        <th class="text-left"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="(item, index) in document.document_staff"
                        :key="index"
                      >
                        <td>{{ index + 1 }}</td>
                        <td>
                          {{
                            item["department"]
                              ? item["department"]["department_code"]
                              : item["department_code"]
                          }}
                        </td>
                        <td>
                          {{
                            item["department"]
                              ? item["department"]["name_" + document.locale]
                              : item["department_name_" + document.locale]
                          }}
                        </td>
                        <td>
                          {{
                            item["position"]
                              ? item["position"]["name_" + document.locale]
                              : item["position_name_" + document.locale]
                          }}
                        </td>
                        <td class="text-center" width="50px">
                          <v-icon
                            class="px-1"
                            color="error"
                            @click="deleteDocumentStaff(item)"
                            >mdi-delete</v-icon
                          >
                        </td>
                      </tr>
                    </tbody>
                  </template>
                </v-simple-table>
              </v-card-text>
            </v-card>

            <v-card
              v-if="document.document_template_id == 597"
              class="my-2 mx-auto"
              outlined
            >
              <v-card-title class="white lighten-3 pa-1 pl-3">
                {{ $t("additional_time") }}
              </v-card-title>
              <v-divider></v-divider>
              <div style="display: flex">
                <div
                  class="ma-2"
                  style="justify-content: flex-start; max-width: 150px"
                >
                  <v-menu
                    v-model.lazy="menu_start_date"
                    :close-on-content-click="false"
                    :nudge-right="40"
                    transition="scale-transition"
                    offset-y
                    min-width="290px"
                  >
                    <template v-slot:activator="{ on }">
                      <v-text-field
                        v-model.lazy="start_date"
                        :label="$t('start_dates')"
                        readonly
                        v-on="on"
                        hide-details="auto"
                        dense
                        outlined
                        append-icon="mdi-calendar"
                      ></v-text-field>
                    </template>
                    <v-date-picker
                      no-title
                      v-model="start_date"
                      @input="menu_start_date = false"
                      :max="moment().format()"
                    ></v-date-picker>
                  </v-menu>
                </div>
                <div
                  class="ma-2"
                  style="justify-content: flex-start; max-width: 120px"
                >
                  <v-autocomplete
                    dense
                    v-model="start_time"
                    :label="$t('start_time')"
                    :items="[
                      '00:00',
                      '01:00',
                      '02:00',
                      '03:00',
                      '04:00',
                      '05:00',
                      '06:00',
                      '07:00',
                      '08:00',
                      '09:00',
                      '10:00',
                      '11:00',
                      '12:00',
                      '13:00',
                      '14:00',
                      '15:00',
                      '16:00',
                      '17:00',
                      '18:00',
                      '19:00',
                      '20:00',
                      '21:00',
                      '22:00',
                      '23:00',
                    ]"
                    outlined
                    hide-details
                  >
                  </v-autocomplete>
                </div>
                <div
                  class="ma-2"
                  style="justify-content: flex-start; max-width: 120px"
                >
                  <v-autocomplete
                    dense
                    v-model="end_time"
                    :label="$t('end_time')"
                    :items="[
                      '00:00',
                      '01:00',
                      '02:00',
                      '03:00',
                      '04:00',
                      '05:00',
                      '06:00',
                      '07:00',
                      '08:00',
                      '09:00',
                      '10:00',
                      '11:00',
                      '12:00',
                      '13:00',
                      '14:00',
                      '15:00',
                      '16:00',
                      '17:00',
                      '18:00',
                      '19:00',
                      '20:00',
                      '21:00',
                      '22:00',
                      '23:00',
                    ]"
                    outlined
                    hide-details="auto"
                  >
                  </v-autocomplete>
                </div>
                <div class="ma-2" style="justify-content: flex-start">
                  <v-btn
                    color="success"
                    fab
                    small
                    outlined
                    @click="addStartEndDates"
                    ><v-icon>mdi-plus</v-icon></v-btn
                  >
                </div>
                <div
                  class="ma-2"
                  style="justify-content: flex-start"
                  v-if="ishlagan_soat_required"
                >
                  <v-alert type="error" outlined dense>{{
                    ishlagan_soat_required
                  }}</v-alert>
                </div>
              </div>
              <v-divider></v-divider>
              <v-simple-table class="ma-2" v-if="start_end_dates.length" dense>
                <template v-slot:default>
                  <thead>
                    <tr>
                      <th class="text-left">#</th>
                      <th class="text-left">{{ $t("start_datetime") }}</th>
                      <th class="text-left">{{ $t("end_datetime") }}</th>
                      <th class="text-left">{{ $t("hour") }}</th>
                      <th class="text-left"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(item, index) in start_end_dates" :key="index">
                      <td class="text-left">{{ index + 1 }}</td>
                      <td class="text-left">{{ item.start_datetime }}</td>
                      <td class="text-left">{{ item.end_datetime }}</td>
                      <td class="text-left">
                        {{
                          checkDateInTwoDates(
                            item.start_datetime,
                            item.end_datetime
                          )
                        }}
                        {{
                          $i18n.locale == "uz_latin"
                            ? " soat"
                            : $i18n.locale == "ru"
                            ? " час(ы)"
                            : " соат"
                        }}
                      </td>
                      <td class="text-left">
                        <v-btn
                          color="error"
                          x-small
                          icon
                          outlined
                          @click="deleteStartEndDate(item)"
                          >X</v-btn
                        >
                      </td>
                    </tr>
                  </tbody>
                </template>
              </v-simple-table>
            </v-card>

            <v-card
              class="mt-2 mx-auto"
              outlined
              v-if="document && document.document_template_id == 620"
            >
              <v-row class="ma-0 pa-0">
                <v-col class="ma-0 text-h5">Grafik</v-col>
                <v-col class="ma-0 py-0"
                  ><v-autocomplete
                    v-model="year"
                    class="my-2"
                    :items="[
                      { value: 2023, text: '2023' },
                      { value: 2024, text: '2024' },
                    ]"
                    :rules="[(v) => !!v || $t('input.required')]"
                    hide-details="auto"
                    :label="$t('Yil')"
                    dense
                    outlined
                    @change="getGraphic"
                  >
                  </v-autocomplete
                ></v-col>
                <v-col class="ma-0 py-0"
                  ><v-autocomplete
                    v-model="month"
                    class="my-2"
                    :items="[
                      { value: '01', text: 'Yanvar' },
                      { value: '02', text: 'Fevral' },
                      { value: '03', text: 'Mart' },
                      // { value: '04', text: 'Aprel' },
                      // { value: '05', text: 'May' },
                      // { value: '06', text: 'Iyun' },
                      // { value: '07', text: 'Iyul' },
                      // { value: '08', text: 'Avgust' },
                      // { value: '09', text: 'Sentabr' },
                      // { value: '10', text: 'Oktabr' },
                      // { value: '11', text: 'Noyabr' },
                      // { value: '12', text: 'Dekabr' },
                    ]"
                    :rules="[(v) => !!v || $t('input.required')]"
                    hide-details="auto"
                    :label="$t('Oy')"
                    dense
                    outlined
                    @change="getGraphic"
                  >
                  </v-autocomplete
                ></v-col>
                <v-col class="ma-0 py-0">
                  <v-autocomplete
                    v-model="day"
                    @change="getGraphic"
                    class="my-2"
                    :items="[
                      { value: 0, text: 'Ish kuni' },
                      { value: 1, text: 'Dam olish kuni' },
                    ]"
                    hide-details="auto"
                    :label="$t('Rejim')"
                    dense
                    outlined
                  >
                  </v-autocomplete>
                </v-col>
                <v-col cols="8" class="text-right">
                  <v-btn
                    v-if="year && month"
                    color="success"
                    class="ml-2"
                    small
                    icon
                    title
                    outlined
                    @click="addGraphics"
                  >
                    <v-icon>mdi-plus</v-icon>
                  </v-btn>
                </v-col>
              </v-row>
              <v-divider></v-divider>
              <template v-if="year && month">
                <table
                  border="1"
                  style="width: 100%"
                  class="ma-1"
                  v-for="(graphic, gk) in document.graphics"
                  :key="gk"
                >
                  <tr>
                    <td colspan="34">
                      <div class="d-flex justify-space-between">
                        <div style="width: 40%" class="px-2">
                          <v-autocomplete
                            v-model="graphic.department_id"
                            class="my-2"
                            clearable
                            :items="departmentList"
                            item-text="name"
                            item-value="id"
                            :rules="[(v) => !!v || $t('input.required')]"
                            hide-details="auto"
                            :label="$t('department_id')"
                            @change="graphicsValidate"
                            dense
                            outlined
                          >
                          </v-autocomplete>
                        </div>
                        <div style="width: 10%" class="px-2">
                          <v-autocomplete
                            v-model="graphic.abcd"
                            class="my-2"
                            clearable
                            :items="abcds"
                            :rules="[(v) => !!v || $t('input.required')]"
                            hide-details="auto"
                            :label="$t('Smena')"
                            dense
                            outlined
                          >
                          </v-autocomplete>
                        </div>
                        <div style="width: 10%" class="px-2">
                          <v-autocomplete
                            v-model="graphic.shift"
                            class="my-2"
                            clearable
                            :items="shifts"
                            :rules="[(v) => !!v || $t('input.required')]"
                            hide-details="auto"
                            :label="$t('Kod')"
                            dense
                            outlined
                          >
                          </v-autocomplete>
                        </div>
                        <div style="width: 10%" class="px-2">
                          <v-autocomplete
                            v-model="graphic.command"
                            class="my-2"
                            clearable
                            :items="commands"
                            :rules="[(v) => !!v || $t('input.required')]"
                            hide-details="auto"
                            :label="$t('Komanda')"
                            dense
                            outlined
                          >
                          </v-autocomplete>
                        </div>
                        <div style="width: 40%" class="px-2 text-right">
                          <v-btn
                            color="error"
                            class="ml-2"
                            small
                            icon
                            title
                            @click="deleteGraphics(graphic)"
                          >
                            <v-icon>mdi-delete</v-icon>
                          </v-btn>
                        </div>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td
                      v-for="g in graphics"
                      :style="
                        g[1]
                          ? 'background-color:#f99;'
                          : g[2]
                          ? 'background-color:#ddd;'
                          : ''
                      "
                    >
                      {{ g[4] }}
                    </td>
                    <td rowspan="2">Jami</td>
                    <td rowspan="2">Fond</td>
                    <td rowspan="2">Sverx</td>
                  </tr>
                  <tr>
                    <td
                      v-for="g in graphics"
                      :style="
                        g[1]
                          ? 'background-color:#f99;'
                          : g[2]
                          ? 'background-color:#ddd;'
                          : ''
                      "
                    >
                      {{ g[0].replace("d", "") }}
                    </td>
                  </tr>
                  <tr>
                    <td v-for="g in graphics" class="ma-0 pa-0">
                      <select class="pa-1 ma-0" v-model="graphic[g[0]]">
                        <option v-for="h in workHours" :value="h">
                          {{ h }}
                        </option>
                      </select>
                    </td>
                    <td class="ma-0 pa-0">
                      <input
                        v-model="graphic.all"
                        type="text"
                        style="width: 40px"
                      />
                    </td>
                    <td class="ma-0 pa-0">
                      <input
                        v-model="graphic.fond"
                        type="text"
                        :readonly="
                          user.username == 'sd6566' || user.username == 'aad541' || user.id == 3453
                            ? false
                            : true
                        "
                        style="width: 40px"
                      />
                    </td>
                    <td class="ma-0 pa-0">
                      <input
                        v-model="graphic.sverx"
                        type="text"
                        style="width: 40px"
                      />
                    </td>
                  </tr>
                </table>
              </template>
            </v-card>

            <v-card class="mt-2 mx-auto" outlined>
              <v-card-title class="white lighten-3 pa-1 pl-3">
                {{ $t("document.add_parent_document") }}
                <v-spacer></v-spacer>
                <!-- <span class="ml-1" v-if="errorDocRel" style="color: #e53935">{{
                  errorDocRelMessage
                }}</span> -->
                <v-autocomplete
                  clearable
                  :label="$t('document.document_number')"
                  v-model="parent_document_id"
                  @keyup="getDocumentList"
                  id="documentRelation"
                  :search-input.sync="search"
                  :items="documentsList"
                  hide-details
                  dense
                  small-chips
                  outlined
                  item-text="document_number"
                  item-value="id"
                  style="max-width: 230px"
                >
                  <template v-slot:selection="{ item }">
                    {{ item.document_number }}
                  </template>
                  <template v-slot:item="{ item }">
                    <v-list-item-content>
                      <span>
                        <v-list-item-title
                          v-text="
                            item.document_number +
                            ' ' +
                            item.employee['firstname_' + language] +
                            ' ' +
                            item.employee['lastname_' + language]
                          "
                        ></v-list-item-title>
                        <v-list-item-subtitle
                          v-text="
                            item.document_template['name_' + $i18n.locale]
                          "
                        >
                        </v-list-item-subtitle>
                      </span>
                    </v-list-item-content>
                  </template>
                </v-autocomplete>
                <v-btn
                  color="success"
                  class="ml-2"
                  small
                  icon
                  title
                  outlined
                  @click="addDocumentParent"
                >
                  <v-icon>mdi-plus</v-icon>
                </v-btn>
              </v-card-title>
              <hr />
              <v-card-text class="pt-0">
                <v-simple-table
                  v-if="documentParents && documentParents.length"
                  dense
                  class="mt-2"
                  style="border: 1px solid #aaa"
                >
                  <template v-slot:default>
                    <thead>
                      <tr>
                        <th class="text-left">#</th>
                        <th class="text-left">
                          {{ $t("document.document_number") }}
                        </th>
                        <th class="text-left">
                          {{ $t("document.document_date") }}
                        </th>
                        <th class="text-left">
                          {{ $t("document.document_name") }}
                        </th>
                        <th class="text-left">{{ $t("document.creator") }}</th>
                        <th class="text-left"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(item, index) in documentParents" :key="index">
                        <td>{{ index + 1 }}</td>
                        <td>{{ item.document_number }}</td>
                        <td>{{ item.document_date }}</td>
                        <td>{{ item.document_name }}</td>
                        <td>{{ item.creator }}</td>
                        <td class="text-center" width="50px">
                          <v-icon
                            class="px-1"
                            color="error"
                            @click="deleteDocumentRelation(item)"
                            >mdi-delete</v-icon
                          >
                        </td>
                      </tr>
                    </tbody>
                  </template>
                </v-simple-table>
              </v-card-text>
            </v-card>

            <v-card class="mt-2 mx-auto" outlined>
              <v-card-title class="white lighten-3 pa-1 pl-3">
                {{ $t("files") }}
                <v-spacer></v-spacer>
                <span class="ml-1" v-if="errorDocRel" style="color: #e53935">{{
                  errorDocRelMessage
                }}</span>
                <v-btn
                  color="purple"
                  small
                  title
                  outlined
                  class="mr-4"
                  v-if="document.id == 2446936"
                  @click="uploadGMKFile"
                >
                  <v-icon left style="font-size: 28px"
                    >mdi-file-upload-outline</v-icon
                  >
                  {{ $t("GMK yuklash") }}
                </v-btn>
                <v-btn
                  color="purple"
                  small
                  title
                  outlined
                  class="mr-4"
                  v-if="document.is_manual_file"
                  @click="uploadManualFile"
                >
                  <v-icon left style="font-size: 28px"
                    >mdi-file-upload-outline</v-icon
                  >
                  {{ $t("Maxsus Pdf yuklash") }}
                </v-btn>
                <v-btn
                  color="success"
                  small
                  title
                  outlined
                  @click="getDocumentFile"
                >
                  <v-icon left style="font-size: 28px"
                    >mdi-file-upload-outline</v-icon
                  >
                  {{ $t("uploadFiles") }}
                </v-btn>
              </v-card-title>
              <hr />
              <v-card-text class="pt-0">
                <v-simple-table
                  v-if="documentFiles && documentFiles.length"
                  dense
                  class="mt-2"
                  style="border: 1px solid #aaa"
                >
                  <template v-slot:default>
                    <thead>
                      <tr>
                        <th class="text-left">#</th>
                        <th class="text-left">
                          {{ $t("document.file_name") }}
                        </th>
                        <th class="text-left"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(item, index) in documentFiles" :key="index">
                        <td>{{ index + 1 }}</td>
                        <td v-if="item.object_type_id == 17">
                          <span style="color: #e53935">{{
                            item.file_name
                          }}</span>
                        </td>
                        <td v-else>{{ item.file_name }}</td>
                        <td class="text-lg-right" width="100px">
                          <v-icon class="px-1" color="success">mdi-eye</v-icon>
                          <v-icon
                            class="px-1"
                            color="error"
                            @click="deleteFile(item)"
                            >mdi-delete</v-icon
                          >
                        </td>
                      </tr>
                    </tbody>
                  </template>
                </v-simple-table>
              </v-card-text>
            </v-card>
          </v-card-text>
        </v-card>

        <v-card v-if="model" class="mt-3">
          <v-card-title class="grey lighten-3 pa-1 pl-3">
            {{ $t("document.signers") }}
            <v-spacer></v-spacer>
            <v-autocomplete
              clearable
              :label="$t('signerGroup.signer_group_id')"
              v-model.lazy="signer_group_id"
              @change="addSignerGroup(signer_group_id)"
              :items="
                signer_groups.map((sg) => {
                  sg.text =
                    sg.name_uz_latin + ' ' + sg.name_uz_cyril + ' ' + sg.ru;
                  return sg;
                })
              "
              hide-details
              class="white mr-12"
              dense
              outlined
              item-value="id"
              :loading="isLoading"
              style="max-width: 350px"
              :rules="
                signer_groups.length ? [(v) => !!v || $t('input.required')] : []
              "
            >
              <template v-slot:selection="{ item }">{{
                item["name_" + $i18n.locale]
              }}</template>
              <template v-slot:item="{ item }">
                <v-list-item-content>
                  <v-list-item-title
                    v-text="item['name_' + $i18n.locale]"
                  ></v-list-item-title>
                </v-list-item-content>
              </template>
            </v-autocomplete>
            <v-btn
              class="mr-4"
              v-if="document.add_signer_employee"
              color="primary"
              dense
              icon
              tile
              outlined
              @click="getSignersEmployee"
            >
              <v-icon>mdi-account-check-outline</v-icon>
            </v-btn>
            <v-btn
              class="mr-4"
              v-if="
                document.add_signer &&
                (!signer_groups.length || signer_group_id)
              "
              color="primary"
              dense
              icon
              tile
              outlined
              @click="getSigners"
            >
              <v-icon>mdi-account-multiple-check-outline</v-icon>
            </v-btn>
          </v-card-title>
          <v-card-text>
            <v-row
              style="border: 2px solid #1e88e5"
              v-if="document.is_from_to_department_show"
            >
              <!-- <v-row v-if="document.is_from_to_department_show && document.document_template_id == 414"> -->
              <v-col cols="6">
                <v-autocomplete
                  label="Yuboruvchi"
                  v-model="document.isFromStaff"
                  clearable
                  class="pa-0"
                  :items="
                    document.document_signers.filter((v) => v.sequence > 98)
                  "
                  item-value="staff_id"
                  hide-details
                  dense
                  outlined
                >
                  <template v-slot:selection="{ item }">{{
                    item.department_code +
                    " " +
                    item.staff_name +
                    " " +
                    item.fio
                  }}</template>
                  <template v-slot:item="{ item }">
                    <v-list-item-content>
                      <v-list-item-title
                        v-text="
                          item.department_code +
                          ' ' +
                          item.staff_name +
                          ' ' +
                          item.fio
                        "
                      ></v-list-item-title>
                    </v-list-item-content>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col cols="6">
                <v-autocomplete
                  label="Qabul qiluvchi"
                  v-model="document.isToStaff"
                  class="pa-0"
                  clearable
                  :items="
                    document.document_signers.filter((v) => v.sequence <= 98)
                  "
                  item-value="staff_id"
                  hide-details
                  dense
                  outlined
                >
                  <template v-slot:selection="{ item }">{{
                    item.department_code +
                    " " +
                    item.staff_name +
                    " " +
                    item.fio
                  }}</template>
                  <template v-slot:item="{ item }">
                    <v-list-item-content>
                      <v-list-item-title
                        v-text="
                          item.department_code +
                          ' ' +
                          item.staff_name +
                          ' ' +
                          item.fio
                        "
                      ></v-list-item-title>
                    </v-list-item-content>
                  </template>
                </v-autocomplete>
              </v-col>
            </v-row>
            <!-- </v-template> -->
            <v-data-table
              dense
              :headers="headerSigner"
              :items="document.document_signers"
              class="mainTable ma-1"
              style="border: 1px solid #aaa"
              :disable-pagination="true"
              hide-default-footer
            >
              <template v-slot:item.id="{ item }">
                {{
                  document.document_signers
                    .map(function (x) {
                      return x.id;
                    })
                    .indexOf(item.id) + 1
                }}
              </template>
              <template v-slot:item.staff_name="{ item }">
                <strong :title="JSON.stringify(item)">{{
                  (item.department_code ? item.department_code : "") +
                  " " +
                  (item.staff_name ? item.staff_name : item.department)
                }}</strong>
                <br />
                <strong>{{ item.staff_position }}</strong>
              </template>
              <template v-slot:item.action="{ item }">
                <v-btn
                  icon
                  color="error"
                  v-if="
                    item.visible ||
                    ((item.staff_id == 3788 ||
                      item.staff_id == 3799 ||
                      item.staff_id == 39) &&
                      item.action_type_id == 3)
                  "
                  class="mr-4"
                >
                  <v-icon @click="deleteSigner(item)">mdi-delete</v-icon>
                </v-btn>
              </template>
            </v-data-table>
          </v-card-text>
        </v-card>
      </v-card>
    </v-form>

    <v-dialog v-model="validateTasdiqDiolog" persistent width="800">
      <v-card>
        <v-card-title class="grey lighten-3">
          {{ $t("Xatolik!") }}
          <v-spacer></v-spacer>
          <v-btn
            color="red"
            outlined
            x-small
            fab
            class
            @click="
              validateTasdiqDiolog = false;
              is_gmk_file = false;
              is_manual_file = false;
            "
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <h1 style="text-align: center; margin: 20px; padding: 20px">
            {{ $t("Xujjatda tasdiqlov mavjud emas!") }}
          </h1>
          <!-- <v-btn style="align:center" class="primary">{{ 'OK' }}</v-btn> -->
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog v-model="modalDocumentFile" persistent width="800">
      <v-card>
        <v-card-title class="grey lighten-3">
          {{ $t("document.add_file") }}
          <v-spacer></v-spacer>
          <v-btn
            color="red"
            outlined
            x-small
            fab
            class
            @click="
              modalDocumentFile = false;
              is_gmk_file = false;
              is_manual_file = false;
            "
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form @keyup.native.enter="save" ref="dialogForm">
            <v-row>
              <v-col
                cols="10"
                style="min-width: 100px; max-width: 100%"
                class="flex-grow-1 flex-shrink-0"
              >
                <label for>{{ $t("document.files") }}</label>
                <v-file-input
                  v-model="selectFiles"
                  :rules="[
                    (files) => {
                      let allowedExtensions = /(\.pdf)$/i;
                      let error = false;
                      files.forEach((file) => {
                        if (!allowedExtensions.exec(file.name)) {
                          error = true;
                        }
                      });
                      return !error || $t('requiredpdf');
                    },
                    (files) =>
                      !files ||
                      !files.some((file) => file.size > 60000000) ||
                      $t('requiredsize'),
                  ]"
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
              <!-- <v-col v-if="$store.getters.checkPermission('upload_file_manual')" cols="12">
                <label for>{{ $t("document.file_type") }}.</label>
                <v-autocomplete
                  :items="fileTypes.filter(v=>v.visible)"
                  outlined
                  dense
                  v-model="fileType"
                ></v-autocomplete>
              </v-col>
              <v-col cols="12">
                <v-btn
                  :disabled="!selectFiles || selectFiles.length == 0"
                  color="success"
                  class="float-right"
                  @click="addFiles"
                  ><v-icon>mdi-plus</v-icon></v-btn
                >
              </v-col> -->
              <v-col cols="2" style="min-width: 100px" class="px-0">
                <v-btn
                  :disabled="!selectFiles || selectFiles.length == 0"
                  class="mt-6"
                  color="success"
                  block
                  @click="addFiles"
                  >+</v-btn
                >
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog v-model="signatories" persistent width="800">
      <v-card>
        <v-card-title class="grey lighten-3">
          {{ $t("document.add_signer") }}
          <v-spacer></v-spacer>
          <v-btn
            color="red"
            outlined
            x-small
            fab
            class
            @click="signatories = false"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form ref="addSignerForm">
            <v-row>
              <v-col
                cols="12"
                style="min-width: 100px; max-width: 100%"
                class="flex-grow-1 flex-shrink-0 px-1"
              >
                <v-autocomplete
                  :label="$t('document.department')"
                  v-model="new_signer.department"
                  @keyup="getSigners"
                  no-filter
                  class="pa-0"
                  clearable
                  :items="departments"
                  :search-input.sync="search"
                  item-value="id"
                  item-text="text"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details
                  dense
                  outlined
                  :loading="isLoading"
                >
                  <template v-slot:selection="{ item }">
                    <v-list-item-content>
                      <v-list-item-title
                        v-text="item.code + ' ' + item.department_name"
                      ></v-list-item-title>
                      <v-list-item-subtitle>
                        {{
                          item.first_name +
                          " " +
                          item.last_name +
                          " " +
                          item.position_name
                        }}
                      </v-list-item-subtitle>
                    </v-list-item-content>
                  </template>
                  <template v-slot:item="{ item }">
                    <v-list-item-content>
                      <v-list-item-title
                        v-text="item.code + ' ' + item.department_name"
                      ></v-list-item-title>
                      <v-list-item-subtitle>
                        {{
                          item.first_name +
                          " " +
                          item.last_name +
                          " " +
                          item.position_name
                        }}
                      </v-list-item-subtitle>
                    </v-list-item-content>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col
                cols="4"
                style="min-width: 100px; max-width: 100%"
                class="flex-grow-1 flex-shrink-0 px-1"
              >
                <v-autocomplete
                  :label="$t('document.action_type')"
                  v-model="new_signer.action_type_id"
                  class="pa-0"
                  :items="
                    action_types.filter(
                      (v) =>
                        v.id != 13 &&
                        (v.id != 4 || document.document_template_id != 70)
                    )
                  "
                  item-value="id"
                  :item-text="'name_' + $i18n.locale"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
              <v-col
                cols="4"
                style="min-width: 100px; max-width: 100%"
                class="flex-grow-1 flex-shrink-0 px-1"
              >
                <v-text-field
                  v-if="
                    new_signer.action_type_id == 1 ||
                    new_signer.action_type_id == 2 ||
                    new_signer.action_type_id == 8 ||
                    new_signer.action_type_id == 9 ||
                    new_signer.action_type_id == 10 ||
                    new_signer.action_type_id == 12
                  "
                  v-model="new_signer.sequence"
                  :label="$t('documentTemplates.sequence')"
                  :rules="[
                    (v) => !!v || $t('input.required'),
                    (v) => (!!v && v > 10) || '10 dan k\'op',
                    (v) => (!!v && v <= 70) || '70 dan kam',
                  ]"
                  type="number"
                  hide-details="auto"
                  dense
                  outlined
                  full-width
                ></v-text-field>
              </v-col>
              <v-col
                cols="4"
                style="min-width: 100px; max-width: 100%"
                class="flex-grow-1 flex-shrink-0 px-1"
              >
                <!-- <v-menu
                  v-model="resolution_due_date"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="290px"
                >
                  <template v-slot:activator="{ on }">
                    <v-text-field
                      v-model="new_signer.due_date"
                      readonly
                      :label="$t('document.due_date')"
                      v-on="on"
                      hide-details
                      class="white"
                      dense
                      outlined
                      placeholder="YYYY-MM-DD"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    no-title
                    v-model="new_signer.due_date"
                    @input="resolution_due_date = false"
                  ></v-date-picker>
                </v-menu>-->
                <v-text-field
                  v-model.lazy="new_signer.due_date"
                  :label="$t('document.due_date')"
                  hide-details="auto"
                  type="datetime-local"
                  dense
                  outlined
                  clearable
                  placeholder="YYYY-MM-DD"
                ></v-text-field>
              </v-col>
              <v-col
                cols="2"
                style="min-width: 100px; max-width: 100%"
                class="flex-grow-1 flex-shrink-0 px-1"
              >
                <v-btn color="success" outlined block @click="addSigners">
                  {{ $t("add") }}
                </v-btn>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog v-model="signatoriesemployye" persistent width="800">
      <v-card>
        <v-card-title class="grey lighten-3">
          {{ $t("signerGroup.add_signer_employee") }}
          <v-spacer></v-spacer>
          <v-btn
            color="red"
            outlined
            x-small
            fab
            class
            @click="signatoriesemployye = false"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form ref="addSignerForm">
            <v-row>
              <v-col
                cols="12"
                style="min-width: 100px; max-width: 100%"
                class="flex-grow-1 flex-shrink-0 px-1"
              >
                <v-autocomplete
                  :label="$t('document.department')"
                  v-model="new_signer_employee.staff"
                  @keyup="getSignersEmployee"
                  no-filter
                  class="pa-0"
                  clearable
                  :items="resalution_employee"
                  :search-input.sync="search_signer_employee"
                  item-value="id"
                  item-text="text"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details
                  dense
                  outlined
                  :loading="isLoading"
                >
                </v-autocomplete>
              </v-col>
              <v-col
                cols="4"
                style="min-width: 100px; max-width: 100%"
                class="flex-grow-1 flex-shrink-0 px-1"
              >
                <v-autocomplete
                  :label="$t('document.action_type')"
                  v-model="new_signer_employee.action_type_id"
                  class="pa-0"
                  :items="action_types.filter((v) => v.id != 13)"
                  item-value="id"
                  :item-text="'name_' + $i18n.locale"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
              <v-col
                cols="4"
                style="min-width: 100px; max-width: 100%"
                class="flex-grow-1 flex-shrink-0 px-1"
              >
                <v-text-field
                  v-if="
                    new_signer_employee.action_type_id == 1 ||
                    new_signer_employee.action_type_id == 2 ||
                    new_signer_employee.action_type_id == 8 ||
                    new_signer_employee.action_type_id == 9 ||
                    new_signer_employee.action_type_id == 10 ||
                    new_signer_employee.action_type_id == 12
                  "
                  v-model="new_signer_employee.sequence"
                  :label="$t('documentTemplates.sequence')"
                  :rules="[
                    (v) => !!v || $t('input.required'),
                    (v) => (!!v && v > 10) || '10 dan k\'op',
                    (v) => (!!v && v <= 70) || '70 dan kam',
                  ]"
                  type="number"
                  hide-details="auto"
                  dense
                  outlined
                  full-width
                ></v-text-field>
              </v-col>
              <v-col
                cols="4"
                style="min-width: 100px; max-width: 100%"
                class="flex-grow-1 flex-shrink-0 px-1"
              >
                <v-text-field
                  v-model.lazy="new_signer_employee.due_date"
                  :label="$t('document.due_date')"
                  hide-details="auto"
                  type="datetime-local"
                  dense
                  outlined
                  clearable
                  placeholder="YYYY-MM-DD"
                ></v-text-field>
              </v-col>
              <v-col
                cols="2"
                style="min-width: 100px; max-width: 100%"
                class="flex-grow-1 flex-shrink-0 px-1"
              >
                <v-btn
                  color="success"
                  outlined
                  block
                  @click="addSignersEmployee"
                >
                  {{ $t("add") }}
                </v-btn>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
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
    <v-dialog v-model="errorKpi" width="300" hide-overlay>
      <v-card>
        <v-card-text>
          <v-row>
            <v-col cols="8">{{ "Ko`rsatkich salmog`i 100% emas!" }}</v-col>
            <v-col cols="4">
              <v-btn
                color="red"
                outlined
                x-small
                fab
                class
                @click="errorKpi = false"
              >
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog v-model="errorKpiMukofot" width="300" hide-overlay>
      <v-card>
        <v-card-text>
          <v-row>
            <v-col cols="8">{{
              "Mukofot miqdori 60 dan katta bo'lmasligi kerak!"
            }}</v-col>
            <v-col cols="4">
              <v-btn
                color="red"
                outlined
                x-small
                fab
                class
                @click="errorKpiMukofot = false"
              >
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog v-model="errorKpiLength" width="300" hide-overlay>
      <v-card>
        <v-card-text>
          <v-row>
            <v-col cols="8">{{
              "KPI maqsadlar soni 3 tadan ko`p bo`lishi lozim!"
            }}</v-col>
            <v-col cols="4">
              <v-btn
                color="red"
                outlined
                x-small
                fab
                class
                @click="errorKpiLength = false"
              >
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog v-model="pdfViewDialog" fullscreen persistent>
      <v-card>
        <v-card-title primary-title>
          <v-spacer></v-spacer>
          <!-- <v-btn
            color="red"
            outlined
            @click="
              pdfViewDialog = false;
              getResume(user.employee.tabel);
            "
            >{{ $t("close") }}</v-btn
          > -->
        </v-card-title>
        <v-row class="mx-0 px-0">
          <v-col cols="12">
            <pdf
              v-for="i in numPages"
              :key="i"
              :src="loadingTask"
              :page="i"
              :style="'margin-left:auto;margin-right:auto;margin-bottom:10px;border: 5px double #555;width:100%;'"
            ></pdf>
          </v-col>
        </v-row>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="success"
            outlined
            @click="
              pdfViewDialog = false;
              getResume(user.employee.tabel);
            "
            class="mr-4"
          >
            {{ $t("document.accept") }}
          </v-btn>
          <v-btn color="red" outlined @click="goBack" class="mr-4">
            {{ $t("document.cancel") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="resumeDialog" width="60%" hide-overlay>
      <!-- <resume :resumeEmployee="resumeEmployee" :tabel="forTabelResume"> -->
      <resume :tabel="forTabelResume"> </resume>
    </v-dialog>
  </div>
</template>
<script>
const axios = require("axios").default;
const moment = require("moment");
import Swal from "sweetalert2";
import Help from "@/components/Help.vue";
import resume from "@/components/resume.vue";
import xulosa from "@/components/xulosa.vue";
import { Vue2TinymceEditor } from "vue2-tinymce-editor";
import pdf from "vue-pdf";
export default {
  components: {
    resume,
    xulosa,
    Help,
    Vue2TinymceEditor,
    pdf,
  },

  data() {
    return {
      validateGraphics: true,
      fonds: {
        "2023-11": 176,
        "2023-12": 159,
        "2024-01": 176,
        "2024-02": 168,
        "2024-03": 142,
      },
      result_answer: [],
      result_answer1: false,
      result_answer2: false,
      year: null,
      notsigners: [],
      month: "11",
      day: null,
      graphics: [],
      staj: 0,
      fileType: 0,
      pdfViewDialog: false,
      validateTasdiqDiolog: false,
      resumeValidate: false,
      resumeEmployee: null,
      forTabelResume: null,
      resumeDialog: false,
      numPages: null,
      loadingTask: null,
      start_time: null,
      ishlagan_soat_required: null,
      fileTypes: [
        { value: 0, text: this.$t("document.ilova"), visible: true },
        {
          value: 1,
          text: this.$t("document.manual_document"),
          visible: this.$store.getters.checkPermission("upload_file_manual"),
        },
      ],
      start_date: "2023-08-01",
      end_time: null,
      start_end_dates: [],
      menu_start_date: null,
      menu_end_date: null,
      options: {
        menubar: true,
        plugins:
          "fullscreen advlist autolink charmap code codesample directionality emoticons preview table lists hr searchreplace",
        toolbar1:
          "fullscreen preview code | undo redo | fontsizeselect bold italic underline strikethrough forecolor backcolor | alignleft aligncenter alignright alignjustify | lineheight numlist bullist | outdent indent | link table removeformat hr customInsertButton",
        fontsize_formats: "8pt 10pt 12pt 14pt 16pt 18pt 24pt 36pt",
        formats: {
          // bold: [
          //   // { inline: 'strong', remove: 'all' },
          //   // { inline: 'h2', styles: { fontWeight: 'normal' } },
          //   // { inline: 'b', remove: 'all' }
          //   // { inline: 'b', remove: 'all' }
          // ],
          // customformat: { inline: 'span', styles: { color: '#00ff00', fontSize: '20px' }, attributes: { title: 'My custom format'} , classes: 'example1'},
          removeformat: [
            // Configures `clear formatting` to remove specified elements regardless of its attributes
            // { selector: 'b,strong,em,i,font,u,strike,s', remove: 'all' },
            { selector: "h1,h2,h3,h4,h5,h6,h7,span,p", remove: "all" },

            // // Configures `clear formatting` to remove the class red from spans and if the element then becomes empty i.e has no attributes it gets removed
            // { selector: 'span', classes: 'red', remove: 'empty' },

            // // Configures `clear formatting` to remove the class green from spans and if the element then becomes empty it's left intact
            // { selector: 'span', classes: 'green', remove: 'none' }
          ],
        },
        visualblocks_default_state: true,
        forced_root_block: "p",
        content_style:
          "body { font-family:Times,sans-serif; font-size:14pt } p{margin:0px; padding:0px;} h2{font-weight:normal;} .indent{ text-indent:40px;}",
        height: "300px",
        language: "ru",
      },
      select_staff_id: null,
      coefficientsForm: {
        tariff_scale_id: null,
        value: null,
      },
      staff: [],
      staffs: [],
      staffs_all: [],
      coefficients: [],
      departmentList: [],
      search_staff: "",
      resolution_due_date: "",
      chief_employee: null,
      table_list_employee_staff: null,
      chiefEmployees: [],
      editorOption: {},
      loading: false,
      documentTitle: "",
      mehmonPassport: "",
      mehmonData: [],
      document: {
        document_type_id: null,
        document_template_id: null,
        department2_id: null,
        staff_id: null,
        responsible_contact: null,
        document_details: [],
        document_signers: [],
        document_staff: [],
        graphics: [],
      },
      abcds: ["A", "B", "D", "E", "F", "G", "Y"],
      commands: [
        { text: "2 komanda", value: 2 },
        { text: "3 komanda", value: 3 },
        { text: "4 komanda", value: 4 },
      ],
      shifts: [
        "C",
        "E",
        "F",
        "G",
        "Y",
        "AP1",
        "BP1",
        "DP1",
        "AP2",
        "BP2",
        "DP2",
        "AW1O",
        "BW1O",
        "AW1T",
        "BW1T",
        "DW1T",
        "AW2C",
        "BW2C",
        "DW2C",
        "AW3G",
        "BW3G",
        "APS",
        "BPS",
        "DPS",
        "AGAR",
        "BGAR",
        "DGAR",
        "AGA1",
        "BGA1",
        "DGA1",
        "AGA2",
        "BGA2",
        "DGA2",
        "ASKD",
        "BSKD",
        "DSKD",
        "AMENT",
        "BMENT",
        "DMENT",
        "S",
        "AAVTO",
        "BAVTO",
        "GDA",
        "GDB",
        "GNA",
        "GNB",
        "AQCOS",
        "BQCOS",
        "DQCOS",
        "ASTL",
        "BSTL",
        "DSTL",
        "ESTL",
        "FSTL",
        "ATIB",
        "BTIB",
        "DTIB",
        "C05",
        "V",
        "AMYM",
        "BMYM",
        "DMYM",
        "M",
        "P",
        "Z",
        "MAV",
        "PAV",
        "MBO",
        "PBO",
        "ZBO",
        "MGA",
        "PGA",
        "ZGA",
        "EMB",
        "FMB",
        "GMB",
        "YMB",
        "MPA",
        "PPA",
        "ZPA",
        "MPR",
        "PPR",
        "ZPR",
        "EX",
        "FX",
        "GX",
        "YX",
      ],
      workHours: [
        "11D",
        "11N",
        "V",
        // "1D",
        // "1N",
        // "2D",
        // "2N",
        // "3D",
        // "3N",
        // "4D",
        // "4N",
        // "5D",
        // "5N",
        // "6D",
        // "6N",
        "7D",
        "7N",
        "8D",
        "8N",
        "9D",
        "9N",
        "10D",
        "10N",
        "12D",
        "12N",
      ],
      documentName: "",
      document_detail_attributes: [],
      attributes: [],

      headerSigner: [
        { text: "#", value: "id", align: "center", width: 30, sortable: false },
        {
          text: this.$t("document.staff"),
          value: "staff_name",
          sortable: false,
        },
        {
          text: this.$t("document.action_type"),
          value: "action_type",
          sortable: false,
        },
        {
          text: this.$t("document.signer_sequence"),
          value: "sequence",
          sortable: true,
        },
        {
          text: this.$t("employee.info"),
          value: "fio",
          sortable: true,
        },
        {
          text: "",
          value: "action",
          sortable: false,
          width: 30,
        },
      ],
      employees: [],
      employee_signers: [],
      employeeTabel: [],
      model: false,
      addEmpBtn: [false],
      errorEmp: [false],
      errorDocRel: false,
      errorKpi: false,
      errorKpiMukofot: false,
      kpi_validat_mukofot: true,
      errorKpiLength: false,
      kpi_validat: true,
      errorEmpMessage: "",
      errorStaffMessage: "",
      errorDocRelMessage: "",
      successMessage: "",
      disabled: false,
      menu: [],
      action_type: {
        id: 3,
        ru: "Отправитель",
        uz_cyril: "Юборувчи",
        uz_latin: "Yuboruvchi",
      },
      action_type_receiver: {
        id: 3,
        ru: "Согласование",
        uz_cyril: "Розилик",
        uz_latin: "Rozilik",
      },
      action_types: [
        {
          id: 1,
          name_uz_latin: "Rozilik",
          name_uz_cyril: "Розилик",
          name_ru: "Согласование",
        },
        {
          id: 2,
          name_uz_latin: "Tasdiq",
          name_uz_cyril: "Тасдиқ",
          name_ru: "Утверждение",
        },
        {
          id: 4,
          name_uz_latin: "Ijro uchun",
          name_uz_cyril: "Ижро учун",
          name_ru: "Для исполнения",
        },
        {
          id: 16,
          name_uz_latin: "Ijro uchun(Asosiy)",
          name_uz_cyril: "Ижро учун(Асосий)",
          name_ru: "Для исполнения(Основной)",
        },
        {
          id: 5,
          name_uz_latin: "Ma`lumot uchun",
          name_uz_cyril: "Маълумот учун",
          name_ru: "Для информации",
        },
        {
          id: 8,
          name_uz_latin: "Komissiya a'zolari",
          name_uz_cyril: "Комиссия аъзолари",
          name_ru: "Члены комиссии",
        },
        {
          id: 9,
          name_uz_latin: "Komissiya raisi",
          name_uz_cyril: "Комиссия раиси",
          name_ru: "Председатель комиссии",
        },
        {
          id: 10,
          name_uz_latin: "Komissiya kotibi",
          name_uz_cyril: "Комиссия котиби",
          name_ru: "Секретарь комиссии",
        },
        {
          id: 11,
          name_uz_latin: "Nazoratchi",
          name_uz_cyril: "Назоратчи",
          name_ru: "Контрольщик",
        },
        {
          id: 12,
          name_uz_latin: "Kuzatuvchi (Komissiya)",
          name_uz_cyril: "Кузатувчи (Комиссия)",
          name_ru: "Наблюдатель (комиссии)",
        },
        {
          id: 13,
          name_uz_latin: "Hujjat yaratuvchisi",
          name_uz_cyril: "Ҳужжат яратувчиси",
          name_ru: "Создатель документа",
        },
      ],
      tableLists: [],
      modalDocumentFile: false,
      signatories: false,
      signatoriesemployye: false,
      selectFiles: [],
      documentFiles: [],
      formData: [],
      doc_id: 0,
      departments: [],
      resalution_employee: [],
      new_signer: {
        action_type_id: 1,
      },
      new_signer_employee: {
        action_type_id: 1,
      },
      isLoading: false,
      tableLoading: [],
      search: "",
      search_signer_employee: "",
      searchTable: [],
      documentsList: [],
      complaens_variants: [
        { id: 1, text: "Ha" },
        { id: 0, text: "Yo`q" },
      ],
      complaens_variants2: [
        { id: 1, text: "Mavjud" },
        { id: 0, text: "Mavjud emas" },
      ],
      complaens_questions: [],
      complaens_answers: [],
      relativesList: [],
      relativeSearch: "",
      relativesForm: {
        relative_id: null,
        relative_type: null,
      },
      complaensEmployeesList: [],
      parent_document_id: null,
      documentParents: [],
      documentStaff: [],
      signer_groups: [],
      signer_group_id: null,
      money: {
        decimal: ".",
        thousands: ",",
        masked: false,
        precision: 2,
      },
      is_gmk_file: false,
      is_manual_file: false,
      employeeVacations: [],
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    language() {
      return this.$i18n.locale == "ru" ? "uz_cyril" : this.$i18n.locale;
    },
    user() {
      let localStorage = window.localStorage;
      return JSON.parse(localStorage.getItem("user"));
    },
    headers() {
      let headers = [
        {
          text: "#",
          value: "id",
          align: "center",
          width: 30,
          sortable: false,
          visible: true,
        },
        {
          text: this.$t("employee.tabel"),
          value: "tabel_number",
          align: "center",
          sortable: false,
          visible: true,
        },
        {
          text: this.$t("employee.info"),
          value: "fio",
          sortable: false,
          visible: true,
        },
        {
          text: this.$t("department.index"),
          value: "department",
          sortable: false,
          visible: true,
        },
        {
          text: this.$t("position.index"),
          value: "employee_position",
          sortable: false,
          visible: true,
        },
        {
          text: this.$t("tariffScale.category"),
          value: "tariff_scale_id",
          sortable: false,
          visible: this.document.document_template.change_staff == 1,
        },
        {
          text: this.$t("employee.coefficients"),
          value: "coefficients",
          sortable: false,
          visible: this.document.document_template.change_staff == 1,
        },
        {
          text: this.$t("employee.d_range"),
          value: "range_id",
          sortable: false,
          visible: this.document.document_template.change_staff == 1,
        },
        {
          text: this.$t("actions"),
          value: "actions",
          width: 50,
          align: "center",
          sortable: false,
          visible: true,
        },
      ];
      let localStorage = window.localStorage;
      return headers;
    },
  },
  methods: {
    result(e) {
      this.result_answer.push(e);
      // console.log(this.result_answer);
      // console.log(this.complaens_answers);
      this.result_answer1 = this.result_answer.some(
        (v) => [11, 12].includes(v.question_id) && v.answer == 1
      );
      this.result_answer2 = this.result_answer.some(
        (v) =>
          [13, 14, 15, 16, 17, 18, 19].includes(v.question_id) && v.answer == 1
      );
      if (this.document.document_template_id == 619) {
        this.complaens_answers.find((v) => v.question_id == 31).answer = this
          .result_answer1
          ? 1
          : 0;
        this.complaens_answers.find((v) => v.question_id == 32).answer = this
          .result_answer2
          ? 1
          : 0;
      }
    },
    checkDateInTwoDates(dateFrom, dateTo) {
      let d1 = parseInt(
        dateFrom.replaceAll("-", "").replaceAll(" ", "").replaceAll(":", "")
      );
      let d2 = parseInt(
        dateTo.replaceAll("-", "").replaceAll(" ", "").replaceAll(":", "")
      );
      let c1 = parseInt(d1.toString().substring(0, 8) + "123000");
      let c2 = parseInt(d2.toString().substring(0, 8) + "003000");
      let lanch = 0;
      if (d1 < c1 && c1 < d2) {
        lanch = 1;
      } else if (d1 < c2 && c2 < d2) {
        lanch = 1;
      }
      return (
        Math.abs(new Date(dateTo) - new Date(dateFrom)) / (1000 * 60 * 60) -
        lanch
      );
    },
    getGraphic() {
      if (!this.document.graphics.length) {
        this.document.graphics = this.getNewGraphics();
      }
      if (this.year && this.month) {
        axios
          .post(this.$store.state.backend_url + "api/graphics/get-graphic", {
            year: this.year,
            month: this.month,
          })
          .then((response) => {
            this.graphics = response.data;
            this.document.graphics = this.document.graphics.map((g) => {
              g.year = this.year;
              g.month = this.month;
              g.day = this.day;
              if (this.$route.params.documentTemplateId) {
                g.fond = this.fonds[this.year + "-" + this.month];
              }
              response.data.forEach((m) => {
                if (!g[m[0]]) {
                  // console.log(!g[m[0]], m[0])
                  g[m[0]] = null;
                }
              });
              return g;
            });
            // console.log(this.document.graphics);
          })
          .catch((error) => {});
      }
    },
    getNewGraphics() {
      return [
        {
          id: Date.now(),
          document_id: this.document.id,
          year: this.year,
          month: this.month,
          day: this.day,
          department_id: null,
          all: null,
          fond: null,
          sverx: null,
          shift: null,
          command: null,
          abcd: null,
        },
      ];
    },
    addGraphics() {
      this.getGraphic();
      this.document.graphics.push({
        id: Date.now(),
        document_id: this.document.id,
        year: this.year,
        month: this.month,
        day: this.day,
        department_id: null,
        all: null,
        fond: null,
        sverx: null,
        shift: null,
        command: null,
        abcd: null,
      });
    },
    deleteGraphics(item) {
      this.document.graphics = this.document.graphics.filter(
        (v) => v.id != item.id
      );
    },
    deleteStartEndDate(item) {
      this.start_end_dates = this.start_end_dates.filter((v) => v != item);
    },
    addStartEndDates() {
      if (this.start_date && this.start_time && this.end_time) {
        let end_date = this.start_date;
        if (this.start_time > this.end_time) {
          const date = new Date(this.start_date);
          date.setDate(date.getDate() + 1);
          end_date = date.toISOString().slice(0, 10);
        }
        this.start_end_dates.push({
          start_datetime: this.start_date + " " + this.start_time + ":00",
          end_datetime: end_date + " " + this.end_time + ":00",
        });
        // this.start_date = null;
        this.start_time = null;
        this.end_time = null;
        this.ishlagan_soat_required = null;
      }
    },
    addCoefficient(document_detail) {
      document_detail.document_detail_coefficients.push({
        id: Date.now(),
        document_detail_id: document_detail.id,
        tariff_scale_id: this.coefficientsForm.tariff_scale_id,
        value: this.coefficientsForm.value,
        type: 1,
      });
      this.coefficientsForm = {
        tariff_scale_id: null,
        value: null,
      };
    },
    getDepartments() {
      axios
        .post(
          this.$store.state.backend_url + "api/departments/get-department",
          {
            language: this.$i18n.locale,
          }
        )
        .then((response) => {
          this.departmentList = response.data.map((v) => {
            v.name = v.code + " " + v.name;
            return v;
          });
        })
        .catch((error) => {});
    },
    getStaff() {
      this.isLoading = true;
      axios
        .post(this.$store.state.backend_url + "api/department/get-staffs", {
          search: this.search_staff,
          language: this.$i18n.locale,
        })
        .then((res) => {
          this.staffs = res.data.map((v) => {
            v["department_name_" + this.$i18n.locale] =
              v.department_code +
              " " +
              v["department_name_" + this.$i18n.locale];
            v.search =
              v.department_code +
              " " +
              v["department_name_" + this.$i18n.locale] +
              " " +
              v["position_name_" + this.$i18n.locale];
            return v;
          });
          this.isLoading = false;
        })
        .catch((err) => {
          this.isLoading = false;
          console.error(err);
        });
    },
    getAllStaff() {
      axios
        .post(this.$store.state.backend_url + "api/department/get-all-staffs", {
          search: this.search_staff,
          language: this.$i18n.locale,
          document_template_id:
            this.$route.params.documentTemplateId |
            this.document.document_template_id,
        })
        .then((res) => {
          this.staffs_all = res.data.map((v) => {
            v.search =
              v.department_code +
              " " +
              v["department_name_" + this.$i18n.locale] +
              " " +
              v["position_name_" + this.$i18n.locale];
            v["department_name_" + this.$i18n.locale] =
              v.department_code +
              " " +
              v["department_name_" + this.$i18n.locale];
            return v;
          });
        })
        .catch((err) => {
          this.isLoading = false;
          console.error(err);
        });
    },
    getCoefficients() {
      this.isLoading = true;
      axios
        .get(this.$store.state.backend_url + "api/coefficients/show")
        .then((res) => {
          this.coefficients = res.data;
          this.isLoading = false;
        })
        .catch((err) => {
          this.isLoading = false;
          console.error(err);
        });
    },
    addSignersEmployee() {
      let signerEmployee = this.resalution_employee.find((s) => {
        return this.new_signer_employee.staff == s.id ? s : null;
      });
      if (signerEmployee.id) {
        this.document.document_signers.push({
          id: Date.now() + Math.floor(Math.random() * 1000),
          document_id: this.document.id,
          staff_id: signerEmployee.staff_id,
          signer_employee_id: signerEmployee.employee_id,
          // action_type_id: 4,
          action_type_id: this.new_signer_employee.action_type_id,
          due_date: this.new_signer_employee.due_date,
          taken_datetime: null,
          // sequence: 0,
          sequence:
            this.new_signer_employee.action_type_id == 1 ||
            this.new_signer_employee.action_type_id == 2 ||
            this.new_signer_employee.action_type_id == 8 ||
            this.new_signer_employee.action_type_id == 9 ||
            this.new_signer_employee.action_type_id == 10
              ? this.new_signer_employee.sequence
              : 0,
          sign_type: 1,
          signer_group_id: 0,
          visible: true,
          department_code: signerEmployee.staff.department.department_code,
          staff_name:
            signerEmployee.staff.department["name_" + this.$i18n.locale],
          staff_position:
            signerEmployee.staff.position["name_" + this.$i18n.locale],
          action_type: this.action_types.find((v) => {
            if (v.id == 4) return v;
          })["name_" + this.$i18n.locale],

          fio: signerEmployee.employee.id
            ? signerEmployee.employee.firstname[0] +
              "." +
              signerEmployee.employee.middlename[0] +
              "." +
              signerEmployee.employee.lastname
            : this.$t("document.vacancy"),
        });
      }
      this.signatoriesemployye = false;
    },
    addSigners() {
      let item = this.departments.find(
        (v) => this.new_signer.department == v.id
      );
      if (
        this.document.document_type_id == 2 &&
        this.new_signer.department == 165
      ) {
        this.new_signer.sequence = 2;
      }

      // if (
      //   !this.document.document_signers.filter((v) => {
      //     if (v.staff_id == item.manager_staff_id && v.action_type_id != 4) {
      //       return true;
      //     }
      //   }).length &&
      //   this.$refs.addSignerForm.validate()
      // )
      if (
        !this.document.document_signers.find(
          (v) =>
            v.staff_id == item.manager_staff_id &&
            v.action_type_id == this.new_signer.action_type_id
        ) &&
        this.$refs.addSignerForm.validate()
      ) {
        if (this.document.document_template_id == 1) {
          this.document.department_id = item.id;
        }
        if (item.manager_staff.employees.length)
          this.document.document_signers.push({
            id: Date.now() + Math.floor(Math.random() * 1000),
            document_id: this.document.id,
            staff_id: item.manager_staff_id,
            action_type_id: this.new_signer.action_type_id,
            due_date: this.new_signer.due_date,
            taken_datetime: null,
            sequence:
              this.new_signer.action_type_id == 1 ||
              this.new_signer.action_type_id == 2 ||
              this.new_signer.action_type_id == 8 ||
              this.new_signer.action_type_id == 9 ||
              this.new_signer.action_type_id == 10
                ? this.new_signer.sequence
                : 0,
            sign_type: 1,
            signer_group_id: 0,
            visible: true,
            department_code: item.code,
            staff_name: item.department_name,
            staff_position:
              item.manager_staff.position["name_" + this.$i18n.locale],
            action_type: this.action_types.find((v) => {
              if (v.id == this.new_signer.action_type_id) return v;
            })["name_" + this.$i18n.locale],
            fio: item.manager_staff.employees.length
              ? item.manager_staff.employees[0][
                  "firstname_" + this.language
                ].substr(0, 1) +
                "." +
                (item.manager_staff.employees[0][
                  "middlename_" + this.language
                ] == " "
                  ? " "
                  : item.manager_staff.employees[0][
                      "middlename_" + this.language
                    ].substr(0, 1) + ". ") +
                item.manager_staff.employees[0]["lastname_" + this.language]
              : this.$t("document.vacancy"),
          });
      }
      this.signatories = false;
    },
    addEmployee(key) {
      if (
        (this.document.document_template.change_staff == 1 &&
          this.document.document_details[key].document_detail_employees
            .length) ||
        (this.$route.params.documentTemplateId == 369 &&
          this.document.document_details[key].document_detail_employees.length)
      ) {
        this.successMessage = "";
        this.errorEmp[key] = true;
        this.errorEmpMessage = "Boshqa tabel qo`sha olmaysiz";
      } else if (
        this.document.document_details[key].document_detail_employees.filter(
          (v) => {
            if (v.tabel_number == this.employeeTabel[key]) {
              return true;
            }
          }
        ).length &&
        this.employeeTabel[key]
      ) {
        this.successMessage = "";
        this.errorEmp[key] = true;
        this.errorEmpMessage = "Bu tabel ro'yxatga qo'shildi";
      } else {
        this.loading = true;
        // this.getResume(this.employeeTabel[key]);
        this.getEmployeeVacationInfo(this.employeeTabel[key]);
        this.document.document_details[key].document_detail_employees.push({
          id: Date.now(),
          document_detail_id: this.document.document_details[key].id,
          employee: this.employee,
          employee_id: this.employee.id,
          coefficients: this.employee.employee_coefficients,
          tabel_number: this.employeeTabel[key],
          description: "",
          fio:
            this.employee.firstname +
            " " +
            this.employee.lastname +
            " " +
            this.employee.middlename,
          department: this.staff.department["name_" + this.$i18n.locale],
          employee_position: this.staff.position["name_" + this.$i18n.locale],
          tariff_scale: this.employee.tariff_scale
            ? this.employee.tariff_scale
            : "",
          tariff_scale_id: this.employee.tariff_scale_id,
          range:
            this.employee.main_staff && this.employee.main_staff[0]
              ? this.employee.main_staff[0].range
              : "",
          range_id: this.employee.tariff_scale_id,
        });
        // console.log(this); sardor
        this.employee_signers.forEach((element) => {
          if (
            !this.document.document_signers.filter((v) => {
              if (v.staff_id == element.manager_staff_id) {
                return true;
              }
            }).length &&
            this.document.add_parent
          ) {
            if (
              element.manager_staff &&
              !this.notsigners.includes(element.manager_staff.id) &&
              element.manager_staff.employees.length
            ) {
              this.document.document_signers.push({
                id: Date.now() + Math.floor(Math.random() * 1000),
                document_id: this.document.id,
                staff_id: element.manager_staff_id,
                action_type_id: 3,
                taken_datetime: moment().add(1, "days").format("YYYY-MM-DD"),
                sequence: element.department_type.sequence <= 3 ? 99 : 100,
                sign_type: 1,
                visible: true,
                department_code: element.department_code,
                staff_name: element["name_" + this.$i18n.locale],
                staff_position:
                  element.manager_staff.position["name_" + this.$i18n.locale],
                action_type: this.action_type[this.$i18n.locale],
                fio: element.manager_staff.employees.length
                  ? element.manager_staff.employees[0][
                      "firstname_" + this.language
                    ].substr(0, 1) +
                    "." +
                    (element.manager_staff.employees[0][
                      "middlename_" + this.language
                    ] == " "
                      ? " "
                      : element.manager_staff.employees[0][
                          "middlename_" + this.language
                        ].substr(0, 1) + ". ") +
                    element.manager_staff.employees[0][
                      "lastname_" + this.language
                    ]
                  : this.$t("document.vacancy"),
              });
            }
          }
        });
        this.employeeTabel[key] = "";
        this.successMessage = "";
        this.loading = false;
        this.addEmpBtn[key] = false;
      }
    },
    getEmployeeVacationInfo(tabel){
      axios
        .post(
          this.$store.state.backend_url + "api/getEmployeeVacationInfo",
          {tabel : tabel}
        )
        .then((res) => {
          this.employeeVacations = res.data;
          console.log(this.employeeVacations);
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: this.$t("swal_error_title"),
            text: this.$t("swal_error_text"),
            //footer: "<a href>Why do I have this issue?</a>"
          });
        });
    },
    addDocumentDetail() {
      this.document.document_details.push({
        id: Date.now() + Math.floor(Math.random() * 100),
        document_id: this.document.id,
        content: "",
        document_detail_attribute_values: [],
        document_detail_employees: [],
        document_detail_coefficients: [],
      });
      let length = this.document.document_details.length - 1;
      this.document_detail_attributes.forEach((element) => {
        this.document.document_details[
          length
        ].document_detail_attribute_values.push({
          id: Date.now(),
          document_detail_id: this.document.document_details[length].id,
          d_d_attribute_id: element.id,
          attribute_value: "",
          type: element.type,
          attribute_name: element["attribute_name_" + this.$i18n.locale],
          value_min_length: element.value_min_length,
          value_max_length: element.value_max_length,
          required: element.required,
          table_list_id: element.table_list_id,
          signer_staff_id: element.signer_staff_id,
          document_detail_attributes: element,
        });
      });
    },
    addDocumentParent() {
      let doc = this.documentsList.find((v) => {
        if (v.id == this.parent_document_id) return v;
      });

      if (
        !this.documentParents.filter((v) => {
          if (v.parent_document_id == this.parent_document_id) {
            return true;
          }
        }).length
      ) {
        this.documentParents.push({
          document_id: this.document.id,
          parent_document_id: this.parent_document_id,
          creator:
            doc.employee["lastname_" + this.language] +
            " " +
            doc.employee["firstname_" + this.language] +
            " " +
            doc.employee["middlename_" + this.language],
          document_number: doc.document_number,
          document_date: doc.created_at.substr(0, 10),
          document_name: doc.document_template["name_" + this.$i18n.locale],
        });
      }
    },
    addDocumentStaff() {
      if (
        this.select_staff_id &&
        !this.document.document_staff.some(
          (v) => v.id == this.select_staff_id.id
        )
      ) {
        if(this.document.document_template_id==637 && this.document.document_staff.length==0){
           this.document.document_staff.push(this.select_staff_id);
        }
         else if (this.document.document_template_id!=637){
           this.document.document_staff.push(this.select_staff_id);
        }
      }

      this.select_staff_id = null;
    },
    addFiles() {
      // this.errorDocRel = false;
      this.selectFiles.forEach((v, i) => {
        if (this.is_gmk_file) {
          this.formData.append("gmk_files[]", v);
        } else if (this.is_manual_file) {
          this.formData.append("manual_files[]", v);
        } else {
          this.formData.append("files[]", v);
        }
        this.documentFiles.push({
          id: Date.now() + Math.floor(Math.random() * 1000),
          file_name: v.name,
        });
      });
      // console.log(this.documentFiles);
      this.selectFiles = [];
      this.is_gmk_file = false;
      this.is_manual_file = false;
      this.modalDocumentFile = false;
    },
    addSignerGroup(id) {
      this.document.document_signers = this.document.document_signers.filter(
        (v) => {
          if (!v.signer_group_id) {
            return v;
          }
        }
      );
      let signer_group = this.signer_groups.find((v) => {
        if (v.id == id) return v;
      });
      // console.log(signer_group.signer_group_details);
      signer_group &&
        signer_group.signer_group_details.map((signer_group_detail) => {
          if (
            !this.document.document_signers.filter((v) => {
              if (
                v.staff_id == signer_group_detail.staff_id &&
                v.action_type_id == signer_group_detail.staff_id
              ) {
                return true;
              }
            }).length
          ) {
            if (signer_group_detail.action_type_id == 2) {
              this.document.department_id =
                signer_group_detail.staff.department.id;
            }
            this.document.document_signers.push({
              id: Date.now() + Math.floor(Math.random() * 1000),
              document_id: this.document.id,
              staff_id: signer_group_detail.staff_id,
              action_type_id: signer_group_detail.action_type_id,
              taken_datetime: null,
              sequence: signer_group_detail.sequence,
              sign_type: signer_group_detail.sign_type,
              visible: false,
              signer_group_id: id,
              is_registry: signer_group_detail.is_registry,
              department_code:
                signer_group_detail.staff.department.department_code,
              staff_name:
                signer_group_detail.staff.department[
                  "name_" + this.$i18n.locale
                ],
              staff_position:
                signer_group_detail.staff.position["name_" + this.$i18n.locale],
              // action_type: signer_group_detail.action_type_id,
              action_type: this.action_types.find(
                (v) => v.id == signer_group_detail.action_type_id
              )
                ? this.action_types.find(
                    (v) => v.id == signer_group_detail.action_type_id
                  )["name_" + this.$i18n.locale]
                : signer_group_detail.action_type_id,
              fio: signer_group_detail.staff.employees.length
                ? signer_group_detail.staff.employees[0][
                    "firstname_" + this.language
                  ].substr(0, 1) +
                  "." +
                  (signer_group_detail.staff.employees[0][
                    "middlename_" + this.language
                  ] == " "
                    ? " "
                    : signer_group_detail.staff.employees[0][
                        "middlename_" + this.language
                      ].substr(0, 1) + ". ") +
                  signer_group_detail.staff.employees[0][
                    "lastname_" + this.language
                  ]
                : this.$t("document.vacancy"),
            });
          }
        });
    },
    getDocument(id) {
      this.disabled = true;
      this.loading = true;

      axios
        .post(this.$store.state.backend_url + "api/documents/show-document", {
          id: id,
        })
        .then((res) => {
          this.document = res.data.document;
          this.getNotSigners();
          if (this.document.document_template_id == 620) {
            if (res.data.document.graphics.length > 0) {
              // console.log(
              //   res.data.document.graphics[0].year,
              //   res.data.document.graphics[0].month
              // );
              this.year = res.data.document.graphics[0].year;
              this.month = res.data.document.graphics[0].month + "";
              this.day = res.data.document.graphics[0].day;
              this.getGraphic();
            }
          }
          if (
            this.document.document_template_id == 615 ||
            this.document.document_template_id == 622 ||
            this.document.document_template_id == 619
          ) {
            this.getQuestions(id);
          }
          if (
            this.document.document_details[0].document_detail_employees.length
          ) {
            this.start_end_dates =
              this.document.document_details[0].document_detail_employees[0].otgul_dates.map(
                (v) => {
                  v.start_datetime = v.start_date;
                  v.end_datetime = v.end_date;
                  return v;
                }
              );
          }

          if (this.document.staff) {
            this.document.staff["department_name_" + this.$i18n.locale] =
              this.document.staff["department_code"] +
              " " +
              this.document.staff["department_name_" + this.$i18n.locale];
          }
          this.signer_groups = this.document.document_template.signer_groups;
          this.documentName =
            this.document.document_template["name_" + this.$i18n.locale];
          if (
            (this.document &&
              (this.document.status < 2 || this.document.status == 11) &&
              this.document.created_employee_id == this.user.employee_id) ||
            this.$store.getters.checkPermission("edit_all_document") ||
            this.document.action_type_id == 13 ||
            (this.document &&
              this.document.created_employee_id == this.user.employee_id &&
              [305, 357].includes(this.document.document_template_id))
          ) {
            this.model = true;
            this.document.document_details.forEach((element) => {
              element.document_detail_attribute_values.sort((a,b)=>{
                if(a.d_d_attribute_id > b.d_d_attribute_id) return 1;
                if(a.d_d_attribute_id < b.d_d_attribute_id) return -1;
                return 0;
              }).map((v) => {
                if (
                  v.document_detail_attributes.data_type.name_uz_latin ==
                  "Float"
                ) {
                  v.type = "number";
                } else if (
                  v.document_detail_attributes.data_type.name_uz_latin ==
                  "Boolean"
                ) {
                  v.type = "checkbox";
                } else if (
                  v.document_detail_attributes.data_type.name_uz_latin ==
                  "String"
                ) {
                  v.type = "string";
                } else if (
                  v.document_detail_attributes.data_type.name_uz_latin == "Text"
                ) {
                  v.type = "text";
                } else if (
                  v.document_detail_attributes.data_type.name_uz_latin ==
                  "Integer"
                ) {
                  v.type = "number";
                } else if (
                  v.document_detail_attributes.data_type.name_uz_latin ==
                  "Summa"
                ) {
                  v.type = "money";
                } else if (
                  v.document_detail_attributes.data_type.name_uz_latin == "Sana"
                ) {
                  v.type = "date";
                } else if (
                  v.document_detail_attributes.data_type.name_uz_latin ==
                  "Datetime"
                ) {
                  v.type = "datetime";
                } else if (
                  v.document_detail_attributes.data_type.name_uz_latin ==
                  "staffs"
                ) {
                  v.type = "staffs";
                } else if (
                  v.document_detail_attributes.data_type.name_uz_latin ==
                  "staffs_all"
                ) {
                  v.type = "staffs_all";
                } else if (
                  v.document_detail_attributes.data_type.name_uz_latin ==
                  "Summa"
                ) {
                  v.type = "money";
                } else if (v.document_detail_attributes.data_type.id == 6) {
                  v.type = "list";
                } else {
                  v.type = "text";
                }
                v.attribute_name =
                  v.document_detail_attributes[
                    "attribute_name_" + this.$i18n.locale
                  ];
                v.value_min_length =
                  v.document_detail_attributes.value_min_length;
                v.value_max_length =
                  v.document_detail_attributes.value_max_length;
                v.table_list_id = v.document_detail_attributes.table_list_id;
                v.signer_staff_id =
                  v.document_detail_attributes.signer_staff_id;
                return v;
              });
            });
            let i = 0;
            this.document.document_details.forEach(
              (document_detail, document_detail_index) => {
                document_detail.document_detail_attribute_values.forEach(
                  (element, index) => {
                    if (element.table_lists) {
                      let colums =
                        element.document_detail_attributes.table_list.column_name.split(
                          ", "
                        );
                      let search = "";
                      colums.forEach((colum) => {
                        colum = colum.replace("locale", this.$i18n.locale);
                        if (element.table_lists.length > 0) {
                          search = element.table_lists[0][colum]
                            ? search + " " + element.table_lists[0][colum]
                            : search;
                        }
                      });
                      element.table_lists.map((v) => {
                        v.search = search.trim().replace(/  /g, " ");
                      });
                      this.tableLists[index + "_" + document_detail_index] =
                        element.table_lists;
                    }
                    if (document_detail_index == 0) {
                      this.document_detail_attributes.push(
                        element.document_detail_attributes
                      );
                    }
                    i++;
                  }
                );
              }
            );
            this.document.document_details.forEach((document_detail) => {
              document_detail.document_detail_attribute_values.forEach(
                (document_detail_attribute_value) => {
                  if (
                    document_detail_attribute_value.table_lists ||
                    document_detail_attribute_value.type == "staff"
                  ) {
                    document_detail_attribute_value.attribute_value = Number(
                      document_detail_attribute_value.attribute_value
                    );
                  }
                }
              );
            });

            this.document_detail_attributes.map((v) => {
              if (v.data_type.name_uz_latin == "Float") {
                v.type = "number";
              } else if (v.data_type.name_uz_latin == "Boolean") {
                v.type = "checkbox";
              } else if (v.data_type.name_uz_latin == "String") {
                v.type = "text";
              } else if (v.data_type.name_uz_latin == "Integer") {
                v.type = "number";
              } else if (v.data_type.name_uz_latin == "Sana") {
                v.type = "date";
              } else if (v.data_type.name_uz_latin == "Datetime") {
                v.type = "datetime";
              } else if (v.data_type.name_uz_latin == "Summa") {
                v.type = "money";
              } else if (v.data_type.name_uz_latin == "staffs") {
                v.type = "staffs";
              } else if (v.data_type.name_uz_latin == "staffs_all") {
                v.type = "staffs_all";
              } else if (v.data_type.id == 6) {
                v.type = "list";
              } else {
                v.type = "text";
              }
              return v;
            });

            this.document.document_details.forEach((element) => {
              element.document_detail_employees.map((v) => {
                v.coefficients = v.employee.employee_coefficients;
                v.tabel_number = v.employee.tabel;
                v.fio =
                  this.$i18n.locale == "ru"
                    ? v.employee["firstname_uz_cyril"] +
                      " " +
                      v.employee["lastname_uz_cyril"] +
                      " " +
                      v.employee["middlename_uz_cyril"]
                    : v.employee["firstname_" + this.$i18n.locale] +
                      " " +
                      v.employee["lastname_" + this.$i18n.locale] +
                      " " +
                      v.employee["middlename_" + this.$i18n.locale];
                v.department = v.employee.employee_staff[0]
                  ? v.employee.employee_staff[0].staff.position[
                      "name_" + this.$i18n.locale
                    ]
                  : "";
                return v;
              });
            });

            this.document.document_signers.map((v) => {
              if (!this.signer_group_id) {
                this.signer_group_id = v.signer_group_id
                  ? v.signer_group_id
                  : null;
              }
              v.department_code = v.staff.department.department_code;
              v.staff_name = v.staff.department["name_" + this.$i18n.locale];
              v.staff_position =
                v.staff && v.staff.position
                  ? v.staff.position["name_" + this.$i18n.locale]
                  : "";
              v.action_type = v.action_types[0]["name_" + this.$i18n.locale];
              v.fio = v.signer_employee
                ? v.signer_employee["firstname_" + this.language].substr(0, 1) +
                  "." +
                  (v.signer_employee["middlename_" + this.language] == " "
                    ? " "
                    : v.signer_employee["middlename_" + this.language].substr(
                        0,
                        1
                      ) + ". ") +
                  v.signer_employee["lastname_" + this.language]
                : v.employee_staffs
                ? v.employee_staffs.employee[
                    "firstname_" + this.language
                  ].substr(0, 1) +
                  "." +
                  v.employee_staffs.employee[
                    "middlename_" + this.language
                  ].substr(0, 1) +
                  ". " +
                  v.employee_staffs.employee["lastname_" + this.language]
                : this.$t("document.vacancy");
              v.visible =
                (v.action_type_id != 6 &&
                  !v.taken_datetime &&
                  !this.document.document_template.document_signer_templates.find(
                    (val) => {
                      if (v.staff_id == val.staff_id) return v;
                    }
                  ) &&
                  v.sequence != 100 &&
                  v.signer_group_id == 0) ||
                v.action_type_id == 13
                  ? true
                  : false;
            });
            this.document.has_employee =
              this.document.document_template.has_employee;
            this.document.add_signer =
              this.document.document_template.add_signer;
            this.document.add_signer_employee =
              this.document.document_template.add_signer_employee;
            this.document.is_from_to_department_show =
              this.document.document_template.is_from_to_department_show;
            this.document.add_parent =
              this.document.document_template.add_parent;
            this.document.is_content_visible =
              this.document.document_template.is_content_visible;
            this.document.is_manual_file =
              this.document.document_template.is_manual_file;

            this.document.isFromStaff = this.document.from_staff_id;
            this.document.isToStaff = this.document.to_staff_id;

            this.document.is_document_relation =
              this.document.document_template.is_document_relation;
            this.document.document_relation.forEach((doc_relation) => {
              this.documentParents.push({
                document_id: this.document.id,
                parent_document_id: doc_relation.id,
                creator:
                  doc_relation.employee["lastname_" + this.language] +
                  " " +
                  doc_relation.employee["firstname_" + this.language] +
                  " " +
                  doc_relation.employee["middlename_" + this.language],
                document_number: doc_relation.document_number,
                document_date: doc_relation.created_at.substr(0, 10),
                document_name:
                  doc_relation.document_template["name_" + this.$i18n.locale],
              });
            });
            this.loading = false;
          } else {
            Swal.fire(
              this.$t("swal_error_title"),
              this.$t("swal_error_text"),
              "error"
            );
            window.history.back();
          }

          setTimeout(() => {
            this.document.document_details.forEach((element) => {
              element.content = element.content + " ";
            });
          }, 500);
          this.documentFiles = res.data.document_files;
          this.getAllStaff();
        })
        .catch((err) => {
          this.loading = false;
        });
    },
    getSignersEmployee() {
      this.isLoading = true;
      axios
        .post(this.$store.state.backend_url + "api/signer/employee-staff", {
          language: this.$i18n.locale,
          search: this.search_signer_employee,
        })
        .then((res) => {
          // console.log(res);
          this.resalution_employee = res.data.data.map((v) => {
            if (v.employee && v.staff) {
              v.text =
                v.employee.firstname[0] +
                "." +
                v.employee.middlename[0] +
                "." +
                v.employee.lastname +
                " " +
                v.staff.department.department_code +
                " " +
                v.staff.department["name_" + this.$i18n.locale] +
                " " +
                v.staff.position["name_" + this.$i18n.locale];
              // v.text = v.employee.firstname[0] + " " + v.firstname + " " + v.lastname;
            }
            return v;
          });
          this.isLoading = false;
        })
        .catch((error) => {
          console.log(error);
        });
      this.signatoriesemployye = true;
    },
    getSigners() {
      this.isLoading = true;
      axios
        .post(this.$store.state.backend_url + "api/departments/list", {
          search: this.search,
          locale: this.$i18n.locale,
        })
        .then((res) => {
          this.departments = res.data.data.map((v) => {
            v.text =
              v.code +
              " " +
              v.department_name +
              " " +
              v.first_name +
              " " +
              v.last_name;
            return v;
          });
          this.isLoading = false;
        })
        .catch((err) => {
          console.error(err);
          this.isLoading = false;
        });
      this.signatories = true;
    },

    getDocumentCopy(id) {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/documents/show-document", {
          id: id,
        })
        .then((rescopy) => {
          // console.log(rescopy.data);
          this.document.document_template_id =
            rescopy.data.document.document_template_id;
            console.log(444);

          axios
            .post(
              this.$store.state.backend_url + "api/document-templates/get-all",
              {
                document_template_id: this.document.document_template_id,
                // document_template_id: rescopy.data.document.document_template_id,
                language: this.$i18n.locale,
              }
            )
            .then((res) => {
              this.documentName = res.data["name_" + this.$i18n.locale];
              this.document.document_template = res.data;
              this.document.department_id = res.data.department_id;
              this.document.document_type_id = res.data.document_type_id;
              this.document.has_employee = res.data.has_employee;
              this.document.add_signer = res.data.add_signer;
              this.document.add_signer_employee = res.data.add_signer_employee;
              this.document.add_parent = res.data.add_parent;
              this.document.is_content_visible = res.data.is_content_visible;
              this.document.is_manual_file = res.data.is_manual_file;
              this.document.is_from_to_department_show =
                res.data.is_from_to_department_show;
              this.document.is_document_relation =
                res.data.is_document_relation;
              this.document.locale = this.$i18n.locale;
              this.signer_groups = res.data.signer_groups;
              this.document.document_number = "000000";
              this.document.document_details = [];
              this.document_detail_attributes =
                res.data.document_detail_templates[0].document_detail_attributes;
              this.document_detail_attributes.map((v) => {
                if (v.data_type.name_uz_latin == "Float") {
                  v.type = "number";
                } else if (v.data_type.name_uz_latin == "Boolean") {
                  v.type = "checkbox";
                } else if (v.data_type.name_uz_latin == "String") {
                  v.type = "text";
                } else if (v.data_type.name_uz_latin == "Integer") {
                  v.type = "number";
                } else if (v.data_type.name_uz_latin == "Sana") {
                  v.type = "date";
                } else if (v.data_type.name_uz_latin == "Datetime") {
                  v.type = "datetime";
                } else if (v.data_type.name_uz_latin == "Summa") {
                  v.type = "money";
                } else if (v.data_type.name_uz_latin == "staffs") {
                  v.type = "staffs";
                } else if (v.data_type.name_uz_latin == "staffs_all") {
                  v.type = "staffs_all";
                } else if (v.data_type.id == 6) {
                  v.type = "list";
                } else {
                  v.type = "text";
                }
                return v;
              });

              // console.log(rescopy.data.document.document_details);
              // console.log(this.document_detail_attributes);

              rescopy.data.document.document_details.forEach((e, i) => {
                this.document.document_details.push({
                  id: Date.now() + Math.floor(Math.random() * 100),
                  document_id: this.document.id,
                  content: "",
                  document_detail_attribute_values: [],
                  document_detail_employees: [],
                  document_detail_coefficients: [],
                });
                this.document_detail_attributes.forEach((element) => {
                  this.document.document_details[
                    i
                  ].document_detail_attribute_values.push({
                    id: Date.now(),
                    document_detail_id: this.document.document_details[i].id,
                    d_d_attribute_id: element.id,
                    // attribute_value: e.document_detail_attribute_values.find((v)=>v.d_d_attribute_id == element.id).attribute_value,
                    attribute_value: e.document_detail_attribute_values.find(
                      (v) => v.d_d_attribute_id == element.id
                    )
                      ? e.document_detail_attribute_values.find(
                          (v) => v.d_d_attribute_id == element.id
                        ).attribute_value
                      : null,
                    type: element.type,
                    attribute_name:
                      element["attribute_name_" + this.$i18n.locale],
                    value_min_length: element.value_min_length,
                    value_max_length: element.value_max_length,
                    required: element.required,
                    table_list_id: element.table_list_id,
                    // table_lists: e.document_detail_attribute_values.find((v)=>v.d_d_attribute_id == element.id).table_lists,
                    table_lists: e.document_detail_attribute_values.find(
                      (v) => v.d_d_attribute_id == element.id
                    )
                      ? e.document_detail_attribute_values.find(
                          (v) => v.d_d_attribute_id == element.id
                        ).table_lists
                      : null,
                    signer_staff_id: element.signer_staff_id,
                    document_detail_attributes: element,
                  });
                });
              });

              this.document.document_details.forEach((el) => {
                if (
                  el.document_detail_attribute_values.find(
                    (v) => v.type == "list"
                  )
                ) {
                  let o = el.document_detail_attribute_values.find(
                    (v) => v.type == "list"
                  ).table_lists[0];
                  if (o) {
                    o.search = o["lifetime_" + this.$i18n.locale];
                  }
                }
              });

              let i = 0;
              rescopy.data.document.document_details.forEach(
                (document_detail, document_detail_index) => {
                  document_detail.document_detail_attribute_values.forEach(
                    (element, index) => {
                      if (element.table_lists) {
                        let colums =
                          element.document_detail_attributes.table_list.column_name.split(
                            ", "
                          );
                        let search = "";
                        colums.forEach((colum) => {
                          colum = colum.replace("locale", this.$i18n.locale);
                          if (element.table_lists.length > 0) {
                            search = element.table_lists[0][colum]
                              ? search + " " + element.table_lists[0][colum]
                              : search;
                          }
                        });
                        element.table_lists.map((v) => {
                          v.search = search.trim().replace(/  /g, " ");
                        });
                        this.tableLists[index + "_" + document_detail_index] =
                          element.table_lists;
                        // console.log( this.tableLists[index + "_" + document_detail_index])
                      }
                      if (document_detail_index == 0) {
                        this.document_detail_attributes.push(
                          element.document_detail_attributes
                        );
                      }
                      i++;
                    }
                  );
                }
              );

              this.document.document_details.forEach((document_detail) => {
                document_detail.document_detail_attribute_values.forEach(
                  (document_detail_attribute_value) => {
                    if (
                      (document_detail_attribute_value.table_lists &&
                        document_detail_attribute_value.table_lists[0].id ==
                          1) ||
                      document_detail_attribute_value.table_lists ||
                      document_detail_attribute_value.type == "staff"
                    ) {
                      document_detail_attribute_value.attribute_value = Number(
                        document_detail_attribute_value.attribute_value
                      );
                    }
                  }
                );
              });

              this.document.document_details[0].content =
                rescopy.data.document.document_details[0].content;

              // console.log(rescopy.data.document.document_details);
              this.model = true;
              let table_index = 0;
              let colums = [];
              this.document.document_signers = [];
              let localStorage = window.localStorage;
              let user = JSON.parse(localStorage.getItem("user"));
              let chief_employee = this.chiefEmployees.find(
                (v) => v.value == this.chief_employee
              );
              axios
                .post(
                  this.$store.state.backend_url + "api/employees/get-employee",
                  {
                    tabel: chief_employee
                      ? chief_employee.tabel
                      : user.employee.tabel,
                    language:
                      this.$i18n.locale == "ru"
                        ? "uz_cyril"
                        : this.$i18n.locale,
                  }
                )
                .then((resp) => {
                  this.document.document_signers.push({
                    id: Date.now() + Math.floor(Math.random() * 1000),
                    document_id: this.document.id,
                    staff_id: resp.data.employee[0].staff[0].id,
                    action_type_id: 6,
                    taken_datetime: moment()
                      .add(1, "days")
                      .format("YYYY-MM-DD"),
                    sequence: 100,
                    status: 1,
                    visible: false,
                    department_code:
                      resp.data.employee[0].staff[0].department.department_code,
                    staff_name:
                      resp.data.employee[0].staff[0].department[
                        "name_" + this.$i18n.locale
                      ],
                    staff_position:
                      resp.data.employee[0].staff[0].position[
                        "name_" + this.$i18n.locale
                      ],
                    action_type: this.action_type[this.$i18n.locale],
                    // signer_employee_id: resp.data.employee[0].id,
                    fio:
                      resp.data.employee[0].firstname.substr(0, 1) +
                      "." +
                      (resp.data.employee[0].middlename
                        ? resp.data.employee[0].middlename.substr(0, 1)
                        : "") +
                      ". " +
                      resp.data.employee[0].lastname,
                  });
                  res.data.document_signer_templates.forEach((element) => {
                    if (element.staff.employees.length)
                      this.document.document_signers.push({
                        id: Date.now() + element.id,
                        document_id: this.document.id,
                        staff_id: element.staff_id,
                        is_registry: element.is_registry,
                        action_type_id: element.action_type_id,
                        sequence: element.sequence,
                        sign_type: element.sign_type,
                        visible: false,
                        department_code:
                          element.staff.department.department_code,
                        staff_name:
                          element.staff.department["name_" + this.$i18n.locale],
                        staff_position:
                          element.staff.position["name_" + this.$i18n.locale],
                        action_type:
                          element.action_type["name_" + this.$i18n.locale],
                        // signer_employee_id: element.staff.employees.length
                        // ? element.staff.employees[0].id : '',
                        fio: element.staff.employees.length
                          ? element.staff.employees[0][
                              "firstname_" + this.language
                            ].substr(0, 1) +
                            "." +
                            element.staff.employees[0][
                              "middlename_" + this.language
                            ].substr(0, 1) +
                            ". " +
                            element.staff.employees[0][
                              "lastname_" + this.language
                            ]
                          : this.$t("document.vacancy"),
                      });
                  });
                  resp.data.parents.manager_staff.forEach((element) => {
                    if (
                      !this.document.document_signers.filter((v) => {
                        if (
                          v.staff_id == element.manager_staff_id &&
                          v.action_type_id != 4
                        ) {
                          return true;
                        }
                      }).length &&
                      this.document.add_parent
                    ) {
                      if (
                        element.manager_staff &&
                        element.manager_staff.employees.length
                      ) {
                        this.document.document_signers.push({
                          id: Date.now() + Math.floor(Math.random() * 1000),
                          document_id: this.document.id,
                          staff_id: element.manager_staff_id,
                          action_type_id: 3,
                          taken_datetime: moment()
                            .add(1, "days")
                            .format("YYYY-MM-DD"),
                          sequence:
                            element.department_type.sequence <= 3 ? 99 : 100,
                          visible:
                            element.department_type.sequence <= 3
                              ? true
                              : false,
                          department_code: element.department_code,
                          staff_name: element["name_" + this.$i18n.locale],
                          staff_position:
                            element.manager_staff.position[
                              "name_" + this.$i18n.locale
                            ],
                          action_type: this.action_type[this.$i18n.locale],
                          // signer_employee_id: element.manager_staff.employees.length
                          //   ? element.manager_staff.employees[0].id : '',
                          fio: element.manager_staff.employees.length
                            ? element.manager_staff.employees[0][
                                "firstname_" + this.language
                              ].substr(0, 1) +
                              "." +
                              element.manager_staff.employees[0][
                                "middlename_" + this.language
                              ].substr(0, 1) +
                              ". " +
                              element.manager_staff.employees[0][
                                "lastname_" + this.language
                              ]
                            : this.$t("document.vacancy"),
                        });
                      }
                    }
                  });

                  setTimeout(() => {
                    this.document.document_details[0].content =
                      this.document.document_details[0].content + " ";
                  }, 500);
                })
                .catch((err) => {
                  console.error(err);
                });
              this.loading = false;
              this.getAllStaff();
            })
            .catch((err) => {
              console.error(err);
              this.loading = false;
            });
        })
        .catch((err) => {
          this.loading = false;
        });
    },
    getDocumentCreate() {
      this.loading = true;
      axios
        .post(
          this.$store.state.backend_url + "api/document-templates/get-all",
          {
            document_template_id: this.document.document_template_id,
            language: this.$i18n.locale,
          }
        )
        .then((res) => {
          this.documentName = res.data["name_" + this.$i18n.locale];
          this.document.document_template = res.data;
          this.document.department_id = res.data.department_id;
          this.document.document_type_id = res.data.document_type_id;
          this.document.has_employee = res.data.has_employee;
          this.document.add_signer = res.data.add_signer;
          this.document.add_signer_employee = res.data.add_signer_employee;
          this.document.add_parent = res.data.add_parent;
          this.document.is_content_visible = res.data.is_content_visible;
          this.document.is_manual_file = res.data.is_manual_file;
          this.document.is_from_to_department_show =
            res.data.is_from_to_department_show;
          this.document.is_document_relation = res.data.is_document_relation;
          this.document.locale = this.$i18n.locale;
          this.signer_groups = res.data.signer_groups;
          this.document.document_number = "000000";
          this.document.document_details[0].content =
            res.data.document_detail_templates[0][
              "content_" + this.$i18n.locale
            ];
          this.document_detail_attributes =
            res.data.document_detail_templates[0].document_detail_attributes;

          this.document_detail_attributes.sort((a,b)=>{
                if(a.sequence > b.sequence) return 1;
                if(a.sequence < b.sequence) return -1;
                return 0;
              }).map((v) => {
            if (v.data_type.name_uz_latin == "Float") {
              v.type = "number";
            } else if (v.data_type.name_uz_latin == "Boolean") {
              v.type = "checkbox";
            } else if (v.data_type.name_uz_latin == "String") {
              v.type = "text";
            } else if (v.data_type.name_uz_latin == "Integer") {
              v.type = "number";
            } else if (v.data_type.name_uz_latin == "Sana") {
              v.type = "date";
            } else if (v.data_type.name_uz_latin == "Datetime") {
              v.type = "datetime";
            } else if (v.data_type.name_uz_latin == "Summa") {
              v.type = "money";
            } else if (v.data_type.name_uz_latin == "staffs") {
              v.type = "staffs";
            } else if (v.data_type.name_uz_latin == "staffs_all") {
              v.type = "staffs_all";
            } else if (v.data_type.id == 6) {
              v.type = "list";
            } else {
              v.type = "text";
            }
            return v;
          });
          this.model = true;
          this.document.document_details[0].document_detail_attribute_values =
            [];
          this.document.document_details[0].document_detail_coefficients = [];
          let table_index = 0;
          let colums = [];
          this.document_detail_attributes.forEach((element) => {
            this.document.document_details[0].document_detail_attribute_values.push(
              {
                id: Date.now(),
                document_detail_id: this.document.document_details[0].id,
                d_d_attribute_id: element.id,
                attribute_value: "",
                type: element.type,
                attribute_name: element["attribute_name_" + this.$i18n.locale],
                value_min_length: element.value_min_length,
                value_max_length: element.value_max_length,
                required: element.required,
                table_list_id: element.table_list_id,
                signer_staff_id: element.signer_staff_id,
                document_detail_attributes: element,
              }
            );
          });
          this.document.document_signers = [];
          let localStorage = window.localStorage;
          let user = JSON.parse(localStorage.getItem("user"));
          let chief_employee = this.chiefEmployees.find(
            (v) => v.value == this.chief_employee
          );
          axios
            .post(
              this.$store.state.backend_url + "api/employees/get-employee",
              {
                tabel: chief_employee
                  ? chief_employee.tabel
                  : user.employee.tabel,
                language:
                  this.$i18n.locale == "ru" ? "uz_cyril" : this.$i18n.locale,
              }
            )
            .then((resp) => {
              if (
                this.document.document_template_id == 622 ||
                this.document.document_template_id == 615
              ) {
                // console.log(this.user); //sardor
                this.document.document_details[0].document_detail_employees.push(
                  {
                    id: Date.now(),
                    document_detail_id: this.document.document_details[0].id,
                    employee: this.user.employee,
                    employee_id: this.user.employee_id,
                    // coefficients: this.employee.employee_coefficients,
                    tabel_number: this.user.employee.tabel,
                    description: "",
                    fio:
                      this.user.employee["firstname_" + this.$i18n.locale] +
                      " " +
                      this.user.employee["lastname_" + this.$i18n.locale] +
                      " " +
                      this.user.employee["middlename_" + this.$i18n.locale],
                    department:
                      this.user.employee.employee_staff[0].staff.department[
                        "name_" + this.$i18n.locale
                      ],
                    employee_position:
                      this.user.employee.employee_staff[0].staff.position[
                        "name_" + this.$i18n.locale
                      ],
                  }
                );
                //men uchun sardor
                // this.getResume(this.user.employee.tabel)
              }
              this.document.document_signers.push({
                id: Date.now() + Math.floor(Math.random() * 1000),
                document_id: this.document.id,
                staff_id: resp.data.employee[0].staff[0].id,
                action_type_id: 6,
                taken_datetime: moment().add(1, "days").format("YYYY-MM-DD"),
                sequence: 100,
                signer_employee_id: resp.data.employee[0].id,
                status: 1,
                visible: false,
                department_code:
                  resp.data.employee[0].staff[0].department.department_code,
                staff_name:
                  resp.data.employee[0].staff[0].department[
                    "name_" + this.$i18n.locale
                  ],
                staff_position:
                  resp.data.employee[0].staff[0].position[
                    "name_" + this.$i18n.locale
                  ],
                action_type: this.action_type[this.$i18n.locale],
                fio:
                  resp.data.employee[0].firstname.substr(0, 1) +
                  "." +
                  (resp.data.employee[0].middlename
                    ? resp.data.employee[0].middlename.substr(0, 1)
                    : "") +
                  ". " +
                  resp.data.employee[0].lastname,
              });
              res.data.document_signer_templates.forEach((element) => {
                if (element.staff.employees.length)
                  this.document.document_signers.push({
                    id: Date.now() + element.id,
                    document_id: this.document.id,
                    staff_id: element.staff_id,
                    is_registry: element.is_registry,
                    action_type_id: element.action_type_id,
                    sequence: element.sequence,
                    sign_type: element.sign_type,
                    visible: false,
                    department_code: element.staff.department.department_code,
                    staff_name:
                      element.staff.department["name_" + this.$i18n.locale],
                    staff_position:
                      element.staff.position["name_" + this.$i18n.locale],
                    action_type:
                      element.action_type["name_" + this.$i18n.locale],
                    fio: element.staff.employees.length
                      ? element.staff.employees[0][
                          "firstname_" + this.language
                        ].substr(0, 1) +
                        "." +
                        element.staff.employees[0][
                          "middlename_" + this.language
                        ].substr(0, 1) +
                        ". " +
                        element.staff.employees[0]["lastname_" + this.language]
                      : this.$t("document.vacancy"),
                  });
              });
              // && (!element.manager_staff || !this.notsigners.includes(element.manager_staff.id))
              resp.data.parents.manager_staff.forEach((element) => {
                if (
                  !this.document.document_signers.filter((v) => {
                    if (
                      v.staff_id == element.manager_staff_id &&
                      v.action_type_id != 4
                    ) {
                      return true;
                    }
                  }).length &&
                  this.document.add_parent &&
                  !!element.manager_staff &&
                  !this.notsigners.includes(element.manager_staff.id)
                ) {
                  if (
                    element.manager_staff &&
                    element.manager_staff.employees.length
                  ) {
                    this.document.document_signers.push({
                      id: Date.now() + Math.floor(Math.random() * 1000),
                      document_id: this.document.id,
                      staff_id: element.manager_staff_id,
                      action_type_id: 3,
                      taken_datetime: moment()
                        .add(1, "days")
                        .format("YYYY-MM-DD"),
                      sequence:
                        element.department_type.sequence <= 3 ? 99 : 100,
                      visible:
                        element.department_type.sequence <= 3 ? true : false,
                      department_code: element.department_code,
                      staff_name: element["name_" + this.$i18n.locale],
                      staff_position:
                        element.manager_staff.position[
                          "name_" + this.$i18n.locale
                        ],
                      action_type: this.action_type[this.$i18n.locale],
                      fio: element.manager_staff.employees.length
                        ? element.manager_staff.employees[0][
                            "firstname_" + this.language
                          ].substr(0, 1) +
                          "." +
                          element.manager_staff.employees[0][
                            "middlename_" + this.language
                          ].substr(0, 1) +
                          ". " +
                          element.manager_staff.employees[0][
                            "lastname_" + this.language
                          ]
                        : this.$t("document.vacancy"),
                    });
                  }
                }
              });

              setTimeout(() => {
                this.document.document_details[0].content =
                  this.document.document_details[0].content + " ";
              }, 500);
            })
            .catch((err) => {
              console.error(err);
            });
          this.loading = false;
          this.getAllStaff();
        })
        .catch((err) => {
          console.error(err);
          this.loading = false;
        });
    },
    getTableList(table_index, table_list_id, search) {
      if (table_list_id) {
        this.isLoading = true;
        axios
          .post(this.$store.state.backend_url + "api/document-table-list", {
            table_list_id: table_list_id,
            search: this.searchTable[table_index],
          })
          .then((res) => {
            if (table_list_id == 15) {
              //  console.log(table_list_id);
              //  console.log(this.searchTable[table_index]);
              axios
                .post(
                  this.$store.state.backend_url +
                    "api/table_list_employee_staff",
                  {
                    search: this.searchTable[table_index],
                  }
                )
                .then((r) => {
                  this.table_list_employee_staff = r.data;
                  //  console.log(r.data);
                });
            }
            let tableList = res.data.table_list;
            tableList.map((v) => {
              let search = "";
              res.data.columns.forEach((colum) => {
                colum = colum.replace("locale", this.$i18n.locale);
                search = v[colum] ? search + " " + v[colum] : search;
              });
              v.search = search.trim().replace(/  /g, " ");
              return v;
            });
            this.tableLists[table_index] = tableList;
            this.isLoading = false;
          })
          .catch((err) => {
            console.error(err);
          });
      }
    },
    getEmployee(key) {
      if (this.employeeTabel[key].length == this.$store.state.tabel_length) {
        this.loading = true;
        this.errorEmp[key] = false;
        this.errorEmpMessage = "";
        this.successMessage = "";
        axios
          .post(this.$store.state.backend_url + "api/employees/get-employee", {
            tabel: this.employeeTabel[key],
            document_template_id: this.document.document_template_id,
            language:
              this.$i18n.locale == "ru" ? "uz_cyril" : this.$i18n.locale,
          })
          .then((res) => {
            this.staj = res.data.staj;
            this.resumeEmployee = res.data.employee[0];
            this.document.document_details[0].document_detail_attribute_values.map(
              (ddav) => {
                if (ddav.d_d_attribute_id == 2571) {
                  ddav.attribute_value = res.data.staj;
                } else if (ddav.d_d_attribute_id == 2570) {
                  ddav.attribute_value =
                    parseInt(res.data.staj / 5) * 2 > 8
                      ? 8
                      : parseInt(res.data.staj / 5) * 2;
                }
                return ddav;
              }
            );
            // (int)(($interval) / 5) * 2
            this.employee = res.data.employee[0];
            this.employee_signers = res.data.parents.manager_staff;
            this.staff = res.data.employee[0].staff[0];
            this.loading = false;
            this.successMessage =
              this.employee.firstname +
              " " +
              this.employee.lastname +
              " " +
              this.employee.middlename;
            this.addEmpBtn[key] = true;
            document.getElementById("addEmployeeTabel" + key).focus();
          })
          .catch((err) => {
            console.error(err);
            this.errorEmp[key] = true;
            this.errorEmpMessage = this.$t("tabel_acces_denied");
            document.getElementById("addEmployeeTabel" + key).focus();
            this.loading = false;
          });
      }
    },
    uploadGMKFile() {
      // console.log(this.is_gmk_file);
      this.is_gmk_file = true;
      // console.log(this.is_gmk_file);
      this.getDocumentFile();
    },
    uploadManualFile() {
      this.is_manual_file = true;
      this.getDocumentFile();
    },
    getDocumentFile() {
      this.modalDocumentFile = true;
      axios
        .get(
          this.$store.state.backend_url +
            "api/documents/files/" +
            this.document.id
        )
        .then((res) => {
          res.data.forEach((element) => {
            if (element.file_name) {
              if (
                !this.documentFiles.find((v) => {
                  return v.id == element.id ? 1 : 0;
                })
              ) {
                this.documentFiles.push({
                  id: element.id,
                  file_name: element.file_name,
                });
              }
            }
          });
        })
        .catch((err) => {
          console.error(err);
        });
    },
    getGuestInformation() {
      if (this.mehmonPassport.length == 9) {
        axios
          .get(
            this.$store.state.backend_url +
              "api/mehmon/guest-info/" +
              this.mehmonPassport
          )
          .then((res) => {
            if (res.data != 0) {
              this.mehmonData = res.data;
              this.mehmonData = this.mehmonData.filter(
                (v) =>
                  v.d_d_attribute_id != 2380 &&
                  v.d_d_attribute_id != 2381 &&
                  v.d_d_attribute_id != 2382
              );
              this.document.document_details[0].document_detail_attribute_values.map(
                (element) => {
                  element.attribute_value = this.mehmonData.find(
                    (v) => v.d_d_attribute_id == element.d_d_attribute_id
                  )
                    ? this.mehmonData.find(
                        (v) => v.d_d_attribute_id == element.d_d_attribute_id
                      ).attribute_value
                    : "";
                }
              );
            } else {
              this.document.document_details[0].document_detail_attribute_values.map(
                (element) => {
                  element.attribute_value = "";
                }
              );
              alert("Bunday mehmon topilmadi!");
            }
          })
          .catch((err) => {
            console.error(err);
          });
      }
    },
    getDocumentList() {
      axios
        .post(this.$store.state.backend_url + "api/documents", {
          search: this.search,
        })
        .then((res) => {
          this.documentsList = res.data.data.map((v) => {
            if (!!v.document_number_reg) {
              v.document_number = v.document_number_reg;
            }
            return v;
          });
        })
        .catch((err) => {
          console.error(err);
        });
    },
    getNotSigners() {
      axios
        .get(
          this.$store.state.backend_url +
            "api/notsigner/for-template/" +
            this.document.document_template_id
        )
        .then((res) => {
          this.notsigners = res.data;
        })
        .catch((err) => {
          console.error(err);
        });
    },
    kpiKursatkich() {
      let summ1 = 0;
      let summ2 = 0;
      let summ3 = 0;
      let summ4 = 0;
      this.document.document_details.forEach((document_detail, index) => {
        let a = document_detail.document_detail_attribute_values.find(
          (v) => v.d_d_attribute_id == 1322
        );
        summ1 = summ1 + parseInt(a.attribute_value);
        let b = document_detail.document_detail_attribute_values.find(
          (v) => v.d_d_attribute_id == 2079
        );
        summ2 = summ2 + parseInt(b.attribute_value);
        let c = document_detail.document_detail_attribute_values.find(
          (v) => v.d_d_attribute_id == 2083
        );
        summ3 = summ3 + parseInt(c.attribute_value);
        let d = document_detail.document_detail_attribute_values.find(
          (v) => v.d_d_attribute_id == 2087
        );
        summ4 = summ4 + parseInt(d.attribute_value);
      });
      // console.log(summ1);
      if (
        this.document.document_details.length >= 3
        // && this.document.document_details.length <= 7
      ) {
        //  this.kpi_validat = true;
        // if (summ1 != 100 || summ2 != 100) {
        if (summ1 != 100 || summ2 != 100 || summ3 != 100 || summ4 != 100) {
          this.kpi_validat = false;
          this.errorKpi = true;
        } else {
          this.kpi_validat = true;
        }
      } else {
        this.errorKpiLength = true;
      }
    },
    kpiMukofot() {
      let summ1 = 0;
      let summ2 = 0;
      let summ3 = 0;
      let summ4 = 0;
      this.document.document_details.forEach((document_detail, index) => {
        let a = document_detail.document_detail_attribute_values.find(
          (v) => v.d_d_attribute_id == 1323
        );
        summ1 = summ1 + parseInt(a.attribute_value);
        let b = document_detail.document_detail_attribute_values.find(
          (v) => v.d_d_attribute_id == 2080
        );
        summ2 = summ2 + parseInt(b.attribute_value);
        let c = document_detail.document_detail_attribute_values.find(
          (v) => v.d_d_attribute_id == 2084
        );
        summ3 = summ3 + parseInt(c.attribute_value);
        let d = document_detail.document_detail_attribute_values.find(
          (v) => v.d_d_attribute_id == 2088
        );
        summ4 = summ4 + parseInt(d.attribute_value);
      });
      // if (summ1 > 60) {
      if (summ1 > 60 || summ2 > 60 || summ3 > 60 || summ4 > 60) {
        this.kpi_validat_mukofot = false;
        this.errorKpiMukofot = true;
      } else {
        this.kpi_validat_mukofot = true;
      }
    },

    save() {
      let tasdiq = this.document.document_signers.some(
        (v) => v.action_type_id == 2
      );
      if (
        !tasdiq &&
        [438, 434].includes(parseInt(this.document.document_template_id))
      ) {
        this.validateTasdiqDiolog = true;
      } else {
        if (this.document.document_template_id == 597) {
          this.document.start_end_dates = this.start_end_dates;
        } else if (this.document.document_template_id == 431) {
          this.kpi_validat = false;
          this.kpiKursatkich();
          this.kpiMukofot();
        }
        if (
          this.document.document_template_id == 615 ||
          this.document.document_template_id == 622 ||
          this.document.document_template_id == 619
        ) {
          this.document["complaens_answers"] = this.complaens_answers;
        }
        if (
          this.document.document_template_id == 620 &&
          !this.validateGraphics
        ) {
          Swal.fire(
            "Error",
            "error",
            "Ushbu bo'lim(lar) uchun tasdiqlangan ish grafigi mavjud emas."
          );
        }
        if (
          this.$refs.documentCreateForm.validate() &&
          (this.document.document_template_id != 620 || this.validateGraphics)
        ) {
          this.loading = true;
          let employee_validat = false;
          let staff_validat = true;
          let document_relation_validat = false;
          if (this.document.has_employee == 1) {
            this.document.document_details.forEach((document_detail, index) => {
              if (document_detail.document_detail_employees.length) {
                employee_validat = true;
              } else {
                employee_validat = false;
                this.errorEmp[index] = true;
                this.errorEmpMessage = this.$t("document.empty_employee");
                this.loading = false;
              }
            });
          } else {
            employee_validat = true;
          }

          if (
            this.document.is_document_relation &&
            !this.documentFiles.length
          ) {
            document_relation_validat = false;
            this.errorDocRel = true;
            this.errorDocRelMessage = this.$t(
              "document.empty_document_relation"
            );
            this.loading = false;
          } else {
            document_relation_validat = true;
          }
          if (
            employee_validat &&
            staff_validat &&
            document_relation_validat &&
            this.kpi_validat &&
            this.kpi_validat_mukofot
          ) {

            let localStorage = window.localStorage;
            let user = JSON.parse(localStorage.getItem("user"));
            this.document.employee_id = this.chief_employee
              ? this.chief_employee
              : user.employee_id;
            axios
              .post(
                this.$store.state.backend_url + "api/documents/update",
                this.document
              )
              .then((res) => {
                if (!res.data.document_id) {
                  if (typeof res.data == "string") {
                    Swal.fire({
                      icon: "error",
                      title: "Хатолик...",
                      text: res.data,
                    });
                  } else {
                    Swal.fire({
                      icon: "error",
                      title: "Хатолик...",
                      text:
                        res.data[1] +
                        res.data[2] +
                        " Юборувчи: " +
                        res.data[3] +
                        " Ҳолати: " +
                        res.data[4],
                    });
                  }
                  this.loading = false;
                } else {
                  this.doc_id = res.data.document_id;
                  this.pdf_file_name = res.data.pdf_file_name;
                  axios
                    .post(
                      this.$store.state.backend_url +
                        "api/documents/update-document-relation/" +
                        this.doc_id,
                      this.documentParents
                    )
                    .then((res) => {
                      this.loading = false;
                    })
                    .catch((err) => {
                      console.error(err);
                      this.loading = false;
                    });
                  if (this.documentFiles.some((v) => !v.created_at)) {
                    axios
                      .post(
                        this.$store.state.backend_url +
                          "api/documents/update-files/" +
                          this.doc_id,
                        this.formData,
                        {
                          headers: {
                            "Content-Type": "multipart/form-data",
                          },
                        }
                      )
                      .then((res) => {
                        this.loading = true;
                        if (res.status == 200) {
                          this.$router.push("/documentsidebar/document/" + this.pdf_file_name);
                        }
                        this.loading = false;
                      })
                      .catch((err) => {
                        console.error(err);
                        this.loading = false;
                      });
                  } else {
                    this.$router.push("/documentsidebar/document/" + this.pdf_file_name);
                  }

                  this.loading = false;
                }
              })
              .catch((err) => {
                console.error(err);
                this.loading = false;
              });
          } else {
            this.loading = false;
          }
        } else if (
          this.document.document_template_id == 620 &&
          !this.validateGraphics
        ) {
          Swal.fire(
            "Error",
            "error",
            "Avval Ish grafigini kiritib tasdiqlatishingiz kerak."
          );
        }
      }
    },
    deleteEmployee(item, key) {
      this.document.document_details[key].document_detail_employees =
        this.document.document_details[key].document_detail_employees.filter(
          (v) => {
            if (v.tabel_number != item.tabel_number) {
              return v;
            }
          }
        );
    },
    deleteDocumentDetail(item) {
      this.document.document_details = this.document.document_details.filter(
        (v) => {
          if (v.id != item.id) {
            return v;
          }
        }
      );
    },
    deleteSigner(item) {
      this.document.document_signers = this.document.document_signers.filter(
        (v) => {
          if (v.id != item.id) {
            return v;
          }
        }
      );
    },
    graphicsValidate() {
      axios
        .post(this.$store.state.backend_url + "api/graphics/validate", {
          graphics: this.document.graphics,
        })
        .then((res) => {
          if (res.data.status == 500) {
            this.validateGraphics = false;
            Swal.fire(
              "Error",
              "error",
              "Ushbu bo'lim, Yil, Oy uchun tasdiqlangan ish grafigi mavjud emas."
            );
          } else {
            this.validateGraphics = true;
          }
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: this.$t("swal_error_title"),
            text: this.$t("swal_error_text"),
            //footer: "<a href>Why do I have this issue?</a>"
          });
        });
    },
    deleteFile(item) {
      axios
        .delete(
          this.$store.state.backend_url +
            "api/documents/delete-files/" +
            item.id
        )
        .then((res) => {
          Swal.fire("Deleted!", this.$t("swal_deleted"), "success");
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: this.$t("swal_error_title"),
            text: this.$t("swal_error_text"),
            //footer: "<a href>Why do I have this issue?</a>"
          });
        });
      this.formData.delete(item.file_name);
      this.documentFiles = this.documentFiles.filter((v) => {
        if (v.file_name != item.file_name) {
          return v;
        }
      });
    },
    deleteDocumentRelation(item) {
      axios
        .post(
          this.$store.state.backend_url + "api/documents/delete-relation",
          item
        )
        .then((res) => {
          Swal.fire("Deleted!", this.$t("swal_deleted"), "success");
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: this.$t("swal_error_title"),
            text: this.$t("swal_error_text"),
            //footer: "<a href>Why do I have this issue?</a>"
          });
        });
      this.documentParents = this.documentParents.filter((v) => {
        if (v.parent_document_id != item.parent_document_id) {
          return v;
        }
      });
    },
    deleteDocumentStaff(item) {
      axios
        .post(
          this.$store.state.backend_url + "api/documents/delete-document-staff",
          {
            staff_id: item.id,
            document_id: this.document.id,
          }
        )
        .then((res) => {
          Swal.fire("Deleted!", this.$t("swal_deleted"), "success");
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: this.$t("swal_error_title"),
            text: this.$t("swal_error_text"),
          });
        });
      this.document.document_staff = this.document.document_staff.filter(
        (v) => {
          if (v.id != item.id) {
            return v;
          }
        }
      );
    },
    getChiefs(helper_employee_id) {
      axios
        .get(
          this.$store.state.backend_url +
            "api/employees/get-chiefs/" +
            helper_employee_id
        )
        .then((res) => {
          this.chiefEmployees = res.data.map((v) => {
            return {
              text:
                v["firstname_" + this.$i18n.locale] +
                " " +
                v["lastname_" + this.$i18n.locale],
              value: v.id,
              tabel: v.tabel,
            };
          });
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: this.$t("swal_error_title"),
            text: this.$t("swal_error_text"),
            //footer: "<a href>Why do I have this issue?</a>"
          });
        });
      this.documentParents = this.documentParents.filter((v) => {
        if (v.parent_document_id != item.parent_document_id) {
          return v;
        }
      });
    },
    getQuestions(id) {
      // console.log(this.document.document_template_id);
      axios
        .post(this.$store.state.backend_url + "api/complaens/get-questions", {
          document_template_id: this.document.document_template_id,
          document_id: id,
        })
        .then((res) => {
          this.getFamilyRelatives();
          let nizom = res.data[1];
          this.loadingTask = pdf.createLoadingTask(
            "data:application/pdf;base64," + nizom
          );
          this.loadingTask.promise.then((pdf) => {
            this.numPages = pdf.numPages;
          });
          if (
            nizom.length > 0 &&
            this.$route.params.documentTemplateId &&
            this.document.document_template_id != 619
            // this.user.id != 34
          ) {
            this.pdfViewDialog = true;
          }
          if (id == 1) {
            this.complaens_questions = res.data[0];
            this.complaens_answers = this.complaens_questions.map(
              (question, index) => {
                return {
                  id: Date.now(),
                  question_id: question.id,
                  question: question.question,
                  question_type: question.question_type,
                  document_id: null,
                  answer: null,
                  description: null,
                  question_description: null,
                  relatives: [],
                };
              }
            );
          } else {
            this.complaens_questions = res.data[0];
            this.complaens_answers = this.complaens_questions.map(
              (item, index) => {
                return {
                  id: item.id,
                  question_id: item.question_id,
                  question: item.questions.question,
                  question_type: item.questions.question_type,
                  document_id: item.document_id,
                  answer: parseInt(item.answer),
                  description: item.description,
                  question_description: item.questions.description,
                  relatives: item.relatives,
                };
              }
            );
            // this.complaens_answers.forEach((v, i)=>{
            //   if(v.question_type==1){

            //     this.tableLists[0].id =
            //               v.description;
            //   }
            // })
          }
          // console.log('complaens_answers -');
          // console.log( this.complaens_answers);
          // console.log('complaens_questions');
          // console.log(this.complaens_questions);
        })
        .catch((err) => {
          console.error(err);
        });
    },
    getComplaensEmployees(search) {
      if (search && search.length > 0)
        axios
          .post(
            this.$store.state.backend_url + "api/employees/search-employee",
            {
              search: search,
            }
          )
          .then((res) => {
            this.complaensEmployeesList = res.data.data;
            // console.log(this.complaensEmployeesList);
          })
          .catch((error) => {
            console.log(error);
          });
    },
    getFamilyRelatives() {
      axios
        .get(this.$store.state.backend_url + "api/family-relative")
        .then((res) => {
          this.relativesList = res.data;
          // console.log(this.relativesList);
        })
        .catch((err) => {
          console.error(err);
        });
    },
    deleteRelative(id) {
      // console.log(id);
      axios
        .delete(
          this.$store.state.backend_url + "api/complaens-relatives/delete/" + id
        )
        .then((res) => {
          Swal.fire("Deleted!", this.$t("swal_deleted"), "success");
          this.getQuestions(this.$route.params.documentId);
        })
        .catch((err) => {
          console.error(err);
        });
    },
    addRelative(answer) {
      // console.log(this.relativesForm);
      answer.relatives.push({
        id: Date.now(),
        answer_id: answer.id,
        relative_id: this.relativesForm.relative_id.id,
        employee: this.relativesForm.relative_id,
        relative: this.relativesForm.relative_type,
        relative_type_id: this.relativesForm.relative_type.id,
      });
      // console.log(this.complaens_answers);
      this.relativesForm = {
        relative_id: null,
        relative_type: null,
      };
    },
    goBack() {
      this.$router.go(-1);
    },
    getResume(tabel) {
      // if(this.user.id == 34 && this.resumeValidate){
      // if (this.user.id == 34 || this.user.id == 2805) {
      this.forTabelResume = tabel;
      this.resumeDialog = true;
      // console.log(this.user.id);
      // }
    },
  },
  mounted() {
    this.getStaff();
    this.getCoefficients();
    this.getDepartments();
    if (this.$store.getters.checkPermission("create_document_for_chief")) {
      let localStorage = window.localStorage;
      let user = JSON.parse(localStorage.getItem("user"));
      this.getChiefs(user.employee_id);
    }
    if (this.$route.params.documentTemplateId) {
      this.documentTitle = this.$t("document.create");
      this.document.id = Date.now();
      this.document.document_template_id =
        this.$route.params.documentTemplateId;
      this.document.has_employee = 0;
      this.getNotSigners();
      this.document.document_details = [
        {
          id: Date.now(),
          document_id: this.document.id,
          content: "0",
          document_detail_attribute_values: [],
          document_detail_employees: [],
        },
      ];
      this.document.document_date = new Date().toISOString().substr(0, 10);
      this.getDocumentCreate();
      if (
        this.document.document_template_id == 615 ||
        this.document.document_template_id == 622 ||
        this.document.document_template_id == 619
      ) {
        this.getQuestions(1);
      }
    } else if (this.$route.params.documentId) {
      this.documentTitle = this.$t("document.update");
      this.getDocument(this.$route.params.documentId);
    } else if (this.$route.params.docId) {
      this.documentTitle = this.$t("document.create");
      this.document.id = Date.now();
      this.document.document_template_id =
        this.$route.params.documentTemplateId;
      this.document.has_employee = 0;
      this.document.document_details = [
        {
          id: Date.now(),
          document_id: this.document.id,
          content: "0",
          document_detail_attribute_values: [],
          document_detail_employees: [],
        },
      ];
      this.document.document_date = new Date().toISOString().substr(0, 10);
      this.getDocumentCopy(this.$route.params.docId);
    } else {
      this.$router.push("/documents/list");
    }
    this.formData = new FormData();
    this.getDocumentList();
    this.getCoefficients();

    if (!["305", "357"].includes(this.document.document_template_id)) {
      this.action_types = this.action_types.filter((v) => v.id != 16);
    }
    // if(this.document.document_template_id==70){
    //   this.action_types = this.action_types.filter((v) => v.id != 4);
    // }
  },
};
</script>

<style>
body {
  margin: 0px;
}
</style>
