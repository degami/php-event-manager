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

use SplPriorityQueue;
use RuntimeException;

/**
 *
 */
trait EventEmitterTrait
{
    /**
     * @var array|CallbackHandlerInterface[]
     */
    private array $eventListeners = [];

    /**
     * @var array|SplPriorityQueue[]
     */
    private array $priorityQueues = [];

    /**
     * @param string|array $eventNameList
     * @param callable $listener
     * @param int|null $priority
     * @param bool $isCalledOnce
     * @throws RuntimeException
     * @return CallbackHandlerInterface
     */
    protected function registerEvent(string|array $eventNameList, callable $listener, ?int $priority, bool $isCalledOnce = false) : CallbackHandlerInterface
    {
        if (is_string($eventNameList)) {
            $eventNameList = [$eventNameList];
        }

        if (!is_array($eventNameList)) {
            throw new RuntimeException('First parameter shoud be either a string or an array');
        }

        if ($priority === null) {
            $priority = EventEmitterInterface::DEFAULT_PRIORITY;
        }

        $eventEntry = $this->newCallbackHandler($listener, [
            'is_called_once' => (bool) $isCalledOnce,
            'priority'       => $priority
        ]);

        foreach ($eventNameList as $eventName) {
            if (!isset($this->eventListeners[$eventName])) {
                $this->eventListeners[$eventName] = [];
            }
            if (!isset($this->priorityQueues[$eventName])) {
                $this->priorityQueues[$eventName] = new SplPriorityQueue();
            }

            $this->eventListeners[$eventName][] = $eventEntry;
            $this->priorityQueues[$eventName]->insert($eventEntry, $priority);
        }

        return $eventEntry;
    }

    /**
     * @param string|array $eventNameList
     * @param callable $listener
     * @param int|null $priority
     * @return CallbackHandlerInterface
     */
    public function on(string|array $eventNameList, callable $listener, ?int $priority = null) : CallbackHandlerInterface
    {
        return $this->registerEvent($eventNameList, $listener, $priority, false);
    }

    /**
     * @param string|array $eventNameList
     * @param callable $listener
     * @param int|null $priority
     * @return CallbackHandlerInterface
     */
    public function once(string|array $eventNameList, callable $listener, ?int $priority = null) : CallbackHandlerInterface
    {
        return $this->registerEvent($eventNameList, $listener, $priority, true);
    }

    /**
     * Remove a listener from an event list. This operation consumes lots of resources as long as each priority queue
     * has to be destroyed and re-populated.
     *
     * @param string|array $eventNameList
     * @param CallbackHandlerInterface $callbackHandler
     * @return EventEmitterInterface
     * @throws RuntimeException
     */
    public function removeListener($eventNameList, CallbackHandlerInterface $callbackHandler) : EventEmitterInterface
    {
        if (is_string($eventNameList)) {
            if ($eventNameList == '*') {
                $eventNameList = array_keys($this->eventListeners);
            } else {
                $eventNameList = [$eventNameList];
            }
        }

        if (!is_array($eventNameList)) {
            throw new RuntimeException('First parameter shoud be either a string or an array');
        }

        foreach ($eventNameList as $eventName) {
            $eventName = (string) $eventName;
            if (empty($eventName)) {
                continue;
            }

            $key = array_search($callbackHandler, $this->eventListeners[$eventName]);
            if ($key === false) {
                continue;
            }
            unset($this->eventListeners[$eventName][$key]);
            $this->priorityQueues[$eventName] = new SplPriorityQueue();
            foreach ($this->eventListeners[$eventName] as $item) {
                $this->priorityQueues[$eventName]->insert($item, $item->getData('priority'));
            }
        }

        return $this;
    }

    /**
     * @param string|array $eventNameList
     * @return EventEmitterInterface
     * @throws \RuntimeException
     */
    public function removeAllListeners($eventNameList) : EventEmitterInterface
    {
        if (is_string($eventNameList)) {
            if ($eventNameList == '*') {
                $eventNameList = array_keys($this->eventListeners);
            } else {
                $eventNameList = [$eventNameList];
            }
        }

        if (!is_array($eventNameList)) {
            throw new RuntimeException('First parameter shoud be either a string or an array');
        }

        foreach ($eventNameList as $eventName) {
            $eventName = (string) $eventName;
            if (empty($eventName)) {
                continue;
            }

            if (!isset($this->eventListeners[$eventName])) {
                continue;
            }

            unset($this->eventListeners[$eventName]);
            unset($this->priorityQueues[$eventName]);
        }

        return $this;
    }

    /**
     * @param string|array $eventNameList
     * @return array
     * @throws \RuntimeException
     */
    public function getListeners($eventNameList) : array
    {
        if (is_string($eventNameList)) {
            if ($eventNameList == '*') {
                $eventNameList = array_keys($this->eventListeners);
            } else {
                $eventNameList = [$eventNameList];
            }
        }

        if (!is_array($eventNameList)) {
            throw new RuntimeException('First parameter shoud be either a string or an array');
        }

        $listenerList = [];
        foreach ($eventNameList as $eventName) {
            $eventName = (string) $eventName;
            if (empty($eventName)) {
                continue;
            }

            if (!isset($this->eventListeners[$eventName])) {
                continue;
            }

            foreach ($this->eventListeners[$eventName] as $eventEntry) {
                $listenerList[] = $eventEntry->getCallback();
            }
        }

        return $listenerList;
    }

    /**
     * @param EventInterface $event
     * @param array $params
     * @param callable|null $cleanupCallback
     * @return EventEmitterInterface
     * @throws \RuntimeException
     */
    public function emit(EventInterface $event, array $params = [], ?callable $cleanupCallback = null) : EventEmitterInterface
    {
        $eventName = $event->getName();
        if (!is_string($eventName) || empty($eventName)) {
            throw new \RuntimeException('Could not trigger event, event name should be a non-empty string.');
        }

        if (!isset($this->eventListeners[$eventName])) {
            return $this;
        }

        $event->setEventEmitter($this);

        array_unshift($params, $event);
        foreach ($this->eventListeners[$eventName] as $eventEntry) {
            /** @var CallbackHandlerInterface $eventEntry */
            $eventEntry->call($params);

            if ($eventEntry->getData('is_called_once')) {
                $this->removeListener($eventName, $eventEntry);
            }

            if ($event->isStopped()) {
                break;
            }
        }

        if ($cleanupCallback !== null) {
            call_user_func_array($cleanupCallback, $params);
        }

        return $this;
    }

    /**
     * @param callable $callback
     * @param array $options
     * @return CallbackHandlerInterface
     */
    public function newCallbackHandler(callable $callback, array $options = []) : CallbackHandlerInterface
    {
        return new CallbackHandler($callback, $options);
    }
}
