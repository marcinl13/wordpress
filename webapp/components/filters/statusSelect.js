export default Vue.component("component-statusSelect", {
  props: ["filterSend"],
  data: function name() {
    return {
      phrasesFilter: langSettingsFilter[0],
      status: statuses[0] || [, ,]
    };
  },
  created: function() {},
  methods: {
    chooseStatus: function(_value) {
      this.filterSend({ type: FILTER_STATUS || "", val: _value });
    }
  },
  template: `
  <div>
    <select class="form-control" id="selectStatus" v-on:change="chooseStatus($event.target.value);">
        <option value="0">{{phrasesFilter.CHOOSE_STATUS}}</option>
        <option value="1">{{status[1]}}</option>
        <option value="2">{{status[2]}}</option>
        <option value="3">{{status[3]}}</option>
    </select> 
  </div>
  `
});
