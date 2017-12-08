<?php
$apiKey = '{Qapla\' API Key}';
$magentoURL = '{Mangeto url}'.'/index.php/api/v2_soap/?wsdl';

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
$complexFilter = [
    'complex_filter' => [
        ['key' => 'updated_at', 'value' => ['key' => 'from','value' => date('Y-m-d').' 00:00:00'],],
    ]];
    
try{
    $result = $proxy->salesOrderList($sessionId, $complexFilter);
}
catch(Exception $e){
    exit('Cannot read order list.'.$e->__toString());
}

echo '&#10003; Order list OK<br/>';

if(!count($result)):
    echo '&#10003; No orders found today';
else:
    echo '&#10003; '.count($result).' orders found<table class="mTop10">';

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
    $filter = array('filter' => array(array('key' => 'order_id', 'value' => $orderID)));
    try{
        $order = $proxy->salesOrderList($sessionId, $filter);
    }
    catch(Exception $e) {
        exit('Cannot get the order.'.$e->__toString());
    }
endif;
