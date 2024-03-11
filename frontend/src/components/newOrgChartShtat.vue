<template>
  <div>
    <div style="height: 50px; border: 1px solid red">
      <v-btn color="success" outlined @click="compact = !compact; chartReference.compact(compact).render().fit()"
        >Compact({{ compact ? "True" : "False" }})</v-btn
      >
      {{ headCount }}
    </div>
    <div ref="svgElementContainer"></div>
  </div>
</template>
<script>
import { OrgChart } from "d3-org-chart";

export default {
  name: "Chart3",
  props: ["data", "chartType", "headCount"],
  data() {
    return {
      chartReference: null,
      compact: false,
    };
  },
  watch: {
    data(value) {
      this.renderChart(value);
    },
    // headCount(newVal, oldVal, data) {
    //   // Only re-render the chart if headCount actually changes
    //   if (newVal !== oldVal) {
    //     this.renderChart(data);
    //   }
    // }
  },
  created() {
    console.log("created org chart");
  },
  methods: {
    renderChart(data) {
      // console.log(this.headCount);
      if (!this.chartReference) {
        this.chartReference = new OrgChart();
      }
      this.chartReference
        .container(this.$refs.svgElementContainer) // node or css selector
        .data(data)
        // .nodeWidth((d) => 250)
        // .initialZoom(0.7)
        .nodeHeight((d) => 50)
        // .childrenMargin((d) => 40)
        // .compactMarginBetween((d) => 15)
        // .compactMarginPair((d) => 40)
        // .nodeHeight((d) => 120)
        .nodeContent(function (d, i, arr, state) {
          const color = "#FFFFFF";
          const imageDiffVert = 25 + 2;
          let menejer = "Vakant";
          let tabel = '-';
          let tarif = '-';
          let range = '-';
          let head_count = '';
          let imageUrl = "https://edo.uzautomotors.com/img/User-Default.5c6aa235.jpg";
          if(d.data.manager){
            menejer = d.data.manager
          }
          if(d.data.tabel!='avatar'){
            imageUrl = 'https://b-edo.uzautomotors.com/get-img/' + d.data.tabel;
            tabel = d.data.tabel;
          }
          if(d.data.tariff){
            tarif = d.data.tariff
          }
          if(d.data.tariff){
            range = d.data.range
          }
          if(this.headCount==2){
            head_count = d.data.rate_bp;
          }
          else if(this.headCount==3){
            head_count = d.data.rate_count;
          }
          else{
            head_count = d.data.head_count;
          }
          
          return `
          <div style="font-family: 'Inter', sans-serif;background-color:${color}; position:absolute;margin-top:-1px; margin-left:-1px;width:${d.width}px;height:${d.height};">
              
            <table style="border-collapse: collapse; font-size: 12px; width: 100%; ">
              <thead>
                <tr>
                  
                  <td colspan="5" style="max-width:20px; padding: 10px; border: 1px solid black; white-space: wrap; text-align:center;">${d.data.name}</td>
                </tr>
                
              </thead>
              </table>


           </div>
                            `;
        })
        .onNodeClick((d) => console.log(d + " node clicked"))
        .render();
    },
  },
};
</script>
<style scoped>
.avatar {
  border-radius: 5px;
  border: 1px #dce5ef solid;
  margin: 5px;
  height: 85px;
  width: 65px;
}
th, td {
            border: 1px solid black; /* Adds inner borders to the cells */
            padding: 8px; /* Optional: Adds some padding inside the cells */
        }
</style>