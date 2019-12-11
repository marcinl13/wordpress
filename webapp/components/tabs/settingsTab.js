export default Vue.component("tab-settings", {
  data: function() {
    return {
      conSettings: testSettings[0],
      cCode: testSettings[0].currencyCode,
      languageSettings: langSettings[0]["SETTINGS_TAB"],
      acceptedLangs: ["PL", "EN"]
    };
  },
  template: `<div>
    <h3>{{ languageSettings.TITLE }}</h3>

    <div class="form-group">
      <label for="cLang" class=" col-sm-2">{{ languageSettings.LANGUAGE }}:</label>

      <select id="cLang" name="cLang" v-model="conSettings.lang">
        <option v-for="lang in acceptedLangs">{{ lang }}</option>
      </select>
      
    </div>

    <div class="form-group">
      <label for="cCur" class=" col-sm-2">{{ languageSettings.CURRENCY }}:</label>
      <input class="form-control" type="text" id="cCur" name="cCur" :value=cCode />
    </div>

    <div class="form-group">
      <label for="dCan" class=" col-sm-2">{{ languageSettings.TIME_CANCEL }}:</label>

      <select id="dCan" name="dCan" v-model="conSettings.dayCancel">
        <option v-for="i in 7" :value=i>{{ i }}</option>
      </select>

      <span>{{languageSettings.DAYS }}</span>
    </div>

    <div class="form-group">
      <label for="tPri" class=" col-sm-2">{{ languageSettings.TRANSPORT_PRICE }}:</label>
      <input class="form-control" type="text" id="tPri" name="tPri" :value=conSettings.transportPrice />
      <span>{{cCode}}</span>
    </div>

    <hr/>

    <div class="form-group">
      <label for="csf" class=" col-sm-2">{{languageSettings.DOC_SAVE_FORMAT}}</label>
      <input class="form-control" type="text" id="csf" name="csf" :value=conSettings.invoiceFormat size="30" />
    </div>

    <div class="form-group">
      <label for="hpr" class=" col-sm-2">{{languageSettings.API_KEY}} 
        <a href="https://www.html2pdfrocket.com">html2pdfrocket</a></label>
      <input class="form-control" type="text" id="hpr" name="hpr" :value=conSettings.html2pdfrocket size="30" />
    </div>

  </div>`
});
