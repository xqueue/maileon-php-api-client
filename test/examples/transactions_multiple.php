<?php
    namespace Maileon\Test\Examples;

    use de\xqueue\maileon\api\client\transactions\Transaction;
    use de\xqueue\maileon\api\client\transactions\ContactReference;
    use de\xqueue\maileon\api\client\transactions\TransactionsService;

    // The maileon configuration
    $config = array(
        "BASE_URI" => "https://api-test.maileon.com/1.0",
        "API_KEY" => "05509dd4-2859-4770-85df-1de1de04fd28 ",
        "THROW_EXCEPTION" => "true",
        "DEBUG" => true,
        "TIMEOUT" => 0
    );

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>Maileon Transactions Testpage</title>
</head>
<body>

<?php 
    $transactionsService = new TransactionsService($config);
    $transactionsService->setDebug($config['DEBUG']);
?>

<ul>
	<li>
        POST create transactions
        <?php


        $transactions = array();
        for ($i=0; $i<100; $i++) {

            $transaction = new Transaction();
            $transaction->contact = new ContactReference();

            $transaction->import->email = "max$i@baunzt.de";

            // Hier muss die ID des Typs angelegt werden
            $transaction->type = 142;

            $transaction->content['information'] = "http://xqueue.de/shop/cart$i";
            $transaction->content['cart.products'] = array(
                array(
                    'link' => 'http://xqueue.de/shop/'.$i,
                    'name' => 'Testname '.$i,
                    'image' => 'http://xqueue.de/shop/image'.$i.'jpg',
                    'discount' => "10%"),
                array(
                    'link' => 'http://xqueue.de/shop/p'.$i,
                    'name' => 'Testname '.$i,
                    'image' => 'http://xqueue.de/shop/image'.$i.'jpg',
                    'discount' => "20%"));
            $transactions[] = $transaction;
        }

        echo "<pre>";
        echo json_encode($transactions, JSON_PRETTY_PRINT);
        echo "</pre>";

        $response = $transactionsService->createTransactions($transactions, false, false);

        if ($response->isSuccess() && $response->getResult()->reports[0]) {
            echo "<br /><pre>Sample for accessing message of report [0] (if available): " . $response->getResult()->reports[0]->message . "</pre>";
        }
        ?>
    </li>

    <li>
        Get 100 last transactions
        <?php

        $response = $transactionsService->getRecentTransactions(142, 10);

        foreach ($response->getResult() as $transaction) {
            var_dump($transaction);
        }
        ?>
    </li>
</ul>
</body>
</html>