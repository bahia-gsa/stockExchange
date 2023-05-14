fetch('json/shareINtotalDashboard.json')
  .then(response => response.json())
  .then(data => {
    Highcharts.chart('container_dashboard', {
      chart: {
        backgroundColor: 'none', 
          plotBackgroundColor: null,
          plotBorderWidth: null,
          plotShadow: false,
          type: 'pie'
      },
      title: {
          text: 'ALLOCATION',
          align: 'center',
          style: {
            color: 'yellow', // set the color of the chart title
            fontSize: '18px' // set the font size of the chart title
        }
      },
      tooltip: {
          pointFormat: '{series.name}: <b>{point.percentage:.2f}%</b>'
      },
      accessibility: {
          point: {
              valueSuffix: '%'
          }
      },
      plotOptions: {
          pie: {
              allowPointSelect: true,
              cursor: 'pointer',
              dataLabels: {
                  enabled: true,
                  color: 'white',
                  fontSize: '15px',
                  fontFamily: 'Arial, sans-serif',
                  format: '<b>{point.name}</b>: {point.percentage:.2f} %'
              },
              borderWidth: 2, // set the width of the border
              borderColor: 'none', // set the color of the border
          }
      },
      series: [{
        name: 'stake',
        colorByPoint: true,
        data: data

      }] 
    });
  });
