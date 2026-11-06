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
interface EventEmitterInterface
{
    const DEFAULT_PRIORITY = 1;

    /**
     * @abstract
     * @param string|array $eventNameList
     * @param callable $listener
     * @return CallbackHandlerInterface
     */
    public function on(string|array $eventNameList, callable $listener, ?int $priority = null) : CallbackHandlerInterface;

    /**
     * @abstract
     * @param string|array $eventNameList
     * @param callable $listener
     * @return CallbackHandlerInterface
     */
    public function once(string|array $eventNameList, callable $listener, ?int $priority = null) : CallbackHandlerInterface;

    /**
     * @abstract
     * @param string|array $eventNameList
     * @param CallbackHandlerInterface $callbackHandler
     * @return EventEmitterInterface
     */
    public function removeListener(string|array $eventNameList, CallbackHandlerInterface $callbackHandler) : EventEmitterInterface;

    /**
     * @abstract
     * @param string|array $eventNameList
     * @return EventEmitterInterface
     */
    public function removeAllListeners(string|array $eventNameList) : EventEmitterInterface;

    /**
     * @abstract
     * @param string|array $eventNameList
     * @return array
     */
    public function getListeners(string|array $eventNameList) : array;

    /**
     * @abstract
     * @param EventInterface $event
     * @param array $params
     * @param callable $cleanupCallback
     * @return EventEmitterInterface
     */
    public function emit(EventInterface $event, array $params = [], ?callable $cleanupCallback = null) : EventEmitterInterface;

    /**
     * @param callable $callback
     * @param array $options
     * @return CallbackHandlerInterface
     */
    public function newCallbackHandler(callable $callback, array $options = []) : CallbackHandlerInterface;
}
