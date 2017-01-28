<?php

namespace Dgame\GitBot\Github;

/**
 * Class Label
 * @package Dgame\GitBot\Github
 */
final class Label
{
    /**
     * @var array
     */
    private $label = [];

    /**
     * Label constructor.
     *
     * @param array $label
     */
    public function __construct(array $label)
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->label['url'];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->label['name'];
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->label['color'];
    }

    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return (bool) $this->label['default'];
    }
}