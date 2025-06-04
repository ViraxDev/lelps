<?php

final class PlaceholderReplacer
{
    private $applicationContext;

    public function __construct(ApplicationContext $applicationContext = null)
    {
        $this->applicationContext = $applicationContext ?: ApplicationContext::getInstance();
    }

    public function replacePlaceholders(string $text, array $data): string
    {
        $quote = $this->getQuote($data);
        if ($quote) {
            $text = $this->replaceQuotePlaceholders($text, $quote);
        }

        $user = $this->getUser($data);
        if ($user) {
            $text = $this->replaceUserPlaceholders($text, $user);
        }

        return $text;
    }

    /**
     * @param array $data
     * @return Quote|null
     */
    private function getQuote(array $data)
    {
        return (isset($data['quote']) && $data['quote'] instanceof Quote) ? $data['quote'] : null;
    }

    /**
     * @param array $data
     * @return User|null
     */
    private function getUser(array $data)
    {
        return (isset($data['user']) && $data['user'] instanceof User)
            ? $data['user']
            : $this->applicationContext->getCurrentUser();
    }

    /**
     * @param string $text
     * @param Quote $quote
     * @return string
     */
    private function replaceQuotePlaceholders(string $text, Quote $quote): string
    {
        $_quoteFromRepository = QuoteRepository::getInstance()->getById($quote->id);
        $usefulObject = SiteRepository::getInstance()->getById($quote->siteId);
        $destinationOfQuote = DestinationRepository::getInstance()->getById($quote->destinationId);

        if (strpos($text, '[quote:destination_link]') !== false) {
            $destination = DestinationRepository::getInstance()->getById($quote->destinationId);
        }

        $containsSummaryHtml = strpos($text, '[quote:summary_html]');
        $containsSummary = strpos($text, '[quote:summary]');

        if ($containsSummaryHtml !== false || $containsSummary !== false) {
            if ($containsSummaryHtml !== false) {
                $text = str_replace(
                    '[quote:summary_html]',
                    Quote::renderHtml($_quoteFromRepository),
                    $text
                );
            }
            if ($containsSummary !== false) {
                $text = str_replace(
                    '[quote:summary]',
                    Quote::renderText($_quoteFromRepository),
                    $text
                );
            }
        }

        if (strpos($text, '[quote:destination_name]') !== false) {
            $text = str_replace('[quote:destination_name]', $destinationOfQuote->countryName, $text);
        }

        if (isset($destination)) {
            $text = str_replace('[quote:destination_link]', $usefulObject->url . '/' . $destination->countryName . '/quote/' . $_quoteFromRepository->id, $text);
        } else {
            $text = str_replace('[quote:destination_link]', '', $text);
        }

        return $text;
    }

    private function replaceUserPlaceholders(string $text, User $user): string
    {
        if (strpos($text, '[user:first_name]') !== false) {
            $text = str_replace('[user:first_name]', ucfirst(mb_strtolower($user->firstname)), $text);
        }

        return $text;
    }
}
