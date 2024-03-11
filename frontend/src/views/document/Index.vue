<template>
  <div class="fullHeight">
    <v-card
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span v-if="filter.menu_item == 'cancel'" class="headerTitle mb-2">{{
          $t("document.cancels")
        }}</span>
        <span v-else-if="filter.menu_item == 'outbox'" class="headerTitle mb-2">{{
          $t("document.outboxs")
        }}</span>
        <span v-else-if="filter.menu_item == 'draft'" class="headerTitle mb-2">{{
          $t("document.drafts")
        }}</span>
        <span v-else-if="filter.menu_item == 'expected'" class="headerTitle mb-2">{{
          $t("document.expected")
        }}</span>
        <span v-else-if="filter.menu_item == 'tarkibiy-t'" class="headerTitle mb-2">{{
          $t("tarkibiy_tuzilma")
        }}</span>
        <span v-else-if="filter.menu_item == 'lavozim-y'" class="headerTitle mb-2">{{
          $t("lavozim_yuriqnomasi")
        }}</span>
        <span v-else-if="filter.menu_item == 'kasbiy-y'" class="headerTitle mb-2">{{
          $t("kasbiy_yuriqnomasi")
        }}</span>
        <span v-else-if="filter.menu_item == 'akt'" class="headerTitle mb-2">{{
          $t("AKT")
        }}</span>
        <span v-else-if="filter.menu_item == 'akt-cancel'" class="headerTitle mb-2">{{
          $t("Cancelled Acts")
        }}</span>
        <span v-else-if="filter.menu_item == 'in_out'" class="headerTitle mb-2">{{
          $t("in_out")
        }}</span>
        <span v-else-if="filter.menu_item == 'annulirovan'" class="headerTitle mb-2">{{
          $t("annulirovan")
        }}</span>
        <span v-else-if="filter.menu_item == 'nazorat'" class="headerTitle mb-2">{{
          $t("notification.nazorat")
        }}</span>
        <span v-else class="headerTitle mb-2"></span>
        <div class="headerSearch d-flex align-center">
          <v-text-field
            class="txt_search1"
            v-model="filter.content"
            prepend-inner-icon="mdi-magnify"
            style="width: 100px !important"
            :placeholder="$t('searchInText')"
            dense
            hide-details
            clearable
            solo
            @keyup.native.enter="getFilter()"
          ></v-text-field>
          <v-text-field
            class="txt_search1"
            v-model="filter.content_table"
            style="width: 100px !important; border-radius: 0px; border-left: 0px"
            :placeholder="$t('searchInTable')"
            @keyup.native.enter="getFilter()"
            dense
            hide-details
            clearable
            solo
          ></v-text-field>
          <v-text-field
            v-if="filter.menu_item != 'akt'"
            v-model="filter.tabel"
            class="txt_search1"
            style="width: 100px !important; border-radius: 0px; border-left: 0px"
            :placeholder="$t('employee.tabel')"
            @keyup.native.enter="filter.tabel.length == 4 ? getFilter() : ''"
            dense
            hide-details
            multiple
            clearable
            solo
          ></v-text-field>
          <v-autocomplete
            v-model="filter.reaction_status"
            v-if="['inbox', 'outbox'].includes(filter.menu_item)"
            :items="reaction_status"
            outlined
            dense
            :label="$t('document.reaction_status')"
            class="reaction_status"
            multiple
            hide-details
            style="max-width: 500px; min-width: 466px; border-radius: 0px"
            @change="getFilter()"
          >
            <template v-slot:selection="{ item }">
              <v-chip
                :class="
                  item.value == 1
                    ? 'success'
                    : item.value == 2
                    ? 'error'
                    : item.value == 3
                    ? 'deep-purple'
                    : item.value == 4
                    ? 'orange lighten-1'
                    : ''
                "
                x-small
                :dark="item.reaction_status == 0 ? false : true"
                class="ma-0 mr-1 px-1"
                >{{ item.text }}</v-chip
              >
            </template>
            <template v-slot:item="{ item }">
              <v-list-item-content>
                <v-list-item-title v-text="item.text"></v-list-item-title>
              </v-list-item-content>
            </template>
          </v-autocomplete>
          <v-autocomplete
            v-model="filter.staff_id"
            v-if="$route.params.menu_item == 'archive'"
            :items="staffs"
            :search-input.sync="search"
            :loading="isLoading"
            outlined
            dense
            :label="$t('position.index')"
            class="reaction_status"
            hide-details
            style="max-width: 520px; min-width: 466px; border-radius: 0px"
            @change="getFilter()"
            @keyup="getStaffs()"
            item-value="id"
            item-text="search"
            clearable
          >
            <template v-slot:selection="{ item }" style="max-width: 150px">
              {{ item.department_code + " " + item["department_name_" + $i18n.locale] }}
            </template>
            <template v-slot:item="{ item }">
              <v-list-item-content>
                <v-list-item-title>
                  {{
                    item.department_code + " " + item["department_name_" + $i18n.locale]
                  }}
                </v-list-item-title>
                <v-list-item-subtitle>
                  {{ item["position_name_" + $i18n.locale] }}
                </v-list-item-subtitle>
              </v-list-item-content>
            </template>
          </v-autocomplete>
          <v-btn class="filterBtn px-2" style="background: #fff; height: 34px">
            <v-icon color="#00B950" left>mdi-filter-outline</v-icon>Фильтр
          </v-btn>
          <!-- <v-btn class="filterBtn px-2" style="background: #fff; height: 34px;">
              Столбцы <v-icon color="#00B950" right>mdi-checkbox-marked-outline</v-icon>
          </v-btn> -->
          <!-- ///////////////////// -->
          <v-menu
            transition="slide-y-transition"
            left
            :close-on-content-click="false"
            :nudge-width="50"
            offset-y
          >
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                class="filterBtn px-2"
                outlined
                v-bind="attrs"
                v-on="on"
                style="background: #fff; height: 34px"
              >
                Столбцы
                <v-icon color="green" right>mdi-checkbox-marked-outline</v-icon>
              </v-btn>
            </template>
            <v-card>
              <v-list v-for="(item, i) in headers.filter((v) => v.tabList)" :key="i">
                <v-list-item :style="i > 0 ? 'margin:-35px 0px 0px 0px' : ''">
                  <v-list-item-action>
                    <!-- <v-switch 
                      v-model="item.visible"
                      color="primary"
                      @click="showHeaderss()"
                    ></v-switch> -->
                    <v-checkbox
                      style="color: #2c8dff"
                      :style="
                        item.visible
                          ? 'margin-left: -30px;cursor: pointer;'
                          : 'color:grey;margin-left: -30px;cursor: pointer;'
                      "
                      v-model="item.visible"
                      @click="showHeaderss()"
                    ></v-checkbox>
                  </v-list-item-action>
                  <!-- ;' -->
                  <v-list-item-title
                    class="rowTestClass"
                    @click="showHeaderss2(item)"
                    :style="
                      item.visible
                        ? 'margin-left: -30px;cursor: pointer'
                        : 'color:grey;margin-left: -30px;cursor: pointer;'
                    "
                    >{{ item.text }}</v-list-item-title
                  >
                </v-list-item>
              </v-list>
            </v-card>
          </v-menu>
          <!-- ///////////////////// -->
          <v-menu
            transition="slide-y-transition"
            left
            :close-on-content-click="false"
            :nudge-width="50"
            offset-y
          >
            <template v-slot:activator="{ on, attrs }">
              <v-btn class="txt_searchBtn ml-2" outlined v-bind="attrs" v-on="on">
                <v-icon size="18" color="white">mdi-format-list-bulleted</v-icon>
              </v-btn>
            </template>
            <v-card>
              <v-list class="dropdown-list pa-0">
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
                    <v-icon color="#107C41" size="18">mdi-microsoft-excel</v-icon>
                    Скачать таблицу Excel
                  </v-list-item-title></v-list-item
                >
              </v-list>
            </v-card>
          </v-menu>
        </div>
        <!-- <div style="max-width: 300px; width: 300px">
          <v-text-field
            v-model="filter.content"
            hide-details
            outlined
            dense
            clearable
            :label="$t('searchInText')"
            class="mr-1"
            @keyup.native.enter="getFilter()"
          ></v-text-field>
        </div>
        <div style="max-width: 200px; width: 300px">
          <v-text-field
            v-model="filter.content_table"
            hide-details
            outlined
            dense
            clearable
            :label="$t('searchInTable')"
            class="mr-1"
            @keyup.native.enter="getFilter()"
          ></v-text-field>
        </div> -->
        <!-- <v-autocomplete
          v-model="filter.staff_id"
          v-if="$route.params.menu_item == 'archive'"
          :items="staffs"
          :search-input.sync="search"
          :loading="isLoading"
          outlined
          dense
          :label="$t('position.index')"
          hide-details
          style="max-width: 520px; min-width: 466px"
          @change="getFilter()"
          @keyup="getStaffs()"
          item-value="id"
          item-text="search"
          clearable
        >
          <template v-slot:selection="{ item }" style="max-width: 150px">
            {{
              item.department_code +
              " " +
              item["department_name_" + $i18n.locale]
            }}
          </template>
          <template v-slot:item="{ item }">
            <v-list-item-content>
              <v-list-item-title>
                {{
                  item.department_code +
                  " " +
                  item["department_name_" + $i18n.locale]
                }}
              </v-list-item-title>
              <v-list-item-subtitle>
                {{ item["position_name_" + $i18n.locale] }}
              </v-list-item-subtitle>
            </v-list-item-content>
          </template>
        </v-autocomplete> -->
        <!-- <div style="max-width: 100px">
          <v-text-field
            v-if="filter.menu_item != 'akt'"
            v-model="filter.tabel"
            outlined
            dense
            clearable
            class="mr-1"
            :label="$t('employee.tabel')"
            multiple
            hide-details
            @keyup.native.enter="filter.tabel.length == 4 ? getFilter() : ''"
          >
          </v-text-field>
        </div> -->
        <!-- <v-autocomplete
          v-model="filter.reaction_status"
          v-if="['inbox','outbox'].includes(filter.menu_item)"
          :items="reaction_status"
          outlined
          dense
          :label="$t('document.reaction_status')"
          multiple
          hide-details
          style="max-width: 520px; min-width: 466px"
          @change="getFilter()"
        >
          <template v-slot:selection="{ item }">
            <v-chip
              :class="
                item.value == 1
                  ? 'success'
                  : item.value == 2
                  ? 'error'
                  : item.value == 3
                  ? 'deep-purple'
                  : item.value == 4
                  ? 'orange lighten-1'
                  : ''
              "
              small
              :dark="item.reaction_status == 0 ? false : true"
              class="ma-0 mr-1 px-1"
              >{{ item.text }}</v-chip
            >
          </template>
          <template v-slot:item="{ item }">
            <v-list-item-content>
              <v-list-item-title v-text="item.text"></v-list-item-title>
            </v-list-item-content>
          </template>
        </v-autocomplete> -->
      </v-card-title>
      <v-row class="mx-0">
        <v-col class="ma-0 pa-0" xs="12">
          <v-data-table
            class="doc-template_data-table"
            dense
            style="width: 100%; height: 100%; border-radius: 10px"
            fixed-header
            :loading-text="$t('loadingText')"
            :no-data-text="noDataText"
            :height="screenHeight"
            :loading="loading"
            :headers="active_headers"
            :items="items"
            item-key="id"
            :server-items-length="server_items_length"
            :options.sync="dataTableOptions"
            :disable-pagination="true"
            :footer-props="{
              itemsPerPageOptions: [20, 50, 100, 200],
              itemsPerPageAllText: $t('itemsPerPageAllText'),
              itemsPerPageText: $t('itemsPerPageText'),
              showFirstLastPage: true,
              firstIcon: 'mdi-arrow-collapse-left',
              lastIcon: 'mdi-arrow-collapse-right',
              prevIcon: 'mdi-arrow-left',
              nextIcon: 'mdi-arrow-right',
            }"
            @update:page="updatePage"
            @update:items-per-page="updatePerPage"
            @update:sort-by="updatePage"
            :expanded.sync="expanded"
            single-expand
            show-expand
            @update:expanded="toggleExpand"
            @dblclick:row="rowClick"
          >
            <template v-slot:body.prepend>
              <tr class="py-0 my-0 prepend_height">
                <td></td>
                <td class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filter.id"
                    type="text"
                    hide-details
                    @keyup.native.enter="getFilter()"
                  ></v-text-field>
                </td>
                <td
                  class="py-0 my-0 dense"
                  v-if="headers.find((v) => v.value === 'document_number').visible"
                >
                  <v-text-field
                    v-model="filter.document_number"
                    type="text"
                    hide-details
                    @keyup.native.enter="getFilter()"
                    clearable
                  ></v-text-field>
                </td>
                <!-- <td class="py-0 my-0 dense" v-if="$route.params.menu_item == 'akt'">
                  <v-select
                    v-model="filter.act_date"
                    :items = "months"
                    hide-details
                    @change="getFilter()"
                    clearable
                  ></v-select>
                </td> -->
                <td
                  class="py-0 my-0 dense"
                  v-if="headers.find((v) => v.value === 'document_date').visible"
                >
                  <v-menu
                    ref="rangeMenu"
                    :close-on-content-click="false"
                    :return-value.sync="filter.document_range"
                    offset-y
                    min-width="290px"
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field
                        v-model="filter.document_range"
                        v-bind="attrs"
                        @keyup.native.enter="getFilter()"
                        v-on="on"
                        hide-details
                        clearable
                      ></v-text-field>
                    </template>
                    <v-date-picker v-model="date" range no-title scrollable>
                      <v-spacer></v-spacer>
                      <v-btn
                        text
                        color="primary"
                        @click="$refs.rangeMenu.isActive = false"
                        >Cancel</v-btn
                      >
                      <v-btn
                        text
                        color="primary"
                        @click="
                          $refs.rangeMenu.save(date);
                          filter.document_range = date;
                          getFilter();
                        "
                        >OK</v-btn
                      >
                    </v-date-picker>
                  </v-menu>
                </td>

                <td
                  class="py-0 my-0 dense"
                  v-if="headers.find((v) => v.value === 'title').visible"
                >
                  <v-text-field
                    v-model="filter.title"
                    type="text"
                    hide-details
                    @keyup.native.enter="getFilter()"
                    clearable
                  ></v-text-field>
                </td>

                <td
                  class="py-0 my-0 dense"
                  v-if="headers.find((v) => v.value === 'document_template').visible"
                >
                  <v-autocomplete
                    class="py-0"
                    clearable
                    v-model="filter.document_template_id"
                    :items="
                      $route.params.document_type != 0
                        ? document_templates.filter((v) => {
                            if (v.document_type_id == $route.params.document_type)
                              return v;
                          })
                        : document_templates
                    "
                    hide-details
                    item-value="id"
                    @change="getFilter()"
                  >
                    <template v-slot:selection="{ item }">{{ item.text }}</template>
                    <template v-slot:item="{ item }">
                      <v-list-item-content>
                        <v-list-item-title v-text="item.text"></v-list-item-title>
                      </v-list-item-content>
                    </template>
                  </v-autocomplete>
                </td>
                <td
                  class="py-0 my-0 dense"
                  style="width: 250px"
                  v-if="headers.find((v) => v.value === 'creator').visible"
                >
                  <v-text-field
                    v-model="filter.created_by"
                    type="text"
                    hide-details
                    @keyup.native.enter="getFilter()"
                    clearable
                  ></v-text-field>
                </td>
                <td
                  class="py-0 my-0 dense"
                  v-if="
                    $route.params.document_type == 4 &&
                    headers.find((v) => v.value === 'summary').visible
                  "
                >
                  <v-text-field
                    v-model="filter.summary"
                    type="text"
                    hide-details
                    @keyup.native.enter="getFilter()"
                    clearable
                  ></v-text-field>
                </td>
                <td
                  class="py-0 my-0 dense"
                  v-if="
                    $route.params.document_type == 4 &&
                    headers.find((v) => v.value === 'registration').visible
                  "
                >
                  <v-text-field
                    v-model="filter.registration"
                    type="text"
                    hide-details
                    @keyup.native.enter="getFilter()"
                    clearable
                  ></v-text-field>
                </td>
                <td
                  class="py-0 my-0 dense"
                  v-if="
                    $route.params.document_type == 4 &&
                    headers.find((v) => v.value === 'korrespondent').visible
                  "
                >
                  <v-text-field
                    v-model="filter.korrespondent"
                    type="text"
                    hide-details
                    @keyup.native.enter="getFilter()"
                    clearable
                  ></v-text-field>
                </td>
                <td
                  class="py-0 my-0 dense"
                  v-if="
                    $route.params.document_type == 4 &&
                    headers.find((v) => v.value === 'type').visible
                  "
                >
                  <v-text-field
                    v-model="filter.type"
                    type="text"
                    hide-details
                    @keyup.native.enter="getFilter()"
                    clearable
                  ></v-text-field>
                </td>
                <!-- <td class="py-0 my-0 dense">
                  <v-text-field
                    v-model="filter.send_by"
                    type="text"
                    hide-details
                    @keyup.native.enter="getFilter()"
                    clearable
                  ></v-text-field>
                </td> -->
                <td
                  class="py-0 my-0 dense"
                  v-if="headers.find((v) => v.value === 'to_department').visible"
                >
                  <v-text-field
                    v-model="filter.receive_by"
                    type="text"
                    hide-details
                    @keyup.native.enter="getFilter()"
                    clearable
                  ></v-text-field>
                </td>
                <td
                  class="py-0 my-0 dense"
                  style="min-width: 130px"
                  v-if="headers.find((v) => v.value === 'pending_action').visible"
                >
                  <v-text-field
                    v-model="filter.pending_action"
                    type="text"
                    hide-details
                    @keyup.native.enter="getFilter()"
                    clearable
                  ></v-text-field>
                </td>
                <td class="py-0 my-0 dense">
                  <v-autocomplete
                    class="py-0"
                    clearable
                    v-model="filter.document_status"
                    :items="document_status"
                    hide-details
                    item-value="id"
                    @change="getFilter()"
                  >
                    <template v-slot:selection="{ item }">
                      {{ item["name_" + $i18n.locale] }}
                    </template>
                    <template v-slot:item="{ item }">
                      <v-list-item-content>
                        <v-list-item-title
                          v-text="item['name_' + $i18n.locale]"
                        ></v-list-item-title>
                      </v-list-item-content>
                    </template>
                  </v-autocomplete>
                </td>
                <td class="py-0 my-0 dense"></td>
              </tr>
            </template>
            <template v-slot:item.document_number="{ item }">
              <!-- <a :href="'#/documents/show/'+item.id">{{item.document_number}}</a> -->
              <!-- {{ signered[items.map(function(x) {return x.id; }).indexOf(item.id) + from - 1] }} -->
              <v-btn
                style="height: 21px"
                outlined
                small
                :dark="item.reaction_status == 0 ? false : true"
                rounded
                :class="
                  item.action_type_id == 5
                    ? 'info'
                    : item.reaction_status == 1
                    ? 'success'
                    : item.reaction_status == 2
                    ? 'error'
                    : item.reaction_status == 3
                    ? 'deep-purple'
                    : item.reaction_status == 4
                    ? 'orange lighten-1'
                    : ''
                "
                :to="'/documentsidebar/document/' + item.pdf_file_name"
                >{{
                  item.document_number_reg
                    ? item.document_number_reg
                    : item.document_number
                }}</v-btn
              >
            </template>
            <template v-slot:item.id="{ item }">
              {{ item.id }}
            </template>
            <template v-slot:item.act_date="{ item }">
              {{
                item.act_date
                  ? item.act_date["act_date"] == "2024-01"
                    ? "Yanvar, 2024"
                    : item.act_date["act_date"] == "2024-02"
                    ? "Fevral, 2024"
                    : item.act_date["act_date"] == "2024-03"
                    ? "Mart, 2024"
                    : item.act_date["act_date"] == "2024-04"
                    ? "Aprel, 2024"
                    : item.act_date["act_date"] == "2024-05"
                    ? "May, 2024"
                    : item.act_date["act_date"] == "2024-06"
                    ? "Iyun, 2024"
                    : item.act_date["act_date"] == "2024-07"
                    ? "Iyul, 2024"
                    : item.act_date["act_date"] == "2024-08"
                    ? "Avgust, 2024"
                    : item.act_date["act_date"] == "2024-09"
                    ? "Sentyabr, 2024"
                    : item.act_date["act_date"] == "2024-10"
                    ? "Oktyabr, 2024"
                    : item.act_date["act_date"] == "2024-11"
                    ? "Noyabr, 2024"
                    : item.act_date["act_date"] == "2024-12"
                    ? "Dekabr, 2024"
                    : item.act_date["act_date"] == "2023-01"
                    ? "Yanvar, 2023"
                    : item.act_date["act_date"] == "2023-02"
                    ? "Fevral, 2023"
                    : item.act_date["act_date"] == "2023-03"
                    ? "Mart, 2023"
                    : item.act_date["act_date"] == "2023-04"
                    ? "Aprel, 2023"
                    : item.act_date["act_date"] == "2023-05"
                    ? "May, 2023"
                    : item.act_date["act_date"] == "2023-06"
                    ? "Iyun, 2023"
                    : item.act_date["act_date"] == "2023-07"
                    ? "Iyul, 2023"
                    : item.act_date["act_date"] == "2023-08"
                    ? "Avgust, 2023"
                    : item.act_date["act_date"] == "2023-09"
                    ? "Sentyabr, 2023"
                    : item.act_date["act_date"] == "2023-10"
                    ? "Oktyabr, 2023"
                    : item.act_date["act_date"] == "2023-11"
                    ? "Noyabr, 2023"
                    : item.act_date["act_date"] == "2023-12"
                    ? "Dekabr, 2023"
                    : ""
                  : ""
              }}
            </template>
            <template v-slot:item.from_department="{ item }">
              <span
                class="d-inline-block text-truncate"
                style="max-width: 150px"
                :title="item.from_department"
              >
                {{ item.from_department }}
              </span>
              <span
                class="d-inline-block text-truncate"
                style="max-width: 150px"
                :title="item.from_manager"
              >
                <b>{{
                  item.from_manager &&
                  item.from_manager
                    .split(" ")
                    .map((v, k) => {
                      if (k == 0) return v;
                      else return v[0] + ".";
                    })
                    .join(" ")
                }}</b>
              </span>
            </template>
            <template v-slot:item.to_department="{ item }">
              <td style="max-width: 300px">
                <span :title="item.to_department">
                  {{ item.to_department }}
                </span>
                <br />
                <span :title="item.to_manager">
                  <b>{{
                    item.to_manager &&
                    item.to_manager
                      .split(" ")
                      .slice(0, 3)
                      .map((v, k) => {
                        if (k == 0) return v;
                        else return v[0] + ".";
                      })
                      .join(" ")
                  }}</b>
                </span>
              </td>
            </template>
            <template v-slot:item.arrive_date="{ item }">
              {{
                item.document_signers.find((v) => v.staff_id == 1)
                  ? item.document_signers
                      .find((v) => v.staff_id == 1)
                      .taken_datetime.substring(0, 16)
                  : ""
              }}
            </template>
            <template v-slot:item.creator="{ item }">
              <td style="max-width: 250px">
                {{
                  item.employee.employee_staff[0] &&
                  item.employee.employee_staff[0].staff.department
                    ? item.employee.employee_staff[0].staff.department[
                        "name_" + $i18n.locale
                      ]
                    : "***"
                }}
                <br />
                {{
                  item.employee.employee_staff[0]
                    ? item.employee.employee_staff[0].staff.position[
                        "name_" + $i18n.locale
                      ]
                    : ""
                }}
                <br />
                <b>
                  {{
                    $i18n.locale == "uz_latin"
                      ? item.employee.lastname_uz_latin +
                        " " +
                        item.employee.firstname_uz_latin.substr(0, 1) +
                        "." +
                        (item.employee.middlename_uz_latin
                          ? item.employee.middlename_uz_latin.substr(0, 1) + "."
                          : "")
                      : item.employee.lastname_uz_cyril +
                        " " +
                        item.employee.firstname_uz_cyril.substr(0, 1) +
                        "." +
                        (item.employee.middlename_uz_cyril
                          ? item.employee.middlename_uz_cyril.substr(0, 1)
                          : "") +
                        "."
                  }}
                </b>
              </td>
            </template>
            <template v-slot:item.document_type="{ item }">
              <td style="max-width: 100px">
                {{ item.document_type["name_" + $i18n.locale] }}
              </td>
            </template>
            <template v-slot:item.title="{ item }">
              <td style="max-width: 150px">
                <span
                  class="d-inline-block text-truncate"
                  style="max-width: 150px"
                  :title="item.title"
                >
                  {{ item.title }}
                </span>
              </td>
            </template>
            <template v-slot:item.document_template="{ item }">
              <td class="text-trancate" style="max-width: 200px">
                {{
                  item.document_template && item.document_template["name_" + $i18n.locale]
                }}
              </td>
            </template>
            <template v-slot:item.document_date="{ item }">
              <td style="max-width: 100px">
                {{
                  item.document_date_reg
                    ? item.document_date_reg.substr(0, 10) +
                      " " +
                      item.document_date_reg.substr(11, 5)
                    : item.document_date.substr(0, 10) +
                      " " +
                      item.document_date.substr(11, 5)
                }}
              </td>
            </template>
            <template v-slot:item.status="{ item }" style="padding: 0px">
              <span
                v-if="item.status == 0"
                style="
                  display: block;
                  background: #757575;
                  color: #fff;
                  border-radius: 10px;
                  padding: 1px 5px;
                "
              >
                {{
                  document_status[item.status]
                    ? document_status[item.status]["name_" + $i18n.locale]
                    : ""
                }}
              </span>
              <span
                v-if="item.status == 1"
                style="
                  display: block;
                  background: #00acc1;
                  color: #fff;
                  border-radius: 10px;
                  padding: 1px 5px;
                "
              >
                {{
                  document_status[item.status]
                    ? document_status[item.status]["name_" + $i18n.locale]
                    : ""
                }}
              </span>
              <span
                v-if="item.status == 2"
                style="
                  display: block;
                  background: #039be5;
                  color: #fff;
                  border-radius: 10px;
                  padding: 1px 5px;
                "
              >
                {{
                  document_status[item.status]
                    ? document_status[item.status]["name_" + $i18n.locale]
                    : ""
                }}
              </span>
              <span
                v-if="item.status == 3"
                style="
                  display: block;
                  background: teal;
                  color: #fff;
                  border-radius: 10px;
                  padding: 1px 5px;
                "
              >
                {{
                  document_status[item.status]
                    ? document_status[item.status]["name_" + $i18n.locale]
                    : ""
                }}
              </span>
              <span
                v-if="item.status == 4"
                style="
                  display: block;
                  background: #d8cd1d;
                  color: #fff;
                  border-radius: 10px;
                  padding: 1px 5px;
                "
              >
                {{
                  document_status[item.status]
                    ? document_status[item.status]["name_" + $i18n.locale]
                    : ""
                }}
              </span>
              <span
                v-if="item.status == 5"
                style="
                  display: block;
                  background: #00c853;
                  color: #fff;
                  border-radius: 10px;
                  padding: 1px 5px;
                "
              >
                {{
                  document_status[item.status]
                    ? document_status[item.status]["name_" + $i18n.locale]
                    : ""
                }}
              </span>
              <span
                v-if="item.status == 6"
                style="
                  display: block;
                  background: #ef6c00;
                  color: #fff;
                  border-radius: 10px;
                  padding: 1px 5px;
                "
              >
                {{
                  document_status[item.status]
                    ? document_status[item.status]["name_" + $i18n.locale]
                    : ""
                }}
              </span>
              <span
                v-if="item.status == 7"
                style="
                  display: block;
                  background: #8bc34a;
                  color: #fff;
                  border-radius: 10px;
                  padding: 1px 5px;
                "
              >
                {{
                  document_status[item.status]
                    ? document_status[item.status]["name_" + $i18n.locale]
                    : ""
                }}
              </span>
              <span
                v-if="item.status == 8"
                style="
                  display: block;
                  background: #ef6c00;
                  color: #fff;
                  border-radius: 10px;
                  padding: 1px 5px;
                "
              >
                {{
                  document_status[item.status]
                    ? document_status[item.status]["name_" + $i18n.locale]
                    : ""
                }}
              </span>
            </template>
            <template v-slot:item.pending_action="{ item }">
              <template>
                <v-tooltip bottom color="primary">
                  <template v-slot:activator="{ on, attrs }">
                    <div v-on="on">
                      <div
                        v-for="document_signer in filteredSigners(item).slice(0, 2)"
                        :key="document_signer.id"
                      >
                        <div v-bind="attrs">
                          {{ getSignerName(document_signer) }}
                        </div>
                      </div>
                      <span v-if="filteredSigners(item).length > 2">...</span>
                    </div>
                  </template>
                  <template v-for="documentSigner in filteredSigners(item)">
                    <span>{{ getSignerName(documentSigner) }}</span
                    ><br>
                  </template>
                </v-tooltip>
              </template>
            </template>
            <!-- <template v-slot:item.pending_action="{ item }">
              <template
                v-for="document_signer in item.document_signers.filter((v) => {
                  if (v.status == 0 || v.status == 3 || v.status == 4) return v;
                })"
              >
                <div :key="document_signer.id" class="ma-0" v-if="item.status != 6">
                  <div v-if="document_signer.signer_employee">
                    {{
                      document_signer.signer_employee &&
                      document_signer.signer_employee["lastname_" + language] +
                        " " +
                        document_signer.signer_employee[
                          "firstname_" + language
                        ].substr(0, 1) +
                        "." +
                        (document_signer.signer_employee[
                          "middlename_" + language
                        ] ? document_signer.signer_employee[
                          "middlename_" + language
                        ].substr(0, 1) + "." : '')
                    }}
                  </div>
                  <div v-else>
                    {{
                      document_signer.employee_staffs &&
                      document_signer.employee_staffs.employee[
                        "lastname_" + language
                      ] +
                        " " +
                        document_signer.employee_staffs.employee[
                          "firstname_" + language
                        ].substr(0, 1) +
                        "." +
                        (document_signer.employee_staffs.employee[
                          "middlename_" + language
                        ] ? document_signer.employee_staffs.employee[
                          "middlename_" + language
                        ].substr(0, 1) : '') +
                        "."
                    }}
                  </div>
                </div>
              </template>
            </template> -->
            <template
              v-slot:item.summary="{ item }"
              v-if="$route.params.document_type == 4"
            >
              {{
                item.document_details[0] &&
                item.document_details[0].document_detail_contents &&
                item.document_details[0].document_detail_contents.find(
                  (v) =>
                    v.d_d_attribute_id == 744 ||
                    v.d_d_attribute_id == 761 ||
                    v.d_d_attribute_id == 715
                )
                  ? item.document_details[0].document_detail_contents.find(
                      (v) =>
                        v.d_d_attribute_id == 744 ||
                        v.d_d_attribute_id == 761 ||
                        v.d_d_attribute_id == 715
                    ).value
                  : ""
              }}
            </template>
            <template
              v-slot:item.registration="{ item }"
              v-if="$route.params.document_type == 4"
            >
              {{
                item.document_details[0] &&
                item.document_details[0].document_detail_contents &&
                item.document_details[0].document_detail_contents.find(
                  (v) =>
                    v.d_d_attribute_id == 730 ||
                    v.d_d_attribute_id == 750 ||
                    v.d_d_attribute_id == 704
                )
                  ? item.document_details[0].document_detail_contents.find(
                      (v) =>
                        v.d_d_attribute_id == 730 ||
                        v.d_d_attribute_id == 750 ||
                        v.d_d_attribute_id == 704
                    ).value
                  : ""
              }}
            </template>
            <template
              v-slot:item.korrespondent="{ item }"
              v-if="$route.params.document_type == 4"
            >
              {{
                item.document_details[0] &&
                item.document_details[0].document_detail_contents &&
                item.document_details[0].document_detail_contents.find(
                  (v) =>
                    v.d_d_attribute_id == 734 ||
                    v.d_d_attribute_id == 751 ||
                    v.d_d_attribute_id == 705
                )
                  ? item.document_details[0].document_detail_contents.find(
                      (v) =>
                        v.d_d_attribute_id == 734 ||
                        v.d_d_attribute_id == 751 ||
                        v.d_d_attribute_id == 705
                    ).value
                  : ""
              }}
            </template>
            <template v-slot:item.type="{ item }" v-if="$route.params.document_type == 4">
              {{
                item.document_details[0] &&
                item.document_details[0].document_detail_contents &&
                item.document_details[0].document_detail_contents.find(
                  (v) =>
                    v.d_d_attribute_id == 732 ||
                    v.d_d_attribute_id == 749 ||
                    v.d_d_attribute_id == 703
                )
                  ? item.document_details[0].document_detail_contents.find(
                      (v) =>
                        v.d_d_attribute_id == 732 ||
                        v.d_d_attribute_id == 749 ||
                        v.d_d_attribute_id == 703
                    ).value
                  : ""
              }}
            </template>
            <template v-slot:item.actions="{ item }">
              <v-btn
                v-if="
                  (user.username == 'sd6566' ||
                    user.username == 'ay2275' ||
                    user.username == 'SA2280' ||
                    user.username == 'Ih3404' ||
                    user.username == 'qg9592') &&
                  item.created_employee_id == user.employee_id
                "
                color="primary"
                class="px-0"
                style="min-width: 25px"
                small
                icon
                @click="documentCopyS(item.id)"
              >
                <v-icon size="18">mdi-alpha-s</v-icon>
              </v-btn>
              <v-btn
                v-if="
                  $store.getters.checkPermission('document-copy') &&
                  item.created_employee_id == user.employee_id
                "
                color="primary"
                class="px-0"
                style="min-width: 25px"
                small
                icon
                @click="documentCopy(item.id)"
              >
                <v-icon size="18">mdi-content-copy</v-icon>
              </v-btn>
              <v-btn
                v-if="
                  item.created_employee_id == user.employee_id &&
                  item.status == 6 &&
                  item.restore == 1
                "
                color="primary"
                class="px-0"
                style="min-width: 25px"
                small
                icon
                @click="restoreDocument(item.id)"
              >
                <v-icon size="18">mdi-undo-variant</v-icon>
              </v-btn>
              <v-btn
                color="success"
                class="px-0"
                style="min-width: 25px"
                small
                icon
                :href="'#/document/' + item.pdf_file_name"
                target="_blank"
              >
                <v-icon size="18">mdi-eye-outline</v-icon>
              </v-btn>
              <v-btn
                v-if="
                  $store.getters.checkPermission('document-update') && item.status < 1
                "
                class="px-0"
                style="min-width: 25px"
                small
                icon
                @click="$router.push('/document/update/' + item.id)"
              >
                <v-icon size="18">mdi-pencil</v-icon>
              </v-btn>
              <v-btn
                v-if="item.status == 0"
                color="red"
                class="px-0"
                style="min-width: 25px"
                small
                icon
                @click="deleteItem(item)"
              >
                <v-icon size="18">mdi-trash-can-outline</v-icon>
              </v-btn>
            </template>
            <template v-slot:expanded-item="{ headers, item }">
              <td :colspan="headers.length">
                <v-row class="justify-center">
                  <v-col cols="12" md="10" lg="9" xl="7" class="py-1 ma-2">
                    <v-card class="pa-3">
                      <div
                        v-for="(document_detail, detail_index) in item.document_details"
                        :key="detail_index"
                      >
                        <strong
                          style="float: left"
                          v-if="item.document_details.length > 1"
                          >{{ detail_index + 1 }}.</strong
                        >
                        <p
                          class="text-left font-weight-black my-1 pl-6"
                          v-html="document_detail.content"
                        >
                          <!-- {{  }} -->
                        </p>
                        <v-simple-table dense>
                          <template v-slot:default>
                            <tbody>
                              <tr
                                v-for="item in document_detail.document_detail_attribute_values"
                                :key="item.index"
                              >
                                <td class="text-right px-2">
                                  <b>
                                    {{
                                      item.document_detail_attributes[
                                        "attribute_name_" + $i18n.locale
                                      ]
                                    }}:
                                  </b>
                                </td>
                                <td class="text-left px-2" width="50%">
                                  {{ item.attribute_value }}
                                </td>
                              </tr>
                            </tbody>
                          </template>
                        </v-simple-table>
                      </div>
                    </v-card>
                  </v-col>
                </v-row>
              </td>
            </template>
          </v-data-table>
        </v-col>
      </v-row>
    </v-card>
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
const axios = require("axios").default;
const moment = require("moment");
import Swal from "sweetalert2";
import Axios from "axios";
const Cookies = require("js-cookie");
// import moment from 'moment';
export default {
  data() {
    return {
      headers: [
        { text: "", value: "data-table-expand" },
        // { text: "#", value: "id", align: "center", width: 30 },
        {
          text: this.$t("ID"),
          value: "id",
          width: 50,
          tabList: false,
          visible: true,
          // sortable: false,
        },
        {
          text: this.$t("document.document_number"),
          value: "document_number",
          align: "center",
          width: 150,
          sortable: false,
          tabList: true,
          visible: true,
        },
        {
          text: this.$t("document.document_date"),
          value: "act_date",
          width: 100,
          sortable: false,
          tabList: false,
          visible: false,
        },
        {
          text: this.$t("document.document_date"),
          value: "document_date",
          width: 100,
          sortable: false,
          tabList: true,
          visible: true,
        },
        {
          text: this.$t("document.title"),
          value: "title",
          width: 150,
          sortable: false,
          tabList: true,
          visible: true,
        },
        {
          text: this.$t("document.document_type"),
          value: "document_template",
          width: 200,
          sortable: false,
          tabList: true,
          visible: true,
        },
        {
          // text: this.$t("document.creator"),
          text: this.$t("document.department_send"),
          value: "creator",
          width: 200,
          sortable: false,
          tabList: true,
          visible: true,
        },
        {
          text: this.$t("document.short_content"),
          value: "summary",
          sortable: false,
          tabList: false,
          visible: false,
        },
        {
          text: this.$t("document.number"),
          value: "registration",
          sortable: false,
          tabList: false,
          visible: false,
        },
        {
          text: this.$t("document.correspondent"),
          value: "korrespondent",
          sortable: false,
          tabList: false,
          visible: false,
        },
        {
          text: this.$t("document.type"),
          value: "type",
          sortable: false,
          tabList: false,
          visible: false,
        },
        // {
        //   text: this.$t("document.department_send"),
        //   value: "from_department",
        //   sortable: false,
        // },
        {
          text: this.$t("document.department_receiver"),
          value: "to_department",
          sortable: false,
          tabList: true,
          visible: true,
        },
        {
          text: this.$t("document.pending_action"),
          value: "pending_action",
          sortable: false,
          tabList: true,
          visible: true,
        },
        {
          text: this.$t("document.status"),
          value: "status",
          align: "center",
          sortable: false,
          tabList: true,
          visible: true,
        },
        {
          text: this.$t("actions"),
          value: "actions",
          sortable: false,
          tabList: false,
          visible: true,
        },
      ],
      date: null,
      loading: false,
      isLoading: false,
      expanded: [],
      search: "",
      dialog: false,
      editMode: null,
      items: [],
      form: {},
      dialogHeaderText: "",
      dataTableOptions: { page: 1, itemsPerPage: 20 },
      page: 1,
      from: 0,
      server_items_length: -1,
      document_type: [],
      document_template: {
        document_detail_templates: [
          {
            document_detail_attributes: {},
          },
        ],
      },
      document_templates: [],
      filter: {
        title: "",
        id: "",
        document_type_id: 0,
        document_template_id: 0,
        document_start_date: "",
        document_end_date: "",
        attributes: [],
        menu_item: "",
        document_number: "",
        created_by: "",
        korrespondent: "",
        type: "",
        registration: "",
        act_date: "",
        send_by: "",
        receive_by: "",
        content: "",
        content_table: "",
        pending_action: "",
        reaction_status: [0, 1, 2, 3, 4],
        staff_id: null,
      },
      showFilter: false,
      menu: [],
      tableLists: [],
      table_name: [],
      column_name: [],
      is_locale: [],
      document_status: [
        {
          id: 0,
          name_uz_latin: "Yangi",
          name_uz_cyril: "Янги",
          name_ru: "Новый",
        },
        {
          id: 1,
          name_uz_latin: "E'lon qilindi",
          name_uz_cyril: "Эьлон қилинди",
          name_ru: "Опубликованный",
        },
        {
          id: 2,
          name_uz_latin: "Qayta ishlash",
          name_uz_cyril: "қайта ишлаш",
          name_ru: "Обработка",
        },
        {
          id: 3,
          name_uz_latin: "Imzolandi",
          name_uz_cyril: "Имзоланди",
          name_ru: "Подписано",
        },
        {
          id: 4,
          name_uz_latin: "Bajarildi",
          name_uz_cyril: "Бажарилди",
          name_ru: "Выполнено",
        },
        {
          id: 5,
          name_uz_latin: "Yakunlandi",
          name_uz_cyril: "Якунланди",
          name_ru: "Завершено",
        },
        {
          id: 6,
          name_uz_latin: "Bekor qilindi",
          name_uz_cyril: "Бекор қилинди",
          name_ru: "Отменен",
        },
        {
          id: 7,
          name_uz_latin: "Oldindan kelishuv",
          name_uz_cyril: "Олдидан килишув",
          name_ru: "Предсогласование",
        },
        {
          name_uz_latin: "Bekor qilindi(Аннулирован)",
          name_uz_cyril: "Бекор қилинди(Аннулирован)",
          name_ru: "Аннулирован",
          color: "error",
          id: 8,
        },
      ],
      reaction_status: [
        {
          text: this.$t("document.new"),
          value: 0,
        },
        {
          text: this.$t("document.ok"),
          value: 1,
        },
        {
          text: this.$t("document.cancel"),
          value: 2,
        },
        {
          text: this.$t("document.process"),
          value: 3,
        },
        {
          text: this.$t("substantiate"),
          value: 4,
        },
      ],
      noDataText: "",
      staffs: [],
      months: [
        { text: "Yanvar, 2024", value: "2024-01" },
        { text: "Fevral, 2024", value: "2024-02" },
        { text: "Mart, 2024", value: "2024-03" },
        { text: "Aprel, 2024", value: "2024-04" },
        { text: "May, 2024", value: "2024-05" },
        { text: "Iyun, 2024", value: "2024-06" },
        { text: "Iyul, 2024", value: "2024-07" },
        { text: "Avgust, 2024", value: "2024-08" },
        { text: "Sentyabr, 2024", value: "2024-09" },
        { text: "Oktyabr, 2024", value: "2024-10" },
        { text: "Noyabr, 2024", value: "2024-11" },
        { text: "Dekabr, 2024", value: "2024-12" },
        { text: "Yanvar, 2023", value: "2023-01" },
        { text: "Fevral, 2023", value: "2023-02" },
        { text: "Mart, 2023", value: "2023-03" },
        { text: "Aprel, 2023", value: "2023-04" },
        { text: "May, 2023", value: "2023-05" },
        { text: "Iyun, 2023", value: "2023-06" },
        { text: "Iyul, 2023", value: "2023-07" },
        { text: "Avgust, 2023", value: "2023-08" },
        { text: "Sentyabr, 2023", value: "2023-09" },
        { text: "Oktyabr, 2023", value: "2023-10" },
        { text: "Noyabr, 2023", value: "2023-11" },
        { text: "Dekabr, 2023", value: "2023-12" },
      ],
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 175;
    },
    user() {
      let localStorage = window.localStorage;
      return JSON.parse(localStorage.getItem("user"));
    },
    active_headers() {
      return this.headers.filter((v) => v.visible);
    },
    language() {
      return this.$i18n.locale == "ru" ? "uz_cyril" : this.$i18n.locale;
    },
  },
  methods: {
    format_date(value) {
      if (value) {
        return moment(String(value)).format("DD.MM.YYYY");
      }
    },
    showHeaderss() {
      return this.headers.filter((s) => s.visible);
    },
    showHeaderss2(item) {
      item.visible = !item.visible;
      this.showHeaderss();
    },
    updatePage($event) {
      this.getList();
    },
    updatePerPage($event) {
      this.getList();
    },
    toggleExpand($event) {},
    rowClick(item, row) {
      row.expand(!row.isExpanded);
      // if (this.expanded[0] && this.expanded[0].id == item.id) this.expanded = [];
      // else this.expanded = [item];
    },
    filteredSigners(item) {
      return item.document_signers.filter((v) => [0, 3, 4].includes(v.status));
    },
    getSignerName(document_signer) {
      const language = this.language;
      if (document_signer.signer_employee) {
        return (
          document_signer.signer_employee["lastname_" + language] +
          " " +
          document_signer.signer_employee["firstname_" + language].substr(0, 1) +
          "." +
          (document_signer.signer_employee["middlename_" + language]
            ? document_signer.signer_employee["middlename_" + language].substr(0, 1) + "."
            : "")
        );
      } else {
        return (
          document_signer.employee_staffs.employee["lastname_" + language] +
          " " +
          document_signer.employee_staffs.employee["firstname_" + language].substr(0, 1) +
          "." +
          (document_signer.employee_staffs.employee["middlename_" + language]
            ? document_signer.employee_staffs.employee["middlename_" + language].substr(
                0,
                1
              ) + "."
            : "")
        );
      }
    },
    getList() {
      this.loading = true;
      this.noDataText = this.$t("loadingText");
      axios
        .post(this.$store.state.backend_url + "api/documents/filter", {
          pagination: this.dataTableOptions,
          language: this.$i18n.locale,
          filter: this.filter,
        })
        .then((response) => {
          this.items = response.data.documents.data;
          this.table_list_value = response.data.table_list_value;
          this.items.map((document, index) => {
            document.reaction_status = 0;
            document.action_type_id = 0;
            document.document_signers.map((document_signer) => {
              if (this.user.employee_id == document_signer.signer_employee_id) {
                document.reaction_status = document_signer.status;
                document.action_type_id = document_signer.action_type_id;
                // if([0,3].includes(document_signer.status) && document_signer.taken_at=="01-Yan. 1970y. 06:00"){
                //   document.reaction_status = 22;
                // }
              }
              // else if(document_signer.signer_employee_id == null && this.user.employee_id == document_signer.employee_staffs.employee_id){
              //   if([0,3].includes(document_signer.status) && document_signer.taken_at=="01-Yan. 1970y. 06:00"){
              //     document.reaction_status = 22;
              //   }
              // }
            });
            return document;
          });
          if (!this.items.length) {
            this.noDataText = this.$t("noDataText");
          }
          this.server_items_length = response.data.documents.total;
          this.from = response.data.documents.from;
          this.loading = false;
          this.getStaffs();
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });

      // axios
      //   .post(this.$store.state.backend_url + "api/mobile/document-list", {
      //     pagination: this.dataTableOptions,
      //     language: this.$i18n.locale,
      //     filter: this.filter,
      //   })
      //   .then((res) => {
      //     console.log(res);
      //   })
      //   .catch((err) => {
      //     console.log(err);
      //   });
    },
    getDocumentTemplate() {
      axios
        .post(this.$store.state.backend_url + "api/document-templates/get-list", {
          language: this.$i18n.locale,
        })
        .then((res) => {
          this.document_templates = res.data;
        })
        .catch((err) => {
          console.error(err);
        });
    },
    getDocumentType() {
      this.filter.attributes = [];
      axios
        .get(this.$store.state.backend_url + "api/document-types")
        .then((response) => {
          this.document_type = response.data;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getFilter() {
      this.showFilter = true;
      this.document_template = this.document_templates.find((v) => {
        return v.id == this.filter.document_template_id;
      });
      // console.log(this.filter);
      Cookies.set("filter", this.filter);
      this.getList();
    },
    getTableList(id) {
      this.isLoading = true;
      let columnName =
        this.is_locale[id] == 1
          ? this.column_name[id] + "_" + this.$i18n.locale
          : this.column_name[id];
      axios
        .post(this.$store.state.backend_url + "api/documents/table-list", {
          table_name: this.table_name[id],
          column_name: columnName,
          is_locale: this.is_locale[id],
          search: this.search,
        })
        .then((response) => {
          this.tableLists["table_" + id] = response.data.data;
          this.isLoading = false;
        })
        .catch((error) => {
          console.log(error);
          this.isLoading = false;
        });
    },
    getStaffs() {
      this.staffs = [];
      if (this.$route.params.menu_item == "archive") {
        if (this.$route.params.document_type == "employee") {
          this.isLoading = true;
          axios
            .post(this.$store.state.backend_url + "api/get-staffs", {
              search: this.search,
              employee: true,
            })
            .then((res) => {
              this.staffs = res.data.data;
              this.staffs.map((v) => {
                v.search =
                  v.department_code +
                  " " +
                  v.department_name_ru +
                  " " +
                  v.department_name_uz_cyril +
                  " " +
                  v.department_name_uz_latin +
                  " " +
                  v.position_name_ru +
                  " " +
                  v.position_name_uz_cyril +
                  " " +
                  v.position_name_uz_latin;
              });
              this.isLoading = false;
            })
            .catch((err) => {
              console.log(err);
              this.isLoading = false;
            });
        } else {
          if (this.$store.getters.checkPermission("all-document-show")) {
            this.isLoading = true;
            axios
              .post(this.$store.state.backend_url + "api/get-staffs", {
                search: this.search,
                employee: false,
              })
              .then((res) => {
                this.staffs = res.data.data;
                this.staffs.map((v) => {
                  v.search =
                    v.department_code +
                    " " +
                    v.department_name_ru +
                    " " +
                    v.department_name_uz_cyril +
                    " " +
                    v.department_name_uz_latin +
                    " " +
                    v.position_name_ru +
                    " " +
                    v.position_name_uz_cyril +
                    " " +
                    v.position_name_uz_latin;
                });
                this.isLoading = false;
              })
              .catch((err) => {
                console.log(err);
                this.isLoading = false;
              });
          } else {
            user.employee.employee_staff.map((v) => {
              this.staffs.push(v.staff);
            });

            this.staffs.map((v) => {
              v.department_code = v.department.department_code;
              v["department_name_" + this.$i18n.locale] =
                v.department["name_" + this.$i18n.locale];
              v["position_name_" + this.$i18n.locale] =
                v.position["name_" + this.$i18n.locale];
              v.search =
                v.department_code +
                " " +
                v.department.name_ru +
                " " +
                v.department.name_uz_cyril +
                " " +
                v.department.name_uz_latin +
                " " +
                v.position.name_ru +
                " " +
                v.position.name_uz_cyril +
                " " +
                v.position.name_uz_latin;
            });
          }
        }
      }
      console.log(this.staffs);
    },
    editItem(item) {},
    documentCopyS(id) {
      this.$router.push("/document/copys/" + id);
    },
    documentCopy(id) {
      axios
        .post(this.$store.state.backend_url + "api/document/copy", {
          document_id: id,
          restore: false,
        })
        .then((res) => {
          this.$router.push("/document/update/" + res.data);
        })
        .catch((err) => {
          console.log(err);
        });
    },
    restoreDocument(id) {
      axios
        .post(this.$store.state.backend_url + "api/document/copy", {
          document_id: id,
          restore: true,
        })
        .then((res) => {
          this.$router.push("/document/update/" + res.data);
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
          if (item.status == 0) {
            axios
              .delete(this.$store.state.backend_url + "api/documents/delete/" + item.id)
              .then((res) => {
                Swal.fire("Deleted!", this.$t("swal_deleted"), "success");
                this.getList();
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
        }
      });
    },
  },
  watch: {
    $route(to, from) {
      this.filter.menu_item = this.$route.params.menu_item;
      this.filter.document_type_id = this.$route.params.document_type;
      this.filter.staff_id = null;
      Cookies.set("filter", this.filter);
      this.getList();
    },
  },
  mounted() {
    if (Cookies.get("filter")) {
      this.filter = JSON.parse(Cookies.get("filter"));
    } else {
      this.filter.reaction_status = [0, 1, 2, 3, 4];
    }
    if (this.$route.params.menu_item) {
      this.filter.menu_item = this.$route.params.menu_item;
      this.filter.document_type_id = this.$route.params.document_type;
      Cookies.set("filter", this.filter);
    }
    this.getDocumentTemplate();
    this.getList();
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
