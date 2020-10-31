<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-ext/sms.
 *
 * @link     https://github.com/hyperf-ext/sms
 * @contact  eric@zhu.email
 * @license  https://github.com/hyperf-ext/sms/blob/master/LICENSE
 */
namespace HyperfExt\Sms;

use Hyperf\AsyncQueue\Job;
use HyperfExt\Sms\Contracts\SmsMessageInterface;

class QueuedSmsMessageJob extends Job
{
    /**
     * @var \HyperfExt\Sms\Contracts\SmsMessageInterface
     */
    public $message;

    public function __construct(SmsMessageInterface $message)
    {
        $this->message = $message;
    }

    public function handle()
    {
        $this->message->send();
    }
}
