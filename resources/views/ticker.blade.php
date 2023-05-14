@extends('layouts.main')

@section('content')

<script type="text/javascript" src="js/ticker.js"></script>

<div class="menu-tciker">
    <div>
        <form action="see_ticker" id="form" method="GET">
            <select name="get_ticker" onchange="submitForm()">
            <option value="">--ticker--</option>
            @php
                foreach ($tickers as $row) {
                    echo '<option value="'.$row['ticker'].'">'.$row['ticker'].'</option>'; 
                }
            @endphp
            </select>
            <!--<input type="text" name="get_ticker" placeholder="Ticker" minlength="5" maxlength="6" required>-->
        </form>
    </div>
    <div id="extras" style="height: 8vh; width:40vw;" class="ag-theme-alpine"></div>
</div>
<script>
    function submitForm() { // Soumettre select sans bouton
        const formElement = document.getElementById('form');
        formElement.submit();
    }
</script>
  

@if(isset($ticker))
<script>
    //var dataJSON = @json($data);
    var dataJSON = {!! json_encode($data) !!};
    var resultMonthJSON = {!! json_encode($resultMonth) !!};

    console.log(resultMonthJSON);
    console.log(dataJSON);
</script>
    <div class="container_ticker">
        <div id="tableResults" style="height: 100%; width:40vw;" class="ag-theme-alpine"></div>
        <div id="balance_sheet_ticker" style="height: 100%; width:40vw;" class="ag-theme-alpine"></div>
        <div id="listTransactions" style="height: 100%; width:40vw;" class="ag-theme-alpine"></div>
    </div>
        
@endif


@endsection