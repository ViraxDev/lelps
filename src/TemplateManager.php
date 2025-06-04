<?php

require_once __DIR__ . '/../src/Helper/PlaceholderReplacer.php';

final class TemplateManager
{
    private $placeholderReplacer;

    public function __construct()
    {
        $this->placeholderReplacer = new PlaceholderReplacer();
    }

    /**
     * @param Template $tpl
     * @param array $data
     * @return Template
     */
    public function getTemplateComputed(Template $tpl, array $data): Template
    {
        $replaced = clone($tpl);
        $replaced->subject = $this->placeholderReplacer->replacePlaceholders($replaced->subject, $data);
        $replaced->content = $this->placeholderReplacer->replacePlaceholders($replaced->content, $data);

        return $replaced;
    }
}
