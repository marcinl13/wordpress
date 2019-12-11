export default Vue.component("component-pagitation", {
  props: ["pagi", "currentPage", "dataSize", "selectRows"],
  methods: {
    page: function(_type) {
      this.pagi(_type);
    },
    pagg: function(typ) {
      if (typ == "-") {
        if (this.currentPage > 1) this.currentPage--;
      }
      if (typ == "+") {
        if (this.currentPage * this.selectRows < this.dataSize) this.currentPage++;
      }
      if (typ == "start") {
        this.currentPage = 1;
      }
      if (typ == "end") {
        var x = this.dataSize / this.selectRows;

        if (this.dataSize > 0) this.currentPage = x > parseInt(x) ? parseInt(x) + 1 : parseInt(x);
        else this.currentPage = 1;
      }

      this.pagi(this.currentPage);
    },
    totalPages: function() {
      var x = this.dataSize / this.selectRows;

      if (this.dataSize > 0) return x > parseInt(x) ? parseInt(x) + 1 : parseInt(x);
      else return 1;
    },
    scrollToTop: function() {
      setTimeout(function() {
        window.scrollTo({ top: 0, behavior: "smooth" });
      }, 100);
    }
  },
  template: `
    <div class="d-flex justify-content-center pagitation">    
      <button class="form-control btn btn-sm btn-outline-info mr-1" type="button" v-on:click="page('start');"> << </button>  

      <button class="form-control btn btn-sm btn-outline-info" type="button" v-on:click="page('-');"> << </button>
      <p class="px-3 h-1 my-auto">{{currentPage}} / {{totalPages()}}</p>
      <button class="form-control btn btn-sm btn-outline-info" type="button" v-on:click="page('+');"> >> </button> 

      <button class="form-control btn btn-sm btn-outline-info ml-1" type="button" v-on:click="page('end');"> >> </button> 
    </div>`
});
