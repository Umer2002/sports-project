"use strict";
$(function () {
  initCharts();
  chart1();
});
//Charts
function initCharts() {
  //Chart Bar
  $(".chart.chart-bar").sparkline(
    [6, 4, 8, 6, 8, 10, 5, 6, 7, 9, 5, 6, 4, 8, 6, 8, 10, 5, 6, 7, 9, 5],
    {
      type: "bar",
      barColor: "#fff",
      negBarColor: "#fff",
      barWidth: "4px",
      height: "45px",
    }
  );

  //Chart Pie
  $(".chart.chart-pie").sparkline([30, 35, 25, 8], {
    type: "pie",
    height: "45px",
    sliceColors: [
      "rgba(255,255,255,0.70)",
      "rgba(255,255,255,0.85)",
      "rgba(255,255,255,0.95)",
      "rgba(255,255,255,1)",
    ],
  });

  //Chart Line
  $(".chart.chart-line").sparkline([9, 4, 6, 5, 6, 4, 7, 3], {
    type: "line",
    width: "60px",
    height: "45px",
    lineColor: "#fff",
    lineWidth: 1.3,
    fillColor: "rgba(0,0,0,0)",
    spotColor: "rgba(255,255,255,0.40)",
    maxSpotColor: "rgba(255,255,255,0.40)",
    minSpotColor: "rgba(255,255,255,0.40)",
    spotRadius: 3,
    highlightSpotColor: "#fff",
  });
}

function chart1() {
  var options = {
    chart: {
      type: "pie",
    },
    series: [549, 342, 258],
    labels: ["Data 1", "Data 2", "Data 3"],
    plotOptions: {
      pie: {
        customScale: 1.0,
        donut: {
          labels: {
            show: true,
          },
        },
      },
    },
    theme: {
      mode: "light",
      palette: "palette4",
    },
    stroke: {
      show: false,
      width: 0,
      colors: undefined,
    },
    dataLabels: {
      enabled: false,
    },
    legend: {
      fontSize: "14px",
      horizontalAlign: "center",
      position: "bottom",
    },
    title: {
      text: "report Data",
      align: "center",
      style: {
        fontSize: "20px",
        color: "#9aa0ac",
      },
    },
  };

  var options2 = {
    chart: {
      height: 350,
      type: "bar",
      toolbar: {
        show: false,
      },
    },
    plotOptions: {
      bar: {
        horizontal: false,
        columnWidth: "55%",
        endingShape: "rounded",
      },
    },
    dataLabels: {
      enabled: false,
    },
    stroke: {
      show: true,
      width: 2,
      colors: ["transparent"],
    },
    series: [
      {
        name: "Female",
        data: [181, 228, 345],
      },
      {
        name: "Male",
        data: [368, 124, 285],
      },
    ],
    xaxis: {
      categories: ["Operator 1", "Operator 2", "Operator 3"],
      labels: {
        style: {
          colors: "#9aa0ac",
        },
      },
    },
    yaxis: {
      title: {
        text: "Count",
        style: {
          color: "#9aa0ac",
        },
      },
      labels: {
        style: {
          colors: "#9aa0ac",
        },
      },
    },
    fill: {
      opacity: 1,
    },
    theme: {
      mode: "light",
      palette: "palette4",
    },
    title: {
      text: "Mobile Users",
      align: "center",
      style: {
        fontSize: "20px",
        color: "#9aa0ac",
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

  var options3 = {
    chart: {
      height: 350,
      type: "bar",
      toolbar: {
        show: false,
      },
    },
    plotOptions: {
      bar: {
        horizontal: false,
        columnWidth: "55%",
        endingShape: "rounded",
      },
    },
    dataLabels: {
      enabled: false,
    },
    stroke: {
      show: true,
      width: 2,
      colors: ["transparent"],
    },
    series: [
      {
        name: "Type 1",
        data: [80, 60],
      },
      {
        name: "Type 2",
        data: [97, 115],
      },
      {
        name: "Type 3",
        data: [255, 200],
      },
    ],
    xaxis: {
      categories: ["Male", "Female"],
      labels: {
        style: {
          colors: "#9aa0ac",
        },
      },
    },
    yaxis: {
      title: {
        text: "Count",
        style: {
          color: "#9aa0ac",
        },
      },
      labels: {
        style: {
          colors: "#9aa0ac",
        },
      },
    },
    fill: {
      opacity: 1,
    },
    theme: {
      mode: "light",
      palette: "palette4",
    },
    title: {
      text: "Car Users",
      align: "center",
      style: {
        fontSize: "20px",
        color: "#9aa0ac",
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

  var chart = new ApexCharts(document.querySelector("#chart"), options);

  chart.render();

  var chart2 = new ApexCharts(document.querySelector("#chart2"), options2);

  chart2.render();

  var chart3 = new ApexCharts(document.querySelector("#chart3"), options3);

  chart3.render();
}
