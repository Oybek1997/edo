<template>
  <div>
    <v-form ref="documentCreateForm">
      <v-card
        class="ma-0 pa-4"
        elevation="0"
        style="border: 1px solid #dce5ef; border-radius: 10px"
      >
        <v-card-title class="pa-0">
          <span class="cardTitle">{{ documentTitle + "*" }}</span>
          <v-spacer></v-spacer>
          <span
            class="card-btn"
            color="#2765AE"
            small
            elevation="0"
            style="text-transform: none; cursor: default; padding: 0px 10px"
          >
            {{ $t("Произволный шаблон") }}
          </span>
        </v-card-title>
        <v-divider class="my-3" style="border-color: #dce5ef"></v-divider>
        <v-card-title class="lighten-3 pa-0 docCreTextField">
          <span class="cardTitle mr-1" style="color: #c31313"
            >{{ $t("Контакты ответственного") }}:</span
          >
          <v-text-field
            v-model="document.responsible_contact"
            dense
            outlined
            :rules="[(v) => !!v || $t('input.required')]"
            hide-details
            class="input_text white mr-8"
          ></v-text-field>
          <span class="cardTitle mr-1" style="color: #c31313"
            >{{ $t("Тема документа") }}:</span
          >
          <v-text-field
            v-model="document.title"
            dense
            outlined
            hide-details
            class="input_text white"
            :rules="[(v) => !!v || $t('input.required')]"
          ></v-text-field>
          <v-spacer></v-spacer>
        </v-card-title>

        <div v-if="model">
          <v-card
            elevation="0"
            class
            outlined
            v-for="(document_detail, detail_index) in document.document_details"
            :key="detail_index"
            style="border: none"
          >
            <v-card-title class="pa-0 mt-3">
              <span class="cardTitle"
                >{{ $t("document.punkt") }} {{ detail_index + 1 }}</span
              >
              <v-spacer></v-spacer>
              <v-btn
                color="error"
                v-if="detail_index != 0"
                icon
                @click="deleteDocumentDetail(document_detail)"
              >
                <v-icon size="20">mdi-trash-can-outline</v-icon>
              </v-btn>
              <v-btn
                v-else-if="
                  document.document_template_id != 157 &&
                  document.document_template_id != 158 &&
                  document.document_template_id != 12 &&
                  document.document_template_id != 474 &&
                  document.document_template_id != 597
                "
                color="success"
                small
                outlined
                @click="addDocumentDetail"
                elevation="0"
                style="text-transform: none; border-radius: 15px"
                >{{ $t("document.add_punkt") }}</v-btn
              >
            </v-card-title>
            <v-divider class="my-3" style="border-color: #dce5ef"></v-divider>
            <v-card-text class="pa-0">
              <v-row class="ma-0" v-if="model">
                <v-col
                  class="pa-0"
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
                class="mt-3 mb-3"
                outlined
                elevation="0"
                style="border: none"
              >
                <v-card-title class="pa-0 mt-3">
                  <span class="cardTitle">{{ $t("employee.index") }}</span>
                  <v-spacer></v-spacer>
                  <v-form
                    @keyup.native.enter="addEmployee(detail_index)"
                    v-if="
                      document.document_details[0].document_detail_employees
                        .length < 1 || document.document_template_id != 597
                    "
                    class="form-add_employee 12"
                  >
                    <v-text-field
                      @keyup="getEmployee(detail_index)"
                      v-model="employeeTabel[detail_index]"
                      :id="'addEmployeeTabel' + detail_index"
                      dense
                      outlined
                      small
                      type="text"
                      hide-details
                      label="Tabel raqami"
                      style="max-width: 150px; min-width: 100px"
                      class="input_text white"
                    ></v-text-field>
                  </v-form>
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
                    class
                    v-if="errorEmp[detail_index]"
                    style="color: #e53935"
                    >{{ errorEmpMessage }}</span
                  >
                  <v-btn
                    color="success"
                    class
                    small
                    outlined
                    @click="addEmployee(detail_index)"
                    elevation="0"
                    style="text-transform: none; border-radius: 15px"
                    v-if="addEmpBtn[detail_index] && !errorEmp[detail_index]"
                    >{{ $t("document.add_employee") }}</v-btn
                  >
                </v-card-title>
                <v-divider
                  class="my-3"
                  style="border-color: #dce5ef"
                ></v-divider>
                <v-card-text class="pa-0">
                  <v-data-table
                    dense
                    :headers="headers.filter((v) => v.visible)"
                    :items="document_detail.document_detail_employees"
                    class="groupSignaTable"
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
                    <template v-slot:item.tariff_scale_id="{ item }">{{
                      item.tariff_scale.category
                    }}</template>
                    <template v-slot:item.coefficients="{ item }">
                      <div v-for="coef in item.coefficients">
                        {{
                          coef.coefficient.description +
                          ": " +
                          coef.percent +
                          "%"
                        }}
                      </div>
                    </template>
                    <template v-slot:item.range_id="{ item }">{{
                      item.range ? item.range.code : ""
                    }}</template>
                    <template v-slot:item.actions="{ item }">
                      <v-btn
                        color="red"
                        small
                        text
                        @click="deleteEmployee(item, detail_index)"
                      >
                        <v-icon size="20">mdi-trash-can-outline</v-icon>
                      </v-btn>
                    </template>
                  </v-data-table>
                </v-card-text>
              </v-card>

              <v-row class="mt-2">
                <v-col
                  cols="6"
                  class="pa-0 ma-0"
                  v-for="(
                    attribute, i
                  ) of document_detail.document_detail_attribute_values"
                  :key="i"
                >
                  <v-row
                    class="ma-0"
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
                      class="py-1 form-add_employee 23"
                      v-if="attribute.type == 'string'"
                    >
                      <v-text-field
                        v-model.lazy="attribute.attribute_value"
                        dense
                        :label="attribute.attribute_name"
                        outlined
                        type="text"
                        class="input_text white"
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
                      class="py-1 form-add_employee 34 text_area_margin_top"
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
                        class="input_text white"
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
                      class="py-1 form-add_employee 45"
                      v-if="attribute.type == 'number'"
                    >
                      <v-text-field
                        v-model.lazy="attribute.attribute_value"
                        dense
                        :label="attribute.attribute_name"
                        outlined
                        type="number"
                        class="input_text white"
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
                      class="py-1 form-add_employee 56"
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
                            class="input_text white"
                            hide-details="auto"
                            dense
                            outlined
                            :rules="
                              attribute.required
                                ? [(v) => !!v || $t('input.required')]
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
                      class="py-1 form-add_employee 67"
                      v-if="attribute.type == 'datetime'"
                    >
                      <v-text-field
                        v-model.lazy="attribute.attribute_value"
                        :label="attribute.attribute_name"
                        hide-details="auto"
                        class="input_text white"
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
                      class="py-1 form-add_employee 78"
                      v-if="attribute.type == 'money'"
                    >
                      <v-text-field
                        v-model.lazy="attribute.attribute_value"
                        :label="attribute.attribute_name"
                        hide-details="auto"
                        class="input_text white"
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
                      class="py-1 form-add_employee 89"
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
                      class="py-1 form-add_employee input_border"
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
                        :search-input.sync="searchTable[i + '_' + detail_index]"
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
                      class="py-1 form-add_employee 01"
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
                            <v-col cols="12" class="ma-0 pa-0">
                              {{ item["department_name_" + $i18n.locale] }}
                            </v-col>
                            <v-col cols="12" class="ma-0 pa-0">
                              {{ item["position_name_" + $i18n.locale] }}
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
                              {{ item["department_name_" + $i18n.locale] }}
                            </v-col>
                            <v-col cols="12" class="ma-0 pa-0">
                              {{ item["position_name_" + $i18n.locale] }}
                            </v-col>
                          </v-row>
                        </template>
                      </v-autocomplete>
                    </v-col>
                    <v-col
                      cols="12"
                      md="12"
                      class="py-1 form-add_employee 02"
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
                            <v-col cols="12" class="ma-0 pa-0">
                              {{ item["department_name_" + $i18n.locale] }}
                            </v-col>
                            <v-col cols="12" class="ma-0 pa-0">
                              {{ item["position_name_" + $i18n.locale] }}
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
                              {{ item["department_name_" + $i18n.locale] }}
                            </v-col>
                            <v-col cols="12" class="ma-0 pa-0">
                              {{ item["position_name_" + $i18n.locale] }}
                            </v-col>
                          </v-row>
                        </template>
                      </v-autocomplete>
                    </v-col>
                  </v-row>
                </v-col>
              </v-row>
              <v-card
                outlined
                elevation="0"
                v-if="document.document_template.change_staff"
              >
                <v-card-text>
                  <v-row>
                    <v-col cols="6">Coefficients</v-col>
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
                ></v-autocomplete>
              </v-responsive>
              <span class="ml-1" v-if="errorDocRel" style="color: #e53935">
                {{ errorDocRelMessage }}
              </span>
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
              <span class="ml-1" v-if="errorDocRel" style="color: #e53935">
                {{ errorDocRelMessage }}
              </span>
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
                      <v-col cols="12" class="ma-0 pa-0">
                        {{ item["department_name_" + $i18n.locale] }}
                      </v-col>
                      <v-col cols="12" class="ma-0 pa-0 font-weight-black">
                        {{ item["position_name_" + $i18n.locale] }}
                      </v-col>
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
                      <v-col cols="12" class="ma-0 pa-0">
                        {{ item["department_name_" + $i18n.locale] }}
                      </v-col>
                      <v-col cols="12" class="ma-0 pa-0 font-weight-black">
                        {{ item["position_name_" + $i18n.locale] }}
                      </v-col>
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
                      <th class="text-left">{{ $t("document.position") }}</th>
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
            <v-card-title class="white lighten-3 pa-1 pl-3">{{
              $t("additional_time")
            }}</v-card-title>
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
                ></v-autocomplete>
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
                ></v-autocomplete>
              </div>
              <div class="ma-2" style="justify-content: flex-start">
                <v-btn
                  color="success"
                  fab
                  small
                  outlined
                  @click="addStartEndDates"
                >
                  <v-icon>mdi-plus</v-icon>
                </v-btn>
              </div>
              <div
                class="ma-2"
                style="justify-content: flex-start"
                v-if="ishlagan_soat_required"
              >
                <v-alert type="error" outlined dense>
                  {{ ishlagan_soat_required }}
                </v-alert>
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
                    <th class="text-left"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in start_end_dates" :key="index">
                    <td class="text-left">{{ index + 1 }}</td>
                    <td class="text-left">{{ item.start_datetime }}</td>
                    <td class="text-left">{{ item.end_datetime }}</td>
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

          <v-card class="linked_document mt-2" outlined elevation="0" style="border: none">
            <v-card-title class="pa-0 mt-3">
              <span class="cardTitle mr-1" style="color: #c31313"
                >{{ $t("Введите номер связанного документа") }}:</span
              >
              <span class v-if="errorDocRel" style="color: #c31313">
                {{ errorDocRelMessage }}
              </span>
              <v-autocomplete
                class="input_text mr-3"
                clearable
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
                <template v-slot:selection="{ item }">{{
                  item.document_number
                }}</template>
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
                        v-text="item.document_template['name_' + $i18n.locale]"
                      ></v-list-item-subtitle>
                    </span>
                  </v-list-item-content>
                </template>
              </v-autocomplete>
              <v-btn
                color="success"
                class
                small
                outlined
                @click="addDocumentParent"
                elevation="0"
                style="text-transform: none; border-radius: 15px"
                >{{ $t("Добавить") }}</v-btn
              >
            </v-card-title>
            <v-simple-table
              v-if="documentParents && documentParents.length"
              dense
              class="doc-table mt-3 mb-3"
            >
              <template v-slot:default>
                <thead class="doc-table_head">
                  <tr>
                    <th>#</th>
                    <th>{{ $t("document.document_number") }}</th>
                    <th>{{ $t("document.document_date") }}</th>
                    <th>{{ $t("document.document_name") }}</th>
                    <th>{{ $t("document.creator") }}</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody class="doc-table_body">
                  <tr v-for="(item, index) in documentParents" :key="index">
                    <td>{{ index + 1 }}</td>
                    <td style="color: #148cfb !important">
                      {{ item.document_number }}
                    </td>
                    <td>{{ item.document_date }}</td>
                    <td>{{ item.document_name }}</td>
                    <td>{{ item.creator }}</td>
                    <td class="text-center" width="50px">
                      <v-icon
                        class
                        size="20"
                        color="error"
                        @click="deleteDocumentRelation(item)"
                        >mdi-trash-can-outline</v-icon
                      >
                    </td>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>
          </v-card>
          <!-- ------------------------ -->
          <div class="ma-0 pa-0">
            <span class="cardTitle" style="color: #c31313">{{
              $t("Прикрепите файлы: ")
            }}</span>
            <div>
              <vue-dropzone
                style="
                  color: grey;
                  border: 3px dotted #d8d4d4;
                  width: 97%;
                  margin: 15px 0px 0px 16px;
                "
                ref="myVueDropzone"
                id="dropzone"
                height="10px"
                :options="dropzoneOptions"
                @vdropzone-success="handleSuccess"
                @vdropzone-complete="handleComplete"
                v-on:vdropzone-removed-file="removeThisFile"
              >
              </vue-dropzone>

              <v-card-text
                class="pt-0"
                style="width: 700px"
                v-if="uploadedFiles.length > 0"
              >
                <v-simple-table dense class="mt-2" style="border: none">
                  <template v-slot:default>
                    <thead>
                      <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">{{ $t("File Name") }}</th>
                        <th class="text-center">{{ $t("File type") }}</th>
                        <th class="text-center">{{ $t("File size") }}</th>
                        <th class="text-center">{{ $t("Action") }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(item, index) in uploadedFiles" :key="index">
                        <td class="text-left" style="width: 30px">
                          {{ index + 1 }}
                        </td>
                        <td
                          class="text-left"
                          :id="'file-td-' + item.name"
                          style="width: 400px"
                        >
                          <v-icon class="px-0" color="indigo"
                            >mdi-file-document-outline</v-icon
                          >
                          {{ item.name }}
                        </td>
                        <td
                          class="text-left"
                          style="max-width: 150px; overflow: hidden"
                        >
                          {{ item.type }}
                        </td>
                        <td class="text-left">{{ item.size }} kB</td>
                        <td class="text-center" width="50px">
                          <!-- <v-icon
                              @click="downloadFile(item)"
                              :label="$t('File download')"
                              class="px-1"
                              color="green"
                            >
                              mdi-download-outline
                            </v-icon>
                            <v-icon :label="$t('File view')" class="px-1" color="green">
                              mdi-file-eye-outline
                            </v-icon> -->
                          <v-icon
                            @click="removeFileFromTable(item)"
                            :label="$t('File delete')"
                            class="px-1"
                            color="error"
                          >
                            mdi-trash-can-outline
                          </v-icon>
                        </td>
                      </tr>
                    </tbody>
                  </template>
                </v-simple-table>
              </v-card-text>
            </div>
          </div>
          <!-- ------------------------ -->
        </div>
        <v-card v-if="model" class="mt-3" outlined style="border: none">
          <v-card-title class="pa-1 group-signers">
            <span class="cardTitle mr-1" style="color: #c31313"
              >{{ $t("Группа подписантов") }}:</span
            >
            <v-text-field
              class="input_text mdi-20 mr-4"
              readonly
              v-model="signer_group_name"
              prepend-inner-icon="mdi-account-multiple"
              color="#dce5ef"
              :placeholder="$t('search')"
              dense
              hide-details
              solo
              @click="selectSignatory = 'true'"
              style="color: #c31313"
            ></v-text-field>

            <span class="cardTitle mr-1" style="color: #c31313"
              >{{ $t("Индивидуальный подписант") }}:</span
            >
            <v-text-field
              class="input_text"
              v-model="search"
              prepend-inner-icon="mdi-account"
              color="#dce5ef"
              :placeholder="$t('search')"
              dense
              hide-details
              solo
              @click="signatoriesemployye = 'true'"
              style="color: #c31313; font-size: 9px"
            ></v-text-field>
          </v-card-title>
          <v-card-title class="pa-1 group-signers">
            <span class="cardTitle mr-1" style="color: #c31313"
              >{{ $t("Отправитель") }}:</span
            >
            <v-autocomplete
              v-model="document.isFromStaff"
              clearable
              class="input_text mdi-20 mr-4"
              :items="document.document_signers.filter((v) => v.sequence > 98)"
              item-value="staff_id"
              hide-details
              dense
              solo
              outlined
              style="color: #c31313; width: 260px"
            >
              <template v-slot:selection="{ item }">{{
                item.department_code + " " + item.staff_name + " " + item.fio
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

            <span class="cardTitle mr-1" style="color: #c31313"
              >{{ $t("Получатель") }}:</span
            >
            <v-autocomplete
              class="input_text mdi-20 mr-4"
              v-model="document.isToStaff"
              clearable
              :items="document.document_signers.filter((v) => v.sequence <= 98)"
              item-value="staff_id"
              hide-details
              dense
              solo
              outlined
              style="color: #c31313; width: 180px"
            >
              <template v-slot:selection="{ item }">{{
                item.department_code + " " + item.staff_name + " " + item.fio
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
          </v-card-title>
          <!-- ------------------------- -->

          <!-- ------------------------- -->
          <!-- <v-card-title class="pa-1 group-signers" style="border:1px solid red">
              <span class="cardTitle mr-1" style="color: #c31313;border:1px solid red;width:150px"
                >{{ $t("Отправитель") }}:</span>
              <v-autocomplete
                v-model="document.isFromStaff"
                clearable
                class="txt_search1 mdi-20"
                :items="
                  document.document_signers.filter((v) => v.sequence > 98)
                "
                item-value="staff_id"
                hide-details
                dense
                outlined
                style="color: #c31313;border:1px solid red;width:260px"
              >
                <template v-slot:selection="{ item }">{{
                  item.department_code + " " + item.staff_name + " " + item.fio
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
              <span class="cardTitle mr-1" style="color: #c31313;border:1px solid red;width:180px"
                >{{ $t("Получатель") }}:</span>
              <v-autocomplete
              class="txt_search1 mdi-20"
                v-model="document.isToStaff"
                clearable
                :items="
                  document.document_signers.filter((v) => v.sequence <= 98)
                "
                item-value="staff_id"
                hide-details
                dense
                outlined
                style="color: #c31313;border:1px solid red;width:180px"
              >
                <template v-slot:selection="{ item }">{{
                  item.department_code + " " + item.staff_name + " " + item.fio
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
              <v-spacer></v-spacer>
            </v-card-title> -->
          <!-- ------------------------- -->
          <v-data-table
            dense
            :headers="headerSigner"
            :items="document.document_signers"
            class="groupSignaTable mt-3"
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
              <span :title="JSON.stringify(item)">
                {{
                  (item.department_code ? item.department_code : "") +
                  " " +
                  (item.staff_name ? item.staff_name : item.department)
                }}
              </span>
              <br />
              <span style="color: #044693; font-style: italic">{{
                item.staff_position
              }}</span>
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
                <v-icon @click="deleteSigner(item)" size="20"
                  >mdi-trash-can-outline</v-icon
                >
              </v-btn>
            </template>
          </v-data-table>
        </v-card>
        <v-card
          class="mt-10 text-end"
          outlined
          elevation="0"
          style="border: none"
        >
          <v-btn
            class="mr-3"
            color="#3FCB5D"
            right
            small
            dark
            @click="save()"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("save") }}
          </v-btn>
          <v-btn
            class
            color="#EB4034"
            right
            small
            dark
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("Отменить") }}
          </v-btn>
        </v-card>
      </v-card>
    </v-form>

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

    <!-- <v-dialog v-model="signatories" persistent width="800">
      <v-card class="ma-0 pa-3" style="border-radius: 1px">
        <span class="dialog-head_title">{{ $t("Выберите подписанта:") }}</span>
        <v-divider class="mt-1 mb-4" style="border-color: #dce5ef"></v-divider>
        <v-form
          ref="addSignerForm"
          style="border: 1px solid #dce5ef; border-radius: 5px; padding: 10px"
        >
          <v-row class="ma-0 pa-0 dialog-form">
            <v-col cols="12" md="12" class="mb-3 pa-0">
              <v-autocomplete
                :label="$t('document.department')"
                v-model="new_signer_employee.staff"
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
            <v-col cols="5" md="5" class="mb-3 pa-0">
              <v-autocomplete
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
              cols="7"
              md="7"
              class="mb-3 pl-5"
              style="display: flex; align-items: center"
            >
              <span class="dialog-content">Действие или роль подписанта</span>
            </v-col>
            <v-col cols="5" md="5" class="mb-3 pa-0">
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
                hide-details
                dense
                outlined
                full-width
              ></v-text-field>
            </v-col>
            <v-col
              cols="7"
              md="7"
              class="mb-3 pl-5 pr-0 py-0"
              style="display: flex; align-items: center"
            >
              <span class="dialog-content"
                >Последовательность, параллельное подписание если
                последовательность равно другому подписью.</span
              >
            </v-col>
            <v-col cols="5" md="5" class="mb-3 pa-0">
              <v-text-field
              v-model.lazy="new_signer_employee.due_date"
                hide-details
                type="datetime-local"
                dense
                outlined
                clearable
                placeholder="YYYY-MM-DD"
              ></v-text-field>
            </v-col>
            <v-col
              cols="7"
              md="7"
              class="mb-3 pl-5 pr-0 py-0"
              style="display: flex; align-items: center"
            >
              <span class="dialog-content"
                >Срок согласования, утверждения или отклонения.</span
              >
            </v-col>
          </v-row>
        </v-form>
        <v-card
          class="mt-3 text-end"
          outlined
          elevation="0"
          style="border: none"
        >
          <v-btn
            class="mr-3"
            color="#3FCB5D"
            right
            small
            dark
            @click="addSignersEmployee"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("save") }}
          </v-btn>
          <v-btn
            class
            color="#EB4034"
            right
            small
            dark
            elevation="0"
            @click="signatories = false"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("Отменить") }}
          </v-btn>
        </v-card>
      </v-card>
    </v-dialog> -->
    <!-- ------------------------------ -->
    <v-dialog v-model="selectSignatory" persistent width="900">
      <v-card class="ma-0 pa-3" style="border-radius: 1px">
        <span class="dialog-head_title">{{
          $t("Выберите группу подписантов:") + " " + signer_group_name
        }}</span>
        <v-divider class="mt-1 mb-3" style="border-color: #dce5ef"></v-divider>
        <v-card-title class="pa-0 docCreTextField mb-3">
          <v-text-field
            dense
            readonly
            outlined
            v-model="searchFilter.singnerGrup"
            :rules="[(v) => !!v || $t('input.required')]"
            hide-details
            placeholder="Поиск..."
            prepend-inner-icon="mdi-magnify"
            class="input_text"
            style="max-width: 100%"
          ></v-text-field>
        </v-card-title>
        <v-row class="ma-0 pa-0 dialog-form">
          <v-col cols="4" md="4" class="pr-2 pl-0 py-0">
            <v-simple-table
              class="sim-table_left"
              style="border: 1px solid #dce5ef; border-radius: 1px"
            >
              <template v-slot:default>
                <thead>
                  <tr>
                    <th class="text-left"></th>
                    <th class="text-left">Группы подписантов</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(item, index) in signer_groups"
                    :key="index"
                    style="cursor: pointer"
                    @click="addSignerGroups(item.id)"
                  >
                    <td class="text-left" style="width: 30px">
                      {{ index + 1 }}
                    </td>
                    <td>{{ item["name_" + $i18n.locale] }}</td>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>
          </v-col>
          <v-col cols="8" md="8" class="pa-0">
            <v-simple-table
              class="sim-table_left"
              style="border: 1px solid #dce5ef; border-radius: 1px"
            >
              <template v-slot:default>
                <thead>
                  <tr>
                    <th class="text-left"></th>
                    <th class="text-left">Подразделении, должности</th>
                    <th class="text-left">Действие</th>
                    <th class="text-left">Очередь</th>
                    <th class="text-left">Сотрудник</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(itema, indexs) in signerPeron" :key="indexs">
                    <td class="text-left" style="width: 30px">
                      {{ indexs + 1 }}
                    </td>
                    <td>
                      {{
                        itema.staff.department.department_code +
                        " " +
                        itema.staff.department["name_" + $i18n.locale]
                      }}
                    </td>
                    <td>
                      {{
                        action_types.find((v) => {
                          if (v.id == itema.action_type_id) return v;
                        })["name_" + $i18n.locale]
                      }}
                    </td>
                    <td>{{ itema.sequence }}</td>
                    <td>
                      {{
                        itema.staff.employees.length
                          ? itema.staff.employees[0][
                              "firstname_" + $i18n.locale
                            ].substr(0, 1) +
                            "." +
                            itema.staff.employees[0][
                              "middlename_" + $i18n.locale
                            ].substr(0, 1) +
                            "." +
                            itema.staff.employees[0]["lastname_" + $i18n.locale]
                          : ""
                      }}
                    </td>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>
          </v-col>
        </v-row>
          <v-card
            class="mt-0 text-end"
            outlined
            elevation="0"
            style="border: none"
          >
            <v-btn
              class="mr-3"
              color="#3FCB5D"
              right
              small
              dark
              @click="addSignerGroup(signer_group.id)"
              elevation="0"
              style="text-transform: none; border-radius: 5px; padding: 5px 20px">
              {{ $t("save") }}
            </v-btn>
            <v-btn
              class
              color="#EB4034"
              right
              small
              dark
              elevation="0"
              @click="selectSignatory = false"
              style="text-transform: none; border-radius: 5px; padding: 5px 20px"
            >
              {{ $t("Отменить") }}
            </v-btn>
          </v-card>
      </v-card>
    </v-dialog>
    <!-- ------------------------------ -->
    <v-dialog v-model="signatoriesemployye" persistent width="800">
      <v-card class="ma-0 pa-3" style="border-radius: 1px">
        <span class="dialog-head_title">{{ $t("Выберите подписанта:") }}</span>
        <v-divider class="mt-1 mb-4" style="border-color: #dce5ef"></v-divider>
        <v-form
          ref="addSignerForm"
          style="border: 1px solid #dce5ef; border-radius: 5px; padding: 10px"
        >
          <v-row class="ma-0 pa-0 dialog-form">
            <v-col cols="12" md="12" class="mb-3 pa-0">
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
            <v-col cols="5" md="5" class="mb-3 pa-0">
              <v-autocomplete
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
              cols="7"
              md="7"
              class="mb-3 pl-5"
              style="display: flex; align-items: center"
            >
              <span class="dialog-content">Действие или роль подписанта</span>
            </v-col>
            <v-col cols="5" md="5" class="mb-3 pa-0">
              <v-text-field
                v-if="
                  new_signer_employee.action_type_id == 1 ||
                  new_signer_employee.action_type_id == 2 ||
                  new_signer_employee.action_type_id == 8 ||
                  new_signer_employee.action_type_id == 9 ||
                  new_signer_employee.action_type_id == 10 ||
                  new_signer_employee.action_type_id == 12
                "
                v-model="new_signer.sequence"
                :label="$t('documentTemplates.sequence')"
                :rules="[
                  (v) => !!v || $t('input.required'),
                  (v) => (!!v && v > 10) || '10 dan k\'op',
                  (v) => (!!v && v <= 70) || '70 dan kam',
                ]"
                type="number"
                hide-details
                dense
                outlined
                full-width
              ></v-text-field>
            </v-col>
            <v-col
              cols="7"
              md="7"
              class="mb-3 pl-5 pr-0 py-0"
              style="display: flex; align-items: center"
            >
              <span class="dialog-content"
                >Последовательность, параллельное подписание если
                последовательность равно другому подписью.</span
              >
            </v-col>
            <v-col cols="5" md="5" class="mb-3 pa-0">
              <v-text-field
                v-model.lazy="new_signer_employee.due_date"
                hide-details
                type="datetime-local"
                dense
                outlined
                clearable
                placeholder="YYYY-MM-DD"
              ></v-text-field>
            </v-col>
            <v-col
              cols="7"
              md="7"
              class="mb-3 pl-5 pr-0 py-0"
              style="display: flex; align-items: center"
            >
              <span class="dialog-content"
                >Срок согласования, утверждения или отклонения.</span
              >
            </v-col>
          </v-row>
        </v-form>
        <v-card
          class="mt-3 text-end"
          outlined
          elevation="0"
          style="border: none"
        >
          <v-btn
            class="mr-3"
            color="#3FCB5D"
            right
            small
            dark
            @click="addSignersEmployee"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("save") }}
          </v-btn>
          <v-btn
            class
            color="#EB4034"
            right
            small
            dark
            elevation="0"
            @click="signatoriesemployye = false"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("Отменить") }}
          </v-btn>
        </v-card>
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
            <v-col cols="8">
              {{ "Mukofot miqdori 60 dan katta bo'lmasligi kerak!" }}
            </v-col>
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
            <v-col cols="8">
              {{ "KPI maqsadlar soni 3 tadan ko`p bo`lishi lozim!" }}
            </v-col>
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
  </div>
</template>
<script>
const axios = require("axios").default;
const moment = require("moment");
import Swal from "sweetalert2";
import Help from "@/components/Help.vue";
import { Vue2TinymceEditor } from "vue2-tinymce-editor";
// import DragonDropFile from "../document/DocumentTest.vue";
import vue2Dropzone from "vue2-dropzone";
import "vue2-dropzone/dist/vue2Dropzone.min.css";
export default {
  components: {
    vueDropzone: vue2Dropzone,
    // DragonDropFile,
    Help,
    Vue2TinymceEditor,
  },

  data() {
    return {
      dropzoneOptions: {
        url: "https://httpbin.org/post",
        maxFilesize: 1.5,
        addRemoveLinks: true,
        dictDefaultMessage:
          "<img style='height:50px; margin: -30px 0px -20px 0px;' src='img/cloud-upload-outline.png'> Перетащите файлы или <label  style='color:blue'>загрузите с локальной папки</label>",
      },
      uploadedFiles: [],
      staj: 0,
      searchFilter: [],
      start_time: null,
      ishlagan_soat_required: null,
      start_date: "2023-08-01",
      end_time: null,
      start_end_dates: [],
      signerPeron: [],
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
      chiefEmployees: [],
      editorOption: {},
      loading: false,
      documentTitle: "",
      document: {
        document_type_id: null,
        document_template_id: null,
        department2_id: null,
        staff_id: null,
        responsible_contact: null,
        document_details: [],
        document_signers: [],
        document_staff: [],
      },
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
      selectSignatory: false,
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
      parent_document_id: null,
      documentParents: [],
      documentStaff: [],
      signer_groups: [],
      signer_group: [],
      signer_group_id: null,
      money: {
        decimal: ".",
        thousands: ",",
        masked: false,
        precision: 2,
      },
      is_gmk_file: false,
    };
  },
  computed: {
    signer_group_name() {
      return this.signer_group ? this.signer_group.name_ru : "tanlanmagan";
    },

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
    handleSuccess(file, response) {
      this.uploadedFiles.push(file);
    },
    handleComplete(file) {
      // Fayl yuklashni tugatganda ishlatiladi
    },
    downloadFile(file) {
      // Faylni yuklab olish uchun xizmat qiladigan metod
    },
    removeFileFromTable(file) {
      this.removeThisFile(file);
    },
    removeThisFile(file) {
      const index = this.uploadedFiles.findIndex(
        (uploadedFile) => uploadedFile.name === file.name
      );
      if (index !== -1) {
        this.uploadedFiles.splice(index, 1);
        this.$refs.myVueDropzone.removeFile(file);
      }
    },
    deleteStartEndDate(item) {
      this.start_end_dates = this.start_end_dates.filter((v) => v != item);
    },
    addStartEndDates() {
      if (this.start_date && this.start_time && this.end_time) {
        let end_date = this.start_date;
        // console.log(111,this.start_time,this.end_time)
        // console.log(222,this.start_time < '24:00' , this.end_time >= '00:00')
        // if(this.start_time > '12:00' && this.start_time < '24:00' && this.end_time >= '00:00'){
        //   let d = new Date(new Date(this.start_date).getTime() + 86400000);
        //   end_date = d.toISOString().substring(0,10);
        // }
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
            if (v.id == this.new_signer_employee.action_type_id) return v;
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
              element.manager_staff.employees.length
            ) {
              this.document.document_signers.push({
                id: Date.now() + Math.floor(Math.random() * 1000),
                document_id: this.document.id,
                staff_id: element.manager_staff_id,
                action_type_id: 3,
                taken_datetime: moment().add(1, "days").format("YYYY-MM-DD"),
                sequence: element.department_type.sequence <= 2 ? 99 : 100,
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
        this.document.document_staff.push(this.select_staff_id);
      }

      this.select_staff_id = null;
    },
    addFiles() {
      this.selectFiles.forEach((v, i) => {
        if (this.is_gmk_file) {
          this.formData.append("gmk_files[]", v);
        } else {
          this.formData.append("files[]", v);
        }
        this.documentFiles.push({
          id: Date.now() + Math.floor(Math.random() * 1000),
          file_name: v.name,
        });
      });
      this.selectFiles = [];
      this.is_gmk_file = false;
      this.modalDocumentFile = false;
    },
    addSignerGroups(id) {
      this.signer_group = this.signer_groups.find((v) => v.id == id);
      this.signerPeron = this.signer_group.signer_group_details;
      this.searchFilter.singnerGrup =
        this.signer_group["name_" + this.$i18n.locale];
    },
    addSignerGroup(id) {
      console.log("signer_group=", this.signer_group);
      console.log("id=", id);
      console.log("addSignerGroup=", this.addSignerGroup);
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
              action_type: this.action_types.find((v) => {
                if (v.id == signer_group_detail.action_type_id) return v;
              })["name_" + this.$i18n.locale],
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
      this.selectSignatory = false;
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
          this.signer_groups =
            this.document.document_template.signer_groups.map(
              (v) => (v.text = v["name_" + this.$i18n.locale])
            );
          this.documentName =
            this.document.document_template["name_" + this.$i18n.locale];
          if (
            (this.document &&
              (this.document.status < 2 || this.document.status == 11) &&
              this.document.created_employee_id == this.user.employee_id) ||
            this.$store.getters.checkPermission("edit_all_document") ||
            this.document.action_type_id == 13
          ) {
            this.model = true;
            this.document.document_details.forEach((element) => {
              element.document_detail_attribute_values.map((v) => {
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
            this.document.add_parent =
              this.document.document_template.add_parent;
            this.document.is_content_visible =
              this.document.document_template.is_content_visible;
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
              v.first_name = v.employee.firstname;
              v.last_name = v.employee.lastname;
              v.code = v.staff.department.department_code;
              v.department_name =
                v.staff.department["name_" + this.$i18n.locale];
              v.position_name = v.staff.position["name_" + this.$i18n.locale];
              // v.text = v.employee.firstname[0] + " " + v.firstname + " " + v.lastname;
            }
            return v;
          });
          console.log("this.resalution_employee=", this.resalution_employee);

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
          this.document.is_document_relation = res.data.is_document_relation;
          this.document.locale = this.$i18n.locale;
          this.signer_groups = res.data.signer_groups;
          this.signer_groups.map((v) => {
            v.text = v["name_" + this.$i18n.locale];
          });
          this.document.document_number = "000000";
          this.document.document_details[0].content =
            res.data.document_detail_templates[0][
              "content_" + this.$i18n.locale
            ];
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
              // if (this.document.document_template_id == 1) {
              //   this.document.department_id =
              //     resp.data.parents.main_department.id;
              // }
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
                        element.department_type.sequence <= 2 ? 99 : 100,
                      visible:
                        element.department_type.sequence <= 2 ? true : false,
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
      if (this.document.document_template_id == 597) {
        // // 2597
        // let days =
        //   this.document.document_details[0].document_detail_attribute_values.find(
        //     (v) => v.d_d_attribute_id == 2597
        //   ).attribute_value;
        // let reduced =
        //   this.start_end_dates.reduce(
        //     (a, b) =>
        //       a + (new Date(b.end_datetime) - new Date(b.start_datetime)),
        //     0
        //   ) /
        //   1000 /
        //   60 /
        //   60;

        // // 2023-01-01 08:00:00    12:00-13:00   s <= 12  &&  13 <= e  ||
        // // 2023-01-01 15:00:00
        // // 2023-01-01 20:00:00    00:00-01:00   s <= 00  &&  01 <= e  ||
        // // 2023-01-01 03:00:00
        // reduced = reduced - this.start_end_dates.reduce((a, b) => {
        //   let s = b.start_datetime.substring(11,16);
        //   let e = b.end_datetime.substring(11,16);
        //   console.log(123,s,e)
        //   if(s <= '12:00'  &&  '13:00' <= e  ||  s <= "24:00" && s >= "12:00"  &&  '01:00' <= e){
        //     return a + 1;
        //   }
        //   return a;
        // }, 0);
        // console.log(reduced, days);
        // if (!this.start_end_dates.length) {
        //   this.ishlagan_soat_required =
        //     "Avval ishdan tashqari ishlagan soatlaringizni kiriting.";
        //   return 0;
        // } else if (reduced != days * 8) {
        //   this.ishlagan_soat_required =
        //     "Ishdan tashqari ishlagan soatlaringiz dam olish kunlari soatlariga mos emas. Har bir kun uchun 8 soatdan kiriting.";
        //   return 0;
        // }

        this.document.start_end_dates = this.start_end_dates;
      } else if (this.document.document_template_id == 431) {
        this.kpi_validat = false;
        this.kpiKursatkich();
        this.kpiMukofot();
      }
      if (this.$refs.documentCreateForm.validate()) {
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

        // if (this.document.document_template.select_staff == 1) {
        //   if (this.document.document_staff.length) {
        //     staff_validat = true;
        //   } else {
        //     staff_validat = false;
        //     this.errorStaffMessage = this.$t("document.empty_staff");
        //     this.loading = false;
        //   }
        // } else {
        //   staff_validat = true;
        // }

        if (
          this.document.is_document_relation &&
          !this.documentParents.length
        ) {
          document_relation_validat = false;
          this.errorDocRel = true;
          this.errorDocRelMessage = this.$t("document.empty_document_relation");
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
          console.log(this.kpi_validat_mukofot);

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
                    // footer: '<a href="">Why do I have this issue?</a>'
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
                    // footer: '<a href="">Why do I have this issue?</a>'
                  });
                }
                this.loading = false;
              } else {
                this.doc_id = res.data.document_id;
                this.pdf_file_name = res.data.pdf_file_name;
                // documentga fiyl biriktirish
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
                    this.loading = false;
                    // this.$router.push("/documents/list");
                  })
                  .catch((err) => {
                    console.error(err);
                    this.loading = false;
                  });
                // documentga document biriktirish
                axios
                  .post(
                    this.$store.state.backend_url +
                      "api/documents/update-document-relation/" +
                      this.doc_id,
                    this.documentParents
                  )
                  .then((res) => {
                    this.loading = false;
                    // this.$router.push("/documents/list");
                  })
                  .catch((err) => {
                    console.error(err);
                    this.loading = false;
                  });
                this.loading = false;
                this.$router.push("/documentsidebar/document/" + this.pdf_file_name);
              }
            })
            .catch((err) => {
              console.error(err);
              this.loading = false;
            });
        } else {
          this.loading = false;
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
    } else if (this.$route.params.documentId) {
      this.documentTitle = this.$t("document.update");
      this.getDocument(this.$route.params.documentId);
    } else {
      this.$router.push("/documents/list");
    }
    this.formData = new FormData();
    this.getDocumentList();
    this.getCoefficients();

    if (!["305", "357"].includes(this.document.document_template_id)) {
      this.action_types = this.action_types.filter((v) => v.id != 16);
    }
  },
};
</script>

<style scoped>
body {
  margin: 0px;
}
.cardTitle {
  color: black;
  font-size: 14px;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
  font-weight: 400;
  word-wrap: break-word;
}
.card-btn {
  color: #fff;
  font-size: 12px;
  font-weight: 400;
  background: #2765ae;
  border-radius: 15px;
  text-transform: normal;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.form-add_employee .input_text {
  max-width: 100%;
  border: 1px solid #dce5ef !important;
  border-radius: 1px;
}
.input_text {
  max-width: 350px;
  border: 1px solid #dce5ef !important;
  border-radius: 1px;
}
.input_text label {
  color: red !important;
}
.doc-table table {
  border: 1px solid #d9d9d9;
}
.doc-table_head tr,
.doc-table_head th {
  color: #000 !important;
  font-size: 12px;
  font-weight: 600;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
  padding: 0px 10px !important;
  border: 1px solid #d9d9d9 !important;
}
.doc-table_body tr,
.doc-table_body td {
  color: rgba(0, 0, 0, 0.5) !important;
  font-size: 12px;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
  padding: 0px 10px !important;
  border: 1px solid #d9d9d9 !important;
}
.doc-data_table thead tr th {
  color: red !important;
}

.dialog-head_title {
  color: #000;
  font-size: 14px;
  font-weight: 600;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.dialog-content {
  color: #0868c4;
  font-size: 12px;
  font-weight: 400;
  font-style: italic;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.sim-table_left {
  height: 100%;
}
.sim-table_left table {
  width: 100%;
  border-spacing: 0;
  border-radius: 0;
}
.sim-table_left table > thead > tr > th,
.sim-table_left table > tbody > tr > td,
.sim-table_left table > tr {
  padding: 5px !important;
}
.sim-table_left table > thead > tr {
  background: #f1f5f8;
  border-bottom: 1px solid #dce5ef;
}
.sim-table_left table > thead > tr > th {
  height: 25px !important;
  color: #6c869f !important;
  font-size: 12px;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}

.sim-table_left table > tbody > tr > td {
  max-width: 240px;
  height: 25px !important;
  color: rgba(0, 0, 0, 0.7) !important;
  font-size: 12px !important;
  font-weight: 500;
  overflow: hidden;
  text-overflow: ellipsis;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.dropzone {
  padding: 0px !important;
  min-height: 70px !important;
}
.txt_search1 {
  width: 60px;
  border: 1px solid #e6e6e6;
  box-shadow: none;
  max-height: 25px;
  overflow: hidden;
  border-radius: 0 0 0 0;
  height: 25px;
  color: #212529;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
  margin: 0px 20px 0px 5px;
}
</style>
