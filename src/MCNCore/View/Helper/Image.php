<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCNCore\View\Helper;
use Zend\View\Helper\AbstractHelper,
    MCN\Service\ImageLibrary as ImageLibraryService;

/**
 *
 */
class Image extends AbstractHelper
{
    /**
     * @var \MCN\Service\ImageLibrary
     */
    protected $service;

    /**
     * @param ImageLibraryService $service
     */
    public function __construct(ImageLibraryService $service)
    {
        $this->service = $service;
    }

    /**
     * @param mixed  $object
     * @param string $resolution
     * @param string $alt
     * @param string $position
     *
     * @return string
     * @throws \Exception
     */
    public function render($object, $resolution, $alt = '', $position = 'default')
    {
        $images = $object->getImages();

        // filter the images so we only get the ones we want
        $result = $images->filter(function($image) use ($position) {

            return $image->getTargetPosition() == $position;
        });

        $count = count($result);

        if ($count == 0) {

            return null;
        }

        if ($count != 1) {

            throw new \Exception('This function only supports rendering a single image.');
        }

        // make it more convenient to access the image
        $image = $result[0];

        if (! $image->hasResolution($resolution)) {

            $this->service->resize($image, $resolution);
        }

        // If no alt is specified then default to the image name when uploaded
        $alt = empty($alt) ? $object->getName() : $alt;

        return sprintf('<img src="%s" alt="%s"/>', $image->getResolution($resolution), $alt);
    }

    /**
     * @param string $object
     * @param string $position
     *
     * @return bool
     */
    public function hasImage($object, $position = 'default')
    {
        $images = $object->getImages();

        // filter the images so we only get the ones we want
        $result = $images->filter(function($image) use ($position) {

            return $image->getTargetPosition() == $position;
        });

        return count($result) > 0;
    }

    /**
     * @param string $image
     * @param string $resolution
     * @return mixed
     */
    public function getPublicUrl($image, $resolution)
    {
        if (! $image->hasResolution($resolution)) {

            $this->service->resize($image, $resolution);
        }

        return $image->getResolution($resolution);
    }
}