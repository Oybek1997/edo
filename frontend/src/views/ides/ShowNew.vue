<template>
  <div>
    <v-card>
      <v-card-title class="pa-0 pb-1" style="color: #000" color="black">
        <v-btn icon @click="fullScreenDialog = true">
          <v-icon>mdi-fullscreen</v-icon>
        </v-btn>

        <v-btn icon @click="downloadPdf">
          <v-icon>mdi-download</v-icon>
        </v-btn>
        <v-spacer></v-spacer>
        <v-btn v-if="numPages" icon @click="zoomPdf(0)" fab outlined x-small>
          <v-icon>mdi-minus</v-icon>
        </v-btn>
        <v-slider
          max="120"
          v-if="numPages"
          v-model="zoom"
          color="green"
          hide-details
          dense
        ></v-slider>
        <v-btn v-if="numPages" icon @click="zoomPdf(1)" fab outlined x-small>
          <v-icon>mdi-plus</v-icon>
        </v-btn>
        <v-spacer></v-spacer>
        <div class="ma-2">
          <!-- {{enableIdesReceivedButton}} -->
          <v-btn
            v-if="enableIdesReceivedButton"
            color="success"
            class="mx-1"
            outlined
            small
            @click="idesreceived(document.id)"
            >{{ $t("document.received") }}</v-btn
          >
          <!-- <v-btn
            v-if="enableCommentButton && $store.getters.checkPermission('ides')"
            color="success"
            class="mx-1"
            outlined
            small
            @click="getNewDocumentTemplate()"
            >{{ $t("document.newDoc") }}</v-btn
          > -->
          <v-btn
            v-if="enableAcceptButton && $store.getters.checkPermission('ides') && ![109,111,12572].includes(user.employee_id)"
            color="success"
            class="mx-1"
            outlined
            small
            @click="showReaction('accept')"
            >{{ $t("document.accept") }}</v-btn
          >
          <v-btn
            v-if="enableCommentButton && $store.getters.checkPermission('ides')"
            color="success"
            class="mx-1"
            outlined
            small
            @click="showReaction('comment')"
            >{{ $t("document.comment") }}</v-btn
          >
          <v-btn
            v-if="enableRejectButton && $store.getters.checkPermission('ides') && ![109,111,12572].includes(user.employee_id)"
            color="error"
            class="mx-1"
            outlined
            small
            @click="showReaction('reject')"
            >{{ $t("document.reject") }}</v-btn
          >
          <v-btn
            v-if="enableProcessingButton"
            color="primary"
            class="mx-1"
            outlined
            small
            @click="showReaction('processing')"
            >{{ $t("document.processing") }}</v-btn
          >
        </div>
      </v-card-title>
      <v-card-text>
        <!-- <v-row>
            <v-col>
              <v-btn
                v-if="document && document.status == 0"
                color="primary"
                class="mx-1"
                outlined
                small
                @click="publish"
                >Publish</v-btn
              >
            </v-col>
          </v-row> -->
        <v-row>
          <v-col
            cols="8"
            class="pa-1 col-lg-9 col-xl-9 col-md-9"
            :style="'overflow-y: scroll;height:' + screenHeight + 'px;'"
          >
            <div
              :style="
                'margin-left:auto;margin-right:auto;width:' + (zoom + 40) + '%;'
              "
            >
              <pdf
                v-for="i in numPages"
                :key="i"
                :src="loadingTask"
                :page="i"
                style="margin-bottom: 20; border: 5px double #555"
              ></pdf>
            </div>
          </v-col>

          <!-- Right menu -->
          <v-col
            cols="4"
            v-if="document"
            class="ma-0 pa-0 col-xl-3 col-md-3"
            :style="'overflow-y: scroll;height:' + screenHeight + 'px;'"
          >
            <!-- History -->
            <v-card outlined class="mx-0 my-2 pa-0">
              <v-system-bar
                dark
                color="primary"
                style="width: 100%; color: white"
              >
                {{ $t("control_punkt.name") }}
                <v-spacer></v-spacer>
                <v-btn
                  icon
                  small
                  @click="control_punkt_drop = !control_punkt_drop"
                >
                  <v-icon class="ma-0" color="white" v-if="control_punkt_drop"
                    >mdi-menu-down</v-icon
                  >
                  <v-icon class="ma-0" color="white" v-else>mdi-menu-up</v-icon>
                </v-btn>
              </v-system-bar>
              <v-card class="pa-1" v-if="control_punkt_drop">

                <!-- <fieldset>
                  <legend>
                    {{ $t("document.content") }}
                  </legend>
                  <v-card-title class="pa-1 font-italic">
                    {{ document ? document.content : "" }}
                  </v-card-title>
                </fieldset> -->
                

                <v-card-text class="ma-0 pa-1">
                  <label>{{ $t("control_punkt.controller") }}</label>
                  <v-simple-table class="mainTable" dense>
                    <template v-slot:default>
                      <tbody>
                        <tr
                          v-for="(
                            signer, key
                          ) in document.document_signers.filter(
                            (v) => v.action_type_id == 11
                          )"
                          :key="key"
                        >
                          <td>{{ signer.user.fio[$i18n.locale] }}</td>

                          <td
                            v-if="
                              signer.status == 0 && signer.seen_datetime == null
                            "
                          >
                            {{ "" }}
                          </td>
                          <td
                            v-else-if="
                              signer.status == 0 && signer.seen_datetime != null
                            "
                          >
                            {{
                              signer.seen_datetime ? signer.seen_datetime : ""
                            }}
                          </td>
                          <td v-else>
                            {{
                              signer.signed_datetime
                                ? signer.signed_datetime
                                : ""
                            }}
                          </td>
                          <td>
                            <v-btn
                              v-if="
                                signer.status == 0 &&
                                signer.seen_datetime == null
                              "
                              text
                              small
                            >
                              <v-icon color="error">mdi-eye-off</v-icon>
                              <!-- <v-icon color="green">mdi-eye</v-icon> -->
                            </v-btn>
                            <v-btn
                              v-else-if="
                                signer.status == 0 &&
                                signer.seen_datetime != null
                              "
                              text
                              small
                            >
                              <v-icon color="green">mdi-eye</v-icon>
                            </v-btn>
                            <v-btn v-else-if="signer.status == 2" text small>
                              <v-icon color="error">mdi-close</v-icon>
                            </v-btn>
                            <v-img
                              v-else
                              src="img/icons/e-imzo.png"
                              width="30"
                              class="mx-auto"
                              contain
                            ></v-img>
                          </td>
                        </tr>
                        
                        <tr v-if="document.document_signers.find(
                            (v) => v.action_type_id == 11 &&  v.comments.length > 0
                          )">
                          <!-- <td class="pa-0 ma-0" v-for="(comment, k) in document.document_signers.find(
                            (v) => v.action_type_id == 11 &&  v.comments.length > 0
                          ).comments" :key="k" colspan="3"> -->
                          <td class="pa-0 ma-0"  colspan="3">
                          <template>
                            <v-alert
                              v-if="control_comment.status == 3"
                              border="left"
                              dense
                              icon="mdi-comment-outline"
                              text
                              type="info"
                              class="ma-1 pa-2"
                            >
                              <div
                                style="
                                  display: flex;
                                  justify-content: space-between;
                                "
                              >                               
                                <label
                                  v-if="control_comment.created_by"
                                  style="font-weight: bold; font-size: 16px"
                                >
                                  {{ control_comment.comment ? control_comment.comment : '' }}                                 
                                </label>
                              </div>
                              <hr />
                              <label style="font-size: 12px; color: black">
                                <i> {{ '( ' + control_comment.created_by["fio_" + language] + ' )' }} </i>
                              </label>
                              <v-simple-table
                                :disable-pagination="true"
                                dense
                                style="border: none"
                              >
                                <template v-slot:default>
                                  <tbody>
                                    <tr
                                      v-for="(item, index) in control_comment.comment_files"
                                      :key="index"
                                      style="background-color: #fff"
                                    >
                                      <td style="width: 100%; padding-left: 4px">
                                        <v-chip
                                          outlined
                                          small
                                          style="color: #000"
                                          @click="viewPdfFile(item)"
                                          >{{ item.file_name }}</v-chip
                                        >
                                      </td>
                                      <td>
                                        <a
                                          style="text-decoration: none"
                                          :href="
                                            $store.state.backend_url +
                                            'api/ides/file/' +
                                            item.id
                                          "
                                        >
                                          <v-icon color="green"
                                            >mdi-download</v-icon
                                          >
                                        </a>
                                      </td>
                                    </tr>
                                  </tbody>
                                </template>
                              </v-simple-table>
                            </v-alert>
                          </template>
                          </td>
                        </tr>
                      </tbody>
                    </template>
                  </v-simple-table>
                </v-card-text>

                <!-- Files -->

                <v-card-text class="ma-0 pa-1">
                  <label>{{ $t("files") }}</label>
                  <v-simple-table
                    :disable-pagination="true"
                    dense
                    class
                    style="border: 1px solid #aaa"
                  >
                    <template v-slot:default>
                      <tbody>
                        <tr v-for="(item, index) in documentFiles" :key="index">
                          <td style="width: 100%; padding-left: 4px">
                            <v-chip outlined small @click="viewPdfFile(item)">{{
                              item.file_name
                            }}</v-chip>
                          </td>
                          <td>
                            <v-btn
                              text
                              small
                              :href="
                                $store.state.backend_url +
                                'api/ides/file/' +
                                item.id
                              "
                            >
                              <v-icon color="green">mdi-download</v-icon>
                            </v-btn>
                          </td>
                        </tr>
                      </tbody>
                    </template>
                  </v-simple-table>
                </v-card-text>
                <!-- Files -->

                <!-- History -->
                <v-card outlined class="mx-0 my-2 pa-0">
                  <v-card-text class="pa-0" v-if="document">
                    <fieldset
                      v-for="(signer, key) in document.document_signers.filter(
                        (v) => v.action_type_id != 6 && v.action_type_id != 11
                      )"
                      :key="key"
                    >
                      <legend
                        v-if="signer.organization_id == 70"
                        style="font-weight: bold"
                      >
                        {{
                          signer.organization
                            ? signer.organization["name_" + $i18n.locale] +
                              "( " +
                              signer.user.fio[$i18n.locale] +
                              ")"
                            : ""
                        }}
                      </legend>
                      <legend v-else style="font-weight: bold">
                        {{
                          signer.organization
                            ? signer.organization["name_" + $i18n.locale]
                            : ""
                        }}
                      </legend>

                      <v-simple-table class="ma-1 mainTable" dense>
                        <template v-slot:default>
                          <tbody>
                            <tr>
                              <td>
                                <p class="ma-0">
                                  {{
                                    action_types.filter(
                                      (v) => v.id == signer.action_type_id
                                    )[0]["name_" + $i18n.locale]
                                  }}
                                </p>
                                <p class="ma-0">
                                  {{ signer.taken_datetime ? "(" + signer.taken_datetime + ")" : '' }}
                                </p>
                                <p class="ma-0">
                                  {{ signer.due_datetime ? "(" + signer.due_datetime + ")" : '' }}
                                </p>
                              </td>
                              <td
                                v-if="
                                  signer.status == 0 &&
                                  signer.seen_datetime == null
                                "
                              >
                                {{ "" }}
                              </td>
                              <td
                                v-else-if="
                                  signer.status == 0 &&
                                  signer.seen_datetime != null
                                "
                              >
                                {{
                                  signer.seen_datetime
                                    ? signer.seen_datetime
                                    : ""
                                }}
                                <br>
                                {{(signer.seen_by) ? ("( " + signer.seenby["fio_" + language] + " )") : ''}}
                                <!-- {{'( ' + document.document_signers.find + ' )'}} -->
                              </td>
                              <td v-else>
                                {{
                                  signer.signed_datetime
                                    ? signer.signed_datetime
                                    : ""
                                }}
                              </td>
                              <td>
                                <v-btn
                                  v-if="
                                    signer.status == 0 &&
                                    signer.seen_datetime == null
                                  "
                                  text
                                  small
                                >
                                  <v-icon color="error">mdi-eye-off</v-icon>
                                </v-btn>
                                <v-btn
                                  v-else-if="
                                    signer.status == 0 &&
                                    signer.seen_datetime != null
                                  "
                                  text
                                  small
                                >
                                  <v-icon color="green">mdi-eye</v-icon>
                                </v-btn>
                                <v-btn
                                  v-else-if="signer.status == 2"
                                  text
                                  small
                                >
                                  <v-icon color="error">mdi-close</v-icon>
                                </v-btn>
                                <v-btn
                                  v-else-if="signer.status == 3"
                                  text
                                  small
                                >
                                  <v-icon color="primary">mdi-timer-sand-empty</v-icon>
                                </v-btn>
                                <v-img
                                  v-else
                                  src="img/icons/e-imzo.png"
                                  width="30"
                                  class="mx-auto"
                                  contain
                                ></v-img>
                              </td>
                            </tr>
                            <tr v-if="signer.description">
                              <td style="font-weight: bold" colspan="3">
                                {{ signer.description }}
                              </td>
                            </tr>
                          </tbody>
                        </template>
                      </v-simple-table>
                      <div v-for="(comment, k) in signer.comments" :key="k">
                        <v-alert
                          v-if="comment.status == 1"
                          border="left"
                          dense
                          icon="mdi-check-bold"
                          text
                          type="success"
                          class="ma-1"
                        >
                          <div
                            style="
                              display: flex;
                              justify-content: space-between;
                            "
                          >
                            <span
                              v-if="comment.created_by"
                              style="font-weight: bold; font-size: 14px"
                            >
                              {{
                                ( signer.organization_id != 70) ?
                                signer.organization_director
                                  ? signer.organization_director[
                                      "fio_" + language
                                    ]
                                  : comment.created_by["fio_" + language] : comment.created_by["fio_" + language]
                              }}
                            </span>
                            <span style="font-size: 12px">
                              <i>{{ comment.created_at ? comment.created_at.substr(0,10) + " " + comment.created_at.substr(11,5):'' }}</i>
                            </span>
                          </div>
                          <hr />
                          <div
                            style="
                              display: flex;
                              justify-content: space-between;
                              align-items: center;
                              font-size: 12px;
                            "
                          >
                            {{ comment.comment }}
                          </div>
                          <v-simple-table
                            :disable-pagination="true"
                            dense
                            style="border: none"
                            v-if="comment.comment != 'Hujjat yaratildi.'"
                          >
                            <template v-slot:default>
                              <tbody>
                                <tr
                                  v-for="(item, index) in comment.comment_files"
                                  :key="index"
                                  style="background-color: #fff"
                                >
                                  <td style="width: 100%; padding-left: 4px">
                                    <v-chip
                                      outlined
                                      small
                                      style="color: #000"
                                      @click="viewPdfFile(item)"
                                      >{{ item.file_name }}</v-chip
                                    >
                                  </td>
                                  <td>
                                    <a
                                      style="text-decoration: none"
                                      :href="
                                        $store.state.backend_url +
                                        'api/ides/file/' +
                                        item.id
                                      "
                                    >
                                      <v-icon color="green"
                                        >mdi-download</v-icon
                                      >
                                    </a>
                                  </td>
                                </tr>
                              </tbody>
                            </template>
                          </v-simple-table>
                        </v-alert>
                        <v-alert
                          v-if="comment.status == 5"
                          border="left"
                          dense
                          icon="mdi-timer-sand-empty"
                          text
                          type="info"
                          class="ma-1 pa-2"
                        >
                          <div
                            style="
                              display: flex;
                              justify-content: space-between;
                            "
                          >
                            <label
                              v-if="
                                comment.created_by &&
                                comment.created_by.organization_id != 70
                              "
                              style="font-weight: bold; font-size: 14px"
                            >
                              {{
                                signer.organization_director
                                  ? signer.organization_director[
                                      "fio_" + language
                                    ]
                                  : comment.created_by["fio_" + language]
                              }}

                              <!-- {{ comment.created_by.fio.uz_latin }} -->
                            </label>
                            <label
                              v-else="comment.created_by"
                              style="font-weight: bold; font-size: 14px"
                            >
                              <!-- {{ (signer.organization_director) ? signer.organization_director['fio_'  + language ] :  comment.created_by['fio_'  + language ]}} -->
                              {{ comment.created_by["fio_" + language] }}
                            </label>
                            <label style="font-size: 12px">
                              <i>{{ comment.created_at }}</i>
                            </label>
                          </div>
                          <hr />
                          <label style="font-size: 14px">
                            {{ comment.comment }}
                          </label>
                          <v-simple-table
                            :disable-pagination="true"
                            dense
                            style="border: none"
                            v-if="comment.comment != 'Hujjat yaratildi.'"
                          >
                            <template v-slot:default>
                              <tbody>
                                <tr
                                  v-for="(item, index) in comment.comment_files"
                                  :key="index"
                                  style="background-color: #fff"
                                >
                                  <td style="width: 100%; padding-left: 4px">
                                    <v-chip
                                      outlined
                                      small
                                      style="color: #000"
                                      @click="viewPdfFile(item)"
                                      >{{ item.file_name }}</v-chip
                                    >
                                  </td>
                                  <td>
                                    <a
                                      style="text-decoration: none"
                                      :href="
                                        $store.state.backend_url +
                                        'api/ides/file/' +
                                        item.id
                                      "
                                    >
                                      <v-icon color="green"
                                        >mdi-download</v-icon
                                      >
                                    </a>
                                  </td>
                                </tr>
                              </tbody>
                            </template>
                          </v-simple-table>
                        </v-alert>
                        <v-alert
                          v-if="comment.status == 3"
                          border="left"
                          dense
                          icon="mdi-comment-outline"
                          text
                          type="info"
                          class="ma-1 pa-2"
                        >
                          <div
                            style="
                              display: flex;
                              justify-content: space-between;
                            "
                          >
                            <label
                              v-if="
                                comment.created_by &&
                                comment.created_by.organization_id != 70
                              "
                              style="font-weight: bold; font-size: 14px"
                            >
                              {{
                                signer.organization_director
                                  ? signer.organization_director[
                                      "fio_" + language
                                    ]
                                  : comment.created_by["fio_" + language]
                              }}

                              <!-- {{ comment.created_by.fio.uz_latin }} -->
                            </label>
                            <label
                              v-else="comment.created_by"
                              style="font-weight: bold; font-size: 14px"
                            >
                              <!-- {{ (signer.organization_director) ? signer.organization_director['fio_'  + language ] :  comment.created_by['fio_'  + language ]}} -->
                              {{ comment.created_by["fio_" + language] }}
                            </label>
                            <label style="font-size: 12px">
                              <i>{{ comment.created_at }}</i>
                            </label>
                          </div>
                          <hr />
                          <label style="font-size: 14px">
                            {{ comment.comment }}
                          </label>
                          <v-simple-table
                            :disable-pagination="true"
                            dense
                            style="border: none"
                            v-if="comment.comment != 'Hujjat yaratildi.'"
                          >
                            <template v-slot:default>
                              <tbody>
                                <tr
                                  v-for="(item, index) in comment.comment_files"
                                  :key="index"
                                  style="background-color: #fff"
                                >
                                  <td style="width: 100%; padding-left: 4px">
                                    <v-chip
                                      outlined
                                      small
                                      style="color: #000"
                                      @click="viewPdfFile(item)"
                                      >{{ item.file_name }}</v-chip
                                    >
                                  </td>
                                  <td>
                                    <a
                                      style="text-decoration: none"
                                      :href="
                                        $store.state.backend_url +
                                        'api/ides/file/' +
                                        item.id
                                      "
                                    >
                                      <v-icon color="green"
                                        >mdi-download</v-icon
                                      >
                                    </a>
                                  </td>
                                </tr>
                              </tbody>
                            </template>
                          </v-simple-table>
                        </v-alert>
                        <v-alert
                          v-if="comment.status == 2"
                          border="left"
                          dense
                          icon="mdi-close"
                          text
                          type="error"
                          class="ma-1"
                        >
                          <div
                            style="
                              display: flex;
                              justify-content: space-between;
                            "
                          >
                            <span
                              v-if="comment.created_by"
                              style="font-weight: bold; font-size: 14px"
                            >
                              {{
                                signer.organization_director
                                  ? signer.organization_director[
                                      "fio_" + language
                                    ]
                                  : comment.created_by["fio_" + language]
                              }}
                            </span>
                            <span style="font-size: 14px">
                              <i>{{ comment.created_at }}</i>
                            </span>
                          </div>
                          <hr />
                          <div
                            style="
                              display: flex;
                              justify-content: space-between;
                              align-items: center;
                              font-size: 14px;
                            "
                          >
                            {{ comment.comment }}
                          </div>
                          <v-simple-table
                            :disable-pagination="true"
                            dense
                            style="border: none"
                            v-if="comment.comment != 'Hujjat yaratildi.'"
                          >
                            <template v-slot:default>
                              <tbody>
                                <tr
                                  v-for="(item, index) in comment.comment_files"
                                  :key="index"
                                  style="background-color: #fff"
                                >
                                  <td style="width: 100%; padding-left: 4px">
                                    <v-chip
                                      outlined
                                      small
                                      style="color: #000"
                                      @click="viewPdfFile(item)"
                                      >{{ item.file_name }}</v-chip
                                    >
                                  </td>
                                  <td>
                                    <a
                                      style="text-decoration: none"
                                      :href="
                                        $store.state.backend_url +
                                        'api/ides/file/' +
                                        item.id
                                      "
                                    >
                                      <v-icon color="green"
                                        >mdi-download</v-icon
                                      >
                                    </a>
                                  </td>
                                </tr>
                              </tbody>
                            </template>
                          </v-simple-table>
                        </v-alert>
                      </div>
                    </fieldset>
                  </v-card-text>
                </v-card>
              </v-card>
            </v-card>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>
    <v-dialog
      v-model="reactionDialog"
      persistent
      max-width="500px"
      transition="dialog-top-transition"
    >
      <v-card>
        <v-card-title primary-title>
          {{ $t("actions") }}
          <v-spacer></v-spacer>
          <v-btn color="error" small @click="reactionDialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form ref="signForm">
            <v-row>
              <v-col cols="12">
                <v-autocomplete
                v-if="pendingSigners.length > 1"
                  :items="pendingSigners"
                  v-model="document_signer_id"
                  :rules="[(v) => !!v || 'Выберите подписанта']"
                  item-text="description"
                  item-value="id"
                  :label="$t('document.pending_action')"
                  hide-details
                  outlined
                  dense
                >
                <template  v-slot:item="{ item }">
                  <v-list-item-content style="width: 70px">
                    <v-list-item-subtitle>
                       {{ item.description }}
                    </v-list-item-subtitle>
                </v-list-item-content>
                </template>
              </v-autocomplete>
              </v-col>
              <v-col cols="12">
                <v-textarea
                  :label="$t('document.comment')"
                  :rules="[(v) => !!v || 'Required']"
                  rows="2"
                  v-model="reactionComment"
                  hide-details
                  outlined
                ></v-textarea>
              </v-col>
              <v-col cols="12">
                <label for>{{ $t("document.files") }}</label>
                <v-file-input
                  v-model="selectFiles"
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
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions class="pb-4">
          <v-spacer></v-spacer>
          <v-btn
            v-if="reactionStatus == 'accept'"
            color="success"
            class="mx-1"
            outlined
            @click="documentReaction('accept')"
            >{{ $t("document.accept") }}</v-btn
          >
          <v-btn
            v-else-if="reactionStatus == 'reject'"
            color="error"
            class="mx-1"
            outlined
            @click="documentReaction('reject')"
            >{{ $t("document.reject") }}</v-btn
          >
          <v-btn
            v-else-if="reactionStatus == 'processing'"
            color="primary"
            class="mx-1"
            outlined
            @click="documentReaction('processing')"
            >{{ $t("document.processing") }}</v-btn
          >
          <v-btn
            v-else-if="reactionStatus == 'comment'"
            color="warning"
            class="mx-1"
            outlined
            @click="documentReaction('comment')"
            >{{ $t("document.comment") }}</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="fullScreenDialog"
      @keydown.esc="fullScreenDialog = false"
      fullscreen
      hide-overlay
      transition="dialog-bottom-transition"
    >
      <v-card>
        <v-card-title>
          <v-btn outlined x-small fab class @click="changeSize()" v-if="false">
            <v-icon>mdi-arrow-expand-vertical</v-icon>
          </v-btn>
          <v-spacer></v-spacer>
          <v-btn
            color="red"
            outlined
            x-small
            fab
            class
            @click="fullScreenDialog = false"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-row class="mx-0 px-0">
          <v-col>
            <pdf
              v-for="i in numPages"
              :key="i"
              :src="loadingTask"
              :page="i"
              style="margin-bottom: 20px; border: 5px double #555"
            ></pdf>
          </v-col>
        </v-row>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="newDocumentDialog"
      persistent
      max-width="500px"
      transition="dialog-top-transition"
    >
      <v-card>
        <v-card-title primary-title>
          {{ $t("message.create") }}
          <v-spacer></v-spacer>
          <v-btn color="error" small @click="newDocumentDialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form ref="newDocForm">
            <v-row>
              <v-col cols="12">
                <v-select
                  :items="newDocumentTemplates"
                  v-model="selectedTemplate"
                  :rules="[(v) => !!v || 'Выберите ']"
                  item-text="name_uz_latin"
                  item-value="id"
                  :label="$t('userTemplate.table_document_template')"
                  hide-details
                  outlined
                  dense
                ></v-select>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions class="pb-4">
          <v-spacer></v-spacer>
          <v-btn
            v-if="selectedTemplate"
            color="success"
            class="mx-1"
            outlined
            @click="$router.push('/document/create/' + selectedTemplate)"
            >{{ $t("message.create") }}</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="loading"
      hide-overlay
      persistent
      width="300"
      transition="dialog-top-transition"
    >
      <v-card color="primary" dark>
        <v-card-text>
          Please waiting!
          <v-progress-linear
            indeterminate
            color="white"
            class="mb-0"
          ></v-progress-linear>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog v-model="pdfViewDialog" fullscreen>
      <v-card>
        <v-card-title primary-title>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined @click="pdfViewDialog = false">{{
            $t("close")
          }}</v-btn>
        </v-card-title>
        <v-card-text class="d-flex justify-center">
          <iframe
            :width="screenWidth"
            :height="screenHeight"
            :src="$store.state.backend_url + 'api/ides/getFile/' + fileForView.id"
          ></iframe>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="red"
            outlined
            @click="pdfViewDialog = false"
            class="mr-4"
          >
            {{ $t("close") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
const axios = require("axios").default;
import pdf from "vue-pdf";
const moment = require("moment");
export default {
  props: ["documentId"],
  watch: {
    documentId: function (id, oldVal) {
      // watch it
      this.getDocument(id);
    },
    recentMethod: function (id) {
      this.recent(id);
    },
    $route(to, from) {
      // this.ides_doc_id = to.params.id;
      this.getDocument(to.params.id);
      // this.getList(false);
    },
  },
  components: {
    pdf,
  },
  data() {
    return {
      // org_id: null,
      control_comment: null,
      control_punkt_drop: true,
      status_event: false,
      fullScreenDialog: false,
      reactionComment: null,
      document_signer_id: null,
      reactionStatus: null,
      numPages: [],
      document: null,
      documentFiles: null,
      fileForView: { id: 0 },
      pdfViewDialog: false,
      actionTypes: [
        {
          id: '4',
          name_ru: 'Для исполнения',
          name_uz_cyril: 'Ижро учун',
          name_uz_latin: 'Ijro uchun',
        },
        {
          id: '5',
          name_ru: 'Для сведения',
          name_uz_cyril: 'Маълумот учун',
          name_uz_latin: 'Ma`lumot uchun',
        },
        {
          id: '12',
          name_ru: 'На контроль',
          name_uz_cyril: 'Назорат қилиш учун',
          name_uz_latin: 'Nazorat qilish uchun',
        },
        {
          id: '10',
          name_ru: 'Контрольный пункт ',
          name_uz_cyril: 'Топшириқ',
          name_uz_latin: 'Topshiriq',
        },
      ],
      document_status: [
        {
          name_uz_latin: "yangi",
          name_uz_cyril: "янги",
          name_ru: "новый",
          color: "black",
        },
        {
          name_uz_latin: "E'lon qilingan",
          name_uz_cyril: "Эьлон қилинган",
          name_ru: "опубликован",
          color: "cyan",
        },
        {
          name_uz_latin: "qayta ishlashda",
          name_uz_cyril: "қайта ишлашда",
          name_ru: "Обработка",
          color: "blue",
        },
        {
          name_uz_latin: "Imzolangan",
          name_uz_cyril: "Имзоланган",
          name_ru: "Подписано",
          color: "teal",
        },
        {
          name_uz_latin: "Bajarilgan",
          name_uz_cyril: "Бажарилган",
          name_ru: "Выполнено",
          color: "amber",
        },
        {
          name_uz_latin: "Yakunlangan",
          name_uz_cyril: "Якунланган",
          name_ru: "Завершено",
          color: "success",
        },
        {
          name_uz_latin: "Bekor qilingan",
          name_uz_cyril: "Бекор қилинган",
          name_ru: "Отменен",
          color: "error",
        },
      ],
      action_types: [
        {
          id: 4,
          name_uz_latin: "Ijro uchun",
          name_uz_cyril: "Ижро учун",
          name_ru: "Для исполнения",
        },
        {
          id: 15,
          name_uz_latin: "Yig'uvchi",
          name_uz_cyril: "Йигувчи",
          name_ru: "Свод информации",
        },
        {
          id: 11,
          name_uz_latin: "Nazoratchi",
          name_uz_cyril: "Назоратчи",
          name_ru: "Контролшик",
        },
        {
          id: 12,
          name_uz_latin: "Nazorat uchun",
          name_uz_cyril: "Назорат учун",
          name_ru: "На контроль",
        },
        {
          id: 10,
          name_uz_latin: "Topshiriq",
          name_uz_cyril: "Топшириқ",
          name_ru: "Поручения",
        },
        {
          id: 5,
          name_uz_latin: "Ma'lumot uchun",
          name_uz_cyril: "Маълумот учун",
          name_ru: "Для сведения",
        },
      ],
      reactionDialog: false,
      newDocumentDialog: false,
      enableAcceptButton: false,
      enableProcessingButton: false,
      enableRejectButton: false,
      enableCommentButton: false,
      enableIdesReceivedButton: false,
      docSigner: "",
      loading: true,
      pendingSigners: [],
      commentSigners: [],
      selectFiles: [],
      newDocumentTemplates: [],
      selectedTemplate: null,
      base64: null,
      zoom: 60,
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 85;
    },
    screenWidth() {
      return window.innerWidth;
    },
    user() {
      return this.$store.getters.getUser();
    },
    language() {
      return this.$i18n.locale == "ru" ? "uz_cyril" : this.$i18n.locale;
    },
    // user() {
    //   let localStorage = window.localStorage;
    //   return JSON.parse(localStorage.getItem("user"));
    // }
  },
  methods: {
    idesreceived(id) {
      this.loading = true;
      axios
      .post(
          this.$store.state.backend_url + "api/ides/received",
          {
            id: id
          }
        )    
        .then((response) => {
          if (response.status == 200) {
            this.enableIdesReceivedButton = false;
          }
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
        });
      console.log(id);
    },
    recent(id) {
      this.loading = true;
      axios
        .get(this.$store.state.backend_url + "api/ides/recent-signer/" + id)
        .then((response) => {
          if (response.status == 200) {
            this.status_event = true;
          }
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
        });
      // console.log(id);
    },

    getDocument(id) {
      // console.log(this.user);
      this.loading = true;
      axios
        .get(this.$store.state.backend_url + "api/ides/show-new/" + id)
        .then((response) => {          

          this.document = response.data; 

          // nazoratga olish button
          if (
              ![0, 6, 5].includes(this.document.status) &&
              this.document.document_signers.filter(
                (v) =>
                  v.status == 0  &&
                  v.action_type_id != 11 &&
                  v.received_by == null &&
                  this.$store.getters.checkPermission('ides') && !this.$store.getters.checkPermission('ides-control') &&
                  (v.organization_id ==this.$store.state.ides_organization_id ||
                  (v.user && v.user.username == this.user.ides_username))
              ).length > 0
            ) {
              this.enableIdesReceivedButton = true;
            }
            // nazoratga olish button
          // console.log(this.document);         
          this.documentFiles = response.data.document_files;
          this.document.document_signers.map((v) => {
            v.taken_datetime = v.taken_datetime
              ? moment(v.taken_datetime).format("DD.MM.YYYY hh:mm")
              : null;
            v.due_datetime = v.due_datetime
              ? moment(v.due_datetime).format("DD.MM.YYYY hh:mm")
              : null;

            v.comments.map((e) => {
              e.created_at = e.created_at
                ? moment(e.created_at).format("DD.MM.YYYY hh:mm")
                : null;
            });
          });
          let testPdf = "";
          this.documentFiles.forEach((v) => {
            if (v.object_type_id == 1) {
              // testPdf = "https://b-ides.uzavtosanoat.uz/document/file/" + v.id;
              testPdf =  this.$store.state.backend_url + "ides/showFile/" + v.id;
            }
          });
          this.loadingTask = pdf.createLoadingTask(testPdf);
          // this.loadingTask = pdf.createLoadingTask(
          //   "data:application/pdf;base64," +
          //     this.document.document_base64.base64
          // );
          // this.base64 = this.document.document_base64.base64;
          this.loadingTask.promise.then((pdf) => {
            this.numPages = pdf.numPages;
          });
          this.control_comment = this.document.document_signers.find(
              (v) => v.action_type_id == 11 &&  v.comments.length > 0
            ) ?  this.document.document_signers.find(
            (v) => v.action_type_id == 11 &&  v.comments.length > 0).comments[0] : '';
          this.documentsSigner = "";
          this.showButtons();
          this.getDocumentSigner();
          // console.log(this.docSigner);
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    downloadPdf() {
      let testPdf = "";
      let fileName = "";
      this.documentFiles.forEach((v) => {
        if (v.object_type_id == 1) {
          // testPdf = "https://b-ides.uzavtosanoat.uz/document/file/" + v.id;
          testPdf =  this.$store.state.backend_url + "ides/showFile/" + v.id;
          fileName = v.file_name;
          // this.$store.state.backend_url + "api/ides/getFile/" + v.id;
        }
      });
      const linkSource = testPdf;
      const downloadLink = document.createElement("a");
      // const fileName = this.document.document_number + ".pdf";

      downloadLink.href = linkSource;
      downloadLink.download = fileName;
      downloadLink.click();
      // window.open("data:application/pdf;base64," + this.base64);
    },
    zoomPdf(val) {
      if (val) {
        if (this.zoom > 95) this.zoom = 100;
        else this.zoom += 5;
      } else {
        if (this.zoom < 5) this.zoom = 0;
        else this.zoom -= 5;
      }
    },
    getComments() {
      this.document.document_signers.forEach((v) => {
        if (v.comments.filter((c) => c.parent_id == null).length > 0) {
          this.comments = this.comments.concat(
            v.comments.filter((c) => c.parent_id == null)
          );
        }
      });
    },

    showButtons() {
      // if (this.user.employee_id == 109) {
      //   org_id = 16;
      // } else if (this.user.employee_id == 10772) {
      //   org_id = 17;
      // } else {
      //   org_id = 3;
      // }

      this.enableAcceptButton = false;
      this.enableRejectButton = false;
      this.enableProcessingButton = false;
      this.enableCommentButton = false;
      if (
        ![0, 6, 5].includes(this.document.status) &&
        this.document.document_signers.filter(
          (v) =>
          (v.status == 0 || v.status == 3) &&
            v.organization_id == this.$store.state.ides_organization_id
            // v.organization_id == this.org_id
        ).length > 0
      ) {
        this.enableAcceptButton = true;
      }
      if (
        ![0, 6, 5].includes(this.document.status) &&
        this.document.document_signers.filter(
          (v) =>
          (v.status == 0 || v.status == 3) &&
            v.organization_id == this.$store.state.ides_organization_id
            // v.organization_id == this.org_id
        ).length > 0
      ) {
        this.enableRejectButton = true;
      }
      if (
        ![0, 6, 5].includes(this.document.status) &&
        this.document.document_signers.filter(
          (v) =>
            v.status == 0  &&
            v.action_type_id != 11 &&
            // (v.organization_id == this.org_id ||
            (v.organization_id ==this.$store.state.ides_organization_id ||
            ( v.user && v.user.username == this.user.ides_username))
        ).length > 0
      ) {
        this.enableProcessingButton = true;
      }
      if (
        ![0, 6, 5].includes(this.document.status) &&
        this.document.document_signers.length > 0
      ) {
        this.enableCommentButton = true;
      }
    },
    getDocumentSigner() {
      let signer_id = this.document.document_signers.filter(
        (v) => 
        // v.user_id && v.user && v.user.username == this.user.ides_username
        v.status == 0 &&
        v.organization_id == this.$store.state.ides_organization_id
      );
      this.docSigner = (signer_id && signer_id[0]) ? signer_id[0].id : '';
      
      // console.log(signer_id);
      // console.log(this.docSigner);
    },
    showReaction(status) {
      this.reactionStatus = status;
      this.reactionDialog = true;
      this.getPendingSigners();
    },
    getNewDocumentTemplate() {
      this.newDocumentDialog = true;
      axios
        .post(
          this.$store.state.backend_url + "api/document-templates/get-name",
          {
            document_type_id: "24",
            for_ides: 1
          }
        )
        .then((response) => {
          this.newDocumentTemplates = response.data;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    documentReaction(status) {
      if (this.$refs.signForm.validate()) {
        let formData = new FormData();
        this.selectFiles.forEach((v, i) => {
          formData.append("files[]", v);
        });
        formData.append("comment[]", "comm");

        this.loading = true;
        axios
          .post(this.$store.state.backend_url + "api/ides/reaction", {
            id: this.document.id,
            status: status,
            comment: this.reactionComment,
            // document_signer_id: this.docSigner,
            document_signer_id: this.pendingSigners.length > 1 ? this.document_signer_id : this.docSigner,
            organization_id: this.$store.state.ides_organization_id,
            // organization_id: this.org_id,

            // document_signer_id: this.document_signer_id,
          })
          .then((response) => {
            this.loading = false;
            this.reactionDialog = false;
            this.reactionComment = null;
            this.reactionStatus = null;
            axios.post(
              this.$store.state.backend_url +
                "api/ides/updatefiles/" +
                response.data,
              formData,
              {
                headers: {
                  "Content-Type": "multipart/form-data",
                },
              }
            );
            this.getDocument(this.document.id);
          })
          .catch((error) => {
            console.log(error);
          });
      }
    },
    viewPdfFile(item) {
      this.fileForView = item;
      this.pdfViewDialog = true;
    },
    getPendingSigners() {
      // console.log(this.user);
      if (
        ["publish"].includes(this.reactionStatus) &&
        this.document.status == 0
      ) {
        this.pendingSigners = this.document.document_signers
          .filter((v) => v.action_type_id == 6)
          .map((v, k) => {
            v.organization.name_uz_latin =
              v.organization.name_uz_latin + " " + (k + 1);
            v.organization.name_uz_cyril =
              v.organization.name_uz_cyril + " " + (k + 1);
            v.organization.name_uz_ru =
              v.organization.name_uz_ru + " " + (k + 1);
            return v;
          });
      } else if (
        ["accept", "reject", "processing"].includes(this.reactionStatus) &&
        ![0, 5, 6].includes(this.document.status)
      ) {
        this.pendingSigners = this.document.document_signers
          .filter(
            (v) =>
              v.status == 0 &&
              // v.user_id &&
              // v.user.username == this.user.ides_username
              v.organization_id == this.$store.state.ides_organization_id
              // v.organization_id == this.org_id
          )
          .map((v, k) => {
            v.description = v.description ? v.description : this.actionTypes.find((s)=>s.id == v.action_type_id)["name_" + this.$i18n.locale];
            // v.organization.name_uz_latin =
            //   v.organization.name_uz_latin + " " + (k + 1);
            // v.organization.name_uz_cyril =
            //   v.organization.name_uz_cyril + " " + (k + 1);
            // v.organization.name_uz_ru =
            //   v.organization.name_uz_ru + " " + (k + 1);
            return v;
          });
      } else if (
        ["comment"].includes(this.reactionStatus) &&
        ![0, 5, 6].includes(this.document.status)
      ) {
        if (this.document.creator.user.name == this.user.ides_username) {
          this.pendingSigners = this.document.document_signers
            .filter((v) => v.action_type_id != 6)
            .map((v, k) => {
              v.organization.name_uz_latin = v.organization.name_uz_latin;
              v.organization.name_uz_cyril = v.organization.name_uz_cyril;
              v.organization.name_uz_ru = v.organization.name_uz_ru;
              return v;
            });
        } else {
          this.pendingSigners = this.document.document_signers
            .filter(
              (v) =>
                v.action_type_id != 6 &&
                v.organization_id == this.$store.state.ides_organization_id
            )
            .map((v, k) => {
              v.description = v.description ? v.description : this.actionTypes.find((s)=>s.id == v.action_type_id)["name_" + this.$i18n.locale];
              // v.organization.name_uz_latin = v.organization.name_uz_latin;
              // v.organization.name_uz_cyril = v.organization.name_uz_cyril;
              // v.organization.name_uz_ru = v.organization.name_uz_ru;
              return v;
            });
        }
      } else this.pendingSigners = [];
    },
  },
  mounted() {
    // if (this.user.employee_id == 10772) {
    //     this.org_id = 17;
    //   } else {
    //     this.org_id = 16;
    //   }
    // this.getDocument(this.documentId);
    this.getDocument(this.$route.params.id);
    // console.log(this.enableIdesReceivedButton);
    // this.user = storage.get("user");
  },
};
</script>
<style scoped>
tr td {
  font-size: 90% !important;
}
tr td p {
}
</style>
