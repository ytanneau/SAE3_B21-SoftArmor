<?php

function pset($value) {
    return isset($value) ? htmlentities($value) : "";
}

?>