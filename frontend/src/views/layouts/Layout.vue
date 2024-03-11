<template>
  <v-app style="background-color: #eef0f7" class="overflow-y-auto">
    <v-app-bar app color="#fffeff" class="elevation-0">
      <v-app-bar-nav-icon @click.stop="drawer = !drawer">
        <v-icon>mdi-menu</v-icon>
      </v-app-bar-nav-icon>

      <v-toolbar-title>
        <v-row :class="drawer ? 'ml-1' : ''">
          <v-col>
            <v-col cols="12" class="pa-0 caption">
              {{ $store.state["COMPANY_NAME_" + $i18n.locale.toUpperCase()] }}
            </v-col>
            <v-col cols="12" class="pa-0 caption">
              {{ $t("contacts") }}:
              <span class="font-weight-bold" v-if="$store.state.PHONE_IT"
                >IT:</span
              >
              {{ $store.state.PHONE_IT }}
              <span class="font-weight-bold" v-if="$store.state.PHONE_PM"
                >HR:</span
              >
              {{ $store.state.PHONE_PM }}
              <span
                class="font-weight-bold"
                v-if="$store.state.PHONE_PM_TASHKENT"
                >{{ $t("PM_tashkent") }}</span
              >
              {{ $store.state.PHONE_PM_TASHKENT }}
            </v-col>
          </v-col>
        </v-row>
      </v-toolbar-title>

      <v-spacer></v-spacer>

      <v-btn
        v-if="user && !user.type"
        text
        small
        color="white"
        class="px-2 mx-1"
        to="/timeline"
        :title="$t('timeline.index')"
      >
        <v-icon color="blue" large>mdi-forum-outline</v-icon>

        <v-badge
          v-show="timeline_count"
          color="red"
          :content="timeline_count"
          class="py-2"
        ></v-badge>
      </v-btn>

      <v-btn
        text
        small
        color="green"
        v-if="$store.getters.checkPermission('view_online*')"
        :class="notification && notification.length_info ? '' : 'mr-6'"
        to="/users/online"
      >
        <v-badge color="green" :content="notification.online">
          {{ $t("online") }}
        </v-badge>
      </v-btn>

      <v-menu offset-y>
        <template v-slot:activator="{ on }">
          <v-btn text outlined small v-on="on">
            <v-icon left>mdi-flag</v-icon>
            {{ languages[$i18n.locale] }}
          </v-btn>
        </template>
        <v-list>
          <v-list-item
            v-for="(item, index) in locales"
            :key="index"
            @click="setLocale(item.value)"
          >
            <v-list-item-title>{{ item.text }}</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>

      <!-- ides notifications -->
      <v-menu
        offset-y
        v-if="
          idesDocCount &&
          idesDocCount != 0 &&
          drawerShow &&
          $store.getters.checkPermission('ides') &&
          !$store.getters.checkPermission('ides-control')
        "
      >
        <template v-slot:activator="{ on }">
          <v-btn
            text
            small
            class="ml-1"
            :title="$t('control_punkt.name')"
            to="/ides/index/new-pending"
          >
            <v-badge color="warning" :content="idesDocCount">
              <v-icon color="warning">mdi-security</v-icon>
            </v-badge>

            <!-- <v-icon v-else>mdi-email-outline</v-icon> -->
          </v-btn>
        </template>

        <v-card v-if="idesDocList">
          <v-list
            class="py-0"
            style="cursor: pointer"
            dense
            v-for="(item, index) in idesDocList"
            :key="index"
          >
            <v-row>
              <v-list-item :to="'/ides/show/' + item.id">
                <v-col cols="9" class="py-0">
                  <v-list-item-title>
                    <strong>
                      {{ item.document_number }}
                    </strong>
                    <br />
                    {{
                      item.document_date.substr(0, 10) +
                      " " +
                      item.document_date.substr(11, 5)
                    }}
                  </v-list-item-title>
                </v-col>
                <v-col cols="3" class="py-0">
                  <v-list-item-title v-if="item.document_date">
                    {{
                      item.document_date.substr(0, 10) +
                      " " +
                      item.document_date.substr(11, 5)
                    }}
                  </v-list-item-title>
                </v-col>
                <v-divider></v-divider>
              </v-list-item>
            </v-row>
          </v-list>

          <v-list class="py-0" style="cursor: pointer" dense>
            <v-list-item to="/ides/index/new-pending">
              <v-list-item-title>{{ $t("message.all") }}</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-card>

        <v-list v-else>
          <v-list-item>
            <v-list-item-title>{{ $t("message.noAlerts") }}</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
      <!-- ides notifications -->

      <!-- ides notifications ides control -->
      <v-menu
        offset-y
        v-if="
          idesDocCountReceived &&
          idesDocCountReceived != 0 &&
          drawerShow &&          
          ($store.getters.checkPermission('ides-control') ||
          $store.getters.checkPermission('ides'))
        "
      >
        <template v-slot:activator="{ on }">
          <v-btn
            text
            small
            class="ml-1"
            :title="$t('control_punkt.name')"
            to="/ides/index/pending-control"
          >
            <v-badge color="green" :content="idesDocCountReceived">
              <v-icon color="green">mdi-security</v-icon>
            </v-badge>

            <!-- <v-icon v-else>mdi-email-outline</v-icon> -->
          </v-btn>
        </template>

        <v-card v-if="idesDocList">
          <v-list
            class="py-0"
            style="cursor: pointer"
            dense
            v-for="(item, index) in idesDocList"
            :key="index"
          >
            <v-row>
              <v-list-item :to="'/ides/show/' + item.id">
                <v-col cols="9" class="py-0">
                  <v-list-item-title>
                    <strong>
                      {{ item.document_number }}
                    </strong>
                    <br />
                    {{
                      item.document_date.substr(0, 10) +
                      " " +
                      item.document_date.substr(11, 5)
                    }}
                  </v-list-item-title>
                </v-col>
                <v-col cols="3" class="py-0">
                  <v-list-item-title v-if="item.document_date">
                    {{
                      item.document_date.substr(0, 10) +
                      " " +
                      item.document_date.substr(11, 5)
                    }}
                  </v-list-item-title>
                </v-col>
                <v-divider></v-divider>
              </v-list-item>
            </v-row>
          </v-list>

          <v-list class="py-0" style="cursor: pointer" dense>
            <v-list-item to="/ides/index/pending-control">
              <v-list-item-title>{{ $t("message.all") }}</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-card>

        <v-list v-else>
          <v-list-item>
            <v-list-item-title>{{ $t("message.noAlerts") }}</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
      <!-- ides notifications -->

      <v-menu offset-y v-if="notification && notification.length_akt">
        <template v-slot:activator="{ on }">
          <v-btn
            text
            small
            to="/documents/list/nazorat/0"
            class="ml-2"
            :title="$t('notification.akt')"
          >
            <v-badge
              color="orange"
              v-if="notification && notification.length_akt"
              :content="notification && notification.length_akt"
            >
              <v-icon color="red">mdi-repeat</v-icon>
            </v-badge>
          </v-btn>
        </template>
        <v-card v-if="notification && notification.length_akt">
          <v-list
            class="py-0"
            style="cursor: pointer"
            dense
            v-for="(item, index) in notification.akt"
            :key="index"
          >
            <v-row>
              <v-list-item :to="'/document/' + item.pdf_file_name">
                <v-col cols="9" class="py-0">
                  <v-list-item-title>
                    <strong>
                      {{ item.document_type["name_" + $i18n.locale] }}
                    </strong>
                    {{ item.document_template["name_" + $i18n.locale] }}
                    <br />
                    {{
                      item.document_number_reg
                        ? item.document_number_reg
                        : item.document_number
                    }}
                  </v-list-item-title>
                </v-col>
                <v-col cols="3" class="py-0">
                  <v-list-item-title v-if="item.document_date_reg">
                    {{
                      item.document_date_reg.substr(0, 10) +
                      " " +
                      item.document_date_reg.substr(11, 5)
                    }}
                  </v-list-item-title>
                  <v-list-item-title v-else>
                    {{
                      item.document_date.substr(0, 10) +
                      " " +
                      item.document_date.substr(11, 5)
                    }}
                  </v-list-item-title>
                </v-col>
                <v-divider></v-divider>
              </v-list-item>
            </v-row>
          </v-list>

          <v-list class="py-0" style="cursor: pointer" dense>
            <v-list-item to="/documents/list/nazorat/0">
              <v-list-item-title>{{ $t("message.all") }}</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-card>
        <v-list v-else>
          <v-list-item>
            <v-list-item-title>{{ $t("message.noAlerts") }}</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>

      <v-menu
        offset-y
        v-if="notification && notification.length_nazorat && drawerShow"
      >
        <template v-slot:activator="{ on }">
          <v-btn
            text
            small
            to="/documents/list/nazorat/0"
            class="ml-2"
            :title="$t('notification.nazorat')"
          >
            <v-badge
              color="red"
              v-if="notification && notification.length_nazorat"
              :content="notification && notification.length_nazorat"
            >
              <v-icon color="red">mdi-order-bool-ascending-variant</v-icon>
            </v-badge>
          </v-btn>
        </template>
        <v-card v-if="notification && notification.length_nazorat">
          <v-list
            class="py-0"
            style="cursor: pointer"
            dense
            v-for="(item, index) in notification.nazorat"
            :key="index"
          >
            <v-row>
              <v-list-item :to="'/document/' + item.pdf_file_name">
                <v-col cols="9" class="py-0">
                  <v-list-item-title>
                    <strong>
                      {{ item.document_type["name_" + $i18n.locale] }}
                    </strong>
                    {{ item.document_template["name_" + $i18n.locale] }}
                    <br />
                    {{
                      item.document_number_reg
                        ? item.document_number_reg
                        : item.document_number
                    }}
                  </v-list-item-title>
                </v-col>
                <v-col cols="3" class="py-0">
                  <v-list-item-title v-if="item.document_date_reg">
                    {{
                      item.document_date_reg.substr(0, 10) +
                      " " +
                      item.document_date_reg.substr(11, 5)
                    }}
                  </v-list-item-title>
                  <v-list-item-title v-else>
                    {{
                      item.document_date.substr(0, 10) +
                      " " +
                      item.document_date.substr(11, 5)
                    }}
                  </v-list-item-title>
                </v-col>
                <v-divider></v-divider>
              </v-list-item>
            </v-row>
          </v-list>

          <v-list class="py-0" style="cursor: pointer" dense>
            <v-list-item to="/documents/list/nazorat/0">
              <v-list-item-title>{{ $t("message.all") }}</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-card>
        <v-list v-else>
          <v-list-item>
            <v-list-item-title>{{ $t("message.noAlerts") }}</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>

      <!-- sardor -->

      <!-- kpi notification -->

      <v-menu
        offset-y
        v-if="notification && notification.length_kpi_resolution && drawerShow"
      >
        <template v-slot:activator="{ on }">
          <v-btn
            text
            small
            v-on="on"
            class="ml-2"
            :to="'/kpi-assistant'"
            :title="$t('KPI')"
          >
            <v-badge
              color="light-blue"
              v-if="notification && notification.length_kpi_resolution_new"
              :content="notification && notification.length_kpi_resolution_new"
            >
              <v-icon color="light-blue">mdi-finance</v-icon>
            </v-badge>
            <v-icon v-else color="light-blue">mdi-finance</v-icon>
          </v-btn>
        </template>
      </v-menu>
      <!-- kpi notification -->
      <!-- <template> -->
      <v-menu
        offset-y
        v-if="
          notification && notification.length_document_out_three && drawerShow
        "
      >
        <template v-slot:activator="{ on }">
          <v-btn
            text
            small
            to="/documents/list/document_out_three/0"
            class="ml-2"
            :title="$t('notification.document_out_three')"
          >
            <v-badge
              color="light-blue"
              v-if="notification && notification.length_document_out_three"
              :content="notification && notification.length_document_out_three"
            >
              <v-icon color="light-blue">mdi-numeric-3-box-multiple</v-icon>
            </v-badge>
          </v-btn>
        </template>
        <v-card v-if="notification && notification.length_document_out_three">
          <v-list
            class="py-0"
            style="cursor: pointer"
            dense
            v-for="(item, index) in notification.document_out_three"
            :key="index"
          >
            <v-row>
              <v-list-item :to="'/document/' + item.pdf_file_name">
                <v-col cols="9" class="py-0">
                  <v-list-item-title>
                    <strong>
                      {{ item.document_type["name_" + $i18n.locale] }}
                    </strong>
                    {{ item.document_template["name_" + $i18n.locale] }}
                    <br />
                    {{
                      item.document_number_reg
                        ? item.document_number_reg
                        : item.document_number
                    }}
                  </v-list-item-title>
                </v-col>
                <v-col cols="3" class="py-0">
                  <v-list-item-title v-if="item.document_date_reg">
                    {{
                      item.document_date_reg.substr(0, 10) +
                      " " +
                      item.document_date_reg.substr(11, 5)
                    }}
                  </v-list-item-title>
                  <v-list-item-title v-else>
                    {{
                      item.document_date.substr(0, 10) +
                      " " +
                      item.document_date.substr(11, 5)
                    }}
                  </v-list-item-title>
                </v-col>
                <v-divider></v-divider>
              </v-list-item>
            </v-row>
          </v-list>

          <v-list class="py-0" style="cursor: pointer" dense>
            <v-list-item to="/documents/list/document_out_three/0">
              <v-list-item-title>{{ $t("message.all") }}</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-card>
        <v-list v-else>
          <v-list-item>
            <v-list-item-title>{{ $t("message.noAlerts") }}</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>

      <v-menu
        offset-y
        v-if="
          notification && notification.length_document_out_two && drawerShow
        "
      >
        <template v-slot:activator="{ on }">
          <v-btn
            text
            small
            to="/documents/list/document_out_two/0"
            class="ml-2"
            :title="$t('notification.document_out_two')"
          >
            <v-badge
              color="#8A2BE2"
              v-if="notification && notification.length_document_out_two"
              :content="notification && notification.length_document_out_two"
            >
              <v-icon color="#8A2BE2">mdi-numeric-2-box-multiple</v-icon>
            </v-badge>
          </v-btn>
        </template>
        <v-card v-if="notification && notification.length_document_out_two">
          <v-list
            class="py-0"
            style="cursor: pointer"
            dense
            v-for="(item, index) in notification.document_out_two"
            :key="index"
          >
            <v-row>
              <v-list-item :to="'/document/' + item.pdf_file_name">
                <v-col cols="9" class="py-0">
                  <v-list-item-title>
                    <strong>
                      {{ item.document_type["name_" + $i18n.locale] }}
                    </strong>
                    {{ item.document_template["name_" + $i18n.locale] }}
                    <br />
                    {{
                      item.document_number_reg
                        ? item.document_number_reg
                        : item.document_number
                    }}
                  </v-list-item-title>
                </v-col>
                <v-col cols="3" class="py-0">
                  <v-list-item-title v-if="item.document_date_reg">
                    {{
                      item.document_date_reg.substr(0, 10) +
                      " " +
                      item.document_date_reg.substr(11, 5)
                    }}
                  </v-list-item-title>
                  <v-list-item-title v-else>
                    {{
                      item.document_date.substr(0, 10) +
                      " " +
                      item.document_date.substr(11, 5)
                    }}
                  </v-list-item-title>
                </v-col>
                <v-divider></v-divider>
              </v-list-item>
            </v-row>
          </v-list>
          <v-list class="py-0" style="cursor: pointer" dense>
            <v-list-item to="/documents/list/document_out_two/0">
              <v-list-item-title>{{ $t("message.all") }}</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-card>
        <v-list v-else>
          <v-list-item>
            <v-list-item-title>{{ $t("message.noAlerts") }}</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>

      <v-menu
        offset-y
        v-if="
          notification && notification.length_document_out_one && drawerShow
        "
      >
        <template v-slot:activator="{ on }">
          <v-btn
            text
            small
            class="ml-2"
            :title="$t('notification.document_out_one')"
            to="/documents/list/document_out_one/0"
          >
            <v-badge
              color="red"
              v-if="notification && notification.length_document_out_one"
              :content="notification && notification.length_document_out_one"
            >
              <v-icon color="red">mdi-numeric-1-box-multiple</v-icon>
            </v-badge>
          </v-btn>
        </template>
        <v-card v-if="notification && notification.length_document_out_one">
          <v-list
            class="py-0"
            style="cursor: pointer"
            dense
            v-for="(item, index) in notification.document_out_one"
            :key="index"
          >
            <v-row>
              <v-list-item :to="'/document/' + item.pdf_file_name">
                <v-col cols="9" class="py-0">
                  <v-list-item-title>
                    <strong>
                      {{ item.document_type["name_" + $i18n.locale] }}
                    </strong>
                    {{ item.document_template["name_" + $i18n.locale] }}
                    <br />
                    {{
                      item.document_number_reg
                        ? item.document_number_reg
                        : item.document_number
                    }}
                  </v-list-item-title>
                </v-col>
                <v-col cols="3" class="py-0">
                  <v-list-item-title v-if="item.document_date_reg">
                    {{
                      item.document_date_reg.substr(0, 10) +
                      " " +
                      item.document_date_reg.substr(11, 5)
                    }}
                  </v-list-item-title>
                  <v-list-item-title v-else>
                    {{
                      item.document_date.substr(0, 10) +
                      " " +
                      item.document_date.substr(11, 5)
                    }}
                  </v-list-item-title>
                </v-col>
                <v-divider></v-divider>
              </v-list-item>
            </v-row>
          </v-list>

          <v-list class="py-0" style="cursor: pointer" dense>
            <v-list-item to="/documents/list/document_out_one/0">
              <v-list-item-title>{{ $t("message.all") }}</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-card>

        <v-list v-else>
          <v-list-item>
            <v-list-item-title>{{ $t("message.noAlerts") }}</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>

      <!-- </template> -->
      <!-- sardor -->

      <v-menu
        offset-y
        v-if="notification && notification.length_star && drawerShow"
      >
        <template v-slot:activator="{ on }">
          <v-btn text small to="/documents/list/star/0" class="ml-2" :title="$t('star')">
            <v-badge
              color="#85d5ff"
              v-if="notification && notification.length_star"
              :content="notification && notification.length_star"
            >
              <v-icon color>mdi-star-outline</v-icon>
            </v-badge>
          </v-btn>
        </template>
        <v-card v-if="notification && notification.length_star">
          <v-list
            class="py-0"
            style="cursor: pointer"
            dense
            v-for="(item, index) in notification.star"
            :key="index"
          >
            <v-row>
              <v-list-item :to="'/document/' + item.pdf_file_name">
                <v-col cols="9" class="py-0">
                  <v-list-item-title>
                    <strong>
                      {{ item.document_type["name_" + $i18n.locale] }}
                    </strong>
                    {{ item.document_template["name_" + $i18n.locale] }}
                    <br />
                    {{
                      item.document_number_reg
                        ? item.document_number_reg
                        : item.document_number
                    }}
                  </v-list-item-title>
                </v-col>
                <v-col cols="3" class="py-0">
                  <v-list-item-title v-if="item.document_date_reg">
                    {{
                      item.document_date_reg.substr(0, 10) +
                      " " +
                      item.document_date_reg.substr(11, 5)
                    }}
                  </v-list-item-title>
                  <v-list-item-title v-else>
                    {{
                      item.document_date.substr(0, 10) +
                      " " +
                      item.document_date.substr(11, 5)
                    }}
                  </v-list-item-title>
                </v-col>
                <v-divider></v-divider>
              </v-list-item>
            </v-row>
          </v-list>

          <v-list class="py-0" style="cursor: pointer" dense>
            <v-list-item to="/documents/list/star/0">
              <v-list-item-title>{{ $t("message.all") }}</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-card>

        <v-list v-else>
          <v-list-item>
            <v-list-item-title>{{ $t("message.noAlerts") }}</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>

      <v-menu
        offset-y
        v-if="notification && notification.length_info && drawerShow"
      >
        <template v-slot:activator="{ on }">
          <v-btn text small to="/documents/list/information/0" class="ml-1" :title="$t('for_info')">
            <v-badge
              color="grey darken-2"
              :content="notification && notification.length_info"
            >
              <v-icon color="black">mdi-information-variant</v-icon>
            </v-badge>

            <!-- <v-icon v-else>mdi-email-outline</v-icon> -->
          </v-btn>
        </template>
        <v-card v-if="notification && notification.length_info">
          <v-list
            class="py-0"
            style="cursor: pointer"
            dense
            v-for="(item, index) in notification.information"
            :key="index"
          >
            <v-row>
              <v-list-item :to="'/document/' + item.pdf_file_name">
                <v-col cols="9" class="py-0">
                  <v-list-item-title>
                    <strong>
                      {{ item.document_type["name_" + $i18n.locale] }}
                    </strong>
                    {{ item.document_template["name_" + $i18n.locale] }}
                    <br />
                    {{
                      item.document_number_reg
                        ? item.document_number_reg
                        : item.document_number
                    }}
                  </v-list-item-title>
                </v-col>
                <v-col cols="3" class="py-0">
                  <v-list-item-title v-if="item.document_date_reg">
                    {{
                      item.document_date_reg.substr(0, 10) +
                      " " +
                      item.document_date_reg.substr(11, 5)
                    }}
                  </v-list-item-title>
                  <v-list-item-title v-else>
                    {{
                      item.document_date.substr(0, 10) +
                      " " +
                      item.document_date.substr(11, 5)
                    }}
                  </v-list-item-title>
                </v-col>
                <v-divider></v-divider>
              </v-list-item>
            </v-row>
          </v-list>

          <v-list class="py-0" style="cursor: pointer" dense>
            <v-list-item to="/documents/list/information/0">
              <v-list-item-title>{{ $t("") }}</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-card>

        <v-list v-else>
          <v-list-item>
            <v-list-item-title>{{ $t("message.noAlerts") }}</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>

      <v-menu
        offset-y
        v-if="notification && notification.length_prosesing && drawerShow"
      >
        <template v-slot:activator="{ on }">
          <v-btn text small to="/documents/list/processing/0" :title="$t('pending')">
            <v-badge
              color="deep-purple darken-2"
              v-if="notification && notification.length_prosesing"
              :content="notification && notification.length_prosesing"
            >
              <v-icon>mdi-timer-sand</v-icon>
            </v-badge>

            <v-icon v-else>mdi-timer-sand</v-icon>
          </v-btn>
        </template>
        <v-card v-if="notification && notification.length_prosesing">
          <v-list
            class="py-0"
            style="cursor: pointer"
            dense
            v-for="(item, index) in notification.prosesing"
            :key="index"
          >
            <v-row>
              <v-list-item :to="'/document/' + item.pdf_file_name">
                <v-col cols="9" class="py-0">
                  <v-list-item-title>
                    <strong>
                      {{ item.document_type["name_" + $i18n.locale] }}
                    </strong>
                    {{ item.document_template["name_" + $i18n.locale] }}
                    <br />
                    {{
                      item.document_number_reg
                        ? item.document_number_reg
                        : item.document_number
                    }}
                  </v-list-item-title>
                </v-col>
                <v-col cols="3" class="py-0">
                  <v-list-item-title v-if="item.document_date_reg">
                    {{
                      item.document_date_reg.substr(0, 10) +
                      " " +
                      item.document_date_reg.substr(11, 5)
                    }}
                  </v-list-item-title>
                  <v-list-item-title v-else>
                    {{
                      item.document_date.substr(0, 10) +
                      " " +
                      item.document_date.substr(11, 5)
                    }}
                  </v-list-item-title>
                </v-col>
                <v-divider></v-divider>
              </v-list-item>
            </v-row>
          </v-list>

          <v-list class="py-0" style="cursor: pointer" dense>
            <v-list-item to="/documents/list/processing/0">
              <v-list-item-title>{{ $t("message.all") }}</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-card>

        <v-list v-else>
          <v-list-item>
            <v-list-item-title>{{ $t("message.noAlerts") }}</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>

      <v-menu
        offset-y
        v-if="notification && notification.length_substantiate && drawerShow"
      >
        <template v-slot:activator="{ on }">
          <v-btn text small to="/documents/list/substantiate/0" class="ml-2" :title="$t('substantiate')">
            <v-badge
              color="orange darken-2"
              v-if="notification && notification.length_substantiate"
              :content="notification && notification.length_substantiate"
            >
              <v-icon>mdi-alert-outline</v-icon>
            </v-badge>

            <v-icon v-else>mdi-alert-outline</v-icon>
          </v-btn>
        </template>
        <v-card v-if="notification && notification.length_substantiate">
          <v-list
            class="py-0"
            style="cursor: pointer"
            dense
            v-for="(item, index) in notification.substantiate"
            :key="index"
          >
            <v-row>
              <v-list-item :to="'/document/' + item.pdf_file_name">
                <v-col cols="9" class="py-0">
                  <v-list-item-title>
                    <strong>
                      {{ item.document_type["name_" + $i18n.locale] }}
                    </strong>
                    {{ item.document_template["name_" + $i18n.locale] }}
                    <br />
                    {{
                      item.document_number_reg
                        ? item.document_number_reg
                        : item.document_number
                    }}
                  </v-list-item-title>
                </v-col>
                <v-col cols="3" class="py-0">
                  <v-list-item-title v-if="item.document_date_reg">
                    {{
                      item.document_date_reg.substr(0, 10) +
                      " " +
                      item.document_date_reg.substr(11, 5)
                    }}
                  </v-list-item-title>
                  <v-list-item-title v-else>
                    {{
                      item.document_date.substr(0, 10) +
                      " " +
                      item.document_date.substr(11, 5)
                    }}
                  </v-list-item-title>
                </v-col>
                <v-divider></v-divider>
              </v-list-item>
            </v-row>
          </v-list>

          <v-list class="py-0" style="cursor: pointer" dense>
            <v-list-item to="/documents/list/substantiate/0">
              <v-list-item-title>{{ $t("message.all") }}</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-card>

        <v-list v-else>
          <v-list-item>
            <v-list-item-title>{{ $t("message.noAlerts") }}</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
      <v-menu
        offset-y
        v-if="notification && notification.length_executor && drawerShow"
      >
        <template v-slot:activator="{ on }">
          <v-btn text small to="/documents/list/executor/0" class="ml-1" :title="$t('executor')">
            <v-badge
              color="pink darken-4"
              v-if="notification && notification.length_executor"
              :content="notification && notification.length_executor"
            >
              <v-icon>mdi-lightning-bolt-outline</v-icon>
            </v-badge>

            <v-icon v-else>mdi-lightning-bolt-outline</v-icon>
          </v-btn>
        </template>
        <v-card v-if="notification && notification.length_executor">
          <v-list
            class="py-0"
            style="cursor: pointer"
            dense
            v-for="(item, index) in notification.executor"
            :key="index"
          >
            <v-row>
              <v-list-item :to="'/document/' + item.pdf_file_name">
                <v-col cols="9" class="py-0">
                  <v-list-item-title>
                    <strong>
                      {{ item.document_type["name_" + $i18n.locale] }}
                    </strong>
                    {{ item.document_template["name_" + $i18n.locale] }}
                    <br />
                    {{
                      item.document_number_reg
                        ? item.document_number_reg
                        : item.document_number
                    }}
                  </v-list-item-title>
                </v-col>
                <v-col cols="3" class="py-0">
                  <v-list-item-title v-if="item.document_date_reg">
                    {{
                      item.document_date_reg.substr(0, 10) +
                      " " +
                      item.document_date_reg.substr(11, 5)
                    }}
                  </v-list-item-title>
                  <v-list-item-title v-else>
                    {{
                      item.document_date.substr(0, 10) +
                      " " +
                      item.document_date.substr(11, 5)
                    }}
                  </v-list-item-title>
                </v-col>
                <v-divider></v-divider>
              </v-list-item>
            </v-row>
          </v-list>

          <v-list class="py-0" style="cursor: pointer" dense>
            <v-list-item to="/documents/list/executor/0">
              <v-list-item-title>{{ $t("message.all") }}</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-card>

        <v-list v-else>
          <v-list-item>
            <v-list-item-title>{{ $t("message.noAlerts") }}</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>

      <v-menu
        offset-y
        v-if="notification && notification.length_resolutions && drawerShow"
      >
        <template v-slot:activator="{ on }">
          <v-btn text small to="/documents/list/resolutions/0" class="ml-1" :title="$t('resolutions')">
            <v-badge
              color="light-blue darken-4"
              v-if="notification && notification.length_resolutions"
              :content="notification && notification.length_resolutions"
            >
              <v-icon>mdi-bell-plus-outline</v-icon>
            </v-badge>

            <v-icon v-else>mdi-bell-plus-outline</v-icon>
          </v-btn>
        </template>
        <v-card v-if="notification && notification.length_resolutions">
          <v-list
            class="py-0"
            style="cursor: pointer"
            dense
            v-for="(item, index) in notification.resolutions"
            :key="index"
          >
            <v-row>
              <v-list-item :to="'/document/' + item.pdf_file_name">
                <v-col cols="9" class="py-0">
                  <v-list-item-title>
                    <strong>
                      {{ item.document_type["name_" + $i18n.locale] }}
                    </strong>
                    {{ item.document_template["name_" + $i18n.locale] }}
                    <br />
                    {{
                      item.document_number_reg
                        ? item.document_number_reg
                        : item.document_number
                    }}
                  </v-list-item-title>
                </v-col>
                <v-col cols="3" class="py-0">
                  <v-list-item-title v-if="item.document_date_reg">
                    {{
                      item.document_date_reg.substr(0, 10) +
                      " " +
                      item.document_date_reg.substr(11, 5)
                    }}
                  </v-list-item-title>
                  <v-list-item-title v-else>
                    {{
                      item.document_date.substr(0, 10) +
                      " " +
                      item.document_date.substr(11, 5)
                    }}
                  </v-list-item-title>
                </v-col>
                <v-divider></v-divider>
              </v-list-item>
            </v-row>
          </v-list>

          <v-list class="py-0" style="cursor: pointer" dense>
            <v-list-item to="/documents/list/resolutions/0">
              <v-list-item-title>{{ $t("message.all") }}</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-card>

        <v-list v-else>
          <v-list-item>
            <v-list-item-title>{{ $t("message.noAlerts") }}</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>

      <v-menu
        offset-y
        v-if="notification && notification.length_results && drawerShow"
      >
        <template v-slot:activator="{ on }">
          <v-btn
            text
            small
            to="/documents/list/resolution_results/0"
            class="ml-1"
            :title="$t('resolution_results')"
          >
            <v-badge
              color="light-blue darken-4"
              v-if="notification && notification.length_results"
              :content="notification && notification.length_results"
            >
              <v-icon>mdi-bell-check-outline</v-icon>
            </v-badge>

            <v-icon v-else>mdi-bell-check-outline</v-icon>
          </v-btn>
        </template>
        <v-card v-if="notification && notification.length_results">
          <v-list
            class="py-0"
            style="cursor: pointer"
            dense
            v-for="(item, index) in notification.resolution_results"
            :key="index"
          >
            <v-row>
              <v-list-item :to="'/document/' + item.pdf_file_name">
                <v-col cols="9" class="py-0">
                  <v-list-item-title>
                    <strong>
                      {{ item.document_type["name_" + $i18n.locale] }}
                    </strong>
                    {{ item.document_template["name_" + $i18n.locale] }}
                    <br />
                    {{
                      item.document_number_reg
                        ? item.document_number_reg
                        : item.document_number
                    }}
                  </v-list-item-title>
                </v-col>
                <v-col cols="3" class="py-0">
                  <v-list-item-title v-if="item.document_date_reg">
                    {{
                      item.document_date_reg.substr(0, 10) +
                      " " +
                      item.document_date_reg.substr(11, 5)
                    }}
                  </v-list-item-title>
                  <v-list-item-title v-else>
                    {{
                      item.document_date.substr(0, 10) +
                      " " +
                      item.document_date.substr(11, 5)
                    }}
                  </v-list-item-title>
                </v-col>
                <v-divider></v-divider>
              </v-list-item>
            </v-row>
          </v-list>

          <v-list class="py-0" style="cursor: pointer" dense>
            <v-list-item to="/documents/list/resolution_results/0">
              <v-list-item-title>{{ $t("message.all") }}</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-card>

        <v-list v-else>
          <v-list-item>
            <v-list-item-title>{{ $t("message.noAlerts") }}</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>

      <v-menu
        offset-y
        v-if="notification && notification.length_watcher && drawerShow"
      >
        <template v-slot:activator="{ on }">
          <v-btn text small to="/documents/list/watcher/0" class="ml-1" :title="$t('expired')">
            <v-badge
              color="success darken-2"
              :content="notification && notification.length_watcher"
            >
              <v-icon color>mdi-magnify</v-icon>
            </v-badge>

            <!-- <v-icon v-else>mdi-email-outline</v-icon> -->
          </v-btn>
        </template>
        <v-card v-if="notification && notification.length_watcher">
          <v-list
            class="py-0"
            style="cursor: pointer"
            dense
            v-for="(item, index) in notification.watcher"
            :key="index"
          >
            <v-row>
              <v-list-item :to="'/document/' + item.pdf_file_name">
                <v-col cols="9" class="py-0">
                  <v-list-item-title>
                    <strong>
                      {{ item.document_type["name_" + $i18n.locale] }}
                    </strong>
                    {{ item.document_template["name_" + $i18n.locale] }}
                    <br />
                    {{
                      item.document_number_reg
                        ? item.document_number_reg
                        : item.document_number
                    }}
                  </v-list-item-title>
                </v-col>
                <v-col cols="3" class="py-0">
                  <v-list-item-title v-if="item.document_date_reg">
                    {{
                      item.document_date_reg.substr(0, 10) +
                      " " +
                      item.document_date_reg.substr(11, 5)
                    }}
                  </v-list-item-title>
                  <v-list-item-title v-else>
                    {{
                      item.document_date.substr(0, 10) +
                      " " +
                      item.document_date.substr(11, 5)
                    }}
                  </v-list-item-title>
                </v-col>
                <v-divider></v-divider>
              </v-list-item>
            </v-row>
          </v-list>

          <v-list class="py-0" style="cursor: pointer" dense>
            <v-list-item to="/documents/list/watcher/0">
              <v-list-item-title>{{ $t("message.all") }}</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-card>

        <v-list v-else>
          <v-list-item>
            <v-list-item-title>{{ $t("message.noAlerts") }}</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>

      <v-menu offset-y v-if="notification && notification.length_expired">
        <template v-slot:activator="{ on }">
          <v-btn text small to="/documents/list/expired/0" class="ml-1" :title="$t('expired')">
            <v-badge
              color="red darken-2"
              :content="notification && notification.length_expired"
            >
              <v-icon color="red darken-4">mdi-fire</v-icon>
            </v-badge>

            <!-- <v-icon v-else>mdi-email-outline</v-icon> -->
          </v-btn>
        </template>
        <v-card v-if="notification && notification.length_expired">
          <v-list
            class="py-0"
            style="cursor: pointer"
            dense
            v-for="(item, index) in notification.expired"
            :key="index"
          >
            <v-row>
              <v-list-item :to="'/document/' + item.pdf_file_name">
                <v-col cols="9" class="py-0">
                  <v-list-item-title>
                    <strong>
                      {{ item.document_type["name_" + $i18n.locale] }}
                    </strong>
                    {{ item.document_template["name_" + $i18n.locale] }}
                    <br />
                    {{
                      item.document_number_reg
                        ? item.document_number_reg
                        : item.document_number
                    }}
                  </v-list-item-title>
                </v-col>
                <v-col cols="3" class="py-0">
                  <v-list-item-title v-if="item.document_date_reg">
                    {{
                      item.document_date_reg.substr(0, 10) +
                      " " +
                      item.document_date_reg.substr(11, 5)
                    }}
                  </v-list-item-title>
                  <v-list-item-title v-else>
                    {{
                      item.document_date.substr(0, 10) +
                      " " +
                      item.document_date.substr(11, 5)
                    }}
                  </v-list-item-title>
                </v-col>
                <v-divider></v-divider>
              </v-list-item>
            </v-row>
          </v-list>
          <v-list class="py-0" style="cursor: pointer" dense>
            <v-list-item to="/documents/list/expired/0">
              <v-list-item-title>{{ $t("message.all") }}</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-card>

        <v-list v-else>
          <v-list-item>
            <v-list-item-title>{{ $t("message.noAlerts") }}</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>

      <v-menu
        offset-y
        v-if="notification && notification.length_canceled && drawerShow"
      >
        <template v-slot:activator="{ on }">
          <v-btn text small to="/documents/list/canceled/0" class="ml-1" :title="$t('negative')">
            <v-badge
              color="red darken-2"
              :content="notification && notification.length_canceled"
            >
              <v-icon color="red darken-4">mdi-file-cancel-outline</v-icon>
            </v-badge>

            <!-- <v-icon v-else>mdi-email-outline</v-icon> -->
          </v-btn>
        </template>
        <v-card v-if="notification && notification.length_canceled">
          <v-list
            class="py-0"
            style="cursor: pointer"
            dense
            v-for="(item, index) in notification.canceled"
            :key="index"
          >
            <v-row>
              <v-list-item :to="'/document/' + item.pdf_file_name">
                <v-col cols="9" class="py-0">
                  <v-list-item-title>
                    <strong>
                      {{ item.document_type["name_" + $i18n.locale] }}
                    </strong>
                    {{ item.document_template["name_" + $i18n.locale] }}
                    <br />
                    {{
                      item.document_number_reg
                        ? item.document_number_reg
                        : item.document_number
                    }}
                  </v-list-item-title>
                </v-col>
                <v-col cols="3" class="py-0">
                  <v-list-item-title v-if="item.document_date_reg">
                    {{
                      item.document_date_reg.substr(0, 10) +
                      " " +
                      item.document_date_reg.substr(11, 5)
                    }}
                  </v-list-item-title>
                  <v-list-item-title v-else>
                    {{
                      item.document_date.substr(0, 10) +
                      " " +
                      item.document_date.substr(11, 5)
                    }}
                  </v-list-item-title>
                </v-col>
                <v-divider></v-divider>
              </v-list-item>
            </v-row>
          </v-list>

          <v-list class="py-0" style="cursor: pointer" dense>
            <v-list-item to="/documents/list/canceled/0">
              <v-list-item-title>{{ $t("message.all") }}</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-card>

        <v-list v-else>
          <v-list-item>
            <v-list-item-title>{{ $t("message.noAlerts") }}</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>

      <v-menu
        offset-y
        v-if="notification && notification.length_agreement && drawerShow"
      >
        <template v-slot:activator="{ on }">
          <v-btn text small to="/documents/list/agreement/0" class="ml-1" :title="$t('negative')">
            <v-badge
              color="light-green darken-2"
              :content="notification && notification.length_agreement"
            >
              <v-icon color="light-green darken-4"
                >mdi-file-check-outline</v-icon
              >
            </v-badge>

            <!-- <v-icon v-else>mdi-email-outline</v-icon> -->
          </v-btn>
        </template>
        <v-card v-if="notification && notification.length_agreement">
          <v-list
            class="py-0"
            style="cursor: pointer"
            dense
            v-for="(item, index) in notification.agreement"
            :key="index"
          >
            <v-row>
              <v-list-item :to="'/document/' + item.pdf_file_name">
                <v-col cols="9" class="py-0">
                  <v-list-item-title>
                    <strong>
                      {{ item.document_type["name_" + $i18n.locale] }}
                    </strong>
                    {{ item.document_template["name_" + $i18n.locale] }}
                    <br />
                    {{
                      item.document_number_reg
                        ? item.document_number_reg
                        : item.document_number
                    }}
                  </v-list-item-title>
                </v-col>
                <v-col cols="3" class="py-0">
                  <v-list-item-title v-if="item.document_date_reg">
                    {{
                      item.document_date_reg.substr(0, 10) +
                      " " +
                      item.document_date_reg.substr(11, 5)
                    }}
                  </v-list-item-title>
                  <v-list-item-title v-else>
                    {{
                      item.document_date.substr(0, 10) +
                      " " +
                      item.document_date.substr(11, 5)
                    }}
                  </v-list-item-title>
                </v-col>
                <v-divider></v-divider>
              </v-list-item>
            </v-row>
          </v-list>

          <v-list class="py-0" style="cursor: pointer" dense>
            <v-list-item to="/documents/list/agreement/0">
              <v-list-item-title>{{ $t("message.all") }}</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-card>

        <v-list v-else>
          <v-list-item>
            <v-list-item-title>{{ $t("message.noAlerts") }}</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>

      <v-menu offset-y v-if="drawerShow && notification.length">
        <template v-slot:activator="{ on }">
          <v-btn
            text
            small
            to="/documents/list/notification/0"
            class="ml-1"
            :title="$t('document.news_document')"
          >
            <v-badge
              color="green"
              v-if="notification && notification.length"
              :content="notification && notification.length"
            >
              <v-icon color="black">mdi-email-outline</v-icon>
            </v-badge>

            <v-icon v-else>mdi-email-outline</v-icon>
          </v-btn>
        </template>
        <v-card v-if="notification && notification.length">
          <v-list
            class="py-0"
            style="cursor: pointer"
            dense
            v-for="(item, index) in notification.document"
            :key="index"
          >
            <v-row>
              <v-list-item :to="'/document/' + item.pdf_file_name">
                <v-col cols="9" class="py-0">
                  <v-list-item-title>
                    <strong>
                      {{ item.document_type["name_" + $i18n.locale] }}
                    </strong>
                    {{ item.document_template["name_" + $i18n.locale] }}
                    <br />
                    {{
                      item.document_number_reg
                        ? item.document_number_reg
                        : item.document_number
                    }}
                  </v-list-item-title>
                </v-col>
                <v-col cols="3" class="py-0">
                  <v-list-item-title v-if="item.document_date_reg">
                    {{
                      item.document_date_reg.substr(0, 10) +
                      " " +
                      item.document_date_reg.substr(11, 5)
                    }}
                  </v-list-item-title>
                  <v-list-item-title v-else>
                    {{
                      item.document_date.substr(0, 10) +
                      " " +
                      item.document_date.substr(11, 5)
                    }}
                  </v-list-item-title>
                </v-col>
                <v-divider></v-divider>
              </v-list-item>
            </v-row>
          </v-list>

          <v-list class="py-0" style="cursor: pointer" dense>
            <v-list-item to="/documents/list/notification/0">
              <v-list-item-title>{{ $t("message.all") }}</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-card>

        <v-list v-else>
          <v-list-item>
            <v-list-item-title>{{ $t("message.noAlerts") }}</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>

      <v-menu offset-y>
        <template v-slot:activator="{ on }">
          <v-btn text v-on="on" class="ml-2">
            <v-icon>mdi-account-outline</v-icon>
            {{ user && user.username ? user.username : "" }}
            <!--<v-icon>mdi-mdi-dots-vertical</v-icon>-->
          </v-btn>
        </template>

        <v-list dense>
          <v-list-item
            to="/koreshok/list"
            v-if="
              false &&
              $store.state.COMPANY_ID == 1 &&
              user &&
              !user.type &&
              drawerShow
            "
          >
            <v-list-item-title>{{ $t("message.koreshok") }}</v-list-item-title>
          </v-list-item>
          <v-list-item
            v-if="false"
            @click="
              () => {
                myProfileDialog = !myProfileDialog;
              }
            "
          >
            <v-list-item-title>
              {{ $t("message.profileDialog") }}
            </v-list-item-title>
          </v-list-item>
          <v-list-item>
            <v-list-item-title>
              <router-link
                to="/tabel"
                style="text-decoration: none; color: inherit"
                >{{ $t("tabel") }}</router-link
              >
            </v-list-item-title>
          </v-list-item>
          <v-list-item v-if="drawerShow">
            <v-list-item-title>
              <router-link
                :to="'/personcontrol/profile/' + employee.id"
                style="text-decoration: none; color: inherit"
                >{{ $t("message.profile") }}</router-link
              >
            </v-list-item-title>
          </v-list-item>

          <v-list-item v-if="drawerShow">
            <v-list-item-title>
              <router-link
                to="/phonebook"
                style="text-decoration: none; color: inherit"
                >{{ $t("employee.phones") }}</router-link
              >
            </v-list-item-title>
          </v-list-item>

          <v-list-item @click="logout">
            <v-list-item-title>{{ $t("message.logout") }}</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
      <v-menu
        offset-y
        v-if="
          true ||
          (user &&
            (user.username == 'test' ||
              user.username == 'ak7948' ||
              user.username == 'sd6566' ||
              user.username == 'sa6567' ||
              user.username == 'ra8108' ||
              user.username == 'os7679' ||
              user.username == 'bt7485' ||
              user.username == 'ms8107' ||
              user.username == 'SU9996' ||
              user.username == 'qg9592'))
        "
        :close-on-content-click="false"
      >
        <template v-slot:activator="{ on }">
          <v-btn text v-on="on" class="ml-2">
            <v-icon>mdi-apps</v-icon>
          </v-btn>
        </template>

        <template v-slot:default class="mr-12">
          <v-card width="300">
            <v-item-group>
              <v-container>
                <v-row>
                  <v-col cols="4">
                    <v-item v-slot="{ toggle }">
                      <v-card
                        class="text-center pa-2"
                        height="80"
                        width="80"
                        @click="toggle"
                        flat
                        to="/orgtex/my-devices"
                      >
                        <v-icon color="blue" x-large
                          >mdi-monitor-cellphone</v-icon
                        >Org.Tex.
                      </v-card>
                    </v-item>
                  </v-col>
                  <v-col cols="4" v-if="$store.getters.checkPermission('skud_swood')">
                    <v-item
                      v-slot="{ toggle }"
                      
                    >
                      <v-card
                        class="text-center pa-2"
                        height="80"
                        width="100"
                        @click="toggle"
                        flat
                        to="/skud/swod"
                        v-if="$store.getters.checkPermission('skud_swood')"
                      >
                        <v-icon color="blue" x-large>mdi-chart-line</v-icon
                        >Skud.Kunlik
                      </v-card>
                    </v-item>
                  </v-col>
                  <v-col cols="4" v-if="$store.getters.checkPermission('skud_full')">
                    <v-item
                      v-slot="{ toggle }"
                      
                    >
                      <v-card
                        class="text-center pa-2"
                        height="80"
                        width="100"
                        @click="toggle"
                        flat
                        to="/skud/full"
                        v-if="$store.getters.checkPermission('skud_full')"
                      >
                        <v-icon color="blue" x-large>mdi-chart-line</v-icon
                        >Skud.Oylik
                      </v-card>
                    </v-item>
                  </v-col>

                  <v-col cols="4" v-if="$store.getters.checkPermission('skud_fullmanual')">
                    <v-item
                      v-slot="{ toggle }"
                      
                    >
                      <v-card
                        class="text-center pa-2 ml-5"
                        height="80"
                        width="100"
                        @click="toggle"
                        flat
                        to="/skud/fullmanual"
                        v-if="$store.getters.checkPermission('skud_fullmanual')"
                      >
                        <v-icon color="blue" x-large>mdi-chart-line</v-icon
                        >Oylik.Adminga
                      </v-card>
                    </v-item>
                  </v-col>

                  <v-col cols="4" v-if="$store.getters.checkPermission('worktask')">
                    <v-item v-slot="{ toggle }">
                      <v-card
                        class="text-center pa-2"
                        height="80"
                        width="80"
                        @click="toggle"
                        flat
                        to="/worktask/index"
                        
                      >
                        <v-icon color="blue" x-large>mdi-calendar-check</v-icon
                        >WorkTask
                      </v-card>
                    </v-item>
                  </v-col>

                  <v-col cols="4" v-if="$store.getters.checkPermission('skud_swoodmanual')">
                    <v-item
                      v-slot="{ toggle }"
                      
                    >
                      <v-card
                        class="text-center pa-2 ml-5"
                        height="80"
                        width="100"
                        @click="toggle"
                        flat
                        to="/skud/swodmanual"
                        v-if="
                          $store.getters.checkPermission('skud_swoodmanual')
                        "
                      >
                        <v-icon color="blue" x-large>mdi-chart-line</v-icon
                        >KunlikAdminga
                      </v-card>
                    </v-item>
                  </v-col>

                  <v-col cols="4" v-if="$store.getters.checkPermission('cdpt')">
                    <v-item v-slot="{ toggle }">
                      <v-card
                        class="text-center pa-2"
                        height="80"
                        width="80"
                        @click="toggle"
                        flat
                        to="/cdpt"
                      >
                        <v-icon color="blue" x-large
                          >mdi-briefcase-account-outline</v-icon
                        >CDPT
                      </v-card>
                    </v-item>
                  </v-col>
        <v-col cols="4" v-if="$store.getters.checkPermission('med_punkt')">
                        <v-item v-slot="{ toggle }">
                          <v-card
                            class="text-center pa-2"
                            height="80"
                            width="80"
                            @click="toggle"
                            flat
                            to="/medpunkt"
                          >
                            <v-icon color="blue" x-large
                              >mdi-briefcase-account-outline</v-icon
                            >Med Punkt
                          </v-card>
                        </v-item>
                      </v-col>
                      <v-col cols="4" v-if="$store.getters.checkPermission('linestop_model')">
                        <v-item v-slot="{ toggle }">
                          <v-card
                            class="text-center pa-2"
                            height="80"
                            width="80"
                            @click="toggle"
                            flat
                            to="/linestop"
                          >
                            <v-icon color="blue" x-large
                              >mdi-alert-outline</v-icon
                            >Line stop
                          </v-card>
                        </v-item>
                      </v-col>
                  <v-col cols="4" v-if="false">
                    <v-item v-slot="{ toggle }">
                      <v-card
                        class="text-center pa-2"
                        height="80"
                        width="80"
                        @click="toggle"
                        flat
                      >
                        <v-icon color="blue" x-large
                          >mdi-account-box-outline</v-icon
                        >OTZ
                      </v-card>
                    </v-item>
                  </v-col>
                  <v-col cols="4" v-if="false">
                    <v-item v-slot="{ toggle }">
                      <v-card
                        class="text-center pa-2"
                        height="80"
                        width="80"
                        @click="toggle"
                        flat
                      >
                        <v-icon color="blue" x-large
                          >mdi-card-account-details-outline</v-icon
                        >Tabel
                      </v-card>
                    </v-item>
                  </v-col>
                  <v-col cols="4" v-if="false">
                    <v-item v-slot="{ toggle }">
                      <v-card
                        class="text-center pa-2"
                        height="80"
                        width="80"
                        @click="toggle"
                        flat
                      >
                        <v-icon color="blue" x-large>mdi-finance</v-icon>Report
                      </v-card>
                    </v-item>
                  </v-col>
                  <v-col cols="4" v-if="false">
                    <v-item v-slot="{ toggle }">
                      <v-card
                        class="text-center pa-2"
                        height="80"
                        width="80"
                        @click="toggle"
                        flat
                      >
                        <v-icon color="blue" x-large>mdi-account-cog</v-icon
                        >Admin
                      </v-card>
                    </v-item>
                  </v-col>
                </v-row>
              </v-container>
            </v-item-group>
          </v-card>
        </template>
      </v-menu>
    </v-app-bar>
    <v-navigation-drawer
      app
      mobile-breakpoint="100"
      :expand-on-hover="!drawer"
      :mini-variant="!drawer"
    >
      <template>
        <v-list subheader style="background-color: #f7f8fb" class="pb-0">
          <v-list-item
            @click="staff = false"
            to="/"
            :title="$t('message.home')"
            class="py-1"
          >
            <v-list-item-content color="#163e72">
              <v-list-item-title
                class="text-h5 text-center"
                style="color: #163e72; font-weight: 600"
                v-text="$t('UzAuto')"
              ></v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </v-list>

        <v-card class="mx-auto elevation-0" max-width="300" tile>
          <v-divider></v-divider>

          <v-list dense subheader class="left-aside">
            <v-list-group :title="$t('message.create')">
              <template v-slot:activator>
                <v-list-item-icon class="mr-1">
                  <v-icon>mdi-clipboard-file-outline</v-icon>
                </v-list-item-icon>

                <v-list-item-content>
                  <v-list-item-title>
                    {{ $t("message.create") }}
                  </v-list-item-title>
                </v-list-item-content>
              </template>

              <template v-for="(item, i) in create_document">
                <v-list-item
                  v-if="
                    item.id == 0 && user && !user.type ? true : item.visible
                  "
                  :key="i"
                  router
                  :to="item.route"
                  class="pl-4"
                  style
                  :title="item.text"
                >
                  <v-list-item-icon class="mr-1" :title="item.text">
                    <v-icon small v-text="item.icon"></v-icon>
                  </v-list-item-icon>

                  <v-list-item-content>
                    <v-list-item-title
                      v-text="
                        item.id == 27
                          ? item['name_' + $i18n.locale] + ' (Кадр)'
                          : item['name_' + $i18n.locale]
                      "
                    ></v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </template>
            </v-list-group>

            <v-list-group v-if="!!document_list.find((v) => v.visible)">
              <template v-slot:activator>
                <v-list-item-icon class="mr-1">
                  <v-icon>mdi-file-document-outline</v-icon>
                </v-list-item-icon>

                <v-list-item-content>
                  <v-list-item-title>
                    {{ $t("my_documents") }}
                  </v-list-item-title>
                </v-list-item-content>
              </template>
              <v-list-group
                sub-group
                prepend-icon
                v-for="(item, i) in document_list.filter(
                  (v) =>
                    v.menu_item != 'allhr' &&
                    v.menu_item != 'alllsp' &&
                    v.menu_item != 'allznz' &&
                    v.menu_item != 'all'
                )"
                :key="i"
                :title="item.menu_item"
                v-if="item.visible"
              >
                <template v-slot:appendIcon>
                  <v-icon>mdi-chevron-down</v-icon>
                </template>

                <template v-slot:activator>
                  <v-list-item-icon class="mr-1">
                    <v-icon>mdi-file-document-outline</v-icon>
                  </v-list-item-icon>

                  <v-list-item-title>
                    {{ $t("document." + item.menu_item) }}
                  </v-list-item-title>
                </template>

                <template v-for="(itm, i_type) in item.document_types">
                  <v-list-item
                    v-if="itm.count"
                    :key="i_type"
                    router
                    :to="item.route + '/' + itm.id"
                    class="pl-4"
                    style
                    :title="itm['name_' + $i18n.locale]"
                    color="primary"
                  >
                    <v-list-item-content class="ml-2">
                      <v-list-item-title
                        v-text="
                          itm.id == 27
                            ? itm['name_' + $i18n.locale] + ' (Кадр)'
                            : itm['name_' + $i18n.locale]
                        "
                      ></v-list-item-title>
                    </v-list-item-content>

                    <v-badge
                      class="mr-6"
                      color="purple darken-3"
                      v-if="parseInt(itm.count_new)"
                      :content="itm.count_new"
                    ></v-badge>
                  </v-list-item>
                </template>

                <template>
                  <v-list-item
                    v-if="
                      item.menu_item == 'canceled' &&
                      $store.getters.checkPermission('okd_kanselyariya')
                    "
                    router
                    :to="item.route + '/uzauto'"
                    class="pl-4"
                    style
                    :title="$t('uzauto')"
                  >
                    <v-list-item-icon class="mr-1" :title="$t('uzauto')">
                      <v-icon small v-text="item.icon"></v-icon>
                    </v-list-item-icon>

                    <v-list-item-content>
                      <v-list-item-title
                        v-text="$t('uzauto')"
                      ></v-list-item-title>
                    </v-list-item-content>
                  </v-list-item>
                </template>

                <v-list-item
                  router
                  :to="item.route + '/0'"
                  class="pl-4"
                  style
                  :title="$t('document.all')"
                >
                  <v-list-item-content class="ml-6">
                    <v-list-item-title
                      v-text="$t('document.all')"
                    ></v-list-item-title>
                  </v-list-item-content>
                </v-list-item>

                <template></template>
              </v-list-group>
              <v-list-item
                v-if="$store.getters.checkPermission('document-list-all')"
                router
                to="/documents/list/all/0"
                class="pl-4"
                style
                :title="$t('document.all')"
              >
                <v-list-item-content class="ml-6">
                  <v-list-item-title
                    v-text="$t('document.all')"
                  ></v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list-group>

            
            <v-list-item
              router
              to="/documents/list/inbox/32"
              class="pl-4"
              style
              v-if="$store.getters.checkPermission('company_outbox')"
            >
              <v-list-item-icon class="mr-1">
                <v-icon>mdi-file-document-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title>
                  {{ $t("document.company_outbox") }}
                </v-list-item-title>
              </v-list-item-content>
            </v-list-item>

            <v-list-group
              :title="$t('KPI')"
              v-if="
                !!this.kpiLinks.find((v) => v.visible) &&
                user.type != 'D' &&
                user.type != 'K'
              "
            >
              <template v-slot:activator>
                <v-list-item-icon class="mr-1">
                  <v-icon>mdi-finance</v-icon>
                </v-list-item-icon>

                <v-list-item-content>
                  <v-list-item-title>{{ $t("KPI") }}</v-list-item-title>
                </v-list-item-content>
              </template>

              <template v-for="(item, i) in kpiLinks">
                <v-list-item
                  v-if="item.visible"
                  :key="i"
                  router
                  :to="item.route"
                  class="pl-4"
                  style
                  :title="item.text"
                >
                  <v-list-item-icon class="mr-1">
                    <v-icon small v-text="item.icon"></v-icon>
                  </v-list-item-icon>

                  <v-list-item-content>
                    <v-list-item-title v-text="item.text"></v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </template>
            </v-list-group>

            <v-list-item
              router
              to="/documents/signed"
              class="pl-4"
              style
              v-if="user.type != 'D' && user.type != 'K'"
            >
              <v-list-item-icon class="mr-1">
                <v-icon>mdi-file-document-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title>
                  {{ $t("message.signed_documents") }}
                </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-group
              :title="$t('Complaens')"
              v-if="$store.getters.checkRole('compliance')"
            >
              <template v-slot:activator>
                <v-list-item-icon class="mr-1">
                  <v-icon>mdi-file-document-outline</v-icon>
                </v-list-item-icon>

                <v-list-item-content>
                  <v-list-item-title>{{ $t("Complaens") }}</v-list-item-title>
                </v-list-item-content>
              </template>

              <template v-for="(item, i) in compliance">
                <v-list-item
                  v-if="item.visible"
                  :key="i"
                  router
                  :to="item.route"
                  class="pl-4"
                  style
                  :title="item.text"
                >
                  <v-list-item-icon class="mr-1" :title="item.text">
                    <v-icon small v-text="item.icon"></v-icon>
                  </v-list-item-icon>

                  <v-list-item-content>
                    <v-list-item-title v-text="item.text"></v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </template>
            </v-list-group>

            <v-list-group
              :title="$t('UZLOGISTIC')"
              v-if="$store.getters.checkPermission('centrum')"
            >
              <template v-slot:activator>
                <v-list-item-icon class="mr-1">
                  <v-icon>mdi-file-document-outline</v-icon>
                </v-list-item-icon>

                <v-list-item-content>
                  <v-list-item-title>{{ $t("UZLOGISTIC") }}</v-list-item-title>
                </v-list-item-content>
              </template>

              <template v-for="(item, i) in centrum">
                <v-list-item
                  v-if="item.visible"
                  :key="i"
                  router
                  :to="item.route"
                  class="pl-4"
                  style
                  :title="item.text"
                >
                  <v-list-item-icon class="mr-1" :title="item.text">
                    <v-icon small v-text="item.icon"></v-icon>
                  </v-list-item-icon>

                  <v-list-item-content>
                    <v-list-item-title v-text="item.text"></v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </template>
            </v-list-group>

            <v-list-group
              :title="$t('pochta')"
              v-if="$store.getters.checkPermission('pochta')"
            >
              <template v-slot:activator>
                <v-list-item-icon class="mr-1">
                  <v-icon>mdi-file-document-outline</v-icon>
                </v-list-item-icon>

                <v-list-item-content>
                  <v-list-item-title>{{ $t("pochta") }}</v-list-item-title>
                </v-list-item-content>
              </template>

              <template v-for="(item, i) in pochta">
                <v-list-item
                  v-if="item.visible"
                  :key="i"
                  router
                  :to="item.route"
                  class="pl-4"
                  style
                  :title="item.text"
                >
                  <v-list-item-icon class="mr-1" :title="item.text">
                    <v-icon small v-text="item.icon"></v-icon>
                  </v-list-item-icon>

                  <v-list-item-content>
                    <v-list-item-title v-text="item.text"></v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </template>
            </v-list-group>

            <v-list-group
              :title="$t('message.reports')"
              v-if="user.type != 'D' && user.type != 'K'"
            >
              <template v-slot:activator>
                <v-list-item-icon class="mr-1">
                  <v-icon>mdi-chart-bar</v-icon>
                </v-list-item-icon>

                <v-list-item-content>
                  <v-list-item-title>
                    {{ $t("message.reports") }}
                  </v-list-item-title>
                </v-list-item-content>
              </template>

              <template v-for="(item, i) in reports">
                <v-list-item
                  v-if="item.visible"
                  :key="i"
                  router
                  :to="item.route"
                  class="pl-4"
                  style
                  :title="item.text"
                >
                  <v-list-item-icon class="mr-1" :title="item.text">
                    <v-icon small v-text="item.icon"></v-icon>
                  </v-list-item-icon>

                  <v-list-item-content>
                    <v-list-item-title v-text="item.text"></v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </template>
            </v-list-group>
            <!-- control_document -->
            <v-list-group
              :title="$t('message.document_control')"
              v-if="
                !!control_document.filter((v) => v.visible).length &&
                $store.getters.checkPermission('control_document')
              "
            >
              <template v-slot:activator>
                <v-list-item-icon class="mr-1">
                  <v-icon>mdi-microsoft-xbox-controller-menu</v-icon>
                </v-list-item-icon>

                <v-list-item-content>
                  <v-list-item-title>
                    {{ $t("message.document_control") }}
                  </v-list-item-title>
                </v-list-item-content>
              </template>

              <template v-for="(item, i) in control_document">
                <v-list-item
                  v-if="item.visible"
                  :key="i"
                  router
                  :to="item.route"
                  class="pl-4"
                  style
                  :title="item.text"
                >
                  <v-list-item-icon class="mr-1" :title="item.text">
                    <v-icon small v-text="item.icon"></v-icon>
                  </v-list-item-icon>

                  <v-list-item-content>
                    <v-list-item-title v-text="item.text"></v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </template>
            </v-list-group>

            <v-list-group
              :title="$t('workflow')"
              v-if="!!workflowLinks.find((v) => v.visible)"
            >
              <template v-slot:activator>
                <v-list-item-icon class="mr-1">
                  <v-icon>mdi-clipboard-file-outline</v-icon>
                </v-list-item-icon>

                <v-list-item-content>
                  <v-list-item-title>{{ $t("workflow") }}</v-list-item-title>
                </v-list-item-content>
              </template>

              <template v-for="(item, i) in workflowLinks">
                <v-list-item
                  v-if="item.visible"
                  :key="i"
                  router
                  :to="item.route"
                  class="pl-4"
                  style
                  :title="item.text"
                >
                  <v-list-item-icon class="mr-1" :title="item.text">
                    <v-icon small v-text="item.icon"></v-icon>
                  </v-list-item-icon>

                  <v-list-item-content>
                    <v-list-item-title v-text="item.text"></v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </template>
            </v-list-group>

            <v-list-group
              :title="$t('staff.hr')"
              v-if="
                !!hrLinks.find((v) => v.visible) &&
                user.type != 'D' &&
                user.type != 'K'
              "
            >
              <template v-slot:activator>
                <v-list-item-icon class="mr-1">
                  <v-icon>mdi-clipboard-account-outline</v-icon>
                </v-list-item-icon>

                <v-list-item-content>
                  <v-list-item-title>{{ $t("staff.hr") }}</v-list-item-title>
                </v-list-item-content>
              </template>

              <template v-for="(item, i) in hrLinks">
                <v-list-item
                  v-if="item.visible"
                  :key="i"
                  router
                  :to="item.route"
                  class="pl-4"
                  style
                  :title="item.text"
                >
                  <v-list-item-icon class="mr-1" :title="item.text">
                    <v-icon small v-text="item.icon"></v-icon>
                  </v-list-item-icon>

                  <v-list-item-content>
                    <v-list-item-title v-text="item.text"></v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </template>
            </v-list-group>
            <v-list-group
              :title="$t('staff.admin')"
              v-if="!!this.adminLinks.find((v) => v.visible)"
            >
              <template v-slot:activator>
                <v-list-item-icon class="mr-1">
                  <v-icon>mdi-account-outline</v-icon>
                </v-list-item-icon>

                <v-list-item-content>
                  <v-list-item-title>{{ $t("staff.admin") }}</v-list-item-title>
                </v-list-item-content>
              </template>

              <template v-for="(item, i) in adminLinks">
                <v-list-item
                  v-if="item.visible"
                  :key="i"
                  router
                  :to="item.route"
                  class="pl-4"
                  style
                  :title="item.text"
                >
                  <v-list-item-icon class="mr-1" :title="item.text">
                    <v-icon small v-text="item.icon"></v-icon>
                  </v-list-item-icon>

                  <v-list-item-content>
                    <v-list-item-title v-text="item.text"></v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </template>
            </v-list-group>
            <v-list-group
              :title="$t('staff.staff_admin')"
              v-if="
                !!this.staffLinks.find((v) => v.visible) &&
                $store.getters.checkPermission('tashkiliy_tuzilma-create')
              "
            >
              <template v-slot:activator>
                <v-list-item-icon class="mr-1">
                  <v-icon>mdi-select-group</v-icon>
                </v-list-item-icon>

                <v-list-item-content>
                  <v-list-item-title>
                    {{ $t("staff.staff_admin") }}
                  </v-list-item-title>
                </v-list-item-content>
              </template>

              <template v-for="(item, i) in staffLinks">
                <v-list-item
                  v-if="item.visible"
                  :key="i"
                  router
                  :to="item.route"
                  class="pl-4"
                  style
                  :title="item.text"
                >
                  <v-list-item-icon class="mr-1">
                    <v-icon small v-text="item.icon"></v-icon>
                  </v-list-item-icon>

                  <v-list-item-content>
                    <v-list-item-title v-text="item.text"></v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </template>
            </v-list-group>

            <v-list-group
              :title="$t('inventory.index')"
              v-if="!!this.inventoryLinks.find((v) => v.visible)"
            >
              <template v-slot:activator>
                <v-list-item-icon class="mr-1">
                  <v-icon>mdi-select-inverse</v-icon>
                </v-list-item-icon>

                <v-list-item-content>
                  <v-list-item-title>
                    {{ $t("inventory.index") }}
                  </v-list-item-title>
                </v-list-item-content>
              </template>

              <template v-for="(item, i) in inventoryLinks">
                <v-list-item
                  v-if="item.visible"
                  :key="i"
                  router
                  :to="item.route"
                  class="pl-4"
                  style
                  :title="item.text"
                >
                  <v-list-item-icon class="mr-1">
                    <v-icon small v-text="item.icon"></v-icon>
                  </v-list-item-icon>

                  <v-list-item-content>
                    <v-list-item-title v-text="item.text"></v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </template>
            </v-list-group>

            <v-list-item
              router
              to="/car-purchases/list"
              class="pl-4"
              v-if="false"
            >
              <v-list-item-icon class="mr-1">
                <v-icon color="success">mdi-car</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title>
                  {{ $t("carPurchase.view") }}
                </v-list-item-title>
              </v-list-item-content>
            </v-list-item>

            <!--Blank templates-->
            <v-list-group
              v-if="$store.getters.checkPermission('blank-template-index')"
            >
              <template v-slot:activator>
                <v-list-item-icon class="mr-1">
                  <v-icon>mdi-file-document-multiple-outline</v-icon>
                </v-list-item-icon>

                <v-list-item-content>
                  <v-list-item-title>
                    {{ $t("blankTemplate.index") }}
                  </v-list-item-title>
                </v-list-item-content>
              </template>
              <v-list-item
                router
                to="/blank-templates/list"
                class="pl-4"
                v-if="$store.getters.checkPermission('blank-template-create')"
              >
                <v-list-item-icon class="mr-1">
                  <v-icon small>mdi-file-plus-outline</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>
                    {{ $t("blankTemplate.create") }}
                  </v-list-item-title>
                </v-list-item-content>
              </v-list-item>

              <v-list-item router to="/blank-templates/get-blank" class="pl-4">
                <v-list-item-icon class="mr-1">
                  <v-icon small>mdi-file-document-multiple-outline</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>
                    {{ $t("blankTemplate.get") }}
                  </v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list-group>

            <v-list-group
              :title="$t('archive')"
              v-if="
                !!this.archiveLinks.find((v) => v.visible) &&
                user.type != 'D' &&
                user.type != 'K'
              "
            >
              <template v-slot:activator>
                <v-list-item-icon class="mr-1">
                  <v-icon>mdi-archive-outline</v-icon>
                </v-list-item-icon>

                <v-list-item-content>
                  <v-list-item-title>{{ $t("archive") }}</v-list-item-title>
                </v-list-item-content>
              </template>

              <template v-for="(item, i) in archiveLinks">
                <v-list-item
                  v-if="item.visible"
                  :key="i"
                  router
                  :to="item.route"
                  class="pl-4"
                  style
                  :title="item.text"
                >
                  <v-list-item-icon class="mr-1">
                    <v-icon small v-text="item.icon"></v-icon>
                  </v-list-item-icon>

                  <v-list-item-content>
                    <v-list-item-title v-text="item.text"></v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </template>
            </v-list-group>

            <!--AS400 to Excel-->
            <v-list-group
              v-if="
                $store.getters.checkPermission('as400_query_control1') ||
                $store.getters.checkPermission('as400_download_excel1')
              "
            >
              <template v-slot:activator>
                <v-list-item-icon class="mr-1">
                  <v-icon>mdi-database</v-icon>
                </v-list-item-icon>

                <v-list-item-content>
                  <v-list-item-title>{{ $t("AS4001") }}</v-list-item-title>
                </v-list-item-content>
              </template>
              <v-list-item
                router
                to="/as400toexcel/list"
                class="pl-4"
                v-if="$store.getters.checkPermission('as400_query_control')"
              >
                <v-list-item-icon class="mr-1">
                  <v-icon small>mdi-shape-square-plus</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>{{ $t("Queries") }}</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
              <v-list-item
                router
                to="/as400toexcel/history"
                class="pl-4"
                v-if="$store.getters.checkPermission('as400_download_excel')"
              >
                <v-list-item-icon class="mr-1">
                  <v-icon small color="blue">mdi-microsoft-excel</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>
                    {{ $t("Download Excel") }}
                  </v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list-group>

            <!-- IDES LINK -->
            <v-list-group v-if="$store.getters.checkPermission('ides')">
              <template v-slot:activator>
                <v-list-item-icon class="mr-1">
                  <v-icon>mdi-folder-open</v-icon>
                </v-list-item-icon>

                <v-list-item-content>
                  <v-list-item-title>{{
                    $t("control_punkt.name")
                  }}</v-list-item-title>
                </v-list-item-content>
              </template>
              <v-list-item
                router
                to="/ides/index/inbox"
                class="pl-4"
                v-if="$store.getters.checkPermission('ides')"
              >
                <v-list-item-icon class="mr-1">
                  <v-icon small>mdi-folder-open</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>{{
                    $t("department.incoming")
                  }}</v-list-item-title>
                </v-list-item-content>
                <v-badge
                  class="mr-2 mt-3"
                  color="green darken-1"
                  v-if="idesDocCount && idesDocCount != 0 && !$store.getters.checkPermission('ides-control')"
                  :content="idesDocCount"
                ></v-badge>
                <v-badge
                  class="mr-2 mt-3"
                  color="green darken-1"
                  v-else-if="idesDocCountReceived && idesDocCountReceived != 0 && $store.getters.checkPermission('ides-control')"
                  :content="idesDocCountReceived"
                ></v-badge>
              </v-list-item>
            </v-list-group>
            <!-- END IDES LINK -->


            <!-- Tasdiqlangan Hujjatlar bloki -->

            <v-list-group
            v-if="$store.state.COMPANY_ID == 1 &&
                    user.type != 'D' &&
                    user.type != 'K'"
            >
              <template v-slot:activator>
                <v-list-item-icon class="mr-1">
                  <v-icon>mdi-file-outline</v-icon>
                </v-list-item-icon>

                <v-list-item-content>
                  <v-list-item-title>
                    {{ $t("approved_documents") }}
                  </v-list-item-title>
                </v-list-item-content>
              </template>
              <v-list-group
                sub-group
                prepend-icon
                v-for="(item, i) in [

                {
                  title: 'lavozim_yuriqnomasi',
                  link: '/#/documents/list/lavozim-y/57',
                  link2: '/#/documents/list/lavozim-y-cancel/0',
                  icon: 'mdi-file-outline',
                  target: '',
                  visible:
                    $store.state.COMPANY_ID == 1 &&
                    user.type != 'D' &&
                    user.type != 'K',
                },
                {
                  title: 'kasbiy_yuriqnomasi',
                  link: '/#/documents/list/kasbiy-y/59',
                  link2: '/#/documents/list/kasbiy-y-cancel/0',
                  icon: 'mdi-file-outline',
                  target: '',
                  visible:
                    $store.state.COMPANY_ID == 1 &&
                    user.type != 'D' &&
                    user.type != 'K',
                },
                {
                  title: 'tarkibiy_tuzilma',
                  link: '/#/documents/list/tarkibiy-t/58',
                  link2: '/#/documents/list/tarkibiy-t-cancel/0',
                  icon: 'mdi-file-outline',
                  target: '',
                  visible:
                    $store.state.COMPANY_ID == 1 &&
                    user.type != 'D' &&
                    user.type != 'K',
                },
                {
                  title: 'standard',
                  link: '/#/documents/list/standard/0',
                  link2: '/#/documents/list/standard-cancel/0',
                  icon: 'mdi-file-outline',
                  target: '',
                  visible:
                    $store.state.COMPANY_ID == 1 &&
                    user.type != 'D' &&
                    user.type != 'K',
                },
                {
                  title: 'process_card',
                  link: '/#/documents/list/karta-p/0',
                  link2: '/#/documents/list/karta-p-cancel/0',
                  icon: 'mdi-file-outline',
                  target: '',
                  visible:
                    $store.state.COMPANY_ID == 1 &&
                    user.type != 'D' &&
                    user.type != 'K',
                },
                {
                  title: 'risks',
                  link: '/#/documents/list/risk/0',
                  link2: '/#/documents/list/risk-cancel/0',
                  icon: 'mdi-file-outline',
                  target: '',
                  visible:
                    $store.state.COMPANY_ID == 1 &&
                    user.type != 'D' &&
                    user.type != 'K',
                },
              ]"
                :key="i"
                :title="item.menu_item"
                v-if="item.visible"
              >
                <template v-slot:appendIcon>
                  <v-icon>mdi-chevron-down</v-icon>
                </template>

                <template v-slot:activator>
                  <v-list-item-icon class="mr-1">
                    <v-icon>mdi-file-outline</v-icon>
                  </v-list-item-icon>

                  <v-list-item-title>
                    {{ $t(item.title) }}
                  </v-list-item-title>
                </template>

                <v-list-item
                  router
                  :href="item.link"
                  class="pl-4"
                  style
                  :title="$t('document.all')"
                >
                  <v-list-item-content class="ml-6">
                    <v-list-item-title
                      v-text="$t('document.all')"
                    ></v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
                <v-list-item
                  router
                  :href="item.link2"
                  class="pl-4"
                  style
                  :title="$t('document.cancel')"
                >
                  <v-list-item-content class="ml-6">
                    <v-list-item-title
                      v-text="$t('document.cancel')"
                    ></v-list-item-title>
                  </v-list-item-content>
                </v-list-item>

                <template></template>
              </v-list-group>
              <v-list-item
                router
                to="/documents/list/annulirovan/0"
                class="pl-4"
                style
                :title="$t('annulirovan')"
              >
                <v-list-item-content class="ml-6">
                  <v-list-item-title
                    v-text="$t('annulirovan')"
                  ></v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list-group>
            <!-- Tasdiqlangan Hujjatlar bloki -->
            <template
              v-for="(item, index) in [
                {
                  title: 'New Index',
                  link: '/#/documents/index-new',
                  icon: 'mdi-file-outline',
                  target: '',
                  visible:
                    $store.state.COMPANY_ID == 3 &&
                    user &&
                    user.username == 'test',
                },
                {
                  title: 'carPurchase.ERI',
                  link: '/tt',
                  img: 'img/icons/e-imzo.png',
                  target: '_blank',
                  visible: user && !user.eimzo_username,
                },
              ]"
            >
              <v-list-item
                :key="index"
                router
                :href="item.link"
                class="pl-4"
                style
                :target="item.target"
                v-if="item.visible"
              >
                <img
                  v-if="item.img"
                  :src="item.img"
                  class="ma-0 pa-0"
                  width="30"
                  height="30"
                />

                <v-list-item-icon v-else class="mr-1">
                  <v-icon>{{ item.icon }}</v-icon>
                </v-list-item-icon>

                <v-list-item-content>
                  <v-list-item-title>{{ $t(item.title) }}</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </template>
            <v-list-item
              v-if="$store.getters.checkPermission('qrcode')"
              router
              to="/qrcode"
              class="pl-4"
            >
              <v-list-item-icon class="mr-1">
                <v-icon>mdi-qrcode-scan</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title>
                  {{ $t("QRcode") }}
                </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              v-if="$store.getters.checkPermission('qrcode')"
              router
              to="/trial-period"
              class="pl-4"
            >
              <v-list-item-icon class="mr-1">
                <v-icon>mdi-account-edit-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title>
                  {{ $t("Trial Period") }}
                </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-card>
      </template>
    </v-navigation-drawer>
    <v-main>
      <router-view></router-view>

      <v-layout
        style="
          position: fixed;
          bottom: 30px;
          right: 10px;
          width: 50px;
          z-index: 1000;
        "
      >
        <v-row class="ma-0 pa-0">
          <v-col cols="12" class="ma-0 pa-0">
            <v-btn
              v-if="false"
              fab
              dark
              color="success"
              @click="toTop"
              class="mb-2"
            >
              <v-icon>mdi-check</v-icon>
            </v-btn>
          </v-col>
          <v-col cols="12" class="ma-0 pa-0">
            <v-btn v-if="fab" fab dark color="primary" @click="toTop">
              <v-icon>mdi-chevron-up</v-icon>
            </v-btn>
          </v-col>
        </v-row>
      </v-layout>
    </v-main>

    <!-- settingDialog -->
    <v-dialog
      v-model="settingDialog"
      scrollable
      persistent
      :overlay="false"
      max-width="500px"
      transition="dialog-transition"
      width="500"
    >
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          {{ $t("message.settings") }}
          <v-spacer></v-spacer>
          <v-btn
            color="red"
            dark
            x-small
            fab
            class
            @click="settingDialog = false"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text class="my-2"></v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" text @click="dialog = false">
            {{ $t("save") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <!-- myCompanyDialog -->
    <v-dialog
      v-model="myCompanyDialog"
      scrollable
      persistent
      :overlay="false"
      max-width="500px"
      transition="dialog-transition"
      width="500"
    >
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          {{ $t("message.myCompany") }}
          <v-spacer></v-spacer>
          <v-btn
            color="red"
            dark
            x-small
            fab
            class
            @click="myCompanyDialog = false"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text class="my-2"></v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" text @click="dialog = false">
            {{ $t("save") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <!-- myProfileDialog -->
    <v-dialog
      v-model="myProfileDialog"
      @keydown.esc="myProfileDialog = false"
      scrollable
      persistent
      :overlay="false"
      max-width="650px"
      transition="dialog-transition"
      width="600"
    >
      <v-card style="height: 100%">
        <v-card-title class="headline grey lighten-2" primary-title>
          {{ $t("message.profile") }}
          <v-spacer></v-spacer>
          <v-btn
            color="red"
            dark
            x-small
            fab
            class
            @click="myProfileDialog = false"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-list-item two-line>
          <!-- <v-list-item-avatar>
            <img
              src="https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcQMfFHgcFEi3Q4c6jYqEmK6io2WzYy-aDIheg&usqp=CAU"
            />
          </v-list-item-avatar>-->
          <v-badge bottom overlap color="#606060" @click="uploadPhoto()">
            <v-avatar size="50" @click="uploadPhoto()">
              <v-img
                src="https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcQMfFHgcFEi3Q4c6jYqEmK6io2WzYy-aDIheg&usqp=CAU"
              ></v-img>
            </v-avatar>
            <template v-slot:badge @click="uploadPhoto()">
              <!-- <v-avatar> -->

              <!-- <v-img src="https://cdn.vuetifyjs.com/images/logos/v.png"></v-img> -->

              <v-icon>mdi-camera</v-icon>

              <!-- </v-avatar> -->
            </template>
          </v-badge>

          <v-list-item-content class="ml-4">
            <v-list-item-title v-if="$i18n.locale == 'uz_latin'">
              {{ employee.employee && employee.employee.lastname_uz_latin }}
              {{ employee.employee && employee.employee.firstname_uz_latin }}
              {{ employee.employee && employee.employee.middlename_uz_latin }}
            </v-list-item-title>
            <v-list-item-title v-else>
              {{ employee.employee && employee.employee.firstname_uz_cyril }}
              {{ employee.employee && employee.employee.lastname_uz_cyril }}
              {{ employee.employee && employee.employee.middlename_uz_cyril }}
            </v-list-item-title>
            <v-list-item-subtitle>
              {{ staff.position && staff.position["name_" + $i18n.locale] }}
            </v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
        <!-- <v-list-item> -->
        <v-row class="ma-0">
          <v-col cols="12" sm="6">
            <v-card-text class="pa-2">
              {{ $t("profile.user_name") }}
              <span class="font-weight-bold">{{ employee.username }}</span>
            </v-card-text>
            <v-card-text class="pa-2">
              {{ $t("profile.role") }}
              <span v-for="(role, i) in roles" :key="i" class="font-weight-bold"
                >{{ role.name }},</span
              >
            </v-card-text>
            <v-card-text class="pa-2">
              {{ $t("profile.department") }}
              <span class="font-weight-bold">
                {{
                  staff.department && staff.department["name_" + $i18n.locale]
                }}
              </span>
            </v-card-text>
          </v-col>
          <v-col cols="12" md="6">
            <v-card-text class="pa-2">
              {{ $t("profile.tabel") }}
              <span class="font-weight-bold">
                {{ employee.employee && employee.employee.tabel }}
              </span>
            </v-card-text>
            <v-card-text class="pa-2">
              {{ $t("profile.born_date") }}
              <span class="font-weight-bold">
                {{ employee.employee && employee.employee.born_date }}
              </span>
            </v-card-text>
            <v-card-text class="pa-2">
              {{ $t("profile.email") }}
              <span class="font-weight-bold">
                {{ employee && employee.email }}
              </span>
            </v-card-text>
          </v-col>
          <v-col
            cols="12"
            md="12"
            v-if="
              employee.eimzo_name ||
              employee.eimzo_username ||
              employee.eimzo_password
            "
          >
            <v-card>
              <v-card-text
                class="pa-2 text-center"
                v-if="employee.eimzo_password"
              >
                <span class="font-weight-bold">{{ $t("profile.imzo") }}</span>
              </v-card-text>
              <v-row class="ma-0">
                <v-col cols="12" md="6">
                  <v-card-text class="pa-2" v-if="employee.eimzo_name">
                    {{ $t("profile.employee_name") }}:
                    <span class="font-weight-bold">
                      {{ employee.eimzo_name }}
                    </span>
                  </v-card-text>
                  <v-card-text class="pa-2" v-if="employee.eimzo_username">
                    {{ $t("profile.user_name") }}
                    <span class="font-weight-bold">
                      {{ employee.eimzo_username }}
                    </span>
                  </v-card-text>
                  <v-card-text class="pa-2" v-if="employee.eimzo_password">
                    {{ $t("profile.password") }}:
                    <span class="font-weight-bold">
                      {{ employee.eimzo_password }}
                    </span>
                  </v-card-text>
                </v-col>
                <v-col cols="12" md="6">
                  <v-card-text class="pa-2">
                    {{ $t("profile.stir") }}:
                    <span class="font-weight-bold">{{ eimzo_inn }}</span>
                  </v-card-text>
                  <v-card-text class="pa-2" v-if="eimzo_given_date">
                    {{ $t("profile.given_date") }}:
                    <span class="font-weight-bold">
                      {{ eimzo_given_date.substr(0, 10) }}
                    </span>
                  </v-card-text>
                  <v-card-text class="pa-2" v-if="eimzo_expere_date">
                    {{ $t("profile.expere_date") }}:
                    <span class="font-weight-bold">
                      {{ eimzo_expere_date.substr(0, 10) }}
                    </span>
                  </v-card-text>
                </v-col>
              </v-row>
            </v-card>
          </v-col>
          <v-col cols="12" md="12" v-if="false">
            <v-card-text>
              <v-card-text>
                <label v-show="false" id="message"></label>
                <v-form name="testform">
                  <v-row>
                    <v-col cols="12" class="my-0 py-0">
                      {{ $t("profile.select_key") }}
                      <br />
                      <select
                        name="key"
                        @change="cbChanged(this)"
                        style="border: 1px solid black"
                        class="pa-2 v-input__control"
                      ></select>
                      <br />
                      <label v-show="false" id="keyId"></label>
                    </v-col>
                    <v-col cols="12" class="mt-0 mb-6 py-0">
                      <v-btn block color="#203d5b" dark @click="push()">
                        {{ $t("profile.push") }}
                        <v-progress-circular
                          v-if="loading"
                          indeterminate
                          :width="3"
                          :size="18"
                        ></v-progress-circular>
                      </v-btn>
                    </v-col>
                  </v-row>
                </v-form>
              </v-card-text>
            </v-card-text>
          </v-col>
        </v-row>

        <v-card-actions>
          <v-spacer></v-spacer>
          <!-- <v-btn color="primary" text @click="dialog = false">{{$t('save')}}</v-btn> -->
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="uploadPhotoDialog" persistent max-width="800px">
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">Upload Photos</span>
          <v-spacer></v-spacer>
          <v-btn
            color="red"
            outlined
            x-small
            fab
            class
            @click="uploadPhotoDialog = false"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form>
            <v-row>
              <v-col
                cols="10"
                style="min-width: 100px; max-width: 100%"
                class="flex-grow-1 flex-shrink-0"
              >
                <label for>Rasm yuklash</label>
                <v-file-input
                  accept="image/png, image/jpeg, image/bmp"
                  placeholder
                  prepend-icon="mdi-camera"
                ></v-file-input>
              </v-col>
              <v-col cols="2" style="min-width: 100px" class="px-0">
                <v-btn
                  class="mt-6"
                  color="success"
                  block
                  @click="successUploadPhoto()"
                  >+</v-btn
                >
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
      </v-card>
    </v-dialog>

    <!--<v-footer color="indigo" app>
      <span class="white--text">&copy; 2019</span>
    </v-footer>-->
  </v-app>
</template>

<script>
import Navigation from "@/components/Navigation";

const axios = require("axios").default;
import Cookies from "js-cookie";
import Swal from "sweetalert2";

export default {
  data() {
    return {
      idesDocList: [],
      test: false,
      fab: false,
      loading: false,
      settingDialog: false,
      myProfileDialog: false,
      myCompanyDialog: false,
      expandOnHover: false,
      documentCreate: [],
      idesDocCount: "",
      idesDocCountReceived: "",
      staff: false,
      admin: false,
      uploadPhotoDialog: false,
      locales: [],
      eimzo_username: "",
      eimzo_name: "",
      eimzo_password: "",
      eimzo_inn: null,
      eimzo_given_date: "",
      eimzo_expere_date: "",
      languages: {},
      drawer: true,
      drawerShow: true,
      // notifications: {},
      create_document: [
        {
          id: 0,
          icon: "mdi-folder-open",
          name_uz_latin: "Erkin shablon",
          name_uz_cyril: "Эркин шаблон",
          name_ru: "Произвольный шаблон",
          route: "/document/create/1",
          count: "",
          visible: false,
        },
      ],
      // document_list: [{}],
      employee: {},
      staff: [],
      roles: [],
      e_imzo: false,
      keys: [],
      EIMZO_MAJOR: 3,
      EIMZO_MINOR: 37,
      errorCAPIWS:
        "Ошибка соединения с E-IMZO. Возможно у вас не установлен модуль E-IMZO или Браузер E-IMZO.",
      errorBrowserWS:
        "Браузер не поддерживает технологию WebSocket. Установите последнюю версию браузера.",
      errorUpdateApp:
        'ВНИМАНИЕ !!! Установите новую версию приложения E-IMZO или Браузера E-IMZO.<br /><a href="https://e-imzo.uz/main/downloads/" role="button">Скачать ПО E-IMZO</a>',
      errorWrongPassword: "Пароль неверный.",
      alert_news: null,
      timeline_count: null,
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
    user() {
      return this.$store.getters.getUser();
    },
    notification() {
      if (
        this.$store.state.notifications.alert &&
        this.$store.state.notifications.alert.length
      ) {
        let alert = "";
        this.$store.state.notifications.alert.map((v) => {
          alert = "| " + v.content + " |" + alert;
        });
        this.alert_news = alert;
      }
      return this.$store.state.notifications;
      // return this.notifications ? this.notifications : "";
    },
    document_list() {
      return this.$store.state.document_list;
    },
    hrLinks() {
      return [
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("employee.index"),
          route: "/employees/list",
          visible: this.$store.getters.checkPermission("employee-index"),
        },
        // {
        //   icon: "mdi-clipboard-account",
        //   text: this.$t("employee.index"),
        //   route: "/employees/children",
        //   visible: this.$store.getters.checkPermission("employee-index")
        // },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("dismissed.employee"),
          route: "/dismissed-employees/list",
          visible: this.$store.getters.checkPermission("employee-index"),
        },
        // {
        //   icon: "mdi-clipboard-account-outline",
        //   text: this.$t("employeeDocument.index"),
        //   route: "/document-employee/list",
        //   visible: this.$store.getters.checkPermission(
        //     "document_employee-index"),
        // },
        // {
        //   icon: "mdi-clipboard-account-outline",
        //   text: "Reports",
        //   route: "/reports/list",
        //   visible: this.$store.getters.checkPermission("report-index")
        // },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("reasons.index"),
          route: "/leaving-reasons/list",
          visible: this.$store.getters.checkPermission("leaving_reasons-index"),
        },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("appeal_content.index"),
          route: "/appeal-content/list",
          visible: this.$store.getters.checkPermission("appeal_content-index"),
        },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("registry.vacation"),
          route: "/vacation-registry",
          visible:
            this.$store.getters.checkPermission("vacation-registry-asaka") ||
            this.$store.getters.checkPermission("vacation-registry-angren") ||
            this.$store.getters.checkPermission("vacation-registry-toshkent") ||
            this.$store.getters.checkPermission("vacation-registry-xorazm"),
        },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("registry.education"),
          route: "/education-registry",
          visible:
            this.$store.getters.checkPermission("education-registry-asaka") ||
            this.$store.getters.checkPermission("education-registry-toshkent") ||
            this.$store.getters.checkPermission("education-registry-xorazm"),
        },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("registry.business_trip"),
          route: "/business-trip-registry",
          visible: this.$store.getters.checkPermission("business_trip"),
        },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("registry.work_calendar"),
          route: "/work-calendar",
          visible: true,
        },
        {
          icon: "mdi-account-network-outline",
          text: this.$t("structure_company"),
          route: "/structure-company",
          visible: this.$store.getters.checkPermission(
            "structure-company-index"
          ),
        },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("familyRelatives.index"),
          route: "/family-relative/list",
          visible: this.$store.getters.checkPermission("family-relative-index"),
        },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("hr_language.index"),
          route: "/hr-language/list",
          visible: this.$store.getters.checkPermission("hr-language-index"),
        },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("hr_party.index"),
          route: "/hr-party/list",
          visible: this.$store.getters.checkPermission("hr-party-index"),
        },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("hr_study_degree.index"),
          route: "/hr-study-degree/list",
          visible: this.$store.getters.checkPermission("hr-study-degree-index"),
        },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("hr_study_type.index"),
          route: "/hr-study-type/list",
          visible: this.$store.getters.checkPermission("hr-study-type-index"),
        },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("hr_university.index"),
          route: "/hr-university/list",
          visible: this.$store.getters.checkPermission("hr-university-index"),
        },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("hr_major.index"),
          route: "/hr-major/list",
          visible: this.$store.getters.checkPermission("hr-major-index"),
        },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("hr_military_rank.index"),
          route: "/hr-military-rank/list",
          visible: this.$store.getters.checkPermission(
            "hr-military-rank-index"
          ),
        },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("hr_state_award.index"),
          route: "/hr-state-awards/list",
          visible: this.$store.getters.checkPermission("hr-state-award-index"),
        },
      ];
    },
    adminLinks() {
      return [
        {
          icon: "mdi-account-tie",
          text: this.$t("user.index"),
          route: "/users/list",
          visible: this.$store.getters.checkPermission("user-index"),
        },
        {
          icon: "mdi-account-tie",
          text: this.$t("user.indexDiller"),
          route: "/users/diller",
          visible: this.$store.getters.checkRole("superadministrator"),
        },
        {
          icon: "mdi-account-lock-outline",
          text: this.$t("user.role-permission"),
          route: "/role-permission/list",
          visible: this.$store.getters.checkPermission("role_permission-index"),
        },
        {
          icon: "mdi-account-key-outline",
          text: this.$t("user.permission"),
          route: "/users/permission",
          visible: this.$store.getters.checkPermission("permission-index"),
        },
        {
          icon: "mdi-account-key-outline",
          text: this.$t("user.role_permission_menu"),
          route: "/users/role-permission",
          visible: this.$store.getters.checkPermission("permission-index"),
        },
        {
          icon: "mdi-account-tie",
          text: this.$t("unblocked_users"),
          route: "/unblocked-users/list",
          visible: this.$store.getters.checkPermission(
            "show_unblocked_user_list"
          ),
        },
        {
          icon: "mdi-account-tie",
          text: this.$t("userTemplate.navigation_main"),
          route: "/user-template/list",
          visible: this.$store.getters.checkPermission(
            "show_unblocked_user_list"
          ),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("company.index"),
          route: "/companies/list",
          visible: this.$store.getters.checkPermission("company-index"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("message.countries"),
          route: "/countries/list",
          visible: this.$store.getters.checkPermission("country-index"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("message.regions"),
          route: "/regions/list",
          visible: this.$store.getters.checkPermission("region-index"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("message.natioanalities"),
          route: "/nationalities/list",
          visible: this.$store.getters.checkPermission("nationality-index"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("message.districts"),
          route: "/districts/list",
          visible: this.$store.getters.checkPermission("district-index"),
        },
        {
          icon: "mdi-bell",
          text: this.$t("message.notifications"),
          route: "/notifications/list",
          visible: this.$store.getters.checkPermission("notification-index"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("refreshDocument"),
          route: "/refresh-document/list",
          visible: this.$store.getters.checkPermission("refresh-document"),
        },
        {
          icon: "mdi-account-lock-outline",
          text: this.$t("Template Report"),
          route: "/template",
          visible: this.$store.getters.checkRole("superadministrator"),
        },
      ];
    },
    inventoryLinks() {
      return [
        {
          icon: "mdi-select-inverse",
          text: this.$t("inventory.index1"),
          route: "/inventory/list/1",
          visible:
            this.$store.getters.checkRole("inventory_operator") ||
            this.$store.getters.checkRole("inventory_controller") ||
            this.$store.getters.checkRole("inventory_report"),
        },
        // {
        //   icon: "mdi-select-inverse",
        //   text: this.$t("inventory.index2"),
        //   route: "/inventory/list/2",
        //   visible:
        //     this.$store.getters.checkRole("inventory_operator") ||
        //     this.$store.getters.checkRole("inventory_controller") ||
        //     this.$store.getters.checkRole("inventory_report"),
        // },
        // {
        //   icon: "mdi-warehouse",
        //   text: this.$t("inventory.address"),
        //   route: "/inventory/addresses",
        //   visible:
        //     // this.$store.getters.checkRole("inventory_operator") ||
        //     this.$store.getters.checkRole("inventory_controller"),
        //   // this.$store.getters.checkRole("inventory_report"),
        // },
        // {
        //   icon: "mdi-car-battery",
        //   text: this.$t("inventory.product"),
        //   route: "/inventory/products",
        //   visible: false,
        //   // this.$store.getters.checkRole("inventory_operator") ||
        //   // this.$store.getters.checkRole("inventory_controller"),
        //   // this.$store.getters.checkRole("inventory_report"),
        // },
        // {
        //   icon: "mdi-tablet-dashboard",
        //   text: this.$t("Hisobot"),
        //   route: "/inventory/report",
        //   visible:
        //     this.$store.getters.checkRole("inventory_operator") ||
        //     this.$store.getters.checkRole("inventory_controller") ||
        //     this.$store.getters.checkRole("inventory_report")
        // },
        // {
        //   icon: "mdi-tablet-dashboard",
        //   text: this.$t("Hisobot"),
        //   route: "/inventory/report1",
        //   visible:
        //     this.$store.getters.checkRole("inventory_operator") ||
        //     this.$store.getters.checkRole("inventory_controller") ||
        //     this.$store.getters.checkRole("inventory_report"),
        // },
        {
          icon: "mdi-tablet-dashboard",
          text: this.$t("Hisobot"),
          route: "/inventory/report2",
          visible:
            this.$store.getters.checkRole("inventory_operator") ||
            this.$store.getters.checkRole("inventory_controller") ||
            this.$store.getters.checkRole("inventory_report"),
        },
        {
          icon: "mdi-paperclip",
          text: this.$t("Biriktirish"),
          route: "/inventory/attaching",
          visible: this.$store.getters.checkRole("inventory_controller"),
        },
      ];
    },
    archiveLinks() {
      return [
        {
          icon: "mdi-archive-outline",
          text: this.$t("employee.name"),
          route: "/documents/list/archive/employee",
          visible: true,
        },
        {
          icon: "mdi-archive-outline",
          text: this.$t("position.index"),
          route: "/documents/list/archive/staff",
          visible: true,
        },
      ];
    },
    kpiLinks() {
      return [
        {
          icon: "mdi-finance",
          text: this.$t("KPI"),
          route: "/kpi",
          visible: true,
        },
        {
          icon: "mdi-finance",
          text: this.$t("KPI-report"),
          route: "/kpi-report",
          visible: this.$store.getters.checkPermission("kpi-comission"),
        },
        {
          icon: "mdi-finance",
          text: this.$t("KPI-report2"),
          route: "/kpi-report2",
          visible: this.$store.getters.checkPermission("kpi-comission"),
        },
        {
          icon: "mdi-finance",
          text: this.$t("KPI-report3"),
          route: "/kpi-report3",
          visible: this.$store.getters.checkPermission("kpi-comission"),
        },
        {
          icon: "mdi-finance",
          text: this.$t("KPI-report4"),
          route: "/kpi-report4",
          visible: this.$store.getters.checkPermission("kpi-comission"),
        },
        {
          icon: "mdi-finance",
          text: this.$t("KPI-report5"),
          route: "/kpi-report5",
          visible: this.$store.getters.checkPermission("kpi-comission"),
        },
      ];
    },
    staffLinks() {
      return [
        {
          icon: "mdi-card-account-details-outline x-small",
          text: this.$t("staff.index"),
          route: "/staffs/list",
          visible: this.$store.getters.checkPermission("shtat-korish"),
        },
        {
          icon: "mdi-account-plus-outline",
          text: this.$t("critical.index"),
          route: "/staff-criticals/list",
          visible: this.$store.getters.checkPermission("critical-index"),
        },
        {
          icon: "mdi-select-group x-small",
          text: this.$t("department.index"),
          route: "/departments/list",
          visible: this.$store.getters.checkPermission("department-index"),
        },
        {
          icon: "mdi-group x-small",
          text: this.$t("department.tree"),
          route: "/departments/tree",
          visible: this.$store.getters.checkPermission("department-index_tree"),
        },
        {
          icon: "mdi-ungroup",
          text: this.$t("message.departmentType"),
          route: "/department-types/list",
          visible: this.$store.getters.checkPermission("department_type-index"),
        },
        {
          icon: "mdi-account-network-outline",
          text: this.$t("message.positions"),
          route: "/positions/list",
          visible: this.$store.getters.checkPermission("position-index"),
        },
        {
          icon: "mdi-account-hard-hat",
          text: this.$t("message.positionType"),
          route: "/position-types/list",
          visible: this.$store.getters.checkPermission("position_type-index"),
        },
        {
          icon: "mdi-file-alert-outline",
          text: this.$t("message.tariffScale"),
          route: "/tariff-scales/list",
          visible: this.$store.getters.checkPermission("tariff_scale-index"),
        },
        {
          icon: "mdi-cash-plus",
          text: this.$t("message.coefficient"),
          route: "/coefficients/list",
          visible: this.$store.getters.checkPermission("coefficient-index"),
        },
        {
          icon: "mdi-currency-usd",
          text: this.$t("message.currency"),
          route: "/currencies/list",
          visible: this.$store.getters.checkPermission("currency-index"),
        },
        {
          icon: "mdi-currency-usd",
          text: this.$t("message.currencyHistory"),
          route: "/currencies/history",
          visible: this.$store.getters.checkPermission("currency-index"),
        },
        {
          icon: "mdi-office-building-outline",
          text: this.$t("message.accessDepartment"),
          route: "/access-departments/list",
          visible: this.$store.getters.checkPermission(
            "access-department-index"
          ),
        },
        {
          icon: "mdi-account-convert",
          text: this.$t("message.accessType"),
          route: "/access-types/list",
          visible: this.$store.getters.checkPermission("access-type-index"),
        },
        {
          icon: "mdi-chart-timeline-variant",
          text: this.$t("message.ranges"),
          route: "/ranges/list",
          visible: this.$store.getters.checkPermission("range-index"),
        },
        {
          icon: "mdi-account-convert",
          text: this.$t("message.personalType"),
          route: "/personal-types/list",
          visible: this.$store.getters.checkPermission("personal_type-index"),
        },
        {
          icon: "mdi-wallet-outline",
          text: this.$t("message.expenceType"),
          route: "/expence-types/list",
          visible: this.$store.getters.checkPermission("expence_type-index"),
        },
        {
          icon: "mdi-email-alert-outline",
          text: this.$t("message.requirement"),
          route: "/requirements/list",
          visible: this.$store.getters.checkPermission("requirement-index"),
        },
        {
          icon: "mdi-email-variant",
          text: this.$t("message.requirementType"),
          route: "/requirement-types/list",
          visible: this.$store.getters.checkPermission(
            "requirement_type-index"
          ),
        },
        {
          icon: "mdi-webhook",
          text: this.$t("object_type.index"),
          route: "/object-types/list",
          visible: this.$store.getters.checkPermission("object_type-index"),
        },
        {
          icon: "mdi-account-key-outline",
          text: this.$t("sap_transaction.index"),
          route: "/sap-transaction/list",
          visible: this.$store.getters.checkPermission(
            "sap-transactions-index"
          ),
        },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("joint_venture.index"),
          route: "/joint-venture/list",
          visible: this.$store.getters.checkPermission("joint-ventures-index"),
        },
      ];
    },
    links() {
      return [
        {
          icon: "mdi-home-outline",
          text: this.$t("message.home"),
          route: "/",
        },
      ];
    },
    workflowLinks() {
      return [
        {
          icon: "mdi-clipboard-file-outline",
          text: this.$t("message.document_template"),
          route: "/document-templates/list",
          visible: this.$store.getters.checkPermission(
            "document_template-index"
          ),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("message.requisites"),
          route: "/company-requisites/list",
          visible: this.$store.getters.checkPermission("requisite-index"),
        },
        {
          icon: "mdi-checkbox-multiple-marked-outline",
          text: this.$t("message.signers_group"),
          route: "/signers-group/list",
          visible: this.$store.getters.checkPermission("signer_group-index"),
        },
        {
          icon: "mdi-checkbox-multiple-marked-outline",
          text: this.$t("purchase_catalogs.catalogs"),
          route: "/purchase-catalogs/list",
          visible: this.$store.getters.checkPermission(
            "purchase_catalog-index"
          ),
        },
        {
          icon: "mdi-checkbox-multiple-marked-outline",
          text: this.$t("partners.index"),
          route: "/partners/list",
          visible: this.$store.getters.checkPermission("partners-index"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("message.document_types"),
          route: "/document-types/list",
          visible: this.$store.getters.checkPermission("document_type-index"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("ChiefEmployee"),
          route: "/chief-employee/list",
          visible: this.$store.getters.checkPermission("document_type-index"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("message.unioncom"),
          route: "/unioncom/list",
          visible: this.$store.getters.checkPermission("unioncom-index"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("message.bank"),
          route: "/bank/list",
          visible: this.$store.getters.checkPermission("bank-index"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("message.tmib"),
          route: "/tmib/list",
          visible: this.$store.getters.checkPermission("tmib-index"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("message.tmig"),
          route: "/tmig/list",
          visible: this.$store.getters.checkPermission("tmig-index"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("message.embassy"),
          route: "/embassy/list",
          visible: this.$store.getters.checkPermission("embassy-index"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("directory.index"),
          route: "/directories/list",
          visible: this.$store.getters.checkRole("directory"),
        },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("organization.index"),
          route: "/organization/list",
          visible: this.$store.getters.checkPermission("organization-index"),
        },
        {
          icon: "mdi-clipboard-account-outline",
          text: this.$t("requestdoc.index"),
          route: "/requestdoc/list",
          visible: this.$store.getters.checkPermission("requestdoc-index"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("Document History"),
          route: "/documents/document-event",
          visible: this.$store.getters.checkRole("superadministrator"),
        },
      ];
    },
    documentLinks() {
      return [
        {
          icon: "mdi-folder-open",
          text: this.$t("document.inboxs"),
          route: "/documents/list",
          visible: true,
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("document.outboxs"),
          route: "/documents/list",
          visible: true,
        },
      ];
    },
    reports() {
      return [
        // {
        //   icon: "mdi-folder-open",
        //   text: this.$t("message.report"),
        //   route: "/reports/template",
        //   visible: true
        // },
        {
          icon: "mdi-folder-open",
          text: this.$t("message.report"),
          route: "/documents/report",
          visible: false,
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("message.znz"),
          route: "/documents/report/znz",
          visible: false,
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("message.lsp_report"),
          route: "/documents/report/lsp",
          visible: false,
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("department.index"),
          route: "/reports/department/0",
          visible: false,
        },
        // {
        //   icon: "mdi-folder-open",
        //   text: this.$t("department.okd"),
        //   route: "/reports/department-okd/0",
        //   visible: this.$store.getters.checkPermission("okd-report-index"),
        // },

        {
          icon: "mdi-folder-open",
          text: this.$t("department.okd"),
          route: "/reports/okd-report-full",
          visible: this.$store.getters.checkPermission("okd-report-index"),
        },
        {
          icon: "mdi-folder-open",
          text: "OKD (T)",
          route: "/reports/okd-report-full-toshkent",
          visible: this.$store.getters.checkPermission("okd-report-index"),
        },
        // {
        //   icon: "mdi-folder-open", 96525
        //   text: this.$t("department.okd_asaka"),
        //   route: "/reports/okd-report",
        //   visible: this.$store.getters.checkPermission("okd-report-index"),
        // },
        {
          icon: "mdi-folder-open",
          text: this.$t("department.myokd"),
          route: "/reports/document-report-employee",
          visible: true,
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("my_documents"),
          route: "/reports/document-attribute-report/my",
          visible: this.$store.getters.checkPermission("my_documents_report"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("my_documents_2"),
          route: "/reports/document-attribute-report/my-inbox",
          visible: this.$store.getters.checkPermission("my_documents_report"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("report.my_documents"),
          route: "/reports/my-document-report",
          visible: true
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("all_documents"),
          route: "/reports/document-attribute-report/all",
          visible: this.$store.getters.checkRole("superadministrator"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("Template reports"),
          route: "/reports/document-attribute-report/selected",
          visible: this.$store.getters.checkPermission("attribute-report"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("control_punkt.report"),
          route: "/reports/control-punkt-report",
          visible: this.$store.getters.checkPermission("control_punkt_report"),
        },
        // {
        //   icon: "mdi-folder-open",
        //   text: this.$t("Avia report"),
        //   route: "/reports/avia-report",
        //   visible: this.$store.getters.checkPermission("attribute-report"),
        // },
      ];
    },

    control_document() {
      return [
        {
          icon: "mdi-folder-open",
          text: this.$t("department.appeal"),
          route: "/documents/control/murojaat/4",
          visible: this.$store.getters.checkPermission("control_document"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("department.incoming"),
          route: "/documents/control/kiruvchi/24",
          visible: this.$store.getters.checkPermission("control_document"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("department.order"),
          route: "/documents/control/buyruq/2-24",
          visible: this.$store.getters.checkPermission("control_document"),
        },
        {
          icon: "mdi-folder-open",
          text: this.$t("department.outgoing"),
          route: "/documents/control/chiquvchi/23",
          visible: this.$store.getters.checkPermission("control_document"),
        },
      ];
    },
    compliance() {
      return [
        {
          icon: "mdi-file-document-outline",
          text: this.$t("cancel_documents"),
          route: "/complaens/cencel-document",
          visible: this.$store.getters.checkPermission(
            "complaens-cancel-documents"
          ),
        },
        {
          icon: "mdi-file-document-outline",
          text: this.$t("report.payment_sheet"),
          route: "/complaens/control/payment_sheet",
          visible: this.$store.getters.checkPermission(
            "complaens-cancel-documents"
          ),
        },
        {
          icon: "mdi-file-document-outline",
          text: this.$t("report.lsp"),
          route: "/complaens/control/lsp",
          visible: this.$store.getters.checkPermission(
            "complaens-cancel-documents"
          ),
        },
        {
          icon: "mdi-file-document-outline",
          text: this.$t("department.incoming"),
          route: "/complaens/control/compliance_incoming",
          visible: this.$store.getters.checkPermission(
            "complaens-cancel-documents"
          ),
        },
      ];
    },
    pochta() {
      return [
        {
          icon: "mdi-file-document-outline",
          text: this.$t("pochta_list"),
          route: "/post-order/list",
          visible: this.$store.getters.checkPermission("pochta"),
        },
        {
          icon: "mdi-file-document-outline",
          text: this.$t("pochta_info"),
          route: "/post-order/info",
          visible: this.$store.getters.checkPermission("pochta"),
        },
      ];
    },
    centrum() {
      return [
      {
          icon: "mdi-file-document-outline",
          text: this.$t("uploadFiles"),
          route: "/centrum/import",
          visible: this.$store.getters.checkPermission("centrum"),
        },
        {
          icon: "mdi-file-document-outline",
          text: this.$t("AKT"),
          route: "/documents/list/akt/0",
          visible: this.$store.getters.checkPermission("centrum"),
        },
        {
          icon: "mdi-file-document-outline",
          text: this.$t("Cancelled Acts"),
          route: "/documents/list/akt-cancel/0",
          visible: this.$store.getters.checkPermission("centrum"),
        },
        {
          icon: "mdi-file-document-outline",
          text: this.$t("Akt Report"),
          route: "/centrum/report",
          visible: this.$store.getters.checkPermission("centrum"),
        },
        {
          icon: "mdi-file-document-outline",
          text: this.$t("Attribute Report"),
          route: "/centrum/attribute-report",
          visible: this.$store.getters.checkPermission("centrum"),
        },
        {
          icon: "mdi-file-document-outline",
          text: this.$t("ACT Report"),
          route: "/centrum/act-report",
          visible: this.$store.getters.checkPermission("centrum"),
        },
      ];
    },
  },
  created() {
    window.addEventListener("scroll", this.onScroll);
  },
  methods: {
    onScroll(e) {
      // if (typeof window === 'undefined') return
      const top = window.pageYOffset || e.target.scrollTop || 0;
      this.fab = top > 20;
    },
    toTop() {
      this.$vuetify.goTo(0);
    },
    push() {
      axios
        .post(this.$store.state.backend_url + "api/users/eimzo-push", {
          eimzo_username: this.eimzo_username,
          eimzo_name: this.eimzo_name,
          eimzo_password: this.eimzo_password,
          eimzo_inn: this.eimzo_inn,
          eimzo_given_date: this.eimzo_given_date,
          eimzo_expere_date: this.eimzo_expere_date,
        })
        .then((res) => {
          Swal.fire({
            position: "top-end",
            icon: "success",
            title: "Your E-IMZO has been saved",
            showConfirmButton: false,
            timer: 1500,
          });
        })
        .catch((err) => {});
    },
    AppLoad() {
      EIMZOClient.API_KEYS = [
        "edo.uzautomotors.com",
        "79DC56F42765A0017C31309DB9600EA924684ED023A8079460454768331626AB94CFFF8FC2D4007976D4A6C56F11D56DFA962276DC54AE8C0F39E8A8EBDFA10B",
      ];
      this.uiLoading();
      let EIMZO_MAJOR = this.EIMZO_MAJOR;
      let EIMZO_MINOR = this.EIMZO_MINOR;
      let uiLoadKeys = this.uiLoadKeys;
      EIMZOClient.checkVersion(
        function (major, minor) {
          var newVersion = EIMZO_MAJOR * 100 + EIMZO_MINOR;
          var installedVersion = parseInt(major) * 100 + parseInt(minor);
          if (installedVersion < newVersion) {
            this.uiUpdateApp();
          } else {
            EIMZOClient.installApiKeys(
              function () {
                uiLoadKeys();
              },
              function (e, r) {
                if (r) {
                  this.uiShowMessage(r);
                } else {
                  this.wsError(e);
                }
              }
            );
          }
        },
        function (e, r) {
          if (r) {
            this.uiShowMessage(r);
          } else {
            this.uiNotLoaded(e);
          }
        }
      );
    },
    uiShowMessage(message) {
      alert(message);
    },
    uiLoading() {
      var l = document.getElementById("message");
      if (l) {
        l.innerHTML = "Загрузка ...";
        l.style.color = "red";
      }
    },
    uiNotLoaded(e) {
      var l = document.getElementById("message");
      if (l) {
        l.innerHTML = "";
        if (e) {
          this.wsError(e);
        } else {
          this.uiShowMessage(this.errorBrowserWS);
        }
      }
    },
    uiUpdateApp() {
      var l = document.getElementById("message");
      if (l) l.innerHTML = this.errorUpdateApp;
    },
    uiLoadKeys() {
      this.uiClearCombo();
      let uiCreateItem = this.uiCreateItem;
      let uiShowMessage = this.uiShowMessage;
      let eimzo_password = this.employee.eimzo_password;
      let getUserAuth = this.getUserAuth;
      EIMZOClient.listAllUserKeys(
        function (o, i) {
          var itemId = "itm-" + o.serialNumber + "-" + i;
          return itemId;
        },
        function (itemId, v) {
          return uiCreateItem(itemId, v);
        },
        function (items, firstId) {
          var combo = document.testform.key;
          var option = document.createElement("option");
          option.text = "select";
          combo.add(option);
          // combo.append(<option value="">Select</option>);
          for (var itm in items) {
            var vo = items[itm].getAttribute("vo");
            combo.append(items[itm]);
            if (vo.includes(eimzo_password)) {
              items[itm].setAttribute("selected", "true");
              getUserAuth();
            }
          }
          // if (firstId) {
          //   var id = document.getElementById(firstId);
          //   id.setAttribute("selected", "true");
          // }
        },
        function (e, r) {
          uiShowMessage(this.errorCAPIWS);
        }
      );
    },
    cbChanged(c) {
      if (document.getElementById("keyId"))
        document.getElementById("keyId").innerHTML = "";
      this.getUserAuth();
    },
    uiClearCombo() {
      var combo = document.testform.key;
      combo.length = 0;
    },
    uiCreateItem(itmkey, vo) {
      var now = new Date();
      vo.expired = dates.compare(now, vo.validTo) > 0;
      var itm = document.createElement("option");
      itm.value = itmkey;
      itm.text = vo.CN;
      if (!vo.expired) {
      } else {
        itm.style.color = "gray";
        itm.text = itm.text + " (срок истек)";
      }
      itm.setAttribute("vo", JSON.stringify(vo));
      itm.setAttribute("id", itmkey);
      return itm;
    },
    wsError(e) {
      if (e) {
        this.uiShowMessage(this.errorCAPIWS + " : " + e);
      } else {
        this.uiShowMessage(this.errorBrowserWS);
      }
    },
    sign() {
      var itm = document.testform.key.value;
      if (itm) {
        var id = document.getElementById(itm);
        var vo = JSON.parse(id.getAttribute("vo"));
        var data = document.testform.data.value;
        var keyId = document.getElementById("keyId").innerHTML;
        if (keyId) {
          EIMZOClient.createPkcs7(
            keyId,
            data,
            null,
            function (pkcs7) {
              document.testform.pkcs7.value = pkcs7;
            },
            function (e, r) {
              if (r) {
                if (r.indexOf("BadPaddingException") != -1) {
                  this.uiShowMessage(this.errorWrongPassword);
                } else {
                  this.uiShowMessage(r);
                }
              } else {
                document.getElementById("keyId").innerHTML = "";
                this.uiShowMessage(this.errorBrowserWS);
              }
              if (e) this.wsError(e);
            }
          );
        } else {
          EIMZOClient.loadKey(
            vo,
            function (id) {
              document.getElementById("keyId").innerHTML = id;
              EIMZOClient.createPkcs7(
                id,
                data,
                null,
                function (pkcs7) {
                  document.testform.pkcs7.value = pkcs7;
                },
                function (e, r) {
                  if (r) {
                    if (r.indexOf("BadPaddingException") != -1) {
                      this.uiShowMessage(this.errorWrongPassword);
                    } else {
                      this.uiShowMessage(r);
                    }
                  } else {
                    document.getElementById("keyId").innerHTML = "";
                    this.uiShowMessage(this.errorBrowserWS);
                  }
                  if (e) this.wsError(e);
                }
              );
            },
            function (e, r) {
              if (r) {
                if (r.indexOf("BadPaddingException") != -1) {
                  this.uiShowMessage(this.errorWrongPassword);
                } else {
                  this.uiShowMessage(r);
                }
              } else {
                this.uiShowMessage(this.errorBrowserWS);
              }
              if (e) this.wsError(e);
            }
          );
        }
      }
    },
    getUserAuth() {
      var itm = document.testform.key.value;
      var id = document.getElementById(itm);
      if (id && id.hasAttribute("vo")) {
        var vo = JSON.parse(id.getAttribute("vo"));
        this.eimzo_username = vo.name;
        this.eimzo_name = vo.CN;
        this.eimzo_password = vo.serialNumber;
        this.eimzo_inn = vo.TIN;
        this.eimzo_given_date = vo.validFrom;
        this.eimzo_expere_date = vo.validTo;
      } else {
        this.eimzo_username = "";
        this.eimzo_name = "";
        this.eimzo_password = "";
        this.eimzo_inn = "";
        this.eimzo_given_date = "";
        this.eimzo_expere_date = "";
      }
    },
    getUser() {
      let user = this.$store.getters.getUser();
      this.employee = user.employee;
      this.staff = user.employee.employee_staff[0].staff;
      this.roles = this.$store.getters.getRoles();
      // axios
      //   .get(this.$store.state.backend_url + "api/users/show")
      //   .then(res => {
      //     this.employee = res.data;
      //     this.staff = res.data.employee.employee_staff[0].staff;
      //     this.roles = res.data.roles;
      //   })
      //   .catch(e => {
      //     console.error(e);
      //   });
    },
    alert() {
      //location.reload();
    },
    setLocale: function (arg) {
      this.$i18n.locale = arg;
      this.$store.dispatch("setLocale", arg);
      location.reload();
    },
    logout() {
      this.$store.dispatch("setUser", null);
      this.$store.dispatch("setEimzoKey", null);
      this.$store.dispatch("setPermissions", null);
      this.$store.dispatch("setRole", null);
      this.$store.dispatch("setAccessToken", null);
      window.localStorage.clear();
      Cookies.remove("access_token");
      this.$router.push("/login");
    },
    getCreateDocument() {
      axios
        .get(this.$store.state.backend_url + "api/document-types")
        .then((response) => {
          response.data.forEach((element) => {
            let i = 0;
            element.permissions.forEach((permission) => {
              if (this.$store.getters.checkPermission(permission)) {
                i++;
              }
            });
            this.create_document.push({
              id: element.id,
              icon: "mdi-folder-open",
              name_uz_latin: element.name_uz_latin,
              name_uz_cyril: element.name_uz_cyril,
              name_ru: element.name_ru,
              route: "/document/template/" + element.id,
              count: element.count,
              visible: i == 0 ? false : true,
            });
          });
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    getDocumentList() {
      if (this.user.id > 0) {
        axios
          .get(this.$store.state.backend_url + "api/documents/list-new")
          .then((response) => {
            let document_list = response.data;
            document_list.map((v) => {
              v.visible = this.$store.getters.checkPermission(
                "document-list-" + v.menu_item
              );
              return v;
            });
            this.$store.dispatch("setDocumentList", document_list);
            this.getCreateDocument();
            this.getTimelineCount();
          })
          .catch((error) => {
            console.log(error);
          });
      } else {
        axios
          .get(this.$store.state.backend_url + "api/documents/list")
          .then((response) => {
            let document_list = response.data;
            document_list.map((v) => {
              v.visible = this.$store.getters.checkPermission(
                "document-list-" + v.menu_item
              );
              return v;
            });
            this.$store.dispatch("setDocumentList", document_list);
            this.getCreateDocument();
            this.getTimelineCount();
          })
          .catch((error) => {
            console.log(error);
          });
      }
    },
    getNotifications() {
      axios
        .get(
          this.$store.state.backend_url +
            "api/documents/notification/" +
            this.$i18n.locale
        )
        .then((res) => {
          // this.notifications = res.data;
          this.$store.dispatch("setNotifications", res.data);
          // console.log(res.data.length_expired)
          if (this.$store.state.COMPANY_ID == 1) {
            this.drawerShow = 1; // res.data.length_expired == 0 || res.data.unblocked_user == 1;
          } else this.drawerShow = 1;
        })
        .catch((err) => {
          console.log(err);
        });
    },
    uploadPhoto() {
      this.uploadPhotoDialog = true;
    },
    successUploadPhoto() {
      alert("Successfull!");
    },
    idesCounter() {
      axios
        .get(this.$store.state.backend_url + "api/ides/getCount")
        .then((response) => {
          this.idesDocCount = response.data.doccount;
          this.idesDocCountReceived = response.data.doccountreceived;
          this.idesDocList = response.data.docs;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getTimelineCount() {
      axios
        .get(this.$store.state.backend_url + "api/timeline/get-count")
        .then((response) => {
          this.timeline_count = response.data;
        })
        .catch((error) => {
          console.log(error);
        });
    },
  },
  watch: {
    myProfileDialog(value) {
      if (value) this.AppLoad();
    },
  },
  mounted() {
    this.getDocumentList();
    this.getNotifications();
    this.languages =
      this.$store.state.COMPANY_ID == 1
        ? {
            uz_latin: `O'zbekcha`,
            uz_cyril: `English`,
            ru: `Русский`,
          }
        : {
            uz_latin: `O'zbekcha`,
            uz_cyril: `Ўзбекча`,
            ru: `Русский`,
          };
    this.locales =
      this.$store.state.COMPANY_ID == 1
        ? [
            { value: `uz_latin`, text: `O'zbekcha` },
            { value: `uz_cyril`, text: `English` },
            { value: `ru`, text: `Русский` },
          ]
        : [
            { value: `uz_latin`, text: `O'zbekcha` },
            { value: `uz_cyril`, text: `Ўзбекча` },
            { value: `ru`, text: `Русский` },
          ];
    this.getUser();
    setInterval(() => {
      axios
        .get(
          this.$store.state.backend_url +
            "api/documents/notification/" +
            this.$i18n.locale
        )
        .then((res) => {
          // this.notifications = res.data;
          // if(this.notifications.alert && this.notifications.alert.length)
          //   alert(this.notifications.alert);
          this.$store.dispatch("setNotifications", res.data);
        })
        .catch((err) => {
          console.log(err);
        });
    }, 600000);
    if (this.$store.getters.checkPermission("ides")) {
      this.idesCounter();
    }
  },
};
</script>

<style scoped>
.left-aside .v-list-item--link {
  border-bottom: 1px solid #e5e5e9;
}

.left-aside .v-list-item--link:hover {
  background: #f0f0f5;
}

.left-aside .v-list-group {
  border-bottom: 1px solid #e5e5e9;
}

.v-list-group--active {
  background: #f1f1f1;
}

.v-list-group--active .v-list-group__items .v-list-item--link:first-child {
  border-top: 1px solid #e5e5e9;
}
</style>
