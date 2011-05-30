<?php

namespace Phuby\Observer {
    class Broadcaster {

        protected $_subscribers = array();

        function subscribe($channel, $callback) {
            $channel = $this->channel($channel);
            if (!in_array($subscriber, $channel)) $channel[] = $subscriber;
        }

        function subscribers($channel) {
            return $this->channel($channel);
        }

        function notify($channel, $callback) {
            return array_walk($this->channel($channel), $callback);
        }

        function unsubscribe($channel, $subscriber) {
            $channel = $this->channel($channel);
            if (($index = array_search($subscriber, $channel)) !== false) unset($channel[$index]);
        }

        protected function &channel($channel) {
            if (!isset($this->_subscribers[$channel])) $this->_subscribers[$channel] = array();
            return $this->_subscribers[$channel];
        }

    }
}