<?php

namespace api\components;

use common\models\Postback;

class PostbackHandler
{
    public static function add($orderId, $subId, $status)
    {
        $postback = new Postback();
        $postback->url = "http://91.223.123.67/f89e783/postback?subid=" . urlencode($subId) . "&tid=" . $orderId . "&payout=1&status=" . $status . "&from=m4leads.com";
        $postback->send = false;
        $postback->status = null;
        return $postback->save();
    }
}