<template>
  <div>
    <div style="text-align: right">
      <v-btn
        color="primary"
        @click="getHome"
        small
        icon
        style="position: relative; z-index: 999; padding: 5px"
        ><v-icon>mdi-home</v-icon></v-btn
      >
      <v-btn
        color="success"
        @click="capture"
        small
        icon
        style="position: relative; z-index: 999; padding: 5px"
        ><v-icon>mdi-download</v-icon></v-btn
      >
    </div>
    <div style="height: 90vh; text-align: center" class="pa-1">
      <zm-tree-org
        id="capture"
        v-if="!!data"
        :data="data"
        :collapsable="true"
        :draggable="true"
        :draggable-on-node="true"
        :label-style="{ borderRadius: '10px', boxShadow: 'none' }"
        :node-draggable="false"
        :only-one-node="false"
        @on-node-click="view"
        @on-expand="onExpand"
        menus="[{ text: 'gdfgd', command: 'copy' },{ name: 'hdf', command: 'add' },{ name: 'dfg', command: 'edit' },{ name: 'ert', command: 'delete' }]"
      >
        <template slot-scope="{ node }">
          <div class="my-card">
            <v-icon
              color="primary"
              class="chevron-up"
              @click="newView(node.parent_id)"
              v-if="node.root && node.parent_id"
              >mdi-chevron-up</v-icon
            >
            <v-icon
              color="primary"
              class="chevron-left"
              @click="newView(node.id)"
              v-if="!node.root"
              >mdi-chevron-left</v-icon
            >
            <v-icon
              color="primary"
              class="chevron-right"
              @click="newView(node.id)"
              v-if="!node.root"
              >mdi-chevron-right</v-icon
            >
            <v-avatar v-if="node.tabel == 'avatar'" class="avatar" size="63"
              ><v-icon>mdi-account</v-icon></v-avatar
            >
            <v-img
              v-else
              class="avatar"
              :src="$store.state.backend_url + 'get-img/' + node.tabel"
              :alt="node.tabel"
              contain
            ></v-img>
            <div class="my-content">
              <div
                class="my-sub-content1"
                :title="node.manager"
                v-if="node.manager"
              >
                {{ node.manager }}
              </div>
              <div class="my-sub-content1" :title="node.manager" v-else>
                Вакансия
              </div>
              <div class="my-sub-content2 text-truncate" :title="node.position">
                {{ node.position }}
              </div>
              <div class="my-sub-content3" :title="node.name">
                {{ node.name }}
              </div>
            </div>
          </div>
        </template>
        <template v-slot:expand="{ node }">
          <div style="color: #000">{{ node.children.length }}</div>
        </template>
      </zm-tree-org>
    </div>
    <v-navigation-drawer v-model="drawer" temporary absolute right>
      <div v-if="viewForm" class="pa-2">
        <div class="text-center">
          <v-avatar size="80">
            <v-img
              style="border: 1px solid #cde"
              :src="$store.state.backend_url + 'get-img/' + viewForm.tabel"
            ></v-img>
          </v-avatar>
        </div>
        <div
          class="text-center subtitle-1"
          :title="viewForm.manager ? viewForm.manager : 'Вакансия'"
        >
          {{ viewForm.manager ? viewForm.manager : "Вакансия" }}
        </div>
        <div class="text-center caption" :title="viewForm.position">
          {{ viewForm.position }}
        </div>
      </div>
      <v-divider></v-divider>
      <v-list
        dense
        v-if="viewForm && viewForm.children && viewForm.children.length"
      >
        <v-subheader>Подчиненные</v-subheader>
        <v-list-item
          v-for="children in showAll
            ? viewForm.children
            : viewForm.children
            ? viewForm.children.slice(0, 3)
            : []"
          :key="children.id"
          link
        >
          <v-list-item-avatar>
            <v-img
              style="border: 1px solid #cde"
              :src="$store.state.backend_url + 'get-img/' + children.tabel"
            ></v-img>
          </v-list-item-avatar>
          <v-list-item-content>
            <v-list-item-title
              :title="children.manager ? children.manager : 'Вакансия'"
              >{{
                children.manager ? children.manager : "Вакансия"
              }}</v-list-item-title
            >
            <v-list-item-subtitle :title="children.name">{{
              children.name
            }}</v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
        <v-subheader
          v-if="viewForm.children && viewForm.children.length - 3 > 0"
        >
          <v-btn x-small color="primary" text @click="showAll = !showAll">{{
            showAll
              ? "Показать три строки"
              : "Ещё " + (viewForm.children.length - 3) + " строки"
          }}</v-btn></v-subheader
        >
      </v-list>
      <v-divider></v-divider>
    </v-navigation-drawer>
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
import viewDepartment from "@/components/viewDepartment.vue";
import html2canvas from "html2canvas";
import $ from "jquery";
export default {
  components: {
    viewDepartment,
  },
  data() {
    return {
      id: null,
      parent_id: null,
      showAll: false,
      drawer: false,
      viewForm: null,
      items: [
        { title: "Home", icon: "mdi-view-dashboard" },
        { title: "About", icon: "mdi-forum" },
      ],
      documents: [],
      documentsDialog: false,
      documentsDialogData: {
        documents: [],
        staff_documents: [],
      },
      selectedDepertment: null,
      data: null,
      horizontal: false,
      collapsable: true,
      onlyOneNode: false,
      cloneNodeDrag: true,
      expandAll: false,
      disaled: false,
      loading: false,
    };
  },
  computed: {
    screenHeight() {
      return window.screen.height;
    },
  },
  methods: {
    newView(id) {
      // this.$router.push('/test-orgchart2/'+id);
      window.location =
        "https://edonew.uzautomotors.com/#/personcontrol/test-orgchart2/" + id;
      location.reload();
    },
    getHome() {
      // this.$router.push('/test-orgchart2/'+id);
      window.location = "https://edonew.uzautomotors.com/#/personcontrol/test-orgchart2/1";
      location.reload();
    },
    view($event, data) {
      if ($event.srcElement._prevClass == "my-sub-content1") {
        this.viewForm = data;
        this.drawer = true;
      }
    },
    actionShow() {},
    capture() {
      html2canvas($(".tree-org-node")[0], { useCORS: true, scale: 5 }).then(
        function (canvas) {
          var link = document.createElement("a");
          link.download = "OrganizationChart.png";
          link.href = canvas.toDataURL();
          document.body.appendChild(link);
          link.click();
          // clearDynamicLink(link);
        }
      );
    },
    onExpand($event, data, parent_id) {
      // console.log(parent_id, data);
      if (data.expand || data.id == null) {
        this.loading = true;
        axios
          .post(this.$store.state.backend_url + "api/org-chart-new2", {
            id: data.id,
            parent_id: !!parent_id ? parent_id : null,
          })
          .then((res) => {
            if (!!parent_id) {
              this.data = res.data[0];
              console.log(this.data);
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
    getView(data) {
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
    this.id = this.$route.params.id;
    this.onExpand(null, { id: null }, this.$route.params.id);
    setInterval(() => {
      $("li[action='delete']").remove();
      $("li[action='edit']").remove();
      $("li[action='add']").remove();
      $("li[action='copy']").remove();
      // $(".zm-tree-handle").remove();
      // $(".zoom-container").css("background-color", "red");
      $(".zm-draggable").css("width", "10000px");
      $(".zm-draggable").css("height", "10000px");
      // $(".zm-draggable").css("border", "1px");
      // $(".zm-draggable").css("border-style", "solid");
      $(".zm-draggable").css("border-color", "#def");
      // $(".zm-draggable").css("background-color", "#fcfdfe");
      $(".zm-draggable").css("padding", "10px");
      $(".zm-draggable").css("margin", "center");
      // $(".zoom-container").css("height", "4000px");
    }, 100);
  },
};
</script>
<style scoped>
.my-card {
  background: white;
  border-radius: 10px;
  border: 1px #dce5ef solid;
  width: 254px;
  height: 77px;
  display: flex;
  flex-wrap: nowrap;
}
.avatar {
  border-radius: 5px;
  border: 1px #dce5ef solid;
  margin: 5px;
  height: 65px;
  width: 65px;
}
.my-content {
  width: 180px;
  display: inline-block;
  text-align: center;
  /* border: 1px solid red; */
  text-align: left;
  margin-left: 5px;
  margin-top: 3px;
}
.my-sub-content1 {
  z-index: 999;
  position: absolute;
  cursor: pointer;
  color: #ff5c00;
  font-size: 13px;
  font-weight: 400;
  height: 18px;
  word-wrap: break-word;
}
.my-sub-content2 {
  margin-top: 20px;
  color: #476887;
  font-size: 9px;
  font-weight: 400;
  word-wrap: break-word;
  max-width: 170px;
}
.my-sub-content3 {
  color: black;
  font-size: 9px;
  font-weight: 600;
  word-wrap: break-word;
  margin-top: 10px;
  max-width: 170px;
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 2;
  white-space: normal;
  line-height: 10px;
}

.chevron-up {
  position: absolute;
  z-index: 999;
  margin-top: -16px;
  margin-left: 120px;
  cursor: pointer;
}
.chevron-left {
  position: absolute;
  z-index: 999;
  margin-top: 25px;
  margin-left: 238px;
  cursor: pointer;
}
.chevron-right {
  position: absolute;
  z-index: 999;
  margin-top: 25px;
  margin-left: -10px;
  cursor: pointer;
}
</style>
