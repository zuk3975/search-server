<?php

/*
 * This file is part of the Apisearch Server
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

declare(strict_types=1);

namespace Apisearch\Plugin\NewRelic\Listener;

use Ekino\NewRelicBundle\NewRelic\NewRelicInteractorInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class RequestListener.
 */
class InteractorListener
{
    /**
     * New Relic interactor.
     *
     * @var NewRelicInteractorInterface
     */
    private $interactor;

    /**
     * App name.
     *
     * @var string
     */
    private $appName;

    /**
     * License.
     *
     * @var string
     */
    private $license;

    /**
     * RequestListener constructor.
     *
     * @param NewRelicInteractorInterface $interactor
     * @param string                      $appName
     * @param string                      $licence
     */
    public function __construct(
        NewRelicInteractorInterface $interactor,
        string $appName,
        string $licence
    ) {
        $this->interactor = $interactor;
        $this->appName = $appName;
        $this->license = $licence;
    }

    /**
     * On kernel request.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $this
            ->interactor
            ->startTransaction(
                $this->appName,
                $this->license
            );
    }

    /**
     * On kernel response.
     *
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $this
            ->interactor
            ->endTransaction();
    }
}
