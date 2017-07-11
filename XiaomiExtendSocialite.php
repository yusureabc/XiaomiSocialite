<?php

namespace yusureabc\XiaomiSocialite;

use SocialiteProviders\Manager\SocialiteWasCalled;

class XiaomiExtendSocialite
{
    /**
     * Register the provider.
     *
     * @param \SocialiteProviders\Manager\SocialiteWasCalled $socialiteWasCalled
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite(
            'xiaomi', __NAMESPACE__.'\Provider'
        );
    }
}
