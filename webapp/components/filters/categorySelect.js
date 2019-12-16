export default Vue.component("component-categorySelect", {
  props: ["setCategory"],
  data: function name() {
    return {
      category: [{}]
    };
  },
  created: function() {
    try {
      this.category = serverGet(settings.apiUrl + "category/", {});
    } catch (error) {}
  },
  methods: {
    chooseCategory: function(_value) {
      this.setCategory(_value);
    }
  },
  template: `
  <select class="form-control" name="category" id="category" v-on:change="chooseCategory($event.target.value);">
    <option value="0">Wybierz wszystie</option>
    <option v-for="cat in category" :value=cat.id>{{cat.name}}</option>
  </select>  
  `
});
