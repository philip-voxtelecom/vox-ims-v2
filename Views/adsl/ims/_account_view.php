<?php

$xajax->registerFunction("accountView");
$xajax->registerFunction("accountSubmit");
$xajax->registerFunction("logout");

function accountView($viewrequest, $viewarray) {
    $view = new AccountView_ims();
    if (method_exists($view, $viewrequest)) {
        return call_user_func_array(array($view, $viewrequest), array($viewarray));
    } else {
        throw new Exception("Function $viewrequest does not exist in accountView class");
    }
}

function accountSubmit($submitrequest, $data) {
    $request = new AccountSubmit_ims();
    if (method_exists($request, $submitrequest)) {
        return call_user_func_array(array($request, $submitrequest), array($data));
    } else {
        throw new Exception("Function $submitrequest does not exist in accountSubmit class");
    }
}

class AccountView_ims extends AccountView {

    protected $xajax;

    public function __construct($viewobject = null) {
        $this->xajax = new xajaxResponse();
        parent::__construct($viewobject);
    }

    public function logout() {
        session_destroy();
        $url = $GLOBALS['config']->baseURL;
        $this->xajax->script("window.location.href='http://$url';");
        return $this->xajax;
    }

    public function display($viewarray = NULL) {
        ;
    }

    public function listall($viewarray = NULL) {
        $accounts = parent::listall($viewarray);

        // Todo this shouldn't be here, should be passed as part of parent object
        $List = AccountListFactory::Create();
        $List->getList(0, 0, $this->search);
        $count = $List->count();


        $this->smarty->assign("accounts", $accounts);
        $this->smarty->assign("count", $count);
        $this->smarty->assign("offset", "$this->offset");
        $this->smarty->assign("limit", $this->limit);
        $this->smarty->assign("search", $this->search);
        $accountlist = $this->smarty->fetch('accountList.tpl');
        $this->smarty->clear_all_assign();

        $viewobject = new ViewObject('<root/>');

        $menu1 = $viewobject->addChild('globalmenu');
        if ($GLOBALS['auth']->checkAuth('adsl_account', AUTH_CREATE)) {
            $menuitem = $menu1->addChild('menuitem');
            $menuitem->addChild('face', 'Create account');
            $menuitem->addChild('action', "xajax_accountView('create',{});");
        }

        $this->smarty->assign("search", $this->search);
        $this->smarty->assign("viewobject", $viewobject);
        $accountlistbar = $this->smarty->fetch('accountListMenu.tpl');
        $this->xajax->assign("content", "innerHTML", $accountlist);
        $this->xajax->assign("right_bar", "innerHTML", $accountlistbar);
        return $this->xajax;
    }

    public function actions($viewarray = NULL) {

        $this->setPage($viewarray);

        $return = FALSE;

        if (isset($viewarray['return']))
            $return = $viewarray['return'];

        $accountId = $viewarray['id'];
        //$account = AccountFactory::Create();
        $account = new AccountController();
        $account->read($accountId);
        $viewobject = new ViewObject('<root/>');
        $data = $viewobject->addChild('data');
        append_simplexml($data, $account->asXML());

        //$accountId = $viewobject->data->account->id;

        $menu2 = $viewobject->addChild('globalmenu');
        if ($GLOBALS['auth']->checkAuth('adsl_account', AUTH_CREATE)) {
            $menuitem = $menu2->addChild('menuitem');
            $menuitem->addChild('face', 'Create account');
            $menuitem->addChild('action', "xajax_accountView('create',{});");
        }

        $menu1 = $viewobject->addChild('menu');
        $menu1->addAttribute('title', 'Account Actions');

        if ($GLOBALS['auth']->checkAuth('adsl_account', AUTH_READ)) {
            $menuitem = $menu1->addChild('menuitem');
            $menuitem->addChild('face', 'Detail Summary');
            $menuitem->addChild('action', "xajax_accountView('detail',{id:$accountId});");
        }

        if ($GLOBALS['auth']->checkAuth('adsl_account', AUTH_UPDATE) and $viewobject->data->account->status != "cancelled") {
            $menuitem = $menu1->addChild('menuitem');
            $menuitem->addChild('face', 'Edit Account');
            $menuitem->addChild('action', "xajax_accountView('edit',{id: $accountId, offset: $this->offset, limit: $this->limit, search: '$this->search'});");
        }

        /*
          if ($GLOBALS['auth']->checkAuth('adsl_account', AUTH_UPDATE) and $viewobject->data->account->status != "cancelled") {
          $menuitem = $menu1->addChild('menuitem');
          $menuitem->addChild('face', 'Edit Product');
          $menuitem->addChild('action', "xajax_accountProductUpdateDisplay($accountId);");
          }
         * 
         */

        //if ($GLOBALS['auth']->checkAuth('adsl_account', AUTH_UPDATE) and $viewobject->data->account->status != "cancelled" and $ownerproduct->topupable == 'yes') {
        if ($GLOBALS['auth']->checkAuth('adsl_account', AUTH_UPDATE) and $viewobject->data->account->status != "cancelled") {
            $menuitem = $menu1->addChild('menuitem');
            $menuitem->addChild('face', 'Top up Account');
            $menuitem->addChild('action', "xajax_accountView('topup',{id: $accountId,offset: $this->offset, limit: $this->limit, search: '$this->search'});");
        }

        if ($GLOBALS['auth']->checkAuth('adsl_account', AUTH_UPDATE) and ( $viewobject->data->account->status != "cancelled" or $GLOBALS['login']->isAdmin())) {
            $menuitem = $menu1->addChild('menuitem');
            $menuitem->addChild('face', 'Change Status');
            $menuitem->addChild('action', "xajax_accountView('status',{id: $accountId, offset: $this->offset, limit: $this->limit, search: '$this->search'});");
        }


        if ($GLOBALS['auth']->checkAuth('adsl_account', AUTH_READ) and $viewobject->data->account->status != "cancelled") {
            $menuitem = $menu1->addChild('menuitem');
            $menuitem->addChild('face', 'Current Usage');
            $menuitem->addChild('action', "xajax_accountView('dailyUsage',{id: $accountId});");
        }

        if ($GLOBALS['auth']->checkAuth('adsl_account', AUTH_READ) and $viewobject->data->account->status != "cancelled") {
            $menuitem = $menu1->addChild('menuitem');
            $menuitem->addChild('face', 'Historical Usage');
            $menuitem->addChild('action', "xajax_accountView('monthlyUsage',{id: $accountId});");
        }
        if ($GLOBALS['auth']->checkAuth('adsl_account', AUTH_READ) and $viewobject->data->account->status != "cancelled") {
            $menuitem = $menu1->addChild('menuitem');
            $menuitem->addChild('face', 'Active Sessions');
            $menuitem->addChild('action', "xajax_accountView('activeSessions',{id: $accountId});");
        }
        /*
          if ($GLOBALS['auth']->checkAuth('adsl_account', AUTH_READ)) {
          $menuitem = $menu1->addChild('menuitem');
          $menuitem->addChild('face', 'Session Statistics');
          $menuitem->addChild('action', "xajax_accountSessionDisplay($accountId);");
          }
         * 
         */

        /*
          if ($GLOBALS['auth']->checkAuth('adsl_account', AUTH_UPDATE) and $viewobject->data->account->status != "cancelled") {
          $menuitem = $menu1->addChild('menuitem');
          $menuitem->addChild('face', 'Cancel Account');
          $menuitem->addChild('action', "xajax_accountCancelDisplay($accountId);");
          }
         * 
         */

        if ($GLOBALS['auth']->checkAuth('adsl_account', AUTH_DELETE)) {
            $menuitem = $menu1->addChild('menuitem');
            $menuitem->addChild('face', 'Delete Account');
            $menuitem->addChild('action', "xajax_accountView('delete',{id: $accountId,offset: $this->offset, limit: $this->limit, search: '$this->search'});");
        }


        $this->smarty->assign('viewobject', $viewobject);
        $this->smarty->assign("offset", "$this->offset");
        $this->smarty->assign("return", "$return");
        $this->smarty->assign("limit", $this->limit);
        $this->smarty->assign("search", $this->search);
        $content = $this->smarty->fetch('accountActions.tpl');

        $this->xajax->assign('right_bar', 'innerHTML', $content);
        //$this->xajax->append('right_bar','innerHTML',"<pre>".print_r($viewobject,true)."</pre>");
        return $this->xajax;
    }

    public function detail($viewarray = NULL) {

        $accountId = $viewarray['id'];

        //$account = AccountFactory::create();
        $account = new AccountController();
        $account->read($accountId);

        $product = ProductFactory::Create();
        $product->read($account->product());

        $viewobject = new ViewObject('<root></root>');
        $data = $viewobject->addChild('data');
        append_simplexml($data, $account->asXML());
        append_simplexml($data, $product->asXML());

        $this->smarty->assign('viewobject', $viewobject);
        $content = $this->smarty->fetch('accountDetails.tpl');
        $this->xajax->assign("content", "innerHTML", $content);

        return $this->xajax;
    }

    public function create($viewarray = NULL) {
        $productlist = new ProductView(NULL);
        $owner = OwnerViewFactory::Create();
        $realms = $owner->realms($GLOBALS['login']->getLoginId());
        $account = new AccountController();
        $accountoptions = $account->options();

        $this->smarty->assign('accountoptions', $accountoptions);
        $accountdetail = $this->smarty->fetch('accountDetail.tpl');
        $this->smarty->assign('accountDetail', $accountdetail);
        $this->smarty->assign('productlist', $productlist->listall());
        $this->smarty->assign('productoptions', $productlist->options());
        $this->smarty->assign('realms', $realms);
        $content = $this->smarty->fetch('accountCreate.tpl');
        $this->xajax->assign("content", "innerHTML", $content);
        $this->xajax->assign("right_bar", "innerHTML", "");
        $this->xajax->script("
            var myform=new formtowizard({
               formid: 'accountCreateForm',
               persistsection: false,
               revealfx: ['none', 500],
               onpagechangestart:function($, i, \$fieldset){
                 var validated=true
                 var fieldset=\$fieldset.get(0)
                 var allels=fieldset.getElementsByTagName('input')
                 for (var i=0; i<allels.length; i++){
                    var valid = Validation.validate(allels[i].getAttribute('id'))
                    //alert(allels[i].getAttribute('id'))
                    if (!valid) validated=false
                 }
                 var allels2=fieldset.getElementsByTagName('select')
                 for (var i=0; i<allels2.length; i++){
                    var valid = Validation.validate(allels2[i].getAttribute('id'))
                    //alert(allels2[i].getAttribute('id'))
                    if (!valid) validated=false
                 }
                 return validated
              }
            })
        ");
        return $this->xajax;
    }

    public function edit($viewarray = NULL) {
        $accountId = $viewarray['id'];

        $this->setPage($viewarray);

        $account = new AccountController();
        $account->read($accountId);
        $accountoptions = $account->options();



        $productlist = new ProductView(NULL);

        $viewobject = new ViewObject('<root></root>');
        $data = $viewobject->addChild('data');
        append_simplexml($data, $account->asXML());
        $viewobject->addChild('action', 'update');

        $this->smarty->assign('viewobject', $viewobject);
        $this->smarty->assign('accountoptions', $accountoptions);
        $this->smarty->assign('productoptions', $productlist->options());
        $accountdetail = $this->smarty->fetch('accountDetail.tpl');
        $productdetail = $this->smarty->fetch('accountProduct.tpl');
        $this->smarty->assign('accountDetail', $accountdetail);
        $this->smarty->assign('accountProduct', $productdetail);
        $this->smarty->assign("offset", "$this->offset");
        $this->smarty->assign("limit", $this->limit);
        $this->smarty->assign("search", $this->search);
        $content = $this->smarty->fetch('accountUpdate.tpl');
        $this->xajax->assign("content", "innerHTML", $content);
        $this->xajax->assign("right_bar_content", "innerHTML", "");
        $this->xajax->script("
            var myform=new formtowizard({
               formid: 'accountUpdateForm',
               persistsection: false,
               revealfx: ['none', 500],
               onpagechangestart:function($, i, \$fieldset){
                 var validated=true
                 var fieldset=\$fieldset.get(0)
                 var allels=fieldset.getElementsByTagName('input')
                 for (var i=0; i<allels.length; i++){
                    var valid = Validation.validate(allels[i].getAttribute('id'))
                    //alert(allels[i].getAttribute('id'))
                    if (!valid) validated=false
                 }
                 var allels2=fieldset.getElementsByTagName('select')
                 for (var i=0; i<allels2.length; i++){
                    var valid = Validation.validate(allels2[i].getAttribute('id'))
                    //alert(allels2[i].getAttribute('id'))
                    if (!valid) validated=false
                 }
                 return validated
              }
            })
        ");
        return $this->xajax;
    }

    public function status($viewarray = NULL) {
        $viewobject = new ViewObject(parent::status($viewarray));

        $this->setPage($viewarray);

        $this->smarty->assign("viewobject", $viewobject);
        $this->smarty->assign("offset", "$this->offset");
        $this->smarty->assign("limit", $this->limit);
        $this->smarty->assign("search", $this->search);
        $content = $this->smarty->fetch('accountStatus.tpl');
        $this->xajax->assign("content", "innerHTML", $content);
        //return Error("<pre><xmp>".print_r($viewobject->asXML(),true)."</xmp></pre>");
        return $this->xajax;
    }

    public function topup($viewarray = NULL) {
        $viewobject = new ViewObject(parent::topup($viewarray));

        $this->setPage($viewarray);

        $this->smarty->assign("viewobject", $viewobject);
        $this->smarty->assign("offset", "$this->offset");
        $this->smarty->assign("limit", $this->limit);
        $this->smarty->assign("search", $this->search);
        $content = $this->smarty->fetch('accountTopup.tpl');
        $this->xajax->assign("content", "innerHTML", $content);
        //return Error("<pre><xmp>".print_r($viewobject->asXML(),true)."</xmp></pre>");
        return $this->xajax;
    }

    public function delete($viewarray = NULL) {
        $viewobject = new ViewObject(parent::delete($viewarray));

        $this->setPage($viewarray);

        $this->smarty->assign("viewobject", $viewobject);
        $this->smarty->assign("offset", "$this->offset");
        $this->smarty->assign("limit", $this->limit);
        $this->smarty->assign("search", $this->search);
        $content = $this->smarty->fetch('accountDelete.tpl');
        $this->xajax->assign("content", "innerHTML", $content);

        return $this->xajax;
    }

    public function dailyUsage($viewarray = NULL) {
        //$viewobject = new ViewObject(parent::dailyUsage($viewarray));
        try {
            $viewobject = parent::dailyUsage($viewarray);
        } catch (Exception $e) {
            $this->xajax->script("alert('No usage found');");
            return $this->xajax;
        }

        $this->setPage($viewarray);
        $date = preg_split('/-/', $viewobject['usageMonth']);
        $this->smarty->assign("viewobject", $viewobject['days']);
        $this->smarty->assign("totals", $viewobject['totals']);
        $this->smarty->assign("offset", "$this->offset");
        $this->smarty->assign("limit", $this->limit);
        $this->smarty->assign("search", $this->search);
        $this->smarty->assign("year", $date[0]);
        $this->smarty->assign("month", $date[1]);
        $this->smarty->assign("day", $date[2]);
        $this->smarty->assign("accountId", $viewarray['id']);
        $content = $this->smarty->fetch('accountDailyStats.tpl');
        $this->xajax->assign("content", "innerHTML", $content);

        return $this->xajax;
    }

    public function dailyUsageDetail($viewarray = NULL) {
        try {
            $viewobject = parent::dailyUsageDetail($viewarray);
        } catch (Exception $e) {
            $this->xajax->script("alert('No usage found');");
            return $this->xajax;
        }


        $this->setPage($viewarray);

        $this->smarty->assign("viewobject", $viewobject['sessions']);
        $this->smarty->assign("offset", "$this->offset");
        $this->smarty->assign("limit", $this->limit);
        $this->smarty->assign("search", $this->search);
        $this->smarty->assign("year", $viewarray['year']);
        $this->smarty->assign("month", $viewarray['month']);
        $this->smarty->assign("day", $viewarray['day']);
        $content = $this->smarty->fetch('accountDailyDetailStats.tpl');
        $this->xajax->assign("content", "innerHTML", $content);

        return $this->xajax;
    }

    public function MonthlyUsage($viewarray = NULL) {
        //$viewobject = new ViewObject(parent::dailyUsage($viewarray));
        try {
            $viewobject = parent::monthlyUsage($viewarray);
        } catch (Exception $e) {
            $this->xajax->script("alert('No usage found');");
            return $this->xajax;
        }

        $this->setPage($viewarray);

        $this->smarty->assign("viewobject", $viewobject);
        $this->smarty->assign("offset", "$this->offset");
        $this->smarty->assign("limit", $this->limit);
        $this->smarty->assign("search", $this->search);
        $this->smarty->assign("accountId", $viewarray['id']);
        $content = $this->smarty->fetch('accountMonthlyStats.tpl');
        $this->xajax->assign("content", "innerHTML", $content);

        return $this->xajax;
    }

    public function activeSessions($viewarray = NULL) {
        //$viewobject = new ViewObject(parent::dailyUsage($viewarray));
        try {
            $viewobject = parent::activeSessions($viewarray);
        } catch (Exception $e) {
            $this->xajax->script("alert('No active sessions found');");
            return $this->xajax;
        }

        $this->setPage($viewarray);
        $this->smarty->assign("viewobject", $viewobject);
        $this->smarty->assign("offset", "$this->offset");
        $this->smarty->assign("limit", $this->limit);
        $this->smarty->assign("search", $this->search);
        $this->smarty->assign("accountId", $viewarray['id']);
        $content = $this->smarty->fetch('accountActiveSessions.tpl');
        $this->xajax->assign("content", "innerHTML", $content);

        return $this->xajax;
    }

    public function detailCallback($viewarray = NULL) {

        $this->setPage($viewarray);

        $return = 'true';

        if (isset($viewarray['return']))
            $return = $viewarray['return'];

        $viewobject = new ViewObject($this->viewobject);
        $accountId = $viewobject->data->id;
        $this->xajax->script("xajax_accountView('actions',{id: $accountId, return: $return, offset: $this->offset, limit: $this->limit, search: '$this->search'});");
        $this->xajax->script("xajax_accountView('detail',{id: $accountId,offset: $this->offset, limit: $this->limit, search: '$this->search'});");
        return $this->xajax;
    }

    public function listCallback($viewarray = NULL) {

        $this->setPage($viewarray);

        $this->xajax->script("xajax_accountView('listall',{offset: $this->offset, limit: $this->limit, search: '$this->search'});");
        //$this->xajax->script("xajax_accountView('detail',{id: $accountId,offset: $offset, limit: $limit, search: '$search'});");
        return $this->xajax;
    }

    public function printlist($viewarray = NULL) {
        $accounts = parent::listall($viewarray);
        $this->smarty->assign("usernames", $accounts);
        $accountlist = $this->smarty->fetch('accountListPrint.tpl');
        $this->xajax->append("content", "innerHTML", $accountlist);
        $this->xajax->script('printpage("account_list");');

        return $this->xajax;
    }

    public function exportlist($viewarray = NULL) {
        $accounts = parent::listall($viewarray);
        $this->smarty->assign("usernames", $accounts);
        $accountlist = $this->smarty->fetch('accountListExport.tpl');
        $fileid = "accountlist_" . uniqid() . ".csv";
        file_put_contents($GLOBALS['documentroot'] . "/cache/" . $fileid, $accountlist);
        //$this->xajax->append("content","innerHTML",$accountlist);
        $this->xajax->script("window.location= '/download.php?file=$fileid&rm=1'");

        return $this->xajax;
    }

    public function isUsernameAvailable($username) {

        $response = '';
        try {
            $account = new AccountController();
            $id = $account->isUsernameAvailable($username);
            !empty($id) ? $response = 'available' : $response = 'unavailable';
        } catch (Exception $e) {
            throw new Exception("Error finding account: " . $e->getMessage());
        }

        $this->xajax->assign("isUsernameAvailable", "value", $response);
        return $this->xajax;
    }

    public function errorCallback($viewarray = NULL) {

        if (isset($viewarray['error']))
            $error = $viewarray['error'];

        $this->xajax->script("alert('The following error was encountered: $error');");
        return $this->xajax;
    }

}

//TOD this should be in the controller as a loaded provider controller
class AccountSubmit_ims {

    public function create($formdata) {
        $viewobject = new ViewObject('<root></root>');
        $data = $viewobject->addChild('data');


        try {
            $account = new AccountController();
            $params = array();

            if (isset($formdata['_save_name']))
                $params['description'] = $formdata['_save_name'];
            if (isset($formdata['_save_password']))
                $params['password'] = $formdata['_save_password'];
            if (isset($formdata['_save_email']) and !empty($formdata['_save_email']))
                $params['notifyemail'] = $formdata['_save_email'];
            if (isset($formdata['_save_cellno']))
                $params['notifycell'] = $formdata['_save_cellno'];
            if (isset($formdata['_save_note']))
                $params['note'] = $formdata['_save_note'];
            if (isset($formdata['_save_mailreport']))
                $params['mailreport'] = $formdata['_save_mailreport'];
            if (isset($formdata['product']))
                $params['product'] = $formdata['product'];
            if (isset($formdata['_save_username']))
                $params['username'] = $formdata['_save_username'];
            if (isset($formdata['_save_systemReference']))
                $params['systemReference'] = $formdata['_save_systemReference'];
            if (isset($formdata['_save_bundlesize']))
                $params['bundlesize'] = $formdata['_save_bundlesize'];
            if (isset($formdata['_save_callingstation']))
                $params['callingstation'] = $formdata['_save_callingstation'];

            $id = $account->create($params);
        } catch (Exception $e) {
            $view = new AccountView_ims($viewobject->asXML());
            error_log("Error creating account: " . $e->getMessage());
            return $view->errorCallback(array('error' => 'Could not create account: ' . $e->getMessage()));
            //throw new Exception("Error creating account: " . $e->getMessage());
        }

        $view = new AccountView_ims($viewobject->asXML());
        $data->addChild('id', $id);
        return $view->detailCallback(array('return' => 'false'));
    }

    public function update($formdata) {

        $offset = 0;
        $limit = $GLOBALS['config']->displayRowLimit;
        $search = NULL;
        $viewobject = new ViewObject('<root></root>');
        $data = $viewobject->addChild('data');

        try {
            $account = new AccountController();
            $params = array();

            $accountId = $formdata['id'];
            $account->read($accountId);
            $properties = $account->properties();

            /*
            foreach ($formdata as $key => $update) {
                error_log("$key = $update");
            }
             * 
             */

            if (isset($formdata['_save_status']) and $formdata['_save_status'] != $properties['status'])
                $params['status'] = $formdata['_save_status'];
            if (isset($formdata['_save_topup']))
                $params['topup'] = $formdata['_save_topup'];
            if (isset($formdata['_save_email']) and $formdata['_save_email'] != $properties['notifyemail'])
                $params['notifyemail'] = $formdata['_save_email'];
            if (isset($formdata['_save_cellno']) and $formdata['_save_cellno'] != $properties['notifycell'])
                $params['notifycell'] = $formdata['_save_cellno'];
            if (isset($formdata['_save_password']) and !empty($formdata['_save_password']))
                $params['password'] = $formdata['_save_password'];
            if (isset($formdata['_save_note']) and $formdata['_save_note'] != $properties['note'])
                $params['note'] = $formdata['_save_note'];
            if (isset($formdata['_save_name']) and $formdata['_save_name'] != $properties['description'])
                $params['description'] = $formdata['_save_name'];
            if (isset($formdata['_save_bundlesize']) and $formdata['_save_bundlesize'] != $properties['bundlesize'])
                $params['bundlesize'] = $formdata['_save_bundlesize'];
            if (isset($formdata['_save_callingstation']) and $formdata['_save_callingstation'] != $properties['callingstation'])
                $params['callingstation'] = $formdata['_save_callingstation'];
            $id = $account->update($params);
        } catch (Exception $e) {
            $view = new AccountView_ims($viewobject->asXML());
            error_log("Error updating account: " . $e->getMessage());
            return $view->errorCallback(array('error' => 'Could not update account: ' . $e->getMessage()));
            //throw new Exception("Error updating account: " . $e->getMessage());
        }

        $data->addChild('id', $accountId);

        $offset = $formdata['offset'];
        $search = $formdata['search'];
        $limit = $formdata['limit'];

        $view = new AccountView_ims($viewobject->asXML());
        return $view->detailCallback(array('offset' => $offset, 'search' => $search, 'limit' => $limit));
    }

    public function delete($formdata) {

        $offset = 0;
        $limit = $GLOBALS['config']->displayRowLimit;
        $search = NULL;

        try {
            $account = new AccountController();
            $params = array();

            $accountId = $formdata['id'];
            $account->read($accountId);
            $account->delete();
        } catch (Exception $e) {
            $view = new AccountView_ims();
            error_log("Error deleting account: " . $e->getMessage());
            return $view->errorCallback(array('error' => 'Could not delete account: ' . $e->getMessage()));
            //throw new Exception("Error deleting account: " . $e->getMessage());
        }

        $offset = $formdata['offset'];
        $search = $formdata['search'];
        $limit = $formdata['limit'];

        $view = new AccountView_ims();
        return $view->listCallback(array('offset' => $offset, 'search' => $search, 'limit' => $limit));
    }

    public function disconnectSession($formdata) {

        $offset = 0;
        $limit = $GLOBALS['config']->displayRowLimit;
        $search = NULL;
        $viewobject = new ViewObject('<root></root>');
        $data = $viewobject->addChild('data');
        $accountId = $formdata['id'];
        
        try {
            $account = new AccountController();

            $account->read($accountId);
            $properties = $account->properties();
            $username = $properties['username'];
            
            $server = "196.22.195.3:8080";
            $wsdl = "voxdsl/soap/description";

            $conmanager = new SoapClient("http://$server/$wsdl",
                            array("trace" => 1,
                                "exceptions" => 1,
                                "cache_wsdl" => WSDL_CACHE_NONE,
                                'soap_version' => SOAP_1_1
                            )
            );

            //TODO fix for production
            //if ($result = $conmanager->sendPOD("CON", $username)) {
            if ($result = $conmanager->sendPOD("CON", "12123@fast.interprise.co.za")) {
                error_log("Failed sending disconnect for $username");
            } else {
                error_log("Successfully sent disconnect for $username");
            }
        } catch (Exception $e) {
            
        }

        $data->addChild('id', $accountId);
                
        $offset = $formdata['offset'];
        $search = $formdata['search'];
        $limit = $formdata['limit'];

        $view = new AccountView_ims($viewobject->asXML());
        return $view->detailCallback(array('offset' => $offset, 'search' => $search, 'limit' => $limit));
    }

}

?>
