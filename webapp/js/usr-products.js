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
        this.objFilter = _obj;

        let dataFiltered = filterData(this.products, _obj, this.selected, {
          FILTER_STW: "nazwa",
          FILTER_CAT: "id_kategori",
          FILTER_STATUS: "id_statusu"
        });

        let filtered = dataFiltered.filtered;
        this.onFilterLenght = filtered.length;
        this.selected = dataFiltered._selected;

        return filtered;
      }
    },
    computed: {
      productsSorted: function() {
        let filtered = this.onFilterChange(this.objFilter);

        return sortData(filtered, this.currentSort, this.currentSortDir, this.currentPage, this.selected);
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
