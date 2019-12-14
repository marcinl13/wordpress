import optionBar from "../components/filters/optionBar.js";

new Vue({
  el: "#adm-docs",
  data: {
    tableID: "",
    phrases: langSettings[0],
    phrasesFilter: langSettingsFilter[0],
    docs: [],
    currentSort: "id",
    currentSortDir: "desc",
    currentPage: 1,
    selected: 5,
    onFilterLenght: 1,
    objFilter: {},
    optionBarSettingsTop: {
      selectByPage: true,
      selectBeginData: true,
      pagitation: true
    },
    optionBarSettingsBottom: {
      pagitation: true
    }
  },
  created: function() {
    this.tableID = uniqID();
    
    this.redeemData();
  },
  methods: {
    redeemData: function() {
      try {
        var response = serverGet(settings.apiUrl + "docs", {
          token: token.jwt
        });

        if (response.status == 200) {
          this.docs = response.data;
          this.onFilterLenght = this.docs.length;
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
    showHideAddNew: function(_mode) {
      if (_mode == "show") {
        document.prevDoclementById("addNew").style.display = "flex";
      } else {
        document.prevDoclementById("addNew").style.display = "none";
      }
    },
    clearAddNew: function() {
      this.redeemData();
    },
    onFilterChange: function(_obj) {
      this.objFilter = _obj;

      let dataFiltered = filterData(this.docs, _obj, this.selected, {
        FILTER_DSTART: "dateCreate",
        FILTER_DEND: "dateEnd"
      });

      let filtered = dataFiltered.filtered;
      this.onFilterLenght = filtered.length;
      this.selected = dataFiltered._selected;

      return filtered;
    },
    dateFormating: function(_date) {
      return dateStandard(_date);
    },
    previewText: function(_docType) {
      return this.phrasesFilter.PREVIEW + " " + _docType.split(" ")[0];
    },
    prevDoc: function(_id, _gt, _mode) {
      var posted = serverPost(settings.apiUrl + "docs", {
        token: token.jwt,
        id: _id,
        dT: _gt,
        mode: _mode
      });

      if (posted && posted.status == 200) {
        Swal.fire({
          title: "",
          type: "info",
          html: `<div class="mx-2" style="overflow: auto;">${posted.data}</div>`,
          showCloseButton: false,
          focusConfirm: false,
          confirmButtonText: this.phrasesFilter.CLOSE
        });
      } else {
        Swal.fire("", posted.message, "warning");
      }
    }
  },
  computed: {
    computedDocs: function() {
      let filtered = this.onFilterChange(this.objFilter);

      return sortData(filtered, this.currentSort, this.currentSortDir, this.currentPage, this.selected);
    }
  },
  components: {
    optionBar: optionBar
  },
  template: `
  <div class="d-block w-100 px-3 mt-3">

     <optionBar
      :dataSize=onFilterLenght    
      :pagi=onPagitationClick 
      :currentPage=currentPage  
      :selectRows=selected 
      :optionBarSettings=optionBarSettingsTop
      :filterProps=onFilterChange
    />

    <table :id=tableID class="table table-striped table-hover table-sm ">
      <thead class="table-primary">
        <th class="text-center">{{phrases.LP}}</th>
        <th @click="sort('docType')" class="text-center poiter">{{phrases.NAME}}</th>
        <th @click="sort('dateCreate')" class="text-center poiter">{{phrases.DATE_BEGIN}}</th>
        <th @click="sort('dateEnd')" class="text-center poiter">{{phrases.DATE_END}}</th>
        <th class="text-center ">{{phrases.OPTIONS}}</th>
      </thead>
      <tbody class="table-light" v-if="onFilterLenght>0">
        <tr v-for="(doc, count) in computedDocs">
          <td class="text-center">{{ ((-1 + currentPage) * selected) +( count+1)}}</td>
          <td class="text-center">{{doc.shortName}}</td>
          <td class="text-center">{{dateFormating(doc.dateCreate)}}</td>
          <td class="text-center">{{dateFormating(doc.dateEnd)}}</td>
          <td class="text-center">
            
            <div class="popup" 
              onMouseEnter="this.children[1].classList.toggle('show')" 
              onMouseLeave="this.children[1].classList.toggle('show')">
                <a class="bg-white btn btn-small btn-outline-primary fa fa-search"                   
                  v-on:click="prevDoc(doc.id, doc.docType, 'gz')" ></a>
                <span class="popuptext" id="myPopup">{{phrasesFilter.SHOW_DETAILS}}</span>
            </div>

            <div class="popup" 
              onMouseEnter="this.children[1].classList.toggle('show')" 
              onMouseLeave="this.children[1].classList.toggle('show')">
                <a class="bg-white btn btn-small btn-outline-primary fa fa-file-pdf-o orange"
                  :href="'?page=docs&mode=show&dT=' + doc.docType +'&id='+ doc.id" ></a>
                <span class="popuptext" id="myPopup">{{previewText(doc.shortName)}}</span> 
            </div>

          </td>
        </tr>
      </tbody>
      <tfoot v-else>
        <tr class="text-center" >
          <td :colspan=controlTableSize(tableID)>{{phrases.NO_DATA}}</td>
        </tr>
      </tfoot>
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
