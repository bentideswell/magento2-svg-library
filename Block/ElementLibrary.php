<?php
/**
 * @
 */
declare(strict_types=1);

namespace FishPig\SvgLibrary\Block;

class ElementLibrary extends \Magento\Framework\View\Element\AbstractBlock
{
    /**
     * @var array
     */
    private $svgData = [];
    
    /**
     *
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \FishPig\SvgLibrary\App\SvgSpriteGeneratorFactory $svgSpriteGeneratorFactory,
        \Psr\Log\LoggerInterface $logger,
        array $data = []
    ) {
        $this->svgSpriteGeneratorFactory = $svgSpriteGeneratorFactory;
        $this->logger = $logger;
        parent::__construct($context, $data);
    }

    /**
     *
     */
    protected function _toHtml()
    {
        try {
            if ($spriteData = $this->svgSpriteGeneratorFactory->create()->getSpriteData(
                $this->getGroup() ?? 'default',
                false
            )) {
                return '<svg style="display: none;">' . $spriteData . '</svg>';
            }
        } catch (\Exception $e) {
            $this->logger->error($e);
        }
        
        return '';
    }
}
