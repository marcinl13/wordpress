let updateChart02 = _chart => {
  const xs = [];
  const ys = [];
  let maxVal = 0;

  chart2Data.forEach(elem => {
    xs.push(elem.name);
    ys.push(parseFloat(elem.curMag));

    maxVal = maxVal < parseInt(elem.curMag) ? parseInt(elem.curMag) : maxVal;
  });

  const { cr, cg, cb } = genereteRGB();

  const model = {
    label: langSettings[0].CONTENT_MAG || "Zawartość w magazynie",
    data: ys,
    fill: true,
    backgroundColor: "rgba(" + cr + ", " + cg + ", " + cb + ", 0.2)",
    borderColor: "rgba(" + cr + ", " + cg + ", " + cb + ", 1)",
    borderWidth: 1
  };

  _chart.data.labels = xs;
  _chart.data.datasets = [model];
  _chart.options = {
    responsive: true,
    title: {
      display: true,
      text: langSettings[0].MAG || "Magazyn"
    },
    hover: {
      animationDuration: 0
    },
    animation: {
      duration: 1,
      onComplete: function() {
        var chartInstance = this.chart,
          ctx = chartInstance.ctx;

        ctx.font = Chart.helpers.fontString(
          Chart.defaults.global.defaultFontSize,
          Chart.defaults.global.defaultFontStyle,
          Chart.defaults.global.defaultFontFamily
        );

        ctx.textAlign = "left";
        ctx.textBaseline = "center";

        this.data.datasets.forEach(function(dataset, i) {
          var meta = chartInstance.controller.getDatasetMeta(i);
          meta.data.forEach(function(bar, index) {
            var data = dataset.data[index];
            ctx.fillStyle = "blue";
            ctx.fillText(data, bar._model.x + 5, bar._model.y);
          });
        });
      }
    },
    scales: {
      xAxes: [
        {
          ticks: {
            max: Math.floor(Math.round(maxVal) / 100) * 100 + 100,
            stepSize: 100,
            beginAtZero: true,
            callback: function(val, i, vals) {
              return val;
            }
          }
        }
      ]
    }
  };

  _chart.update();
};

let drawChart02 = () => {
  const chartTmp2 = emptyChart("myChart02", chart2Data.length > 0 ? "horizontalBar" : "bar");

  if (chart2Data.length == 0) return null; //break if not data

  updateChart02(chartTmp2);
};

drawChart02();
