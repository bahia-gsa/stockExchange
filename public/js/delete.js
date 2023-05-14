function currencyFormatter(currency, sign) {
    var sansDec = currency.toFixed(0);
    var formatted = sansDec.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    return sign + `${formatted}`;
  }

var gridOptions1 = {
    columnDefs: [
        {field: "id", width: 120, filter: 'agNumberColumnFilter'},
        {field: "ticker", width: 120},
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
        {
          headerName: 'Excluir',
          cellRenderer: function(params) {
            // Define o botão de exclusão para cada linha
            var button = document.createElement('button');
            button.innerHTML = 'Delete';
            button.addEventListener('click', function() {
              // Captura a linha selecionada
              var selectedRow = params.data;
              var ticker = selectedRow.ticker; // pega o ticker da linha
              // Pede a confirmação do usuário
              if (confirm('By confirming, all ' + ticker + ' transactions from that date onwards will be removed')) {
                // Envia o ID da linha selecionada para o Laravel
                $.ajax({
                  url: 'delete/' + selectedRow.id,
                  type: 'GET',
                  success: function(result) {
                    location.reload(-1);
                  }
                });
              }
            });
            
            // Retorna o botão de exclusão para ser exibido na coluna "Excluir"
            return button;
          }
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
  document.addEventListener('DOMContentLoaded', function() {
    var grid1 = document.querySelector('#list_transactions_delete');
    new agGrid.Grid(grid1, gridOptions1);

    fetch('json/ListTransactionsDelete.json')
    .then((response) => response.json())
    .then((data) => gridOptions1.api.setRowData(data)); 
});