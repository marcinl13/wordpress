export default Vue.component("component-productsTable", {
  props: ["productsData", "sortFunction", "AddNewOrder", "selectRows", "currentPage", "dataSize"],
  data: function() {
    return { languageSettings: langSettings[0], tableID: uniqID() };
  },
  methods: {
    sorting: function(_s) {
      this.sortFunction(_s);
    },
    dodajDoKoszyka: function(_id) {
      this.AddNewOrder(_id);
    },
    imagePreview: function(_image) {
      return previewImage(_image);
    }
  },
  template: `
  <table :id=tableID class="table table-striped table-sm table-hover ">
    <thead class="table-primary">
      <th class="text-center">{{languageSettings.LP}}</th>
      <th class="text-center">{{languageSettings.PHOTO}}</th>
      <th class="text-center poiter" v-on:click="sorting('name')">{{languageSettings.NAME}}</th>
      <th class="text-center poiter" v-on:click="sorting('id_kategori')">{{languageSettings.CATEGORY}}</th>
      <th class="text-center poiter" v-on:click="sorting('price')">{{languageSettings.PRICE}}</th>
      <th class="text-center"></th>
    </thead>
    <tbody class="table-light" v-if="dataSize>0">
      <tr v-for="(product, i) in productsData">
        <td class="text-center align-middle">{{ ((-1 + currentPage) * selectRows) +( i+1)}}</td>
        <td><img class="small-img" :src=imagePreview(product.image) ></td>
        <td class="text-center align-middle" >{{product.name}}</td>
        <td class="text-center align-middle" >{{product.nazwa_kategori}}</td>
        <td class="text-center align-middle" >{{product.price}} zł</td>  
        <td class="text-center align-middle" >
          <button class="cart-buy" v-on:click="dodajDoKoszyka(product.id)">
          <svg width="64" height="64" viewBox="0 0 172 172"><path d="M12.09375,14.78125c-2.28438,0 -4.03125,1.74687 -4.03125,4.03125c0,2.28438 1.74687,4.03125 4.03125,4.03125h9.40625v99.4375c0,7.39063 6.04688,13.4375 13.4375,13.4375h67.1875c2.28437,0 4.03125,-1.74687 4.03125,-4.03125c0,-2.28438 -1.74688,-4.03125 -4.03125,-4.03125h-67.1875c-2.95625,0 -5.375,-2.41875 -5.375,-5.375v-86h117.31042c1.74687,0 3.35833,0.8052 4.29895,2.28333c1.075,1.47812 1.34375,3.2271 0.67188,4.8396l-18.40833,55.36145c-2.01562,6.04688 -7.52552,10.07813 -13.97552,10.07813h-75.11615c-2.28437,0 -4.03125,1.74688 -4.03125,4.03125c0,2.28437 1.74688,4.03125 4.03125,4.03125h75.11615c9.80937,0 18.54322,-6.3151 21.63385,-15.58697l18.40833,-55.36407c1.34375,-4.16562 0.67397,-8.6 -1.87915,-12.09375c-2.55312,-3.49375 -6.58647,-5.6427 -10.88647,-5.6427h-117.17395v-9.40625c0,-2.28438 -1.74687,-4.03125 -4.03125,-4.03125zM34.9375,143.78125c-7.39062,0 -13.4375,6.04688 -13.4375,13.4375c0,7.39063 6.04688,13.4375 13.4375,13.4375c7.39063,0 13.4375,-6.04687 13.4375,-13.4375c0,-7.39062 -6.04687,-13.4375 -13.4375,-13.4375zM102.125,143.78125c-7.39062,0 -13.4375,6.04688 -13.4375,13.4375c0,7.39063 6.04688,13.4375 13.4375,13.4375c7.39063,0 13.4375,-6.04687 13.4375,-13.4375c0,-7.39062 -6.04687,-13.4375 -13.4375,-13.4375zM34.9375,151.84375c2.95625,0 5.375,2.41875 5.375,5.375c0,2.95625 -2.41875,5.375 -5.375,5.375c-2.95625,0 -5.375,-2.41875 -5.375,-5.375c0,-2.95625 2.41875,-5.375 5.375,-5.375zM102.125,151.84375c2.95625,0 5.375,2.41875 5.375,5.375c0,2.95625 -2.41875,5.375 -5.375,5.375c-2.95625,0 -5.375,-2.41875 -5.375,-5.375c0,-2.95625 2.41875,-5.375 5.375,-5.375z"></path></svg> 
          </button>
        </td>   
      </tr>
    </tbody>
    <tbody v-else>
        <tr>
          <td class="text-center" :colspan=controlTableSize(tableID)>{{languageSettings.NO_DATA}}</td>
        </tr>
      </tbody>
  </table>`
});
