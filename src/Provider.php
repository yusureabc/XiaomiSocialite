<?php

namespace Yusureabc\XiaomiSocialite;

use Laravel\Socialite\Two\ProviderInterface;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

/**
 * 新手引导         https://dev.mi.com/docs/passport/user-guide/
 * OAuth2.0        https://dev.mi.com/docs/passport/oauth2/
 * 小米帐号开放API  https://dev.mi.com/docs/passport/open-api/
 * @package Yusureabc\XiaomiSocialite
 */
class Provider extends AbstractProvider implements ProviderInterface
{
    /**
     * Unique Provider Identifier.
     */
    const IDENTIFIER = 'XIAOMI';
    
    
    
    /**
     * {@inheritdoc}.
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase( 'https://account.xiaomi.com/oauth2/authorize', $state );
    }
    
    /**
     * {@inheritdoc}.
     * https://account.xiaomi.com/oauth2/authorize?client_id=2882303761517596640&response_type=code&redirect_uri=http%3A%2F%2Ftestthirdparty.yeelight.com%2FgetToken.php&state=state
     */
    protected function buildAuthUrlFromBase($url, $state)
    {
        $query = http_build_query($this->getCodeFields($state), '', '&', $this->encodingType);

        return $url . '?' . $query;
    }
    
    /**
     * {@inheritdoc}.
     */
    protected function getCodeFields($state = null)
    {
        return [
            'client_id'     => $this->clientId,
            'response_type' => 'code',
            'redirect_uri'  => $this->redirectUrl,
            'state'         => $state,
        ];
    }
    
    /**
     * {@inheritdoc}.
     * @see \Laravel\Socialite\Two\AbstractProvider::getTokenUrl()
     */
    protected function getTokenUrl()
    {
        return 'https://account.xiaomi.com/oauth2/token';
    }
    
    /**
     * {@inheritdoc}.
     * @see \Laravel\Socialite\Two\AbstractProvider::getUserByToken()
     */
    protected function getUserByToken($token)
    {
        
        $response = $this->getHttpClient()->get('https://open.account.xiaomi.com/user/profile', [
            'query' => [
                'clientId' => $this->clientId,
                'token'    => $token,
            ],
        ]);
        
        $contents = json_decode($response->getBody()->getContents(), true);
        return $contents['data'];
    }
    
    /**
     * {@inheritdoc}.
     * @see \Laravel\Socialite\Two\AbstractProvider::mapUserToObject()
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id'       => $user['userId'],
            'nickname' => $user['miliaoNick'],
            'avatar'   => $user['miliaoIcon_320'],
        ]);
    }
    
    /**
     * {@inheritdoc}.
     * @see \Laravel\Socialite\Two\AbstractProvider::getTokenFields()
     */
    protected function getTokenFields($code)
    {
        return [
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri'  => $this->redirectUrl,
            'token_type'    => 'mac',
        ];
    }
    
    /**
     * {@inheritdoc}.
     */
    public function getAccessTokenResponse($code)
    {
        $response = $this->getHttpClient()->get($this->getTokenUrl(), [
            'query' => $this->getTokenFields($code),
        ]);
        
        $contents = str_replace("&&&START&&&","", $response->getBody()->getContents() );
        
        $this->credentialsResponseBody = json_decode( $contents, true );
        
        return $this->credentialsResponseBody;
    }
}