<?php

    namespace Maileon\Test\Examples;

    use Maileon\Transactions\Transaction;
    use Maileon\Transactions\ContactReference;
    use Maileon\Transactions\TransactionsService;

    // The maileon configuration
    $config = array(
        "BASE_URI" => "https://api.maileon.com/1.0",
        "API_KEY" => "XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX",
        "THROW_EXCEPTION" => "true",
        "DEBUG" => "true", // NEVER enable on production
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

        $transaction = new Transaction();
        $transaction->contact = new ContactReference();

        // Die Email des Kontaktes. Alternativ kann auch die Externe ID genutzt werden, siehe dev.maileon.com
        $transaction->contact->email = "marcus.staender@xqueue.com";

        // Hier muss die ID des Typs angelegt werden
        $transaction->type = 4;

        $transaction->content['information'] = "http://xqueue.de/shop/cart100";
        $transaction->content['cart.products'] = array(
            array(
                'link' => 'http://xqueue.de/shop/p1',
                'name' => 'Testname 1',
                'image' => 'http://xqueue.de/shop/image1.jpg',
                'discount' => "10%"),
            array(
                'link' => 'http://xqueue.de/shop/p2',
                'name' => 'Testname 2',
                'image' => 'http://xqueue.de/shop/image2.jpg',
                'discount' => "20%"));
        $transactions = array($transaction);

        // Wenn man mehrere Transaktionen auf einmal übertragenn woll könnte man diese in einer Loop erzeugen und dem Array
        // $transactions hinzufügen. Im Beispiel gibt es nur eine Transaction.

        $response = $transactionsService->createTransactions($transactions, false, false);

        if ($response->isSuccess() && $response->getResult()->reports[0]) {
            echo "<br /><pre>Sample for accessing message of report [0] (if available): " . $response->getResult()->reports[0]->message . "</pre>";
        }
        ?>
    </li>
</ul>


</body>
</html>