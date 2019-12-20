export default Vue.component("component-search", {
  props: ["filterSend"],
  data: function() {
    return { languageSettings: langSettingsFilter[0] };
  },
  methods: {
    filterBy: function(_value) {
      this.filterSend({ type: FILTER_STW || "", val: _value });
    }
  },
  template: `
    <input 
      class="form-control w-25" 
      type="text" 
      :placeholder=languageSettings.SEARCH
      v-on:input="filterBy($event.target.value)" 
    />
  `
});
