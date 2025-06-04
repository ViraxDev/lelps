<?php

require_once __DIR__ . '/../Helper/PlaceholderConstants.php';
require_once __DIR__ . '/../Helper/Strategy/QuotePlaceholderStrategy.php';
require_once __DIR__ . '/../Helper/Strategy/UserPlaceholderStrategy.php';

final class PlaceholderReplacer
{
    private $strategies;

    public function __construct(array $strategies = null)
    {
        $this->strategies = $strategies ?: $this->getDefaultStrategies();
    }

    public function replacePlaceholders(string $text, array $data): string
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->canHandle($text, $data)) {
                $text = $strategy->replace($text, $data);
            }
        }

        return $text;
    }

    private function getDefaultStrategies(): array
    {
        return [
            new QuotePlaceholderStrategy(),
            new UserPlaceholderStrategy(),
        ];
    }
}
