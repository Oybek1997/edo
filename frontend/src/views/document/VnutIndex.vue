<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <!-- <span>{{'sasasa'}}</span> -->
        <span v-if="filter.menu_item == 'cancel'">{{
          $t("document.cancels")
        }}</span>
        <span v-else-if="filter.menu_item == 'outbox'">{{
          $t("register.inside_sz")
        }}</span>
        <span v-else-if="filter.menu_item == 'draft'">{{
          $t("document.drafts")
        }}</span>
        <span v-else-if="filter.menu_item == 'expected'">{{
          $t("document.expected")
        }}</span>
        <span v-else>{{ $t("document.all") }}</span>
        <v-spacer></v-spacer>
        <v-text-field
          v-model="filter.content"
          hide-details
          outlined
          dense
          :label="$t('searchInText')"
          class="mr-1"
          @keyup.native.enter="getFilter()"
        ></v-text-field>
        <v-autocomplete
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
        </v-autocomplete>
        <v-autocomplete
          v-model="filter.reaction_status"
          v-else
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
        </v-autocomplete>
      </v-card-title>
      <v-data-table
        dense
        fixed-header
        :loading-text="$t('loadingText')"
        :no-data-text="noDataText"
        :height="screenHeight"
        :loading="loading"
        :headers="headers"
        :items="items"
        class="ma-1"
        style="border: 1px solid #aaa"
        item-key="id"
        :server-items-length="server_items_length"
        :options.sync="dataTableOptions"
        :disable-pagination="true"
        :footer-props="{
          itemsPerPageOptions:
            $store.state.COMPANY_ID == 3
              ? [20, 50, 100, 200, -1]
              : [20, 50, 100],
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
        :expanded.sync="expanded"
        single-expand
        show-expand
        @update:expanded="toggleExpand"
        @dblclick:row="rowClick"
      >
        <template v-slot:body.prepend>
          <tr class="py-0 my-0 prepend_height">
            <td class="py-0 my-0 dense"></td>
            <td class="py-0 my-0 dense"></td>

            <td class="py-0 my-0 dense">
              <v-text-field
                v-model="filter.id"
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
                v-model="filter.document_template_id"
                :items="
                  $route.params.document_type != 0
                    ? document_templates.filter((v) => {
                        if ($route.params.menu_item == 'outbox') {
                          // alert(v.document_type_id);
                          if (
                            $route.params.document_type == 2524 &&
                            [4, 17, 23, 18, 6, 47, 5].includes(v.id)
                          ) {
                            return true;
                          } else if (
                            $route.params.document_type == 1 &&
                            [1, 44, 54].includes(v.id)
                          ) {
                            return true;
                          } else return false;
                        } else if (
                          v.document_type_id == $route.params.document_type
                        )
                          return true;
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

            <td class="py-0 my-0 dense">
              <v-text-field
                v-model="filter.document_number"
                type="text"
                hide-details
                @keyup.native.enter="getFilter()"
                clearable
              ></v-text-field>
            </td>

            <td
              class="py-0 my-0 dense"
              v-if="user.id == 517 || user.id == 518"
            ></td>

            <td class="py-0 my-0 dense">
              <v-menu
                ref="rangeMenu"
                :close-on-content-click="false"
                :return-value.sync="filter.document_range"
                offset-y
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

            <td class="py-0 my-0 dense"></td>
            <td class="py-0 my-0 dense"></td>
            <td class="py-0 my-0 dense"></td>
            <td class="py-0 my-0 dense"></td>

            <td class="py-0 my-0 dense">
              <v-text-field
                v-model="filter.title"
                type="text"
                hide-details
                @keyup.native.enter="getFilter()"
                clearable
              ></v-text-field>
            </td>

            <td class="py-0 my-0 dense">
              <v-text-field
                v-model="filter.created_by"
                type="text"
                hide-details
                @keyup.native.enter="getFilter()"
                clearable
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense" v-if="$route.params.document_type == 4">
              <v-text-field
                v-model="filter.summary"
                type="text"
                hide-details
                @keyup.native.enter="getFilter()"
                clearable
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense" v-if="$route.params.document_type == 4">
              <v-text-field
                v-model="filter.registration"
                type="text"
                hide-details
                @keyup.native.enter="getFilter()"
                clearable
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense" v-if="$route.params.document_type == 4">
              <v-text-field
                v-model="filter.korrespondent"
                type="text"
                hide-details
                @keyup.native.enter="getFilter()"
                clearable
              ></v-text-field>
            </td>

            <td class="py-0 my-0 dense">
              <v-text-field
                v-model="filter.send_by"
                type="text"
                hide-details
                @keyup.native.enter="getFilter()"
                clearable
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense">
              <v-text-field
                v-model="filter.receive_by"
                type="text"
                hide-details
                @keyup.native.enter="getFilter()"
                clearable
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense" style="min-width: 130px">
              <v-text-field
                v-model="filter.pending_action"
                type="text"
                hide-details
                @keyup.native.enter="getFilter()"
                clearable
              ></v-text-field>
            </td>
            <td class="py-0 my-0 dense"></td>
            <td class="py-0 my-0 dense"></td>
            <td class="py-0 my-0 dense"></td>

            <!-- <td class="py-0 my-0 dense">
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
            </td> -->
          </tr>
        </template>
        <template v-slot:item.num="{ item }">
          {{
            items
              .map(function (x) {
                return x.id;
              })
              .indexOf(item.id) + 1
          }}
        </template>
        <template v-slot:item.document_number="{ item }">
          <!-- <a :href="'#/documents/show/'+item.id">{{item.document_number}}</a> -->
          <!-- {{ signered[items.map(function(x) {return x.id; }).indexOf(item.id) + from - 1] }} -->
          <v-btn
            outlined
            small
            :dark="item.reaction_status == 0 ? false : true"
            rounded
            style="padding: 0 6px"
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
            :to="'/document/' + item.pdf_file_name"
            >{{
              item.document_number_reg
                ? item.document_number_reg
                : item.document_number
            }}</v-btn
          >
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
        <template v-slot:item.docfiles="{ item }">
          <div v-for="(file, k) in item.files" :key="k" style="font-size: 12px">
            <a
              :href="
                $store.state.backend_url + 'staffs/file-download/' + file.id
              "
              >{{ file.file_name }}</a
            >
          </div>
        </template>
        <template v-slot:item.podpisant="{ item }">
          <span style="font-size: 12px"> {{ item.podpisant }}</span>
        </template>

        <template v-slot:item.from_department="{ item }">
          <span style="font-size: 12px">
            {{ item.from_department }}
          </span>
        </template>
        <template v-slot:item.from_podpisant="{ item }">
          <span style="font-size: 12px">
            {{ item.from_manager }}
          </span>
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
          {{
            $i18n.locale == "uz_latin"
              ? item.employee.lastname_uz_latin +
                " " +
                item.employee.firstname_uz_latin.substr(0, 1) +
                "." +
                item.employee.middlename_uz_latin.substr(0, 1) +
                "."
              : item.employee.lastname_uz_cyril +
                " " +
                item.employee.firstname_uz_cyril.substr(0, 1) +
                "." +
                item.employee.middlename_uz_cyril.substr(0, 1) +
                "."
          }}
        </template>
        <template v-slot:item.document_type="{ item }">
          {{ item.document_type["name_" + $i18n.locale] }}
        </template>
        <template v-slot:item.title="{ item }">
          <span
            class="d-inline-block text-truncate"
            style="max-width: 150px"
            :title="item.title"
          >
            {{ item.title }}
          </span>
        </template>
        <template v-slot:item.document_template="{ item }">
          <div style="font-size: 12px">
            {{
              item.document_template &&
              item.document_template["name_" + $i18n.locale]
            }}
          </div>
        </template>

        <template v-slot:item.content="{ item }">
          {{
            ides_items.filter((v) => v.id == item.ides_document_id)[0]
              ? ides_items.filter((v) => v.id == item.ides_document_id)[0]
                  .content
              : ""
          }}
        </template>
        <template v-slot:item.due_date="{ item }">
          <!-- <table
            v-if="ides_items.filter((v) => v.id == item.ides_document_id)[0]"
          >
            <tr
              v-for="(signer_ides, l) in ides_items.filter(
                (v) => v.id == item.ides_document_id
              )[0].document_signers"
              :key="l"
            >
              <td>{{ format_date(signer_ides.due_datetime) }}</td>
            </tr>
          </table> -->
        </template>
        <template v-slot:item.signed_date="{ item }">
          <table
            v-if="ides_items.filter((v) => v.id == item.ides_document_id)[0]"
          >
            <tr
              v-for="(signer_ides, l) in ides_items.filter(
                (v) => v.id == item.ides_document_id
              )[0].document_signers"
              :key="l"
            >
              <td>{{ format_date(signer_ides.due_datetime) }}</td>
            </tr>
          </table>
        </template>
        <template v-slot:item.newsletter="{ item }">
          <table>
            <tr v-for="(signer, l) in item.document_signers" :key="l">
              <td>
                {{
                  signer.signer_employee
                    ? signer.signer_employee["firstname_" + $i18n.locale]
                    : ""
                }}
              </td>
            </tr>
          </table>
        </template>
        <template v-slot:item.resolution="{ item }" >
          <div v-if="item.document_signers.some((v) => [3314, 3366, 3362, 3364, 3363, 3365].includes(v.staff_id))">         
                    <a
                      style="text-decoration: none"
                      :href="
                        $store.state.backend_url + 'fishkatwo/file/' + item.id
                      "
                    >
                      {{ $t("register.resolution") }}
                    </a>
                  </div>
        </template>
        <!-- <template v-slot:item.resolution="{ item }">
          <table
            v-if="ides_items.filter((v) => v.id == item.ides_document_id)[0]"
          >
            <tr
              v-for="(signer_ides, l) in ides_items.filter(
                (v) => v.id == item.ides_document_id
              )[0].document_signers"
              :key="l"
            >
              <td v-if="!signer_ides.description">{{ "-" }}</td>
              <td v-else>{{ signer_ides.description }}</td>
            </tr>
          </table>
        </template> -->
        <template v-slot:item.doers="{ item }">
          <table
            v-if="ides_items.filter((v) => v.id == item.ides_document_id)[0]"
          >
            <tr
              v-for="(signer_ides, l) in ides_items.filter(
                (v) => v.id == item.ides_document_id
              )[0].document_signers"
              :key="l"
            >
              <td v-if="signer_ides.organization_id == 70">
                {{ signer_ides.user["fio_" + [$i18n.locale]] }}
              </td>
              <td v-else>
                {{ signer_ides.organization["name_" + [$i18n.locale]] }}
              </td>
            </tr>
          </table>
        </template>
        <template v-slot:item.info_doer="{ item }">
          <table  class="mainTable"
            v-if="ides_items.filter((v) => v.id == item.ides_document_id)[0]"
          >
            <tr 
              v-for="(signer_ides, l) in ides_items.filter(
                (v) => v.id == item.ides_document_id
              )[0].document_signers"
              :key="l"
            >
              <td  style="max-width: 150px"  v-if="!signer_ides.comments[0]">{{ "-" }}</td>
              <td  style="max-width: 150px"  v-else>{{ signer_ides.comments[0].comment }}</td>
              <!-- <td>{{ signer_ides.comments[0] ? signer_ides.comments[0].comment : "" }}</td> -->
            </tr>
          </table>
        </template>

        <template v-slot:item.korrespondent_new="{ item }">
          {{ item.from_position }}
        </template>

        <template v-slot:item.document_date="{ item }">
          {{
            item.document_date_reg
              ? item.document_date_reg.substr(0, 10) +
                " " +
                item.document_date_reg.substr(11, 5)
              : item.document_date
              ? item.document_date.substr(0, 10) +
                " " +
                item.document_date.substr(11, 5)
              : ""
          }}
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
        </template>
        <template v-slot:item.pending_action="{ item }">
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
                    document_signer.signer_employee[
                      "middlename_" + language
                    ].substr(0, 1) +
                    "."
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
                    document_signer.employee_staffs.employee[
                      "middlename_" + language
                    ].substr(0, 1) +
                    "."
                }}
              </div>
            </div>
          </template>
        </template>
        <template
          v-slot:item.summary="{ item }"
          v-if="$route.params.document_type == 4"
        >
          {{
            item.document_details[0] &&
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
        <template
          v-slot:item.type="{ item }"
          v-if="$route.params.document_type == 4"
        >
          {{
            item.document_details[0] &&
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
            v-if="item.created_employee_id == user.employee_id"
            color="primary"
            class="px-1"
            small
            icon
            @click="documentCopy(item.id)"
          >
            <v-icon>mdi-content-copy</v-icon>
          </v-btn>
          <v-btn
            v-if="
              item.created_employee_id == user.employee_id &&
              item.status == 6 &&
              item.restore == 1
            "
            color="primary"
            class="px-1"
            small
            icon
            @click="restoreDocument(item.id)"
          >
            <v-icon>mdi-undo-variant</v-icon>
          </v-btn>
          <v-btn
            color="success"
            class="px-1"
            small
            icon
            @click="$router.push('/document/' + item.pdf_file_name)"
          >
            <v-icon>mdi-eye-outline</v-icon>
          </v-btn>
          <v-btn
            class="px-1"
            v-if="
              $store.getters.checkPermission('document-update') &&
              item.status < 1
            "
            color="blue"
            small
            icon
            @click="$router.push('/document/update/' + item.id)"
          >
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
          <v-btn
            v-if="item.status == 0"
            color="red"
            class="px-1"
            small
            icon
            @click="deleteItem(item)"
          >
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template>
        <template v-slot:expanded-item="{ headers, item }">
          <td :colspan="headers.length">
            <v-row class="justify-center">
              <v-col cols="12" md="10" lg="9" xl="7" class="py-1 ma-2">
                <v-card class="pa-3">
                  <div
                    v-for="(
                      document_detail, detail_index
                    ) in item.document_details"
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
      date: null,
      loading: false,
      isLoading: false,
      expanded: [],
      search: "",
      dialog: false,
      editMode: null,
      items: [],
      ides_items: [],
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
        send_by: "",
        receive_by: "",
        content: "",
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
    headers() {
      let headers = [
        { text: "", value: "data-table-expand" },
        { text: "#", value: "num", align: "center", width: 30 },
        {
          text: "ID",
          value: "id",
          align: "center",
          width: 50,
          sortable: false,
        },
        {
          text: this.$t("document.type_document"),
          value: "document_template",
          sortable: false,
        },
        {
          text: this.$t("register.reg_num"),
          value: "document_number",
          align: "center",
          sortable: false,
        },
        {
          text: this.$t("document.arrive_date"),
          value: "arrive_date",
          sortable: false,
        },
        {
          text: this.$t("register.reg_date"),
          value: "document_date",
          sortable: false,
        },
        {
          text: this.$t("register.out_num"),
          value: "",
          sortable: false,
        },
        {
          text: this.$t("register.out_date"),
          value: "document_date",
          sortable: false,
        },
        {
          text: this.$t("document.correspondent"),
          value: "from_department",
          sortable: false,
        },

        {
          text: this.$t("register.dop_kor"),
          value: "from_podpisant",
          sortable: false,
        },

        {
          text: this.$t("register.content"),
          value: "content",
          width: 150,
          sortable: false,
        },

        {
          text: this.$t("register.doc_expired_date"),
          value: "due_date",
          sortable: false,
        },

        {
          text: this.$t("register.fact_expired_date"),
          value: "signed_date",
          sortable: false,
        },

        {
          text: this.$t("register.newsletter"),
          value: "podpisant",
          sortable: false,
        },

        {
          text: this.$t("register.resolution"),
          value: "resolution",
          sortable: false,
        },

        {
          text: this.$t("register.doers"),
          value: "doers",
          sortable: false,
        },
        {
          text: this.$t("register.info_doer"),
          value: "info_doer",
          sortable: false,
          // width: 50
        },

        {
          text: this.$t("files"),
          value: "docfiles",
          sortable: false,
        },

        {
          text: this.$t("document.short_content"),
          value: "summary",
          sortable: false,
        },

        {
          text: this.$t("document.number"),
          value: "registration",
          sortable: false,
        },

        {
          text: this.$t("document.type"),
          value: "type",
          sortable: false,
        },

        {
          text: this.$t("document.pending_action"),
          value: "pending_action",
          sortable: false,
        },
        // {
        //   text: this.$t("document.status"),
        //   value: "status",
        //   align: "center",
        //   sortable: false
        // }
      ];
      let localStorage = window.localStorage;
      let User = JSON.parse(localStorage.getItem("user"));
      if (!((User && User.id == 517) || (User && User.id == 518))) {
        headers = headers.filter((v) => v.value != "arrive_date");
      }
      let filtered_headers = headers.filter((header) => {
        if (this.$route.params.document_type != 4) {
          return (
            header.value != "pending_action" &&
            header.value != "summary" &&
            header.value != "registration" &&
            header.value != "type"
          );
        } else return headers;
      });
      return filtered_headers;
    },
    language() {
      return this.$i18n.locale == "ru" ? "uz_cyril" : this.$i18n.locale;
    },
  },
  methods: {
    ides_document_id() {
      this.doc_id = this.items
        .filter((v) => v.ides_document_id)
        .map((v) => {
          return v.ides_document_id;
        });
      if (this.doc_id.length > 0) {
        // console.log(this.items.filter((v) => v.ides_document_id));
        // console.log(this.doc_id);
        axios
          .post(this.$store.state.backend_url + "api/ides/doc-ids", {
            // pagination: this.dataTableOptions,
            doc_id: this.doc_id,
            language: this.$i18n.locale,
            filter: this.filter,
          })
          .then((res) => {
            // console.log(res);
            this.ides_items = res.data;
            // this.loading = false;
          })
          .catch((error) => {
            console.log(error);
            // this.loading = false;
          });
      }
    },
    format_date(value) {
      if (value) {
        return moment(String(value)).format("DD.MM.YYYY");
      }
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
          this.ides_document_id();
          this.items.map((document, index) => {
            let signer = document.document_signers.find(
              (s) => s.action_type_id == 2
            );
            if (signer) {
              if (this.$i18n.locale == "uz_latin") {
                document.podpisant =
                  signer.signer_employee.lastname_uz_latin +
                  " " +
                  signer.signer_employee.firstname_uz_latin.substr(0, 1) +
                  "." +
                  signer.signer_employee.middlename_uz_latin.substr(0, 1) +
                  ".";
              } else {
                document.podpisant =
                  signer.signer_employee.lastname_uz_cyril +
                  " " +
                  signer.signer_employee.firstname_uz_cyril.substr(0, 1) +
                  "." +
                  signer.signer_employee.middlename_uz_cyril.substr(0, 1) +
                  ".";
              }
            } else {
              document.podpisant = "";
            }

            document.docfiles = "123";
            document.reaction_status = 0;
            document.action_type_id = 0;
            document.document_signers.map((document_signer) => {
              if (this.user.employee_id == document_signer.signer_employee_id) {
                document.reaction_status = document_signer.status;
                document.action_type_id = document_signer.action_type_id;
              }
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
        .post(
          this.$store.state.backend_url + "api/document-templates/get-list",
          {
            language: this.$i18n.locale,
          }
        )
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
      // console.log(this.staffs);
    },
    editItem(item) {},
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
              .delete(
                this.$store.state.backend_url +
                  "api/documents/delete/" +
                  item.id
              )
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
.v-item--active {
  background-color: #fff !important;
}
.dense {
  padding: 0px;
  height: 10px !important;
}

.dense .v-text-field__details {
  display: none !important;
}

.dense .v-text-field {
  padding: 0px;
  margin: 0px;
}

.dense div div div {
  margin-bottom: 0px !important;
}
</style>
