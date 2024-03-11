<template>
  <div>
    <vue-dropzone
      style="
        color: grey;
        border: 3px dotted #d8d4d4;
        width: 97%;
        margin: 15px 0px 0px 16px;
      "
      ref="myVueDropzone"
      id="dropzone"
      height='10px'
      :options="dropzoneOptions"
      @vdropzone-success="handleSuccess"
      @vdropzone-complete="handleComplete"
      v-on:vdropzone-removed-file="removeThisFile"
    >
    </vue-dropzone>

    <v-card-text class="pt-0" style="width: 700px">
      <v-simple-table dense class="mt-2" style="border: none">
        <template v-slot:default>
          <thead>
            <tr>
              <th class="text-center">#</th>
              <th class="text-center">{{ $t("File Name") }}</th>
              <th class="text-center">{{ $t("File type") }}</th>
              <th class="text-center">{{ $t("File size") }}</th>
              <th class="text-center">{{ $t("Action") }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in uploadedFiles" :key="index">
              <td class="text-left" style="width: 30px">{{ index + 1 }}</td>
              <td
                class="text-left"
                :id="'file-td-' + item.name"
                style="width: 400px"
              >
                <v-icon class="px-0" color="indigo"
                  >mdi-file-document-outline</v-icon
                >
                {{ item.name }}
              </td>
              <td class="text-left" style="max-width: 150px; overflow: hidden">
                {{ item.type }}
              </td>
              <td class="text-left">{{ item.size }} kB</td>
              <td class="text-center" width="50px">
                <!-- <v-icon
                  @click="downloadFile(item)"
                  :label="$t('File download')"
                  class="px-1"
                  color="green"
                >
                  mdi-download-outline
                </v-icon>
                <v-icon :label="$t('File view')" class="px-1" color="green">
                  mdi-file-eye-outline
                </v-icon> -->
                <v-icon
                  @click="removeFileFromTable(item)"
                  :label="$t('File delete')"
                  class="px-1"
                  color="error"
                >
                  mdi-trash-can-outline
                </v-icon>
              </td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>
    </v-card-text>
  </div>
</template>

<script>
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
        dictDefaultMessage: "<img style='height:50px; margin: -30px 0px -20px 0px;' src='img/cloud-upload-outline.png'> Перетащите файлы или загрузите с локальной папки",
      },
      uploadedFiles: [],
    };
  },
  computed: {},

  watch: {},
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
  },
  mounted() {},
};
</script>
<style scoped>
.dropzone
{
  padding: 0px !important;
  min-height: 70px!important;
}
</style>