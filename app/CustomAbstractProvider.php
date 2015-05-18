<?php
/**
 * Created by PhpStorm.
 * User: bancuadrian
 * Date: 5/18/15
 * Time: 2:00 PM
 */

namespace App;


use Laravel\Socialite\Two\AbstractProvider;

abstract class CustomAbstractProvider extends AbstractProvider{

    /**
     * The auth type
     *
     * @var string
     */
    protected $auth_type = null;

    /**
     * Get the GET parameters for the code request.
     *
     * @param  string  $state
     * @return array
     */
    protected function getCodeFields($state)
    {
        $r = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
            'scope' => $this->formatScopes($this->scopes, $this->scopeSeparator),
            'state' => $state,
            'response_type' => 'code',
        ];

        if($this->auth_type)
        {
            $r['auth_type'] = $this->auth_type;
        }

        return $r;
    }

    /**
     * @return string
     */
    public function getAuthType()
    {
        return $this->auth_type;
    }

    /**
     * @param string $auth_type
     *
     * @return $this
     */
    public function setAuthType($auth_type)
    {
        $this->auth_type = $auth_type;

        return $this;
    }

    /**
     * Redirect the user of the application to the provider's authentication screen.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getRedirectLink()
    {
        $this->request->getSession()->set(
            'state', $state = sha1(time().$this->request->getSession()->get('_token'))
        );

        return $this->getAuthUrl($state);
    }

}