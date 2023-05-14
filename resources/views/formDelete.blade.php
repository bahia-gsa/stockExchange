@extends('layouts.main')

@section('content')


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="js/delete.js"></script>

<div class="menu-tciker">
    <div>
        <form action="show_transactions" id="form" method="GET">
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
</div>
<script>
    function submitForm() {
        const formElement = document.getElementById('form');
        formElement.submit();
    }
</script>
  


@if(isset($ticker))

   <!-- <Div class="container-delete">
        <div id="list_transactions_delete" style="height: 80vh; width:60vw;" class="ag-theme-alpine"></div>
    </Div> -->

    <div class="container-delete">
        <div id="list_transactions_delete" style="height: 80vh; width:60vw;" class="ag-theme-alpine"></div>
    </div>


        
@endif



@endsection