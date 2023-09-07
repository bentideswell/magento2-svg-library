<?php
/**
 *
 */
declare(strict_types=1);

namespace FishPig\SvgLibrary\App;

use Magento\Framework\View\File\CollectorInterface;
use Magento\Framework\View\Design\ThemeInterface;

class FileCollector implements CollectorInterface
{
    /**
     *
     */
    const PATH_TEMPLATE = 'web/svg/sprite/%s/*.svg';

    /**
     *
     */
    private $design = null;

    /**
     * @var array
     */
    private $fileCollectors = [];

    /**
     *
     */
    public function __construct(
        \Magento\Framework\View\DesignInterface $design,
        array $fileCollectors = []
    ) {
        $this->design = $design;

        foreach ($fileCollectors as $collectorId => $fileCollector) {
            if (false === ($fileCollector instanceof CollectorInterface)) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'File collector "%s" does not implement "%s".',
                        $collectorId,
                        CollectorInterface::class
                    )
                );
            }

            $this->fileCollectors[$collectorId] = $fileCollector;
        }
    }

    /**
     *
     */
    public function collect(string $groupCode): array
    {
        return $this->getFiles($this->design->getDesignTheme(), $groupCode);
    }

    /**
     *
     */
    public function getFiles(ThemeInterface $theme, $groupCode)
    {
        $filePath = sprintf(self::PATH_TEMPLATE, $groupCode);
        $files = [];

        foreach ($this->fileCollectors as $fileCollector) {
            $files = array_merge(
                $files,
                $fileCollector->getFiles(
                    $this->design->getDesignTheme(),
                    $filePath
                )
            );
        }

        return $files;
    }
}