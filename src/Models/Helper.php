<?php

namespace Bitrock\Models;
use Bitrock\Models\Model;

class Helper extends Model
{
    /**
     * Метод словоформ от кол-ва
     * Пример вызова: wordForm(17, 'товар', 'товара', 'товаров')
     * @param $num
     * @param $form_for_1
     * @param $form_for_2
     * @param $form_for_5
     * @return mixed
     */
    public static function wordForm($num, $form1, $form2, $form5)
    {

        $num = abs($num) % 100;

        $numX = $num % 10;

        if ($num > 10 && $num < 20)
            return $form5;

        if ($numX > 1 && $numX < 5)
            return $form2;

        if ($numX == 1)
            return $form1;

        return $form5;
    }

    public static function makeYoutubeVideoEmbed($string, $options = [])
    {
        if (empty($string)) return false;

        $watchPattern = '#watch\?v=#';
        $autoplay = $options['autoplay'];

        $embeded = preg_replace($watchPattern, 'embed/', $string);
        if ($autoplay && !preg_match('#\?autoplay=1#', $watchPattern)) {
            $embeded .= '?autoplay=1';
        }

        return $embeded;
    }
}