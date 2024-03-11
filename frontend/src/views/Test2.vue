<template>
  <div class="fullHeight">

  </div>
</template>

<script>
const axios = require("axios").default;
export default {
  data() {
    return {
      item: "",
      testData: [{
          label: 'Bo Andersson',
          children: [{
            label: 'Obidov Sh',
            children: [{
              label: 'Asadillo',
            }, {
              label: 'Qosimov',
            }, {
              label: 'Umidillo',
            }]
          }, {
            label: 'Nozimjon',
            children: [{
          label: 'Bo Andersson',
          children: [{
            label: 'Obidov Sh',
            children: [{
              label: 'Asadillo',
            }, {
              label: 'Qosimov',
            }, {
              label: 'Umidillo',
            }]
          }, {
            label: 'Nozimjon',
            children: [{
                label: 'Komiljon',
              },{
                label: 'Cho',
              }
            ]
          },{
            label: 'Erdonayev'
          }]
        }]
          },{
            label: 'Erdonayev'
          }]
        }]
    };
  },
  methods: {
      renderContent (h, node) {
        return
          `<div class=['diy-wrapper', node.isCurrent ? 'current-select' : ''] >
            <div class="diy-con-name">{node.data.label}</div>
            <div class="diy-con-content">{node.data.content}</div>
          </div>`;
      }
  },

  mounted() {},
  created() {},
};
</script>
<style>
.node{
  border:1px solid red;
  background-color: #ddeeff;
}
</style>
