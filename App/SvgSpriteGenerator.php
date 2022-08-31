<?php
/**
 *
 */
declare(strict_types=1);

namespace FishPig\SvgLibrary\App;

class SvgSpriteGenerator
{
    /**
     *
     */
    const GROUP_DEFAULT = 'default';

    /**
     * @var array
     */
    private $svgDataByGroup = [];
    
    /**
     *
     */
    public function __construct(
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \FishPig\SvgLibrary\App\SvgMinifier $minifier,
        array $files = []  
    ) {
        $this->assetRepo = $assetRepo;
        $this->minifier = $minifier;

        foreach ($files as $file) {
            try {
                $this->addFile($file);
            } catch (\Magento\Framework\View\Asset\File\NotFoundException $e) {
                // File not found
            }
        }
    }
    
    //
    public function addFile($file, $id = null, $group = null): void
    {
        if (is_array($file)) {
            if (empty($file['file'])) {
                throw new \InvalidArgumentException('File not set for SVG.');
            }
            
            if (!empty($file['group'])) {
                $group = $file['group'];
            }
            
            $file = $file['file'];
        }
        
        if (!$group) {
            $group = self::GROUP_DEFAULT;
        }

        if ($asset = $this->assetRepo->createAsset($file)) {
            $this->addFileByString(
                file_get_contents($asset->getSourceFile()),
                $id,
                $group
            );
        }
    }
    
    /**
     * @param  string $data
     * @param  string $id   = null
     * @return ?string
     */
    public function addFileByString(string $data, string $id = null, string $group = null): string
    {
        // Clean up SVG data
        $data = trim(preg_replace('/^<\?xml.*\?>/Us', '', trim($data)));
        $data = preg_replace('/ (version|xmlns(:(xlink))?)=([\'"]{1})[^\4]+\4/U', '', $data);


        if (!preg_match('/<svg[^>]+>/s', $data, $match)) {
            return null;
        }
        
        $originalRootSvgNode = $rootSvgNode = $match[0];

        // Check for ID
        if (preg_match('/ id=([\'"]{1})([^\1]+)\1/U', $rootSvgNode, $match)) {
            $id = $match[2];
        }

        if (!$id) {
            throw new \InvalidArgumentException('Unable to find ID for SVG item.');
        }
        
        $data = str_replace(
            $originalRootSvgNode,
            preg_replace('/ (width|height)=([\'"]{1})[^\2]+\2/Us', '', $rootSvgNode),
            $data
        );

        // Convert root SVG tag to symbol
        $data = str_replace(['<svg', '</svg'], ['<symbol', '</symbol'], $data);

        if (!isset($this->svgDataByGroup[$group])) {
            $this->svgDataByGroup[$group] = [];
        }
        
        $this->svgDataByGroup[$group][$id] = $data;

        return $id;
    }
    
    public function getSpriteData(string $group, $includeContainer = true): string
    {
        if (empty($this->svgDataByGroup[$group])) {
            throw new \InvalidArgumentException('Group name not valid.');
        }

        $svg = '<defs>' . $this->minifier->minify(implode(PHP_EOL, $this->svgDataByGroup[$group])) . '</defs>';

        if ($includeContainer) {
            $svg = '<?xml version="1.0" encoding="UTF-8"?><svg xmlns="http://www.w3.org/2000/svg">' . $svg . '</svg>';
        }
        
        return $svg;
    }
}