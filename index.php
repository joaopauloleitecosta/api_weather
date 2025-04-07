<?php

require_once 'inc/api.php';
require_once 'inc/config.php';

$city = 'Natal';
if (isset($_GET['city'])) {
    $city = $_GET['city'];
}
$days = '5';

$results = Api::get($city, $days);

if ($results['status'] == 'error') {
    echo $results['message'];
    exit;
}

$data = json_decode($results['data'], true);

//location data
$location = [];
$location['name']         = $data['location']['name'];
$location['region']       = $data['location']['region'];
$location['country']      = $data['location']['country'];
$location['latitude']     = $data['location']['lat'];
$location['longitude']    = $data['location']['lon'];
$location['current_time'] = $data['location']['localtime'];

//current weather data
$current = [];
$current['info']           = 'Neste momento:';
$current['temperature']    = $data['current']['temp_c'];
$current['condition']      = $data['current']['condition']['text'];
$current['condition_icon'] = $data['current']['condition']['icon'];
$current['wind_speed']     = $data['current']['wind_kph'];

//forest weather data
$forecast = [];
foreach ($data['forecast']['forecastday'] as $day) {
    $forecast_day = [];
    $forecast_day['info'] = null;
    $forecast_day['date'] = $day['date'];
    $forecast_day['condition'] = $day['day']['condition']['text'];
    $forecast_day['condition_icon'] = $day['day']['condition']['icon'];
    $forecast_day['max_temp'] = $day['day']['maxtemp_c'];
    $forecast_day['min_temp'] = $day['day']['mintemp_c'];
    $forecast[] = $forecast_day;
}

function city_selected($city, $selected_city) {
    if($city == $selected_city) {
        return 'selected';
    }

    return '';
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.4/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-DQvkBjpPgn7RC31MCQoOeC9TI2kdqa4+BSgNMNj8v77fdC77Kj5zpWFTJaaAoMbC" crossorigin="anonymous">
    <title>Consume API Weather</title>
</head>

<body class="bg-dark text-white">
    <div class="container-fluid mt-5">
        <div class="row justify-content-center mt-5">
            <div class="col-10 p-5 bg-light text-black">

                <div class="row">
                    <div class="col-9">
                        <h3>Tempo para a cidade <strong><?= $location['name'] ?></strong></h3>
                        <p class="my-2">Região: <?= $location['region'] ?> | <?= $location['country'] ?> | <?= $location['current_time'] ?> Previsão para: <strong><?= $days ?></strong> dias</p>
                    </div>
                    <div class="col-3 text-end">
                        <select class="form-select">
                            <option value="Natal" <?= city_selected('Natal', $city) ?>>Natal</option>
                            <option value="Brasilia" <?= city_selected('Brasilia', $city) ?>>Brasilia</option>
                            <option value="Lisbon" <?= city_selected('Lisbon', $city) ?>>Lisboa</option>
                            <option value="Paris" <?= city_selected('Paris', $city) ?>>Paris</option>
                        </select>
                    </div>
                </div>

                <!-- current -->
                <?php
                $weather_info = $current;
                include 'inc/weather_info.php';
                ?>

                <!-- forecast -->
                <?php foreach ($forecast as $day) : ?>
                    <?php
                    $weather_info = $day;
                    include 'inc/weather_info.php';
                    ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        const select = document.querySelector('select');
        select.addEventListener('change', (e) => {
            const city = e.target.value;
            window.location.href = `index.php?city=${city}`;
        });
    </script>
</body>

</html>