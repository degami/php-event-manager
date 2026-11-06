<?php
/**
 * This file is part of php-event-manager.
 *
 * php-event-manager is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * php-event-manager is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU Lesser General Public License
 * along with php-event-manager.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author  Degami<degami@github.com>
 * @licence GNU Lesser General Public Licence (http://www.gnu.org/licenses/lgpl-3.0.txt)
 */

/**
 * @namespace
 */
namespace Degami\EventManager;

/**
 *
 */
trait CallbackHandlerTrait
{
    /**
     * @var callable
     */
    private ?callable $callback = null;

    /**
     * @var array
     */
    private array $data = [];

    /**
     * @param array $datas
     * @return CallbackHandlerInterface
     */
    public function initData(array $datas) : CallbackHandlerInterface
    {
        $this->data = $datas;

        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return CallbackHandlerInterface
     */
    public function setData(string $key, $value = null) : CallbackHandlerInterface
    {
        $this->data[(string) $key] = $value;

        return $this;
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getData(string $key, $default = null) : mixed
    {
        if (!isset($this->data[(string) $key])) {
            return $default;
        }
        return $this->data[(string) $key];
    }

    /**
     * @param array $parameters
     * @return void
     */
    abstract public function call(array $parameters = []) : void;

    /**
     * @return void
     */
    public function __invoke() : void
    {
        $this->call(func_get_args());
    }

    /**
     * @param callable $callback
     * @return CallbackHandlerInterface
     */
    public function setCallback(callable $callback) : CallbackHandlerInterface
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * @return callable|null
     */
    public function getCallback() : ?callable
    {
        return $this->callback;
    }
}
