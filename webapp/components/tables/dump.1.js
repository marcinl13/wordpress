export default Vue.component("component-dump", {
  data: function() {
    return {
      tableID: "",
      productsList: [{}],
      orderTmp: {},
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

      this.orderTmp = parsed.products;

      var response = serverGet(settings.apiUrl + "cart", {
        ids: parsed.products.join(","),
        token: token.jwt
      });

      if (response.status == 200) {
        this.productsList = response.data;
        this.hasElements = response.data[0].hasOwnProperty("price");

        this.rebuild();
      }
    } catch (error) {}
  },
  methods: {
    rebuild: function() {
      let arr = this.orderTmp;
      let copyProductList = [...this.productsList];

      let uniqs = arr.reduce((acc, val) => {
        acc[val] = acc[val] === undefined ? 1 : (acc[val] += 1);
        return acc;
      }, {});

      copyProductList.forEach(element => {
        let id = element.id;

        element["amount"] = uniqs[id];
      });

      this.orderTmp = copyProductList;
    },
    changeCounter: function() {
      let arr = [];
      let koszykData = JSON.parse(localStorage.getItem(LSI));

      this.orderTmp.forEach(element => {
        for (let i = 0; i < element.amount; i++) {
          arr.push(element.id);
        }
      });

      koszykData.products = arr;
      localStorage.setItem(LSI, JSON.stringify(koszykData));

      document.getElementById("ordersCount").innerText = arr.length < 0 ? 0 : arr.length;
    },
    increase: function(_target) {
      try {
        var rowIndex = _target.offsetParent.parentNode.rowIndex;

        this.productsList[rowIndex].quantity = parseInt(this.productsList[rowIndex].quantity + 1);

        this.orderTmp[rowIndex].amount += 1;

        this.changeCounter();
        this.calc();
      } catch (error) {}
    },
    decrease: function(_target) {
      try {
        var val = parseInt(_target.parentNode.parentNode.cells[3].innerText);
        val = val - 1;

        var rowIndex = _target.offsetParent.parentNode.rowIndex;

        this.productsList[rowIndex].quantity = parseInt(this.productsList[rowIndex].quantity - 1);

        this.orderTmp[rowIndex].amount -= 1;

        this.changeCounter();
        this.calc();

        if (val <= 0) _target.parentNode.parentNode.style.display = "none";
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
      let tmpOrder = [];

      if (this.productsList.length == 0) return false;

      this.productsList.forEach(element => {
        tmpOrder.push({
          id: element.id,
          name: element.name,
          jm: element.unit,
          discount: "0.00",
          quantity: parseInt(element.quantity),
          price: parseFloat(element.price).toFixed(2),
          netto: element.netto
        });
      });

      $.post(settings.apiUrl + "cart", {
        sumPrice: parseFloat(this.sumPri) - parseFloat(transportPrice ? transportPrice[0] : 0),
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
    },
    reOrdersCount: function() {
      return;
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
          <tr v-for="(product, i) in orderTmp">
            <td class="text-center align-middle">{{ ( i+1) }}</td>
            <td class="text-center align-middle">{{product.name}}</td>
            <td class="text-center align-middle">{{product.price.replace(".",",")}} ${currencyCode[0]}</td>  
            <td class="text-center align-middle">{{product.amount}}</td>   
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
          <tr style="font-weight: bolder;">
            <td class="text-center "></td>
            <td class="text-center ">{{phrases.TRANSPORT_PRICE}}</td>
            <td class="text-center ">${transportPrice[0].toFixed(2)} ${currencyCode[0]}</td>
            <td class="text-center ">1</td>
          </tr>
          <tr style="font-weight: bold;">
            <td class="text-center ">{{calc()}} </td>
            <td class="text-center ">{{phrases.SUM}}</td>
            <td class="text-center ">{{sumPri}} ${currencyCode[0]}</td>
            <td class="text-center ">{{sumQuan}}</td>
          </tr>
        </tfoot>
        
      </table>
    </div>

    <div class="d-flex justify-content-center">
      <button class="text-center btn-payU" @click="makeOrder()" > </button>
    </div>

  </div>`
});
