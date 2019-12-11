let updateChart04 = (_chart, _targetValue, _targetText, _newColor = false) => {
  let xs = [];
  let ys = [];
  let maxVal = 0;

  chart4Data.forEach(e => {
    e.forEach(elem => {
      if (elem.m == _targetValue) {
        xs.push(elem.categoryName);
        ys.push(parseInt(elem.quantity));

        maxVal = maxVal < parseInt(elem.quantity) ? parseInt(elem.quantity) : maxVal;
      }
    });
  });

  _chart.data.labels = xs;
  _chart.data.datasets[0].label = _targetText;
  _chart.data.datasets[0].data = ys;
  _chart.options = {
    responsive: true,
    title: {
      display: true,
      text: (langSettings[0].CATEGORY || "") + _targetText
    },
    scales: {
      yAxes: [
        {
          ticks: {
            max: Math.floor(Math.round(maxVal) / 10) * 10 + 10,
            stepSize: maxVal > 10 ? 10 : null,
            beginAtZero: true
          }
        }
      ]
    }
  };

  //generate everytime new color
  if (_newColor) {
    const { cr, cg, cb } = genereteRGB();

    _chart.data.datasets[0].backgroundColor = "rgba(" + cr + ", " + cg + ", " + cb + ", 0.2)";
    _chart.data.datasets[0].borderColor = "rgba(" + cr + ", " + cg + ", " + cb + ", 1)";
  }

  _chart.update();
};

let drawChart04 = () => {
  let _chartID = "myChart4";
  let _selectName = "selectMonthChart4";

  createMonthSelect(_chartID, _selectName, "pl");

  const chartTmp = emptyChart(_chartID);

  const activities = document.getElementById(_selectName);

  var cVal = (activities.value = new Date().getMonth() + 1);
  var ctext = activities[activities.value - 1].text;
  ctext = ctext.charAt(0).toUpperCase() + ctext.slice(1); // first letter upperCase

  // if (!chart4Data) return null; //break if not data

  updateChart04(chartTmp, cVal, ctext);

  activities.addEventListener("change", () => {
    cVal = activities.value;
    ctext = activities[activities.value - 1].text;

    updateChart04(chartTmp, cVal, ctext, true);
  });
};

drawChart04();
