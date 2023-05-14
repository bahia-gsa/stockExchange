
@extends('layouts.main')
     @section('content')

<script>
    // TESTER SI BOM TICKER
document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById('buy');
    form.addEventListener('submit', checkTicker);

    function checkTicker(event) {
    event.preventDefault(); // arreter la submition du form
    const ticker = event.target.ticker.value;
    const qtd = event.target.qtd.value;
    const value = event.target.value.value;

    if (!Number.isInteger(+qtd)) {
        alert('There cannot be a fraction of an asset');
        return;
    }
    if (isNaN(+value)) {
        alert('You have to enter a valid number.');
        return;
    }
    fetch('json/tickers.json')
    .then(response => response.json())
    .then(jsonData => {

        const tickerExists = jsonData.some(obj => obj.Ticker == ticker); // Check if the ticker exists in the JSON data
        if (tickerExists) {
        const form = document.getElementById('buy'); 
            form.submit();
        }else {
            alert(`The ticker ${ticker} is not listed`);
        }
    })
    }
})
</script>
     <div class="compra-venda">

            <form class="formBuySell" id="buy" action="op_buy" method="POST">
                <h1>BUY</h1>
                @if (session()->has('success'))
                    <div class="alert alert-success">
                        {{ session()->get('success') }}
                    </div>
                @elseif (session()->has('error'))
                    <div class="alert alert-error">
                        {{ session()->get('error') }}
                    </div>
                @endif
                @csrf   
                <label for="ticker">TICKER</label>
                <input type="text" name="ticker" minlength="5" maxlength="6" required>
                
                <label for="qtd">QUANTIDADE</label>
                <input type="number" name="qtd" required>
                
                <label for="value">VALOR</label>
                <input type="number" name="value" step="0.01" required>
                
                <label for="date">DATA OPERAÇÂO</label>
                <input type="date" name="date" required>
                <input type="hidden" name="op" value="buy"> <!-- OPERAçÃO BUY-->
                <input type="submit" name="buy_acoes" value="Register">
            </form>

        </div>


    @endsection

