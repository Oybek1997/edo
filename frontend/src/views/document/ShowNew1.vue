<template>
  <div>
    <v-card class="ma-1 pa-1 mr-0" style="overflow: hidden">
      <v-card-title class="pa-0 pb-1" style="color: #000" color="black">
        <img
          src="img/imzo.png"
          height="25"
          contain
          style="cursor: pointer"
          @click="getFile()"
          v-if="document.base64"
        />
        <v-btn icon @click="fullScreenDialog = true">
          <v-icon>mdi-fullscreen</v-icon>
        </v-btn>
        <v-btn icon @click="downloadPdf">
          <v-icon>mdi-download</v-icon>
        </v-btn>
        <v-btn icon @click="downloadPdfWithComment" title="Download pdf with comments.">
          <v-icon>mdi-download-circle-outline</v-icon>
        </v-btn>
        <v-spacer></v-spacer>
        <v-btn v-if="numPages" icon @click="zoomPdf(0)" fab outlined x-small>
          <v-icon>mdi-minus</v-icon>
        </v-btn>
        <v-slider max="120" v-if="numPages" v-model="zoom" color="green" hide-details dense></v-slider>
        <v-btn v-if="numPages" icon @click="zoomPdf(1)" fab outlined x-small>
          <v-icon>mdi-plus</v-icon>
        </v-btn>
        <v-spacer></v-spacer>
        <v-btn
          v-if="$store.getters.checkPermission('add_control_punkt')"
          small
          outlined
          width="220px"
          color="success"
          class="ma-1"
          @click="
            modelControlPunkt = true;
            modelControlPunktTitle = $t('control_punkt.add');
          "
        >
          <span style="white-space: normal; width: 185px">
            {{
            $t("control_punkt.name")
            }}
          </span>
        </v-btn>
        <v-btn
          v-if="$store.getters.checkPermission('pomoshnik') && [25,214,357,71,305,333, 218].includes(document.document_template_id) && document.reaction_show && document.action_type_id != 11 && (document.status == 1 || document.status == 2)"
          small
          outlined
          width="220px"
          color="error"
          class="ma-1"
          @click="returnDocument"
        >
          <span style="white-space: normal; width: 185px">
            {{
            $t("mening_xujjatim_emas")
            }}
          </span>
        </v-btn>
        <v-btn
          v-if="
            user &&
            user.employee_id == document.created_employee_id &&
            document.status == 0 &&
            ($store.state.COMPANY_ID == 2 || $store.state.COMPANY_ID == 3)
          "
          small
          outlined
          width="220px"
          class="ma-1"
          color="success"
          @click="preAgreement()"
        >
          <span style="white-space: normal; width: 185px">{{ $t("document.pre_agreement") }}</span>
        </v-btn>
        <v-btn
          small
          outlined
          v-if="document.reaction_show && document.action_type_id != 11"
          width="150px"
          color="success"
          class="ma-1"
          @click="
            modalDocumentReaction = true;
            reaction = 1;
          "
        >
          <span style="white-space: normal; width: 180px">{{ $t("document.accept") }}</span>
        </v-btn>
        <v-btn
          small
          outlined
          v-if="document.pre_agreement"
          width="150px"
          color="light-green"
          class="ma-1"
          @click="modalDocumentPreAgreement = true"
        >
          <span style="white-space: normal; width: 180px">{{ $t("document.reaction") }}</span>
        </v-btn>
        <v-btn
          v-if="
            user && user.employee_id == document.created_employee_id &&
            (
              document.status == 0 ||
              (document.status == 7 && !document.document_signers.filter(v => v.status == 5).length)
            )
          "
          small
          outlined
          width="200px"
          color="success"
          class="ma-1"
          @click="modalDocumentReaction = true"
        >
          <span style="white-space: normal; width: 180px">{{ $t("publish") }}</span>
        </v-btn>
        <v-btn
          v-if="
            (
              user && user.employee_id == document.created_employee_id &&
              (
                document.status == 0 ||
                (
                  document.status == 7 && !document.document_signers.filter(v => v.status == 5).length
                ) || 
                $store.getters.checkPermission('okd_kanselyariya') && document.document_template_id != 432 && (document.status == 1 || document.status == 2)
              )
            ) 
            || (document.action_type_id == 13 && document.reaction_show)
            || user.username == 'qg9592'
          "
          small
          outlined
          width="200px"
          color="info"
          class="ma-1"
          @click="$router.push('/document/update/' + document.id)"
        >
          <span style="white-space: normal; width: 180px">{{ $t("edit") }}</span>
        </v-btn>
        <v-btn
          v-if="
            document.reaction_status != 3 &&
            document.reaction_show &&
            document.action_type_id != 11
          "
          small
          outlined
          width="150px"
          color="primary"
          class="ma-1"
          @click="processing()"
        >
          <span style="white-space: normal; width: 180px">
            {{
            $t("document.processing")
            }}
          </span>
        </v-btn>
        <v-btn
          v-if="
            (documentSigners &&
              documentSigners.find((v) => {
                if (v.signer_employee_id == user.employee_id) return true;
              })) ||
            document.reaction_show
          "
          small
          outlined
          width="150px"
          color="primary"
          class="ma-1"
          @click="modalDocumentComment = true"
        >
          <span style="white-space: normal; width: 180px">{{ $t("document.comment") }}</span>
        </v-btn>
        <v-btn
          v-if="
            ($store.getters.checkPermission('out_of_control') || document.action_type_id == 11) && document.status == 3
          "
          small
          outlined
          width="200px"
          color="info"
          class="ma-1"
          @click="outOfControl()"
        >
          <span style="white-space: normal; width: 180px">
            {{
            $t("document.out_of_control")
            }}
          </span>
        </v-btn>
        <v-btn
          v-else-if="
            control_punkt_id &&
            documentSigners &&
            !documentSigners.filter((v) => {
              if (
                v.control_punkt_id == control_punkt_id &&
                !(v.status == 1 || v.status == 2) &&
                v.action_type_id == 4
              )
                return v;
            }).length
          "
          small
          outlined
          width="200px"
          color="info"
          class="ma-1"
          @click="
            modalDocumentReaction = true;
            reaction = 1;
          "
        >
          <span style="white-space: normal; width: 180px">
            {{
            $t("document.out_of_control")
            }}
          </span>
        </v-btn>
        <v-btn
          v-if="document.confirmation_show"
          small
          outlined
          width="200px"
          color="info"
          class="ma-1"
          @click="confirmation(1)"
        >
          <span style="white-space: normal; width: 180px">{{ $t("document.confirmation") }}</span>
        </v-btn>
        <v-btn
          v-if="
            document.reaction_status != 4 &&
            document.reaction_show &&
            document.action_type_id != 11 &&
            (![3,4,5].includes(document.status) || [11705, 8928, 8916,11888].includes(user.employee_id) || document.action_type_id == 8 || ![70, 71, 75, 305, 214, 357, 136, 139, 155, 172].includes(document.document_template_id))
          "
          small
          outlined
          width="150px"
          color="warning"
          class="ma-1"
          @click="
            modalDocumentSubstantiate = true
          "
        >
          <span style="white-space: normal; width: 180px">{{ $t("substantiate") }}</span>
        </v-btn>
        <v-btn
          v-if="
            !(document.resolution && document.resolution.action_type_id == 5) &&
            document.reaction_show &&
            document.action_type_id != 11 && 
            (![3,4,5].includes(document.status) || [11705, 8928, 8916,11888].includes(user.employee_id) || document.action_type_id == 8 || ![70, 71, 75, 305, 214, 357, 136, 139, 155, 172, 373].includes(document.document_template_id))
          "
          small
          color="error"
          outlined
          class="ma-1"
          width="150px"
          @click="
            modalDocumentReaction = true;
            reaction = 0;
          "
        >
          <span style="white-space: normal">{{ $t("document.reject") }}</span>
        </v-btn>
        <v-btn
          v-if="document.confirmation_show"
          small
          outlined
          width="200px"
          color="error"
          class="ma-1"
          @click="
            modalDocumentConfirmation = true;
            reaction = 0;
            documentComment = '';
          "
        >
          <span style="white-space: normal; width: 180px">{{ $t("document.return_to") }}</span>
        </v-btn>
        <v-btn
          v-if="$store.getters.checkPermission('okd_kanselyariya')"
          icon
          @click="dialogEditDocumentTitle = true"
          color="success"
          :title="$t('document.edit_title')"
        >
          <v-icon>mdi-pencil</v-icon>
        </v-btn>
        <v-btn
          v-if="$store.state.COMPANY_ID != 3"
          icon
          :to="'/documents/show-only-pdf/' + document.pdf_file_name"
          color="blue"
          :title="$t('old_version')"
        >
          <v-icon>mdi-file</v-icon>
        </v-btn>
        <v-btn
          v-if="document.status == 6"
          icon
          @click="removeCancelledDocument()"
          color="green"
          :title="$t('kordim')"
        >
          <v-icon color="red">mdi-close</v-icon>
        </v-btn>
        <v-btn icon @click="getList(true)" color="green" :title="$t('refresh_pdf')">
          <v-icon>mdi-refresh</v-icon>
        </v-btn>
        <v-btn icon @click="star()" color="green" :title="$t('Remember')">
          <v-icon v-if="isStar == 1">mdi-star</v-icon>
          <v-icon v-else>mdi-star-outline</v-icon>
        </v-btn>
        <v-btn icon @click="stamp()" color="green" :title="$t('Stamp')" v-if="document && !document.stamped && $store.getters.checkPermission('shtamp_pdf') && [305,333,357].includes(document.document_template_id)">
          <v-icon>mdi-stamper</v-icon>
        </v-btn>
        <v-btn
          v-if="$store.getters.checkPermission('edit_signers')"
          icon
          @click="editSigners"
          color="success"
          :title="$t('document.edit_title')"
        >
          <v-icon>mdi-account-edit-outline</v-icon>
        </v-btn>
        <v-btn class icon @click="setCookie()">
          <v-icon>mdi-menu</v-icon>
        </v-btn>
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
              <v-btn color="red" outlined x-small fab class @click="fullScreenDialog = false">
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
      </v-card-title>
      <v-divider></v-divider>
      <v-card-text class="pa-1" style="color: #000">
        <v-row class="justify-center px-2" :height="screenHeight">
          <v-col
            cols="12"
            :lg="rightMenu ? '9' : '12'"
            class="pa-0 scrollbar"
            :style="'height:' + mainHeight + 'px; overflow:auto;'"
          >
            <div :style="'margin-left:auto;margin-right:auto;width:' + (zoom + 40) + '%;'">
              <pdf
                ref="mypdf"
                v-for="i in numPages"
                :key="i"
                :src="loadingTask"
                :page="i"
                style="margin-bottom: 20px; border: 5px double #555"
              ></pdf>
            </div>
            <v-row class="ma-0 pa-0">
              <v-col cols="12">
                <!-- HUJJAT TARIXI-->
                <v-card outlined class="ma-1">
                  <v-system-bar
                    class="pa-1 justify-center"
                    style="background-color: #163e72; color: #fff"
                    elevation="3"
                  >
                    <v-spacer></v-spacer>
                    {{ $t("document.history") }}
                    <v-spacer></v-spacer>
                    <v-btn icon small @click="active_history = !active_history">
                      <v-icon class="ma-0" color="white" v-if="active_history">mdi-menu-down</v-icon>
                      <v-icon class="ma-0" color="white" v-else>mdi-menu-up</v-icon>
                    </v-btn>
                  </v-system-bar>
                  <v-card-text class="pa-0 pb-1" v-if="active_history">
                    <v-treeview open-all :items="history" :open="tree_ids" item-key="id">
                      <template v-slot:label="{ item }">
                        <div class="historyList">
                          <div class="historyAvatar">
                            <div
                              class="no-img"
                              v-if="item.signer_employee_id"
                            >{{ item.fio && item.fio.charAt(0) }}</div>
                          </div>
                          <div class="historyContent lighten-4">
                            <v-row>
                              <v-col cols="12" class="py-0">
                                <template v-if="item.signer_employee_id">
                                  <div>
                                    <strong>{{ item.fio }}:</strong>
                                    <div v-for="(comment, i) in item.comments" :key="i">
                                      <v-alert
                                        v-if="
                                        comment.status == 1 ||
                                        comment.status == 10 ||
                                        comment.status == 11
                                      "
                                        text
                                        dense
                                        border="left"
                                        type="success"
                                        class="mb-1 pa-1 pl-2"
                                      >
                                        {{ comment.comment }}
                                        <v-chip
                                          class="pa-1"
                                          label
                                          small
                                          color="green lighten-5"
                                          text-color="black"
                                        >{{ comment.signed_at }}</v-chip>
                                      </v-alert>
                                      <v-alert
                                        v-else-if="comment.status == 0"
                                        text
                                        dense
                                        border="left"
                                        type="success"
                                        class="mb-1 pa-1 pl-2"
                                      >
                                        {{ $t("create") }}
                                        <v-chip
                                          class="pa-1"
                                          label
                                          small
                                          color="green lighten-5"
                                          text-color="black"
                                        >{{ comment.signed_at }}</v-chip>
                                      </v-alert>
                                      <v-alert
                                        v-else-if="
                                        comment.status == 2 || comment.status == 21
                                      "
                                        text
                                        dense
                                        border="left"
                                        type="error"
                                        class="mb-1 pa-1 pl-2"
                                        icon="mdi-close-thick"
                                      >
                                        {{ comment.comment }}
                                        <v-chip
                                          class="pa-1"
                                          label
                                          small
                                          color="red lighten-5"
                                          text-color="black"
                                        >{{ comment.signed_at }}</v-chip>
                                      </v-alert>
                                      <v-alert
                                        v-else-if="comment.status == 3"
                                        text
                                        dense
                                        border="left"
                                        color="primary"
                                        class="mb-1 pa-1 pl-2"
                                        icon="mdi-timer-sand"
                                      >
                                        {{ $t("document.processing") }}
                                        <v-chip
                                          class="pa-1"
                                          label
                                          small
                                          color="blue lighten-5"
                                          text-color="black"
                                        >{{ comment.signed_at }}</v-chip>
                                      </v-alert>
                                      <v-alert
                                        v-else-if="comment.status == 4"
                                        text
                                        dense
                                        border="left"
                                        type="warning"
                                        class="mb-1 pa-1 pl-2"
                                        style="font-size: 13px"
                                      >
                                        {{ comment.comment }}
                                        <v-chip
                                          class="pa-1"
                                          label
                                          small
                                          color="orange lighten-5"
                                          text-color="black"
                                        >{{ comment.signed_at }}</v-chip>
                                      </v-alert>
                                      <v-alert
                                        v-else-if="comment.status == 5"
                                        text
                                        dense
                                        border="left"
                                        color="blue-grey"
                                        class="mb-1 pa-1 pl-2"
                                        icon="mdi-comment-outline"
                                        style="font-size: 13px"
                                      >
                                        {{ comment.comment }}
                                        <v-chip
                                          class="pa-1"
                                          label
                                          small
                                          color="blue-grey lighten-5"
                                          text-color="black"
                                        >{{ comment.signed_at }}</v-chip>
                                      </v-alert>
                                      <v-alert
                                        v-else-if="
                                        comment.status == 6 || comment.status == 7
                                      "
                                        text
                                        dense
                                        border="left"
                                        color="blue-grey"
                                        class="mb-1 pa-1 pl-2"
                                        icon="mdi-comment-plus-outline"
                                        style="font-size: 13px"
                                      >
                                        {{ comment.comment }}
                                        <v-chip
                                          class="pa-1"
                                          label
                                          small
                                          color="blue-grey lighten-5"
                                          text-color="black"
                                        >{{ comment.signed_at }}</v-chip>
                                      </v-alert>
                                      <v-alert
                                        v-else-if="comment.status == 8"
                                        text
                                        dense
                                        border="left"
                                        color="blue"
                                        class="mb-1 pa-1 pl-2"
                                        icon="mdi-pencil"
                                        style="font-size: 13px"
                                      >
                                        {{ $t("changed") }}
                                        <v-chip
                                          class="pa-1"
                                          label
                                          small
                                          color="blue lighten-5"
                                          text-color="black"
                                        >{{ comment.signed_at }}</v-chip>
                                      </v-alert>
                                      <v-alert
                                        v-else-if="comment.status == 9"
                                        text
                                        dense
                                        border="left"
                                        type="success"
                                        icon="mdi-publish"
                                        class="mb-1 pa-1 pl-2"
                                      >
                                        {{ $t(comment.comment) }}
                                        <v-chip
                                          class="pa-1"
                                          label
                                          small
                                          color="green lighten-5"
                                          text-color="black"
                                        >{{ comment.signed_at }}</v-chip>
                                      </v-alert>
                                      <v-alert
                                        v-else-if="
                                        comment.status == 12 || comment.status == 14
                                      "
                                        text
                                        dense
                                        border="left"
                                        color="blue-grey"
                                        class="mb-1 pa-1 pl-2"
                                        icon="mdi-comment-arrow-left-outline"
                                        style="font-size: 13px"
                                      >
                                        {{ comment.comment }}
                                        <v-chip
                                          class="pa-1"
                                          label
                                          small
                                          color="blue-grey lighten-5"
                                          text-color="black"
                                        >{{ comment.signed_at }}</v-chip>
                                      </v-alert>
                                      <v-alert
                                        v-else-if="comment.status == 13"
                                        text
                                        dense
                                        border="left"
                                        type="success"
                                        class="mb-1 pa-1 pl-2"
                                      >
                                        {{ comment.comment }}
                                        <v-chip
                                          class="pa-1"
                                          label
                                          small
                                          color="green lighten-5"
                                          text-color="black"
                                        >{{ comment.signed_at }}</v-chip>
                                      </v-alert>
                                      <v-alert
                                        v-else-if="!comment.status"
                                        text
                                        dense
                                        border="left"
                                        type="success"
                                        class="mb-1 pa-1 pl-2"
                                        style="font-size: 13px"
                                      >
                                        {{ comment.comment }}
                                        <v-chip
                                          class="pa-1"
                                          label
                                          small
                                          color="green lighten-5"
                                          text-color="black"
                                        >{{ comment.signed_at }}</v-chip>
                                      </v-alert>
                                      <v-chip
                                        v-for="(file, index) in comment.files"
                                        :key="index"
                                        class="pa-1 mt-n1 mb-1"
                                        small
                                        color="white"
                                        outlined
                                        
                                        text-color="black"
                                      >
                                        <v-icon small>mdi-paperclip</v-icon>
                                        <span @click="viewPdfFile(file)">{{ file.file_name }}</span>
                                        <v-btn
                                            text
                                            x-small
                                            class="ml-2"
                                            :href="
                                              $store.state.backend_url +
                                              'staffs/file-download/' +
                                              file.id
                                            "
                                          >
                                            <v-icon color="green">mdi-download</v-icon>
                                          </v-btn>
                                      </v-chip>
                                    </div>
                                  </div>
                                </template>
                              </v-col>
                            </v-row>
                          </div>
                        </div>
                      </template>
                    </v-treeview>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
          </v-col>
          <v-col cols="12" lg="3" v-if="rightMenu" class="pa-0">
            <v-card
              class="ml-1 scrollbar"
              :height="screenHeight - 50"
              elevation="0"
              style="overflow: hidden; overflow-y: auto"
            >
              <v-card outlined class="mb-2">
                <v-system-bar class="pa-1 white">
                  <strong>{{ $t("document.status") }}:</strong>
                  <v-spacer></v-spacer>
                  <v-btn
                    x-small
                    depressed
                    rounded
                    dark
                    :color="
                      document_status[document.status ? document.status : 0]['color']
                    "
                    class="px-1"
                  >
                    {{
                    document &&
                    document_status[document.status ? document.status : 0][
                    "name_" + $i18n.locale
                    ]
                    }}
                  </v-btn>
                </v-system-bar>
                <v-divider></v-divider>
                <v-system-bar class="pa-1 white">
                  <strong>{{ $t("document.creator") }}:</strong>
                  <v-spacer></v-spacer>
                  {{
                  documentSigners.find((v) => {
                  if (v.action_type_id == 6) return v;
                  }) &&
                  documentSigners.find((v) => {
                  if (v.action_type_id == 6) return v;
                  }).fio
                  }}
                </v-system-bar>
                <v-divider></v-divider>
                <v-row class="ma-0 px-1">
                  <v-col class="ma-0 pa-0">
                    <strong>{{ $t("document.responsible_contact") }}:</strong>
                  </v-col>
                  <v-col class="ma-0 pa-0">
                    {{
                    document.responsible_contact
                    }}
                  </v-col>
                </v-row>
                <v-divider></v-divider>
                <v-row
                  class="ma-0 px-1"
                  v-if="document && document.document_details && document.document_details[0].document_detail_contents && document.document_details[0].document_detail_contents[7] && document.document_details[0].document_detail_contents[7].d_d_attribute_id == 1171"
                >
                  <v-col class="ma-0 pa-0">
                    <strong>{{ $t("document.send_method") }}:</strong>
                  </v-col>
                  <v-col class="ma-0 pa-0">
                    {{
                    document.document_details[0].document_detail_contents[7].value
                    }}
                  </v-col>
                </v-row>
                <v-row
                  class="ma-0 px-1"
                  v-if="document && document.document_details && document.document_details[0].document_detail_contents && document.document_details[0].document_detail_contents[10] && document.document_details[0].document_detail_contents[10].d_d_attribute_id == 1170"
                >
                  <v-col class="ma-0 pa-0">
                    <strong>{{ $t("document.send_method") }}:</strong>
                  </v-col>
                  <v-col class="ma-0 pa-0">
                    {{
                    document.document_details[0].document_detail_contents[10].value
                    }}
                  </v-col>
                </v-row>
                <v-row
                  class="ma-0 px-1"
                  v-if="document && document.document_details && document.document_details[0].document_detail_contents && document.document_details[0].document_detail_contents[10] && document.document_details[0].document_detail_contents[10].d_d_attribute_id == 1172"
                >
                  <v-col class="ma-0 pa-0">
                    <strong>{{ $t("document.send_method") }}:</strong>
                  </v-col>
                  <v-col class="ma-0 pa-0">
                    {{
                    document.document_details[0].document_detail_contents[10].value
                    }}
                  </v-col>
                </v-row>
                <v-divider></v-divider>
                <v-system-bar v-if="document.old_document_id" class="pa-1 white">
                  <strong>{{ $t("document.previous_version") }}:</strong>
                  <v-spacer></v-spacer>
                  <router-link
                    :to="'/document/' + document.previous_version.pdf_file_name"
                  >{{ document.previous_version.document_number }}</router-link>
                </v-system-bar>
                <v-divider></v-divider>
                <v-system-bar class="pa-1 white">
                  <strong>ID:</strong>
                  <v-spacer></v-spacer>
                  {{ document.id }}
                </v-system-bar>
                <v-divider
                  v-if="
                    $store.getters.checkPermission('edit_document_reg_data') &&
                    (document.status != 0 || document.status != 6) 
                  "
                ></v-divider>
                <v-system-bar
                  v-if="
                    $store.getters.checkPermission('edit_document_reg_data') &&
                    (document.status != 0 || document.status != 6) 
                  "
                  class="pa-1 white"
                >
                  <strong>Document number:</strong>
                  <v-spacer></v-spacer>
                  {{ document.document_number }}
                </v-system-bar>
                <v-system-bar
                  v-if="
                    $store.getters.checkPermission('edit_document_reg_data') &&
                    (document.status != 0 || document.status != 6) &&
                    document.document_date
                  "
                  class="pa-1 white"
                >
                  <strong>Document date:</strong>
                  <v-spacer></v-spacer>
                  {{ document.document_date.substring(0, 10) }}
                </v-system-bar>
                <v-divider
                  v-if="
                    $store.getters.checkPermission('edit_document_reg_data') &&
                    (document.status != 0 || document.status != 6)
                  "
                ></v-divider>
                <v-app-bar
                  v-if="
                    $store.getters.checkPermission('edit_document_reg_data') &&
                    (document.status != 0 || document.status != 6) 
                  "
                  class="pa-1 white"
                >
                  <strong>Reg number:</strong>
                  <v-spacer></v-spacer>
                  <v-text-field v-model="reg_number" outlined dense hide-details></v-text-field>
                </v-app-bar>
                <v-app-bar
                  v-if="
                    $store.getters.checkPermission('edit_document_reg_data') &&
                    (document.status != 0 || document.status != 6) 
                  "
                  class="pa-1 white"
                >
                  <strong>Reg date:</strong>
                  <v-spacer></v-spacer>
                  <v-text-field v-model="reg_date" outlined dense hide-details></v-text-field>
                </v-app-bar>
                <v-app-bar
                  v-if="
                    $store.getters.checkPermission('edit_document_reg_data') &&
                    (document.status != 0 || document.status != 6) 
                  "
                >
                  <v-spacer
                    v-if="
                      $store.getters.checkPermission('edit_document_reg_data') &&
                      (document.status != 0 || document.status != 6) 
                    "
                  ></v-spacer>
                  <v-btn
                    v-if="
                      $store.getters.checkPermission('edit_document_reg_data') &&
                      (document.status != 0 || document.status != 6) 
                    "
                    @click="saveRegData()"
                    color="success"
                    outlined
                    small
                  >
                    <v-icon>mdi-content-save</v-icon>
                  </v-btn>
                </v-app-bar>
              </v-card>
              <!-- topshiriqlar -->
              <v-card
                outlined
                class="mb-2"
                v-if="
                  document_signers.some(
                    (ds) =>
                      user.employee.employee_staff.find(
                        (v) => v.staff_id == ds.staff_id
                      ) && ds.control_punkt_id !== null
                  )
                "
              >
                <v-system-bar
                  class="pa-1"
                  style="background-color: #163e72; color: #fff"
                  elevation="3"
                  small
                >
                  {{ $t("control_punkt.name") }}
                  <v-spacer></v-spacer>
                  {{ $t("document.assignment") }}
                </v-system-bar>
                <v-card-text class="pa-1">
                  <template v-for="(control_punkt, cp_index) in control_punkts">
                    <v-card
                      outlined
                      tile
                      class="mb-1"
                      :key="cp_index"
                      v-if="
                        document_signers.find(
                          (ds) =>
                            user.employee.employee_staff.find(
                              (v) => v.staff_id == ds.staff_id
                            ) && ds.control_punkt_id == control_punkt.id
                        )
                      "
                    >
                      <v-system-bar
                        color="grey lighten-4"
                      >{{ $t("control_punkt.name") + " " + (cp_index + 1) }}</v-system-bar>
                      <v-card-text class="pa-1">{{ control_punkt.content }}</v-card-text>
                    </v-card>
                  </template>
                </v-card-text>
              </v-card>
              <v-card outlined class="mb-2" v-if="document.resolution">
                <v-system-bar
                  class="pa-1"
                  style="background-color: #163e72; color: #fff"
                  elevation="3"
                  small
                >
                  <strong
                    style="
                      white-space: nowrap;
                      width: 60%;
                      text-overflow: ellipsis;
                      overflow: hidden;
                    "
                    class
                  >
                    {{
                    document.resolution.parent_employee["lastname_" + locale] +
                    " " +
                    document.resolution.parent_employee["firstname_" + locale] +
                    " " +
                    document.resolution.parent_employee["middlename_" + locale]
                    }}:
                  </strong>
                  <v-spacer></v-spacer>
                  {{ $t("document.assignment") }}
                </v-system-bar>
                <v-card-text class="pa-1">
                  <div>{{ document.resolution.assignment }}</div>
                  <div>
                    <strong>{{ $t("document.due_date") }}:</strong>
                    {{ document.resolution.due_date }}
                  </div>
                </v-card-text>
              </v-card>

              <!-- edit attributes -->
              <v-card outlined class="mb-2" v-if="edit_attributes.length">
                <v-system-bar
                  class="pa-1 justify-center"
                  style="background-color: #163e72; color: #fff"
                  elevation="3"
                  small
                >
                  <v-spacer></v-spacer>
                  {{ $t("document.edit_attribute") }}
                  <v-spacer></v-spacer>
                  <v-btn icon small @click="active_edit_attribute = !active_edit_attribute">
                    <v-icon class="ma-0" color="white" v-if="active_edit_attribute">mdi-menu-down</v-icon>
                    <v-icon class="ma-0" color="white" v-else>mdi-menu-up</v-icon>
                  </v-btn>
                </v-system-bar>
                <v-card-text class="pa-1" v-if="active_edit_attribute">
                  <template v-for="(edit_attribute, index) in edit_attributes">
                    <v-card outlined :key="index">
                      <v-system-bar color="grey lighten-4">
                        {{ index + 1 }}.
                        <span
                          v-if="edit_attribute.document_detail_employees.length"
                        >
                          <router-link
                            :to="'/personcontrol/profile/' + edit_attribute.document_detail_employees[0].employee_id"
                            style="text-decoration: none"
                          >
                            {{
                            edit_attribute.document_detail_employees[0]
                            .employee_fio
                            }}
                          </router-link>
                        </span>
                        <span v-if="edit_attribute.document_detail_employees.length > 1">...</span>
                      </v-system-bar>
                      <v-card-text class="pa-1">
                        <template
                          v-for="(
                            document_detail_signer_attribute, att_index
                          ) in edit_attribute.document_detail_signer_attributes"
                        >
                          <v-row :key="att_index" class="ma-0 align-center">
                            <v-col cols="5" class="pa-0 pr-2 text-right">
                              <strong>
                                {{
                                document_detail_signer_attribute
                                .document_detail_attributes[
                                "attribute_name_" + $i18n.locale
                                ]
                                }}:
                              </strong>
                            </v-col>
                            <v-col cols="7" class="pa-0">
                              <v-autocomplete
                                v-if="document_detail_signer_attribute.document_detail_attributes.data_type_id == 6"
                                clearable
                                :readonly="
                                  !(
                                    edit_attributes.some((e) =>
                                      e.document_detail_signer_attributes.some((a) =>
                                        user.employee.employee_staff.find((v) =>
                                          a.attribute_signer_staff.find(
                                            (va) => v.staff_id == va.staff_id
                                          )
                                        )
                                      )
                                    ) && document.reaction_show
                                  )
                                "
                                :label="document_detail_signer_attribute.document_detail_attributes['attribute_name_' + $i18n.locale]"
                                v-model="document_detail_signer_attribute.value"
                                @keyup="
                                  getTableList(att_index + '_' + 0, document_detail_signer_attribute.document_detail_attributes.table_list_id)
                                "
                                @click="
                                  getTableList(att_index + '_' + 0, document_detail_signer_attribute.document_detail_attributes.table_list_id)
                                "
                                :search-input.sync="searchTable[att_index + '_' + 0]"
                                :items="tableLists[att_index + '_' + 0]"
                                :rules="
                                  document_detail_signer_attribute.document_detail_attributes.required ? [(v) => !!v || $t('input.required')] : []
                                "
                                hide-details
                                dense
                                outlined
                                full-width
                                item-text="search"
                                item-value="id"
                                :loading="isLoading"
                              >
                                <template v-slot:item="{ item }">
                                  <v-list-item-content>
                                    <v-list-item-title v-text="item.search"></v-list-item-title>
                                  </v-list-item-content>
                                </template>
                              </v-autocomplete>
                              <v-textarea
                                v-else
                                v-model="document_detail_signer_attribute.value"
                                auto-grow
                                id="id"
                                dense
                                hide-details
                                :readonly="
                                  !(
                                    edit_attributes.some((e) =>
                                      e.document_detail_signer_attributes.some((a) =>
                                        user.employee.employee_staff.find((v) =>
                                          a.attribute_signer_staff.find(
                                            (va) => v.staff_id == va.staff_id
                                          )
                                        )
                                      )
                                    ) && document.reaction_show
                                  )
                                "
                                rows="1"
                              ></v-textarea>
                            </v-col>
                          </v-row>
                        </template>
                      </v-card-text>
                    </v-card>
                  </template>
                </v-card-text>
                <v-card-actions class="pa-1 pt-0">
                  <v-spacer></v-spacer>
                  <v-btn
                    v-if="
                      edit_attributes.some((e) =>
                        e.document_detail_signer_attributes.some((a) =>
                          user.employee.employee_staff.find((v) =>
                            a.attribute_signer_staff.find(
                              (va) => v.staff_id == va.staff_id
                            )
                          )
                        )
                      ) && document.reaction_show
                    "
                    color="success"
                    x-small
                    outlined
                    @click="saveEditAttribute()"
                  >{{ $t("save") }}</v-btn>
                </v-card-actions>
              </v-card>

              <!-- rezalutsiya berilgan hodimlar -->
              <v-card
                outlined
                class="mb-2"
                v-if="
                  document.resolution_show ||
                  document.action_type_id == 5 ||
                  document.action_type_id == 11 ||
                  $store.getters.checkPermission('out_of_control')
                "
              >
                <v-system-bar
                  class="pa-1 justify-center"
                  style="background-color: #163e72; color: #fff"
                  elevation="3"
                >
                  <v-spacer></v-spacer>
                  {{ $t("document.resolution") }}
                  <v-spacer></v-spacer>
                  <v-btn icon small @click="active_resolution = !active_resolution">
                    <v-icon class="ma-0" color="white" v-if="active_resolution">mdi-menu-down</v-icon>
                    <v-icon class="ma-0" color="white" v-else>mdi-menu-up</v-icon>
                  </v-btn>
                </v-system-bar>
                <v-card-text class="pa-1" v-if="active_resolution">
                  <template v-for="resolutionEmployee in resolutionEmployees">
                    <v-card outlined :key="resolutionEmployee.index" class="mb-2">
                      <v-system-bar
                        class="pa-1"
                        :class="
                          resolutionEmployee.status == 1
                            ? 'success lighten-4'
                            : resolutionEmployee.status == 2
                            ? 'error lighten-4'
                            : resolutionEmployee.status == 3
                            ? 'primary lighten-4'
                            : 'grey lighten-4'
                        "
                        style="color: #000"
                        dark
                        elevation="3"
                      >
                        {{
                        resolutionEmployee.fio
                        ? resolutionEmployee.fio
                        : resolutionEmployee.staff.employees[0][
                        "firstname_" + language
                        ].substr(0, 1) +
                        "." +
                        resolutionEmployee.staff.employees[0][
                        "middlename_" + language
                        ].substr(0, 1) +
                        ". " +
                        resolutionEmployee.staff.employees[0][
                        "lastname_" + language
                        ]
                        }}
                        <v-spacer></v-spacer>
                        <v-chip class="mx-1" color="black" depressed outlined small rounded text>
                          <v-icon color="black" left>mdi-clock-outline</v-icon>
                          {{ momentTime(resolutionEmployee.due_date) }}
                        </v-chip>
                        <v-btn
                          v-if="
                            resolutionEmployee.status == 1 ||
                            resolutionEmployee.status == 2
                          "
                          @click="
                            modalToReturn = true;
                            returnEmployee = resolutionEmployee;
                          "
                          text
                          x-small
                        >
                          <v-icon color="black">mdi-undo-variant</v-icon>
                        </v-btn>
                        <v-btn
                          v-if="resolutionEmployee.status == 0"
                          @click="deleteResolutionEmployee(resolutionEmployee)"
                          fab
                          text
                          x-small
                        >
                          <v-icon color="red">mdi-delete</v-icon>
                        </v-btn>
                      </v-system-bar>
                      <v-card-text class="pa-1">
                        <p v-if="resolutionEmployee.assignment" class="my-1">
                          <strong>{{ $t("document.assignment") }}:</strong>
                          {{ resolutionEmployee.assignment }}
                        </p>
                        <v-divider v-if="resolutionEmployee.assignment"></v-divider>
                        <p
                          v-if="resolutionEmployee.description"
                          class="my-1"
                        >- {{ resolutionEmployee.description }}</p>
                      </v-card-text>
                    </v-card>
                  </template>
                  <v-card outlined class="mb-2" v-if="resolutionEmployees.length == 0">
                    <v-card-text class="pa-1 text-center">{{ $t("noDataText") }}</v-card-text>
                  </v-card>
                </v-card-text>
                <v-card-text
                  class="pa-1 mt-n3 text-right"
                  v-if="
                    reaction_status == 0 ||
                    reaction_status == 3 ||
                    reaction_status == 4 ||
                    document.action_type_id == 5 ||
                    document.action_type_id == 11 ||
                    $store.getters.checkPermission('out_of_control')
                  "
                >
                  <v-btn
                    x-small
                    outlined
                    @click="modelAddResolution = true"
                    color="success"
                  >{{ $t("add") }}</v-btn>
                </v-card-text>
              </v-card>

              <!-- bajaruvchilar ro'yxati nazotarchi uchun -->
              <v-card outlined v-if="this.watcher" class="mb-2">
                <v-system-bar
                  class="pa-1 justify-center"
                  style="background-color: #163e72; color: #fff"
                  elevation="3"
                >
                  <v-spacer></v-spacer>
                  {{ $t("document.doers") }}
                  <v-spacer></v-spacer>
                  <v-btn icon small @click="active_watcher = !active_watcher">
                    <v-icon class="ma-0" color="white" v-if="active_watcher">mdi-menu-down</v-icon>
                    <v-icon class="ma-0" color="white" v-else>mdi-menu-up</v-icon>
                  </v-btn>
                </v-system-bar>
                <v-card-text class="pa-1" v-if="active_watcher">
                  <template
                    v-for="documentDoer in documentSigners.filter((v) => {
                      if (control_punkt_id) {
                        if (
                          v.action_type_id == 4 &&
                          v.control_punkt_id == control_punkt_id
                        ) {
                          return v;
                        }
                      } else {
                        if (v.action_type_id == 4) {
                          return v;
                        }
                      }
                    })"
                  >
                    <v-card outlined :key="documentDoer.index" class="mb-2">
                      <v-system-bar
                        class="pa-1"
                        :class="
                          documentDoer.status == 1
                            ? 'success lighten-4'
                            : documentDoer.status == 2
                            ? 'error lighten-4'
                            : documentDoer.status == 3
                            ? 'primary lighten-4'
                            : 'grey lighten-4'
                        "
                        style="color: #000"
                        dark
                        elevation="3"
                      >
                        {{
                        documentDoer.fio
                        ? documentDoer.fio
                        : documentDoer.staff.employees[0][
                        "firstname_" + language
                        ].substr(0, 1) +
                        "." +
                        documentDoer.staff.employees[0][
                        "middlename_" + language
                        ].substr(0, 1) +
                        ". " +
                        documentDoer.staff.employees[0]["lastname_" + language]
                        }}
                        <v-spacer></v-spacer>
                        <v-chip class="mx-1" color="black" depressed outlined small rounded text>
                          <v-icon color="black" left>mdi-clock-outline</v-icon>
                          {{ documentDoer.due_date }}
                        </v-chip>

                        <v-spacer></v-spacer>
                        <v-btn
                          v-if="documentDoer.status == 1 || documentDoer.status == 2"
                          @click="
                            modalToReturn = true;
                            returnEmployee = documentDoer;
                          "
                          text
                          x-small
                        >
                          <v-icon color="black">mdi-undo-variant</v-icon>
                        </v-btn>
                        <v-btn
                          v-else
                          @click="
                            modalDocumentComment = true;
                            commentSigner = documentDoer;
                          "
                          fab
                          text
                          x-small
                        >
                          <v-icon color="black">mdi-comment-processing-outline</v-icon>
                        </v-btn>
                      </v-system-bar>
                      <v-card-text class="pa-1">
                        <p v-if="documentDoer.assignment" class="my-1">
                          <strong>{{ $t("document.assignment") }}:</strong>
                          {{ documentDoer.assignment }}
                        </p>
                        <v-divider v-if="documentDoer.assignment"></v-divider>
                        <p
                          v-if="documentDoer.description"
                          class="my-1"
                        >- {{ documentDoer.description }}</p>
                      </v-card-text>
                    </v-card>
                  </template>
                </v-card-text>
              </v-card>

              <!-- imzolashi kutilayotganlar -->
              <v-card
                outlined
                class="mb-2"
                v-if="
                  documentSigners &&
                  documentSigners.filter((v) => {
                    if (
                      v.taken_datetime &&
                      (v.status == 0 || v.status == 3 || v.status == 4) &&
                      v.is_done < 2
                    )
                      return v;
                  }).length
                "
              >
                <v-system-bar
                  class="pa-1 justify-center"
                  style="background-color: #163e72; color: #fff"
                  elevation="3"
                  small
                >
                  <v-spacer></v-spacer>
                  {{ $t("document.pending_action") }}
                  <v-spacer></v-spacer>
                  <v-btn icon small @click="active_pending_action = !active_pending_action">
                    <v-icon class="ma-0" color="white" v-if="active_pending_action">mdi-menu-down</v-icon>
                    <v-icon class="ma-0" color="white" v-else>mdi-menu-up</v-icon>
                  </v-btn>
                </v-system-bar>
                <v-card-text class="pa-1" v-if="active_pending_action">
                  <v-popover
                    v-for="(documentSigner, index) in documentSigners &&
                    documentSigners.filter((v) => {
                      if (
                        v.taken_datetime &&
                        (v.status == 0 || v.status == 3) &&
                        v.is_done < 2
                      )
                        return v;
                    })"
                    :key="index"
                  >
                    <v-chip
                      label
                      small
                      class="mr-1 mb-1 px-1"
                      :class="documentSigner.status == 3 ? 'primary' : 'white'"
                    >
                      {{
                      documentSigner.fio
                      ? documentSigner.fio
                      : documentSigner &&
                      documentSigner.staff &&
                      documentSigner.staff.employees.length > 0
                      ? documentSigner.staff.employees[0][
                      "firstname_" + language
                      ].substr(0, 1) +
                      "." +
                      documentSigner.staff.employees[0][
                      "middlename_" + language
                      ].substr(0, 1) +
                      ". " +
                      documentSigner.staff.employees[0]["lastname_" + language]
                      : ""
                      }}
                    </v-chip>

                    <template slot="popover">
                      <v-card style="padding: 3px; box-shadow: 0px 0px 20px">
                        <v-card outlined tile>
                          <v-system-bar color="grey lighten-2">
                            {{
                            documentSigner.fio
                            ? documentSigner.fio
                            : documentSigner &&
                            documentSigner.staff &&
                            documentSigner.staff.employees[0]
                            ? documentSigner.staff.employees[0][
                            "firstname_" + language
                            ].substr(0, 1) +
                            "." +
                            documentSigner.staff.employees[0][
                            "middlename_" + language
                            ].substr(0, 1) +
                            ". " +
                            documentSigner.staff.employees[0][
                            "lastname_" + language
                            ]
                            : ""
                            }}
                          </v-system-bar>
                          <v-card-text style="color: #000; padding: 5px">
                            {{ documentSigner.department }}
                            <br />
                            {{ documentSigner.position }}
                          </v-card-text>
                        </v-card>
                        <v-card outlined tile>
                          <v-card-text style="color: #000; padding: 5px">
                            <v-chip class="mx-1" depressed outlined small rounded text>
                              <v-icon left>mdi-arrow-down-bold</v-icon>
                              {{ documentSigner.taken_at }}
                            </v-chip>
                            <v-chip
                              v-if="documentSigner.status == 1"
                              color="success"
                              class="mx-1"
                              depressed
                              outlined
                              small
                              rounded
                              text
                            >
                              <v-icon left>mdi-check-bold</v-icon>
                              {{ documentSigner.signed_at }}
                            </v-chip>
                            <v-chip
                              v-else-if="documentSigner.status == 2"
                              color="error"
                              class="mx-1"
                              depressed
                              outlined
                              small
                              rounded
                              text
                            >
                              <v-icon left>mdi-close-thick</v-icon>
                              {{ documentSigner.signed_at }}
                            </v-chip>
                            <v-chip v-else class="mx-1" depressed outlined small rounded text>
                              <v-icon left>mdi-clock-outline</v-icon>
                              {{ documentSigner.due_at }}
                            </v-chip>
                          </v-card-text>
                        </v-card>
                      </v-card>
                    </template>
                  </v-popover>
                </v-card-text>
              </v-card>

              <!-- biriktirilgan hujjatlar -->
              <v-card
                v-if="document.document_relation && document.document_relation.length"
                class="mb-2"
                outlined
              >
                <v-system-bar
                  class="pa-1 justify-center"
                  style="background-color: #163e72; color: #fff"
                  elevation="3"
                  small
                >
                  <v-spacer></v-spacer>
                  {{ $t("document.add_parent_document") }}
                  <v-spacer></v-spacer>
                  <v-btn icon small @click="active_parent_document = !active_parent_document">
                    <v-icon class="ma-0" color="white" v-if="active_parent_document">mdi-menu-down</v-icon>
                    <v-icon class="ma-0" color="white" v-else>mdi-menu-up</v-icon>
                  </v-btn>
                </v-system-bar>
                <v-card-text class="pa-1" v-if="active_parent_document">
                  <v-simple-table dense class style="border: 1px solid #aaa">
                    <template v-slot:default>
                      <thead>
                        <tr>
                          <th class="text-left">#</th>
                          <th class="text-left">{{ $t("document.document_number") }}</th>
                          <th class="text-left">{{ $t("document.document_name") }}</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(item, index) in document.document_relation" :key="index">
                          <td>{{ index + 1 }}</td>
                          <td>
                            <router-link
                              v-if="$store.getters.checkPermission('view_document_with_link')"
                              :to="'/documents/show-only-pdf/' + item.pdf_file_name"
                              target="_blank"
                            >{{ item.document_number }}</router-link>
                            <span v-else>{{ item.document_number }}</span>
                          </td>
                          <td>{{ item.document_template }}</td>
                        </tr>
                      </tbody>
                    </template>
                  </v-simple-table>
                </v-card-text>
              </v-card>

              <!-- biriktirilgan quyi hujjatlar -->
              <v-card
                v-if="document.document_children && document.document_children.length"
                class="mb-2"
                outlined
              >
                <v-system-bar
                  class="pa-1 justify-center"
                  style="background-color: #163e72; color: #fff"
                  elevation="3"
                  small
                >
                  <v-spacer></v-spacer>
                  {{ $t("document.children_document")
                  }}
                  <v-spacer></v-spacer>
                  <v-btn icon small @click="active_child_document = !active_child_document">
                    <v-icon class="ma-0" color="white" v-if="active_child_document">mdi-menu-down</v-icon>
                    <v-icon class="ma-0" color="white" v-else>mdi-menu-up</v-icon>
                  </v-btn>
                </v-system-bar>
                <v-card-text class="pa-1" v-if="active_child_document">
                  <v-simple-table dense class style="border: 1px solid #aaa">
                    <template v-slot:default>
                      <thead>
                        <tr>
                          <th class="text-left">#</th>
                          <th class="text-left">{{ $t("document.document_number") }}</th>
                          <th class="text-left">{{ $t("document.document_name") }}</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(item, index) in document.document_children" :key="index">
                          <td>{{ index + 1 }}</td>
                          <td>
                            <router-link
                              v-if="$store.getters.checkPermission('view_document_with_link')"
                              :to="'/documents/show-only-pdf/' + item.pdf_file_name"
                              target="_blank"
                            >{{ item.document_number }}</router-link>
                            <span v-else>{{ item.document_number }}</span>
                          </td>
                          <td>{{ item.document_template }}</td>
                        </tr>
                      </tbody>
                    </template>
                  </v-simple-table>
                </v-card-text>
              </v-card>

              <!-- biriktirilgan fayllar -->
              <v-card v-if="documentFiles && documentFiles.length" class="mb-2" outlined>
                <v-system-bar
                  class="pa-1 justify-center"
                  style="background-color: #163e72; color: #fff"
                  elevation="3"
                >
                  <v-spacer></v-spacer>
                  {{ $t("files") }}
                  <v-spacer></v-spacer>
                  <v-btn icon small @click="active_files = !active_files">
                    <v-icon class="ma-0" color="white" v-if="active_files">mdi-menu-down</v-icon>
                    <v-icon class="ma-0" color="white" v-else>mdi-menu-up</v-icon>
                  </v-btn>
                </v-system-bar>
                <v-card-text class="ma-0 pa-1" v-if="active_files">
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
                            <v-chip outlined small @click="viewPdfFile(item)">{{ item.file_name }}</v-chip>
                          </td>
                          <td>
                            <v-btn
                              text
                              small
                              :href="
                                $store.state.backend_url +
                                'staffs/file-download/' +
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
              </v-card>

              <!-- blanka fayllar -->
              <v-card
                v-if="
                  document_blank_templates &&
                  document_blank_templates.length &&
                  document.status >= 3
                "
                class="mb-2"
                outlined
              >
                <v-system-bar
                  class="pa-1 justify-center"
                  style="background-color: #163e72; color: #fff"
                  elevation="3"
                >
                  <v-spacer></v-spacer>
                  {{ $t("blankTemplate.get") }}
                  <v-spacer></v-spacer>
                  <v-btn icon small @click="active_blanks = !active_blanks">
                    <v-icon class="ma-0" color="white" v-if="active_blanks">mdi-menu-down</v-icon>
                    <v-icon class="ma-0" color="white" v-else>mdi-menu-up</v-icon>
                  </v-btn>
                </v-system-bar>
                <v-card-text class="ma-0 pa-1" v-if="active_blanks">
                  <div
                    v-for="(document_detail, detail_index) in document &&
                    document.document_details"
                    :key="detail_index"
                  >
                    <div
                      v-for="(
                        document_blank_template, blank_index
                      ) in document_blank_templates"
                      :key="blank_index"
                    >
                      <div v-if="document_detail.document_detail_employees.length">
                        <div
                          v-for="(
                            employee, emp_index
                          ) in document_detail.document_detail_employees"
                          :key="emp_index"
                        >
                          <v-chip
                            small
                            @click="
                              blankDownload(
                                document_blank_template,
                                document_detail,
                                employee
                              )
                            "
                          >
                            {{ document_blank_template.blank_template.blank_name }}
                            {{ employee.employee.tabel }}
                          </v-chip>
                        </div>
                      </div>
                      <div v-else>
                        <v-chip
                          small
                          @click="
                            blankDownload(document_blank_template, document_detail, null)
                          "
                        >{{ document_blank_template.blank_template.blank_name }}</v-chip>
                      </div>
                    </div>
                  </div>
                </v-card-text>
              </v-card>

              <!-- imzolovchilar -->
              <v-card outlined class="mb-2">
                <v-system-bar
                  class="pa-1"
                  style="background-color: #163e72; color: #fff"
                  elevation="3"
                  small
                >
                  <v-spacer></v-spacer>
                  {{ $t("document.signers") }}
                  <v-spacer></v-spacer>
                  <v-btn icon small @click="active_sign = !active_sign">
                    <v-icon class="ma-0" color="white" v-if="active_sign">mdi-menu-down</v-icon>
                    <v-icon class="ma-0" color="white" v-else>mdi-menu-up</v-icon>
                  </v-btn>
                </v-system-bar>
                <v-card-text class="pa-1" v-if="active_sign">
                  <template v-for="action_type in action_types">
                    <v-card
                      outlined
                      tile
                      :key="action_type.index"
                      class="mb-1"
                      v-if="
                        documentSigners &&
                        documentSigners.find((v) => {
                          if (action_type.id == v.action_type_id && !v.control_punkt_id)
                            return true;
                          else false;
                        })
                      "
                    >
                      <v-system-bar color="grey lighten-4">{{ action_type["name_" + $i18n.locale] }}</v-system-bar>
                      <v-card-text class="pa-1">
                        <template
                          v-for="documentSigner in documentSigners.filter((v) => {
                            if (action_type.id == v.action_type_id && !v.control_punkt_id)
                              return v;
                          })"
                        >
                          <v-row :key="documentSigner.index" class="ma-0 justify-space-between">
                            <v-col class="pa-0">
                              <v-popover
                                bottom
                                v-if="
                                  documentSigner.fio ||
                                  documentSigner.staff.employees.length
                                "
                              >
                                <v-chip label small color="white">
                                  {{
                                  documentSigner.fio
                                  ? documentSigner.fio
                                  : documentSigner.staff.employees[0][
                                  "firstname_" + language
                                  ].substr(0, 1) +
                                  "." +
                                  documentSigner.staff.employees[0][
                                  "middlename_" + language
                                  ].substr(0, 1) +
                                  ". " +
                                  documentSigner.staff.employees[0][
                                  "lastname_" + language
                                  ]
                                  }}
                                  <v-icon v-if="documentSigner.description">mdi-dots-horizontal</v-icon>
                                </v-chip>
                                <template slot="popover">
                                  <v-card
                                    style="
                                      padding: 3px;
                                      box-shadow: 0px 0px 20px;
                                      max-width: 620px;
                                    "
                                  >
                                    <v-card outlined tile>
                                      <v-system-bar color="grey lighten-2">
                                        {{
                                        documentSigner.fio
                                        ? documentSigner.fio
                                        : documentSigner.staff.employees[0][
                                        "firstname_" + language
                                        ].substr(0, 1) +
                                        "." +
                                        documentSigner.staff.employees[0][
                                        "middlename_" + language
                                        ].substr(0, 1) +
                                        ". " +
                                        documentSigner.staff.employees[0][
                                        "lastname_" + language
                                        ]
                                        }}
                                      </v-system-bar>
                                      <v-card-text style="color: #000; padding: 5px">
                                        {{ documentSigner.department }}
                                        <br />
                                        {{ documentSigner.position }}
                                      </v-card-text>
                                    </v-card>
                                    <v-card outlined tile v-if="documentSigner.taken_datetime">
                                      <v-card-text style="color: #000; padding: 5px">
                                        <v-chip class="mx-1" depressed outlined small rounded text>
                                          <v-icon left>mdi-arrow-down-bold</v-icon>
                                          {{ documentSigner.taken_datetime }}
                                        </v-chip>
                                        <v-chip
                                          v-if="documentSigner.status == 1"
                                          color="success"
                                          class="mx-1"
                                          depressed
                                          outlined
                                          small
                                          rounded
                                          text
                                        >
                                          <v-icon left>mdi-check-bold</v-icon>
                                          {{
                                          documentSigner.updated_at.substr(0, 10) +
                                          " " +
                                          documentSigner.updated_at.substr(11, 8)
                                          }}
                                        </v-chip>
                                        <v-chip
                                          v-else-if="documentSigner.status == 2"
                                          color="error"
                                          class="mx-1"
                                          depressed
                                          outlined
                                          small
                                          rounded
                                          text
                                        >
                                          <v-icon left>mdi-close-thick</v-icon>
                                          {{
                                          documentSigner.updated_at.substr(0, 10) +
                                          " " +
                                          documentSigner.updated_at.substr(11, 8)
                                          }}
                                        </v-chip>
                                        <v-chip
                                          v-else
                                          class="mx-1"
                                          depressed
                                          outlined
                                          small
                                          rounded
                                          text
                                        >
                                          <v-icon left>mdi-clock-outline</v-icon>
                                          {{ documentSigner.due_date }}
                                        </v-chip>
                                      </v-card-text>
                                    </v-card>
                                    <v-card
                                      outlined
                                      tile
                                      v-if="documentSigner.description"
                                      style="max-width: 660px"
                                    >
                                      <v-system-bar
                                        color="grey lighten-2"
                                      >{{ $t("document.comment") }}</v-system-bar>
                                      <v-card-text
                                        style="color: #000; padding: 5px"
                                      >{{ documentSigner.description }}</v-card-text>
                                    </v-card>
                                  </v-card>
                                </template>
                              </v-popover>
                            </v-col>
                            <v-col class="pa-0 text-right">
                              <v-chip v-if="documentSigner.status" small color="white">
                                {{
                                documentSigner.signed_at
                                }}
                              </v-chip>
                              <v-btn
                                icon
                                small
                                :color="
                                  documentSigner.status == 1
                                    ? 'success'
                                    : documentSigner.status == 2
                                    ? 'error'
                                    : documentSigner.status == 3
                                    ? 'primary'
                                    : documentSigner.status == 4
                                    ? 'orange'
                                    : ''
                                "
                                class="text-right"
                              >
                                <template v-if="documentSigner.status == 1">
                                  <v-icon v-if="documentSigner.sign_type == 0">mdi-check-bold</v-icon>
                                  <v-img
                                    v-else
                                    src="img/icons/e-imzo.png"
                                    width="30"
                                    class="mx-auto"
                                    contain
                                  ></v-img>
                                </template>
                                <template v-else-if="documentSigner.status == 2">
                                  <v-icon>mdi-close-thick</v-icon>
                                  <v-btn
                                    color="primary"
                                    icon
                                    small
                                    class="mr-1"
                                    v-if="
                                      document.status == 6 &&
                                      documentSigner.status == 2 &&
                                      $store.getters.checkPermission('okd_kanselyariya')
                                    "
                                    @click="dialogReturnRajapov = true"
                                  >
                                    <v-icon>mdi-undo-variant</v-icon>
                                  </v-btn>
                                </template>
                                <v-icon v-else-if="documentSigner.status == 3">mdi-timer-sand</v-icon>
                                <v-icon
                                  v-else-if="documentSigner.status == 4"
                                >mdi-alert-circle-outline</v-icon>
                              </v-btn>
                            </v-col>
                          </v-row>
                        </template>
                      </v-card-text>
                    </v-card>
                  </template>
                  <v-card
                    v-for="(control_punkt, con_index) in control_punkts"
                    :key="con_index"
                    outlined
                    tile
                    class="mb-1"
                  >
                    <v-system-bar color="grey lighten-4">
                      {{ $t("control_punkt.name") + " " + (con_index + 1) }}
                      <v-spacer></v-spacer>
                      <v-icon
                        v-if="
                          $store.getters.checkPermission('add_control_punkt') &&
                          !(
                            control_punkt.controller.status == 1 ||
                            control_punkt.controller.status == 2
                          )
                        "
                        color="primary"
                        @click="editControlPunkt(control_punkt)"
                      >mdi-pencil</v-icon>
                    </v-system-bar>
                    <v-card-text class="pa-1">
                      {{ control_punkt.content }}
                      <v-popover
                        bottom
                        v-if="
                          control_punkt.controller.fio ||
                          control_punkt.controller.staff.employees.length
                        "
                      >
                        <v-chip
                          label
                          small
                          :outlined="control_punkt.controller.status == 0 ? true : false"
                          :color="
                            control_punkt.controller.status == 1
                              ? 'green lighten-4'
                              : control_punkt.controller.status == 2
                              ? 'red lighten-4'
                              : control_punkt.controller.status == 3
                              ? 'blue lighten-4'
                              : control_punkt.controller.status == 4
                              ? 'orange lighten-4'
                              : 'black'
                          "
                        >
                          {{
                          control_punkt.controller.fio
                          ? control_punkt.controller.fio
                          : control_punkt.controller.staff.employees[0][
                          "firstname_" + language
                          ].substr(0, 1) +
                          "." +
                          control_punkt.controller.staff.employees[0][
                          "middlename_" + language
                          ].substr(0, 1) +
                          ". " +
                          control_punkt.controller.staff.employees[0][
                          "lastname_" + language
                          ]
                          }}
                          <v-icon v-if="control_punkt.controller.description">mdi-dots-horizontal</v-icon>
                        </v-chip>
                        <template slot="popover">
                          <v-card
                            style="
                              padding: 3px;
                              box-shadow: 0px 0px 20px;
                              max-width: 620px;
                            "
                          >
                            <v-card outlined tile>
                              <v-system-bar color="grey lighten-2">
                                {{ $t("control_punkt.for_control") }}:
                                {{
                                control_punkt.controller.fio
                                ? control_punkt.controller.fio
                                : control_punkt.controller.staff.employees[0][
                                "firstname_" + language
                                ].substr(0, 1) +
                                "." +
                                control_punkt.controller.staff.employees[0][
                                "middlename_" + language
                                ].substr(0, 1) +
                                ". " +
                                control_punkt.controller.staff.employees[0][
                                "lastname_" + language
                                ]
                                }}
                              </v-system-bar>
                              <v-card-text style="color: #000; padding: 5px">
                                {{ control_punkt.controller.department }}
                                <br />
                                {{ control_punkt.controller.position }}
                              </v-card-text>
                            </v-card>
                            <v-card outlined tile v-if="control_punkt.controller.taken_datetime">
                              <v-card-text style="color: #000; padding: 5px">
                                <v-chip class="mx-1" depressed outlined small rounded text>
                                  <v-icon left>mdi-arrow-down-bold</v-icon>
                                  {{ control_punkt.controller.taken_datetime }}
                                </v-chip>
                                <v-chip
                                  v-if="control_punkt.controller.status == 1"
                                  color="success"
                                  class="mx-1"
                                  depressed
                                  outlined
                                  small
                                  rounded
                                  text
                                >
                                  <v-icon left>mdi-check-bold</v-icon>
                                  {{
                                  control_punkt.controller.updated_at.substr(0, 10) +
                                  " " +
                                  control_punkt.controller.updated_at.substr(11, 8)
                                  }}
                                </v-chip>
                                <v-chip
                                  v-else-if="control_punkt.controller.status == 2"
                                  color="error"
                                  class="mx-1"
                                  depressed
                                  outlined
                                  small
                                  rounded
                                  text
                                >
                                  <v-icon left>mdi-close-thick</v-icon>
                                  {{
                                  control_punkt.controller.updated_at.substr(0, 10) +
                                  " " +
                                  control_punkt.controller.updated_at.substr(11, 8)
                                  }}
                                </v-chip>
                                <v-chip v-else class="mx-1" depressed outlined small rounded text>
                                  <v-icon left>mdi-clock-outline</v-icon>
                                  {{ control_punkt.controller.due_date }}
                                </v-chip>
                              </v-card-text>
                            </v-card>
                            <v-card
                              outlined
                              tile
                              v-if="control_punkt.controller.description"
                              style="max-width: 660px"
                            >
                              <v-system-bar color="grey lighten-2">{{ $t("document.comment") }}</v-system-bar>
                              <v-card-text
                                style="color: #000; padding: 5px"
                              >{{ control_punkt.controller.description }}</v-card-text>
                            </v-card>
                          </v-card>
                        </template>
                      </v-popover>
                    </v-card-text>
                    <v-divider></v-divider>
                    <v-card-text class="pa-1">
                      <v-treeview
                        :items="
                          document_signers.filter((v) => {
                            if (
                              v.control_punkt_id == control_punkt.id &&
                              v.id != control_punkt.controller_id
                            )
                              return v;
                          })
                        "
                      >
                        <template v-slot:label="{ item }">
                          <v-popover bottom v-if="item.fio || item.staff.employees.length">
                            <v-chip
                              label
                              small
                              :outlined="item.status == 0 ? true : false"
                              :color="
                                item.status == 1
                                  ? 'green lighten-4'
                                  : item.status == 2
                                  ? 'red lighten-4'
                                  : item.status == 3
                                  ? 'blue lighten-4'
                                  : item.status == 4
                                  ? 'orange lighten-4'
                                  : 'black'
                              "
                            >
                              {{
                              item.fio
                              ? item.fio
                              : item.staff.employees[0][
                              "firstname_" + language
                              ].substr(0, 1) +
                              "." +
                              item.staff.employees[0][
                              "middlename_" + language
                              ].substr(0, 1) +
                              ". " +
                              item.staff.employees[0]["lastname_" + language]
                              }}
                              <v-icon v-if="item.description">mdi-dots-horizontal</v-icon>
                            </v-chip>
                            <template slot="popover">
                              <v-card
                                style="
                                  padding: 3px;
                                  box-shadow: 0px 0px 20px;
                                  max-width: 620px;
                                "
                              >
                                <v-card outlined tile>
                                  <v-system-bar color="grey lighten-2">
                                    {{
                                    item.fio
                                    ? item.fio
                                    : item.staff.employees[0][
                                    "firstname_" + language
                                    ].substr(0, 1) +
                                    "." +
                                    item.staff.employees[0][
                                    "middlename_" + language
                                    ].substr(0, 1) +
                                    ". " +
                                    item.staff.employees[0]["lastname_" + language]
                                    }}
                                  </v-system-bar>
                                  <v-card-text style="color: #000; padding: 5px">
                                    {{ item.department }}
                                    <br />
                                    {{ item.position }}
                                  </v-card-text>
                                </v-card>
                                <v-card outlined tile v-if="item.taken_datetime">
                                  <v-card-text style="color: #000; padding: 5px">
                                    <v-chip class="mx-1" depressed outlined small rounded text>
                                      <v-icon left>mdi-arrow-down-bold</v-icon>
                                      {{ item.taken_datetime }}
                                    </v-chip>
                                    <v-chip
                                      v-if="item.status == 1"
                                      color="success"
                                      class="mx-1"
                                      depressed
                                      outlined
                                      small
                                      rounded
                                      text
                                    >
                                      <v-icon left>mdi-check-bold</v-icon>
                                      {{
                                      item.updated_at.substr(0, 10) +
                                      " " +
                                      item.updated_at.substr(11, 8)
                                      }}
                                    </v-chip>
                                    <v-chip
                                      v-else-if="item.status == 2"
                                      color="error"
                                      class="mx-1"
                                      depressed
                                      outlined
                                      small
                                      rounded
                                      text
                                    >
                                      <v-icon left>mdi-close-thick</v-icon>
                                      {{
                                      item.updated_at.substr(0, 10) +
                                      " " +
                                      item.updated_at.substr(11, 8)
                                      }}
                                    </v-chip>
                                    <v-chip
                                      v-else
                                      class="mx-1"
                                      depressed
                                      outlined
                                      small
                                      rounded
                                      text
                                    >
                                      <v-icon left>mdi-clock-outline</v-icon>
                                      {{ item.due_date }}
                                    </v-chip>
                                  </v-card-text>
                                </v-card>
                                <v-card
                                  outlined
                                  tile
                                  v-if="item.description"
                                  style="max-width: 660px"
                                >
                                  <v-system-bar color="grey lighten-2">{{ $t("document.comment") }}</v-system-bar>
                                  <v-card-text
                                    style="color: #000; padding: 5px"
                                  >{{ item.description }}</v-card-text>
                                </v-card>
                              </v-card>
                            </template>
                          </v-popover>
                        </template>
                      </v-treeview>
                      <!-- <v-chip
                        v-for="(
                          document_signer, DSIndex
                        ) in document_signers.filter((v) => {
                          if (v.control_punkt_id == control_punkt.id && v.id != control_punkt.controller_id) return v;
                        })"
                        :key="DSIndex"
                      ></v-chip>-->
                    </v-card-text>
                  </v-card>
                </v-card-text>
              </v-card>

              <v-card outlined class="mb-2" v-if="document.staff">
                <v-system-bar
                  class="pa-1 justify-center"
                  style="background-color: #163e72; color: #fff"
                  elevation="3"
                >
                  {{ $t("staff_id") }}
                  <v-spacer></v-spacer>
                </v-system-bar>
                <v-card-text class="pa-2 pb-1">
                  {{
                  document.staff["department_code"] +
                  " " +
                  document.staff["department_name_" + $i18n.locale]
                  }}
                  <br />
                  {{ document.staff["position_name_" + $i18n.locale] }}
                </v-card-text>
              </v-card>

              <v-card outlined class="mb-2" v-if="document.department2">
                <v-system-bar
                  class="pa-1 justify-center"
                  style="background-color: #163e72; color: #fff"
                  elevation="3"
                >
                  {{ $t("department_id") }}
                  <v-spacer></v-spacer>
                </v-system-bar>
                <v-card-text class="pa-2 pb-1">
                  {{
                  document.department2["department_code"] +
                  " " +
                  document.department2["department_name_" + $i18n.locale]
                  }}
                  <br />
                  {{ document.department2["position_name_" + $i18n.locale] }}
                </v-card-text>
              </v-card>

              <!-- HUJJAT TARIXI-->
              <v-card outlined class="mb-2">
                <v-system-bar
                  class="pa-1 justify-center"
                  style="background-color: #163e72; color: #fff"
                  elevation="3"
                >
                  <v-spacer></v-spacer>
                  {{ $t("document.history") }}
                  <v-spacer></v-spacer>
                  <v-btn icon small @click="active_history = !active_history">
                    <v-icon class="ma-0" color="white" v-if="active_history">mdi-menu-down</v-icon>
                    <v-icon class="ma-0" color="white" v-else>mdi-menu-up</v-icon>
                  </v-btn>
                </v-system-bar>
                <v-card-text class="pa-0 pb-1" v-if="active_history">
                  <v-treeview open-all :items="history" :open="tree_ids" item-key="id">
                    <template v-slot:label="{ item }">
                      <div class="historyList">
                        <div class="historyAvatar">
                          <div
                            class="no-img"
                            v-if="item.signer_employee_id"
                          >{{ item.fio && item.fio.charAt(0) }}</div>
                        </div>
                        <div class="historyContent lighten-4">
                          <v-row>
                            <v-col cols="12" class="py-0">
                              <template v-if="item.signer_employee_id">
                                <div>
                                  <strong>{{ item.fio }}:</strong>
                                  <div v-for="(comment, i) in item.comments" :key="i">
                                    <v-alert
                                      v-if="
                                        comment.status == 1 ||
                                        comment.status == 10 ||
                                        comment.status == 11
                                      "
                                      text
                                      dense
                                      border="left"
                                      type="success"
                                      class="mb-1 pa-1 pl-2"
                                    >
                                      {{ comment.comment }}
                                      <v-chip
                                        class="pa-1"
                                        label
                                        small
                                        color="green lighten-5"
                                        text-color="black"
                                      >{{ comment.signed_at }}</v-chip>
                                    </v-alert>
                                    <v-alert
                                      v-else-if="comment.status == 0"
                                      text
                                      dense
                                      border="left"
                                      type="success"
                                      class="mb-1 pa-1 pl-2"
                                    >
                                      {{ $t("create") }}
                                      <v-chip
                                        class="pa-1"
                                        label
                                        small
                                        color="green lighten-5"
                                        text-color="black"
                                      >{{ comment.signed_at }}</v-chip>
                                    </v-alert>
                                    <v-alert
                                      v-else-if="
                                        comment.status == 2 || comment.status == 21
                                      "
                                      text
                                      dense
                                      border="left"
                                      type="error"
                                      class="mb-1 pa-1 pl-2"
                                      icon="mdi-close-thick"
                                    >
                                      {{ comment.comment }}
                                      <v-chip
                                        class="pa-1"
                                        label
                                        small
                                        color="red lighten-5"
                                        text-color="black"
                                      >{{ comment.signed_at }}</v-chip>
                                    </v-alert>
                                    <v-alert
                                      v-else-if="comment.status == 3"
                                      text
                                      dense
                                      border="left"
                                      color="primary"
                                      class="mb-1 pa-1 pl-2"
                                      icon="mdi-timer-sand"
                                    >
                                      {{ $t("document.processing") }}
                                      <v-chip
                                        class="pa-1"
                                        label
                                        small
                                        color="blue lighten-5"
                                        text-color="black"
                                      >{{ comment.signed_at }}</v-chip>
                                    </v-alert>
                                    <v-alert
                                      v-else-if="comment.status == 4"
                                      text
                                      dense
                                      border="left"
                                      type="warning"
                                      class="mb-1 pa-1 pl-2"
                                      style="font-size: 13px"
                                    >
                                      {{ comment.comment }}
                                      <v-chip
                                        class="pa-1"
                                        label
                                        small
                                        color="orange lighten-5"
                                        text-color="black"
                                      >{{ comment.signed_at }}</v-chip>
                                    </v-alert>
                                    <v-alert
                                      v-else-if="comment.status == 5"
                                      text
                                      dense
                                      border="left"
                                      color="blue-grey"
                                      class="mb-1 pa-1 pl-2"
                                      icon="mdi-comment-outline"
                                      style="font-size: 13px"
                                    >
                                      {{ comment.comment }}
                                      <v-chip
                                        class="pa-1"
                                        label
                                        small
                                        color="blue-grey lighten-5"
                                        text-color="black"
                                      >{{ comment.signed_at }}</v-chip>
                                    </v-alert>
                                    <v-alert
                                      v-else-if="
                                        comment.status == 6 || comment.status == 7
                                      "
                                      text
                                      dense
                                      border="left"
                                      color="blue-grey"
                                      class="mb-1 pa-1 pl-2"
                                      icon="mdi-comment-plus-outline"
                                      style="font-size: 13px"
                                    >
                                      {{ comment.comment }}
                                      <v-chip
                                        class="pa-1"
                                        label
                                        small
                                        color="blue-grey lighten-5"
                                        text-color="black"
                                      >{{ comment.signed_at }}</v-chip>
                                    </v-alert>
                                    <v-alert
                                      v-else-if="comment.status == 8"
                                      text
                                      dense
                                      border="left"
                                      color="blue"
                                      class="mb-1 pa-1 pl-2"
                                      icon="mdi-pencil"
                                      style="font-size: 13px"
                                    >
                                      {{ $t("changed") }}
                                      <v-chip
                                        class="pa-1"
                                        label
                                        small
                                        color="blue lighten-5"
                                        text-color="black"
                                      >{{ comment.signed_at }}</v-chip>
                                    </v-alert>
                                    <v-alert
                                      v-else-if="comment.status == 9"
                                      text
                                      dense
                                      border="left"
                                      type="success"
                                      icon="mdi-publish"
                                      class="mb-1 pa-1 pl-2"
                                    >
                                      {{ $t(comment.comment) }}
                                      <v-chip
                                        class="pa-1"
                                        label
                                        small
                                        color="green lighten-5"
                                        text-color="black"
                                      >{{ comment.signed_at }}</v-chip>
                                    </v-alert>
                                    <v-alert
                                      v-else-if="
                                        comment.status == 12 || comment.status == 14
                                      "
                                      text
                                      dense
                                      border="left"
                                      color="blue-grey"
                                      class="mb-1 pa-1 pl-2"
                                      icon="mdi-comment-arrow-left-outline"
                                      style="font-size: 13px"
                                    >
                                      {{ comment.comment }}
                                      <v-chip
                                        class="pa-1"
                                        label
                                        small
                                        color="blue-grey lighten-5"
                                        text-color="black"
                                      >{{ comment.signed_at }}</v-chip>
                                    </v-alert>
                                    <v-alert
                                      v-else-if="comment.status == 13"
                                      text
                                      dense
                                      border="left"
                                      type="success"
                                      class="mb-1 pa-1 pl-2"
                                    >
                                      {{ comment.comment }}
                                      <v-chip
                                        class="pa-1"
                                        label
                                        small
                                        color="green lighten-5"
                                        text-color="black"
                                      >{{ comment.signed_at }}</v-chip>
                                    </v-alert>
                                    <v-alert
                                      v-else-if="!comment.status"
                                      text
                                      dense
                                      border="left"
                                      type="success"
                                      class="mb-1 pa-1 pl-2"
                                      style="font-size: 13px"
                                    >
                                      {{ comment.comment }}
                                      <v-chip
                                        class="pa-1"
                                        label
                                        small
                                        color="green lighten-5"
                                        text-color="black"
                                      >{{ comment.signed_at }}</v-chip>
                                    </v-alert>
                                    <v-chip
                                      v-for="(file, index) in comment.files"
                                      :key="index"
                                      class="pa-1 mt-n1 mb-1"
                                      small
                                      color="white"
                                      outlined
                                      
                                      text-color="black"
                                    >
                                      <v-icon small>mdi-paperclip</v-icon>
                                      <span @click="viewPdfFile(file)">{{ file.file_name }}</span>
                                      <v-btn
                                          text
                                          x-small
                                          class="ml-2"
                                          :href="
                                            $store.state.backend_url +
                                            'staffs/file-download/' +
                                            file.id
                                          "
                                        >
                                          <v-icon color="green">mdi-download</v-icon>
                                        </v-btn>
                                    </v-chip>
                                  </div>
                                </div>
                              </template>
                            </v-col>
                          </v-row>
                        </div>
                      </div>
                    </template>
                  </v-treeview>
                </v-card-text>
              </v-card>
            </v-card>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <v-dialog v-model="dialogReturnRajapov" persistent width="500">
      <v-card>
        <v-card-title class="grey lighten-3">
          {{ $t("document.reaction") }}
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="dialogReturnRajapov = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form ref="dialogReturnRajapov">
            <v-row>
              <v-col cols="12">
                <v-textarea
                  :label="$t('document.comment')"
                  v-model="documentComment"
                  rows="1"
                  row-height="15"
                  dense
                  outlined
                  auto-grow
                  hide-details="auto"
                  :rules="[(v) => !!v || $t('input.required')]"
                ></v-textarea>
              </v-col>
              <v-col cols="12">
                <label for>{{ $t("document.files") }}</label>
                <v-file-input
                  v-model="selectFiles"
                  :rules="[
                    (files) => {
                      let allowedExtensions = /(\.pdf)$/i;
                      let error = false;
                      files.forEach((file) => {
                        if (!allowedExtensions.exec(file.name)) {
                          error = true;
                        }
                      });
                      return !error || $t('requiredpdf');
                    },
                  ]"
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
              <v-col cols="6" offset="6" style="min-width: 100px" class="text-right">
                <v-btn
                  outlined
                  block
                  width="200px"
                  color="success"
                  class="mx-2"
                  @click="returnRajapov()"
                >
                  <span style="white-space: normal; width: 180px">{{ $t("document.send") }}</span>
                </v-btn>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
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
          <v-btn outlined x-small fab class @click="changeSize()" v-if="true">
            <v-icon v-if="horizontalIcon">mdi-arrow-expand-horizontal</v-icon>
            <v-icon v-else>mdi-arrow-expand-vertical</v-icon>
          </v-btn>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="fullScreenDialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-row class="mx-0 px-0">
          <v-col cols="12">
            <pdf
              v-for="i in numPages"
              :key="i"
              :src="loadingTask"
              :page="i"
              :style="
                'margin-left:auto;margin-right:auto;margin-bottom:10px;border: 5px double #555;width:' +
                pdfWidth +
                'px;'
              "
            ></pdf>
          </v-col>
        </v-row>
      </v-card>
    </v-dialog>

    <v-dialog
      v-model="dialog"
      @keydown.esc="dialog = false"
      fullscreen
      hide-overlay
      transition="dialog-bottom-transition"
    >
      <div class="fullHeight">
        <v-card class="pa-1">
          <v-card-title>
            <b>{{ $t("document.eri_info") }}</b>
            <v-spacer></v-spacer>
            <v-btn color="red" outlined x-small fab class @click="dialog = false">
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>
          <v-card-text>
            <v-row class="mx-4">
              <v-col xl="7" offset-xl="1" lg="7" offset-lg="1" md="7" sm="7" xs="12" cols="12">
                <pdf
                  v-for="i in numPagesEimzo"
                  :key="i"
                  :src="loadingTaskEimzo"
                  :page="i"
                  :height="screenHeight - 80"
                  width="100%"
                  style="margin-bottom: 20px; border: 5px double #555"
                ></pdf>
              </v-col>
              <v-col xl="3" lg="3" md="5" sm="5" xs="12" cols="12">
                <!-- From -->
                <v-list>
                  <b
                    style="color: black; font-size: 18px; margin-left: 15px"
                  >{{ $t("document.confirmers") }}</b>
                  <v-divider></v-divider>
                  <v-list v-for="(signer, indx1) in eiSigners" :key="indx1">
                    <v-list-item v-if="signer">
                      <v-list-item-content class="my-0 py-0">
                        <v-list-item-title>
                          {{
                          $t("document.when_who")
                          }}
                        </v-list-item-title>
                        <v-list-item-subtitle>
                          <b>
                            {{
                            signer.certificate[0].subjectName
                            .split(",")[0]
                            .split("=")[1]
                            }}
                          </b>
                          <span class="float-right" style="color: black">
                            <b>{{ momentTime(signer.signingTime) }}</b>
                          </span>
                        </v-list-item-subtitle>
                      </v-list-item-content>
                    </v-list-item>
                    <v-list-item>
                      <v-list-item-content class="my-0 py-0">
                        <v-list-item-title>
                          {{
                          $t("document.eri_given")
                          }}
                        </v-list-item-title>
                        <v-list-item-subtitle>
                          <b>
                            {{
                            signer.certificate[0].issuerName.split(",")[0].split("=")[1]
                            }}
                          </b>
                        </v-list-item-subtitle>
                      </v-list-item-content>
                    </v-list-item>

                    <v-list-item>
                      <v-list-item-content class="my-0 py-0">
                        <v-list-item-title>
                          {{
                          $t("document.eri_serial")
                          }}
                        </v-list-item-title>
                        <v-list-item-subtitle>
                          <b>{{ signer.certificate[0].serialNumber }}</b>
                        </v-list-item-subtitle>
                      </v-list-item-content>
                    </v-list-item>

                    <v-list-item>
                      <v-list-item-content class="my-0 py-0">
                        <v-list-item-title>
                          {{
                          $t("document.eri_valid_date")
                          }}
                        </v-list-item-title>
                        <v-list-item-subtitle>
                          <b>{{ signer.certificate[0].validTo }}</b>
                        </v-list-item-subtitle>
                      </v-list-item-content>
                    </v-list-item>
                    <v-divider v-if="eiSigners.length"></v-divider>
                  </v-list>
                </v-list>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </div>
    </v-dialog>

    <v-dialog v-model="modalDocumentReaction" persistent width="500">
      <v-card>
        <v-card-title class="grey lighten-3">
          {{ $t("document.reaction") }}
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="modalDocumentReaction = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form name="testform" ref="reactionForm">
            <v-text-field name="pkcs7" v-show="false"></v-text-field>
            <v-row>
              <v-col cols="12" class="my-2 py-0">
                {{ $t("select_eimzo") }}
                <br />
                <select
                  name="key"
                  @change="cbChanged(this)"
                  style="border: 1px solid #9e9e9e; border-radius: 4px"
                  class="pa-2 v-input__control"
                ></select>
                <span v-if="!eimzo_username" style="color: red">{{ $t("input.required") }}</span>
                <label v-show="false" id="keyId"></label>
              </v-col>
              <v-col cols="12">
                <label for>{{ $t("document.comment") }}</label>
                <v-textarea
                  v-model="documentComment"
                  rows="1"
                  row-height="15"
                  dense
                  outlined
                  auto-grow
                  hide-details="auto"
                  :rules="
                    document.parent_employee_id
                      ? [(v) => !!v || $t('input.required')]
                      : []
                  "
                ></v-textarea>
                <!-- :rules="[(v) => !!v || $t('input.required')]" -->
              </v-col>
              <v-col cols="12">
                <label for>{{ $t("document.files") }}</label>
                <v-file-input
                  v-model="selectFiles"
                  :rules="[
                    (files) => {
                      let allowedExtensions = /(\.pdf)$/i;
                      let error = false;
                      if(files.length > 0){
                        files.forEach((file) => {
                          if (!allowedExtensions.exec(file.name)) {
                            error = true;
                          }
                        });
                        return !error || $t('requiredpdf');
                      } else  {
                        if(user && user.employee && user.employee.employee_staff.length > 0 && !['3267', '3636'].includes(user.employee.tabel)){
                          let userstaffs = user.employee.employee_staff.map(v => {
                            return v.staff_id;
                          });
                          let ds = documentSigners.some(v => v.action_type_id == 16 && userstaffs.includes(v.staff_id) && !!v.taken_datetime);
                          let ct = documentSigners.some((v) => !(v.status == 1 || v.status == 2) && v.action_type_id == 11);

                          let children = documentSigners.some(v => v.parent_employee_id == user.employee_id);
                          
                          if(ds && ct && [3,4,5].includes(document.status) && [305,357].includes(document.document_template_id) && ![11705, 8928, 8916,11888].includes(user.employee_id) && !children){
                            return $t('input.required');
                          }
                        }
                        return true;
                      }
                    },
                  ]"
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
              <v-col cols="12" style="min-width: 100px" class="text-right">
                <v-btn
                  outlined
                  v-if="
                    user &&
                    user.employee_id == document.created_employee_id &&
                    document.status == 0
                  "
                  width="200px"
                  color="success"
                  class="mx-2"
                  @click="getNumber()"
                >
                  <span style="white-space: normal; width: 180px">{{ $t("publish") }}</span>
                </v-btn>
                <v-btn
                  outlined
                  v-else-if="reaction"
                  width="200px"
                  color="success"
                  class="mx-2"
                  @click="verify(1)"
                >
                  <span style="white-space: normal; width: 180px">{{ $t("document.accept") }}</span>
                </v-btn>
                <v-btn
                  color="error"
                  outlined
                  width="200px"
                  class="mx-2"
                  v-else
                  @click="verify(2)"
                >{{ $t("document.reject") }}</v-btn>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog v-model="modalDocumentComment" persistent width="500">
      <v-card>
        <v-card-title class="grey lighten-3">
          {{ $t("document.reaction") }}
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="modalDocumentComment = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-row>
            <v-col cols="12">
              <v-textarea
                :label="$t('document.comment')"
                v-model="documentComment"
                rows="1"
                row-height="15"
                dense
                outlined
                auto-grow
                hide-details="auto"
              ></v-textarea>
            </v-col>
            <v-col cols="12">
              <label for>{{ $t("document.files") }}</label>
              <v-file-input
                v-model="selectFiles"
                :rules="[
                  (files) => {
                    let allowedExtensions = /(\.pdf)$/i;
                    let error = false;
                    files.forEach((file) => {
                      if (!allowedExtensions.exec(file.name)) {
                        error = true;
                      }
                    });
                    return !error || $t('requiredpdf');
                  },
                ]"
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
            <v-col cols="6" offset="6" style="min-width: 100px" class="text-right">
              <v-btn
                outlined
                block
                width="200px"
                color="success"
                class="mx-2"
                @click="comment(commentSigner.id)"
              >
                <span style="white-space: normal; width: 180px">{{ $t("document.send") }}</span>
              </v-btn>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog v-model="modalDocumentSubstantiate" persistent width="500">
      <v-card>
        <v-card-title class="grey lighten-3">
          {{ $t("document.reaction") }}
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="modalDocumentSubstantiate = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-row>
            <v-col cols="12">
              <v-textarea
                :label="$t('document.comment')"
                v-model="modalDocumentSubstantiateComment"
                rows="1"
                row-height="15"
                dense
                outlined
                auto-grow
                hide-details="auto"
              ></v-textarea>
            </v-col>
            <v-col cols="6" offset="6" style="min-width: 100px" class="text-right">
              <v-btn
                outlined
                block
                width="200px"
                color="success"
                class="mx-2"
                @click="commentSubstantiate(
                  documentSigners &&
                    documentSigners.find((v) => {
                      if (v.action_type_id == 6) return v.id;
                    }).id
                )"
              >
                <span style="white-space: normal; width: 180px">{{ $t("document.send") }}</span>
              </v-btn>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog v-model="modalDocumentPreAgreement" persistent width="500">
      <v-card>
        <v-card-title class="grey lighten-3">
          {{ $t("document.reaction") }}
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="modalDocumentPreAgreement = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-row>
            <v-col cols="12">
              <v-textarea
                :label="$t('document.comment')"
                v-model="documentComment"
                rows="1"
                row-height="15"
                dense
                outlined
                auto-grow
                hide-details="auto"
              ></v-textarea>
            </v-col>
            <v-col cols="12">
              <label for>{{ $t("document.files") }}</label>
              <v-file-input
                v-model="selectFiles"
                :rules="[
                  (files) => {
                    let allowedExtensions = /(\.pdf)$/i;
                    let error = false;
                    files.forEach((file) => {
                      if (!allowedExtensions.exec(file.name)) {
                        error = true;
                      }
                    });
                    return !error || $t('requiredpdf');
                  },
                ]"
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
            <v-col cols="6" offset="6" style="min-width: 100px" class="text-right">
              <v-btn
                outlined
                block
                width="200px"
                color="success"
                class="mx-2"
                @click="reactPreAgreement()"
              >
                <span style="white-space: normal; width: 180px">{{ $t("document.send") }}</span>
              </v-btn>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog v-model="modalDocumentConfirmation" persistent width="500">
      <v-card>
        <v-card-title class="grey lighten-3">
          {{ $t("document.reaction") }}
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="modalDocumentConfirmation = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-row>
            <v-col cols="12">
              <v-textarea
                :label="$t('document.comment')"
                v-model="documentComment"
                rows="1"
                row-height="15"
                dense
                outlined
                auto-grow
                hide-details="auto"
              ></v-textarea>
            </v-col>
            <v-col cols="6" offset="6" style="min-width: 100px" class="text-right">
              <v-btn
                outlined
                v-if="reaction"
                width="200px"
                color="success"
                class="mx-2"
                @click="confirmation(1)"
              >
                <span style="white-space: normal; width: 180px">{{ $t("document.accept") }}</span>
              </v-btn>
              <v-btn
                color="error"
                outlined
                width="200px"
                class="mx-2"
                v-else
                @click="confirmation(2)"
              >{{ $t("document.reject") }}</v-btn>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog v-model="modalEditAttribute" persistent width="800">
      <v-card>
        <v-card-title class="grey lighten-3">
          {{ $t("document.edit_attribute") }}
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="modalEditAttribute = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form ref="dialogFormAtt">
            <v-row>
              <v-col cols="9">
                <v-row
                  v-if="
                    user &&
                    edit_attribute &&
                    edit_attribute.document_detail_attributes &&
                    !!user.employee.employee_staff.find((v) => {
                      if (
                        v.staff_id ==
                        edit_attribute.document_detail_attributes.signer_staff_id
                      )
                        return v;
                    })
                  "
                >
                  <v-col
                    cols="12"
                    md="12"
                    class="py-1"
                    v-if="edit_attribute.document_detail_attributes.data_type_id == 1"
                  >
                    <v-text-field
                      v-model="edit_attribute.attribute_value"
                      dense
                      :label="
                        edit_attribute.document_detail_attributes[
                          'attribute_name_' + $i18n.locale
                        ]
                      "
                      outlined
                      type="text"
                      hide-details="auto"
                    ></v-text-field>
                  </v-col>
                  <v-col
                    cols="12"
                    md="12"
                    class="py-1"
                    v-if="edit_attribute.document_detail_attributes.data_type_id == 2"
                  >
                    <v-text-field
                      v-model="edit_attribute.attribute_value"
                      dense
                      :label="
                        edit_attribute.document_detail_attributes[
                          'attribute_name_' + $i18n.locale
                        ]
                      "
                      outlined
                      type="number"
                      hide-details="auto"
                    ></v-text-field>
                  </v-col>
                  <v-col
                    cols="12"
                    md="12"
                    class="py-1"
                    v-if="edit_attribute.document_detail_attributes.data_type_id == 6"
                  >
                    <v-autocomplete
                      clearable
                      :label="
                        edit_attribute.document_detail_attributes[
                          'attribute_name_' + $i18n.locale
                        ]
                      "
                      v-model.lazy="edit_attribute.attribute_value"
                      @keyup="
                        getTableList(
                          table_index,
                          edit_attribute.document_detail_attributes.table_list_id
                        )
                      "
                      :search-input.sync="search"
                      :items="tableLists[table_index]"
                      hide-details
                      dense
                      outlined
                      full-width
                      item-text="search"
                      item-value="id"
                      :loading="isLoading"
                    >
                      <!-- <template v-slot:selection="{ item }">
                            <v-chip color="white" class="pa-1 ma-0">
                              <span v-text="item.sear"></span>
                            </v-chip>
                      </template>-->
                      <template v-slot:item="{ item }">
                        <v-list-item-content>
                          <v-list-item-title v-text="item.search"></v-list-item-title>
                        </v-list-item-content>
                      </template>
                    </v-autocomplete>
                  </v-col>
                  <v-col
                    cols="12"
                    md="12"
                    class="py-1"
                    v-if="edit_attribute.document_detail_attributes.data_type_id == 5"
                  >
                    <v-checkbox
                      class="ma-1"
                      v-model="edit_attribute.attribute_value"
                      hide-details="auto"
                      :label="
                        edit_attribute.document_detail_attributes[
                          'attribute_name_' + $i18n.locale
                        ]
                      "
                    ></v-checkbox>
                  </v-col>
                </v-row>
              </v-col>
              <v-col
                cols="3"
                style="min-width: 100px; max-width: 100%"
                class="flex-grow-1 flex-shrink-0"
              >
                <v-btn
                  color="success"
                  class="my-1"
                  outlined
                  block
                  @click="editAttribute(edit_attribute)"
                >{{ $t("save") }}</v-btn>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog v-model="signatories" persistent width="1200">
      <v-card>
        <v-card-title class="grey lighten-3">
          {{ $t("document.add_signer") }}
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="signatories = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form ref="dialogForm">
            <v-row>
              <v-col
                cols="12"
                style="min-width: 100px; max-width: 100%"
                class="flex-grow-1 flex-shrink-0"
              >
                <v-autocomplete
                  :label="$t('document.department')"
                  v-model="new_signer.department"
                  @keyup="getSigners"
                  no-filter
                  class="pa-0"
                  clearable
                  :items="departments"
                  :search-input.sync="search"
                  item-value="id"
                  item-text="text"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details
                  dense
                  outlined
                  :loading="isLoading"
                >
                  <template v-slot:selection="{ item }">
                    <v-list-item-content>
                      <v-list-item-title v-text="item.code + ' ' + item.department_name"></v-list-item-title>
                      <v-list-item-subtitle>
                        {{
                        item.first_name +
                        " " +
                        item.last_name +
                        " " +
                        item.position_name
                        }}
                      </v-list-item-subtitle>
                    </v-list-item-content>
                  </template>
                  <template v-slot:item="{ item }">
                    <v-list-item-content>
                      <v-list-item-title v-text="item.code + ' ' + item.department_name"></v-list-item-title>
                      <v-list-item-subtitle>
                        {{
                        item.first_name +
                        " " +
                        item.last_name +
                        " " +
                        item.position_name
                        }}
                      </v-list-item-subtitle>
                    </v-list-item-content>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col
                cols="6"
                style="min-width: 100px; max-width: 100%"
                class="flex-grow-1 flex-shrink-0"
              >
                <v-autocomplete
                  :label="$t('document.resolution_type')"
                  class="pa-0"
                  clearable
                  v-model="new_signer.action_type_id"
                  :items="resolutionTypes"
                  item-value="id"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details
                  dense
                  outlined
                >
                  <template v-slot:selection="{ item }">
                    <v-chip color="white" class="pa-1 ma-0">
                      <span v-text="item['name_' + $i18n.locale]"></span>
                    </v-chip>
                  </template>
                  <template v-slot:item="{ item }">
                    <v-list-item-content>
                      <v-list-item-title v-text="item['name_' + $i18n.locale]"></v-list-item-title>
                    </v-list-item-content>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col
                cols="4"
                style="min-width: 100px; max-width: 100%"
                class="flex-grow-1 flex-shrink-0"
                v-if="new_signer.action_type_id == 4"
              >
                <v-text-field
                  v-model="new_signer.due_date"
                  :label="$t('document.term')"
                  hide-details
                  type="number"
                  dense
                  outlined
                  :rules="[(v) => !!v || $t('input.required')]"
                ></v-text-field>
              </v-col>
              <v-col
                cols="2"
                style="min-width: 100px; max-width: 100%"
                class="flex-grow-1 flex-shrink-0"
              >
                <v-btn color="success" outlined block @click="addSigners">{{ $t("add") }}</v-btn>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog v-model="modelAddResolution" persistent width="800">
      <v-card>
        <v-card-title class="grey lighten-3">
          {{ $t("document.add_resolution") }}
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="modelAddResolution = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form ref="resolutionForm">
            <v-row>
              <v-col cols="6" md="4" class="pa-1">
                <v-autocomplete
                  multiple
                  :label="$t('employee.index')"
                  class="pa-0"
                  clearable
                  v-model="resolution.employees"
                  :items="childrenEmployees"
                  @keyup="getResolutionEmployees()"
                  @click="getResolutionEmployees()"
                  :search-input.sync="search"
                  item-value="id"
                  item-text="search"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details
                  :loading="isLoading"
                  dense
                  outlined
                >
                  <template v-slot:selection="{ item }">
                    <v-chip color="white" class="pa-1 ma-0">
                      <span v-text="item.fio"></span>
                    </v-chip>
                  </template>
                  <template v-slot:item="{ item }">
                    <v-list-item-avatar
                      color="indigo"
                      class="headline font-weight-light white--text"
                    >{{ item.fio && item.fio.charAt(0) }}</v-list-item-avatar>
                    <v-list-item-content>
                      <v-list-item-title v-text="item.fio"></v-list-item-title>
                      <v-list-item-subtitle v-text="item.staff"></v-list-item-subtitle>
                    </v-list-item-content>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col cols="4" md="4" class="pa-1">
                <v-autocomplete
                  :label="$t('document.resolution_type')"
                  class="pa-0"
                  clearable
                  v-model="resolution.action_type"
                  :items="
                  [305,357].includes(document.document_template_id) 
                  ? (
                    document.document_signers.find(ds => user.employee.employee_staff.map((v) => v.staff_id).includes(ds.staff_id) && ds.taken_datetime && (ds.status == 0 || ds.status == 3) && ds.action_type_id == 16)
                    ? resolutionTypes.filter((v) => v.is_resolution && v.id != 4) 
                    : (
                      document.document_signers.find(ds => user.employee.employee_staff.map((v) => v.staff_id).includes(ds.staff_id) && ds.taken_datetime && (ds.status == 0 || ds.status == 3) && ds.action_type_id == 4) 
                      ? resolutionTypes.filter((v) => v.is_resolution && v.id != 16)
                      : resolutionTypes.filter((v) => v.is_resolution)
                    )
                  ) 
                  : resolutionTypes.filter((v) => v.is_resolution && v.id != 16)"
                  item-value="id"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details
                  dense
                  outlined
                >
                  <template v-slot:selection="{ item }">
                    <v-chip color="white" class="pa-1 ma-0">
                      <span v-text="item['name_' + $i18n.locale]"></span>
                    </v-chip>
                  </template>
                  <template v-slot:item="{ item }">
                    <v-list-item-content>
                      <v-list-item-title v-text="item['name_' + $i18n.locale]"></v-list-item-title>
                    </v-list-item-content>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col cols="2" md="4" class="pa-1">
                 <!-- <label for>{{ $t("employeeDocument.valid_until") }}</label> -->
                <v-menu
                  v-model="resolution_due_date"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="290px"
                >
                  <template v-slot:activator="{ on }">
                    <v-text-field
                      v-model="resolution.due_date"
                      readonly
                      :label="$t('document.term')"
                      v-on="on"
                      hide-details
                      class="white"
                      dense
                      outlined
                      placeholder="YYYY-MM-DD"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    no-title
                    v-model="resolution.due_date"
                    @input="resolution_due_date = false"
                  ></v-date-picker>
                </v-menu>               
              </v-col>
              <v-col cols="9" md="10" class="pa-1">
                <v-textarea
                  :label="$t('document.comment')"
                  v-model="resolution.assignment"
                  rows="2"
                  dense
                  outlined
                  auto-grow
                  hide-details="auto"
                  clearable
                ></v-textarea>
              </v-col>
              <v-col cols="3" md="2" class="py-1 pl-1">
                <v-btn width="100px" block color="success px-1" @click="saveResolution">
                  {{
                  $t("add")
                  }}
                </v-btn>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog v-model="modalToReturn" persistent width="500">
      <v-card>
        <v-card-title class="grey lighten-3">
          {{ $t("document.reaction") }}
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="modalToReturn = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-row>
            <v-col cols="12">
              <v-textarea
                :label="$t('document.comment')"
                v-model="documentComment"
                rows="1"
                row-height="15"
                dense
                outlined
                auto-grow
                hide-details="auto"
              ></v-textarea>
            </v-col>
            <v-col cols="6" offset="6" style="min-width: 100px" class="text-right">
              <v-btn
                outlined
                block
                width="200px"
                color="success"
                class="mx-2"
                @click="toReturn(returnEmployee.id)"
              >
                <span style="white-space: normal; width: 180px">{{ $t("document.send") }}</span>
              </v-btn>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog v-model="dialogEditDocumentTitle" persistent width="700">
      <v-card>
        <v-card-title class="grey lighten-3">
          {{ $t("document.edit_title") }}
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="dialogEditDocumentTitle = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-row>
            <v-col cols="12" class="px-1">
              <v-textarea
                :label="$t('document.title')"
                v-model="document.title"
                rows="1"
                row-height="15"
                dense
                outlined
                auto-grow
                hide-details="auto"
              ></v-textarea>
            </v-col>
            <v-col cols="4" offset="8" style="min-width: 100px" class="text-right">
              <v-btn
                outlined
                block
                width="200px"
                color="success"
                class="mx-2"
                @click="saveDocumentTitle(document.title)"
              >
                <span style="white-space: normal; width: 180px">{{ $t("save") }}</span>
              </v-btn>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog v-model="modelControlPunkt" persistent width="1200">
      <v-card>
        <v-card-title class="grey lighten-2">
          {{ modelControlPunktTitle }}
          <v-spacer></v-spacer>
          <v-btn
            color="success"
            outlined
            small
            class="mx-3"
            @click="
              addControlPunkt();
              modelControlPunkt = false;
            "
          >{{ $t("save") }}</v-btn>
          <v-btn color="red" outlined x-small fab class @click="modelControlPunkt = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form ref="dialogControlForm">
            <v-row>
              <v-col cols="12">
                <v-label @click="getComments()">
                  {{ $t("control_punkt.content") }}
                  <v-icon color="primary">mdi-dots-horizontal</v-icon>
                </v-label>
                <v-textarea
                  outlined
                  :rules="[(v) => !!v || $t('input.required')]"
                  v-model="control_punkt.content"
                  hide-details
                  rows="3"
                  dense
                ></v-textarea>
              </v-col>
              <v-col cols="12" md="6" lg="4">
                <v-autocomplete
                  :label="$t('control_punkt.priority')"
                  class="pa-0"
                  clearable
                  v-model="control_punkt.priority"
                  :items="priorities"
                  item-value="id"
                  :item-text="'name_' + $i18n.locale"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
              <v-col cols="12" md="6" lg="4">
                <v-autocomplete
                  :label="$t('control_punkt.punkt_type')"
                  class="pa-0"
                  clearable
                  v-model="control_punkt.punkt_type"
                  :items="punkt_types"
                  item-value="id"
                  :item-text="'name_' + $i18n.locale"
                  :rules="[(v) => !!v || $t('input.required')]"
                  hide-details
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
              <v-col cols="12" md="6" lg="4">
                <v-autocomplete
                  :label="$t('control_punkt.journal')"
                  class="pa-0"
                  disabled
                  clearable
                  v-model="control_punkt.journal_id"
                  :items="journals"
                  item-value="id"
                  :item-text="'name_' + $i18n.locale"
                  hide-details
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>
              <v-col
                cols="12"
                style="min-width: 100px; max-width: 100%"
                class="flex-grow-1 flex-shrink-0"
              >
                <v-autocomplete
                  :label="$t('control_punkt.controller')"
                  v-model="control_punkt.department_id"
                  @keyup="getForControlSigners"
                  no-filter
                  class="pa-0"
                  clearable
                  :items="control_departments"
                  :search-input.sync="sigsearch"
                  item-value="id"
                  item-text="text"
                  :rules="
                    control_punkt.punkt_type != 1
                      ? [(v) => !!v || $t('input.required')]
                      : []
                  "
                  hide-details
                  dense
                  outlined
                  :loading="isLoading"
                >
                  <template v-slot:selection="{ item }">
                    <v-list-item-content>
                      <v-list-item-title v-text="item.code + ' ' + item.department_name"></v-list-item-title>
                      <v-list-item-subtitle>
                        {{
                        item.first_name +
                        " " +
                        item.last_name +
                        " " +
                        item.position_name
                        }}
                      </v-list-item-subtitle>
                    </v-list-item-content>
                  </template>
                  <template v-slot:item="{ item }">
                    <v-list-item-content>
                      <v-list-item-title v-text="item.code + ' ' + item.department_name"></v-list-item-title>
                      <v-list-item-subtitle>
                        {{
                        item.first_name +
                        " " +
                        item.last_name +
                        " " +
                        item.position_name
                        }}
                      </v-list-item-subtitle>
                    </v-list-item-content>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col cols="12">
                <v-card outlined>
                  <v-card-title
                    class="grey lighten-4 py-0"
                  >{{ $t("control_punkt.distribution_list") }}</v-card-title>
                  <v-card-text class="py-0">
                    <v-form ref="addSignerForControl">
                      <v-row>
                        <v-col>
                          <v-text-field
                            v-model="new_signer.due_date"
                            :label="$t('document.term')"
                            hide-details
                            type="datetime-local"
                            dense
                            outlined
                            :rules="
                              control_punkt.punkt_type == 1
                                ? []
                                : [(v) => !!v || $t('input.required')]
                            "
                          ></v-text-field>
                        </v-col>
                        <v-col>
                          <v-autocomplete
                            :label="$t('control_punkt.nominated')"
                            v-model="new_signer.department"
                            @keyup="getSigners"
                            no-filter
                            class="pa-0"
                            clearable
                            :items="departments"
                            :search-input.sync="search"
                            item-value="id"
                            item-text="text"
                            hide-details
                            dense
                            outlined
                            :loading="isLoading"
                            :rules="[(v) => !!v || $t('input.required')]"
                          >
                            <template v-slot:selection="{ item }">
                              <v-list-item-content>
                                <v-list-item-title v-text="item.code + ' ' + item.department_name"></v-list-item-title>
                                <v-list-item-subtitle>
                                  {{
                                  item.first_name +
                                  " " +
                                  item.last_name +
                                  " " +
                                  item.position_name
                                  }}
                                </v-list-item-subtitle>
                              </v-list-item-content>
                            </template>
                            <template v-slot:item="{ item }">
                              <v-list-item-content>
                                <v-list-item-title v-text="item.code + ' ' + item.department_name"></v-list-item-title>
                                <v-list-item-subtitle>
                                  {{
                                  item.first_name +
                                  " " +
                                  item.last_name +
                                  " " +
                                  item.position_name
                                  }}
                                </v-list-item-subtitle>
                              </v-list-item-content>
                            </template>
                          </v-autocomplete>
                        </v-col>
                        <v-col cols="12" md="3" lg="2">
                          <v-btn
                            color="success"
                            outlined
                            block
                            @click="addSignerForControlPunkt"
                          >{{ $t("add") }}</v-btn>
                        </v-col>
                      </v-row>
                    </v-form>
                  </v-card-text>
                  <v-data-table
                    dense
                    fixed-header
                    class="mainTable ma-1"
                    :disable-pagination="true"
                    style="border: 1px solid #aaa"
                    :headers="headerSignerForPunkt"
                    :items="control_punkt.document_signers"
                    hide-default-footer
                  >
                    <template v-slot:item.id="{ item }">
                      {{
                      control_punkt.document_signers
                      .map(function (x) {
                      return x.id;
                      })
                      .indexOf(item.id) + 1
                      }}
                    </template>
                    <template v-slot:item.staff_name="{ item }">
                      <strong>{{ item.department }}</strong>
                      <br />
                      <strong>{{ item.position }}</strong>
                    </template>
                    <template v-slot:item.action="{ item }">
                      <v-btn
                        v-if="!item.status"
                        color="error"
                        icon
                        @click="deleteControlSigner(item)"
                      >
                        <v-icon color="error">mdi-delete</v-icon>
                      </v-btn>
                    </template>
                  </v-data-table>
                </v-card>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog v-model="modalComments" persistent width="600">
      <v-card>
        <v-card-title class="grey lighten-3">
          {{ $t("document.comment") }}
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="modalComments = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text class="pa-2">
          <v-row class="ma-0">
            <v-col cols="11" class="pa-1">
              <v-textarea
                :label="$t('document.comment')"
                v-model="commentText"
                rows="1"
                row-height="15"
                dense
                outlined
                auto-grow
                :loading="isLoading"
                hide-details="auto"
                @keyup="getComments()"
              ></v-textarea>
            </v-col>
            <v-col cols="1" class="pa-1">
              <v-btn icon outlined color="success" @click="addComment(commentText)">
                <v-icon>mdi-plus</v-icon>
              </v-btn>
            </v-col>
          </v-row>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-text class="pa-2">
          <v-simple-table dense>
            <template v-slot:default>
              <thead>
                <tr>
                  <th>{{ $t("document.comment") }}</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in comments" :key="item.index">
                  <td
                    @click="
                      control_punkt.content = item.text;
                      modalComments = false;
                      commentText = '';
                    "
                  >{{ item.text }}</td>
                  <td width="140">
                    <v-btn
                      small
                      text
                      color="primary"
                      @click="
                        control_punkt.content = control_punkt.content
                          ? control_punkt.content + ' ' + item.text + ' '
                          : item.text + ' ';
                        modalComments = false;
                        commentText = '';
                      "
                    >{{ $t("add") }}</v-btn>
                    <v-btn icon small color="error" @click="commentDelate(item)">
                      <v-icon small>mdi-delete</v-icon>
                    </v-btn>
                  </td>
                </tr>
              </tbody>
            </template>
          </v-simple-table>
        </v-card-text>
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

    <v-dialog v-model="pdfViewDialog" fullscreen>
      <v-card>
        <v-card-title primary-title>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined @click="pdfViewDialog = false">{{ $t("close") }}</v-btn>
        </v-card-title>
        <v-card-text class="d-flex justify-center">
          <iframe
            :width="screenWidth - 200"
            :height="screenHeight"
            :src="$store.state.backend_url + 'staffs/get-file/' + fileForView.id"
          ></iframe>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined @click="pdfViewDialog = false" class="mr-4">
            {{
            $t("close")
            }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
const axios = require("axios").default;
const moment = require("moment");
import pdf from "vue-pdf";
import Swal from "sweetalert2";
import Cookies from "js-cookie";

export default {
  components: {
    pdf
  },
  data() {
    return {
      searchTable: [],
      dialogReturnRajapov: false,
      isStar: 0,
      tableLists: [],
      reg_number: "",
      resolution_due_date: "",
      reg_date: "",
      zoom: 60,
      mainHeight: 0,
      pdfWidth: 0,
      pdfHeight: 0,
      fullScreenDialog: false,
      loadingTask: null,
      loadingTaskEimzo: null,
      numPages: null,
      numPagesEimzo: null,
      docB64: null,
      eiSigners: null,
      dialog: false,
      eimzo_username: null,
      eimzo_password: null,
      eimzo_name: null,
      active_sign: true,
      active_history: true,
      active_files: true,
      active_child_document: true,
      active_parent_document: true,
      active_edit_attribute: true,
      active_pending_action: true,
      active_watcher: true,
      active_resolution: true,
      active_blanks: true,
      base64: null,
      pdf_file_name: "",
      loading: false,
      isLoading: false,
      test: false,
      action_types: [
        {
          id: 2,
          name_uz_latin: "Tasdiq",
          name_uz_cyril: "Тасдиқ",
          name_ru: "Утверждение"
        },
        {
          id: 9,
          name_uz_latin: "Komissiya raisi",
          name_uz_cyril: "Комиссия раиси",
          name_ru: "Председатель комиссии"
        },
        {
          id: 8,
          name_uz_latin: "Komissiya a'zolari",
          name_uz_cyril: "Комиссия аъзолари",
          name_ru: "Члены комиссии"
        },
        {
          id: 12,
          name_uz_latin: "Kuzatuvchi",
          name_uz_cyril: "Кузатувчи",
          name_ru: "Наблюдатель"
        },
        {
          id: 10,
          name_uz_latin: "Komissiya kotibi",
          name_uz_cyril: "Комиссия котиби",
          name_ru: "Секретарь комиссии"
        },
        {
          id: 1,
          name_uz_latin: "Rozilik",
          name_uz_cyril: "Розилик",
          name_ru: "Согласование"
        },
        {
          id: 3,
          name_uz_latin: "Bo'lim ichida rozilik",
          name_uz_cyril: "Бўлим ичида розилик",
          name_ru: "Согласование внутри подразделение"
        },
        {
          id: 4,
          name_uz_latin: "Bajaruvchilar",
          name_uz_cyril: "Бажарувчилар",
          name_ru: "Исполнители"
        },
        {
          id: 16,
          name_uz_latin: "Bajaruvchilar(Yordamchi)",
          name_uz_cyril: "Бажарувчилар(Ёрдамчи)",
          name_ru: "Исполнители(Вспомогательный)"
        },
        {
          id: 11,
          name_uz_latin: "Nazoratchi",
          name_uz_cyril: "Назоратчи",
          name_ru: "Контрольщик"
        },
        {
          id: 5,
          name_uz_latin: "Ma'lumot uchun",
          name_uz_cyril: "Маълумот учун",
          name_ru: "Для информации"
        },
        // {
        //   id: 13,
        //   name_uz_latin: "Hujjat yaratuvchisi",
        //   name_uz_cyril: "Ҳужжат яратувчиси",
        //   name_ru: "Создатель документа",
        // },
        {
          id: 14,
          name_uz_latin: "Taqatuvchi",
          name_uz_cyril: "Тарқатувчи",
          name_ru: "Рассылки"
        }
      ],
      document: {},
      document_status: [
        {
          name_uz_latin: "yangi",
          name_uz_cyril: "янги",
          name_ru: "новый",
          color: "black"
        },
        {
          name_uz_latin: "E'lon qilish",
          name_uz_cyril: "Эьлон қилиш",
          name_ru: "опубликован",
          color: "cyan"
        },
        {
          name_uz_latin: "qayta ishlash",
          name_uz_cyril: "қайта ишлаш",
          name_ru: "обработка",
          color: "blue"
        },
        {
          name_uz_latin: "Imzolandi",
          name_uz_cyril: "Имзоланди",
          name_ru: "Подписано",
          color: "teal"
        },
        {
          name_uz_latin: "Bajarildi",
          name_uz_cyril: "Бажарилди",
          name_ru: "Выполнено",
          color: "amber"
        },
        {
          name_uz_latin: "Yakunlandi",
          name_uz_cyril: "Якунланди",
          name_ru: "Завершено",
          color: "success"
        },
        {
          name_uz_latin: "Bekor qilindi",
          name_uz_cyril: "Бекор қилинди",
          name_ru: "Отменен",
          color: "error"
        },
        {
          name_uz_latin: "Oldindan kelishuv",
          name_uz_cyril: "Олдидан килишув",
          name_ru: "Предсогласование",
          color: "light-green"
        }
      ],
      documentSigners: [],
      document_signers: [],
      document_locale: "",
      resolutionEmployees: [],
      childrenEmployees: [],
      resolution: {
        employees: [],
        assignment: "",
        due_date: "",
        action_type: null
      },
      subordinates: [],
      tree_ids: [],
      history: [],
      documentFiles: [],
      fileForView: { id: 0 },
      pdfViewDialog: false,
      modalEditAttribute: false,
      modalDocumentReaction: false,
      modalDocumentComment: false,
      modalDocumentConfirmation: false,
      modalToReturn: false,
      modelAddResolution: false,
      modelControlPunkt: false,
      modelControlPunktTitle: "",
      modalDocumentPreAgreement: false,
      dialogEditDocumentTitle: false,
      modalComments: false,
      documentComment: "",
      formData: [],
      tariff_scales: [],
      modalDocumentSubstantiate: null,
      modalDocumentSubstantiateComment: null,
      selectFiles: [],
      reaction: 0,
      reaction_status: 0,
      reaction_comment: "",
      reaction_eimzo: false,
      doc_signer_id: null,
      documentCommentFiles: [],
      form_attribute: [],
      edit_attribute: {},
      edit_attributes: [],
      returnEmployee: {},
      departments: [],
      control_departments: [],
      signatories: false,
      new_signer: {
        action_type_id: 5
      },
      search: "",
      sigsearch: "",
      resolutionTypes: [],
      commentSigner: {},
      docSigShow: false,
      watcher: false,
      rightMenu: false,
      substantiate: false,
      document_blank_templates: [],
      horizontalIcon: true,
      control_punkt: {
        id: Date.now(),
        document_signers: []
      },
      control_punkt_id: null,
      control_punkts: [],
      cp_content_show: [],
      priorities: [
        {
          id: 1,
          name_uz_latin: "Past",
          name_uz_cyril: "Паст",
          name_ru: "Низкий"
        },
        {
          id: 2,
          name_uz_latin: "Normal",
          name_uz_cyril: "Нормал",
          name_ru: "обычный"
        },
        {
          id: 3,
          name_uz_latin: "Yuqori",
          name_uz_cyril: "Юқори",
          name_ru: "Высокий"
        }
      ],
      punkt_types: [
        {
          id: 1,
          name_uz_latin: "Topshiriq",
          name_uz_cyril: "Топшириқ",
          name_ru: "Поручение"
        },
        {
          id: 2,
          name_uz_latin: "punktni tekshiring",
          name_uz_cyril: "punktni tekshiring",
          name_ru: "контрольный пункт"
        }
      ],
      journals: [
        {
          id: 1,
          name_uz_latin: "1-jurnal",
          name_uz_cyril: "1-jurnal",
          name_ru: "1-jurnal"
        },
        {
          id: 2,
          name_uz_latin: "1-jurnal",
          name_uz_cyril: "1-jurnal",
          name_ru: "1-jurnal"
        }
      ],
      headerSignerForPunkt: [
        { text: "#", value: "id", align: "center", width: 30, sortable: false },
        {
          text: this.$t("document.staff"),
          value: "staff_name",
          sortable: false
        },
        // {
        //   text: this.$t("document.signer_sequence"),
        //   value: "sequence",
        //   sortable: true,
        // },
        {
          text: this.$t("employee.info"),
          value: "fio",
          sortable: true
        },
        {
          text: this.$t("document.due_date"),
          value: "due_date",
          sortable: false
        },
        {
          text: "",
          value: "action",
          sortable: false,
          width: 30
        }
      ],
      comments: [],
      commentText: "",
      limit_due_date: 0
    };
  },
  watch: {
    modalDocumentReaction(value) {
      // console.log(value);
      this.AppLoad();
    },
    $route(to, from) {
      this.pdf_file_name = to.params.pdf_file_name;
      this.getList(false);
    }
  },
  computed: {
    pkcs7InfoSigners() {
      return this.pkcs7Info.signers;
    },
    screenWidth() {
      return window.innerWidth;
    },
    screenHeight() {
      return window.innerHeight - 80;
    },
    language() {
      return this.document_locale == "ru" ? "uz_cyril" : this.document_locale;
    },
    locale() {
      return this.$i18n.locale == "ru" ? "uz_cyril" : this.$i18n.locale;
    },
    user() {
      return this.$store.getters.getUser();
    }
  },
  methods: {
    stamp(){
      axios
          .get(this.$store.state.backend_url + "api/documents/add-stamp/"+this.document.id)
          .then(res => {
            this.getList(false);
          })
          .catch(err => {
            console.error(err);
          });
    },
    getTableList(table_index, table_list_id, search) {
      // console.log(table_index);
      if (table_list_id) {
        this.isLoading = true;
        axios
          .post(this.$store.state.backend_url + "api/document-table-list", {
            table_list_id: table_list_id,
            search: this.searchTable[table_index]
          })
          .then(res => {
            let tableList = res.data.table_list;
            tableList.map(v => {
              let search = "";
              res.data.columns.forEach(colum => {
                colum = colum.replace("locale", this.$i18n.locale);
                search = v[colum] ? search + " " + v[colum] : search;
              });
              v.search = search.trim().replace(/  /g, " ");
              return v;
            });
            this.tableLists[table_index] = tableList;
            // console.log(this.tableLists[table_index]);
            this.isLoading = false;
          })
          .catch(err => {
            console.error(err);
          });
        // console.log(table_index, table_list_id, search);
      }
    },
    getTariffScales(){
      this.loading = true;
      axios
        .get(this.$store.state.backend_url + "api/tariff-scales/get-tariff-scales")
        .then(res => {
          this.tariff_scales = res.data;
          this.loading = false;
        })
        .catch(err => {
          console.log(err);
          this.loading = false;
        });
    },
    returnDocument() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/documents/return-document", {
          document_id: this.document.id
        })
        .then(res => {
          this.getList(true);
          this.loading = false;
        })
        .catch(err => {
          console.log(err);
          this.loading = false;
        });
    },
    returnRajapov() {
      if (this.$refs.dialogReturnRajapov.validate()) {
        this.loading = true;
        axios
          .post(this.$store.state.backend_url + "api/document-restore", {
            document_id: this.document.id,
            comment: this.documentComment
          })
          .then(res => {
            this.documentComment = "";
            if (res.data) {
              this.doc_signer_id = res.data.id;
              // console.log(this.selectFiles.length);
              if (this.selectFiles.length) {
                this.addFiles();
              } else {
                this.getList();
              }
            }
            this.dialogReturnRajapov = false;
            this.loading = false;
          })
          .catch(err => {
            console.log(err);
            this.loading = false;
          });
      }
    },
    editSigners() {
      window.location.href =
        "https://edo.uzautomotors.com/#/document/signers/" + this.pdf_file_name;
    },
    removeCancelledDocument() {
      this.loading = true;
      axios
        .post(
          this.$store.state.backend_url +
            "api/documents/remove-cancelled-document",
          {
            id: this.document.id
          }
        )
        .then(res => {
          if (res.data) {
            location.reload();
          }
          this.loading = false;
        })
        .catch(err => {
          console.log(err);
          this.loading = false;
        });
    },
    star() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/documents/star", {
          id: this.document.id,
          isStar: this.isStar
        })
        .then(res => {
          this.isStar = res.data;
          this.loading = false;
        })
        .catch(err => {
          console.log(err);
          this.loading = false;
        });
    },
    is_star(id) {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/documents/is-star", {
          id: id
        })
        .then(res => {
          this.isStar = res.data;
          this.loading = false;
        })
        .catch(err => {
          console.log(err);
          this.loading = false;
        });
    },
    saveRegData() {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/documents/change-reg-data", {
          document_id: this.document.id,
          reg_number: this.reg_number,
          reg_date: this.reg_date
        })
        .then(res => {
          this.loading = false;
          this.getList(false);
        })
        .catch(err => {
          console.log(err);
          this.loading = false;
        });
    },
    addControlPunkt() {
      if (this.$refs.dialogControlForm.validate()) {
        let department = this.control_departments.find(v => {
          if (v.id == this.control_punkt.department_id) return v;
        });
        this.control_punkt.document_id = this.document.id;
        this.control_punkt.controller = {
          id: Date.now(),
          control_punkt_id: this.control_punkt.id,
          document_id: this.document.id,
          staff_id: department.manager_staff_id,
          action_type_id: 4,
          sequence: 0,
          sign_type: 1
        };
        this.loading = true;
        axios
          .post(
            this.$store.state.backend_url + "api/control-punkt/update",
            this.control_punkt
          )
          .then(res => {
            // console.log(res);
            this.getList();
            (this.control_punkt = {
              id: Date.now(),
              document_signers: []
            }),
              (this.loading = false);
          })
          .catch(err => {
            console.log(err);
            this.loading = false;
          });
        // console.log(this.control_punkt);
      }
    },
    addSignerForControlPunkt() {
      if (this.$refs.addSignerForControl.validate()) {
        let department = this.departments.find(v => {
          if (v.id == this.new_signer.department) return v;
        });
        if (
          this.control_punkt.document_signers.find(v => {
            if (v.staff_id == department.manager_staff_id) return v;
          })
        ) {
        } else {
          this.control_punkt.document_signers.push({
            id: Date.now(),
            control_punkt_id: this.control_punkt.id,
            document_id: this.document.id,
            staff_id: department.manager_staff_id,
            action_type_id: 4,
            sequence: 0,
            sign_type: 1,
            due_date: this.new_signer.due_date,
            department: department.department_name,
            department_code: department.code,
            position: department.position_name,
            fio:
              department.first_name.substr(0, 1) +
              "." +
              department.middle_name.substr(0, 1) +
              ". " +
              department.last_name
          });
          this.new_signer.department = null;
          this.$refs.addSignerForControl.reset();
        }
      }
    },
    editControlPunkt(item) {
      this.control_punkt = item;
      this.modelControlPunkt = true;
      this.modelControlPunktTitle = this.$t("control_punkt.edit");
      this.control_punkt.document_signers = this.document_signers.filter(v => {
        if (v.control_punkt_id == item.id && v.id != item.controller_id) {
          return v;
        }
      });
      this.control_punkt.document_signers.map(v => {
        v.fio =
          v.staff.employees[0]["firstname_" + this.$i18n.locale].substr(0, 1) +
          "." +
          v.staff.employees[0]["middlename_" + this.$i18n.locale].substr(0, 1) +
          ". " +
          v.staff.employees[0]["lastname_" + this.$i18n.locale];
      });
      this.sigsearch = item.controller.department;
      this.getForControlSigners();
      // console.log(this.control_punkt);
    },
    deleteControlSigner(item) {
      this.control_punkt.document_signers = this.control_punkt.document_signers.filter(
        v => {
          if (v.id != item.id) {
            return v;
          }
        }
      );
      axios
        .delete(
          this.$store.state.backend_url +
            "api/document-signers/delete/" +
            item.id
        )
        .then(res => {
          // console.log(res);
        })
        .catch(err => {
          console.log(err);
        });
      // console.log(item);
    },
    preAgreement() {
      Swal.fire({
        title: this.$t("swal_title"),
        text: this.$t("swal_text"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: this.$t("swal_ok")
      }).then(result => {
        if (result.value) {
          axios
            .get(
              this.$store.state.backend_url +
                "api/document/pre-agreement/" +
                this.document.id
            )
            .then(res => {
              // console.log(res);
              this.getList(false);
            });
        }
      });
    },
    reactPreAgreement() {
      this.loading = true;
      axios
        .post(
          this.$store.state.backend_url + "api/document-signers/pre-agreement",
          {
            document_id: this.document.id,
            comment: this.documentComment
          }
        )
        .then(res => {
          this.documentComment = "";
          if (res.data) {
            this.doc_signer_id = res.data.id;
            // console.log(this.selectFiles.length);
            if (this.selectFiles.length) {
              this.addFiles();
            }
            this.getList(false);
            this.getNotifications();
          }
          this.modalDocumentPreAgreement = false;
          this.loading = false;
        })
        .catch(err => {
          console.log(err);
          this.loading = false;
        });
    },
    getComments() {
      this.modalComments = true;
      this.isLoading = true;
      axios
        .post(this.$store.state.backend_url + "api/get-comments", {
          search: this.commentText
        })
        .then(res => {
          this.comments = res.data.data;
          this.isLoading = false;
          // console.log(res);
        })
        .catch(err => {
          console.log(err);
        });
    },
    addComment(text) {
      axios
        .post(this.$store.state.backend_url + "api/comment/update", {
          text: text
        })
        .then(res => {
          // console.log(res);
          this.commentText = "";
          this.getComments();
        })
        .catch(err => {
          console.log(err);
        });
    },
    commentDelate(item) {
      this.isLoading = true;
      axios
        .delete(this.$store.state.backend_url + "api/comment/delete/" + item.id)
        .then(res => {
          this.isLoading = false;
          this.comments = this.comments.filter(v => {
            if (v.id != item.id) return v;
          });
          // console.log(res);
        })
        .catch(err => {
          console.log(err);
        });
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
    blankDownload(blank, document_detail, employee) {
      // console.log(blank, document_detail, employee);
      let form_blank = {};
      form_blank.file_type = blank.blank_template.file_type;
      form_blank.blank_attribute_templates = [];
      blank.document_blank_attribute.forEach(element => {
        let blank_attribute_templates = {
          set_name: ""
        };
        blank_attribute_templates.blank_id = blank.blank_id;
        blank_attribute_templates.id = element.blank_attribute_template.id;
        blank_attribute_templates.data_type_id =
          element.blank_attribute_template.data_type_id;
        blank_attribute_templates.parameter_name =
          element.blank_attribute_template.parameter_name;
        if (element.relation_type == 1 && employee) {
          if (element.relation_attribute == "full_fio") {
            blank_attribute_templates.set_name = employee.employee_fio;
          } else if (element.relation_attribute == "short_fio") {
            blank_attribute_templates.set_name =
              employee.employee.firsname.substring(0, 1) +
              "." +
              employee.employee.middlename.substring(0, 1) +
              ". " +
              employee.employee.lastname;
          } else if (element.relation_attribute == "tabel") {
            blank_attribute_templates.set_name = employee.employee.tabel;
          } else if (element.relation_attribute == "pass_seria") {
            blank_attribute_templates.set_name =
              employee.employee.employee_official_document &&
              employee.employee.employee_official_document.length
                ? employee.employee.employee_official_document.find(v => {
                    if (v.official_document_type_id == 1) return v;
                  }).series
                : "";
          } else if (element.relation_attribute == "pass_number") {
            blank_attribute_templates.set_name =
              employee.employee.employee_official_document &&
              employee.employee.employee_official_document.length
                ? employee.employee.employee_official_document.find(v => {
                    if (v.official_document_type_id == 1) return v;
                  }).number
                : "";
          } else if (element.relation_attribute == "staff") {
            blank_attribute_templates.set_name =
              employee.employee_department + " " + employee.employee_position;
          } else if (element.relation_attribute == "department") {
            blank_attribute_templates.set_name = employee.employee_department;
          } else if (element.relation_attribute == "position") {
            blank_attribute_templates.set_name = employee.employee_position;
          } else if (element.relation_attribute == "ip_telefon") {
            blank_attribute_templates.set_name =
              employee.employee.employee_phones &&
              employee.employee.employee_phones.length
                ? employee.employee.employee_phones.find(v => {
                    if (v.phone_type == "Ip") return v;
                  }).phone_number
                : "";
          } else if (element.relation_attribute == "telefon") {
            blank_attribute_templates.set_name =
              employee.employee.employee_phones &&
              employee.employee.employee_phones.length
                ? employee.employee.employee_phones.find(v => {
                    if (v.phone_type == "Mobile") return v;
                  }).phone_number
                : "";
          }
        } else if (element.relation_type == 2) {
          let attribute = document_detail.document_detail_contents.find(v => {
            if (v.d_d_attribute_id == element.relation_attribute) return v;
          });
          if (element.date_format && attribute) {
            if (element.date_format == 1) {
              blank_attribute_templates.set_name = attribute.value;
            } else if (element.date_format == 2) {
              blank_attribute_templates.set_name = moment(
                attribute.value
              ).format("DD");
            } else if (element.date_format == 3) {
              blank_attribute_templates.set_name = moment(
                attribute.value
              ).format("MM");
            } else if (element.date_format == 4) {
              blank_attribute_templates.set_name = moment(
                attribute.value
              ).format("YYYY");
            }
          } else {
            blank_attribute_templates.set_name = attribute
              ? attribute.value
              : "";
          }
        } else if (element.relation_type == 3) {
          if (element.relation_attribute == "doc_date") {
            if (element.date_format == 1) {
              blank_attribute_templates.set_name = this.document.document_date;
            } else if (element.date_format == 2) {
              blank_attribute_templates.set_name = moment(
                this.document.document_date
              ).format("DD");
            } else if (element.date_format == 3) {
              blank_attribute_templates.set_name = moment(
                this.document.document_date
              ).format("MM");
            } else if (element.date_format == 4) {
              blank_attribute_templates.set_name = moment(
                this.document.document_date
              ).format("YYYY");
            }
          } else if (element.relation_attribute == "doc_number") {
            blank_attribute_templates.set_name = this.document.document_number;
          } else if (element.relation_attribute == "doc_signer") {
            let document_signer = this.document.document_signers.find(v => {
              if (v.action_type_id == 2 || v.action_type_id == 6) return v;
            });
            blank_attribute_templates.set_name =
              document_signer.signer_employee.firstname_uz_latin.substr(0, 1) +
              ". " +
              document_signer.signer_employee.middlename_uz_latin.substr(0, 1) +
              ". " +
              document_signer.signer_employee.lastname_uz_latin +
              " " +
              document_signer.signed_at;
          }
        }
        form_blank.blank_attribute_templates.push(blank_attribute_templates);
      });
      axios
        .post(
          this.$store.state.backend_url + "api/blank-templates/download",
          form_blank
        )
        .then(res => {
          let downloadUrl = this.$store.state.backend_url + res.data;
          window.open(downloadUrl);
          axios.post(
            this.$store.state.backend_url + "api/blank-templates/delete-file",
            {
              file: res.data
            }
          );
        });
      // console.log(form_blank);
    },
    downloadPdf() {
      const linkSource = `data:application/pdf;base64,` + this.base64;
      const downloadLink = document.createElement("a");
      const fileName = (this.document.document_number_reg ? this.document.document_number_reg : this.document.document_number) + ".pdf";

      downloadLink.href = linkSource;
      downloadLink.download = fileName;
      downloadLink.click();
      // window.open("data:application/pdf;base64," + this.base64);
    },
    downloadPdfWithComment() {
      axios
        .get(this.$store.state.backend_url + "api/documents/get-pdf-with-comments/"+this.document.id)
        .then(res => {
          const linkSource = `data:application/pdf;base64,` + res.data;
          const downloadLink = document.createElement("a");
          const fileName = this.document.document_number + ".pdf";

          downloadLink.href = linkSource;
          downloadLink.download = fileName;
          downloadLink.click();
        });
      // window.open("data:application/pdf;base64," + this.base64);
    },
    setCookie() {
      this.rightMenu = !this.rightMenu;
      Cookies.set("right_menu", this.rightMenu);
      // console.log(Cookies.get('right_menu'));
    },
    changeSize() {
      this.horizontalIcon = !this.horizontalIcon;
      //70/99
      if (this.pdfWidth == window.innerWidth - 200) {
        this.pdfWidth = window.innerHeight * 0.65;
      } else {
        this.pdfWidth = window.innerWidth - 200;
      }
    },
    getFile() {
      this.loading = true;
      axios
        .get(
          "https://b-edo.uzautomotors.com/api/documents/eimzoinfo/" +
            this.document.id
        )
        .then(res => {
          if (res.data && res.data.documentBase64 && res.data.signers) {
            this.document.eimzoInfo = res.data;
            this.docB64 = atob(res.data.documentBase64);
            this.loadingTaskEimzo = pdf.createLoadingTask(
              "data:application/pdf;base64," + this.docB64
            );
            this.loadingTaskEimzo.promise.then(pdf => {
              this.numPagesEimzo = pdf.numPages;
            });
            this.eiSigners = res.data.signers.sort((a, b) => {
              if (a.signingTime > b.signingTime) {
                return -1;
              } else if (a.signingTime < b.signingTime) {
                return 1;
              }
              return 0;
            });
            // console.log(this.eiSigners);
            this.dialog = true;
            this.error = "";
          }
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
          this.loading = false;
        });
    },
    addFiles() {
      // console.log(this.selectFiles);
      this.selectFiles.forEach((v, i) => {
        this.formData.append("files[]", v);
        this.documentCommentFiles.push({
          id: Date.now() + Math.floor(Math.random() * 1000),
          file_name: v.name
        });
      });
      this.selectFiles = [];
      axios
        .post(
          this.$store.state.backend_url +
            "api/documents/comment-files/" +
            this.doc_signer_id,
          this.formData,
          {
            headers: {
              "Content-Type": "multipart/form-data"
            }
          }
        )
        .then(res => {
          this.loading = false;
          this.getDocumentHistory();
        })
        .catch(err => {
          console.error(err);
          this.loading = false;
        });
      this.formData = new FormData();
      this.modalDocumentFile = false;
    },
    addSigners() {
      // console.log(this.new_signer.department, this.document.id);
      if (this.$refs.dialogForm.validate()) {
        axios
          .post(this.$store.state.backend_url + "api/documents/add_signer", {
            document_id: this.document.id,
            department_id: this.new_signer.department,
            action_type_id: this.new_signer.action_type_id,
            due_date: this.new_signer.due_date ? this.new_signer.due_date : 24
          })
          .then(res => {
            // console.log(res.data);
            if (res.data == 1) {
              Swal.fire("Kuzatuvchi qo'shilgan!!!");
            } else {
              this.getList(false);
            }
          })
          .catch(err => {
            console.log(err);
          });
        this.signatories = false;
      }
    },
    editAttribute(att) {
      axios
        .post(
          this.$store.state.backend_url + "api/documents/edit-attribute",
          att
        )
        .then(res => {
          this.modalEditAttribute = false;
          // if (res.data) {
          this.getList(false);
          // }
        })
        .catch(err => {
          console.error(err);
        });
    },
    deleteResolutionEmployee(data) {
      // console.log(data);
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
                "api/document-signers/delete-signer/" +
                data.id
            )
            .then(res => {
              // console.log(res);
              this.getList(false);
              this.document.resolution_employee = this.document.resolution_employee.filter(
                v => {
                  return !(
                    v.parent_employee_id == data.parent_employee_id &&
                    v.signer_employee_id == data.signer_employee_id
                  );
                }
              );
            });
        }
      });
    },
    //------------- E-imzo ---------------
    AppLoad() {
      EIMZOClient.API_KEYS = [
        this.$store.state.EIMZO_DOMAIN,
        this.$store.state.EIMZO_API_KEY
      ];

      let EIMZO_MAJOR = this.EIMZO_MAJOR;
      let EIMZO_MINOR = this.EIMZO_MINOR;
      let uiLoadKeys = this.uiLoadKeys;
      EIMZOClient.checkVersion(
        function(major, minor) {
          var newVersion = EIMZO_MAJOR * 100 + EIMZO_MINOR;
          var installedVersion = parseInt(major) * 100 + parseInt(minor);

          EIMZOClient.installApiKeys(
            function() {
              uiLoadKeys();
            },
            function(e, r) {}
          );
        },
        function(e, r) {
          if (r) {
          } else {
          }
        }
      );
    },
    uiLoadKeys() {
      this.uiClearCombo();
      let uiCreateItem = this.uiCreateItem;
      let uiShowMessage = this.uiShowMessage;
      let eimzo_password = this.$store.getters.getUser().eimzo_password;
      let getUserAuth = this.getUserAuth;
      let reactionEimzo = this.reactionEimzo;
      if (document.testform)
        EIMZOClient.listAllUserKeys(
          function(o, i) {
            var itemId = "itm-" + o.serialNumber + "-" + i;
            return itemId;
          },
          function(itemId, v) {
            return uiCreateItem(itemId, v);
          },
          function(items, firstId) {
            var combo = document.testform.key;
            var option = document.createElement("option");
            option.text = "select";
            combo.add(option);
            combo.append(<option value="">Select</option>);
            for (var itm in items) {
              // var id = document.getElementById(itm);
              var vo = items[itm].getAttribute("vo");
              // console.log(!JSON.parse(vo).expired);
              
              if (vo.includes(eimzo_password) ) {
                items[itm].setAttribute("selected", "true");
                combo.append(items[itm]);
                getUserAuth();
                // var id = document.getElementById("12345");
                // id.setAttribute("selected", "true");
                // console.log(true);
                reactionEimzo();
              }
            }
            // if (firstId) {
            // }
          },
          function(e, r) {}
        );
    },
    cbChanged(c) {
      document.getElementById("keyId").innerHTML = "";
      this.getUserAuth();
    },
    uiClearCombo() {
      if (document.testform) {
        var combo = document.testform.key;
        combo.length = 0;
      }
    },
    reactionEimzo() {
      this.reaction_eimzo = true;
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
    getUserAuth() {
      var itm = document.testform.key.value;
      let localStorage = window.localStorage;
      // let eimzo_key = localStorage.getItem('eimzo_key');
      let store = this.$store;
      let eimzo_key = this.$store.getters.getEimzoKey();
      if (eimzo_key == null || eimzo_key == "null") {
        var id = document.getElementById(itm);
        var vo = JSON.parse(id.getAttribute("vo"));
        EIMZOClient.loadKey(
          vo,
          function(id) {
            // localStorage.setItem('eimzo_key',id);
            store.commit("setEimzoKey", id);
            eimzo_key = id;
            //document.getElementById("keyId").innerHTML = id;
            // console.log(id);
          },
          function(e, r) {}
        );
      }

      var itm = document.testform.key.value;
      var id = document.getElementById(itm);
      if (id && id.hasAttribute("vo")) {
        var vo = JSON.parse(id.getAttribute("vo"));
        this.eimzo_username = vo.name;
        this.eimzo_name = vo.CN;
        this.eimzo_password = vo.serialNumber;
        // console.log(vo.name, " - ", vo.UID, " - ", vo.CN);
      } else {
        this.eimzo_username = "";
        this.eimzo_name = "";
        this.eimzo_password = "";
      }
    },
    setBase64(base64) {
      // this.base64 = base64;
      this.document.base64 = base64;
      axios.post(this.$store.state.backend_url + "api/documents/set-base64", {
        document_id: this.document.id,
        base64: base64
      });
    },
    getEimzoSigners() {
      let setPkcs7Info = this.setPkcs7Info;
      let base64 = this.document.base64;
      if (base64 != null)
        CAPIWS.callFunction(
          {
            plugin: "pkcs7",
            name: "get_pkcs7_attached_info",
            arguments: [
              //Ранее созданный документ PKCS#7/CMS в кодировке BASE64
              // this.document.base64,
              base64,
              //Идентификатор хранилища доверенных сертификатов (полученный из фукнции других плагинов), если требуется верификация сертификатов, иначе может быть пустым
              ""
            ]
          },
          function(event, data) {
            setPkcs7Info(data.pkcs7Info);
          },
          function(error) {
            // window.alert(error);
          }
        );
      else alert("Ushbu dokumentga hali imzo chekilmagan.");
    },
    setPkcs7Info(pkcs7Info) {
      this.pkcs7Info = pkcs7Info;
      this.pkcs7InfoDialog = true;
    },
    sign(reac) {
      let documentReaction = this.documentReaction;
      var itm = document.testform.key.value;
      let store = this.$store;
      if (itm) {
        var id = document.getElementById(itm);
        var vo = JSON.parse(id.getAttribute("vo"));
        var data = this.document.pdf;
        var doc = this.document;
        let localStorage = window.localStorage;
        var keyId = localStorage.getItem("eimzo_key");
        let eimzo_key = store.getters.getEimzoKey();
        // var keyId = Cookies.get("eimzo_key");
        var setBase64 = this.setBase64;
        var publish = this.publish;
        var modalDocumentReaction = this.modalDocumentReaction;
        if (data == null) alert("PDF not generated.");
        else if (eimzo_key != null) {
          EIMZOClient.createPkcs7(
            eimzo_key,
            data,
            null,
            function(pkcs7) {
              setBase64(pkcs7);
              if (doc.status == 0) {
                modalDocumentReaction = false;
                publish();
              } else {
                documentReaction(reac, vo.serialNumber);
              }
            },
            function(e, r) {
              if (r) {
                if (r.indexOf("BadPaddingException") != -1) {
                } else {
                }
              } else {
                document.getElementById("keyId").innerHTML = "";
              }
            }
          );
        } else {
          EIMZOClient.loadKey(
            vo,
            function(id) {
              //document.getElementById("keyId").innerHTML = id;
              // Cookies.set("eimzo_key", id, { expires: 1 / 4 });
              // localStorage.setItem('eimzo_key',id);
              store.dispatch("setEimzoKey", id);
              eimzo_key = id;
              EIMZOClient.createPkcs7(
                eimzo_key,
                data,
                null,
                function(pkcs7) {
                  // document.testform.pkcs7.value = pkcs7;
                  setBase64(pkcs7);
                  if (doc.status == 0) {
                    modalDocumentReaction = false;
                    publish();
                  } else {
                    documentReaction(reac, vo.serialNumber);
                  }
                },
                function(e, r) {
                  if (r) {
                    if (r.indexOf("BadPaddingException") != -1) {
                    } else {
                    }
                  } else {
                    document.getElementById("keyId").innerHTML = "";
                  }
                }
              );
            },
            function(e, r) {
              if (r) {
                if (r.indexOf("BadPaddingException") != -1) {
                } else {
                }
              } else {
              }
            }
          );
        }
      }
    },
    verify(reac) {
      let documentReaction = this.documentReaction;
      if (this.$refs.reactionForm.validate()) {
        // if (this.document.sign_type) {
        var itm = document.testform.key.value;
        var id = document.getElementById(itm);
        var vo = JSON.parse(id.getAttribute("vo"));
        // let eimzo_key = "";
        let swal = this.Swal;
        let setBase64 = this.setBase64;
        let base64 = this.document.base64;
        let sign = this.sign;
        let localStorage = window.localStorage;
        var eimzo_key = localStorage.getItem("eimzo_key");
        if (!eimzo_key) {
          EIMZOClient.loadKey(
            vo,
            function(id) {
              eimzo_key = id;
              localStorage.setItem("eimzo_key", id);
            },
            function(e, r) {}
          );

          CAPIWS.callFunction(
            {
              plugin: "pfx",
              name: "verify_password",
              arguments: [eimzo_key]
            },
            function(event, data) {
              // Cookies.set("eimzo_key", eimzo_key, { expires: 1 / 4 });

              if (base64 == null) sign(reac);
              else
                CAPIWS.callFunction(
                  {
                    plugin: "pkcs7",
                    name: "append_pkcs7_attached",
                    arguments: [base64, eimzo_key]
                  },
                  function(event, data) {
                    if (data["success"]) {
                      documentReaction(reac, data["signer_serial_number"]);
                      setBase64(data["pkcs7_64"]);
                    }
                  },
                  function(error) {}
                );
            },
            function(error) {
              window.alert(error);
            }
          );
        } else {
          if (base64 == null) sign(reac);
          else
            CAPIWS.callFunction(
              {
                plugin: "pkcs7",
                name: "append_pkcs7_attached",
                arguments: [base64, eimzo_key]
              },
              function(event, data) {
                if (data["success"]) {
                  documentReaction(reac, data["signer_serial_number"]);
                  setBase64(data["pkcs7_64"]);
                }
              },
              function(error) {}
            );
        }
        // } else {
        //   documentReaction(reac, null);
        // }
      }
    },
    //------------- /E-imzo --------------
    getNumber() {
      this.sign(1);
      // this.loading = true;
      // if (this.document.document_number != "YYXX-0000-0000") {
      //   this.loading = false;
      //   this.sign(1);
      // } else
      //   axios
      //     .post(
      //       this.$store.state.backend_url + "api/documents/get-number/" + this.document.id
      //     )
      //     .then((res) => {
      //       this.document.pdf = res.data.pdf;
      //       this.base64 = res.data.pdf;
      //       this.document.base64 = res.data.base64;
      //       this.sign(1);
      //       this.loading = false;
      //     })
      //     .catch((err) => {
      //       console.error(err);
      //       this.loading = false;
      //     });
    },
    publish() {
      this.loading = true;
      axios
        .post(
          this.$store.state.backend_url +
            "api/documents/publish/" +
            this.document.id
        )
        .then(res => {
          this.loading = false;
          this.modalDocumentReaction = false;
          this.$router.push("/documents/list/outbox/0");
        })
        .catch(err => {
          console.error(err);
          this.loading = false;
        });

      // Swal.fire({
      //   title: this.$t("swal_title"),
      //   text: this.$t("swal_text"),
      //   icon: "warning",
      //   showCancelButton: true,
      //   confirmButtonColor: "#4caf50",
      //   cancelButtonColor: "#d33",
      //   confirmButtonText: this.$t("publish"),
      // }).then((result) => {
      //   if (result.value) {
      //   }
      // });
    },
    commentSubstantiate(signer_id) {
      this.processing();
      this.documentComment =
        "Ушбу ҳужжат асослаб берилсин! Прошу обосновать документ! \n" +
        this.modalDocumentSubstantiateComment;
      this.substantiate = true;
      this.comment(signer_id);
    },
    comment(signer_id) {
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/document-signers/comment", {
          document_id: this.document.id,
          comment: this.documentComment,
          substantiate: this.substantiate,
          signer_id: signer_id
        })
        .then(res => {
          this.documentComment = "";
          if (res.data) {
            this.doc_signer_id = res.data.id;
            // console.log(this.selectFiles.length);
            if (this.selectFiles.length) {
              this.addFiles();
            } else {
              this.getDocumentHistory();
            }
          }
          this.modalDocumentComment = false;
          this.loading = false;
          if (this.substantiate == true) {
            this.$router.back();
          }
        })
        .catch(err => {
          console.log(err);
          this.loading = false;
        });
    },
    confirmation(reac) {
      Swal.fire({
        title: this.$t("swal_title"),
        text: this.$t("swal_text"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#4caf50",
        cancelButtonColor: "#d33",
        confirmButtonText:
          reac == 1
            ? this.$t("document.confirmation")
            : this.$t("document.return_to")
      }).then(result => {
        if (result.value) {
          // console.log(this.document.sign_type, 6546);
          this.loading = true;
          axios
            .post(
              this.$store.state.backend_url +
                "api/document-signers/confirmation",
              {
                document_id: this.document.id,
                description: this.documentComment,
                status: reac,
                sign_type: 1
              }
            )
            .then(res => {
              this.documentComment = "";
              this.getDocumentList();
              this.getNotifications();
              this.getList(false);
              this.modalDocumentConfirmation = false;
              this.loading = false;
            })
            .catch(err => {
              console.log(err);
              this.loading = false;
            });
        }
      });
    },
    documentReaction(reac, signer_serial_number) {
      // console.log(this.document.sign_type, 6546);
      this.loading = true;
      axios
        .post(this.$store.state.backend_url + "api/document-signers/reaction", {
          document_id: this.document.id,
          description: this.documentComment,
          status: reac,
          sign_type: 1,
          signer_serial_number
        })
        .then(res => {
          this.doc_signer_id = res.data.signer_event_id;
          this.documentComment = "";
          this.getDocumentList();
          this.getNotifications();
          this.getList(false);
          if (this.selectFiles.length) {
            this.addFiles();
          }
          window.history.back();
          this.modalDocumentReaction = false;
          this.loading = false;
        })
        .catch(err => {
          console.log(err);
          this.loading = false;
        });
    },
    outOfControl() {
      Swal.fire({
        title: this.$t("swal_title"),
        text: this.$t("swal_text"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: this.$t("document.out_of_control")
      }).then(result => {
        if (result.value) {
          axios
            .post(
              this.$store.state.backend_url + "api/document/out_of_control",
              {
                document_id: this.document.id
              }
            )
            .then(res => {
              window.history.back();
            });
        }
      });
    },
    processing() {
      // console.log([this.document.reaction_status, this.document.status]);
      if (!this.document.reaction_status && this.document.status != 6) {
        this.loading = true;
        axios
          .post(
            this.$store.state.backend_url + "api/document-signers/processing",
            {
              document_id: this.document.id,
              comment: "processing"
            }
          )
          .then(res => {
            if (res.data) {
              // this.$router.back();
              // this.getDocumentList();
              // this.getNotifications();
              this.getList(false);
            }
            this.loading = false;
          })
          .catch(err => {
            console.log(err);
            this.loading = false;
          });
      }
    },
    toReturn(signer_id) {
      // console.log(signer_id);
      this.loading = true;
      axios
        .post(
          this.$store.state.backend_url + "api/document-signers/to-return",
          {
            document_id: this.document.id,
            description: this.documentComment,
            signer_id: signer_id
          }
        )
        .then(res => {
          this.documentComment = "";
          this.getList(false);
          this.getDocumentList();
          this.getNotifications();
          this.modalToReturn = false;
          this.loading = false;
        })
        .catch(err => {
          console.log(err);
          this.loading = false;
        });
    },
    getForControlSigners() {
      this.isLoading = true;
      axios
        .post(this.$store.state.backend_url + "api/departments/list", {
          search: this.sigsearch,
          locale: this.$i18n.locale
        })
        .then(res => {
          this.control_departments = res.data.data.map(v => {
            v.text =
              v.code +
              " " +
              v.department_name +
              " " +
              v.first_name +
              " " +
              v.last_name;
            return v;
          });
          // console.log(this.departments);
          this.isLoading = false;
        })
        .catch(err => {
          console.error(err);
          this.isLoading = false;
        });
    },
    getSigners() {
      this.isLoading = true;
      axios
        .post(this.$store.state.backend_url + "api/departments/list", {
          search: this.search,
          locale: this.$i18n.locale
        })
        .then(res => {
          this.departments = res.data.data.map(v => {
            v.text =
              v.code +
              " " +
              v.department_name +
              " " +
              v.first_name +
              " " +
              v.last_name;
            return v;
          });
          // console.log(this.departments);
          this.isLoading = false;
        })
        .catch(err => {
          console.error(err);
          this.isLoading = false;
        });
    },
    getList(refreshPdf) {
      this.loading = true;
      this.attributes = [];
      this.document = {};
      axios
        .post(
          this.$store.state.backend_url + "api/documents/show_new-document",
          {
            pdf_file_name: this.pdf_file_name,
            refresh_pdf: refreshPdf
          }
        )
        .then(res => {
          this.is_star(res.data.document.id);
          this.document = res.data.document;
          let i = 0;
            this.document.document_details.forEach(
              (document_detail, document_detail_index) => {
                document_detail.document_detail_attribute_values.forEach(
                  (element, index) => {
                    if (element.table_lists) {
                      let colums = element.document_detail_attributes.table_list.column_name.split(
                        ", "
                      );
                      let search = "";
                      colums.forEach(colum => {
                        colum = colum.replace("locale", this.$i18n.locale);
                        search = element.table_lists[0][colum]
                          ? search + " " + element.table_lists[0][colum]
                          : search;
                      });
                      element.table_lists.map(v => {
                        v.search = search.trim().replace(/  /g, " ");
                      });
                      this.tableLists[index + "_" + document_detail_index] =
                        element.table_lists;
                    }
                    if (document_detail_index == 0 && this.document_detail_attributes) {
                      this.document_detail_attributes.push(
                        element.document_detail_attributes
                      );
                    }
                    i++;
                  }
                );
              }
            );
          this.base64 = res.data.document.pdf;
          this.loadingTask = pdf.createLoadingTask(
            "data:application/pdf;base64," + this.base64
          );
          this.loadingTask.promise.then(pdf => {
            this.numPages = pdf.numPages;
          });
          this.control_punkts = res.data.control_punkts;
          this.control_punkts.map((v, index) => {
            this.cp_content_show[index] = true;
          });
          let reg_date = this.document.document_date_reg
            ? this.document.document_date_reg
            : this.document.document_date;
          this.reg_date = reg_date.substring(0, 10);
          this.reg_number = this.document.document_number_reg
            ? this.document.document_number_reg
            : this.document.document_number;
          this.documentSigners = this.document.document_signers;
          this.document_signers = res.data.document_signers;
          let userstaffs = [];
          this.user.employee.employee_staff.map(v => {
            userstaffs.push(v.staff_id);
          });
          this.documentSigners.map(v => {
            v.signed_date = v.signed_date
              ? moment(v.signed_date * 1000).format("DD.MM.YYYY hh:mm")
              : null;
            if (
              userstaffs.find(va => va == v.staff_id) &&
              v.status != 1 &&
              v.status != 2
            ) {
              let due_date = new Date(v.due_date).getTime();
              let now_date = new Date().getTime();
              this.limit_due_date = due_date - now_date;
              this.limit_due_date = Math.round(
                this.limit_due_date / 1000 / 60 / 60
              );
            }
          });
          this.document_locale = this.document.locale;
          this.resolutionEmployees = res.data.resolutionEmployee;
          this.documentFiles = res.data.document_files;
          this.resolutionTypes = res.data.resolutionTypes;
          this.resolution.document_id = this.document.id;
          this.resolution.sequence = this.document.sequence;
          this.reaction_status = this.document.reaction_status;
          this.document_blank_templates = res.data.document_blank_templates;
          this.edit_attributes = [];
          this.document.document_details.map(v => {
            if (v.document_detail_signer_attributes.length) {
              if(v.document_detail_signer_attributes.some(ddsa => ddsa.document_detail_attributes.data_type_id == 6) && v.document_detail_signer_attributes.some(ddsa => ddsa.document_detail_attributes.table_list_id == 5)){
                  this.getTariffScales();
              }
              this.edit_attributes.push({
                id: v.id,
                document_detail_employees: v.document_detail_employees,
                document_detail_signer_attributes:
                  v.document_detail_signer_attributes
              });
            }
          });
          // console.log(this.edit_attributes);
          // this.edit_attributes = res.data.edit_attributes;
          this.getDocumentDetailTemplate();
          this.document.document_signers.map(v => {
            // if (v.action_type_id == 6) this.creatorSigner = v;
            userstaffs.forEach(userstaff => {
              if (
                userstaff == v.staff_id &&
                v.action_type_id == 11 &&
                v.taken_datetime
              ) {
                if (v.control_punkt_id) {
                  this.control_punkt_id = v.control_punkt_id;
                } else {
                  this.control_punkt_id = null;
                }
                this.watcher = true;
                // console.log(v.control_punkt_id);
              }
            });
          });
          this.loading = false;
          this.getDocumentHistory();
        })
        .catch(err => {
          console.log(err);
          this.loading = false;
        });
    },
    getDocumentDetailTemplate() {
      // this.star();
      axios
        .post(
          this.$store.state.backend_url +
            "api/document-template/signed-attribute",
          {
            document_template_id: this.document.document_template_id,
            document_id: this.document.id
          }
        )
        .then(res => {
          if (res.data) {
            this.getList(false);
          }
        })
        .catch(err => {
          console.error(err);
        });
    },
    momentTime(time) {
      return moment(time).format("DD.MM.YYYY hh:mm");
    },
    getDocumentHistory() {
      axios
        .get(
          this.$store.state.backend_url +
            "api/documents/history/" +
            this.document.id
        )
        .then(res => {
          this.history = res.data.history;
          this.tree_ids = res.data.tree_ids;
        })
        .catch(err => {
          console.log(err);
        });
    },
    getDocumentList() {
      axios
        .get(this.$store.state.backend_url + "api/documents/list")
        .then(response => {
          let document_list = response.data;
          document_list.map(v => {
            v.visible = this.$store.getters.checkPermission(
              "document-list-" + v.menu_item
            );
            return v;
          });
          this.$store.dispatch("setDocumentList", document_list);
          // console.log(this.document_list);
        })
        .catch(error => {
          console.log(error);
        });
    },
    getNotifications() {
      axios
        .get(
          this.$store.state.backend_url +
            "api/documents/notification/" +
            this.$i18n.locale
        )
        .then(res => {
          // this.notifications = res.data;
          this.$store.dispatch("setNotifications", res.data);
        })
        .catch(err => {
          console.log(err);
        });
    },
    getResolutionEmployees() {
      this.isLoading = true;
      axios
        .post(
          this.$store.state.backend_url + "api/document/resolution-employees",
          {
            search: this.search
          }
        )
        .then(res => {
          this.childrenEmployees = res.data.data;
          let lang = this.$i18n.locale == "ru" ? "uz_cyril" : this.$i18n.locale;
          // if(this.resolutionEmployees.length){
          //   this.childrenEmployees = this.childrenEmployees.filter(v=>{
          //     if(!this.resolutionEmployees.find(va=>{if(va.signer_employee_id == v.id) return va;}))
          //       return v;
          //   })
          // }
          this.childrenEmployees.map(v => {
            v.fio =
              v["firstname_" + lang] +
              " " +
              v["lastname_" + lang] +
              " " +
              v["middlename_" + lang];
            v.staff = v.main_staff[0]
              ? v.main_staff[0].position["name_" + this.$i18n.locale]
              : "";
            v.search =
              v.tabel +
              " " +
              v.firstname_uz_latin +
              " " +
              v.lastname_uz_latin +
              " " +
              v.middlename_uz_latin +
              " " +
              v.firstname_uz_cyril +
              " " +
              v.lastname_uz_cyril +
              " " +
              v.middlename_uz_cyril +
              " " +
              v.lastname_uz_latin +
              " " +
              v.firstname_uz_latin +
              " " +
              v.middlename_uz_latin +
              " " +
              v.lastname_uz_cyril +
              " " +
              v.firstname_uz_cyril +
              " " +
              v.middlename_uz_cyril;
          });
          // console.log(this.resolutionEmployees);
          this.isLoading = false;
        })
        .catch(err => {
          console.log(err);
        });
    },
    saveResolution() {
      if (this.$refs.resolutionForm.validate()) {
      this.loading = true;
      axios
        .post(
          this.$store.state.backend_url + "api/document-signers/add-signers",
          this.resolution
        )
        .then(res => {
          this.loading = false;
          this.resolution.action_type = null;
          this.resolution.assignment = "";
          this.resolution.due_date = null;
          this.resolution.employee = null;
          this.$refs.resolutionForm.resetValidation();
          this.getDocumentList();
          this.getNotifications();
          this.getList(false);
          this.modelAddResolution = false;
        })
        .catch(err => {
          console.log(err);
          this.loading = false;
        });
      }
    },
    saveEditAttribute() {
      if (this.document.reaction_show) {
        this.loading = true;
        this.processing();
        axios
          .post(
            this.$store.state.backend_url +
              "api/document-detail-signer-attributes/edit",
            {
              edit_attributes: this.edit_attributes,
              document_id: this.document.id
            }
          )
          .then(res => {
            this.loading = false;
            const Toast = Swal.mixin({
              toast: true,
              position: "top-end",
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true,
              didOpen: toast => {
                toast.addEventListener("mouseenter", Swal.stopTimer);
                toast.addEventListener("mouseleave", Swal.resumeTimer);
              }
            });

            Toast.fire({
              icon: "success",
              title: this.$t("create_update_operation")
            });
            this.getDocumentHistory();
          })
          .catch(err => {
            console.log(err);
            this.loading = false;
          });
      }
    },
    saveDocumentTitle(title) {
      if (this.$store.getters.checkPermission("okd_kanselyariya")) {
        this.loading = true;
        this.dialogEditDocumentTitle = false;
        axios
          .post(this.$store.state.backend_url + "api/document-edit-title", {
            title: title,
            document_id: this.document.id
          })
          .then(res => {
            this.loading = false;
            const Toast = Swal.mixin({
              toast: true,
              position: "top-end",
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true,
              didOpen: toast => {
                toast.addEventListener("mouseenter", Swal.stopTimer);
                toast.addEventListener("mouseleave", Swal.resumeTimer);
              }
            });

            Toast.fire({
              icon: "success",
              title: this.$t("create_update_operation")
            });
            this.getList();
          })
          .catch(err => {
            console.log(err);
            this.loading = false;
          });
      }
    },
    viewPdfFile(item) {
      console.log(item);
      this.fileForView = item;
      this.pdfViewDialog = true;
    }
  },
  mounted() {
    this.getTableList();
    if (this.$store.state.DEVELOPMENT) this.zoom = 5;
    this.changeSize();
    this.mainHeight = window.innerHeight - 130;
    this.pdf_file_name = this.$route.params.pdf_file_name;
    this.getList(false);

    this.formData = new FormData();
    let user = this.$store.getters.getUser();
    if (user.id == 518 || user.id == 517) {
      this.rightMenu = false;
    } else if (Cookies.get("right_menu") == "false") {
      this.rightMenu = false;
    } else {
      this.rightMenu = true;
    }
    // axios
    //   .get(this.$store.state.backend_url + "api/users/show")
    //   .then((data) => {
    //     let user = data.data;
    //     if (user.id == 518 || user.id == 517) {
    //       this.rightMenu = false;
    //     } else if (Cookies.get("right_menu") == "false") {
    //       this.rightMenu = false;
    //     } else {
    //       this.rightMenu = true;
    //     }
    //   })
    //   .catch((e) => {
    //     console.error(e);
    //   });
  }
};
</script>
<style scoped>
.ok_success,
.e_imzo_success,
.error_cancel,
.primary_prosesing,
.orange_substantiate {
  background-repeat: no-repeat;
  background-size: contain;
  background-position: center;
}
.orange_substantiate {
  background-color: #fb8c0059;
}
.font_family {
  font-family: "Times New Roman", Times, serif !important;
}
thead tr {
  background-color: #eef0f7 !important;
}
tbody tr {
  background-color: #fff !important;
}
tbody tr:hover {
  background-color: #fff !important;
}
thead tr:hover {
  background-color: #eef0f7 !important;
}
table {
  line-height: 1rem !important;
}
.historyList {
  position: relative;
  margin-left: 20px;
  margin-top: 5px;
}
.historyListChild {
  position: relative;
  margin-left: 20px;
  margin-top: 10px;
}
.historyAvatar img {
  width: 25px;
  border-radius: 50%;
}
.historyAvatar .img {
  position: absolute;
  left: -35px;
  top: 5px;
}
.historyAvatar .no-img {
  width: 25px;
  height: 25px;
  border-radius: 50%;
  background: #fff;
  border: #3f51b5 solid 1px;
  font-family: "Roboto", sans-serif !important;
  color: #3f51b5;
  font-size: 14px;
  position: absolute;
  left: -20px;
  top: 5px;
  text-align: center;
}
.historyContent {
  min-height: 45px;
  border: #d3d3ff solid 1px;
  border-radius: 5px;
  padding: 2px 2px 2px 7px;
  white-space: normal;
}
.historyContent h4 span {
  color: #5b9bd5;
  white-space: normal;
  font-size: 11px;
}
.whiteSpace tr th,
.whiteSpace tr td {
  white-space: normal !important;
  min-width: 30px;
}
.doc_font {
  font-size: 14px !important;
  font-family: "Times New Roman", Times, serif;
}
b {
  color: #4c6992;
}
.v-list,
.v-list-item {
  background-color: white !important;
}
/* .p_content {
  text-indent: 40px;
} */
</style>
<style>
.v-treeview-node__root {
  min-height: 30px !important;
}
.v-treeview-node__content {
  margin-left: -5px !important;
}
</style>
