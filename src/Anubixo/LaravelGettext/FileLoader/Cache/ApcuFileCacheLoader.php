<?php
/**
 * Created by PhpStorm.
 * User: aaflalo
 * Date: 18-03-01
 * Time: 10:23
 */

namespace Anubixo\LaravelGettext\FileLoader\Cache;

use Symfony\Component\Translation\Exception\InvalidResourceException;
use Symfony\Component\Translation\Loader\FileLoader;

class ApcuFileCacheLoader extends FileLoader
{

    /**
     * @var FileLoader
     */
    private FileLoader $underlyingFileLoader;

    /**
     * ApcuFileCacheLoader constructor.
     *
     * @param FileLoader $underlyingFileLoader
     */
    public function __construct(FileLoader $underlyingFileLoader)
    {
        $this->underlyingFileLoader = $underlyingFileLoader;
    }


    /**
     * @param string $resource
     *
     * @return array
     *
     * @throws InvalidResourceException if stream content has an invalid format
     */
    protected function loadResource(string $resource): array
    {
        if (!extension_loaded('apcu')) {
            return $this->underlyingFileLoader->loadResource($resource);
        }

        return $this->cachedMessages($resource);
    }

    /**
     * Calculate the checksum for the file
     *
     * @param $resource
     *
     * @return string
     */
    private function checksum($resource): string
    {
        return filemtime($resource) . '-' . filesize($resource);
    }

    /**
     * Checksum saved in cache
     *
     * @param $resource
     *
     * @return string
     */
    private function cacheChecksum($resource): string
    {
        return apcu_fetch($resource . '-checksum');
    }

    /**
     * Set the cache checksum
     *
     * @param $resource
     * @param $checksum
     *
     * @return array|bool
     */
    private function setCacheChecksum($resource, $checksum): bool|array
    {
        return apcu_store($resource . '-checksum', $checksum);
    }

    /**
     * Return the cached messages
     *
     * @param $resource
     *
     * @return array
     */
    private function cachedMessages($resource): array
    {
        if ($this->cacheChecksum($resource) == ($currentChecksum = $this->checksum($resource))) {
            return apcu_fetch($resource . '-messages');
        }

        $messages = $this->underlyingFileLoader->loadResource($resource);

        apcu_store($resource . '-messages', $messages);
        $this->setCacheChecksum($resource, $currentChecksum);

        return $messages;
    }
}
