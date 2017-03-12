<?php
/**
 * This file is part of the PositibeLabs Projects.
 *
 * (c) Pedro Carlos Abreu <pcabreus@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Positibe\Bundle\CmfRoutingExtraBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class GenericContentController
 * @package Positibe\Bundle\CmfRoutingExtraBundle\Controller
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
class GenericContentController extends Controller
{
    private $defaultTemplate = 'content/index.html.twig';

    public function indexAction(Request $request, $contentDocument = null, $template = null)
    {
        $template = $template ?: $this->defaultTemplate;

        $template = str_replace(
            array('{_format}', '{_locale}'),
            array($request->getRequestFormat(), $request->getLocale()),
            $template
        );

        $params = array('content' => $contentDocument);

        return $this->render($template, $params);
    }
} 