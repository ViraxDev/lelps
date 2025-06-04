<?php

final class PlaceholderConstants
{
    // Quote placeholders
    const QUOTE_SUMMARY_HTML = '[quote:summary_html]';
    const QUOTE_SUMMARY = '[quote:summary]';
    const QUOTE_DESTINATION_NAME = '[quote:destination_name]';
    const QUOTE_DESTINATION_LINK = '[quote:destination_link]';

    // User placeholders
    const USER_FIRST_NAME = '[user:first_name]';

    /**
     * Get all quote placeholders
     * @return array
     */
    public static function getQuotePlaceholders(): array
    {
        return [
            self::QUOTE_SUMMARY_HTML,
            self::QUOTE_SUMMARY,
            self::QUOTE_DESTINATION_NAME,
            self::QUOTE_DESTINATION_LINK,
        ];
    }

    /**
     * Get all user placeholders
     * @return array
     */
    public static function getUserPlaceholders(): array
    {
        return [
            self::USER_FIRST_NAME,
        ];
    }

    /**
     * Check if text contains any quote placeholders
     * @param string $text
     * @return bool
     */
    public static function hasQuotePlaceholders(string $text): bool
    {
        foreach (self::getQuotePlaceholders() as $placeholder) {
            if (strpos($text, $placeholder) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if text contains any user placeholders
     * @param string $text
     * @return bool
     */
    public static function hasUserPlaceholders(string $text): bool
    {
        foreach (self::getUserPlaceholders() as $placeholder) {
            if (strpos($text, $placeholder) !== false) {
                return true;
            }
        }
        return false;
    }
}
