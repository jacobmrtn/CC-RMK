<?php

    // add dashboard ip address between quotes 
    $dashboard_ip = "";

    // add dashboard username between quotes
    $dashboard_username = "";

    // add dashboard password between quotes 
    $dashboard_password = "";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://{$dashboard_ip}/auth/local/login");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, "logemail={$dashboard_username}&logpassword={$dashboard_password}&origin=%2Fview%2Fstatus%2Foverview");
    $response = curl_exec($ch);
    curl_close($ch);

    // if response is OK
    if(http_response_code() == 200) {
        $tokenPOS = strpos($response, 'x-jwt:');

        // Extract the token from the response headers
        if($tokenPOS != false) {
            
            $tokenStart = $tokenPOS + strlen('x-jwt: ');
            $tokenEnd = strpos($response, "\n", $tokenStart);
            // trim whitesapce off token
            $token = trim(substr($response, $tokenStart, $tokenEnd - $tokenStart));

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://{$dashboard_ip}/api/1/devices/1/endpoints/rmk");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                "Authorization: Bearer {$token}",
            ]);
        
            $response = curl_exec($ch);
            curl_close($ch);
    
            if(http_response_code() == 200) {
                echo "RMK in progress";
            } else {
                echo $response;
            }

        } else {
            echo 'No token was found';
        }
    } else {
        echo "Error:" .http_response_code();
    }
