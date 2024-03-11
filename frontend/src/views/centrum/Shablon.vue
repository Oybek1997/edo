<template>
  <div class="fullHeight">
    <v-card
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3">
        <span class="headerTitle mb-3">{{ $t("Создать новый документ") }}</span>
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
                  style="margin: 0px; max-height: 34px; min-height: 34px"
                  @click="0"
                >
                  <v-list-item-title
                      @click="
                        getStaffExcel(1);
                        staff_excel = [];
                      "
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
      <v-row class="mx-5 mr-1">
        <v-col class="pa-0" md="2" sm="6" xs="12">
          <v-hover>
            <template v-slot="{ hover }">
              <v-card
                :class="`elevation-${hover ? 5 : 0}`"
                class="mr-4 mb-4 pa-3"
                @click="ActiveContet('departmenttypepage')"
                style="
                  border: 1px solid #dce5ef;
                  background: #fbfcfe;
                  border-radius: 2px;
                "
              >
                <template>
                  <v-card-title class="pa-0">
                    <v-icon class="card_icon" size="20" color="orange" large left>
                      mdi-folder-outline
                    </v-icon>
                    <span class="pa-0 cardTitle"> Произвольный шаблон </span>
                  </v-card-title>
                </template>
              </v-card>
            </template>
          </v-hover>
        </v-col>
        <v-col class="pa-0" md="2" sm="6" xs="12">
          <v-hover>
            <template v-slot="{ hover }">
              <v-card
                :class="`elevation-${hover ? 5 : 0}`"
                class="mr-4 mb-4 pa-3"
                @click="ActiveContet('positionpage')"
                style="
                  border: 1px solid #dce5ef;
                  background: #fbfcfe;
                  border-radius: 2px;
                "
              >
                <template>
                  <v-card-title class="pa-0">
                    <v-icon class="card_icon" size="20" color="orange" large left>
                      mdi-folder-outline
                    </v-icon>
                    <span class="pa-0 cardTitle"> Заявки </span>
                  </v-card-title>
                </template>
              </v-card>
            </template>
          </v-hover>
        </v-col>
        <v-col class="pa-0" md="2" sm="6" xs="12">
          <v-hover>
            <template v-slot="{ hover }">
              <v-card
                :class="`elevation-${hover ? 5 : 0}`"
                class="mr-4 mb-4 pa-3"
                @click="ActiveContet('positionpagetype')"
                style="
                  border: 1px solid #dce5ef;
                  background: #fbfcfe;
                  border-radius: 2px;
                "
              >
                <template>
                  <v-card-title class="pa-0">
                    <v-icon class="card_icon" size="20" color="orange" large left>
                      mdi-folder-outline
                    </v-icon>
                    <span class="pa-0 cardTitle"> Сертификаты </span>
                  </v-card-title>
                </template>
              </v-card>
            </template>
          </v-hover>
        </v-col>
        <v-col class="pa-0" md="2" sm="6" xs="12">
          <v-hover>
            <template v-slot="{ hover }">
              <v-card
                :class="`elevation-${hover ? 5 : 0}`"
                class="mr-4 mb-4 pa-3"
                @click="ActiveContet('coefficientpage')"
                style="
                  border: 1px solid #dce5ef;
                  background: #fbfcfe;
                  border-radius: 2px;
                "
              >
                <template>
                  <v-card-title class="pa-0">
                    <v-icon class="card_icon" size="20" color="orange" large left>
                      mdi-folder-outline
                    </v-icon>
                    <span class="pa-0 cardTitle"> Протоколы </span>
                  </v-card-title>
                </template>
              </v-card>
            </template>
          </v-hover>
        </v-col>
        <v-col class="pa-0" md="2" sm="6" xs="12">
          <v-hover>
            <template v-slot="{ hover }">
              <v-card
                :class="`elevation-${hover ? 5 : 0}`"
                class="mr-4 mb-4 pa-3"
                @click="ActiveContet('currencypage')"
                style="
                  border: 1px solid #dce5ef;
                  background: #fbfcfe;
                  border-radius: 2px;
                "
              >
                <template>
                  <v-card-title class="pa-0">
                    <v-icon class="card_icon" size="20" color="orange" large left>
                      mdi-folder-outline
                    </v-icon>
                    <span class="pa-0 cardTitle"> Докладные </span>
                  </v-card-title>
                </template>
              </v-card>
            </template>
          </v-hover>
        </v-col>
        <v-col class="pa-0" md="2" sm="6" xs="12">
          <v-hover>
            <template v-slot="{ hover }">
              <v-card
                :class="`elevation-${hover ? 5 : 0}`"
                class="mr-4 mb-4 pa-3"
                @click="ActiveContet('currencyhistorypage')"
                style="
                  border: 1px solid #dce5ef;
                  background: #fbfcfe;
                  border-radius: 2px;
                "
              >
                <template>
                  <v-card-title class="pa-0">
                    <v-icon class="card_icon" size="20" color="orange" large left>
                      mdi-folder-outline
                    </v-icon>
                    <span class="pa-0 cardTitle"> С угловым печатом </span>
                  </v-card-title>
                </template>
              </v-card>
            </template>
          </v-hover>
        </v-col>
        <v-col class="pa-0" md="2" sm="6" xs="12">
          <v-hover>
            <template v-slot="{ hover }">
              <v-card
                :class="`elevation-${hover ? 5 : 0}`"
                class="mr-4 mb-4 pa-3"
                style="
                  border: 1px solid #dce5ef;
                  background: #fbfcfe;
                  border-radius: 2px;
                "
              >
                <template>
                  <v-card-title class="pa-0">
                    <v-icon class="card_icon" size="20" color="orange" large left>
                      mdi-folder-outline
                    </v-icon>
                    <span class="pa-0 cardTitle"> Приказы </span>
                  </v-card-title>
                </template>
              </v-card>
            </template>
          </v-hover>
        </v-col>
        <v-col class="pa-0" md="2" sm="6" xs="12">
          <v-hover>
            <template v-slot="{ hover }">
              <v-card
                :class="`elevation-${hover ? 5 : 0}`"
                class="mr-4 mb-4 pa-3"
                @click="ActiveContet('personaltypepage')"
                style="
                  border: 1px solid #dce5ef;
                  background: #fbfcfe;
                  border-radius: 2px;
                "
              >
                <template>
                  <v-card-title class="pa-0">
                    <v-icon class="card_icon" size="20" color="orange" large left>
                      mdi-folder-outline
                    </v-icon>
                    <span class="pa-0 cardTitle"> Планы мероприятий </span>
                  </v-card-title>
                </template>
              </v-card>
            </template>
          </v-hover>
        </v-col>
        <v-col class="pa-0" md="2" sm="6" xs="12">
          <v-hover>
            <template v-slot="{ hover }">
              <v-card
                :class="`elevation-${hover ? 5 : 0}`"
                class="mr-4 mb-4 pa-3"
                @click="ActiveContet('expencetypepage')"
                style="
                  border: 1px solid #dce5ef;
                  background: #fbfcfe;
                  border-radius: 2px;
                "
              >
                <template>
                  <v-card-title class="pa-0">
                    <v-icon class="card_icon" size="20" color="orange" large left>
                      mdi-folder-outline
                    </v-icon>
                    <span class="pa-0 cardTitle"> АКТы </span>
                  </v-card-title>
                </template>
              </v-card>
            </template>
          </v-hover>
        </v-col>
        <v-col class="pa-0" md="2" sm="6" xs="12">
          <v-hover>
            <template v-slot="{ hover }">
              <v-card
                :class="`elevation-${hover ? 5 : 0}`"
                class="mr-4 mb-4 pa-3"
                @click="ActiveContet('requirementspage')"
                style="
                  border: 1px solid #dce5ef;
                  background: #fbfcfe;
                  border-radius: 2px;
                "
              >
                <template>
                  <v-card-title class="pa-0">
                    <v-icon class="card_icon" size="20" color="orange" large left>
                      mdi-folder-outline
                    </v-icon>
                    <span class="pa-0 cardTitle"> Претензии к дилерам </span>
                  </v-card-title>
                </template>
              </v-card>
            </template>
          </v-hover>
        </v-col>
        <v-col class="pa-0" md="2" sm="6" xs="12">
          <v-hover>
            <template v-slot="{ hover }">
              <v-card
                :class="`elevation-${hover ? 5 : 0}`"
                class="mr-4 mb-4 pa-3"
                @click="ActiveContet('requirementtypepage')"
                style="
                  border: 1px solid #dce5ef;
                  background: #fbfcfe;
                  border-radius: 2px;
                "
              >
                <template>
                  <v-card-title class="pa-0">
                    <v-icon class="card_icon" size="20" color="orange" large left>
                      mdi-folder-outline
                    </v-icon>
                    <span class="pa-0 cardTitle"> Служебные обрашении </span>
                  </v-card-title>
                </template>
              </v-card>
            </template>
          </v-hover>
        </v-col>
        <v-col class="pa-0" md="2" sm="6" xs="12">
          <v-hover>
            <template v-slot="{ hover }">
              <v-card
                :class="`elevation-${hover ? 5 : 0}`"
                class="mr-4 mb-4 pa-3"
                @click="ActiveContet('saptransactionpage')"
                style="
                  border: 1px solid #dce5ef;
                  background: #fbfcfe;
                  border-radius: 2px;
                "
              >
                <template>
                  <v-card-title class="pa-0">
                    <v-icon class="card_icon" size="20" color="orange" large left>
                      mdi-folder-outline
                    </v-icon>
                    <span class="pa-0 cardTitle"> Отчёты дилеров </span>
                  </v-card-title>
                </template>
              </v-card>
            </template>
          </v-hover>
        </v-col>
        <v-col class="pa-0" md="2" sm="6" xs="12">
          <v-hover>
            <template v-slot="{ hover }">
              <v-card
                :class="`elevation-${hover ? 5 : 0}`"
                class="mr-4 mb-4 pa-3"
                @click="ActiveContet('jointventurepage')"
                style="
                  border: 1px solid #dce5ef;
                  background: #fbfcfe;
                  border-radius: 2px;
                "
              >
                <template>
                  <v-card-title class="pa-0">
                    <v-icon class="card_icon" size="20" color="orange" large left>
                      mdi-folder-outline
                    </v-icon>
                    <span class="pa-0 cardTitle"> Draft Report </span>
                  </v-card-title>
                </template>
              </v-card>
            </template>
          </v-hover>
        </v-col>
        <v-col class="pa-0" md="2" sm="6" xs="12">
          <v-hover>
            <template v-slot="{ hover }">
              <v-card
                :class="`elevation-${hover ? 5 : 0}`"
                class="mr-4 mb-4 pa-3"
                @click="ActiveContet('jointventurepage')"
                style="
                  border: 1px solid #dce5ef;
                  background: #fbfcfe;
                  border-radius: 2px;
                "
              >
                <template>
                  <v-card-title class="pa-0">
                    <v-icon class="card_icon" size="20" color="orange" large left>
                      mdi-folder-outline
                    </v-icon>
                    <span class="pa-0 cardTitle"> Потребность </span>
                  </v-card-title>
                </template>
              </v-card>
            </template>
          </v-hover>
        </v-col>
        <v-col class="pa-0" md="2" sm="6" xs="12">
          <v-hover>
            <template v-slot="{ hover }">
              <v-card
                :class="`elevation-${hover ? 5 : 0}`"
                class="mr-4 mb-4 pa-3"
                @click="ActiveContet('jointventurepage')"
                style="
                  border: 1px solid #dce5ef;
                  background: #fbfcfe;
                  border-radius: 2px;
                "
              >
                <template>
                  <v-card-title class="pa-0">
                    <v-icon class="card_icon" size="20" color="orange" large left>
                      mdi-folder-outline
                    </v-icon>
                    <span class="pa-0 cardTitle"> Фирменный бланк </span>
                  </v-card-title>
                </template>
              </v-card>
            </template>
          </v-hover>
        </v-col>
        <v-col class="pa-0" md="2" sm="6" xs="12">
          <v-hover>
            <template v-slot="{ hover }">
              <v-card
                :class="`elevation-${hover ? 5 : 0}`"
                class="mr-4 mb-4 pa-3"
                @click="ActiveContet('jointventurepage')"
                style="
                  border: 1px solid #dce5ef;
                  background: #fbfcfe;
                  border-radius: 2px;
                "
              >
                <template>
                  <v-card-title class="pa-0">
                    <v-icon class="card_icon" size="20" color="orange" large left>
                      mdi-folder-outline
                    </v-icon>
                    <span class="pa-0 cardTitle"> Распоряжении </span>
                  </v-card-title>
                </template>
              </v-card>
            </template>
          </v-hover>
        </v-col>
        <v-col class="pa-0" md="2" sm="6" xs="12">
          <v-hover>
            <template v-slot="{ hover }">
              <v-card
                :class="`elevation-${hover ? 5 : 0}`"
                class="mr-4 mb-4 pa-3"
                @click="ActiveContet('jointventurepage')"
                style="
                  border: 1px solid #dce5ef;
                  background: #fbfcfe;
                  border-radius: 2px;
                "
              >
                <template>
                  <v-card-title class="pa-0">
                    <v-icon class="card_icon" size="20" color="orange" large left>
                      mdi-folder-outline
                    </v-icon>
                    <span class="pa-0 cardTitle"> Отчёты </span>
                  </v-card-title>
                </template>
              </v-card>
            </template>
          </v-hover>
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
import Swal from "sweetalert2";
export default {
  data() {
    return {
      loading: false,
    };
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 175;
    },
  },
  methods: {
    ActiveContet(selectedPage) {
      Object.keys(this.$data).forEach(page => {
        this[page] = page === selectedPage;
      });
    },
  },
  mounted() {},
};
</script>
<style scoped>
.fullHeight {
  height: calc(100% - 0px);
}
.heightFull {
  height: 83vh;
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
.dialog-content {
  color: #0868c4;
  font-size: 12px;
  font-weight: 400;
  font-style: italic;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
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
.cardTitle {
  color: #000;
  font-size: 12px;
  font-weight: 300;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.card_icon {
  font-size: 25px!important;
}
</style>
