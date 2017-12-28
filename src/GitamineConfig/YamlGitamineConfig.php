<?php
declare(strict_types=1);

namespace App\GitamineConfig;

use Gitamine\Domain\Directory;
use Gitamine\Domain\Event;
use Gitamine\Domain\File;
use Gitamine\Domain\Hook;
use Gitamine\Domain\Plugin;
use Gitamine\Domain\PluginOptions;
use Gitamine\Infrastructure\GitamineConfig;
use Symfony\Component\Yaml\Yaml;

/**
 * Class YamlGitamineConfig
 *
 * @package App\GitamineConfig
 */
class YamlGitamineConfig implements GitamineConfig
{
    private const GITAMINE_FILE = 'gitamine.yaml';

    /**
     * @var array
     */
    private $config;

    /**
     * @param Directory $directory
     *
     * @return File
     */
    public function getConfigurationFile(Directory $directory): File
    {
        return $directory->open(self::GITAMINE_FILE);
    }

    /**
     * @param Directory $directory
     *
     * @return array
     */
    public function getConfiguration(Directory $directory): array
    {
        $this->config = $this->config ?? Yaml::parseFile($this->getConfigurationFile($directory)->file());

        return $this->config['gitamine'];
    }

    /**
     * @param Plugin        $plugin
     * @param PluginOptions $pluginOptions
     * @param null|string   $output
     *
     * @return bool
     */
    public function runPlugin(Plugin $plugin, PluginOptions $pluginOptions, ?string &$output = null): bool
    {
        $status = 0;
        $out    = [];

        $params = '';

        foreach ($pluginOptions->options() as $key => $value) {
            $params .= sprintf(' --%s=%s', $key, $value);
        }

        exec($this->getPluginExecutableFile($plugin)->file() . $params . ' 2> /dev/null', $out, $status);
        $output = implode(PHP_EOL, $out);

        return $status === 0;
    }

    /**
     * @param Directory $directory
     * @param Event     $event
     *
     * @return Plugin[]
     */
    public function getPluginList(Directory $directory, Event $event): array
    {
        $config            = $this->getConfiguration($directory);
        $config['plugins'] = $config[$event->event()] ?? [];

        $plugins = [];

        foreach ($config['plugins'] as $plugin => $value) {
            $plugins[] = new Plugin($plugin);
        }

        return $plugins;
    }

    /**
     * @param Directory $directory
     * @param Plugin    $plugin
     * @param Event     $event
     *
     * @return PluginOptions
     */
    public function getOptionsForPlugin(Directory $directory, Plugin $plugin, Event $event): PluginOptions
    {
        $config            = $this->getConfiguration($directory);
        $config['plugins'] = $config['plugins'] ?? [];

        return new PluginOptions($config[$event->event()][$plugin->name()] ?? []);
    }

    /**
     * @return Directory
     */
    public function getGitamineFolder(): Directory
    {
        return $this->getHomeFolder()->cd('.gitamine');
    }

    /**
     * @return Plugin[]
     */
    public function getGitaminePlugins(): array
    {
        $pluginsDir = $this->getGitamineFolder()->cd('plugins')->directories();
        $plugins    = [];

        foreach ($pluginsDir as $pluginDir) {
            $plugins[] = new Plugin($pluginDir->name());
        }

        return $plugins;
    }

    /**
     * @param Plugin $plugin
     *
     * @return Hook[]
     */
    public function getGitaminePluginHooks(Plugin $plugin): array
    {
        // TODO: Implement getGitaminePluginHooks() method.
        return [];
    }

    /**
     * @param Plugin $plugin
     *
     * @return File
     */
    public function getPluginExecutableFile(Plugin $plugin): File
    {
        return $this->getGitamineFolder()->cd('plugins')->cd($plugin->name())->open('run');
    }

    /**
     * @return Directory
     */
    private function getHomeFolder(): Directory
    {
        return new Directory($_SERVER['HOME']);
    }

    /**
     * @return Directory
     */
    public function getProjectFolder(): Directory
    {
        return new Directory(getcwd());
    }
}
