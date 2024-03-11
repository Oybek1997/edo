<template>
  <div class="fullHeight" style="height: 85vh;">
    <v-card
      class="heightFull"
      style="border-radius: 10px; border: 1px solid #dce5ef"
      elevation="0"
    >
      <v-card-title class="px-4 py-3" style="background: #CEE3F2;">
        <span class="headerTitle">{{ $t('Корпоративный чат') }}</span>
        <v-spacer></v-spacer>
        <span class="headerTitle mr-6" style="display: flex; align-items: center;">
            <v-icon size="18" color="#070707" class="mr-1">mdi-cog-outline</v-icon>
            Настройки
        </span>
        <span class="headerTitle mr-5" style="display: flex; align-items: center;">
            <v-icon size="18" color="#14FF00" class="mr-1">mdi-checkbox-blank-circle</v-icon>
            Онлайн
        </span>
      </v-card-title>
      <v-row class="mx-0">
        <v-col class="ma-0 pa-0" cols="3" style="border-right: 1px solid #DCE5EF;">
          <div class="pa-3">
<!--            <v-text-field-->
<!--              class="txt_search"-->
<!--              v-model="search"-->
<!--              :placeholder="$t('Поиск...')"-->
<!--              @keyup.native.enter="getList"-->
<!--              dense-->
<!--              hide-details-->
<!--              solo-->
<!--              elevation="0"-->
<!--            >-->
<!--              <template v-slot:append>-->

<!--                &lt;!&ndash; <v-btn color="primary" @click="sendMessage"> &ndash;&gt;-->
<!--                <v-icon class="mr-1" style="font-size: 20px; cursor: pointer; color: #676768;" @click="addUser">-->
<!--                  mdi-account-plus-outline-->
<!--                </v-icon>-->
<!--                &lt;!&ndash; </v-btn> &ndash;&gt;-->
<!--              </template>-->
<!--            </v-text-field>-->

<!--            <select-user v-model="form.responsible_id"></select-user>-->
<!--            &lt;!&ndash; <v-btn color="primary" @click="sendMessage"> &ndash;&gt;-->
<!--            <v-icon class="mr-1" style="font-size: 20px; cursor: pointer; color: #676768;" @click="addUser">-->
<!--              mdi-account-plus-outline-->
<!--            </v-icon>-->
<!--            &lt;!&ndash; </v-btn> &ndash;&gt;-->


            <div style="display: flex; align-items: center;">
              <select-user v-model="form.responsible_id" style="flex: 2;"></select-user>
<!--              <v-icon class="mr-1" style="font-size: 20px; cursor: pointer; color: #676768;" @click="addUser">-->
<!--                mdi-account-plus-outline-->
<!--              </v-icon>-->
            </div>



          </div>
          <v-list class="py-0">
            <v-list-item-group>
              <v-divider color="#DCE5EF"></v-divider>
              <v-list-item class="px-3">
                <v-list-item-avatar>
                  <img style="border: 3px solid #B5C3CF;" src="../../assets/User-Default.jpg"/>
                </v-list-item-avatar>
                <v-list-item-content>
                  <v-list-item-title class="chat_item_title" style="color:#000; font-weight: 600;">Аллаберганов А
                  </v-list-item-title>
                  <v-list-item-subtitle></v-list-item-subtitle>
                </v-list-item-content>
                <v-list-item-action class="my-1">
                  <v-icon
                    style="margin-top: -15px; color: #A29C9C;"
                    color=""
                  >
                    mdi-settings-helper
                  </v-icon>
                  <v-list-item-action-text>
                    <v-badge
                      style="position: absolute; bottom: 10px; right: 30px; font-size: 10px;!important"
                      color="#2C8DFF"
                      content="2"
                    >
                    </v-badge>
                  </v-list-item-action-text>
                </v-list-item-action>
              </v-list-item>
              <v-divider color="#DCE5EF"></v-divider>
              <v-list-item class="px-3">
                <v-icon size="10" color="#14FF00" class="mr-1"
                        style=" position: absolute; left: 11px; bottom: 10px; z-index: 1; border: 1px solid #fff; border-radius: 10px;">
                  mdi-checkbox-blank-circle
                </v-icon>
                <v-list-item-avatar class="mr-3">
                  <img style="border: 3px solid #B5C3CF;" src="../../assets/User-Default.jpg"/>
                </v-list-item-avatar>
                <v-list-item-content>
                  <v-list-item-title class="chat_item_title">Юсупов Кобилжон</v-list-item-title>
                  <v-list-item-subtitle></v-list-item-subtitle>
                </v-list-item-content>
                <v-list-item-action class="my-1">
                  <v-icon
                    style="margin-top: -15px; color: #A29C9C;"
                    color=""
                  >
                    mdi-settings-helper
                  </v-icon>
                  <v-list-item-action-text>
                    <v-badge
                      style="position: absolute; bottom: 10px; right: 30px;"
                      color="#2C8DFF"
                      content="13"
                    >
                    </v-badge>
                  </v-list-item-action-text>
                </v-list-item-action>
              </v-list-item>
              <v-divider color="#DCE5EF"></v-divider>
              <v-list-item class="px-3">
                <v-list-item-avatar>
                  <img style="border: 3px solid #B5C3CF;" src="../../assets/User-Default.jpg"/>
                </v-list-item-avatar>
                <v-list-item-content>
                  <v-list-item-title class="chat_item_title">Ниёзов Хуршид</v-list-item-title>
                  <v-list-item-subtitle></v-list-item-subtitle>
                </v-list-item-content>
                <v-list-item-action class="my-1">
                  <v-icon
                    style="margin-top: -20px; color: #A29C9C;"
                    color=""
                  >
                    mdi-settings-helper
                  </v-icon>
                  <v-list-item-action-text>
                    <v-badge
                      v-if="false"
                      style="position: absolute; bottom: 8px; right: 30px;"
                      color="#2C8DFF"
                      content="2"
                    >
                    </v-badge>
                  </v-list-item-action-text>
                </v-list-item-action>
              </v-list-item>
              <v-divider color="#DCE5EF"></v-divider>
            </v-list-item-group>
          </v-list>
        </v-col>
        <v-col class="ma-0 pa-4" cols="9">
          <v-card
            class=""
            style="border-radius: 10px; border: 1px solid #dce5ef"
            elevation="0"
          >
            <v-card-title class="px-4 py-3" style="background: #fff; border-bottom: 1px solid #DCE5EF;">
              <span class="chat_item_title mr-3" style="color:#000; font-weight: 600;">Аллаберганов А</span>
              <span class="chat_item_title" style="color:#676768; font-size: 11px;">5 минут назад</span>
              <!-- <span class="headerTitle" style="color:#000; font-weight: 600;">{{ $t('Аллаберганов А') }}</span> -->
              <v-spacer></v-spacer>
              <!-- <span class="headerTitle mr-6" style="display: flex; align-items: center;">
                  <v-icon size="18" color="#070707" class="mr-1">mdi-cog-outline</v-icon>
                  Настройки
              </span> -->
              <!-- <span class="headerTitle mr-5" style="display: flex; align-items: center;">
                  <v-icon size="18" color="#14FF00" class="mr-1">mdi-checkbox-blank-circle</v-icon>
                  Онлайн
              </span> -->
            </v-card-title>
            <v-row class="mx-0">
              <v-col class="ma-0 pa-3" cols="12" style="">
                <p class="text-center">
                  <span class="chat_item_title" style="color:#676768; font-size: 12px;">13.02.2024</span>
                </p>
                <div class="mb-1">
                  <a href="#" class="pull-left mr-2">
                    <img style="border: 0px solid #B5C3CF; border-radius: 15px; width: 30px; height: 30px;"
                         src="https://flatfull.com/themes/flatkit/assets/images/a2.jpg"/>
                  </a>
                  <div class="clear text-left" style="display: block; overflow: hidden;">
                    <div>
                      <div class="mt-1 px-3 py-1"
                           style="border: 1px solid #B5C3CF; background-color: #fff; border-radius: 4px; color: #000; display: inline-block;">
                        Dlor soe isep slie gosese..
                      </div>
                    </div>
                    <div class="m-t-xs">
                      <div class="mt-1 px-3 py-1"
                           style="border: 1px solid #B5C3CF; background-color: #fff; border-radius: 4px; color: #000; display: inline-block;">
                        Loris aspim..
                      </div>
                    </div>
                    <div class="text-muted mt-1" style="font-size: 11px;">5 seconds ago</div>
                  </div>
                </div>
                <div class="mb-1">
                  <a href="#" class="pull-right ml-2">
                    <img style="border: 0px solid #B5C3CF; border-radius: 15px; width: 30px; height: 30px;"
                         src="https://flatfull.com/themes/flatkit/assets/images/a1.jpg"/>
                  </a>
                  <div class="clear text-right" style="display: block; overflow: hidden;">
                    <div>
                      <div class="mt-1 px-3 py-1"
                           style="background-color: #6887ff; border-radius: 4px; color: #fff; display: inline-block;">
                        Dlor soe isep slie gosese..
                      </div>
                    </div>
                    <div class="m-t-xs">
                      <div class="mt-1 px-3 py-1"
                           style="background-color: #6887ff; border-radius: 4px; color: #fff; display: inline-block;">
                        Loris aspim..
                      </div>
                    </div>
                    <div class="text-muted mt-1" style="font-size: 11px;">Just now</div>
                  </div>
                </div>
              </v-col>
              <v-row class="mx-0 mb-5">
                <v-col class="mx-0 pa-3" cols="12" style="margin-top: 220px;">
                  <div>
                    <v-textarea
                      auto-grow
                      outlined
                      rows="1"
                      row-height="15"
                      hide-details
                      class="sendMessage"
                    >
                      <template v-slot:append>
                        <!-- <v-btn color="primary" @click="sendMessage"> -->
                        <v-icon class="mr-2" style="cursor: pointer; color: #C2BED4;" @click="sendMessage">
                          mdi-emoticon-happy-outline
                        </v-icon>
                        <v-icon style="cursor: pointer; color: #447498;" @click="sendMessage">mdi-telegram</v-icon>
                        <!-- </v-btn> -->
                      </template>
                    </v-textarea>
                  </div>
                </v-col>
                <v-col class="ma-0 pa-3" cols="6">
                  <v-container class="pa-0">
                    <vue-dropzone
                      class="dropzone_margin pa-0"
                      style="color: grey; border: 1px solid #F3F3F3; width: 100%; min-height: 50px; border-radius: 7px;"
                      ref="myVueDropzone"
                      id="dropzone"
                      height="10px"
                      :options="dropzoneOptions"
                      @vdropzone-success="handleSuccess"
                      @vdropzone-complete="handleComplete"
                      v-on:vdropzone-removed-file="removeThisFile"
                    ></vue-dropzone>
                  </v-container>
                </v-col>
                <v-col class="ma-0 pa-3" cols="6">
                  <ul class="chat_item_list">
                    <li>Невозможно отправить файлы размером больше 2Mb</li>
                    <li>Отправленные файлы будут удаляться через 24 часа после отправки</li>
                    <li>Не рекомендуется использовать эту систему в качестве файлообменника</li>
                    <li>Не рекомендуем отправлять файлы личного характера.</li>
                  </ul>
                </v-col>
              </v-row>
            </v-row>
          </v-card>
        </v-col>
      </v-row>
    </v-card>
    <v-dialog v-model="loading" width="300" hide-overlay>
      <v-card color="primary" dark>
        <v-card-text>
          {{ $t('loadingText') }}
          <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
const axios = require("axios").default;
import Swal from "sweetalert2";
import vue2Dropzone from "vue2-dropzone";
import "vue2-dropzone/dist/vue2Dropzone.min.css";

export default {
  components: {
    vueDropzone: vue2Dropzone,
  },
  data() {
    return {
      dropzoneOptions: {
        url: "https://httpbin.org/post",
        maxFilesize: 1.5,
        addRemoveLinks: true,
        dictDefaultMessage:
          "<img style='height:20px; margin: -30px 0px -20px 0px;' src='img/cloud-upload-outline.png'> Перетащите файлы или <label  style='color:blue'>загрузите с локальной папки<p style='color:red'>(*Максимальный размер файла- 1.5мб)</p></label>",
      },
      options: {
        menubar: true,
        plugins:
          "fullscreen advlist autolink charmap code codesample directionality emoticons preview table lists hr searchreplace",
        toolbar1:
          "fullscreen preview code | undo redo | fontsizeselect bold italic underline strikethrough forecolor backcolor | alignleft aligncenter alignright alignjustify | lineheight numlist bullist | outdent indent | link table removeformat hr customInsertButton",
        fontsize_formats: "8pt 10pt 12pt 14pt 16pt 18pt 24pt 36pt",
        formats: {
          removeformat: [
            {selector: "h1,h2,h3,h4,h5,h6,h7,span,p", remove: "all"},
          ],
        },
        visualblocks_default_state: true,
        forced_root_block: "p",
        content_style:
          "body { font-family:Times,sans-serif; font-size:14pt } p{margin:0px; padding:0px;} h2{font-weight:normal;} .indent{ text-indent:40px;}",
        height: "300px",
        language: "ru",
      },
      loading: false,
      search: "",
      dialog: false,
      form: {
        responsible_id: null,
      },

    };
  },

  computed: {
    screenHeight() {
      return window.innerHeight - 170;
    },
  },
  methods: {
    handleSuccess(file, response) {
      this.uploadedFiles.push(file);
    },
    handleComplete(file) {
      // Fayl yuklashni tugatganda ishlatiladi
    },
    downloadFile(file) {
      // Faylni yuklab olish uchun xizmat qiladigan metod
    },
    removeFileFromTable(file) {
      this.removeThisFile(file);
    },
    removeThisFile(file) {
      const index = this.uploadedFiles.findIndex(
        (uploadedFile) => uploadedFile.name === file.name
      );
      if (index !== -1) {
        this.uploadedFiles.splice(index, 1);
        this.$refs.myVueDropzone.removeFile(file);
      }
    },

    sendMessage() {

    },

    addUser() {

      console.log('Hello Everybody');
    },

    getList() {

    },


  },
  mounted() {
  },
  created() {
  }
};
</script>
<style scoped>
.pull-left {
  float: left;
}

.pull-right {
  float: right;
}

.fullHeight {
  height: calc(100% - 0px);
}

.heightFull {
  height: 100%;
  background: #fff;
}

.headerTitle {
  color: #070707;
  font-size: 15px;
  line-height: 1.4;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}

.chat_item_title {
  color: #676768;
  font-size: 13px;
  line-height: 1.4;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}

.txt_search {
  max-width: 100%;
  border: 1px solid #DCE5EF;
  box-shadow: none;
  max-height: 100%;
  overflow: hidden;
  border-radius: 5px;
  color: #212529;
  font-size: 12px;
  font-weight: 400;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}

.chat_item_list {
  color: #F8A300;
  font-size: 10px;
  line-height: 1.8;
  font-weight: 500;
  font-family: "Inter", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
</style>
