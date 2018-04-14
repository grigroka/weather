<?php
/**
 * Created by PhpStorm.
 * User: mac3c970e3fc144
 * Date: 4/12/18
 * Time: 5:30 PM
 */

namespace App\Weather;

use App\GoogleApi\WeatherService;
use App\Model\Weather;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LoaderService
{
    /**
     * @var WeatherService
     */
    private $googleWeatherService;

    /**
     * @var FilesystemCache
     */
    private $cacheService;

    /**
     * @var ValidatorInterface
     */
    private $validationService;

    /**
     * LoaderService constructor.
     * @param WeatherService $googleWeatherService
     * @param FilesystemCache $cacheService
     * @param ValidatorInterface $validationService
     */
    public function __construct(
        WeatherService $googleWeatherService,
        FilesystemCache $cacheService,
        ValidatorInterface $validationService
    ) {
        $this->googleWeatherService = $googleWeatherService;
        $this->cacheService = $cacheService;
        $this->validationService = $validationService;
    }

    /**
     * @param \DateTime $day
     * @return Weather
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Exception
     */
    public function loadWeatherByDay(\DateTime $day): Weather
    {
        $cacheKey = $this->getCacheKey($day);
        if ($this->cacheService->has($cacheKey)) {
            echo "from cache";
            $weather = $this->cacheService->get($cacheKey);
        } else {
            echo "from API";
            $weather = $this->googleWeatherService->getToday($day);
            $this->cacheService->set($cacheKey, $weather);
        }

        return $weather;
    }

    /**
     * @param \DateTime $day
     * @return string
     */
    public function getCacheKey(\DateTime $day): string
    {
        return $day->format('Y-m-d');
    }


}