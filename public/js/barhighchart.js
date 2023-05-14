fetch('json/barhighchart.json')
  .then(response => response.json())
  .then(data => {
    Highcharts.chart('container_barhighchart', {
        chart: {
            backgroundColor: 'none', 
            type: 'column'
        },
        title: {
            text: 'PORTFOLIO',
            style: {
                color: 'yellow', // set the color of the chart title
                fontSize: '18px' // set the font size of the chart title
            }
        },
        xAxis: {
            categories: ['ticker'],          
            crosshair: true,
            lineColor: 'yellow', // Set the color of the X axis line
            lineWidth: 0, // Set the width of the X axis line
            gridLineWidth: 0,  // Hide the gridlines for the X axis
            labels: {
                style: {
                    color: 'white' // set the color of the x-axis labels
                }
            }
        },
        yAxis: {
            visible: false
        },
        
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        legend: {
            itemStyle: {
                color: 'white',
                fontSize: '12px',
                fontWeight: 'normal'
            },
            backgroundColor: 'black',
            borderColor: 'none',
            borderWidth: 1,
            borderRadius: 5,
            shadow: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    color: 'white',
                    fontSize: '15px',
                    fontFamily: 'Arial, sans-serif',
                    formatter: function() {
                        return 'R$ ' + Highcharts.numberFormat(this.y, 0, ',', '.');
                    }
                }
                
                
            }
        },
        series: data
    });
});
