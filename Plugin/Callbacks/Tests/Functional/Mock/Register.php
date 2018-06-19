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

namespace Apisearch\Plugin\Callbacks\Tests\Functional\Mock;

/**
 * Class Register.
 */
class Register
{
    /**
     * @var array
     *
     * Register
     */
    private $register = [];

    /**
     * Add.
     *
     * @param array $element
     */
    public function add(array $element)
    {
        $this->register[] = $element;
    }

    /**
     * Get register.
     *
     * @return array
     */
    public function get(): array
    {
        return $this->register;
    }

    /**
     * Flush register.
     */
    public function flush()
    {
        $this->register = [];
    }
}
