export default Vue.component("component-dump", {
  data: function() {
    return {
      tableID: "",
      productsList: [{}],
      phrases: langSettings[0],
      phrasesFilter: langSettingsFilter[0],
      sumQuan: 0,
      sumPri: parseFloat(0).toFixed(2),
      sizeList: 1,
      hasElements: false
    };
  },
  created: function() {
    this.tableID = uniqID();

    try {
      var parsed = JSON.parse(localStorage.getItem(LSI));

      var response = serverGet(settings.apiUrl + "cart", {
        ids: parsed.products.join(","),
        token: token.jwt
      });

      if (response.status == 200) {
        this.productsList = response.data;
        this.hasElements = response.data[0].hasOwnProperty("price");
      }
    } catch (error) {}
  },
  methods: {
    increase: function(_target) {
      try {
        var val = parseInt(_target.parentNode.parentNode.cells[3].innerText);

        var rowIndex = _target.offsetParent.parentNode.rowIndex;

        this.productsList[rowIndex].quantity = parseInt(this.productsList[rowIndex].quantity + 1);

        _target.parentNode.parentNode.cells[3].innerText = val + 1;

        this.calc();
      } catch (error) {}
    },
    decrease: function(_target) {
      try {
        var val = parseInt(_target.parentNode.parentNode.cells[3].innerText);
        val = val - 1;

        var rowIndex = _target.offsetParent.parentNode.rowIndex;

        this.productsList[rowIndex].quantity = parseInt(this.productsList[rowIndex].quantity - 1);

        _target.parentNode.parentNode.cells[3].innerText = val;

        this.calc();

        if (val <= 0) _target.parentNode.parentNode.remove();
      } catch (error) {}
    },
    sumQuantity: function() {
      try {
        var quantity = 0;

        this.productsList.forEach(element => {
          quantity += parseInt(element.quantity);
        });

        if (transportPrice) {
          quantity += 1;
        }

        return quantity;
      } catch (error) {}
    },
    sumPrice: function() {
      try {
        var priceSum = 0.0;

        this.productsList.forEach(element => {
          priceSum += parseFloat(element.price) * parseInt(element.quantity);
        });

        if (transportPrice) {
          priceSum += parseFloat(transportPrice[0]);
        }

        return priceSum.toFixed(2);
      } catch (error) {}
    },
    calc: function() {
      if (this.hasElements == false) return;

      this.sumQuan = this.sumQuantity();
      this.sumPri = this.sumPrice();
    },
    makeOrder: function() {
      var tmpOrder = [];

      if (this.productsList.length == 0) return false;

      this.productsList.forEach(element => {
        tmpOrder.push({
          id: element.id,
          name: element.nazwa,
          jm: element.jm,
          discount: "0.00",
          quantity: parseInt(element.quantity),
          price: parseFloat(element.price).toFixed(2),
          netto: element.netto
        });
      });

      $.post(settings.apiUrl + "cart", {
        sumPrice: this.sumPri,
        token: token.jwt,
        order: tmpOrder
      })
        .then(response => {
          if (response.status == 201 && response.redirect != "") {
            Swal.fire(response.message, "", "success");
            localStorage.removeItem(LSI);
            // window.location.href = settings.homeUrl + "/sklep/";

            window.location.href = response.redirect;
          } else {
            Swal.fire("", response.message, "warning");
          }
        })
        .catch(error => {
          Swal.fire(error.responseJSON.message, "", "error");
          // window.location.href = settings.homeUrl + "/sklep/";
        });
    }
  },
  template: `
  <div>
    <div class="d-block w-100 px-3 mb-3">
      <table :id=tableID class="table table-striped table-sm table-hover">
        <thead class="table-primary">
          <th class="text-center">{{phrases.LP}}</th>
          <th class="text-center">{{phrases.NAME}}</th>
          <th class="text-center">{{phrases.PRICE}}</th>
          <th class="text-center">{{phrases.QUANTITY}}</th>
          <th class="text-center">+/-</th>
        </thead>
        <tbody class="table-light" v-if="hasElements">
          <tr v-for="(product, i) in productsList">
            <td class="text-center align-middle">{{ ( i+1) }}</td>
            <td class="text-center align-middle">{{product.nazwa}}</td>
            <td class="text-center align-middle">{{product.price.replace(".",",")}} ${currencyCode[0]}</td>  
            <td class="text-center align-middle">{{product.quantity}}</td>   
            <td class="text-center align-middle">
              <button class="btn btn-small btn-success" v-on:click="increase($event.target)">+</button>
              <button class="btn btn-small btn-danger"  v-on:click="decrease($event.target)">-</button>
            </td>   
          </tr>
        </tbody>

        <tbody class="table-light" v-else>
          <tr>
            <td class="text-center align-middle" :colspan="5">{{phrases.NO_DATA}}</td>         
          </tr>
        </tbody>

        <tfoot>
          <tr>
            <td class="text-center "></td>
            <td class="text-center ">{{phrases.TRANSPORT_PRICE}}</td>
            <td class="text-center ">${transportPrice[0].toFixed(2)} ${currencyCode[0]}</td>
            <td class="text-center ">1</td>
          </tr>
          <tr>
            <td class="text-center font-weight-bold">{{calc()}} </td>
            <td class="text-center font-weight-bold">{{phrases.SUM}}</td>
            <td class="text-center font-weight-bold">{{sumPri}} ${currencyCode[0]}</td>
            <td class="text-center font-weight-bold">{{sumQuan}}</td>
          </tr>
        </tfoot>
        
      </table>
    </div>

    <div class="d-flex justify-content-center">
      <button class="text-center btn-payU" @click="makeOrder()" > </button>
    </div>

  </div>`
});
