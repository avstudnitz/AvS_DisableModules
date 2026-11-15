<?php
namespace AvS\DisableModules\Console\Report\RemovableModules;

use Magento\Setup\Module\Dependency\Parser\Composer\Json;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class Parser extends Json
{
    protected $moduleNamesByComposerName = [];
    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    /**
     * @var OutputInterface|null
     */
    private $output;

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
        $this->output = $options['output'] ?? new ConsoleOutput();
        $this->moduleNamesByComposerName = [];

        foreach ($options['files_for_parse'] as $file) {
            $package = $this->getModuleComposerPackage($file);
            try {
                $magentoModuleName = $this->extractMagentoModuleName($file);
            } catch (\Exception $e) {
                $this->output->writeln("<comment>{$e->getMessage()}</comment> Skipping...");
                continue;
            }
            $this->moduleNamesByComposerName[$this->extractModuleName($package)] = $magentoModuleName;
        }

        $modules = [];
        foreach ($options['files_for_parse'] as $file) {
            $package = $this->getModuleComposerPackage($file);
            $composerName = $this->extractModuleName($package);
            if (!isset($this->moduleNamesByComposerName[$composerName])) {
                continue;
            }
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
                if (!isset($this->moduleNamesByComposerName[$key])) {
                    $this->output->writeln(
                        '<comment>Module name for composer name "' . $key . '" not found.</comment> Skipping...',
                        OutputInterface::VERBOSITY_VERBOSE
                    );
                    continue;
                }
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
