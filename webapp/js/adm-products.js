import optionBar from "../components/filters/optionBar.js";

new Vue({
  el: "#adm-products",
  data: {
    phrases: langSettings[0],
    phrasesFilter: langSettingsFilter[0],
    products: [],
    categories: [{}],
    vatValues: [{}],
    selectedProducts: 0,
    selectedCategory: 0,
    currentSort: "id",
    currentSortDir: "desc",
    currentPage: 1,
    selected: 5,
    objFilter: {},
    onFilterLenght: 1,
    optionBarSettingsTop: {
      searchWithCategory: true,
      newButton: true,
      selectByPage: true,
      // categorySelect: true,
      // search: true,
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
        var response1 = serverGet(settings.apiUrl + "vat", {
          token: token.jwt
        });
        var response2 = serverGet(settings.apiUrl + "products", {
          token: token.jwt
        });
        var responseCategory = serverGet(settings.apiUrl + "category", {
          token: token.jwt
        });

        this.vatValues = response1.data;
        this.categories = responseCategory.data;
        this.products = response2.data;
        this.onFilterLenght = this.products.length;
      } catch (error) {}
    },
    onSortClick: function(s) {
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
    validURL: function(str) {
      var pattern = new RegExp(
        "^(https?:\\/\\/)?" + // protocol
        "((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|" + // domain name
        "((\\d{1,3}\\.){3}\\d{1,3}))" + // OR ip (v4) address
        "(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*" + // port and path
        "(\\?[;&a-z\\d%_.~+=-]*)?" + // query string
          "(\\#[-a-z\\d_]*)?$",
        "i"
      ); // fragment locator
      return !!pattern.test(str);
    },
    imagePreview: function(_image) {
      return this.validURL(_image)
        ? _image
        : "https://childrensmattressesonline.co.uk/i/others/empty-product-large.png?v=5c3fc1a0";
    },
    createProduct: function() {
      this.editProduct();
    },
    editProduct: function(_id = null) {
      var categoryOptions = "";
      var vatOptions = "";
      var stawki = escape(JSON.stringify(this.vatValues).toString());
      var unitSelectOptions = "";

      /* mode create */
      var defaultID = "";
      var defaultName = "";
      var defaultCategory = 1;
      var defaultNetto = "0.00";
      var defaultVat = 1;
      var defaultBrutto = "0.00";
      var defaultDetails = "";
      var defaultImage = "";
      var defaultUnit = units[0] || "szt";
      var defaultQuantity = 0;
      var defaultTitle = this.phrases.ADD_NEW_PRODUCT;

      /* mode edit */
      if (_id != null) {
        var filtered = this.products.filter(data => data.id == _id)[0];
        defaultTitle = this.phrases.EDIT_PRODUCT;
        defaultID = filtered.id;
        defaultName = filtered.nazwa;
        defaultCategory = filtered.id_kategori;
        defaultNetto = filtered.netto;
        defaultVat = filtered.id_stawki;
        defaultBrutto = filtered.brutto;
        defaultDetails = filtered.opis || "";
        defaultImage = filtered.zdjecie || "";
        defaultUnit = filtered.jm || "szt";
        defaultQuantity = filtered.quantity || 0;
      }

      /* static */

      this.vatValues.forEach(element => {
        if (element.id == defaultVat) {
          vatOptions += `<option value="${element.id}" selected="">${element.name}</option>`;
        } else {
          vatOptions += `<option value="${element.id}">${element.name}</option>`;
        }
      });

      this.categories.forEach(element => {
        if (element.id == defaultCategory) {
          categoryOptions += `<option value="${element.id}" selected="">${element.nazwa}</option>`;
        } else {
          categoryOptions += `<option value="${element.id}">${element.nazwa}</option>`;
        }
      });

      units[0].forEach(element => {
        if (element == defaultUnit) {
          unitSelectOptions += `<option value="${element}" selected="">${element}</option>`;
        } else {
          unitSelectOptions += `<option value="${element}">${element}</option>`;
        }
      });

      Swal.fire({
        title: defaultTitle,
        html: `
        <div class="form px-5 py-2 center-block mx-auto">
          <hidden id="_id" value="${defaultID}"/>
          <div class="form-group row mb-0">
            <label for="name" class="col-sm-4 col-form-label col-form-label-sm">${this.phrases.NAME}</label>
            <input class="form-control" id="name" type='text' value="${defaultName}" maxlength="20"></input>
          </div>
          
          <div class="form-group row mb-0">
            <label for="_scategory" class="col-sm-4 col-form-label col-form-label-sm">${this.phrases.CATEGORY}</label>
            <select class="form-control col-sm-3" id="_scategory" >
              ${categoryOptions}
            </select>
          </div>

          <div class="form-group row mb-0">
            <label for="priceN" class="col-sm-4 col-form-label col-form-label-sm">${this.phrases.PRICE} Netto</label>
            <input class="form-control" size="5" id="priceN" 
              value="${defaultNetto}" type='text' oninput="policzBrutto(this,'${stawki}')"></input>
            <label for="priceB" class="col-sm-2 col-form-label col-form-label-sm">zł</label>
            <select id="priceS" class="form-control mb-0" onchange="policzBrutto(this,'${stawki}')">
              ${vatOptions}
            </select>       
          </div>

          <div class="form-group row mb-0">
            <label for="priceB" class="col-sm-4 col-form-label col-form-label-sm">${this.phrases.PRICE} Brutto</label> 
            <input class="form-control" size="5"  id="priceB" type='text' value="${defaultBrutto}" readonly>
            <label for="priceB" class="col-sm-2 col-form-label col-form-label-sm">zł</label>
          </div>

          <div class="form-group row mb-0">
            <label for="details" class="col-sm-4 col-form-label col-form-label-sm">${this.phrases.DESCRIPTION}</label>
            <textarea class="form-control" id="details">${defaultDetails}</textarea>
          </div>
          
          <div class="form-group row ">
            <label for="zdjecie" class="col-sm-4 col-form-label col-form-label-sm">${this.phrases.PHOTO}</label>
            <input class="form-control" id="zdjecie" type='url' value="${defaultImage}"></input>
          </div>

          <div class="form-group row mb-0">
            <label for="quan" class="col-sm-4 col-form-label col-form-label-sm">${this.phrases.QUANTITY}</label>
            <input class="form-control" size="5" id="quan"
              value="${defaultQuantity}" type='text' ${_id == null ? "" : "readonly"}></input>
            <label for="unitList" class="col-sm-1 col-form-label col-form-label-sm"></label>
            <select id="unitList" class="form-control mb-0">${unitSelectOptions}</select>       
          </div>

        </div> `,
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: this.phrasesFilter.SAVE,
        cancelButtonText: this.phrasesFilter.CLOSE
      }).then(result => {
        if (result.value) {
          var name = document.getElementById("name").value;
          var category = document.getElementById("_scategory").value;
          var priceS = document.getElementById("priceS").value;
          var priceN = document.getElementById("priceN").value;
          var priceB = document.getElementById("priceB").value;
          var details = document.getElementById("details").value;
          var img = document.getElementById("zdjecie").value;
          var unitVal = document.getElementById("unitList").value;
          var quantityVal = document.getElementById("quan").value;

          priceN = priceN.replace(/ /g, ".").replace(",", ".");
          priceN = parseFloat(priceN);
          priceB = parseFloat(priceB);

          // if (validate([name, details, unitVal], [category, priceS, quantityVal], [priceN, priceB]) === false) {
          //   Swal.fire("", this.phrasesFilter.FILL_FIELDS, "warning");
          //   return false;
          // }

          var edited = serverPost(settings.apiUrl + "products", {
            token: token.jwt,
            id: defaultID,
            nazwa: name,
            id_stawki: priceS,
            id_kategori: category,
            netto: priceN.toFixed(2),
            brutto: priceB.toFixed(2),
            zdjecie: img,
            opis: details,
            quantity: _id != null ? filtered.quantity : quantityVal,
            unit: unitVal
          });

          if (edited.status == 200 || edited.status == 201) {
            Swal.fire("", edited.message, "success");

            this.products = [{}];
            this.products = edited.data;
            // this.onFiltered();
          } else {
            Swal.fire("", edited.message, "warning");
          }
        }
      });
    },
    deleteProduct: function(_id) {
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
          var deleted = serverDelete(settings.apiUrl + "products", {
            token: token.jwt,
            ids: _id
          });

          if (deleted.status == 200) {
            Swal.fire("", deleted.message, "success");

            this.redeemData();
          } else {
            Swal.fire("", deleted.message, "warning");
          }
        }
      });
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
    },
    addQuantity: function(_id) {
      //
      // quantityVal && isInt(quantityVal) &&

      Swal.fire({
        title: this.phrases.ADD_QUANTITY,
        html: `
          <div class="form-group row mb-0">
            <label for="qua" class="col-sm-4 col-form-label col-form-label-sm">${this.phrases.QUANTITY}</label>
            <input class="form-control" id="qua" type='text' value="0"></input>
          </div>`,
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: this.phrasesFilter.SAVE,
        cancelButtonText: this.phrasesFilter.CLOSE
      }).then(result => {
        if (result.value) {
          var addQuantityVal = document.getElementById("qua").value;
          addQuantityVal = parseInt(addQuantityVal);

          if (Number.isInteger(addQuantityVal) == false || addQuantityVal <= 0) {
            Swal.fire("", "Wprowadź dane poprawnie", "warning");
            return null;
          }

          var postRes = serverPost(settings.apiUrl + "mag", {
            token: token.jwt,
            pID: _id,
            quantity: addQuantityVal
          });

          if (postRes.status == 201 || postRes.status == 200) {
            Swal.fire("", postRes.message, "success");

            this.redeemData();
          } else {
            Swal.fire("", postRes.message, "warning");
          }
        }
      });
    }
  },
  components: {
    optionBar: optionBar
  },
  computed: {
    productsSorted: function() {
      let filtered = this.onFilterChange(this.objFilter);

      return sortData(filtered, this.currentSort, this.currentSortDir, this.currentPage, this.selected);
    }
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
      :onAddNew=createProduct 
    /> 

    <table id="products" class="table table-striped table-hover table-sm ">
      <thead class="table-primary">
        <th class="text-center">{{phrases.LP}}</th>
        <th @click="onSortClick('nazwa')" class="text-center poiter">{{phrases.NAME}}</th>
        <th @click="onSortClick('id_kategori')" class="text-center poiter">{{phrases.CATEGORY}}</th>
        <th @click="onSortClick('quantity')" class="text-center poiter">{{phrases.QUANTITY}}</th>
        <th @click="onSortClick('price')" class="text-center poiter">{{phrases.PRICE}}</th>
        <th class="text-center">{{phrases.PHOTO}}</th>
        <th class="text-center">{{phrases.OPTIONS}}</th>
      </thead>
      <tbody class="table-light" v-if="onFilterLenght>0">
        <tr v-for="(product, i) in productsSorted">
          <td class="text-center">{{ ((-1 + currentPage) * selected) +( i+1)}}</td>
          <td class="text-center">{{product.nazwa}}</td>
          <td class="text-center">{{product.nazwa_kategori}}</td>
          <td class="text-center">{{product.quantity}}</td>
          <td class="text-center">{{product.price}} zł</td>
          <td>
            <a class="thumbnail" href="#">
              <p>{{phrasesFilter.IMAGE_PREVIEW}}</p>
              <span>
                <img class="small-img" :src=imagePreview(product.zdjecie) />              
              </span>
            </a>          
          </td>
          <td>
          <button class="btn btn-sm btn-success" v-on:click="editProduct(product.id)">{{phrasesFilter.EDIT}}</button>
          <button class="btn btn-sm btn-danger" v-on:click="deleteProduct(product.id)">{{phrasesFilter.DELETE}}</button>
          <button class="btn btn-sm btn-primary" v-on:click="addQuantity(product.id)">+</button>
          </td>
        </tr>
      </tbody>
      <tfoot v-else>
        <tr class="text-center" >
          <td :colspan="6">{{phrases.NO_DATA}}</td>
        </tr>
      </tfoot>
    </table>
     
    <optionBar
      :dataSize=onFilterLenght
      :pagi=onPagitationClick 
      :currentPage=currentPage  
      :selectRows=selected 
      :optionBarSettings=optionBarSettingsBottom
      :onAddNew=createProduct 
    />   

  </div>`
});
