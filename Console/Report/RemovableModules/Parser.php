<?php
namespace AvS\DisableModules\Console\Report\RemovableModules;

use Magento\Setup\Module\Dependency\Parser\Composer\Json;
use Symfony\Component\Console\Output\ConsoleOutput;

class Parser extends Json
{
    protected $moduleNamesByComposerName = [];
    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */

    public function __construct(\Magento\Framework\Module\Manager $moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }

    /**
     * {@inheritdoc}
     */
    public function parse(array $options)
    {
        $this->checkOptions($options);

        foreach ($options['files_for_parse'] as $file) {
            $package = $this->getModuleComposerPackage($file);
            try {
                $magentoModuleName = $this->extractMagentoModuleName($file);
            } catch (\Exception $e) {
                $output = new ConsoleOutput;
                $output->writeln("<comment>{$e->getMessage()}</comment> Skipping...");
            }
            $this->moduleNamesByComposerName[$this->extractModuleName($package)] = $magentoModuleName;
        }

        $modules = [];
        foreach ($options['files_for_parse'] as $file) {
            $package = $this->getModuleComposerPackage($file);
            $composerName = $this->extractModuleName($package);
            $magentoModuleName = $this->moduleNamesByComposerName[$composerName];
            if (!$this->isModuleEnabled($magentoModuleName)) {
                continue;
            }
            $modules[] = [
                'name' => $composerName,
                'module_name' => $magentoModuleName,
                'dependencies' => $this->extractDependencies($package),
            ];
        }
        return $modules;
    }

    /**
     * @param string $file
     * @return string
     * @throws \Exception
     */
    protected function extractMagentoModuleName($file)
    {
        $registrationFile = dirname($file) . DIRECTORY_SEPARATOR . 'registration.php';
        $registrationFileContents = @file_get_contents($registrationFile);
        $registrationFileContents = str_replace(["\n", "\r"], '', $registrationFileContents);
        preg_match('/ComponentRegistrar::MODULE,.*\'(.*)\'/', $registrationFileContents, $matches);
        if (sizeof($matches)) {
            return $matches[1];
        }
        throw new \Exception('Could not find module declaration in registration.php belongig to "' . $file . '".');
    }

    /**
     * Template method. Extract dependencies step
     *
     * @param Package $package
     * @return array
     */
    protected function extractDependencies($package)
    {
        $dependencies = [];
        $requires = $package->get('require', '/.+\/module-/');
        if ($requires) {
            foreach ($requires as $key => $value) {

                $magentoModuleName = $this->getModuleNameByComposerName($key);
                if (!$this->isModuleEnabled($magentoModuleName)) {
                    continue;
                }
                $dependencies[] = [
                    'module' => $magentoModuleName,
                    'type' => 'hard',
                ];
            }
        }

        return $dependencies;
    }

    /**
     * Prepare module name
     *
     * @param string $name
     * @return string
     */
    protected function getModuleNameByComposerName($name)
    {
        if (!isset($this->moduleNamesByComposerName[$name])) {
            throw new \Exception('Module name for composer name "' . $name . '" not found.');
        }
        return $this->moduleNamesByComposerName[$name];
    }

    protected function isModuleEnabled($magentoModuleName)
    {
        return $this->moduleManager->isEnabled($magentoModuleName);
    }
}
