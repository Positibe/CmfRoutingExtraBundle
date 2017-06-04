<?php
/**
 * This file is part of the PositibeLabs Projects.
 *
 * (c) Pedro Carlos Abreu <pcabreus@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Positibe\Bundle\CmfRoutingExtraBundle\Entity\Traits;

/**
 * Class CustomRouteInformationTrait
 * @package Positibe\Bundle\CmfRoutingExtraBundle\Entity\Traits
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
trait CustomRouteInformationTrait
{
    /**
     * @var string
     *
     * @ORM\Column(name="custom_controller", type="string", length=100, nullable=TRUE)
     */
    protected $customController;

    /**
     * @var string
     *
     * @ORM\Column(name="custom_template", type="string", length=100, nullable=TRUE)
     */
    protected $customTemplate;

    /**
     * @var string
     *
     * @ORM\Column(name="host", type="string", length=100, nullable=TRUE)
     */
    protected $host;

    /**
     * @return string|null
     */
    public function getCustomController()
    {
        return $this->customController;
    }

    /**
     * @param string $customController
     */
    public function setCustomController($customController)
    {
        $this->customController = $customController;
    }

    /**
     * @return string
     */
    public function getCustomTemplate()
    {
        return $this->customTemplate;
    }

    /**
     * @param string $customTemplate
     */
    public function setCustomTemplate($customTemplate)
    {
        $this->customTemplate = $customTemplate;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }
}