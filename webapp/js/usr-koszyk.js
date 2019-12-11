import dump from "../components/tables/dump.js";

new Vue({
  el: "#usr-koszyk",
  data: {
    productsList: [{}]
  },
  created: function() {
    document.getElementsByTagName("article")[0].children[0].remove();
    document.getElementsByTagName("article")[0].children[0].removeAttribute("class");
  },
  methods: {
    pobierzDane: function() {
      try {
        var parsed = JSON.parse(localStorage.getItem(LSI));

        // var response = serverGet(settings.apiUrl + "dump", {
        //   ids: parsed.products.join(","),
        //   token: token.jwt
        // });
        
        var response = serverGet(settings.apiUrl + "cart", {
          ids: parsed.products.join(","),
          token: token.jwt
        });

        if (response.status == 200) {
          this.productsList = response.data;
        }
      } catch (error) {}
    }
  },
  components: {
    dump: dump
  },
  template: `
  <div class="d-block w-100 px-3 mb-3">
    <dump class='d-flex-inline' />
  </div>
  `
});
