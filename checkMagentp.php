<?php
$apiKey = '{Qapla\' API Key}';

$magentoURL = '{Mangeto url}'.'/index.php/api/v2_soap/?wsdl';

$dateFrom = date('Y-m-d').' 00:00:00';

echo '<h4>Connecting to '.$magentoURL.'</h4>';

/** Soap connection */
try{
    $proxy = new SoapClient($magentoURL);
}
catch (Exception $e){
    exit($e->__toString());
}
echo '&#10003; SOAP OK<br/>';

/** login */
try{
    $sessionId = $proxy->login('qapla', $apiKey);
}
catch (Exception $e){
    exit($e->__toString());
}
echo '&#10003; Login OK<br/>';

/** salesOrderList */
echo '<h4>Getting orders starting from '.$dateFrom.'</h4>';

$complexFilter = [
    'complex_filter' => [
        ['key' => 'updated_at', 'value' => ['key' => 'from','value' => $dateFrom],],
    ]];

try{
    $result = $proxy->salesOrderList($sessionId, $complexFilter);
}
catch(Exception $e){
    exit('Cannot read order list.'.$e->__toString());
}

if(!count($result)):
    echo '<h4>No orders found today</h4>';
else:
    echo '<h4>'.count($result).' orders found</h4><table>';

    $rows = 0;

    foreach($result as $order):

        try{
            $orderInfo = $proxy->salesOrderInfo($sessionId, $order->increment_id);
        }
        catch (Exception $e){
            exit('Cannot get the order.'.$e->__toString());
        }

        echo '<tr><td width="50">'.sprintf("%03s",++$rows).'</td><td width="250">'.$order->increment_id.'</td><td width="200">'.$order->updated_at.'</td><td width="200">'.$order->status.'</td></tr>';

        $orderID = $order->order_id;

    endforeach;

    echo '</table>';

    echo '<h4>Getting order: '.$order->increment_id.', id: '.$orderID.'</h4>';

    /** salesOrderList */
    $filter = ['filter' => [['key' => 'order_id', 'value' => $orderID]]];
    try{
        $order = $proxy->salesOrderList($sessionId, $filter);
    }
    catch(Exception $e) {
        exit('Cannot get the order.'.$e->__toString());
    }

    var_dump($order);
endif;
