<template>
  <div class="fullHeight">
    <v-card class="heightFull">
      <template>
        <v-parallax
          dark
          height="200"
          src="https://cdn.vuetifyjs.com/images/backgrounds/vbanner.jpg"
        >
        </v-parallax>
        <!--  -->
        <v-col cols="6" offset="3">
          <v-card style="height: 700px" class="headerSearch">
            <v-card-title class="text-h5">IT LIVE</v-card-title>
            <v-card-subtitle>
              Welcome! You can raise a request for IT LIVE using the options provided
            </v-card-subtitle>
            <template>
              <v-container id="dropdown-example-2">
                <span style="font-size: 12px; color: grey"
                  >What can we help you with?</span
                >
                <v-overflow-btn
                  v-model="filter.messageType"
                  class="my-2"
                  :items="itemMessages"
                  item-text="text"
                  item-value="id"
                ></v-overflow-btn>
                <p>
                  <span style="font-size: 12px; color: grey"
                    >Raise this request on behalf of<span style="color: red"
                      >*</span
                    ></span
                  >
                </p>
                <v-autocomplete
                  v-model="filter.authPerson"
                  :items="items"
                  outlined
                  clearable
                  item-text="text"
                  item-value="value"
                  filled
                  dense
                  style="margin: -15px 0px 0px 0px; border-radius: 2"
                >
                  <template v-slot:selection="data">
                    <v-chip :input-value="data.selected" @click="data.select">
                      <v-avatar left>
                        <v-img :src="data.item.imageUrl"></v-img>
                      </v-avatar>
                      {{ data.item.text }}
                    </v-chip>
                  </template>
                  <template v-slot:item="data">
                    <v-list-item-avatar>
                      <img :src="data.item.imageUrl" />
                    </v-list-item-avatar>
                    <v-list-item-content v-text="data.item.text"></v-list-item-content>
                  </template>
                </v-autocomplete>
                <p style="margin: -10px 0px 0px 0px">
                  <span style="font-size: 12px; color: grey"
                    >Summary<span style="color: red">*</span></span
                  >
                </p>
                <v-text-field
                  auto-grow
                  outlined
                  dense
                  style="border-radius: 2"
                ></v-text-field>
                <p style="margin: -10px 0px 0px 0px">
                  <span style="font-size: 12px; color: grey"
                    >What are the details of your request?<span style="color: red"
                      >*</span
                    ></span
                  >
                </p>
                <vue2-tinymce-editor
                  v-model="document_detail"
                  :options="options"
                  v-if="true"
                ></vue2-tinymce-editor>
                <v-divider class="my-2" style="border-color: #dce5ef"></v-divider>
                <v-btn depressed color="primary" @click="$router.push('/support/queues/custom')"> Send </v-btn>
                <v-btn class="my-1 ml-2" depressed color="#ECEFF1"> Cencel </v-btn>
              </v-container>
            </template>
          </v-card>
        </v-col>

        <!--  -->
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
    document_detail: null,
    filter: {
      messageType: 0,
      authPerson: 0,

    },
    editorOption: {},
    items: [
      {
        value: 0,
        text: "7485 Tursunov Botirjon Muxtorjonovich",
        imageUrl: "https://cdn.vuetifyjs.com/images/lists/1.jpg",
      },
      {
        value: 1,
        text: "7486 Baxrom Nazarov Abdulxakimovich ",
        imageUrl: "https://cdn.vuetifyjs.com/images/lists/2.jpg",
      },
      {
        value: 2,
        text: "7487 Salimov Mirzoxid Abdukarimovich",
        imageUrl: "https://cdn.vuetifyjs.com/images/lists/2.jpg",
      },
    ],
    dropdown_items1: [
      {
        text: "7485 Tursunov Botirjon",
        icon: "mdi-account",
        callback: () => console.log("Option 1 Item"),
      },
      { text: "Item 2", icon: "mdi-bell", callback: () => console.log("Option 2 Item") },
      { text: "Item 3", icon: "mdi-email", callback: () => console.log("Option 3 Item") },
      // Add more items with text and icon properties as needed
    ],
    itemMessages: [
      {
        id: 1,
        text: "Submit a request or incident",
        subtext: "Whitsunday Island, Whitsunday Islands",
        path: "/support/message",
        icon: "mdi-web",
      },
      {
        id: 2,
        text: "Ask a question",
        subtext: "Have aquestion? Submit it here",
        // path: "/staffs/employees/vacancy",
        icon: "mdi-message-text-outline",
      },
    ],
    options: {
      menubar: false,
      plugins:
        "fullscreen advlist autolink charmap code codesample directionality emoticons preview table lists hr searchreplace",
      toolbar:
        "fontsizeselect | bold italic underline| alignleft aligncenter alignright alignjustify | numlist bullist | outdent indent | removeformat",
      fontsize_formats: "8pt 10pt 12pt 14pt 16pt 18pt 24pt 36pt",
      // formats: {
      //   removeformat: [{ selector: "h1,h2,h3,h4,h5,h6,h7,span,p", remove: "all" }],
      // },
      visualblocks_default_state: true,
      // forced_root_block: "p",
      content_style:
        "body { font-family:Times,sans-serif; font-size:14pt } p{margin:0px; padding:0px;} h2{font-weight:normal;} .indent{ text-indent:40px;}",
      height: "250px",
      language: "ru",
      resize: false,
    },
  }),
  computed: {
    screenHeight() {
      return window.innerHeight - 180;
    },
    headers() {
      return [];
    },
  },
  methods: {},
  mounted() {
    this.filter.messageType=parseInt(this.$route.params.messageType);
    this.filter.authPerson =1;
  },
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
</style>
