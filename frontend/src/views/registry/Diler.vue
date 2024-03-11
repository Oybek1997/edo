<template>
  <div>
    <v-card class="ma-1 pa-1">
      <v-card-title class="pa-1">
        <span
          >{{ $t("reysters") }}:
          {{
            items[0] ? items[0].document_template["name_" + $i18n.locale] : ""
          }}</span
        >
        <v-spacer></v-spacer>
        <v-btn
          small
          outlined
          color="success"
          class="mx-2"
          @click="makeDocument"
          v-if="items.filter((v) => !!v.selected).length > 0"
          >{{ $t("create") }}
        </v-btn>
      </v-card-title>
      <v-simple-table
        dense
        fixed-header
        :height="screenHeight"
        class="mainTable"
      >
        <template v-slot:default>
          <thead>
            <tr>
              <th style="background-color: #f0f0f5">#</th>
              <th style="background-color: #f0f0f5">Diler kodi</th>
              <th style="background-color: #f0f0f5">Diler nomi</th>
              <th style="background-color: #f0f0f5">To'lov amalga oshiriladi (Xisob raqami)</th>
              <th style="background-color: #f0f0f5">To'lov amalga oshiriladi (Bank nomi)</th>
              <th style="background-color: #f0f0f5">To'lov amalga oshiriladi (Bank MFO)</th>
              <th style="background-color: #f0f0f5">Mijoz (JSh yoki Yuridik shaxs nomi)</th>
              <th style="background-color: #f0f0f5">JShShIR/STIR</th>
              <th style="background-color: #f0f0f5">Shartnoma raqami</th>
              <th style="background-color: #f0f0f5">Shartnoma sanasi</th>
              <th style="background-color: #f0f0f5">Qaytariladigan to'lov miqdori</th>
              <th style="background-color: #f0f0f5">Oluvchining nomi</th>
              <th style="background-color: #f0f0f5">Oluvchi bank nomi</th>
              <th style="background-color: #f0f0f5">Oluvchi bank STIRi</th>
              <th style="background-color: #f0f0f5">MFO</th>
              <th style="background-color: #f0f0f5">Hisob raqami</th>
              <th style="background-color: #f0f0f5">Plastik karta raqami</th>
              <th style="background-color: #f0f0f5">Asos</th>
              <th style="background-color: #f0f0f5">To'lov maqsadi</th>
              <th style="background-color: #f0f0f5" width="40">
                <v-checkbox
                  class="ml-3"
                  v-model="select_all"
                  @click="selectAll()"
                  :indeterminate="indeterminate"
                ></v-checkbox>
              </th>
              <th style="background-color: #f0f0f5" width="40"></th>
            </tr>
          </thead>
          <tbody
            v-for="(item, key) in items"
            :key="key"
          >
            <tr
              style="background-color: #ffffff; cursor: pointer"
              v-for="(detail, detail_key) in item.document_details"
              :key="detail_key"
            >
              <td
                :rowspan="detail_key == 0 ? item.document_details.length : 1"
                v-if="detail_key == 0"
                @click="pdfViewInDialog(item)"
              >
                {{ key + 1 }}
              </td>
              <td>{{ '' }}</td>
              <td>{{ '' }}</td>
              <td>{{ detail.document_detail_contents.find(v=>v.d_d_attribute_id == 2191) ? detail.document_detail_contents.find(v=>v.d_d_attribute_id == 2191).value : '' }}</td>
              <td>{{ detail.document_detail_contents.find(v=>v.d_d_attribute_id == 2192) ? detail.document_detail_contents.find(v=>v.d_d_attribute_id == 2192).value : '' }}</td>
              <td>{{ detail.document_detail_contents.find(v=>v.d_d_attribute_id == 2193) ? detail.document_detail_contents.find(v=>v.d_d_attribute_id == 2193).value : '' }}</td>
              <td>{{ detail.document_detail_contents.find(v=>v.d_d_attribute_id == 1561) ? detail.document_detail_contents.find(v=>v.d_d_attribute_id == 1561).value : '' }}</td>
              <td>{{ detail.document_detail_contents.find(v=>v.d_d_attribute_id == 1562) ? detail.document_detail_contents.find(v=>v.d_d_attribute_id == 1562).value : '' }}</td>
              <td>{{ detail.document_detail_contents.find(v=>v.d_d_attribute_id == 1559) ? detail.document_detail_contents.find(v=>v.d_d_attribute_id == 1559).value : '' }}</td>
              <td>{{ detail.document_detail_contents.find(v=>v.d_d_attribute_id == 1560) ? detail.document_detail_contents.find(v=>v.d_d_attribute_id == 1560).value : '' }}</td>
              <td>{{ detail.document_detail_contents.find(v=>v.d_d_attribute_id == 1564) ? detail.document_detail_contents.find(v=>v.d_d_attribute_id == 1564).value : '' }}</td>
              <td>{{ detail.document_detail_contents.find(v=>v.d_d_attribute_id == 1565) ? detail.document_detail_contents.find(v=>v.d_d_attribute_id == 1565).value : '' }}</td>
              <td>{{ detail.document_detail_contents.find(v=>v.d_d_attribute_id == 1567) ? detail.document_detail_contents.find(v=>v.d_d_attribute_id == 1567).value : '' }}</td>
              <td>{{ detail.document_detail_contents.find(v=>v.d_d_attribute_id == 1568) ? detail.document_detail_contents.find(v=>v.d_d_attribute_id == 1568).value : '' }}</td>
              <td>{{ detail.document_detail_contents.find(v=>v.d_d_attribute_id == 1569) ? detail.document_detail_contents.find(v=>v.d_d_attribute_id == 1569).value : '' }}</td>
              <td>{{ detail.document_detail_contents.find(v=>v.d_d_attribute_id == 1570) ? detail.document_detail_contents.find(v=>v.d_d_attribute_id == 1570).value : '' }}</td>
              <td>{{ detail.document_detail_contents.find(v=>v.d_d_attribute_id == 1571) ? detail.document_detail_contents.find(v=>v.d_d_attribute_id == 1571).value : '' }}</td>
              <td>{{ '' }}</td>
              <td>{{ '' }}</td>
              <td
                width="40"
                :rowspan="detail_key == 0 ? item.document_details.length : 1"
                v-if="detail_key == 0"
              >
                <v-checkbox class="ml-3" v-model="item.selected"></v-checkbox>
              </td>
              <td
                width="40"
                :rowspan="detail_key == 0 ? item.document_details.length : 1"
                v-if="detail_key == 0"
              >
                <v-icon class="ml-3" color="red" @click="deleteItem(item)"
                  >mdi-delete</v-icon
                >
              </td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>
    </v-card>

    <v-dialog v-model="pdfViewDialog" fullscreen>
      <v-card>
        <v-card-title primary-title>
          <v-spacer></v-spacer
          ><v-btn color="red" outlined @click="pdfViewDialog = false">{{
            $t("close")
          }}</v-btn>
        </v-card-title>
        <v-card-text class="d-flex justify-center">
          <iframe
            v-if="pdfViewDialog"
            :width="screenWidth - 200"
            :height="screenHeight - 20"
            :src="'data:application/pdf;base64,' + selected_document.pdf"
          ></iframe>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer
          ><v-btn
            color="red"
            outlined
            @click="pdfViewDialog = false"
            class="mr-4"
            >{{ $t("close") }}</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="dialog"
      @keydown.esc="dialog = false"
      persistent
      max-width="800"
    >
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          <span class="headline">{{ $t("company.dialogHeaderText") }}</span>
          <v-spacer></v-spacer>
          <v-btn color="red" outlined x-small fab class @click="dialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form @keyup.native.enter="save" ref="dialogForm">
            <v-row>
              <v-col cols="12">
                <label for>{{ $t("user.name") }}</label>
                <v-text-field
                  v-model="form.name"
                  :rules="[(v) => !!v || $t('input_required')]"
                  hide-details="auto"
                  dense
                  outlined
                ></v-text-field>
              </v-col>
            </v-row>
          </v-form>
          <small color="red">{{ $t("input_required") }}</small>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="save">{{ $t("save") }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="modalDocumentReaction" persistent width="500">
      <v-card>
        <v-card-title class="grey lighten-3">
          {{ $t("document.reaction") }}
          <v-spacer></v-spacer>
          <v-btn
            color="red"
            outlined
            x-small
            fab
            class
            @click="modalDocumentReaction = false"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-form name="testform">
            <v-text-field name="pkcs7" v-show="false"></v-text-field>
            <v-row>
              <v-col cols="12" class="my-2 py-0" v-if="sign_type">
                Выберите ключ
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
              <v-col cols="6" style="min-width: 100px" class="text-right">
                <v-img
                  v-if="sign_type"
                  :src="'img/ldap.jpg'"
                  width="70"
                  class="mt-2 ml-auto"
                  contain
                  style="cursor: pointer"
                  @click="
                    sign_type = !sign_type;
                    AppLoad();
                  "
                ></v-img>
                <v-img
                  v-if="!sign_type"
                  :src="'img/imzo.png'"
                  width="100"
                  class="mt-2 ml-auto"
                  contain
                  style="cursor: pointer"
                  @click="
                    sign_type = !sign_type;
                    AppLoad();
                  "
                ></v-img>
              </v-col>
              <v-col cols="6" style="min-width: 100px" class="text-right">
                <v-btn
                  v-if="reaction == 1"
                  outlined
                  width="200px"
                  color="success"
                  class="mx-2"
                  @click="actionSelectedDocuments(1)"
                >
                  <span style="white-space: normal; width: 180px">{{
                    $t("document.accept")
                  }}</span>
                </v-btn>
                <v-btn
                  v-else
                  color="error"
                  outlined
                  width="200px"
                  class="mx-2"
                  @click="actionSelectedDocuments(2)"
                  >{{ $t("document.reject") }}</v-btn
                >
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog v-model="loading" hide-overlay persistent width="300">
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
import Cookies from "js-cookie";
import Swal from "sweetalert2";
const axios = require("axios").default;
const moment = require("moment");

export default {
  data: () => ({
    wait: false,
    indeterminate: false,
    base64: null,
    test: false,
    selected_document: null,
    pdfViewDialog: false,
    select_all: false,
    loading: 0,
    sign_type: true,
    docDefinition: [],
    modalDocumentReaction: false,
    search: "",
    reaction: 0,
    dialog: false,
    editMode: null,
    action_types: [
      {
        id: 2,
        name_uz_latin: "Tasdiq",
        name_uz_cyril: "Тасдиқ",
        name_ru: "Утверждение",
      },
      {
        id: 1,
        name_uz_latin: "Rozilik",
        name_uz_cyril: "Розилик",
        name_ru: "Согласование",
      },
      {
        id: 3,
        name_uz_latin: "Bo'lim ichida rozilik",
        name_uz_cyril: "Бўлим ичида розилик",
        name_ru: "Согласование внутри подразделение",
      },
      {
        id: 6,
        name_uz_latin: "Hujjat yaratuvchisi",
        name_uz_cyril: "Ҳужжат яратувчиси",
        name_ru: "Создатель документа",
      },
    ],
    cancelImageUrl:
      "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC0AAAAtCAYAAAA6GuKaAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAAEnQAABJ0Ad5mH3gAAA4oSURBVGhDxVlpkB1VFT7v9dtmyUwgZJskJJCFJIBEQAHRMoDgVlZJlQKWyhK2UBIMuFQplqWFBZGlIkQNqCiCC1iGRdlmErMSErIvM5PJbNlJSDLJrC+vXy/X7zv3dfImZCZD+cOvp1/3vX3uud8999zb5/TEDCD/R3ieJ6SQSCT0Ph6PSzKZLDw9NfolHeKM4VcFwhgKAW4c1MQkhmJcn4u4PV3SXdsgueZGyb6/X/KdPRAzUjJkiKTHjJFB502V8imTVJ6gFmMCcWJxapLssSwqoA+EM5mMFeoHAyKtJCFl8OMUuu5pbZIdzz0nHf94VcLt9ajncLRvO0iAZRKkHq8kLZlPXi5VM2ZI1ddvkkRJiiIFeRygwUEMBP2S5qMQROMe7pNxpdu2YY3UznpAku+uVFIuzvT4c6Xy058VueRSyYwaKekzBknoGcm17ZNwx27pWb1WupaskHh3h5BqF86K79wj0x5+VBIV5ehHxA88SThJncHTgqT7QhiGJsQ1wJk7ut8s/9IXzDI0WYtz+cTJpnX+H4x75LCV7eMsxuG6WrPpu/eaZcmkWQcd/8FZO+cX+ox9DBSnJB0EgQkD3/iFbve8ssAskYRZg06WjRtn9le/pfURVCpiWMQWY7bXwhnBx7ntkYdMDfSR/OKp5xn34CH7zA+Nj/4DTFXvVidwEmkKgXDBwsTG7//IrITipTjr5jyqdbSKj569iNUA4YV5NYZXaOb1dJuV068270L3wljMvL9yhdYHYQ4cOLRToxdpn2Qh7AZW65pbbjGrobC6rMy0N9RpHcFBcTaOm3KA4EDzsGAYuOiLM2lR9/jj5h1aHOeB6kVal/f7dhglTRL2ylHqrVl//33WAsOGGf/oYZPX57YbElbSCjaw54n5OTUC0oS1PegJ0FGgxK2enX97QWdzIc62zZu0jlCN+CnW3It0RGTnC1bBotKMyR/dp3WwDTo5IReiU3ZYfPLoDxwy+6J70G9JpZhM61+eN8vRb00yYfLZHqNe7QWQ944bk5DeVjPm2IEdOk1c2d21W7QhOwh1YViQcAQ8UbJctH2RPj6TgdWhs6b3H5bf+uMHzXvoe+kVn9IyB4qVAA5aVAg7Zdtop1g+/SqzCo0afjtPy7SuWhb3JOuGtnX20CGzv7bODgorntYoHgxBST2hI3rS8OYbenWxIHtLQ0+hYtG0aerjLX/9o5bpRtEsE8J+IksfXLRE9+GlF07VMuWKSXPxEHgRmDdGjFT/O9LSpHXcokg6OtAadfzVW8WWOQ+b19Fm/d332AqiSIZ6iZ69+6x/VwzSMuEXzbRw6nTXQGHRhdOU9OENdiFgHwFhPOfeCQkOzc8fM2+NGmtqIdeEF+qbibjpamyCJAfvqVzk3znMit1vQfiRh9Vfmx1Hp3/9nTO13iXp0DV5GMYPuCVaA6698zbduRrnPqXlADMTQS1Nwh1rNqjl3r3sCn3A/TSgK+ChS2ujLuhqNwtHnmvqIFefSpu6VMpsR2RUk3LM0fpt2k59HO10kNzjgLo5j+he3+gkzNZ0qdmesG/VdXffps/p47q2IO7pgT7bPjCLILMYM0oolwIkWhyrZ9yuvrzn5Ze17EOIVNVFODLglWs+Z7aScCZuGrDCmxIkkTKNIP5W0jFdzU2WAFZ8HrSJurlzdfbqMLC6VNJsS2ZwLTVN8ZSS2v7SS2o0zmYEv7DqVl13Hd7EmPlVa7UcQSKjL6woN4uhLPRy1qrQEWJPtffYdjiAzg7zdtUIdQ12XJtOgjwsjkHUSxy7TtK0NW1XfUTtnF+qhetSMVi3BFfIsk0iqVO/5i7rIlw33Lsj2tEa27Hgn+pSm+6ZpeUIuk931jeYajxcde2XtZKDDmnpoimJ4Ls95u1Ro9XiDYkMiKdgPWtxDmZxLGOy7+822381z7761bokm4J82mxDG8Yw79w1Q/UpUe1Pi73gd3fp1rtk7OhCjYUGsAdWrZIKXM+8djqLiJ5NIX7uHd/CImJSpXJdS4PsqRotgZ9DuIqIO5aSdIBW6YRUOTnZOm6KdM6eJcOSjiRC6GDCYGKSR2KQRRtz191y5TPP2ljbINpGX2iusXUxnLJyCadMEG/XXsEACrU2+ZCurZvEx7Xk4o+zKHFNS2z8XAzmLHAoBNBlck1LreytGiNJ18XgAsyYgyAeJ55VIsAvS5ciNkbETVXgFcesum5egrtmyiefeVo1aeJAw+AvgfteoXRhBGd87HJ9lm1tsRWA8so27xbE+TJ00mQW+wS7cZAmacCeqZRrkb20jhovDoiL5EEyIW4KXWSzIO+IFwM1LnYM6ogHwjNnyieemY8ZC/BSO9muvYGJUZRPnCDMGDtadtkKQEln2g+qpZ1BpSz2iVg8hllISxIZBhuadFquat4gB6acL17KkVgmJY4PoqUZCfKepGBhL+5Ij+dL8t5Zctn8+RgaMiEctHJ/oIsS6eFD9Op3HNYroaRjOfomSAwgqSRoPb3CYslMhVTdMUO8Y54E2Rysje5g6Rj8P4B7JP2YuPGUnP/gD7RNIsDi59G/oYGCqR2uGhD1aVYLS3pQiY4r5uZY7BOUUbnj+hxpmPeMfPC970nFoDJJpVISwlVInD1pQoyFOTjMy8pxF0j3jlYJQEJzcO359IhhHTAxDpMltgLQptnKIZpweocOsWgtyb+TzBGiyAWEvUXL9U/OlaP3zZQRZZWSzQfi5rqZKUMGaumU2ILyjiN+Ji1VbqesnjhF8lxQcLNIf/FxKnQdOKQkU2edYSsAJV0+aYrSONzYzOIJ0h9SBELGV9nauU9K2+wHZDAIBU6IBeqDSwI8fXHymDEMAtVwhzi2Q7TDLIwKfFk+ebJ079qJAcU106cRovNUyDU1ShbXyrFjbQWgpCsvmqbbyrH1m1hUUGGkRi2CMw5TJ2NJafjNk3LkgdkyHDuFU1omsR74MuQ4jR1eKLmbb5W28RMk5rnig3kAd0nA8tzHx3uBLMeOkG1uxmKEo6Ce74Q+OEv7+rXKrXTiebYCUNJDr7xCv0UcXoj3IhBAC6eZyop1xeCPjb+eJwfvnS0jysslyJSLAQn6MPfiXN6VcMbdctGf/ySXbF4t+0aeLZkcN1Orh4N2sX9PRJsVF14ona3N4usCobt8mLV/BK6xa5fEJ02SOHaqCNBjpHzs2eKMOEvyK1ZI0NMuCax6tXRBF0nx3PzEo3Jk1n0yvDwNK2KZYfdA3gi5UNr8vPh33CmXP/u0ILKWDPx8elO9tI7GtGIwcb5NIVcauJLLODIx58q68ZOlqxZRC4mzLx5oy5PY/eobwk148PVf1XIELgnFWTfcgE3cyM4FL2s5Dp+L4y0fR12eKxDg3kq7+bRo6IpzDC8U15dO7MNyy7fkst//Tl0EO7aa1ikrk6u3bZG9I4fBxT0MMiku3CsNcQ8CbgKzWdhlYRa1ED+94f2l2PenP6o/n3vTN21FBEa+RNdOmxsun3SelhmWMrpjSO8j5MsXgqem38w3KyDX7MTNppK42Yj79265XZ8x5mFSEaVdnm+jxLAb0eHwkRpkbUc4Wwf3rkbg1LG7VeWYKDDSYzJCeYa33S2NNsmdOEFlmFhEQDxtg3binc98VuPXvTVvWAIwMa82E7HZC7Ft3hMaI5Pwyttu1bpokMUIkYloFo97t/uIqRk6DLE3svxkielqbVaZYwhJ8xFZTTysEVdff73NE//+gpajjIZAPM3DJgJHN23WUHDp8OFapoVJllbgkUc7r2DFrXMeM2/dfLPeU50HwpQrhuaMTKW8Qn7X2W7+PGUqCNuYO7Iu41KO1/MjHhuVcM05Y7VMrVGMTcCNemPNjTdpDrfxhz/UMnXa/KzwYQVEkDni3kL75BXnCbWnRoG6QjmwURHcQkVN1TnqgvurX7e60T+zzwi9SFPA7+4xNSUlOtID1dW2Hrkem2lWDpZ5EKePs19ay87E6UljMaI93QHDj0ijLa0dDei9b3zLrEff737tRi0HyEg0cyrMMNGLNLNpPvpg2QrNOmqwU3dtWa/P7Hc+2zC6/q/gAKiLiTOx4aGf6SxXVw3TcgQ1Vl/uwQQ2GhEXG9Oi/yARbd9Wr3U+pomdRNfTWfZkcCGTn65X3HgwUmTAzT9/WL8dvo7ULXvgA1upJrRGKjZUb/egImw4dGHq3fbYHN1N6F+7FixQGW5iSLPUZT4q6RBZNqdapxuEovbv3X6HGqgaGf3R7fbrLDeBvnCSpakYtLBV6doDWp97Xj9+c89859tMRq0y3YELlrK7Bin03RHBWeRXJH6bIzqaGs2isePMZuiuGTrGZHfv0Xp+oLRb36n1fcg9dGFx0eFwC8Q76reYxeVDdIHQ8i1P/do+OA4uRB79I3redeSgWffNG3QG+Z+AFV/5gtbzuV3gvOPSHABp9R0Qp8/ZPZYrm+8niw33f1/fmuxoSSZjNs2+37SvX1N42j+8fLd5/1+vmTWfv1Z9V//nUnGm2fvv6OMQ1wlO9Zm+rUz0+9+tCCCv/+MjckgUGn7yU+l49vdSEiDJRV2QKpfkpVOk4oKLJTV6jJjKQZJAJOe1H5LOlhZxt9SJqatjdKEIR50jQx+8XybeMxMRiM03GXDEmb0PAAMiHYnwP4pUzM4ZNB167RXZ8+KL4i9aKmFnB2MkjX1JgjL5wpXlcPwkGfzFz8vI274hZ158BWps/A1Hl1iSwRKT5mhY/WNApCNg/pBwIOOgWzFcRZ21P1K2tsPSiSwjwEzE25F2IVOJVZaLc/YIGTxhkiRSZUqSYDsNHBGqcqSMKFmMdJ0OH420qmaXUYLAH5Q13laRPsFe+FmAiRXvYko00mdxGhUFiPwXkjyHqBpZmKwAAAAASUVORK5CYII=",
    imzoUrl:
      "data:image/png;base64,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",
    imageUrl:
      "data:image/png;base64,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",
    items: [],
    departments: [],
    roles: [],
    form: {},
    dialogHeaderText: "",
    documentComment: "",
  }),
  computed: {
    screenHeight() {
      return window.innerHeight - 120;
    },
    screenWidth() {
      return window.innerWidth;
    },
    language() {
      return this.$i18n.locale == "ru" ? "uz_cyril" : this.$i18n.locale;
    },
  },
  watch: {
    modalDocumentReaction(value) {
      this.AppLoad();
    },
    pdfViewDialog(value) {
      if (!value) {
        this.base64 = null;
        this.selected_document = null;
      }
    },
  },
  methods: {
    makeDocument() {
      let ids = this.items.filter((v) => v.selected).map((v) => v.id);
      axios
        .post(
          this.$store.state.backend_url + "api/diler-registry-create",
          {
            ids: ids,
            locale: this.$i18n.locale,
          }
        )
        .then((res) => {
          this.$router.push("document/" + res.data.pdf_file_name);
        });
    },
    pdfViewInDialog(doc) {
      this.createDefaultPdf(doc, true);
    },
    selectAll() {
      // this.select_all = !this.select_all;
      this.items = this.items.map((v) => {
        v.selected = this.select_all;
        return v;
      });
    },
    checkSelect() {
      let res = this.items.reduce(
        (accumulator, currentValue) => {
          if (currentValue.selected) accumulator.a++;
          else accumulator.b++;
          return accumulator;
        },
        { a: 0, b: 0 }
      );
      if (res.a != 0 && res.b == 0) this.select_all = true;
      else this.select_all = false;
      this.indeterminate = res.a != 0 && res.b != 0;
    },
    documentSignerData(doc, pageWidth) {
      let data = [{ text: "", margin: [0, 5, 0, 0] }];
      let atLength = this.action_types.length;
      this.action_types.map((action, actinoIdx) => {
        if (
          doc.document_signers &&
          doc.document_signers.find((v) => {
            return action.id == v.action_type_id && action.id != 6
              ? true
              : false;
          })
        ) {
          let tmp = {
            table: {
              widths: [250, "*", "*"],
              body: [
                [
                  {
                    text: action["name_" + this.$i18n.locale],
                    colSpan: 3,
                    fillColor: "#eef0f7",
                    fontSize: 8,
                  },
                  {},
                  {},
                ],
              ],
            },
          };
          let cancelImageUrl =
            "data:image/png;base64,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";
          let imzoUrl =
            "data:image/png;base64,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";
          let imageUrl =
            "data:image/png;base64,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";

          doc.document_signers
            .filter((f) => f.action_type_id == action.id)
            .map((signer) => {
              let actionColumn = [];
              let updatedAt =
                signer.updated_at &&
                signer.updated_at.substr(0, 10) +
                  " " +
                  signer.updated_at.substr(11, 5);
              if (signer.status == 1 && signer.sign_type == 1) {
                actionColumn = [
                  {
                    image: imzoUrl,
                    width: 20,
                    height: 20,
                    alignment: "center",
                  },
                  {
                    text: updatedAt,
                    alignment: "center",
                    margin: [0, 5, 0, 0],
                    fontSize: 8,
                  },
                ];
              } else if (signer.status == 1) {
                actionColumn = [
                  {
                    image: imageUrl,
                    width: 20,
                    height: 20,
                    alignment: "center",
                  },
                  {
                    text: updatedAt,
                    alignment: "center",
                    margin: [0, 5, 0, 0],
                    fontSize: 8,
                  },
                ];
              } else if (signer.status == 2) {
                actionColumn = [
                  {
                    image: cancelImageUrl,
                    width: 20,
                    height: 20,
                    alignment: "center",
                  },
                  {
                    text: updatedAt,
                    alignment: "center",
                    margin: [0, 5, 0, 0],
                    fontSize: 8,
                  },
                ];
              }
              let employeeFIO = "";

              if (signer.signer_employee_id) {
                employeeFIO =
                  signer.signer_employee &&
                  signer.signer_employee["firstname_" + this.language].substr(
                    0,
                    1
                  ) +
                    "." +
                    signer.signer_employee[
                      "middlename_" + this.language
                    ].substr(0, 1) +
                    ". " +
                    signer.signer_employee["lastname_" + this.language];
              } else if (signer.employee_staffs) {
                employeeFIO =
                  signer.employee_staffs.employee &&
                  signer.employee_staffs.employee[
                    "firstname_" + this.language
                  ].substr(0, 1) +
                    "." +
                    signer.employee_staffs.employee[
                      "middlename_" + this.language
                    ].substr(0, 1) +
                    ". " +
                    signer.employee_staffs.employee[
                      "lastname_" + this.language
                    ];
              } else {
                employeeFIO = this.$t("document.vacancy");
              }
              tmp.table.body.push([
                {
                  text:
                    signer.staffs[0].position["name_" + this.$i18n.locale] +
                    "\n" +
                    signer.staffs[0].department["name_" + this.$i18n.locale],
                  fontSize: 10,
                  verticalAlignment: "center",
                  fontSize: 8,
                },
                actionColumn,
                {
                  text: employeeFIO,
                  fontSize: 8,
                },
              ]);
            });
          data.push(tmp);
        }
      });
      data.push({
        text: [
          {
            text: this.$t("document.creator") + ": ",
            bold: true,
            fontSize: 8,
          },
          doc.employee
            ? doc.employee["lastname_" + this.language] +
              " " +
              doc.employee["firstname_" + this.language].substr(0, 1) +
              "." +
              doc.employee["middlename_" + this.language].substr(0, 1)
            : " ",
          " (",
          this.$t("employee.tabel"),
          " - ",
          doc.employee.tabel,
          ")",
        ],
        margin: [0, 5, 0, 0],
        fontSize: 8,
      });
      return data;
    },
    getFromDepartament(doc) {
      this.loading = this.loading + 1;
      axios
        .post(this.$store.state.backend_url + "api/employees/get-employee", {
          access_type: 100,
          tabel: doc.employee.tabel,
          language: this.$i18n.locale == "ru" ? "uz_cyril" : this.$i18n.locale,
        })
        .then((res) => {
          this.items.map((v) => {
            if (v.id == doc.id) {
              v.from_department = res.data.parents.main_department;
            }
            if (!v.base64) {
              v.newBase64 = true;
            } else v.newBase64 = false;
            return v;
          });
          this.loading = this.loading - 1;
        })
        .catch((err) => {
          console.error(err);
        });
    },
    getList() {
      let locale = this.$i18n.locale;
      this.loading = this.loading + 1;
      axios
        .get(
          this.$store.state.backend_url + "api/diler-registry/" + locale
        )
        .then((response) => {
          this.items = response.data.data.map((v) => {
            v.selected = false;
            // v.document_details = v.document_details.map((dd) => {
            //   dd.document_detail_attribute_values =
            //     dd.document_detail_attribute_values.map((ddav) => {
            //       if (ddav.document_detail_attributes.table_list)
            //         if (ddav.document_detail_attributes.table_list_id == 11)
            //           ddav.table_value = ddav.table_lists[0]["name_" + locale];
            //         else if (
            //           ddav.document_detail_attributes.table_list_id == 14
            //         )
            //           ddav.table_value = ddav.table_lists[0].period;
            //         else ddav.table_value = "";
            //       return ddav;
            //     });
            //   return dd;
            // });
            return v;
          });
          this.loading = this.loading - 1;
        })
        .catch((error) => {
          console.log(error);
          this.loading = this.loading - 1;
        });
    },
    getDocument(doc) {
      axios
        .post(this.$store.state.backend_url + "api/documents/show-document", {
          id: doc.id,
          language: this.$i18n.locale,
        })
        .then((res) => {
          doc = res.data;
          doc.document_signers.sort((a, b) => {
            return (
              a.staffs[0].department.department_code -
              b.staffs[0].department.department_code
            );
          });
          doc.document_signers.sort((a, b) => {
            return a.sequence - b.sequence;
          });

          this.items = this.items.map((v) => {
            if (v.id == doc.id) return doc;
            else return v;
          });
          this.refreshDocument(doc);
          this.items = this.items.filter((v) => v.id != doc.id);
        })
        .catch((err) => {
          console.log(err);
        });
    },
    newItem() {
      this.dialogHeaderText = this.$t("company.newOrganization");
      this.form = {
        id: Date.now(),
        name: "",
        email: "",
        phone: "",
      };
      this.dialog = true;
      this.editMode = false;
      if (this.$refs.dialogForm) this.$refs.dialogForm.reset();
    },
    editItem(item) {
      this.dialogHeaderText = this.$t("company.updateCompany");
      this.formIndex = this.items.indexOf(item);
      this.form = Object.assign({}, item);
      this.dialog = true;
      this.editMode = true;
      if (this.$refs.dialogForm) this.$refs.dialogForm.resetValidation();
    },
    save() {
      if (this.$refs.dialogForm.validate())
        axios
          .post(
            this.$store.state.backend_url + "api/companies/update",
            this.form
          )
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
              title: $t("create_update_operation"),
            });
          })
          .catch((err) => {
            console.log(err);
          });
    }, //companies
    deleteItem(item) {
      const index = this.items.indexOf(item);
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
          this.loading = true;
          axios
            .get(
              this.$store.state.backend_url +
                "api/business-trip-delete/" +
                item.id
            )
            .then((res) => {
              this.loading = false;
              this.items = this.items.filter((v) => v.id != item.id);
              // this.$router.push("document/" + res.data.pdf_file_name);
            });
        }
      });
    },
    // deleteItem(item) {
    //   const index = this.items.indexOf(item);
    //   Swal.fire({
    //     title: this.$t("swal_title"),
    //     text: this.$t("swal_text"),
    //     icon: "warning",
    //     showCancelButton: true,
    //     confirmButtonColor: "#3085d6",
    //     cancelButtonColor: "#d33",
    //     confirmButtonText: this.$t("swal_delete"),
    //   }).then((result) => {
    //     if (result.value) {
    //       axios
    //         .delete(
    //           this.$store.state.backend_url + "api/users/delete/" + item.id
    //         )
    //         .then((res) => {
    //           this.getList(this.page, this.itemsPerPage);
    //           this.dialog = false;
    //           Swal.fire("Deleted!", this.$t("swal_deleted"), "success");
    //         })
    //         .catch((err) => {
    //           Swal.fire({
    //             icon: "error",
    //             title: this.$t("swal_error_title"),
    //             text: this.$t("swal_error_text"),
    //             //footer: "<a href>Why do I have this issue?</a>"
    //           });
    //           console.log(err);
    //         });
    //     }
    //   });
    // },
    //------------- E-imzo ---------------
    AppLoad() {
      EIMZOClient.API_KEYS = [
        "edo.uzautomotors.com",
        "79DC56F42765A0017C31309DB9600EA924684ED023A8079460454768331626AB94CFFF8FC2D4007976D4A6C56F11D56DFA962276DC54AE8C0F39E8A8EBDFA10B",
      ];

      let EIMZO_MAJOR = this.EIMZO_MAJOR;
      let EIMZO_MINOR = this.EIMZO_MINOR;
      let uiLoadKeys = this.uiLoadKeys;
      EIMZOClient.checkVersion(
        function (major, minor) {
          var newVersion = EIMZO_MAJOR * 100 + EIMZO_MINOR;
          var installedVersion = parseInt(major) * 100 + parseInt(minor);
          if (installedVersion < newVersion) {
          } else {
            EIMZOClient.installApiKeys(
              function () {
                uiLoadKeys();
              },
              function (e, r) {}
            );
          }
        },
        function (e, r) {}
      );
    },
    uiLoadKeys() {
      this.uiClearCombo();
      let uiCreateItem = this.uiCreateItem;
      let eimzo_password = this.$store.getters.getUser().eimzo_password;
      let reactionEimzo = this.reactionEimzo;
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
          combo.append(<option value="">Select</option>);
          for (var itm in items) {
            var vo = items[itm].getAttribute("vo");
            if (vo.includes(eimzo_password)) {
              items[itm].setAttribute("selected", "true");
              combo.append(items[itm]);
            }
          }
        },
        function (e, r) {}
      );
    },
    cbChanged(c) {
      Cookies.remove("eimzo_key");
    },
    uiClearCombo() {
      var combo = document.testform.key;
      combo.length = 0;
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
    getEimzoSigners() {
      let setPkcs7Info = this.setPkcs7Info;
      let base64 = doc.base64;
      if (base64 != null)
        CAPIWS.callFunction(
          {
            plugin: "pkcs7",
            name: "get_pkcs7_attached_info",
            arguments: [
              //Ранее созданный документ PKCS#7/CMS в кодировке BASE64
              // doc.base64,
              base64,
              //Идентификатор хранилища доверенных сертификатов (полученный из фукнции других плагинов), если требуется верификация сертификатов, иначе может быть пустым
              "",
            ],
          },
          function (event, data) {
            setPkcs7Info(data.pkcs7Info);
          },
          function (error) {
            // window.alert(error);
          }
        );
      else alert("Ushbu dokumentga hali imzo chekilmagan.");
    },
    setPkcs7Info(pkcs7Info) {
      this.pkcs7Info = pkcs7Info;
      this.pkcs7InfoDialog = true;
    },
    setBase64(doc, base64) {
      let item = this.items.find((v) => (v.id = doc.id));
      let idx = this.items.indexOf(item);
      doc.base64 = base64;
      this.items[idx] = doc;
      axios
        .post(this.$store.state.backend_url + "api/documents/set-base64", {
          document_id: doc.id,
          base64: base64,
        })
        .then((res) => {});
    },
    documentReaction(doc, reac, signer_serial_number) {
      axios
        .post(this.$store.state.backend_url + "api/document-signers/reaction", {
          document_id: doc.id,
          description: this.documentComment,
          status: reac,
          sign_type: this.sign_type ? 1 : 0,
          staff_id: doc.reaction_staff_id,
          signer_serial_number: signer_serial_number,
        })
        .then((res) => {
          this.getDocument(doc);
          this.modalDocumentReaction = false;

          this.pdfViewDialog = false;
          this.selected_document = null;
        })
        .catch((err) => {
          console.log(err);
        });
    },
    sign(doc, reac) {
      var itm = document.testform.key.value;
      var id = document.getElementById(itm);
      var vo = JSON.parse(id.getAttribute("vo"));
      let setBase64 = this.setBase64;
      let documentReaction = this.documentReaction;
      let key = Cookies.get("eimzo_key");
      if (doc.newBase64)
        EIMZOClient.createPkcs7(
          key,
          doc.base64,
          null,
          function (pkcs7) {
            setBase64(doc, pkcs7);
            documentReaction(doc, reac, vo.serialNumber);
          },
          function (e, r) {}
        );
      else
        CAPIWS.callFunction(
          {
            plugin: "pkcs7",
            name: "append_pkcs7_attached",
            arguments: [doc.base64, key],
          },
          function (event, data) {
            if (data["success"]) {
              setBase64(doc, data["pkcs7_64"]);
              documentReaction(doc, reac, vo.serialNumber);
            }
          },
          function (e, r) {}
        );
    },
    verify(doc, reac) {
      if (this.sign_type) {
        this.sign(doc, reac);
      } else {
        this.documentReaction(doc, reac, null);
      }
    },
    actionSelectedDocuments(action) {
      if (this.sign_type && !Cookies.get("eimzo_key")) {
        let itm = document.testform.key.value;
        let id = document.getElementById(itm);
        let vo = JSON.parse(id.getAttribute("vo"));
        let items = this.items;
        let verify = this.verify;
        let reaction = this.reaction;
        let selected_document = this.selected_document;
        EIMZOClient.loadKey(
          vo,
          function (id) {
            CAPIWS.callFunction(
              {
                plugin: "pfx",
                name: "verify_password",
                arguments: [
                  //Идентификатор ключа
                  id,
                ],
              },
              function (event, data) {
                if (data.success) {
                  Cookies.set("eimzo_key", id, { expires: 1 / 4 });
                  if (selected_document) {
                    verify(selected_document, reaction);
                  } else
                    items
                      .filter((v) => v.selected)
                      .forEach((doc) => {
                        verify(doc, reaction);
                      });
                } else
                  Swal.fire({
                    position: "top-end",
                    icon: "error",
                    title: "Wrong E-IMZO password.",
                    showConfirmButton: false,
                    timer: 1500,
                  });
              },
              function (error) {
                Swal.fire({
                  position: "top-end",
                  icon: "error",
                  title: "Wrong E-IMZO password.",
                  showConfirmButton: false,
                  timer: 1500,
                });
              }
            );
          },
          function (e, r) {}
        );
      } else {
        if (this.selected_document) {
          this.verify(this.selected_document, this.reaction);
        } else
          this.items
            .filter((v) => v.selected)
            .forEach((doc) => {
              this.createDefaultPdf(doc, false);
              setTimeout(() => {
                this.verify(doc, this.reaction);
              }, 1000);
            });
      }
    },
    //------------- /E-imzo --------------
  },
  mounted() {
    this.getList();
  },
};
</script>
