<?php

namespace Charcoal\Support\Model;

use RuntimeException;

// From 'charcoal-core'
use Charcoal\Model\Service\MetadataLoader;
use Charcoal\Model\MetadataInterface;

/**
 * Provides describable metadata features.
 */
trait ManufacturableMetadataTrait
{
    /**
     * Store the metadata loader.
     *
     * @var MetadataLoader
     */
    protected $metadataLoader;

    /**
     * Set a metadata loader.
     *
     * @param MetadataLoader $loader The metadata loader.
     * @return self
     */
    protected function setMetadataLoader(MetadataLoader $loader)
    {
        $this->metadataLoader = $loader;

        return $this;
    }

    /**
     * Retrieve the metadata loader.
     *
     * @throws RuntimeException If the metadata loader was not previously set.
     * @return MetadataLoader
     */
    public function metadataLoader()
    {
        if (!isset($this->metadataLoader)) {
            throw new RuntimeException(
                sprintf('Metadata Loader is not defined for "%s"', get_class($this))
            );
        }

        return $this->metadataLoader;
    }

    /**
     * Load a metadata file.
     *
     * @param  string $metadataIdent A metadata file path or namespace.
     * @return MetadataInterface
     */
    protected function loadMetadata($metadataIdent)
    {
        $metadataLoader = $this->metadataLoader();
        $metadata = $metadataLoader->load($metadataIdent, $this->createMetadata());

        return $metadata;
    }

    /**
     * @return MetadataInterface
     */
    abstract protected function createMetadata();
}
