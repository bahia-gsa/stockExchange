
function currencyFormatter(currency, sign) {
  var sansDec = currency.toFixed(0);
  var formatted = sansDec.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  return sign + `${formatted}`;
}
function AVGCostcurrencyFormatter(currency, sign) { // para o AvgCost que é diferente dos outros
  var sansDec = currency.toFixed(2);
  var formatted = sansDec.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  return sign + `${formatted}`;
}



var gridOptions1 = {
  columnDefs: [
    { 
      headerName: "History of transactions",
      headerClass: "full-width-header",
      children: [
        {field: "op",  width: 120, filter: 'agSetColumnFilter',
        cellStyle: params => {
          const value = params.value;
          if(value == "buy"){
            return {fontWeight: 'none', color: '#00FF00', textAlign: 'center'};
          }else if(value == "sell") {
            return {fontWeight: 'none', color: 'red', textAlign: 'center'};
          }else if(value == "split"){
            return {fontWeight: 'none', color: 'blue', textAlign: 'center'};
          }
        }
       },
      {field: "QTD", width: 120, valueFormatter: params => currencyFormatter(params.data.qtd, ""), filter: 'agNumberColumnFilter'},
      {field: "VALUE", valueFormatter: params => currencyFormatter(params.data.value, "R$ "), flex: 1, filter: 'agNumberColumnFilter'},
      {field: "date_op", flex: 1, sortable: true, filter: 'agDateColumnFilter'},
  ]
    }
],

  rowStyle: {border: 'none' },
  headerHeight:30,
  defaultColDef: {
    width: 200,
    editable: true,
    cellStyle: {textAlign: 'center', color: "white"},
    sortable: true,
  },




}; 

var gridOptions2 = {
    columnDefs: [
      { 
        headerName: "History of sells",
        headerClass: "full-width-header",
        children: [
          {field: "QTD", width: 120, valueFormatter: params => currencyFormatter(params.data.qtd, "")},
          {field: "SOLD", flex: 1, valueFormatter: params => currencyFormatter(params.data.value_sold, "R$ ")},
          {field: "COST", flex: 1, valueFormatter: params => currencyFormatter(params.data.value_bought, "R$ ")},
          {field: "Result", flex: 1, cellStyle: params => {
              const value = params.data.result;
              const isNegative = value <= 0;
              return {fontWeight: isNegative ? 'none' : 'none', color: isNegative ? 'red' : '#00FF00', textAlign: 'center'};
            }, valueFormatter: params => currencyFormatter(params.data.result, "R$ ")},  
          {field: "date_balance", filter: 'agDateColumnFilter'},
    ]
  }
  ],
    rowStyle: {border: 'red' },
    headerHeight: 30,
    autoSizeColumns: true,
    defaultColDef: {
      width: 200,
      editable: true,
      cellStyle: {textAlign: 'center', color: "white"},
      sortable: true,
      filter: 'agNumberColumnFilter'
      
  },

};
  
var gridOptions3 = {
    columnDefs: [
      { headerName: 'TICKER', width: 119,field: 'ticker', suppressAutoSize: true },
      { headerName: 'QTD', width: 119, field: 'qtd', valueFormatter: params => currencyFormatter(params.data.qtd, ""), suppressAutoSize: true },
      { headerName: 'COST', field: 'value', valueFormatter: params => currencyFormatter(params.data.value, "R$ "), flex: 1, suppressAutoSize: true },
      { headerName: 'Avg Cost', width: 135, field: 'avgCost', valueFormatter: params => AVGCostcurrencyFormatter(params.data.avgCost, "R$ "), suppressAutoSize: true },
      { headerName: '(%) in Total', field: 'share', flex: 1, suppressAutoSize: true },
    ],
    rowStyle: {border: 'none' },
    headerHeight: 30,
    defaultColDef: {
      width: 200,
      editable: true,
      cellStyle: {textAlign: 'center', border: 'none', color: 'white', fontSize: '1.5em'},
      headerClass: 'centered-header',
      //resizable: true,
  },
   
};  


   
// Initialize the grids
document.addEventListener('DOMContentLoaded', function() {
      var grid1 = document.querySelector('#listTransactions');
      new agGrid.Grid(grid1, gridOptions1);
      var grid2 = document.querySelector('#balance_sheet_ticker');
      new agGrid.Grid(grid2, gridOptions2);
      var grid3 = document.querySelector('#extras');
      new agGrid.Grid(grid3, gridOptions3);

      gridOptions1.api.setRowData(dataJSON.transactions);
      gridOptions2.api.setRowData(dataJSON.balances);
      gridOptions3.api.setRowData([JSON.parse(dataJSON.dashboard)]);

  
});

   // tableResults  TICKER//
   document.addEventListener('DOMContentLoaded', function() {
    const columnDefs = [
      { headerName: 'Total sells per month',
      headerClass: "full-width-header", 
      children: [
      { headerName: 'Month', field: 'month', width: 120},
      { headerName: 'Ticker', field: 'ticker', width: 120 },
      { headerName: 'Value Sold', field: 'value_sold', flex: 1, valueFormatter: params => currencyFormatter(params.data.value_sold, "R$ ")},
      { headerName: 'Value Bought', field: 'value_bought', flex: 1, valueFormatter: params => currencyFormatter(params.data.value_bought, "R$ ")},
      { headerName: 'Result', field: 'result', flex: 1, cellStyle: params => {
        const value = params.data.result;
        const isNeg = value < 0;
        return {fontWeight: isNeg ? 'none' : 'none', color: isNeg ? 'red' : '#00FF00', textAlign: 'center'};
      }, valueFormatter: params => currencyFormatter(params.data.result, "R$ ") }
      ]}
    ];

    const rowData = [];
    for (const month in resultMonthJSON) {
      for (const item of resultMonthJSON[month]) {
        rowData.push({
          month,
          ...item
        });
      }
    }

    const gridOptions4 = {
      columnDefs,
      rowData,
      headerHeight: 30,
      rowStyle: {border: 'none' },
      defaultColDef: {
        sortable: true,
        cellStyle: {textAlign: 'center', border: 'none', color: 'white'},
        headerClass: 'centered-header',

      },

    };

    new agGrid.Grid(document.getElementById('tableResults'), gridOptions4);
   });

