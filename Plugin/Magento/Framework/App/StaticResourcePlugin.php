<?php
/**
 *
 */
declare(strict_types=1);

namespace FishPig\SvgLibrary\Plugin\Magento\Framework\App;

use Magento\Framework\App\StaticResource;
use Magento\MediaStorage\Model\File\Storage\Request;

class StaticResourcePlugin
{
    /**
     * @var Request
     */
    private $request;
    
    /**
     * @param Webp $webp
     */
    public function __construct(
        \Magento\Framework\HTTP\PhpEnvironment\Request $request,
        \FishPig\SvgLibrary\App\SvgSpriteGeneratorFactory $svgSpriteGeneratorFactory,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\View\DesignInterface $viewDesign
    ) {
        $this->request = $request;
        $this->svgSpriteGeneratorFactory = $svgSpriteGeneratorFactory;
        $this->appState = $appState;
        $this->viewDesign = $viewDesign;
    }
    
    /**
     * @param StaticResource $subject
     */
    public function beforeLaunch(StaticResource $subject)
    {
        if (null === ($params = $this->parsePathInfo($this->request->getPathInfo()))) {
            return;
        }

        $this->appState->setAreaCode($params['designParams']['area']);
        $this->viewDesign->setDesignTheme($params['designParams']['theme'], $params['designParams']['area']);
        
        header('Content-Type: image/svg+xml');
        echo $this->svgSpriteGeneratorFactory->create()->getSpriteData($params['group']);
        exit;
    }
    
    /**
     * @return bool
     */
    private function parsePathInfo(string $pathInfo): ?array
    {
        if (trim((string)$pathInfo) === '') {
            return null;
        }
        
        // Strip version string
        $pathInfo = preg_replace('/\/version[0-9]{1,}\//', '/', $pathInfo);

        $spritePattern = '/\/static\/(frontend)\/([^\/]+\/[^\/]+)\/([a-z]{2}_[A-Z]{2})\/sprite\/([a-z0-9_\-]{1,})\.svg$/';

        if (!preg_match($spritePattern, $pathInfo, $match)) {
            return null;
        }

        return [
            'designParams' => [
                'area' => $match[1],
                'theme' => $match[2],
                'locale' => $match[3],
            ],
            'group' => $match[4]
        ];
    }
}