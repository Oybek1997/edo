<template>
  <div>
    <v-card elevation="10" class="mt-3 ml-3">
      <v-card-title primary-title>
        {{ tasks.title }}
        <v-spacer></v-spacer>
        <!-- <v-btn @click="taskSave" color="success" outlined>
          <v-icon>mdi-floppy</v-icon>
          {{ $t("save") }}
        </v-btn> -->
      </v-card-title>
      <v-row class="mx-0 mb-10" style="display: flex; align-items: flex-end">
        <v-col class="d-flex justify-space-around" md="12" sm="12" xs="12">
          <v-hover>
            <template v-slot="{ hover }">
              <v-card
                class="card-color d-flex align-center"
                width="23%"
                :class="`elevation-${hover ? 16 : 3}`"
                @click="
                  informer = 1;
                  showInformer();
                "
              >
                <v-icon class="ma-2" x-large dark color="primary">
                  mdi-calendar-text
                </v-icon>
                <div style="margin-left: 30px">
                  <v-card-title class="card_title">{{
                    expiredTasks.length
                  }}</v-card-title>
                  <p>Barcha topshiriqlar</p>
                </div>
              </v-card>
            </template>
          </v-hover>
          <v-hover>
            <template v-slot="{ hover }">
              <v-card
                class="card-color d-flex align-center"
                width="23%"
                :class="`elevation-${hover ? 16 : 3}`"
                @click="
                  informer = 2;
                  showInformer();
                "
              >
                <v-icon class="ma-2" x-large dark color="primary">
                  mdi-calendar-check
                </v-icon>
                <div style="margin-left: 30px">
                  <v-card-title class="card_title">
                    {{
                      expiredTasks.find((o) => o.status == 7)
                        ? expiredTasks.filter((o) => o.status == 7).length
                        : 0
                    }}
                  </v-card-title>
                  <p>Bajarilgan topshiriqlar</p>
                </div>
              </v-card>
            </template> </v-hover
          ><v-hover>
            <template v-slot="{ hover }">
              <v-card
                class="card-color d-flex align-center"
                width="23%"
                :class="`elevation-${hover ? 16 : 3}`"
                @click="
                  informer = 3;
                  showInformer();
                "
              >
                <v-icon class="ma-2" x-large dark color="primary">
                  mdi-calendar-clock
                </v-icon>
                <div style="margin-left: 30px">
                  <v-card-title class="card_title">
                    <span>
                      {{
                        expiredTasks.find(
                          (o) =>
                            Date.parse(o.due_datetime) / 1000 <
                            Date.now() / 1000
                        )
                          ? expiredTasks.filter(
                              (o) =>
                                Date.parse(o.due_datetime) / 1000 <
                                  Date.now() / 1000 && o.status == 2
                            ).length
                          : 0
                      }}
                      <!-- t.user.filter((v) => {
                  if (Date.parse(v.due_datetime) / 1000 < Date.now() / 1000)
                    return v;
                }).length -->
                    </span>
                  </v-card-title>
                  <p>Muddati o'tgan topshiriqlar</p>
                </div>
              </v-card>
            </template> </v-hover
          ><v-hover>
            <template v-slot="{ hover }">
              <v-card
                class="card-color d-flex align-center"
                width="23%"
                :class="`elevation-${hover ? 16 : 3}`"
                @click="
                  informer = 4;
                  showInformer();
                "
              >
                <v-icon class="ma-2" x-large dark color="primary">
                  mdi-calendar-refresh
                </v-icon>
                <div style="margin-left: 30px">
                  <v-card-title class="card_title">{{
                    expiredTasks.find((o) => o.status == 2)
                      ? expiredTasks.filter((o) => o.status == 2).length
                      : 0
                  }}</v-card-title>
                  <p>Jarayondagi topshiriqlar</p>
                </div>
              </v-card>
            </template>
          </v-hover>
        </v-col>
      </v-row>
      <v-row class="mx-10 mb-10">
        <v-spacer></v-spacer>
        <v-btn
          color="success"
          @click="$refs.componentTaskCreate.addTaskFromParent()"
        >
          <v-icon>mdi-playlist-plus</v-icon> Topshiriq qo'shish</v-btn
        >
        <WorkTaskCreate
          @getTask="getTask"
          @showTask="showTask"
          ref="componentTaskCreate"
        />
      </v-row>
      <v-tabs v-model="tab" background-color="indigo accent-4" centered dark>
        <v-tabs-slider></v-tabs-slider>

        <v-tab href="#projects"> Loyihalar </v-tab>

        <v-tab href="#employees"> Hodimlar </v-tab>
      </v-tabs>

      <v-tabs-items v-model="tab">
        <v-tab-item :key="1" value="projects">
          <v-card flat>
            <v-card-text>
              <v-data-table
                :headers="headers"
                :items="tasks"
                :options.sync="dataTableOptions"
                hide-default-footer
                class="mainTable elevation-1 pa-1"
              >
                <template v-slot:[`body.prepend`]>
                  <tr class="py-0 my-0 prepend_height">
                    <td class="py-0 my-0 dense"></td>
                    <td class="py-0 my-0 dense">
                      <v-text-field
                        v-model="filterForm.title"
                        type="text"
                        hide-details
                        @keyup.native.enter="getTask()"
                      ></v-text-field>
                    </td>
                    <td class="py-0 my-0 dense">
                      <v-text-field
                        v-model="filterForm.content"
                        type="text"
                        hide-details
                        @keyup.native.enter="getTask()"
                      ></v-text-field>
                    </td>
                    <td class="py-0 my-0 dense">
                      <v-select
                        style="margin-bottom: -20px"
                        v-model="filterForm.category"
                        :items="taskCategory"
                        item-value="id"
                        item-text="name"
                        @change="getTask()"
                        clearable
                      ></v-select>
                    </td>
                    <td class="py-0 my-0 dense">
                      <v-menu
                        ref="rangeMenu"
                        :close-on-content-click="false"
                        :return-value.sync="filterForm.date"
                        offset-y
                        min-width="290px"
                      >
                        <template v-slot:activator="{ on, attrs }">
                          <v-text-field
                            v-model="filterForm.date"
                            v-bind="attrs"
                            @keyup.native.enter="getTask()"
                            v-on="on"
                            hide-details
                            clearable
                          ></v-text-field>
                        </template>
                        <v-date-picker v-model="date" range no-title scrollable>
                          <v-spacer></v-spacer>
                          <v-btn text color="primary" @click="menu = false">
                            Bekor qilish
                          </v-btn>
                          <v-btn
                            text
                            color="primary"
                            @click="
                              $refs.rangeMenu.save(date);
                              filterForm.date = date;
                              getTask();
                            "
                          >
                            OK
                          </v-btn>
                        </v-date-picker>
                      </v-menu>
                    </td>
                    <td class="py-0 my-0 dense">
                      <v-select
                        style="margin-bottom: -20px"
                        v-model="filterForm.status"
                        :items="status"
                        item-value="id"
                        item-text="text"
                        @change="getTask()"
                        clearable
                      ></v-select>
                    </td>
                    <td class="py-0 my-0 dense"></td>
                    <td class="py-0 my-0 dense"></td>
                    <td class="py-0 my-0 dense"></td>
                  </tr>
                </template>
                <template v-slot:item.id="{ item }">{{
                  tasks.map((v) => v.id).indexOf(item.id) + 1
                }}</template>
                <template v-slot:item.title="{ item }">
                  <td style="max-width: 300px; width: 300px">
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          color="green"
                          text
                          @click="showTask(item.id)"
                          v-bind="attrs"
                          v-on="on"
                          style="
                            white-space: normal;
                            text-transform: capitalize;
                          "
                        >
                          {{ item.title }}</v-btn
                        ></template
                      >
                      <span>{{ item.title }}</span>
                    </v-tooltip>
                  </td>
                </template>
                <template v-slot:item.category="{ item }">
                  {{ item.category.name }}
                </template>
                <template v-slot:item.status="{ item }">
                  <v-chip class="ma-2" v-if="item.status == 0">
                    Qoralama
                  </v-chip>
                  <v-chip class="ma-2" v-if="item.status == 1" color="warning">
                    E'lon qilindi
                  </v-chip>
                  <v-chip class="ma-2" v-if="item.status == 2" color="success">
                    Jarayonda
                  </v-chip>
                  <v-chip class="ma-2" v-if="item.status == 3" color="error">
                    Bekor qilingan
                  </v-chip>
                  <v-chip class="ma-2" v-if="item.status == 5" color="warning">
                    Muddati o'tgan
                  </v-chip>
                </template>
                <template v-slot:item.percent="{ item }">
                  <v-progress-linear
                    height="25"
                    :value="
                      item.user.find((o) => o.status == 7)
                        ? (item.user.filter((o) => o.status == 7).length *
                            100) /
                          item.user.filter((o) => o.description != null).length
                        : 0
                    "
                    :color="
                      (item.user.filter((o) => o.status == 7).length * 100) /
                        item.user.filter((o) => o.description != null).length >
                      90
                        ? 'success'
                        : (item.user.filter((o) => o.status == 7).length *
                            100) /
                            item.user.filter((o) => o.description != null)
                              .length >=
                          50
                        ? 'primary'
                        : (item.user.filter((o) => o.status == 7).length *
                            100) /
                            item.user.filter((o) => o.description != null)
                              .length <
                          40
                        ? 'warning'
                        : 'error'
                    "
                  >
                    <template v-slot:default="{ value }">
                      <strong>{{ Math.ceil(value) }}%</strong>
                    </template>
                  </v-progress-linear>
                </template>
                <template v-slot:item.creator="{ item }">
                  {{
                    item.user.find((o) => o.assignment_type == 1)
                      ? item.user.find((o) => o.assignment_type == 1).doer
                          .lastname_uz_latin +
                        " " +
                        item.user.find((o) => o.assignment_type == 1).doer
                          .firstname_uz_latin
                      : ""
                  }}
                </template>
                <template v-slot:item.doer="{ item }">
                  <div v-for="u in item.user" v-if="u.assignment_type == '0'">
                    <td style="max-width: 200px">
                      <v-tooltip
                        bottom
                        :color="
                          Date.parse(u.due_datetime) / 1000 < Date.now() / 1000
                            ? 'error'
                            : 'success'
                        "
                      >
                        <template v-slot:activator="{ on, attrs }">
                          <span
                            v-bind="attrs"
                            v-on="on"
                            :style="
                              Date.parse(u.due_datetime) / 1000 <
                              Date.now() / 1000
                                ? 'color: red'
                                : 'color: green'
                            "
                            >{{ u.doer.lastname_uz_latin }}
                            {{ u.doer.firstname_uz_latin }}</span
                          >
                        </template>
                        <span
                          >{{ u.due_datetime.substr(0, 10) }}
                          {{ u.due_datetime.substr(11, 5) }}</span
                        >
                      </v-tooltip>
                    </td>
                  </div>
                </template>
                <template v-slot:item.created_at="{ item }">
                  <v-chip
                    >{{ item.created_at.substr(0, 10) }}
                    {{
                      new Date(item.created_at).toLocaleString().substr(11, 6)
                    }}</v-chip
                  >
                </template>
              </v-data-table>
            </v-card-text>
          </v-card>
        </v-tab-item>
        <v-tab-item :key="2" value="employees">
          <v-card flat>
            <v-card-text
              ><v-data-table
                :headers="employeeTaskHeader"
                :items="employeeTasks"
                :options.sync="dataTableOptions"
                hide-default-footer
                class="mainTable elevation-1 pa-1"
              >
                <template v-slot:item.id="{ item }">{{
                  employeeTasks.map((v) => v.id).indexOf(item.id) + 1
                }}</template>
                <!-- <template v-slot:item.title="{ item }">
                  <v-btn color="green" text @click="showTask(item.id)">
                    {{ item.title }}</v-btn
                  >
                </template> -->
                <template v-slot:item.doer="{ item }">
                  <div>
                    {{ item.lastname_uz_latin }}
                    {{ item.firstname_uz_latin }}
                    <!-- <v-chip>{{ item.due_datetime }}</v-chip> -->
                  </div>
                </template>
                <template v-slot:item.projects="{ item }">
                  <v-chip>{{
                    item.worktasks.filter((v) => {
                      if (v.assignment_type == 0 && v.deleted_at == null)
                        return v;
                    }).length
                  }}</v-chip>
                </template>
                <template v-slot:item.tasks="{ item }">
                  <v-chip>{{
                    item.worktasks.filter((v) => {
                      if (v.assignment_type == 3 && v.deleted_at == null)
                        return v;
                    }).length
                  }}</v-chip>
                </template>
                <template v-slot:item.done="{ item }">
                  <v-chip color="success">{{
                    item.worktasks.filter((v) => {
                      if (
                        v.assignment_type == 3 &&
                        v.deleted_at == null &&
                        v.status == 7
                      )
                        return v;
                    }).length
                  }}</v-chip>
                </template>
                <template v-slot:item.process="{ item }">
                  <v-chip color="warning">{{
                    item.worktasks.filter((v) => {
                      if (
                        v.assignment_type == 3 &&
                        v.deleted_at == null &&
                        v.status == 2
                      )
                        return v;
                    }).length
                  }}</v-chip>
                </template>
                <template v-slot:item.expired="{ item }">
                  <v-chip color="error">{{
                    item.worktasks.filter((v) => {
                      if (
                        v.assignment_type == 3 &&
                        v.deleted_at == null &&
                        v.status == 2 &&
                        Date.parse(v.due_datetime) / 1000 < Date.now() / 1000
                      )
                        return v;
                    }).length
                  }}</v-chip>
                </template>
                <template v-slot:item.percent="{ item }">
                  <v-progress-linear
                    height="25"
                    :value="
                      item.worktasks.find((o) => o.status == 7)
                        ? (item.worktasks.filter((o) => o.status == 7).length *
                            100) /
                          item.worktasks.filter((o) => o.assignment_type == 3)
                            .length
                        : 0
                    "
                    :color="
                      (item.worktasks.filter((o) => o.status == 7).length *
                        100) /
                        item.worktasks.filter((o) => o.assignment_type == 3)
                          .length >
                      90
                        ? 'success'
                        : (item.worktasks.filter((o) => o.status == 7).length *
                            100) /
                            item.worktasks.filter((o) => o.assignment_type == 3)
                              .length >=
                          50
                        ? 'primary'
                        : (item.worktasks.filter((o) => o.status == 7).length *
                            100) /
                            item.worktasks.filter((o) => o.assignment_type == 3)
                              .length <
                          40
                        ? 'warning'
                        : 'error'
                    "
                  >
                    <template v-slot:default="{ value }">
                      <strong>{{ Math.ceil(value) }}%</strong>
                    </template>
                  </v-progress-linear>
                </template>
              </v-data-table></v-card-text
            >
          </v-card>
        </v-tab-item>
      </v-tabs-items>
    </v-card>
    <v-dialog
      v-model="showTaskDialog"
      persistent
      max-width="80%"
      @keydown.esc="closeDialog()"
    >
      <v-card height="800px">
        <v-card-title primary-title>
          {{ showedTasks.title }}
          <v-spacer></v-spacer>
          <v-btn
            color="success"
            class="mr-2"
            v-show="showedTasks.status == 1 && showTaskActions"
            @click="actionTask(showedTasks.id, (action = '1'))"
          >
            Qabul qilish
          </v-btn>
          <v-btn
            color="error"
            class="mr-2"
            v-show="showedTasks.status == 1 && showTaskActions"
            @click="actionTask(showedTasks.id, (action = '0'))"
          >
            Rad etish
          </v-btn>
          <v-btn
            color="success"
            class="mr-2"
            @click="addChildTaskDialog = true"
            v-if="showedTasks.status == 2 && showTaskActions"
          >
            Podzadacha
          </v-btn>
          <v-btn
            color="success"
            class="mr-2"
            v-if="showedTasks.status == 0 && showPublish"
            @click="actionTask(showedTasks.id, (action = '3'))"
          >
            E'lon qilish
          </v-btn>
          <v-btn
            color="success"
            outlined
            class="mr-2"
            v-if="showPublish"
            @click="
              $refs.componentTaskCreate.editTaskFromParent(showedTasks.id)
            "
          >
            Tahrirlash
          </v-btn>
          <v-btn
            color="error"
            class="mr-2"
            v-if="showPublish"
            @click="deleteTask(showedTasks.id)"
          >
            O'chirish
          </v-btn>

          <v-btn color="red" x-small outlined fab class @click="closeDialog()">
            <v-icon>mdi-close</v-icon>
          </v-btn>
          <!-- <v-btn @click="taskSave" color="success" outlined>
          <v-icon>mdi-floppy</v-icon>
          {{ $t("save") }}
        </v-btn> -->
        </v-card-title>
        <v-col>
          <v-card-title>{{ showedTasks.content }}</v-card-title>
          <hr />
          <div class="d-flex">
            <div
              v-for="task in showedTasks.user"
              v-if="task.assignment_type == 1 && task.description == null"
            >
              <h3>Topshiriq beruvchi</h3>
              <p>
                😎
                {{ task.doer.lastname_uz_latin }}
                {{ task.doer.firstname_uz_latin }}
              </p>
            </div>
            <v-spacer></v-spacer>
            <div>
              <h3>Bajaruvchilar</h3>
              <div v-for="task in showedTasks.user">
                <div v-if="task.assignment_type == 0">
                  👔
                  {{ task.doer.lastname_uz_latin }}
                  {{ task.doer.firstname_uz_latin }}
                </div>
              </div>
            </div>
          </div>
        </v-col>
        <v-card-text class="pt-0">
          <v-simple-table
            v-for="task in showedTasks.user"
            v-if="task.task_files && task.task_files.length"
            dense
            class="mt-2"
            style="border: 1px solid #aaa"
          >
            <template v-slot:default>
              <thead>
                <tr>
                  <th class="text-left">#</th>
                  <th class="text-left">Nomi</th>
                  <th class="text-left"></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in task.task_files" :key="index">
                  <td>{{ index + 1 }}</td>
                  <td>{{ item.file_name }}</td>
                  <td class="text-lg-right" width="100px">
                    <!-- <a
                      style="text-decoration: none"
                      :href="
                        $store.state.backend_url + 'document/file/' + item.id
                      "
                    > -->
                    <v-icon @click="viewDoc(item)" class="px-1" color="success"
                      >mdi-eye</v-icon
                    >
                    <!-- </a> -->
                    <v-icon class="px-1" color="error" @click="deleteFile(item)"
                      >mdi-delete</v-icon
                    >
                  </td>
                </tr>
              </tbody>
            </template>
          </v-simple-table>
        </v-card-text>
        <v-col class="mb-10">
          <v-data-table
            :headers="childTaskHeaders"
            :items="childTasks"
            :options.sync="dataTableOptions"
            hide-default-footer
            class="mainTable elevation-1 pa-1"
          >
            <template v-slot:item.id="{ item }">{{
              childTasks.map((v) => v.id).indexOf(item.id) + 1
            }}</template>
            <template v-slot:item.title="{ item }">
              <v-btn
                color="success"
                text
                class="mr-2"
                @click="$refs.componentSubTaskShow.showSubTaskInfo(item.id)"
              >
                {{ item.description }}
              </v-btn>
              <SubTaskShow
                @getTask="getTask"
                @showTask="showTask"
                ref="componentSubTaskShow"
              />
            </template>
            <template v-slot:item.status="{ item }">
              <v-chip class="ma-2" v-if="item.status == 0"> Qoralama </v-chip>
              <v-chip class="ma-2" v-if="item.status == 1">
                E'lon qilindi
              </v-chip>
              <v-chip class="ma-2" v-if="item.status == 2" color="warning">
                Jarayonda
              </v-chip>
              <v-chip class="ma-2" v-if="item.status == 3" color="red">
                Bekor qilingan
              </v-chip>
              <v-chip class="ma-2" v-if="item.status == 5" color="warning">
                Muddati o'tgan
              </v-chip>
              <v-chip class="ma-2" v-if="item.status == 7" color="success">
                Bajarildi
              </v-chip>
            </template>
            <template v-slot:item.due_datetime="{ item }">
              {{ item.due_datetime }}
            </template>
            <template v-slot:item.priority="{ item }">
              <v-chip class="ma-2" color="error" v-if="item.priority == 1">
                Yuqori
              </v-chip>
              <v-chip class="ma-2" color="warning" v-if="item.priority == 2">
                O'rta
              </v-chip>
              <v-chip class="ma-2" color="primary" v-if="item.priority == 3">
                Past
              </v-chip>
            </template>
            <template v-slot:item.actions="{ item }">
              <v-icon
                class="mr-5"
                color="success"
                v-if="item.status == 2"
                @click="childTaskDone(item)"
                >mdi-check-circle</v-icon
              >
              <v-icon color="error" @click="childTaskDelete(item)"
                >mdi-delete</v-icon
              >
            </template>
          </v-data-table>
        </v-col>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="showInformerDialog"
      persistent
      max-width="80%"
      @keydown.esc="showInformerDialog = false"
    >
      <v-card height="800px">
        <v-card-title primary-title>
          {{ informerTitle }}
          <v-spacer></v-spacer>
          <v-btn
            color="red"
            x-small
            outlined
            fab
            class
            @click="showInformerDialog = false"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-col class="mb-10">
          <v-data-table
            :headers="informerTaskHeaders"
            :items="informerTasks"
            :options.sync="dataTableOptions"
            hide-default-footer
            class="mainTable elevation-1 pa-1"
          >
            <template v-slot:item.id="{ item }">{{
              expiredTasks.map((v) => v.id).indexOf(item.id) + 1
            }}</template>
            <template v-slot:item.title="{ item }">
              <v-btn
                color="success"
                text
                class="mr-2"
                @click="$refs.componentSubTaskShow.showSubTaskInfo(item.id)"
              >
                {{ item.description }}
              </v-btn>
              <SubTaskShow
                @getTask="getTask"
                @showTask="showTask"
                ref="componentSubTaskShow"
              />
            </template>
            <template v-slot:item.status="{ item }">
              <v-chip class="ma-2" v-if="item.status == 0"> Qoralama </v-chip>
              <v-chip class="ma-2" v-if="item.status == 1">
                E'lon qilindi
              </v-chip>
              <v-chip class="ma-2" v-if="item.status == 2" color="warning">
                Jarayonda
              </v-chip>
              <v-chip class="ma-2" v-if="item.status == 3" color="red">
                Bekor qilingan
              </v-chip>
              <v-chip class="ma-2" v-if="item.status == 5" color="warning">
                Muddati o'tgan
              </v-chip>
              <v-chip class="ma-2" v-if="item.status == 7" color="success">
                Bajarildi
              </v-chip>
            </template>
            <template v-slot:item.due_datetime="{ item }">
              {{ item.due_datetime }}
            </template>
            <template v-slot:item.priority="{ item }">
              <v-chip class="ma-2" color="error" v-if="item.priority == 1">
                Yuqori
              </v-chip>
              <v-chip class="ma-2" color="warning" v-if="item.priority == 2">
                O'rta
              </v-chip>
              <v-chip class="ma-2" color="primary" v-if="item.priority == 3">
                Past
              </v-chip>
            </template>
            <template v-slot:item.actions="{ item }">
              <p>
                {{ item.doer.lastname_uz_latin }}
                {{ item.doer.firstname_uz_latin }}
              </p>
            </template>
          </v-data-table>
        </v-col>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="addChildTaskDialog"
      persistent
      max-width="50%"
      @keydown.esc="addChildTaskDialog = false"
    >
      <v-card height="300px">
        <v-card-title primary-title>
          Yangi podzadacha
          <v-spacer></v-spacer>
          <v-btn
            color="red"
            x-small
            outlined
            fab
            class
            @click="addChildTaskDialog = false"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
          <!-- <v-btn @click="taskSave" color="success" outlined>
          <v-icon>mdi-floppy</v-icon>
          {{ $t("save") }}
        </v-btn> -->
        </v-card-title>
        <fieldset class="my-2" style="border: none">
          <v-form ref="addTaskForm">
            <v-col cols="12">
              <v-textarea
                v-model="childTask.content"
                rows="2"
                :label="$t('Topshiriq haqida')"
                outlined
                dense
                hide-details="auto"
              ></v-textarea>
            </v-col>
            <v-row class="ma-1">
              <v-col cols="6">
                <v-autocomplete
                  :items="priority"
                  item-value="id"
                  :item-text="'text'"
                  v-model="childTask.priority"
                  :label="$t('control_punkt.priority')"
                  outlined
                  dense
                  hide-details="auto"
                  clearable
                ></v-autocomplete>
              </v-col>
              <v-col cols="6">
                <v-text-field
                  v-model="childTask.due_datetime"
                  :label="$t('Muddati')"
                  type="datetime-local"
                  outlined
                  dense
                  clearable
                ></v-text-field>
              </v-col>
            </v-row>
            <v-spacer></v-spacer>
            <v-card-actions class="pt-0">
              <v-spacer></v-spacer>
              <v-btn color="green" dark @click="childTaskSave">{{
                $t("save")
              }}</v-btn>
              <!--                        <v-btn color="red darken-1" dark @click="onClickOutside">{{ $t('close') }}</v-btn>-->
            </v-card-actions>
          </v-form>
        </fieldset>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="viewDocument"
      scrollable
      @keydown.esc="viewDocument = false"
      persistent
      max-width="80%"
    >
      <template>
        <v-card class="ma-1 pa-1">
          <v-card-title class="pa-1">
            <span>{{ $t("Faylni ko'rish") }}</span>
            <v-spacer></v-spacer>
            <v-btn
              color="red"
              outlined
              x-small
              fab
              @click="viewDocument = false"
            >
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>
          <iframe
            :src="currentDoc"
            :width="screenWidth - 290"
            :height="screenHeight"
          ></iframe>
          <!-- <img
              :src="downloadFile"
              :width="screenWidth - 200"
              :height="screenHeight"
            />  -->
        </v-card></template
      >
    </v-dialog>
  </div>
</template>
<script>
const axios = require("axios").default;
import WorkTaskCreate from "@/views/worktask/Create.vue";
import SubTaskShow from "@/views/worktask/showSubTask.vue";
import Swal from "sweetalert2";
export default {
  components: {
    WorkTaskCreate,
    SubTaskShow,
  },
  computed: {
    screenHeight() {
      return window.innerHeight - 210;
    },
    screenWidth() {
      return window.innerWidth;
    },
  },

  data() {
    return {
      viewDocument: false,
      currentDoc: "",
      informer: "",
      informerTitle: "",
      showInformerDialog: false,
      informerTasks: [],
      date: null,
      currentDate: new Date().toISOString().substr(0, 7),
      tab: null,
      showTaskDialog: false,
      addChildTaskDialog: false,
      dataTableOptions: { page: 1, itemsPerPage: 50 },
      tasks: [],
      childTasks: [],
      showedTasks: [],
      twoTasks: [],
      employeeTasks: [],
      taskCategory: [],
      childTask: {
        content: "",
        priority: null,
        due_datetime: "",
        task_id: "",
      },
      headers: [
        { text: "№", value: "id", sortable: false },
        {
          text: "Nomi",
          align: "start",
          sortable: false,
          value: "title",
        },
        { text: "Mazmuni", value: "content", sortable: false },
        { text: "Kategoriya", value: "category", sortable: false },
        { text: "Boshlanish vaqti", value: "created_at", sortable: false },
        { text: "Holati", value: "status", sortable: false },
        { text: "Progress", value: "percent", sortable: false, width: "20%" },
        { text: "Tuzuvchi", value: "creator", sortable: false },
        { text: "Bajaruvchilar", value: "doer", sortable: false },
      ],
      employeeTaskHeader: [
        { text: "№", value: "id", sortable: false },
        { text: "Hodim", value: "doer", sortable: false },
        { text: "Loyihalar soni", value: "projects", sortable: false },
        { text: "Topshiriqlar soni", value: "tasks", sortable: false },
        { text: "Bajarildi", value: "done", sortable: false },
        { text: "Jarayonda", value: "process", sortable: false },
        { text: "Muddati o'tgan", value: "expired", sortable: false },
        { text: "Progress", value: "percent", sortable: false, width: "20%" },
      ],
      childTaskHeaders: [
        { text: "№", value: "id", sortable: false },
        {
          text: "Nomi",
          align: "start",
          sortable: false,
          value: "title",
          width: "30%",
        },
        { text: "Holati", value: "status", sortable: false },
        { text: "Muhimliligi", value: "priority", sortable: false },
        { text: "Muddati", value: "due_datetime", sortable: false },
        {
          text: "Amallar",
          align: "center",
          value: "actions",
          sortable: false,
          width: 100,
        },
        // { text: "Bajaruvchilar", value: "doer", sortable: false },
      ],
      informerTaskHeaders: [
        { text: "№", value: "id", sortable: false },
        {
          text: "Nomi",
          align: "start",
          sortable: false,
          value: "title",
          width: "30%",
        },
        { text: "Holati", value: "status", sortable: false },
        { text: "Muhimliligi", value: "priority", sortable: false },
        { text: "Muddati", value: "due_datetime", sortable: false },
        {
          text: "Bajaruvchi",
          align: "center",
          value: "actions",
          sortable: false,
          width: 100,
        },
        // { text: "Bajaruvchilar", value: "doer", sortable: false },
      ],
      filterForm: {
        title: "",
        content: "",
        category: "",
        status: "",
        creator: "",
        date: "",
      },
      showPublish: false,
      showTaskActions: false,
      value: 0,
      expiredTasks: [],
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
      status: [
        {
          id: 0,
          text: "Qoralama",
        },
        {
          id: 1,
          text: "E'lon qilindi",
        },
        {
          id: 2,
          text: "Jarayonda",
        },
        {
          id: 3,
          text: "Bekor qilingan",
        },
        {
          id: 5,
          text: "Muddati o'tgan",
        },
        {
          id: 7,
          text: "Bajarildi",
        },
      ],
    };
  },
  methods: {
    getTask() {
      // let user = this.$store.getters.getUser();
      axios
        .post(this.$store.state.backend_url + "api/worktask-all", {
          filter: this.filterForm,
        })
        .then((res) => {
          this.tasks = res.data;
          // console.log(this.tasks);
          this.empInfo();
          this.getCategory();
          this.getExpired();
        })
        .catch((err) => {
          console.error(err);
        });
    },
    viewDoc(item) {
      this.viewDocument = true;
      axios
        .get(this.$store.state.backend_url + "api/worktask/file/" + item.id, {
          responseType: "blob",
        })
        .then((res) => {
          const file = new Blob([res.data], { type: "application/pdf" });

          const fileURL = URL.createObjectURL(file);
          // this.downloadFile = fileURL;
          this.currentDoc = fileURL;
        });
    },
    deleteFile(item) {
      Swal.fire({
        title: this.$t("swal_title"),
        text: this.$t("swal_text"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: this.$t("swal_delete"),
      }).then((result) => {
        if (result.value) {
          axios
            .delete(
              this.$store.state.backend_url +
                "api/worktask/deletefile/" +
                item.id
            )
            .then((res) => {
              this.closeDialog();
              Swal.fire("O'chirildi!", this.$t("swal_deleted"), "success");
            })
            .catch((err) => {
              Swal.fire({
                icon: "error",
                title: this.$t("swal_error_title"),
                text: this.$t("swal_error_text"),
                //footer: "<a href>Why do I have this issue?</a>"
              });
              console.log(err);
            });
        }
      });
    },
    getExpired() {
      axios
        .get(this.$store.state.backend_url + "api/worktask/getExpired")
        .then((res) => {
          this.expiredTasks = res.data;
          // console.log(this.expiredTasks);
        });
    },
    getCategory() {
      axios
        .get(this.$store.state.backend_url + "api/worktask-category")
        .then((res) => {
          this.taskCategory = res.data;
          // console.log(this.taskCategory);
        });
    },
    showInformer() {
      this.showInformerDialog = true;
      if (this.informer == 1) {
        this.informerTitle = "Barcha topshiriqlar";
        this.informerTasks = this.expiredTasks;
      }
      if (this.informer == 2) {
        this.informerTitle = "Bajarilgan topshiriqlar";
        this.informerTasks = this.expiredTasks.filter((o) => o.status == 7);
      }
      if (this.informer == 3) {
        this.informerTitle = "Muddati o'tgan topshiriqlar";
        this.informerTasks = this.expiredTasks.filter(
          (o) =>
            Date.parse(o.due_datetime) / 1000 < Date.now() / 1000 &&
            o.status == 2
        );
      }
      if (this.informer == 4) {
        this.informerTitle = "Jarayondagi topshiriqlar";
        this.informerTasks = this.expiredTasks.filter((o) => o.status == 2);
      }
    },
    empInfo() {
      axios
        .get(this.$store.state.backend_url + "api/worktask/empInfo")
        .then((res) => {
          this.employeeTasks = res.data;
        })
        .catch((err) => {
          console.error(err);
        });
    },
    // getEmployeeTasks() {
    //   // let user = this.$store.getters.getUser();
    //   axios
    //     .get(this.$store.state.backend_url + "api/worktask/employeeTasks")
    //     .then((res) => {
    //       this.employeeTasks = res.data;
    //       console.log(res.data);
    //     })
    //     .catch((err) => {
    //       console.error(err);
    //     });
    // },
    showTask(id) {
      this.showTaskDialog = true;
      axios
        .get(this.$store.state.backend_url + "api/worktask-showinfo/" + id)
        .then((res) => {
          this.showedTasks = res.data[0];
          this.checkButton();
          // this.checkDoer();
          this.getChildTask();
        })
        .catch((err) => {
          console.error(err);
        });
    },
    closeDialog() {
      this.showTaskDialog = false;
      this.showPublish = false;
      this.showTaskActions = false;
      this.showInformerDialog = false;
      this.expiredTasks = [];
      this.showedTasks = [];
      this.childTasks = [];
      this.getTask();
    },
    checkButton() {
      let user = this.$store.getters.getUser();
      if (
        this.showedTasks.user.filter(
          (v) =>
            v.assignment_type == 1 &&
            v.description == null &&
            v.employee_id == user.employee_id
        ).length > 0
      ) {
        this.showPublish = true;
      } else if (
        this.showedTasks.user.filter(
          (v) =>
            v.assignment_type == 0 &&
            v.description == null &&
            v.employee_id == user.employee_id
        ).length > 0
      ) {
        this.showTaskActions = true;
      }
    },
    // checkDoer() {
    //   let user = this.$store.getters.getUser();
    //   console.log(this.showedTasks.status);
    //   this.showedTasks.user.forEach((v) => {
    //     if (v.assignment_type == 0 && v.employee_id == user.employee_id) {
    //       this.showTaskActions = true;
    //     }
    //   });
    // },
    deleteTask(item) {
      Swal.fire({
        title: this.$t("swal_title"),
        text: this.$t("swal_text"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: this.$t("swal_delete"),
      }).then((result) => {
        if (result.value) {
          axios
            .delete(
              this.$store.state.backend_url + "api/worktask-delete/" + item
            )
            .then((res) => {
              this.showTaskDialog = false;
              this.getTask();
              this.dialog = false;
              Swal.fire("O'chirildi!", this.$t("swal_deleted"), "success");
            })
            .catch((err) => {
              Swal.fire({
                icon: "error",
                title: this.$t("swal_error_title"),
                text: this.$t("swal_error_text"),
                //footer: "<a href>Why do I have this issue?</a>"
              });
              console.log(err);
            });
        }
      });
    },
    actionTask(item, status) {
      axios
        .post(this.$store.state.backend_url + "api/worktask-action", {
          task_id: item,
          action: status,
        })
        .then((res) => {
          // console.log("success");
          this.showTaskDialog = false;
          this.getTask();
        });
    },
    getChildTask() {
      axios
        .get(
          this.$store.state.backend_url +
            "api/worktask-info/" +
            this.showedTasks.id
        )
        .then((res) => {
          this.childTasks = res.data;
          // console.log(this.childTasks);
        })
        .catch((err) => {
          console.error(err);
        });
    },
    childTaskSave() {
      this.childTask.task_id = this.showedTasks.id;
      // console.log(this.childTask);
      axios
        .post(
          this.$store.state.backend_url + "api/worktask/childTask-create",
          Object.assign(this.childTask)
        )
        .then((response) => {
          this.addChildTaskDialog = false;
          this.getChildTask();
          this.$refs.addTaskForm.reset();
        })
        .catch((err) => {
          console.error(err);
        });
    },
    childTaskDone(item) {
      axios
        .get(
          this.$store.state.backend_url +
            "api/worktask/childTask-done/" +
            item.id
        )
        .then((res) => {
          this.getChildTask();
          this.dialog = false;
          Swal.fire("Bajarildi", "Ushbu topshiriq bajarildi", "success");
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: this.$t("swal_error_title"),
            text: this.$t("swal_error_text"),
            //footer: "<a href>Why do I have this issue?</a>"
          });
          console.log(err);
        });
    },
    childTaskDelete(item) {
      Swal.fire({
        title: this.$t("swal_title"),
        text: this.$t("swal_text"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: this.$t("swal_delete"),
      }).then((result) => {
        if (result.value) {
          axios
            .delete(
              this.$store.state.backend_url +
                "api/worktask/childTask-delete/" +
                item.id
            )
            .then((res) => {
              this.getChildTask();
              this.dialog = false;
              Swal.fire("O'chirildi!", this.$t("swal_deleted"), "success");
            })
            .catch((err) => {
              Swal.fire({
                icon: "error",
                title: this.$t("swal_error_title"),
                text: this.$t("swal_error_text"),
                //footer: "<a href>Why do I have this issue?</a>"
              });
              console.log(err);
            });
        }
      });
    },
  },
  mounted() {
    this.getTask();

    // this.getTask($id);
  },
};
</script>
<style scoped>
.card_title {
  padding: 0;
  margin: 10px 0;
  text-align: center;
  font-size: 32px;
  text-transform: uppercase;
  white-space: nowrap;
}
th {
  background-color: #2196f3;
  color: white;
}
.theme--light.v-data-table > .v-data-table__wrapper > table > thead > tr > th {
  color: white;
}
.mainTable table > thead.v-data-table-header > tr > th {
  background-color: #2196f3 !important;
  font-weight: bold !important;
  color: rgb(255, 255, 255) !important;
  margin: 10px 10px 0 !important;
}

.mainTable table > tbody > tr > td,
.mainTable table > thead > tr > th {
  border: 1px solid #d3d3ff;
  white-space: normal;
  overflow: hidden;
  text-overflow: ellipsis;
}

.mainTable table > tbody > tr > td p,
.mainTable table > thead > tr > th {
  white-space: normal;
  padding: 5px;
}

.v-item--active {
  background-color: #fff !important;
}
.dense {
  padding: 0px;
  height: 10px !important;
}

.dense .v-text-field__details {
  display: none !important;
}

.dense .v-text-field {
  padding: 0px;
  margin: 0px;
}

.dense div div div {
  margin-bottom: 0px !important;
}
</style>
