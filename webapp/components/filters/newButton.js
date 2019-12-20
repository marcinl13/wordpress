export default Vue.component("component-newButton", {
  data: function name() {
    return {
      languageSettings: langSettingsFilter[0]
    };
  },
  props: ["optionOnAddNew"],
  methods: {
    scrollToTop: function() {
      setTimeout(function() {
        window.scrollTo({ top: 0, behavior: "smooth" });
      }, 1000);
      this.optionOnAddNew();
    }
  },
  template: `
  <button 
    class="form-control btn btn-small btn-success" 
      v-on:click="scrollToTop()">
        {{languageSettings.ADD_NEW}}
  </button>
  `
});
