import pagitation from "./pagitation.js";
import newButton from "./newButton.js";
import search from "./search.js";
import categorySelect from "./categorySelect.js";
import selectByPage from "./selectByPage.js";
import searchWithCategory from "./searchWithCategory.js";
import statusSelect from "./statusSelect.js";
import selectBeginData from "./selectBeginData.js";

export default Vue.component("conponent-optionBar", {
  props: [
    "pagi",
    "category",
    "currentPage",
    "dataSize",
    "selectRows",
    "optionBarSettings",
    "onAddNew",
    "filterProps"
  ],
  data: function() {
    return {};
  },
  methods: {
    optionPagi: function(_typ) {
      this.pagi(_typ);
    },
    optionOnAddNew: function() {
      this.onAddNew();
    },
    filterSend: function(_obj) {
      this.filterProps(_obj);
    }
  },
  components: {
    pagitation: pagitation,
    newButton: newButton,
    search: search,
    categorySelect: categorySelect,
    selectByPage: selectByPage,
    searchWithCategory: searchWithCategory,
    statusSelect: statusSelect,
    selectBeginData: selectBeginData
  },
  template: `
  <div>
    <div 
      class="d-flex align-items-center w-100" 
      :class="{
        'justify-content-between': Object.keys(optionBarSettings).length>2,
        'justify-content-around': Object.keys(optionBarSettings).length==2,
        'justify-content-center': Object.keys(optionBarSettings).length==1
      }" > 

      <categorySelect 
        v-if=optionBarSettings.categorySelect 
        :category=category 
        :filterSend=filterSend 
      />

      <selectByPage 
        v-if=optionBarSettings.selectByPage 
        :filterSend=filterSend
        :pagi=optionPagi
      />
      
      <selectBeginData 
        v-if=optionBarSettings.selectBeginData
        :filterSend=filterSend
      />

      
       
      <search  
        v-if=optionBarSettings.search 
        :filterSend=filterSend 
      />  
      
      <statusSelect  
        v-if=optionBarSettings.statusSelect 
        :filterSend=filterSend 
      />  

      <searchWithCategory 
        v-if=optionBarSettings.searchWithCategory 
        :category=category 
        :filterSend=filterSend 
      />

      <newButton 
        v-if=optionBarSettings.newButton 
        :optionOnAddNew=onAddNew
      />
     
    </div>
    
    <div class="d-flex align-items-center w-100 justify-content-center">
      <pagitation 
        v-if=optionBarSettings.pagitation 
        :pagi=optionPagi 
        :currentPage=currentPage 
        :dataSize=dataSize 
        :selectRows=selectRows         
      />
    </div>

  </div>
  `
});
