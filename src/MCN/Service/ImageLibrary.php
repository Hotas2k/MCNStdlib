<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCN\Service;
use Doctrine\ORM\EntityManager,
    MCN\Entity\Image as ImageEntity;

class ImageLibrary
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var ImageLibraryOptions
     */
    protected $options;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em, ImageLibraryOptions $options = null)
    {
        $this->em      = $em;
        $this->options = ($options === null) ? new ImageLibraryOptions() : $options;
    }

    /**
     * @return \MCNCore\Object\Entity\Repository
     */
    protected function getRepository()
    {
        return $this->em->get('ImageLibrary\Entity\Image');
    }

    /**
     * @param $target
     * @param mixed $resolution
     * @return array
     */
    protected function getTargetConfig($target, $resolution)
    {
        if (
            is_string($resolution) &&
            isSet($this->options->getTargetConfig()[$target]) &&
            isSet($this->options->getTargetConfig()[$target][$resolution])
        ) {

            if (! isSet($this->options->getTargetConfig()[$target][$resolution]['resolution'])) {

                throw new Exception\InvalidArgumentException(
                    sprintf('No resolution specified for image with target "%s" and resolution "%s"', $target, $resolution)
                );
            }

            return array_merge(
                $this->options->getDefaultOptions(),
                $this->options->getTargetConfig()[$target][$resolution]
            );
        }

        if (! is_array($resolution)) {

            $resolution = explode('x', $resolution);

            if (count($resolution) != 2) {

                throw new Exception\InvalidArgumentException(
                    sprintf('Unknown resolution specified "%s" for taget "%s".', $resolution, $target)
                );
            }
        }

        return array_merge(
            $this->options->getDefaultOptions(),
            array(
                'resolution' => $resolution
            )
        );
    }

    public function resize(ImageEntity $image, $resolution)
    {
        // Path to the raw image
        $raw_image = $this->options->getRawImagePath() . '/' . $image->getResolution('raw');

        $options = $this->getTargetConfig($image->getTargetType(), $resolution);

        list($width, $height) = $options['resolution'];

        $imagick = new \Imagick($raw_image);
        $imagick->setImageFormat('jpg');
        $imagick->setCompressionQuality(100);

        if ($imagick->getImageHeight() < $height || $imagick->getImageWidth() < $width) {

            if ($options['resizeIfSmaller']) {

                $imagick->thumbnailImage($width, $height, $options['bestFit']);
            }

        } else {

            $imagick->thumbnailImage($width, $height, $options['bestFit']);
        }

        $path = $this->options->getPublicImagePath() . '/' . md5(microtime(true)) . '.jpeg';

        $imagick->writeImage('public/' . $path);
        $image->addResolution($resolution, $path);

        $this->em->flush();
    }
}