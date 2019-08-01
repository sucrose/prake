<?php
    /**
     *
     *  name:       Prake
     *  desc:       PHP class to rake (scrape) public proxy lists for updated proxies
     *  author:     Sucrose
     *  website:    https://github.com/sucrose/prake
     *  email:      sucrose@pm.me
     *
     */
    class Prake {
        public $debug_mode = true;
        public $cookies;

        function __construct()
        {
            $a = func_get_args();
            $l = func_num_args();
            if (method_exists($this, $f = '__construct' . $l))
            {
                call_user_func_array(array($this, $f), $a);
            }
        }

        function __construct1($a1)
        {
            $this->debug_mode = $a1;
            return;
        }

        function __destruct()
        {
            return '[INFO] Destroying Prake instance...';
        }

        public function __get($var)
        {
            return "[ERROR] Inexistant property: $var";
        }

        public function __toString()
        {
            return $this->get_status();
        }

        public function debug_mode()
        {
            $dbg = ($this->debug_mode) ? 'enabled' : 'disabled';
            echo "[INFO] Debug: $dbg";
            return;
        }

        /**
         * PRIVATE
         */

        /**
         * PUBLIC
         */
    }
?>