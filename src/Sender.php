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

use Hyperf\Utils\Traits\Macroable;
use HyperfExt\Sms\Contracts\SenderInterface;
use HyperfExt\Sms\Contracts\SmsMessageInterface;
use HyperfExt\Sms\Events\SmsMessageSending;
use HyperfExt\Sms\Events\SmsMessageSent;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class Sender implements SenderInterface
{
    use Macroable;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \HyperfExt\Sms\Contracts\DriverInterface
     */
    protected $driver;

    /**
     * @var
     */
    protected $container;

    /**
     * @var \Psr\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;

    public function __construct(
        string $name,
        array $config,
        ContainerInterface $container
    ) {
        $this->name = $name;
        $this->driver = make($config['driver'], ['config' => $config['config'] ?? []]);
        $this->eventDispatcher = $container->get(EventDispatcherInterface::class);
        $this->container = $container;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function send(SmsMessageInterface $message): array
    {
        $message = clone $message;

        call_user_func([$message, 'build'], $this);

        $this->eventDispatcher->dispatch(new SmsMessageSending($message));

        $response = $this->driver->send($message);

        $this->eventDispatcher->dispatch(new SmsMessageSent($message));

        return $response;
    }
}