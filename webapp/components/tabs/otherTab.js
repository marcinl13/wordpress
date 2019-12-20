export default Vue.component("tab-other", {
  data: function() {
    return {
      conSettings: testSettings[0]["SHOP"],
      languageSettings: langSettings[0]["OTHER_TAB"]
    };
  },
  methods: {},
  template: `<div>
    <h3>{{ languageSettings.TITLE }}</h3>

    <hr/>
    <h3>{{ languageSettings.SHOP_DATA }}</h3>
    
    <div class="form-group">
      <label for="sName" class=" col-sm-2">{{ languageSettings.SHOP_NAME }}:</label>
      <input class="form-control" type="text" id="sName" name="sName" :value=conSettings.name size="30" />
    </div>
    
    <div class="form-group">
      <label for="sAdress" class=" col-sm-2">{{ languageSettings.SHOP_ADRESS }}:</label>
      <input class="form-control" type="text" id="sAdress" name="sAdress" :value=conSettings.adress size="30" />
    </div>
    
    <div class="form-group">
      <label for="sEmail" class=" col-sm-2">{{ languageSettings.SHOP_EMAIL }}:</label>
      <input class="form-control" type="email" id="sEmail" name="sEmail" :value=conSettings.email size="30" />
    </div>
    
    <div class="form-group">
      <label for="sLogo" class=" col-sm-2">{{ languageSettings.SHOP_LOGO }}:</label>
      <input class="form-control" type="url" id="sLogo" name="sLogo" :value=conSettings.logoLink size="30" />
    </div>

    

  </div>`
});
