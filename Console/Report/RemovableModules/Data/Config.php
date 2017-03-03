<?php
namespace AvS\DisableModules\Console\Report\RemovableModules\Data;

use Magento\Setup\Module\Dependency\Report\Data\Config\AbstractConfig;

/**
 * Config
 *
 * @method \Magento\Setup\Module\Dependency\Report\Dependency\Data\Module[] getModules()
 */
class Config extends AbstractConfig
{
    /**
     * {@inheritdoc}
     */
    public function getDependenciesCount()
    {
        return $this->getHardDependenciesCount() + $this->getSoftDependenciesCount();
    }

    /**
     * Get hard dependencies count
     *
     * @return int
     */
    public function getHardDependenciesCount()
    {
        $dependenciesCount = 0;
        foreach ($this->getModules() as $module) {
            $dependenciesCount += $module->getHardDependenciesCount();
        }
        return $dependenciesCount;
    }

    /**
     * Get soft dependencies count
     *
     * @return int
     */
    public function getSoftDependenciesCount()
    {
        $dependenciesCount = 0;
        foreach ($this->getModules() as $module) {
            $dependenciesCount += $module->getSoftDependenciesCount();
        }
        return $dependenciesCount;
    }
}
