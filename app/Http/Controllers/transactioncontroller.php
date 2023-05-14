<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\transaction; // chamar o model para poder usar coisas relacionadas à base de dados
use App\Models\support; // chamar o model para poder usar coisas relacionadas à base de dados
use App\Models\balance; // chamar o model para poder usar coisas relacionadas à base de dados

use Illuminate\Support\Facades\Storage;; // para poder registar o JSON físico

class transactioncontroller extends Controller
{
    protected $UserID;

    public function __construct(){
        $this->middleware(function ($request, $next) {
            $this->UserID = auth()->user()->id;
            return $next($request);
        });
    }

    public function index(){
        return view('welcome');
    }
    public function openTicker(){
        $tickers = $this->getTickers();
        return view('ticker', ['tickers'=> $tickers]); 
    }
    public function CreateFormBuy(){
        return view('FormBuy');
    }
    public function CreateFormSell(){
        $tickers = $this->getTickers();
        return view('FormSell', ['tickers'=> $tickers]); 
    }
    public function CreateFormSplit(){
        $tickers = $this->getTickers();
        return view('FormSplit', ['tickers'=> $tickers]);
    }
    public function deletePage(){
        $tickers = $this->getTickers();
        return view('formDelete', ['tickers'=> $tickers]); 
    }
    public function showTransactions(){
        $ticker = request("get_ticker"); // usar depois (fazer um query?) para ter certeza que os dados correspondem ao ticker que a pessoa digitou
        $tickers = $this->getTickers();
        $ListTransactions = $this->getTransactionsByTicker($ticker, $this->UserID);
        $ListTransactionsJSON = json_encode($ListTransactions);
        Storage::disk('public')->put('ListTransactionsDelete.json', $ListTransactionsJSON);
        return view('formDelete', ['ticker'=>$ticker, 'tickers'=> $tickers]); 
    }
     
    public function getTickers(){
        $tickers = transaction::where([['user', $this->UserID]])
            ->orderBy('ticker', 'asc')
            ->groupBy('ticker')
            ->get(['ticker']);
        return $tickers;
    }   


    public function showTicker(Request $request){
        $ticker = request("get_ticker"); 
        $resultMonth = $this->getBalanceTickerMonth($ticker, $this->UserID);
        $TransactionsByTicker = $this->getTransactionsByTicker($ticker, $this->UserID)->map(function($transaction) {
            return collect($transaction)->only(['op', 'qtd', 'value','date_op'])->toArray();
        });
        $BalancesbyTicker = $this->getBalancesbyTicker($ticker, $this->UserID);
        $Extras = $this->getExtras($ticker, $this->UserID); 
        $Extras = get_object_vars($Extras);
        $ShareInTotal = $this->getShareInTotal($ticker, $this->UserID);
        $dashboardTicker = array_merge($Extras, $ShareInTotal);
        $dashboardTicker = json_encode($dashboardTicker); 
        $TransactionsJSON = json_encode($TransactionsByTicker);
        $BalancesJSON = json_encode($BalancesbyTicker);
 
        $data = [
            'transactions' => $TransactionsByTicker,
            'balances' => $BalancesbyTicker,
            'dashboard' => $dashboardTicker,
        ];
        $tickers = $this->getTickers(); // pour aficher le select
        return view('ticker', ['ticker'=>$ticker, 'tickers'=> $tickers, 'data' => $data, 'resultMonth' => $resultMonth]); 
    }

    public function getTransactionsByTicker($ticker, $idUser){
        $TransactionsByTicker = transaction::where([
            ['ticker', $ticker],
            ['user', $idUser], 
        ])->orderBy('date_op', 'asc')
          ->get(['*']);
        foreach ($TransactionsByTicker as $row) {
            $row['qtd'] = floatval($row['qtd']);
            $row['value'] = floatval($row['value']);
        }
        return $TransactionsByTicker;
    }

    function getBalancesbyTicker($ticker, $idUser){
        $BalancesbyTicker = balance::where([
            ['ticker', $ticker],
            ['user', $idUser] 
        ])->orderBy('date_balance', 'asc')
          ->get(['qtd', 'value_sold', 'value_bought', 'result', 'date_balance']);
        foreach ($BalancesbyTicker as $row) {
            $row['qtd'] = floatval($row['qtd']);
            $row['value_sold'] = floatval($row['value_sold']);
            $row['value_bought'] = floatval($row['value_bought']);
            $row['result'] = floatval($row['result']);
        }
        return $BalancesbyTicker;
    }

    public function store_buy(Request $request){
        $transaction = new Transaction;
        $transaction->ticker = $request->ticker;
        $transaction->op = $request->op;
        $transaction->qtd = $request->qtd;
        $transaction->value = $request->value;
        $transaction->date_op = $request->date;
        $transaction->user = $this->UserID; // inserindo na tabela a id do user logado
        $transaction->save();

        $LastIdTransactions = $transaction->id; // id da última insersão
        //checa se é aprimeira vez que insere o ativo
        $ticker = $request->ticker;
        $check = support::where('ticker', $ticker)
            ->where('user', $this->UserID)
            ->first();
        if($check){

                //fazer inserção na Supports
                $ticker = $request->ticker; // pegando o dado do form
                $lastQtd = $this->getLastSupport($ticker, $this->UserID)->qtd;
                $support = new Support;
                $support->ticker = $ticker;
                $support->id_transaction = $LastIdTransactions;
                $support->op = $request->op;
                $support->avgCost =(($request->value)+($this->getLastSupport($ticker, $this->UserID)->value))/(($request->qtd)+($this->getLastSupport($ticker, $this->UserID)->qtd));
                $support->qtd = $lastQtd + $request->qtd;
                $support->date_support = $request->date;
                $support->user = $this->UserID; // inserindo na tabela a id do user logado
                $flasMsg = $support->save();

                if ($flasMsg) {
                    session()->flash('success', $ticker.' was saved successfully!');
                } else {
                    session()->flash('error', 'Failed to save transaction.');
                }
     
                //depois atualizar o último lançamento da suppports para adicionar o valor de value
                $LasIdSupports = $support->id; // pegando a última id inserida
                $LasQtdSupports = $support->qtd;
                $LasAvgCostSupports = $support->avgCost;
                $LastValue = $LasQtdSupports *  $LasAvgCostSupports;
                DB::table('supports')
                    ->where('id', '=', $LasIdSupports)
                    ->update([
                            'value' => $LastValue,
                            ]);
        }else{
                //primeiro lança na supports tudo em zero
                $support = new Support;
                $support->ticker = $ticker;
                $support->id_transaction = $LastIdTransactions;
                $support->op = "cal";
                $support->avgCost = 0;
                $support->qtd = 0;
                $support->value = 0;
                $support->date_support = $request->date;
                $support->user = $this->UserID; // inserindo na tabela a id do user logado
                $support->save();

                 //depois faz a inserção normal na support
                 $support = new Support;
                 $support->ticker = $ticker;
                 $support->id_transaction = $LastIdTransactions;
                 $support->op = $request->op;
                 $support->avgCost =($request->value)/($request->qtd);
                 $support->value = $request->value;
                 $support->qtd = $request->qtd;
                 $support->date_support = $request->date;
                 $support->user = $this->UserID; // inserindo na tabela a id do user logado
                 $flasMsg = $support->save();

                 if ($flasMsg) {
                    session()->flash('success', $ticker.' was saved successfully!');
                } else {
                    session()->flash('error', 'Failed to save transaction.');
                }
        }
        return view('FormBuy');
    }
    
    function getExtras($ticker, $idUser){
        $Extras = DB::table('supports')
            ->select('ticker','qtd','value', 'avgCost')
            ->where('ticker', '=', $ticker)
            ->where('user', '=', $idUser)
            ->where('id', '=', DB::raw("(select max(id) from supports where ticker='$ticker' and user='$idUser')"))
            ->where('date_support', '=', DB::raw("(select max(date_support) from supports where ticker='$ticker' and user='$idUser')"))
            ->first();
        return $Extras;
    }

    function getShareInTotal($ticker, $user){
        $results = DB::table('supports')
        ->select('ticker', 'value')
        ->whereIn(DB::raw("(ticker, date_support)"), function($query) use ($user) {
            $query->select(DB::raw("ticker, MAX(date_support)"))
                  ->from('supports')
                  ->where('user', '=', $user)
                  ->groupBy('ticker');
        })
        ->where('user', '=', $user)
        ->get();
        $total = collect($results)->sum('value');
        $tickerValue = collect($results)->where('ticker', $ticker)->sum('value');
        $shareInTotal['share'] = round(($tickerValue / $total) * 100, 2);
        return $shareInTotal;
    }

    public function getLastSupport ($ticker, $idUser){
        $results = DB::table('supports')
            ->select('*')
            ->where('ticker', '=', $ticker)
            ->where('user', '=', $idUser)
            ->where('id', '=', DB::raw("(select max(id) from supports where ticker='$ticker' and user='$idUser')"))
            ->where('date_support', '=', DB::raw("(select max(date_support) from supports where ticker='$ticker' and user='$idUser')"))
            ->first();
        return $results;


    }

    public function store_sell(Request $request){
        $transaction = new transaction;
        $transaction->ticker=$request->ticker;
        $transaction->op=$request->op;
        $transaction->qtd=$request->qtd;
        $transaction->value=$request->value;
        $transaction->date_op=$request->date;
        $transaction->user = $this->UserID; // inserindo na tabela a id do user logado
        $transaction->save();

        //fazer inserção na Supports
        $ticker = $request->ticker; // pegando o dado do form
        $LastIdTransactions = $transaction->id; // pegando a última id inserida
        $lastQtd = $this->getLastSupport($ticker, $this->UserID)->qtd; // pegar qtd inserida anteriormente na Transactions
        $support = new Support;
        $support->ticker = $ticker;
        $support->id_transaction = $LastIdTransactions;
        $support->op = $request->op;
        $support->avgCost = (($this->getLastSupport($ticker, $this->UserID)->avgCost) * ($request->value)) / (($request->value));
        $support->qtd =  $lastQtd - $request->qtd;
        $support->date_support = $request->date;
        $support->user = $this->UserID; // inserindo na tabela a id do user logado
        $support->save();

        //depois atualizar o último lançamento da suppports para adicionar o valor de value
        $LasIdSupports = $support->id; // pegando a última id inserida
        $LasQtdSupports = $support->qtd;
        $LasAvgCostSupports = $support->avgCost;
        $LastValue = $LasQtdSupports *  $LasAvgCostSupports;
        DB::table('supports')
            ->where('id', '=', $LasIdSupports)
            ->update([
                    'value' => $LastValue,
                    ]);
        
        // depois inserir dados na Balances
        $balance = new Balance;
        $balance->ticker = $request->ticker;
        $balance->qtd = $request->qtd;
        $balance->value_sold = $request->value;
        $balance->value_bought = ($request->qtd) * ($this->getLastSupport($ticker, $this->UserID)->avgCost);
        $balance->result = ($request->value) - (($request->qtd) * ($this->getLastSupport($ticker, $this->UserID)->avgCost));
        $balance->date_balance = $request->date;
        $balance->id_transaction = $LastIdTransactions;
        $balance->user = $this->UserID; // inserindo na tabela a id do user logado
        $flasMsg = $balance->save();

        if ($flasMsg) {
            session()->flash('success', $ticker.' was saved successfully!');
        } else {
            session()->flash('error', 'Failed to save transaction.');
        }   
        $tickers = $this->getTickers();
        return view('FormSell', ['tickers'=> $tickers]); 
    }

    public function dashboardshares(){
        $user = $this->UserID;
        $results = Support::select('ticker', 'qtd', 'value', 'avgCost', 'date_support')
            ->whereIn(DB::raw("(ticker, date_support)"), function ($query) use ($user){
                $query->select(DB::raw("ticker, MAX(date_support)"))
                    ->from('supports')
                    ->where('user', '=', $user)
                    ->where('op', '<>', 'cal')
                    ->groupBy('ticker');
                })
            ->where('user', '=', $user)
            ->where('op', '<>', 'cal')
            ->get();

        if ($results->isEmpty()) { // eviter la creation de charts s'il n'y a pas de transactions
            return view('dashboardshares', ["results" => 0]);
        }
        $this->makeCharts($results);
        $this->dashboardTable();
        return view('dashboardshares');
    }
    
    public function makeCharts($results){
        $barHighChart = [];
        $totalValues = collect($results)->sum('value');
        foreach ($results as $row) {
            $barHighChart[] = [
                'name' => $row->ticker,
                'data' => [$row->value]
            ];
            $shareInTotal[] = [
                'name' => $row->ticker,
                'y' => round(($row->value / $totalValues) * 100, 2)
            ];
        }
        $barHighChart = json_encode($barHighChart);
        $shareInTotal = json_encode($shareInTotal);
        Storage::disk('public')->put('barhighchart.json', $barHighChart);
        Storage::disk('public')->put('shareINtotalDashboard.json', $shareInTotal);
    }

    public function dashboardTable (){
        $idUser = $this->UserID;
        $year = date('Y'); // ver como faze para a pessoa ter a escolha 
        $results = DB::table('balances')
            ->select('ticker', DB::raw('SUM(value_sold) as value_sold'), DB::raw('SUM(value_bought) as value_bought'), DB::raw('SUM(result) as result'))
            ->where('user', '=', $idUser)
            ->whereYear('date_balance', '=', $year)
            ->groupBy('ticker')
            ->get();
        $dashboardTable = json_encode($results);
        Storage::disk('public')->put('dashboardTable.json', $dashboardTable);  
    }

    public function store_split(Request $request){
        $transaction = new transaction;
        $transaction->ticker=$request->ticker;
        $transaction->op=$request->op;
        $transaction->qtd=$request->qtd;
        $transaction->value = 0;
        $transaction->date_op=$request->date;
        $transaction->user = $this->UserID; // inserindo na tabela a id do user logado
        $transaction->save();

        //fazer inserção na Supports
        $ticker = $request->ticker; // pegando o dado do form
        $LastIdTransactions = $transaction->id; // pegando a última id inserida
        $lastQtd = $this->getLastSupport($ticker, $this->UserID)->qtd; // pegar qtd inserida anteriormente na Transactions
        $support = new Support;
        $support->ticker = $ticker;
        $support->id_transaction = $LastIdTransactions;
        $support->op = $request->op;
        $support->avgCost = (($this->getLastSupport($ticker, $this->UserID)->value) / ($lastQtd + $request->qtd));
        $support->qtd =  $lastQtd + $request->qtd;
        $support->value = $this->getLastSupport($ticker, $this->UserID)->value;
        $support->date_support = $request->date;
        $support->user = $this->UserID; // inserindo na tabela a id do user logado
        $flasMsg = $support->save();

        if ($flasMsg) {
            session()->flash('success', $ticker.' was saved successfully!');
        } else {
            session()->flash('error', 'Failed to save transaction.');
        }
        
        $tickers = $this->getTickers();
        return view('FormSplit', ['tickers'=> $tickers]); 

    }

    public function getBalanceTickerMonth($ticker, $idUser){
        $year = date('Y');
        $Months = array(
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'May',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Aug',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dec'
        );
        $tableResults = array();
        for($month = 1; $month <= 12; $month++){
            $results = DB::table('balances')
                ->select('ticker', DB::raw('SUM(value_sold) as value_sold'), DB::raw('SUM(value_bought) as value_bought'), DB::raw('SUM(result) as result'))
                ->where('user', '=', $idUser)
                ->where('ticker', '=', $ticker)
                ->whereYear('date_balance', '=', $year)
                ->whereMonth('date_balance', '=', $month)
                ->groupBy('ticker')
                ->get();
    
                $tableResults[$Months[$month]] = $results;
        }
        return $tableResults;
    }

    public function delete($id){
        $idUser = $this->UserID;
        transaction::where('user', '=', $idUser)->where('id', '>=', $id)->delete();
        support::where('user', '=', $idUser)->where('id_transaction', '>=', $id)->delete();
        balance::where('user', '=', $idUser)->where('id_transaction', '>=', $id)->delete();
        $this->showTransactions();
    }

}