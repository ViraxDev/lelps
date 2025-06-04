<?php

require_once __DIR__ . '/../Strategy/PlaceholderReplacementStrategy.php';

final class UserPlaceholderStrategy implements PlaceholderReplacementStrategy
{
    private $applicationContext;

    public function __construct(ApplicationContext $applicationContext = null)
    {
        $this->applicationContext = $applicationContext ?: ApplicationContext::getInstance();
    }

    public function canHandle(string $text, array $data): bool
    {
        $user = $this->getUser($data);
        return $user !== null && PlaceholderConstants::hasUserPlaceholders($text);
    }

    public function replace(string $text, array $data): string
    {
        $user = $this->getUser($data);

        if (strpos($text, PlaceholderConstants::USER_FIRST_NAME) !== false) {
            $text = str_replace(
                PlaceholderConstants::USER_FIRST_NAME,
                ucfirst(mb_strtolower($user->firstname)),
                $text
            );
        }

        return $text;
    }

    private function getUser(array $data)
    {
        return (isset($data['user']) && $data['user'] instanceof User)
            ? $data['user']
            : $this->applicationContext->getCurrentUser();
    }
}
