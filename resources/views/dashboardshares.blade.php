
      
      
@extends('layouts.main')

@section('content')
<script src="https://code.highcharts.com/highcharts.js"></script>

@if(isset($results))

  <div id="noAssets">You have no assets yet !!!</div>

@else
  <script type="text/javascript" src="js/chartdashboard.js"></script>
  <script type="text/javascript" src="js/barhighchart.js"></script>
  <script type="text/javascript" src="js/dashboardTable.js"></script>


  
  <div class="wrap_dashboard">
      <div id="container_dashboard" ></div>
      <div id="container_barhighchart" ></div>
      <div class="divTable">
        <div id="dashboardTable"  class="ag-theme-alpine"></div>
      </div>
  </div>
@endif

@endsection
