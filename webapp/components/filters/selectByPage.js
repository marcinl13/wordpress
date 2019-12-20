export default Vue.component("component-selectByPage", {
  props: ["filterSend", "pagi"],
  data: function() {
    return {
      rows: [5, 10, 15],
      rowsPerSite: "0"
    };
  },
  created: function() {
    var check = localStorage.getItem(LSI) ? JSON.parse(localStorage.getItem(LSI)).rowsPerSite : 5;

    this.selectByPage(check);
  },
  methods: {
    selectByPage: function(_value) {
      if (Number.isInteger(parseInt(_value)) == false) {
        _value = 5;
      }

      var localStorageData = JSON.parse(localStorage.getItem(LSI));

      localStorageData.rowsPerSite = _value;
      localStorage.setItem(LSI, JSON.stringify(localStorageData));

      this.rowsPerSite = _value;

      this.filterSend({ type: FILTER_ROWPAGE || "", val: _value });
      this.pagi("start");
    },
    getRowsPerSite() {
      return this.rowsPerSite;
    }
  },
  template: `
  <div class=" ">
    <select id="sPages" class="form-control" v-on:change="selectByPage($event.target.value);">
      <option v-for="row in rows" :value=row :selected="rowsPerSite == row">{{row}}</option>
    </select>  
  </div>

  
  `
});
