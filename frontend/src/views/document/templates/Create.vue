<template>
  <div>
    <v-tabs v-model="tab" align-with-title>
      <v-tabs-slider color="success"></v-tabs-slider>
      <v-tab>
        <span>{{ $t('documentTemplates.index') }}</span>
      </v-tab>
      <v-tab v-if="$route.params.id"> blank </v-tab>
    </v-tabs>
    <v-tabs-items v-model="tab">
      <v-tab-item>
        <v-form ref="dialogForm">
          <v-card class="ma-1 pa-1">
            <v-card-title class="pa-1">
              <span>{{ $t('documentTemplates.index') }}</span>
              <v-spacer></v-spacer>
              <v-btn color="#6ac82d" small dark @click="save">
                {{ $t('save') }}
              </v-btn>
            </v-card-title>
            <v-row>
              <v-col cols="6" md="4" class="py-1 pr-1">
                <label for>{{ $t('name_uz_latin') }}</label>
                <v-text-field
                  v-model="form.name_uz_latin"
                  :rules="[(v) => !!v || $t('input_required')]"
                  hide-details
                  dense
                  outlined
                ></v-text-field>
              </v-col>

              <v-col cols="6" md="4" class="pa-1">
                <label for>{{ $t('name_uz_cyril') }}</label>
                <v-text-field
                  v-model="form.name_uz_cyril"
                  :rules="[(v) => !!v || $t('input_required')]"
                  hide-details
                  dense
                  outlined
                ></v-text-field>
              </v-col>

              <v-col cols="6" md="4" class="py-1 pl-1">
                <label for>{{ $t('name_ru') }}</label>
                <v-text-field
                  v-model="form.name_ru"
                  :rules="[(v) => !!v || $t('input_required')]"
                  hide-details
                  dense
                  outlined
                ></v-text-field>
              </v-col>

              <v-col cols="6" md="4" class="py-1 pr-1">
                <label for>{{ $t('documentTypes.index') }}</label>
                <v-autocomplete
                  clearable
                  v-model="form.document_type_id"
                  :items="documentTypes"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>

              <v-col cols="6" md="4" class="pa-1">
                <label for>{{ $t('department.index') }}</label>
                <v-autocomplete
                  class="pa-0"
                  clearable
                  v-model="form.department_id"
                  :items="departments"
                  @keyup="getDepartment()"
                  @click="getDepartment()"
                  :search-input.sync="search"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details
                  dense
                  outlined
                >
                  <template v-slot:item="{ item }">
                    <v-list-item-content>
                      <v-list-item-title
                        v-text="item['name_' + $i18n.locale]"
                      ></v-list-item-title>
                    </v-list-item-content>
                  </template>
                </v-autocomplete>
              </v-col>

              <v-col cols="6" md="4" class="py-1 pl-1">
                <label for>{{ $t('signerGroup.signer_group_id') }}</label>
                <v-autocomplete
                  clearable
                  v-model="form.signer_group_ids"
                  :items="signerGroups"
                  item-value="id"
                  multiple
                  hide-details
                  dense
                  outlined
                  @change="getSignerGroupStaffs()"
                ></v-autocomplete>
              </v-col>

              <v-col cols="6" md="4" lg="3" class="py-1 pr-1">
                <label for>{{ $t('documentTemplates.numeration_type') }}</label>
                <v-autocomplete
                  clearable
                  v-model="form.numeration_type"
                  :items="[
                    {
                      name_uz_latin: 'YYT-NO-COUNT',
                      name_uz_cyril: 'YYT-NO-COUNT',
                      name_ru: 'YYT-NO-COUNT',
                      value: 6,
                    },
                    {
                      name_uz_latin: 'YYT-COUNT',
                      name_uz_cyril: 'YYT-COUNT',
                      name_ru: 'YYT-COUNT',
                      value: 7,
                    },
                    {
                      name_uz_latin: 'Counter',
                      name_uz_cyril: 'Counter',
                      name_ru: 'Counter',
                      value: 4,
                    },
                    {
                      name_uz_latin: 'Qisqa',
                      name_uz_cyril: 'Қисқа',
                      name_ru: 'Короткая',
                      value: 1,
                    },
                    {
                      name_uz_latin: 'Uzun',
                      name_uz_cyril: 'Узун',
                      name_ru: 'Длинный',
                      value: 2,
                    },
                    {
                      name_uz_latin: 'Uzun -',
                      name_uz_cyril: 'Узун -',
                      name_ru: 'Длинный -',
                      value: 3,
                    },
                    {
                      name_uz_latin: 'Invest',
                      name_uz_cyril: 'Инвест',
                      name_ru: 'Инвест',
                      value: 5,
                    },
                  ]"
                  hide-details
                  dense
                  outlined
                  :item-text="'name_' + $i18n.locale"
                ></v-autocomplete>
              </v-col>

              <v-col cols="6" md="4" lg="3" class="py-1 pr-1">
                <label for>{{ $t('documentTemplates.template_code') }}</label>
                <v-text-field
                  v-model="form.template_code"
                  hide-details
                  dense
                  outlined
                ></v-text-field>
              </v-col>

              <v-col cols="6" md="4" lg="3" class="py-1 pr-1">
                <label for>{{ $t('documentTemplates.folder_code') }}</label>
                <v-text-field
                  v-model="form.folder_code"
                  hide-details
                  dense
                  outlined
                ></v-text-field>
              </v-col>

              <v-col cols="6" md="4" lg="3" class="py-1 pr-1">
                <label for>{{ $t('documentTemplates.digital') }}</label>
                <v-text-field
                  v-model="form.digital"
                  hide-details
                  dense
                  outlined
                ></v-text-field>
              </v-col>

              <v-col cols="6" md="4" lg="3" class="py-1 pr-1">
                <label for>{{ $t('documentTemplates.numbering_order') }}</label>
                <v-autocomplete
                  clearable
                  v-model="form.numbering_order"
                  :items="[
                    {
                      name_uz_latin: 'Elon qilinganda',
                      name_uz_cyril: 'Эълон килинганда',
                      name_ru: 'Когда объявлено',
                      value: 1,
                    },
                    {
                      name_uz_latin: `Boshqarmada imzolanib bo'lganda`,
                      name_uz_cyril: 'Бошкармада имзоланиб булганида',
                      name_ru: 'Утвержден внутри подразделения',
                      value: 2,
                    },
                    {
                      name_uz_latin: 'Rahbar tasdiqlagandan keyin',
                      name_uz_cyril: 'Рахбар тасдиклагандан кейин',
                      name_ru: 'После утверждения руководства',
                      value: 3,
                    },
                  ]"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details
                  dense
                  outlined
                  :item-text="'name_' + $i18n.locale"
                ></v-autocomplete>
              </v-col>

              <v-col cols="6" md="4" lg="3" class="py-1">
                <label for>{{ $t('new_employee') }}</label>
                <v-autocomplete
                  clearable
                  v-model="form.change_staff"
                  :items="[
                    {
                      name_uz_latin: 'Xodim shtatini o`zgartirish',
                      name_uz_cyril: 'Changing the staff',
                      name_ru: 'Изменение штата сотрудников',
                      value: 1,
                    },
                    {
                      name_uz_latin: 'Yangi xodim qo`shish',
                      name_uz_cyril: 'Adding a new employee',
                      name_ru: 'Добавление нового сотрудника',
                      value: 2,
                    },
                  ]"
                  hide-details
                  dense
                  outlined
                  :item-text="'name_' + $i18n.locale"
                ></v-autocomplete>
              </v-col>

              <v-col cols="6" md="4" lg="3" class="py-1">
                <label for>{{ $t('table_font_size') }}</label>
                <v-select
                  v-model="form.table_font_size"
                  :items="[5, 6, 7, 8, 9, 10, 11, 12, 13, 14]"
                  hide-details
                  dense
                  outlined
                ></v-select>
              </v-col>

              <v-col cols="6" md="4" lg="3" class="py-1">
                <v-checkbox
                  class="ma-0"
                  :label="$t('signerGroup.add_signer')"
                  v-model="form.add_signer"
                  hide-details
                ></v-checkbox>
              </v-col>

              <v-col cols="6" md="4" lg="3" class="py-1">
                <v-checkbox
                  class="ma-0"
                  :label="$t('signerGroup.add_signer_employee')"
                  v-model="form.add_signer_employee"
                  hide-details
                ></v-checkbox>
              </v-col>

              <v-col cols="6" md="4" lg="3" class="py-1">
                <v-checkbox
                  class="ma-0"
                  :label="$t('has_employee')"
                  v-model="form.has_employee"
                  hide-details
                ></v-checkbox>
              </v-col>

              <v-col cols="6" md="4" lg="3" class="py-1">
                <v-checkbox
                  class="ma-0"
                  :label="$t('document.add_parent')"
                  v-model="form.add_parent"
                  hide-details
                ></v-checkbox>
              </v-col>

              <v-col cols="6" md="4" lg="3" class="py-1">
                <v-checkbox
                  class="ma-0"
                  :label="$t('document.is_pdf_portrait')"
                  v-model="form.is_pdf_portrait"
                  hide-details
                ></v-checkbox>
              </v-col>

              <v-col cols="6" md="4" lg="3" class="py-1">
                <v-checkbox
                  class="ma-0"
                  :label="$t('document_detail_templates.is_list_vertical')"
                  v-model="form.is_list_vertical"
                  hide-details
                ></v-checkbox>
              </v-col>

              <v-col cols="6" md="4" lg="3" class="py-1">
                <v-checkbox
                  class="ma-0"
                  :label="$t('documentTemplates.is_from_to_department_show')"
                  v-model="form.is_from_to_department_show"
                  hide-details
                ></v-checkbox>
              </v-col>

              <v-col cols="6" md="4" lg="3" class="py-1">
                <v-checkbox
                  class="ma-0"
                  :label="$t('documentTemplates.is_content_visible')"
                  v-model="form.is_content_visible"
                  hide-details
                ></v-checkbox>
              </v-col>

              <v-col cols="6" md="4" lg="3" class="py-1">
                <v-checkbox
                  class="ma-0"
                  :label="$t('documentTemplates.is_document_relation')"
                  v-model="form.is_document_relation"
                  hide-details
                ></v-checkbox>
              </v-col>
              <v-col cols="6" md="4" lg="3" class="py-1">
                <v-checkbox
                  class="ma-0"
                  :label="$t('select_staff')"
                  v-model="form.select_staff"
                  hide-details
                ></v-checkbox>
              </v-col>
              <v-col cols="6" md="4" lg="3" class="py-1">
                <v-checkbox
                  class="ma-0"
                  :label="$t('select_department')"
                  v-model="form.select_department"
                  hide-details
                ></v-checkbox>
              </v-col>
              <v-col cols="6" md="4" lg="3" class="py-1">
                <v-checkbox
                  class="ma-0"
                  :label="$t('documentTemplates.is_attribute_show')"
                  v-model="form.is_attribute_show"
                  hide-details
                ></v-checkbox>
              </v-col>
              <v-col cols="6" md="4" lg="3" class="py-1">
                <v-checkbox
                  class="ma-0"
                  :label="$t('documentTemplates.is_confidential')"
                  v-model="form.is_confidential"
                  hide-details
                ></v-checkbox>
              </v-col>
            </v-row>
          </v-card>

          <v-card class="ma-1 pa-1">
            <div>
              <v-system-bar window color="#eee">
                <span class="font-weight-bold">{{
                  $t('documentTemplates.signers')
                }}</span>
                <v-spacer></v-spacer>
                <v-icon
                  color="success"
                  medium
                  @click="
                    form.document_signer_templates.push({
                      id: Date.now(),
                      action_type_id: '',
                      due_day_count: 24,
                      staff_id: '',
                      sequence: '',
                    })
                  "
                  >mdi-plus</v-icon
                >
              </v-system-bar>
            </div>
            <v-card-text>
              <v-simple-table dense>
                <template v-slot:default>
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>{{ $t('staff.index') }}</th>
                      <th>{{ $t('actionTypes.index') }}</th>
                      <th style="max-width: 200px !important">
                        {{ $t('document.term') }}
                      </th>
                      <th style="max-width: 100px !important">
                        {{ $t('documentTemplates.sequence') }}
                      </th>
                      <th style="max-width: 100px !important">
                        {{ $t('documentTemplates.sign_type') }}
                      </th>
                      <th style="max-width: 100px !important">
                        {{ $t('documentTemplates.registry_type') }}
                      </th>
                      <th style="max-width: 100px !important">
                        {{ $t('actions') }}
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="(item, index) in form.document_signer_templates"
                      :key="index"
                    >
                      <td>{{ index + 1 }}</td>
                      <td>
                        <v-autocomplete
                          clearable
                          v-model="item.staff_id"
                          :items="staffs"
                          item-text="staffInfo"
                          item-value="id"
                          :rules="[(v) => !!v || $t('input.required')]"
                          hide-details
                          dense
                          outlined
                          full-width
                          class="my-1"
                          @keyup="getStaff(index)"
                          @change="getFormSigners()"
                          :search-input.sync="search_staff[index]"
                        >
                          <template v-slot:selection="{ item }">
                            <v-row class="ma-0 pa-0" style="font-size: 12px">
                              <v-col cols="12" class="ma-0 pa-0">
                                <b>{{
                                  item && item.department
                                    ? item.department.code +
                                      ' ' +
                                      item.department.text
                                    : ''
                                }}</b>
                              </v-col>
                              <v-col cols="12" class="ma-0 pa-0">
                                <b>{{
                                  item && item.position
                                    ? item.position.text
                                    : ''
                                }}</b>
                              </v-col>
                              <!-- <v-col cols="12" class="ma-0 pa-0">
                            {{$t('employee.range')}}:
                            {{item.range ? item.range.code : ''}} /
                            {{$t('staff.rate_count')}}:
                            {{item.rate_count}}
                          </v-col>-->
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
                                    ? item.department.code +
                                      ' ' +
                                      item.department.text
                                    : ''
                                }}</b>
                              </v-col>
                              <v-col cols="12" class="ma-0 pa-0">
                                <b>{{
                                  item && item.position
                                    ? item.position.text
                                    : ''
                                }}</b>
                              </v-col>
                            </v-row>
                          </template>
                        </v-autocomplete>
                      </td>
                      <td>
                        <v-autocomplete
                          clearable
                          v-model="item.action_type_id"
                          :items="actionTypes"
                          :rules="[(v) => !!v || $t('input.required')]"
                          hide-details
                          dense
                          outlined
                          full-width
                        >
                          <template v-slot:selection="{ item }">
                            {{ item.text }}
                          </template>
                          <template v-slot:item="{ item }">
                            <v-list-item-content>
                              <v-list-item-title
                                v-text="item.text"
                              ></v-list-item-title>
                              <v-list-item-subtitle>
                                {{ item.description }}
                              </v-list-item-subtitle>
                            </v-list-item-content>
                          </template>
                        </v-autocomplete>
                      </td>
                      <td>
                        <v-text-field
                          v-model="item.due_day_count"
                          :rules="[(v) => !!v || $t('input.required')]"
                          type="number"
                          max="10"
                          min="1"
                          hide-details
                          dense
                          outlined
                          full-width
                        ></v-text-field>
                      </td>
                      <td>
                        <v-text-field
                          v-model="item.sequence"
                          :rules="[(v) => !!v || $t('input.required')]"
                          type="number"
                          max="10"
                          min="0"
                          hide-details
                          dense
                          outlined
                          full-width
                        ></v-text-field>
                      </td>
                      <td>
                        <v-autocomplete
                          clearable
                          v-model="item.sign_type"
                          :items="signTypes"
                          hide-details
                          dense
                          outlined
                          full-width
                        ></v-autocomplete>
                      </td>
                      <td>
                        <v-autocomplete
                          clearable
                          v-model="item.is_registry"
                          :items="registryTypes"
                          hide-details
                          dense
                          outlined
                          full-width
                        ></v-autocomplete>
                      </td>
                      <td>
                        <v-btn
                          color="red"
                          small
                          text
                          @click="deleteDocumentSignerTemplates(item)"
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

          <v-card class="ma-1 pa-1">
            <div>
              <v-system-bar window color="#eee">
                <span class="font-weight-bold">{{
                  $t('document_detail_templates.index')
                }}</span>
                <v-spacer></v-spacer>
                <v-icon
                  color="success"
                  medium
                  @click="
                    form.document_detail_templates[0].document_detail_attributes.push(
                      {
                        id: Date.now(),
                        data_type_id: '',
                        attribute_name_uz_latin: '',
                        attribute_name_uz_cyril: '',
                        attribute_name_ru: '',
                        value_min_length: '',
                        value_max_length: '',
                        description: '',
                        table_list_id: null,
                        required: null,
                        is_list_vertical: true,
                        is_from_to_department_show: true,
                        sequence: 1,
                        signer_staff_ids: [],
                        is_registry_show: 1,
                        is_show: 1,
                      }
                    )
                  "
                  >mdi-plus</v-icon
                >
              </v-system-bar>
            </div>
            <v-card-text>
              <v-row>
                <v-col class="pt-0">
                  <h3>
                    {{ $t('document_detail_templates.content_uz_latin') }}
                  </h3>
                  <editor
                    v-model="form.document_detail_templates[0].content_uz_latin"
                    api-key="no-api-key"
                    :init="{
                      height: 200,
                      menubar: true,
                      skin: false,
                      plugins: [
                        'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                        'searchreplace wordcount visualblocks visualchars code fullscreen',
                        'insertdatetime media nonbreaking save table contextmenu directionality',
                        'template paste textcolor colorpicker textpattern imagetools toc help emoticons hr codesample',
                      ],
                      browser_spellcheck: true,
                      spellchecker_languages: 'Russian=ru_RU',
                      spellchecker_language: 'ru_RU',
                      language: 'ru',
                      //forced_root_block: false,
                      // force_br_newlines: true,
                      force_p_newlines: true,
                      // convert_newlines_to_brs: true,
                      // nonbreaking_force_tab: true,
                      setup: function (editor) {
                        editor.addButton('indentText', {
                          icon: 'indent',
                          onclick: function () {
                            //editor.insertContent('fkjgfkj');
                            //editor.execCommand('mceInsertContent', false, 'your content');
                            editor.dom.addClass(
                              editor.selection.getNode(),
                              'indent'
                            );
                          },
                        });
                        editor.addButton('indentRemove', {
                          icon: 'outdent',
                          onclick: function () {
                            //editor.insertContent('fkjgfkj');
                            //editor.execCommand('mceInsertContent', false, 'your content');
                            editor.dom.removeClass(
                              editor.selection.getNode(),
                              'indent'
                            );
                          },
                        });
                      },
                      content_style:
                        'body { font-family:Times,sans-serif; font-size:14pt } p{margin:0px; padding:0px;} .indent{ text-indent:40px;}',
                      fontsize_formats: '8pt 10pt 12pt 14pt 16pt 18pt',
                      toolbar:
                        'fullscreen | preview | undo | redo | fontsizeselect | bold italic  strikethrough  forecolor backcolor | alignleft aligncenter alignright alignjustify  | numlist bullist indentText indentRemove  | removeformat',
                    }"
                  />
                  <vueTinymce
                    v-if="false"
                    id="d1"
                    v-model="form.document_detail_templates[0].content_uz_latin"
                  ></vueTinymce>
                  <h3 class="mt-4">
                    {{ $t('document_detail_templates.content_uz_cyril') }}
                  </h3>
                  <editor
                    v-model="form.document_detail_templates[0].content_uz_cyril"
                    api-key="no-api-key"
                    :init="{
                      height: 200,
                      menubar: true,
                      skin: false,
                      plugins: [
                        'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                        'searchreplace wordcount visualblocks visualchars code fullscreen',
                        'insertdatetime media nonbreaking save table contextmenu directionality',
                        'template paste textcolor colorpicker textpattern imagetools toc help emoticons hr codesample',
                      ],
                      browser_spellcheck: true,
                      spellchecker_languages: 'Russian=ru_RU',
                      spellchecker_language: 'ru_RU',
                      language: 'ru',
                      //forced_root_block: false,
                      // force_br_newlines: true,
                      force_p_newlines: true,
                      // convert_newlines_to_brs: true,
                      // nonbreaking_force_tab: true,
                      setup: function (editor) {
                        editor.addButton('indentText', {
                          icon: 'indent',
                          onclick: function () {
                            //editor.insertContent('fkjgfkj');
                            //editor.execCommand('mceInsertContent', false, 'your content');
                            editor.dom.addClass(
                              editor.selection.getNode(),
                              'indent'
                            );
                          },
                        });
                        editor.addButton('indentRemove', {
                          icon: 'outdent',
                          onclick: function () {
                            //editor.insertContent('fkjgfkj');
                            //editor.execCommand('mceInsertContent', false, 'your content');
                            editor.dom.removeClass(
                              editor.selection.getNode(),
                              'indent'
                            );
                          },
                        });
                      },
                      content_style:
                        'body { font-family:Times,sans-serif; font-size:14pt } p{margin:0px; padding:0px;} .indent{ text-indent:40px;}',
                      fontsize_formats: '12pt 14pt 16pt 18pt',
                      toolbar:
                        'fullscreen | preview | undo | redo | fontsizeselect | bold italic  strikethrough  forecolor backcolor | alignleft aligncenter alignright alignjustify  | numlist bullist indentText indentRemove  | removeformat',
                    }"
                  />
                  <vueTinymce
                    v-if="false"
                    id="d2"
                    v-model="form.document_detail_templates[0].content_uz_cyril"
                  ></vueTinymce>
                  <h3 class="mt-4">
                    {{ $t('document_detail_templates.content_ru') }}
                  </h3>
                  <editor
                    v-model="form.document_detail_templates[0].content_ru"
                    api-key="no-api-key"
                    :init="{
                      height: 200,
                      menubar: true,
                      skin: false,
                      plugins: [
                        'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                        'searchreplace wordcount visualblocks visualchars code fullscreen',
                        'insertdatetime media nonbreaking save table contextmenu directionality',
                        'template paste textcolor colorpicker textpattern imagetools toc help emoticons hr codesample',
                      ],
                      browser_spellcheck: true,
                      spellchecker_languages: 'Russian=ru_RU',
                      spellchecker_language: 'ru_RU',
                      language: 'ru',
                      //forced_root_block: false,
                      // force_br_newlines: true,
                      force_p_newlines: true,
                      // convert_newlines_to_brs: true,
                      // nonbreaking_force_tab: true,
                      setup: function (editor) {
                        editor.addButton('indentText', {
                          icon: 'indent',
                          onclick: function () {
                            //editor.insertContent('fkjgfkj');
                            //editor.execCommand('mceInsertContent', false, 'your content');
                            editor.dom.addClass(
                              editor.selection.getNode(),
                              'indent'
                            );
                          },
                        });
                        editor.addButton('indentRemove', {
                          icon: 'outdent',
                          onclick: function () {
                            //editor.insertContent('fkjgfkj');
                            //editor.execCommand('mceInsertContent', false, 'your content');
                            editor.dom.removeClass(
                              editor.selection.getNode(),
                              'indent'
                            );
                          },
                        });
                      },
                      content_style:
                        'body { font-family:Times,sans-serif; font-size:14pt } p{margin:0px; padding:0px;} .indent{ text-indent:40px;}',
                      fontsize_formats: '12pt 14pt 16pt 18pt',
                      toolbar:
                        'fullscreen | preview | undo | redo | fontsizeselect | bold italic  strikethrough  forecolor backcolor | alignleft aligncenter alignright alignjustify  | numlist bullist indentText indentRemove  | removeformat',
                    }"
                  />
                  <vueTinymce
                    v-if="false"
                    id="d3"
                    v-model="form.document_detail_templates[0].content_ru"
                  ></vueTinymce>
                </v-col>
              </v-row>
              <v-row
                v-for="(item, index) in form.document_detail_templates[0]
                  .document_detail_attributes"
                :key="index"
                style="border: 1px solid black"
                class="pa-1 mt-1"
              >
                <v-col cols="12" class="ma-0 pa-0">
                  <div>
                    <v-system-bar window color="#edf5ff">
                      <span class="font-weight-bold">{{
                        $t('document_detail_attributes.index')
                      }}</span>
                      <v-spacer></v-spacer>
                      <v-icon
                        color="error"
                        medium
                        @click="deleteDocumentDetailAttribute(item.id, index)"
                        >mdi-delete</v-icon
                      >
                    </v-system-bar>
                  </div>
                </v-col>
                <v-col cols="6" md="4" class="py-1 px-0">
                  <label>
                    {{
                      $t('document_detail_templates.attribute_name_uz_latin')
                    }}
                  </label>
                  <v-text-field
                    v-model="item.attribute_name_uz_latin"
                    :rules="[(v) => !!v || $t('input.required')]"
                    hide-details
                    dense
                    outlined
                  ></v-text-field>
                </v-col>
                <v-col cols="6" md="4" class="py-1 px-1">
                  <label>
                    {{
                      $t('document_detail_templates.attribute_name_uz_cyril')
                    }}
                  </label>
                  <v-text-field
                    v-model="item.attribute_name_uz_cyril"
                    :rules="[(v) => !!v || $t('input.required')]"
                    hide-details
                    dense
                    outlined
                    full-width
                  ></v-text-field>
                </v-col>
                <v-col cols="6" md="4" class="py-1 px-0">
                  <label>
                    {{ $t('document_detail_templates.attribute_name_ru') }}
                  </label>
                  <v-text-field
                    v-model="item.attribute_name_ru"
                    :rules="[(v) => !!v || $t('input.required')]"
                    hide-details
                    dense
                    outlined
                    full-width
                  ></v-text-field>
                </v-col>
                <v-col cols="6" md="4" class="py-1 px-0">
                  <label>{{
                    $t('document_detail_templates.data_type_id')
                  }}</label>
                  <v-autocomplete
                    clearable
                    v-model="item.data_type_id"
                    :items="dataTypes"
                    :rules="[(v) => !!v || $t('input.required')]"
                    hide-details
                    dense
                    outlined
                    full-width
                    @change="tableList(item.data_type_id, index)"
                  ></v-autocomplete>
                </v-col>
                <v-col cols="6" md="2" class="py-1 px-1">
                  <label>
                    {{ $t('document_detail_templates.value_max_length') }}
                  </label>
                  <v-text-field
                    v-model="item.value_max_length"
                    :rules="[(v) => !!v || $t('input.required')]"
                    type="number"
                    max="255"
                    min="0"
                    hide-details
                    dense
                    outlined
                    full-width
                  ></v-text-field>
                </v-col>
                <v-col cols="6" md="2" class="py-1 px-0">
                  <label>{{
                    $t('document_detail_templates.value_min_length')
                  }}</label>
                  <v-text-field
                    v-model="item.value_min_length"
                    :rules="[(v) => !!v || $t('input.required')]"
                    type="number"
                    max="255"
                    min="0"
                    hide-details
                    dense
                    outlined
                    full-width
                  ></v-text-field>
                </v-col>
                <v-col cols="6" md="4" class="py-1 pl-1 pr-0">
                  <label>{{
                    $t('document_detail_templates.description')
                  }}</label>
                  <v-text-field
                    v-model="item.description"
                    :rules="[(v) => !!v || $t('input.required')]"
                    hide-details
                    dense
                    outlined
                    full-width
                  ></v-text-field>
                </v-col>
                <v-col
                  cols="6"
                  md="6"
                  class="py-1 pl-1 pr-0"
                  v-if="showTableList[index]"
                >
                  <label>{{ $t('document_detail_templates.directory') }}</label>
                  <v-autocomplete
                    clearable
                    v-model="item.table_list_id"
                    :items="tableLists"
                    :rules="[(v) => !!v || $t('input.required')]"
                    hide-details
                    dense
                    outlined
                    full-width
                    item-value="id"
                  >
                    <template v-slot:selection="{ item }">
                      <v-chip color="white" class="pa-1 ma-0">
                        <span
                          v-text="item['description_' + $i18n.locale]"
                        ></span>
                      </v-chip>
                    </template>
                    <template v-slot:item="{ item }">
                      <v-list-item-content>
                        <v-list-item-title
                          v-text="item['description_' + $i18n.locale]"
                        ></v-list-item-title>
                      </v-list-item-content>
                    </template>
                  </v-autocomplete>
                </v-col>
                <v-col cols="12" class="py-0">
                  <v-row>
                    <v-col cols="6" md="4" lg="3" xl="2" class="py-1 pl-1 pr-0">
                      <v-checkbox
                        class="ma-0"
                        :label="$t('input_required')"
                        v-model="item.required"
                        aria-checked="true"
                        hide-details
                      ></v-checkbox>
                    </v-col>
                    <v-col cols="6" md="4" lg="3" xl="2" class="py-1 pl-1 pr-0">
                      <v-checkbox
                        class="ma-0"
                        :label="$t('input_unique')"
                        v-model="item.unique"
                        aria-checked="false"
                        hide-details
                      ></v-checkbox>
                    </v-col>
                    <v-col cols="6" md="4" lg="3" xl="2" class="py-1 pl-1 pr-0">
                      <v-checkbox
                        class="ma-0"
                        :label="
                          $t('document_detail_templates.is_list_vertical')
                        "
                        v-model="item.is_list_vertical"
                        aria-checked="true"
                        hide-details
                      ></v-checkbox>
                    </v-col>
                    <v-col cols="6" md="4" lg="3" xl="2" class="py-1 pl-1 pr-0">
                      <v-checkbox
                        class="ma-0"
                        :label="
                          $t('document_detail_templates.is_registry_show')
                        "
                        v-model="item.is_registry_show"
                        aria-checked="true"
                        hide-details
                      ></v-checkbox>
                    </v-col>
                    <v-col cols="6" md="4" lg="3" xl="2" class="py-1 pl-1 pr-0">
                      <v-checkbox
                        class="ma-0"
                        :label="$t('document_detail_templates.is_show')"
                        v-model="item.is_show"
                        aria-checked="true"
                        hide-details
                      ></v-checkbox>
                    </v-col>
                    <v-col cols="6" md="4" lg="3" xl="2" class="py-1 pl-1 pr-0">
                      <v-checkbox
                        class="ma-0"
                        :label="$t('document_detail_templates.is_summa')"
                        v-model="item.is_summa"
                        aria-checked="false"
                        hide-details
                      ></v-checkbox>
                    </v-col>
                    <v-col cols="6" md="4" lg="3" xl="2" class="py-1 pl-1 pr-0">
                      <v-text-field
                        dense
                        type="number"
                        :label="$t('actionTypes.sequence')"
                        v-model="item.sequence"
                        hide-details
                        :rules="[(v) => !!v || $t('input.required')]"
                        outlined
                      ></v-text-field>
                    </v-col>
                    <v-col cols="6" md="4" class="py-1 pl-1 pr-0">
                      <v-autocomplete
                        clearable
                        v-model="item.signer_staff_ids"
                        :items="form_signers.concat(signer_group_staffs)"
                        hide-details
                        dense
                        outlined
                        full-width
                        item-value="id"
                        multiple
                      >
                        <template v-slot:selection="{ item }">
                          <v-row
                            class="ma-0 pa-0"
                            style="
                              border-bottom: 1px solid #ccc;
                              font-size: 12px;
                            "
                          >
                            <v-col cols="12" class="ma-0 pa-0">
                              <b>{{
                                item.department
                                  ? item.department.code +
                                    ' ' +
                                    item.department.text
                                  : ''
                              }}</b>
                            </v-col>
                            <v-col cols="12" class="ma-0 pa-0">
                              <b>{{
                                item.position ? item.position.text : ''
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
                                item.department
                                  ? item.department.code +
                                    ' ' +
                                    item.department.text
                                  : ''
                              }}</b>
                            </v-col>
                            <v-col cols="12" class="ma-0 pa-0">
                              <b>{{
                                item.position ? item.position.text : ''
                              }}</b>
                            </v-col>
                          </v-row>
                        </template>
                      </v-autocomplete>
                    </v-col>
                  </v-row>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>
        </v-form>
      </v-tab-item>
      <v-tab-item>
        <document-blank-template
          :attributes="
            form.document_detail_templates[0].document_detail_attributes
          "
        ></document-blank-template>
      </v-tab-item>
    </v-tabs-items>

    <v-dialog v-model="loading" width="300" hide-overlay>
      <v-card color="primary" dark>
        <v-card-text>
          {{ $t('loadingText') }}
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
const axios = require('axios').default;
import Swal from 'sweetalert2';
import vueTinymce from '@/components/TinymceVue';
import DocumentBlankTemplate from './DocumentBlankTemplate.vue';
import Editor from '@tinymce/tinymce-vue';

export default {
  components: {
    vueTinymce,
    DocumentBlankTemplate,
    editor: Editor,
  },
  data() {
    return {
      tabs: [
        {
          label: 'google',
          key: 'google',
          closable: false,
        },
        {
          label: 'facebook',
          key: 'facebook',
        },
      ],
      tab: 'google',
      page: 1,
      from: 0,
      addDocumentSignerTemplateDialog: false,
      server_items_length: -1,
      dataTableOptions: { page: 1, itemsPerPage: 50 },
      dataTableValue: [],
      loading: false,
      search: '',
      search_staff: [],
      dialog: false,
      editMode: null,
      items: [],
      actionTypes: [],
      signTypes: [
        {
          text: 'e-imzo',
          value: 1,
        },
        {
          text: 'AD',
          value: 0,
        },
      ],
      registryTypes: [
        {
          text: 'Reisterli',
          value: 1,
        },
        {
          text: 'Reistersiz',
          value: 0,
        },
      ],
      staffs: [],
      regions: [],
      form: {
        id: Date.now(),
        has_employee: 0,
        add_signer: 0,
        add_signer_employee: 0,
        add_parent: 0,
        is_pdf_portrait: 0,
        table_font_size: 12,
        is_list_vertical: 0,
        is_from_to_department_show: 0,
        is_content_visible: 1,
        is_attribute_show: 1,
        is_document_relation: 0,
        document_signer_templates: [],
        signer_group_ids: [],
        document_detail_templates: [
          {
            id: Date.now(),
            document_detail_attributes: [],
            content_ru: '',
            content_uz_cyril: '',
            content_uz_latin: '',
          },
        ],
      },
      dialogHeaderText: '',
      createdAtMenu2: false,
      documentSignerTemplateForm: { action_type: '', staff: '' },
      departments: [],
      documentTypes: [],
      signerGroups: [],
      expanded: [],
      dataTypes: [],
      StaffForm: {},
      showTableList: [false],
      tableLists: [],
      form_signers: [],
      signer_group_staffs: [],
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
  },
  methods: {
    // getAttSigner() {
    //   this.isLoading = true;
    //   axios
    //     .post(this.$store.state.backend_url + "api/staffs/attribute", {
    //       staffs: this.form.document_signer_templates,
    //       locale: this.$i18n.locale,
    //     })
    //     .then((res) => {
    //       this.att_signers = res.data;
    //     })
    //     .catch((err) => {
    //       console.log(err);
    //     });
    // },
    deleteDocumentDetailAttribute(id, index) {
      this.form.document_detail_templates[0].document_detail_attributes =
        this.form.document_detail_templates[0].document_detail_attributes.filter(
          (v) => v.id != id
        );
      this.showTableList[index] = false;
      axios
        .delete(
          this.$store.state.backend_url +
            'api/document-templates/deleteDocumentDetailAttribute/' +
            id
        )
        .then((res) => {})
        .catch((err) => {
          console.log(err);
        });
    },
    deleteDocumentSignerTemplates(item) {
      this.form.document_signer_templates =
        this.form.document_signer_templates.filter((v) => v.id != item.id);
    },
    changeSignerGroup($event) {
      $event.forEach((event) => {
        let tmp = this.signerGroups.find((v) => v.id == event);
        if (!!tmp);
        this.form.document_signer_templates =
          this.form.document_signer_templates.concat(tmp.signer_group_details);
      });
    },
    getForm(id) {
      this.loading = true;
      axios
        .get(
          this.$store.state.backend_url +
            'api/document-templates/edit/' +
            id +
            '/' +
            this.$i18n.locale
        )
        .then((res) => {
          this.form = res.data;
          this.form.signer_group_ids = [];
          res.data.signer_groups.map((v) => {
            this.form.signer_group_ids.push(v.id);
          });
          this.search = this.form.department['name_' + this.$i18n.locale];
          let i = 0;
          this.form.document_detail_templates[0].document_detail_attributes.forEach(
            (element) => {
              if (element.table_list_id) {
                this.showTableList[i++] = true;
                axios
                  .get(this.$store.state.backend_url + 'api/directory/list')
                  .then((res) => {
                    this.tableLists = res.data;
                  })
                  .catch((err) => {
                    console.log(err);
                  });
              } else {
                this.showTableList[i++] = false;
              }
              if (element.signer_staff_ids.length) {
                let signer_staff_ids = [];
                element.signer_staff_ids.map((v) => {
                  signer_staff_ids.push(v.staff_id);
                });
                element.signer_staff_ids = signer_staff_ids;
              }
            }
          );
          this.form.document_signer_templates.map((v) => {
            if (v.staff_id) {
              v.staff.staffInfo = '';
              if (v.staff.department) {
                v.staff.staffInfo += v.staff.department.code;
                v.staff.staffInfo += ' ';
                v.staff.staffInfo += v.staff.department.text;
              }
              if (v.staff.range) v.staff.staffInfo += v.staff.range.code;
              v.staff.staffInfo += ' ';
              if (v.staff.position) v.staff.staffInfo += v.staff.position.text;
              this.form_signers.push(v.staff);
            }
          });
          this.staffs = this.form_signers.concat(this.staffs);
          this.getDepartment();
          this.getSignerGroupStaffs();
          this.loading = false;
        })
        .catch((err) => {
          console.log(err);
          this.loading = false;
        });
    },
    newAttribute(item) {
      this.StaffForm = {
        id: Date.now(),
        document_template_id: item.id,
        attribute_name_uz_latin: '',
        attribute_name_uz_cyril: '',
        attribute_name_ru: '',
        value_min_lenght: '',
        value_max_lenght: '',
        description: '',
        data_type_id: '',
      };
      this.StaffDialog = true;
      this.editMode = false;
      if (this.$refs.staffDialogform) this.$refs.staffDialogform.reset();
    },
    getRef() {
      this.loading = true;
      axios
        .post(
          this.$store.state.backend_url + 'api/document-templates/get-ref',
          {
            language: this.$i18n.locale,
          }
        )
        .then((response) => {
          this.actionTypes = response.data.actionTypes;
          let staffs = response.data.staffs.data.map((v) => {
            v.staffInfo = '';
            if (v.department) {
              v.staffInfo += v.department.code;
              v.staffInfo += ' ';
              v.staffInfo += v.department.text;
            }
            if (v.range) v.staffInfo += v.range.code;
            v.staffInfo += ' ';
            if (v.position) v.staffInfo += v.position.text;
            return v;
          });
          this.staffs = this.form_signers.concat(staffs);
          this.signerGroups = response.data.signerGroups.filter(
            (v) => v.signer_group_details.length > 0
          );
          this.documentTypes = response.data.documentTypes;

          this.dataTypes = response.data.dataTypes;
          // this.getAttSigner();
          this.getSignerGroupStaffs();
          this.loading = false;
        })
        .catch((error) => {
          console.error(error);
          this.loading = false;
        });
    },
    getDepartment() {
      this.isLoading = true;
      axios
        .post(this.$store.state.backend_url + 'api/departments/select', {
          search: this.search,
          locale: this.$i18n.locale,
        })
        .then((res) => {
          this.departments = res.data.data.map((v) => {
            v.text = v['name_' + this.$i18n.locale];
            return v;
          });
          // console.log(this.departments);
        })
        .catch((err) => {
          console.error(err);
        });
    },
    getStaff(item) {
      this.isLoading = true;
      axios
        .post(this.$store.state.backend_url + 'api/get-staffs', {
          search: this.search_staff[item],
          language: this.$i18n.locale,
        })
        .then((res) => {
          let staffs = res.data.data.map((v) => {
            v.staffInfo = '';
            v.department = {};
            v.position = {};
            v.staffInfo += v.department_code;
            v.department.code = v.department_code;
            v.staffInfo += ' ';
            v.staffInfo += v['department_name_' + this.$i18n.locale];
            v.department.text = v['department_name_' + this.$i18n.locale];
            v.staffInfo += ' ';
            v.staffInfo += v['position_name_' + this.$i18n.locale];
            v.position.text = v['position_name_' + this.$i18n.locale];
            return v;
          });
          this.staffs = this.form_signers.concat(staffs);
          this.isLoading = false;
        })
        .catch((err) => {
          this.isLoading = false;
          console.error(err);
        });
    },
    getSignerGroupStaffs() {
      let staffs = [];
      this.form.signer_group_ids.map((signer_group_id) => {
        this.signerGroups.map((v) => {
          if (v.id == signer_group_id) {
            v.signer_group_details.map((signer_group_detail) => {
              staffs.push(signer_group_detail.staff);
            });
          }
        });
        this.signer_group_staffs = staffs.map((v) => {
          v.staffInfo = '';
          if (v.department) {
            v.staffInfo += v.department.code;
            v.staffInfo += ' ';
            v.staffInfo += v.department.text;
          }
          if (v.range) v.staffInfo += v.range.code;
          v.staffInfo += ' ';
          if (v.position) v.staffInfo += v.position.text;
          return v;
        });
      });
      // console.log(this.form.signer_group_ids);
    },
    getFormSigners() {
      let staffs = [];
      this.form.document_signer_templates.map((v) => {
        if (v.staff_id) {
          staffs.push(
            this.staffs.find((va) => {
              if (va && va.id == v.staff_id) return va;
            })
          );
        }
      });
      this.form_signers = staffs;
    },
    newItem() {
      this.dialogHeaderText = this.$t('document-templates.newDistrict');
      this.form = {
        id: Date.now(),
        department_id: '',
        document_type_id: '',
        signer_group_ids: [],
        add_signer: null,
        add_signer_employee: null,
        name_uz_latin: '',
        name_uz_cyril: '',
        name_ru: '',
        decription_uz_latin: '',
        decription_uz_cyril: '',
        decription_ru: '',
      };
      this.dialog = true;
      this.editMode = false;
      if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
    },
    editItem(item) {
      this.dialogHeaderText = this.$t('document-templates.newDistrict');
      this.formIndex = this.items.indexOf(item);
      this.form = Object.assign({}, item);
      this.dialog = true;
      this.editMode = true;
      if (this.$refs.dialogForm) this.$refs.dialogForm.resetValidation();
    },
    save() {
      if (this.$refs.dialogForm.validate()) {
        this.loading = true;
        axios
          .post(
            this.$store.state.backend_url + 'api/document-templates/update',
            this.form
          )
          .then((res) => {
            this.dialog = false;
            const Toast = Swal.mixin({
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true,
              onOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
              },
            });

            Toast.fire({
              icon: 'success',
              title: this.$t('create_update_operation'),
            });
            this.loading = false;
            if(res.data.status == 200){
              this.$router.push('/document-templates/list');
            }
          })
          .catch((err) => {
            console.error(err);
            this.loading = false;
          });
      }
    }, //document-types
    deleteItem(item) {
      const index = this.items.indexOf(item);
      Swal.fire({
        title: this.$t('swal_title'),
        text: this.$t('swal_text'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: this.$t('swal_delete'),
      }).then((result) => {
        if (result.value) {
          axios
            .delete(
              this.$store.state.backend_url +
                'api/document-templates/delete/' +
                item.id
            )
            .then((res) => {
              this.getList(this.page, this.itemsPerPage);
              this.dialog = false;
              Swal.fire('Deleted!', this.$t('swal_deleted'), 'success');
            })
            .catch((err) => {
              Swal.fire({
                icon: 'error',
                title: this.$t('swal_error_title'),
                text: this.$t('swal_error_text'),
                //footer: "<a href>Why do I have this issue?</a>"
              });
              console.log(err);
            });
        }
      });
    },
    tableList(id, i) {
      if (id == 6) {
        this.showTableList[i] = true;
        axios
          .get(this.$store.state.backend_url + 'api/directory/list')
          .then((res) => {
            this.tableLists = res.data;
          })
          .catch((err) => {
            console.log(err);
          });
      } else {
        this.showTableList[i] = false;
      }
    },
  },
  mounted() {
    if (this.$route.params.id) this.getForm(this.$route.params.id);
    this.getRef();

    // this.getDepartment();
  },
};
</script>
