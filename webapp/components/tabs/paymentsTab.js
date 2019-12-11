export default Vue.component("tab-payments", {
  data: function() {
    return {
      mode: testSettings[0].paymentMode,
      gate1: testSettings[0],
      cCode: testSettings[0].currencyCode,
      languageSettings: langSettings[0]["PAYMENTS_TAB"]
    };
  },
  template: `<div>
  
  <h3>{{ languageSettings.TITLE }}</h3>
  
  <div class="form-group">
    <label for="mID" class=" col-sm-2">{{languageSettings.MODE}}:</label>
    <div class="custom-control custom-radio custom-control-inline">
      <input type="radio" id="mode1" name="mode" class="custom-control-input" value="sandbox" v-model="mode" />
      <label class="custom-control-label col-sm-2" for="mode1">{{ languageSettings.SANDBOX }}</label>
    </div>
    <div class="custom-control custom-radio custom-control-inline">
      <input type="radio" id="mode2" name="mode" class="custom-control-input" value="production" v-model="mode" />
      <label class="custom-control-label col-sm-2" for="mode2">{{ languageSettings.PRODUCTION }}</label>
    </div>
  </div>

  <div class="form-group">
    <label for="mID" class=" col-sm-2">{{ languageSettings.MERCH_ID }}:</label>
    <input class="form-control" type="text" id="mID" name="mID" :value=gate1.merchantPosId  />
  </div>

  <div class="form-group">
    <label for="mSec" class=" col-sm-2">{{ languageSettings.MERCH_SEC }}:</label>
    <input class="form-control" type="text" id="mSec" name="mSec" :value=gate1.merchantSecond />
  </div>

  <div class="form-group">
    <label for="cID" class=" col-sm-2">{{ languageSettings.CLI_TID }}:</label>
    <input class="form-control" type="text" id="cID" name="cID" :value=gate1.clientID  />
  </div>

  <div class="form-group">
    <label for="cSec" class=" col-sm-2">{{ languageSettings.CLI_SEC }}:</label>
    <input class="form-control" type="text" id="cSec" name="cSec" :value=gate1.clientSecret />
  </div> 

</div>`
});
