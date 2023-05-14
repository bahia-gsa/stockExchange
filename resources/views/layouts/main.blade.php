<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/ag-grid-enterprise@29.2.0/dist/ag-grid-enterprise.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">
    <link rel="stylesheet" href="/css/style.css">
    <title>PROJETO</title>

  </head>

  <body>
    <nav>
      <ul class="menu">
        <li>
          OVERALL
          <ul class="sub-menu">
            <li><a href="dashboardshares">Shares</a></li>
            <li><a href="funds">FIIs</a></li>
          </ul>
        </li>
        <li>
          SHARES
          <ul class="sub-menu">
            <li><a href="view_ticker">Position</a></li>
            <li><a href="FormBuy">Buy</a></li>
            <li><a href="FormSell">Sell</a></li>
            <li><a href="FormSplit">Split</a></li>
            <li><a href="FormBonus">Bonus</a></li>
            <li><a href="FormSubscription">Subscription</a></li>
            <li><a href="delete_ticker">Delete</a></li>
          </ul>
        </li>
        <li>
          FIIs
          <ul class="sub-menu">
            <li>Position</li>
            <li>Buy</li>
            <li>Sell</li>
            <li>Subscription</li>
          </ul>
        </li>
        @auth
        <li style="font-size: 1.1em">
          {{$user = auth()->user()->email}}
          <ul class='sub-menu'>
            <li>
                <a href="{{ route('profile.show') }}">Profile</a>
            </li>
            <li>
              <form action="logout" method="post">
              @csrf
              <a href="logout" onclick="event.preventDefault(); this.closest('form').submit()">Logout</a>
              </form>
            </li>
          </ul>
        </li>
        @endauth
        @guest
       
       
       
        <li>
          Connection
          <ul class='sub-menu'>
            <li><a href='login'>Log in</a></li>
            <li><a href='register'>Sing up</a></li>
          </ul>
        </li>
        @endguest
      </ul>
    </nav>

        @yield('content')

    </body>
</html>