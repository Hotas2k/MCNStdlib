<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCN\Service;
use Zend\Stdlib\AbstractOptions;

/**
 *
 */
class ImageLibraryOptions extends AbstractOptions
{
    /**
     * Relative path to the folder containing the raw images
     *
     * @var string
     */
    protected $raw_image_path = null;

    /**
     * Relative public image path
     *
     * @var string
     */
    protected $public_image_path = null;

    /**
     * @var array
     */
    protected $target_config = array();

    /**
     * @var array
     */
    protected $default_options = array(
        'bestFit'         => false,
        'resizeIfSmaller' => false
    );

    /**
     * @param $default_options
     */
    public function setDefaultOptions($default_options)
    {
        $this->default_options = $default_options;
    }

    /**
     * @return array
     */
    public function getDefaultOptions()
    {
        return $this->default_options;
    }

    /**
     * @param string $public_image_path
     */
    public function setPublicImagePath($public_image_path)
    {
        $this->public_image_path = $public_image_path;
    }

    /**
     * @return string
     */
    public function getPublicImagePath()
    {
        return $this->public_image_path;
    }

    /**
     * @param string $raw_image_path
     */
    public function setRawImagePath($raw_image_path)
    {
        $this->raw_image_path = $raw_image_path;
    }

    /**
     * @return string
     */
    public function getRawImagePath()
    {
        return $this->raw_image_path;
    }

    /**
     * @param array $target_configuration
     */
    public function setTargetConfig(array $target_configuration)
    {
        $this->target_config = $target_configuration;
    }

    /**
     * @return array
     */
    public function getTargetConfig()
    {
        return $this->target_config;
    }
}