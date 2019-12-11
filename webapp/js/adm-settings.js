import settingsTab from "../components/tabs/settingsTab.js";
import otherTab from "../components/tabs/otherTab.js";
import paymentsTab from "../components/tabs/paymentsTab.js";

new Vue({
  el: "#adm-settings",
  data: {
    tabs: ["Settings", "Payments", "Other"],
    currentTab: "Settings",
    langSettingsFilter: langSettingsFilter[0]
  },
  methods: {
    replaceTabs: function(_tabName) {
      var args = {
        Settings: langSettings[0].SETTINGS,
        Payments: langSettings[0].PAYMENTS,
        Other: langSettings[0].OTHER
      };

      return args.hasOwnProperty(_tabName) ? args[_tabName] : _tabName;
    },
    refreshPage: function(_tabName) {
      
      if (_tabName == "Settings") {
        location.reload(true);
      }
    }
  },
  component: {
    "tab-settings": settingsTab,
    "tab-payments": otherTab,
    "tab-other": paymentsTab
  },
  computed: {
    currentTabComponent: function() {
      return "tab-" + this.currentTab.toLowerCase();
    }
  },
  template: `
  <div class="d-block w-100 px-3 mt-3">
    <button
      v-for="tab in tabs"
      v-bind:key="tab"
      v-bind:class="['tab-button', { active: currentTab === tab }]"
      v-on:click="currentTab = tab"
    >{{ replaceTabs(tab) }}</button>

    <form method="post" :action="['?page=settings&action=save' + currentTab]">
      <component
        v-bind:is="currentTabComponent"
        class="tab"
      ></component>

      <button type="submit" class="d-block my-2 mx-auto btn btn-success" v-on:click="refreshPage(currentTab)">{{langSettingsFilter.SAVE}}</button>
     </form> 
  </div>`
});
