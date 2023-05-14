<h1>Isso é um teste</h1>


<div class="container_ticker">
    <div class="row_ticker">
      <div class="square square1"></div>
      <div class="square square2"></div>
    </div>
    <div class="row_ticker">
      <div class="square square3"></div>
      <div class="square square4"></div>
    </div>
  </div>
  

<style>

.container {
  display: flex;
  flex-direction: column;
  height: 90vh;
  width: 100vw;
}

.row {
  display: flex;
  flex-direction: row;
  flex-wrap: nowrap;
  height: 50%;
}

.square {
  flex: 1;
  border: 1px solid black;
  box-sizing: border-box;
}

.square1 {
  background-color: red;
}

.square2 {
  background-color: green;
}

.square3 {
  background-color: blue;
}

.square4 {
  background-color: yellow;
}




</style>


