
<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span v-if="filter.menu_item == 'inbox'">{{
          $t("register.inbox")
        }}</span>
        <span v-else-if="filter.menu_item == 'appeal'">{{
          $t("register.appeal_fiz")
        }}</span>
        <span v-else-if="filter.menu_item == 'appeal_yur'">{{
          $t("register.appeal_yur")
        }}</span>
        <span v-else-if="filter.menu_item == 'inbox_avto'">{{
          $t("register.inbox_auto")
        }}</span>
        <span v-else-if="filter.menu_item == 'appeal_el'">{{
          $t("register.еappeal_fiz")
        }}</span>
        <span v-else-if="filter.menu_item == 'appeal_oral'">{{
          $t("register.voice_appeal")
        }}</span>
        <span v-else-if="filter.menu_item == 'appeal_el_yur'">{{
          $t("register.еappeal_yur")
        }}</span>
        <span v-else-if="filter.menu_item == 'act'">{{
          $t("register.act_pro")
        }}</span>
        <v-spacer></v-spacer>
        <v-text-field
          v-model="filter.content"
          hide-details
          outlined
          dense
          :label="$t('searchInText')"
          class="mr-1"
          @keyup.native.enter="getList()"
        ></v-text-field>
      </v-card-title>
      <v-simple-table v-show="!loading" class="mainTable" dense fixed-header>
        <template v-slot:default>
          <thead style="text-align: center">
            <tr style="background-color: LightGray">
              <td class="py-0 my-0 dense">#</td>
              <td class="py-0 my-0 dense">id</td>
              <td class="py-0 my-0 dense">
                {{ $t("message.document_types") }}
              </td>
              <td class="py-0 my-0 dense">{{ $t("register.reg_num") }}</td>
              <td class="py-0 my-0 dense">{{ $t("register.reg_date") }}</td>
              <td class="py-0 my-0 dense">{{ $t("register.out_date") }}</td>
              <td class="py-0 my-0 dense">{{ $t("register.out_num") }}</td>
              <td
                v-if="
                  $route.params.menu_item != 'inbox' &&
                    $route.params.menu_item != 'act'
                "
                class="py-0 my-0 dense"
              >
                {{ $t("register.repeat") }}
              </td>
              <td class="py-0 my-0 dense">
                {{ $t("document.correspondent") }}
              </td>
              <td class="py-0 my-0 dense">{{ $t("register.dop_kor") }}</td>
              <td
                v-if="
                  $route.params.menu_item != 'appeal_oral' &&
                    $route.params.menu_item != 'inbox' &&
                    $route.params.menu_item != 'act' &&
                    $route.params.menu_item != 'appeal'
                "
                class="py-0 my-0 dense"
              >
                {{ $t("register.yur_litso") }}
              </td>
              <td
                v-if="
                  $route.params.menu_item == 'appeal_yur' ||
                    $route.params.menu_item == 'appeal_el' ||
                    $route.params.menu_item == 'appeal_el_yur' ||
                    $route.params.menu_item == 'inbox_avto'
                "
                class="py-0 my-0 dense"
              >
                {{ $t("register.signer") }}
              </td>
              <td
                v-if="
                  $route.params.menu_item == 'appeal_oral' ||
                    $route.params.menu_item == 'appeal'
                "
                class="py-0 my-0 dense"
              >
                {{ $t("register.appeal_fio") }}
              </td>
              <td
                v-if="
                  $route.params.menu_item != 'inbox' &&
                    $route.params.menu_item != 'act'
                "
                class="py-0 my-0 dense"
              >
                {{ $t("register.appeal_region") }}
              </td>
              <td
                v-if="
                  $route.params.menu_item != 'inbox' &&
                    $route.params.menu_item != 'act'
                "
                class="py-0 my-0 dense"
              >
                {{ $t("register.appeal_address") }}
              </td>
              <td
                v-if="
                  $route.params.menu_item != 'inbox' &&
                    $route.params.menu_item != 'act'
                "
                class="py-0 my-0 dense"
              >
                {{ $t("register.appeal_type") }}
              </td>
              <td
                v-if="
                  $route.params.menu_item != 'inbox' &&
                    $route.params.menu_item != 'act'
                "
                class="py-0 my-0 dense"
              >
                {{ $t("register.appeal_subject") }}
              </td>
              <td class="py-0 my-0 dense">{{ $t("register.content") }}</td>
              <td
                v-if="
                  $route.params.menu_item != 'inbox' &&
                    $route.params.menu_item != 'act'
                "
                class="py-0 my-0 dense"
              >
                {{ $t("register.auto_marka") }}
              </td>
              <td class="py-0 my-0 dense">
                {{ $t("register.doc_expired_date") }}
              </td>
              <td class="py-0 my-0 dense">{{ $t("register.due_date") }}</td>
              <td class="py-0 my-0 dense">{{ $t("register.newsletter") }}</td>
              <td
                v-if="
                  $route.params.menu_item != 'appeal_el' &&
                    $route.params.menu_item != 'appeal_el_yur'
                "
                class="py-0 my-0 dense"
              >
                {{ $t("register.resolution") }}
              </td>
              <td class="py-0 my-0 dense">{{ $t("register.doers") }}</td>
              <td class="py-0 my-0 dense">{{ $t("register.info_doer") }}</td>
              <td class="py-0 my-0 dense">{{ $t("files") }}</td>
              <td
                v-if="
                  $route.params.menu_item == 'inbox' ||
                    $route.params.menu_item == 'act'
                "
                class="py-0 my-0 dense"
              >
                {{ $t("register.doer_mark") }}
              </td>
            </tr>
            <tr style="background-color: #f0f0f5">
              <td class="py-0 my-0 dense"></td>
              <td class="py-0 my-0 dense">
                <v-text-field
                  v-model="filter.id"
                  type="text"
                  hide-details
                  dense
                  @keyup.native.enter="getList()"
                  clearable
                ></v-text-field>
              </td>
              <!-- Вид документа -->
              <td class="py-0 my-0 dense"></td>
              <!-- рег.№ -->
              <td class="py-0 my-0 dense">
                <v-text-field
                  v-model="filter.reg_num"
                  type="text"
                  hide-details
                  dense
                  @keyup.native.enter="getList()"
                  clearable
                ></v-text-field>
              </td>
              <!-- рег. дата -->
              <td class="py-0 my-0 dense">
                <v-menu
                  ref="rangeMenu"
                  :close-on-content-click="false"
                  :return-value.sync="filter.reg_date"
                  offset-y
                  min-width="290px"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      v-model="filter.reg_date"
                      v-bind="attrs"
                      @keyup.native.enter="getList()"
                      v-on="on"
                      hide-details
                      dense
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
                        filter.reg_date = date;
                        getList();
                      "
                      >OK</v-btn
                    >
                  </v-date-picker>
                </v-menu>
              </td>
              <!-- Исх.№ -->
              <td class="py-0 my-0 dense"></td>
              <!-- Исх. дата -->
              <td class="py-0 my-0 dense"></td>
              <!-- Повторная -->
              <td
                v-if="
                  $route.params.menu_item != 'inbox' &&
                    $route.params.menu_item != 'act'
                "
                class="py-0 my-0 dense"
              ></td>
              <!-- correspondent -->
              <td class="py-0 my-0 dense"></td>
              <!-- Доп. к корр. -->
              <td class="py-0 my-0 dense"></td>
              <!-- Юридическое лицо -->
              <td
                v-if="
                  $route.params.menu_item == 'appeal_yur' ||
                    $route.params.menu_item == 'appeal_el' ||
                    $route.params.menu_item == 'appeal_el_yur' ||
                    $route.params.menu_item == 'inbox_avto'
                "
                class="py-0 my-0 dense"
              ></td>
              <!--Подписант -->
              <td
                v-if="
                  $route.params.menu_item == 'appeal_yur' ||
                    $route.params.menu_item == 'appeal_el' ||
                    $route.params.menu_item == 'appeal_el_yur' ||
                    $route.params.menu_item == 'inbox_avto'
                "
                class="py-0 my-0 dense"
              ></td>
              <!-- ФИО заявителя -->
              <td
                v-if="
                  $route.params.menu_item == 'appeal_oral' ||
                    $route.params.menu_item == 'appeal'
                "
                class="py-0 my-0 dense"
              ></td>
              <!-- Регион -->
              <td
                v-if="
                  $route.params.menu_item != 'inbox' &&
                    $route.params.menu_item != 'act'
                "
                class="py-0 my-0 dense"
              ></td>
              <!-- Адрес -->
              <td
                v-if="
                  $route.params.menu_item != 'inbox' &&
                    $route.params.menu_item != 'act'
                "
                class="py-0 my-0 dense"
              ></td>
              <!-- Тип обращения -->
              <td
                v-if="
                  $route.params.menu_item != 'inbox' &&
                    $route.params.menu_item != 'act'
                "
                class="py-0 my-0 dense"
              ></td>
              <!-- Тема обращения-->
              <td
                v-if="
                  $route.params.menu_item != 'inbox' &&
                    $route.params.menu_item != 'act'
                "
                class="py-0 my-0 dense"
              ></td>
              <!-- Содержание -->
              <td class="py-0 my-0 dense"></td>
              <!-- Марка автомобиля-->
              <td
                v-if="
                  $route.params.menu_item != 'inbox' &&
                    $route.params.menu_item != 'act'
                "
                class="py-0 my-0 dense"
              ></td>
              <!-- Срок исп.док -->
              <td class="py-0 my-0 dense"></td>
              <!-- Срок исп -->
              <td class="py-0 my-0 dense"></td>
              <!-- Рассылка -->
              <td class="py-0 my-0 dense"></td>
              <!-- Резолюция -->
              <td
                v-if="
                  $route.params.menu_item != 'appeal_el' &&
                    $route.params.menu_item != 'appeal_el_yur'
                "
                class="py-0 my-0 dense"
              ></td>
              <!-- Исполнители -->
              <td class="py-0 my-0 dense"></td>
              <!-- Информация об исполнении -->
              <td class="py-0 my-0 dense"></td>
              <!-- Файлы -->
              <td class="py-0 my-0 dense"></td>
              <!-- Отметка об исполнении -->
              <td
                v-if="
                  $route.params.menu_item == 'inbox' ||
                    $route.params.menu_item == 'act'
                "
                class="py-0 my-0 dense"
              ></td>
            </tr>
          </thead>
          <tbody v-for="(item, i) in items" :key="i" style="text-align: center">
            <template v-if="item.ides_signers.length > 0">
              <tr v-for="(signer, k) in item.ides_signers" :key="k">
                <td>
                  {{
                    items
                      .map(function(x) {
                        return x.id;
                      })
                      .indexOf(item.id) + 1
                  }}
                </td>
                <td :rowspan="item.ides_signers.length" v-if="k == 0">
                  <div>
                    <a
                      style="text-decoration: none"
                      :href="
                        $store.state.backend_url + 'fishka/file/' + item.id
                      "
                    >
                      {{ item.id }}
                    </a>
                  </div>
                </td>
                <!-- Вид документа -->
                <td :rowspan="item.ides_signers.length" v-if="k == 0">
                  {{
                    item.document_details[0] &&
                    item.document_details[0].document_detail_contents.find(
                      v =>
                        v.d_d_attribute_id == 654 ||
                        v.d_d_attribute_id == 723 ||
                        v.d_d_attribute_id == 759 ||
                        v.d_d_attribute_id == 786 ||
                        v.d_d_attribute_id == 840 ||
                        v.d_d_attribute_id == 813 ||
                        v.d_d_attribute_id == 866 ||
                        v.d_d_attribute_id == 697
                    )
                      ? item.document_details[0].document_detail_contents.find(
                          v =>
                            v.d_d_attribute_id == 654 ||
                            v.d_d_attribute_id == 723 ||
                            v.d_d_attribute_id == 759 ||
                            v.d_d_attribute_id == 786 ||
                            v.d_d_attribute_id == 840 ||
                            v.d_d_attribute_id == 813 ||
                            v.d_d_attribute_id == 866 ||
                            v.d_d_attribute_id == 697
                        ).value
                      : ""
                  }}
                </td>
                <!-- рег.№ -->
                <td :rowspan="item.ides_signers.length" v-if="k == 0">
                  <v-tab
                    :to="'/document/' + item.pdf_file_name"
                    target="_blank"
                  >
                    {{ item.document_number }}
                  </v-tab>
                </td>
                <!-- рег. дата -->
                <td :rowspan="item.ides_signers.length" v-if="k == 0">
                  {{ item.document_date }}
                </td>
                <!-- Исх.№ -->
                <td :rowspan="item.ides_signers.length" v-if="k == 0">
                  {{
                    item.document_details[0] &&
                    item.document_details[0].document_detail_contents.find(
                      v =>
                        v.d_d_attribute_id == 635 ||
                        v.d_d_attribute_id == 721 ||
                        v.d_d_attribute_id == 757 ||
                        v.d_d_attribute_id == 784 ||
                        v.d_d_attribute_id == 838 ||
                        v.d_d_attribute_id == 811 ||
                        v.d_d_attribute_id == 864 ||
                        v.d_d_attribute_id == 695
                    )
                      ? item.document_details[0].document_detail_contents.find(
                          v =>
                            v.d_d_attribute_id == 635 ||
                            v.d_d_attribute_id == 721 ||
                            v.d_d_attribute_id == 757 ||
                            v.d_d_attribute_id == 784 ||
                            v.d_d_attribute_id == 838 ||
                            v.d_d_attribute_id == 811 ||
                            v.d_d_attribute_id == 864 ||
                            v.d_d_attribute_id == 695
                        ).value
                      : ""
                  }}
                </td>
                <!-- Исх. дата -->
                <td :rowspan="item.ides_signers.length" v-if="k == 0">
                  {{
                    item.document_details[0] &&
                    item.document_details[0].document_detail_contents.find(
                      v =>
                        v.d_d_attribute_id == 653 ||
                        v.d_d_attribute_id == 722 ||
                        v.d_d_attribute_id == 758 ||
                        v.d_d_attribute_id == 785 ||
                        v.d_d_attribute_id == 839 ||
                        v.d_d_attribute_id == 812 ||
                        v.d_d_attribute_id == 865 ||
                        v.d_d_attribute_id == 696
                    )
                      ? item.document_details[0].document_detail_contents.find(
                          v =>
                            v.d_d_attribute_id == 653 ||
                            v.d_d_attribute_id == 722 ||
                            v.d_d_attribute_id == 758 ||
                            v.d_d_attribute_id == 785 ||
                            v.d_d_attribute_id == 839 ||
                            v.d_d_attribute_id == 812 ||
                            v.d_d_attribute_id == 865 ||
                            v.d_d_attribute_id == 696
                        ).value
                      : ""
                  }}
                </td>
                <!-- Повторная -->
                <td
                  v-if="
                    $route.params.menu_item != 'inbox' &&
                      $route.params.menu_item != 'act' &&
                      k == 0
                  "
                  :rowspan="item.ides_signers.length"
                >
                  {{
                    item.document_details[0] &&
                    item.document_details[0].document_detail_contents.find(
                      v =>
                        v.d_d_attribute_id == 719 ||
                        v.d_d_attribute_id == 782 ||
                        v.d_d_attribute_id == 862 ||
                        v.d_d_attribute_id == 809 ||
                        v.d_d_attribute_id == 836 ||
                        v.d_d_attribute_id == 746
                    )
                      ? item.document_details[0].document_detail_contents.find(
                          v =>
                            v.d_d_attribute_id == 719 ||
                            v.d_d_attribute_id == 782 ||
                            v.d_d_attribute_id == 862 ||
                            v.d_d_attribute_id == 809 ||
                            v.d_d_attribute_id == 836 ||
                            v.d_d_attribute_id == 746
                        ).value
                      : ""
                  }}
                </td>
                <!-- correspondent -->
                <td :rowspan="item.ides_signers.length" v-if="k == 0">
                  {{
                    item.document_details[0] &&
                    item.document_details[0].document_detail_contents.find(
                      v =>
                        v.d_d_attribute_id == 657 ||
                        v.d_d_attribute_id == 726 ||
                        v.d_d_attribute_id == 762 ||
                        v.d_d_attribute_id == 843 ||
                        v.d_d_attribute_id == 789 ||
                        v.d_d_attribute_id == 816 ||
                        v.d_d_attribute_id == 869 ||
                        v.d_d_attribute_id == 700
                    )
                      ? item.document_details[0].document_detail_contents.find(
                          v =>
                            v.d_d_attribute_id == 657 ||
                            v.d_d_attribute_id == 726 ||
                            v.d_d_attribute_id == 762 ||
                            v.d_d_attribute_id == 843 ||
                            v.d_d_attribute_id == 789 ||
                            v.d_d_attribute_id == 816 ||
                            v.d_d_attribute_id == 869 ||
                            v.d_d_attribute_id == 700
                        ).value
                      : ""
                  }}
                </td>
                <!-- Доп. к корр. -->
                <td :rowspan="item.ides_signers.length" v-if="k == 0">
                  {{
                    item.document_details[0] &&
                    item.document_details[0].document_detail_contents.find(
                      v =>
                        v.d_d_attribute_id == 658 ||
                        v.d_d_attribute_id == 727 ||
                        v.d_d_attribute_id == 763 ||
                        v.d_d_attribute_id == 790 ||
                        v.d_d_attribute_id == 844 ||
                        v.d_d_attribute_id == 817 ||
                        v.d_d_attribute_id == 870 ||
                        v.d_d_attribute_id == 701
                    )
                      ? item.document_details[0].document_detail_contents.find(
                          v =>
                            v.d_d_attribute_id == 658 ||
                            v.d_d_attribute_id == 727 ||
                            v.d_d_attribute_id == 763 ||
                            v.d_d_attribute_id == 790 ||
                            v.d_d_attribute_id == 844 ||
                            v.d_d_attribute_id == 817 ||
                            v.d_d_attribute_id == 870 ||
                            v.d_d_attribute_id == 701
                        ).value
                      : ""
                  }}
                </td>
                <!-- Юридическое лицо -->
                <td
                  v-if="
                    ($route.params.menu_item == 'appeal_yur' ||
                      $route.params.menu_item == 'appeal_el' ||
                      $route.params.menu_item == 'appeal_el_yur' ||
                      $route.params.menu_item == 'inbox_avto') &&
                      k == 0
                  "
                  :rowspan="item.ides_signers.length"
                >
                  {{
                    item.document_details[0] &&
                    item.document_details[0].document_detail_contents.find(
                      v =>
                        v.d_d_attribute_id == 729 ||
                        v.d_d_attribute_id == 792 ||
                        v.d_d_attribute_id == 819 ||
                        v.d_d_attribute_id == 765
                    )
                      ? item.document_details[0].document_detail_contents.find(
                          v =>
                            v.d_d_attribute_id == 729 ||
                            v.d_d_attribute_id == 792 ||
                            v.d_d_attribute_id == 819 ||
                            v.d_d_attribute_id == 765
                        ).value
                      : ""
                  }}
                </td>
                <!--Подписант -->
                <td
                  v-if="
                    ($route.params.menu_item == 'appeal_yur' ||
                      $route.params.menu_item == 'appeal_el' ||
                      $route.params.menu_item == 'appeal_el_yur' ||
                      $route.params.menu_item == 'inbox_avto') &&
                      k == 0
                  "
                  :rowspan="item.ides_signers.length"
                >
                  {{
                    item.document_details[0] &&
                    item.document_details[0].document_detail_contents.find(
                      v =>
                        v.d_d_attribute_id == 730 ||
                        v.d_d_attribute_id == 793 ||
                        v.d_d_attribute_id == 820 ||
                        v.d_d_attribute_id == 766
                    )
                      ? item.document_details[0].document_detail_contents.find(
                          v =>
                            v.d_d_attribute_id == 730 ||
                            v.d_d_attribute_id == 793 ||
                            v.d_d_attribute_id == 820 ||
                            v.d_d_attribute_id == 766
                        ).value
                      : ""
                  }}
                </td>
                <!-- ФИО заявителя -->
                <td
                  v-if="
                    ($route.params.menu_item == 'appeal_oral' ||
                      $route.params.menu_item == 'appeal') &&
                      k == 0
                  "
                  :rowspan="item.ides_signers.length"
                >
                  {{
                    item.document_details[0] &&
                    item.document_details[0].document_detail_contents.find(
                      v =>
                        v.d_d_attribute_id == 703 || v.d_d_attribute_id == 846
                    )
                      ? item.document_details[0].document_detail_contents.find(
                          v =>
                            v.d_d_attribute_id == 703 ||
                            v.d_d_attribute_id == 846
                        ).value
                      : ""
                  }}
                </td>
                <!-- Регион -->
                <td
                  v-if="
                    $route.params.menu_item != 'inbox' &&
                      $route.params.menu_item != 'act' &&
                      k == 0
                  "
                  :rowspan="item.ides_signers.length"
                >
                  {{
                    item.document_details[0] &&
                    item.document_details[0].document_detail_contents.find(
                      v =>
                        v.d_d_attribute_id == 702 ||
                        v.d_d_attribute_id == 764 ||
                        v.d_d_attribute_id == 791 ||
                        v.d_d_attribute_id == 845 ||
                        v.d_d_attribute_id == 818 ||
                        v.d_d_attribute_id == 728
                    )
                      ? item.document_details[0].document_detail_contents.find(
                          v =>
                            v.d_d_attribute_id == 702 ||
                            v.d_d_attribute_id == 764 ||
                            v.d_d_attribute_id == 791 ||
                            v.d_d_attribute_id == 845 ||
                            v.d_d_attribute_id == 818 ||
                            v.d_d_attribute_id == 728
                        ).value
                      : ""
                  }}
                </td>
                <!-- Адрес -->
                <td
                  v-if="
                    $route.params.menu_item != 'inbox' &&
                      $route.params.menu_item != 'act' &&
                      k == 0
                  "
                  :rowspan="item.ides_signers.length"
                >
                  {{
                    item.document_details[0] &&
                    item.document_details[0].document_detail_contents.find(
                      v =>
                        v.d_d_attribute_id == 704 ||
                        v.d_d_attribute_id == 767 ||
                        v.d_d_attribute_id == 794 ||
                        v.d_d_attribute_id == 847 ||
                        v.d_d_attribute_id == 821 ||
                        v.d_d_attribute_id == 731
                    )
                      ? item.document_details[0].document_detail_contents.find(
                          v =>
                            v.d_d_attribute_id == 704 ||
                            v.d_d_attribute_id == 767 ||
                            v.d_d_attribute_id == 794 ||
                            v.d_d_attribute_id == 847 ||
                            v.d_d_attribute_id == 821 ||
                            v.d_d_attribute_id == 731
                        ).value
                      : ""
                  }}
                </td>
                <!-- Тип обращения -->
                <td
                  v-if="
                    $route.params.menu_item != 'inbox' &&
                      $route.params.menu_item != 'act' &&
                      k == 0
                  "
                  :rowspan="item.ides_signers.length"
                >
                  {{
                    item.document_details[0] &&
                    item.document_details[0].document_detail_contents.find(
                      v =>
                        v.d_d_attribute_id == 705 ||
                        v.d_d_attribute_id == 768 ||
                        v.d_d_attribute_id == 795 ||
                        v.d_d_attribute_id == 848 ||
                        v.d_d_attribute_id == 822 ||
                        v.d_d_attribute_id == 732
                    )
                      ? item.document_details[0].document_detail_contents.find(
                          v =>
                            v.d_d_attribute_id == 705 ||
                            v.d_d_attribute_id == 768 ||
                            v.d_d_attribute_id == 795 ||
                            v.d_d_attribute_id == 848 ||
                            v.d_d_attribute_id == 822 ||
                            v.d_d_attribute_id == 732
                        ).value
                      : ""
                  }}
                </td>
                <!-- Тема обращения-->
                <td
                  v-if="
                    $route.params.menu_item != 'inbox' &&
                      $route.params.menu_item != 'act' &&
                      k == 0
                  "
                  :rowspan="item.ides_signers.length"
                >
                  {{
                    item.document_details[0] &&
                    item.document_details[0].document_detail_contents.find(
                      v =>
                        v.d_d_attribute_id == 706 ||
                        v.d_d_attribute_id == 769 ||
                        v.d_d_attribute_id == 796 ||
                        v.d_d_attribute_id == 849 ||
                        v.d_d_attribute_id == 823 ||
                        v.d_d_attribute_id == 733
                    )
                      ? item.document_details[0].document_detail_contents.find(
                          v =>
                            v.d_d_attribute_id == 706 ||
                            v.d_d_attribute_id == 769 ||
                            v.d_d_attribute_id == 796 ||
                            v.d_d_attribute_id == 849 ||
                            v.d_d_attribute_id == 823 ||
                            v.d_d_attribute_id == 733
                        ).value
                      : ""
                  }}
                </td>
                <!-- Содержание -->
                <td :rowspan="item.ides_signers.length" v-if="k == 0">
                  {{
                    item.document_details[0] &&
                    item.document_details[0].document_detail_contents.find(
                      v =>
                        v.d_d_attribute_id == 665 ||
                        v.d_d_attribute_id == 711 ||
                        v.d_d_attribute_id == 774 ||
                        v.d_d_attribute_id == 801 ||
                        v.d_d_attribute_id == 828 ||
                        v.d_d_attribute_id == 877 ||
                        v.d_d_attribute_id == 738
                    )
                      ? item.document_details[0].document_detail_contents.find(
                          v =>
                            v.d_d_attribute_id == 665 ||
                            v.d_d_attribute_id == 711 ||
                            v.d_d_attribute_id == 774 ||
                            v.d_d_attribute_id == 801 ||
                            v.d_d_attribute_id == 828 ||
                            v.d_d_attribute_id == 877 ||
                            v.d_d_attribute_id == 738
                        ).value
                      : ""
                  }}
                </td>
                <!-- Марка автомобиля-->
                <td
                  v-if="
                    $route.params.menu_item != 'inbox' &&
                      $route.params.menu_item != 'act' &&
                      k == 0
                  "
                  :rowspan="item.ides_signers.length"
                >
                  {{
                    item.document_details[0] &&
                    item.document_details[0].document_detail_contents.find(
                      v =>
                        v.d_d_attribute_id == 712 ||
                        v.d_d_attribute_id == 775 ||
                        v.d_d_attribute_id == 802 ||
                        v.d_d_attribute_id == 855 ||
                        v.d_d_attribute_id == 829 ||
                        v.d_d_attribute_id == 739
                    )
                      ? item.document_details[0].document_detail_contents.find(
                          v =>
                            v.d_d_attribute_id == 712 ||
                            v.d_d_attribute_id == 775 ||
                            v.d_d_attribute_id == 802 ||
                            v.d_d_attribute_id == 855 ||
                            v.d_d_attribute_id == 829 ||
                            v.d_d_attribute_id == 739
                        ).value
                      : ""
                  }}
                </td>
                <!-- Срок исп.док -->
                <td :rowspan="item.ides_signers.length" v-if="k == 0">
                  {{
                    item.document_details[0] &&
                    item.document_details[0].document_detail_contents.find(
                      v =>
                        v.d_d_attribute_id == 660 ||
                        v.d_d_attribute_id == 771 ||
                        v.d_d_attribute_id == 798 ||
                        v.d_d_attribute_id == 851 ||
                        v.d_d_attribute_id == 825 ||
                        v.d_d_attribute_id == 872 ||
                        v.d_d_attribute_id == 735
                    )
                      ? item.document_details[0].document_detail_contents.find(
                          v =>
                            v.d_d_attribute_id == 660 ||
                            v.d_d_attribute_id == 771 ||
                            v.d_d_attribute_id == 798 ||
                            v.d_d_attribute_id == 851 ||
                            v.d_d_attribute_id == 825 ||
                            v.d_d_attribute_id == 872 ||
                            v.d_d_attribute_id == 735
                        ).value
                      : ""
                  }}
                </td>
                <!-- Срок исп -->
                <td>{{ signer.due_datetime }}</td>
                <!-- Рассылка -->
                <td :rowspan="item.ides_signers.length" v-if="k == 0">
                  {{
                    item.document_signers.find(v => v.action_type_id == 2)
                      ? item.document_signers.find(v => v.action_type_id == 2)
                          .signer_employee.lastname_uz_cyril +
                        " " +
                        item.document_signers.find(v => v.action_type_id == 2)
                          .signer_employee.firstname_uz_cyril +
                        " " +
                        item.document_signers.find(v => v.action_type_id == 2)
                          .signer_employee.middlename_uz_cyril
                      : ""
                  }}
                </td>
                <!-- Резолюция -->
                <td
                  v-if="
                    $route.params.menu_item != 'appeal_el' &&
                      $route.params.menu_item != 'appeal_el_yur'
                  "
                >
                  {{ signer.description }}
                </td>
                <!-- Исполнители -->
                <td>{{ signer.organization.name_uz_latin }}</td>
                <!-- Информация об исполнении -->
                <td>
                  {{ signer.comments[0] ? signer.comments[0].comment : "" }}
                </td>
                <!-- Файлы -->
                <td :rowspan="item.ides_signers.length" v-if="k == 0">
                  <div
                    v-for="(file, k) in item.files"
                    :key="k"
                    style="font-size: 12px"
                  >
                    <a
                      :href="
                        $store.state.backend_url +
                          'staffs/file-download/' +
                          file.id
                      "
                      >{{ file.file_name }}</a
                    >
                  </div>
                </td>
                <!-- Отметка об исполнении -->
                <td
                  v-if="
                    $route.params.menu_item == 'inbox' ||
                      $route.params.menu_item == 'act'
                  "
                >
                  {{ signer.comments[0] ? signer.comments[0].comment : "" }}
                </td>
              </tr>
            </template>
            <template v-else>
              <tr>
                <td>
                  <div>
                    <a
                      style="text-decoration: none"
                      :href="
                        $store.state.backend_url + 'fishka/file/' + item.id
                      "
                    >
                      {{ item.id }}
                    </a>
                  </div>
                </td>
                <!-- Вид документа -->
                <td>
                  {{
                    item.document_details[0] &&
                    item.document_details[0].document_detail_contents.find(
                      v =>
                        v.d_d_attribute_id == 654 ||
                        v.d_d_attribute_id == 723 ||
                        v.d_d_attribute_id == 759 ||
                        v.d_d_attribute_id == 786 ||
                        v.d_d_attribute_id == 840 ||
                        v.d_d_attribute_id == 813 ||
                        v.d_d_attribute_id == 866 ||
                        v.d_d_attribute_id == 697
                    )
                      ? item.document_details[0].document_detail_contents.find(
                          v =>
                            v.d_d_attribute_id == 654 ||
                            v.d_d_attribute_id == 723 ||
                            v.d_d_attribute_id == 759 ||
                            v.d_d_attribute_id == 786 ||
                            v.d_d_attribute_id == 840 ||
                            v.d_d_attribute_id == 813 ||
                            v.d_d_attribute_id == 866 ||
                            v.d_d_attribute_id == 697
                        ).value
                      : ""
                  }}
                </td>
                <!-- рег.№ -->
                <td>
                  <v-tab
                    :to="'/document/' + item.pdf_file_name"
                    target="_blank"
                  >
                    {{ item.document_number }}
                  </v-tab>
                </td>
                <!-- рег. дата -->
                <td>{{ item.document_date }}</td>
                <!-- Исх.№ -->
                <td>
                  {{
                    item.document_details[0] &&
                    item.document_details[0].document_detail_contents.find(
                      v =>
                        v.d_d_attribute_id == 635 ||
                        v.d_d_attribute_id == 721 ||
                        v.d_d_attribute_id == 757 ||
                        v.d_d_attribute_id == 784 ||
                        v.d_d_attribute_id == 838 ||
                        v.d_d_attribute_id == 811 ||
                        v.d_d_attribute_id == 864 ||
                        v.d_d_attribute_id == 695
                    )
                      ? item.document_details[0].document_detail_contents.find(
                          v =>
                            v.d_d_attribute_id == 635 ||
                            v.d_d_attribute_id == 721 ||
                            v.d_d_attribute_id == 757 ||
                            v.d_d_attribute_id == 784 ||
                            v.d_d_attribute_id == 838 ||
                            v.d_d_attribute_id == 811 ||
                            v.d_d_attribute_id == 864 ||
                            v.d_d_attribute_id == 695
                        ).value
                      : ""
                  }}
                </td>
                <!-- Исх. дата -->
                <td>
                  {{
                    item.document_details[0] &&
                    item.document_details[0].document_detail_contents.find(
                      v =>
                        v.d_d_attribute_id == 653 ||
                        v.d_d_attribute_id == 722 ||
                        v.d_d_attribute_id == 758 ||
                        v.d_d_attribute_id == 785 ||
                        v.d_d_attribute_id == 839 ||
                        v.d_d_attribute_id == 812 ||
                        v.d_d_attribute_id == 865 ||
                        v.d_d_attribute_id == 696
                    )
                      ? item.document_details[0].document_detail_contents.find(
                          v =>
                            v.d_d_attribute_id == 653 ||
                            v.d_d_attribute_id == 722 ||
                            v.d_d_attribute_id == 758 ||
                            v.d_d_attribute_id == 785 ||
                            v.d_d_attribute_id == 839 ||
                            v.d_d_attribute_id == 812 ||
                            v.d_d_attribute_id == 865 ||
                            v.d_d_attribute_id == 696
                        ).value
                      : ""
                  }}
                </td>
                <!-- Повторная -->
                <td
                  v-if="
                    $route.params.menu_item != 'inbox' &&
                      $route.params.menu_item != 'act'
                  "
                >
                  {{
                    item.document_details[0] &&
                    item.document_details[0].document_detail_contents.find(
                      v =>
                        v.d_d_attribute_id == 719 ||
                        v.d_d_attribute_id == 782 ||
                        v.d_d_attribute_id == 862 ||
                        v.d_d_attribute_id == 809 ||
                        v.d_d_attribute_id == 836 ||
                        v.d_d_attribute_id == 746
                    )
                      ? item.document_details[0].document_detail_contents.find(
                          v =>
                            v.d_d_attribute_id == 719 ||
                            v.d_d_attribute_id == 782 ||
                            v.d_d_attribute_id == 862 ||
                            v.d_d_attribute_id == 809 ||
                            v.d_d_attribute_id == 836 ||
                            v.d_d_attribute_id == 746
                        ).value
                      : ""
                  }}
                </td>
                <!-- correspondent -->
                <td>
                  {{
                    item.document_details[0] &&
                    item.document_details[0].document_detail_contents.find(
                      v =>
                        v.d_d_attribute_id == 657 ||
                        v.d_d_attribute_id == 726 ||
                        v.d_d_attribute_id == 762 ||
                        v.d_d_attribute_id == 843 ||
                        v.d_d_attribute_id == 789 ||
                        v.d_d_attribute_id == 816 ||
                        v.d_d_attribute_id == 869 ||
                        v.d_d_attribute_id == 700
                    )
                      ? item.document_details[0].document_detail_contents.find(
                          v =>
                            v.d_d_attribute_id == 657 ||
                            v.d_d_attribute_id == 726 ||
                            v.d_d_attribute_id == 762 ||
                            v.d_d_attribute_id == 843 ||
                            v.d_d_attribute_id == 789 ||
                            v.d_d_attribute_id == 816 ||
                            v.d_d_attribute_id == 869 ||
                            v.d_d_attribute_id == 700
                        ).value
                      : ""
                  }}
                </td>
                <!-- Доп. к корр. -->
                <td>
                  {{
                    item.document_details[0] &&
                    item.document_details[0].document_detail_contents.find(
                      v =>
                        v.d_d_attribute_id == 658 ||
                        v.d_d_attribute_id == 727 ||
                        v.d_d_attribute_id == 763 ||
                        v.d_d_attribute_id == 790 ||
                        v.d_d_attribute_id == 844 ||
                        v.d_d_attribute_id == 817 ||
                        v.d_d_attribute_id == 870 ||
                        v.d_d_attribute_id == 701
                    )
                      ? item.document_details[0].document_detail_contents.find(
                          v =>
                            v.d_d_attribute_id == 658 ||
                            v.d_d_attribute_id == 727 ||
                            v.d_d_attribute_id == 763 ||
                            v.d_d_attribute_id == 790 ||
                            v.d_d_attribute_id == 844 ||
                            v.d_d_attribute_id == 817 ||
                            v.d_d_attribute_id == 870 ||
                            v.d_d_attribute_id == 701
                        ).value
                      : ""
                  }}
                </td>
                <!-- Юридическое лицо -->
                <td
                  v-if="
                    $route.params.menu_item == 'appeal_yur' ||
                      $route.params.menu_item == 'appeal_el' ||
                      $route.params.menu_item == 'appeal_el_yur' ||
                      $route.params.menu_item == 'inbox_avto'
                  "
                >
                  {{
                    item.document_details[0] &&
                    item.document_details[0].document_detail_contents.find(
                      v =>
                        v.d_d_attribute_id == 729 ||
                        v.d_d_attribute_id == 792 ||
                        v.d_d_attribute_id == 819 ||
                        v.d_d_attribute_id == 765
                    )
                      ? item.document_details[0].document_detail_contents.find(
                          v =>
                            v.d_d_attribute_id == 729 ||
                            v.d_d_attribute_id == 792 ||
                            v.d_d_attribute_id == 819 ||
                            v.d_d_attribute_id == 765
                        ).value
                      : ""
                  }}
                </td>
                <!--Подписант -->
                <td
                  v-if="
                    $route.params.menu_item == 'appeal_yur' ||
                      $route.params.menu_item == 'appeal_el' ||
                      $route.params.menu_item == 'appeal_el_yur' ||
                      $route.params.menu_item == 'inbox_avto'
                  "
                >
                  {{
                    item.document_details[0] &&
                    item.document_details[0].document_detail_contents.find(
                      v =>
                        v.d_d_attribute_id == 730 ||
                        v.d_d_attribute_id == 793 ||
                        v.d_d_attribute_id == 820 ||
                        v.d_d_attribute_id == 766
                    )
                      ? item.document_details[0].document_detail_contents.find(
                          v =>
                            v.d_d_attribute_id == 730 ||
                            v.d_d_attribute_id == 793 ||
                            v.d_d_attribute_id == 820 ||
                            v.d_d_attribute_id == 766
                        ).value
                      : ""
                  }}
                </td>
                <!-- ФИО заявителя -->
                <td
                  v-if="
                    $route.params.menu_item == 'appeal_oral' ||
                      $route.params.menu_item == 'appeal'
                  "
                >
                  {{
                    item.document_details[0] &&
                    item.document_details[0].document_detail_contents.find(
                      v =>
                        v.d_d_attribute_id == 703 || v.d_d_attribute_id == 846
                    )
                      ? item.document_details[0].document_detail_contents.find(
                          v =>
                            v.d_d_attribute_id == 703 ||
                            v.d_d_attribute_id == 846
                        ).value
                      : ""
                  }}
                </td>
                <!-- Регион -->
                <td
                  v-if="
                    $route.params.menu_item != 'inbox' &&
                      $route.params.menu_item != 'act'
                  "
                >
                  {{
                    item.document_details[0] &&
                    item.document_details[0].document_detail_contents.find(
                      v =>
                        v.d_d_attribute_id == 702 ||
                        v.d_d_attribute_id == 764 ||
                        v.d_d_attribute_id == 791 ||
                        v.d_d_attribute_id == 845 ||
                        v.d_d_attribute_id == 818 ||
                        v.d_d_attribute_id == 728
                    )
                      ? item.document_details[0].document_detail_contents.find(
                          v =>
                            v.d_d_attribute_id == 702 ||
                            v.d_d_attribute_id == 764 ||
                            v.d_d_attribute_id == 791 ||
                            v.d_d_attribute_id == 845 ||
                            v.d_d_attribute_id == 818 ||
                            v.d_d_attribute_id == 728
                        ).value
                      : ""
                  }}
                </td>
                <!-- Адрес -->
                <td
                  v-if="
                    $route.params.menu_item != 'inbox' &&
                      $route.params.menu_item != 'act'
                  "
                >
                  {{
                    item.document_details[0] &&
                    item.document_details[0].document_detail_contents.find(
                      v =>
                        v.d_d_attribute_id == 704 ||
                        v.d_d_attribute_id == 767 ||
                        v.d_d_attribute_id == 794 ||
                        v.d_d_attribute_id == 847 ||
                        v.d_d_attribute_id == 821 ||
                        v.d_d_attribute_id == 731
                    )
                      ? item.document_details[0].document_detail_contents.find(
                          v =>
                            v.d_d_attribute_id == 704 ||
                            v.d_d_attribute_id == 767 ||
                            v.d_d_attribute_id == 794 ||
                            v.d_d_attribute_id == 847 ||
                            v.d_d_attribute_id == 821 ||
                            v.d_d_attribute_id == 731
                        ).value
                      : ""
                  }}
                </td>
                <!-- Тип обращения -->
                <td
                  v-if="
                    $route.params.menu_item != 'inbox' &&
                      $route.params.menu_item != 'act'
                  "
                >
                  {{
                    item.document_details[0] &&
                    item.document_details[0].document_detail_contents.find(
                      v =>
                        v.d_d_attribute_id == 705 ||
                        v.d_d_attribute_id == 768 ||
                        v.d_d_attribute_id == 795 ||
                        v.d_d_attribute_id == 848 ||
                        v.d_d_attribute_id == 822 ||
                        v.d_d_attribute_id == 732
                    )
                      ? item.document_details[0].document_detail_contents.find(
                          v =>
                            v.d_d_attribute_id == 705 ||
                            v.d_d_attribute_id == 768 ||
                            v.d_d_attribute_id == 795 ||
                            v.d_d_attribute_id == 848 ||
                            v.d_d_attribute_id == 822 ||
                            v.d_d_attribute_id == 732
                        ).value
                      : ""
                  }}
                </td>
                <!-- Тема обращения-->
                <td
                  v-if="
                    $route.params.menu_item != 'inbox' &&
                      $route.params.menu_item != 'act'
                  "
                >
                  {{
                    item.document_details[0] &&
                    item.document_details[0].document_detail_contents.find(
                      v =>
                        v.d_d_attribute_id == 706 ||
                        v.d_d_attribute_id == 769 ||
                        v.d_d_attribute_id == 796 ||
                        v.d_d_attribute_id == 849 ||
                        v.d_d_attribute_id == 823 ||
                        v.d_d_attribute_id == 733
                    )
                      ? item.document_details[0].document_detail_contents.find(
                          v =>
                            v.d_d_attribute_id == 706 ||
                            v.d_d_attribute_id == 769 ||
                            v.d_d_attribute_id == 796 ||
                            v.d_d_attribute_id == 849 ||
                            v.d_d_attribute_id == 823 ||
                            v.d_d_attribute_id == 733
                        ).value
                      : ""
                  }}
                </td>
                <!-- Содержание -->
                <td>
                  {{
                    item.document_details[0] &&
                    item.document_details[0].document_detail_contents.find(
                      v =>
                        v.d_d_attribute_id == 665 ||
                        v.d_d_attribute_id == 711 ||
                        v.d_d_attribute_id == 774 ||
                        v.d_d_attribute_id == 801 ||
                        v.d_d_attribute_id == 828 ||
                        v.d_d_attribute_id == 877 ||
                        v.d_d_attribute_id == 738
                    )
                      ? item.document_details[0].document_detail_contents.find(
                          v =>
                            v.d_d_attribute_id == 665 ||
                            v.d_d_attribute_id == 711 ||
                            v.d_d_attribute_id == 774 ||
                            v.d_d_attribute_id == 801 ||
                            v.d_d_attribute_id == 828 ||
                            v.d_d_attribute_id == 877 ||
                            v.d_d_attribute_id == 738
                        ).value
                      : ""
                  }}
                </td>
                <!-- Марка автомобиля-->
                <td
                  v-if="
                    $route.params.menu_item != 'inbox' &&
                      $route.params.menu_item != 'act'
                  "
                >
                  {{
                    item.document_details[0] &&
                    item.document_details[0].document_detail_contents.find(
                      v =>
                        v.d_d_attribute_id == 712 ||
                        v.d_d_attribute_id == 775 ||
                        v.d_d_attribute_id == 802 ||
                        v.d_d_attribute_id == 855 ||
                        v.d_d_attribute_id == 829 ||
                        v.d_d_attribute_id == 739
                    )
                      ? item.document_details[0].document_detail_contents.find(
                          v =>
                            v.d_d_attribute_id == 712 ||
                            v.d_d_attribute_id == 775 ||
                            v.d_d_attribute_id == 802 ||
                            v.d_d_attribute_id == 855 ||
                            v.d_d_attribute_id == 829 ||
                            v.d_d_attribute_id == 739
                        ).value
                      : ""
                  }}
                </td>
                <!-- Срок исп.док -->
                <td>
                  {{
                    item.document_details[0] &&
                    item.document_details[0].document_detail_contents.find(
                      v =>
                        v.d_d_attribute_id == 660 ||
                        v.d_d_attribute_id == 771 ||
                        v.d_d_attribute_id == 798 ||
                        v.d_d_attribute_id == 851 ||
                        v.d_d_attribute_id == 825 ||
                        v.d_d_attribute_id == 872 ||
                        v.d_d_attribute_id == 735
                    )
                      ? item.document_details[0].document_detail_contents.find(
                          v =>
                            v.d_d_attribute_id == 660 ||
                            v.d_d_attribute_id == 771 ||
                            v.d_d_attribute_id == 798 ||
                            v.d_d_attribute_id == 851 ||
                            v.d_d_attribute_id == 825 ||
                            v.d_d_attribute_id == 872 ||
                            v.d_d_attribute_id == 735
                        ).value
                      : ""
                  }}
                </td>
                <!-- Срок исп -->
                <td></td>
                <!-- Рассылка -->
                <td>
                  {{
                    item.document_signers.find(v => v.action_type_id == 2)
                      ? item.document_signers.find(v => v.action_type_id == 2)
                          .signer_employee.lastname_uz_cyril +
                        " " +
                        item.document_signers.find(v => v.action_type_id == 2)
                          .signer_employee.firstname_uz_cyril +
                        " " +
                        item.document_signers.find(v => v.action_type_id == 2)
                          .signer_employee.middlename_uz_cyril
                      : ""
                  }}
                </td>
                <!-- Резолюция -->
                <td
                  v-if="
                    $route.params.menu_item != 'appeal_el' &&
                      $route.params.menu_item != 'appeal_el_yur'
                  "
                ></td>
                <!-- Исполнители -->
                <td></td>
                <!-- Информация об исполнении -->
                <td></td>
                <!-- Файлы -->
                <td>
                  <div
                    v-for="(file, k) in item.files"
                    :key="k"
                    style="font-size: 12px"
                  >
                    <a
                      :href="
                        $store.state.backend_url +
                          'staffs/file-download/' +
                          file.id
                      "
                      >{{ file.file_name }}</a
                    >
                  </div>
                </td>
                <!-- Отметка об исполнении -->
                <td
                  v-if="
                    $route.params.menu_item == 'inbox' ||
                      $route.params.menu_item == 'act'
                  "
                ></td>
              </tr>
            </template>
            <!-- <template v-if="item.ides_document_id">
              <tr
                v-for="(it, j) in ides_items
                  .filter((v) => item.ides_document_id == v.id)
                  .map((v) => v.document_signers)[0]"
                :key="j"
              >
                <td>{{ it.description ? it.description : "" }}</td>
                <td>{{ it.organization ? it.organization.name_uz_latin : "" }}</td>
                <td>{{ it.due_datetime ? it.due_datetime : "" }}</td>
                <td><i>{{ it.comments && it.comments[0] ? '"' + it.comments[0].comment + '"' : "" }}</i>
                <td><i>{{ it.comments && it.comments[0] ? '"' + it.comments[0].comment + '"' : "" }}</i>
                </td>                
              </tr>
            </template>
            <template v-else>
              <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
            </template> -->
          </tbody>
        </template>
      </v-simple-table>
      <v-row class="my-0">
        <v-col></v-col>
        <v-col xl="1" lg="2" md="4">
          <v-select
            v-model="pagination.per_page"
            :items="[20, 50, 100, 200]"
            color="#78909C"
            outlined
            dense
            hide-details
            @change="perPageUpdate"
          >
          </v-select>
        </v-col>
        <v-col xl="4" lg="6" md="7">
          <v-btn
            :disabled="arrow1"
            color="#78909C"
            outlined
            class="mx-1"
            @click="firstPage"
            ><v-icon>mdi-arrow-collapse-left</v-icon></v-btn
          >
          <v-btn
            :disabled="arrow2"
            color="#78909C"
            outlined
            class="mx-1"
            @click="prevPage"
            ><v-icon>mdi-arrow-left</v-icon></v-btn
          >
          {{ from }}-{{ to }} of {{ total }}
          <v-btn
            :disabled="arrow3"
            color="#78909C"
            outlined
            class="mx-1"
            @click="nextPage"
            ><v-icon>mdi-arrow-right</v-icon></v-btn
          >
          <v-btn
            :disabled="arrow4"
            color="#78909C"
            outlined
            class="mx-1"
            @click="lastPage"
            ><v-icon>mdi-arrow-collapse-right</v-icon></v-btn
          >
        </v-col>
      </v-row>
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
const Cookies = require("js-cookie");
// import moment from 'moment';
export default {
  data() {
    return {
      loading: false,
      search: "",
      a: null,
      dialog: false,
      items: [],
      ides_items: [],
      doc_id: [],
      date: "",
      pagination: {
        page: 1,
        per_page: 20
      },
      arrow1: true,
      arrow2: true,
      arrow3: true,
      arrow4: true,
      last_page: null,
      from: 0,
      total: 0,
      to: 0,
      filter: {
        id: "",
        title: "",
        reg_num: "",
        reg_date: ""
      },
      document_templates: [],
      document_status: [
        {
          id: 0,
          name_uz_latin: "Yangi",
          name_uz_cyril: "Янги",
          name_ru: "Новый"
        },
        {
          id: 1,
          name_uz_latin: "E'lon qilindi",
          name_uz_cyril: "Эьлон қилинди",
          name_ru: "Опубликованный"
        },
        {
          id: 2,
          name_uz_latin: "Qayta ishlash",
          name_uz_cyril: "қайта ишлаш",
          name_ru: "Обработка"
        },
        {
          id: 3,
          name_uz_latin: "Imzolandi",
          name_uz_cyril: "Имзоланди",
          name_ru: "Подписано"
        },
        {
          id: 4,
          name_uz_latin: "Bajarildi",
          name_uz_cyril: "Бажарилди",
          name_ru: "Выполнено"
        },
        {
          id: 5,
          name_uz_latin: "Yakunlandi",
          name_uz_cyril: "Якунланди",
          name_ru: "Завершено"
        },
        {
          id: 6,
          name_uz_latin: "Bekor qilindi",
          name_uz_cyril: "Бекор қилинди",
          name_ru: "Отменен"
        }
      ]
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
    language() {
      return this.$i18n.locale == "ru" ? "uz_cyril" : this.$i18n.locale;
    }
  },
  methods: {
    rowspan(id) {
      this.a = this.ides_items
        .filter(v => 15 == v.id)
        .map(v => v.document_signers)[0]
        ? this.ides_items
            .filter(v => 15 == v.id)
            .map(v => v.document_signers)[0].length
        : 2;
      // return 4;
      console.log(this.a);
    },
    ides_document_id() {
      this.doc_id = this.items
        .filter(v => v.ides_document_id)
        .map(v => {
          return v.ides_document_id;
        });
      axios
        .post(this.$store.state.backend_url + "api/ides/doc-id-signers", {
          // pagination: this.dataTableOptions,
          doc_id: this.doc_id,
          language: this.$i18n.locale,
          filter: this.filter
        })
        .then(res => {
          // console.log(res);
          this.ides_items = res.data;
          this.rowspan();
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
          this.loading = false;
        });
    },
    format_date(value) {
      if (value) {
        return moment(String(value)).format("DD.MM.YYYY");
      }
    },
    getList() {
      this.loading = true;
      this.noDataText = this.$t("loadingText");
      axios
        .post(this.$store.state.backend_url + "api/documents/register-group", {
          pagination: this.pagination,
          language: this.$i18n.locale,
          filter: this.filter
        })
        .then(response => {
          // console.log(response.data.data);
          this.items = response.data.data;
          this.last_page = response.data.last_page;
          this.from = response.data.from;
          this.total = response.data.total;
          this.to = response.data.to;

          if (response.data.next_page_url && response.data.last_page_url) {
            this.arrow3 = false;
            this.arrow4 = false;
          } else {
            this.arrow3 = true;
            this.arrow4 = true;
          }
          if (response.data.prev_page_url) {
            this.arrow1 = false;
            this.arrow2 = false;
          } else {
            this.arrow1 = true;
            this.arrow2 = true;
          }
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
          this.loading = false;
        });
    },
    getFilter() {
      this.showFilter = true;
      this.document_template = this.document_templates.find(v => {
        return v.id == this.filter.document_template_id;
      });
      // console.log(this.filter);
      Cookies.set("filter", this.filter);
      this.getList();
    },
    getDocumentTemplate() {
      axios
        .post(
          this.$store.state.backend_url + "api/document-templates/get-list",
          {
            language: this.$i18n.locale
          }
        )
        .then(res => {
          this.document_templates = res.data;
        })
        .catch(err => {
          console.error(err);
        });
    },
    nextPage() {
      this.pagination.page += 1;
      this.getList();
    },
    prevPage() {
      this.pagination.page -= 1;
      this.getList();
    },
    lastPage() {
      this.pagination.page = this.last_page;
      this.getList();
    },
    firstPage() {
      this.pagination.page = 1;
      this.getList();
    },
    perPageUpdate() {
      this.pagination.page = 1;
      this.getList();
    }
  },
  watch: {
    $route(to, from) {
      this.filter.menu_item = this.$route.params.menu_item;
      this.getList();
    }
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
  }
};
</script>
