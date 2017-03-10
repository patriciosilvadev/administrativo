<?php

if (!defined('BASEPATH'))
    exit('N&atilde;o &eacute; permitido acesso direto a esse script.');

class Utilitario {

    function autorizar($modulo, $permissoes) {
        if (array_search($modulo, $permissoes) == false) {
            return false;
        } else {
            return true;
        }
    }

    function remover_caracter($string) {
        $string = preg_replace("/[áàâãä]/", "a", $string);
        $string = preg_replace("/[ÁÀÂÃÄ]/", "A", $string);
        $string = preg_replace("/[éèê]/", "e", $string);
        $string = preg_replace("/[ÉÈÊ]/", "E", $string);
        $string = preg_replace("/[íì]/", "i", $string);
        $string = preg_replace("/[ÍÌ]/", "I", $string);
        $string = preg_replace("/[óòôõö]/", "o", $string);
        $string = preg_replace("/[ÓÒÔÕÖ]/", "O", $string);
        $string = preg_replace("/[úùü]/", "u", $string);
        $string = preg_replace("/[ÚÙÜ]/", "U", $string);
        $string = preg_replace("/ç/", "c", $string);
        $string = preg_replace("/Ç/", "C", $string);
        $string = preg_replace("/[][><}{)(:;,!?*%~^`@\.-]/", "", $string);
        return $string;
    }

    function codigo_uf($codMunIbge, $retorno = 'sigla') {
        $codUF = substr($codMunIbge, 0, 2);
        if($retorno != 'sigla'){
            return $codUF;
        }
        else{
            switch ($codUF) {
                case '11':
                    return 'RO';
                    break;
                case '12':
                    return 'AC';
                    break;
                case '13':
                    return 'AM';
                    break;
                case '14':
                    return 'RR';
                    break;
                case '15':
                    return 'PA';
                    break;
                case '16':
                    return 'AP';
                    break;
                case '17':
                    return 'TO';
                    break;
                case '21':
                    return 'MA';
                    break;
                case '22':
                    return 'PI';
                    break;
                case '23':
                    return 'CE';
                    break;
                case '24':
                    return 'RN';
                    break;
                case '25':
                    return 'PB';
                    break;
                case '26':
                    return 'PE';
                    break;
                case '27':
                    return 'AL';
                    break;
                case '28':
                    return 'SE';
                    break;
                case '29':
                    return 'BA';
                    break;
                case '31':
                    return 'MG';
                    break;
                case '32':
                    return 'ES';
                    break;
                case '33':
                    return 'RJ';
                    break;
                case '35':
                    return 'SP';
                    break;
                case '41':
                    return 'PR';
                    break;
                case '42':
                    return 'SC';
                    break;
                case '43':
                    return 'RS';
                    break;
                case '50':
                    return 'MS';
                    break;
                case '51':
                    return 'MT';
                    break;
                case '52':
                    return 'GO';
                    break;
                case '53':
                    return 'DF';
                    break;

                default:
                    return false;
                    break;
            }
        }
    }
    
    function filler($tamanho) {

        $filler = "";
        for ($i = 0; $i < $tamanho; $i++) {
            $filler .= "_";
        }
        $filler = str_replace("_", " ", $filler);
        return $filler;
    }

    function zeros($tamanho) {

        $zero = "";
        for ($i = 0; $i < $tamanho; $i++) {
            $zero .= "0";
        }
        return $zero;
    }

    function fatorVencimentoBNB($vencimento) {

        $dtVenc = new DateTime($vencimento);
        $dtBase = new DateTime('1997-10-07');

        $intervalo = $dtVenc->diff($dtBase);
        return $intervalo->days;
    }

    function dvCodigoBNB($linha) {
        $linha = (string) $linha;
        $totAlg = strlen($linha);
        
        $soma = 0;
        $fatorMultiplicador = 2;
        for ($i = $totAlg - 1; $i >= 0; $i--) {
            //GERAR DINAMICAMENTE O ARRAY DOS ALGARISMOS 
            $numero = (int)substr($linha, $i,1);
            $resParcial =  $numero * $fatorMultiplicador;
            $soma += $resParcial;
            $fatorMultiplicador = ($fatorMultiplicador == 9)? 2 : $fatorMultiplicador+1;
        }
        $modulo = $soma % 11;
        $digitoVerificador = (($modulo == 0) || ($modulo == 1) || ($modulo == 10) ) ? 1 : 11 - $modulo;
        return $digitoVerificador;
    }

    function dvLinhaBNB($campo) {
        $campo = (string) $campo;
        $totAlg = strlen($campo);
        if ($totAlg == 9) { //Primeiro Campo da Linha
            $algarismos = array(
                (int) substr($campo, 0, 1),
                (int) substr($campo, 1, 1),
                (int) substr($campo, 2, 1),
                (int) substr($campo, 3, 1),
                (int) substr($campo, 4, 1),
                (int) substr($campo, 5, 1),
                (int) substr($campo, 6, 1),
                (int) substr($campo, 7, 1),
                (int) substr($campo, 8, 1)
            );
            
            $fatorMultiplicador = 2;
            $soma = 0;
            for ($i = count($algarismos) - 1; $i >= 0; $i--) {
                $res = $algarismos[$i] * $fatorMultiplicador;
                $resParcial = ($res > 9) ? $res - 9 : $res;
                $soma += $resParcial;
                $fatorMultiplicador = ($fatorMultiplicador == 2) ? 1 : 2;
            }
            
            $modulo = $soma % 10;
            $digitoVerificador = ($modulo > 0) ? 10 - $modulo : 0;
            return $digitoVerificador;
        } 
        elseif ($totAlg == 10) { //Outros Campo da Linha
            $algarismos = array(
                (int) substr($campo, 0, 1),
                (int) substr($campo, 1, 1),
                (int) substr($campo, 2, 1),
                (int) substr($campo, 3, 1),
                (int) substr($campo, 4, 1),
                (int) substr($campo, 5, 1),
                (int) substr($campo, 6, 1),
                (int) substr($campo, 7, 1),
                (int) substr($campo, 8, 1),
                (int) substr($campo, 9, 1)
            );
            $fatorMultiplicador = 2;
            $soma = 0;
            
            for ($i = count($algarismos) - 1; $i >= 0; $i--) {
                $res = $algarismos[$i] * $fatorMultiplicador;
                $resParcial = ($res > 9) ? $res - 9 : $res;
                $soma += $resParcial;
                $fatorMultiplicador = ($fatorMultiplicador == 2) ? 1 : 2;
            }
            $modulo = $soma % 10;
            $digitoVerificador = ($modulo > 0) ? 10 - $modulo : 0;
            return $digitoVerificador;
        } 
        else {
            return false;
        }
    }

    function digito_nosso_numeroBNB($nossoNum) {
        $nossoNum = (string) $nossoNum;
        $algarismos = array(
            "um" => (int) substr($nossoNum, 0, 1),
            "dois" => (int) substr($nossoNum, 1, 1),
            "tres" => (int) substr($nossoNum, 2, 1),
            "quatro" => (int) substr($nossoNum, 3, 1),
            "cinco" => (int) substr($nossoNum, 4, 1),
            "seis" => (int) substr($nossoNum, 5, 1),
            "sete" => (int) substr($nossoNum, 6, 1)
        );
        $soma = 0;
        $i = 8;
        foreach ($algarismos as $value) {
            $soma += $value * $i;
            $i--;
        }
        $modulo = $soma % 11;
        if ($modulo === 0 || $modulo === 1) {
            $digito = 0;
        } else {
            $digito = 11 - $modulo;
        }
        return $digito;
    }

    function tamanho_string($texto, $tamCampo, $tipo = 'text') {
        if ($tipo == 'text') {
            $tamanho = strlen($texto);
            if ($tamanho < $tamCampo) {
                $diferenca = (int) $tamCampo - (int) $tamanho;
                $texto .= $this->filler($diferenca);
            } elseif ($tamanho > $tamCampo) {
                $texto = substr($texto, 0, $tamCampo);
            }
            return $texto;
        } else {
            $tamanho = strlen($texto);
            $zeros = '';
            if ($tamanho < $tamCampo) {
                $diferenca = (int) $tamCampo - (int) $tamanho;
                $zeros .= $this->zeros($diferenca);
            } elseif ($tamanho > $tamCampo) {
                $texto = substr($texto, 0, $tamCampo);
            }
            $texto = $zeros . $texto;
            return $texto;
        }
    }

    function preencherDireita($valor, $tamanho, $caractere = "") {
        $i = strlen($valor);


        if ($i < $tamanho) {

            for ($i; $i < $tamanho; $i++) {
                $valor .= $caractere;
            }
        } else {
            $valor = substr($valor, 0, $tamanho);
        }

        return $valor;
    }

    function preencherEsquerda($valor, $tamanho, $caractere = "") {
        $i = strlen($valor);
        $retorno = "";
        if ($i < $tamanho) {
            for ($i; $i < $tamanho; $i++) {
                $retorno .= $caractere;
            }
            $retorno .= $valor;
        } else {
            $retorno = substr($valor, 0, $tamanho);
        }

        return $retorno;
    }

    function paginacao($url, $total, $pagina, $limit = 10) {

        $CI = & get_instance();

        $config['base_url'] = $url;
        $config['total_rows'] = $total;
        $config['num_links'] = 10;
        $config['per_page'] = $limit;
        $config['first_link'] = 'primeira';
        $config['last_link'] = 'última';
        $config['next_link'] = '&gt;';
        $config['prev_link'] = '&lt;';

        $CI->pagination->initialize($config);
        echo $CI->pagination->create_links();
    }

    function build_query_params($baseurl, $args = array()) {
        $parts = array();
        foreach ($args as $chave => $valor) {
            if ($chave != 'per_page') {
                array_push($parts, urlencode($chave) . '=' . urlencode($valor));
            }
        }
        return $baseurl . '?' . join('&', $parts);
    }

    function pmf_mensagem($mensagem = '') {
//        var_dump($mensagem);
        if ($mensagem && strlen(trim($mensagem)) > 0) {
            echo '<div class="div-mensagem hidden" title="Mensagem:">';
            echo $mensagem;
            echo '</div>';
        }
    }

    function barcode($text = "0", $filepath = "", $size = "20", $orientation = "horizontal", $code_type = "code128", $print = false, $SizeFactor = 1) {
//        var_dump($text, $filepath , $print);die;
        $code_string = "";
        // Translate the $text into barcode the correct $code_type
        if (in_array(strtolower($code_type), array("code128", "code128b"))) {
            $chksum = 104;
            // Must not change order of array elements as the checksum depends on the array's key to validate final code
            $code_array = array(" " => "212222", "!" => "222122", "\"" => "222221", "#" => "121223", "$" => "121322", "%" => "131222", "&" => "122213", "'" => "122312", "(" => "132212", ")" => "221213", "*" => "221312", "+" => "231212", "," => "112232", "-" => "122132", "." => "122231", "/" => "113222", "0" => "123122", "1" => "123221", "2" => "223211", "3" => "221132", "4" => "221231", "5" => "213212", "6" => "223112", "7" => "312131", "8" => "311222", "9" => "321122", ":" => "321221", ";" => "312212", "<" => "322112", "=" => "322211", ">" => "212123", "?" => "212321", "@" => "232121", "A" => "111323", "B" => "131123", "C" => "131321", "D" => "112313", "E" => "132113", "F" => "132311", "G" => "211313", "H" => "231113", "I" => "231311", "J" => "112133", "K" => "112331", "L" => "132131", "M" => "113123", "N" => "113321", "O" => "133121", "P" => "313121", "Q" => "211331", "R" => "231131", "S" => "213113", "T" => "213311", "U" => "213131", "V" => "311123", "W" => "311321", "X" => "331121", "Y" => "312113", "Z" => "312311", "[" => "332111", "\\" => "314111", "]" => "221411", "^" => "431111", "_" => "111224", "\`" => "111422", "a" => "121124", "b" => "121421", "c" => "141122", "d" => "141221", "e" => "112214", "f" => "112412", "g" => "122114", "h" => "122411", "i" => "142112", "j" => "142211", "k" => "241211", "l" => "221114", "m" => "413111", "n" => "241112", "o" => "134111", "p" => "111242", "q" => "121142", "r" => "121241", "s" => "114212", "t" => "124112", "u" => "124211", "v" => "411212", "w" => "421112", "x" => "421211", "y" => "212141", "z" => "214121", "{" => "412121", "|" => "111143", "}" => "111341", "~" => "131141", "DEL" => "114113", "FNC 3" => "114311", "FNC 2" => "411113", "SHIFT" => "411311", "CODE C" => "113141", "FNC 4" => "114131", "CODE A" => "311141", "FNC 1" => "411131", "Start A" => "211412", "Start B" => "211214", "Start C" => "211232", "Stop" => "2331112");
            $code_keys = array_keys($code_array);
            $code_values = array_flip($code_keys);
            for ($X = 1; $X <= strlen($text); $X++) {
                $activeKey = substr($text, ($X - 1), 1);
                $code_string .= $code_array[$activeKey];
                $chksum = ($chksum + ($code_values[$activeKey] * $X));
            }
            $code_string .= $code_array[$code_keys[($chksum - (intval($chksum / 103) * 103))]];
            $code_string = "211214" . $code_string . "2331112";
        } elseif (strtolower($code_type) == "code128a") {
            $chksum = 103;
            $text = strtoupper($text); // Code 128A doesn't support lower case
            // Must not change order of array elements as the checksum depends on the array's key to validate final code
            $code_array = array(" " => "212222", "!" => "222122", "\"" => "222221", "#" => "121223", "$" => "121322", "%" => "131222", "&" => "122213", "'" => "122312", "(" => "132212", ")" => "221213", "*" => "221312", "+" => "231212", "," => "112232", "-" => "122132", "." => "122231", "/" => "113222", "0" => "123122", "1" => "123221", "2" => "223211", "3" => "221132", "4" => "221231", "5" => "213212", "6" => "223112", "7" => "312131", "8" => "311222", "9" => "321122", ":" => "321221", ";" => "312212", "<" => "322112", "=" => "322211", ">" => "212123", "?" => "212321", "@" => "232121", "A" => "111323", "B" => "131123", "C" => "131321", "D" => "112313", "E" => "132113", "F" => "132311", "G" => "211313", "H" => "231113", "I" => "231311", "J" => "112133", "K" => "112331", "L" => "132131", "M" => "113123", "N" => "113321", "O" => "133121", "P" => "313121", "Q" => "211331", "R" => "231131", "S" => "213113", "T" => "213311", "U" => "213131", "V" => "311123", "W" => "311321", "X" => "331121", "Y" => "312113", "Z" => "312311", "[" => "332111", "\\" => "314111", "]" => "221411", "^" => "431111", "_" => "111224", "NUL" => "111422", "SOH" => "121124", "STX" => "121421", "ETX" => "141122", "EOT" => "141221", "ENQ" => "112214", "ACK" => "112412", "BEL" => "122114", "BS" => "122411", "HT" => "142112", "LF" => "142211", "VT" => "241211", "FF" => "221114", "CR" => "413111", "SO" => "241112", "SI" => "134111", "DLE" => "111242", "DC1" => "121142", "DC2" => "121241", "DC3" => "114212", "DC4" => "124112", "NAK" => "124211", "SYN" => "411212", "ETB" => "421112", "CAN" => "421211", "EM" => "212141", "SUB" => "214121", "ESC" => "412121", "FS" => "111143", "GS" => "111341", "RS" => "131141", "US" => "114113", "FNC 3" => "114311", "FNC 2" => "411113", "SHIFT" => "411311", "CODE C" => "113141", "CODE B" => "114131", "FNC 4" => "311141", "FNC 1" => "411131", "Start A" => "211412", "Start B" => "211214", "Start C" => "211232", "Stop" => "2331112");
            $code_keys = array_keys($code_array);
            $code_values = array_flip($code_keys);
            for ($X = 1; $X <= strlen($text); $X++) {
                $activeKey = substr($text, ($X - 1), 1);
                $code_string .= $code_array[$activeKey];
                $chksum = ($chksum + ($code_values[$activeKey] * $X));
            }
            $code_string .= $code_array[$code_keys[($chksum - (intval($chksum / 103) * 103))]];
            $code_string = "211412" . $code_string . "2331112";
        } elseif (strtolower($code_type) == "code39") {
            $code_array = array("0" => "111221211", "1" => "211211112", "2" => "112211112", "3" => "212211111", "4" => "111221112", "5" => "211221111", "6" => "112221111", "7" => "111211212", "8" => "211211211", "9" => "112211211", "A" => "211112112", "B" => "112112112", "C" => "212112111", "D" => "111122112", "E" => "211122111", "F" => "112122111", "G" => "111112212", "H" => "211112211", "I" => "112112211", "J" => "111122211", "K" => "211111122", "L" => "112111122", "M" => "212111121", "N" => "111121122", "O" => "211121121", "P" => "112121121", "Q" => "111111222", "R" => "211111221", "S" => "112111221", "T" => "111121221", "U" => "221111112", "V" => "122111112", "W" => "222111111", "X" => "121121112", "Y" => "221121111", "Z" => "122121111", "-" => "121111212", "." => "221111211", " " => "122111211", "$" => "121212111", "/" => "121211121", "+" => "121112121", "%" => "111212121", "*" => "121121211");
            // Convert to uppercase
            $upper_text = strtoupper($text);
            for ($X = 1; $X <= strlen($upper_text); $X++) {
                $code_string .= $code_array[substr($upper_text, ($X - 1), 1)] . "1";
            }
            $code_string = "1211212111" . $code_string . "121121211";
        } elseif (strtolower($code_type) == "code25") {
            $code_array1 = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
            $code_array2 = array("3-1-1-1-3", "1-3-1-1-3", "3-3-1-1-1", "1-1-3-1-3", "3-1-3-1-1", "1-3-3-1-1", "1-1-1-3-3", "3-1-1-3-1", "1-3-1-3-1", "1-1-3-3-1");
            for ($X = 1; $X <= strlen($text); $X++) {
                for ($Y = 0; $Y < count($code_array1); $Y++) {
                    if (substr($text, ($X - 1), 1) == $code_array1[$Y])
                        $temp[$X] = $code_array2[$Y];
                }
            }
            for ($X = 1; $X <= strlen($text); $X+=2) {
                if (isset($temp[$X]) && isset($temp[($X + 1)])) {
                    $temp1 = explode("-", $temp[$X]);
                    $temp2 = explode("-", $temp[($X + 1)]);
                    for ($Y = 0; $Y < count($temp1); $Y++)
                        $code_string .= $temp1[$Y] . $temp2[$Y];
                }
            }
            $code_string = "1111" . $code_string . "311";
        } elseif (strtolower($code_type) == "codabar") {
            $code_array1 = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "-", "$", ":", "/", ".", "+", "A", "B", "C", "D");
            $code_array2 = array("1111221", "1112112", "2211111", "1121121", "2111121", "1211112", "1211211", "1221111", "2112111", "1111122", "1112211", "1122111", "2111212", "2121112", "2121211", "1121212", "1122121", "1212112", "1112122", "1112221");
            // Convert to uppercase
            $upper_text = strtoupper($text);
            for ($X = 1; $X <= strlen($upper_text); $X++) {
                for ($Y = 0; $Y < count($code_array1); $Y++) {
                    if (substr($upper_text, ($X - 1), 1) == $code_array1[$Y])
                        $code_string .= $code_array2[$Y] . "1";
                }
            }
            $code_string = "11221211" . $code_string . "1122121";
        }
        // Pad the edges of the barcode
        $code_length = 20;
        if ($print) {
            $text_height = 30;
        } else {
            $text_height = 0;
        }

        for ($i = 1; $i <= strlen($code_string); $i++) {
            $code_length = $code_length + (integer) (substr($code_string, ($i - 1), 1));
        }
        if (strtolower($orientation) == "horizontal") {
            $img_width = $code_length * $SizeFactor;
            $img_height = $size;
        } else {
            $img_width = $size;
            $img_height = $code_length * $SizeFactor;
        }
        $image = imagecreate($img_width, $img_height + $text_height);
        $black = imagecolorallocate($image, 0, 0, 0);
        $white = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $white);
        if ($print) {
            imagestring($image, 5, 31, $img_height, $text, $black);
        }
        $location = 10;
        for ($position = 1; $position <= strlen($code_string); $position++) {
            $cur_size = $location + ( substr($code_string, ($position - 1), 1) );
            if (strtolower($orientation) == "horizontal")
                imagefilledrectangle($image, $location * $SizeFactor, 0, $cur_size * $SizeFactor, $img_height, ($position % 2 == 0 ? $white : $black));
            else
                imagefilledrectangle($image, 0, $location * $SizeFactor, $img_width, $cur_size * $SizeFactor, ($position % 2 == 0 ? $white : $black));
            $location = $cur_size;
        }


        // Draw barcode to the screen or save in a file
        if ($filepath == "") {
            header('Content-type: image/png');
            imagepng($image);
            imagedestroy($image);
        } else {
            imagepng($image, $filepath);
            imagedestroy($image);
        }

        return $filepath;

        // necessita instalar gd-library no ubuntu -> sudo apt-get install php5-gd
    }

}
