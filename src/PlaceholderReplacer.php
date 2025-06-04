<?php

require_once __DIR__ . '/../src/PlaceholderConstants.php';

final class PlaceholderReplacer
{
    private $applicationContext;
    private $quoteRepository;
    private $siteRepository;
    private $destinationRepository;

    public function __construct(
        ApplicationContext $applicationContext = null,
        QuoteRepository $quoteRepository = null,
        SiteRepository $siteRepository = null,
        DestinationRepository $destinationRepository = null
    ) {
        $this->applicationContext = $applicationContext ?: ApplicationContext::getInstance();
        $this->quoteRepository = $quoteRepository ?: QuoteRepository::getInstance();
        $this->siteRepository = $siteRepository ?: SiteRepository::getInstance();
        $this->destinationRepository = $destinationRepository ?: DestinationRepository::getInstance();
    }

    public function replacePlaceholders(string $text, array $data): string
    {
        $quote = $this->getQuote($data);
        if ($quote && PlaceholderConstants::hasQuotePlaceholders($text)) {
            $text = $this->replaceQuotePlaceholders($text, $quote);
        }

        $user = $this->getUser($data);
        if ($user && PlaceholderConstants::hasUserPlaceholders($text)) {
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
    private function getUser(array $data): User
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
        $fullQuote = $this->quoteRepository->getById($quote->id);
        $site = $this->siteRepository->getById($quote->siteId);
        $destination = $this->destinationRepository->getById($quote->destinationId);

        $needsDestinationLink = strpos($text, PlaceholderConstants::QUOTE_DESTINATION_LINK) !== false;

        $containsSummaryHtml = strpos($text, PlaceholderConstants::QUOTE_SUMMARY_HTML) !== false;
        $containsSummary = strpos($text, PlaceholderConstants::QUOTE_SUMMARY) !== false;

        if ($containsSummaryHtml || $containsSummary) {
            if ($containsSummaryHtml) {
                $text = str_replace(
                    PlaceholderConstants::QUOTE_SUMMARY_HTML,
                    Quote::renderHtml($fullQuote),
                    $text
                );
            }
            if ($containsSummary) {
                $text = str_replace(
                    PlaceholderConstants::QUOTE_SUMMARY,
                    Quote::renderText($fullQuote),
                    $text
                );
            }
        }

        if (strpos($text, PlaceholderConstants::QUOTE_DESTINATION_NAME) !== false) {
            $text = str_replace(PlaceholderConstants::QUOTE_DESTINATION_NAME, $destination->countryName, $text);
        }

        if ($needsDestinationLink) {
            $destinationLink = $site->url . '/' . $destination->countryName . '/quote/' . $fullQuote->id;
            $text = str_replace(PlaceholderConstants::QUOTE_DESTINATION_LINK, $destinationLink, $text);
        }

        return $text;
    }

    private function replaceUserPlaceholders(string $text, User $user): string
    {
        if (strpos($text, PlaceholderConstants::USER_FIRST_NAME) !== false) {
            $text = str_replace(PlaceholderConstants::USER_FIRST_NAME, ucfirst(mb_strtolower($user->firstname)), $text);
        }

        return $text;
    }
}