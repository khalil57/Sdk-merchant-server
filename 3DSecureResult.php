<?php

/*
 * Copyright (c) 2016 Mastercard
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

include '_bootstrap.php';

// capture POST data from issuer
if (intercept('POST')) {
    // ensure we have a 3DSecureId
    $threeDSecureId = requiredQueryParam('3DSecureId');

    // parse payload to get encoded paRes value
    $post = array_change_key_case($_POST, CASE_LOWER);
    $paResParam = 'pares';
    if (!array_key_exists($paResParam, $post) || empty($post[$paResParam])) {
        error(400, 'Missing required issuer response information');
    }

    $data = array(
        'apiOperation' => 'PROCESS_ACS_RESULT',
        '3DSecure' => array(
            'paRes' => $post[$paResParam]
        )
    );

    // decode paRes by calling Process ACS Result to obtain result
    $response = doRequest($gatewayUrl . '/3DSecureId/' . $threeDSecureId, 'POST', json_encode($data), $headers);

    // build mobile redirect
    doRedirect("gatewaysdk://3dsecure?acsResult=" . urlencode($response));
}
