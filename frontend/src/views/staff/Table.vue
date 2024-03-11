<template>
  <div class="fullHeight">
    <v-card 
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{ $t("position_document") }}</span>
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
                  v-if="$store.getters.checkPermission('shtat-qoshish')"
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
                        getStaffExcel(1);
                        staff_excel = [];
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
        <v-col class="ma-0 pa-0" xs="12">
          <v-data-table
            class="doc-template_data-table"
            dense
            style="width: 100%; height: 100%; border-radius: 10px"
            fixed-header
            :loading-text="$t('loadingText')"
            :no-data-text="$t('noDataText')"
            :height="screenHeight"
            :loading="loading"
            :headers="headers"
            :items="staffData"
            :server-items-length="server_items_length"
            :options.sync="dataTableOptions"
            @update:page="updatePage"
            @update:items-per-page="updatePerPage"
            :disable-pagination="true"
            disable-sort
            :footer-props="{
              itemsPerPageOptions: [50, 100, 200],
              itemsPerPageAllText: $t('itemsPerPageAllText'),
              itemsPerPageText: $t('itemsPerPageText'),
              showFirstLastPage: true,
              firstIcon: 'mdi-arrow-collapse-left',
              lastIcon: 'mdi-arrow-collapse-right',
              prevIcon: 'mdi-arrow-left',
              nextIcon: 'mdi-arrow-right',
            }"
          >
            <template v-slot:body.prepend="{ item }">
              <tr class="prepend_height">
                <td></td>
                <td>
                  <v-text-field
                    v-model="filter.department_code"
                    hide-details
                    densepln0086
                    @keyup.enter="getList"
                  ></v-text-field>
                </td>
                <td>
                  <v-text-field
                    v-model="filter.department_name"
                    hide-details
                    dense
                    @keyup.enter="getList"
                  ></v-text-field>
                </td>
                <td>
                  <v-autocomplete
                    clearable
                    v-model="filter.position_id"
                    :items="
                      positions.filter((v)=> v.staff_count != 0)
                      .map((v) => ({
                        text: v.code,
                        value: v.id,
                      }))
                    "
                    
                    :rules="[(v) => !!v || $t('input.required')]"
                    hide-details
                    dense
                    @change="getList"
                  ></v-autocomplete>
                </td>
                <td>
                  <v-autocomplete
                    clearable
                    v-model="filter.position_id"
                    :items="
                      positions.filter((v)=> v.staff_count != 0)
                      .map((v) => ({
                        text: v.code + ' ' + v['name_' + $i18n.locale],
                        value: v.id,
                      }))
                    "                    
                    :rules="[(v) => !!v || $t('input.required')]"
                    hide-details
                    dense
                    @change="getList"
                  ></v-autocomplete>
                </td>
              </tr>
            </template>
            <template v-slot:item="{ item, index }">
              <tr>
                <td>{{ index + from }}</td>
                <td style>
                  {{ item.department ? item.department.department_code : "" }}
                </td>
                <td
                  style="max-width: 300px"
                  :title="
                    item.department ? item.department['name_' + $i18n.locale] : ''
                  "
                >
                  {{
                    item.department ? item.department["name_" + $i18n.locale] : ""
                  }}
                </td>
                <td  :title=" item.position ? item.position['name_' + $i18n.locale] : ''"
                >
                  {{ item.position ? item.position.code : "" }}
                </td>
                <td
                  style="max-width: 300px"
                  :title="
                    item.position ? item.position['name_' + $i18n.locale] : ''
                  "
                >
                  {{ item.position ?  item.position["name_" + $i18n.locale] : "" }}
                  <v-btn
                  style="float: right;"
                  v-if="item.department.documents.legth || item.document_staffs.length"
                  color="#7B68EE"
                  small
                  icon
                  @click="relationdocument(item)"
                >
                  <v-icon>
                    {{ "mdi-book-open-page-variant-outline"}}
                  </v-icon>
                </v-btn>
                </td>
              </tr>
            </template>
            <template v-slot:expanded-item="{ headers, item }">
              <td :colspan="headers.length" class="ma-0 pa-0">
                <v-card class="ma-1 pa-1">
                  <table style="border: 1px solid #aaa">
                    <tr>
                      <td style="word-wrap: break-word; white-space: normal">
                        {{ $t("staff.personal_type_id") }}
                      </td>
                      <td style="word-wrap: break-word; white-space: normal">
                        <span>
                          {{
                            item.personal_type
                              ? item.personal_type["name_" + $i18n.locale]
                              : ""
                          }}
                        </span>
                      </td>
                    </tr>
                    <tr>
                      <td style="word-wrap: break-word; white-space: normal">
                        {{ $t("staff.expence_type_id") }}
                      </td>
                      <td style="word-wrap: break-word; white-space: normal">
                        <span>
                          {{
                            item.expence_type
                              ? item.expence_type["name_" + $i18n.locale]
                              : ""
                          }}
                        </span>
                      </td>
                    </tr>
                    <tr>
                      <td style="word-wrap: break-word; white-space: normal">
                        {{ $t("staff.order_date") }}
                      </td>
                      <td style="word-wrap: break-word; white-space: normal">
                        <span>{{ item.order_date }}</span>
                      </td>
                    </tr>
                    <tr>
                      <td style="word-wrap: break-word; white-space: normal">
                        {{ $t("staff.order_number") }}
                      </td>
                      <td style="word-wrap: break-word; white-space: normal">
                        <span>{{ item.order_number }}</span>
                      </td>
                    </tr>
                    <tr>
                      <td style="word-wrap: break-word; white-space: normal">
                        {{ $t("staff.begin_date") }}
                      </td>
                      <td style="word-wrap: break-word; white-space: normal">
                        <span>{{ item.begin_date }}</span>
                      </td>
                    </tr>
                    <tr>
                      <td style="word-wrap: break-word; white-space: normal">
                        {{ $t("staff.end_date") }}
                      </td>
                      <td style="word-wrap: break-word; white-space: normal">
                        <span>{{ item.end_date }}</span>
                      </td>
                    </tr>
                  </table>
                </v-card>
              </td>
            </template>
          </v-data-table>
        </v-col>
      </v-row>
    </v-card>
    <v-dialog
      v-model="dialog"
      @keydown.esc="dialog = false"
      persistent
      max-width="800px"
    >
      <v-card class="pa-5">
        <v-card-title class="pa-0" primary-title>
          <span class="dialogTitle">{{ dialogHeaderText }}</span>
        </v-card-title>
        <v-divider color="#DCE5EF" class="my-1"></v-divider>
        <v-card-text>
          <v-form @keyup.native.enter="save" ref="dialogForm">
            <v-row class="mx-0 dialogForm">
              <v-col cols="12" class="pa-0 mb-3">
                <label class="labelTitle" for>{{ $t("staff.department_id") }}</label>
                <v-autocomplete
                  clearable
                  v-model="form.department_id"
                  :items="departmentList"
                  :item-text="'name_' + $i18n.locale"
                  item-value="id"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
              <v-col cols="12" class="pa-0 mb-3">
                <label class="labelTitle" for>{{ $t("staff.position_id") }}</label>
                <v-autocomplete
                  clearable
                  v-model="form.position_id"
                  :items="
                    positions.map((v) => ({
                      text: v['name_' + $i18n.locale],
                      value: v.id,
                    }))
                  "
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
              <v-col cols="4" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("staff.range_id") }}</label>
                <v-autocomplete
                  clearable
                  v-model="form.range_id"
                  :items="ranges.map((v) => ({ text: v.code, value: v.id }))"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
              <v-col cols="4" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("staff.rate_count_bp") }}</label>
                <v-text-field
                  v-model="form.rate_count_bp"
                  type="number"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="4" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("staff.rate_count") }}</label>
                <v-text-field
                  v-model="form.rate_count"
                  type="number"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("staff.personal_type_id") }}</label>
                <v-autocomplete
                  clearable
                  v-model="form.personal_type_id"
                  :items="
                    personalTypes.map((v) => ({
                      text: v['name_' + $i18n.locale],
                      value: v.id,
                    }))
                  "
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
              <v-col cols="6" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("staff.expence_type_id") }}</label>
                <v-autocomplete
                  clearable
                  v-model="form.expence_type_id"
                  :items="
                    expenceTypes.map((v) => ({
                      text: v['name_' + $i18n.locale],
                      value: v.id,
                    }))
                  "
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
              <v-col cols="3" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("staff.order_number") }}</label>
                <v-text-field
                  v-model="form.order_number"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="3" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("staff.order_date") }}</label>
                <v-text-field
                  v-model="form.order_date"
                  placeholder="YYYY-MM-DD"
                  :rules="dateRules"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="3" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("staff.begin_date") }}</label>
                <v-text-field
                  v-model="form.begin_date"
                  placeholder="YYYY-MM-DD"
                  :rules="dateRules"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="3" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("staff.end_date") }}</label>
                <v-text-field
                  v-model="form.end_date"
                  placeholder="YYYY-MM-DD"
                  :rules="dateRules"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="4" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("staff.branch_id") }}</label>
                <v-autocomplete
                  clearable
                  v-model="form.branch_id"
                  :items="branches"
                  item-value="id"
                  item-text="name"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
              <v-col cols="4" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("staff.coefficient") }}</label>
                <v-autocomplete
                  clearable
                  v-model="form.coefficient_id"
                  :items="coefficients"
                  item-value="id"
                  item-text="description"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
              <v-col cols="4" class="px-1 py-0 mb-3">
                <label class="labelTitle" for>{{ $t("staff.shift") }}</label>
                <v-text-field
                  v-model="form.shift"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions class="pa-0 mt-5">
          <v-spacer></v-spacer>
          <v-btn
            class="mr-3"
            color="#3FCB5D"
            right
            small
            dark
            @click="save"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("save") }}
          </v-btn>
          <v-btn
            class=""
            color="red"
            right
            small
            dark
            @click="dialog = false"
            elevation="0"
            style="text-transform: none; border-radius: 5px; padding: 5px 20px"
          >
            {{ $t("Отменить") }}
          </v-btn>
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

    <v-dialog v-model="relationdocumentdialog" persistent max-width="800px">
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">{{ 'Xujjatlar' }}</span>
          <v-spacer></v-spacer>   
          <v-btn
            color="red"
            outlined
            x-small
            fab
            class
            @click="relationdocumentdialog = false"
          >
          <v-icon>mdi-close</v-icon>
        </v-btn>   
        </v-card-title>
        <v-card-text>
          <v-row>
            <v-col cols="6">
              <span><b>{{ $t('lavozim_yuriqnomasi') +' yoki '+$t('kasbiy_yuriqnomasi') }}</b></span>               
              <!-- <span><b>{{ $t('lavozim_yuriqnomasi') }}</b></span>                -->
              <v-list-item-content v-for="(i, ind) in relation_document.document_staffs"  :key="ind">
                <v-list-item-title>
                  <b>{{ 'ID' }} : </b>
                  {{ i.document ? i.document.id : '' }}
                </v-list-item-title>            
                <v-list-item-subtitle>
                  <b>{{ 'Xujjat raqami' }} : </b>
                    <v-btn
                      outlined
                      small                        
                      rounded
                      :to="'/document/' + i.document.pdf_file_name"
                      target="_blank"
                      >
                      {{ i.document.document_number}}
                    </v-btn>       
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-col>
            <v-col v-if="relation_document.department" cols="6">
              <span><b>{{ $t('tarkibiy_tuzilma')}}</b></span>               
              <v-list-item-content v-for="(i, ind) in relation_document.department.documents"  :key="ind">
                <v-list-item-title  >
                  <b>{{ 'ID' }} : </b>
                  {{ i ? i.id : '' }}
                </v-list-item-title>                
                <v-list-item-subtitle>
                  <b>{{ 'Xujjat raqami' }} : </b>
                    <v-btn
                      outlined
                      small                        
                      rounded
                      :to="'/document/' + i.pdf_file_name"
                      target="_blank"
                      >
                      {{ i.document_number}}
                    </v-btn>       
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-col>
          </v-row>
        </v-card-text>
        <v-divider></v-divider>
      </v-card>
    </v-dialog>
    <v-dialog v-model="requirementDialog" persistent max-width="800px">
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">{{ $t("requirement.index") }}</span>
          <v-spacer></v-spacer>
          <v-btn
            color="red"
            outlined
            x-small
            fab
            class
            @click="requirementDialog = false"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-list-item-content>
            <v-list-item-title>
              <b>{{ $t("department.index") }}:</b>
              {{ staffTmp.department_code + " " + staffTmp.department_name }}
            </v-list-item-title>
            <v-list-item-subtitle>
              <b>{{ $t("position.index") }}:</b>
              {{ staffTmp.position_name }}
            </v-list-item-subtitle>
          </v-list-item-content>

          <v-form @keyup.native.enter="save" ref="dialogForm">
            <v-row>
              <v-col
                cols="10"
                style="min-width: 100px; max-width: 100%"
                class="flex-grow-1 flex-shrink-0"
              >
                <label for>{{ $t("requirement.index") }}</label>
                <v-autocomplete
                  clearable
                  v-model="selectRequirement"
                  :items="requirementList"
                  item-value="id"
                  item-text="name_ru"
                  hide-details
                  dense
                  outlined
                >
                  <template v-slot:selection="item">
                    <v-list-item-content style="min-width: 550px">
                      <v-list-item-title>
                        {{ item.item["name_" + $i18n.locale] }}
                      </v-list-item-title>
                      <v-list-item-subtitle>
                        <b>{{ $t("requirement.type") }}</b>
                        :{{
                          item.item.requirement_type["name_" + $i18n.locale]
                        }}
                      </v-list-item-subtitle>
                    </v-list-item-content>
                  </template>
                  <template v-slot:item="item">
                    <v-list-item-content>
                      <v-list-item-title>
                        {{ item.item["name_" + $i18n.locale] }}
                      </v-list-item-title>
                      <v-list-item-subtitle>
                        <b>{{ $t("requirement.type") }}</b>
                        :{{
                          item.item.requirement_type["name_" + $i18n.locale]
                        }}
                      </v-list-item-subtitle>
                    </v-list-item-content>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col cols="1" style="min-width: 100px" class="px-0">
                <v-btn
                  :disabled="!selectRequirement"
                  class="mt-6"
                  color="success"
                  @click="addRequirement"
                  >+</v-btn
                >
              </v-col>
            </v-row>
            <v-simple-table>
              <template v-slot:default>
                <thead>
                  <tr>
                    <th width="20">#</th>
                    <th class="text-left">{{ $t("requirement.index") }}</th>
                    <th class="text-left">{{ $t("requirementType.index") }}</th>
                    <th width="20"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(item, index) in staffTmp.requirements"
                    :key="index"
                  >
                    <td>{{ index + 1 }}</td>
                    <td>{{ item["name_" + $i18n.locale] }}</td>
                    <td>
                      {{
                        item.requirement_type
                          ? item.requirement_type["name_" + $i18n.locale]
                          : ""
                      }}
                    </td>
                    <td>
                      <v-icon color="error" @click="removeRequirement(item)"
                        >mdi-minus-circle-outline</v-icon
                      >
                    </td>
                    <td>{{ 'sas' }}</td>
                  </tr>
                  <!-- <tr v-for="(i, ind) in staffTmp.document_staffs"  :key="ind">
                    <td v-if="i.document"> {{'ID: '+ item.document.id}}</td>
                    <td v-if="i.document"> 
                      <v-btn
                        outlined
                        small                        
                        rounded
                        :to="'/document/' + i.document.pdf_file_name"
                        target="_blank"
                        >
                        {{'Xujjat raqami: '+ i.document.document_number}}
                    </v-btn>                     
                    </td>
                  </tr> -->
                </tbody>
              </template>
            </v-simple-table>
          </v-form>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="saveRequirements">
            {{ $t("save") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="fileDialog" persistent max-width="800px">
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">{{ $t("staff.files") }}</span>
          <v-spacer></v-spacer>
          <v-btn
            color="red"
            outlined
            x-small
            fab
            class
            @click="fileDialog = false"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-list-item-content>
            <v-list-item-title>
              <b>{{ $t("department.index") }}:</b>
              {{ staffTmp.department_code + " " + staffTmp.department_name }}
            </v-list-item-title>
            <v-list-item-subtitle>
              <b>{{ $t("position.index") }}:</b>
              {{ staffTmp.position_name }}
            </v-list-item-subtitle>
          </v-list-item-content>

          <v-form @keyup.native.enter="save" ref="dialogForm">
            <v-row>
              <v-col
                cols="12"
                style="min-width: 100px; max-width: 100%"
                class="flex-grow-1 flex-shrink-0"
              >
                <label for>{{ $t("staff.uploadFiles") }}</label>
                <v-file-input
                  v-model="selectFiles"
                  multiple
                  outlined
                  dense
                  prepend-icon
                  append-icon="mdi-file-pdf-box-outline"
                  accept=".pdf"
                  small-chips
                  show-size
                  hide-details
                ></v-file-input>
              </v-col>
              <v-col
                cols="10"
                style="min-width: 100px; max-width: 100%"
                class="flex-grow-1 flex-shrink-0"
              >
                <label for>{{ $t("staff.fileType") }}</label>
                <v-autocomplete
                  clearable
                  v-model="selectObjectType"
                  :items="objectTypesList"
                  item-value="id"
                  :item-text="['name_' + $i18n.locale]"
                  hide-details
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
              <v-col cols="1" style="min-width: 100px" class="px-0">
                <v-btn
                  :disabled="selectFiles.length == 0 || !selectObjectType"
                  class="mt-6"
                  color="success"
                  @click="addFiles"
                  >+</v-btn
                >
              </v-col>
            </v-row>
            <v-simple-table>
              <template v-slot:default>
                <thead>
                  <tr>
                    <th width="20" class="text-left">#</th>
                    <th class="text-left">{{ $t("staff.file") }}</th>
                    <th class="text-left">{{ $t("staff.objectType") }}</th>
                    <th width="20" class="text-left"></th>
                    <th width="20" class="text-left"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in getFormDataValues" :key="index">
                    <td>{{ index + 1 }}</td>
                    <td style="max-width: 340px">{{ item.file_name }}</td>
                    <td>
                      {{
                        objectTypesList.find((v) => v.id == item.object_type_id)
                          ? objectTypesList.find(
                              (v) => v.id == item.object_type_id
                            )["name_" + $i18n.locale]
                          : ""
                      }}
                    </td>
                    <td>
                      <v-btn color="primary" text @click="viewPdfFile(item)">
                        <v-icon>mdi-download</v-icon>
                      </v-btn>
                    </td>
                    <td>
                      <v-icon color="error" @click="removeTmpFile(item.id)"
                        >mdi-minus-circle-outline</v-icon
                      >
                    </td>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>
          </v-form>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog v-model="pdfViewDialog" width="800">
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">{{ fileForView.file_name }}</span>
          <v-spacer></v-spacer>
          <v-btn
            color="red"
            outlined
            x-small
            fab
            class
            @click="pdfViewDialog = false"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>

        <v-card-text class>
          <pdf
            v-if="fileForView.id > 0"
            :src="
              $store.state.backend_url + 'staffs/get-file/' + fileForView.id
            "
          ></pdf>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="green"
            text
            :href="
              $store.state.backend_url + 'staffs/get-file/' + fileForView.id
            "
            >{{ $t("download") }}</v-btn
          >
          <v-btn
            color="primary"
            text
            @click="
              pdfViewDialog = false;
              fileForView.id = 0;
            "
            >{{ $t("close") }}</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!--dsfsdfsdf -->

    <v-dialog v-model="showItemDialog" persistent max-width="800">
      <v-card>
        <v-card-title class="headline">
          {{
            $i18n.locale == "ru"
              ? (this.dialogHeaderText = "Штатное расписание")
              : $i18n.locale == "uz_latin"
              ? (this.dialogHeaderText = "Shtatlar jadvali")
              : (this.dialogHeaderText = "Штатлар жадвали")
          }}
          <v-spacer></v-spacer>
          <v-btn
            color="red"
            outlined
            x-small
            fab
            class
            @click="showItemDialog = false"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <p v-for="(employee, index) in employeess" :key="index">
            {{ employee["name_" + $i18n.locale] }}
          </p>
          <v-simple-table>
            <template v-slot:default>
              <thead>
                <tr>
                  <th class="text-left">№</th>
                  <th class="text-left">{{ $t("employee.index") }}</th>
                  <th class="text-left">{{ $t("employee.tabel") }}</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(employee, index) in employeess.filter(
                    (v) => v.employee
                  )"
                  :key="index"
                >
                  <td>{{ index + 1 }}</td>
                  <td>
                    {{
                      $i18n.locale == "ru"
                        ? employee.employee.firstname_uz_cyril +
                          " " +
                          employee.employee.lastname_uz_cyril +
                          " " +
                          employee.employee.middlename_uz_cyril
                        : employee.employee["firstname_" + $i18n.locale] +
                          " " +
                          employee.employee["lastname_" + $i18n.locale] +
                          " " +
                          employee.employee["middlename_" + $i18n.locale]
                    }}
                  </td>
                  <td>{{ employee.employee.tabel }}</td>
                </tr>
              </tbody>
            </template>
          </v-simple-table>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog v-model="downloadExcel" hide-overlay persistent width="300">
      <v-card>
        <v-card-text class="py-1 px-3">
          <v-btn
            color="success"
            class="mx-10"
            @click="downloadExcel = false"
            text
          >
            <download-excel
              :data="staff_excel"
              :name="'staff_ruyxati_' + new Date().getTime() + '.xls'"
            >
              <span style="color: #4caf50">{{ $t("download") }}</span>
              <v-icon color="success" height="20">mdi-download</v-icon>
            </download-excel>
          </v-btn>
          <v-btn class color="error" @click="downloadExcel = false" icon>
            <v-icon color="error" height="20">mdi-close</v-icon>
          </v-btn>
        </v-card-text>
      </v-card>
    </v-dialog>
    <!--dsfsdfsdf -->

    <!-- Dialog Staff Critical -->
    <v-dialog
      v-model="dialogStaffCritical"
      @keydown.esc="dialogStaffCritical = false"
      persistent
      max-width="800px"
    >
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">{{ $t("critical.index") }}</span>
          <v-spacer></v-spacer>

          <v-btn
            color="red"
            outlined
            x-small
            fab
            class
            @click="[(dialogStaffCritical = false), (criticalForm = {})]"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form @keyup.native.enter="save" ref="dialogForm">
            <v-row>
              <v-col cols="4">
                <label for>{{ $t("department.department_code") }}</label>
                <v-text-field
                  v-model="criticalDepartment.department_code"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="4">
                <label for>{{ $t("department.department_name") }}</label>
                <v-text-field
                  v-model="criticalDepartment['name_' + $i18n.locale]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="4">
                <label for>{{ $t("user.position") }}</label>
                <v-text-field
                  v-model="criticalPosition['name_' + $i18n.locale]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="4">
                <label for>{{ $t("user.employee") }}</label>
                <v-autocomplete
                  clearable
                  v-model="criticalForm.employee_id"
                  :items="
                    criticalEmployees.map((v) => ({
                      text:
                        v.employee['lastname_' + $i18n.locale] +
                        ' ' +
                        v.employee['firstname_' + $i18n.locale],
                      value: v.employee.id,
                    }))
                  "
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
              <v-col cols="4">
                <label for>{{ $t("staff.begin_date") }}</label>
                <v-text-field
                  v-model="criticalForm.begin_date"
                  placeholder="YYYY-MM-DD"
                  :rules="dateRules"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="4" v-if="criticalForm.isNew == false">
                <label for>{{ $t("staff.end_date") }}</label>
                <v-text-field
                  v-model="criticalForm.end_date"
                  placeholder="YYYY-MM-DD"
                  :rules="dateRules"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="4">
                <label for>{{ $t("employee.description") }}</label>
                <v-text-field
                  v-model="criticalForm.description"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="saveCritical">{{ $t("save") }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
const axios = require("axios").default;
import pdf from "vue-pdf";
import Swal from "sweetalert2";
export default {
  components: {
    pdf,
  },
  data: () => ({
    dateRules: [
      (v) =>
        !v ||
        /^\d{4}[\-\-](0?[1-9]|1[012])[\-\-](0?[1-9]|[12][0-9]|3[01])$/.test(
          v
        ) ||
        "Date must be valid(YYYY-DD-MM)",
    ],
    filter: {
      department_code: "",
      department_name: "",
      position_id: "",
      range_id: "",
      expence_type_id: "",
      personal_type_id: "",
      status: "1",
    },
    isActives: [],
    selectObjectType: "",
    objectTypesList: [],
    fileDialog: false,
    pdfViewDialog: false,
    showItemDialog: false,
    fileForView: { id: 0 },
    selectFiles: [],
    loading: false,
    requirementDialog: false,
    staff_id: null,
    page: 1,
    from: 0,
    server_items_length: -1,
    dataTableOptions: { page: 1, itemsPerPage: 50 },
    dataTableValue: [],
    relationdocumentdialog: false,
    relation_document: "",
    createdAtMenu1: false,
    createdAtMenu2: false,
    createdAtMenu3: false,
    search: "",
    selectRequirement: "",
    dialog: false,
    dialogStaffCritical: false,
    editMode: null,
    items: [],
    departments: [],
    coefficients: [],
    positions: [],
    requirements: [],
    ranges: [],
    personalTypes: [],
    expenceTypes: [],
    form: {},
    staffData: [],
    branches: [],
    staffTmp: { requirements: [] },
    dialogHeaderText: "",
    formData: null,
    employeess: [],
    staff_excel: [],
    criticalDepartment: [],
    criticalPosition: [],
    criticalEmployees: [],
    criticalForm: {
      id: Date.now(),
      staff_id: null,
    },
    downloadExcel: false,
  }),
  computed: {
    screenHeight() {
      return window.innerHeight - 180;
    },
    headers() {
      return [
        { text: "#", value: "id", align: "center", width: 30 },
        {
          text: this.$t("department.department_code"),
          value: "department_code",
          width: 30,
          class: "max-width",
        },
        {
          text: this.$t("staff_department"),
          value: "department_name",
        },
        {
          text: this.$t("position_code"),
          value: "position_code",
        },
        {
          text: this.$t("employee.position"),
          value: "position_name",
        },
        // { text: this.$t("staff.range_code"), value: "code", width: "10" },
        // {
        //   text: this.$t("staff.expence_type"),
        //   value: "expence_type",
        //   class: "max-width",
        // },
        // {
        //   text: this.$t("staff.personal_type"),
        //   value: "personal_type",
        //   class: "max-width",
        // },
        // {
        //   text: this.$t("staff.rate_count_bp"),
        //   value: "rate_count",
        //   // class: "max-width",
        //   width: 20,
        // },
        // {
        //   text: this.$t("staff.rate_count"),
        //   value: "rate_count",
        //   // class: "max-width",
        //   width: 20,
        // },
        // {
        //   text: this.$t("staff.employees_count"),
        //   value: "employees_count",
        //   // class: "max-width",
        //   width: 20,
        // },
        // {
        //   text: this.$t("employee.status"),
        //   value: "is_active",
        //   align: "center",
        //   width: 50,
        // },
        // {
        //   text: this.$t("actions"),
        //   value: "actions",
        //   width: 80,
        //   align: "center",
        // },
      ].filter(
        (v) =>
          v.value != "actions" ||
          this.$store.getters.checkPermission("staff-update") ||
          this.$store.getters.checkPermission("staff-delete")
      );
    },
    getFormDataValues() {
      return this.staffTmp.files;
    },
    requirementList() {
      if (this.staffTmp && this.staffTmp.requirements)
        return this.requirements.filter((v) => {
          return !this.staffTmp.requirements.find((r) => r.id == v.id);
        });
      return this.requirementList;
    },
    departmentList() {
      return this.departments.map((value) => {
        let v = value;
        v.name_uz_latin = v.department_code
          ? v.department_code + " " + v.name_uz_latin
          : "";

        v.name_uz_cyril = v.department_code
          ? v.department_code + " " + v.name_uz_cyril
          : "";

        v.name_ru = v.department_code
          ? v.department_code + " " + v.name_ru
          : "";
        return v;
      });
    },
  },
  methods: {
    updatePage($event) {
      this.getList();
    },
    updatePerPage($event) {
      this.getList();
    },
    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/staffs", {
          pagination: this.dataTableOptions,
          filter: this.filter,
          language: this.$i18n.locale,
        })
        .then((response) => {
          this.staffData = response.data.data;
          this.from = response.data.from;
          this.server_items_length = response.data.total;
          this.loading = false;
          this.getRef();
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getStaffExcel(page) {
      let new_array = [];
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/staffs", {
          language: this.$i18n.locale,
          pagination: {
            page: page,
            itemsPerPage: 1000,
          },
          filter: this.filter,
        })
        .then((response) => {
          response.data.data.map((element, index) => {
            let language =
              this.$i18n.locale == "ru" ? "uz_cyril" : this.$i18n.locale;
            let employee = "";
            let tabel = "";
            let enter_order_number = "";
            element.employee_staff.forEach((v) => {
              employee +=
                v.employee["firstname_" + language] +
                " " +
                v.employee["lastname_" + language] +
                " " +
                v.employee["middlename_" + language] +
                "<br>";
              tabel += v.employee.tabel + "<br>";
              enter_order_number += v.enter_order_number + "<br>";
            });
            let count = element.employee_staff.length;
            new_array.push({
              "#": page * 1000 - 999,
              "Код подразделения": element.department
                ? element.department.department_code
                : "",
              Подразделения: element.department
                ? element.department["name_" + this.$i18n.locale]
                : "",
              Должность: element.position
                ? element.position["name_" + this.$i18n.locale]
                : "",
              Разряд: element.range ? element.range.code : "",
              "Тип расход": element.expence_type
                ? element.expence_type["name_" + this.$i18n.locale]
                : "",
              "Тип персонала": element.personal_type
                ? element.personal_type["name_" + this.$i18n.locale]
                : "",
              "Количество ставок(БП)": element.rate_count_bp,
              "Количество ставок": element.rate_count,
              "Кол-во сотр.": count,
              "Табел №": tabel,
              Сотрудники: employee,
              "Номер приказа": enter_order_number,
            });
          });
          this.staff_excel = this.staff_excel.concat(new_array);
          if (response.data.data.length == 1000) {
            this.getStaffExcel(++page);
          } else {
            this.loading = false;
            this.downloadExcel = true;
          }
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getRef() {
      axios
        .post(this.$store.state.backend_url + "api/staffs/get-ref", {
          language: this.$i18n.locale,
        })
        .then((response) => {
          this.departments = response.data.departments;
          this.positions = response.data.positions;
          this.requirements = response.data.requirements;
          this.ranges = response.data.ranges;
          this.personalTypes = response.data.personal_types;
          this.expenceTypes = response.data.expence_types;
          this.branches = response.data.branches;
          this.objectTypesList = response.data.object_types;
          this.coefficients = response.data.coefficients;
          this.loading = false;
          // console.log(this.positions);
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    viewPdfFile(item) {
      this.fileForView = item;
      this.pdfViewDialog = true;
      // $store.state.backend_url + 'staffs/get-file/'+item.id
    },
    editItemFiles(item) {
      // if (this.$store.getters.checkPermission("staff-update_requirements"))
      {
        this.formData = new FormData();
        this.staffTmp = item;
        this.fileDialog = true;
      }
    },
    addFiles() {
      this.formData = new FormData();
      this.selectFiles.forEach((v, i) => {
        this.formData.append("files[]", v);
      });

      this.formData.append("object_type_id", this.selectObjectType);

      axios
        .post(
          this.$store.state.backend_url +
            "api/staffs/update-files/" +
            this.staffTmp.id,
          this.formData,
          {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          }
        )
        .then((res) => {
          this.selectFiles = [];
          this.selectObjectType = "";
          this.staffTmp.files = res.data.files;
        })
        .catch(function (e) {
          console.log("FAILURE!!");
        });

      // Display the key/value pairs
      // for (var pair of this.formData.entries()) {
      // }
      // this.selectFiles.forEach((v,i) => this.formData.append('files['+i+']', 'v'));
    },
    removeTmpFile(id) {
      axios
        .delete(this.$store.state.backend_url + "api/staffs/delete-file/" + id)
        .then((res) => {
          this.staffTmp.files = this.staffTmp.files.filter((v) => v.id != id);
        })
        .catch(function (e) {
          console.log("FAILURE!!");
        });
    },
    addRequirement() {
      let requirement = this.requirements.find(
        (v) => v.id == this.selectRequirement
      );
      this.staffTmp.requirements.push(requirement);
      this.selectRequirement = "";
    },
    removeRequirement(item) {
      this.staffTmp.requirements = this.staffTmp.requirements.filter(
        (v) => v.id != item.id
      );
      this.selectRequirement = "";
    },
    newItem() {
      if (this.$store.getters.checkPermission("staff-create")) {
        this.dialogHeaderText = this.$t("staff.newStaff");
        this.form = {
          id: Date.now(),
          name_uz_latin: "",
          name_uz_cyril: "",
          name_ru: "",
          department_id: "",
          position_id: "",
          range_id: "",
          coefficient_id: "",
          shift: "",
          personal_type_id: "",
          expence_type_id: "",
          rate_count_bp: "",
          rate_count: "",
          order_date: "",
          order_number: "",
          begin_date: "",
          end_date: "",
        };
        this.dialog = true;
        this.editMode = false;
        if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
      }
    },
    editItem(item) {
      if (this.$store.getters.checkPermission("staff-update")) {
        this.dialogHeaderText = this.$t("staff.editStaff");
        this.form = Object.assign({}, item);
        this.dialog = true;
        this.editMode = true;
        if (this.$refs.dialogForm) this.$refs.dialogForm.resetValidation();
      }
    },
    relationdocument(item){
      this.relation_document = item;
      this.relationdocumentdialog = true;
    },
    showItem(item) {
      // this.dialogHeaderText = "Salom";

      this.employeess = item.employee_staff;
      if (this.employeess.length) {
        this.showItemDialog = true;
      }
    },
    getStaff(item) {
      this.criticalForm.staff_id = item.id;
      this.criticalDepartment = item.department;
      this.criticalPosition = item.position;
      this.criticalEmployees = item.employee_staff;
      axios
        .get(
          this.$store.state.backend_url + "api/staff-criticals/show/" + item.id
        )
        .then((response) => {
          if (response.data) {
            this.criticalForm = Object.assign({}, response.data);
            this.criticalForm.id = response.data.id;
            this.criticalForm.isNew = false;
          } else {
            this.criticalForm.id = Date.now();
            this.criticalForm.isNew = true;
          }
        })
        .catch((error) => {
          console.log(error);
        });
      this.dialogStaffCritical = true;
    },
    saveCritical() {
      axios
        .post(this.$store.state.backend_url + "api/staff-criticals/update", {
          form: this.criticalForm,
        })
        .then((res) => {
          this.getList();
          this.dialogStaffCritical = false;
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
          this.criticalForm = {};
        })
        .catch((err) => {
          console.log(err);
        });
    },
    editItemRequirements(item) {
      if (this.$store.getters.checkPermission("staff-update_requirements")) {
        this.staffTmp = item;
        this.requirementDialog = true;

        //this.dialogHeaderText = this.$t("staff.editStaff");
        //this.form = Object.assign({}, item);
        //this.dialog = true;
        //this.editMode = true;
        //if (this.$refs.dialogForm) this.$refs.dialogForm.resetValidation();
      }
    },
    saveRequirements() {
      axios
        .post(
          this.$store.state.backend_url + "api/staffs/update-requitements",
          { staff: this.staffTmp }
        )
        .then((res) => {
          this.requirementDialog = false;
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
    },
    save() {
      if (this.$refs.dialogForm.validate())
        axios
          .post(this.$store.state.backend_url + "api/staffs/update", this.form)
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
    },
    deleteItem(item) {
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
          if (this.$store.getters.checkPermission("staff-delete")) {
            const index = this.items.indexOf(item);
            axios
              .get(
                this.$store.state.backend_url +
                  "api/staffs/deactivate/" +
                  item.id
              )
              .then((res) => {
                this.getList(this.page, this.itemsPerPage);
                this.dialog = false;
                Swal.fire("Deleted!", this.$t("swal_deleted"), "success");
              })
              .catch((err) => {
                console.log(err);
              });
          }
        }
      });
    },
  },
  mounted() {
    this.getList();
    // Swal.fire({
    //   position: "top-end",
    //   icon: "success",
    //   title: "Your work has been saved",
    //   showConfirmButton: false,
    //   timer: 1500
    // });

    this.isActives = [
      { text: this.$t("employee.active"), value: "1" },
      { text: this.$t("employee.inactive"), value: "0" },
    ];
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