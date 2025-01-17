<?php
/**
 * Module.php
 *
 * -Description-
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * @link       https://www.librenms.org
 *
 * @copyright  2022 Tony Murray
 * @author     Tony Murray <murraytony@gmail.com>
 */

namespace LibreNMS\Util;

use App\Models\Device;
use LibreNMS\Config;
use LibreNMS\Modules\LegacyModule;
use LibreNMS\Polling\ModuleStatus;

class Module
{
    public static function fromName(string $name): \LibreNMS\Interfaces\Module
    {
        $module_class = StringHelpers::toClass($name, '\\LibreNMS\\Modules\\');

        return class_exists($module_class) ? new $module_class : new LegacyModule($name);
    }

    public static function status(string $name, Device $device, ?bool $manual = null): ModuleStatus
    {
        return new ModuleStatus(
            Config::get('poller_modules.' . $name),
            Config::get("os.{$device->os}.poller_modules.$name"),
            $device->getAttrib('poll_' . $name),
            $manual,
        );
    }

    public static function exists(string $module): bool
    {
        return class_exists(StringHelpers::toClass($module, '\\LibreNMS\\Modules\\'))
            || is_file(base_path("includes/polling/$module.inc.php"));
    }
}
