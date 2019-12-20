import optionBar from "../components/filters/optionBar.js";

new Vue({
  el: "#usr-orders",
  data: {
    tableID: "",
    phrases: langSettings[0],
    phrasesFilter: langSettingsFilter[0],
    orders: [],
    category: [],
    currentSort: "id",
    currentSortDir: "desc",
    currentPage: 1,
    selected: 5,
    onFilterLenght: 0,
    statusy: statuses[0] || [, ,],
    objFilter: {},
    optionBarSettingsTop: {
      selectByPage: true,
      pagitation: true,
      statusSelect: true,
      selectBeginData: true
    },
    optionBarSettingsBottom: {
      pagitation: true
    }
  },
  created: function() {
    this.tableID = uniqID();

    document.getElementsByTagName("article")[0].children[0].remove();
    document.getElementsByTagName("article")[0].children[0].removeAttribute("class");

    this.redeemData();
  },
  methods: {
    redeemData: function() {
      try {
        var response = serverGet(settings.apiUrl + "orders", {
          token: token.jwt
        });

        if (response.status == 200) {
          this.orders = response.data;
          this.onFilterLenght = this.orders.length;
        }
      } catch (error) {}
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
    dateFormating: function(_date) {
      return dateStandard(_date);
    },
    generateDetailsTable: function(_order) {
      var data = JSON.parse(atob(_order));
      var tmp = "";
      var count = 0;

      data.forEach(element => {
        count += 1;
        tmp += `<tr>
          <td  class="col-sm-2">${count}</td>
          <td  class="col-sm-5">${element.name}</td>
          <td  class="col-sm-2">${element.quantity}</td>
          <td  class="col-sm-3">${element.price} ${currencyCode[0]}</td>
        </tr>`;
      });

      Swal.fire({
        title: "",
        type: "info",
        html: `<table class="table " style="display: block; height: 150px; overflow:auto">
            <thead class='table-primary' style="position: sticky; top: 0; display:inline-table; width:100%; z-index:1;"> 
              <th class='text-center col-sm-2'>${this.phrases.LP}</th>
              <th class='text-center col-sm-5'>${this.phrases.NAME}</th>
              <th class='text-center col-sm-2'>${this.phrases.QUANTITY}</th>
              <th class='text-left col-sm-3'>${this.phrases.PRICE}</th>
            </thead>
            <tbody class=" " style="display:inline-table; width:100%;">${tmp}</tbody>
          </table> `,
        showCloseButton: true,
        focusConfirm: false
      });
    },
    onFilterChange: function(_obj) {
      this.objFilter = _obj;

      let dataFiltered = filterData(this.orders, _obj, this.selected, {
        FILTER_STATUS: "statusID",
        FILTER_DSTART: "dateOrder",
        FILTER_DEND: "dateRealization"
      });

      let filtered = dataFiltered.filtered;
      this.onFilterLenght = filtered.length;
      this.selected = dataFiltered._selected;

      return filtered;
    }
  },
  computed: {
    orderSorted: function() {
      let filtered = this.onFilterChange(this.objFilter);

      return sortData(filtered, this.currentSort, this.currentSortDir, this.currentPage, this.selected);
    }
  },
  components: {
    optionBar: optionBar
  },
  template: `
  <div class="d-block w-100 px-3 ">

    <optionBar
      :dataSize=onFilterLenght   
      :pagi=onPagitationClick 
      :currentPage=currentPage  
      :selectRows=selected 
      :optionBarSettings=optionBarSettingsTop
      :filterProps=onFilterChange
    /> 

    <table :id=tableID class="zamowienia table table-striped table-hover table-sm ">
      <thead class="table-primary" >
        <th class="text-center col-sm-1" style="width: 8%;">{{phrases.LP}}</th>
        <th class="text-center poiter" v-on:click="sort('dateOrder')">{{phrases.DATE_BEGIN}}</th>
        <th class="text-center poiter" v-on:click="sort('dateRealization')">{{phrases.DATE_END}}</th>
        <th class="text-center poiter" v-on:click="sort('statusID')">{{phrases.STATUS}}</th>
        <th class="text-center poiter" v-on:click="sort('completed')">{{phrases.PAYED}}</th>
        <th class="text-center poiter" v-on:click="sort('price')">{{phrases.PRICE}}</th>
        <th class="text-center poiter" v-on:click="sort('priceTransport')">{{phrases.TRANSPORT_PRICE}}</th>
        <th class="text-center poiter" v-on:click="sort('total')">{{phrases.FULL_PRICE}}</th>
        <th class="text-center">{{phrases.OPTIONS}}</th>
      </thead>
      <tbody v-if="onFilterLenght>0">
        <tr v-for="(zamowienie,count) in orderSorted" :class="{ 
          'o-inpg-bg': zamowienie.statusID == 1 , 
          'o-cmpl-bg': zamowienie.statusID == 2,
          'o-deny-bg': zamowienie.statusID == 3}">
          
          <td class="text-center">{{ ((-1 + currentPage) * selected) +( count+1)}}</td>
          <td class="text-center">{{dateFormating(zamowienie.dateOrder)}}</td>
          <td class="text-center">{{dateFormating(zamowienie.dateRealization)}}</td>
          <td class="text-center">{{statusy[zamowienie.statusID]}}</td>
          <td class="text-center" style="vertical-align: middle !important;">
            <i v-if="zamowienie.completed == 1" class="fa fa-check green"></i>
            <i v-else class="fa fa-close red"></i>
          </td>
          <td class="text-center">{{zamowienie.price}} ${currencyCode[0]}</td>          
          <td class="text-center">{{zamowienie.priceTransport}} ${currencyCode[0]}</td>          
          <td class="text-center">{{zamowienie.total}} ${currencyCode[0]}</td>          
          <td class="text-center">
          
            <div class="popup" 
              onMouseEnter="this.children[1].classList.toggle('show')" 
              onMouseLeave="this.children[1].classList.toggle('show')">
                <a class="bg-white btn btn-small btn-outline-primary fa fa-search" 
                  v-on:click="generateDetailsTable(zamowienie.produkty)"></a>
                <span class="popuptext" id="myPopup">{{phrasesFilter.SHOW_DETAILS}}</span>
            </div>
            
            <div class="popup" v-if="zamowienie.completed==0 && zamowienie.statusID==1" 
              onMouseEnter="this.children[1].classList.toggle('show')" 
              onMouseLeave="this.children[1].classList.toggle('show')">
                <a class="bg-white btn btn-small btn-outline-primary fa fa-money green"
                  :href=zamowienie.redirect></a>
                <span class="popuptext" id="myPopup">{{phrasesFilter.PAY}}</span>
            </div>

          </td>          
        </tr>
      </tbody>
      <tbody v-else>
        <tr>
          <td class="text-center" :colspan=controlTableSize(tableID)>{{phrases.NO_DATA}}</td>
        </tr>
      </tbody>
    </table>

    <optionBar
      :dataSize=onFilterLenght   
      :pagi=onPagitationClick 
      :currentPage=currentPage  
      :selectRows=selected 
      :optionBarSettings=optionBarSettingsBottom
    /> 
  </div>`
});
