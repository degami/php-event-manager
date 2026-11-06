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
trait EventTrait
{
    /**
     * @var string|null
     */
    protected ?string $name = null;

    /**
     * @var bool
     */
    protected bool $isStopped = false;

    /**
     * @var array
     */
    protected array $data = [];

    /**
     * @var EventEmitterInterface
     */
    protected ?EventEmitterInterface $eventEmitter = null;

    /**
     * @return string
     */
    public function getName() : ?string
    {
        return $this->name;
    }

    /**
     * @return EventInterface
     */
    public function stop() : EventInterface
    {
        $this->isStopped = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function isStopped() : bool
    {
        return (bool) $this->isStopped;
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
     * @param string $key
     * @param mixed $value
     * @return EventInterface
     */
    public function setData(string $key, $value) : EventInterface
    {
        $this->data[(string) $key] = $value;

        return $this;
    }

    /**
     * @param EventEmitterInterface $eventEmitter
     * @return EventInterface
     */
    public function setEventEmitter(EventEmitterInterface $eventEmitter) : EventInterface
    {
        $this->eventEmitter = $eventEmitter;

        return $this;
    }

    /**
     * @return EventEmitterInterface|null
     */
    public function getEventEmitter() : ?EventEmitterInterface
    {
        return $this->eventEmitter;
    }
}
