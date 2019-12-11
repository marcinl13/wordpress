export default Vue.component("component-orders-details", {
  props: ["products"],
  template: `
    <table class='table table-striped table-hover table-sm'>
      <thead class='table-primary'>
        <th class='text-center'>ID</th>             
        <th class='text-center'>Nazwa</th>
        <th class='text-center'>Netto</th>
        <th class='text-center'>Cena brutto</th>            
        <th class='text-center'>Jed</th>            
        <th class='text-center'>ilosc</th>            
       </thead>            
       <tbody class='table-light' v-for='order in JSON.parse(atob(products))'>               
        <tr>
          <td class='text-center'>{{order.id}}</td> 
          <td class='text-center'>{{order.name}}</td> 
          <td class='text-center'>{{order.netto}}</td> 
          <td class='text-center'>{{order.price}} zł</td>
          <td class='text-center'>szt</td>
          <td class='text-center'>{{order.quantity}}</td>
        </tr>              
    </table>`
});
