function currencyFormatter(currency, sign) {
    var sansDec = currency.toFixed(0);
    var formatted = sansDec.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    return sign + `${formatted}`;
  }

var gridOptions1 = {
    columnDefs: [
      { headerName: 'Total sells in the year by ticker',
      headerClass: "full-width-header", 
      children: [
      { headerName: 'TICKER', field: 'ticker', width: 120 ,suppressAutoSize: true },
      { headerName: 'SELLS', field: 'value_sold', flex: 1 ,valueFormatter: params => currencyFormatter(params.data.value_sold, "R$ "), suppressAutoSize: true },
      { headerName: 'COSTS', field: 'value_bought', valueFormatter: params => currencyFormatter(params.data.value_bought, "R$ "), flex: 1, suppressAutoSize: true },
      { headerName: 'RESULTS', field: 'result', cellStyle: params => {
        const value = params.data.result;
        const isNegative = value < 0;
        return {fontWeight: isNegative ? 'none' : 'none', color: isNegative ? 'red' : '#00FF00',  fontSize: '1.5em', textAlign: 'center'};
      }, valueFormatter: params => currencyFormatter(params.data.result, "R$ "), filter: 'agNumberColumnFilter'},
      ]
    }
    ],
    rowStyle: {border: 'none' },
    headerHeight:35,
    defaultColDef: {
      width: 200,
      editable: true,
      cellStyle: {textAlign: 'center', color: "white", fontSize: '1.5em', border: 'none'},
      headerClass: 'centered-header',
      sortable: true,

  },
  
   
  };  

  document.addEventListener('DOMContentLoaded', function() {
    var grid1 = document.querySelector('#dashboardTable');
    new agGrid.Grid(grid1, gridOptions1);

    fetch('json/dashboardTable.json')
    .then((response) => response.json())
    .then((data) => gridOptions1.api.setRowData(data)); 
});