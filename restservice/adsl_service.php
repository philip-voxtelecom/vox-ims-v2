<?php

/*
 * GET = READ
 * PUT = CREATE
 * POST = UPDATE
 * DELETE = DELETE
 */

if ($service->url_elements[2] == 'accounts') {
    if ($service->verb == 'GET') {
        $response = array();
        $accounts = new AccountController();
        $data = array();
        try {
            foreach ($accounts->listall() as $account) {
                array_push($data, $account->properties());
            }
            $response['reply'] = $data;
            $response['responseCode'] = 'COMPLETED';
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
            $response['responseCode'] = 'FAILED';
        }


        header("Content-Type: application/json");
        echo json_encode($response);
    } else {
        $response['message'] = 'Method not supported';
        $response['responseCode'] = 'FAILED';

        header("Content-Type: application/json");
        echo json_encode($response);
    }
}

if ($service->url_elements[2] == 'account') {

    if ($service->verb == 'GET') {
        /*
         * GET
         */
        $response = array();
        $account = new AccountController();
        try {
            if (preg_match('/@/', $service->url_elements[3])) {
                $id = $account->findByUsername($service->url_elements[3]);
            } else {
                $id = $service->url_elements[3];
            }
            $account->read($id);
            if (isset($service->parameters['auth'])) {
                $auth = $account->authenticate($service->parameters['auth']);
                if (empty($auth)) {
                    throw new Exception("Username or Password Invalid");
                }
                $response['reply'] = true;
            } else {
                $response['reply'] = $account->properties();
            }
            $response['responseCode'] = 'COMPLETED';
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
            $response['responseCode'] = 'FAILED';
        }

        header("Content-Type: application/json");
        echo json_encode($response);
    } elseif ($service->verb == 'PUT') {
        /*
         * PUT
         */
        $account = new AccountController();
        try {
            $account->create($service->parameters);
            $response['reply'] = $account->properties();
            $response['responseCode'] = 'COMPLETED';
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
            $response['responseCode'] = 'FAILED';
        }
        header("Content-Type: application/json");
        echo json_encode($response);
    } elseif ($service->verb == 'POST') {
        /*
         * POST
         */
        $account = new AccountController();
        try {
            if (preg_match('/@/', $service->url_elements[3])) {
                $id = $account->findByUsername($service->url_elements[3]);
            } else {
                $id = $service->url_elements[3];
            }
            $account->read($id);
            $account->update($service->parameters);
            $response['reply'] = $account->properties();
            $response['responseCode'] = 'COMPLETED';
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
            $response['responseCode'] = 'FAILED';
        }
        header("Content-Type: application/json");
        echo json_encode($response);
    } elseif ($service->verb == 'DELETE') {
        /*
         * DELETE
         */
        $account = new AccountController();
        try {
            if (preg_match('/@/', $service->url_elements[3])) {
                $id = $account->findByUsername($service->url_elements[3]);
            } else {
                $id = $service->url_elements[3];
            }
            $account->read($id);
            $account->delete();
            $response['responseCode'] = 'COMPLETED';
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
            $response['responseCode'] = 'FAILED';
        }
        header("Content-Type: application/json");
        echo json_encode($response);
    } else {
        $response['message'] = 'Method not supported';
        $response['responseCode'] = 'FAILED';

        header("Content-Type: application/json");
        echo json_encode($response);
    }
}

if ($service->url_elements[2] == 'products') {
    if ($service->verb == 'GET') {
        $response = array();
        $products = ProductListFactory::Create();
        $data = array();
        try {
            foreach ($products->getList() as $product) {
                array_push($data, $product->getAttributes());
            }
            $response['reply'] = $data;
            $response['responseCode'] = 'COMPLETED';
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
            $response['responseCode'] = 'FAILED';
        }


        header("Content-Type: application/json");
        echo json_encode($response);
    } else {
        $response['message'] = 'Method not supported';
        $response['responseCode'] = 'FAILED';

        header("Content-Type: application/json");
        echo json_encode($response);
    }
}

if ($service->url_elements[2] == 'product') {

    if ($service->verb == 'GET') {
        /*
         * GET
         */
        $response = array();
        $product = ProductFactory::Create();
        try {
            $product->read($service->url_elements[3]);
            $response['reply'] = $product->getAttributes();
            $response['responseCode'] = 'COMPLETED';
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
            $response['responseCode'] = 'FAILED';
        }

        header("Content-Type: application/json");
        echo json_encode($response);
    }
}

if ($service->url_elements[2] == 'usage') {

    if ($service->verb == 'GET') {
        /*
         * GET
         */
        $account = new AccountController();
        try {
            if (preg_match('/@/', $service->url_elements[3])) {
                $id = $account->findByUsername($service->url_elements[3]);
            } else {
                $id = $service->url_elements[3];
            }
        } catch (Exception $e) {
            $response['message'] = 'Account not found';
            $response['responseCode'] = 'FAILED';
        }
        $usage = array();
        $usage = new UsageController();
        try {
            if ($service->parameters['type'] == 'currentdaily')
                $response['reply'] = $usage->dailyUsageForMonth($id, date('m'));
            if ($service->parameters['type'] == 'currenttotal')
                $response['reply'] = $usage->totalAccountUsage($id);
            $response['responseCode'] = 'COMPLETED';
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
            $response['responseCode'] = 'FAILED';
        }

        header("Content-Type: application/json");
        echo json_encode($response);
    }
}
?>
