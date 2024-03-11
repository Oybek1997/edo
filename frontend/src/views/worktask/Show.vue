<template>
  <div>
    <v-card height="800px">
      <v-card-title primary-title>
        {{ tasks.title }}
        <v-spacer></v-spacer>

        <v-btn
          color="success"
          class="mr-2"
          v-if="tasks.status == 0 && showPublish"
        >
          E'lon qilish
        </v-btn>
        <v-btn
          color="success"
          outlined
          class="mr-2"
          v-if="tasks.status == 0 && showPublish"
          :to="'/worktask/update/' + tasks.id"
        >
          Tahrirlash
        </v-btn>
        <v-btn color="primary" class="mr-2">
          {{ tasks.created_at.substr(0, 10) }}
        </v-btn>
        <v-btn
          color="red"
          x-small
          outlined
          fab
          class
          @click="showTaskDialog = false"
        >
          <v-icon>mdi-close</v-icon>
        </v-btn>
        <!-- <v-btn @click="taskSave" color="success" outlined>
          <v-icon>mdi-floppy</v-icon>
          {{ $t("save") }}
        </v-btn> -->
      </v-card-title>
      <v-col>
        <h4>Topshiriq mazmuni:</h4>
        <h5>{{ tasks.content }}</h5>
        <div v-for="task in tasks.user" v-if="task.assignment_type == 1">
          <h3>Topshiriq beruvchi</h3>
          <p>
            {{ task.doer.lastname_uz_latin }}
            {{ task.doer.firstname_uz_latin }}
            {{ task.doer.middlename_uz_latin }}
          </p>
        </div>
        <h3>Bajaruvchilar</h3>
        <div v-for="task in tasks.user">
          <ul>
            <li v-if="task.assignment_type == 0">
              {{ task.doer.lastname_uz_latin }}
              {{ task.doer.firstname_uz_latin }}
              {{ task.doer.middlename_uz_latin }}
            </li>
          </ul>
        </div>
      </v-col>
    </v-card>
  </div>
</template>
<script>
const axios = require("axios").default;
export default {
  data() {
    return {
      tasks: [],
      editModal: false,
      task: {
        // id: Date.now(),
        content: "",
        title: "",
        priority: null,
        files: null,
        task_type_id: null,
        user: [],
      },
      newDoer: {
        user: [],
        due_datetime: "",
      },
      priority: [
        {
          id: 1,
          text: "Yuqori",
        },
        {
          id: 2,
          text: "O'rta",
        },
        {
          id: 3,
          text: "Past",
        },
      ],
      users: [],
      selectedUser: [],
      search: "",
      showPublish: false,
    };
  },
  methods: {
    showTask(id) {
      console.log(id);
      axios
        .get(this.$store.state.backend_url + "api/worktask-showinfo/" + id)
        .then((res) => {
          this.tasks = res.data[0];
          this.checkButton();
        })
        .catch((err) => {
          console.error(err);
        });
    },
    checkButton() {
      let user = this.$store.getters.getUser();
      this.tasks.user.forEach((v) => {
        if (v.assignment_type == 1 && v.employee_id == user.employee_id) {
          this.showPublish = true;
        }
      });
    },
  },
  mounted() {
    this.showTask(this.$parent.showTaskID);
    // this.getTask($id);
  },
};
</script>
