<?php
/**
 * Created by PhpStorm.
 * User: 47143
 * Date: 2019/3/29
 * Time: 17:56
 */

namespace App\Http\Service;
use RequestFilter;
use App\Http\Contracts\Data ;
class SourceElasticsearch implements Data
{

    public function countlyUpOption(){
        RequestFilter::countlyTerms();
        return '';
    }

    public function countlyUpData(){
        return '';
    }

}