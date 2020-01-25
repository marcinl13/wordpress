export default Vue.component("component-selectBeginData", {
  props: ["filterSend"],
  data: function() {
    return { currentMonthFrom: 0, currentMonthTo: 0, months: [{}] };
  },
  created: function() {
    this.currentMonth = new Date().getMonth() + 1;

    const monthList = getDateMonthList();

    for (let i = 0; i < 12; i++) {
      this.months.push({
        id: i + 1,
        name: monthList[i]
      });
    }
  },
  methods: {
    selectMonthFrom: function(_val) {
      this.filterSend({ type: FILTER_DSTART || "", val: _val });
    },
    selectMonthTo: function(_val) {
      this.filterSend({ type: FILTER_DEND || "", val: _val });
    }
  },
  template: `
  <div class=" ">
    <label class="label-form" for="dateFrom">od</label>
    <select id="dateFrom" class="form-control" style="max-width: 50px" v-on:change="selectMonthFrom($event.target.value);">
      <option v-for="month in months" :value=month.id>{{month.name}}</option>
    </select> 

    <label class="label-form" for="dateTo">do</label>
    <select id="dateTo" class="form-control" style="max-width: 50px" v-on:change="selectMonthTo($event.target.value);">
      <option v-for="month in months" :value=month.id>{{month.name}}</option>
    </select>  
  </div>

  
  `
});
