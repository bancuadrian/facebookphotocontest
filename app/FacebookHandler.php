<?php
/**
 * Created by PhpStorm.
 * User: bancuadrian
 * Date: 5/8/15
 * Time: 4:51 PM
 */

namespace App;

use Facebook\FacebookRedirectLoginHelper;
use Illuminate\Support\Facades\Session;

class FacebookHandler extends FacebookRedirectLoginHelper
{
    protected function storeState($state)
    {
        Session::put('state', $state);
    }

    protected function loadState()
    {
        return $this->state = Session::get('state');
    }
}