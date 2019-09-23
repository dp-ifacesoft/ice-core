<?php

namespace Ifacesoft\Ice\Core\V2\Application;

use Dotenv\Dotenv;
use Exception;
use Ifacesoft\Ice\Core\V2\Domain\Dto;
use Ifacesoft\Ice\Core\V2\Domain\Module;
use RuntimeException;

final class Environment extends SingletonService
{
    const ENV_CONFIG_FILE = '.env.ice';
    const APP_CONFIG_FILE = '.ice.php';
    const VENDOR_DIR = 'vendor/';

    /**
     * @param array $environments
     * @return array
     * @throws Exception
     */
    private function parseEnvironment(array $environments)
    {
        $serverHost = \gethostname();

        $foundPattern = null;

        foreach ($environments as $pattern => $environment) {
            $matches = [];

            preg_match($pattern, $serverHost, $matches);

            if (!empty($matches)) {
                $foundPattern = $pattern;
                break;
            }
        }

        if (!$foundPattern) {
            throw new RuntimeException('Environment for host ' . $serverHost . ' not found', 0, null);
        }

        $configData = [];

        $environment = $environments[$foundPattern];

        while (!empty($environment['parent'])) {
            $configData = array_merge_recursive($configData, $environment);

            $environment = is_array($environment['parent'])
                ? $environments[reset($environment['parent'])]
                : $environments[$environment['parent']];
        }

        return array_merge_recursive($configData, $environment);
    }

    /**
     * @return Service
     * @throws Exception
     */
    protected function init()
    {
        $autoloadPath = 'vendor/autoload.php';

        $length = strlen($autoloadPath);

        $path = null;

        foreach (get_included_files() as $file) {
            if (substr($file, -$length) === $autoloadPath) {
                $path = strstr($file, $autoloadPath, true);
                break;
            }
        }

        if (is_file($path . self::ENV_CONFIG_FILE)) {
            Dotenv::create($path, self::ENV_CONFIG_FILE)->load();
        }

        $configData = require $path . self::APP_CONFIG_FILE;

        /** @var Module $module */
        $module = Module::create($configData);

        $environmentParams = $this->parseEnvironment($module->get('environments', []));

        // todo: remove 'environments' in module
        $environmentParams['modules'][$module->get('alias')] = $module;

        $environment = parent::init(Dto::create($environmentParams));

//        $this->setLocale();
//        $this->setTimezone();
//        $this->setPhpSettings();

        return $environment;
    }

    /**
     * @throws \Error
     * @throws \Exception
     */
    private function setLocale()
    {
        $locales = [
            'LC_CTYPE' => LC_CTYPE,
            'LC_COLLATE' => LC_COLLATE,
            'LC_TIME' => LC_TIME,
            'LC_NUMERIC' => LC_NUMERIC,
            'LC_MONETARY' => LC_MONETARY,
            'LC_MESSAGES' => LC_MESSAGES,
            'LC_ALL' => LC_ALL
        ];

        if ($locale = $this->get('php/functions/setlocale', [])) {
            if (is_string($locale[0]) && is_numeric($locale[0])) {
                $locale[0] = $locales[$locale[0]];
            }

            $this->callFunctionPhp('setlocale', $locale);
        } else {

            $categories = [];

            exec('locale', $categories);

            foreach ($categories as $locale) {
                $locale = explode('=', $locale);

                if (!in_array($locale[0], array_keys($locales))) {
                    continue;
                }

                $locale[0] = $locales[$locale[0]];

                if (!isset($locale[1])) {
                    $locale[1] = '';
                } else {
                    $locale[1] = \trim($locale[1], '"');
                }

                if ($locale[1]) {
                    $this->callFunctionPhp('setlocale', $locale);
                }
            }
        }

        \setlocale(LC_NUMERIC, 'C');

        $this->remove('php/functions/setlocale');

        $this->set(['locale' => \setlocale(LC_ALL, 0)]);
    }

    /**
     * @throws \Error
     * @throws \Exception
     */
    private function setTimezone()
    {
        $timezone = $this->get('php/functions/date_default_timezone_set', null);

        if (!$timezone) {
            $timezone = 'UTC'; // \date_default_timezone_get(); @todo Вернуть обратно

            if (\is_link('/etc/localtime')) {
                // Mac OS X (and older Linuxes)
                // /etc/localtime is a symlink to the
                // timezone in /usr/share/zoneinfo.
                $filename = \readlink('/etc/localtime');
                $pos = \strpos($filename, '/usr/share/zoneinfo/');
                if ($pos !== false) {
                    $timezone = \substr($filename, $pos + 20);
                }
            } elseif (\is_file('/etc/timezone')) {
                // Ubuntu / Debian.
                $data = \file_get_contents('/etc/timezone');
                if ($data) {
                    $timezone = $data;
                }
            } elseif (\is_file('/etc/sysconfig/clock')) {
                // RHEL / CentOS
                $data = \parse_ini_file('/etc/sysconfig/clock');
                if (!empty($data['ZONE'])) {
                    $timezone = $data['ZONE'];
                }
            }
        }

        \date_default_timezone_set($timezone);

        $this->remove('php/functions/date_default_timezone_set');

        $this->set(['timezone' => \date_default_timezone_get()]);
    }

    private function setPhpSettings()
    {
        foreach ($this->get('php/functions', []) as $function => $param_arr) {
            $this->callFunctionPhp($function, $param_arr);
        }

        foreach ($this->get('php/ini_set', []) as $varname => $newvar) {
            $this->iniSetPhp($varname, $newvar);
        }
    }

    public function callFunctionPhp($function, $args)
    {
        if ($args === null || $args === []) {
            return;
        }

        $function = $this->getReflectionFunctionPhp($function);

        $function->invokeArgs(\array_slice((array)$args, 0, \count($function->getParameters())));
    }

    public function iniSetPhp($varname, $newvalue)
    {
        \ini_set($varname, \is_array($newvalue) ? \reset($newvalue) : $newvalue);
    }
}
