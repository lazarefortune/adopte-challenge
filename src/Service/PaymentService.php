<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class PaymentService
{
    public function __construct(
        private HttpClientInterface $client,
        private LoggerInterface     $logger,
        private string              $baseUrl,
        private readonly EntityManagerInterface $em
    ) {}

    private function request(string $method, string $uri, array $payload = []): ?array
    {
        try {
            $response = $this->client->request($method, $this->baseUrl . $uri, [
                'json' => $payload
            ]);

            return $response->toArray();

        } catch (HttpExceptionInterface|TransportExceptionInterface|\Throwable $e) {
            $this->logger->error('Payment API error', [
                'method' => $method,
                'uri' => $uri,
                'payload' => $payload,
                'exception' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Crée un utilisateur sur l’API externe.
     */
    public function createRemoteUser(int $userId, string $cardNumber, int $cvv): ?array
    {
        return $this->request('POST', '/user', [
            'user_id' => $userId,
            'card_number' => $cardNumber,
            'cvv' => $cvv,
        ]);
    }

    /**
     * Met à jour la carte bancaire d'un utilisateur externe.
     */
    public function updateRemoteUser(int $remoteUserId, string $cardNumber, int $cvv): ?array
    {
        return $this->request('PUT', '/user/' . $remoteUserId, [
            'card_number' => $cardNumber,
            'cvv' => $cvv,
        ]);
    }

    /**
     * Crée une transaction sur l’API externe.
     */
    public function createTransaction(int $remoteUserId, float $amount): ?array
    {
        return $this->request('POST', '/transaction', [
            'user_id' => $remoteUserId,
            'amount' => $amount,
        ]);
    }

    public function getRemoteUserId( ?int $getId ) : ?int
    {
        $remoteUser = $this->request('GET', '/user/' . $getId);
        if (!$remoteUser) {
            return null;
        }
        return intval($remoteUser["data"]["user_id"]);
    }

    public function ensureRemoteUser(User $user, string $cardNumber, int $cvv): ?int
    {
        if ($user->getRemotePaymentId()) {
            return $user->getRemotePaymentId();
        }

        $remote = $this->createRemoteUser($user->getId(), $cardNumber, $cvv);

        if (!$remote || !isset($remote['id'])) {
            $fallback = $this->request('GET', '/user/' . $user->getId());
            if (!$fallback || !isset($fallback['data'])) {
                return null;
            }
            $remoteId = (int) $fallback['data']['user_id'];
        } else {
            $remoteId = (int) $remote['id'];
        }

        $user->setRemotePaymentId($remoteId);
        $this->em->persist($user);
        $this->em->flush();

        return $remoteId;
    }

}
