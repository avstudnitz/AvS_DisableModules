<?php
namespace AvS\DisableModules\Console\Report\RemovableModules;

use Magento\Setup\Module\Dependency\Report\Builder\AbstractBuilder;

class Builder extends AbstractBuilder
{
    /**
     * Template method. Prepare data for writer step
     *
     * @param array $modulesData
     * @return \Magento\Setup\Module\Dependency\Report\Dependency\Data\Config
     */
    protected function buildData($modulesData)
    {
        $modules = [];
        foreach ($modulesData as $moduleData) {
            $dependencies = [];
            foreach ($moduleData['dependencies'] as $dependencyData) {
                $dependencies[] = new Data\Dependency($dependencyData['module'], $dependencyData['type']);
            }
            $modules[] = new Data\Module($moduleData['module_name'], $dependencies);
        }
        return new Data\Config($modules);
    }
}