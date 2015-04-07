<?php
    Class Debug {
        private $a;
        public function pre($var){
            ob_start();
                echo "<pre>\n";
                var_dump($var);
                echo "</pre>\n";
                $a = ob_get_contents;
            ob_end_clean;
            return $a;
        }
    }
?>
