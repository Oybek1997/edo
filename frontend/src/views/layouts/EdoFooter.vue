<template>
  <v-toolbar max-width="100%" height="40" dense>
    <div>
      {{ new Date().getFullYear() }} yil - «UzAuto Motors» AJ Murojaat uchun:
      IT: 3056, 3078 HR: 3923 Kadr(Toshkent): 1703
    </div>
    <v-menu offset-y>
              <template v-slot:activator="{ on }">
                <v-btn text outlined small v-on="on">
                    <v-icon left>mdi-flag</v-icon>
                    {{ languages[$i18n.locale] }}
                </v-btn>
              </template>
              <v-list>
                <v-list-item
                    v-for="(item, index) in locales"
                    :key="index"
                    @click="setLocale(item.value)"
                >
                    <v-list-item-title>{{ item.text }}</v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>
    <v-spacer></v-spacer>
    <v-btn color="primary" class="ml-4" small icon to="/documentsidebar/chat">
      <v-icon>mdi-wechat</v-icon>
    </v-btn>
    <div class="mb-n3">
      <template
        v-for="(notification, key) in notifications.filter((n) => n.count)"
      >
        <v-badge
          :color="notification.color"
          overlap
          :content="notification.count"
          class="mx-4"
        >
          <v-btn
            small
            icon
            :to="notification.link"
            :title="$t('document.' + notification.name)"
          >
            <v-icon :color="notification.color">{{ notification.icon }}</v-icon>
          </v-btn>
        </v-badge>
      </template>
    </div>

    <v-btn color="primary" class="ml-4" small icon to="/documentsidebar/phonebook/list">
      <v-icon>mdi-phone</v-icon>
    </v-btn>
  </v-toolbar>
</template>
<script>
const axios = require("axios").default;
export default {
  data() {
    return {
      notifications: [],
      languages: {},
      locales: [],
      mylinestops_length: "",
    };
  },
  computed: {
    user() {
      return this.$store.getters.getUser();
    },
  },
  methods: {
    setLocale: function (arg) {
      this.$i18n.locale = arg;
      this.$store.dispatch("setLocale", arg);
      location.reload();
    },
    getList() {
      axios
        .post(this.$store.state.backend_url + "api/linestop/allmylinestops", {
          mylineid: this.user.employee.id,
          pagination: this.dataTableOptions,
          // line: this.selectedLines,
          search: this.search,
        })
        .then((response) => {
          this.mylinestops_length = response.data.mylinestopsq;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getNotifications() {
      axios
        .get(
          this.$store.state.backend_url +
            "api/documents/notification-new/" +
            this.$i18n.locale
        )
        .then((res) => {
          // this.$store.dispatch("setNotifications", res.data);
          this.notifications = res.data.notifications;
        })
        .catch((err) => {
          console.log(err);
        });
    },
  },
  mounted() {
    this.getNotifications();
    this.getList();
    this.languages =
    this.$store.state.COMPANY_ID == 1
        ? {
            uz_latin: `O'zbekcha`,
            uz_cyril: `English`,
            ru: `Русский`,
        }
        : {
            uz_latin: `O'zbekcha`,
            uz_cyril: `Ўзбекча`,
            ru: `Русский`,
        };
    this.locales =
    this.$store.state.COMPANY_ID == 1
        ? [
            { value: `uz_latin`, text: `O'zbekcha` },
            { value: `uz_cyril`, text: `English` },
            { value: `ru`, text: `Русский` },
        ]
        : [
            { value: `uz_latin`, text: `O'zbekcha` },
            { value: `uz_cyril`, text: `Ўзбекча` },
            { value: `ru`, text: `Русский` },
        ];
  },
};
</script>
