<?php

// # # # # Route # # # #

Route::PUT('bkash-payment', 'BkashController@bkash');


// # # # # Controller # # # #


public function bkash()
    {

        // Merchant Info

        $msisdn = "01200000000"; // bKash Merchant Number.

        $user = "Xyz"; // bKash Merchant Username.

        $pass = "123"; // bKash Merchant Password.

        $url = "https://www.bkashcluster.com:9081"; // bKash API URL with Port Number.

        $trxid = "66666AAAAA"; // bKash Transaction ID : TrxID.


// Final URL for getting response from bKash.

        $bkash_url = $url.'/dreamwave/merchant/trxcheck/sendmsg?user='.$user.'&pass='.$pass.'&msisdn='.$msisdn.'&trxid='.$trxid;


        $curl = curl_init();

        curl_setopt_array($curl, array(

            CURLOPT_PORT => 9081,

            CURLOPT_URL => $bkash_url,

            CURLOPT_RETURNTRANSFER => true,

            CURLOPT_ENCODING => "",

            CURLOPT_MAXREDIRS => 10,

            CURLOPT_TIMEOUT => 30,

            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

            CURLOPT_CUSTOMREQUEST => "GET",

            CURLOPT_HTTPHEADER => array(

                "cache-control: no-cache",

                "content-type: application/json"

            ),

        ));


        $response = curl_exec($curl);

        $err = curl_error($curl);

        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);
    
            //print_r($response); // For Getting all Response Data.
    
            $api_response = json_decode ($response, true);  // Getting Response from bKash API.

            $transaction_status = $api_response['transaction']['trxStatus']; // Transaction Status Codes
    

        if ($err || $transaction_status == "4001") {
                echo 'Problem for Sending Response to bKash API ! Try Again after fews minutes.';
            }
        else
        {
// Assign Transaction Information

            $transaction_amount = $api_response['transaction']['amount']; // bKash Payment Amount.

            $transaction_reference = $api_response['transaction']['reference']; // bKash Reference for Invoice ID.

            $transaction_time = $api_response['transaction']['trxTimestamp']; // bKash Transaction Time & Date.


// Return Transaction Information into Your Blade Template.

            return view('transaction.bkash', compact('transaction_status', 'transaction_amount', 'transaction_reference', 'transaction_time'));

        }
    }
    
    
    
// # # # # View # # # #  // Laravel Blade File

              <div class="panel">
                    <div class="panel-heading">
                        <h3 class="panel-title">bKash Payment Confirmation</h3>
                    </div>

                    <div class="panel-body">

                        @php
                        
                            if($transaction_status == '0000'){
                                    echo "<div class='alert alert-success'>Transaction Successful. trxID is valid and transaction is successful.</div>";
                            }

                            elseif($transaction_status == '0010'){
                                    echo "<div class='alert alert-warning'>Transaction Pending... trxID is valid but transaction is in pending state.</div>";
                            }

                            elseif($transaction_status == '0011'){
                                    echo "<div class='alert alert-warning'>Transaction Pending... trxID is valid but transaction is in pending state.</div>";
                            }

                            elseif($transaction_status == '0100'){
                                    echo "<div class='alert alert-danger'>Transaction Reversed ! trxID is valid but transaction has been reversed.</div>";
                            }

                            elseif($transaction_status == '0111'){
                                    echo "<div class='alert alert-danger'>Transaction Failure ! trxID is valid but transaction has failed.</div>";
                            }

                            elseif($transaction_status == '1001'){
                                    echo "<div class='alert alert-danger'>Format Error ! Invalid MSISDN input. Try with correct mobile no.</div>";
                            }

                            elseif($transaction_status == '1002'){
                                    echo "<div class='alert alert-danger'>Invalid Reference ! Invalid trxID, it does not exist.</div>";
                            }

                            elseif($transaction_status == '1003'){
                                    echo "<div class='alert alert-danger'>Authorization Error ! Access denied. Username or Password is incorrect.</div>";
                            }

                            elseif($transaction_status == '1004'){
                                    echo "<div class='alert alert-danger'>Authorization Error ! Access denied. trxID is not related to this username.</div>";
                            }

                            elseif($transaction_status == '9999'){
                                    echo "<div class='alert alert-danger'>System Error ! Could not process request.</div>";
                            }

                            else{
                                    echo "<div class='alert alert-danger'>Unknown ERROR !</div>";
                            }

                        @endphp
                       
          // Print Transaction Information

                        <b>Amount :</b> {{ $transaction_amount }} <br><br>

                        <b>Reference :</b> {{ $transaction_reference }} <br><br>

                        <b>Time :</b> {{ $transaction_time }} <br><br><br>

          // Invoice Generate
          
                        <a href="{{ url('get-invoice') }}" class="btn btn-primary">{{ 'Click Here to Get Invoice' }}</a>
                    </div>
                </div>
?>
