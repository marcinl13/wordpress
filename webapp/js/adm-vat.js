import optionBar from "../components/filters/optionBar.js";

new Vue({
  el: "#adm-vat",
  data: {
    phrases: langSettings[0],
    phrasesFilter: langSettingsFilter[0],
    vatList: [],
    selectedProducts: 0,
    selectedCategory: 0,
    currentSort: "id",
    currentSortDir: "desc",
    currentPage: 1,
    selected: 5,
    onFilterLenght: 1,
    objFilter: {},
    optionBarSettingsTop: {
      newButton: true,
      selectByPage: true,
      search: true,
      pagitation: true
    },
    optionBarSettingsBottom: {
      pagitation: true
    }
  },
  created: function() {
    this.redeemData();
  },
  methods: {
    redeemData: function() {
      try {
        var response = serverGet(settings.apiUrl + "vat", {
          token: token.jwt
        });

        if (response.status == 200) {
          this.vatList = response.data;
          this.onFilterLenght = this.vatList.length;
        }
      } catch (error) {}
    },
    clearAddNew: function() {
      this.redeemData();
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
        document.getElementById("addNew").style.display = "flex";
      } else {
        document.getElementById("addNew").style.display = "none";
      }
    },
    clearAddNew: function() {
      this.redeemData();
    },
    onCreateVat: function() {
      this.onEditVat();
    },
    onEditVat: function(_id = null) {
      /* mode create */
      var defaultID = "",
        defaultName = "",
        defaultValue = "0.00",
        defaultTitle = this.phrases.ADD_NEW_VAT;

      /* mode edit */
      if (_id != null) {
        var filtered = this.vatList.filter(data => data.id == _id)[0];
        defaultID = filtered.id;
        defaultName = filtered.name;
        defaultValue = filtered.stawka;
        defaultTitle = this.phrases.EDIT_VAT;
      }

      /* window */
      Swal.fire({
        title: `${defaultTitle}`,
        html: `
        <div class="form px-5 py-2 center-block mx-auto">
          <hidden id="id" value="${defaultID}"/>

          <div class="form-group row mb-0">            
            <label for="_name" class="col-sm-3 col-form-label col-form-label-sm mr-2">${this.phrases.NAME}</label>
            <input class="form-control" id="_name" type='text' placeholder="podaj nazwę" value="${defaultName}"/>
          </div>

          <div class="form-group row mb-0">            
            <label for="_vat" class="col-sm-3 col-form-label col-form-label-sm mr-2">${this.phrases.RATE}</label>
            <input class="form-control" id="_vat" type='text' placeholder="podaj nazwę" value="${defaultValue}"/>
          </div>
        </div>`,
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: this.phrasesFilter.SAVE,
        cancelButtonText: this.phrasesFilter.CLOSE
      }).then(result => {
        if (result.value) {
          var name = document.getElementById("_name").value;
          var vatValue = document.getElementById("_vat").value;

          try {
            if (validate([name], [], [vatValue]) === false) {
              Swal.fire("", this.phrasesFilter.FILL_FIELDS, "warning");
              return false;
            }

            var posted = serverPost(settings.apiUrl + "vat", {
              token: token.jwt,
              name: name,
              value: vatValue,
              id: defaultID
            });

            if (posted.status == 200 || posted.status == 201) {
              Swal.fire("", posted.message, "success");

              this.vatList = [{}];
              this.vatList = posted.data;
              this.onFiltered();
            } else {
              Swal.fire("", posted.message, "warning");
            }
          } catch (error) {}
        }
      });
    },
    onDeleteVat: function(_id) {
      Swal.fire({
        text: this.phrasesFilter.ASK_DELETE,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: this.phrasesFilter.YES,
        cancelButtonText: this.phrasesFilter.NO
      }).then(result => {
        if (result.value) {
          try {
            var deleted = serverDelete(settings.apiUrl + "vat", {
              token: token.jwt,
              id: _id
            });

            if (deleted.status == 200) {
              Swal.fire("", deleted.message, "success");

              this.redeemData();
            } else {
              Swal.fire("", deleted.message, "warning");
            }
          } catch (error) {}
        }
      });
    },
    onFilterChange: function(_obj) {
      let filtered = this.vatList;

      this.objFilter = _obj;

      if (_obj != undefined && _obj.type == FILTER_ROWPAGE) {
        this.selected = parseInt(_obj.val);
      }
      if (_obj != undefined && _obj.type == FILTER_STW && _obj.val != "") {
        var filterByName = _obj.val.toLowerCase();

        filtered = filtered.filter(function(data) {
          return data.name.toLowerCase().indexOf(filterByName) == 0;
        });
      }

      this.onFilterLenght = filtered.length;

      return filtered;
    }
  },
  computed: {
    ComputedVat: function() {
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
      :category=vatList      
      :pagi=onPagitationClick 
      :currentPage=currentPage  
      :selectRows=selected 
      :optionBarSettings=optionBarSettingsTop
      :onAddNew=onCreateVat
      :filterProps=onFilterChange
    /> 
    
    <table class="table table-striped table-hover table-sm ">
      <thead class="table-primary">
        <th class="text-center">{{phrases.LP}}</th>
        <th @click="sort('nazwa')" class="text-center poiter">{{phrases.NAME}}</th>
        <th @click="sort('stawka')" class="text-center poiter">{{phrases.RATE}}</th>
        <th class="text-center ">{{phrases.OPTIONS}}</th>
      </thead>
      <tbody class="table-light" v-if="onFilterLenght>0">
        <tr v-for="(vat, count) in ComputedVat">
          <td class="text-center">{{ ((-1 + currentPage) * selected) +( count+1)}}</td>
          <td class="text-center">{{vat.name}}</td>
          <td class="text-center">{{vat.stawka}}</td>
          <td class="text-center">
            <button class="btn btn-sm btn-success" v-on:click="onEditVat(vat.id)">{{phrasesFilter.EDIT}}</button>
            <button class="btn btn-sm btn-danger" v-on:click="onDeleteVat(vat.id)">{{phrasesFilter.DELETE}}</button>
          </td>
        </tr>
      </tbody>
      <tfoot v-else>
        <tr class="text-center" >
          <td colspan="5">{{phrases.NO_DATA}}</td>
        </tr>
      </tfoot>
    </table>

  </div>`
});
