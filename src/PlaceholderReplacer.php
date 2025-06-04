<?php

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

        $needsDestinationLink = strpos($text, '[quote:destination_link]') !== false;

        $containsSummaryHtml = strpos($text, '[quote:summary_html]') !== false;
        $containsSummary = strpos($text, '[quote:summary]') !== false;

        if ($containsSummaryHtml || $containsSummary) {
            if ($containsSummaryHtml) {
                $text = str_replace(
                    '[quote:summary_html]',
                    Quote::renderHtml($fullQuote),
                    $text
                );
            }
            if ($containsSummary) {
                $text = str_replace(
                    '[quote:summary]',
                    Quote::renderText($fullQuote),
                    $text
                );
            }
        }

        if (strpos($text, '[quote:destination_name]') !== false) {
            $text = str_replace('[quote:destination_name]', $destination->countryName, $text);
        }

        if ($needsDestinationLink) {
            $destinationLink = $site->url . '/' . $destination->countryName . '/quote/' . $fullQuote->id;
            $text = str_replace('[quote:destination_link]', $destinationLink, $text);
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