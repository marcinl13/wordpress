import optionBar from "../components/filters/optionBar.js";

new Vue({
  el: "#adm-category",
  data: {
    phrases: langSettings[0],
    phrasesFilter: langSettingsFilter[0],
    categories: [],
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
    this.pobierzDane();
  },
  methods: {
    pobierzDane: function() {
      try {
        var response = serverGet(settings.apiUrl + "category", {
          token: token.jwt
        });

        if (response.status == 200) {
          this.categories = response.data;
          this.onFilterLenght = this.categories.length;
        }
      } catch (error) {}
    },
    clearAddNew: function() {
      this.pobierzDane();
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
      return false;
    },
    showHideAddNew: function(_mode) {
      if (_mode == "show") {
        document.getElementById("addNew").style.display = "flex";
      } else {
        document.getElementById("addNew").style.display = "none";
      }
    },
    onCreateCategory: function() {
      this.onEditCategory();
    },
    onEditCategory: function(_id = null) {
      /* mode create */
      var defaultID = "",
        defaultName = "",
        defaultTitle = this.phrases.ADD_NEW_CATEGORY;

      /* mode edit */
      if (_id != null) {
        var filtered = this.categories.filter(data => data.id == _id)[0];
        defaultID = filtered.id;
        defaultName = filtered.nazwa;
        defaultTitle = this.phrases.EDIT_CATEGORY;
      }

      /* window */
      Swal.fire({
        title: `${defaultTitle}`,
        html: `
        <div class="form px-5 py-2 center-block mx-auto">
          <hidden id="id" value="${defaultID}"/>

          <div class="form-group row mb-0">            
            <label for="_name" class="col-sm-4 col-form-label col-form-label-sm mr-2">${this.phrases.NAME}</label>
            <input class="form-control" id="_name" type='text' placeholder="podaj nazwę" value="${defaultName}" maxlength="20"/>
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

          try {
            if (validate([name]) === false) {
              Swal.fire("", this.phrasesFilter.FILL_FIELDS, "warning");
              return false;
            }

            var posted = serverPost(settings.apiUrl + "category", {
              token: token.jwt,
              name: name,
              id: defaultID
            });

            if (posted.status == 200 || posted.status == 201) {
              Swal.fire("", posted.message, "success");

              this.categories = [{}];
              this.categories = posted.data;
              this.onFiltered();
            } else {
              Swal.fire("", posted.message, "warning");
            }
          } catch (error) {}
        }
      });
    },
    onDeleteCategory: function(_id) {
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
            var deleted = serverDelete(settings.apiUrl + "category", {
              token: token.jwt,
              id: _id
            });

            if (deleted.status == 200) {
              Swal.fire("", deleted.message, "success");

              this.pobierzDane();
            } else {
              Swal.fire("", deleted.message, "warning");
            }
          } catch (error) {}
        }
      });
    },
    onFilterChange2: function(_obj) {
      let filtered = this.categories;

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

      this.onFilterLenght = filtered.length;

      return filtered;
    }
  },
  computed: {
    productsCategory: function() {
      var filtered = this.onFilterChange2(this.objFilter);
      return filtered
        .sort((a, b) => {
          let modifier = 1;
          if (this.currentSortDir === "desc") modifier = -1;
          if (this.currentSort == "id") {
            if (parseInt(a[this.currentSort]) < parseInt(b[this.currentSort])) return -1 * modifier;
            if (parseInt(a[this.currentSort]) > parseInt(b[this.currentSort])) return 1 * modifier;
          } else {
            if (a[this.currentSort] < b[this.currentSort]) return -1 * modifier;
            if (a[this.currentSort] > b[this.currentSort]) return 1 * modifier;
          }

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
    optionBar: optionBar
  },
  template: `
  <div class="d-block w-100 px-3 mt-3">
 
    <optionBar
      :dataSize=onFilterLenght
      :category=categories      
      :pagi=onPagitationClick 
      :currentPage=currentPage  
      :selectRows=selected 
      :optionBarSettings=optionBarSettingsTop
      :onAddNew=onCreateCategory
      :filterProps=onFilterChange2
    /> 
    
    <table class="table table-striped table-hover table-sm ">
      <thead class="table-primary">
        <th class="text-center">{{phrases.LP}}</th>
        <th @click="sort('nazwa')" class="text-center poiter">{{phrases.NAME}}</th>
        <th class="text-center ">{{phrases.OPTIONS}}</th>
      </thead>
      <tbody class="table-light" v-if="onFilterLenght>0">
        <tr v-for="(category, count) in productsCategory">
          <td class="text-center">{{ ((-1 + currentPage) * selected) +( count+1)}}</td>
          <td class="text-center">{{category.nazwa}}</td>
          <td class="text-center">
            <button class="btn btn-sm btn-success" v-on:click="onEditCategory(category.id)">{{phrasesFilter.EDIT}}</button>
            <button class="btn btn-sm btn-danger" v-on:click="onDeleteCategory(category.id)">{{phrasesFilter.DELETE}}</button>
          </td>
        </tr>
      </tbody>
      <tfoot v-else>
        <tr class="text-center" >
          <td colspan="4">{{phrases.NO_DATA}}</td>
        </tr>
      </tfoot>
    </table>

     <optionBar
      :dataSize=onFilterLenght
      :category=categories      
      :pagi=onPagitationClick 
      :currentPage=currentPage  
      :selectRows=selected 
      :optionBarSettings=optionBarSettingsBottom
      :onAddNew=onCreateCategory
      :filterProps=onFilterChange2
    />

  </div>`
});
