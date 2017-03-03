<?php
namespace AvS\DisableModules\Console\Report\RemovableModules;

use Magento\Setup\Module\Dependency\Report\Writer\Csv\AbstractWriter;

/**
 * Csv file writer for removable modules report
 */
class Writer extends AbstractWriter
{
    public function __construct(\Magento\Framework\File\Csv $writer)
    {
        parent::__construct($writer);
    }

    /**
     * Template method. Prepare data step
     *
     * @param \Magento\Setup\Module\Dependency\Report\Dependency\Data\Config $config
     * @return array
     */
    protected function prepareData($config)
    {
        $removableModules = [];
        foreach ($config->getModules() as $module) {
            /** @var \Magento\Setup\Module\Dependency\Report\Dependency\Data\Module $module */
            $removableModules[$module->getName()] = [$module->getName()];
        }
        foreach ($config->getModules() as $module) {
            /** @var \Magento\Setup\Module\Dependency\Report\Dependency\Data\Module $module */
            foreach ($module->getDependencies() as $dependency) {
                /** @var \Magento\Setup\Module\Dependency\Report\Dependency\Data\Dependency $dependency */
                if (!$dependency->isHard()) {
                    continue;
                }
                unset($removableModules[$dependency->getModule()]);
            }
        }
        $data = $removableModules;
        array_unshift($data, []);
        array_unshift($data, ['=========================== ']);
        array_unshift($data, ['Modules without dependencies:']);
        return $data;
    }
}
