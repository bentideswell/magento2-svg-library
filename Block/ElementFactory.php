<?php
/**
 *
 */
declare(strict_types=1);

namespace FishPig\SvgLibrary\Block;

class ElementFactory extends \Magento\Framework\View\Element\AbstractBlock
{
    /**
     * @const string
     */
    const OPTION_EXTERNAL = '_external';

    /**
     *
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \FishPig\SvgLibrary\App\SvgMinifier $minifier,
        array $data = []
    ) {
        $this->minifier = $minifier;
        parent::__construct($context, $data);
    }
    
    /**
     *
     */
    public function create(string $id, $useHref = null, $options = null): ?string
    {
        if (is_array($useHref)) {
            $options = $useHref;
            $useHref = $id;
        }
        
        if ($useHref === null) {
            $useHref = $id;
        }

        if (!empty($options[self::OPTION_EXTERNAL]) && (int)$options[self::OPTION_EXTERNAL] === 1) {
            if (strpos($useHref, '.svg#') === false && $this->isExternal()) {
                $useHref = 'sprite/default.svg#' . $useHref;
            }
            
            $useHref = $this->getViewFileUrl($useHref);
        } else {
            $useHref = '#'. $useHref;
        }

        // Build <svg> tag
        $svgTag = '<svg';
        
        if ($options) {
            foreach ($options as $key => $value) {
                $svgTag .= ' ' . $key . '="' . $value . '"';
            }
        }

        $svgTag .= '><use xlink:href="' . $useHref . '"/></svg>';

        return $svgTag;
    }
    
    /**
     *
     */
    public function createFromFile(string $file): ?string
    {
        if ($asset = $this->_assetRepo->createAsset($file)) {
            if (!is_file($asset->getSourceFile())) {
                return null;
            }
            
            return $this->minifier->minify(file_get_contents($asset->getSourceFile()));
        }
        
        return null;
    }
}
