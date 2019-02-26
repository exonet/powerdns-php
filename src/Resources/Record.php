<?php

declare(strict_types=1);

namespace Exonet\Powerdns\Resources;

class Record
{
    /**
     * @var string The content of this record.
     */
    private $content;

    /**
     * @var bool Whether or not this record is disabled.
     */
    private $disabled;

    /**
     * @var bool If set to true, the server will find the matching reverse zone and create a PTR there. Existing PTR
     *           records are replaced. If no matching reverse Zone, an error is thrown. Only valid in client bodies, only
     *           valid for A and AAAA types. Not returned by the server.
     */
    private $setPtr;

    /**
     * Record constructor.
     *
     * @param string $content Optional content to set.
     */
    public function __construct(?string $content = null)
    {
        if ($content !== null) {
            $this->setContent($content);
        }
    }

    /**
     * Get the record content.
     *
     * @return string The content.
     */
    public function getContent() : string
    {
        return $this->content;
    }

    /**
     * Set the content.
     *
     * @param string $content The content.
     *
     * @return $this The current Record instance.
     */
    public function setContent(string $content) : self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get the record disabled state.
     *
     * @return bool Whether or not this record is disabled.
     */
    public function isDisabled() : bool
    {
        return $this->disabled ?? false;
    }

    /**
     * Set the record disabled state.
     *
     * @param bool $disabled Whether or not this record is disabled.
     *
     * @return $this The current Record instance.
     */
    public function setDisabled(bool $disabled) : self
    {
        $this->disabled = $disabled;

        return $this;
    }

    /**
     * Get the record set PTR state.
     *
     * @return bool Whether or not a PTR record must be created when PATCH/POST this record.
     */
    public function isSetPtr() : bool
    {
        return $this->setPtr ?? false;
    }

    /**
     * Set the record set PTR state.
     *
     * @param bool $setPtr Whether or not a PTR record must be created when PATCH/POST this record.
     *
     * @return $this The current Record instance.
     */
    public function setSetPtr(bool $setPtr) : self
    {
        $this->setPtr = $setPtr;

        return $this;
    }
}
