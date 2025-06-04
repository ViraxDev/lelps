<?php

interface PlaceholderReplacementStrategy
{
    /**
     * Check if this strategy can handle the given data and text
     * @param string $text
     * @param array $data
     * @return bool
     */
    public function canHandle(string $text, array $data): bool;

    /**
     * Replace placeholders in text with actual values
     * @param string $text
     * @param array $data
     * @return string
     */
    public function replace(string $text, array $data): string;
}
