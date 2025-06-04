<?php

require_once __DIR__ . '/../Strategy/PlaceholderReplacementStrategy.php';

final class QuotePlaceholderStrategy implements PlaceholderReplacementStrategy
{
    private $quoteRepository;
    private $siteRepository;
    private $destinationRepository;

    public function __construct(
        QuoteRepository $quoteRepository = null,
        SiteRepository $siteRepository = null,
        DestinationRepository $destinationRepository = null
    ) {
        $this->quoteRepository = $quoteRepository ?: QuoteRepository::getInstance();
        $this->siteRepository = $siteRepository ?: SiteRepository::getInstance();
        $this->destinationRepository = $destinationRepository ?: DestinationRepository::getInstance();
    }

    public function canHandle(string $text, array $data): bool
    {
        return isset($data['quote'])
            && $data['quote'] instanceof Quote
            && PlaceholderConstants::hasQuotePlaceholders($text);
    }

    public function replace(string $text, array $data): string
    {
        $quote = $data['quote'];
        $fullQuote = $this->quoteRepository->getById($quote->id);
        $site = $this->siteRepository->getById($quote->siteId);
        $destination = $this->destinationRepository->getById($quote->destinationId);

        $text = $this->replaceSummaryPlaceholders($text, $fullQuote);
        $text = $this->replaceDestinationPlaceholders($text, $destination);
        $text = $this->replaceDestinationLink($text, $site, $destination, $fullQuote);

        return $text;
    }

    private function replaceSummaryPlaceholders(string $text, Quote $fullQuote): string
    {
        if (strpos($text, PlaceholderConstants::QUOTE_SUMMARY_HTML) !== false) {
            $text = str_replace(
                PlaceholderConstants::QUOTE_SUMMARY_HTML,
                Quote::renderHtml($fullQuote),
                $text
            );
        }

        if (strpos($text, PlaceholderConstants::QUOTE_SUMMARY) !== false) {
            $text = str_replace(
                PlaceholderConstants::QUOTE_SUMMARY,
                Quote::renderText($fullQuote),
                $text
            );
        }

        return $text;
    }

    private function replaceDestinationPlaceholders(string $text, $destination): string
    {
        if (strpos($text, PlaceholderConstants::QUOTE_DESTINATION_NAME) !== false) {
            $text = str_replace(
                PlaceholderConstants::QUOTE_DESTINATION_NAME,
                $destination->countryName,
                $text
            );
        }

        return $text;
    }

    private function replaceDestinationLink(string $text, $site, $destination, Quote $fullQuote): string
    {
        if (strpos($text, PlaceholderConstants::QUOTE_DESTINATION_LINK) !== false) {
            $destinationLink = $site->url . '/' . $destination->countryName . '/quote/' . $fullQuote->id;
            $text = str_replace(
                PlaceholderConstants::QUOTE_DESTINATION_LINK,
                $destinationLink,
                $text
            );
        }

        return $text;
    }
}