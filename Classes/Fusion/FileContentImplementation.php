<?php
namespace Carbon\IncludeAssets\Fusion;

/*
 * This file is part of the Carbon.IncludeAssets package.
 *
 * (c) Handcrafted with love by Jon Uhlmann
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */
use Neos\Flow\Annotations as Flow;
use Neos\Fusion\FusionObjects\AbstractFusionObject;
use Neos\Utility\Files;


class FileContentImplementation extends AbstractFusionObject
{

    /**
     * The location of the resource, must be a resource://... URI
     *
     * @return string
     */
    public function getPath()
    {
        return $this->fusionValue('path');
    }

    /**
     * If specified, this resource object is used instead of the path and package information
     *
     * @return Resource
     */
    public function getResource()
    {
        return $this->fusionValue('resource');
    }

    /**
     * Returns the file content of a resource. Fails silent
     *
     * @return string | boolean
     */
    public function evaluate()
    {
        $resource = $this->getResource();

        if ($resource) {
            return stream_get_contents($resource->getStream());
        }

        $path = $this->getPath();
        if ($path) {
            return Files::getFileContents($path);
        }

        return false;
    }
}
