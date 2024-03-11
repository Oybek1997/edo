@@ -0,0 +1,461 @@
<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span>{{ $t("inventory.commission") }}</span>
        <v-spacer></v-spacer>
        <v-btn
          v-if="$store.getters.checkRole('inventory_report')"
          @click="newCommission()"
          color="success"
          class="ml-8"
          dark
          outlined
          small
          icon
        >
          <v-icon text>mdi-plus-thick</v-icon>
        </v-btn>
      </v-card-title>
      <v-card-text class="pb-1"> </v-card-text>
      <v-data-table
        dense
        fixed-header
        :loading-text="$t('loadingText')"
        :no-data-text="$t('noDataText')"
        :height="screenHeight"
        :loading="loading"
        :headers="headers"
        :items="items"
        class="mainTable ma-1"
        style="border: 1px solid #aaa"
        item-key="id"
        :server-items-length="server_items_length"
        :options.sync="dataTableOptions"
        :disable-pagination="true"
        :footer-props="{
          itemsPerPageOptions: [20, 50, 100],
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
      >
        <template v-slot:item.id="{ item }">
          {{ items.indexOf(item) + 1 }}
        </template>
        <template v-slot:item.warehouse.name="{ item }">
          {{ item.warehouse ? item.warehouse.name : "" }}
        </template>
        <template v-slot:item.responsible="{ item }">
          <span v-if="item.responsible">{{
            $i18n.locale == "uz_latin"
              ? item.responsible.tabel +
                "-" +
                item.responsible.lastname_uz_latin +
                " " +
                item.responsible.firstname_uz_latin.substr(0, 1) +
                "." +
                item.responsible.middlename_uz_latin.substr(0, 1) +
                "."
              : item.responsible.tabel +
                "-" +
                item.responsible.lastname_uz_cyril +
                " " +
                item.responsible.firstname_uz_cyril.substr(0, 1) +
                "." +
                item.responsible.middlename_uz_cyril.substr(0, 1) +
                "."
          }}</span>
          <span v-else-if="!item.responsible">{{ "" }}</span>
        </template>
        <template v-slot:item.chairman="{ item }">
          <span v-if="item.chairman">{{
            $i18n.locale == "uz_latin"
              ? item.chairman.tabel +
                "-" +
                item.chairman.lastname_uz_latin +
                " " +
                item.chairman.firstname_uz_latin.substr(0, 1) +
                "." +
                item.chairman.middlename_uz_latin.substr(0, 1) +
                "."
              : item.chairman.tabel +
                "-" +
                item.chairman.lastname_uz_cyril +
                " " +
                item.chairman.firstname_uz_cyril.substr(0, 1) +
                "." +
                item.chairman.middlename_uz_cyril.substr(0, 1) +
                "."
          }}</span>
          <span v-else-if="!item.chairman">{{ "" }}</span>
        </template>
        <template v-slot:item.members="{ item }">
          <span v-if="item.member1">
            {{
              $i18n.locale == "uz_latin"
                ? item.member1.tabel +
                  "-" +
                  item.member1.lastname_uz_latin +
                  " " +
                  item.member1.firstname_uz_latin.substr(0, 1) +
                  "." +
                  item.member1.middlename_uz_latin.substr(0, 1) +
                  "."
                : item.member1.tabel +
                  "-" +
                  item.member1.lastname_uz_cyril +
                  " " +
                  item.member1.firstname_uz_cyril.substr(0, 1) +
                  "." +
                  item.member1.middlename_uz_cyril.substr(0, 1) +
                  "."
            }}</span
          >
          <span v-else-if="!item.member1">{{ "" }}</span>
          <span v-if="item.member2">
            {{
              $i18n.locale == "uz_latin"
                ? item.member2.tabel +
                  "-" +
                  item.member2.lastname_uz_latin +
                  " " +
                  item.member2.firstname_uz_latin.substr(0, 1) +
                  "." +
                  item.member2.middlename_uz_latin.substr(0, 1) +
                  "."
                : item.member2.tabel +
                  "-" +
                  item.member2.lastname_uz_cyril +
                  " " +
                  item.member2.firstname_uz_cyril.substr(0, 1) +
                  "." +
                  item.member2.middlename_uz_cyril.substr(0, 1) +
                  "."
            }}</span
          >
          <span v-else-if="!item.member2">{{ "" }}</span>
          <span v-if="item.member3">
            {{
              $i18n.locale == "uz_latin"
                ? item.member3.tabel +
                  "-" +
                  item.member3.lastname_uz_latin +
                  " " +
                  item.member3.firstname_uz_latin.substr(0, 1) +
                  "." +
                  item.member3.middlename_uz_latin.substr(0, 1) +
                  "."
                : item.member3.tabel +
                  "-" +
                  item.member3.lastname_uz_cyril +
                  " " +
                  item.member3.firstname_uz_cyril.substr(0, 1) +
                  "." +
                  item.member3.middlename_uz_cyril.substr(0, 1) +
                  "."
            }}
          </span>
          <span v-else-if="!item.member3">{{ "" }}</span>
          <span v-if="item.member4">
            {{
              $i18n.locale == "uz_latin"
                ? item.member4.tabel +
                  "-" +
                  item.member4.lastname_uz_latin +
                  " " +
                  item.member4.firstname_uz_latin.substr(0, 1) +
                  "." +
                  item.member4.middlename_uz_latin.substr(0, 1) +
                  "."
                : item.member4.tabel +
                  "-" +
                  item.member4.lastname_uz_cyril +
                  " " +
                  item.member4.firstname_uz_cyril.substr(0, 1) +
                  "." +
                  item.member4.middlename_uz_cyril.substr(0, 1) +
                  "."
            }}
          </span>
          <span v-else-if="!item.member4">{{ "" }}</span>
          <span v-if="item.member5">
            {{
              $i18n.locale == "uz_latin"
                ? item.member5.tabel +
                  "-" +
                  item.member5.lastname_uz_latin +
                  " " +
                  item.member5.firstname_uz_latin.substr(0, 1) +
                  "." +
                  item.member5.middlename_uz_latin.substr(0, 1) +
                  "."
                : item.member5.tabel +
                  "-" +
                  item.member5.lastname_uz_cyril +
                  " " +
                  item.member5.firstname_uz_cyril.substr(0, 1) +
                  "." +
                  item.member5.middlename_uz_cyril.substr(0, 1) +
                  "."
            }}
          </span>
          <span v-else-if="!item.member5">{{ "" }}</span>
        </template>
        <template v-slot:item.shtab_members="{ item }">
          <span v-if="item.shtab_member1">
            {{
              $i18n.locale == "uz_latin"
                ? item.shtab_member1.tabel +
                  "-" +
                  item.shtab_member1.lastname_uz_latin +
                  " " +
                  item.shtab_member1.firstname_uz_latin.substr(0, 1) +
                  "." +
                  item.shtab_member1.middlename_uz_latin.substr(0, 1) +
                  "."
                : item.shtab_member1.tabel +
                  "-" +
                  item.shtab_member1.lastname_uz_cyril +
                  " " +
                  item.shtab_member1.firstname_uz_cyril.substr(0, 1) +
                  "." +
                  item.shtab_member1.middlename_uz_cyril.substr(0, 1) +
                  "."
            }}</span
          >
          <span v-else-if="!item.shtab_member1">{{ "" }}</span>
          <span v-if="item.shtab_member2">
            {{
              $i18n.locale == "uz_latin"
                ? item.shtab_member2.tabel +
                  "-" +
                  item.shtab_member2.lastname_uz_latin +
                  " " +
                  item.shtab_member2.firstname_uz_latin.substr(0, 1) +
                  "." +
                  item.shtab_member2.middlename_uz_latin.substr(0, 1) +
                  "."
                : item.shtab_member2.tabel +
                  "-" +
                  item.shtab_member2.lastname_uz_cyril +
                  " " +
                  item.shtab_member2.firstname_uz_cyril.substr(0, 1) +
                  "." +
                  item.shtab_member2.middlename_uz_cyril.substr(0, 1) +
                  "."
            }}</span
          >
          <span v-else-if="!item.shtab_member2">{{ "" }}</span>
        </template>
        <template v-slot:item.control_group="{ item }">
          <span v-if="item.control_group">{{
            $i18n.locale == "uz_latin"
              ? item.control_group.tabel +
                "-" +
                item.control_group.lastname_uz_latin +
                " " +
                item.control_group.firstname_uz_latin.substr(0, 1) +
                "." +
                item.control_group.middlename_uz_latin.substr(0, 1) +
                "."
              : item.control_group.tabel +
                "-" +
                item.control_group.lastname_uz_cyril +
                " " +
                item.control_group.firstname_uz_cyril.substr(0, 1) +
                "." +
                item.control_group.middlename_uz_cyril.substr(0, 1) +
                "."
          }}</span>
          <span v-else-if="!item.control_group">{{ "" }}</span>
        </template>
        <template v-slot:item.actions="{ item }">
          <v-btn color="blue" small icon @click="editItem(item)">
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
          <v-btn color="red" small text @click="deleteItem(item)">
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template>
      </v-data-table>
    </v-card>
    <v-dialog v-model="editDialog" width="500">
      <v-card>
        <v-card-title class="headline grey lighten-2">
          <span class="headline">{{ $t("inventory.commission") }}</span>
          <v-spacer></v-spacer>
          <v-btn
            color="red"
            outlined
            x-small
            fab
            class
            @click="editDialog = false"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form @keyup.native.enter="save" ref="inventorCreateForm">
            <v-row>
              <v-col class="pa-1" cols="6">
                <v-text-field
                  :label="$t('Kommissiya raqami')"
                  v-model="form.commission_number"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details
                  outlined
                  type="number"
                  dense
                ></v-text-field>
              </v-col>
              <v-col class="pa-1" cols="6">
                <v-autocomplete
                  :label="$t('Ombor')"
                  class="pa-0"
                  clearable
                  v-model="form.warehouse_id"
                  :items="warehouses"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details
                  item-value="id"
                  item-text="name"
                  dense
                  outlined
                >
                  <template v-slot:selection="{ item }">
                    <v-chip color="white" class="pa-1 ma-0">
                      <span v-text="item.name"></span>
                    </v-chip>
                  </template>
                  <template v-slot:item="{ item }">
                    <v-list-item-content>
                      <v-list-item-title v-text="item.name"></v-list-item-title>
                      <v-list-item-subtitle
                        v-text="item.code"
                      ></v-list-item-subtitle>
                    </v-list-item-content>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col class="pa-1" cols="6">
                <v-text-field
                  :label="$t('Modellar')"
                  v-model="form.models"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details
                  outlined
                  dense
                ></v-text-field>
              </v-col>
              <v-col class="pa-1" cols="6">
                <v-text-field
                  :label="$t('Uchastka')"
                  v-model="form.uchastka"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details
                  outlined
                  dense
                ></v-text-field>
              </v-col>
              <v-col class="pa-1" cols="6">
                <v-autocomplete
                  clearable
                  v-model="form.responsible_person"
                  :label="$t('Javobgar shaxs')"
                  @keyup="getEmployeeList()"
                  :search-input.sync="search1"
                  :items="employees1"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                  item-text="search"
                  item-value="id"
                >
                  <template v-slot:selection="data">{{
                    data.item.full_name
                  }}</template>
                  <template v-slot:item="data">
                    <template v-if="typeof data.item !== 'object'">
                      <v-list-item-content
                        v-text="data.item"
                      ></v-list-item-content>
                    </template>
                    <template v-else>
                      <v-list-item-content>
                        <v-list-item-title
                          v-html="data.item.full_name"
                        ></v-list-item-title>
                        <v-list-item-subtitle
                          v-html="data.item.tabel"
                        ></v-list-item-subtitle>
                      </v-list-item-content>
                    </template>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col class="pa-1" cols="6">
                <v-autocomplete
                  clearable
                  :label="$t('Kommissiya raisi')"
                  v-model="form.chairman"
                  @keyup="getEmployeeList()"
                  :search-input.sync="search2"
                  :items="employees2"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                  item-text="search"
                  item-value="id"
                >
                  <template v-slot:selection="data">{{
                    data.item.full_name
                  }}</template>
                  <template v-slot:item="data">
                    <template v-if="typeof data.item !== 'object'">
                      <v-list-item-content
                        v-text="data.item"
                      ></v-list-item-content>
                    </template>
                    <template v-else>
                      <v-list-item-content>
                        <v-list-item-title
                          v-html="data.item.full_name"
                        ></v-list-item-title>
                        <v-list-item-subtitle
                          v-html="data.item.tabel"
                        ></v-list-item-subtitle>
                      </v-list-item-content>
                    </template>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col class="pa-1" cols="6">
                <v-autocomplete
                  clearable
                  v-model="form.member1"
                  :label="$t('1-A\'zo')"
                  @keyup="getEmployeeList()"
                  :search-input.sync="search3"
                  :items="employees3"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                  item-text="search"
                  item-value="id"
                >
                  <template v-slot:selection="data">{{
                    data.item.full_name
                  }}</template>
                  <template v-slot:item="data">
                    <template v-if="typeof data.item !== 'object'">
                      <v-list-item-content
                        v-text="data.item"
                      ></v-list-item-content>
                    </template>
                    <template v-else>
                      <v-list-item-content>
                        <v-list-item-title
                          v-html="data.item.full_name"
                        ></v-list-item-title>
                        <v-list-item-subtitle
                          v-html="data.item.tabel"
                        ></v-list-item-subtitle>
                      </v-list-item-content>
                    </template>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col class="pa-1" cols="6">
                <v-autocomplete
                  clearable
                  v-model="form.member2"
                  :label="$t('2-A\'zo')"
                  @keyup="getEmployeeList()"
                  :search-input.sync="search4"
                  :items="employees4"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                  item-text="search"
                  item-value="id"
                >
                  <template v-slot:selection="data">{{
                    data.item.full_name
                  }}</template>
                  <template v-slot:item="data">
                    <template v-if="typeof data.item !== 'object'">
                      <v-list-item-content
                        v-text="data.item"
                      ></v-list-item-content>
                    </template>
                    <template v-else>
                      <v-list-item-content>
                        <v-list-item-title
                          v-html="data.item.full_name"
                        ></v-list-item-title>
                        <v-list-item-subtitle
                          v-html="data.item.tabel"
                        ></v-list-item-subtitle>
                      </v-list-item-content>
                    </template>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col class="pa-1" cols="6">
                <v-autocomplete
                  clearable
                  v-model="form.member3"
                  :label="$t('3-A\'zo')"
                  @keyup="getEmployeeList()"
                  :search-input.sync="search5"
                  :items="employees5"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                  item-text="search"
                  item-value="id"
                >
                  <template v-slot:selection="data">{{
                    data.item.full_name
                  }}</template>
                  <template v-slot:item="data">
                    <template v-if="typeof data.item !== 'object'">
                      <v-list-item-content
                        v-text="data.item"
                      ></v-list-item-content>
                    </template>
                    <template v-else>
                      <v-list-item-content>
                        <v-list-item-title
                          v-html="data.item.full_name"
                        ></v-list-item-title>
                        <v-list-item-subtitle
                          v-html="data.item.tabel"
                        ></v-list-item-subtitle>
                      </v-list-item-content>
                    </template>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col class="pa-1" cols="6">
                <v-autocomplete
                  clearable
                  v-model="form.member4"
                  :label="$t('4-A\'zo')"
                  @keyup="getEmployeeList()"
                  :search-input.sync="search6"
                  :items="employees6"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                  item-text="search"
                  item-value="id"
                >
                  <template v-slot:selection="data">{{
                    data.item.full_name
                  }}</template>
                  <template v-slot:item="data">
                    <template v-if="typeof data.item !== 'object'">
                      <v-list-item-content
                        v-text="data.item"
                      ></v-list-item-content>
                    </template>
                    <template v-else>
                      <v-list-item-content>
                        <v-list-item-title
                          v-html="data.item.full_name"
                        ></v-list-item-title>
                        <v-list-item-subtitle
                          v-html="data.item.tabel"
                        ></v-list-item-subtitle>
                      </v-list-item-content>
                    </template>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col class="pa-1" cols="6">
                <v-autocomplete
                  clearable
                  v-model="form.member5"
                  :label="$t('5-A\'zo')"
                  @keyup="getEmployeeList()"
                  :search-input.sync="search7"
                  :items="employees7"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                  item-text="search"
                  item-value="id"
                >
                  <template v-slot:selection="data">{{
                    data.item.full_name
                  }}</template>
                  <template v-slot:item="data">
                    <template v-if="typeof data.item !== 'object'">
                      <v-list-item-content
                        v-text="data.item"
                      ></v-list-item-content>
                    </template>
                    <template v-else>
                      <v-list-item-content>
                        <v-list-item-title
                          v-html="data.item.full_name"
                        ></v-list-item-title>
                        <v-list-item-subtitle
                          v-html="data.item.tabel"
                        ></v-list-item-subtitle>
                      </v-list-item-content>
                    </template>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col class="pa-1" cols="6">
                <v-text-field
                  :label="$t('Inspektor')"
                  v-model="form.inspector"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details
                  outlined
                  dense
                ></v-text-field>
              </v-col>
              <v-col class="pa-1" cols="6">
                <v-autocomplete
                  clearable
                  v-model="form.shtab_member1"
                  :label="$t('1-Shtab A\'zosi')"
                  @keyup="getEmployeeList()"
                  :search-input.sync="search8"
                  :items="employees8"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                  item-text="search"
                  item-value="id"
                >
                  <template v-slot:selection="data">{{
                    data.item.full_name
                  }}</template>
                  <template v-slot:item="data">
                    <template v-if="typeof data.item !== 'object'">
                      <v-list-item-content
                        v-text="data.item"
                      ></v-list-item-content>
                    </template>
                    <template v-else>
                      <v-list-item-content>
                        <v-list-item-title
                          v-html="data.item.full_name"
                        ></v-list-item-title>
                        <v-list-item-subtitle
                          v-html="data.item.tabel"
                        ></v-list-item-subtitle>
                      </v-list-item-content>
                    </template>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col class="pa-1" cols="6">
                <v-autocomplete
                  clearable
                  v-model="form.shtab_member2"
                  :label="$t('2-Shtab A\'zosi')"
                  @keyup="getEmployeeList()"
                  :search-input.sync="search9"
                  :items="employees9"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                  item-text="search"
                  item-value="id"
                >
                  <template v-slot:selection="data">{{
                    data.item.full_name
                  }}</template>
                  <template v-slot:item="data">
                    <template v-if="typeof data.item !== 'object'">
                      <v-list-item-content
                        v-text="data.item"
                      ></v-list-item-content>
                    </template>
                    <template v-else>
                      <v-list-item-content>
                        <v-list-item-title
                          v-html="data.item.full_name"
                        ></v-list-item-title>
                        <v-list-item-subtitle
                          v-html="data.item.tabel"
                        ></v-list-item-subtitle>
                      </v-list-item-content>
                    </template>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col class="pa-1" cols="6">
                <v-autocomplete
                  clearable
                  v-model="form.control_group"
                  :label="$t('Nazoratchi')"
                  @keyup="getEmployeeList()"
                  :search-input.sync="search10"
                  :items="employees10"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details="auto"
                  dense
                  outlined
                  item-text="search"
                  item-value="id"
                >
                  <template v-slot:selection="data">{{
                    data.item.full_name
                  }}</template>
                  <template v-slot:item="data">
                    <template v-if="typeof data.item !== 'object'">
                      <v-list-item-content
                        v-text="data.item"
                      ></v-list-item-content>
                    </template>
                    <template v-else>
                      <v-list-item-content>
                        <v-list-item-title
                          v-html="data.item.full_name"
                        ></v-list-item-title>
                        <v-list-item-subtitle
                          v-html="data.item.tabel"
                        ></v-list-item-subtitle>
                      </v-list-item-content>
                    </template>
                  </template>
                </v-autocomplete>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="success" @click="save">Saqlash</v-btn>
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
  </div>
</template>
<script>
const axios = require("axios").default;
const moment = require("moment");
import Swal from "sweetalert2";
import Template from "../document/Template.vue";
export default {
  components: { Template },
  data() {
    return {
      form: {
        id: 0,
        commission_number: null,
        warehouse_id: null,
        models: "",
        uchastka: "",
        responsible_person: null,
        chairman: null,
        member1: null,
        member2: null,
        member3: null,
        member4: null,
        member5: null,
        inspector: "",
        shtab_member1: null,
        shtab_member2: null,
        control_group: null,
      },
      warehouses: [],
      employees1: [],
      employees2: [],
      employees3: [],
      employees4: [],
      employees5: [],
      employees6: [],
      employees7: [],
      employees8: [],
      employees9: [],
      employees10: [],
      editDialog: false,
      search1: "",
      search2: "",
      search3: "",
      search4: "",
      search5: "",
      search6: "",
      search7: "",
      search8: "",
      search9: "",
      search10: "",
      isLoading: false,
      loading: false,
      items: [],
      dataTableOptions: { page: 1, itemsPerPage: 20 },
      page: 1,
      from: 0,
      server_items_length: -1,
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 220;
    },
    headers() {
      return [
        { text: "#", value: "id", align: "center", sortable: false },
        {
          text: this.$t("Komissiya"),
          value: "commission_number",
          width: 30,
        },
        {
          text: this.$t("Ombor"),
          value: "warehouse.name",
        },
        {
          text: this.$t("responsible"),
          value: "responsible",
          sortable: false,
        },
        {
          text: this.$t("models"),
          value: "models",
          sortable: false,
        },
        {
          text: this.$t("Uchastka"),
          value: "uchastka",
          sortable: false,
        },
        {
          text: this.$t("chairman"),
          value: "chairman",
          sortable: false,
        },
        {
          text: this.$t("members"),
          value: "members",
          sortable: false,
        },
        {
          text: this.$t("inspector"),
          value: "inspector",
          sortable: false,
        },
        {
          text: this.$t("shtabMember"),
          value: "shtab_members",
          sortable: false,
        },
        {
          text: this.$t("controlGroup"),
          value: "control_group",
          sortable: false,
        },
        {
          text: "",
          value: "actions",
          width: 90,
          align: "center",
          sortable: false,
        },
      ];
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
        .post(this.$store.state.backend_url + "api/inventory-commissions", {
          pagination: this.dataTableOptions,
        })
        .then((res) => {
          this.items = res.data.commissions.data;
          this.from = res.data.commissions.from;
          this.server_items_length = res.data.commissions.total;
          this.loading = false;
        })
        .catch((err) => {
          console.log(err);
          this.loading = false;
        });
    },
    getRef() {
      this.loading = true;
      axios
        .get(this.$store.state.backend_url + "api/inventory/get-ref")
        .then((res) => {
          this.warehouses = res.data.warehouses;
          this.loading = false;
        })
        .catch((err) => {
          console.log(err);
          this.loading = false;
        });
    },
    getEmployeeList() {
      if (this.search1) {
        axios
          .post(this.$store.state.backend_url + "api/employee-search", {
            search: this.search1,
          })
          .then((res) => {
            this.employees1 = res.data.data;
            this.employees1 = this.employees1.map((v) => {
              v.full_name =
                this.$i18n.locale != "ru"
                  ? v["firstname_" + this.$i18n.locale] +
                    " " +
                    v["lastname_" + this.$i18n.locale] +
                    " " +
                    v["middlename_" + this.$i18n.locale]
                  : v.firstname_uz_cyril +
                    " " +
                    v.lastname_uz_cyril +
                    " " +
                    v.middlename_uz_cyril;
              v.search =
                v.firstname_uz_cyril +
                " " +
                v.lastname_uz_cyril +
                " " +
                v.middlename_uz_cyril +
                " " +
                v.firstname_uz_latin +
                " " +
                v.lastname_uz_latin +
                " " +
                v.middlename_uz_latin +
                " " +
                v.tabel;
              return v;
            });
          })
          .catch((err) => {
            console.error(err);
          });
      } else if (this.search2) {
        axios
          .post(this.$store.state.backend_url + "api/employee-search", {
            search: this.search2,
          })
          .then((res) => {
            this.employees2 = res.data.data;
            this.employees2 = this.employees2.map((v) => {
              v.full_name =
                this.$i18n.locale != "ru"
                  ? v["firstname_" + this.$i18n.locale] +
                    " " +
                    v["lastname_" + this.$i18n.locale] +
                    " " +
                    v["middlename_" + this.$i18n.locale]
                  : v.firstname_uz_cyril +
                    " " +
                    v.lastname_uz_cyril +
                    " " +
                    v.middlename_uz_cyril;
              v.search =
                v.firstname_uz_cyril +
                " " +
                v.lastname_uz_cyril +
                " " +
                v.middlename_uz_cyril +
                " " +
                v.firstname_uz_latin +
                " " +
                v.lastname_uz_latin +
                " " +
                v.middlename_uz_latin +
                " " +
                v.tabel;
              return v;
            });
          })
          .catch((err) => {
            console.error(err);
          });
      } else if (this.search3) {
        axios
          .post(this.$store.state.backend_url + "api/employee-search", {
            search: this.search3,
          })
          .then((res) => {
            this.employees3 = res.data.data;
            this.employees3 = this.employees3.map((v) => {
              v.full_name =
                this.$i18n.locale != "ru"
                  ? v["firstname_" + this.$i18n.locale] +
                    " " +
                    v["lastname_" + this.$i18n.locale] +
                    " " +
                    v["middlename_" + this.$i18n.locale]
                  : v.firstname_uz_cyril +
                    " " +
                    v.lastname_uz_cyril +
                    " " +
                    v.middlename_uz_cyril;
              v.search =
                v.firstname_uz_cyril +
                " " +
                v.lastname_uz_cyril +
                " " +
                v.middlename_uz_cyril +
                " " +
                v.firstname_uz_latin +
                " " +
                v.lastname_uz_latin +
                " " +
                v.middlename_uz_latin +
                " " +
                v.tabel;
              return v;
            });
          })
          .catch((err) => {
            console.error(err);
          });
      } else if (this.search4) {
        axios
          .post(this.$store.state.backend_url + "api/employee-search", {
            search: this.search4,
          })
          .then((res) => {
            this.employees4 = res.data.data;
            this.employees4 = this.employees4.map((v) => {
              v.full_name =
                this.$i18n.locale != "ru"
                  ? v["firstname_" + this.$i18n.locale] +
                    " " +
                    v["lastname_" + this.$i18n.locale] +
                    " " +
                    v["middlename_" + this.$i18n.locale]
                  : v.firstname_uz_cyril +
                    " " +
                    v.lastname_uz_cyril +
                    " " +
                    v.middlename_uz_cyril;
              v.search =
                v.firstname_uz_cyril +
                " " +
                v.lastname_uz_cyril +
                " " +
                v.middlename_uz_cyril +
                " " +
                v.firstname_uz_latin +
                " " +
                v.lastname_uz_latin +
                " " +
                v.middlename_uz_latin +
                " " +
                v.tabel;
              return v;
            });
          })
          .catch((err) => {
            console.error(err);
          });
      } else if (this.search5) {
        axios
          .post(this.$store.state.backend_url + "api/employee-search", {
            search: this.search5,
          })
          .then((res) => {
            this.employees5 = res.data.data;
            this.employees5 = this.employees5.map((v) => {
              v.full_name =
                this.$i18n.locale != "ru"
                  ? v["firstname_" + this.$i18n.locale] +
                    " " +
                    v["lastname_" + this.$i18n.locale] +
                    " " +
                    v["middlename_" + this.$i18n.locale]
                  : v.firstname_uz_cyril +
                    " " +
                    v.lastname_uz_cyril +
                    " " +
                    v.middlename_uz_cyril;
              v.search =
                v.firstname_uz_cyril +
                " " +
                v.lastname_uz_cyril +
                " " +
                v.middlename_uz_cyril +
                " " +
                v.firstname_uz_latin +
                " " +
                v.lastname_uz_latin +
                " " +
                v.middlename_uz_latin +
                " " +
                v.tabel;
              return v;
            });
          })
          .catch((err) => {
            console.error(err);
          });
      } else if (this.search6) {
        axios
          .post(this.$store.state.backend_url + "api/employee-search", {
            search: this.search6,
          })
          .then((res) => {
            this.employees6 = res.data.data;
            this.employees6 = this.employees6.map((v) => {
              v.full_name =
                this.$i18n.locale != "ru"
                  ? v["firstname_" + this.$i18n.locale] +
                    " " +
                    v["lastname_" + this.$i18n.locale] +
                    " " +
                    v["middlename_" + this.$i18n.locale]
                  : v.firstname_uz_cyril +
                    " " +
                    v.lastname_uz_cyril +
                    " " +
                    v.middlename_uz_cyril;
              v.search =
                v.firstname_uz_cyril +
                " " +
                v.lastname_uz_cyril +
                " " +
                v.middlename_uz_cyril +
                " " +
                v.firstname_uz_latin +
                " " +
                v.lastname_uz_latin +
                " " +
                v.middlename_uz_latin +
                " " +
                v.tabel;
              return v;
            });
          })
          .catch((err) => {
            console.error(err);
          });
      } else if (this.search7) {
        axios
          .post(this.$store.state.backend_url + "api/employee-search", {
            search: this.search7,
          })
          .then((res) => {
            this.employees7 = res.data.data;
            this.employees7 = this.employees7.map((v) => {
              v.full_name =
                this.$i18n.locale != "ru"
                  ? v["firstname_" + this.$i18n.locale] +
                    " " +
                    v["lastname_" + this.$i18n.locale] +
                    " " +
                    v["middlename_" + this.$i18n.locale]
                  : v.firstname_uz_cyril +
                    " " +
                    v.lastname_uz_cyril +
                    " " +
                    v.middlename_uz_cyril;
              v.search =
                v.firstname_uz_cyril +
                " " +
                v.lastname_uz_cyril +
                " " +
                v.middlename_uz_cyril +
                " " +
                v.firstname_uz_latin +
                " " +
                v.lastname_uz_latin +
                " " +
                v.middlename_uz_latin +
                " " +
                v.tabel;
              return v;
            });
          })
          .catch((err) => {
            console.error(err);
          });
      } else if (this.search8) {
        axios
          .post(this.$store.state.backend_url + "api/employee-search", {
            search: this.search8,
          })
          .then((res) => {
            this.employees8 = res.data.data;
            this.employees8 = this.employees8.map((v) => {
              v.full_name =
                this.$i18n.locale != "ru"
                  ? v["firstname_" + this.$i18n.locale] +
                    " " +
                    v["lastname_" + this.$i18n.locale] +
                    " " +
                    v["middlename_" + this.$i18n.locale]
                  : v.firstname_uz_cyril +
                    " " +
                    v.lastname_uz_cyril +
                    " " +
                    v.middlename_uz_cyril;
              v.search =
                v.firstname_uz_cyril +
                " " +
                v.lastname_uz_cyril +
                " " +
                v.middlename_uz_cyril +
                " " +
                v.firstname_uz_latin +
                " " +
                v.lastname_uz_latin +
                " " +
                v.middlename_uz_latin +
                " " +
                v.tabel;
              return v;
            });
          })
          .catch((err) => {
            console.error(err);
          });
      } else if (this.search9) {
        axios
          .post(this.$store.state.backend_url + "api/employee-search", {
            search: this.search9,
          })
          .then((res) => {
            this.employees9 = res.data.data;
            this.employees9 = this.employees9.map((v) => {
              v.full_name =
                this.$i18n.locale != "ru"
                  ? v["firstname_" + this.$i18n.locale] +
                    " " +
                    v["lastname_" + this.$i18n.locale] +
                    " " +
                    v["middlename_" + this.$i18n.locale]
                  : v.firstname_uz_cyril +
                    " " +
                    v.lastname_uz_cyril +
                    " " +
                    v.middlename_uz_cyril;
              v.search =
                v.firstname_uz_cyril +
                " " +
                v.lastname_uz_cyril +
                " " +
                v.middlename_uz_cyril +
                " " +
                v.firstname_uz_latin +
                " " +
                v.lastname_uz_latin +
                " " +
                v.middlename_uz_latin +
                " " +
                v.tabel;
              return v;
            });
          })
          .catch((err) => {
            console.error(err);
          });
      } else if (this.search10) {
        axios
          .post(this.$store.state.backend_url + "api/employee-search", {
            search: this.search10,
          })
          .then((res) => {
            this.employees10 = res.data.data;
            this.employees10 = this.employees10.map((v) => {
              v.full_name =
                this.$i18n.locale != "ru"
                  ? v["firstname_" + this.$i18n.locale] +
                    " " +
                    v["lastname_" + this.$i18n.locale] +
                    " " +
                    v["middlename_" + this.$i18n.locale]
                  : v.firstname_uz_cyril +
                    " " +
                    v.lastname_uz_cyril +
                    " " +
                    v.middlename_uz_cyril;
              v.search =
                v.firstname_uz_cyril +
                " " +
                v.lastname_uz_cyril +
                " " +
                v.middlename_uz_cyril +
                " " +
                v.firstname_uz_latin +
                " " +
                v.lastname_uz_latin +
                " " +
                v.middlename_uz_latin +
                " " +
                v.tabel;
              return v;
            });
          })
          .catch((err) => {
            console.error(err);
          });
      }
    },
    newCommission() {
      this.form = {
        id: 0,
        commission_number: null,
        warehouse_id: null,
        models: "",
        uchastka: "",
        responsible_person: null,
        chairman: null,
        member1: null,
        member2: null,
        member3: null,
        member4: null,
        member5: null,
        inspector: "",
        shtab_member1: null,
        shtab_member2: null,
        control_group: null,
      };
      this.editDialog = true;
      this.DialogHeaderText = this.$t("Kommissiya kiritish");
      if (this.$refs.inventorCreateForm) this.$refs.inventorCreateForm.reset();
    },
    editItem(item) {
      this.editDialog = true;
      this.DialogHeaderText = this.$t("commissions.edit");
      this.form = JSON.parse(JSON.stringify(item));
    },
    save() {
      if (this.$refs.inventorCreateForm.validate()) {
        this.loading = true;
        axios
          .post(
            this.$store.state.backend_url + "api/inventory-commissions/update",
            this.form
          )
          .then((res) => {
            this.editDialog = false;
            this.getList();
            const Toast = Swal.mixin({
              toast: true,
              position: "top-end",
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true,
            });
            Toast.fire({
              icon: "success",
              title: this.$t("create_update_operation"),
            });
            this.$refs.inventorCreateForm.resetValidation();
            this.getList();
            this.loading = false;
          })
          .catch((err) => {
            console.log(err);
            this.loading = false;
          });
      }
    },
    deleteItem(item) {
      Swal.fire({
        title: this.$t("delete"),
        text: this.$t("commission.delete"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: this.$t("delete"),
        cancelButtonText: this.$t("close"),
      }).then((result) => {
        if (result.value) {
          this.loading = true;
          axios
            .delete(
              this.$store.state.backend_url +
                "api/inventory-commissions/delete/" +
                item.id
            )
            .then((res) => {
              this.items = this.items.filter((v) => v.id != item.id);
              Swal.fire({
                position: "top-end",
                toast: true,
                icon: "success",
                title: this.$t("swal_deleted"),
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
              });
              this.getList();
              this.loading = false;
            })
            .catch((error) => {
              console.error(error);
              this.getList();
              this.loading = false;
              Swal.fire({
                position: "center",
                icon: "error",
                width: "250px",
                title: this.$t("swal_error_text"),
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
              });
            });
        }
      });
    },
  },
  mounted() {
    this.getRef();
    this.getList();
  },
};
</script>
