"use strict";
$(function () {
  $("#chat-conversation").slimscroll({
    height: "264px",
    size: "5px",
  });
  initCardChart();
  initSparkline();
  initLineChart();
  initSalesChart();

  initChartReport1();
  initChartReport2();
});

function initCardChart() {
  //Chart Bar
  $(".chart.chart-bar").sparkline(
    [6, 4, 8, 6, 8, 10, 5, 6, 7, 9, 5, 6, 4, 8, 6, 8, 10, 5, 6, 7, 9, 5],
    {
      type: "bar",
      barColor: "#FF9800",
      negBarColor: "#fff",
      barWidth: "4px",
      height: "45px",
    }
  );

  //Chart Pie
  $(".chart.chart-pie").sparkline([30, 35, 25, 8], {
    type: "pie",
    height: "45px",
    sliceColors: ["#65BAF2", "#F39517", "#F44586", "#6ADF42"],
  });

  //Chart Line
  $(".chart.chart-line").sparkline([9, 4, 6, 5, 6, 4, 7, 3], {
    type: "line",
    width: "60px",
    height: "45px",
    lineColor: "#65BAF2",
    lineWidth: 2,
    fillColor: "rgba(0,0,0,0)",
    spotColor: "#F39517",
    maxSpotColor: "#F39517",
    minSpotColor: "#F39517",
    spotRadius: 3,
    highlightSpotColor: "#F44586",
  });

  // live chart
  var mrefreshinterval = 500; // update display every 500ms
  var lastmousex = -1;
  var lastmousey = -1;
  var lastmousetime;
  var mousetravel = 0;
  var mpoints = [];
  var mpoints_max = 30;
  $("html").on("mousemove", function (e) {
    var mousex = e.pageX;
    var mousey = e.pageY;
    if (lastmousex > -1) {
      mousetravel += Math.max(
        Math.abs(mousex - lastmousex),
        Math.abs(mousey - lastmousey)
      );
    }
    lastmousex = mousex;
    lastmousey = mousey;
  });
  var mdraw = function () {
    var md = new Date();
    var timenow = md.getTime();
    if (lastmousetime && lastmousetime != timenow) {
      var pps = Math.round((mousetravel / (timenow - lastmousetime)) * 1000);
      mpoints.push(pps);
      if (mpoints.length > mpoints_max) mpoints.splice(0, 1);
      mousetravel = 0;
      $("#liveChart").sparkline(mpoints, {
        width: mpoints.length * 2,
        height: "45px",
        tooltipSuffix: " pixels per second",
      });
    }
    lastmousetime = timenow;
    setTimeout(mdraw, mrefreshinterval);
  };
  // We could use setInterval instead, but I prefer to do it this way
  setTimeout(mdraw, mrefreshinterval);
}
function initChartReport1() {
  var options = {
    series: [
      {
        name: "Data 1",
        data: [45, 52, 38, 45, 51],
      },
      {
        name: "Data 2",
        data: [12, 42, 68, 33, 48],
      },
    ],
    chart: {
      height: 400,
      type: "line",
      dropShadow: {
        enabled: true,
        color: "#000",
        top: 18,
        left: 7,
        blur: 10,
        opacity: 0.2,
      },
      toolbar: {
        show: false,
      },
    },
    colors: ["#14CD10", "#387AED", "#c0c0c0"],
    dataLabels: {
      enabled: true,
      style: {
        fontSize: "12px",
        fontFamily: "Poppins",
        fontWeight: "bold",
      },
      background: {
        enabled: true,
        foreColor: "#fff",
        padding: 6,
        borderRadius: 50,
        borderWidth: 1,
        borderColor: "#fff",
      },
    },
    markers: {
      size: 1,
    },
    stroke: {
      width: 5,
      curve: "smooth",
      lineCap: "round",
    },
    xaxis: {
      categories: ["11:15", "11:30", "11:45", "12:00", "12:15"],
      labels: {
        style: {
          colors: "#9aa0ac",
        },
      },
    },
    yaxis: {
      labels: {
        style: {
          colors: "#9aa0ac",
        },
      },
    },
    legend: {
      show: true,
      labels: {
        colors: "#9aa0ac",
      },
    },
    tooltip: {
      theme: "dark",
      marker: {
        show: true,
      },
      x: {
        show: true,
      },
    },
  };

  var chart = new ApexCharts(document.querySelector("#chart3"), options);
  chart.render();
}
function initChartReport2() {
  var options = {
    chart: {
      height: 400,
      type: "bar",
      toolbar: {
        show: false,
      },
    },
    plotOptions: {
      bar: {
        dataLabels: {
          position: "top",
        },
      },
    },
    dataLabels: {
      enabled: true,
      formatter: function (val) {
        return val.toLocaleString() + "%";
      },
      offsetY: -20,
      style: {
        colors: ["#9aa0ac"],
      },
    },
    grid: {
      show: true,
    },

    annotations: {
      position: "front",
      yaxis: [
        {
          y: 9.5,
          borderColor: "#111",

          label: {
            borderColor: "transparent",
            style: {
              color: "#666",
              background: "#e7e7e7",
              padding: {
                left: 15,
                right: 15,
                top: 10,
                bottom: 12,
              },
            },
            text: "9,5% p.a.",
          },
        },
      ],
    },
    series: [
      {
        name: "Product 1",
        data: [32, 6, 15, 1, 14, -12, 9],
      },
      {
        name: "Product 2",
        data: [21, 7, 11, 1, 10, -11, 8],
      },
    ],
    xaxis: {
      categories: ["2013", "2014", "2015", "2016", "2017", "2018", "2019"],
      position: "bottom",
      labels: {
        offsetY: -18,
        style: {
          colors: "#9aa0ac",
        },
      },
      axisBorder: {
        show: false,
      },
      axisTicks: {
        show: false,
      },
      crosshairs: {
        show: false,
        fill: {
          type: "gradient",
          gradient: {
            colorFrom: "#D8E3F0",
            colorTo: "#BED1E6",
            stops: [0, 100],
            opacityFrom: 0.4,
            opacityTo: 0.5,
          },
        },
      },
    },
    legend: {
      show: true,
      labels: {
        colors: "#9aa0ac",
      },
    },
    fill: {
      gradient: {
        shade: "light",
        type: "horizontal",
        shadeIntensity: 0.25,
        gradientToColors: undefined,
        inverseColors: true,
        opacityFrom: 1,
        opacityTo: 1,
        stops: [50, 0, 100, 100],
      },
    },
    yaxis: {
      show: false,
    },
    tooltip: {
      theme: "dark",
      marker: {
        show: true,
      },
      x: {
        show: true,
      },
    },
    colors: ["#625b4c", "#c9c1b1"],
  };

  var chart = new ApexCharts(document.querySelector("#chart4"), options);

  chart.render();
}
function initSparkline() {
  $(".sparkline").each(function () {
    var $this = $(this);
    $this.sparkline("html", $this.data());
  });
}

function initLineChart() {
  try {
    //line chart
    var ctx = document.getElementById("lineChart");
    if (ctx) {
      ctx.height = 150;
      var myChart = new Chart(ctx, {
        type: "line",
        data: {
          labels: [
            "January",
            "February",
            "March",
            "April",
            "May",
            "June",
            "July",
          ],
          defaultFontFamily: "Poppins",
          datasets: [
            {
              label: "My First dataset",
              borderColor: "rgba(0,0,0,.09)",
              borderWidth: "1",
              backgroundColor: "rgba(0,0,0,.07)",
              data: [22, 44, 67, 43, 76, 45, 12],
            },
            {
              label: "My Second dataset",
              borderColor: "rgba(0, 123, 255, 0.9)",
              borderWidth: "1",
              backgroundColor: "rgba(0, 123, 255, 0.5)",
              pointHighlightStroke: "rgba(26,179,148,1)",
              data: [16, 32, 18, 26, 42, 33, 44],
            },
          ],
        },
        options: {
          legend: {
            position: "top",
            labels: {
              fontFamily: "Poppins",
            },
          },
          responsive: true,
          tooltips: {
            mode: "index",
            intersect: false,
          },
          hover: {
            mode: "nearest",
            intersect: true,
          },
          scales: {
            xAxes: [
              {
                ticks: {
                  fontFamily: "Poppins",
                },
              },
            ],
            yAxes: [
              {
                ticks: {
                  beginAtZero: true,
                  fontFamily: "Poppins",
                },
              },
            ],
          },
        },
      });
    }
  } catch (error) {
    console.log(error);
  }
}

function initSalesChart() {
  try {
    //Sales chart
    var ctx = document.getElementById("sales-chart");
    if (ctx) {
      ctx.height = 150;
      var myChart = new Chart(ctx, {
        type: "line",
        data: {
          labels: ["2010", "2011", "2012", "2013", "2014", "2015", "2016"],
          type: "line",
          defaultFontFamily: "Poppins",
          datasets: [
            {
              label: "Foods",
              data: [0, 30, 10, 120, 50, 63, 10],
              backgroundColor: "transparent",
              borderColor: "#222222",
              borderWidth: 2,
              pointStyle: "circle",
              pointRadius: 3,
              pointBorderColor: "transparent",
              pointBackgroundColor: "#222222",
            },
            {
              label: "Electronics",
              data: [0, 50, 40, 80, 40, 79, 120],
              backgroundColor: "transparent",
              borderColor: "#f96332",
              borderWidth: 2,
              pointStyle: "circle",
              pointRadius: 3,
              pointBorderColor: "transparent",
              pointBackgroundColor: "#f96332",
            },
          ],
        },
        options: {
          responsive: true,
          tooltips: {
            mode: "index",
            titleFontSize: 12,
            titleFontColor: "#000",
            bodyFontColor: "#000",
            backgroundColor: "#fff",
            titleFontFamily: "Poppins",
            bodyFontFamily: "Poppins",
            cornerRadius: 3,
            intersect: false,
          },
          legend: {
            display: false,
            labels: {
              usePointStyle: true,
              fontFamily: "Poppins",
            },
          },
          scales: {
            xAxes: [
              {
                display: true,
                gridLines: {
                  display: false,
                  drawBorder: false,
                },
                scaleLabel: {
                  display: false,
                  labelString: "Month",
                },
                ticks: {
                  fontFamily: "Poppins",
                },
              },
            ],
            yAxes: [
              {
                display: true,
                gridLines: {
                  display: false,
                  drawBorder: false,
                },
                scaleLabel: {
                  display: true,
                  labelString: "Value",
                  fontFamily: "Poppins",
                },
                ticks: {
                  fontFamily: "Poppins",
                },
              },
            ],
          },
          title: {
            display: false,
            text: "Normal Legend",
          },
        },
      });
    }
  } catch (error) {
    console.log(error);
  }
}
