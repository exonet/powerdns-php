<?php

namespace Exonet\Powerdns\Resources;

use Exonet\Powerdns\Exceptions\InvalidMetaKind;
use Exonet\Powerdns\Meta as MetaParent;
use Exonet\Powerdns\MetaType;
use LogicException;

class Meta
{
    /**
     * @var string|null The meta kind.
     */
    protected $kind;

    /**
     * @var array|string The meta data.
     */
    protected $data;

    /**
     * @var MetaParent|null The meta parent class required to make API calls.
     */
    private $metaParent;

    /**
     * Meta resource constructor.
     *
     * @param string|null     $kind       The meta kind.
     * @param array|string    $data       The meta data.
     * @param MetaParent|null $metaParent The meta parent class required to make API calls.
     *
     * @throws InvalidMetaKind When the $kind is not a valid meta kind.
     */
    public function __construct(?string $kind = null, $data = [], MetaParent $metaParent = null)
    {
        if (!is_null($kind)) {
            $this->setKind($kind);
        }

        if (!empty($data)) {
            $this->setData($data);
        }

        $this->metaParent = $metaParent;
    }

    /**
     * Set the meta kind.
     *
     * @param string $kind The meta kind.
     *
     * @throws InvalidMetaKind When the $kind is not a valid meta kind.
     *
     * @return $this The current instance.
     */
    public function setKind(string $kind): self
    {
        // If $kind start with an X-, it's a custom meta type.
        if (strpos($kind, 'X-') === 0) {
            $this->kind = $kind;

            return $this;
        }

        // Check if the $kind is a valid meta type.
        $kindConstant = MetaType::class.'::'.str_replace('-', '_', strtoupper($kind));
        if (defined($kindConstant)) {
            $this->kind = constant($kindConstant);

            return $this;
        }

        throw new InvalidMetaKind(sprintf('[%s] is not a valid meta kind.', $kind));
    }

    /**
     * Get the meta kind.
     *
     * @return string|null The meta kind.
     */
    public function getKind(): ?string
    {
        return $this->kind;
    }

    /**
     * Set the meta data.
     *
     * @param array|string $data The meta data.
     *
     * @return $this The current instance.
     */
    public function setData($data): self
    {
        $this->data = is_string($data) ? [$data] : $data;

        return $this;
    }

    /**
     * Get the meta data.
     *
     * @return array|null The meta data.
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * Save this meta data.
     *
     * @throws LogicException When no meta parent is set.
     *
     * @return bool True on success, false on failure.
     */
    public function save(): bool
    {
        if ($this->metaParent === null) {
            throw new LogicException('Can not save meta data, no meta parent set.');
        }

        return $this->metaParent->update($this);
    }

    /**
     * Delete this meta data.
     *
     * @throws LogicException When no meta parent is set.
     *
     * @return bool True on success, false on failure.
     */
    public function delete(): bool
    {
        if ($this->metaParent === null) {
            throw new LogicException('Can not save meta data, no meta parent set.');
        }

        return $this->metaParent->delete($this);
    }
}
