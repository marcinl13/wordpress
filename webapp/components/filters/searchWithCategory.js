export default Vue.component("searchWithCategory", {
  props: ["filterSend"],
  data: function name() {
    return {
      categories: [{}],
      languageSettings: langSettingsFilter[0]
    };
  },
  created: function() {
    try {
      var response = serverGet(settings.apiUrl + "category", {
        token: token.jwt
      });

      if (response.status == 200) {
        this.categories = response.data;
      }
    } catch (error) {}
  },
  methods: {
    chooseCategory: function(_value) {
      this.filterSend({ type: FILTER_CAT || "", val: _value });
    },
    filterBy: function(_value) {
      this.filterSend({ type: FILTER_STW || "", val: _value });
    }
  },
  template: `
  <div class="input-group searchWithCategory">
    <input type="text" class="form-control" :placeholder=languageSettings.SEARCH v-on:input="filterBy($event.target.value)">
  <div class="input-group-append">
    <select class="form-control" name="category" id="category" v-on:change="chooseCategory($event.target.value);">
      <option value="0">{{languageSettings.ALL}}</option>
      <option v-for="cat in categories" :value=cat.id>{{cat.name}}</option>
    </select> 
  </div>
</div>`
});
