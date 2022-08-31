<?php
/**
 *
 */
declare(strict_types=1);

namespace FishPig\SvgLibrary\Observer;

class InjectSvgFactoryIntoBlocksObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     *
     */
    public function __construct(
        \FishPig\SvgLibrary\Block\ElementFactory $svgElementFactory
    ) {
        $this->svgElementFactory = $svgElementFactory;
    }
    
    /**
     * @param  \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();

        if ($block instanceof \Magento\Framework\View\Element\Template) {
            $block->assign('svgFactory', $this->svgElementFactory);
        }
    }
}
