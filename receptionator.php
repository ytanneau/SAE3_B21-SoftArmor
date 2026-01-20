<?php
function binaireEnOctets($binString) {
    $ret = '';
    $length = strlen($binString);
    for ($i = 0; $i < $length; $i += 8) {
        $byte = substr($binString, $i, 8);
        if (strlen($byte) < 8) break; // ignore le reste incomplet
        $ret .= chr(bindec($byte));
    }
    return $ret;
}
echo binaireEnOctets("1000100101010000010011100100011100001101000010100001101000001010011100100110111101100110011010010110110001100101");
?>