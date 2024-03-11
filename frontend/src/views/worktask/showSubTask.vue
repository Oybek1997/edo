<template>
  <div>
    <v-dialog
      v-model="showChildTaskDialog"
      persistent
      max-width="50%"
      @keydown.esc="showChildTaskDialog = false"
    >
      <v-card>
        <v-card-title primary-title>
          {{ showedChildTasks.description }}
          <v-spacer></v-spacer>
          <v-btn
            color="red"
            x-small
            outlined
            fab
            class
            @click="showChildTaskDialog = false"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <div v-for="comment in showedChildTasks.comments">
          <v-list-item>
            <v-list-item-content>
              <v-list-item-title>{{ comment.comment }}</v-list-item-title>
              <v-list-item-subtitle>{{
                comment.commented_by.lastname_uz_latin +
                " " +
                comment.commented_by.lastname_uz_latin
              }}</v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
        </div>
        <fieldset class="my-2" style="border: none">
          <v-form ref="addTaskForm">
            <v-col cols="12">
              <v-textarea
                v-model="commentTask"
                rows="2"
                :label="$t('Komment yozish')"
                outlined
                dense
                hide-details="auto"
              ></v-textarea>
            </v-col>
            <v-spacer></v-spacer>
            <v-card-actions class="pt-0">
              <v-spacer></v-spacer>
              <v-btn color="green" dark @click="saveSubTaskComment">{{
                $t("save")
              }}</v-btn>
              <!--                        <v-btn color="red darken-1" dark @click="onClickOutside">{{ $t('close') }}</v-btn>-->
            </v-card-actions>
          </v-form>
        </fieldset>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
const axios = require("axios").default;
export default {
  // props: ["childTaskID"],
  data: () => ({
    commentTask: "",
    showedChildTasks: [],
    showChildTaskDialog: false,
    formData: [],
    childTaskId: "",
  }),
  computed: {
    screenHeight() {
      return window.innerHeight - 200;
    },
  },
  methods: {
    showSubTaskInfo(id) {
      this.childTaskId = id;
      this.showChildTaskDialog = true;
      axios
        .get(this.$store.state.backend_url + "api/worktask/childTaskInfo/" + id)
        .then((res) => {
          this.showedChildTasks = res.data[0];
        })
        .catch((err) => {
          console.error(err);
        });
    },
    saveSubTaskComment() {
      axios
        .post(this.$store.state.backend_url + "api/worktask/commentAdd", {
          id: this.childTaskId,
          comment: this.commentTask,
        })
        .then((res) => {
          this.commentTask = "";
          this.showSubTaskInfo(this.childTaskId);
        });
    },
    clear() {
      this.selectedUser = [];
      this.task = [];
    },
  },
  mounted() {},
};
</script>

<style scoped>
.v-card {
  transition: opacity 0.4s ease-in-out;
}

.v-card:not(.on-hover) {
  opacity: 1;
}
.theme--light.v-card > .v-card__text,
.theme--light.v-card > .v-card__subtitle {
  color: #000;
}
.show-btns {
  color: rgba(255, 255, 255, 1) !important;
}
</style>
