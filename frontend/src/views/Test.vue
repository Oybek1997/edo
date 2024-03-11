<template>
  <div class="fullHeight">
    <div style="height: 90vh; border: 1px solid #fff" class="pa-1">
      <zm-tree-org
        v-if="!!data"
        :data="data"
        :disabled="disaled"
        :horizontal="horizontal"
        :collapsable="collapsable"
        :label-style="style"
        :node-draggable="false"
        :only-one-node="onlyOneNode"
        @on-expand="onExpand"
        @on-node-click="onNodeClick"
      >
        <template slot-scope="{ node }">
          <v-hover v-slot="{ hover }">
            <v-card
              :elevation="hover ? 12 : 0"
              class=""
              width="250"
              color="#fafaff"
              outline
            >
              <v-row class="ma-0 pa-0">
                <v-col cols="3" class="ma-0 pa-0">
                  <v-card width="40" height="70" class="ma-1">
                    <v-img
                      v-if="node.tabel"
                      :src="$store.state.backend_url + 'get-img/' + node.tabel"
                      alt="9777"
                      height="50"
                      :aspect-ratio="5 / 4"
                    ></v-img>
                    {{ node.tabel }}
                  </v-card>
                </v-col>
                <v-col cols="9" class="text-left ma-0 pa-0 mt-1" style="font-size: 14px">
                  <p class="font-weight-black ma-0 caption">
                    {{ node.manager }}
                  </p>
                  <p class="mr-2 my-0 caption d-inline" style="font-size: 12px">
                    {{ node.code }}
                  </p>
                  <p class="mr-2 my-0" style="font-size: 10px">{{ node.label }}</p>
                </v-col>
              </v-row>
              <v-row class="ma-0 pa-0">
                <v-col class="ma-0 pa-0">
                  <v-hover v-slot="{ hover }">
                    <v-card
                      :elevation="hover ? 12 : 0"
                      :style="'cursor: pointer;'+(node.show_employee ? 'border:1px solid black;' : '')"
                      @click="node.show_employee = !node.show_employee"
                    >
                      <v-icon class="mx-1" color="blue" title="Please double click"
                        >mdi-account-outline</v-icon
                      >
                    </v-card>
                  </v-hover>
                </v-col>
                <v-col class="ma-0 pa-0">
                  <v-hover v-slot="{ hover }">
                    <v-card :elevation="hover ? 12 : 0" style="cursor: pointer">
                      <v-icon
                        @dblclick="view(node)"
                        class="mx-1"
                        color="blue"
                        title="Please double click"
                        >mdi-information-outline</v-icon
                      >
                    </v-card>
                  </v-hover>
                </v-col>
                <v-col class="ma-0 pa-0">
                  <v-hover v-slot="{ hover }">
                    <v-card :elevation="hover ? 12 : 0" style="cursor: pointer">
                      <v-icon
                        @dblclick="view(node)"
                        class="mx-1"
                        color="blue"
                        title="Please double click"
                        >mdi-file-document-outline</v-icon
                      >
                    </v-card>
                  </v-hover>
                </v-col>
              </v-row>
              <v-row v-show="node.show_employee" class="ma-0 pa-0">
                <v-col style="position:fixed; background-color:#cde; z-index:999; width:300px; border:1px solid black;" class="ma-0 pa-0">
                  <v-list dense style="text-align:left;">
                    <v-list-item-group color="primary" style="background:#fff; z-index:999;">
                      <v-list-item v-for="(item, i) in node.employees" :key="i" class="pa-1" :to="'personcontrol/profile/'+item.id" target="_blank">
                        <v-list-item-icon class="ma-1">
                          <v-avatar
                            size="30"
                          >
                            <img :src="$store.state.backend_url + 'get-img/' + item.tabel">
                          </v-avatar>
                        </v-list-item-icon>
                        <v-list-item-content class="ma-0 pa-0">
                          <v-list-item-title v-text="item.fullname"></v-list-item-title>
                          <v-list-item-subtitle>{{item.staff[0].position.name}}</v-list-item-subtitle>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list-item-group>
                  </v-list>
                </v-col>
              </v-row>
            </v-card>
          </v-hover>
        </template>
        <template v-slot:expand="{ node }">
          <div style="color: #000">{{ node.children.length }}</div>
        </template>
      </zm-tree-org>
    </div>
    <v-dialog v-model="loading" width="300" hide-overlay>
      <v-card color="primary" dark>
        <v-card-text>
          {{ $t("loadingText") }}
          <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="documentsDialog"
      scrollable
      fullscreen
      persistent
      :overlay="false"
      max-width="500px"
      transition="dialog-transition"
    >
      <v-card>
        <v-card-title primary-title>
          Documents
          <v-spacer></v-spacer>
          <v-btn color="red" icon x-small @click="documentsDialog = false"
            ><v-icon>mdi-close</v-icon></v-btn
          >
        </v-card-title>
        <v-card-text>
          <v-row>
            <v-col>
              <v-simple-table>
                <template v-slot:default>
                  <thead>
                    <tr>
                      <th class="text-left">
                        ID
                      </th>
                      <th class="text-left">
                        Document number
                      </th>
                      <th class="text-left">
                        Document date
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="item in documentsDialogData.documents"
                      :key="item.id"
                    >
                      <td>{{ item.id }}</td>
                      <td>{{ item.document_number }}</td>
                      <td>{{ item.document_date }}</td>
                    </tr>
                  </tbody>
                </template>
              </v-simple-table>
            </v-col>
            <v-col>
              <v-simple-table>
                <template v-slot:default>
                  <thead>
                    <tr>
                      <th class="text-left">
                        ID
                      </th>
                      <th class="text-left">
                        Document number
                      </th>
                      <th class="text-left">
                        Document date
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="item in documentsDialogData.staff_documents"
                      :key="item.id"
                    >
                      <td>{{ item.id }}</td>
                      <td>{{ item.document_number }}</td>
                      <td>{{ item.document_date }}</td>
                    </tr>
                  </tbody>
                </template>
              </v-simple-table>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
const axios = require("axios").default;
import viewDepartment from "../components/viewDepartment.vue";
export default {
  components: {
    viewDepartment,
  },
  data() {
    return {
      documents: [],
      documentsDialog: false,
      documentsDialogData: {
        documents:[],
        staff_documents:[],
      },
      selectedDepertment: null,
      data: null,
      horizontal: false,
      collapsable: true,
      onlyOneNode: true,
      cloneNodeDrag: true,
      expandAll: false,
      disaled: false,
      loading: false,
      style: {
        background: "#cde",
        color: "#5e6d82",
      },
    };
  },
  methods: {
    onExpand(e, data) {
      if (data.expand || data.id == null) {
        this.loading = true;
        axios
          .post(this.$store.state.backend_url + "api/departments-org-chart", {
            id: data.id,
          })
          .then((res) => {
            if (data.id == null) {
              this.data = res.data[0];
            } else {
              data.children = res.data;
            }
            this.loading = false;
          })
          .catch((error) => {
            console.log(error);
            this.loading = false;
          });
      }
    },
    onNodeClick(e, data) {},
    view(data) {
      console.log(data);
      this.loading = true;
        axios
          .post(this.$store.state.backend_url + "api/departments-get-documents", {
            id: data.id,
          })
          .then((res) => {
            this.documentsDialogData.documents = res.data[0];
            this.documentsDialogData.staff_documents = res.data[1];
            this.loading = false;
            this.documentsDialog = true;
          })
          .catch((error) => {
            console.log(error);
            this.loading = false;
          });
    },
    expandChange() {
      this.toggleExpand(this.data, this.expandAll);
    },
    toggleExpand(data, val) {
      if (Array.isArray(data)) {
        data.forEach((item) => {
          this.$set(item, "expand", val);
          if (item.children) {
            this.toggleExpand(item.children, val);
          }
        });
      } else {
        this.$set(data, "expand", val);
        if (data.children) {
          this.toggleExpand(data.children, val);
        }
      }
    },
  },
  mounted() {
    this.onExpand(null, { id: null });
  },
};
</script>
