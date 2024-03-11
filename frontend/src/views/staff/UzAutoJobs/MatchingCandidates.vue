<template>
  <div class="fullHeight">
    <v-card
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-2">{{
          $t("Tanlangan nomzodlarni ishga moshlashtirish")
        }}</span>
        <span
          class="headerTitle mb-3"
          style="color: blue; font-style: italic; font-size: 14px"
        >
          {{
            items[0] && items[0].choice
              ? items[0].choice.staff &&
                items[0].choice.staff.department &&
                items[0].choice.staff.position
                ? items[0].choice.staff.department.branch.name +
                  " " +
                  items[0].choice.staff.department.name_uz_latin +
                  " " +
                  items[0].choice.staff.position.name_uz_latin +
                  " lavozimiga"
                : ""
              : ""
          }}
        </span>

        <div class="headerSearch d-flex align-center">
          <v-text-field
            v-model="filter.search"
            append-icon="mdi-magnify"
            class="txt_search1"
            style="width: 100px !important"
            :placeholder="$t('search')"
            @keyup.native.enter="getList"
            readonly
            dense
            hide-details
            solo
            single-line
          ></v-text-field>
          <v-btn
            class="filterBtn px-2"
            style="background: #fff; height: 34px"
            @click="getList"
            disabled
          >
            <v-icon color="#00B950" left>mdi-magnify</v-icon>Қидириш
          </v-btn>
          <v-btn
            class="filterBtn px-2"
            style="background: #fff; height: 34px"
            @click="
              getDetailExcel();
              inventory_excel = [];
            "
            disabled
          >
            <v-icon color="#107C41" left>mdi-microsoft-excel</v-icon>Юклаб олиш
          </v-btn>
          <v-btn
          :disabled="fullStatus"
          v-if="!protokolNumber"
            class="filterBtn px-2"
            style="background: #fff; height: 34px"
            @click="createDoc()"
          >
            <v-icon color="#00B950" left>mdi-file-plus</v-icon>Баённома*
          </v-btn>
          <v-btn
          v-if="protokolNumber&&itemStatus1"
            class="filterBtn px-2"
            style="background: #fff; height: 34px"
            @click="$router.push('/documents/show-only-pdf/' + protokolNumber)"
          >
            <v-icon color="#00B950" left>mdi-file-document</v-icon>Баённома
          </v-btn>
          <v-btn
          v-if="protokolNumber&&itemStatus1"
            class="filterBtn px-2"
            style="background: #fff; height: 34px"
            @click="kategoriyaDialog = true"
          >
            <v-icon color="#00B950" left>mdi-pencil</v-icon>Biriktirish*
          </v-btn>
          <v-btn
          :disabled="contractStatus"
          v-if="orderNumber!=null"
            class="filterBtn px-2"
            style="background: #fff; height: 34px"
            @click="contractDialog=true"
          >
            <v-icon color="#00B950" left>mdi-file-document</v-icon>Shartnoma*
          </v-btn>
          <v-btn
          :disabled="!contractStatus"
          v-if="!orderNumber&&itemStatus2"
            class="filterBtn px-2"
            style="background: #fff; height: 34px"
            @click="createOrder()"
          >
            <v-icon color="#00B950" left>mdi-file-document</v-icon>Buyruq*
          </v-btn>
          <v-btn
          v-if="orderNumber!=null"
            class="filterBtn px-2"
            style="background: #fff; height: 34px"
            @click="$router.push('/documents/show-only-pdf/' + orderNumber)"
          >
            <v-icon color="#00B950" left>mdi-file-document</v-icon>Buyruq
          </v-btn>
        </div>
      </v-card-title>
      <!-- :items="staffData.filter((v) => v.employee_staff.length > 0)" -->
      <v-data-table
        dense
        fixed-header
        :loading-text="$t('loadingText')"
        :no-data-text="$t('noDataText')"
        :height="screenHeight"
        :loading="loading"
        :headers="headers"
        :items="items"
        class="mainTable"
        style="width: 100%; height: 100%; border-radius: 10px"
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
          firstIcon: 'mdi-arrow-left-box',
          lastIcon: 'mdi-arrow-right-box',
          prevIcon: 'mdi-arrow-left-drop-circle-outline',
          nextIcon: 'mdi-arrow-right-drop-circle-outline',
        }"
      >
        <template v-slot:[`body.prepend`]>
          <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>
              <v-autocomplete
                v-model="filter.status"
                :items="itemMessages"
                item-text="name"
                item-value="id"
                clearable
                hide-details="auto"
                dense
                @change="getList"
              ></v-autocomplete>
            </td>
            <td>
              <v-autocomplete
                v-model="filter.status1"
                :items="itemMessages.filter((v) => v.id != 9991 && v.id != 9999)"
                item-text="name"
                item-value="id"
                clearable
                hide-details="auto"
                dense
                @change="getList"
              ></v-autocomplete>
            </td>
            <td>
              <v-autocomplete
                v-model="filter.status2"
                :items="itemMessages.filter((v) => v.id != 9991 && v.id != 9999)"
                item-text="name"
                item-value="id"
                clearable
                hide-details="auto"
                dense
                @change="getList"
              ></v-autocomplete>
            </td>
            <td>
              <v-autocomplete
                v-model="filter.status3"
                :items="itemMessages.filter((v) => v.id != 9999)"
                item-text="name"
                item-value="id"
                clearable
                hide-details="auto"
                dense
                @change="getList"
              ></v-autocomplete>
            </td>
            <td>
              <v-autocomplete
                v-model="filter.status4"
                :items="itemMessages.filter((v) => v.id != 9991 && v.id != 9999)"
                item-text="name"
                item-value="id"
                clearable
                hide-details="auto"
                dense
                @change="getList"
              ></v-autocomplete>
            </td>
            <td>
              <v-autocomplete
                v-model="filter.status5"
                :items="itemMessages.filter((v) => v.id != 9991 && v.id != 9999)"
                item-text="name"
                item-value="id"
                clearable
                hide-details="auto"
                dense
                @change="getList"
              ></v-autocomplete>
            </td>           
          </tr>
        </template>

        <template v-slot:item.number="{ item, index }" style>
          {{ from + index }}
        </template>       
        <template v-slot:item.choice_id="{ item, index }" style>
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <span v-bind="attrs" v-on="on">{{ String(item.choice.tanlov_id ).padStart(6, '0') }}</span>
            </template>
            <span>{{
              item.choice
                ? "[" +
                  item.choice.staff.department.branch.name +
                  "] " +
                  item.choice.staff.department.name_uz_latin +
                  ", " +
                  item.choice.staff.position.name_uz_latin
                : ""
            }}</span>
          </v-tooltip>
        </template>
        <template v-slot:item.vacancie_name="{ item, index }" style>
          {{
            item.vacancies.lastname_uz_latin +
            " " +
            item.vacancies.firstname_uz_latin +
            " " +
            item.vacancies.middlename_uz_latin
          }}
        </template>
        <template v-slot:item.knowledge_direction="{ item, index }" style>
          {{
            item.vacancies.knowledge_specialty +
            "(" +
            item.vacancies.knowledge_direction +
            ")" +
            item.vacancies.knowledge_name
          }}
        </template>
        <template v-slot:item.language_skills="{ item, index }" style>
          {{
            item.vacancies.language_skills_first + item.vacancies.language_skills_second
              ? item.vacancies.language_skills_first +
                ", " +
                item.vacancies.language_skills_second
              : ""
          }}
        </template>
        <template v-slot:item.knowledgeNum="{ item, index }" style>
          {{ item.vacancies.knowledge_serial + " " + item.vacancies.knowledge_number }}
        </template>
        <template v-slot:item.vacancies_status="{ item, index }" style>
          <span v-if="item.status == null" style="color: blue">
            {{ item.status == null ? "Yangi" : item.status }}
          </span>
          <v-icon color="primary" v-else-if="item.status == true">mdi-check</v-icon>
          <v-icon color="error" v-else-if="item.status == false">mdi-close</v-icon>
        </template>
        <template v-slot:item.vacancies_status1="{ item, index }" style>
          <template v-if="item.one_sorting_status === null">
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  v-bind="attrs"
                  v-on="on"
                  style="margin-left: 5px"
                  class="ma-0 pa-0 float-left"
                  text
                  x-small
                  color="error"
                  @click="
                    (rejectDialog = true),
                      (rejectID.itemId = item.id),
                      (rejectID.sort = 1)
                  "
                >
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </template>
              <span>{{ $t("Sinovdan o'ta olmadi") }}</span>
            </v-tooltip>
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  v-bind="attrs"
                  v-on="on"
                  style="margin-left: 5px"
                  class="ma-0 pa-0 float-left"
                  text
                  x-small
                  color="primary"
                  @click="handleApproveClick(item.id, 1)"
                >
                  <v-icon>mdi-check</v-icon>
                </v-btn>
              </template>
              <span>{{ $t("Sinovdan o'tdi") }}</span>
            </v-tooltip>
          </template>
          <v-icon color="primary" v-else-if="item.one_sorting_status === 1"
            >mdi-check</v-icon
          >
          <span
            v-else-if="item.one_sorting_status !== 1 && item.one_sorting_status !== null"
          >
            <v-icon color="error">mdi-close</v-icon>
            {{
              itemMessages
                .find((v) => v.id === item.one_sorting_status)
                ?.name?.slice(0, 10)
            }}
          </span>
        </template>
        <template v-slot:item.vacancies_status2="{ item, index }" style>
          <template
            v-if="
              item.two_sorting_status == null &&
              item.one_sorting_status != null &&
              item.one_sorting_status === 1
            "
          >
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  v-bind="attrs"
                  v-on="on"
                  style="margin-left: 5px"
                  class="ma-0 pa-0 float-left"
                  text
                  x-small
                  color="error"
                  @click="
                    (rejectDialog = true),
                      (rejectID.itemId = item.id),
                      (rejectID.sort = 2)
                  "
                >
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </template>
              <span>{{ $t("Sinovdan o'ta olmadi") }}</span>
            </v-tooltip>
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  v-bind="attrs"
                  v-on="on"
                  style="margin-left: 5px"
                  class="ma-0 pa-0 float-left"
                  text
                  x-small
                  color="primary"
                  @click="handleApproveClick(item.id, 2)"
                >
                  <v-icon>mdi-check</v-icon>
                </v-btn>
              </template>
              <span>{{ $t("Sinovdan o'tdi") }}</span>
            </v-tooltip>
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  v-bind="attrs"
                  v-on="on"
                  style="margin-left: 5px"
                  class="ma-0 pa-0 float-left"
                  text
                  x-small
                  color="error"
                  @click="
                    rejectCanditate(
                      (rejectID.type = 9991),
                      (rejectID.itemId = item.id),
                      (rejectID.sort = 2)
                    )
                  "
                >
                  <v-icon>mdi-select</v-icon>
                </v-btn>
              </template>
              <span>{{ $t("NA") }}</span>
            </v-tooltip>
          </template>
          <v-icon color="primary" v-else-if="item.two_sorting_status === 1"
            >mdi-check</v-icon
          >
          <span v-else-if="item.two_sorting_status === 9991">
            <v-icon color="error">mdi-select</v-icon> N/A
          </span>
          <span
            v-else-if="item.two_sorting_status !== 1 && item.two_sorting_status !== null"
          >
            <v-icon color="error">mdi-close</v-icon>
            {{
              itemMessages
                .find((v) => v.id === item.two_sorting_status)
                ?.name?.slice(0, 10)
            }}
          </span>
        </template>
        <template v-slot:item.vacancies_status3="{ item, index }" style>
          <template
            v-if="
              item.three_sorting_status == null &&
              item.two_sorting_status != null &&
              (item.two_sorting_status === 1 || item.two_sorting_status === 9991)
            "
          >
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  v-bind="attrs"
                  v-on="on"
                  style="margin-left: 5px"
                  class="ma-0 pa-0 float-left"
                  text
                  x-small
                  color="error"
                  @click="
                    (rejectDialog = true),
                      (rejectID.itemId = item.id),
                      (rejectID.sort = 3)
                  "
                >
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </template>
              <span>{{ $t("Sinovdan o'ta olmadi") }}</span>
            </v-tooltip>
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  v-bind="attrs"
                  v-on="on"
                  style="margin-left: 5px"
                  class="ma-0 pa-0 float-left"
                  text
                  x-small
                  color="primary"
                  @click="handleApproveClick(item.id, 3)"
                >
                  <v-icon>mdi-check</v-icon>
                </v-btn>
              </template>
              <span>{{ $t("Sinovdan o'tdi") }}</span>
            </v-tooltip>
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  v-bind="attrs"
                  v-on="on"
                  style="margin-left: 5px"
                  class="ma-0 pa-0 float-left"
                  text
                  x-small
                  color="error"
                  @click="
                    rejectCanditate(
                      (rejectID.type = 9991),
                      (rejectID.itemId = item.id),
                      (rejectID.sort = 3)
                    )
                  "
                >
                  <v-icon>mdi-select</v-icon>
                </v-btn>
              </template>
              <span>{{ $t("NA") }}</span>
            </v-tooltip>
          </template>
          <v-icon color="primary" v-else-if="item.three_sorting_status === 1"
            >mdi-check</v-icon
          >
          <span v-else-if="item.three_sorting_status === 9991">
            <v-icon color="error">mdi-select</v-icon> N/A
          </span>
          <span
            v-else-if="
              item.three_sorting_status !== 1 && item.three_sorting_status !== null
            "
          >
            <v-icon color="error">mdi-close</v-icon>
            {{
              itemMessages
                .find((v) => v.id === item.three_sorting_status)
                ?.name?.slice(0, 10)
            }}
          </span>
        </template>

        <template v-slot:item.vacancies_status4="{ item, index }" style>
          <template
            v-if="
              item.four_sorting_status == null &&
              item.three_sorting_status != null &&
              (item.three_sorting_status === 1 || item.three_sorting_status === 9991)
            "
          >
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  v-bind="attrs"
                  v-on="on"
                  style="margin-left: 5px"
                  class="ma-0 pa-0 float-left"
                  text
                  x-small
                  color="error"
                  @click="
                    (rejectDialog = true),
                      (rejectID.itemId = item.id),
                      (rejectID.sort = 4)
                  "
                >
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </template>
              <span>{{ $t("Sinovdan o'ta olmadi") }}</span>
            </v-tooltip>
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  v-bind="attrs"
                  v-on="on"
                  style="margin-left: 5px"
                  class="ma-0 pa-0 float-left"
                  text
                  x-small
                  color="primary"
                  @click="handleApproveClick(item.id, 4)"
                >
                  <v-icon>mdi-check</v-icon>
                </v-btn>
              </template>
              <span>{{ $t("Sinovdan o'tdi") }}</span>
            </v-tooltip>
          </template>
          <v-icon color="primary" v-else-if="item.four_sorting_status === 1"
            >mdi-check</v-icon
          >
          <span
            v-else-if="
              item.four_sorting_status !== 1 && item.four_sorting_status !== null
            "
          >
            <v-icon color="error">mdi-close</v-icon>
            {{
              itemMessages
                .find((v) => v.id === item.four_sorting_status)
                ?.name?.slice(0, 10)
            }}
          </span>
        </template>
        <template v-slot:item.vacancies_status5="{ item, index }" style>
          <template
            v-if="
              item.five_sorting_status == null &&
              item.four_sorting_status != null &&
              item.four_sorting_status === 1
            "
          >
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  v-bind="attrs"
                  v-on="on"
                  style="margin-left: 5px"
                  class="ma-0 pa-0 float-left"
                  text
                  x-small
                  color="error"
                  @click="
                    (rejectDialog = true),
                      (rejectID.itemId = item.id),
                      (rejectID.sort = 5)
                  "
                >
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </template>
              <span>{{ $t("Sinovdan o'ta olmadi") }}</span>
            </v-tooltip>
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  v-bind="attrs"
                  v-on="on"
                  style="margin-left: 5px"
                  class="ma-0 pa-0 float-left"
                  text
                  x-small
                  color="primary"
                  @click="handleApproveClick(item.id, 5, item)"
                >
                  <v-icon>mdi-check</v-icon>
                </v-btn>
              </template>
              <span>{{ $t("Sinovdan o'tdi") }}</span>
            </v-tooltip>
          </template>
          <v-icon color="primary" v-else-if="item.five_sorting_status === 1"
            >mdi-check</v-icon
          >
          <span
            v-else-if="
              item.five_sorting_status !== 1 && item.five_sorting_status !== null
            "
          >
            <v-icon color="error">mdi-close</v-icon>
            {{
              itemMessages
                .find((v) => v.id === item.five_sorting_status)
                ?.name?.slice(0, 10)
            }}
          </span>
        </template>
      </v-data-table>
    </v-card>
    <v-dialog v-model="loading" width="300" hide-overlay>
      <v-card color="primary" dark>
        <v-card-text>
          {{ $t("loadingText") }}
          <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="downloadExcel"
      hide-overlay
      persistent
      width="300"
      @keydown.esc="downloadExcel = false"
    >
      <v-card class="pa-3" style="border-radius: 1px">
        <v-card-text>
          <span class="dialog-head_title">Excel га юклаб олиш</span>
          <v-divider class="ma-0 pa-0" style="border-color: #dce5ef"></v-divider>
          <v-btn
            color="success"
            @click="downloadExcel = false"
            style="text-transform: none; border-radius: 5px"
            class="ma-3 pa-3"
            text
            right
            small
            dark
          >
            <download-excel :data="application_excel" :name="'RP_' + today + '.xls'">
              <span style="color: #4caf50">{{ $t("download") }}</span>
              <v-icon color="success" height="20">mdi-download</v-icon>
            </download-excel>
          </v-btn>
          <v-btn
            color="red"
            @click="downloadExcel = false"
            style="text-transform: none; border-radius: 5px"
            class="ma-3 pa-3"
            text
            right
            small
            dark
            ><span style="color: red">{{ $t("close") }}</span>
            <v-icon color="red" height="20">mdi-close</v-icon>
          </v-btn>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="rejectDialog"
      hide-overlay
      persistent
      width="300"
      @keydown.esc="rejectDialog = false"
    >
      <v-card class="pa-3" style="border-radius: 1px">
        <v-card-text>
          <v-btn
            color="green"
            outlined
            x-small
            fab
            class="pa-1"
            @click="rejectListDialog = true"
          >
            <v-icon>mdi-plus</v-icon>
          </v-btn>
          <span class="dialog-head_title pa-0 ml-1">Bekor bo'lish sababi</span>
          <v-spacer></v-spacer>
          <v-divider class="ma-0 pa-1" style="border-color: #dce5ef"></v-divider>
          <v-autocomplete
            :items="itemMessages.filter((v) => v.id > 1 && v.id != 9999)"
            v-model="rejectID.type"
            item-text="name"
            item-value="id"
            clearable
            solo
            dense
            hide-details="auto"
            @change="getList"
          ></v-autocomplete>
          <v-divider class="ma-0 pa-0" style="border-color: #dce5ef"></v-divider>
          <v-btn
            color="success"
            style="text-transform: none; border-radius: 5px"
            class="ma-3 pa-3"
            text
            right
            small
            dark
            v-if="rejectID.type !== null"
            @click="rejectCanditate(), (rejectDialog = false)"
          >
            <span style="color: success">{{ $t("OK") }}</span>
            <v-icon color="success" height="20">mdi-check</v-icon>
          </v-btn>
          <v-btn
            @click="rejectDialog = false"
            color="red"
            style="text-transform: none; border-radius: 5px"
            class="ma-3 pa-3"
            text
            right
            small
            dark
            ><span style="color: red">{{ $t("close") }}</span>
            <v-icon color="red" height="20">mdi-close</v-icon>
          </v-btn>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="rejectListDialog"
      hide-overlay
      persistent
      width="300"
      @keydown.esc="rejectListDialog = false"
    >
      <v-card class="pa-3" style="border-radius: 1px">
        <v-card-text>
          <span class="dialog-head_title pa-0 ml-1">Sababni kiritish</span>
          <v-divider class="ma-0 pa-1" style="border-color: #dce5ef"></v-divider>
          <v-text-field
            v-model="filter.rejList"
            clearable
            solo
            dense
            hide-details="auto"
          ></v-text-field>
          <v-divider class="ma-0 pa-0" style="border-color: #dce5ef"></v-divider>
          <v-btn
            color="success"
            style="text-transform: none; border-radius: 5px"
            class="ma-3 pa-3"
            text
            right
            small
            dark
            :disabled="filter.rejList == ''"
            @click="rejectListAdd(), (rejectListDialog = false)"
          >
            <span style="color: success">{{ $t("OK") }}</span>
            <v-icon color="success" height="20">mdi-check</v-icon>
          </v-btn>
          <v-btn
            @click="rejectListDialog = false"
            color="red"
            style="text-transform: none; border-radius: 5px"
            class="ma-3 pa-3"

            text
            right
            small
            dark
            ><span style="color: red">{{ $t("close") }}</span>
            <v-icon color="red" height="20">mdi-close</v-icon>
          </v-btn>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="contractDialog"
      hide-overlay
      persistent
      width="300"
      @keydown.esc="contractDialog = false"
    >
      <v-card class="pa-3" style="border-radius: 1px">
        <v-card-text>
          <span class="dialog-head_title pa-0 ml-1">Mexnat shartnoma sanasi</span>
          <v-divider class="ma-0 pa-1" style="border-color: #dce5ef"></v-divider>
          <v-text-field
            v-model="contractDate"
            placeholder="2023 yilning 13  noyabr"
            clearable
            :rules="[v => v.length >= 16 || 'Eng kam belgi 16 dona']"
            counter="16"
            solo
            dense
            hide-details="auto"
          ></v-text-field>
          <v-divider class="ma-0 pa-0" style="border-color: #dce5ef"></v-divider>
          <v-btn
            color="success"
            style="text-transform: none; border-radius: 5px"
            class="ma-3 pa-3"
            text
            right
            small
            dark
            v-if="contractDate?contractDate.length>16:false"
            @click="createShartnoma(), (contractDialog = false)"
          >
            <span style="color: success">{{ $t("OK") }}</span>
            <v-icon color="success" height="20">mdi-check</v-icon>
          </v-btn>
          <v-btn
            @click="contractDialog = false"
            color="red"
            style="text-transform: none; border-radius: 5px"
            class="ma-3 pa-3"
            text
            right
            small
            dark
            ><span style="color: red">{{ $t("close") }}</span>
            <v-icon color="red" height="20">mdi-close</v-icon>
          </v-btn>
        </v-card-text>
      </v-card>
    </v-dialog>
    <!-- ///////////////////////// -->
    <v-dialog
      v-model="kategoriyaDialog"
      fullscreen
      persistent
      hide-overlay
      transition="dialog-bottom-transition"
      ref="valval"
    >
      <v-card>
        <v-toolbar dark color="primary">
          <v-btn icon dark @click="kategoriyaDialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
          <v-toolbar-title
            >Tanlangan nomzodlarga Kategoriga va Tabel raqam biriktirish</v-toolbar-title
          >          
          </v-toolbar-items>
        </v-toolbar>
        <!-- ///////////// -->
        <v-card style="border: 1px solid #337ab7">
          <v-container fluid class="pa-1 grey lighten-5">
            <v-simple-table>
              <thead class="header">
                <tr>
                  <th style="max-width: 30px; white-space: normal; text-align: center">
                    {{ $t("#") }}
                  </th>
                  <th style="white-space: normal; text-align: center">
                    {{ $t("Tanlov Index") }}
                  </th>
                  <th style="width: 120px; white-space: normal; text-align: center">
                    {{ $t("Tab N*") }}
                  </th>
                  <th style="white-space: normal; text-align: center">
                    {{ $t("Familiya") }}
                  </th>
                  <th style="white-space: normal; text-align: center">
                    {{ $t("Ismi") }}
                  </th>
                  <th style="white-space: normal; text-align: center">
                    {{ $t("Sharifi") }}
                  </th>
                  <th style="white-space: normal; text-align: center">
                    {{ $t("Tashkiliy tuzilma") }}
                  </th>
                  <th style="white-space: normal; text-align: center">{{ $t("Kod") }}</th>                  
                  <th style="white-space: normal; text-align: center">
                    {{ $t("Lavozim/Kasb") }}
                  </th>
                  <th style="white-space: normal; text-align: center">
                    {{ $t("Diapazon") }}
                  </th>
                  <th style="width: 120px;white-space: normal; text-align: center">
                    {{ $t("Kategoriya") }}
                  </th>
                  <th style="width: 100px;white-space: normal; text-align: center">{{ $t("Smena") }}</th>
                  <th style="max-width: 30px; white-space: normal; text-align: center">
                    {{ $t("R") }}
                  </th>
                  <th style="max-width: 30px; white-space: normal; text-align: center">
                    {{ $t("S") }}
                  </th>
                  <th style="max-width: 30px; white-space: normal; text-align: center">
                    {{ $t("H") }}
                  </th>
                  <th style="max-width: 30px; white-space: normal; text-align: center">
                    {{ $t("K") }}
                  </th>
                  <th style="width: 100px; white-space: normal; text-align: center">
                    {{ $t("D") }}
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(item, index) in items.filter((v) => v.status === true)"
                  :key="index"
                >
                  <td style="white-space: normal; text-align: center">
                    {{ index + 1 }}
                  </td>
                  <td style="white-space: normal; text-align: center">
                    {{ String(item.choice.tanlov_id ).padStart(6, '0') }}
                  </td>
                  <td>
                    <v-text-field
                        v-if="$store.getters.checkPermission('candidate-tabel-give')"
                        class="txt_search1"
                        v-model="item.tabelNumber"
                        clearable
                        solo
                        dense
                        hide-details
                        @keyup.native.enter="saveInfo(item.id, 1, item.tabelNumber)" 
                    ></v-text-field>
                    <v-text-field
                        v-else
                        class="txt_search1"
                        v-model="item.tabelNumber"
                        clearable
                        solo
                        dense
                        disabled
                        hide-details
                    ></v-text-field>
                  </td>
                  <td style="white-space: normal; text-align: left">
                    {{
                      item.vacancies.lastname_uz_latin 
                    }}
                  </td>
                  <td style="white-space: normal; text-align: left">
                    {{                    
                      item.vacancies.firstname_uz_latin 
                    }}
                  </td>
                  <td style="white-space: normal; text-align: left">
                    {{
                      item.vacancies.middlename_uz_latin
                    }}
                  </td>
                  <td style="white-space: normal; text-align: center">
                    {{ item.choice.staff.department.name_uz_latin }}
                  </td>
                  <td style="white-space: normal; text-align: center">
                    {{ item.choice.staff.department.department_code }}
                  </td>                 
                  <td style="white-space: normal; text-align: center">
                    {{ item.choice.staff.position.name_uz_latin }}
                  </td>
                  <td style="white-space: normal; text-align: center">
                    {{ item.choice.staff.range.code }}
                  </td>                  
                  <td style="white-space: normal; text-align: center">
                    <v-text-field
                    v-if="$store.getters.checkPermission('candidate-kategoriya-give')"
                      class="txt_search1"
                      v-model="item.categorie"
                      clearable
                      solo
                      dense
                      hide-details
                      @keyup.native.enter="saveInfo(item.id, 2, item.categorie)"
                    ></v-text-field>
                    <v-text-field
                    v-else
                      class="txt_search1"
                      v-model="item.categorie"
                      clearable
                      solo
                      dense
                      hide-details
                      disabled
                    ></v-text-field>
                  </td>
                  <td style="white-space: normal; text-align: center">
                    <v-text-field
                    v-if="$store.getters.checkPermission('candidate-kategoriya-give')"
                      class="txt_search1"
                      v-model="item.shift"
                      clearable
                      solo
                      dense
                      hide-details
                      @keyup.native.enter="saveInfo(item.id, 4, item.shift)"
                    ></v-text-field>
                    <v-text-field
                    v-else
                      class="txt_search1"
                      v-model="item.shift"
                      clearable
                      solo
                      dense
                      hide-details
                      disabled
                    ></v-text-field>
                  </td>
                          <template v-if="item.choice.staff_coefficients.length > 0">
                                    <td style="white-space: normal; text-align: center">
                                <template v-for="itemC in item.choice.staff_coefficients">
                                        {{ itemC.coefficient.code === 'R' ? itemC.coefficient.protsent + '%' : '' }}
                                </template>
                                    </td>
                        </template>
                          <template v-if="item.choice.staff_coefficients.length > 0">
                                    <td style="white-space: normal; text-align: center">
                                <template v-for="itemC in item.choice.staff_coefficients">
                                        {{ itemC.coefficient.code === 'S' ? itemC.coefficient.protsent + '%' : '' }}
                                </template>
                                    </td>
                        </template>
                          <template v-if="item.choice.staff_coefficients.length > 0">
                                    <td style="white-space: normal; text-align: center">
                                <template v-for="itemC in item.choice.staff_coefficients">
                                        {{ itemC.coefficient.code === 'H' ? itemC.coefficient.protsent + '%' : '' }}
                                </template>
                                    </td>
                        </template>
                          <template v-if="item.choice.staff_coefficients.length > 0">
                                    <td style="white-space: normal; text-align: center">
                                <template v-for="itemC in item.choice.staff_coefficients">
                                        {{ itemC.coefficient.code === 'K' ? itemC.coefficient.protsent + '%' : '' }}
                                </template>
                                    </td>
                        </template>
                  <td style="white-space: normal; text-align: center">
                    <v-text-field
                    v-if="$store.getters.checkPermission('candidate-kategoriya-give')"
                      class="txt_search1"
                      v-model="item.coefficient"
                      clearable
                      solo
                      dense
                      :min="1"
                      :max="3"
                      hide-details="auto"
                      @keyup.native.enter="saveInfo(item.id, 3, item.coefficient)"
                    ></v-text-field>
                    <v-text-field
                    v-else
                      class="txt_search1"
                      v-model="item.coefficient"
                      clearable
                      solo
                      dense
                      :min="1"
                      :max="3"
                      hide-details="auto"
                      disabled
                    ></v-text-field>
                  </td>
                </tr>
              </tbody>
            </v-simple-table>
          </v-container>
        </v-card>
        <!-- ///////////// -->
      </v-card>
    </v-dialog>
    <!-- ///////////////////////// -->
  </div>
</template>
<script>
const axios = require("axios").default;
const moment = require("moment");
import Swal from "sweetalert2";
export default {
  data: () => ({
    dateRules: [
      (v) =>
        !v ||
        /^\d{4}[\-\-](0?[1-9]|1[012])[\-\-](0?[1-9]|[12][0-9]|3[01])$/.test(v) ||
        "Date must be valid(YYYY-DD-MM)",
    ],
    items: [],
    staffData: [],
    itemMessages: [
      { id: 9999, name: "Янги", status: 1, type: 0 },
      { id: 9991, name: "NA", status: 1, type: 0 },
      { id: 0, name: "Muvaffaqiyatsiz", status: 1, type: 2 },
    ],
    filter: {},
    loading: false,
    downloadExcel: false,
    rejectDialog: false,
    rejectListDialog: false,
    contractDialog: false,    
    kategoriyaDialog: false,    
    contractStatus: false,    
    fullStatus: false,    
    protokolNumber: null,
    tabelStatus: null,
    kategoriyaStatus: null,
    itemStatus1: false,
    itemStatus2: false,
    orderNumber: null,
    contractDate: '',
    rejectID: {
      itemId: null,
      sort: null,
      type: null,
    },
    application_excel: [],
    choiceItem: [],
    today: moment().format("YYYY-MM-DD"),
    server_items_length: null,
    dataTableOptions: { page: 1, itemsPerPage: 50 },
    server_items_length: -1,
    page: 1,
    from: 0,
  }),
  computed: {
    screenHeight() {
      return window.innerHeight - 205;
    },
    headers() {
      return [
        {
          text: "#",
          value: "number",
          align: "center",
          width: 30,
          class: "blue-grey lighten-5 textCenter",
        },
        {
          text: this.$t("Tanlov Index"),
          value: "choice_id",
          class: "blue-grey lighten-5 textCenter",
          width: 50,
        },
        {
          text: this.$t("ID №"),
          value: "vacancies.uzJobPersonID",
          class: "blue-grey lighten-5 textCenter",
        },
        {
          text: this.$t("F.I.Sh."),
          value: "vacancie_name",
          class: "blue-grey lighten-5 textCenter",
        },
        {
          text: this.$t("Ma`lumoti"),
          value: "vacancies.knowledge_type",
          class: "blue-grey lighten-5 textCenter",
        },
        {
          text: this.$t("Yo`nalishi"),
          value: "vacancies.knowledge_direction",
          class: "blue-grey lighten-5 textCenter",
        },
        {
          text: this.$t("Ish tajribasi"),
          value: "vacancies.experience",
          class: "blue-grey lighten-5 textCenter",
        },
        {
          text: this.$t("Xorijiy til"),
          value: "language_skills",
          class: "blue-grey lighten-5 textCenter",
        },
        {
          text: this.$t("Sertifikat/ Diplom"),
          value: "knowledgeNum",
          class: "blue-grey lighten-5 textCenter",
        },
        {
          text: this.$t("Passport Seria"),
          value: "vacancies.passport_serial",
          class: "blue-grey lighten-5 textCenter",
        },
        {
          text: this.$t("Passport Number"),
          value: "vacancies.passport_number",
          class: "blue-grey lighten-5 textCenter",
        },
        {
          text: this.$t("Passport Number"),
          value: "vacancies.passport_number",
          class: "blue-grey lighten-5 textCenter",
        },
        {
          text: this.$t("Viloyati"),
          value: "vacancies.passport_region",
          class: "blue-grey lighten-5 textCenter",
        },
        {
          text: this.$t("Tuman/ Shaxat"),
          value: "vacancies.passport_town",
          class: "blue-grey lighten-5 textCenter",
        },
        {
          text: this.$t("Tug`ilgan sana"),
          value: "vacancies.born_date",
          class: "blue-grey lighten-5 textCenter",
        },
        {
          text: this.$t("Electron pochtasi"),
          value: "vacancies.email",
          class: "blue-grey lighten-5 textCenter",
        },
        {
          text: this.$t("Telefon raqami"),
          value: "vacancies.tel_first",
          class: "blue-grey lighten-5 textCenter",
        },
        {
          text: this.$t("Tanlov xolati"),
          value: "vacancies_status",
          class: "blue-grey lighten-5 textCenter",
          width: 50,
        },
        {
          text: this.$t("Salash"),
          value: "vacancies_status1",
          class: "blue-grey lighten-5 textCenter",
          width: 120,
        },
        {
          text: this.$t("Test"),
          value: "vacancies_status2",
          class: "blue-grey lighten-5 textCenter",
          width: 120,
        },
        {
          text: this.$t("Sport"),
          value: "vacancies_status3",
          class: "blue-grey lighten-5 textCenter",
          width: 140,
        },
        {
          text: this.$t("Baxolash/Suxbat"),
          value: "vacancies_status4",
          class: "blue-grey lighten-5 textCenter",
          width: 120,
        },
        {
          text: this.$t("Kommisiya"),
          value: "vacancies_status5",
          class: "blue-grey lighten-5 textCenter",
          width: 120,
        },       
        
      ].filter(
        (v) =>
          v.value != "actions" ||
          this.$store.getters.checkPermission("staff-update") ||
          this.$store.getters.checkPermission("staff-delete")
      );
    },
  },
  methods: {
    updatePage($event) {
      this.getList();
    },
    updatePerPage($event) {
      this.getList();
    },
    handleApproveClick(itemId, sort, item) {
      this.rejectID.itemId = itemId;
      this.rejectID.sort = sort;
      this.rejectID.type = 1;
      /////
      if (sort === 5) {
        Swal.fire({
          title: "Yakuniy hulosani tanlang",
          text: `${item?.choice?.staff?.department?.name_uz_latin || ""}, ${
            item?.choice?.staff?.position?.name_uz_latin || ""
          } lavozimiga tavsif etiladimi?`,
          showCancelButton: true,
          confirmButtonText: "Tasdiqlash",
          icon: "warning",
          customClass: {
            actions: "my-actions",
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger",
            denyButton: "order-3",
          },
        }).then((result) => {
          if (result.isConfirmed) {
            Swal.fire("Saved!", "", "success");
            this.rejectCanditate();
          } else if (result.isDenied) {
            Swal.fire("Changes are not saved", "", "info");
          }
        });
      } else {
        this.rejectCanditate();
      }
      /////
    },
    createShartnoma() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/candidates/contract/create",{id:this.filter.selectioID,contractDate:this.contractDate})
        .then((res) => {
          // this.$router.push("/document/" + res.data.pdf_file_name);
          this.contractDate='';
        });
      this.loading = false;
    },   
    createDoc() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/candidates/document/create",{id:this.filter.selectioID,})
        .then((res) => {
          this.$router.push("/document/" + res.data.pdf_file_name);
        });
      this.loading = false;
    },   
    createOrder() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/candidates/order/create",{id:this.filter.selectioID,})
        .then((res) => {
          this.$router.push("/document/" + res.data.pdf_file_name);
        });
      this.loading = false;
    },   
    saveInfo(item, type, info) {       
      if (info!=null&&(type==1&&info.length==4)||(type==2&&info.length==4)||(type==4&&info.length==1)||(type==3&&info.length>1||type==3&&info.lenght<5))
      {this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/candidate/set-information", {
          id: item,
          type: type,
          info: info,
        })
        .then((res) => {
          console.log(res.data);
          if (res.data == 1) {
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
          } 
          if (res.data == 2){
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
              icon: "error",
              title: this.$t("Tabel raqam mavjud !!!"),
            });
          }
          else if (res.data == 3) {
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
              icon: "error",
              title: this.$t("Tabel raqam kiritildi qayta tekshirib ko`ring !!!"),
            });
          }
        });
      this.loading = false;}
      else
      {
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
            icon: "error",
            title: this.$t("Ma`lumotlarni kiritishda xatolikka yo`l qo`yildi, qayta kiring!!!"),
          });
      }
    },
    protokolAdd(item) {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/candidate/set-document", {
          type: item,
          filter: this.filter.protokolNumber,
          idItem: this.$route.params.selectionID,
        })
        .then((res) => {
          this.filter.protokolNumber = [];
          this.getList();
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
        });
      this.loading = false;
    },
    rejectListAdd() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/candidate/set-reject", {
          filter: this.filter.rejList,
        })
        .then((res) => {
          this.filter.rejList = [];
          this.getList();
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
        });
      this.loading = false;
    },
    rejectCanditate() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/candidate/reject", {
          item: this.rejectID.itemId,
          sort: this.rejectID.sort,
          type: this.rejectID.type,
        })
        .then((res) => {
          this.getList();
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
        });
      this.loading = false;
      this.rejectID.type = null;
    },
    getList() {
      this.filter.selectioID = this.$route.params.selectionID;
      this.filter.excells = 0;
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/matching-candidates", {
          pagination: this.dataTableOptions,
          filter: this.filter,
          language: this.$i18n.locale,
        })
        .then((response) => {
          this.items = response.data.candidate.data;
          this.from = response.data.candidate.from;
          this.server_items_length = response.data.candidate.total;
          this.choiceItem = response.data.vacancy;
          this.choiceItem = response.data.vacancy;
          // this.itemMessages = response.data.messages;
          response.data.messages.forEach((element) => {
            let i = 0;
            this.itemMessages.push({
              id: element.id,
              name: element.name,
              style: element.style,
              type: element.type,
            });
          });

          if (this.items.length > 0) {
            this.protokolNumber = this.items[0].choice?.tanlov?.protocol_number;
            this.orderNumber = this.items[0].choice?.tanlov?.order_number;
          } else {
            this.protokolNumber = false;
            this.orderNumber = false;
          }
          const hasNullStatus = this.items.some((item) => item.status === null);
          const hasNullTabel = this.items.some((item) => item.tabelNumber === null);
          const hasNullKategoriya = this.items.some((item) => item.categorie === null);
          const hasNullShift = this.items.some((item) => item.shift === null);
          const filteredItems = this.items.filter(item =>item.status === true);
          this.contractStatus = filteredItems.some((item) => item.contract_id != null);
          //candidate-tabel-give
          //candidate-kategoriya-give
          this.fullStatus=hasNullStatus;          
          this.tabelStatus=hasNullTabel;
          this.kategoriyaStatus=hasNullKategoriya;
          if (!hasNullStatus) {
            if (this.protokolNumber != false) {
              this.itemStatus1 = true;
            }
            if (this.orderNumber==null&&hasNullTabel&&hasNullKategoriya&&hasNullShift) {
              this.itemStatus2 = true;
            }
          } else {
            this.itemStatus1 = false;
            this.itemStatus2 = false;
          }          
          // console.log(
          //   // '\nhasNullStatus',hasNullStatus,
          //   // '\nprotokolNumber',this.protokolNumber,
          //   '\norderNumber',this.orderNumber==null,
          //   '\nhasNullTabel',hasNullTabel,
          //   '\nhasNullKategoriya',hasNullKategoriya,
          //   // '\nitemStatus1',this.itemStatus1,
          //   // '\nitemStatus2',this.itemStatus2,
          // );
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getDetailExcel() {
      let new_array = [];
      this.application_excel = [];
      this.filter.excells = 1;
      this.loading = true;
      this.dataTableOptions.itemsPerPage = 500;
      axios
        .post(this.$store.state.backend_url + "api/employe-staff/full", {
          filter: this.filter,
          pagination: this.dataTableOptions,
          language: this.$i18n.locale,
        })
        // this.staffData = response.data.data.filter((v) => v.employee_staff.length > 0);
        .then((response) => {
          response.data.map((v, index) => {
            new_array.push({
              "№": index + this.dataTableOptions.page,
              // ID: v.ID,
              Branch: v.Branch,
              FunctionalName: v.FunctionalName,
              FunctionalCode: v.FunctionalCode,
              DepartmentName: v.DepartmentName,
              DepartmentCode: v.DepartmentCode,
              PositionName: v.PositionName,
              PositionCode: v.PositionCode,
              Status: v.Status,
              RangeName: v.RangeName,
              RangeCode: v.RangeCode,
              personalType: v.personalType,
              expenceType: v.expenceType,
              firstname: v.firstname,
              lastname: v.lastname,
              middlename: v.middlename,
              bornDate: v.bornDate,
              Category: v.Category,
              tabel: v.tabel,
              Shift: v.Shift,
              experience: v.experience,
              firstWorkDate: v.firstWorkDate,
              enterOrderNumber: v.enterOrderNumber,
              enterOrderDate: v.enterOrderDate,
              BP: v.BP,
              TS: v.TS,
              XS: v.XS,
              AS: v.AS,
              BPV: v.BPV,
              TSV: v.TSV,
              Coeff: v.Coeff,
              WB: v.WB,
              DirInDir: v.DirInDir,
              Coeff: v.Coeff,
              WB: v.WB,
              DirInDir: v.DirInDir,
            });
          });
          this.application_excel = this.application_excel.concat(new_array);
          if (response.data.length == 1000) {
            this.getDetailExcel(++page);
          } else {
            this.loading = false;
            this.downloadExcel = true;
          }
          this.filter.excells = 0;
          this.dataTableOptions.itemsPerPage = 50;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
  },
  mounted() {
    this.getList();
  },
};
</script>

<style scoped>
.staff-s table > thead > tr > th {
  white-space: normal;
  display: block;
  display: -webkit-box;
  max-width: 100%;
  height: 43px;
  margin: 0 auto;
  font-size: 14px;
  line-height: 1;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}
.fullHeight {
  height: calc(100% - 100px);
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
.btn_class {
  padding: 20px;
  margin: 0px 0px -19px 20px;
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
.txt_search1 {
  border: 1px solid #e6e6e6;
  box-shadow: none;
  max-height: 100%;
  overflow: hidden;
  border-radius: 0px 0px 0px 0px;
  color: #212529;
  font-size: 14px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.txt_search2 {
  border: 1px solid #e6e6e6;
  box-shadow: none;
  max-height: 100%;
  overflow: hidden;
  border-radius: 0px 0px 0px 0px;
  color: #212529;
  width: 50px;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
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
.textCenter {
  text-align: center;
}
.v-dialog > .v-card > .v-card__text {
  padding: 0px 0px 0px 0px;
}
.my-actions {
  margin: 2em 2em 0;
}

.order-1 {
  order: 1;
}

.order-2 {
  order: 2;
}

.order-3 {
  order: 3;
}

.right-gap {
  margin-right: auto;
}
</style>
