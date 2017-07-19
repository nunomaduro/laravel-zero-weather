<?php

namespace App;

use Zttp\ZttpRequest;
use NunoMaduro\ZeroFramework\Commands\AbstractCommand;

class DefaultCommand extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weather';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Search for today weather information";

    /**
     * Creates a new instance of the class.
     *
     * @param \Zttp\ZttpRequest $zttp
     */
    public function __construct(ZttpRequest $zttp)
    {
        parent::__construct();

        // Dependency resolved by the Laravel Container...
        $this->zttp = $zttp;
    }

    /**
     * Execute the console command. Here goes the command
     * code.
     *
     * @return void
     */
    public function handle(): void
    {
        // Use the HTTP client to ask today is weather:
        $response = $this->zttp->get('https://www.metaweather.com/api/location/44418/')
            ->json()['consolidated_weather'];

        // Parses the response and build a table.
        [$headers, $rows] = $this->getTablePayload($response);
        $this->info("Hello Artisan! Today's weather:");
        $this->table($headers, $rows);

        // Notify the user on the Operating System that the weather arrived.
        $this->notify('Weather info!', 'Weather information just arrived!');
    }

    /**
     * Returns headers and the rows in order to build a table.
     *
     * @return array
     */
    public function getTablePayload(array $response)
    {
        $headers = ['Information', 'Value'];
        $todayWeather = collect($response)->first();
        $rows = collect($todayWeather)->map(function ($value, $title) {
            return ['Information' => $title, 'Value' => $value];
        })->toArray();

        return [$headers, $rows];
    }
}
