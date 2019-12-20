let updateChart01 = (_chart, _lang = "pl") => {
  const xs = getDateMonthList();
  const dataSet = [];

  Object.keys(chart1Data).forEach(year => {
    const ys = [];
    const { cr, cg, cb } = genereteRGB();

    chart1Data[year].forEach(element => {
      ys.push(element);
    });

    const model = {
      label: (langSettings[0].SELL_HIGH_IN || "") + year + (langSettings[0].IN_YEAR || ""),
      data: ys,
      fill: true,
      backgroundColor: "rgba(" + cr + ", " + cg + ", " + cb + ", 0.2)",
      borderColor: "rgba(" + cr + ", " + cg + ", " + cb + ", 1)",
      borderWidth: 1
    };

    dataSet.push(model);
  });

  _chart.data.labels = xs;
  _chart.data.datasets = dataSet;
  _chart.options = {
    responsive: true,
    title: {
      display: true,
      text: langSettings[0].SELL_HIGH || ""
    },
    scales: {
      yAxes: [
        {
          ticks: {
            beginAtZero: true,
            callback: function(val, i, vals) {
              return val + " zł ";
            }
          }
        }
      ]
    }
  };

  _chart.update();
};

let drawChart01 = () => {
  const chartTmp = emptyChart("myChart01", "line");

  if (!chart1Data) return null; //break if not data

  updateChart01(chartTmp, settings.curLang || "en");
};

drawChart01();
