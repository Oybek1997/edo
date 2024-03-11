<template>
  <div class="fullHeight">
    <v-card 
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{ $t("Қабул") }}</span>
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
          <v-btn @click="printAllPermission(items)" class="filterBtn px-2" style="background: #fff; height: 34px;">
              Pechat <v-icon color="#00B950" right>mdi-printer</v-icon>
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
                  v-if="$store.getters.checkPermission('registration-patient-create')"
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
                  @click="tableToExcel('table', 'Lorem Table')"
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
            ref="table"
            id="table"
            class="doc-template_data-table"
            dense
            style="width: 100%; height: 100%; border-radius: 10px"
            fixed-header
            :loading-text="$t('loadingText')"
            :no-data-text="$t('noDataText')"
            :height="screenHeight"
            :loading="loading"
            :headers="headers"
            :items="items"
            :single-expand="singleExpand"
            :options.sync="dataTableOptions"
            :expanded.sync="expanded"
            item-key="id"
            :server-items-length="server_items_length"
            show-expand
            :footer-props="{
              itemsPerPageOptions: [20, 50, 100, -1],
              itemsPerPageAllText: $t('itemsPerPageAllText'),
              itemsPerPageText: $t('itemsPerPageText'),
              showFirstLastPage: true,
              firstIcon: 'mdi-arrow-collapse-left',
              lastIcon: 'mdi-arrow-collapse-right',
              prevIcon: 'mdi-arrow-left',
              nextIcon: 'mdi-arrow-right',
            }"
            @update:sort-desc="updatePage"
            @update:page="updatePage"
            @update:items-per-page="updatePerPage"
          >
            <template v-slot:body.prepend="{ item }">
              <tr class="prepend_height">
                <td></td>
                <td></td>
                <td>
                <v-autocomplete
                    clearable
                    v-model="filter.hospital_diagnosis_id"
                    :items="
                      hospitalDiagnoses.map(v => ({
                        text: v.text,
                        value: v.value,
                      }))
                    "
                    hide-details
                    dense
                    @change="getList"
                  ></v-autocomplete>
                </td>
                <td>
                <v-text-field v-model="filter.tabel" hide-details dense @keyup.enter="getList"></v-text-field>
                </td>
                <td>
                <v-text-field
                    v-model="filter.lastname_uz_cyril"
                    hide-details
                    dense
                    @keyup.enter="getList"
                  ></v-text-field>
                </td>
                <td>
                <v-text-field
                    v-model="filter.department_code"
                    hide-details
                    dense
                    @keyup.enter="getList"
                  ></v-text-field>
                </td>
                <td>
                <v-text-field
                    v-model="filter.employee_department"
                    hide-details
                    dense
                    @keyup.enter="getList"
                  ></v-text-field>
                </td>
                <td>
                <v-text-field v-model="filter.created_at" hide-details dense @keyup.enter="getList"></v-text-field>
                </td>
                <td>
                  <v-autocomplete
                    clearable
                    v-model="filter.type"
                    :items="
                      types.map(v => ({
                        text: v.text,
                        value: v.value,
                      }))
                    "
                    hide-details
                    dense
                    @change="getList"
                  ></v-autocomplete>
                </td>
                <td>
                <v-text-field v-model="filter.diagnosis" hide-details dense @keyup.enter="getList"></v-text-field>
                </td>
                <td>
                <v-text-field v-model="filter.description" hide-details dense @keyup.enter="getList"></v-text-field>
                </td>
                <td>
                <v-text-field v-model="filter.username" hide-details dense @keyup.enter="getList"></v-text-field>
                </td>
                <td></td>
              </tr>
            </template>
            <template v-slot:item.id="{ item }">
              {{
              items
              .map(function (x) {
              return x.id;
              })
              .indexOf(item.id) + from
              }}
            </template>
            <template v-slot:expanded-item="{ headers, item }">
              <td :colspan="headers.length" class="pa-3">
                <v-card class="my-2">
                  <div>
                    <v-system-bar window color="#eee">
                      <span class="font-weight-bold">{{ $t("Тез ёрдам чақируви") }}</span>
                      <v-spacer></v-spacer>
                      <v-icon v-if="$store.getters.checkPermission('ambulance-call-create')" color="success" medium @click="newAmbulanceCall(item)">mdi-plus</v-icon>
                    </v-system-bar>
                  </div>
                  <v-container fluid class="pa-0">
                    <table
                      class="medTable ma-0 pa-0"
                      v-if="
                        item && item.ambulance_calls
                          ? item.ambulance_calls.length
                          : ''
                      "
                    >
                      <tr>
                        <td class="font-weight-bold">#</td>
                        <td class="font-weight-bold">{{ $t("Тури") }}</td>
                        <td class="font-weight-bold">{{ $t("Санаси ва вақти") }}</td>
                        <td class="font-weight-bold">{{ $t("Изоҳ") }}</td>
                        <td class="font-weight-bold" width="250px">{{ $t("Рўйхатга олиш вақти") }}</td>
                        <td class="font-weight-bold" style="max-width: 50px">{{ $t("Амал") }}</td>
                      </tr>
                      <tr v-for="(itm, ind) in item.ambulance_calls" :key="ind">
                      <td class>{{ ind + 1 }}</td>
                        <td class>
                        <span v-if="itm.ambulance_call_type == 1"> Иш жойида ёрдам кўрсатилди </span>
                        <span v-else-if="itm.ambulance_call_type == 2"> Тиббий қисимга олиб келинди </span>
                        <span v-else="itm.ambulance_call_type == 3"> Даволаш муассасасига олиб чиқилди </span>
                        </td>
                        <td class>{{ itm.call_time }}</td>
                        <td class>{{ itm.description }}</td>
                        <td>{{ moment(itm.created_at).format('YYYY.MM.DD [&nbsp;] HH:mm') }}</td>
                      <td class style="max-width: 40px">
                          <v-btn 
                            v-if="$store.getters.checkPermission('ambulance-call-update')" 
                            class="px-1"
                            color="blue"
                            style="min-width: 25px"
                            small
                            text
                            @click="editAmbulanceCall(itm)">
                            <v-icon size="18">mdi-pencil</v-icon>
                          </v-btn>
                          <v-btn
                            v-if="$store.getters.checkPermission('ambulance-call-delete')"
                            class="px-1"
                            color="error"
                            style="min-width: 25px"
                            small
                            text
                            @click="deleteAmbulanceCall(itm)"
                          >
                            <v-icon size="18">mdi-trash-can-outline</v-icon>
                          </v-btn>
                        </td>
                      </tr>
                    </table>
                  </v-container>
                </v-card>

                <!-- Medical Treatment -->
                <v-card class="my-5 ">
                  <div>
                    <v-system-bar window color="#eee">
                      <span class="font-weight-bold">{{ $t("Тиббий ёрдам турлари") }}</span>
                      <v-spacer></v-spacer>
                      <v-icon v-if="$store.getters.checkPermission('medical-treatment-create')" color="success" medium @click="newMedicalTreatment(item)">mdi-plus</v-icon>
                    </v-system-bar>
                  </div>
                  <v-container fluid class="pa-0">
                    <table
                      class="medTable ma-0 pa-0"
                      v-if="
                        item && item.medical_treatments
                          ? item.medical_treatments.length
                          : ''
                      "
                    >
                      <tr>
                        <td class="font-weight-bold">#</td>
                        <td class="font-weight-bold">{{ $t("Тури") }}</td>
                        <td class="font-weight-bold">{{ $t("Изоҳ") }}</td>
                        <td class="font-weight-bold" width="250px">{{ $t("Рўйхатга олиш вақти") }}</td>
                        <td class="font-weight-bold" style="max-width: 50px">{{ $t("Амал") }}</td>
                      </tr>
                      <tr v-for="(itm, ind) in item.medical_treatments" :key="ind">
                      <td class>{{ ind + 1 }}</td>
                        <td class>
                        <span v-if="itm.treatment_type == 1">Муолажа</span>
                        <span v-else-if="itm.treatment_type == 2">Боғлов</span>
                        <span v-else>Физиотерапия</span>
                        </td>
                        <td class>{{ itm.description }}</td>
                        <td>{{ moment(itm.created_at).format('YYYY.MM.DD [&nbsp;] HH:mm') }}</td>
                      
                            <td class style="max-width: 60px">
                                <v-btn 
                                  v-if="$store.getters.checkPermission('medical-treatment-update')" 
                                  class="px-1"
                                  color="blue"
                                  style="min-width: 25px"
                                  small
                                  text
                                  @click="editMedicalTreatment(itm)">
                                  <v-icon size="18">mdi-pencil</v-icon>
                                </v-btn>
                                <v-btn
                                  v-if="$store.getters.checkPermission('medical-treatment-delete')"
                                  class="px-1"
                                  color="error"
                                  style="min-width: 25px"
                                  small
                                  text
                                  @click="deleteMedicalTreatment(itm)"
                                >
                                  <v-icon size="18">mdi-trash-can-outline</v-icon>
                                </v-btn>
                              </td>
                            </tr>
                          </table>
                        </v-container>
                  </v-card> 
                <!-- </v-container> -->
                <span
                  v-if="!item"
                  style="display: block; text-align: center; color: red"
                  >{{ $t("noDataText") }}</span
                >
              </td>
            </template>
            <template v-slot:item.hospital_diagnosis.name="{ item }">
              <td style="width: 200px;">
                {{ item.hospital_diagnosis.name }}
              </td>
            </template>
            <template v-slot:item.employee_department="{ item }">
              <td style="width: 200px;">
                {{ item.employee_department }}
              </td>
            </template>
            <template v-slot:item.diagnosis="{ item }">
              <td style="width: 150px;">
                {{ item.diagnosis }}
              </td>
            </template>
            <template v-slot:item.description="{ item }">
              <td style="width: 150px;">
                {{ item.description }}
              </td>
            </template>
            <template v-slot:item.created_by.username="{ item }">
              <td style="width: 50px;">
                {{ item.created_by.username }}
              </td>
            </template>
            <template v-slot:item.employee="{ item }">
              <td style="width: 100px;">
              <span v-if="$i18n.locale == 'uz_latin'">
                {{ item.employee ? item.employee.lastname_uz_cyril : ""}}
                {{ item.employee ? item.employee.firstname_uz_cyril : "" }}
              </span>
              <span v-else>
                {{ item.employee ? item.employee.firstname_uz_cyril : "" }}
                {{ item.employee ? item.employee.lastname_uz_cyril : "" }}
              </span>
              </td>
            </template>
            <template v-slot:item.created_at="{ item }">{{ moment(item.created_at).format('YYYY.MM.DD [&nbsp;] HH:mm') }}</template>
            <template v-slot:item.type="{ item }">
              <template v-if="item.type == 1">
              <v-btn
                color="green"
                small
                text
                @click="printPermission(item)"
              >
                Ruxsatnoma
              </v-btn>          
              </template>
              <template v-else-if="item.type == 2">
              <v-btn
                color="green"
                small
                text
                @click="printReferral(item)"
              >
                Yo'llanma
              </v-btn>           
              </template>
              <template v-else></template>
            </template>
            <template v-slot:item.actions="{ item }">
            <v-btn
                v-if="item.type == 1"
                class="px-1"
                color="green"
                style="min-width: 25px"
                small
                text
                @click="printPermission(item)"
              >
                <v-icon size="18">mdi-printer</v-icon>
              </v-btn>
              <v-btn
                v-else-if="item.type == 2"
                class="px-1"
                color="green"
                style="min-width: 25px"
                small
                text
                @click="printReferral(item)"
              >
                <v-icon size="18">mdi-printer</v-icon>
              </v-btn>
              <v-btn
                v-if="$store.getters.checkPermission('registration-patient-update')"
                class="px-1"
                color="blue"
                style="min-width: 25px"
                small
                text
                @click="editItem(item)"
              >
                <v-icon size="18">mdi-pencil</v-icon>
              </v-btn>
              <v-btn  
                v-if="$store.getters.checkPermission('registration-patient-delete')" 
                class="px-1"
                color="error"
                style="min-width: 25px"
                small
                text
                @click="deleteItem(item)">
                <v-icon size="18">mdi-delete</v-icon>
              </v-btn>
            </template>
          </v-data-table>
        </v-col>
      </v-row>
    </v-card>
    <v-dialog v-model="dialog" @keydown.esc="dialog = false" persistent max-width="600">
      <v-card class="pa-5">
        <v-card-title class="pa-0" primary-title>
          <span class="dialogTitle">{{ dialogHeaderText }}</span>
        </v-card-title>
        <v-divider color="#DCE5EF" class="my-1"></v-divider>
        <v-card-text>
          <v-form @keyup.native.enter="save" ref="dialogForm">
            <v-row class="mx-0 dialogForm">
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t('Ходим') }}</label>
                <v-autocomplete
                  outlined
                  class="pa-0"
                  clearable
                  v-model="form.employee_id"
                  :items="employees"
                  @change="changeEmployee"
                  :rules="[v => !!v || $t('input.required')]"
                  hide-details
                  dense
                ></v-autocomplete>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t('Бўлим коди') }}</label>
                <v-text-field
                  outlined
                  class="pa-0"
                  disabled
                  v-model="form.department_code"
                  hide-details
                  dense
                ></v-text-field>
              </v-col>
              <v-col cols="12" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t('Бўлими') }}</label>
                <v-text-field
                  outlined
                  class="pa-0"
                  disabled
                  v-model="form.employee_department"
                  hide-details
                  dense
                ></v-text-field>
              </v-col>
              <v-col cols="12" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t('МКБХ') }}</label>
                <v-autocomplete
                  outlined
                  class="pa-0"
                  clearable
                  v-model="form.hospital_diagnosis_id"
                  :items="hospitalDiagnoses"
                  :item-text="'text'"
                  item-value="value"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details
                  dense
                >
                  <template v-slot:selection="{ item }">
                    <v-chip color="white" class="pa-1 ma-0">
                      <span v-text="item.text"></span>
                    </v-chip>
                  </template>
                  <template v-slot:item="{ item }">
                    <v-list-item-content>
                      <v-list-item-title v-text="item.text"></v-list-item-title>
                    </v-list-item-content>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col cols="12" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t('Ташхис') }}</label>
                <v-text-field
                  v-model="form.diagnosis"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t('Йўлланма ва Рухсатнома') }}</label>
                <v-autocomplete
                  outlined
                  class="pa-0"
                  clearable
                  v-model="form.type"
                  :items="types"
                  hide-details
                  dense
                ></v-autocomplete>
              </v-col>
              <v-col cols="6" class="py-0 px-1 mb-3">
                <label class="labelTitle" for>{{ $t('Изоҳ') }}</label>
                <v-text-field
                  v-model="form.description"
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

    <v-dialog v-model="ambulanceCallDialog" persistent max-width="600px" :fullscreen="fullscreen">
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">{{ $t("Тез ёрдам чақируви") }}</span>
          <v-spacer></v-spacer>
          <v-btn class="mr-2" color outlined x-small fab @click="fullscreen = !fullscreen">
            <v-icon>mdi-window-maximize</v-icon>
          </v-btn>
          <v-btn color="red" outlined x-small fab class @click="ambulanceCallDialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form @keyup.native.enter="saveAmbulanceCall" ref="ambulanceCallDialogForm">
            <v-row class="ma-0 pa-0">
              <v-col cols="6">
                <v-autocomplete
                  :label="$t('Тури')"
                  outlined
                  class="pa-0"
                  clearable
                  v-model="ambulanceCallForm.ambulance_call_type"
                  :items="ambulance_call_types"
                  hide-details
                  dense
                ></v-autocomplete>
              </v-col>
              <v-col cols="6" >
                <v-menu
                  
                  v-model="createdAtMenu"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="290px"
                >
                  <template v-slot:activator="{ on }">
                    <v-text-field
                    :label="$t('Санаси ва вақти')"
                      v-model="ambulanceCallForm.call_time"
                      :rules="[(v) => !!v || $t('input.required')]"                      
                      v-on="on"
                      hide-details="auto"
                      dense
                      outlined
                    ></v-text-field>
                  </template>
                  <v-date-picker v-model="ambulanceCallForm.call_time" @input="createdAtMenu = false"></v-date-picker>
                </v-menu>
              </v-col>
              <v-col cols="12">
                <v-text-field
                :label="$t('Изоҳ')"
                  v-model="ambulanceCallForm.description"
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
          <v-btn color="primary" @click="saveAmbulanceCall">{{ $t("Сақлаш") }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="medicalTreatmentDialog" persistent max-width="600px" :fullscreen="fullscreen">
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">{{ $t("Тиббий ёрдам турлари") }}</span>
          <v-spacer></v-spacer>
          <v-btn class="mr-2" color outlined x-small fab @click="fullscreen = !fullscreen">
            <v-icon>mdi-window-maximize</v-icon>
          </v-btn>
          <v-btn color="red" outlined x-small fab class @click="medicalTreatmentDialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form @keyup.native.enter="medicalTreatment" ref="medicalTreatmentDialogForm">
            <v-row class="ma-0 pa-0">
              <v-col cols="12">
                <v-autocomplete
                  :label="$t('Тури')"
                  outlined
                  class="pa-0"
                  clearable
                  v-model="medicalTreatmentForm.treatment_type"
                  :items="treatment_types"
                  hide-details
                  dense
                ></v-autocomplete>
              </v-col>
              <v-col cols="12">
                <v-text-field
                :label="$t('Изоҳ')"
                  v-model="medicalTreatmentForm.description"
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
          <v-btn color="primary" @click="saveMedicalTreatment">{{ $t("Сақлаш") }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="medicineCostDialog" persistent max-width="600px" :fullscreen="fullscreen">
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">{{ $t("Medicine Cost ") }}</span>
          <v-spacer></v-spacer>
          <v-btn class="mr-2" color outlined x-small fab @click="fullscreen = !fullscreen">
            <v-icon>mdi-window-maximize</v-icon>
          </v-btn>
          <v-btn color="red" outlined x-small fab class @click="medicineCostDialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form @keyup.native.enter="medicineCost" ref="medicineCostDialogForm">
            <v-row class="ma-0 pa-0">
            <v-col cols="12">
                <v-autocomplete
                :label="$t('Dorilar')"
                  outlined
                  class="pa-0"
                  clearable
                  v-model="medicineCostForm.medicine_id"
                  :items="medicines"
                  :item-text="'name'"
                  item-value="id"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details
                  dense
                >
                  <template v-slot:selection="{ item }">
                    <v-chip color="white" class="pa-1 ma-0">
                      <span v-text="item.name"></span>
                    </v-chip>
                  </template>
                  <template v-slot:item="{ item }">
                    <v-list-item-content>
                      <v-list-item-title v-text="item.name"></v-list-item-title>
                    </v-list-item-content>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col cols="12">
                <v-text-field
                :label="$t('amount')"
                  v-model="medicineCostForm.amount"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-autocomplete
                  :label="$t('unit measure')"
                  outlined
                  class="pa-0"
                  clearable
                  v-model="medicineCostForm.unit_measure"
                  :items="unit_measures"
                  hide-details
                  dense
                ></v-autocomplete>
              </v-col>
              <v-col cols="12">
                <v-autocomplete
                  :label="$t('medicine owner')"
                  outlined
                  class="pa-0"
                  clearable
                  v-model="medicineCostForm.medicine_owner"
                  :items="medicine_owners"
                  hide-details
                  dense
                ></v-autocomplete>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="saveMedicineCost">{{ $t("save") }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="loading" width="300" hide-overlay>
      <v-card color="primary" dark>
        <v-card-text>
          {{ $t("loadingText") }}
          <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
const moment = require("moment");
import TableToExcel from "@linways/table-to-excel";
const axios = require("axios").default;
import Swal from "sweetalert2";
export default {
  data() {
    return {
      moment: moment,
      momentDate: new Date(),
      loading: false,
      dialog: false,
      fullscreen: false,
      editMode: null,
      items: [],
      employees: [],
      hospitalDiagnoses: [],
      medicines: [],
      diagnosisCodes: [],
      districts: [],
      createdAtMenu: false,
      ambulanceCalls: [],
      ambulanceCallForm: [],
      ambulanceCallDialog: false,
      ambulanceCallDialogHeaderText: "",
      medicalTreatments: [],
      medicalTreatmentForm: [],
      medicalTreatmentDialog: false,
      medicalTreatmentDialogHeaderText: "",
      medicineCosts: [],
      medicineCostForm: [],
      medicineCostDialog: false,
      medicineCostDialogHeaderText: "",
      form: {},
      filter: {
        hospital_diagnosis_id: "",
        diagnosis_code_id: "",
        description: "",
        created_at: "",
        username: "",
        department_code: "",
        employee_department: "",
        lastname_uz_cyril: "",
        tabel: "",
        type: "",
        diagnosis: ""
      },
      types: [
        {
          value: 1,
          text: "Рухсатнома"
        },
        {
          value: 2,
          text: "Йўлланма"
        },
      ],
      ambulance_call_types: [
        {
          value: 1,
          text: "Иш жойида ёрдам кўрсатилди"
        },
        {
          value: 2,
          text: "Тиббий қисимга олиб келинди"
        },
        {
          value: 3,
          text: "Даволаш муассасасига олиб чиқилди"
        },
      ],
      treatment_types: [
        {
          value: 1,
          text: "Муолажа"
        },
        {
          value: 2,
          text: "Боғлов"
        },
        {
          value: 3,
          text: "Физиотерапия"
        }
      ],
      unit_measures: [
        {
          value: 1,
          text: "Dona"
        },
        {
          value: 2,
          text: "Kg"
        },
        {
          value: 3,
          text: "ml"
        },
        {
          value: 4,
          text: "L"
        },
        {
          value: 5,
          text: "Boshqa"
        },
      ],
      medicine_owners: [
        {
          value: 1,
          text: "Korxona"
        },
        {
          value: 2,
          text: "O'zi olib kelgan"
        },
      ],
      dialogHeaderText: "",
      dataTableOptions: { page: 1, itemsPerPage: 20 },
      page: 1,
      from: 0,
      server_items_length: -1,
      expanded: [],
      singleExpand: false,
    };
  },  
  computed: {
    screenHeight() {
      return window.innerHeight - 175;
    },
    headers() {
      return [
        {
          text: "",
          value: "data-table-expand",
          width: 30,
        },
        { text: "#", value: "id", align: "center", width: 30 },
        { text: this.$t("МКБХ"), value: "hospital_diagnosis.name", width: 200 },
        // { text: this.$t("Код диагнос тикаси"), value: "hospital_diagnosis.diagnosis_code.name" },
        
        { text: this.$t("Табел"), value: "employee.tabel" },
        {
          text: this.$t("Ходим"),
          value: "employee",
          width: 100
        },
        { text: this.$t("Бўлим коди"), value: "department_code" },
        { text: this.$t("Бўлими"), value: "employee_department", width: 200 },
        { text: this.$t("Рўйхатга олиш"), value: "created_at" },
        { text: this.$t("Тури"), value: "type" },
        { text: this.$t("Ташхис"), value: "diagnosis", width: 150 },
        { text: this.$t("Изоҳ"), value: "description", width: 150 },
        { text: this.$t("Фойдаланувчи"), value: "created_by.username", width: 50 },
        {
          text: this.$t("Амал"),
          value: "actions",
          width: 100,
          align: "right"
        }
      ].filter(
        v =>
          v.value != "actions" ||
          this.$store.getters.checkPermission("registration-patient-update") ||
          this.$store.getters.checkPermission("registration-patient-delete")
      );
    }
  },
  methods: {
    changeEmployee($event) {
      this.loading = true;
      axios
        .get(
          this.$store.state.backend_url +
            "api/employees/get-employee-with-staff/" +
            $event
        )
        .then(response => {
          this.form.employee_department =
            response.data.staff[0].department.name_uz_cyril;
          this.form.department_code =
            response.data.staff[0].department.department_code;
          this.form.tabel =
            response.data.tabel;
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
        });
    },
    updatePage($event) {
      this.getList();
    },
    updatePerPage($event) {
      this.getList();
    },
    getRef() {
      let locale = this.$i18n.locale;
      locale = locale == "uz_latin" ? "uz_cyril" : "uz_cyril";
      axios
        .get(
          this.$store.state.backend_url +
            "api/staff-criticals/get-ref/" +
            locale
        )
        .then(response => {
          this.employees = response.data.map(v => ({
            value: v.id,
            text:
              v.tabel +
              " " +
              v["lastname_" + locale] +
              " " +
              (v["firstname_" + locale]
                ? v["firstname_" + locale].substr(0, 1) + ". "
                : "") +
              " " +
              (v["middlename_" + locale]
                ? v["middlename_" + locale].substr(0, 1) + ". "
                : "")
          }));
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
        });
    },
    getRefHospitalDiagnosis() {
      axios
        .get(
          this.$store.state.backend_url +
            "api/medpunkt/registration-period-illness/get-ref-hospital-diagnoses"
        )
        .then(response => {
          this.hospitalDiagnoses = response.data.map(v => ({
            value: v.id,
            text:
              v.diagnosis_code.name +
              " " +
              v.name
          }));
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
        });
    },
    getRefDiagnosisCode() {
      axios
        .get(
          this.$store.state.backend_url +
            "api/medpunkt/diagnosis-code/get-ref"
        )
        .then(response => {
          this.diagnosisCodes = response.data;
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
        });
    },
    getRefMedicines() {
      axios
        .get(
          this.$store.state.backend_url +
            "api/medpunkt/medicine-costs/get-ref"
        )
        .then(response => {
          this.medicines = response.data;
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
        });
    },
    getList() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/medpunkt/registration-patients", {
          pagination: this.dataTableOptions,
          filter: this.filter,
        })
        .then(response => {
          this.items = response.data.data;
          this.server_items_length = response.data.total;
          this.from = response.data.from;
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
          this.loading = false;
        });
    },
    newItem() {
      if (this.$store.getters.checkPermission("registration-patient-create")) {
        this.dialogHeaderText = this.$t("Қўшиш");
        this.form = {
          id: Date.now()
        };
        this.dialog = true;
        this.editMode = false;
        if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
      }
    },
    editItem(item) {
      if (this.$store.getters.checkPermission("registration-patient-update")) {
        this.dialogHeaderText = this.$t("edit");
        this.formIndex = this.items.indexOf(item);
        this.form = Object.assign({}, item);
        this.dialog = true;
        this.editMode = true;
        if (this.$refs.dialogForm) this.$refs.dialogForm.resetValidation();
      }
    },
    save() {
      if (this.$refs.dialogForm.validate())
        axios
          .post(
            this.$store.state.backend_url +
              "api/medpunkt/registration-patients/update",
            Object.assign(this.form, {})
          )
          .then(res => {
            this.getList();
            this.dialog = false;
            const Toast = Swal.mixin({
              toast: true,
              position: "top-end",
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true,
              onOpen: toast => {
                toast.addEventListener("mouseenter", Swal.stopTimer);
                toast.addEventListener("mouseleave", Swal.resumeTimer);
              }
            });

            if(res.data == "xato"){
              Toast.fire({
              icon: "error",
              title: this.$t("Маълумот киритишда хатолик бор")
            });
            }
            else{

            Toast.fire({
              icon: "success",
              title: this.$t("create_update_operation")
            });
            }
          })
          .catch(err => {
            console.log(err);

           
          });

      
    },
    deleteItem(item) {
      if (this.$store.getters.checkPermission("registration-patient-delete")) {
        const index = this.items.indexOf(item);
        Swal.fire({
          title: this.$t("Ушбу амални бажаришга аминмисиз?"),
          text: this.$t("Ушбу амални кейин орқага қайтариб бўлмайди"),
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: this.$t("Ха, ўчирилсин!"),
          cancelButtonText: this.$t("Бекор қилиш")
        }).then(result => {
          if (result.value) {
            axios
              .delete(
                this.$store.state.backend_url +
                  "api/medpunkt/registration-patients/delete/" +
                  item.id
              )
              .then(res => {
                this.getList(this.page, this.itemsPerPage);
                this.dialog = false;
                Swal.fire("Deleted!", this.$t("Маълумот ўчирилди"), "success");
              })
              .catch(err => {
                Swal.fire({
                  icon: "error",
                  title: this.$t("swal_error_title"),
                  text: this.$t("swal_error_text")
                });
                console.log(err);
              });
          }
        });
      }
    },
    newAmbulanceCall(item) {
      if (this.$store.getters.checkPermission("ambulance-call-create")) {
      this.ambulanceCallDialogHeaderText = this.$t("Add ambulance");
      this.ambulanceCallForm = {
        id: Date.now(),
        registration_patient_id: item.id,
        // ambulance_call_type: "",
        // call_time: "",
        // description: "",
      };
      this.ambulanceCallDialog = true;

      if (this.$refs.AmbulanceCallDialogForm)
        this.$refs.AmbulanceCallDialogForm.reset();
      }
    },
    editAmbulanceCall(item) {
      if (this.$store.getters.checkPermission("ambulance-call-update")) {
      this.ambulanceCallDialogHeaderText = this.$t("edit");
      this.ambulanceCallForm = Object.assign({}, item);
      this.ambulanceCallDialog = true;

      if (this.$refs.ambulanceCallDialogForm)
        this.$refs.ambulanceCallDialogForm.resetValidation();
      }
    },
    saveAmbulanceCall() {
      if (this.$refs.ambulanceCallDialogForm.validate()) this.loading = true;
      axios
        .post(
          this.$store.state.backend_url + "api/medpunkt/ambulance-calls/update",
          this.ambulanceCallForm
        )
        .then(res => {
          this.getList();
          this.ambulanceCallDialog = false;
          const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            onOpen: toast => {
              toast.addEventListener("mouseenter", Swal.stopTimer);
              toast.addEventListener("mouseleave", Swal.resumeTimer);
            }
          });
          Toast.fire({
            icon: "success",
            title: this.$t("create_update_operation")
          });
          this.loading = false;
        })
        .catch(err => {
          console.log(err);
          this.loading = false;
        });
    },
    deleteAmbulanceCall(item) {
      if (this.$store.getters.checkPermission("ambulance-call-delete")) {
      Swal.fire({
        title: this.$t("swal_title"),
        text: this.$t("swal_text"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: this.$t("swal_delete")
      }).then(result => {
        if (result.value) {
          axios
            .delete(
              this.$store.state.backend_url +
                "api/medpunkt/ambulance-calls/delete/" +
                item.id
            )
            .then(res => {
              this.items = this.items.map(v => {
                if (v.id == item.registration_patient_id) {
                  v.ambulance_calls = v.ambulance_calls.filter(
                    ea => ea.id != item.id
                  );
                  return v;
                }
                return v;
              });
              Swal.fire("Deleted!", this.$t("swal_deleted"), "success");
            })
            .catch(err => {
              Swal.fire({
                icon: "error",
                title: this.$t("swal_error_title"),
                text: this.$t("swal_error_text")
              });
              console.log(err);
            });
        }
      });
      }
    },
    newMedicalTreatment(item) {
      if (this.$store.getters.checkPermission("medical-treatment-create")) {
      this.medicalTreatmentDialogHeaderText = this.$t("Medical Treatment");
      this.medicalTreatmentForm = {
        id: Date.now(),
        registration_patient_id: item.id,
        // treatment_type: "",
        // description: "",
      };
      this.medicalTreatmentDialog = true;

      if (this.$refs.medicalTreatmentDialogForm)
        this.$refs.medicalTreatmentDialogForm.reset();
      }
    },
    editMedicalTreatment(item) {
      if (this.$store.getters.checkPermission("medical-treatment-update")) {
      this.medicalTreatmentDialogHeaderText = this.$t("edit");
      this.medicalTreatmentForm = Object.assign({}, item);
      this.medicalTreatmentDialog = true;

      if (this.$refs.medicalTreatmentDialogForm)
        this.$refs.medicalTreatmentDialogForm.resetValidation();
      }
    },
    saveMedicalTreatment() {
      if (this.$refs.medicalTreatmentDialogForm.validate()) this.loading = true;
      axios
        .post(
          this.$store.state.backend_url + "api/medpunkt/medical-treatments/update",
          this.medicalTreatmentForm
        )
        .then(res => {
          this.getList();
          this.medicalTreatmentDialog = false;
          const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            onOpen: toast => {
              toast.addEventListener("mouseenter", Swal.stopTimer);
              toast.addEventListener("mouseleave", Swal.resumeTimer);
            }
          });
          Toast.fire({
            icon: "success",
            title: this.$t("create_update_operation")
          });
          this.loading = false;
        })
        .catch(err => {
          console.log(err);
          this.loading = false;
        });
    },
    deleteMedicalTreatment(item) {
      if (this.$store.getters.checkPermission("medical-treatment-delete")) {
      Swal.fire({
        title: this.$t("swal_title"),
        text: this.$t("swal_text"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: this.$t("swal_delete")
      }).then(result => {
        if (result.value) {
          axios
            .delete(
              this.$store.state.backend_url +
                "api/medpunkt/medical-treatments/delete/" +
                item.id
            )
            .then(res => {
              this.items = this.items.map(v => {
                if (v.id == item.registration_patient_id) {
                  v.medical_treatments = v.medical_treatments.filter(
                    ea => ea.id != item.id
                  );
                  return v;
                }
                return v;
              });
              Swal.fire("Deleted!", this.$t("swal_deleted"), "success");
            })
            .catch(err => {
              Swal.fire({
                icon: "error",
                title: this.$t("swal_error_title"),
                text: this.$t("swal_error_text")
              });
              console.log(err);
            });
        }
      });
      }
    },

    newMedicineCost(item) {
      if (this.$store.getters.checkPermission("medicine-cost-create")) {
      this.medicineCostDialogHeaderText = this.$t("Medicine Cost");
      this.medicineCostForm = {
        id: Date.now(),
        medical_treatment_id: "",
        medicine_id: "",
        amount: "",
        unit_measure: "",
        medicine_owner: "",
        description: "",
      };
      this.medicineCostDialog = true;

      if (this.$refs.medicineCostDialogForm)
        this.$refs.medicineCostDialogForm.reset();
      }
    },

    editMedicineCost(item) {
      if (this.$store.getters.checkPermission("medicine-cost-update")) {
      this.medicineCostDialogHeaderText = this.$t("edit");
      this.medicineCostForm = Object.assign({}, item);
      this.medicineCostDialog = true;

      if (this.$refs.medicineCostDialogForm)
        this.$refs.medicineCostDialogForm.resetValidation();
      }
    },

    saveMedicineCost() {
      if (this.$refs.medicineCostDialogForm.validate()) this.loading = true;
      axios
        .post(
          this.$store.state.backend_url + "api/medpunkt/medicine-costs/update",
          this.medicineCostForm
        )
        .then(res => {
          this.getList();
          this.medicineCostDialog = false;
          const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            onOpen: toast => {
              toast.addEventListener("mouseenter", Swal.stopTimer);
              toast.addEventListener("mouseleave", Swal.resumeTimer);
            }
          });
          Toast.fire({
            icon: "success",
            title: this.$t("create_update_operation")
          });
          this.loading = false;
        })
        .catch(err => {
          console.log(err);
          this.loading = false;
        });
    },

    deleteMedicineCost(item) {
      if (this.$store.getters.checkPermission("medicine-cost-delete")) {
      Swal.fire({
        title: this.$t("swal_title"),
        text: this.$t("swal_text"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: this.$t("swal_delete")
      }).then(result => {
        if (result.value) {
          axios
            .delete(
              this.$store.state.backend_url +
                "api/medpunkt/medicine-costs/delete/" +
                item.id
            )
            .then(res => {
              this.items = this.items.map(v => {
                if (v.id == item.medical_treatment_id) {
                  v.medicine_costs = v.medicine_costs.filter(
                    ea => ea.id != item.id
                  );
                  return v;
                }
                return v;
              });
              Swal.fire("Deleted!", this.$t("swal_deleted"), "success");
              this.getList();
            })
            .catch(err => {
              Swal.fire({
                icon: "error",
                title: this.$t("swal_error_title"),
                text: this.$t("swal_error_text")
              });
              console.log(err);
            });
        }
      });
    }
    },
    printPermission(item) {
      console.log('per', item);
      let mywindow = window.open('', 'PRINT');
      mywindow.document.write('<html><head><title>' + 'Ruxsatnoma ' + '</title>');
      mywindow.document.write(
        '<style>.itemSize{font-size:5pt}.text-center{text-align:center;} table > tbody > tr > td{margin:0px} @media print{@page {size: portrait} {-webkit-print-color-adjust: exact !important;}}</style>'
      );
      mywindow.document.write('</head><body >');
      mywindow.document.write("<div style='font-size: 1.2em; font-weight: 700; margin-left: 10px;'>\"UzAuto Motors\" AJ xududidan qisqa muddatga <br /> chiqish uchun shifokor RUXSATNOMASI" +  ' №' + item.id + "</div>");
      mywindow.document.write(
        '<table border="1" style="margin: 1px;border-collapse: collapse;"><thead><tr ></tr></thead><tbody>'
      );
      mywindow.document.write(
        '<tr> <td class="" style="font-size: 1.2em; padding: 3px 5px;">Bolim</td>'
      );
      mywindow.document.write(
        '<td class="" style="font-size: 1.2em; padding: 3px 5px;">'
      );

      mywindow.document.write(item.employee_department);
      mywindow.document.write(
        '</td></tr>'
      );
      mywindow.document.write(
        '<tr> <td class="" style="font-size: 1.2em; padding: 3px 5px;">Tabel</td>'
      );
      mywindow.document.write(
        '<td class="" style="font-size: 1.2em; padding: 3px 5px;">'
      );

      mywindow.document.write(item.employee.tabel);
      mywindow.document.write(
        '</td></tr>'
      );

      mywindow.document.write(
        '<tr> <td class="" style="font-size: 1.2em; padding: 3px 5px;">Ism, Sharif</td>'
      );
      mywindow.document.write(
        '<td class="" style="font-size: 1.2em; padding: 3px 5px;">'
      );

      mywindow.document.write(item.employee.lastname_uz_cyril + ' ' + item.employee.firstname_uz_cyril);
      mywindow.document.write(
        '</td></tr>'
      );

      mywindow.document.write(
        '<tr> <td class="" style="font-size: 1.2em; padding: 3px 5px;">Mutaxassis maslaxati</td>'
      );
      mywindow.document.write(
        '<td class="" style="font-size: 1.2em; padding: 3px 5px;">'
      );

      mywindow.document.write( item.diagnosis );
      mywindow.document.write(
        '</td></tr>'
      );
      mywindow.document.write(
        '<tr> <td class="" style="font-size: 1.2em; padding: 3px 5px;">Berilgan sana</td>'
      );
      mywindow.document.write(
        '<td class="" style="font-size: 1.2em; padding: 3px 5px;">'
      );

      mywindow.document.write(moment(item.created_at).format('YYYY.MM.DD [&nbsp;] HH:mm'));
      mywindow.document.write(
        '</td></tr>'
      );

      mywindow.document.write(
        '<tr> <td class="" style="font-size: 1.2em; padding: 3px 5px;">Shifokor</td>'
      );
      mywindow.document.write(
        '<td class="" style="font-size: 1.2em; padding: 3px 5px;">'
      );

      mywindow.document.write( item.created_by.username );
      mywindow.document.write(
        '</td></tr>'
      );
      

      mywindow.document.write('</tbody>  </table>');
      mywindow.document.write('</body></html>');

      mywindow.document.close(); // necessary for IE >= 10
      mywindow.focus(); // necessary for IE >= 10*/

      setTimeout(() => {
        mywindow.print();
        mywindow.close();
      }, 300);
      // mywindow.onafterprint = this.afterPrint(item);
    },
    printReferral(item) {
      let mywindow = window.open('', 'PRINT');
      mywindow.document.write('<html><head><title>' + 'Yo\'llanma' + '</title>');
      mywindow.document.write(
        '<style>.itemSize{font-size:16pt}.text-center{text-align:center;} table > tbody > tr > td{margin:10px} @media print{@page {size: portrait} {-webkit-print-color-adjust: exact !important;}}</style>'
      );
      mywindow.document.write('</head><body >');
    
      mywindow.document.write(
        '<table border="1" style="margin: 10px;border-collapse: collapse;"><thead><tr ></tr></thead><tbody>'
      );

      mywindow.document.write(
        '<tr> <td style="font-size: 1.2em; padding: 3px 5px; font-weight:700;">"UzAuto Motors" AJ YO\'LLANMASI"</td>'
      );
      mywindow.document.write(
        '<td class="" style="font-size: 1.2em; padding: 3px 5px; font-weight:700;">'
      );

      mywindow.document.write(' № ' + item.id );
      mywindow.document.write(
        '</td></tr>'
      );

      mywindow.document.write(
        '<tr> <td class="" style="font-size: 1.2em; padding: 3px 5px;">Bolim</td>'
      );
      mywindow.document.write(
        '<td class="" style="font-size: 1.2em; padding: 3px 5px;">'
      );

      mywindow.document.write(item.employee_department);
      mywindow.document.write(
        '</td></tr>'
      );

      mywindow.document.write(
        '<tr> <td class="" style="font-size: 1.2em; padding: 3px 5px;">Ism, Sharif</td>'
      );
      mywindow.document.write(
        '<td class="" style="font-size: 1.2em; padding: 3px 5px;">'
      );

      mywindow.document.write(item.employee.lastname_uz_cyril + ' ' + item.employee.firstname_uz_cyril);
      mywindow.document.write(
        '</td></tr>'
      );

      mywindow.document.write(
        '<tr> <td class="" style="font-size: 1.2em; padding: 3px 5px;">Tabel</td>'
      );
      mywindow.document.write(
        '<td class="" style="font-size: 1.2em; padding: 3px 5px;">'
      );

      mywindow.document.write(item.employee.tabel);
      mywindow.document.write(
        '</td></tr>'
      );

      mywindow.document.write(
        '<tr> <td colspan="2" style="font-size: 1.2em; padding: 3px 5px;">Davolanish uchun mutaxassis shifokorga yuborilmoqda.</td>'
      );

      mywindow.document.write(
        '</tr>'
      );


      mywindow.document.write(
        '<tr> <td class="" style="font-size: 1.2em; padding: 3px 5px;">Tashxis</td>'
      );
      mywindow.document.write(
        '<td class="" style="font-size: 1.2em; padding: 3px 5px;">'
      );

      mywindow.document.write( item.diagnosis );
      mywindow.document.write(
        '</td></tr>'
      );

      mywindow.document.write(
        '<tr> <td class="" style="font-size: 1.2em; padding: 3px 5px;">Berilgan sana</td>'
      );
      mywindow.document.write(
        '<td class="" style="font-size: 1.2em; padding: 3px 5px;">'
      );

      mywindow.document.write(moment(item.created_at).format('YYYY.MM.DD [&nbsp;] HH:mm'));
      mywindow.document.write(
        '</td></tr>'
      );

      mywindow.document.write(
        '<tr> <td class="" style="font-size: 1.2em; padding: 3px 5px;">Shifokor</td>'
      );
      mywindow.document.write(
        '<td class="" style="font-size: 1.2em; padding: 3px 5px;">'
      );

      mywindow.document.write( item.created_by.username );
      mywindow.document.write(
        '</td></tr>'
      );
      

      mywindow.document.write('</tbody>  </table>');
      mywindow.document.write('</body></html>');

      mywindow.document.close(); // necessary for IE >= 10
      mywindow.focus(); // necessary for IE >= 10*/

      setTimeout(() => {
        mywindow.print();
        mywindow.close();
      }, 300);
      // mywindow.onafterprint = this.afterPrint(item);
    },
    tableToExcel(table, name) {
      TableToExcel.convert(document.getElementById("table"));
    }
  },
  mounted() {
    this.getList();
    this.getRef();
    this.getRefDiagnosisCode();
    this.getRefHospitalDiagnosis();
    this.getRefMedicines();
  },
  created() {}
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