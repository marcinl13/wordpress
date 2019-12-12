//part version 1.1
function geter(_url, _data, _headers, _dataType = "json", _type = "GET") {
  var result = null;
  $.ajax({
    url: _url,
    type: _type,
    data: _data,
    headers: _headers,
    dataType: _dataType,
    async: false,
    success: function(data) {
      result = data;
    }
  });
  return result;
}

function serverDelete(_url, _data) {
  return geter(_url, _data, "", "json", "delete");
}

function serverPost(_url, _data) {
  return geter(_url, _data, "", "json", "post");
}

function serverGet(_url, _data) {
  return geter(_url, _data);
}

function policzBrutto(_target, _stawki = null) {
  var html = _target.offsetParent.childNodes[1].childNodes[0].childNodes[1].childNodes[1].children;
  var stawka = 0.0;
  var cenaBrutto = 0.0;
  var priceS = html[2].children[3].value;
  var priceN = html[2].children[1].value;

  JSON.parse(unescape(_stawki)).forEach(element => {
    if (priceS == element.id) stawka = parseFloat(element.stawka);
  });

  var netto = priceN.replace(/ /g, ".").replace(",", ".");

  if (parseFloat(netto) > parseFloat(0)) {
    cenaBrutto = (parseFloat(netto) * (1 + stawka)).toFixed(2);
  } else {
    cenaBrutto = "0.00";
  }

  html[3].children[1].value = "";
  html[3].children[1].value += cenaBrutto;
}

// function isNumber(_string) {
//   _string = parseFloat(_string);
//   return typeof _string == "number" && !isNaN(_string) ? true : false;
// }

function geter2(_url, _data, _headers) {
  var result = null;
  $.ajax({
    url: _url,
    type: "GET",
    // data: _data,
    body: JSON.stringify(_data),
    headers: _headers,
    dataType: "json",
    async: false,
    success: function(data) {
      result = data;
    }
  });
  return result;
}

function ordersCounter() {
  try {
    if (localStorage.getItem("koszyk") == null) {
      var tmp = {
        _token: null,
        products: []
      };
      localStorage.setItem("koszyk", JSON.stringify(tmp));
    } else {
      document.getElementById("ordersCount").innerText = JSON.parse(localStorage.getItem("koszyk")).products.length;
    }
  } catch (error) {}
}

/**================================= validation ================================= */

let isInt = _number => {
  if (!/^["|']{0,1}[-]{0,1}\d{0,}(\.{0,1}\d+)["|']{0,1}$/.test(_number)) return false;
  return !(_number - parseInt(_number));
};

let isFloat = _number => {
  if (!/^["|']{0,1}[-]{0,1}\d{0,}(\.{0,1}\d+)["|']{0,1}$/.test(_number)) return false;
  return _number - parseInt(_number) ? true : false;
};

let sumArray = _array => {
  return _array.reduce(function(pv, cv) {
    return pv + cv;
  }, 0);
};

let validate = (_strings = [], _ints = [], _floats = []) => {
  let validated = true;

  if (_strings.length > 0) {
    validated = validated && typeof sumArray(_strings) == "string" && sumArray(_strings).length > 1;
  }
  if (_ints.length > 0) {
    validated = validated && isInt(sumArray(_ints));
  }
  if (_floats.length > 0) {
    validated = validated && isFloat(sumArray(_floats)) && sumArray(_floats) > 0.0;
  }

  return validated;
};

/**================================= chart ================================= */

let genereteRGB = () => {
  const cr = Math.floor(Math.random() * 256);
  const cg = Math.floor(Math.random() * 256);
  const cb = Math.floor(Math.random() * 256);

  //fix colors
  cr > 230 ? cr - 20 : cr;
  cg > 230 ? cg - 20 : cg;
  cb > 230 ? cb - 20 : cb;

  return { cr, cg, cb };
};

function emptyChart(_chartId, _chartType = "bar") {
  const { cr, cg, cb } = genereteRGB();

  const ctx = document.getElementById(_chartId).getContext("2d");

  var chartTmp = new Chart(ctx, {
    type: _chartType,
    data: {
      labels: [],
      datasets: [
        {
          label: "mies",
          data: [],
          fill: true,
          backgroundColor: "rgba(" + cr + ", " + cg + ", " + cb + ", 0.2)",
          borderColor: "rgba(" + cr + ", " + cg + ", " + cb + ", 1)",
          borderWidth: 1
        }
      ]
    },
    options: {
      responsive: true,
      title: {
        display: true,
        text: "TEXT_CHART3"
      },
      scales: {
        yAxes: [
          {
            ticks: {
              beginAtZero: true,
              max: 10,
              callback: function(val, i, vals) {
                return val;
              }
            }
          }
        ]
      }
    }
  });

  return chartTmp;
}

let createMonthSelect = (_chartId, _selectName, _lang = null) => {
  _lang = _lang == null ? settings.curLang || "en" : _lang;

  var selectList = document.createElement("select");
  selectList.id = _selectName;
  selectList.className = "form-control input-sm d-block mx-auto my-2";

  const monthList = getDateMonthList();

  for (let i = 0; i < 12; i++) {
    var monthName = monthList[i];

    var option = document.createElement("option");
    option.value = i + 1;
    option.text = monthName.charAt(0).toUpperCase() + monthName.slice(1); // first letter upperCase
    selectList.appendChild(option);
  }

  document.getElementById(_chartId).before(selectList);
};

let dateStandard = (_date, _lang = null) => {
  _lang = _lang == null ? settings.curLang || "en" : _lang;

  return Date.parse(_date)
    ? new Date(_date).toLocaleDateString(_lang, { year: "numeric", month: "long", day: "numeric" })
    : "------";
};

let getDateMonthList = (_lang = null) => {
  _lang = _lang == null ? settings.curLang || "en" : _lang;

  const months = [];

  for (let i = 0; i < 12; i++) {
    const date = new Date(new Date().getFullYear(), i, 1);
    const monthName = date.toLocaleString(_lang, {
      month: "long"
    });

    months.push(monthName.charAt(0).toUpperCase() + monthName.slice(1));
  }

  return months;
};

/**================================= views ================================= */

let sortData = (_dataFiltered, _currentSort, _currentSortDir, _currentPage, _selected) => {
  return _dataFiltered
    .sort((a, b) => {
      let modifier = 1;
      if (_currentSortDir === "desc") modifier = -1;
      if (_currentSort == "id") {
        if (parseInt(a[_currentSort]) < parseInt(b[_currentSort])) return -1 * modifier;
        if (parseInt(a[_currentSort]) > parseInt(b[_currentSort])) return 1 * modifier;
      } else {
        if (a[_currentSort] < b[_currentSort]) return -1 * modifier;
        if (a[_currentSort] > b[_currentSort]) return 1 * modifier;
      }

      return 0;
    })
    .filter((row, index) => {
      let start = (_currentPage - 1) * _selected;
      let end = _currentPage * _selected;
      if (index >= start && index < end) return true;
    });
};

/**
 *
 * @param {*} _data
 * @param {*} _obj
 * @param {*} _selected
 * @param {FILTER_CAT : "field_name", FILTER_STATUS : "field_name", FILTER_STW : "field_name", FILTER_DSTART : "field_name", FILTER_DEND : "field_name"} _fields
 */
var filterData = (_data, _obj, _selected, _fields = {}) => {
  let filtered = _data;

  if (_obj != undefined && _obj.type == FILTER_ROWPAGE) {
    _selected = parseInt(_obj.val);
  }
  if (_obj != undefined && _obj.type == FILTER_STW && _obj.val != "") {
    var filterByName = _obj.val.toLowerCase();

    filtered = filtered.filter(function(data) {
      return data[_fields.FILTER_STW].toLowerCase().indexOf(filterByName) == 0;
    });
  }
  if (_obj != undefined && _obj.type == FILTER_CAT && _obj.val != 0) {
    filtered = filtered.filter(function(data) {
      return parseInt(data[_fields.FILTER_CAT]) === parseInt(_obj.val);
    });
  }
  if (_obj != undefined && _obj.type == FILTER_STATUS && _obj.val != 0) {
    filtered = filtered.filter(function(data) {
      return parseInt(data[_fields.FILTER_STATUS]) === parseInt(_obj.val);
    });
  }
  if (_obj != undefined && _obj.type == FILTER_DSTART && _obj.val != 0) {
    filtered = filtered.filter(function(data) {
      var dataBegin = new Date(data[_fields.FILTER_DSTART]).getMonth() + 1;

      return dataBegin >= parseInt(_obj.val);
    });
  }
  if (_obj != undefined && _obj.type == FILTER_DEND && _obj.val != 0) {
    filtered = filtered.filter(function(data) {
      var dataEnd = new Date(data[_fields.FILTER_DEND]).getMonth() + 1;

      return dataEnd <= parseInt(_obj.val);
    });
  }

  return { filtered, _selected };
};

/**================================= end ================================= */

let FILTER_CAT = "searchCategory";
let FILTER_STATUS = "searchStatus";
let FILTER_STW = "searchStartWith";
let FILTER_ROWPAGE = "rowPerPage";
let FILTER_DSTART = "searchDateBegin";
let FILTER_DEND = "searchDateEnd";

window.onload = () => {
  ordersCounter();
};

/*<Files ~ "^piwik\.(js|php)|robots\.txt$">
    Allow from all
    Satisfy any
</Files> */
