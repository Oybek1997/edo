<template>
  <div class="fullHeight">
    <v-card
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-5 pt-3 pb-0">
        <span class="headerTitle mb-3">{{ $t("Other Settings") }}</span>
        <!-- <div class="headerSearch d-flex align-center">
          <v-text-field
            v-model="search"
            prepend-inner-icon="mdi-magnify"
            class="txt_search1"
            style="width: 100px !important"
            :placeholder="$t('search')"
            @keyup.enter="getList"
            dense
            hide-details
            solo
          ></v-text-field>
          <v-menu
            transition="slide-y-transition"
            left
            
            :close-on-content-click="false"
            :nudge-width="50"
            offset-y
            v-if="$store.getters.checkPermission('organizations-create')"
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
                  @click="newItem"
                >
                  <v-list-item-title>
                    <v-icon size="18">mdi-plus</v-icon>
                  </v-list-item-title></v-list-item
                >
                <v-list-item
                  style="margin: 0px; max-height: 34px; min-height: 34px"
                  @click="0"
                >
                  <v-list-item-title>
                    <v-icon color="#107C41" size="18"
                      >mdi-microsoft-excel</v-icon
                    >
                    Excel
                  </v-list-item-title></v-list-item
                >
              </v-list>
            </v-card>
          </v-menu>
        </div> -->
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
                    <span class="pa-0 cardTitle"> Типы подразделений </span>
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
                    <span class="pa-0 cardTitle"> Должности </span>
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
                    <span class="pa-0 cardTitle"> Типы должностей </span>
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
                    <span class="pa-0 cardTitle"> Надбавки </span>
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
                    <span class="pa-0 cardTitle"> Тип персонала </span>
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
                    <span class="pa-0 cardTitle"> Тип расходов </span>
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
                    <span class="pa-0 cardTitle"> Требовании к работе </span>
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
                    <span class="pa-0 cardTitle"> Типы требований </span>
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
                    <span class="pa-0 cardTitle"> Предприятии отрасли </span>
                  </v-card-title>
                </template>
              </v-card>
            </template>
          </v-hover>
        </v-col>
      </v-row>
      <v-row class="mx-0 px-5 pt-0 pb-5">
        <v-col class="ma-0 pa-0" xs="12">
          <departmenttype-index
            v-if="departmenttypepage"
          ></departmenttype-index>
          <position-index v-if="positionpage"></position-index>
          <positiontype-index v-if="positionpagetype"></positiontype-index>
          <coefficient-index v-if="coefficientpage"></coefficient-index>
          <personaltype-index v-if="personaltypepage"></personaltype-index>
          <expencetype-index v-if="expencetypepage"></expencetype-index>
          <requirements-index v-if="requirementspage"></requirements-index>
          <requirementtype-index
            v-if="requirementtypepage"
          ></requirementtype-index>
          <jointventure-index v-if="jointventurepage"></jointventure-index>
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
import DepartmenttypeIndex from "@/views/departmentType/Index";
import PositionIndex from "@/views/position/Index";
import PositiontypeIndex from "@/views/positionType/Index";
import CoefficientIndex from "@/views/coefficient/Index";
import PersonaltypeIndex from "@/views/personalType/Index";
import ExpencetypeIndex from "@/views/expenceType/Index";
import RequirementsIndex from "@/views/requirements/Index";
import RequirementtypeIndex from "@/views/requirementType/Index";
import JointventureIndex from "@/views/jointVenture/Index.vue";

const axios = require("axios").default;
import Swal from "sweetalert2";
export default {
  components: {
    DepartmenttypeIndex,
    PositionIndex,
    PositiontypeIndex,
    CoefficientIndex,
    PersonaltypeIndex,
    ExpencetypeIndex,
    RequirementsIndex,
    RequirementtypeIndex,
    JointventureIndex,
  },
  data() {
    return {
      loading: false,
      currencypage: false,
      departmenttypepage: true,
      positionpage: false,
      positionpagetype: false,
      coefficientpage: false,
      currencyhistorypage: false,
      personaltypepage: false,
      expencetypepage: false,
      requirementspage: false,
      requirementtypepage: false,
      saptransactionpage: false,
      jointventurepage: false,
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
