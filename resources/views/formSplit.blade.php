@extends('layouts.main')
     @section('content')


     <div class="compra-venda">

            <form class="formBuySell" id="sell" action="op_split" method="POST">
                <h1>SPLIT</h1>
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
                               
                <label for="date">DATA OPERAÇÂO</label>
                <input type="date" name="date" required>
                <input type="hidden" name="op" value="split"> <!-- OPERAçÃO BUY-->
                <input type="submit" name="add_split" value="Register">
            </form>

        </div>


    @endsection