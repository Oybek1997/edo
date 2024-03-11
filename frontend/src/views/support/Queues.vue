<template>
  <div class="fullHeight">
    <v-card class="heightFull">
      <template>
        <v-card-subtitle style="font-size: 12px"
          >Project / IT LIVE / Queues</v-card-subtitle
        >
        <v-card-title class="text-h5" style="margin: -25px 0px 0px 0px"
          >All open</v-card-title
        >
        <v-card-subtitle> 0 issues</v-card-subtitle>
        <v-col>
          <v-data-table
            :headers="headers"
            :items="desserts"
            :items-per-page="5"
            :height="screenHeight"
            :single-select="singleSelect"
            show-select
          >
            <template v-slot:[`item.messageType`]="{ item }">
              <template v-if="item.messageType == 1">
                <v-icon color="indigo" small>mdi-comment-text</v-icon>
              </template>
              <template v-if="item.messageType == 2">
                <v-icon color="orange" small>mdi-comment-question</v-icon>
              </template>
            </template>
            <template v-slot:[`item.action`]="{ item }">
              <v-btn
              text
              small
              
              class="ma-0 pa-0"
              min-width="0"              
            >
              <v-icon color="#757575" dark>mdi-view-split-vertical</v-icon>
            </v-btn>       
            </template>
            <template v-slot:item.status="{ item, index }" style>
          <!-- {{ item.choice_status[item.choice_status.length - 1].status }} -->
          <span
            v-if="item.status == 1"
            class="tdClass1 text-center"
            style="background: #FFAB91;color:white"
          >
          {{ statusType.find((v) => v.value == item.status).text }}
          </span>
          <span
          v-if="item.status == 2"
            class="tdClass1 text-center"
            style="background: #FFF9C4"
          >
          {{ statusType.find((v) => v.value == item.status).text }}
          </span>
          <span
          v-if="item.status == 3"
            class="tdClass1 text-center"
            style="background: #4DB6AC;color:white"
          >
          {{ statusType.find((v) => v.value == item.status).text }}
          </span>
        </template>
          </v-data-table>
        </v-col>
      </template>
    </v-card>
  </div>
</template>
<script>
import { Vue2TinymceEditor } from "vue2-tinymce-editor";
export default {
  components: {
    Vue2TinymceEditor,
  },
  data: () => ({
    singleSelect: false,
    document_detail: null,
    filter: null,
    editorOption: {},
    items: [],
    statusType:[
      {value:1,text:'Open'},
      {value:2,text:'Inprogress'},
      {value:3,text:'Done'},
    ],
    headers: [
      {
        text: "T", align: "center",
        value: "messageType",
        align: "start",
        sortable: false,
      },
      { text: "Key", align: "center", value: "key" },
      { text: "Summary",  align: "center",value: "summary" },
      { text: "",  value: "action" },
      { text: "Reporter", align: "center",value: "reporter"},
      { text: "Assignee",  align: "center",value: "assignee" },
      { text: "Status",  align: "center",value: "status",width:50 },
      // { text: "Created_by", value: "created_by" },
      { text: "Created_at",  align: "center",value: "created_at" },
    ],
    desserts: [
      {
        messageType: 1,
        key: "Life 1",
        summary: "Question 1",
        action: 1,
        reporter: "Kobiljon Yusupov",
        assignee: "Soliev Akmal",
        status: 1,
        created_by: 4.0,
        created_at: 'Feb 28 10:45 AM',
      },
      {
        messageType: 1,
        key: "Life 2",
        summary: "Question 2",
        action: 1,
        reporter: "Kobiljon Yusupov",
        assignee: "Mirzayev Karim",
        status: 1,
        created_by: 4.0,
        created_at: 'Feb 29 10:45 AM',
      },
      {
        messageType: 2,
        key: "Life 3",
        summary: "Question 3",
        action: 1,
        reporter: "Kobiljon Yusupov",
        assignee: "Solijon Ashurov",
        status: 3,
        created_by: 4.0,
        created_at: 'Feb 20 10:45 AM',
      },
      {
        messageType: 2,
        key: "Life 4",
        summary: "Question 4",
        action: 1,
        reporter: "Kobiljon Yusupov",
        assignee: "Sherzod Umidov",
        status: 2,
        created_by: 4.0,
        created_at: 'Feb 23 10:45 AM',
      },
      {
        messageType: 1,
        key: "Life 5",
        summary: "Question 5",
        action: 1,
        reporter: "Kobiljon Yusupov",
        assignee: "Jasur Aliyev",
        status: 2,
        created_by: 4.0,
        created_at: 'Feb 25 10:45 AM',
      },
    ],
  }),
  computed: {
    screenHeight() {
      return window.innerHeight - 240;
    },
  },
  methods: {},
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
  margin: -150px 0px 0px 0px;
  /* border:1px solid red; */
}
.txt_ss {
  /* border: 1px solid #e6e6e6; */
  box-shadow: none;
  max-height: 100%;
  overflow: hidden;
  border-radius: 5px 0px 0px 5px;
  font-size: 16px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.txt_search1 {
  border: 1px solid #e6e6e6;
  box-shadow: none;
  max-height: 100%;
  overflow: hidden;
  border-radius: 0px 0px 0px 0px;
  color: red;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.theme--light.v-chip:not(.v-chip--active) {
  background: none;
}
.v-chip.v-size--default {
  border-radius: 0px;
}
.theme--light.v-chip {
  border-color: none;
  /* color: rgba(0, 0, 0, 0.87); */
}
.v-avatar {
  border-radius: 0px;
}
.tdClass1 {
  display: block;
  color: #fff;
  color: rgb(3, 3, 3);
  width: 100%;
  height: 25px;
  border-radius: 5px;
  padding: 1px 5px;
}
</style>
