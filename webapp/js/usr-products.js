import optionBar from "../components/filters/optionBar.js";
import productsTable from "../components/tables/productsTable.js";
import productListGrid from "../components/tables/productListGrid.js";

window.onload = () => {
  ordersCounter();

  var newVue = new Vue({
    el: "#usr-products",
    data: {
      message: "Produkty",
      products: [{}],
      selectedProducts: 0,
      selectedCategory: 0,
      currentListType: "",
      currentSort: "nazwa",
      currentSortDir: "desc",
      currentPage: 1,
      selected: 0,
      onFilterLenght: 0,
      objFilter: {},
      optionBarSettingsTop: {
        searchWithCategory: true,
        selectByPage: true,
        pagitation: true
      },
      filters: {
        searchCategory: 0,
        searchStatus: 0,
        rowPerPage: 5,
        searchDateBegin: "",
        searchDateEnd: "",
        searchStartWith: ""
      },
      optionBarSettingsBottom: {
        pagitation: true
      }
    },
    beforeCreate: function() {
      document.getElementsByTagName("article")[0].children[0].remove();
      document.getElementsByTagName("article")[0].children[0].removeAttribute("class");
    },
    created: function() {
      this.redeemData();

      if (localStorage.getItem("ListType")) {
        this.currentListType = localStorage.getItem("ListType") == "grid" ? "grid" : "list";
      } else {
        localStorage.setItem("ListType", "grid");
      }
    },
    methods: {
      redeemData: function() {
        try {
          var rowsPerSite = parseInt(JSON.parse(localStorage.getItem(LSI)).rowsPerSite);

          this.selected = Number.isInteger(rowsPerSite) ? rowsPerSite : 5;

          var response = serverGet(settings.apiUrl + "products", {
            token: token.jwt
          });

          if (response.status == 200) {
            this.products = response.data;
            this.onFilterLenght = this.products.data.length;
          }
        } catch (error) {}
      },
      dodajDoKoszyka: function(_id) {
        var koszykData = JSON.parse(localStorage.getItem(LSI));

        koszykData.products[koszykData.products.length] = _id;
        localStorage.setItem(LSI, JSON.stringify(koszykData));

        document.getElementById("ordersCount").innerText = koszykData.products.length;
      },
      sort: function(s) {
        if (s === this.currentSort) {
          this.currentSortDir = this.currentSortDir === "asc" ? "desc" : "asc";
        }
        this.currentSort = s;
      },
      onPagitationClick: function(typ) {
        if (typ == "-") {
          if (this.currentPage > 1) this.currentPage--;
        }
        if (typ == "+") {
          if (this.currentPage * this.selected < this.onFilterLenght) this.currentPage++;
        }
        if (typ == "start") {
          this.currentPage = 1;
        }
        if (typ == "end") {
          var x = this.onFilterLenght / this.selected;

          if (this.onFilterLenght > 0) this.currentPage = x > parseInt(x) ? parseInt(x) + 1 : parseInt(x);
          else this.currentPage = 1;
        }
        return false;
      },
      onChangeListType: function(_type) {
        localStorage.setItem("ListType", _type ? "grid" : "list");

        this.currentListType = _type ? "grid" : "list";
      },
      onFilterChange: function(_obj) {
        let filtered = this.products;

        this.objFilter = _obj;

        if (_obj != undefined && _obj.type == FILTER_ROWPAGE) {
          this.selected = parseInt(_obj.val);
        }
        if (_obj != undefined && _obj.type == FILTER_STW && _obj.val != "") {
          var filterByName = _obj.val.toLowerCase();

          filtered = filtered.filter(function(data) {
            return data.nazwa.toLowerCase().indexOf(filterByName) == 0;
          });
        }
        if (_obj != undefined && _obj.type == FILTER_CAT && _obj.val != 0) {
          filtered = filtered.filter(function(data) {
            return parseInt(data.id_kategori) === parseInt(_obj.val);
          });
        }
        if (_obj != undefined && _obj.type == FILTER_STATUS && _obj.val != 0) {
          filtered = filtered.filter(function(data) {
            return parseInt(data.id_statusu) === parseInt(_obj.val);
          });
        }

        this.onFilterLenght = filtered.length;

        return filtered;
      }
    },
    computed: {
      productsSorted: function() {
        return this.onFilterChange(this.objFilter)
          .sort((a, b) => {
            let modifier = 1;
            if (this.currentSortDir === "desc") modifier = -1;
            if (a[this.currentSort] < b[this.currentSort]) return -1 * modifier;
            if (a[this.currentSort] > b[this.currentSort]) return 1 * modifier;
            return 0;
          })
          .filter((row, index) => {
            let start = (this.currentPage - 1) * this.selected;
            let end = this.currentPage * this.selected;
            if (index >= start && index < end) return true;
          });
      }
    },
    components: {
      optionBar: optionBar,
      productsTable: productsTable,
      productListGrid: productListGrid
    },
    template: `
      <div class="d-block w-100">

        <optionBar
          :dataSize=onFilterLenght  
          :pagi=onPagitationClick 
          :currentPage=currentPage  
          :selectRows=selected 
          :optionBarSettings=optionBarSettingsTop
          :filterProps=onFilterChange
        />

        <div>
          <button class="btn-list" v-on:click="onChangeListType(1)">
            <svg viewBox="0 0 24 24" preserveAspectRatio="xMidYMid meet" focusable="false" class="btn-list-svg">
              <g>
                <path d="M4 11h5V5H4v6zm0 7h5v-6H4v6zm6 0h5v-6h-5v6zm6 0h5v-6h-5v6zm-6-7h5V5h-5v6zm6-6v6h5V5h-5z"></path>
                <path d="M0 0h24v24H0z" fill="none"></path>
              </g>
            </svg>
          </button>

          <button class="btn-list" v-on:click="onChangeListType(0)">
            <svg viewBox="0 0 24 24" preserveAspectRatio="xMidYMid meet" focusable="false" class="btn-list-svg">
              <g>
                <path d="M4 14h4v-4H4v4zm0 5h4v-4H4v4zM4 9h4V5H4v4zm5 5h12v-4H9v4zm0 5h12v-4H9v4zM9 5v4h12V5H9z" class="style-scope yt-icon"></path>
                <path d="M0 0h24v24H0z" fill="none" class="style-scope yt-icon"></path>
              </g>
            </svg>
          </button>          
        </div>
        
        <productsTable v-if="currentListType == 'list'"
          :productsData=productsSorted 
          :sortFunction=sort 
          :AddNewOrder=dodajDoKoszyka
          :currentPage=currentPage
          :selectRows=selected
          :dataSize=onFilterLenght
        />
        
        <productListGrid v-else
          :productsData=productsSorted 
          :sortFunction=sort 
          :AddNewOrder=dodajDoKoszyka
          :currentPage=currentPage
          :selectRows=selected
          :dataSize=onFilterLenght          
        />

        

        <optionBar
          :dataSize=onFilterLenght  
          :pagi=onPagitationClick 
          :currentPage=currentPage  
          :selectRows=selected 
          :optionBarSettings=optionBarSettingsBottom
        />   
      </div>`
  });
};
