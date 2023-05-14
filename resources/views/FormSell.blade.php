@extends('layouts.main')
@section('content')

<script>
    // TESTER SI BOM TICKER
document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById('sell');
    form.addEventListener('submit', checkForm);

    function checkForm(event) {
    event.preventDefault(); // arreter la submition du form
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
    const form = document.getElementById('sell'); 
            form.submit();
    }
})
</script>


<div class="compra-venda">

        <form class="formSell" id="sell"  action="op_sell" method="POST">
            <h1>SELL</h1>
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
            <select name="ticker">
                <option value="">--ticker--</option>
                @php
                    foreach ($tickers as $row) {
                        echo '<option value="'.$row['ticker'].'">'.$row['ticker'].'</option>'; 
                    }
                @endphp
            </select>
            
            <label for="qtd">QUANTIDADE</label>
            <input type="number" name="qtd" required>
            
            <label for="value">VALOR</label>
            <input type="number" name="value" step="0.01" required>
            
            <label for="date">DATA OPERAÇÂO</label>
            <input type="date" name="date" required>
            
            <input type="hidden" name="op" value="sell"> <!-- OPERAçÃO SELL-->
 
            
            <input type="submit" name="sell_acoes" value="Register">
        </form>
    </div>

@endsection