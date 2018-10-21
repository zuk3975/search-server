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

namespace Apisearch\Server\Domain;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Consumer.
 */
abstract class Consumer
{
    /**
     * Log output.
     *
     * @param OutputInterface $output
     * @param string          $type
     * @param bool            $success
     * @param string          $message
     * @param float           $from
     * @param bool            $forceMessage
     */
    protected function logOutput(
        OutputInterface $output,
        string $type,
        bool $success,
        string $message,
        float $from,
        bool $forceMessage = false
    ) {
        $to = microtime(true);
        $elapsedTime = (int) (($to - $from) * 1000);
        if (0 === $elapsedTime) {
            $elapsedTime = '<1';
        }

        $string = true === $success
            ? "\033[01;32mOk  \033[0m"
            : "\033[01;31mFail\033[0m";
        $string .= " $type ";
        $string .= "(\e[00;37m".$elapsedTime.' ms, '.((int) (memory_get_usage() / 1000000))." MB\e[0m)";
        if (false === $success || $forceMessage) {
            $string .= " - \e[00;37m".$message."\e[0m";
        }

        $output->writeln($string);
    }
}
