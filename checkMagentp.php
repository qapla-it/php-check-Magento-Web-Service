<?php
$apiKey = '{Qapla' API Key}';
$magentoURL = {Mangeto url}.'/index.php/api/v2_soap/?wsdl';
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
// SOAP
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
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

/** Order List */
$complexFilter = [
    'complex_filter' => [
        ['key' => 'updated_at', 'value' => array['key' => 'from','value' => date('Y-m-d').' 00:00:00']]
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
            $ordineInfo = $proxy->salesOrderInfo($sessionId, $order->increment_id);
        }
        catch (Exception $e){
            exit('Cannot get the order.'.$e->__toString());
		  }

        echo '<tr><td width="50">'.sprintf("%03s",++$righe).'</td><td width="250">'.$ordine->increment_id.'</td><td width="200">'.$ordine->updated_at.'</td><td width="200">'.$ordine->status.'</td></tr>';

        $orderID = $ordine->order_id;

    endforeach;

    echo '</table>';

    echo '<h4>Getting order: '.$ordine->increment_id.', id: '.$orderID.'</h4>';

    /////////////////////////////////////////////////////////////////////////////////////////////////
    // Order List
    /////////////////////////////////////////////////////////////////////////////////////////////////
    $filter = array('filter' => array(array('key' => 'order_id', 'value' => $orderID)));
    try{
        $ordine = $proxy->salesOrderList($sessionId, $filter);
    }
    catch(Exception $e) {
        exit('Non riesco ad accedere all\'ordine.'.$e->__toString());
    }
endif;
