import Vue from "vue";
import Vuex from "vuex";
import Cookies from "js-cookie";

Vue.use(Vuex);

export default new Vuex.Store({
  strict: true,
  state: {
    backend_url: "https://b-edo.uzautomotors.com/",
    // backend_url: 'https://b-edo.uzautomotors.com/',
    access_token: Cookies.get("access_token") || "null",
    AS400_url: "https://edo-db2.uzautomotors.com/",
    COMPANY_NAME_RU: "«UzAuto Motors» AJ",
    COMPANY_NAME_UZ_LATIN: "«UzAuto Motors» AJ",
    COMPANY_NAME_UZ_CYRIL: "«UzAuto Motors» AJ",
    EIMZO_DOMAIN: "edo.uzautomotors.com",
    EIMZO_API_KEY:
      "79DC56F42765A0017C31309DB9600EA924684ED023A8079460454768331626AB94CFFF8FC2D4007976D4A6C56F11D56DFA962276DC54AE8C0F39E8A8EBDFA10B",
    BACKEND_URL: "",
    COMPANY_ID: 1,
    PHONE_IT: "3056, 3078",
    PHONE_PM: "3923",
    PHONE_PM_TASHKENT: "1729",
    user: null,
    permissions: null,
    roles: null,
    locale: Cookies.get("locale") || "ru",
    errors: [],
    redirect_url: null,
    document_list: [],
    notifications: {},
  },
  getters: {
    checkPermission: (state) => (permission) => {
      let localStorage = window.localStorage;

      let permissions = JSON.parse(localStorage.getItem("permissions"));
      if (permissions) return permissions.find((v) => v == permission);
      return false;
      // let permissions = state.permissions;
      // if (permissions)
      //   return permissions.find(v => v == permission);
      // return false;
    },
    checkRole: (state) => (role) => {
      let localStorage = window.localStorage;
      let roles = JSON.parse(localStorage.getItem("roles"));
      // let roles = state.roles
      if (roles) return roles.find((v) => v.name == role);
      return false;
    },
    getUser: (state) => () => {
      let localStorage = window.localStorage;
      let user = JSON.parse(localStorage.getItem("user"));
      if (user) return user;
      return false;
    },
    getRoles: (state) => () => {
      let localStorage = window.localStorage;
      let roles = JSON.parse(localStorage.getItem("roles"));
      if (roles) return roles;
      return false;
    },
    getAccessToken: (state) => () => {
      let localStorage = window.localStorage;
      let access_token = localStorage.getItem("access_token");
      if (access_token) return access_token;
      return false;
    },
    // getBooks:(state) => state.books,
  },
  mutations: {
    setUser: (state, arg) => {
      let localStorage = window.localStorage;
      localStorage.setItem("user", JSON.stringify(arg));

      if (!arg) {
        let localStorage = window.localStorage;
        localStorage.setItem("eimzo_key", JSON.stringify(arg));
      }
      // state.user = arg;
    },
    setPermissions: (state, arg) => {
      let localStorage = window.localStorage;
      localStorage.setItem("permissions", JSON.stringify(arg));
      // state.permissions = arg;
    },
    setRole: (state, arg) => {
      let localStorage = window.localStorage;
      localStorage.setItem("roles", JSON.stringify(arg));
      // state.roles = arg;
    },
    setDocumentList: (state, arg) => {
      state.document_list = arg;
    },
    setNotifications: (state, arg) => {
      state.notifications = arg;
    },
    setAccessToken: (state, arg) => {
      let localStorage = window.localStorage;
      localStorage.setItem("access_token", arg);
    },
    setRedirectUrl(state, arg) {
      state.redirect_url = arg;
    },
  },
  actions: {
    setLocale: (state, arg) => {
      Cookies.set("locale", arg);
    },
    setPermissions: (context, arg) => {
      context.commit("setPermissions", arg);
    },
    setRole: (context, arg) => {
      context.commit("setRole", arg);
    },
    setDocumentList: (context, arg) => {
      context.commit("setDocumentList", arg);
    },
    setNotifications: (context, arg) => {
      context.commit("setNotifications", arg);
    },
    setUser: (context, arg) => {
      context.commit("setUser", arg);
    },
    setAccessToken: (context, arg) => {
      context.commit("setAccessToken", arg);
    },
    setRedirectUrl: (context, arg) => {
      context.commit("setRedirectUrl", arg);
    },
  },
  modules: {},
});
