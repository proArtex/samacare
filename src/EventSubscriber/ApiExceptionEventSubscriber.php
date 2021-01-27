<?php declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ApiExceptionEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var string[]
     */
    private $firewalls;

    /**
     * @var FirewallMap
     */
    private $firewallMap;

    /**
     * @var bool
     */
    private $isDebugEnabled;

    public function __construct(array $firewalls, FirewallMap $firewallMap, bool $isDebugEnabled)
    {
        $this->firewalls = $firewalls;
        $this->firewallMap = $firewallMap;
        $this->isDebugEnabled = $isDebugEnabled;
    }

    public static function getSubscribedEvents()
    {
        return [
            ExceptionEvent::class => ['sendJsonResponse', -1],
        ];
    }

    public function sendJsonResponse(ExceptionEvent $event): void
    {
        $firewallConfig = $this->firewallMap->getFirewallConfig($event->getRequest());
        $firewallName = $firewallConfig->getName();

        if (!in_array($firewallName, $this->firewalls, true)) {
            return;
        }

        $exception = $event->getThrowable();

        $response = $this->isDebugEnabled
            ? $this->getDebugResponse($exception)
            : $this->getCommonResponse($exception);

        $event->setResponse($response);
    }

    private function getDebugResponse(Throwable $exception): JsonResponse
    {
        $errors = [
            [
                'message' => $exception->getMessage(),
                'exception' => get_class($exception),
                'trace' => $exception->getTrace()
            ]
        ];

        $code = $exception instanceof HttpException
            ? $exception->getStatusCode()
            : Response::HTTP_INTERNAL_SERVER_ERROR;

        return new JsonResponse(['errors' => $errors], $code);
    }

    private function getCommonResponse(Throwable $exception): JsonResponse
    {
        if ($exception instanceof HttpException) {
            $errors = [$exception->getMessage()];
            $code = $exception->getStatusCode();
        } else {
            $errors = ['Something went wrong'];
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        return new JsonResponse(['errors' => $errors], $code);
    }
}
