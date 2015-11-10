<?php

namespace TadasSiaudvytis\WeatherBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class TadasSiaudvytisWeatherBundle extends Bundle
{
    protected $response   =    false;
    protected $api_key    =    '4a98fdd1f5e1242bfa83beca7f96c48e';
    protected $unit       =    'metric';
    protected $lang       =    'en';
    protected $city       =    'vilnius';

    public function get_temperature () {
        if (!$this->response) {
            $this->getResponse();
        }

        return $this->response->list[0]->main->temp;
    }

    public function get_weather_data () {
        if (!$this->response) {
            $this->getResponse();
        }

        return $this->response->list[0];
    }

    public function setConfig($config = false, $value = false) {
        if ($config && isset($this->{$config}) && $value) {
            $this->{$config} = $this->validate_field($config, $value);

            return true;
        }

        throw new \Exception('TadasSiaudvytisWeatherBundle()->setConfig function requires two parameters.');
    }

    public function getConfig($config) {
        if (isset($this->{$config})) {
            return $this->{$config};
        }

        throw new \Exception('TadasSiaudvytisWeatherBundle() does not have this config.');
    }

    private function getResponse () {
        if (!isset($this->api_key) || !$this->api_key) {

            throw new \Exception('TadasSiaudvytisWeatherBundle() requires "api_key" config.');
        }

        if (!isset($this->unit) || !$this->unit) {

            throw new \Exception('TadasSiaudvytisWeatherBundle() requires "unit" config.');
        }

        if (!isset($this->lang) || !$this->lang) {

            throw new \Exception('TadasSiaudvytisWeatherBundle() requires "lang" config.');
        }

        if (!isset($this->city) || !$this->city) {

            throw new \Exception('TadasSiaudvytisWeatherBundle() requires "city" config.');
        }

        try {
            $this->response = file_get_contents('http://api.openweathermap.org/data/2.5/forecast?units='.$this->unit.'&q='.$this->city.'&appid='.$this->api_key.'&lang='.$this->lang);
            $this->response = json_decode($this->response);
        } catch (Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    private function validate_field ($config, $value) {
        switch ($config) {
            case 'city':
                $this->response = false;
                return urlencode($value);
                break;
            case 'unit':
                $this->response = false;
                switch ($value) {
                    case 'metric':
                        return $value;
                        break;
                    case 'imperial':
                        return $value;
                        break;
                    default:
                        return 'metric';
                }
                break;
            case 'lang':
                $this->response = false;
                switch ($value) {
                    case 'en':
                        return $value;
                        break;
                    case 'ru':
                        return $value;
                        break;
                    default:
                        return 'en';
                }
                break;
            default:
                return $value;
        }
    }
}
