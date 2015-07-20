<?php

namespace Common\Library;
class PayPal {
	private static $account = '376794678@qq.com';
	private static $currency = 'CAD';

	public static function getCode($order) {
		$payId      = $order['pay_id'];
        $amount        = $order['money'];
        $returnUrl    = U('member/respond', '', true, true) . '?code=' . U('');
        $account   = self::$account;
        $currency      = self::$currency;
        $notifyUrl    = $returnUrl;
        $cancelReturn      = U('/', '', true, true);

        $def_url  = '<form id="gotopay" style="text-align:center;" action="https://www.paypal.com/cgi-bin/webscr" method="post">' .   // 不能省略
            "<input type='hidden' name='cmd' value='_xclick'>" .                             // 不能省略
            "<input type='hidden' name='business' value='$account'>" .                 // 贝宝帐号
            "<input type='hidden' name='item_name' value='$order[pay_sn]'>" .                 // payment for
            "<input type='hidden' name='amount' value='$amount'>" .                        // 订单金额
            "<input type='hidden' name='currency_code' value='$currency'>" .            // 货币
            "<input type='hidden' name='return' value='$returnUrl'>" .                    // 付款后页面
            "<input type='hidden' name='invoice' value='$payId'>" .                      // 订单号
            "<input type='hidden' name='charset' value='utf-8'>" .                              // 字符集
            "<input type='hidden' name='no_shipping' value='1'>" .                              // 不要求客户提供收货地址
            "<input type='hidden' name='no_note' value=''>" .                                  // 付款说明
            "<input type='hidden' name='notify_url' value='$notifyUrl'>" .
            "<input type='hidden' name='rm' value='2'>" .
            "<input type='hidden' name='cancel_return' value='$cancelReturn'>" .
            "</form><br />";

        return $def_url;
	}

	public static function respond() {
		$merchantId    = self::$account;               ///获取商户编号

		// read the post from PayPal system and add 'cmd'
        $req = 'cmd=_notify-validate';
        foreach ($_POST as $key => $value)
        {
            $value = urlencode(stripslashes($value));
            $req .= "&$key=$value";
        }

        // post back to PayPal system to validate
        $header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($req) ."\r\n\r\n";
        $fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);

        // assign posted variables to local variables
        $itemName = $_POST['item_name'];
        $itemNumber = $_POST['item_number'];
        $paymentStatus = $_POST['payment_status'];
        $paymentAmount = $_POST['mc_gross'];
        $paymentCurrency = $_POST['mc_currency'];
        $txnId = $_POST['txn_id'];
        $receiverEmail = $_POST['receiver_email'];
        $payerEmail = $_POST['payer_email'];
        $payId = $_POST['invoice'];
        $memo = !empty($_POST['memo']) ? $_POST['memo'] : '';

        if (!$fp)
        {
            fclose($fp);

            return false;
        }
        else
        {
            fputs($fp, $header . $req);
            while (!feof($fp))
            {
                $res = fgets($fp, 1024);
                if (strcmp($res, 'VERIFIED') == 0)
                {
                    // check the payment_status is Completed
                    if ($paymentStatus != 'Completed' && $paymentStatus != 'Pending')
                    {
                        fclose($fp);

                        return false;
                    }

                    // check that txn_id has not been previously processed
                    /*$sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('order_action') . " WHERE action_note LIKE '" . mysql_like_quote($txn_id) . "%'";
                    if ($GLOBALS['db']->getOne($sql) > 0)
                    {
                        fclose($fp);

                        return false;
                    }*/

                    // check that receiver_email is your Primary PayPal email
                    if ($receiverEmail != $merchantId)
                    {
                        fclose($fp);

                        return false;
                    }

                    // check that payment_amount/payment_currency are correct

                    // process payment
                    topicPaid($payId);
                    fclose($fp);

                    return true;
                }
                elseif (strcmp($res, 'INVALID') == 0)
                {
                    // log for manual investigation
                    fclose($fp);

                    return false;
                }
            }
        }
	}
}